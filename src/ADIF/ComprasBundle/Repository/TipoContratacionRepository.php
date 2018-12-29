<?php

namespace ADIF\ComprasBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class TipoContratacionRepository extends EntityRepository {

    /**
     * 
     * @return type
     */
    public function getTiposContrataciones() {

        $query = $this->createQueryBuilder('tc')
                ->select('partial tc.{id, denominacionTipoContratacion, montoDesde, montoHasta}')
                ->where('tc.fechaBaja IS NULL')
                ->getQuery()
                ->useResultCache(true, 7200, 'tipos_contrataciones')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $query->getResult();
    }

}
