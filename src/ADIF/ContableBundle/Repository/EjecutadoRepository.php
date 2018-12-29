<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class EjecutadoRepository extends EntityRepository {

    /**
     * 
     * @param type $cuentaPresupuestariaEconomica
     * @param type $ejercicio
     * @return type
     */
    public function getEjecutadosByCuentaPresupuestariaEconomicaYEjercicio($cuentaPresupuestariaEconomica, $ejercicio) {

        $query = $this->createQueryBuilder('e')
                ->innerJoin('e.asientoContable', 'ac')
                ->where('e.cuentaPresupuestariaEconomica = :cuentaPresupuestariaEconomica')
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

        $query = $this->createQueryBuilder('e')
                ->select('SUM(e.monto) as monto')
                ->innerJoin('e.asientoContable', 'ac')
                ->where('e.cuentaContable = :cuentaContable')
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

}
