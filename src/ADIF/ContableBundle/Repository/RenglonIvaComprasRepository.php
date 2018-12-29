<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class RenglonIvaComprasRepository extends EntityRepository {

    /**
     */
    public function findAllBetweenAnd($fechaInicio, $fechaFin) {

        $qb = $this->createQueryBuilder('ric');
        
        $query = $qb->where($qb->expr()->between('ric.fecha', ':fechaInicio', ':fechaFin'))
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATE)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATE)
        ;

        return $query->getQuery()->getResult();
    }

}
