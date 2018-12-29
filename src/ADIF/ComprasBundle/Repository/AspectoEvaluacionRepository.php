<?php

namespace ADIF\ComprasBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class AspectoEvaluacionRepository extends EntityRepository {

    /**
     * 
     * @param type $idEvaluacionProveedor
     * @return type
     */
    public function getNuevosAspectosByEvaluacionProveedor($idEvaluacionProveedor) {

        $aspectosAsignados = //
                $this->createQueryBuilder('a')
                ->select('ae.id')
                ->from('ADIFComprasBundle:EvaluacionAspectoProveedor', 'eap')
                ->innerJoin('eap.evaluacionProveedor', 'ep')
                ->innerJoin('eap.aspectoEvaluacion', 'ae')
                ->where('a.id = ae.id')
                ->andWhere('ep.id = :idEvaluacionProveedor')
                ->setParameter('idEvaluacionProveedor', $idEvaluacionProveedor)
                ->getQuery()
                ->getResult();

        if (empty($aspectosAsignados)) {
            $aspectosAsignados[] = -1;
        }

        $aspectosNuevos = //
                $this->createQueryBuilder('a')
                ->select('a')
                ->where('a.id NOT IN (:string)')
                ->setParameter('string', $aspectosAsignados, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->getQuery()
                ->getResult();

        return $aspectosNuevos;
    }

}
