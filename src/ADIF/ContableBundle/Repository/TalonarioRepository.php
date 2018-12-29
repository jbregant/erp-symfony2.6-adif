<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;
use ADIF\ContableBundle\Entity\LetraComprobante;
use ADIF\ContableBundle\Entity\Facturacion\PuntoVenta;

/**
 * 
 */
class TalonarioRepository extends EntityRepository {

    /**
     * 
     * @param type $letraComprobante
     * @param type $puntoVenta
     * @return type
     */
    public function getTalonariosActivosByLetraYPuntoVenta(LetraComprobante $letraComprobante, PuntoVenta $puntoVenta) {
        $query = $this->createQueryBuilder('t')
                ->innerJoin('t.letraComprobante', 'lc')
                ->innerJoin('t.puntoVenta', 'pv')
                ->where('t.estaAgotado = 0')
                ->andWhere('lc.id = :letraComprobante')
                ->andWhere('pv.id = :puntoVenta')
                ->setParameter('letraComprobante', $letraComprobante->getId())
                ->setParameter('puntoVenta', $puntoVenta->getId())
                ->orderBy('t.numeroDesde', 'ASC')
        ;

        return $query->getQuery()->getResult();
    }

//
//    /**
//     * 
//     * @param LetraComprobante $letraComprobante
//     * @param string $puntoVenta
//     * @return type
//     */
//    public function getPuntosVentaActivosByLetra(LetraComprobante $letraComprobante) {
//
//   $query = $this->createQueryBuilder('t')
//                ->where('t.estaAgotado = 0')
//                ->andWhere('t.letraComprobante = :letraComprobante')
//                ->andWhere('t.puntoVenta = :puntoVenta')
//                ->setParameter('letraComprobante', $letraComprobante)
//                ->setParameter('puntoVenta', $puntoVenta)
//                ->orderBy('t.numeroDesde', 'ASC')
//        ;
//
//        return $query->getQuery()->getResult();
//        
//        
//        $rsm = new ResultSetMapping();
//
//        $rsm->addScalarResult('id', 'id');
//        $rsm->addScalarResult('punto_venta', 'punto_venta');
//        $rsm->addScalarResult('numero_desde', 'numero_desde');
//        $rsm->addScalarResult('numero_hasta', 'numero_hasta');
//
//        $native_query = $this->_em->createNativeQuery("
//            SELECT
//                t.id, t.numero_desde, t.numero_hasta, t.punto_venta
//            FROM
//                    talonario t
//            INNER JOIN (
//                    SELECT
//                            t2.punto_venta,
//                            MIN(t2.id_letra_comprobante) AS id_letra,
//                            MIN(t2.numero_desde) minimo
//                    FROM
//                            talonario t2
//                    INNER JOIN letra_comprobante lc2 ON lc2.id = t2.id_letra_comprobante
//                    WHERE
//                            lc2.letra ='" . $letra . "'
//                    AND t2.esta_agotado = 0
//                    GROUP BY
//                            t2.punto_venta
//            ) minimos ON minimos.punto_venta = t.punto_venta
//            AND minimos.minimo = t.numero_desde
//            AND t.id_letra_comprobante = minimos.id_letra
//            INNER JOIN letra_comprobante lc ON lc.id = t.id_letra_comprobante
//        ", $rsm);
//
//        return $native_query->getResult();
//    }
//
    /**
     * 
     * @param type $claseContrato
     * @param type $montoNeto
     * @param type $tipoComprobante
     * @param type $letraComprobante
     * @return type
     */
    public function getTalonariosByDatosComprobanteVenta($claseContrato, $montoNeto, $tipoComprobante, $letraComprobante) {

        $query = $this->createQueryBuilder('t')
                ->select('partial t.{id, puntoVenta}')
                ->join('t.puntoVenta', 'pv')
                ->join('pv.puntosVentaClaseContrato', 'pvcc')
                ->where('t.estaAgotado = 0')
                ->andWhere(':montoNeto >=  pvcc.montoMinimo')
                ->andWhere(':montoNeto <= pvcc.montoMaximo')
                ->setParameter('montoNeto', $montoNeto)
                ->andWhere('pvcc.claseContrato = :claseContrato')
                ->andWhere('t.letraComprobante = :letraComprobante')
                ->andWhere('t.tipoComprobante = :tipoComprobante')
                ->setParameter('claseContrato', $claseContrato)
                ->setParameter('letraComprobante', $letraComprobante)
                ->setParameter('tipoComprobante', $tipoComprobante)
                ->groupBy('t.puntoVenta')
                ->orderBy('t.puntoVenta', 'ASC')
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param type $tipoComprobante
     * @param type $letraComprobante
     * @param type $puntoVenta
     * @return type
     */
    public function getTalonarioByTipoComprobanteYLetraYPuntoVenta($tipoComprobante, $letraComprobante, $puntoVenta) {

        $query = $this->createQueryBuilder('t')
                ->where('t.estaAgotado = 0')
                ->andWhere('t.tipoComprobante = :tipoComprobante')
                ->andWhere('t.letraComprobante = :letraComprobante')
                ->andWhere('t.puntoVenta = :puntoVenta')
                ->setParameter('tipoComprobante', $tipoComprobante)
                ->setParameter('letraComprobante', $letraComprobante)
                ->setParameter('puntoVenta', $puntoVenta)
                ->orderBy('t.numeroDesde', 'ASC')
                ->setMaxResults(1)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

}
