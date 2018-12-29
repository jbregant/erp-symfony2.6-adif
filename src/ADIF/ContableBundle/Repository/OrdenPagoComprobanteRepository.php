<?php

namespace ADIF\ContableBundle\Repository;

use ADIF\BaseBundle\Repository\BaseRepository;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;

/**
 * Description of OrdenPagoComprobanteRepository
 *
 * @author Manuel Becerra
 * created 01/12/2014
 */
class OrdenPagoComprobanteRepository extends BaseRepository {

    /**
     * 
     * @return type
     */
    public function getAutorizacionesContablesSinAnular() {

        $qb = $this->createQueryBuilder('op');

        $query = $qb
                ->join('op.estadoOrdenPago', 'e')
                ->where('op.numeroOrdenPago IS NULL')
                ->andWhere('e.denominacionEstado != :denominacionEstado')
                ->setParameter('denominacionEstado', ConstanteEstadoOrdenPago::ESTADO_ANULADA)
                ->getQuery()
        ;

        return $query->getResult();
    }

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function getCantidadAutorizacionesContablesByBeneficiario($ordenPago) {

        $qb = $this->createQueryBuilder('op');

        $query = $qb
                ->select('count(op.id)')
                ->join('op.estadoOrdenPago', 'e')
                ->where('op.numeroOrdenPago IS NULL')
                ->andWhere('op.id != :id')
                ->andWhere('op.idProveedor = :idProveedor')
                ->andWhere('op.numeroAutorizacionContable < :numeroAutorizacionContable')
                ->andWhere('e.denominacionEstado != :denominacionEstado')
                ->setParameter('id', $ordenPago->getId())
                ->setParameter('numeroAutorizacionContable', $ordenPago->getNumeroAutorizacionContable())
                ->setParameter('idProveedor', $ordenPago->getIdProveedor())
                ->setParameter('denominacionEstado', ConstanteEstadoOrdenPago::ESTADO_ANULADA)
                ->getQuery()
        ;

        return $query->getSingleScalarResult();
    }

}
