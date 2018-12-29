<?php

namespace ADIF\ComprasBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class EntidadAutorizanteRepository extends EntityRepository {

    /**
     * 
     * @param type $monto
     * @return type
     */
    public function getEntidadAutorizanteByMonto($monto) {

        $query = $this->createQueryBuilder('ea')
                ->select('ea')
                ->where(':monto >  ea.montoDesde')
                ->andWhere(':monto <= ea.montoHasta')
                ->setParameter('monto', $monto)
                ->getQuery();
                
        return $query->getOneOrNullResult();
    }

    /**
     * 
     * @param type $grupos
     */
    public function getEntidadAutorizanteByGrupoId($grupos) {

        $query = $this->createQueryBuilder('ea')
                ->select('ea')
                ->where('ea.idGrupo IN (:string)')
                ->setParameter('string', $grupos, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->getQuery();

        return $query->getResult();
    }

}
