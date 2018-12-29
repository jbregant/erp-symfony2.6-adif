<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class CuentaPresupuestariaEconomicaRepository extends EntityRepository {

    /**
     * 
     * @return type
     */
    public function getCuentasPresupuestariasEconomicasRaiz() {

        $query = $this->createQueryBuilder('cpe')
                ->select('partial cpe.{id, codigo, denominacion}')
                ->where('cpe.cuentaPresupuestariaEconomicaPadre IS NULL')
                ->getQuery()
                ->useResultCache(true, 7200, 'cuentas_presupuestarias_economicas')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $query->getResult();
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function getCuentasPresupuestariasEconomicasHijas($id) {

        $query = $this->createQueryBuilder('cpe')
                ->select('partial cpe.{id, codigo, denominacion, esImputable}')
                ->join('cpe.cuentaPresupuestariaEconomicaPadre', 'cp')
                ->where('cp.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $query->getResult();
    }

    /**
     * 
     * @param type $string
     * @return type
     */
    public function getCuentasPresupuestariasEconomicasByString($string) {

        $qb = $this->createQueryBuilder('cpe');

        $query = $qb->select('partial cpe.{id, codigo, denominacion, esImputable}')
                ->where($qb->expr()->like('cpe.codigo', ':string'))
                ->orWhere($qb->expr()->like('cpe.denominacion', ':string'))
                ->setParameter('string', '%' . $string . '%')
                ->getQuery()
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $query->getResult();
    }

}
