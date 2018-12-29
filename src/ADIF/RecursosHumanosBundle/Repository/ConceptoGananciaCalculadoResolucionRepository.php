<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class ConceptoGananciaCalculadoResolucionRepository extends EntityRepository {

    /**
     * 
     * @param type $gananciaEmpleado
     * @return type
     */
    public function getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia) {

        $query = $this
                ->createQueryBuilder('cgc')
                ->innerJoin('cgc.conceptoGanancia', 'cg')
                ->where('cgc.gananciaEmpleado = :gananciaEmpleado')
                ->andWhere('cgc.conceptoGanancia = :conceptoGanancia')
                ->setParameter('gananciaEmpleado', $gananciaEmpleado)
                ->setParameter('conceptoGanancia', $conceptoGanancia)
                ->orderBy('cg.denominacion', 'asc')
                ->getQuery();

        return $query->getResult();
    }

}
