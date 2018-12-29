<?php

namespace ADIF\ComprasBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class RenglonCotizacionRepository extends EntityRepository {

    /**
     * 
     * @return type
     */
    public function setRenglonCotizacionGanador($idRenglonCotizacion, $justificacion) {

        $qb = $this->createQueryBuilder('rc');

        $updatePerdedores = $qb->update('ADIFComprasBundle:RenglonCotizacion', 'rc')
                ->set('rc.cotizacionElegida', 0)
                ->where('rc.id != :idRenglonCotizacion')
                ->setParameter('idRenglonCotizacion', $idRenglonCotizacion)
                ->getQuery();

        $updatePerdedores->execute();

        $updateGanador = $qb->update('ADIFComprasBundle:RenglonCotizacion', 'rc')
                ->set('rc.cotizacionElegida', 1)
                ->set('rc.justificacion', $qb->expr()->literal($justificacion))
                ->where('rc.id = :idRenglonCotizacion')
                ->setParameter('idRenglonCotizacion', $idRenglonCotizacion)
                ->getQuery();

        $updateGanador->execute();
    }

}
