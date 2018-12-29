<?php

namespace ADIF\ContableBundle\Repository;

use ADIF\ContableBundle\Entity\EstadoComprobante;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Description of ComprobanteEgresoValorRepository
 *
 * @author Gustavo Luis
 * created 09/06/2017
 */
class ComprobanteEgresoValorRepository extends EntityRepository 
{

    /**
     *
     * @param array $criteria
     * @return array
     */
    public function validarNumeroComprobanteUnico(array $criteria) 
	{
		$emCompras = EntityManagers::getEmCompras();
        $fechaComprobante = $criteria['fechaComprobante'];
        $letraComprobante = $criteria['letraComprobante'];
        $puntoVenta = $criteria['puntoVenta'];
        $numero = $criteria['numero'];
        $cuit = $criteria['CUIT'];
		$tipoComprobante = $criteria['tipoComprobante'];

        $em = $this->getEntityManager();
		
		// Busco el id del proveedor para poder matchearlo
		// con las tablas comprobante_compra/comprobante_obra
		$connection = $em->getConnection();
		$sql = "
			SELECT p.id
			FROM ".$emCompras.".proveedor p
			INNER JOIN ".$emCompras.".cliente_proveedor cp ON p.id_cliente_proveedor = cp.id
			WHERE cp.cuit = :cuit
		";

		$statement = $connection->prepare($sql);
		$statement->bindValue('cuit', $cuit);
		$statement->execute();
		$arrProveedor = $statement->fetch();
		
		$idProveedor = null;
		if ($arrProveedor != null && isset($arrProveedor['id'])) {
			$idProveedor = $arrProveedor['id'];
		}
		
		//var_dump($cuit, $arrProveedor, $idProveedor);exit;

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
		
		//var_dump( $criteria );
		//echo "<br>-------------<br>";
		//\Doctrine\Common\Util\Debug::dump( $query->getQuery()->getArrayResult() ); exit;

        return $query->getQuery()->getArrayResult();
    }

}
