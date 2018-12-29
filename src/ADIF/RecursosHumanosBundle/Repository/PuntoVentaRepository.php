<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use ADIF\ContableBundle\Entity\Facturacion\ClaseContrato;

/**
 * 
 */
class PuntoVentaRepository extends EntityRepository {

    public function getPuntoVentaByClaseContratoYMonto(ClaseContrato $claseContrato, $monto) {
        $query = $this->createQueryBuilder('pv')
                ->innerJoin('pv.puntosVentaClaseContrato', 'pvcc')
                ->innerJoin('pvcc.claseContrato', 'cc')
                ->where('cc.id = :claseContrato')
                ->andWhere('pvcc.montoMinimo <= :monto AND pvcc.montoMaximo >= :monto')

                ->setParameters(new ArrayCollection(array(
            new Parameter('claseContrato', $claseContrato->getId()),
            new Parameter('monto', (float)str_replace(',','.',$monto))))
        );

        return $query->getQuery()->getOneOrNullResult();
    }

}
