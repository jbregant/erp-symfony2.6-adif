<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class DefinitivoRepository extends EntityRepository {

    /**
     * 
     * @param type $cuentaPresupuestariaEconomica
     * @param type $ejercicio
     * @return type
     */
    public function getDefinitivosByCuentaPresupuestariaEconomicaYEjercicio($cuentaPresupuestariaEconomica, $ejercicio) {

        $query = $this->createQueryBuilder('d')
                ->where('d.cuentaPresupuestariaEconomica = :cuentaPresupuestariaEconomica')
                ->andWhere('YEAR(d.fechaDefinitivo) = :ejercicio')
                ->setParameter('cuentaPresupuestariaEconomica', $cuentaPresupuestariaEconomica)
                ->setParameter('ejercicio', $ejercicio)
        ;


//TODO : basicamente hay que combinar estas dos queries
//        $query = $this->createQueryBuilder('d')
//                ->where('d.cuentaPresupuestariaEconomica = :cuentaPresupuestariaEconomica')
//                ->leftJoin('d.devengados', 'dev')
//                ->andWhere('dev.id IS NULL')
//                ->andWhere('YEAR(d.fechaDefinitivo) = :ejercicio')
//                ->setParameter('cuentaPresupuestariaEconomica', $cuentaPresupuestariaEconomica)
//                ->setParameter('ejercicio', $ejercicio)
//        ;
//        $query = $this->createQueryBuilder('d')
//                ->where('d.cuentaPresupuestariaEconomica = :cuentaPresupuestariaEconomica')
//                ->leftJoin('d.devengados', 'dev')
//                ->andWhere('YEAR(d.fechaDefinitivo) = :ejercicio')
//                ->groupBy('d')
//                ->having('d.monto > sum(dev.monto)')
//                ->setParameter('cuentaPresupuestariaEconomica', $cuentaPresupuestariaEconomica)
//                ->setParameter('ejercicio', $ejercicio)
//        ;

        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param type $ejercicio
     */
    public function getDefinitivosByEjercicio($ejercicio) {

        $query = $this->createQueryBuilder('d')
                ->where('YEAR(d.fechaDefinitivo) = :ejercicio')
                ->setParameter('ejercicio', $ejercicio)
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param type $definitivoOrigen
     * @return type
     */
    public function getDefinitivoByDefinitivoOrigen($definitivoOrigen) {

        $query = $this->createQueryBuilder('d');

        $query
                ->where('d.definitivoOrigen = :definitivoOrigen')
                ->setParameter('definitivoOrigen', $definitivoOrigen)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

}
