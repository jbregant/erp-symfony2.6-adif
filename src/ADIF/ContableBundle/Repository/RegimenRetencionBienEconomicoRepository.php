<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of RegimenRetencionRepository
 */
class RegimenRetencionBienEconomicoRepository extends EntityRepository {

    /**
     * 
     * @param type $tipoImpuesto
     * @param type $idBienEconomico
     * @return type
     */
    public function getRegimenRetencionBienEconomicoByImpuestoYBienEconomico($tipoImpuesto, $idBienEconomico) {

        $qb = $this->createQueryBuilder('rb');

        $query = $qb
                ->innerJoin('rb.regimenRetencion', 'rt')
                ->innerJoin('rt.tipoImpuesto', 't')
                ->where('t.denominacion = :tipoImpuesto')
                ->andWhere('rb.idBienEconomico = :idBienEconomico')
                ->setParameters(array('tipoImpuesto' => $tipoImpuesto, 'idBienEconomico' => $idBienEconomico))
                ->getQuery();

        return $query->getOneOrNullResult();
    }

}
