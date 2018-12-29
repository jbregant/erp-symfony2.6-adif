<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use Doctrine\ORM\Query\Expr\Join;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Description of ComprobanteObraRepository
 *
 * @author Esteban Primost
 * created 22/12/2014
 */
class ComprobanteObraRepository extends EntityRepository {

    /**
     * 
     * @param int $idTramo
     */
    public function getComprobantesObraByTramo($idTramo) {
        $query = $this->createQueryBuilder('co')
                ->select('co')
                ->innerJoin('co.documentoFinanciero', 'df')
                ->where('df.tramo = :idTramo')
                ->setParameter('idTramo', $idTramo)
                ->orderBy('co.fechaComprobante', 'ASC')
                ->getQuery();

        return $query->getResult();
    }

    /**
     * 
     * @param type $proveedorId
     * @param type $anio
     * @param type $mes
     * @return type
     */
    public function getComprobanteObraByProveedorYFecha($proveedorId, $anio, $mes = null) {

        $qb = $this->createQueryBuilder('co');

        $query = $qb
                ->innerJoin('co.ordenPago', 'op')
                ->innerJoin('op.estadoOrdenPago', 'eop')
                ->where('co.idProveedor = :proveedorId')
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
                ->andWhere('e.id != :idEstadoComprobante')
                ->andWhere('c.tipoComprobante = :tipoComprobante')
                ->setParameter('idEstadoComprobante', EstadoComprobante::__ESTADO_ANULADO)
                //->setParameter('fechaComprobante', $fechaComprobante, \Doctrine\DBAL\Types\Type::DATE)
                ->setParameter('letraComprobante', $letraComprobante)
                ->setParameter('puntoVenta', $puntoVenta)
                ->setParameter('numero', $numero)
                ->setParameter('idProveedor', $idProveedor)
				->setParameter('cuit', $cuit)
                ->setParameter('tipoComprobante', $tipoComprobante);

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

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();

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

    /**
     * 
     * @param int $idTramo
     */
    public function getComprobantesObraByTramoYFecha($idTramo, $fecha) {
        $query = $this->createQueryBuilder('co')
                ->select('co')
                ->innerJoin('co.documentoFinanciero', 'df')
                ->innerJoin('co.estadoComprobante', 'ec')
                ->where('df.tramo = :idTramo')
                ->andWhere('co.fechaContable <= :fecha')
                ->andWhere('co.fechaAnulacion IS NULL OR co.fechaAnulacion >= :fecha')
                ->setParameter('idTramo', $idTramo)
                ->setParameter('fecha', $fecha->format('Y-m-d 23:59:59'))
                ->addOrderBy('co.puntoVenta , co.numero, co.tipoComprobante, co.letraComprobante')
                ->addOrderBy('ec.id', 'DESC')
                ->addOrderBy('co.fechaAnulacion')
                ->getQuery();

        return $query->getResult();
    }

}
