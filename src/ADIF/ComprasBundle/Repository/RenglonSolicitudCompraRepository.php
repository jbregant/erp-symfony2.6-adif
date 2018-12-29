<?php

namespace ADIF\ComprasBundle\Repository;

use Doctrine\ORM\EntityRepository;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoRenglonSolicitud;

/**
 * 
 */
class RenglonSolicitudCompraRepository extends EntityRepository {

    /**
     * 
     * @return type
     */
    public function getRenglonesSolicitudCompraSupervisados() {

        $query = $this->createQueryBuilder('s')
                ->select('s')
                ->innerJoin('s.estadoRenglonSolicitudCompra', 'er')
                ->where('er.denominacionEstadoRenglonSolicitudCompra = :estadoSupervisado')
                ->orWhere('er.denominacionEstadoRenglonSolicitudCompra = :estadoRequerimientoParcial')
                ->andWhere('s.cantidadPendiente > 0')
                ->setParameters(array(
                    'estadoSupervisado' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_SUPERVISADO,
                    'estadoRequerimientoParcial' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_CON_REQUERIMIENTO_PARCIAL)
                )
                ->getQuery();

        return $query->getResult();
    }

    /**
     * 
     * @return type
     */
    public function getCantidadRenglonesSolicitudCompraSupervisados() {

        $query = $this->createQueryBuilder('s')
                ->select('count(s.id)')
                ->innerJoin('s.estadoRenglonSolicitudCompra', 'er')
                ->where('er.denominacionEstadoRenglonSolicitudCompra = :estadoSupervisado')
                ->orWhere('er.denominacionEstadoRenglonSolicitudCompra = :estadoRequerimientoParcial')
                ->andWhere('s.cantidadPendiente > 0')
                ->setParameters(array(
                    'estadoSupervisado' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_SUPERVISADO,
                    'estadoRequerimientoParcial' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_CON_REQUERIMIENTO_PARCIAL)
                )
                ->getQuery();

        return $query->getSingleScalarResult();
    }

}
