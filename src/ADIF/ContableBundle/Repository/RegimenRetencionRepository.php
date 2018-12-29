<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of RegimenRetencionRepository
 *
 * @author Manuel Becerra
 * created 14/11/2014
 */
class RegimenRetencionRepository extends EntityRepository {

    /**
     * 
     * @param type $tipoImpuesto
     * @param type $idBienEconomico
     * @return type
     */
    public function getRegimenRetencionByImpuestoYBienEconomico($tipoImpuesto, $idBienEconomico) {

        $qb = $this->createQueryBuilder('r');

        $query = $qb
                ->innerJoin('r.regimenesRetencionBienEconomico', 'rb')
                ->innerJoin('r.tipoImpuesto', 't')
                ->where('t.denominacion = :tipoImpuesto')
                ->andWhere('rb.idBienEconomico = :idBienEconomico')
                ->setParameters(array('tipoImpuesto' => $tipoImpuesto, 'idBienEconomico' => $idBienEconomico))
                ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * 
     * @param type $tipoImpuesto
     * @param type $anio
     * @param type $mes
     * @return type
     */
    public function getRegimenRetencionByImpuestoYFecha($tipoImpuesto, $anio, $mes) {

        $qb = $this->createQueryBuilder('r');

        $query = $qb
                ->innerJoin('r.tipoImpuesto', 't')
                ->where('t.denominacion = :tipoImpuesto')
                ->andWhere('YEAR(li.fechaCierreNovedades) = :anio')
                ->andWhere('MONTH(li.fechaCierreNovedades) = :mes')
                ->setParameters(new ArrayCollection(array(
                    new Parameter('tipoImpuesto', $tipoImpuesto),
                    new Parameter('anio', $anio),
                    new Parameter('mes', $mes)))
                )
                ->getQuery();

        return $query->getResult();
    }

    /**
     * 
     * @return type
     */
    public function getRegimenes($constanteTipoImpuesto) {

        $query = $this->createQueryBuilder('rr')
                ->join('rr.tipoImpuesto', 'ti')
                ->where('ti.denominacion = :constanteTipoImpuesto')
                ->andWhere('rr.asociableBienEconomico = :asociableBienEconomico')
                ->setParameter('asociableBienEconomico', true)
                ->setParameter('constanteTipoImpuesto', $constanteTipoImpuesto)
        ;

        return $query;
    }

}
