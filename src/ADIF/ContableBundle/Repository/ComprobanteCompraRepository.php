<?php

namespace ADIF\ContableBundle\Repository;

use ADIF\ContableBundle\Entity\EstadoComprobante;
use Doctrine\ORM\EntityRepository;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMapping;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Description of ComprobanteCompraRepository
 *
 * @author Manuel Becerra
 * created 14/11/2014
 */
class ComprobanteCompraRepository extends EntityRepository {

    /**
     * 
     * @param type $proveedorId
     * @param type $anio
     * @param type $mes
     * @return type
     */
    public function getComprobanteCompraByProveedorYFecha($proveedorId, $anio, $mes = null) {

        $qb = $this->createQueryBuilder('cc');

        $query = $qb
                ->innerJoin('cc.ordenPago', 'op')
                ->innerJoin('op.estadoOrdenPago', 'eop')
                ->andWhere('op.idProveedor = :proveedorId')
//                ->andWhere('YEAR(op.fechaOrdenPago) = :anio')
                ->andWhere('YEAR(op.fechaContable) = :anio')
                ->andWhere('eop.denominacionEstado <> :denominacionEstado')
                ->setParameter('proveedorId', $proveedorId)
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
     * @param int $idOrdenCompra
     */
    public function getComprobantesCompraByOrdenCompra($idOrdenCompra) {
        $query = $this->createQueryBuilder('cc')
                ->select('cc')
                ->where('cc.idOrdenCompra = :idOrdenCompra')
                ->setParameter('idOrdenCompra', $idOrdenCompra)
                ->orderBy('cc.fechaComprobante', 'ASC')
                ->getQuery();

        return $query->getResult();
    }

    /**
     * 
     * @param int $idOrdenCompra
     */
    public function getComprobantesCompraByOrdenCompraYFecha($idOrdenCompra, $fecha) {
        $query = $this->createQueryBuilder('cc')
                ->select('cc')
                ->innerJoin('cc.estadoComprobante', 'ec')
                ->where('cc.idOrdenCompra = :idOrdenCompra')
                ->andWhere('cc.fechaContable <= :fecha')
                ->andWhere('cc.fechaAnulacion IS NULL OR cc.fechaAnulacion >= :fecha')
                ->setParameter('idOrdenCompra', $idOrdenCompra)
                ->setParameter('fecha', $fecha->format('Y-m-d 23:59:59'))
                ->addOrderBy('cc.puntoVenta , cc.numero, cc.tipoComprobante, cc.letraComprobante')
                ->addOrderBy('ec.id', 'DESC')
                ->addOrderBy('cc.fechaAnulacion')
                ->getQuery();

        return $query->getResult();
    }

    /**
     * 
     * @param type $fechaInicio
     * @param type $fechaFin
     * @return type
     */
    public function getTotalIvaByPeriodo($fechaInicio, $fechaFin) {
        $qbr = $this->createQueryBuilder('cc');

        $result = $qbr
                ->select('cc, SUM(r.montoIva) AS total_iva')
                ->innerJoin('cc.renglonesComprobante', 'r')
                ->innerJoin('r.alicuotaIva', 'a')
                ->where('a.valor <> 0')
                ->andWhere($qbr->expr()->between('cc.fechaContable', ':fechaInicio', ':fechaFin'))
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATE)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATE)
                ->getQuery()
                //echo $result->getSQL();die;
                ->setMaxResults(1)
                ->getResult();

        return (count($result) == 0) ? 0 : $result[0]['total_iva'];


