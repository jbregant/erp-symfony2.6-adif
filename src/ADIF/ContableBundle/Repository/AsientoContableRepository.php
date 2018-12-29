<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * 
 */
class AsientoContableRepository extends EntityRepository {

    /**
     * 
     * @param type $denominacionEjercicio
     * @param type $codigoConceptoAsientoContable
     * @return type
     */
    public function countAsientoFormalByEjercicioYConcepto($denominacionEjercicio, $codigoConceptoAsientoContable) {

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('cantidad_total', 'cantidad_total');

        $query = $this->_em->createNativeQuery("   
            SELECT
                SUM(IF(ac.fue_revertido = 1, -1, 1)) AS cantidad_total
            FROM
                asiento_contable ac
            INNER JOIN 
                concepto_asiento_contable cac 
            ON 
                ac.id_concepto_asiento_contable = cac.id
            WHERE
                cac.codigo = ?                
            AND 
                ac.fecha_baja IS NULL
            AND
                YEAR(ac.fecha_contable) = ?", $rsm);

        $query->setParameter(1, $codigoConceptoAsientoContable);
        $query->setParameter(2, $denominacionEjercicio);

        $resultado = $query->getResult();

        return $resultado ? $resultado[0]['cantidad_total'] : 0;
    }
    
    public function getRenglonesAsientosFuenteFinanciamiento($fechaInicio, $fechaFin)
    {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id_cuenta_contable', 'id_cuenta_contable');
        $rsm->addScalarResult('id_asiento_contable', 'id_asiento_contable');
        $rsm->addScalarResult('fecha_contable', 'fecha_contable'); 
        $rsm->addScalarResult('codigo_cuenta_contable', 'codigo_cuenta_contable');
        $rsm->addScalarResult('denominacion_cuenta_contable', 'denominacion_cuenta_contable');
        $rsm->addScalarResult('denominacion_asiento_contable', 'denominacion_asiento_contable');
        $rsm->addScalarResult('importe_mcl', 'importe_mcl');
        
        $sql = "
            SELECT 
                r.`id_cuenta_contable`,
                r.`id_asiento_contable`,
                DATE_FORMAT(r.`fecha_contable`, '%d/%m/%Y') AS fecha_contable,
                r.`codigo_cuenta_contable`,
                r.`denominacion_cuenta_contable`,
                r.`denominacion_asiento_contable`,
                r.`importe_mcl`
            FROM `vw_renglones_asientos_fuente_financiamiento` r
            WHERE r.`id_tipo_operacion_contable` = 1 -- Debe
            AND r.`fecha_contable` BETWEEN :fechaInicio AND :fechaFin
            ORDER BY r.`id_cuenta_contable`, r.`fecha_contable`
            "; 
        
        $query = $this->_em->createNativeQuery($sql, $rsm);

        $query->setParameter('fechaInicio', $fechaInicio);
        $query->setParameter('fechaFin', $fechaFin);

        return $query->getResult();
    }
    
    public function getRenglonesAsientosFuenteFinanciamientoProvision($fechaInicio, $fechaFin)
    {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id_cuenta_contable', 'id_cuenta_contable');
        $rsm->addScalarResult('id_asiento_contable', 'id_asiento_contable');
        $rsm->addScalarResult('fecha_contable', 'fecha_contable'); 
        $rsm->addScalarResult('codigo_cuenta_contable', 'codigo_cuenta_contable');
        $rsm->addScalarResult('denominacion_cuenta_contable', 'denominacion_cuenta_contable');
        $rsm->addScalarResult('denominacion_asiento_contable', 'denominacion_asiento_contable');
        $rsm->addScalarResult('importe_mcl', 'importe_mcl');
        
        $sql = "SELECT * FROM
                (
                    SELECT 
                    cc.id                                               	AS id_cuenta_contable,
                    ac.id                                               	AS id_asiento_contable,
                    ac.`fecha_contable`                                     AS fecha_contable,
                    IF(ccprov.codigo, ccprov.codigo, cc.codigo)         	AS codigo_cuenta_contable,
                    IF(ccprov.codigo, ccprov.denominacion, cc.denominacion) AS denominacion_cuenta_contable,
                    ac.denominacion                                     	AS denominacion_asiento_contable,
                    IF( rac.id_tipo_operacion_contable = 1, rac.importe_mcl, (-1)*rac.importe_mcl) AS importe_mcl,
                    ac.fecha_baja
                    FROM `asiento_contable` `ac`
                    INNER JOIN `renglon_asiento_contable` `rac`
                    ON `rac`.`id_asiento_contable` = `ac`.`id`
                    INNER JOIN `cuenta_contable` `cc`
                    ON `rac`.`id_cuenta_contable` = `cc`.`id`
                    INNER JOIN `fuente_financiamiento` `ff`
                    ON `ff`.`id_cuenta_contable` = `cc`.`id`
                    LEFT JOIN `cuenta_contable` `ccprov`
                    ON rac.`id_cuenta_contable_provision` = `ccprov`.`id`
                    WHERE id_concepto_asiento_contable != 23
                UNION
                    SELECT 
                    cc.id                                               	AS id_cuenta_contable,
                    ac.id                                               	AS id_asiento_contable,
                    ac.`fecha_contable`                                     AS fecha_contable,
                    IF(ccprov.codigo, ccprov.codigo, cc.codigo)         	AS codigo_cuenta_contable,
                    IF(ccprov.codigo, ccprov.denominacion, cc.denominacion) AS denominacion_cuenta_contable,
                    ac.denominacion                                     	AS denominacion_asiento_contable,
                    IF( rac.id_tipo_operacion_contable = 1, rac.importe_mcl, (-1)*rac.importe_mcl) AS importe_mcl,
                    ac.fecha_baja
                    FROM `asiento_contable` `ac`
                    INNER JOIN `renglon_asiento_contable` `rac`
                    ON `rac`.`id_asiento_contable` = `ac`.`id`
                    INNER JOIN `cuenta_contable` `cc`
                    ON `rac`.`id_cuenta_contable` = `cc`.`id`
                    LEFT JOIN `cuenta_contable` `ccprov`
                    ON rac.`id_cuenta_contable_provision` = `ccprov`.`id`
                    WHERE rac.es_provision_obra = 1
                ) AS pp
                    WHERE fecha_contable BETWEEN :fechaInicio AND :fechaFin
                    AND ISNULL(`fecha_baja`)
                    AND ISNULL(`fecha_baja`)
                    ORDER BY id_cuenta_contable, fecha_contable";
        
        $query = $this->_em->createNativeQuery($sql, $rsm);

        $query->setParameter('fechaInicio', $fechaInicio);
        $query->setParameter('fechaFin', $fechaFin);

        return $query->getResult();
    }
    
    public function getRenglonesAsientosProvisionObraByIdCuentaContable($idCuentaContable)
    {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id_cuenta_contable', 'id_cuenta_contable');
        $rsm->addScalarResult('id_asiento_contable', 'id_asiento_contable');
        $rsm->addScalarResult('fecha_contable', 'fecha_contable');
        $rsm->addScalarResult('codigo_cuenta_contable', 'codigo_cuenta_contable');
        $rsm->addScalarResult('denominacion_cuenta_contable', 'denominacion_cuenta_contable');
        $rsm->addScalarResult('denominacion_asiento_contable', 'denominacion_asiento_contable');
        $rsm->addScalarResult('importe_mcl', 'importe_mcl');
        
        $sql = "
            SELECT 
                r.`id_cuenta_contable`,
                r.`id_asiento_contable`,
                DATE_FORMAT(r.`fecha_contable`, '%d/%m/%Y') AS fecha_contable,
                r.codigo_cuenta_contable,
                r.denominacion_cuenta_contable,
                r.denominacion_asiento_contable,
                r.`importe_mcl`
            FROM vw_renglones_asientos_provision_obra r
            WHERE r.`id_cuenta_contable_provision` = :idCuentaContable
            AND r.`id_tipo_operacion_contable` = 1 -- Debe
            "; 
        
        $query = $this->_em->createNativeQuery($sql, $rsm);

        $query->setParameter('idCuentaContable', $idCuentaContable);

        return $query->getResult();
    }
	
	public function getTotalRenglon($idAsientoContable, $idTipoOperacion) 
	{
		$rsm = new ResultSetMapping();

        $rsm->addScalarResult('total', 'total');
		
		$sql = "SELECT getTotalRenglon( ".$idAsientoContable.", ".$idTipoOperacion." ) as total"; 
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		
		//$query->setParameter('idAsientoContable', $idAsientoContable);
		//$query->setParameter('idTipoOperacion', $idTipoOperacion);
		$total = $query->getResult();
        
//\Doctrine\Common\Util\Debug::dump( $total[0]['total'] ); exit;


		return $total[0]['total'];
		
	}
}
