<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use ADIF\ContableBundle\Entity\Facturacion\ClaseContrato;

/**
 * 
 */
class PuntoVentaClaseContratoRepository extends EntityRepository {

    public function getPuntoVentaClaseContratoSuperpuestoEnRango(ClaseContrato $claseContrato, $montoMinimo, $montoMaximo, $id = null) {

        $query = $this->createQueryBuilder('pvcc')
                ->where('pvcc.claseContrato = :claseContrato')
                ->andWhere('pvcc.montoMinimo <= :montoMaximo')
                ->andWhere(':montoMinimo <= pvcc.montoMaximo')
                ->setParameters(new ArrayCollection(array(
            new Parameter('claseContrato', $claseContrato),
            new Parameter('montoMinimo', $montoMinimo),
            new Parameter('montoMaximo', $montoMaximo)))
        );

        if ($id != null) {
            $query->setParameter('id', $id)
                    ->andWhere('pvcc.id != :id');
        }

        return $query->getQuery()->getResult();
    }

}
