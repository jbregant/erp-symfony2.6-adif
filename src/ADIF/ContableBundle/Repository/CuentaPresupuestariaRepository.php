<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class CuentaPresupuestariaRepository extends EntityRepository {

    /**
     * 
     * @return type
     */
    public function getCuentaPresupuestariaByEjercicioYCuentaEconomica($ejercicioContable, $cuentaPresupuestariaEconomica) {

        $query = $this->createQueryBuilder('cp')
                ->innerJoin('cp.presupuesto', 'p')
                ->where('p.ejercicioContable = :ejercicioContable')
                ->andWhere('cp.cuentaPresupuestariaEconomica = :cuentaPresupuestariaEconomica')
                ->setParameter('ejercicioContable', $ejercicioContable)
                ->setParameter('cuentaPresupuestariaEconomica', $cuentaPresupuestariaEconomica)
                ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * 
     * @return type
     */
    public function findAllPresupuestariasImputables($ejercicioContable) {

        $query = $this->createQueryBuilder('cp')
                ->join('cp.cuentaPresupuestariaEconomica', 'cpe')
                ->innerJoin('cp.presupuesto', 'p')
                ->where('cpe.esImputable = true')
                ->andWhere('p.ejercicioContable = :ejercicioContable')
                ->setParameter('ejercicioContable', $ejercicioContable)
        ;

        return $query;
    }

    /**
     * 
     * @return type
     */
    public function findAllPresupuestariasNoImputables($ejercicioContable) {

        $query = $this->createQueryBuilder('cp')
                ->join('cp.cuentaPresupuestariaEconomica', 'cpe')
                ->innerJoin('cp.presupuesto', 'p')
                ->where('cpe.esImputable = false')
                ->andWhere('p.ejercicioContable = :ejercicioContable')
                ->setParameter('ejercicioContable', $ejercicioContable)
                ->getQuery()
        ;

        return $query->getResult();
    }

}
