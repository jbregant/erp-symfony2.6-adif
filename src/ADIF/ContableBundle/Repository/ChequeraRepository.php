<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class ChequeraRepository extends EntityRepository {

    /**
     */
    public function getChequerasByEstado($constanteEstadoChequera) {

        $query = $this->createQueryBuilder('c')
                ->innerJoin('c.estadoChequera', 'ec')
                ->where('ec.denominacionEstado = :estadoChequera')
                ->setParameter('estadoChequera', $constanteEstadoChequera)
        ;

        return $query->getQuery()->getResult();
    }

}
