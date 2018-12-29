<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\Expr\Join;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;

/**
 * Description of ComprobanteRetencionImpuestoComprasRepository
 *
 * @author drapetti
 * @copyright 2014
 * @access public
 */
class ComprobanteRetencionImpuestoComprasRepository extends EntityRepository {

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
                ->innerJoin('cri.ordenPago', 'op')
                ->innerJoin('ADIFContableBundle:OrdenPagoComprobante', 'opc', Join::WITH, 'opc.id = op.id')
                ->innerJoin('opc.estadoOrdenPago', 'eop')
                ->where('cri.regimenRetencion = :regimenRetencion')
                ->andWhere('opc.idProveedor = :proveedorId')
                ->andWhere('YEAR(opc.fechaContable) = :anio')
                ->andWhere('eop.denominacionEstado <> :denominacionEstado')
                ->setParameter('proveedorId', $proveedorId)
                ->setParameter('regimenRetencion', $regimenRetencion)
                ->setParameter('anio', $anio)
                ->setParameter('denominacionEstado', ConstanteEstadoOrdenPago::ESTADO_ANULADA)
        ;

        if ($mes !== null) {
            $query->andWhere('MONTH(opc.fechaContable) = :mes')
                    ->setParameter('mes', $mes);
        }

        return $query->getQuery()->getResult();
    }

}
