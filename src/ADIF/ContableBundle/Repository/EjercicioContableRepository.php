<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class EjercicioContableRepository extends EntityRepository {

    /**
     * 
     * @param type $fecha
     * @return type
     */
    public function getEjercicioContableByFecha($fecha) {

        $qb = $this->createQueryBuilder('e');

        $query = $qb
                ->where($qb->expr()->between(':fecha', 'e.fechaInicio', 'e.fechaFin'))
                ->setParameter('fecha', $fecha, \Doctrine\DBAL\Types\Type::DATE)
                ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * 
     * @param type $denominacionEjercicio
     * @return type
     */
    public function getEjercicioContableByDenominacion($denominacionEjercicio) {

        $qb = $this->createQueryBuilder('e');

        $query = $qb
                ->where('e.denominacionEjercicio = :denominacionEjercicio')
                ->setParameter('denominacionEjercicio', $denominacionEjercicio)
                ->getQuery();

        return $query->getOneOrNullResult();
    }

}
