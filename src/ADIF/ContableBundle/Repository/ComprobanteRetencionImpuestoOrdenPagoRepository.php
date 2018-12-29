<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

/**
 * Description of ComprobanteRetencionImpuestoOrdenPagoRepository
 *
 * @author drapetti
 * @copyright 2014
 * @access public
 */
class ComprobanteRetencionImpuestoOrdenPagoRepository extends EntityRepository {

    /**
     * 
     * @param type $proveedorId
     * @param type $regimenRetencion
     * @param type $anio
     * @param type $mes
     * @return type
     */
    public function getComprobanteRetencionByRegimenProveedorYFecha($proveedorId, $regimenRetencion, $anio, $mes = null) {

        $qb = $this->createQueryBuilder('cri');

        $query = $qb
                ->join('cri.ordenPago', 'opc'/* OrdenPagoComprobante */)
                ->where('cri.regimenRetencion = :regimenRetencion')
                ->andWhere('opc.idProveedor = :proveedorId')
                ->andWhere('YEAR(cri.fechaCreacion) = :anio')
                ->setParameter('proveedorId', $proveedorId)
                ->setParameter('regimenRetencion', $regimenRetencion)
                ->setParameter('anio', $anio)
        ;

        if ($mes !== null) {
            $query->andWhere('MONTH(cri.fechaCreacion) = :mes')
                    ->setParameter('mes', $mes);
        }

        return $query->getQuery()->getResult();
    }

}
