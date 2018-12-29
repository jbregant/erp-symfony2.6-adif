<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use Doctrine\ORM\Query\ResultSetMapping;
use ADIF\ContableBundle\Entity\OrdenPago;
use ADIF\ContableBundle\Entity\Obras\OrdenPagoObra;
use ADIF\ContableBundle\Entity\OrdenPagoComprobante;
use ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria;
use ADIF\ContableBundle\Entity\OrdenPagoPagoParcial;

/**
 * 
 */
class ComprobanteRetencionImpuestoRepository extends EntityRepository 
{

	/**
     * 
     * @param type $proveedorId
     * @param type $regimenRetencion
     * @param type $fecha
     * @param type $anual
     * @return type
     */
    public function getMontoAcumuladoYRetenidoByRegimenProveedorYFecha($proveedorId, $regimenRetencion, $fecha, $anual = false) {

        $fechaInicio = $anual ? $fecha->format('Y') . '-01-01 00:00:00' : $fecha->format('Y-m') . '-01 00:00:00';
		$fechaFin = $fecha->format('Y-m-d') . ' 23:59:59';

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('neto', 'neto');
        $rsm->addScalarResult('monto_retencion', 'monto_retencion');
        
        $query = $this->_em->createNativeQuery("     
            SELECT
                IFNULL(SUM(cri.base_imponible), 0) AS neto,
                IFNULL(SUM(cri.monto), 0) AS monto_retencion
            FROM
                comprobante_retencion_impuesto cri
                INNER JOIN orden_pago op ON cri.id_orden_pago = op.id
                LEFT JOIN orden_pago_comprobante opc ON opc.id = op.id
                LEFT JOIN orden_pago_obra opo ON opo.id = op.id
                INNER JOIN estado_orden_pago eop ON eop.id = op.id_estado_orden_pago
            WHERE
                cri.id_regimen_retencion = ?
                AND (opo.id_proveedor = ? OR opc.id_proveedor = ?)
                AND op.fecha_contable BETWEEN ?
                AND ?
                AND eop.denominacion <> ?
                AND cri.monto > 0", $rsm);

        $query->setParameter(1, $regimenRetencion);
        $query->setParameter(2, $proveedorId);
        $query->setParameter(3, $proveedorId);
        $query->setParameter(4, $fechaInicio);
        $query->setParameter(5, $fechaFin);
        $query->setParameter(6, ConstanteEstadoOrdenPago::ESTADO_ANULADA);

        return $query->getResult()[0];
    }


    /**
     * Version 2 para getMontoAcumuladoYRetenidoByRegimenProveedorYFecha
	 * Esta fixeada para el calculo del neto de comprobantes, no lo saco mas de la base imponible, 
	 * del comprobante retencion de impuesto, sino directamente de los renglones de los comprobantes. 
	 * Esto se hizo porque calculaba mal el neto acumulado para el calculo de Ganancias. 
	 * Es este fix no se tienen en cuenta comprobantes de letra "Y" para el calculo del neto.
	 * Tambien se tienen en cuenta pagos parciales.
	 * 
	 * @author Gustavo Luis
	 * @date 10/07/2017
	 * 
     * @param integer $proveedorId
     * @param integer $regimenRetencion
     * @param \DateTime $fecha
	 * @param ADIF\ContableBundle\Entity\OrdenPago $ordenPago
     * @param boolean $anual
	 * @param boolean $esUTE
     * @return array
     */
    public function getMontoAcumuladoYRetenidoByRegimenProveedorYFechaV2(
		$idProveedor, $idRegimenRetencion, $fecha, OrdenPago $ordenPago, $anual = false, $esUTE = false) {

        $fechaInicio = $anual ? $fecha->format('Y') . '-01-01 00:00:00' : $fecha->format('Y-m') . '-01 00:00:00';
		$fechaFin = $fecha->format('Y-m-d') . ' 23:59:59';
		
		$tablaComprobantes = '';
		if ($ordenPago instanceof OrdenPagoObra) {
			
			$tablaComprobantes = 'comprobante_obra';
			
		} elseif ($ordenPago instanceof OrdenPagoPagoParcial) {
			
			$comprobante = $ordenPago->getPagoParcial()->getComprobante();
			
			if ($comprobante->getEsComprobanteObra()) {
				
				$tablaComprobantes = 'comprobante_obra';
				
			} else {
				
				$tablaComprobantes = 'comprobante_compra';
			}
			
		} else {
			// finalmente si es OrdenPagoComprobante
			$tablaComprobantes = 'comprobante_compra';
		}
		
		if ($tablaComprobantes == 'comprobante_compra' && !$esUTE) {
			// Para comprobantes de compra y si no es una UTE, voy por la logica vieja
			return $this->getMontoAcumuladoYRetenidoByRegimenProveedorYFecha($idProveedor, $idRegimenRetencion, $fecha, $anual);
		}
		
        $rsm = new ResultSetMapping();
		
		$montoRetencion = $this->getMontoAcumuladoYRetenidoByRegimenProveedorYFecha($idProveedor, $idRegimenRetencion, $fecha, $anual);

        $rsm->addScalarResult('neto', 'neto');
		
		$sql = "
			SELECT
				IFNULL(SUM(comp.neto),0) AS neto
			FROM
			(
			SELECT 
				IF (c.id_tipo_comprobante = 3, 
					rc.precio_unitario * rc.cantidad * -1,
					rc.precio_unitario * rc.cantidad)  AS neto
				FROM comprobante c
				INNER JOIN $tablaComprobantes co ON c.id = co.id
				INNER JOIN renglon_comprobante rc ON c.id = rc.id_comprobante
				INNER JOIN orden_pago op ON co.id_orden_pago = op.id
				WHERE 1 = 1 
				AND op.fecha_contable BETWEEN :fechaInicio AND :fechaFin
				AND co.id_proveedor = :idProveedor
				AND op.id_estado_orden_pago <> 5
			) comp
		";
		
        $query = $this->_em->createNativeQuery($sql, $rsm);

		$query->setParameter('fechaInicio', $fechaInicio);
        $query->setParameter('fechaFin', $fechaFin);
        $query->setParameter('idProveedor', $idProveedor);

		//var_dump(get_class($ordenPago));
		//var_dump($fechaInicio, $fechaFin);
		//var_dump( $query->getResult()[0] ); exit;
		
        $netos = $query->getResult()[0];
		
		//$resultado['monto_retencion'] = $montoRetencion['monto_retencion'];
		//$resultado['neto'] = $netos['neto'];
		//var_dump( array_merge($montoRetencion, $netos) ); exit;
		//return $resultado;
		return array_merge($montoRetencion, $netos);
		
    }
	
	public function getAcumuladoGananciasUte($idProveedor, $idRegimenRetencion)
	{
		$fecha = new \DateTime();
		$fechaInicio = $fecha->format('Y-m') . '-01 00:00:00';
		$fechaFin = $fecha->format('Y-m-d') . ' 23:59:59';
		$fechaImplentacionAcumuladoGananciasUte = \DateTime::createFromFormat('Y-m-d H:i:s', '2017-08-10 00:00:00');
		
		$rsm = new ResultSetMapping();
		
        $rsm->addScalarResult('base_imponible_ganancias_ute', 'base_imponible_ganancias_ute');
		$rsm->addScalarResult('fecha_creacion', 'fecha_creacion');
		
		$sql = "
			SELECT 
				IFNULL(SUM(ret.base_imponible_ganancias_ute),0) AS base_imponible_ganancias_ute,
				ret.fecha_creacion
			FROM 
			(
					SELECT 
						IFNULL(cri.base_imponible_ganancias_ute,0) AS base_imponible_ganancias_ute,
						cri.fecha_creacion
					FROM comprobante_retencion_impuesto cri
					INNER JOIN orden_pago op ON cri.id_orden_pago = op.id
					INNER JOIN estado_orden_pago eop ON eop.id = op.id_estado_orden_pago
					LEFT JOIN orden_pago_obra opo ON opo.id = op.id
					LEFT JOIN orden_pago_comprobante opc ON opc.id = op.id
					WHERE 1 = 1 
					AND (opo.id_proveedor = :idProveedor OR opc.id_proveedor = :idProveedor)
					AND op.fecha_contable BETWEEN :fechaInicio AND :fechaFin
					AND cri.id_regimen_retencion = :idRegimenRetencion
					AND eop.denominacion <> :estadoAnulado
					GROUP BY cri.id_orden_pago
					ORDER BY cri.id DESC			
			) ret
		";
		
        $query = $this->_em->createNativeQuery($sql, $rsm);

		$query->setParameter('idProveedor', $idProveedor);
		$query->setParameter('fechaInicio', $fechaInicio);
        $query->setParameter('fechaFin', $fechaFin);
		$query->setParameter('idRegimenRetencion', $idRegimenRetencion);
		$query->setParameter('estadoAnulado', ConstanteEstadoOrdenPago::ESTADO_ANULADA);
        
        $resultado = $query->getOneOrNullResult();
		$baseImponibleGananciasUte = (isset($resultado['base_imponible_ganancias_ute']) && $resultado['base_imponible_ganancias_ute'] != null) 
			? $resultado['base_imponible_ganancias_ute']
			: 0;
		
		// 
		if (!empty($resultado) && $baseImponibleGananciasUte == 0) {
			
			$fechaCreacionComprobanteRetencion = \DateTime::createFromFormat('Y-m-d H:i:s', $resultado['fecha_creacion']);
			if ($fechaCreacionComprobanteRetencion < $fechaImplentacionAcumuladoGananciasUte) {
				// Si la fecha del comprobante de retencion es mas vieja
				// que la fecha de implementacion de esta mejora, tenque hacer el calculo viejo
				
				$resultado = $this->
					getMontoAcumuladoYRetenidoByRegimenProveedorYFecha($idProveedor, $idRegimenRetencion, $fecha, $anual = false);
				
				$baseImponibleGananciasUte = isset($resultado['monto_retencion']) 
					? $resultado['monto_retencion']
					: 0;
				
			}
		}
		
		return $baseImponibleGananciasUte;
		
	}

}
