<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class RangoRemuneracionRepository extends EntityRepository {

    /**
     * 
     * @param type $monto
     */
    public function getRangoRemuneracionByMonto($monto) {

        $query = $this->createQueryBuilder('rr')
                ->where(':monto >  rr.montoDesde')
                ->andWhere(':monto <= rr.montoHasta')
                ->setParameter('monto', $monto)
                ->getQuery();

        return $query->getOneOrNullResult();
    }

}
