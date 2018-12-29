<?php

namespace ADIF\ContableBundle\Service;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteCodigoInternoBienEconomico;
use ADIF\ComprasBundle\Entity\Proveedor;
use ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto;
use ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoCompras;
use ADIF\ContableBundle\Entity\Constantes\ConstanteAlicuotaIva;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoComprobanteRetencionImpuesto;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\Constantes\ConstanteRegimenRetencion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria;
use ADIF\ContableBundle\Entity\EscalaRetencionHonorariosGanancias;
use ADIF\ContableBundle\Entity\Obras\OrdenPagoObra;
use ADIF\ContableBundle\Entity\OrdenPagoComprobante;
use ADIF\ContableBundle\Entity\RegimenRetencion;
use ADIF\ContableBundle\Entity\RenglonComprobanteCompra;
use ADIF\ContableBundle\Entity\RenglonDeclaracionJuradaComprobanteRetencionImpuesto;
use ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor;
use DateTime;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Symfony\Bridge\Monolog\Logger;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use Doctrine\DBAL\Connection;
use ADIF\ContableBundle\Entity\OrdenPagoPagoParcial;
use ADIF\ContableBundle\Entity\OrdenPago;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Description of RetencionesService
 */
class RetencionesService 
{
    /**
     *
     * @var type 
     */
    protected $doctrine;

    /**
     *
     * @var type 
     */
    private $logger;
	
	/**
	* 
	* @var ContainerInterface 
	*/
	private $container;

    /**
     *
     * @var type 
     */
    private $erroresExencion;

    /**
     *
     * @var type 
     */
    private $estadoGenerado = null;

    /**
     *
     * @var type 
     */
    private $regimenesRetencion = null;
	
	/**
	* Este flag determina si el comprobante de obra o de compras, para las  
	* retenciones de pagos parciales.
	* Si es TRUE es un comprobante de obra y si es FALSE es de compras
	*/
	private $esComprobanteObraPP = null;

    /**
     * 
     * @param type $doctrine
     * @param type $kernel
     */
    public function __construct($doctrine, $kernel, Container $container) {
        $this->doctrine = $doctrine;
        $this->logger = new Logger('retenciones');
		$this->container = $container;

        $this->initErroresExencion();

        $monologFormat = "%message%\n";
        $dateFormat = "Y/m/d H:i:s";
        $monologLineFormat = new LineFormatter($monologFormat, $dateFormat);

		$logRetencionDirectorio = $this->container->getParameter('directorio_retenciones');
		$logRetencionArchivo = 'retenciones_' . date('d-m-Y__H_i_s') . '.log';
        $streamHandler = new StreamHandler($logRetencionDirectorio . $logRetencionArchivo, Logger::INFO);
		
		$this->container->get('session')->set('logRetencionArchivo', $logRetencionArchivo);
		
        $streamHandler->setFormatter($monologLineFormat);

        $this->logger->pushHandler($streamHandler);
    }

    /////////////////////////////////
    /////////////COMPRAS/////////////
    /////////////////////////////////

    public function generarComprobantesRetencionCompras(OrdenPagoComprobante $ordenPagoComprobante) {

        /* @var $proveedor Proveedor */
        $proveedor = $ordenPagoComprobante->getProveedor();

        $this->logger->info("CÁLCULO RETENCIONES " . date('d/m/Y H:i:s'));
        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("Proveedor : " . $proveedor);
        $this->logger->info("Id Proveedor : " . $proveedor->getId());
        $this->logger->info("------------------------------------------------------------------------------");

        // Si el Proveedor NO es extranjero
        if (!$proveedor->getClienteProveedor()->getEsExtranjero()) {

            $this->retencionGanancias($proveedor, $ordenPagoComprobante);

            $this->retencionIIBB($proveedor, $ordenPagoComprobante);

            $this->retencionIVA($proveedor, $ordenPagoComprobante);

            $this->retencionSUSS($proveedor, $ordenPagoComprobante);

            return $this->erroresExencion;
        }
        $this->logger->info("------------------------------FIN RETENCIONES---------------------------------");
        $this->logger->info("------------------------------------------------------------------------------");
    }

    /**
     * 
     * @param Proveedor $proveedor
     * @param OrdenPagoComprobante $ordenPagoComprobante
     */
    public function retencionGanancias(Proveedor $proveedor, $ordenPagoComprobante) {
        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("RETENCIÓN GANANCIAS");
        $this->logger->info("------------------------------------------------------------------------------");

        $regimenes_comprobante = $this
                ->getRenglonesComprobantesAgrupadosByRegimen(ConstanteTipoImpuesto::Ganancias, $ordenPagoComprobante->getComprobantes());

        $this->retencionGananciasProveedor($proveedor, $ordenPagoComprobante, $regimenes_comprobante);
        $this->logger->info("FIN RETENCION GANANCIAS-------------------------------------------------------");
    }

    /**
     * 
     * @param Proveedor $proveedor
     * @param OrdenPagoComprobante $ordenPagoComprobante
     */
    private function retencionIIBB(Proveedor $proveedor, $ordenPagoComprobante) {

        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("RETENCIÓN IIBB");
        $this->logger->info("------------------------------------------------------------------------------");

        $regimenes_comprobante = $this
                ->getRenglonesComprobantesAgrupadosByRegimen(ConstanteTipoImpuesto::IIBB, $ordenPagoComprobante->getComprobantes());

        $this->retencionIIBBProveedor($proveedor, $ordenPagoComprobante, $regimenes_comprobante);
        $this->logger->info("FIN RETENCION IIBB-------------------------------------------------------");
    }

    /**
     * 
     * @param Proveedor $proveedor
     * @param OrdenPagoComprobante $ordenPagoComprobante
     */
    private function retencionSUSS(Proveedor $proveedor, $ordenPagoComprobante) {

        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("RETENCIÓN SUSS");
        $this->logger->info("------------------------------------------------------------------------------");

        $regimenes_comprobante = $this
                ->getRenglonesComprobantesAgrupadosByRegimen(ConstanteTipoImpuesto::SUSS, $ordenPagoComprobante->getComprobantes());

        $this->retencionSUSSProveedor($proveedor, $ordenPagoComprobante, $regimenes_comprobante);
        $this->logger->info("FIN RETENCION SUSS------------------------------------------------------------------------------");
    }

    /**
     * 
     * @param Proveedor $proveedor
     * @param OrdenPagoComprobante $ordenPagoComprobante
     */
    private function retencionIVA(Proveedor $proveedor, $ordenPagoComprobante) {

        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("RETENCIÓN IVA");
        $this->logger->info("------------------------------------------------------------------------------");

        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $proveedor, false);

        $condicionIva = $proveedor->getClienteProveedor()->getCondicionIVA();

        // Si el Proveedor es pasible de retención y está inscripto en IVA
        if ($condicionIva->getDenominacionTipoResponsable() == ConstanteTipoResponsable::INSCRIPTO) {

            if ($proveedor->getPasibleRetencionIVA()) {

                $estadoGenerado = $this->getEstadoComprobanteRetencionImpuestoGenerado();

                $situacionProveedor = $proveedor->getClienteProveedor()->getSituacionClienteProveedor();

                $renglonComprobanteByAlicuotaIva = $this->getRenglonComprobanteAgrupadosByAlicuotaIva($ordenPagoComprobante);

                // Si la situación del Proveedor es 2, 3 o 5
                if ($situacionProveedor->getAplicaImpuestoIVA()) {

                    $totalNeto = 0;

                    // Obtener RegimenRetencion RG 2854 - Art 9
                    $regimenRetencion = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_ART_9_INC_A);

                    $netoAcumuladoParaComprobante = 0;

                    foreach ($renglonComprobanteByAlicuotaIva as $renglonComprobante) {
                        $netoConExencion = $this->actualizarNetoPorRegimen($renglonComprobante['total'], $proveedor, ConstanteTipoImpuesto::IVA);
                        $netoAcumuladoParaComprobante += $netoConExencion;
                        $totalNeto = $netoConExencion * $renglonComprobante['alicuota'] / 100;
                    }

                    // Generar ComprobanteRetencion;
                    if ($totalNeto > 0) {
						
						$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPagoComprobante);
						
						$montoRetencionPagoParcial = 0;
						foreach($pagosParciales as $pagoParcial) {
							$montoRetencionPagoParcial += $pagoParcial->getRetencionIva();
						}
						
						$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
						
						// Si tiene pagos parciales, se lo resto del calculo
						$totalNeto -= $montoRetencionPagoParcial;
                        
                        $epsilon = 0.01;
                                
                        if (abs($totalNeto) <= $epsilon) {
                            $totalNeto = 0;
                        }
						
						$this->logger->info("Total final: " . $totalNeto);
						
                        $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                $ordenPagoComprobante, //
                                $regimenRetencion, //
                                $estadoGenerado, //
                                $totalNeto, //
                                $netoAcumuladoParaComprobante
                        );

                        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $proveedor, true);

