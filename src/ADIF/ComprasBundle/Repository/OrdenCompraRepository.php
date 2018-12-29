<?php

namespace ADIF\ComprasBundle\Repository;

use ADIF\BaseBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * 
 */
class OrdenCompraRepository extends BaseRepository {

    /**
     * 
     * @return type
     */
    public function getCantidadOrdenesCompraPendientes() {

        $query = $this->createQueryBuilder('oc')
                ->select('count(oc.id)')
                ->where('oc.ordenCompraOriginal IS NOT NULL')
                ->andWhere('oc.numeroOrdenCompra IS NULL')
                ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function getAdicionalesCotizacion($id) {

        $query = $this->createQueryBuilder('o')
                ->select('a.id, t.id AS idTipoAdicional, a.signo, a.valor, a.tipoValor, a.idAlicuotaIva')
                ->innerJoin('o.cotizacion', 'c')
                ->innerJoin('c.adicionalesCotizacion', 'a')
                ->innerJoin('a.tipoAdicional', 't')
                ->where('o.id = :id')
                ->andWhere('a.adicionalElegido = 1')
                ->setParameter('id', $id)
                ->getQuery();

        return $query->getResult();
    }

    /**
     * 
     * @param type $requerimiento
     * @return type
     */
    public function findFechaOrdenCompraAnteriorByRequerimiento($requerimiento) {

        $query = $this->createQueryBuilder('oc')
                ->select('partial oc.{id, fechaOrdenCompra}')
                ->innerJoin('oc.cotizacion', 'c')
                ->where('c.requerimiento = :requerimiento')
                ->setParameter('requerimiento', $requerimiento)
                ->addOrderBy('oc.fechaOrdenCompra', 'DESC')
                ->setMaxResults(1)
                ->getQuery();

        return $query->getOneOrNullResult();
    }
    
    public function getOrdenesCompra($idProveedor, $ocSinSaldo)
    {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('numeroOrdenCompra', 'numeroOrdenCompra');
        $rsm->addScalarResult('fechaOrdenCompra', 'fechaOrdenCompra');
        $rsm->addScalarResult('numeroCarpeta', 'numeroCarpeta');
        $rsm->addScalarResult('observacion', 'observacion');
        $rsm->addScalarResult('simbolo', 'simbolo');
        $rsm->addScalarResult('total', 'total');
        $rsm->addScalarResult('saldo', 'saldo');
        $rsm->addScalarResult('saldoCantidades', 'saldoCantidades');
        
        $sql = '
            SELECT 
                oc.id,
                numeroOrdenCompra,
                fechaOrdenCompra,
                numeroCarpeta,
                observacion,
                simbolo,
                id_proveedor,
                total,
                IF(saldo_ok,saldo_ok,saldo) AS saldo,
                saldoCantidades
                FROM adif_compras.vw_ordenes_compra oc
                LEFT JOIN 
                (
                        SELECT id_orden_compra, SUM(dif_monto) AS saldo_ok
                        FROM (
                            SELECT *
                            FROM adif_contable.`vw_kpi_oc_comprobantes_proveedores_acumulado`
                        ) 
                        AS p
                        GROUP BY id_orden_compra
                ) saldo_new ON saldo_new.id_orden_compra = oc.id
            WHERE oc.id_proveedor = :idProveedor';
        
        if (!$ocSinSaldo) {
            $sql .= ' AND oc.saldoCantidades > 0 ';
        } else {
            $sql .= ' AND oc.saldoCantidades <= 0 ';
        }
        
        $query = $this->_em->createNativeQuery($sql, $rsm);
        
        $query->setParameter('idProveedor', $idProveedor);
        
        return $query->getResult();
    }

}