        /*
          $result = $this->createQueryBuilder("l")
          ->select('l, le, COUNT(e.id) AS meses_liquidados')
          ->innerJoin('l.liquidacionEmpleados', 'le')
          ->innerJoin('le.empleado', 'e')
          ->where('e.id = :idEmpleado')
          ->andWhere('MONTH(l.fechaCierreNovedades) BETWEEN :mesInicio AND :mesFin')
          ->andWhere('YEAR(l.fechaCierreNovedades) = :anio')
          ->setParameters(new ArrayCollection(array(
          new Parameter('idEmpleado', $idEmpleado),
          new Parameter('mesInicio', $mesInicio),
          new Parameter('mesFin', $mesFin),
          new Parameter('anio', $anio))))
          ->groupBy('e.id')
          ->getQuery()
          ->setMaxResults(1)
          ->getResult();

          return (count($result) == 0) ? 0 : $result[0]['meses_liquidados'];
         * */
    }

    /**
     *
     * @param array $criteria
     * @return array
     */
    public function validarNumeroComprobanteUnico(array $criteria) {
        $emCompras = EntityManagers::getEmCompras();

        $fechaComprobante = $criteria['fechaComprobante'];
        $letraComprobante = $criteria['letraComprobante'];
        $puntoVenta = $criteria['puntoVenta'];
        $numero = $criteria['numero'];
        $idProveedor = $criteria['idProveedor'];
		$tipoComprobante = $criteria['tipoComprobante'];

        $em = $this->getEntityManager();
		
		// Busco el cuit del proveedor para poder matchearlo
		// con la tabla comprobante_egreso_valor
		$connection = $em->getConnection();
		$sql = "
			SELECT cp.cuit
			FROM ".$emCompras.".proveedor p
			INNER JOIN ".$emCompras.".cliente_proveedor cp ON p.id_cliente_proveedor = cp.id
			WHERE p.id = :idProveedor
		";

		$statement = $connection->prepare($sql);
		$statement->bindValue('idProveedor', $idProveedor);
		$statement->execute();
		$arrProveedor = $statement->fetch();
		
		$cuit = null;
		if ($arrProveedor != null && isset($arrProveedor['cuit'])) {
			$cuit = $arrProveedor['cuit'];
		}
		
        $query = $em->getRepository('ADIFContableBundle:Comprobante')
                ->createQueryBuilder('c')
                ->select('c')
                ->innerJoin('c.estadoComprobante', 'e')
                ->leftJoin('ADIFContableBundle:ComprobanteCompra', 'cc', Join::WITH, 'c.id = cc.id')
                ->leftJoin('ADIFContableBundle:Obras\ComprobanteObra', 'co', Join::WITH, 'c.id = co.id')
				->leftJoin('ADIFContableBundle:EgresoValor\ComprobanteEgresoValor', 'cev', Join::WITH, 'c.id = cev.id')
                ->where('c.letraComprobante = :letraComprobante')
                //->andWhere('c.fechaComprobante >= :fechaComprobante')
                ->andWhere('c.numero = :numero')
                ->andWhere('cc.puntoVenta = :puntoVenta OR co.puntoVenta = :puntoVenta OR cev.puntoVenta = :puntoVenta')
                ->andWhere('cc.idProveedor = :idProveedor OR co.idProveedor = :idProveedor OR cev.CUIT = :cuit')
				->andWhere('c.tipoComprobante = :tipoComprobante')
                ->andWhere('e.id != :idEstadoComprobante')
                ->setParameter('idEstadoComprobante', EstadoComprobante::__ESTADO_ANULADO)
                //->setParameter('fechaComprobante', $fechaComprobante, \Doctrine\DBAL\Types\Type::DATE)
                ->setParameter('letraComprobante', $letraComprobante)
                ->setParameter('puntoVenta', $puntoVenta)
                ->setParameter('numero', $numero)
                ->setParameter('idProveedor', $idProveedor)
				->setParameter('cuit', $cuit)
				->setParameter('tipoComprobante', $tipoComprobante)
				;

        if (isset($criteria['id'])) {
            $query
                    ->andWhere('c.id != :id')
                    ->setParameter('id', $criteria['id']);
        }

		//\Doctrine\Common\Util\Debug::dump( $query->getQuery()->getArrayResult() ); exit;
		
        return $query->getQuery()->getArrayResult();
    }

    /**
     *
     * @param array $criteria
     * @return array
     */
    public function validarComprobanteCalipso(array $criteria) {
        
        $emCompras = EntityManagers::getEmCompras();
        
        $letraComprobante = $criteria['letraComprobante'];
        $puntoVenta = $criteria['puntoVenta'];
        $numero = $criteria['numero'];
        $idProveedor = $criteria['idProveedor'];
        $tipoComprobante = $criteria['tipoComprobante'];

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('numero', 'numero');

        $querySTR = "
            SELECT *
            FROM comprobantes_calipso c
                INNER JOIN ".$emCompras.".cliente_proveedor cp ON cp.cuit = c.cuit
                INNER JOIN ".$emCompras.".proveedor p ON p.id_cliente_proveedor = cp.id
            WHERE p.id = ?
                AND c.id_tipo_comprobante = ?
                AND c.id_letra_comprobante = ?
                AND c.numero = ?
            ";

        $em = $this->getEntityManager();

        $query = $em->createNativeQuery($querySTR, $rsm);

        $query->setParameter(1, $idProveedor);
        $query->setParameter(2, $tipoComprobante);
        $query->setParameter(3, $letraComprobante);
        $query->setParameter(4, $puntoVenta . '-' . $numero);

        return $query->getResult();
    }

}
