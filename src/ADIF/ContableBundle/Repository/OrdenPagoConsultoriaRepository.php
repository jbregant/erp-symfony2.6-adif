<?php

namespace ADIF\ContableBundle\Repository;

use ADIF\BaseBundle\Repository\BaseRepository;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;

/**
 * Description of OrdenPagoConsultoriaRepository
 *
 * @author Manuel Becerra
 * created 11/05/2015
 */
class OrdenPagoConsultoriaRepository extends BaseRepository {

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function getCantidadAutorizacionesContablesByBeneficiario($ordenPago) {

        $qb = $this->createQueryBuilder('op');

        $query = $qb
                ->select('count(op.id)')
                ->innerJoin('op.contrato', 'c')
                ->innerJoin('op.estadoOrdenPago', 'e')
                ->where('op.numeroOrdenPago IS NULL')
                ->andWhere('op.id != :id')
                ->andWhere('c.idConsultor = :idConsultor')
                ->andWhere('op.numeroAutorizacionContable < :numeroAutorizacionContable')
                ->andWhere('e.denominacionEstado != :denominacionEstado')
                ->setParameter('id', $ordenPago->getId())
                ->setParameter('numeroAutorizacionContable', $ordenPago->getNumeroAutorizacionContable())
                ->setParameter('idConsultor', $ordenPago->getContrato()->getIdConsultor())
                ->setParameter('denominacionEstado', ConstanteEstadoOrdenPago::ESTADO_ANULADA)
                ->getQuery()
        ;

        return $query->getSingleScalarResult();
    }

}
