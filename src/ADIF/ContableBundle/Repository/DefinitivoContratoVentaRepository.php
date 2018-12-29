<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class DefinitivoContratoVentaRepository extends EntityRepository {

    /**
     * 
     * @param type $contrato
     * @param type $ejercicio
     * @return type
     */
    public function getDefinitivoByContratoYEjercicio($contrato, $ejercicio) {

        $query = $this->createQueryBuilder('d')
                ->where('d.contrato = :contrato')
                ->andWhere('YEAR(d.fechaDefinitivo) = :ejercicio')
                ->setParameter('contrato', $contrato)
                ->setParameter('ejercicio', $ejercicio)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

}
