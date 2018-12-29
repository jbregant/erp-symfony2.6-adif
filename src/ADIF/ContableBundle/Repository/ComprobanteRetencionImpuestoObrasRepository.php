<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\Expr\Join;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteObra;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Description of ComprobanteRetencionImpuestoObrasRepository
 *
 * @author drapetti
 * @copyright 2014
 * @access public
 */
class ComprobanteRetencionImpuestoObrasRepository extends EntityRepository {

    /**
     * 
     * @param type $proveedorId
     * @param type $regimenRetencion
     * @param type $anio
     * @param type $mes
     * @return type
     */
    public function getComprobanteRetencionByRegimenProveedorYFecha($proveedorId, $regimenRetencion, $anio, $mes = null) {

        $qb = $this->createQueryBuilder('cri');

        $query = $qb
                ->innerJoin('cri.ordenPago', 'op')
                ->innerJoin('ADIFContableBundle:Obras\OrdenPagoObra', 'opc', Join::WITH, 'opc.id = op.id')
                ->innerJoin('opc.estadoOrdenPago', 'eop')
                ->where('cri.regimenRetencion = :regimenRetencion')
                ->andWhere('opc.idProveedor = :proveedorId')
                ->andWhere('YEAR(opc.fechaContable) = :anio')
                ->andWhere('eop.denominacionEstado <> :denominacionEstado')
                ->setParameter('proveedorId', $proveedorId)
                ->setParameter('regimenRetencion', $regimenRetencion)
                ->setParameter('anio', $anio)
                ->setParameter('denominacionEstado', ConstanteEstadoOrdenPago::ESTADO_ANULADA)
        ;

        if ($mes !== null) {
            $query->andWhere('MONTH(opc.fechaContable) = :mes')
                    ->setParameter('mes', $mes);
        }

        return $query->getQuery()->getResult();
    }

    public function getMontoAcumuladoYRetenidoByRegimenProveedorYFecha($proveedorId, $impuesto, $regimenRetencion, $fecha) {

        $rsmNetoFacturado = new ResultSetMapping();

        $rsmNetoFacturado->addScalarResult('neto', 'neto');
        $queryNetoFacturado = $this->_em->createNativeQuery("
                SELECT IFNULL(SUM(rc.neto), 0) AS neto
                FROM comprobante_obra co  
                    INNER JOIN orden_pago opc ON co.id_orden_pago = opc.id
                    INNER JOIN orden_pago_obra opo ON opo.id = opc.id
                    INNER JOIN (
                        SELECT rc.id_comprobante, SUM(CASE tc.id WHEN ? THEN rc.monto_neto * (- 1) ELSE rc.monto_neto END) AS neto
                        FROM renglon_comprobante rc
                        INNER JOIN comprobante c ON c.id = rc.id_comprobante
                        INNER JOIN tipo_comprobante tc ON tc.id = c.id_tipo_comprobante
                        GROUP BY rc.id_comprobante
                    ) rc ON rc.id_comprobante = co.id
                    INNER JOIN (
                        SELECT id_orden_pago
                        FROM comprobante_retencion_impuesto
                        WHERE id_regimen_retencion = ?
                        GROUP BY id_orden_pago
                    ) cri ON cri.id_orden_pago = opc.id
                    INNER JOIN documento_financiero df ON df.id = co.id_documento_financiero
                    INNER JOIN tipo_documento_financiero tdf ON tdf.id = df.id_tipo_documento_financiero
                    INNER JOIN estado_orden_pago eop ON eop.id = opc.id_estado_orden_pago
                WHERE opo.id_proveedor = ?
                    AND opc.fecha_contable BETWEEN '2015-08-30' AND ?
                    AND tdf.id_regimen_retencion_" . strtolower($impuesto) . " = ?
                    AND eop.denominacion <> ?", $rsmNetoFacturado);

        $queryNetoFacturado->setParameter(1, ConstanteTipoComprobanteObra::NOTA_CREDITO);
        $queryNetoFacturado->setParameter(2, $regimenRetencion);
        $queryNetoFacturado->setParameter(3, $proveedorId);
        $queryNetoFacturado->setParameter(4, $fecha);
        $queryNetoFacturado->setParameter(5, $regimenRetencion);
        $queryNetoFacturado->setParameter(6, ConstanteEstadoOrdenPago::ESTADO_ANULADA);

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('monto_retencion', 'monto_retencion');
        $query = $this->_em->createNativeQuery("
            SELECT IFNULL(SUM(cri.monto), 0) as monto_retencion
            FROM comprobante_retencion_impuesto cri
                INNER JOIN orden_pago opc ON cri.id_orden_pago = opc.id
                INNER JOIN orden_pago_obra opo ON opo.id = opc.id
                INNER JOIN estado_orden_pago eop ON eop.id = opc.id_estado_orden_pago	
                WHERE cri.id_regimen_retencion = ?
                    AND opo.id_proveedor = ?
                    AND opc.fecha_contable BETWEEN '2015-08-30' AND ?
                    AND eop.denominacion <> ?
            ", $rsm);

        $query->setParameter(1, $regimenRetencion);
        $query->setParameter(2, $proveedorId);
        $query->setParameter(3, $fecha);
        $query->setParameter(4, ConstanteEstadoOrdenPago::ESTADO_ANULADA);
        
        $netos = [];
        $netos['neto'] = $queryNetoFacturado->getResult()[0]['neto'];
        $netos['monto_retencion'] = $query->getResult()[0]['monto_retencion'];
        
        return $netos;
    }

}
