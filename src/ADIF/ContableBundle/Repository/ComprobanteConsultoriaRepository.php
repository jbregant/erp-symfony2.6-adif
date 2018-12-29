<?php

namespace ADIF\ContableBundle\Repository;

use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Description of ComprobanteConsultoriaRepository
 */
class ComprobanteConsultoriaRepository extends EntityRepository {

    /**
     * 
     * @param type $consultorId
     * @param type $anio
     * @param type $mes
     * @return type
     */
    public function getComprobanteConsultoriaByConsultorYFecha($consultorId, $anio, $mes = null) {

        $qb = $this->createQueryBuilder('co');

        $query = $qb
                ->innerJoin('co.ordenPago', 'op')
                ->innerJoin('op.estadoOrdenPago', 'eop')
                ->innerJoin('co.contrato', 'con')
                ->where('con.idConsultor = :idConsultor')
//                ->andWhere('YEAR(op.fechaOrdenPago) = :anio')
                ->andWhere('YEAR(op.fechaContable) = :anio')
                ->andWhere('eop.denominacionEstado <> :denominacionEstado')
                ->setParameter('idConsultor', $consultorId)
                ->setParameter('anio', $anio)
                ->setParameter('denominacionEstado', ConstanteEstadoOrdenPago::ESTADO_ANULADA)
        ;
        if ($mes !== null) {
            $query->setParameter('mes', $mes)
//                    ->andWhere('MONTH(op.fechaOrdenPago) = :mes')
                    ->andWhere('MONTH(op.fechaContable) = :mes')
            ;
        } else {
            $query->andWhere('op.fechaContable > :fechaInicio')
                    ->setParameter('fechaInicio', '2015-08-30');
        }

        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param type $contrato
     * @return type
     */
    public function getComprobantesConsultoriaByContrato($contrato) {
        $query = $this->createQueryBuilder('cc')
                ->select('cc')
                ->where('cc.contrato = :contrato')
                ->setParameter('contrato', $contrato)
                ->orderBy('cc.fechaComprobante', 'ASC')
                ->getQuery();

        return $query->getResult();
    }

    /**
     *
     * @param array $criteria
     * @return array
     */
    public function validarNumeroComprobanteUnico(array $criteria) {

        $contrato = $criteria['contrato'];
        $fechaComprobante = $criteria['fechaComprobante'];
        $letraComprobante = $criteria['letraComprobante'];
        $puntoVenta = $criteria['puntoVenta'];
        $numero = $criteria['numero'];
        $tipoComprobante = $criteria['tipoComprobante'];
        
        $em = $this->getEntityManager();

        $query = $em->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')
                ->createQueryBuilder('c')
                ->select('c')
                ->innerJoin('c.estadoComprobante', 'e')
                ->innerJoin('c.contrato', 'con')
                ->where('c.letraComprobante = :letraComprobante')
                ->andWhere('c.fechaComprobante >= :fechaComprobante')
                ->andWhere('c.numero = :numero')
                ->andWhere('c.puntoVenta = :puntoVenta')
                ->andWhere('con.idConsultor = :idConsultor')
                ->andWhere('e.id != :idEstadoComprobante')
                ->andWhere('c.tipoComprobante = :tipoComprobante')
                ->setParameter('idEstadoComprobante', EstadoComprobante::__ESTADO_ANULADO)
                ->setParameter('fechaComprobante', $fechaComprobante, Type::DATE)
                ->setParameter('letraComprobante', $letraComprobante)
                ->setParameter('puntoVenta', $puntoVenta)
                ->setParameter('numero', $numero)
                ->setParameter('idConsultor', $contrato->getIdConsultor())
                ->setParameter('tipoComprobante', $tipoComprobante);
        
        if (isset($criteria['id'])) {
            $query
                    ->andWhere('c.id != :id')
                    ->setParameter('id', $criteria['id']);
        }

        return $query->getQuery()->getArrayResult();
    }

}
