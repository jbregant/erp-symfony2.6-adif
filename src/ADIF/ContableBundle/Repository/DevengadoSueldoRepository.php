<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class DevengadoSueldoRepository extends EntityRepository {

    /**
     * 
     * @param type $idLiquidacion
     * @param type $idsCuentasContables
     * @return type
     */
    public function getDevengadosSueldoByCuentasContables($idLiquidacion, $idsCuentasContables) {

        $query = $this->createQueryBuilder('ds')
                ->where('ds.idLiquidacion = :idLiquidacion')
                ->andWhere('ds.cuentaContable IN(:ids)')
                ->setParameter('idLiquidacion', $idLiquidacion)
                ->setParameter('ids', $idsCuentasContables)
        ;

        return $query->getQuery()->getResult();
    }

}
