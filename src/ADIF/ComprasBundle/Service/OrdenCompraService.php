<?php

namespace ADIF\ComprasBundle\Service;

use ADIF\ComprasBundle\Entity\OrdenCompra;
use ADIF\ComprasBundle\Entity\RenglonOrdenCompra;
use ADIF\ContableBundle\Entity\ComprobanteCompra;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoOrdenCompra;
use ADIF\BaseBundle\Entity\EntityManagers;
use Doctrine\ORM\NoResultException;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoMoneda;

/**
 * Description of OrdenCompraService
 *
 * @author Manuel Becerra
 * created 04/12/2014
 */
class OrdenCompraService {

    /**
     *
     * @var type 
     */
    protected $service;

    /**
     *
     * @var type 
     */
    protected $doctrine;

    /**
     * 
     * @param type $service
     * @param type $doctrine
     */
    public function __construct($service, $doctrine) {
        $this->service = $service;
        $this->doctrine = $doctrine;
    }

    /**
     * 
     * @param type $idProveedor
     * @param type $idRequerimiento
     * @param type $idCotizacion
     * @param type $idsRenglonCotizacion
     * @param OrdenCompra $ordenCompraAnulada
     * @throws type
     * @throws \Exception
     */
    public function generarOrdenCompraFromCotizacion($idProveedor, $idRequerimiento, $idCotizacion, $idsRenglonCotizacion, OrdenCompra $ordenCompraAnulada = null) {

        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());

        $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')->find($idProveedor);

        $tipoMonedas = [];

        $renglonesByTipoMoneda = [];
		
		$esMonedaExtranjera = false;

        if (!$proveedor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
        }

        /* @var $requerimiento Requerimiento */
        $requerimiento = $emCompras->getRepository('ADIFComprasBundle:Requerimiento')->find($idRequerimiento);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        /* @var $cotizacion Cotizacion */
        $cotizacion = $emCompras->getRepository('ADIFComprasBundle:Cotizacion')->find($idCotizacion);

