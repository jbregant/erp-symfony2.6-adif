<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class TipoResponsableRepository extends EntityRepository {

    /**
     * 
     * @param type $tipoImpuesto
     * @return type
     */
    public function getTiposResponsableByTipoImpuesto($tipoImpuesto) {

        $query = $this->createQueryBuilder('tr')
                ->innerJoin('tr.tiposImpuesto', 'ti')
                ->where('ti.denominacion = :constanteTipoImpuesto')
                ->setParameter('constanteTipoImpuesto', $tipoImpuesto)
                ->orderBy('tr.denominacionTipoResponsable', 'DESC')
        ;

        return $query;
    }

}
