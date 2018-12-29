<?php

namespace ADIF\ContableBundle\Repository;

use ADIF\ContableBundle\Entity\EstadoComprobante;
use Doctrine\ORM\EntityRepository;

/**
 * Description of ComprobanteVentaRepository
 */
class ComprobanteVentaRepository extends EntityRepository {

    /**
     *
     * @param array $criteria
     * @return array
     */
    public function validarNumeroComprobanteUnico(array $criteria) {

        $letraComprobante = $criteria['letraComprobante'];
        $puntoVenta = $criteria['puntoVenta'];
        $numero = $criteria['numero'];
        $idCliente = $criteria['idCliente'];

        $query = $this->createQueryBuilder('cv')
                ->select('cv')
                ->innerJoin('cv.estadoComprobante', 'e')
                ->where('cv.letraComprobante = :letraComprobante')
                ->andWhere('cv.puntoVenta = :puntoVenta')
                ->andWhere('cv.numero = :numero')
                ->andWhere('cv.idCliente = :idCliente')
                ->andWhere('e.id != :idEstadoComprobante')
                ->setParameter('letraComprobante', $letraComprobante)
                ->setParameter('puntoVenta', $puntoVenta)
                ->setParameter('numero', $numero)
                ->setParameter('idCliente', $idCliente)
                ->setParameter('idEstadoComprobante', EstadoComprobante::__ESTADO_ANULADO);

        if (isset($criteria['id'])) {
            $query
                    ->andWhere('cv.id != :id')
                    ->setParameter('id', $criteria['id']);
        }

        return $query->getQuery()->getArrayResult();
    }

}
