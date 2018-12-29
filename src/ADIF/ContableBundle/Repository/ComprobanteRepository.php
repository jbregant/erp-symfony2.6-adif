<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * 
 */
class ComprobanteRepository extends EntityRepository {

    /**
     * 
     * @param type $ids
     * @return type
     */
    public function getComprobantesById($ids) 
    {

        $qb = $this->createQueryBuilder('c');

        $query = $qb
                ->where('c.id IN (:ids)')
                ->setParameter('ids', $ids)
        ;

        return $query->getQuery()->getResult();
    }
    
    public function getComprobantesLibroIVACompras($fechaDesde, $fechaHasta)
    {
        $em = $this->getEntityManager();
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
         
        $sql = "
             
			SELECT c.id
            FROM `comprobante` c 
            INNER JOIN `comprobante_compra` ch ON c.id = ch.id
            WHERE 1 = 1
            AND (
                c.`fecha_creacion` BETWEEN :fechaDesde AND :fechaHasta
                OR ch.`fecha_anulacion` BETWEEN :fechaDesde AND :fechaHasta
            )
            AND c.`id_letra_comprobante` <> 7 
            AND c.`fecha_baja` IS NULL
            GROUP BY ch.`id_proveedor`, c.`numero`, ch.`punto_venta`, c.`id_tipo_comprobante`, c.`id_letra_comprobante`

            UNION 

            SELECT c.id
            FROM `comprobante` c 
            INNER JOIN `comprobante_obra` ch ON c.id = ch.id
            WHERE 1 = 1
            AND (
                c.`fecha_creacion` BETWEEN :fechaDesde AND :fechaHasta
                OR ch.`fecha_anulacion` BETWEEN :fechaDesde AND :fechaHasta
            )
            AND c.`id_letra_comprobante` <> 7 
            AND c.`fecha_baja` IS NULL
            GROUP BY ch.`id_proveedor`, c.`numero`, ch.`punto_venta`, c.`id_tipo_comprobante`, c.`id_letra_comprobante`

            UNION 

            SELECT c.id
            FROM `comprobante` c 
            INNER JOIN `comprobante_consultoria` ch ON c.id = ch.id
            LEFT JOIN `contrato_consultoria` cc ON ch.id_contrato = cc.id
            WHERE 1 = 1
            AND (
                c.`fecha_creacion` BETWEEN :fechaDesde AND :fechaHasta
                OR ch.`fecha_anulacion` BETWEEN :fechaDesde AND :fechaHasta
            )
            AND c.`id_letra_comprobante` <> 7 
            AND c.`fecha_baja` IS NULL
            GROUP BY cc.`id_consultor`, c.`numero`, ch.`punto_venta`, c.`id_tipo_comprobante`, c.`id_letra_comprobante`

            UNION 

            SELECT c.id 
            FROM `comprobante` c
            INNER JOIN `comprobante_egreso_valor` cev ON c.id = cev.id
            INNER JOIN rendicion_egreso_valor rev ON cev.id_rendicion_egreso_valor = rev.id 
            INNER JOIN estado_rendicion_egreso_valor erev ON rev.id_estado_rendicion_egreso_valor = erev.id
            WHERE 1 = 1
            AND rev.`fecha_rendicion` BETWEEN :fechaDesde AND :fechaHasta
            AND c.`id_letra_comprobante` <> 7 
            AND erev.`codigo` = 2
            AND c.`fecha_baja` IS NULL
            GROUP BY cev.`razon_social`, c.`numero`, cev.`punto_venta`, c.`id_tipo_comprobante`, c.`id_letra_comprobante`
		";
         
        $query = $em->createNativeQuery($sql, $rsm);
        $query->setParameter('fechaDesde', $fechaDesde);
        $query->setParameter('fechaHasta', $fechaHasta);
        return $query->getResult();
    }

}
