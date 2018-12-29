<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class TipoConceptoGananciaRepository extends EntityRepository {

    /**
     * 
     * @param type $ordenAplicacion
     * @return type
     */
    public function getTipoConceptoGananciaByOrdenAplicacion($ordenAplicacion) {

        $query = $this
                ->createQueryBuilder('tcg')
                ->where('tcg.ordenAplicacion = :ordenAplicacion')
                ->setParameter('ordenAplicacion', $ordenAplicacion)
                ->orderBy('tcg.denominacion', 'asc')
                ->getQuery();

        return $query->getOneOrNullResult();
    }

}