                        $ordenPagoComprobante->addRetencion($comprobanteRetencion);
                    }
                } else {
                    // Si hay renglones agrupados al 10.5 %
                    if (isset($renglonComprobanteByAlicuotaIva[floatval(ConstanteAlicuotaIva::ALICUOTA_10_5)])) {
                        $renglonComprobante = $renglonComprobanteByAlicuotaIva[floatval(ConstanteAlicuotaIva::ALICUOTA_10_5)];

                        /* @var $regimenRetencion RegimenRetencion */
                        $regimenRetencion = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_ART_8_INC_B);

                        $netoAcumuladoParaComprobante = $this->actualizarNetoPorRegimen($renglonComprobante['total'], $proveedor, ConstanteTipoImpuesto::IVA);


                        $totalNeto = $netoAcumuladoParaComprobante * $regimenRetencion->getAlicuota() / 100;
						
						$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPagoComprobante);
						
						$montoRetencionPagoParcial = 0;
						foreach($pagosParciales as $pagoParcial) {
							$montoRetencionPagoParcial += $pagoParcial->getRetencionIva();
						}
						
						$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
						
						// Si tiene pagos parciales, se lo resto del calculo
						$totalNeto -= $montoRetencionPagoParcial;
                        
                        $epsilon = 0.01;
                                
                        if (abs($totalNeto) <= $epsilon) {
                            $totalNeto = 0;
                        }
						
						$this->logger->info("Total final: " . $totalNeto);
						
                        $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                $ordenPagoComprobante, //
                                $regimenRetencion, //
                                $estadoGenerado, //
                                $totalNeto, //
                                $netoAcumuladoParaComprobante
                        );

                        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $proveedor, true);

                        $ordenPagoComprobante->addRetencion($comprobanteRetencion);
                    }

                    $regimenes_comprobante = $this->getRenglonesComprobantesAgrupadosByRegimen(ConstanteTipoImpuesto::IVA, $ordenPagoComprobante->getComprobantes(), true);

                    foreach ($regimenes_comprobante as $regimen) {

                        //aplico porcentaje exencion
                        $regimen['total'] = $this->actualizarNetoPorRegimen($regimen['total'], $proveedor, ConstanteTipoImpuesto::IVA);

                        $netoAcumuladoParaComprobante = $regimen['total'];

                        $this->logger->info($regimen['total']);

                        $regimenRetencion = $regimen['regimen'];
                        $this->logger->info($regimenRetencion->getDenominacion());

                        $importeResultante = $regimen['total'] * $regimenRetencion->getAlicuota() / 100;

                        if ($importeResultante >= $regimenRetencion->getMinimoRetencion()) {
							
							$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPagoComprobante);
							
							$montoRetencionPagoParcial = 0;
							foreach($pagosParciales as $pagoParcial) {
								$montoRetencionPagoParcial += $pagoParcial->getRetencionIva();
							}
							
							$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
							
							// Si tiene pagos parciales, se lo resto del calculo
							$importeResultante -= $montoRetencionPagoParcial;
                            
                            $epsilon = 0.01;
                                
                            if (abs($importeResultante) <= $epsilon) {
                                $importeResultante = 0;
                            }
							
							$this->logger->info("Total final: " . $importeResultante);
					
                            $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                    $ordenPagoComprobante, //
                                    $regimenRetencion, //
                                    $estadoGenerado, //
                                    $importeResultante, //
                                    $netoAcumuladoParaComprobante
                            );

                            $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $proveedor, true);

                            $ordenPagoComprobante->addRetencion($comprobanteRetencion);
                        }
                    }
                }
            } else {
                $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $proveedor, true);
            }
        }
    }

    /**
     * 
     * @param type $proveedorId
     * @param type $regimenRetencion
     * @param type $anio
     * @param type $mes
     * @return type
     */
    private function getMontoTotalComprobanteRetencionByRegimenProveedorYFecha($tipoRetencion, $proveedorId, $regimenRetencion, $anio, $mes = null) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $total = 0;

        $comprobantesRetencion = $emContable
                ->getRepository('ADIFContableBundle:ComprobanteRetencionImpuesto' . $tipoRetencion)
                ->getComprobanteRetencionByRegimenProveedorYFecha(
                $proveedorId, $regimenRetencion, $anio, $mes
        );

        foreach ($comprobantesRetencion as $comprobanteRetencion) {
            $total += $comprobanteRetencion->getMonto();
        }

        return $total;
    }

    /**
     * 
     * @param type $ordenPagoComprobante
     * @param RegimenRetencion $regimenRetencion
     * @param type $estadoGenerado
     * @param type $monto
     * @param type $idProveedorUTE
     * @return type
     */
    private function nuevoComprobanteRetencion (
		$ordenPagoComprobante, 
		RegimenRetencion $regimenRetencion, 
		$estadoGenerado, 
		$monto, 
		$netoAcumuladoParaComprobante, 
		$idProveedorUTE = null,
		$baseImponibleGananciasUte = null
	) {

        $comprobanteRetencion = $ordenPagoComprobante->getComprobanteRetencion($idProveedorUTE, $this->esComprobanteObraPP);

        $comprobanteRetencion->setOrdenPago($ordenPagoComprobante);

        $comprobanteRetencion->setRegimenRetencion($regimenRetencion);
        $comprobanteRetencion->setEstadoComprobanteRetencionImpuesto($estadoGenerado);
        $comprobanteRetencion->setMonto($monto);
        $comprobanteRetencion->setBaseImponible($netoAcumuladoParaComprobante);
		$comprobanteRetencion->setBaseImponibleGananciasUte($baseImponibleGananciasUte);

        // Creo el Renglon de DDJJ asociado
        $renglonDDJJ = $this->crearRenglonDeclaracionJurada($comprobanteRetencion, $monto);

        $comprobanteRetencion->setRenglonDeclaracionJurada($renglonDDJJ);

        return $comprobanteRetencion;
    }

    /**
     * 
     * @param ComprobanteRetencionImpuesto $comprobanteRetencion
     * @param type $monto
     * @return RenglonDeclaracionJuradaComprobanteRetencionImpuesto
     */
    private function crearRenglonDeclaracionJurada(ComprobanteRetencionImpuesto $comprobanteRetencion, $monto) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        // Creo el Renglon de DDJJ asociado
        $renglonDDJJ = new RenglonDeclaracionJuradaComprobanteRetencionImpuesto();

        $renglonDDJJ->setComprobanteRetencionImpuesto($comprobanteRetencion);

        $renglonDDJJ->setFecha(new \DateTime());
        $renglonDDJJ->setTipoRenglonDeclaracionJurada(
                $emContable->getRepository('ADIFContableBundle:TipoRenglonDeclaracionJurada')
                        ->findOneByCodigo(ConstanteTipoRenglonDeclaracionJurada::COMPROBANTE_RETENCION_IMPUESTO_COMPRA)
        );

        $renglonDDJJ->setTipoImpuesto($comprobanteRetencion->getRegimenRetencion()->getTipoImpuesto());

        $renglonDDJJ->setEstadoRenglonDeclaracionJurada($emContable->getRepository('ADIFContableBundle:EstadoRenglonDeclaracionJurada')
                        ->findOneByDenominacion(ConstanteEstadoRenglonDeclaracionJurada::PENDIENTE));

        $renglonDDJJ->setMonto($monto);
        $renglonDDJJ->setMontoOriginal($monto);

        return $renglonDDJJ;
    }

    /**
     * 
     * @param type $ordenPagoComprobante
     * @return type
     */
    private function getRenglonComprobanteAgrupadosByAlicuotaIva($ordenPagoComprobante) {

        $renglonesByAlicuotaIvaArray = array();

        // Por cada comprobante asociado a la OP
        foreach ($ordenPagoComprobante->getComprobantes() as $comprobante) {

            // Por cada RenglonComprobate del Comprobante
            foreach ($comprobante->getRenglonesComprobante() as $renglonComprobante) {

                $alicuotaIva = $renglonComprobante->getAlicuotaIva();

                $renglonesComprobante = isset($renglonesByAlicuotaIvaArray[floatval($alicuotaIva->getValor())]) //
                        ? $renglonesByAlicuotaIvaArray[floatval($alicuotaIva->getValor())]['renglones'] //
                        : array();

                $renglonesComprobante[] = $renglonComprobante;

                $montoRenglon = $renglonComprobante->getMontoAdicionalProrrateadoDiscriminado();

                if (!isset($renglonesByAlicuotaIvaArray[floatval($alicuotaIva->getValor())]['total'])) {
                    $renglonesByAlicuotaIvaArray[floatval($alicuotaIva->getValor())]['total'] = 0;
                }

                $renglonesByAlicuotaIvaArray[floatval($alicuotaIva->getValor())]['total'] += ($montoRenglon['neto'] * ($comprobante->getEsNotaCredito() ? -1 : 1));

                $renglonesByAlicuotaIvaArray[floatval($alicuotaIva->getValor())] = array(
                    'alicuota' => $alicuotaIva->getValor(),
                    'renglones' => $renglonesComprobante,
                    'total' => $renglonesByAlicuotaIvaArray[floatval($alicuotaIva->getValor())]['total']
                );
            }
        }

        return $renglonesByAlicuotaIvaArray;
    }

    /**
     * 
     * @param type $constanteImpuesto
     * @param type $comprobantes
     * @return type
     */
    private function getRenglonesComprobantesAgrupadosByRegimen($constanteImpuesto, $comprobantes, $filtroIva = false) {

        $regimenes_comprobante = array();

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        // Por cada comprobante asociado a la OP
        foreach ($comprobantes as $comprobante) {
			$comprobanteDetalle = "------COMPROBANTE ID " . $comprobante->getId();
			$comprobanteDetalle .= ' COMPROBANTE ' . $comprobante->getTipoComprobante()->getNombre();
			$comprobanteDetalle .= ($comprobante->getLetraComprobante() != null)
				? ' (' . $comprobante->getLetraComprobante()->getLetra() . ') '
				: ' ';
			$comprobanteDetalle .= $comprobante->getNumeroCompleto();
			
			$this->logger->info($comprobanteDetalle);

            // Por cada RenglonComprobate del Comprobante
            foreach ($comprobante->getRenglonesComprobante() as $renglonComprobante) {

                $renglonCompleto = $emContable->getRepository('ADIFContableBundle:RenglonComprobante')->find($renglonComprobante->getId());

                if (!$filtroIva || $renglonCompleto->getAlicuotaIva()->getValor() != 10.5) {

                    $bienEconomico = $this->getBienEconomicoFromRenglonOrdenCompra($renglonComprobante);

                    /* @var $regimenRetencion RegimenRetencion */
                    $regimenRetencion = $this->getRegimenRetencionByImpuestoYBienEconomico($constanteImpuesto, $bienEconomico->getId());

                    if ($this->getAplicaRegimenRetencion($regimenRetencion)) {

                        $renglones_regimen = isset($regimenes_comprobante[$regimenRetencion->getId()]) //
                                ? $regimenes_comprobante[$regimenRetencion->getId()]['renglones'] //
                                : array();

                        $renglones_regimen[] = $renglonComprobante;

                        $montoRenglon = $renglonComprobante->getMontoAdicionalProrrateadoDiscriminado();

                        if (!isset($regimenes_comprobante[$regimenRetencion->getId()]['total'])) {
                            $regimenes_comprobante[$regimenRetencion->getId()]['total'] = 0;
                        }

                        $regimenes_comprobante[$regimenRetencion->getId()]['total'] += ($montoRenglon['neto'] * ($comprobante->getEsNotaCredito() ? -1 : 1));

                        $regimenes_comprobante[$regimenRetencion->getId()] = array(
                            'regimen' => $regimenRetencion,
                            'renglones' => $renglones_regimen,
                            'total' => $regimenes_comprobante[$regimenRetencion->getId()]['total'],
                            'acumulado' => 0
                        );
                    }
                }
            }
        }



        return $regimenes_comprobante;
    }

    /**
     * 
     * @param RenglonComprobanteCompra $renglonComprobante
     * @return BienEconomico
     */
    private function getBienEconomicoFromRenglonOrdenCompra($renglonComprobante) {

        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());

        /* @var $renglonComprobante RenglonComprobanteCompra */
        $renglonOrdenCompra = $emCompras->getRepository('ADIFComprasBundle:RenglonOrdenCompra')
                ->find($renglonComprobante->getIdRenglonOrdenCompra());

        return $renglonOrdenCompra->getBienEconomico();
    }

    /////////////////////////////////
    //////////////OBRAS//////////////
    /////////////////////////////////

    public function generarComprobantesRetencionObras(OrdenPagoObra $ordenPagoObra) {

        /* @var $proveedor Proveedor */
        $proveedor = $ordenPagoObra->getProveedor();

        $this->logger->info("CÁLCULO RETENCIONES " . date('d/m/Y H:i:s'));
        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("Proveedor : " . $proveedor);
		$this->logger->info("Id Proveedor : " . $proveedor->getId());
        $this->logger->info("------------------------------------------------------------------------------");

        // Si el Proveedor NO es extranjero
        if (!$proveedor->getClienteProveedor()->getEsExtranjero()) {

            $this->retencionGananciasObras($proveedor, $ordenPagoObra);

            $this->retencionIIBBObras($proveedor, $ordenPagoObra);

            $this->retencionIVAObras($proveedor, $ordenPagoObra);

            $this->retencionSUSSObras($proveedor, $ordenPagoObra);

            return $this->erroresExencion;
        }
    }

    /**
     * 
     * @param Proveedor $proveedor
     * @param OrdenPagoObra $ordenPagoObra
     */
    public function retencionGananciasObras(Proveedor $proveedor, $ordenPagoObra) {
        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("RETENCIÓN GANANCIAS");
        $this->logger->info("------------------------------------------------------------------------------");

        $regimenes_comprobante = $this
                ->getRenglonesComprobantesObraAgrupadosByRegimen(ConstanteTipoImpuesto::Ganancias, $ordenPagoObra->getComprobantes());

        $this->retencionGananciasProveedor($proveedor, $ordenPagoObra, $regimenes_comprobante);
        $this->logger->info("FIN RETENCION GANANCIAS-------------------------------------------------------");
    }

    /**
     * 
     * @param type $constanteImpuesto
     * @param type $comprobantes
     * @return type
     */
    private function getRenglonesComprobantesObraAgrupadosByRegimen($constanteImpuesto, $comprobantes, $filtroIva = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $regimenes_comprobante = array();

        // Por cada comprobante asociado a la OP
        foreach ($comprobantes as $comprobante) {
			$comprobanteDetalle = "------COMPROBANTE ID " . $comprobante->getId();
			$comprobanteDetalle .= ' COMPROBANTE ' . $comprobante->getTipoComprobante()->getNombre();
			$comprobanteDetalle .= ($comprobante->getLetraComprobante() != null)
				? ' (' . $comprobante->getLetraComprobante()->getLetra() . ') '
				: ' ';
			$comprobanteDetalle .= $comprobante->getNumeroCompleto();
			
			$this->logger->info($comprobanteDetalle);

            /* @var $renglonComprobante \ADIF\ContableBundle\Entity\Obras\RenglonComprobanteObra */
            
            foreach ($comprobante->getRenglonesComprobante() as $renglonComprobante) {
                if (!$filtroIva || $renglonComprobante->getAlicuotaIva()->getValor() != 10.5) {
                    switch ($constanteImpuesto) {
                        case ConstanteTipoImpuesto::Ganancias:
                            $regimenRetencion = $renglonComprobante->getRegimenRetencionGanancias();
                            break;
                        case ConstanteTipoImpuesto::IIBB:
                            $regimenRetencion = $renglonComprobante->getRegimenRetencionIIBB();
                            break;
                        case ConstanteTipoImpuesto::SUSS:
                            $regimenRetencion = $renglonComprobante->getRegimenRetencionSUSS();
                            break;
                        case ConstanteTipoImpuesto::IVA:
                            $regimenRetencion = $renglonComprobante->getRegimenRetencionIVA();
                            break;
                    }
                    
                    if ($regimenRetencion == null) {
                        $tipoDocumentoFinanciero = $renglonComprobante->getTipoDocumentoFinanciero();
                        if ($renglonComprobante->getComprobante()->getTramo()->getLicitacion()->getId() == 67) {
                            switch ($constanteImpuesto) {
                                case ConstanteTipoImpuesto::Ganancias:
                                    $regimenRetencion = $emContable
                                            ->getRepository('ADIFContableBundle:RegimenRetencion')
                                            ->find(10);
                                    break;
                                case ConstanteTipoImpuesto::IIBB:
                                    $regimenRetencion = $emContable
                                            ->getRepository('ADIFContableBundle:RegimenRetencion')
                                            ->find(26);
                                    break;
                                case ConstanteTipoImpuesto::SUSS:
                                    $regimenRetencion = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_RG_1784);

                                    break;
                                case ConstanteTipoImpuesto::IVA:
                                    $regimenRetencion = $tipoDocumentoFinanciero->getRegimenRetencionIVA();
                                    break;
                            }
                        } else {
                            $tipoObra = $renglonComprobante->getComprobante()->getTramo()->getTipoObra();
                            switch ($constanteImpuesto) {
                                case ConstanteTipoImpuesto::Ganancias:
                                    $regimenRetencion = ($tipoObra->getRegimenRetencionGanancias() == null) ? $tipoDocumentoFinanciero->getRegimenRetencionGanancias() : $tipoObra->getRegimenRetencionGanancias();
                                    break;
                                case ConstanteTipoImpuesto::IIBB:
                                    $regimenRetencion = ($tipoObra->getRegimenRetencionIIBB() == null) ? $tipoDocumentoFinanciero->getRegimenRetencionIIBB() : $tipoObra->getRegimenRetencionIIBB();
                                    break;
                                case ConstanteTipoImpuesto::SUSS:
                                    $regimenRetencion = $tipoObra->getRegimenRetencionSUSS();
                                    break;
                                case ConstanteTipoImpuesto::IVA:
                                    $regimenRetencion = ($tipoObra->getRegimenRetencionIVA() == null) ? $tipoDocumentoFinanciero->getRegimenRetencionIVA() : $tipoObra->getRegimenRetencionIVA();
                                    break;
                            }
                        }
                    }

					#if ($comprobante->getLetraComprobante()->getLetra() != 'Y') {
					
					$renglones_regimen = isset($regimenes_comprobante[$regimenRetencion->getId()]) //
							? $regimenes_comprobante[$regimenRetencion->getId()]['renglones'] //
							: array();

					$renglones_regimen[] = $renglonComprobante;

					$montoRenglon = $renglonComprobante->getMontoAdicionalProrrateadoDiscriminado();

					if (!isset($regimenes_comprobante[$regimenRetencion->getId()]['total'])) {
						$regimenes_comprobante[$regimenRetencion->getId()]['total'] = 0;
					}

					$regimenes_comprobante[$regimenRetencion->getId()]['total'] += ($montoRenglon['neto'] * ($comprobante->getEsNotaCredito() ? -1 : 1));

					$regimenes_comprobante[$regimenRetencion->getId()] = array(
						'regimen' => $regimenRetencion,
						'renglones' => $renglones_regimen,
						'total' => $regimenes_comprobante[$regimenRetencion->getId()]['total'],
						'acumulado' => 0,
                        'idComprobante' => $comprobante->getId()
					);
					
					#}	
                }
            }
        }

        return $regimenes_comprobante;
    }

    /**
     * 
     * @param Proveedor $proveedor
     * @param OrdenPagoComprobante $ordenPagoObra
     */
    private function retencionIIBBObras(Proveedor $proveedor, $ordenPagoObra) {

        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("RETENCIÓN IIBB");
        $this->logger->info("------------------------------------------------------------------------------");

        $regimenes_comprobante = $this
                ->getRenglonesComprobantesObraAgrupadosByRegimen(ConstanteTipoImpuesto::IIBB, $ordenPagoObra->getComprobantes());

        $this->retencionIIBBProveedor($proveedor, $ordenPagoObra, $regimenes_comprobante);
        $this->logger->info("FIN RETENCION IIBB-------------------------------------------------------");
    }

    /**
     * 
     * @param Proveedor $proveedor
     * @param OrdenPagoComprobante $ordenPagoObra
     */
    private function retencionIVAObras(Proveedor $proveedor, $ordenPagoObra) {

        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("RETENCIÓN IVA");
        $this->logger->info("------------------------------------------------------------------------------");

        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $proveedor, false);

        $condicionIva = $proveedor->getClienteProveedor()->getCondicionIVA();

        // Si el Proveedor es pasible de retención y está inscripto en IVA
        if ($condicionIva->getDenominacionTipoResponsable() == ConstanteTipoResponsable::INSCRIPTO) {

            if ($proveedor->getPasibleRetencionIVA()) {
                $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

                $estadoGenerado = $this->getEstadoComprobanteRetencionImpuestoGenerado();

                $situacionProveedor = $proveedor->getClienteProveedor()->getSituacionClienteProveedor();

                $renglonComprobanteByAlicuotaIva = $this->getRenglonComprobanteAgrupadosByAlicuotaIva($ordenPagoObra);

                // Si la situación del Proveedor es 2, 3 o 5
                if ($situacionProveedor->getAplicaImpuestoIVA()) {

                    $totalNeto = 0;

                    // Obtener RegimenRetencion RG 2854 - Art 9
                    $regimenRetencion = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_ART_9_INC_A);

                    $netoAcumuladoParaComprobante = 0;

                    foreach ($renglonComprobanteByAlicuotaIva as $renglonComprobante) {
                        $netoConExencion = $this->actualizarNetoPorRegimen($renglonComprobante['total'], $proveedor, ConstanteTipoImpuesto::IVA);
                        $netoAcumuladoParaComprobante += $netoConExencion;
                        $totalNeto = $netoConExencion * $renglonComprobante['alicuota'] / 100;
                    }

                    // Generar ComprobanteRetencion;
                    if ($totalNeto > 0) {
						
						$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPagoObra);
						
						$montoRetencionPagoParcial = 0;
						foreach($pagosParciales as $pagoParcial) {
							$montoRetencionPagoParcial += $pagoParcial->getRetencionIva();
						}
						
						$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
						
						// Si tiene pagos parciales, se lo resto del calculo
						$totalNeto -= $montoRetencionPagoParcial;
                        
                        $epsilon = 0.01;
                                
                        if (abs($totalNeto) <= $epsilon) {
                            $totalNeto = 0;
                        }
						
						$this->logger->info("Total final: " . $totalNeto);
						
                        $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                $ordenPagoObra, //
                                $regimenRetencion, //
                                $estadoGenerado, //
                                $totalNeto, //
                                $netoAcumuladoParaComprobante
                        );

                        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $proveedor, true);

                        $ordenPagoObra->addRetencion($comprobanteRetencion);
                    }
                } else {
                    // Si hay renglones agrupados al 10.5 %
                    if (isset($renglonComprobanteByAlicuotaIva[floatval(ConstanteAlicuotaIva::ALICUOTA_10_5)])) {
                        $renglonComprobante = $renglonComprobanteByAlicuotaIva[floatval(ConstanteAlicuotaIva::ALICUOTA_10_5)];

                        /* @var $regimenRetencion RegimenRetencion */
                        $regimenRetencion = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_ART_8_INC_B);

                        $netoAcumuladoParaComprobante = $this->actualizarNetoPorRegimen($renglonComprobante['total'], $proveedor, ConstanteTipoImpuesto::IVA);

                        $totalNeto = $netoAcumuladoParaComprobante * $regimenRetencion->getAlicuota() / 100;
						
						$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPagoObra);
						
						$montoRetencionPagoParcial = 0;
						foreach($pagosParciales as $pagoParcial) {
							$montoRetencionPagoParcial += $pagoParcial->getRetencionIva();
						}
						
						$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
						
						// Si tiene pagos parciales, se lo resto del calculo
						$totalNeto -= $montoRetencionPagoParcial;
                        
                        $epsilon = 0.01;
                                
                        if (abs($totalNeto) <= $epsilon) {
                            $totalNeto = 0;
                        }
						
						$this->logger->info("Total final: " . $totalNeto);
						
                        $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                $ordenPagoObra, //
                                $regimenRetencion, //
                                $estadoGenerado, //
                                $totalNeto, //
                                $netoAcumuladoParaComprobante
                        );

                        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $proveedor, true);

                        $ordenPagoObra->addRetencion($comprobanteRetencion);
                    }

                    $regimenes_comprobante = $this->getRenglonesComprobantesObraAgrupadosByRegimen(ConstanteTipoImpuesto::IVA, $ordenPagoObra->getComprobantes(), true);

                    foreach ($regimenes_comprobante as $regimen) {

                        //aplico porcentaje exencion
                        $regimen['total'] = $this->actualizarNetoPorRegimen($regimen['total'], $proveedor, ConstanteTipoImpuesto::IVA);

                        $netoAcumuladoParaComprobante = $regimen['total'];

                        $this->logger->info($regimen['total']);

                        $regimenRetencion = $regimen['regimen'];
                        $this->logger->info($regimenRetencion->getDenominacion());

                        $importeResultante = $regimen['total'] * $regimenRetencion->getAlicuota() / 100;

                        if ($importeResultante >= $regimenRetencion->getMinimoRetencion()) {
							
							$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPagoObra);
							
							$montoRetencionPagoParcial = 0;
							foreach($pagosParciales as $pagoParcial) {
								$montoRetencionPagoParcial += $pagoParcial->getRetencionIva();
							}
							
							$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
							
							// Si tiene pagos parciales, se lo resto del calculo
							$importeResultante -= $montoRetencionPagoParcial;
                            
                            $epsilon = 0.01;
                                
                            if (abs($importeResultante) <= $epsilon) {
                                $importeResultante = 0;
                            }
							
							$this->logger->info("Total final: " . $importeResultante);
							
                            $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                    $ordenPagoObra, //
                                    $regimenRetencion, //
                                    $estadoGenerado, //
                                    $importeResultante, //
                                    $netoAcumuladoParaComprobante
                            );

                            $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $proveedor, true);

                            $ordenPagoObra->addRetencion($comprobanteRetencion);
                        }
                    }
                }
            } else {
                $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $proveedor, true);
            }
        }
    }

    /**
     * 
     * @param Proveedor $proveedor
     * @param OrdenPagoObras $ordenPagoObra
     */
    private function retencionSUSSObras(Proveedor $proveedor, $ordenPagoObra) {

        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("RETENCIÓN SUSS");
        $this->logger->info("------------------------------------------------------------------------------");

        $regimenes_comprobante = $this->getRenglonesComprobantesObraAgrupadosByRegimen(ConstanteTipoImpuesto::SUSS, $ordenPagoObra->getComprobantes());

        $this->retencionSUSSProveedor($proveedor, $ordenPagoObra, $regimenes_comprobante);
        $this->logger->info("FIN RETENCION SUSS------------------------------------------------------------------------------");
    }

    /////////////////////////////////
    ///////////Consultoria///////////
    /////////////////////////////////

    public function generarComprobantesRetencionConsultoria(OrdenPagoConsultoria $ordenPagoConsultoria) {

        /* @var $consultor Consultor */
        $consultor = $ordenPagoConsultoria->getContrato()->getConsultor();

        $this->logger->info("CÁLCULO RETENCIONES " . date('d/m/Y H:i:s'));
        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("Consultor : " . $consultor);
        $this->logger->info("------------------------------------------------------------------------------");

        $this->retencionGananciasConsultoria($consultor, $ordenPagoConsultoria);

        $this->retencionIIBBConsultoria($consultor, $ordenPagoConsultoria);

        $this->retencionIVAConsultoria($consultor, $ordenPagoConsultoria);

        $this->retencionSUSSConsultoria($consultor, $ordenPagoConsultoria);

        return $this->erroresExencion;
    }

    /**
     * 
     * @param Consultor $consultor
     * @param OrdenPagoComprobante $ordenPagoConsultoria
     */
    public function retencionGananciasConsultoria(Consultor $consultor, OrdenPagoConsultoria $ordenPagoConsultoria) {

        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("RETENCIÓN GANANCIAS");
        $this->logger->info("------------------------------------------------------------------------------");

        $this->actualizarErroresExencion(ConstanteTipoImpuesto::Ganancias, $consultor, false);

        /* @var $datosImpositivos \ADIF\ComprasBundle\Entity\DatosImpositivos */

        $datosImpositivos = $emCompras->getRepository('ADIFComprasBundle:DatosImpositivos')->find($consultor->getIdDatosImpositivos());

        $condicionGanancias = $emContable->getRepository('ADIFContableBundle:TipoResponsable')->find($datosImpositivos->getIdCondicionGanancias());

        // Si el Consultor es pasible de retencion y está inscripto en Ganancias
        if ($condicionGanancias->getDenominacionTipoResponsable() == ConstanteTipoResponsable::INSCRIPTO) {

            if ($consultor->getPasibleRetencionGanancias()) {

                $estadoGenerado = $this->getEstadoComprobanteRetencionImpuestoGenerado();

                $anioActual = (new DateTime())->format("Y");
                $mesActual = (new DateTime())->format("m");

                $regimenes_comprobante = array();

                $bienEconomico = $this->getBienEconomicoFromComprobanteConsultoria($ordenPagoConsultoria->getComprobantes()->first());

                $regimenRetencion = $this->getRegimenRetencionByImpuestoYBienEconomico(ConstanteTipoImpuesto::Ganancias, $bienEconomico->getId());

                if ($this->getAplicaRegimenRetencion($regimenRetencion)) {

                    $this->logger->info("REGIMEN: " . $regimenRetencion->getDenominacion());
                    $this->logger->info("------------------------------------------------------------------------------");

                    $regimenes_comprobante[$regimenRetencion->getId()]['acumulado'] = 0;
                    $regimenes_comprobante[$regimenRetencion->getId()]['regimen'] = $regimenRetencion;

                    foreach ($ordenPagoConsultoria->getComprobantes() as $comprobante) {
                        $regimenes_comprobante[$regimenRetencion->getId()]['acumulado'] += ($comprobante->getTotalNeto() * ($comprobante->getEsNotaCredito() ? -1 : 1));
                    }

                    $comprobantesFiltrados = $emContable
                            ->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')
                            ->getComprobanteConsultoriaByConsultorYFecha($consultor->getId(), $anioActual, $mesActual);

                    foreach ($comprobantesFiltrados as $comprobante) {
                        $regimenes_comprobante[$regimenRetencion->getId()]['acumulado'] += ($comprobante->getTotalNeto() * ($comprobante->getEsNotaCredito() ? -1 : 1));
                    }

                    foreach ($regimenes_comprobante as $regimen) {

                        $importeResultante = $regimen['acumulado'];
                        $regimenRetencion = $regimen['regimen'];

                        // Obtengo el mínimo no imponible del RegimenRetencion
                        $minimoNoImponible = $regimenRetencion->getMinimoNoImponible();

                        // Si el RegimenRetención NO utiliza tablas auxiliares
                        if (!$regimenRetencion->getUsaTabla()) {
                            $this->logger->info("NO USA TABLA");
                            $this->logger->info("------------------------------------------------------------------------------");
                            // Obtengo la alicuota del RegimenRetencion
                            $alicuota = $regimenRetencion->getAlicuota();

                            $montoARetener = 0;
                        } else {

                            $importeResultante -= $minimoNoImponible;

							if ($importeResultante <= 0) {
								$importeResultante = 0;
							}
							
                            /* @var $escalaRetencionHonorarioGanancia EscalaRetencionHonorariosGanancias */
                            $escalaRetencionHonorarioGanancia = $emContable
                                    ->getRepository('ADIFContableBundle:EscalaRetencionHonorariosGanancias')
                                    ->getEscalaRetencionHonorariosGananciasByMonto($importeResultante);

                            // Obtengo el mínimo no imponible del EscalaRetencionHonorariosGanancias
                            $minimoNoImponible = $escalaRetencionHonorarioGanancia->getMinimoNoImponible();

                            // Obtengo la alicuota del EscalaRetencionHonorariosGanancias
                            $alicuota = $escalaRetencionHonorarioGanancia->getAlicuota();

                            // Obtengo el monto a retener del EscalaRetencionHonorariosGanancias
                            $montoARetener = $escalaRetencionHonorarioGanancia
                                    ->getMontoARetener();

                            $this->logger->info("USA TABLA");
                            $this->logger->info("IMPORTE RESULTANTE: " . $importeResultante);
                            $this->logger->info("MINIMO NO IMPONIBLE: " . $minimoNoImponible);
                            $this->logger->info("------------------------------------------------------------------------------");
                        }

                        // Calculo el importe
                        $importeCalculado = ($importeResultante - $minimoNoImponible) * $alicuota / 100 + $montoARetener;

                        // Al importe resultante le resto lo que ya le había retenido en dicho mes
                        $importeCalculado -= $this
                                ->getMontoTotalComprobanteRetencionByRegimenProveedorYFecha('Consultoria', $consultor->getId(), $regimenRetencion, $anioActual, $mesActual);

                        $this->logger->info("IMPORTE CALCULADO: " . $importeCalculado);
                        $this->logger->info("------------------------------------------------------------------------------");
                        $this->logger->info("MINIMO RETENCION: " . $regimenRetencion->getMinimoRetencion());
                        $this->logger->info("------------------------------------------------------------------------------");

                        // Si el resultado es mayor al mínimo exento, se crea el comprobante de retención
                        if ($importeCalculado > $regimenRetencion->getMinimoRetencion()) {

                            $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                    $ordenPagoConsultoria, //
                                    $regimenRetencion, //
                                    $estadoGenerado, //
                                    $importeCalculado, //
                                    0
                            );

                            $this->actualizarErroresExencion(ConstanteTipoImpuesto::Ganancias, $consultor, true);

                            $ordenPagoConsultoria->addRetencion($comprobanteRetencion);
                        }
                    }
                }
            } else {
                $this->actualizarErroresExencion(ConstanteTipoImpuesto::Ganancias, $consultor, true);
            }
        }

        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("FIN RETENCIÓN GANANCIAS");
    }

    /**
     * 
     * @param type $comprobanteConsultoria
     * @return type
     */
    private function getBienEconomicoFromComprobanteConsultoria($comprobanteConsultoria) {
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $esHonorarioProfesional = $comprobanteConsultoria->getContrato()->getEsHonorarioProfesional();

        if ($esHonorarioProfesional) {

            $bienEconomico = $emCompras->getRepository('ADIFComprasBundle:BienEconomico')
                    ->findOneByCodigoInterno(ConstanteCodigoInternoBienEconomico::HONORARIO_PROFESIONAL);
        } else {
            $bienEconomico = $emCompras->getRepository('ADIFComprasBundle:BienEconomico')
                    ->findOneByCodigoInterno(ConstanteCodigoInternoBienEconomico::HONORARIO_NO_PROFESIONAL);
        }

        return $bienEconomico;
    }

    /**
     * 
     * @param Consultor $consultor
     * @param OrdenPagoConsultoria $ordenPagoConsultoria
     */
    private function retencionIIBBConsultoria(Consultor $consultor, OrdenPagoConsultoria $ordenPagoConsultoria) {

        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("RETENCIÓN IIBB");
        $this->logger->info("------------------------------------------------------------------------------");

        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IIBB, $consultor, false);

        // Si el Consultor es pasible de retencion de IIBB
        if ($consultor->getPasibleRetencionIngresosBrutos()) {

            // Verifico si el monto de los comprobantes superan el mínimo
            $totalNetoComprobantes = 0;

            foreach ($ordenPagoConsultoria->getComprobantes() as $comprobante) {
                $this->logger->info("Monto renglón: " . ($comprobante->getTotalNeto() * ($comprobante->getEsNotaCredito() ? -1 : 1)));
                $totalNetoComprobantes += ($comprobante->getTotalNeto() * ($comprobante->getEsNotaCredito() ? -1 : 1));
            }

            $recorroBienes = true;

            if ($totalNetoComprobantes >= 300) {

                $estadoGenerado = $this->getEstadoComprobanteRetencionImpuestoGenerado();

                $porcentajeCorrespondiente = 100;

                // Si el Consultor es Convenio Multilateral
                if ($consultor->getDatosImpositivos()->getCondicionIngresosBrutos()
                                ->getDenominacionTipoResponsable() == ConstanteTipoResponsable::CONVENIO_MULTILATERAL) {

                    $convenioMultilateral = $consultor->getDatosImpositivos()
                            ->getConvenioMultilateralIngresosBrutos();

                    if (null != $convenioMultilateral) {
                        // Aplico porcentaje CABA
                        $porcentajeCorrespondiente = $convenioMultilateral->getPorcentajeAplicacionCABA();
                    }
                }

                // Si el Consultor tiene riesgo fiscal
                if ($consultor->getDatosImpositivos()->getTieneRiesgoFiscal()) {

                    // Aplico Regimen correspondiente al Riesgo Fiscal

                    /* @var $regimenRetencion RegimenRetencion */
                    $regimenRetencion = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_RIESGO_FISCAL);

                    $montoTotal = ($totalNetoComprobantes * $porcentajeCorrespondiente / 100) //
                            * $regimenRetencion->getAlicuota() / 100;

                    $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                            $ordenPagoConsultoria, //
                            $regimenRetencion, //
                            $estadoGenerado, //
                            $montoTotal, //
                            0
                    );

                    $this->actualizarErroresExencion(ConstanteTipoImpuesto::IIBB, $consultor, true);

                    $ordenPagoConsultoria->addRetencion($comprobanteRetencion);

                    $recorroBienes = false;
                } else {
                    // Si el Proveedor es Monotributista
                    if ($consultor->getDatosImpositivos()->getCondicionIVA()
                                    ->getDenominacionTipoResponsable() == ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO) {

                        // Me fijo si está en la base de Magnitudes Superadas
                        if ($consultor->getDatosImpositivos()->getIncluyeMagnitudesSuperadas()) {

                            // Aplico Regimen correspondiente a Magnitudes Superadas

                            /* @var $regimenRetencion RegimenRetencion */
                            $regimenRetencion = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_MAGNITUDES_SUPERADAS);

                            $montoTotal = ($totalNetoComprobantes * $porcentajeCorrespondiente / 100) //
                                    * $regimenRetencion->getAlicuota() / 100;

                            $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                    $ordenPagoConsultoria, //
                                    $regimenRetencion, //
                                    $estadoGenerado, //
                                    $montoTotal, //
                                    0
                            );

                            $this->actualizarErroresExencion(ConstanteTipoImpuesto::IIBB, $consultor, false);

                            $ordenPagoConsultoria->addRetencion($comprobanteRetencion);

                            $recorroBienes = false;
                        }
                    }
                }
            } else {
                $recorroBienes = false;
                $this->logger->info("El total de los comprobantes no excede el mínimo exento");
            }

            if ($recorroBienes) {

                $bienEconomico = $this->getBienEconomicoFromComprobanteConsultoria($ordenPagoConsultoria->getComprobantes()->first());

                $regimenRetencion = $this->getRegimenRetencionByImpuestoYBienEconomico(ConstanteTipoImpuesto::IIBB, $bienEconomico->getId());

                if ($this->getAplicaRegimenRetencion($regimenRetencion)) {

                    /* Obtengo el monto y le aplico el porcentaje correspondiente */
                    $totalNetoComprobantes *= $porcentajeCorrespondiente / 100;

                    $totalNetoComprobantes *= $regimenRetencion->getAlicuota() / 100;

                    $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                            $ordenPagoConsultoria, //
                            $regimenRetencion, //
                            $estadoGenerado, //
                            $totalNetoComprobantes, //
                            0
                    );

                    $this->actualizarErroresExencion(ConstanteTipoImpuesto::IIBB, $consultor, false);

                    $ordenPagoConsultoria->addRetencion($comprobanteRetencion);
                }
            }
        } else {
            $this->actualizarErroresExencion(ConstanteTipoImpuesto::IIBB, $consultor, false);
            $this->logger->info("El consultor no es pasible de retención");
        }
    }

    /**
     * 
     * @param Consultor $consultor
     * @param OrdenPagoConsultoria $ordenPagoConsultoria
     */
    private function retencionIVAConsultoria(Consultor $consultor, OrdenPagoConsultoria $ordenPagoConsultoria) {

        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("RETENCIÓN IVA");
        $this->logger->info("------------------------------------------------------------------------------");

        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $consultor, false);

        $condicionIva = $consultor->getDatosImpositivos()->getCondicionIVA();

        // Si el Consultor es pasible de retención y está inscripto en IVA
        if ($condicionIva->getDenominacionTipoResponsable() == ConstanteTipoResponsable::INSCRIPTO) {

            if ($consultor->getPasibleRetencionIVA()) {

                $estadoGenerado = $this->getEstadoComprobanteRetencionImpuestoGenerado();

                $situacionConsultor = $consultor->getDatosImpositivos()->getSituacionClienteProveedor();
                
                $renglonComprobanteByAlicuotaIva = $this->getRenglonComprobanteAgrupadosByAlicuotaIva($ordenPagoConsultoria);

                // Si la situación del Proveedor es 2, 3 o 5
                if ($situacionConsultor != null && $situacionConsultor->getAplicaImpuestoIVA()) {

                    $totalNeto = 0;

                    // Obtener RegimenRetencion RG 2854 - Art 9
                    $regimenRetencion = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_ART_9_INC_A);

                    foreach ($renglonComprobanteByAlicuotaIva as $renglonComprobante) {
                        $totalNeto = $renglonComprobante['total'] * $renglonComprobante['alicuota'] / 100;
                    }

                    // Generar ComprobanteRetencion;
                    if ($totalNeto > 0) {
                        $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                $ordenPagoConsultoria, //
                                $regimenRetencion, //
                                $estadoGenerado, //
                                $totalNeto, //
                                0
                        );

                        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $consultor, true);

                        $ordenPagoConsultoria->addRetencion($comprobanteRetencion);
                    }
                } else {
                    // Si hay renglones agrupados al 10.5 %
                    if (isset($renglonComprobanteByAlicuotaIva[floatval(ConstanteAlicuotaIva::ALICUOTA_10_5)])) {
                        $renglonComprobante = $renglonComprobanteByAlicuotaIva[floatval(ConstanteAlicuotaIva::ALICUOTA_10_5)];

                        /* @var $regimenRetencion RegimenRetencion */
                        $regimenRetencion = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_ART_8_INC_B);

                        $totalNeto = $renglonComprobante['total'] * $regimenRetencion->getAlicuota() / 100;
                        $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                $ordenPagoConsultoria, //
                                $regimenRetencion, //
                                $estadoGenerado, //
                                $totalNeto, //
                                0
                        );

                        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $consultor, true);

                        $ordenPagoConsultoria->addRetencion($comprobanteRetencion);
                    }

                    $bienEconomico = $this->getBienEconomicoFromComprobanteConsultoria($ordenPagoConsultoria->getComprobantes()->first());

                    $regimenRetencion = $this->getRegimenRetencionByImpuestoYBienEconomico(ConstanteTipoImpuesto::IVA, $bienEconomico->getId());

                    $regimenes_comprobante = array();

                    if ($this->getAplicaRegimenRetencion($regimenRetencion)) {

                        $regimenes_comprobante[$regimenRetencion->getId()]['acumulado'] = 0;
                        $regimenes_comprobante[$regimenRetencion->getId()]['regimen'] = $regimenRetencion;

                        foreach ($ordenPagoConsultoria->getComprobantes() as $comprobante) {
                            $regimenes_comprobante[$regimenRetencion->getId()]['acumulado'] += ( $comprobante->getTotalNeto() * ($comprobante->getEsNotaCredito() ? -1 : 1));
                        }

                        foreach ($regimenes_comprobante as $regimen) {

                            $this->logger->info($regimen['acumulado']);

                            $regimenRetencion = $regimen['regimen'];
                            $this->logger->info($regimenRetencion->getDenominacion());

                            $importeResultante = $regimen['acumulado'] * $regimenRetencion->getAlicuota() / 100;

                            if ($importeResultante >= $regimenRetencion->getMinimoRetencion()) {
                                $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                        $ordenPagoConsultoria, //
                                        $regimenRetencion, //
                                        $estadoGenerado, //
                                        $importeResultante, //
                                        0
                                );

                                $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $consultor, true);

                                $ordenPagoConsultoria->addRetencion($comprobanteRetencion);
                            }
                        }
                    }
                }
            } else {
                $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $consultor, true);
            }
        }
    }

    /**
     * 
     * @param Consultor $consultor
     * @param OrdenPagoConsultoria $ordenPagoConsultoria
     */
    private function retencionSUSSConsultoria(Consultor $consultor, OrdenPagoConsultoria $ordenPagoConsultoria) {

        $this->logger->info("");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("RETENCIÓN SUSS");
        $this->logger->info("------------------------------------------------------------------------------");

        $this->actualizarErroresExencion(ConstanteTipoImpuesto::SUSS, $consultor, false);

        $condicionSUSS = $consultor->getDatosImpositivos()->getCondicionSUSS();

        // Si el Proveedor es pasible de retencion de SUSS y esta inscripto o es UTE
        if ($condicionSUSS->getDenominacionTipoResponsable() == ConstanteTipoResponsable::INSCRIPTO) {

            if ($consultor->getPasibleRetencionSUSS()) {

//              $anioActual = (new DateTime())->format("Y");
//              $mesActual = (new DateTime())->format("m");

                $estadoGenerado = $this->getEstadoComprobanteRetencionImpuestoGenerado();

                $bienEconomico = $this->getBienEconomicoFromComprobanteConsultoria($ordenPagoConsultoria->getComprobantes()->first());

                $regimenRetencion = $this->getRegimenRetencionByImpuestoYBienEconomico(ConstanteTipoImpuesto::SUSS, $bienEconomico->getId());

                $regimenes_comprobante = array();

                if ($this->getAplicaRegimenRetencion($regimenRetencion)) {

                    $regimenes_comprobante[$regimenRetencion->getId()]['acumulado'] = 0;
                    $regimenes_comprobante[$regimenRetencion->getId()]['regimen'] = $regimenRetencion;

                    foreach ($ordenPagoConsultoria->getComprobantes() as $comprobante) {
                        $regimenes_comprobante[$regimenRetencion->getId()]['acumulado'] += ( $comprobante->getTotalNeto() * ($comprobante->getEsNotaCredito() ? -1 : 1));
                    }

                    foreach ($regimenes_comprobante as $regimen) {

                        $regimenRetencion = $regimen['regimen'];

                        $importeResultante = $regimen['acumulado'] * $regimenRetencion->getAlicuota() / 100;

                        if ($importeResultante >= $regimenRetencion->getMinimoRetencion()) {
                            $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                    $ordenPagoConsultoria, //
                                    $regimenRetencion, //
                                    $estadoGenerado, //
                                    $importeResultante, //
                                    0
                            );

                            $this->actualizarErroresExencion(ConstanteTipoImpuesto::SUSS, $consultor, true);

                            $ordenPagoConsultoria->addRetencion($comprobanteRetencion);
                        }
                    }
                }
            } else {
                $this->actualizarErroresExencion(ConstanteTipoImpuesto::SUSS, $consultor, true);
            }
        }
    }

    /**
     * 
     * @param ComprobanteRetencionImpuestoCompras $comprobanteRetencionImpuestoCompras
     * @return array
     */
    public function getComprobantesAplicanImpuesto($comprobanteRetencionImpuestoCompras) {
		
        $comprobantes = array();
		
		if ($comprobanteRetencionImpuestoCompras != null 
				&& $comprobanteRetencionImpuestoCompras->getOrdenPago() != null 
				&& $comprobanteRetencionImpuestoCompras->getOrdenPago()->getComprobantes() != null) {
		
			foreach ($comprobanteRetencionImpuestoCompras->getOrdenPago()->getComprobantes() as $comprobante) {
				$comprobantes[] = $comprobante;
			}
		}
		
        return $comprobantes;
    }

    /**
     * 
     * @param ComprobanteRetencionImpuestoCompras $comprobanteRetencionImpuesto
     * @return array
     */
    public function getDatosComprobantesAplicanImpuesto($comprobanteRetencionImpuesto) {
        $comprobantes = array();
        $comprobantes_return = array();

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());

        $base_calculo_total = 0;

        foreach ($comprobanteRetencionImpuesto->getOrdenPago()->getComprobantes() as $comprobante) {
            /* @var $comprobante \ADIF\ContableBundle\Entity\Comprobante */
            if ($comprobante->getLetraComprobante()->getLetra() != \ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante::Y) {
                foreach ($comprobante->getRenglonesComprobante() as $renglon) {
                    $bienEconomico = null;
                    switch ($comprobanteRetencionImpuesto->getTipoComprobanteRetencion()) {
                        case 'COMPRAS':
                            $renglonOrdenCompra = $emCompras->getRepository('ADIFComprasBundle:RenglonOrdenCompra')->find($renglon->getIdRenglonOrdenCompra());

                            if ($renglonOrdenCompra->getRenglonCotizacion() != null) {
                                $bienEconomico = $renglonOrdenCompra->getRenglonCotizacion()->getRenglonRequerimiento()->getRenglonSolicitudCompra()->getBienEconomico();
                            } else {
                                $bienEconomico = $renglonOrdenCompra->getBienEconomico();
                            }
                        case 'CONSULTORIA':
                            $bienEconomico = ($bienEconomico == null ? $this->getBienEconomicoFromComprobanteConsultoria($comprobanteRetencionImpuesto->getOrdenPago()->getComprobantes()->first()) : $bienEconomico);
                            $regimenBien = $emContable->getRepository('ADIFContableBundle:RegimenRetencionBienEconomico')->findOneBy(
                                    array(
                                        'regimenRetencion' => $comprobanteRetencionImpuesto->getRegimenRetencion(),
                                        'idBienEconomico' => $bienEconomico->getId()
                                    )
                            );
                            $regimenRenglon = $regimenBien ? $regimenBien->getRegimenRetencion() : null;
                            break;
                        case 'OBRAS':
                            $tipoDocumentoFinanciero = $renglon->getTipoDocumentoFinanciero();
                            if ($renglon->getComprobante()->getTramo()->getLicitacion()->getId() == 67) {
                                switch ($comprobanteRetencionImpuesto->getRegimenRetencion()->getTipoImpuesto()->getDenominacion()) {
                                    case ConstanteTipoImpuesto::Ganancias:
                                        $regimenRenglon = $emContable
                                                ->getRepository('ADIFContableBundle:RegimenRetencion')
                                                ->find(10);
                                        break;
                                    case ConstanteTipoImpuesto::IIBB:
                                        $regimenRenglon = $emContable
                                                ->getRepository('ADIFContableBundle:RegimenRetencion')
                                                ->find(26);
                                        break;
                                    case ConstanteTipoImpuesto::SUSS:
                                        $regimenRenglon = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_RG_1784);

                                        break;
                                    case ConstanteTipoImpuesto::IVA:
                                        $regimenRenglon = $tipoDocumentoFinanciero->getRegimenRetencionIVA();
                                        break;
                                }
                            } else {
                                $tipoObra = $renglon->getComprobante()->getTramo()->getTipoObra();
                                switch ($comprobanteRetencionImpuesto->getRegimenRetencion()->getTipoImpuesto()->getDenominacion()) {
                                    case ConstanteTipoImpuesto::Ganancias:
                                        $regimenRenglon = ($tipoObra->getRegimenRetencionGanancias() == null) ? $tipoDocumentoFinanciero->getRegimenRetencionGanancias() : $tipoObra->getRegimenRetencionGanancias();
                                        break;
                                    case ConstanteTipoImpuesto::IIBB:
                                        $regimenRenglon = ($tipoObra->getRegimenRetencionIIBB() == null) ? $tipoDocumentoFinanciero->getRegimenRetencionIIBB() : $tipoObra->getRegimenRetencionIIBB();
                                        break;
                                    case ConstanteTipoImpuesto::SUSS:
                                        $regimenRenglon = $tipoObra->getRegimenRetencionSUSS();
                                        break;
                                    case ConstanteTipoImpuesto::IVA:
                                        $regimenRenglon = ($tipoObra->getRegimenRetencionIVA() == null) ? $tipoDocumentoFinanciero->getRegimenRetencionIVA() : $tipoObra->getRegimenRetencionIVA();
                                        break;
                                }
                            }
                            break;
                    }

                    if ($this->getAplicaRegimenRetencion($regimenRenglon)) {
                        if (!isset($comprobantes[$comprobante->getId()])) {
                            $comprobantes[$comprobante->getId()] = array(
                                'comprobante' => $comprobante,
                                'tipo' => $comprobanteRetencionImpuesto->getTipoComprobanteRetencion(),
                                'fecha' => $comprobante->getFechaComprobante()->format('d/m/Y'),
                                'numero' => $comprobante->getNumeroCompleto(),
                                'importe' => number_format($comprobante->getTotal(), 2, ',', ''),
                                'monto_sujeto_retencion' => number_format($comprobante->getImporteTotalNeto(), 2, ',', ''),
                                'alicuota' => number_format($regimenRenglon->getAlicuota(), 2, ',', ''),
                                'codigo_regimen' => $regimenRenglon->getCodigo(),
                                'base_calculo' => 0,
                                'retencion' => 0
                            );
                        }
                        $totalRenglon = $renglon->getMontoNetoBonificado() * ($comprobante->getEsNotaCredito() ? -1 : 1);
                        $base_calculo_total += $totalRenglon;
                        $comprobantes[$comprobante->getId()]['base_calculo'] += $totalRenglon;
                    }
                }
            }
        }
        foreach ($comprobantes as $comprobante) {
            $comprobante['retencion'] = $comprobanteRetencionImpuesto->getMonto() * $comprobante['base_calculo'] / $base_calculo_total;
            $comprobantes_return[] = $comprobante;
        }
        return $comprobantes_return;
    }

    /**
     * 
     * @param type $comprobanteRetencionImpuestoObras
     * @return type
     */
    public function getComprobantesAplicanImpuestoObras(\ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoObras $comprobanteRetencionImpuestoObras) {

        $comprobantes = array();

        foreach ($comprobanteRetencionImpuestoObras->getOrdenPago()->getComprobantes() as $comprobante) {
            foreach ($comprobante->getRenglonesComprobante() as $renglon) {

                $tipoDocumentoFinanciero = $renglon->getTipoDocumentoFinanciero();

                switch ($comprobanteRetencionImpuestoObras->getRegimenRetencion()->getTipoImpuesto()->getDenominacion()) {
                    case ConstanteTipoImpuesto::Ganancias:
                        $regimenRetencion = ($renglon->getRegimenRetencionGanancias() != null) 
                            ? $renglon->getRegimenRetencionGanancias()
                            : $tipoDocumentoFinanciero->getRegimenRetencionGanancias();
                        break;
                    case ConstanteTipoImpuesto::IIBB:
                        $regimenRetencion = ($renglon->getRegimenRetencionIIBB() != null)
                            ? $renglon->getRegimenRetencionIIBB()
                            : $tipoDocumentoFinanciero->getRegimenRetencionIIBB();
                        break;
                    case ConstanteTipoImpuesto::SUSS:
                        $regimenRetencion = ($renglon->getRegimenRetencionSUSS() != null)
                            ? $renglon->getRegimenRetencionSUSS()
                            : $renglon->getComprobante()->getTramo()->getTipoObra()->getRegimenRetencionSUSS();
                        break;
                    case ConstanteTipoImpuesto::IVA:
                        $regimenRetencion = ($renglon->getRegimenRetencionIVA() != null) 
                            ? $renglon->getRegimenRetencionIVA()
                            : $tipoDocumentoFinanciero->getRegimenRetencionIVA();
                        break;
                }
                if ($regimenRetencion->getCodigo() != 'CODIGO_NO_AGRAVADO') {
                    // Mientras sean "no gravado"
                    $comprobantes[] = $comprobante;
                }
            }
        }
        
        return $comprobantes;
    }

    /**
     * 
     * @param type $comprobanteRetencionImpuestoConsultoria
     * @return type
     */
    public function getComprobantesAplicanImpuestoConsultoria(\ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoConsultoria $comprobanteRetencionImpuestoConsultoria) {

        $comprobantes = array();

        foreach ($comprobanteRetencionImpuestoConsultoria->getOrdenPago()->getComprobantes() as $comprobante) {
            $bienEconomico = $this->getBienEconomicoFromComprobanteConsultoria($comprobante);

            $denominacionTipoImpuesto = $comprobanteRetencionImpuestoConsultoria->getRegimenRetencion()->getTipoImpuesto()->getDenominacion();

            $regimenRetencion = $this->getRegimenRetencionByImpuestoYBienEconomico($denominacionTipoImpuesto, $bienEconomico->getId());

            if ($this->getAplicaRegimenRetencion($regimenRetencion) && $regimenRetencion == $comprobanteRetencionImpuestoConsultoria->getRegimenRetencion()) {
                $comprobantes[] = $comprobante;
            }
        }
        return $comprobantes;
    }

    /**
     * 
     */
    private function initErroresExencion() {

        $this->erroresExencion = array();

        $this->erroresExencion['error'] = 0;
        $this->erroresExencion['limitaGeneracion'] = 0;
        $this->erroresExencion[ConstanteTipoImpuesto::Ganancias] = 0;
        $this->erroresExencion[ConstanteTipoImpuesto::IIBB] = 0;
        $this->erroresExencion[ConstanteTipoImpuesto::IVA] = 0;
        $this->erroresExencion[ConstanteTipoImpuesto::SUSS] = 0;
    }

    /**
     * 
     * @param type $impuesto
     * @param type $beneficiario
     * @param type $limita
     */
    private function actualizarErroresExencion($impuesto, $beneficiario, $limita) {

        /* @var $datosImpositivos \ADIF\ComprasBundle\Entity\DatosImpositivos */
        $datosImpositivos = $beneficiario->getDatosImpositivos();

        switch ($impuesto) {
            case ConstanteTipoImpuesto::Ganancias:
                $exencion = $beneficiario->getCertificadoExencionGanancias();
                $exento = $datosImpositivos->getExentoGanancias();
                break;
            case ConstanteTipoImpuesto::IIBB:
                $exencion = $beneficiario->getCertificadoExencionIngresosBrutos();
                $exento = $datosImpositivos->getExentoIngresosBrutos();
                break;
            case ConstanteTipoImpuesto::IVA:
                $exencion = $beneficiario->getCertificadoExencionIVA();
                $exento = $datosImpositivos->getExentoIVA();
                break;
            case ConstanteTipoImpuesto::SUSS:
                $exencion = $beneficiario->getCertificadoExencionSUSS();
                $exento = $datosImpositivos->getExentoSUSS();
                break;
        }

        if ($exento) {
            if ($exencion != null) {

                $fechaHasta = (new \DateTime())->setTime(23, 59, 59);

                if ($exencion->getFechaHasta() <= $fechaHasta) {

                    $this->erroresExencion['error'] = 1;
                    $this->erroresExencion[$impuesto] = 1;

                    if ($limita) {
                        $this->erroresExencion['limitaGeneracion'] = 1;
                    }
                }
            }
        }
    }

    /**
     * 
     * @param type $beneficiario
     * @return type
     */
    public function exencionAnticipo($beneficiario) {

        $this->initErroresExencion();

        $this->actualizarErroresExencion(ConstanteTipoImpuesto::Ganancias, $beneficiario, false);
        $this->actualizarErroresExencion(ConstanteTipoImpuesto::SUSS, $beneficiario, false);
        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IVA, $beneficiario, false);
        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IIBB, $beneficiario, false);

        return $this->erroresExencion;
    }

    /**
     * 
     * @param Proveedor $proveedor
     * @param type $ordenPago
     * @param type $regimenes_comprobante
     */
    private function retencionSUSSProveedor(Proveedor $proveedor, $ordenPago, $regimenes_comprobante) {

        $this->actualizarErroresExencion(ConstanteTipoImpuesto::SUSS, $proveedor, false);

        $condicionSUSS = $proveedor->getClienteProveedor()->getCondicionSUSS();

        // Si el Proveedor es pasible de retencion de SUSS y esta inscripto o es UTE
        if (($condicionSUSS->getDenominacionTipoResponsable() == ConstanteTipoResponsable::INSCRIPTO) || ($proveedor->getEsUTE())) {

            if ($proveedor->getPasibleRetencionSUSS()) {

                $this->logger->info("Es pasible de retencion SUSS");

                $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

                $anioActual = (new DateTime())->format("Y");

                $estadoGenerado = $this->getEstadoComprobanteRetencionImpuestoGenerado();

                $regimenRetencionIngenieria = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_2682_10_A);

                $regimenRetencionArquitectura = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_2682_10_B);

                $regimenRetencionLimpieza = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_RG_1556);

                $regimenRetencionSeguridad = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_RG_1769);

                $regimenRetencionGeneral = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_RG_1784);

                $comprobantesRetencion = array();
				
				$netoTotalParaCalculo = 0;

                $noRetenerRegimen = array( $regimenRetencionLimpieza->getId() /*REG 1556*/, $regimenRetencionGeneral->getId() /* REG 1784*/);

                $retencionMensual = array( $regimenRetencionSeguridad->getId() /* REG 1769 */ );

                $retenerAnual = true;

                $netoComprobantesSinRetencion = 0;

                foreach ($regimenes_comprobante as $regimen) {

                    /* @var $regimenRetencion RegimenRetencion */
                    $regimenRetencion = $regimen['regimen'];

                    $retenerAnual = in_array($regimenRetencion->getId(), $retencionMensual) ? false : true;

                    if (!in_array($regimenRetencion->getId(), $noRetenerRegimen)) {
                        $netoComprobantesSinRetencion = $this->getComprobantesAcumuladosSinRetencionProveedor($regimenRetencion, ConstanteTipoImpuesto::SUSS, $proveedor->getId(), new DateTime(), $retenerAnual);
                    }

                    $netoAcumuladoParaComprobante = $netoTotalParaCalculo = $regimen['total'];
                    $regimen['total'] += $netoComprobantesSinRetencion;
                    $regimen['total'] = $this->actualizarNetoPorRegimen($regimen['total'], $proveedor, ConstanteTipoImpuesto::SUSS);

                    $this->logger->info("Regimen: " . $regimenRetencion);
                    $this->logger->info("Regimen ID: " . $regimenRetencion->getId());
                    $this->logger->info("Neto Acumulado de comprobantes para calculo: " . $netoTotalParaCalculo);

                    $netoComprobantes = $regimen['total'];

                    switch ($regimenRetencion->getId()) {

                        // si limpieza (regimen 1556)
                        case $regimenRetencionLimpieza->getId():

                            // Si el proveedor NO es monotributista
                            if ($proveedor->getClienteProveedor()->getCondicionIVA()->getDenominacionTipoResponsable() != ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO) {
                                $this->logger->info("No es monotributista. Corresponde retencion para este regimen");
                                $this->logger->info("Alicuota a aplicar: " . $regimenRetencion->getAlicuota());
                                $this->logger->info("Calculo aplicado: " . $regimen['total'] . " * " . $regimenRetencion->getAlicuota() . " / 100");
                                //alicuota 6% 
                                $importeResultante = $regimen['total'] * $regimenRetencion->getAlicuota() / 100;
                                $this->logger->info("Importe resultante: " . $importeResultante);
								
								$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPago);
						
								$montoRetencionPagoParcial = 0;
								foreach($pagosParciales as $pagoParcial) {
									$montoRetencionPagoParcial += $pagoParcial->getRetencionSuss();
								}
								
								$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
								
								// Si tiene pagos parciales, se lo resto del calculo
								$importeResultante -= $montoRetencionPagoParcial;
                                
                                $epsilon = 0.01;
                                
                                if (abs($importeResultante) <= $epsilon) {
                                    $importeResultante = 0;
                                }
								
								$this->logger->info("Total final: " . $importeResultante);

                                $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                        $ordenPago, //
                                        $regimenRetencion, //
                                        $estadoGenerado, //
                                        $importeResultante, //
                                        $netoAcumuladoParaComprobante
                                );

                                $this->actualizarErroresExencion(ConstanteTipoImpuesto::SUSS, $proveedor, true);

                                $comprobantesRetencion[] = $comprobanteRetencion;
                            } else {
                                $this->logger->info("Es monotributista. No corresponde retencion para este regimen");
                            }
                            break;

                        // servicios seguridad (regimen 1769)
                        case $regimenRetencionSeguridad->getId():

                            $this->logger->info("Se debe acumular lo facturado en el mes");

                            // sumo importes neto que le abone en mes calendario por ese concepto a ese proveedor
                            //acumulado facturado y retenido
							$arrayNetoMensual = $emContable
									->getRepository('ADIFContableBundle:ComprobanteRetencionImpuesto')
									->getMontoAcumuladoYRetenidoByRegimenProveedorYFechaV2(
										$proveedor->getId(), 
										$regimenRetencion->getId(), 
										new DateTime(),
										$ordenPago
									);

                            $regimen['total'] += $arrayNetoMensual['neto'];

                            $this->logger->info("Neto Acumulado mensual: " . $arrayNetoMensual['neto']);
                            $this->logger->info("Neto Acumulado total: " . $regimen['total']);
                            $this->logger->info("Minimo para retencion: " . $regimenRetencion->getMinimoExento());

                            // Si mayor a 8000
                            if (($regimen['total'] >= $regimenRetencion->getMinimoExento()) || ($netoComprobantes >= $regimenRetencion->getMinimoExento())) {
                                $this->logger->info("Supera el minimo para retencion. Corresponde retencion");
                                $this->logger->info("Alicuota a aplicar: " . $regimenRetencion->getAlicuota());
                                $this->logger->info("Calculo aplicado: " . $regimen['total'] . " * " . $regimenRetencion->getAlicuota() . " / 100");

                                //alicuota 6%
                                $importeResultante = $regimen['total'] * $regimenRetencion->getAlicuota() / 100;

                                $this->logger->info("Importe resultante sin restar retenido en el mes: " . $importeResultante);
                                // Al importe resultante le resto lo que ya le había retenido en dicho mes
                                $importeResultante -= $arrayNetoMensual['monto_retencion'];
                                $this->logger->info("Retenido en el mes: " . $arrayNetoMensual['monto_retencion']);
                                $this->logger->info("Importe resultante: " . $importeResultante);

								$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPago);
								
								$montoRetencionPagoParcial = 0;
								foreach($pagosParciales as $pagoParcial) {
									$montoRetencionPagoParcial += $pagoParcial->getRetencionSuss();
								}
								
								$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
								
								// Si tiene pagos parciales, se lo resto del calculo
								$importeResultante -= $montoRetencionPagoParcial;
                                
                                $epsilon = 0.01;
                                
                                if (abs($importeResultante) <= $epsilon) {
                                    $importeResultante = 0;
                                }
								
								$this->logger->info("Total final: " . $importeResultante);
								
                                $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                        $ordenPago, //
                                        $regimenRetencion, //
                                        $estadoGenerado, //
                                        $importeResultante, //
                                        $netoAcumuladoParaComprobante
                                );

                                $this->actualizarErroresExencion(ConstanteTipoImpuesto::SUSS, $proveedor, true);

                                $comprobantesRetencion[] = $comprobanteRetencion;
                            } else {
                                $this->logger->info("No supera el minimo para retencion. No corresponde retencion");
                            }
                            break;

                        // Si regimen general (regimen 1784)
                        case $regimenRetencionGeneral->getId():

                            // si iva responsable inscripto y si es pasible retencion iva
                            //if (($proveedor->getPasibleRetencionIVA()) && ($proveedor->getClienteProveedor()->getCondicionIVA()->getDenominacionTipoResponsable() == ConstanteTipoResponsable::INSCRIPTO)) {
                            if ($proveedor->getClienteProveedor()->getCondicionIVA()->getDenominacionTipoResponsable() == ConstanteTipoResponsable::INSCRIPTO) {
                                //$this->logger->info("Es pasible de retencion de iva y esta incripto. Corresponde retencion para este regimen");
                                $this->logger->info("Esta incripto en IVA. Corresponde retencion para este regimen");
                                $this->logger->info("Alicuota a aplicar: " . $regimenRetencion->getAlicuota());
                                $this->logger->info("Calculo aplicado: " . $regimen['total'] . " * " . $regimenRetencion->getAlicuota() . " / 100");

                                //alicuota 1% 
                                $importeResultante = $regimen['total'] * $regimenRetencion->getAlicuota() / 100;
                                $this->logger->info("Importe resultante: " . $importeResultante);
                                $this->logger->info("Minimo para retencion: " . $regimenRetencion->getMinimoRetencion());
                                //si es mayor o igual a 40 aplico
                                if ($importeResultante >= $regimenRetencion->getMinimoRetencion()) {
                                    $this->logger->info("Supera el minimo para retencion. Corresponde retencion para este regimen");
                                    $this->logger->info("Importe resultante: " . $importeResultante);

									$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPago);
									
									$montoRetencionPagoParcial = 0;
									foreach($pagosParciales as $pagoParcial) {
										$montoRetencionPagoParcial += $pagoParcial->getRetencionSuss();
									}
									
									$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
									
									// Si tiene pagos parciales, se lo resto del calculo
									$importeResultante -= $montoRetencionPagoParcial;
                                    
                                    $epsilon = 0.01;
                                
                                    if (abs($importeResultante) <= $epsilon) {
                                        $importeResultante = 0;
                                    }
									
									$this->logger->info("Total final: " . $importeResultante);
									
                                    $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                            $ordenPago, //
                                            $regimenRetencion, //
                                            $estadoGenerado, //
                                            $importeResultante, //
                                            $netoAcumuladoParaComprobante
                                    );

                                    $this->actualizarErroresExencion(ConstanteTipoImpuesto::SUSS, $proveedor, true);

                                    $comprobantesRetencion[] = $comprobanteRetencion;
                                } else {
                                    $this->logger->info("No supera el minimo para retencion. No corresponde retencion para este regimen");
                                }
                            } else {
                                $this->logger->info("No es pasible de retencion de iva o no esta incripto. No corresponde retencion para este regimen");
                            }
                            break;

                        // Ingenieria (regimen 2682_10_A)
                        case $regimenRetencionIngenieria->getId():

                            $this->logger->info("Se debe acumular lo facturado en el anio");

                            //acumulado facturado y retenido
							$arrayRetenidoAnual = $emContable
								->getRepository('ADIFContableBundle:ComprobanteRetencionImpuesto')
								->getMontoAcumuladoYRetenidoByRegimenProveedorYFechaV2(
									$proveedor->getId(), 
									$regimenRetencion->getId(), 
									new DateTime(),
									$ordenPago,
									$anual = true
								);
							
							
                            #$this->logger->info('Regimen Neto: ' . $regimen['total']);
                            #$this->logger->info('Neto acumulado restante del anio: ' . $arrayRetenidoAnual['neto']);

                            $regimen['total'] += $arrayRetenidoAnual['neto'];

                            $this->logger->info('Neto para calculo: ' . $netoTotalParaCalculo);

                            if ((($regimen['total'] > $regimenRetencionIngenieria->getMinimoExento()) || ($netoComprobantes >= $regimenRetencionIngenieria->getMinimoExento())) && $netoTotalParaCalculo - $regimenRetencionIngenieria->getMinimoExento() > 0) {

                                #$this->logger->info("Supera los ".$regimenRetencionIngenieria->getMinimoExento()." Corresponde retencion");

                                $this->logger->info("Alicuota a aplicar: " . $regimenRetencion->getAlicuota());
                                $this->logger->info("Calculo: (" . $netoTotalParaCalculo  . " * 0,012)");

                                // Aplicamos al total regimen la alicuota
                                $importeResultante = ($netoTotalParaCalculo * 0.012);

                                $this->logger->info('Monto Retencion ' . $importeResultante);
                                #$this->logger->info("Retenido anual total" . $arrayRetenidoAnual['monto_retencion']);
                                #$retenidoAnualTotal = $arrayRetenidoAnual['neto'] - $regimenRetencionIngenieria->getMinimoExento() > 0 ? ($arrayRetenidoAnual['neto'] - $regimenRetencionIngenieria->getMinimoExento()) * $regimenRetencion->getAlicuota() / 100 : 0;
                                #$importeResultante -= $retenidoAnualTotal;

                                $this->logger->info('Monto Retencion final ' . $importeResultante);
								
								$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPago);
								
								$montoRetencionPagoParcial = 0;
								foreach($pagosParciales as $pagoParcial) {
									$montoRetencionPagoParcial += $pagoParcial->getRetencionSuss();
								}
								
								$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
								
								// Si tiene pagos parciales, se lo resto del calculo
								$importeResultante -= $montoRetencionPagoParcial;
								
                                $epsilon = 0.01;
                                
                                if (abs($importeResultante) <= $epsilon) {
                                    $importeResultante = 0;
                                }
                                
								$this->logger->info("Total final: " . $importeResultante);
								
                                //obtenemos retenciones por año y regimen. las totalizamos y le restamos
                                $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                        $ordenPago, //
                                        $regimenRetencion, //
                                        $estadoGenerado, //
                                        $importeResultante, //
                                        $netoAcumuladoParaComprobante
                                );

                                $this->actualizarErroresExencion(ConstanteTipoImpuesto::SUSS, $proveedor, true);

                                $comprobantesRetencion[] = $comprobanteRetencion;

                            } else {
                                $this->logger->info("No Supera ".$regimenRetencionIngenieria->getMinimoExento()." No corresponde retencion");
                            }
                        break;
                        // Arquitectura (regimen 2682_10_B)
                        case $regimenRetencionArquitectura->getId():

                            $this->logger->info("Se debe acumular lo facturado en el anio");

                            //acumulado facturado y retenido
							$arrayRetenidoAnual = $emContable
								->getRepository('ADIFContableBundle:ComprobanteRetencionImpuesto')
								->getMontoAcumuladoYRetenidoByRegimenProveedorYFechaV2(
									$proveedor->getId(), 
									$regimenRetencion->getId(), 
									new DateTime(),
									$ordenPago,
									$anual = true
								);

							$this->logger->info('Regimen Neto: ' . $regimen['total']);
                            $this->logger->info('Neto acumulado restante del anio: ' . $arrayRetenidoAnual['neto']);

                            $regimen['total'] += $arrayRetenidoAnual['neto'];

                            $this->logger->info('Neto para calculo: ' . $regimen['total']);

                            if (($regimen['total'] > $regimenRetencionArquitectura->getMinimoExento()) || ($netoComprobantes >= $regimenRetencionArquitectura->getMinimoExento())) {

                                $this->logger->info("Supera {$regimenRetencionArquitectura->getMinimoExento()}. Corresponde retencion");

                                $this->logger->info("Retenido anual " . $arrayRetenidoAnual['monto_retencion']);

                                $this->logger->info("Alicuota a aplicar: " . $regimenRetencion->getAlicuota());
                                $this->logger->info("Calculo: " . $regimen['total'] . " * " . $regimenRetencion->getAlicuota() . " / 100");

                                // Aplicamos al total regimen la alicuota
                                $importeResultante = $regimen['total'] * $regimenRetencion->getAlicuota() / 100;

                                $this->logger->info('Monto Retencion sin restar acumulado ' . $importeResultante);

                                $importeResultante -= $arrayRetenidoAnual['monto_retencion'];
                                $this->logger->info('Monto Retencion final ' . $importeResultante);
								
								$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPago);
								
								$montoRetencionPagoParcial = 0;
								foreach($pagosParciales as $pagoParcial) {
									$montoRetencionPagoParcial += $pagoParcial->getRetencionSuss();
								}
								
								$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
								
								// Si tiene pagos parciales, se lo resto del calculo
								$importeResultante -= $montoRetencionPagoParcial;
                                
                                $epsilon = 0.01;
                                
                                if (abs($importeResultante) <= $epsilon) {
                                    $importeResultante = 0;
                                }
								
								$this->logger->info("Total final: " . $importeResultante);
								
                                //obtenemos retenciones por año y regimen. las totalizamos y le restamos
                                $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                        $ordenPago, //
                                        $regimenRetencion, //
                                        $estadoGenerado, //
                                        $importeResultante, //
                                        $netoAcumuladoParaComprobante
                                );

                                $this->actualizarErroresExencion(ConstanteTipoImpuesto::SUSS, $proveedor, true);

                                $comprobantesRetencion[] = $comprobanteRetencion;
                            } else {
                                $this->logger->info("No Supera {$regimenRetencionArquitectura->getMinimoExento()}. No corresponde retencion");
                            }
                            break;
                    }
                }

                $divideUte = false;

                // Si el proveedor es UTE
                if ($proveedor->getEsUTE()) {
                    $proveedores = $proveedor->getProveedoresUTE();
                    $totalUTE = 0;
                    foreach ($proveedores as $proveedorUTE) {
                        $totalUTE += $proveedorUTE->getPorcentajeRemuneracion();
                        $this->logger->info('UTE %' . $proveedorUTE->getPorcentajeRemuneracion());
                    }
                    if ($totalUTE != 0) {
                        $divideUte = true;
                    }
                }


                if ($divideUte) {
					
                    $this->logger->info('Divido por integrante UTE');

                    $proveedores = $proveedor->getProveedoresUTE();

                    // Por cada comprobante de retencion generado
                    foreach ($comprobantesRetencion as $comprobanteRetencion) {

                        // Por cada proveedor que integra la UTE, genero un nuevo comprobante
                        foreach ($proveedores as $proveedorUTE) {
						
							/* Me fijo si en la UTE por c/u de los integrantes si esta exento */
							$correspondeRetener = true;
							
							/* @var $datosImpositivos \ADIF\ComprasBundle\Entity\DatosImpositivos */
							$datosImpositivos = $proveedorUTE->getProveedor()->getDatosImpositivos();

							$exencion = $proveedorUTE->getProveedor()->getCertificadoExencionSUSS();
							$exento = $datosImpositivos->getExentoSUSS();
							
							if ($exento) {
								
								if ($exencion != null) {

									$fechaHoy = (new \DateTime())->setTime(00, 00, 00);

									if ($exencion->getFechaHasta() >= $fechaHoy) {
										$this->logger->info("El proveedor {$proveedorUTE->getProveedor()} no corresponde retener SUSS porque esta exento.");
										$correspondeRetener = false;
									} 
									
								}
							}
							
							if ($correspondeRetener) {
							
								// Aplico el porcentaje de remuneracion del proveedor que integra la UTE
								$importeResultante = $comprobanteRetencion->getMonto() * $proveedorUTE->getPorcentajeRemuneracion() / 100;
								
								$nuevoComprobanteRetencion = $this->nuevoComprobanteRetencion(
										$ordenPago, //
										$comprobanteRetencion->getRegimenRetencion(), //
										$estadoGenerado, //
										$importeResultante, //
										$netoAcumuladoParaComprobante * $proveedorUTE->getPorcentajeRemuneracion() / 100, //
										$proveedorUTE->getProveedor()->getId()
								);

								$ordenPago->addRetencion($nuevoComprobanteRetencion);
							}
                        }
                    }
                } else {

                    $this->logger->info('No divido por integrante UTE');

                    // Por cada comprobante de retencion generado
                    foreach ($comprobantesRetencion as $comprobanteRetencion) {
                        // A la OP le asocio la coleccion de comprobantes de retencion
                        $ordenPago->addRetencion($comprobanteRetencion);
                    }
                }
            } else {
                $this->actualizarErroresExencion(ConstanteTipoImpuesto::SUSS, $proveedor, true);
            }
        }
    }

    /**
     * 
     * @param Proveedor $proveedor
     * @param type $ordenPago
     * @param type $regimenes_comprobante
     */
    private function retencionGananciasProveedor(Proveedor $proveedor, $ordenPago, $regimenes_comprobante) 
	{

        $this->actualizarErroresExencion(ConstanteTipoImpuesto::Ganancias, $proveedor, false);

        $condicionGanancias = $proveedor->getClienteProveedor()->getCondicionGanancias();

        // Si el Proveedor es pasible de retencion y está inscripto en Ganancias
        if ($condicionGanancias->getDenominacionTipoResponsable() == ConstanteTipoResponsable::INSCRIPTO) {
            $this->logger->info("Inscripto en Ganancias");
            if ($proveedor->getPasibleRetencionGanancias()) {

                $this->logger->info("Pasible de retencion de ganancias");

                $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

                $estadoGenerado = $this->getEstadoComprobanteRetencionImpuestoGenerado();

                $comprobantesRetencion = array();

                foreach ($regimenes_comprobante as $regimen) {

                    /* @var $regimenRetencion RegimenRetencion */
                    $regimenRetencion = $regimen['regimen'];

                    $netoComprobantesSinRetencion = $this->getComprobantesAcumuladosSinRetencionProveedor($regimenRetencion, ConstanteTipoImpuesto::Ganancias, $proveedor->getId(), new DateTime(), false);
					
					//\Doctrine\Common\Util\Debug::dump( $netoComprobantesSinRetencion ); exit;
					
                    $this->logger->info("Neto Acumulado de comprobantes sin retencion: " . $netoComprobantesSinRetencion);
                    //aplico porcentaje exencion
                    $regimen['total'] = $this->actualizarNetoPorRegimen($regimen['total'], $proveedor, ConstanteTipoImpuesto::Ganancias);

                    $netoAcumuladoParaComprobante = $regimen['total'];

                    $regimen['total'] += $netoComprobantesSinRetencion;
					
                    //aplico porcentaje exencion
                    //$regimen['total'] = $this->actualizarNetoPorRegimen($regimen['total'], $proveedor, ConstanteTipoImpuesto::Ganancias);

                    $this->logger->info("Regimen: " . $regimenRetencion);
                    $this->logger->info("Regimen ID: " . $regimenRetencion->getId());
                    $this->logger->info("Neto Acumulado de comprobantes para calculo: " . $regimen['total']);


                    //acumulado facturado y retenido
                    $arrayNetoMensual = $emContable
                            ->getRepository('ADIFContableBundle:ComprobanteRetencionImpuesto')
                            ->getMontoAcumuladoYRetenidoByRegimenProveedorYFechaV2(
								$proveedor->getId(), 
								$regimenRetencion->getId(), 
								new DateTime(),
								$ordenPago,
								$anual = false,
								$proveedor->getEsUTE()
							);
					
					// Le sumo el neto mensual de otros comprobantes del mes
                    $regimen['total'] += isset($arrayNetoMensual['neto']) 
						? $arrayNetoMensual['neto']
						: 0;
					
                    $this->logger->info("Neto Acumulado mensual: " . $arrayNetoMensual['neto']);
                    $this->logger->info("Neto Acumulado total: " . $regimen['total']);

                    // Si el RegimenRetención NO utiliza tablas auxiliares
                    if (!$regimenRetencion->getUsaTabla()) {
                        $this->logger->info("No usa tabla");

                        // Obtengo el mínimo no imponible del RegimenRetencion
                        $minimoNoImponible = $regimenRetencion->getMinimoNoImponible();

                        // Obtengo la alicuota del RegimenRetencion
                        $alicuota = $regimenRetencion->getAlicuota();

                        $montoARetener = 0;
                    } else {
                        $this->logger->info("Usa tabla");
						$this->logger->info("Neto acumulado total - Minimo no imponible: (regimen de retención): " . $regimen['total'] . ' - ' . $regimenRetencion->getMinimoNoImponible());
						//$this->logger->info("Neto acumulado total: " . $regimen['total']);
						$regimen['total'] -= $regimenRetencion->getMinimoNoImponible();
						$this->logger->info("Subtotal: " . $regimen['total']);
						if ($regimen['total'] < 0) {
							$regimen['total'] = 0;
						}
						$this->logger->info("Subtotal: " . $regimen['total']);
                        /* @var $escalaRetencionHonorarioGanancia EscalaRetencionHonorariosGanancias */
                        $escalaRetencionHonorarioGanancia = $emContable
                                ->getRepository('ADIFContableBundle:EscalaRetencionHonorariosGanancias')
                                ->getEscalaRetencionHonorariosGananciasByMonto($regimen['total']);
						
						// Obtengo el mínimo no imponible de la escala
						$minimoNoImponible = $escalaRetencionHonorarioGanancia->getMinimoNoImponible();
						
						// Obtengo la alicuota del EscalaRetencionHonorariosGanancias
						$alicuota = $escalaRetencionHonorarioGanancia->getAlicuota();
						
						// Obtengo el monto a retener del EscalaRetencionHonorariosGanancias
						$montoARetener = $escalaRetencionHonorarioGanancia
									->getMontoARetener();
                    }

                    $this->logger->info("Minimo no imponible: " . $minimoNoImponible);
                    $this->logger->info("Alicuota: " . $alicuota);
                    $this->logger->info("Monto a retener por tabla: " . $montoARetener);

                    // Calculo el importe
                    $importeCalculado = ($regimen['total'] - $minimoNoImponible) * $alicuota / 100 + $montoARetener;

                    // Al importe resultante le resto lo que ya le había retenido en dicho mes
					if (!$proveedor->getEsUTE()) {
						
						$this->logger->info("Calculo = ((" . $regimen['total'] . " - " . $minimoNoImponible . ") * " . $alicuota . ") /100) + " . $montoARetener . " - " . $arrayNetoMensual['monto_retencion']);
						$importeCalculado -= $arrayNetoMensual['monto_retencion'];
						
					} else {
						// Es UTE
						$proveedores = $proveedor->getProveedoresUTE();
                        // Por cada proveedor que integra la UTE, genero un nuevo comprobante
                        foreach ($proveedores as $proveedorUTE) {
							
							/* Me fijo si en la UTE por c/u de los integrantes si esta exento */
							$correspondeRetener = true;
							
							/* @var $datosImpositivos \ADIF\ComprasBundle\Entity\DatosImpositivos */
							$datosImpositivos = $proveedorUTE->getProveedor()->getDatosImpositivos();

							$exencion = $proveedorUTE->getProveedor()->getCertificadoExencionGanancias();
							$exento = $datosImpositivos->getExentoGanancias();
							
							if ($exento) {
								
								if ($exencion != null) {

									$fechaHoy = (new \DateTime())->setTime(00, 00, 00);

									if ($exencion->getFechaHasta() >= $fechaHoy) {
										
										$correspondeRetener = false;
									} 
									
								}
							}
						}
						
						if ($correspondeRetener) {
							// Casos de UTEs comunes, que retienen todas...
							$this->logger->info("Calculo = ((" . $regimen['total'] . " - " . $minimoNoImponible . ") * " . $alicuota . ") /100) + " . $montoARetener . " - " . $arrayNetoMensual['monto_retencion']);
							$importeCalculado -= $arrayNetoMensual['monto_retencion'];
						} else {
							// Alguna esta exenta...
							$baseImponibleGananciasUte = $emContable
								->getRepository('ADIFContableBundle:ComprobanteRetencionImpuesto')
								->getAcumuladoGananciasUte($proveedor->getId(), $regimenRetencion->getId());
								
							$this->logger->info("Calculo = ((" . $regimen['total'] . " - " . $minimoNoImponible . ") * " . $alicuota . ") /100) + " . $montoARetener . " - " . $baseImponibleGananciasUte); 
							$importeCalculado -= $baseImponibleGananciasUte;
						}
						
					}

                    $this->logger->info("Final " . $importeCalculado);

                    // Si el resultado es mayor al mínimo exento, se crea el comprobante de retención
                    if ($importeCalculado > $regimenRetencion->getMinimoRetencion()) {

                        $this->logger->info("Supera el minimo de retencion. Corresponde retener");
						
						$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPago);
						
						$montoRetencionPagoParcial = 0;
						foreach($pagosParciales as $pagoParcial) {
							$montoRetencionPagoParcial += $pagoParcial->getRetencionGanancias();
						}
						
						$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
						
						// Si tiene pagos parciales, se lo resto del calculo
						$importeCalculado -= $montoRetencionPagoParcial;
                        
                        $epsilon = 0.01;
                                
                        if (abs($importeCalculado) <= $epsilon) {
                            $importeCalculado = 0;
                        }
						
						$this->logger->info("Total final: " . $importeCalculado);

                        $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                $ordenPago, //
                                $regimenRetencion, //
                                $estadoGenerado, //
                                $importeCalculado, //
                                $netoAcumuladoParaComprobante
                        );

                        $this->actualizarErroresExencion(ConstanteTipoImpuesto::Ganancias, $proveedor, true);

                        $comprobantesRetencion[] = $comprobanteRetencion;
                    } else {
                        $this->logger->info("No supera el minimo de retencion. No corresponde retener");
                    }
                }

                // Si el proveedor es UTE
                if ($proveedor->getEsUTE()) {
					
					$this->logger->info('Divido por integrante UTE');
					
                    $proveedores = $proveedor->getProveedoresUTE();
                    // Por cada comprobante de retencion generado
                    foreach ($comprobantesRetencion as $comprobanteRetencion) {

                        // Por cada proveedor que integra la UTE, genero un nuevo comprobante
                        foreach ($proveedores as $proveedorUTE) {
							
							/* Me fijo si en la UTE por c/u de los integrantes si esta exento */
							$correspondeRetener = true;
							
							/* @var $datosImpositivos \ADIF\ComprasBundle\Entity\DatosImpositivos */
							$datosImpositivos = $proveedorUTE->getProveedor()->getDatosImpositivos();

							$exencion = $proveedorUTE->getProveedor()->getCertificadoExencionGanancias();
							$exento = $datosImpositivos->getExentoGanancias();
							
							if ($exento) {
								
								if ($exencion != null) {

									$fechaHoy = (new \DateTime())->setTime(00, 00, 00);

									if ($exencion->getFechaHasta() >= $fechaHoy) {
										$this->logger->info("El proveedor {$proveedorUTE->getProveedor()} no corresponde retener Ganancias porque esta exento.");
										$participacionProveedor = $importeCalculado * $proveedorUTE->getPorcentajeGanancia() / 100;
										$this->logger->info("Se le saca la participacion del {$proveedorUTE->getPorcentajeGanancia()}% al proveedor {$proveedorUTE->getProveedor()} por un total de $ $participacionProveedor");
										$this->logger->info("Total final: " . ($importeCalculado - $participacionProveedor));
										$correspondeRetener = false;
									} 
									
								}
							}
							
							if ($correspondeRetener) {

								// Aplico el porcentaje de remuneracion del proveedor que integra la UTE
								$importeResultante = $comprobanteRetencion->getMonto() * $proveedorUTE->getPorcentajeGanancia() / 100;

								$nuevoComprobanteRetencion = $this->nuevoComprobanteRetencion(
										$ordenPago, //
										$comprobanteRetencion->getRegimenRetencion(), //
										$estadoGenerado, //
										$importeResultante, //
										$netoAcumuladoParaComprobante * $proveedorUTE->getPorcentajeGanancia() / 100, //
										$proveedorUTE->getProveedor()->getId(),
										$importeCalculado
										
								);

								$ordenPago->addRetencion($nuevoComprobanteRetencion);
							}
                        }
                    }
					
                } else {

                    // Por cada comprobante de retencion generado
                    foreach ($comprobantesRetencion as $comprobanteRetencion) {

                        // A la OP le asocio la coleccion de comprobantes de retencion
                        $ordenPago->addRetencion($comprobanteRetencion);
                    }
                }
            } else {
                $this->actualizarErroresExencion(ConstanteTipoImpuesto::Ganancias, $proveedor, true);
            }
        }
    }

    /**
     * 
     * @param type $monto
     * @param type $beneficiario
     * @param type $impuesto
     * @return type
     */
    private function actualizarNetoPorRegimen($monto, $beneficiario, $impuesto) {
        /* @var $datosImpositivos \ADIF\ComprasBundle\Entity\DatosImpositivos */
        $datosImpositivos = $beneficiario->getDatosImpositivos();
        switch ($impuesto) {
            case ConstanteTipoImpuesto::Ganancias:
                $exencion = $beneficiario->getCertificadoExencionGanancias();
                $exento = $datosImpositivos->getExentoGanancias();
                break;
            case ConstanteTipoImpuesto::IIBB:
                $exencion = $beneficiario->getCertificadoExencionIngresosBrutos();
                $exento = $datosImpositivos->getExentoIngresosBrutos();
                break;
            case ConstanteTipoImpuesto::IVA:
                $exencion = $beneficiario->getCertificadoExencionIVA();
                $exento = $datosImpositivos->getExentoIVA();
                break;
            case ConstanteTipoImpuesto::SUSS:
                $exencion = $beneficiario->getCertificadoExencionSUSS();
                $exento = $datosImpositivos->getExentoSUSS();
                break;
        }
        $this->logger->info("Neto sin exencion: " . $monto);
        if ($exento) {
            if ($exencion != null) {
                $this->logger->info("Porcentaje exencion: " . $exencion->getPorcentajeExencion());
                $monto *= (100 - $exencion->getPorcentajeExencion()) / 100;
                $this->logger->info("Neto con exencion aplicada: " . $monto);
            }
        }
        return $monto;
    }

    /**
     * 
     * @param Proveedor $proveedor
     * @param type $ordenPago
     * @param type $regimenes_comprobante
     */
    private function retencionIIBBProveedor(Proveedor $proveedor, $ordenPago, $regimenes_comprobante) {

        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IIBB, $proveedor, false);

		$aplicaRegimenRetencionIIBBCaba = false;
		$alicuotaIIBBCaba = 0;
		
		if ($proveedor->getClienteProveedor()->getAplicaRgNormalmente() && $proveedor->getIibbCaba() != null) {
			// Si Aplica RG Normalmente (Calificacion fiscal) y tiene asignado un grupo
			$this->logger->info("Aplica RG Normalmente (Calificacion fiscal) y tiene asignado un grupo");
			$this->logger->info('Regimen de retencion IIBB CABA: ' . $proveedor->getIibbCaba()->__toString());
			$alicuotaIIBBCaba = $proveedor->getIibbCaba()->getAlicuota();
			$aplicaRegimenRetencionIIBBCaba = true;
		} 
		
		// Si el Proveedor es pasible de retencion de IIBB
		if ($proveedor->getPasibleRetencionIngresosBrutos()) {

			$this->logger->info("Es pasible de IIBB");

			// Verifico si el monto de los comprobantes superan el mínimo
			$totalNetoComprobantes = 0;

			foreach ($ordenPago->getComprobantes() as $comprobante) {
                
				foreach ($comprobante->getRenglonesComprobante() as $renglonComprobante) {
                    
					$this->logger->info("Monto renglón: " . $renglonComprobante->getMontoNetoBonificado());
                    
                    if ( 
                            $renglonComprobante->getRegimenRetencionIIBB() != null && 
                            $renglonComprobante->getRegimenRetencionIIBB()->getCodigo() == 'CODIGO_NO_AGRAVADO'
                    ) {
                        $totalNetoComprobantes += 0;
                    } else {
                        $totalNetoComprobantes += ($renglonComprobante->getMontoNetoBonificado() * ($comprobante->getEsNotaCredito() ? -1 : 1));
                    }
				}
			}

			$recorroBienes = true;
            $this->logger->info("Monto neto comprobantes: " . $totalNetoComprobantes);
			if ($totalNetoComprobantes >= 300) {

				$estadoGenerado = $this->getEstadoComprobanteRetencionImpuestoGenerado();

				$porcentajeCorrespondiente = 100;

				// Si el Proveedor es Convenio Multilateral
				if ($proveedor->getClienteProveedor()->getCondicionIngresosBrutos()
								->getDenominacionTipoResponsable() == ConstanteTipoResponsable::CONVENIO_MULTILATERAL) {
					$this->logger->info("Es convenio multilateral");
					$convenioMultilateral = $proveedor->getClienteProveedor()
							->getConvenioMultilateralIngresosBrutos();

					if (null != $convenioMultilateral) {
						// Aplico porcentaje CABA
						$porcentajeCorrespondiente = $convenioMultilateral->getPorcentajeAplicacionCABA();
						$this->logger->info("Porcentaje caba " . $porcentajeCorrespondiente);
					}
				}

				// Si el proveedor tiene riesgo fiscal
				if ($proveedor->getClienteProveedor()->getTieneRiesgoFiscal()) {

					//aplico porcentaje exencion
					$totalNetoComprobantes = $this->actualizarNetoPorRegimen($totalNetoComprobantes, $proveedor, ConstanteTipoImpuesto::IIBB);

					$netoAcumuladoParaComprobante = $totalNetoComprobantes;

					// Aplico Regimen correspondiente al Riesgo Fiscal

					/* @var $regimenRetencion RegimenRetencion */
					$regimenRetencion = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_RIESGO_FISCAL);

					if (!$aplicaRegimenRetencionIIBBCaba) {
						$montoTotal = ($totalNetoComprobantes * $porcentajeCorrespondiente / 100) * $regimenRetencion->getAlicuota() / 100;
					} else {
						$montoTotal = ($totalNetoComprobantes * $porcentajeCorrespondiente / 100) * $alicuotaIIBBCaba / 100;
					}
					
					$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPago);
						
					$montoRetencionPagoParcial = 0;
					foreach($pagosParciales as $pagoParcial) {
						$montoRetencionPagoParcial += $pagoParcial->getRetencionIibb();
					}
					
					$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
					
					// Si tiene pagos parciales, se lo resto del calculo
					$montoTotal -= $montoRetencionPagoParcial;
                    
                    $epsilon = 0.01;
                                
                    if (abs($montoTotal) <= $epsilon) {
                        $montoTotal = 0;
                    }
					
					$this->logger->info("Total final: " . $montoTotal);

					$comprobanteRetencion = $this->nuevoComprobanteRetencion(
							$ordenPago, //
							$regimenRetencion, //
							$estadoGenerado, //
							$montoTotal, //
							$netoAcumuladoParaComprobante
					);

					$this->actualizarErroresExencion(ConstanteTipoImpuesto::IIBB, $proveedor, true);

					$ordenPago->addRetencion($comprobanteRetencion);

					$recorroBienes = false;
				} else {
					// Si el Proveedor es Monotributista
					if ($proveedor->getClienteProveedor()->getCondicionIVA()
									->getDenominacionTipoResponsable() == ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO) {

						// Me fijo si está en la base de Magnitudes Superadas
						if ($proveedor->getClienteProveedor()->getIncluyeMagnitudesSuperadas()) {

							//aplico porcentaje exencion
							$totalNetoComprobantes = $this->actualizarNetoPorRegimen($totalNetoComprobantes, $proveedor, ConstanteTipoImpuesto::IIBB);

							$netoAcumuladoParaComprobante = $totalNetoComprobantes;


							// Aplico Regimen correspondiente a Magnitudes Superadas

							/* @var $regimenRetencion RegimenRetencion */
							$regimenRetencion = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_MAGNITUDES_SUPERADAS);

							if (!$aplicaRegimenRetencionIIBBCaba) {
								$montoTotal = ($totalNetoComprobantes * $porcentajeCorrespondiente / 100) * $regimenRetencion->getAlicuota() / 100;
							} else {
								$montoTotal = ($totalNetoComprobantes * $porcentajeCorrespondiente / 100) * $alicuotaIIBBCaba / 100;
							}
							
							$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPago);
						
							$montoRetencionPagoParcial = 0;
							foreach($pagosParciales as $pagoParcial) {
								$montoRetencionPagoParcial += $pagoParcial->getRetencionIibb();
							}
							
							$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
							
							// Si tiene pagos parciales, se lo resto del calculo
							$montoTotal -= $montoRetencionPagoParcial;
                            
                            $epsilon = 0.01;
                                
                            if (abs($montoTotal) <= $epsilon) {
                                $montoTotal = 0;
                            }
							
							$this->logger->info("Total final: " . $montoTotal);

							$comprobanteRetencion = $this->nuevoComprobanteRetencion(
									$ordenPago, //
									$regimenRetencion, //
									$estadoGenerado, //
									$montoTotal, //
									$netoAcumuladoParaComprobante
							);

							$this->actualizarErroresExencion(ConstanteTipoImpuesto::IIBB, $proveedor, true);

							$ordenPago->addRetencion($comprobanteRetencion);

							$recorroBienes = false;
						}
					}
				}
			} else {
				$recorroBienes = false;
				$this->logger->info("El total de los comprobantes no excede el mínimo exento");
			}

			if ($recorroBienes) {
                
                $esComprobanteNoGravado = false;

				foreach ($regimenes_comprobante as $regimen) {
                    
					$this->logger->info("-----------------------------");

					//aplico porcentaje exencion
					$regimen['total'] = $this->actualizarNetoPorRegimen($regimen['total'], $proveedor, ConstanteTipoImpuesto::IIBB);

					$netoAcumuladoParaComprobante = $regimen['total'];
					
					// Obtengo el monto y le aplico el porcentaje correspondiente
					$montoTotal = $regimen['total'] * $porcentajeCorrespondiente / 100;
						
					if (!$aplicaRegimenRetencionIIBBCaba) {

						$regimenRetencion = $regimen['regimen'];
						$this->logger->info("Regimen " . $regimen['regimen']);
						$this->logger->info("Alicuota " . $regimen['regimen']->getAlicuota());
						$this->logger->info("Porcentaje aplicado " . $porcentajeCorrespondiente);
						$this->logger->info("Monto base calculo " . $regimen['total']);
						$this->logger->info("Calculo = ((" . $regimen['total'] . " * " . $porcentajeCorrespondiente . ") /100) * " . $regimenRetencion->getAlicuota() . " / 100");

						$montoTotal *= $regimenRetencion->getAlicuota() / 100;
                        
					} else {
						
                        if ($regimen['regimen'] != null && $regimen['regimen']->getCodigo() != 'CODIGO_NO_AGRAVADO') {
                            // Si el regimen del renglon del comprobante o tipo de obra o tipo de documento financiero
                            // es diferente de "no gravado", se le aplica el IIBB CABA
                            
                            $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
                            $regimenRetencion = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')
                                ->findOneByCodigo(ConstanteRegimenRetencion::CODIGO_IIBB_CABA);
                            
                            $montoTotal *= $alicuotaIIBBCaba / 100;
						
                            $this->logger->info("Regimen " . $regimenRetencion->getDenominacion());
                            $this->logger->info("Alicuota " . $alicuotaIIBBCaba);
                            $this->logger->info("Porcentaje aplicado " . $porcentajeCorrespondiente);
                            $this->logger->info("Monto base calculo " . $regimen['total']);
                            $this->logger->info("Calculo = ((" . $regimen['total'] . " * " . $porcentajeCorrespondiente . ") /100) * " . $alicuotaIIBBCaba . " / 100");
                            
                        } else {
                            // es "No gravado" o no tiene regimen
                            $regimenRetencion = $regimen['regimen'];
                            $montoTotal = 0;
                            $this->logger->info("Regimen " . $regimen['regimen']);
                            $esComprobanteNoGravado = true;
                        }
					}
					
					$pagosParciales = $this->getPagosParcialesByProveedor($proveedor, $ordenPago);
						
					$montoRetencionPagoParcial = 0;
					foreach($pagosParciales as $pagoParcial) {
						$montoRetencionPagoParcial += $pagoParcial->getRetencionIibb();
					}
					
					$this->logger->info("Tiene retenciones en pagos parciales de: " . $montoRetencionPagoParcial);
					
					// Si tiene pagos parciales, se lo resto del calculo
					$montoTotal -= $montoRetencionPagoParcial;
                    
                    $epsilon = 0.01;
                                
                    if (abs($montoTotal) <= $epsilon) {
                        $montoTotal = 0;
                    }
					
					$this->logger->info("Total final: " . $montoTotal);
                    
                    if (!$esComprobanteNoGravado) {

                        $comprobanteRetencion = $this->nuevoComprobanteRetencion(
                                $ordenPago, //
                                $regimenRetencion, //
                                $estadoGenerado, //
                                $montoTotal, //
                                $netoAcumuladoParaComprobante
                        );
                        
                        $this->logger->info("TOTAL = " . $montoTotal);
                        $this->logger->info("-----------------------------");

                        $this->actualizarErroresExencion(ConstanteTipoImpuesto::IIBB, $proveedor, true);

                        $ordenPago->addRetencion($comprobanteRetencion);
                        
                    }
				}
			}
		} else {
			$this->actualizarErroresExencion(ConstanteTipoImpuesto::IIBB, $proveedor, true);
			$this->logger->info("El proveedor no es pasible de retención");
		}
			
    }

    /**
     * 
     * @param type $comprobanteRetencionImpuesto
     * @return type
     */
    public function getBaseImponibleComprobantesRetencion($comprobanteRetencionImpuesto) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());

        $base_calculo_total = 0;

        foreach ($comprobanteRetencionImpuesto->getOrdenPago()->getComprobantes() as $comprobante) {
            /* @var $comprobante Comprobante */
            foreach ($comprobante->getRenglonesComprobante() as $renglon) {

                $bienEconomico = null;
                switch ($comprobanteRetencionImpuesto->getTipoComprobanteRetencion()) {
                    case 'COMPRAS':
                        $renglonOrdenCompra = $emCompras->getRepository('ADIFComprasBundle:RenglonOrdenCompra')->find($renglon->getIdRenglonOrdenCompra());

                        if ($renglonOrdenCompra->getRenglonCotizacion() != null) {
                            $bienEconomico = $renglonOrdenCompra->getRenglonCotizacion()->getRenglonRequerimiento()->getRenglonSolicitudCompra()->getBienEconomico();
                        } else {
                            $bienEconomico = $renglonOrdenCompra->getBienEconomico();
                        }
                    case 'CONSULTORIA':
                        $bienEconomico = ($bienEconomico == null ? $this->getBienEconomicoFromComprobanteConsultoria($comprobanteRetencionImpuesto->getOrdenPago()->getComprobantes()->first()) : $bienEconomico);
                        $regimenBien = $emContable->getRepository('ADIFContableBundle:RegimenRetencionBienEconomico')->findOneBy(
                                array(
                                    'regimenRetencion' => $comprobanteRetencionImpuesto->getRegimenRetencion(),
                                    'idBienEconomico' => $bienEconomico->getId()
                                )
                        );
                        $regimenRenglon = $regimenBien ? $regimenBien->getRegimenRetencion() : null;
                        break;
                    case 'OBRAS':
                        $tipoDocumentoFinanciero = $renglon->getTipoDocumentoFinanciero();
                        if ($renglon->getComprobante()->getTramo()->getLicitacion()->getId() == 67) {
                            switch ($comprobanteRetencionImpuesto->getRegimenRetencion()->getTipoImpuesto()->getDenominacion()) {
                                case ConstanteTipoImpuesto::Ganancias:
                                    $regimenRenglon = $emContable
                                            ->getRepository('ADIFContableBundle:RegimenRetencion')
                                            ->find(10);
                                    break;
                                case ConstanteTipoImpuesto::IIBB:
                                    $regimenRenglon = $emContable
                                            ->getRepository('ADIFContableBundle:RegimenRetencion')
                                            ->find(26);
                                    break;
                                case ConstanteTipoImpuesto::SUSS:
                                    $regimenRenglon = $this->getRegimenRetencionByCodigo(ConstanteRegimenRetencion::CODIGO_RG_1784);

                                    break;
                                case ConstanteTipoImpuesto::IVA:
                                    $regimenRenglon = $tipoDocumentoFinanciero->getRegimenRetencionIVA();
                                    break;
                            }
                        } else {
                            $tipoObra = $renglon->getComprobante()->getTramo()->getTipoObra();
                            switch ($comprobanteRetencionImpuesto->getRegimenRetencion()->getTipoImpuesto()->getDenominacion()) {
                                case ConstanteTipoImpuesto::Ganancias:
                                    $regimenRenglon = ($tipoObra->getRegimenRetencionGanancias() == null) ? $tipoDocumentoFinanciero->getRegimenRetencionGanancias() : $tipoObra->getRegimenRetencionGanancias();
                                    break;
                                case ConstanteTipoImpuesto::IIBB:
                                    $regimenRenglon = ($tipoObra->getRegimenRetencionIIBB() == null) ? $tipoDocumentoFinanciero->getRegimenRetencionIIBB() : $tipoObra->getRegimenRetencionIIBB();
                                    break;
                                case ConstanteTipoImpuesto::SUSS:
                                    $regimenRenglon = $tipoObra->getRegimenRetencionSUSS();
                                    break;
                                case ConstanteTipoImpuesto::IVA:
                                    $regimenRenglon = ($tipoObra->getRegimenRetencionIVA() == null) ? $tipoDocumentoFinanciero->getRegimenRetencionIVA() : $tipoObra->getRegimenRetencionIVA();
                                    break;
                            }
                        }

                        $regimenRenglon = (($this->getAplicaRegimenRetencion($regimenRenglon)) && ($regimenRenglon->getId() == $comprobanteRetencionImpuesto->getRegimenRetencion()->getId())) //
                                ? $regimenRenglon //
                                : null;
                        break;
                }
                if ($this->getAplicaRegimenRetencion($regimenRenglon)) {
                    $base_calculo_total += $renglon->getMontoNetoBonificado() * ($comprobante->getEsNotaCredito() ? -1 : 1);
                }
            }
        }
        return $base_calculo_total;
    }

    /**
     * 
     * @return type
     */
    private function getRegimenesRetencion() {

        if ($this->regimenesRetencion == null) {

            $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

            $this->regimenesRetencion = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')
                    ->createQueryBuilder('rr')
                    ->select('partial rr.{id, denominacion, codigo, minimoExento, minimoNoImponible, minimoRetencion, alicuota, usaTabla}')
                    ->where('rr.codigo IS NOT NULL')
                    ->getQuery()
                    ->getResult();
        }

        return $this->regimenesRetencion;
    }

    /**
     * 
     * @return type
     */
    private function getRegimenRetencionByCodigo($codigoRegimenRetencion) {

//      $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
//
//      return $emContable->getRepository('ADIFContableBundle:RegimenRetencion')
//                      ->findOneByCodigo($codigoRegimenRetencion);

        $regimenRetencionEncontrado = null;

        foreach ($this->getRegimenesRetencion() as $regimenRetencion) {

            if ($regimenRetencion->getCodigo() == $codigoRegimenRetencion) {

                $regimenRetencionEncontrado = $regimenRetencion;

                break;
            }
        }

        return $regimenRetencionEncontrado;
    }

    /**
     * 
     * @param type $denominacionTipoImpuesto
     * @param type $idBienEconomico
     * @return type
     */
    private function getRegimenRetencionByImpuestoYBienEconomico($denominacionTipoImpuesto, $idBienEconomico) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $regimenRetencionEncontrado = $emContable->getRepository('ADIFContableBundle:RegimenRetencion')
                ->getRegimenRetencionByImpuestoYBienEconomico($denominacionTipoImpuesto, $idBienEconomico);

