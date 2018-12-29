<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class ProvisorioRepository extends EntityRepository {

    /**
     * 
     * @param type $cuentaPresupuestariaEconomica
     * @param type $ejercicio
     * @return type
     */
    public function getProvisoriosByCuentaPresupuestariaEconomicaYEjercicio($cuentaPresupuestariaEconomica, $ejercicio) {

        $query = $this->createQueryBuilder('p');

        $query
                ->where('p.cuentaPresupuestariaEconomica = :cuentaPresupuestariaEconomica')
                ->leftJoin('p.definitivo', 'd')
                ->andWhere('d IS NULL')
                ->andWhere('YEAR(p.fechaProvisorio) = :ejercicio')
                ->setParameter('cuentaPresupuestariaEconomica', $cuentaPresupuestariaEconomica)
                ->setParameter('ejercicio', $ejercicio)
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param type $ejercicio
     * @return type
     */
    public function getProvisoriosByEjercicio($ejercicio) {

        $query = $this->createQueryBuilder('p');

        $query
                ->where('YEAR(p.fechaProvisorio) = :ejercicio')
                ->setParameter('ejercicio', $ejercicio)
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param type $ejercicio
     * @return type
     */
    public function getProvisoriosConSaldoByEjercicio($ejercicio) {

        $query = $this->createQueryBuilder('p');

        $query
                ->leftJoin('p.definitivo', 'd')
                ->andWhere('d IS NULL')
                ->where('YEAR(p.fechaProvisorio) = :ejercicio')
                ->setParameter('ejercicio', $ejercicio)
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param type $provisorioOrigen
     * @return type
     */
    public function getProvisorioByProvisorioOrigen($provisorioOrigen) {

        $query = $this->createQueryBuilder('p');

        $query
                ->where('p.provisorioOrigen = :provisorioOrigen')
                ->setParameter('provisorioOrigen', $provisorioOrigen)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

}
