<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\Expr\Join;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;

/**
 * Description of ComprobanteRetencionImpuestoConsultoriaRepository
 *
 */
class ComprobanteRetencionImpuestoConsultoriaRepository extends EntityRepository {

    /**
     * 
     * @param type $proveedorId
     * @param type $regimenRetencion
     * @param type $anio
     * @param type $mes
     * @return type
     */
    public function getComprobanteRetencionByRegimenProveedorYFecha($idConsultor, $regimenRetencion, $anio, $mes = null) {

        $qb = $this->createQueryBuilder('cri');

        $query = $qb
                ->innerJoin('cri.ordenPago', 'op')
                ->innerJoin('ADIFContableBundle:Consultoria\OrdenPagoConsultoria', 'opc', Join::WITH, 'opc.id = op.id')
                ->innerJoin('opc.estadoOrdenPago', 'eop')
                ->innerJoin('opc.contrato', 'con')
                ->where('cri.regimenRetencion = :regimenRetencion')
                ->andWhere('con.idConsultor = :idConsultor')
                ->andWhere('YEAR(opc.fechaContable) = :anio')
                ->andWhere('eop.denominacionEstado <> :denominacionEstado')
                ->setParameter('idConsultor', $idConsultor)
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
