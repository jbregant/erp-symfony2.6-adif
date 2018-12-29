<?php

namespace ADIF\ComprasBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class ProveedorRepository extends EntityRepository {

    /**
     * 
     * @param type $rubrosIds
     * @return type
     */
    public function getProveedorByRubroId($rubrosIds) {

        $query = $this->createQueryBuilder('p')
                ->select('p')
                ->join('p.rubros', 'r')
                ->where('r.id IN (:ids)')
                ->setParameter('ids', $rubrosIds, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->getQuery();

        return $query->getResult();
    }

}
