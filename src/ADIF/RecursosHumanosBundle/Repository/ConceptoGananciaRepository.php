<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class ConceptoGananciaRepository extends EntityRepository {

    /**
     * 
     * @param type $ordenAplicacion
     * @return type
     */
    public function getConceptoGananciaByOrdenAplicacion($ordenAplicacion) {

        $query = $this
                ->createQueryBuilder('cg')
                ->innerJoin('cg.tipoConceptoGanancia', 'tcg')
                ->where('tcg.ordenAplicacion = :ordenAplicacion')
                ->setParameter('ordenAplicacion', $ordenAplicacion)
                ->orderBy('cg.denominacion', 'asc')
                ->getQuery();

        return $query->getResult();
    }
    /**
     * 
     * @return type
     */
    public function findAllConceptosF572() {

        return $this
                ->createQueryBuilder('cg')
                ->where('cg.aplicaEnFormulario572 = 1')
                ->orderBy('cg.denominacion', 'asc');
    }

}
