<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class CategoriaRepository extends EntityRepository {

    /**
     * 
     * @param type $idsCategorias
     * @return type
     */
    public function getCategoriasByIds($idsCategorias) {
        $query = $this->createQueryBuilder('c')
                ->where('c.id IN(:ids)')
                ->setParameter('ids', $idsCategorias)
                ->getQuery();
        return $query->getResult();
    }

}