        if (!$cotizacion) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cotizacion.');
        }


        foreach ($idsRenglonCotizacion as $idRenglonCotizacion) {

            /* @var $renglonCotizacion \ADIF\ComprasBundle\Entity\RenglonCotizacion */
            $renglonCotizacion = $emCompras->getRepository('ADIFComprasBundle:RenglonCotizacion')
                    ->find($idRenglonCotizacion);

            if (!$renglonCotizacion) {
                throw $this->createNotFoundException('No se puede encontrar la entidad RenglonCotizacion.');
            }

            // Si la cotización fue elegida
            if ($renglonCotizacion->getCotizacionElegida()) {

                $renglonOrdenCompra = new RenglonOrdenCompra();

                $renglonOrdenCompra->setRenglonCotizacion($renglonCotizacion);

                $renglonOrdenCompra->setBienEconomico(
                        $renglonCotizacion
                                ->getRenglonRequerimiento()
                                ->getRenglonSolicitudCompra()
                                ->getBienEconomico()
                );

                $renglonOrdenCompra->setCantidad($renglonCotizacion->getCantidad());
                $renglonOrdenCompra->setRestante($renglonCotizacion->getCantidad());

                $renglonOrdenCompra->setPrecioUnitario($renglonCotizacion->getPrecioUnitario(false));

                $renglonOrdenCompra->setTipoCambio($renglonCotizacion->getTipoCambio());

                $renglonOrdenCompra->setUnidadMedida(
                        $renglonCotizacion->getRenglonRequerimiento()
                                ->getRenglonSolicitudCompra()->getUnidadMedida()
                );

                $renglonOrdenCompra->setAlicuotaIva($renglonCotizacion->getAlicuotaIva());

                isset($tipoMonedas[$renglonCotizacion->getIdTipoMoneda()]) //
                                ? $tipoMonedas[$renglonCotizacion->getIdTipoMoneda()][] = $renglonCotizacion->getTipoMoneda() //
                                : $tipoMonedas[$renglonCotizacion->getIdTipoMoneda()] = array($renglonCotizacion->getTipoMoneda());

                isset($renglonesByTipoMoneda[$renglonCotizacion->getIdTipoMoneda()]) //
                                ? $renglonesByTipoMoneda[$renglonCotizacion->getIdTipoMoneda()][] = $renglonOrdenCompra //
                                : $renglonesByTipoMoneda[$renglonCotizacion->getIdTipoMoneda()] = array($renglonOrdenCompra);
								
								
				if ($renglonCotizacion->getTipoMoneda()->getCodigoTipoMoneda() != ConstanteTipoMoneda::PESO_ARGENTINO) {
					// Todos los renglones tiene que ser moneda extranjera, sino no se considera OC con moneda extranjera
					$esMonedaExtranjera = true;
				} else {
					$esMonedaExtranjera = false;
				}
            }
        }

        foreach ($tipoMonedas as $key => $tipoMoneda) {

            $ordenCompra = new OrdenCompra();

            // Set el estado Borrador
            $estadoOrdenCompra = $emCompras->getRepository('ADIFComprasBundle:EstadoOrdenCompra')
                    ->findOneByDenominacionEstado(ConstanteEstadoOrdenCompra::ESTADO_OC_BORRADOR);

            $ordenCompra->setEstadoOrdenCompra($estadoOrdenCompra);

            $ordenCompra->setProveedor($proveedor);
            $ordenCompra->setCotizacion($cotizacion);          

            $ordenCompra->setTipoMoneda($tipoMoneda[0]);

            if ($ordenCompraAnulada != null) {

                $ordenCompra->setNumeroCarpeta($ordenCompraAnulada->getNumeroCarpeta());

                $ordenCompra->setTipoContratacion($ordenCompraAnulada->getTipoContratacion());

                $ordenCompra->setCondicionPago($ordenCompraAnulada->getCondicionPago());

                $ordenCompra->setFechaEntrega($ordenCompraAnulada->getFechaEntrega());

                $ordenCompra->setDomicilioEntrega(clone $ordenCompraAnulada->getDomicilioEntrega());

                $ordenCompra->setObservacion($ordenCompraAnulada->getObservacion());
            } //.
            else {

                $ordenCompra->setTipoContratacion($requerimiento->getTipoContratacion());
            }


            foreach ($renglonesByTipoMoneda[$key] as $renglonOrdenCompra) {

                $renglonOrdenCompra->setOrdenCompra($ordenCompra);

                $ordenCompra->addRenglon($renglonOrdenCompra);
            }
			
			$ordenCompra->setSaldoMonedaExtranjera(null);
			if ($esMonedaExtranjera) {
				$ordenCompra->setSaldoMonedaExtranjera($ordenCompra->getMonto());
			}
			

            $emCompras->persist($ordenCompra);
        }
		
        // Comienzo la transaccion
        $emCompras->getConnection()->beginTransaction();

        try {

            $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

            $emCompras->flush();

            $emContable->flush();

            $emCompras->getConnection()->commit();
        } //.
        catch (\Exception $e) {

            $emCompras->getConnection()->rollback();
            $emCompras->close();

            throw $e;
        }
    }

    /**
     * 
     * @param type $comprobanteCompra
     * @throws type
     */
    public function generarOrdenCompraFromComprobanteCompra(ComprobanteCompra $comprobanteCompra, $em) {

        $ordenCompra = new OrdenCompra();

        // Set el estado Generada
        $estadoOrdenCompra = $em->getRepository('ADIFComprasBundle:EstadoOrdenCompra')
                ->findOneByDenominacionEstado(ConstanteEstadoOrdenCompra::ESTADO_OC_GENERADA);

        $ordenCompra->setEstadoOrdenCompra($estadoOrdenCompra);

        $proveedor = $em->getRepository('ADIFComprasBundle:Proveedor')->find($comprobanteCompra->getIdProveedor());

        if (!$proveedor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Proveedor.');
        }

        $ordenCompra->setProveedor($proveedor);

        foreach ($comprobanteCompra->getRenglonesComprobante() as $renglonComprobanteCompra) {

            /* @var $renglonComprobanteCompra RenglonComprobanteCompra */

            $renglonOrdenCompra = new RenglonOrdenCompra();

            $renglonOrdenCompra->setOrdenCompra($ordenCompra);

            $renglonOrdenCompra->setBienEconomico($renglonComprobanteCompra->getBienEconomico());

            $renglonOrdenCompra->setCantidad($renglonComprobanteCompra->getCantidad());
            $renglonOrdenCompra->setRestante($renglonComprobanteCompra->getCantidad());

            $renglonOrdenCompra->setPrecioUnitario($renglonComprobanteCompra->getPrecioUnitario());

            $renglonOrdenCompra->setAlicuotaIva($renglonComprobanteCompra->getAlicuotaIva());

            $ordenCompra->addRenglon($renglonOrdenCompra);

            $renglonComprobanteCompra->setIdBienEconomico($renglonComprobanteCompra->getBienEconomico()->getId());
            $renglonComprobanteCompra->setRenglonOrdenCompra($renglonOrdenCompra);
        }

        $ordenCompra->setNumeroOrdenCompra($this->getSiguienteNumeroOrdenCompra());
        $ordenCompra->setFechaOrdenCompra(new \DateTime());


        $em->persist($ordenCompra);
        $em->flush();

        $comprobanteCompra->setOrdenCompra($ordenCompra);
    }

    /**
     * 
     * @return type
     */
    public function getSiguienteNumeroOrdenCompra() {

        $repository = $this->doctrine
                ->getRepository('ADIFComprasBundle:OrdenCompra', EntityManagers::getEmCompras());

        $query = $repository->createQueryBuilder('oc')
                ->select('oc.numeroOrdenCompra')
                ->orderBy('oc.numeroOrdenCompra', 'DESC')
                ->setMaxResults(1)
                ->getQuery();

        try {
            $siguienteNumero = $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            $siguienteNumero = 0;
        }

        return $siguienteNumero + 1;
    }

    /**
     * 
     * @return type
     */
    public function getSaldoOrdenCompra($ordenCompra) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $montoARestar = 0;

        $tiposComprobantesSumanSaldo = [
            ConstanteTipoComprobanteCompra::FACTURA,
            ConstanteTipoComprobanteCompra::NOTA_DEBITO,
            ConstanteTipoComprobanteCompra::RECIBO,
            ConstanteTipoComprobanteCompra::TICKET_FACTURA,
            ConstanteTipoComprobanteCompra::NOTA_DEBITO_INTERESES
        ];

        $comprobantesCompra = $emContable->getRepository('ADIFContableBundle:ComprobanteCompra')
                ->findByIdOrdenCompra($ordenCompra->getId());


        $anticiposOrdenCompra = $emContable->getRepository('ADIFContableBundle:AnticipoOrdenCompra')
                ->findByIdOrdenCompra($ordenCompra->getId());

        foreach ($comprobantesCompra as $comprobanteCompra) {

            /* @var $comprobanteCompra ComprobanteCompra */
            if ($comprobanteCompra->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO) {

                $montoARestar += $comprobanteCompra->getTotal() *
                        (in_array($comprobanteCompra->getTipoComprobante()->getId(), $tiposComprobantesSumanSaldo) ? 1 : -1);
            }
        }

        foreach ($anticiposOrdenCompra as $anticipoOrdenCompra) {

            $montoARestar += $anticipoOrdenCompra->getMonto();
        }


        /* @var $ordenCompra OrdenCompra */
        return $ordenCompra->getMonto() - $montoARestar;
    }
    
    /**
     * 
     * @return type
     */
    public function getSaldoOrdenCompraALaFecha($ordenCompra, $fecha) {
        $em = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $montoARestar = 0;

        $tiposComprobantesSumanSaldo = [
            ConstanteTipoComprobanteCompra::FACTURA,
            ConstanteTipoComprobanteCompra::NOTA_DEBITO,
            ConstanteTipoComprobanteCompra::RECIBO,
            ConstanteTipoComprobanteCompra::TICKET_FACTURA,
            ConstanteTipoComprobanteCompra::NOTA_DEBITO_INTERESES
        ];

        $comprobantesCompra = $emContable->getRepository('ADIFContableBundle:ComprobanteCompra')
                ->findByIdOrdenCompra($ordenCompra->getId());

        $anticiposOrdenCompra = $emContable->getRepository('ADIFContableBundle:AnticipoOrdenCompra')
                ->findByIdOrdenCompra($ordenCompra->getId());

        foreach ($comprobantesCompra as $comprobanteCompra) {
            /* @var $comprobanteCompra ComprobanteCompra */
            if ($comprobanteCompra->getEstadoComprobante()->getId() != EstadoComprobante::__ESTADO_ANULADO) {
                $montoARestar += $comprobanteCompra->getTotal() * (in_array($comprobanteCompra->getTipoComprobante()->getId(), $tiposComprobantesSumanSaldo) ? 1 : -1);
            }
        }

        foreach ($anticiposOrdenCompra as $anticipoOrdenCompra) {
            $montoARestar += $anticipoOrdenCompra->getMonto();
        }

        $rsm = new ResultSetMapping();
        
        $rsm->addScalarResult('total', 'total');

        $native_query = $em->createNativeQuery('
                SELECT total
                FROM __vista_oc_total_nc
                WHERE id_orden_compra = ?
            ', $rsm);

        $native_query->setParameter(1, $ordenCompra->getId());

        $totalNotasCredito = $native_query->getResult();

        $montoARestar -= empty($totalNotasCredito) ? 0 : floatval($totalNotasCredito[0]['total']);

        /* @var $ordenCompra OrdenCompra */
        return $ordenCompra->getMonto() - $montoARestar;
    }

}
