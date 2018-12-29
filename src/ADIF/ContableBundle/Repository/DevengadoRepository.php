<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class DevengadoRepository extends EntityRepository {

    /**
     * 
     * @param type $cuentaPresupuestariaEconomica
     * @param type $ejercicio
     * @return type
     */
    public function getDevengadosByCuentaPresupuestariaEconomicaYEjercicio($cuentaPresupuestariaEconomica, $ejercicio) {

        $query = $this->createQueryBuilder('dev')
                ->innerJoin('dev.asientoContable', 'ac')
                ->leftJoin('dev.ejecutado', 'e')
                ->where('e.id IS NULL')
                ->andWhere('dev.cuentaPresupuestariaEconomica = :cuentaPresupuestariaEconomica')
                ->andWhere('YEAR(ac.fechaContable) = :ejercicio')
                ->setParameter('cuentaPresupuestariaEconomica', $cuentaPresupuestariaEconomica)
                ->setParameter('ejercicio', $ejercicio)
        ;
        
        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param type $cuentaContable
     * @param type $mesInicio
     * @param type $mesFin
     * @param type $anio
     * @return int
     */
    public function getMontoByCuentaContableAnioYRango($cuentaContable, $mesInicio, $mesFin, $anio) {

        $query = $this->createQueryBuilder('dev')
                ->select('SUM(dev.monto) as monto')
                ->leftJoin('dev.ejecutado', 'e')
                ->innerJoin('dev.asientoContable', 'ac')
                ->where('e IS NULL')
                ->andWhere('dev.cuentaContable = :cuentaContable')
                ->andWhere('YEAR(ac.fechaContable) = :anio')
                ->andWhere('MONTH(ac.fechaContable) BETWEEN :mesInicio AND :mesFin')
                ->setParameter('mesInicio', $mesInicio)
                ->setParameter('mesFin', $mesFin)
                ->setParameter('anio', $anio)
                ->setParameter('cuentaContable', $cuentaContable)
                ->getQuery()
                ->setMaxResults(1)
                ->getResult();

        if (count($query) > 0) {
            return $query[0]['monto'];
        } else {
            return 0;
        }
    }

    /**
     * 
     * @param type $ejercicio
     * @return type
     */
    public function getDevengadosByEjercicio($ejercicio) {

        $query = $this->createQueryBuilder('dev')
                ->innerJoin('dev.asientoContable', 'ac')
                ->where('YEAR(ac.fechaContable) = :ejercicio')
                ->setParameter('ejercicio', $ejercicio)
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param type $ejercicio
     * @return type
     */
    public function getDevengadosConSaldoByEjercicio($ejercicio) {

        $query = $this->createQueryBuilder('dev')
                ->leftJoin('dev.ejecutado', 'e')
                ->innerJoin('dev.asientoContable', 'ac')
                ->where('e IS NULL')
                ->andWhere('YEAR(ac.fechaContable) = :ejercicio')
                ->setParameter('ejercicio', $ejercicio)
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param type $devengadoOrigen
     * @return type
     */
    public function getDevengadoByDevengadoOrigen($devengadoOrigen) {

        $query = $this->createQueryBuilder('dev');

        $query
                ->where('dev.devengadoOrigen = :devengadoOrigen')
                ->setParameter('devengadoOrigen', $devengadoOrigen)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

}
