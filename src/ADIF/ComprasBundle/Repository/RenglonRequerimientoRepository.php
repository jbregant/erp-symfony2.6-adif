<?php

namespace ADIF\ComprasBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class RenglonRequerimientoRepository extends EntityRepository {

    /**
     * 
     * @param type $requerimiento
     * @return type
     */
    public function getRenglonRequerimientoAgrupadoPorBienEconomico($requerimiento) {

        $query = $this->createQueryBuilder('rr')
                ->select('partial rr.{id, renglonSolicitudCompra} AS renglonRequerimiento', 'SUM(rr.cantidad) AS cantidadTotal')
                ->join('rr.renglonSolicitudCompra', 'rs')
                ->where('rr.requerimiento = :requerimiento')
                ->setParameter('requerimiento', $requerimiento)
                ->groupBy('rs.bienEconomico')
                ->getQuery();

        return $query->getResult();
    }

}
