<?php

namespace ADIF\ComprasBundle\Repository;

use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoRequerimiento;
use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class RequerimientoRepository extends EntityRepository {

    /**
     * 
     * @return type
     */
    public function getRequerimientoByEstadoEqual($denominacionEstadoRequerimiento) {
        $query = $this->createQueryBuilder('r')
                ->innerJoin('r.estadoRequerimiento', 'er')
                ->where('er.denominacionEstadoRequerimiento = (:denominacionEstadoRequerimiento)')
                ->setParameter('denominacionEstadoRequerimiento', $denominacionEstadoRequerimiento)
                ->addOrderBy('r.fechaRequerimiento', 'DESC')
                ->addOrderBy('r.id', 'DESC')
                ->getQuery();

        return $query->getResult();
    }

    /**
     * 
     * @param type $denominacionEstadoRequerimientoArray
     * @return type
     */
    public function getRequerimientoByEstadoNotEqual($denominacionEstadoRequerimientoArray) {

        $qb = $this->createQueryBuilder('r');

        $qb->innerJoin('r.estadoRequerimiento', 'er');

        $index = 1;

        foreach ($denominacionEstadoRequerimientoArray as $denominacionEstadoRequerimiento) {
            $qb
                    ->andWhere('er.denominacionEstadoRequerimiento != :denominacionEstadoRequerimiento_' . $index)
                    ->setParameter('denominacionEstadoRequerimiento_' . $index++, $denominacionEstadoRequerimiento);
        }

        $qb->addOrderBy('r.fechaRequerimiento', 'DESC')
                ->addOrderBy('r.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * 
     * @return type
     */
    public function getCantidadRequerimientosPendientesCotizacionByUsuario($idUsuario) {

        $query = $this->createQueryBuilder('r')
                ->select('count(r.id)')
                ->innerJoin('r.estadoRequerimiento', 'er')
                ->where('r.idUsuario = :idUsuario')
                ->andWhere('er.denominacionEstadoRequerimiento = :denominacionEstadoRequerimiento')
                ->setParameter('idUsuario', $idUsuario)
                ->setParameter('denominacionEstadoRequerimiento', ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_PENDIENTE_COTIZACION)
                ->getQuery();

        return $query->getSingleScalarResult();
    }

}