//        $regimenRetencionEncontrado = null;
//
//        foreach ($this->getRegimenesRetencion() as $regimenRetencion) {
//
//            /* @var $regimenRetencion RegimenRetencion */
//
//            if ($regimenRetencion->getTipoImpuesto()->getDenominacion() == $denominacionTipoImpuesto) {
//
//                foreach ($regimenRetencion->getRegimenesRetencionBienEconomico() as $regimenesRetencionBienEconomico) {
//
//                    if ($regimenesRetencionBienEconomico->getIdBienEconomico() == $idBienEconomico) {
//
//                        $regimenRetencionEncontrado = $regimenRetencion;
//
//                        break 2;
//                    }
//                }
//            }
//        }
//
        return $regimenRetencionEncontrado;
    }

    /**
     * 
     * @return type
     */
    private function getEstadoComprobanteRetencionImpuestoGenerado() {

        if ($this->estadoGenerado == null) {

            $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

            $this->estadoGenerado = $emContable
                    ->getRepository('ADIFContableBundle:EstadoComprobanteRetencionImpuesto')
                    ->findOneByDenominacionEstado(ConstanteEstadoComprobanteRetencionImpuesto::ESTADO_GENERADO);
        }

        return $this->estadoGenerado;
    }

    /**
     * 
     * @param RegimenRetencion $regimenRetencion
     * @return type
     */
    private function getAplicaRegimenRetencion(RegimenRetencion $regimenRetencion = null) {

        return $regimenRetencion != null //
                && $regimenRetencion->getCodigo() != ConstanteRegimenRetencion::CODIGO_NO_GRAVADO;
    }

    private function getComprobantesAcumuladosSinRetencionProveedor($regimenRetencion, $tipoImpuesto, $idProveedor, $fecha, $anual = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $fechaInicio = $anual ? $fecha->format('Y') . '-01-01 00:00:00' : $fecha->format('Y-m') . '-01 00:00:00';

        $totalRegimen = 0;

        /////OBRAS////
        
        $idsOPObrasRet = [];

        $opObraConRetencionIndicada = $emContable->getRepository('ADIFContableBundle:Obras\OrdenPagoObra')
                ->createQueryBuilder('op')
                ->select('partial op.{id}')
				->innerJoin('op.estadoOrdenPago', 'eop')
                ->innerJoin('op.retenciones', 'r')
                ->where('eop.denominacionEstado != :estadoAnulado')
                ->andWhere('op.fechaContable BETWEEN :fechaInicio AND :fechaFin')
                ->andWhere('r.regimenRetencion = :regimen')
                ->andWhere('op.idProveedor = :idProveedor')
                ->groupBy('r.ordenPago')
                ->setParameter('estadoAnulado', ConstanteEstadoOrdenPago::ESTADO_ANULADA)
                ->setParameter('fechaInicio', $fechaInicio)
                ->setParameter('fechaFin', $fecha->format('Y-m-d 23:59:59'))
                ->setParameter('regimen', $regimenRetencion)
                ->setParameter('idProveedor', $idProveedor)
                ->getQuery()
                ->getResult();

        foreach ($opObraConRetencionIndicada as $opRetObras) {
            $idsOPObrasRet[] = $opRetObras->getId();
        }
        
        $comprobantesObra = $emContable->getRepository('ADIFContableBundle:Obras\ComprobanteObra')
			->createQueryBuilder('c')
			->innerJoin('c.ordenPago', 'op')
            ->innerJoin('op.estadoOrdenPago', 'eop')                    
			->where('eop.denominacionEstado != :estadoAnulado')
            ->andWhere('op.fechaContable BETWEEN :fechaInicio AND :fechaFin')
            ->andWhere('op.idProveedor = :idProveedor');
			
        if (!empty($opObraConRetencionIndicada)) {
            $comprobantesObra
                    ->andWhere('op.id NOT IN (:idsOPRet)')
                    ->setParameter('idsOPRet', $idsOPObrasRet, Connection::PARAM_STR_ARRAY);
        }

        $comprobantesObra                
            ->setParameter('estadoAnulado', ConstanteEstadoOrdenPago::ESTADO_ANULADA)
            ->setParameter('fechaInicio', $fechaInicio)
            ->setParameter('fechaFin', $fecha->format('Y-m-d 23:59:59'))
            ->setParameter('idProveedor', $idProveedor);
        
        $comprobantes = $comprobantesObra->getQuery()->getResult();
        
        $regimenes_comprobantes_obras = $this->getRenglonesComprobantesObraAgrupadosByRegimen($tipoImpuesto, $comprobantes);

        $totalRegimen += isset($regimenes_comprobantes_obras[$regimenRetencion->getId()]) ? $regimenes_comprobantes_obras[$regimenRetencion->getId()]['total'] : 0;
        
        /////COMPRAS////
        
        $idsOPComprasRet = [];
        
        $opCompraConRetencionIndicada = $emContable->getRepository('ADIFContableBundle:OrdenPagoComprobante')
			->createQueryBuilder('op')
			->select('partial op.{id}')
            ->innerJoin('op.estadoOrdenPago', 'eop')
            ->leftJoin('op.retenciones', 'r')
            ->where('eop.denominacionEstado != :estadoAnulado')
            ->andWhere('op.fechaContable BETWEEN :fechaInicio AND :fechaFin')
            ->andWhere('r.regimenRetencion = :regimen')
            ->andWhere('op.idProveedor = :idProveedor')
            ->groupBy('r.ordenPago')
            ->setParameter('estadoAnulado', ConstanteEstadoOrdenPago::ESTADO_ANULADA)
            ->setParameter('fechaInicio', $fechaInicio)
            ->setParameter('fechaFin', $fecha->format('Y-m-d 23:59:59'))
            ->setParameter('regimen', $regimenRetencion)
            ->setParameter('idProveedor', $idProveedor)
            ->getQuery()
            ->getResult();

        foreach ($opCompraConRetencionIndicada as $opRetCompras) {
            $idsOPComprasRet[] = $opRetCompras->getId();
        }
        
        $comprobantesCompra = $emContable->getRepository('ADIFContableBundle:ComprobanteCompra')
			->createQueryBuilder('c')
			->innerJoin('c.ordenPago', 'op')
            ->innerJoin('op.estadoOrdenPago', 'eop')                    
            ->where('eop.denominacionEstado != :estadoAnulado')
            ->andWhere('op.fechaContable BETWEEN :fechaInicio AND :fechaFin')
            ->andWhere('op.idProveedor = :idProveedor');
			
        if (!empty($opCompraConRetencionIndicada)) {
            $comprobantesCompra
                    ->andWhere('op.id NOT IN (:idsOPRet)')
                    ->setParameter('idsOPRet', $idsOPComprasRet, Connection::PARAM_STR_ARRAY);
        }
		
        $comprobantesCompra                
            ->setParameter('estadoAnulado', ConstanteEstadoOrdenPago::ESTADO_ANULADA)
            ->setParameter('fechaInicio', $fechaInicio)
            ->setParameter('fechaFin', $fecha->format('Y-m-d 23:59:59'))
            ->setParameter('idProveedor', $idProveedor);
        
        $comprobantes = $comprobantesCompra->getQuery()->getResult();

        $regimenes_comprobantes_compra = $this->getRenglonesComprobantesAgrupadosByRegimen($tipoImpuesto, $comprobantes);

        $totalRegimen += isset($regimenes_comprobantes_compra[$regimenRetencion->getId()]) ? $regimenes_comprobantes_compra[$regimenRetencion->getId()]['total'] : 0;

        return $totalRegimen;
    }
	
	/** PAGOS PARCIALES **/
	
	public function generarComprobantesRetencionPagoParcial(OrdenPagoPagoParcial $ordenPagoPagoParcial, $esComprobanteObra)
	{
		$this->esComprobanteObraPP = $esComprobanteObra;
		
		/* @var $proveedor Proveedor */
		$proveedor = $ordenPagoPagoParcial->getProveedor();
		
		if ($esComprobanteObra) {
			
			$this->logger->info("CÁLCULO RETENCIONES (PAGO PARCIAL) " . date('d/m/Y H:i:s'));
			$this->logger->info("");
			$this->logger->info("------------------------------------------------------------------------------");
			$this->logger->info("Proveedor : " . $proveedor);
			$this->logger->info("------------------------------------------------------------------------------");

			// Si el Proveedor NO es extranjero
			if (!$proveedor->getClienteProveedor()->getEsExtranjero()) {

				$this->retencionGananciasObras($proveedor, $ordenPagoPagoParcial);

				$this->retencionIIBBObras($proveedor, $ordenPagoPagoParcial);

				$this->retencionIVAObras($proveedor, $ordenPagoPagoParcial);

				$this->retencionSUSSObras($proveedor, $ordenPagoPagoParcial);
	
				return $this->erroresExencion;
			}
			
		} else {
			
			$this->logger->info("CÁLCULO RETENCIONES (PAGO PARCIAL) " . date('d/m/Y H:i:s'));
			$this->logger->info("");
			$this->logger->info("------------------------------------------------------------------------------");
			$this->logger->info("Proveedor : " . $proveedor);
			$this->logger->info("Id Proveedor : " . $proveedor->getId());
			$this->logger->info("------------------------------------------------------------------------------");

			// Si el Proveedor NO es extranjero
			if (!$proveedor->getClienteProveedor()->getEsExtranjero()) {

				$this->retencionGanancias($proveedor, $ordenPagoPagoParcial);

				$this->retencionIIBB($proveedor, $ordenPagoPagoParcial);

				$this->retencionIVA($proveedor, $ordenPagoPagoParcial);

				$this->retencionSUSS($proveedor, $ordenPagoPagoParcial);
	
				return $this->erroresExencion;
			}
			
			$this->logger->info("------------------------------FIN RETENCIONES---------------------------------");
			$this->logger->info("------------------------------------------------------------------------------");
		}
	}
	
	public function getPagosParcialesByProveedor(Proveedor $proveedor, OrdenPago $ordenPago)
	{
		$emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
		
		$comprobantes = $ordenPago->getComprobantes();
		
		$pagosParciales = $emContable->getRepository('ADIFContableBundle:PagoParcial')
			->createQueryBuilder('pp')
			->select('pp')
			->innerJoin('pp.ordenPago', 'op')
			->innerJoin('op.estadoOrdenPago', 'eop')
			->innerJoin('pp.comprobante', 'c')
            ->where('eop.denominacionEstado != :estadoAnulado')
			->andWhere('op.idProveedor = :idProveedor')
			->andWhere('c.id = :idComprobante')
			->setParameter('estadoAnulado', ConstanteEstadoOrdenPago::ESTADO_ANULADA)
            ->setParameter('idProveedor', $proveedor->getId())
			->setParameter('idComprobante', $comprobantes->first()->getId())
            ->getQuery()
            ->getResult();
			
		return $pagosParciales;
	}
}
