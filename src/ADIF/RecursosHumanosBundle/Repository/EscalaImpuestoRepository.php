<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

/**
 * 
 */
class EscalaImpuestoRepository extends EntityRepository {

    /**
     * 
     * @param type $mes
     * @param type $monto
     * @return type
     */
    public function getEscalaImpuestoByMesYMonto($mes, $monto) {

        $query = $this->createQueryBuilder('ei')
                ->andWhere(':mes = ei.mes')
                ->andWhere(':monto >=  ei.montoDesde')
                ->andWhere(':monto < ei.montoHasta')
                ->setParameter('mes', $mes)
                ->setParameter('monto', $monto)
                ->getQuery();

        return $query->getOneOrNullResult();
    }
	
	public function getEscalaImpuestoByMesYMontoYVigencia($mes, $monto) {

		$fechaHoy = new \DateTime();
	
        $query = $this->createQueryBuilder('ei')
                ->andWhere(':mes = ei.mes')
                ->andWhere(':monto >=  ei.montoDesde')
                ->andWhere(':monto < ei.montoHasta')
				->andWhere('ei.vigenciaDesde < :fechaHoy AND ei.vigenciaHasta > :fechaHoy')
                ->setParameter('mes', $mes)
                ->setParameter('monto', $monto)
				->setParameter('fechaHoy', $fechaHoy->format('Y-m-d'))
                ->getQuery();

        return $query->getOneOrNullResult();
    }
	
	public function getEscalaImpuestoByMesYMontoYFecha($mes, $monto, $fecha) {

        $query = $this->createQueryBuilder('ei')
                ->andWhere(':mes = ei.mes')
                ->andWhere(':monto >=  ei.montoDesde')
                ->andWhere(':monto < ei.montoHasta')
				->andWhere('ei.vigenciaDesde < :fecha AND ei.vigenciaHasta > :fecha')
                ->setParameter('mes', $mes)
                ->setParameter('monto', $monto)
				->setParameter('fecha', $fecha->format('Y-m-d'))
                ->getQuery();

        return $query->getOneOrNullResult();
    }

}
