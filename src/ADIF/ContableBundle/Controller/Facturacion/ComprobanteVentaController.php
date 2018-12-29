<?php

namespace ADIF\ContableBundle\Controller\Facturacion;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\BaseBundle\Entity\AdifApi;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Entity\ClienteProveedor;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteAfip;
use ADIF\ContableBundle\Entity\Constantes\ConstanteAlicuotaIva;
use ADIF\ContableBundle\Entity\Constantes\ConstanteClaseContrato;
use ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoPercepcion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoContrato;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteVenta;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion;
use ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta;
use ADIF\ContableBundle\Entity\Facturacion\Contrato;
use ADIF\ContableBundle\Entity\Facturacion\CuponVentaPlazo;
use ADIF\ContableBundle\Entity\Facturacion\FacturaVenta;
use ADIF\ContableBundle\Entity\Facturacion\NotaDebitoInteres;
use ADIF\ContableBundle\Entity\Facturacion\RenglonComprobanteVenta;
use ADIF\ContableBundle\Entity\Facturacion\Talonario;
use ADIF\ContableBundle\Entity\RenglonComprobante;
use ADIF\ContableBundle\Entity\RenglonPercepcion;
use ADIF\ContableBundle\Form\Facturacion\ComprobantePliegoCompraType;
use ADIF\ContableBundle\Form\Facturacion\ComprobantePliegoObraType;
use ADIF\ContableBundle\Form\Facturacion\ComprobanteVentaGeneralType;
use ADIF\ContableBundle\Form\Facturacion\ComprobanteVentaType;
//use BG\BarcodeBundle\Util\Base1DBarcode as barCode;
use DateTime;
use mPDF;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoRegimenPercepcion;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * ComprobanteVenta controller.
 *
 * @Route("/comprobanteventa")
 */
class ComprobanteVentaController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;
    private $limitaGeneracion;
    private $errorIIBB;
    private $erroresIIBB;

    /**
     * NUMERO_PUNTO_VENTA_POR_DEFECTO
     */
    const NUMERO_PUNTO_VENTA_POR_DEFECTO = "0002";

    /**
     * ALICUOTA_IVA_GENERAL
     */
    const ALICUOTA_IVA_GENERAL = 0;

    /**
     * ALICUOTA_IVA_PLIEGO
     */
    const ALICUOTA_IVA_PLIEGO = 21;
	
	
    /**
     * 
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->limitaGeneracion = 0;
        $this->errorIIBB = 0;
        $this->erroresIIBB = array();
        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Lists all ComprobanteVenta entities.
     *
     * @Route("/", name="comprobanteventa")
     * @Method("GET")
     * @Template()
     */
//     * @Security("has_role('ROLE_VISUALIZAR_FACTURAS')")
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de venta'] = null;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $estadoAnulado = $em->getRepository('ADIFContableBundle:EstadoComprobante')
                ->find(EstadoComprobante::__ESTADO_ANULADO);

        return array(
            'breadcrumbs' => $bread,
            'id_anulado' => $estadoAnulado->getId(),
            'page_title' => 'Comprobantes de venta',
            'page_info' => 'Lista de comprobantes de venta'
        );
    }

    /**
     * Tabla para ComprobanteVenta .
     *
     * @Route("/index_table/", name="comprobanteventa_table")
     * @Method("GET|POST")
     */
    public function indexTableAction(Request $request) {

        $comprobantes = array();

        if ($request->query->get('fechaInicio') && $request->query->get('fechaFin')) {

            $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaInicio') . ' 00:00:00');
            $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaFin') . ' 23:59:59');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $rsm = new ResultSetMapping();

            $rsm->addScalarResult('id', 'id');
            $rsm->addScalarResult('fecha_comprobante', 'fechaComprobante');
            $rsm->addScalarResult('fecha_contable', 'fechaContable');
            $rsm->addScalarResult('nombre', 'tipoComprobante');
            $rsm->addScalarResult('letra', 'letra');
            $rsm->addScalarResult('puntoVenta', 'puntoVenta');
            $rsm->addScalarResult('numeroComprobante', 'numeroComprobante');
            $rsm->addScalarResult('numeroCupon', 'numeroCupon');
            $rsm->addScalarResult('numeroContrato', 'numeroContrato');
            $rsm->addScalarResult('cliente', 'cliente');
            $rsm->addScalarResult('observaciones', 'observaciones');
            $rsm->addScalarResult('importeTotalNeto', 'importeTotalNeto');
            $rsm->addScalarResult('importeTotalIVA', 'importeTotalIVA');
            $rsm->addScalarResult('percepcionIIBB', 'percepcionIIBB');
            $rsm->addScalarResult('percepcionIVA', 'percepcionIVA');
            $rsm->addScalarResult('totalMCL', 'totalMCL');
            $rsm->addScalarResult('estadoComprobante', 'estadoComprobante');
            $rsm->addScalarResult('idEstadoComprobante', 'idEstadoComprobante');
            $rsm->addScalarResult('licitacion', 'licitacion');

            $native_query = $em->createNativeQuery('
            SELECT
                id,
                fecha_comprobante,
                fecha_contable,
                nombre,
                letra,
                puntoVenta,
                numeroComprobante,
                numeroCupon,
                numeroContrato,
                cliente,
                observaciones,
                importeTotalNeto,
                importeTotalIVA,
                percepcionIIBB,
                percepcionIVA,
                totalMCL,
                estadoComprobante,
                idEstadoComprobante,
                licitacion
            FROM
                vistacomprobanteventa
            WHERE fecha_contable BETWEEN ? AND ?
        ', $rsm);

            $native_query->setParameter(1, $fechaInicio, Type::DATETIME);
            $native_query->setParameter(2, $fechaFin, Type::DATETIME);

            $comprobantes = $native_query->getResult();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de venta'] = null;

        return $this->render('ADIFContableBundle:Facturacion/ComprobanteVenta:index_table.html.twig', array(
                    'conceptoPercepcionIIBB' => ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB,
                    'conceptoPercepcionIVA' => ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA,
                    'entities' => $comprobantes,
                    'id_anulado' => EstadoComprobante::__ESTADO_ANULADO
                        )
        );
    }

    /**
     * @Route("/facturaautomaticastep1", name="comprobanteventa_factura_automatica_step_1")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVenta:generacionFacturaAutomatica.step1.html.twig")
     */
//     * @Security("has_role('ROLE_CREAR_MODIFICAR_FACTURAS')")
    public function generarFacturaAutomaticaStep1Action(Request $request) {
        $this->get('session')->remove('comprobantes_automaticos');
        $this->get('session')->remove('comprobantes_elegidos');
        $this->get('session')->remove('comprobantes_generados');
        $this->get('session')->remove('comprobantes_electronicos');
        $this->get('session')->remove('talonarios');
        $this->get('session')->remove('comprobantes_por_contrato');

        $this->get('session')->remove('post_step1');
        $this->get('session')->remove('post_step2');


        $this->get('session')->remove('esMCL');
        $this->get('session')->remove('tipoContrato');
        $this->get('session')->remove('moneda');

        $ids = $request->request->get('ids');
        $this->get('session')->set('post_step1', $ids);
        $tipoCambio = $request->request->get('tipo-cambio');
        $idMoneda = $request->request->get('idMoneda');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        if ($tipoCambio != null) {
            $this->get('adif.tipomoneda_service')->setTipoCambio($idMoneda, $tipoCambio);
        } else {
            $tipoCambio = 1;
        }

        setlocale(LC_ALL, "es_AR.UTF-8");

        $tipoContrato = '';
        $moneda = '';
        $esMCL = false;
        $mesFacturacion = ucfirst(strftime("%B %Y", (new DateTime())->getTimestamp()));
        $esCupon = false;

        $comprobantes = array();
        $idComprobanteGenerado = 0;

        $cuotasPorCiclos = $this->get('adif.contrato_service')->getCuotasPorCiclos();
        // Por cada contrato seleccionado en el index
        foreach ($ids as $idContrato) {
            /* @var $contrato Contrato */
            $contrato = $em->getRepository('ADIFContableBundle:Facturacion\Contrato')->find($idContrato);
            $letraCliente = $this->get('adif.contrato_service')->getLetraComprobanteVenta($contrato);

            $tipoContrato = $contrato->getClaseContrato();
            $moneda = $contrato->getTipoMoneda();
            $esMCL = $contrato->getTipoMoneda()->getEsMCL();
            $fecha_vencimiento = new \DateTime(date((new \DateTime())->format('Y-m') . '-' . $contrato->getDiaVencimiento()));
            //$fecha_vencimiento = new \DateTime(date((new \DateTime())->format('Y') . '-08-' . $contrato->getDiaVencimiento()));

            $ciclosFacturacion = $contrato->getCiclosFacturacionPendientes();

            // Facturar hasta fecha de desocupacion
            $fechaLimite = ( ($contrato->getEstadoContrato()->getCodigo() == ConstanteEstadoContrato::DESOCUPADO) && ($contrato->getFechaDesocupacion() != null) ) ? $contrato->getFechaDesocupacion() : new DateTime();


//            $numerosCuotasCanceladas = $this->get('adif.contrato_service')->getNumerosCuotasVentas($ciclosFacturacion);
            foreach ($ciclosFacturacion as $cicloFacturacion) {
                /* @var $cicloFacturacion CicloFacturacion */
                $mes_siguiente_factura = $cicloFacturacion->getFechaInicio()->format('m') + ($cicloFacturacion->getCantidadFacturasEmitidas() * $cicloFacturacion->getCantidadUnidadTiempo() * $cicloFacturacion->getUnidadTiempo()->getCantidadMeses());

                if ($mes_siguiente_factura > 12) {
                    $anio_num = intval($cicloFacturacion->getFechaInicio()->format('Y')) + floor($mes_siguiente_factura / 12);
                    $mes_siguiente_factura = $mes_siguiente_factura % 12;
                    if ($mes_siguiente_factura == 0) {
                        $mes_siguiente_factura = 12; //el mod va de 0 a 11 => 0 representa diciembre
                        $anio_num = $anio_num - 1;
                    }
                    $anio = strval($anio_num) . '-';
                } else {
                    $anio = $cicloFacturacion->getFechaInicio()->format('Y-');
                }


                $fecha_limite_factura = new DateTime(date($anio . $mes_siguiente_factura . '-' . $contrato->getDiaVencimiento()));
                $fecha_limite_ciclo = $cicloFacturacion->getFechaFin() > $fecha_vencimiento ? $fecha_vencimiento : $cicloFacturacion->getFechaFin();
                while ((
                $cicloFacturacion->getCantidadFacturasPendientes() > 0 &&
                $fecha_limite_factura <= $fecha_limite_ciclo) && ($fecha_limite_factura->format('Ym') <= $fechaLimite->format('Ym'))) {
                    //Creo el comprobante
                    if ($contrato->getEsContratoAlquiler()) {
                        $comprobante = ConstanteTipoComprobanteVenta::getSubclassFromContrato($contrato->getClaseContrato()->getId());
                        $comprobante->setLetraComprobante($em->getRepository('ADIFContableBundle:LetraComprobante')->findOneByLetra($letraCliente));
                        $comprobante->setTipoComprobante($em->getRepository('ADIFContableBundle:TipoComprobante')->find(ConstanteTipoComprobanteCompra::FACTURA));

                        //fechas de servicio
                        $comprobante->setFechaInicioServicio(new DateTime($cicloFacturacion->getFechaInicio()->format('Y-' . $mes_siguiente_factura . '-01')));
                        $comprobante->setFechaFinServicio(new DateTime($cicloFacturacion->getFechaInicio()->format('Y-' . $mes_siguiente_factura . '-t')));
                    } else {
                        $comprobante = new CuponVentaPlazo();
                        $comprobante->setTipoComprobante($em->getRepository('ADIFContableBundle:TipoComprobante')->find(ConstanteTipoComprobanteCompra::CUPON));
                        $esCupon = true;
                    }
                    $comprobante->setCicloFacturacion($cicloFacturacion);

                    $comprobante->setContrato($contrato);
                    $comprobante->setTipoMoneda($contrato->getTipoMoneda());
                    $comprobante->setTipoCambio($tipoCambio);

                    $comprobante->setEstadoComprobante($em->getRepository('ADIFContableBundle:EstadoComprobante')->find(EstadoComprobante::__ESTADO_INGRESADO));

                    $comprobante->setPeriodo(str_pad($mes_siguiente_factura, 2, '0', STR_PAD_LEFT) . '/' . substr($anio, 0, 4));

                    if ((isset($cuotasPorCiclos[$cicloFacturacion->getId()])) && (count($cuotasPorCiclos[$cicloFacturacion->getId()]) > 0)) {
                        $index = array_keys($cuotasPorCiclos[$cicloFacturacion->getId()])[0];
                        $comprobante->setNumeroCuota($cuotasPorCiclos[$cicloFacturacion->getId()][$index]);
                        unset($cuotasPorCiclos[$cicloFacturacion->getId()][$index]);
                    } else {
                        $comprobante->setNumeroCuota($contrato->getSiguienteNumeroComprobante());
                    }
                    //Decremento la cantidad de facturas pendientes del ciclo de facturacion
                    $cicloFacturacion->setCantidadFacturasPendientes($cicloFacturacion->getCantidadFacturasPendientes() - 1);

                    if ($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES) {
                        $comprobante->setObservaciones('Intereses resarcitorios correspondients al contrato ' . $contrato->getNumeroContrato());
                    } else {
                        $comprobante->setObservaciones($contrato->getClaseContrato() . ' - Correspondiente a la cuota ' . $comprobante->getNumeroCuota() . ' del contrato ' . $contrato->getNumeroContrato());
                    }

                    $total_comprobante = $cicloFacturacion->getImporte();
                    $total_comprobante_moneda_corriente = $total_comprobante * $tipoCambio;

                    //Verificar exencion
                    $this->actualizarErrorIIBB($contrato->getCliente(), $total_comprobante_moneda_corriente > 300);

                    // Creo el rengl&oacute;n 
                    $renglon_comprobante = new RenglonComprobanteVenta();
                    $renglon_comprobante->setDescripcion($comprobante->getObservaciones());
                    $renglon_comprobante->setCantidad(1);
                    $renglon_comprobante->setPrecioUnitario($cicloFacturacion->getImporte());
                    $renglon_comprobante->setMontoIva(0);
                    $renglon_comprobante->setAlicuotaIva($em->getRepository('ADIFContableBundle:AlicuotaIva')->findOneByValor(ConstanteAlicuotaIva::ALICUOTA_0));

                    $renglon_comprobante->setMontoNeto($cicloFacturacion->getImporte());

                    if ($contrato->getCalculaIVA() && $contrato->getEsContratoAlquiler() && $total_comprobante_moneda_corriente > 1500) {
                        // Si el contrato tiene configurado el flag de c&aacute;lculo de IVA y es un contrato de alquiler y el monto es mayor a 1500                            
                        $alicuota_iva = $contrato->getCliente()->getClienteProveedor()->getAlicuotaIva();

                        $renglon_comprobante->setAlicuotaIva($em->getRepository('ADIFContableBundle:AlicuotaIva')->findOneByValor($alicuota_iva));
                        $renglon_comprobante->setMontoIva($total_comprobante_moneda_corriente * $alicuota_iva / 100);

                        $total_comprobante += $total_comprobante * $alicuota_iva / 100;
                    }

                    $comprobante->addRenglonesComprobante($renglon_comprobante);

                    // Renglon de percepcion de IIBB
                    //$alicuota_iibb = $this->get('adif.percepciones_service')->getAlicuotaIIBB($comprobante->getCliente(), $contrato);
                    $regimenPercepcion = $this->get('adif.percepciones_service')->getRegimenIIBB($comprobante->getCliente(), $contrato);
					
					// Regimen de retencion de IIBB 
					
					// Regimen 421/16 - IIBB CABA
					if ($regimenPercepcion != null 
						&& $regimenPercepcion->getCodigo() != null 
						&& $regimenPercepcion->getCodigo() == ConstanteCodigoRegimenPercepcion::CODIGO_IIBB_CABA) {
						
							$alicuota = $this->get('adif.percepciones_service')->getAlicuotaIIBB($contrato->getCliente());
							$renglon_percepcion = new RenglonPercepcion();
							$renglon_percepcion->setConceptoPercepcion($em->getRepository('ADIFContableBundle:ConceptoPercepcion')->findOneByDenominacion(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB));
							$renglon_percepcion->setJurisdiccion($em->getRepository('ADIFContableBundle:Jurisdiccion')->findOneByCodigo(1));
							$renglon_percepcion->setRegimenPercepcion($regimenPercepcion);
							
							$renglon_percepcion->setMonto($total_comprobante_moneda_corriente * $alicuota / 100);
							$total_comprobante += $cicloFacturacion->getImporte() * $alicuota / 100;
							$comprobante->addRenglonesPercepcion($renglon_percepcion);
							
					} else {

						if ($contrato->getEsContratoAlquiler() && $regimenPercepcion) {

							$porcentajeAlicuota = $this->get('adif.percepciones_service')
									->getPorcentajeAlicuotaIIBB($contrato->getCliente());

							// Si la alicuota no es null, hay que percibirle IIBB
							$renglon_percepcion = new RenglonPercepcion();
							$renglon_percepcion->setConceptoPercepcion($em->getRepository('ADIFContableBundle:ConceptoPercepcion')->findOneByDenominacion(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB));
							$renglon_percepcion->setJurisdiccion($em->getRepository('ADIFContableBundle:Jurisdiccion')->findOneByCodigo(1));
							$renglon_percepcion->setRegimenPercepcion($regimenPercepcion);

							$alicuota_iibb = $porcentajeAlicuota * ($regimenPercepcion->getAlicuota() / 100);

							$renglon_percepcion->setMonto($total_comprobante_moneda_corriente * $alicuota_iibb / 100);

							$total_comprobante += $cicloFacturacion->getImporte() * $alicuota_iibb / 100;

							$comprobante->addRenglonesPercepcion($renglon_percepcion);
						}
					}

                    // Renglon de percepcion de IVA
                    $regimenPercepcionIVA = $this->get('adif.percepciones_service')
                            ->getRegimenIVA($comprobante->getCliente(), $contrato);

                    if ($regimenPercepcionIVA && $total_comprobante_moneda_corriente > 1500) {
                        $renglon_percepcion = new RenglonPercepcion();
                        $renglon_percepcion->setConceptoPercepcion($em->getRepository('ADIFContableBundle:ConceptoPercepcion')->findOneByDenominacion(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA));
                        $renglon_percepcion->setRegimenPercepcion($regimenPercepcionIVA);
                        $renglon_percepcion->setJurisdiccion(null);

                        $alicuota_percepcion_iva = $regimenPercepcionIVA->getAlicuota() / 100;

                        $renglon_percepcion->setMonto(($comprobante->getImporteTotalNeto() + $comprobante->getImporteTotalIVA()) * $alicuota_percepcion_iva);

                        $total_comprobante += ($comprobante->getImporteTotalNeto() + $comprobante->getImporteTotalIVA()) * $alicuota_percepcion_iva;

                        $comprobante->addRenglonesPercepcion($renglon_percepcion);
                    }

                    $comprobante->setTotal($total_comprobante);
                    $comprobante->setSaldo($total_comprobante);

                    $comprobantes[$idComprobanteGenerado++] = $comprobante;

                    $mes_siguiente_factura = $cicloFacturacion->getFechaInicio()->format('m') + ($cicloFacturacion->getCantidadFacturasEmitidas() * $cicloFacturacion->getCantidadUnidadTiempo() * $cicloFacturacion->getUnidadTiempo()->getCantidadMeses());

                    if ($mes_siguiente_factura > 12) {
                        $anio_num = intval($cicloFacturacion->getFechaInicio()->format('Y')) + floor($mes_siguiente_factura / 12);
                        $mes_siguiente_factura = $mes_siguiente_factura % 12;
                        if ($mes_siguiente_factura == 0) {
                            $mes_siguiente_factura = 12; //el mod va de 0 a 11 => 0 representa diciembre
                            $anio_num = $anio_num - 1;
                        }
                        $anio = strval($anio_num) . '-';
                    } else {
                        $anio = $cicloFacturacion->getFechaInicio()->format('Y-');
                    }

                    $fecha_limite_factura = new DateTime(date($anio . $mes_siguiente_factura . '-' . $contrato->getDiaVencimiento()));
                }
            }
        }

        $this->mostrarErrorIIBB();

        $this->get('session')->set('comprobantes_automaticos', $comprobantes);

        $this->get('session')->set('esMCL', $esMCL);
        $this->get('session')->set('tipoContrato', $tipoContrato);
        $this->get('session')->set('moneda', $moneda);

        $bread = $this->base_breadcrumbs;
        $bread['Contratos'] = $this->generateUrl('contrato');
        $bread['Facturaci&oacute;n autom&aacute;tica'] = null;

        return array(
            'tipoContrato' => $tipoContrato,
            'moneda' => $moneda,
            'conceptoPercepcionIIBB' => ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB,
            'conceptoPercepcionIVA' => ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA,
            'moneda' => $moneda,
            'esMCL' => $esMCL,
            'mesFacturacion' => $mesFacturacion,
            'comprobantes' => $comprobantes,
            'esCupon' => $esCupon,
            'breadcrumbs' => $bread,
            'page_title' => 'Facturaci&oacute;n autom&aacute;tica'
        );
    }

    /**
     * 
     * @param type $comprobante
     * @param type $regimenPercepcion
     * @param type $renglonPercepcion
     * @return \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada
     */
    private function crearRenglonDeclaracionJurada($comprobante, $regimenPercepcion, $renglonPercepcion, $tipoImpuesto) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $renglonDDJJ = new \ADIF\ContableBundle\Entity\RenglonDeclaracionJuradaRenglonPercepcion();

        $renglonDDJJ->setRegimenPercepcion($regimenPercepcion);

        $renglonDDJJ->setRenglonPercepcion($renglonPercepcion);

        $renglonDDJJ->setFecha($comprobante->getFechaComprobante());

        $renglonDDJJ->setTipoRenglonDeclaracionJurada(
                $em->getRepository('ADIFContableBundle:TipoRenglonDeclaracionJurada')
                        ->findOneByCodigo(ConstanteTipoRenglonDeclaracionJurada::RENGLON_PERCEPCION)
        );

        $renglonDDJJ->setTipoImpuesto($em->getRepository('ADIFContableBundle:TipoImpuesto')
                        ->findOneByDenominacion($tipoImpuesto)
        );

        $renglonDDJJ->setEstadoRenglonDeclaracionJurada($em->getRepository('ADIFContableBundle:EstadoRenglonDeclaracionJurada')
                        ->findOneByDenominacion(ConstanteEstadoRenglonDeclaracionJurada::PENDIENTE));

        /* @var $comprobante FacturaVenta */

        $renglonDDJJ->setMonto($comprobante->getEsNotaCredito() ? ($renglonPercepcion->getMonto() * (-1)) : $renglonPercepcion->getMonto());
        $renglonDDJJ->setMontoOriginal($renglonPercepcion->getMonto());

        return $renglonDDJJ;
    }

    /**
     * 
     * @param type $talonarios
     * @return string
     */
    private function getPuntosDeVentaArray($talonarios) {
        $puntosVentaArray = array();

        foreach ($talonarios as $talonario) {
            /* @var $talonario Talonario */
            $puntosVentaArray[$talonario['punto_venta']] = $talonario['punto_venta'] . ' ' . $talonario['numero_desde'] . '-' . $talonario['numero_hasta'];
        }

        return $puntosVentaArray;
    }

    /**
     * @Route("/facturaautomaticastep2", name="comprobanteventa_factura_automatica_step_2")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVenta:generacionFacturaAutomatica.step2.html.twig")
     */
//     * @Security("has_role('ROLE_CREAR_MODIFICAR_FACTURAS')")
    public function generarFacturaAutomaticaStep2Action(Request $request) {

        $indices = $request->request->get('indices');
        $this->get('session')->set('post_step2', $indices);

        $comprobantes_automaticos = $this->get('session')->get('comprobantes_automaticos');

        $elegidos = array();
        $elegidos_electronicos = array();
        $talonarios = array();

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        setlocale(LC_ALL, "es_AR.UTF-8");

        $mesFacturacion = ucfirst(strftime("%B %Y", (new DateTime())->getTimestamp()));

        foreach ($indices as $indice) {
            /* @var $facturaGenerada FacturaVenta */
            $facturaGenerada = $comprobantes_automaticos[$indice];

            $letraComprobante = $facturaGenerada->getLetraComprobante();

            $puntoVenta = $em->getRepository('ADIFContableBundle:Facturacion\PuntoVenta')->getPuntoVentaByClaseContratoYMonto($facturaGenerada->getContrato()->getClaseContrato(), $facturaGenerada->getTotalMCL());
            /* @var $puntoVenta \ADIF\ContableBundle\Entity\Facturacion\PuntoVenta */
            $facturaGenerada->setPuntoVenta($puntoVenta);

            if (!$puntoVenta->getGeneraComprobanteElectronico()) {
                if (!isset($talonarios[$facturaGenerada->getLetraComprobante()->getId()][$puntoVenta->getNumero()])) {
                    $talonarios[$facturaGenerada->getLetraComprobante()->getId()][$puntoVenta->getNumero()]['talonarios_libres'] = $em->getRepository('ADIFContableBundle:Facturacion\Talonario')->getTalonariosActivosByLetraYPuntoVenta($facturaGenerada->getLetraComprobante(), $puntoVenta);
                    $talonarios[$facturaGenerada->getLetraComprobante()->getId()][$puntoVenta->getNumero()]['talonario_actual'] = null;
                    $talonarios[$facturaGenerada->getLetraComprobante()->getId()][$puntoVenta->getNumero()]['flag_cambio_talonario'] = false;
                    $talonarios[$facturaGenerada->getLetraComprobante()->getId()][$puntoVenta->getNumero()]['flag_talonarios_vacios'] = false;
                    $talonarios[$facturaGenerada->getLetraComprobante()->getId()][$puntoVenta->getNumero()]['numero_comprobantes'] = 0;
                    $talonarios[$facturaGenerada->getLetraComprobante()->getId()][$puntoVenta->getNumero()]['numero_comprobantes_generados'] = 0;
                    $talonarios[$facturaGenerada->getLetraComprobante()->getId()][$puntoVenta->getNumero()]['letra'] = $facturaGenerada->getLetraComprobante()->__toString();
                }

                /* @var $talonarioContrato Talonario */
                $talonarioContrato = $this->getPrimerTalonarioActivo($talonarios[$letraComprobante->getId()][$puntoVenta->getNumero()]['talonarios_libres']);
                if ($talonarioContrato) {
                    // Si existen talonarios con facturas libres
                    if (!$talonarios[$letraComprobante->getId()][$puntoVenta->getNumero()]['talonario_actual']) {
                        // Si es el primer talonario, lo seteo como actual
                        $talonarios[$letraComprobante->getId()][$puntoVenta->getNumero()]['talonario_actual'] = $talonarioContrato;
                    }
                    if ($talonarios[$letraComprobante->getId()][$puntoVenta->getNumero()]['talonario_actual']->getId() != $talonarioContrato->getId()) {
                        //Si se cambio de talonario, seteamos el flag para alertar al usuario
                        $talonarios[$letraComprobante->getId()][$puntoVenta->getNumero()]['flag_cambio_talonario'] = true;
                    }
                } else {
                    // Si se acabaron todos los talonarios de esa letra
                    $talonarios[$letraComprobante->getId()][$puntoVenta->getNumero()]['flag_talonarios_vacios'] = true;
                }

                if (!$talonarios[$letraComprobante->getId()][$puntoVenta->getNumero()]['flag_talonarios_vacios']) {
                    $talonarios[$letraComprobante->getId()][$puntoVenta->getNumero()]['numero_comprobantes_generados'] ++;
                    $this->get('adif.talonario_service')->getSiguienteNumeroComprobante($talonarioContrato);
                }
                $talonarios[$letraComprobante->getId()][$puntoVenta->getNumero()]['numero_comprobantes'] ++;
            } else {
                if (!isset($elegidos_electronicos[$facturaGenerada->getLetraComprobante()->getLetra()][$puntoVenta->getNumero()])) {
                    $elegidos_electronicos[$facturaGenerada->getLetraComprobante()->getLetra()][$puntoVenta->getNumero()] = array();
                }
                $elegidos_electronicos[$facturaGenerada->getLetraComprobante()->getLetra()][$puntoVenta->getNumero()][$indice] = $facturaGenerada;
            }
            $elegidos[$indice] = $facturaGenerada;
        }

        $this->get('session')->set('comprobantes_elegidos', $elegidos);

        $bread = $this->base_breadcrumbs;
        $bread['Contratos'] = $this->generateUrl('contrato');
        $bread['Facturaci&oacute;n autom&aacute;tica'] = null;


        return array(
            'tipoContrato' => $this->get('session')->get('tipoContrato'),
            'moneda' => $this->get('session')->get('moneda'),
            'esMCL' => $this->get('session')->get('esMCL'),
            'conceptoPercepcionIIBB' => ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB,
            'conceptoPercepcionIVA' => ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA,
            'mesFacturacion' => $mesFacturacion,
            'comprobantes' => $elegidos,
            'comprobantesElectronicos' => $elegidos_electronicos,
            'talonarios' => $talonarios,
            'breadcrumbs' => $bread,
            'post_step1' => $this->get('session')->get('post_step1'),
            'esCupon' => false,
            'page_title' => 'Facturaci&oacute;n autom&aacute;tica'
        );
    }

    /**
     * 
     * @param type $talonarios
     * @return type
     */
    private function getPrimerTalonarioActivo($talonarios) {
        foreach ($talonarios as $talonario) {
            if (!$talonario->getEstaAgotado()) {
                return $talonario;
            }
        }
        return null;
    }

    /**
     * @Route("/facturaautomaticastep3", name="comprobanteventa_factura_automatica_step_3")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVenta:generacionFacturaAutomatica.step3.html.twig")
     */
//     * @Security("has_role('ROLE_CREAR_MODIFICAR_FACTURAS')")
    public function generarFacturaAutomaticaStep3Action(Request $request) {

        $indice_comprobantes_a_generar = $request->request->get('comprobantes');
        $esCupon = $request->request->get('esCupon') == 'true';

        $comprobantes_elegidos = $this->get('session')->get($esCupon ? 'comprobantes_automaticos' : 'comprobantes_elegidos');

        $generados = array();
        $generadosElectronicos = array();
        $talonarios = array();
        $comprobantes_por_contrato = array();

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        setlocale(LC_ALL, "es_AR.UTF-8");

        if ($esCupon) {
            $nroCuponInicial = $this->get('adif.cupon_venta_service')->getSiguienteNumeroCupon();
        }

        $mesFacturacion = ucfirst(strftime("%B %Y", (new DateTime())->getTimestamp()));

        foreach ($indice_comprobantes_a_generar as $indice) {

            $comprobanteGenerado = $comprobantes_elegidos[$indice];

            if ($esCupon) {
                /* @var $comprobanteGenerado CuponVentaPlazo */
                $comprobanteGenerado->setNumeroCupon(str_pad($nroCuponInicial++, 8, '0', STR_PAD_LEFT));
            } else {
                /* @var $comprobanteGenerado FacturaVenta */

                $letraComprobante = $comprobanteGenerado->getLetraComprobante();


                $puntoVenta = $em->getRepository('ADIFContableBundle:Facturacion\PuntoVenta')->getPuntoVentaByClaseContratoYMonto($comprobanteGenerado->getContrato()->getClaseContrato(), $comprobanteGenerado->getTotalMCL());
                if (!$puntoVenta->getGeneraComprobanteElectronico()) {
                    if (!isset($talonarios[$comprobanteGenerado->getLetraComprobante()->getId()][$puntoVenta->getNumero()])) {
                        $talonarios[$comprobanteGenerado->getLetraComprobante()->getId()][$puntoVenta->getNumero()]['talonarios_libres'] = $em->getRepository('ADIFContableBundle:Facturacion\Talonario')->getTalonariosActivosByLetraYPuntoVenta($comprobanteGenerado->getLetraComprobante(), $puntoVenta);
                        $talonarios[$comprobanteGenerado->getLetraComprobante()->getId()][$puntoVenta->getNumero()]['talonario_actual'] = null;
                    }

                    /* @var $talonarioContrato Talonario */
                    $talonarioContrato = $this->getPrimerTalonarioActivo($talonarios[$letraComprobante->getId()][$puntoVenta->getNumero()]['talonarios_libres']);

                    // Si existen talonarios con facturas libres
                    if (!$talonarios[$letraComprobante->getId()][$puntoVenta->getNumero()]['talonario_actual']) {
                        // Si es el primer talonario, lo seteo como actual
                        $talonarios[$letraComprobante->getId()][$puntoVenta->getNumero()]['talonario_actual'] = $talonarioContrato;
                    }

                    $comprobanteGenerado->setNumero($this->get('adif.talonario_service')->getSiguienteNumeroComprobante($talonarioContrato));
                }
                $comprobanteGenerado->setPuntoVenta($puntoVenta);
            }

            $comprobanteGenerado->setFechaComprobante(new DateTime());

            if (($comprobanteGenerado->getPuntoVenta() == null) || ($comprobanteGenerado->getPuntoVenta() != null) && (!$comprobanteGenerado->getPuntoVenta()->getGeneraComprobanteElectronico())) {
                if (!isset($comprobantes_por_contrato[$comprobanteGenerado->getContrato()->getId()])) {
                    $comprobantes_por_contrato[$comprobanteGenerado->getContrato()->getId()] = 0;
                }
                $comprobantes_por_contrato[$comprobanteGenerado->getContrato()->getId()] ++;

                $generados[] = $comprobanteGenerado;
            } else {
                if (!isset($generadosElectronicos[$comprobanteGenerado->getLetraComprobante()->getLetra()][$comprobanteGenerado->getPuntoVenta()->getNumero()])) {
                    $generadosElectronicos[$comprobanteGenerado->getLetraComprobante()->getLetra()][$comprobanteGenerado->getPuntoVenta()->getNumero()] = array();
                }
                $generadosElectronicos[$comprobanteGenerado->getLetraComprobante()->getLetra()][$comprobanteGenerado->getPuntoVenta()->getNumero()][] = $comprobanteGenerado;
            }
        }

        if (!$esCupon) {
            $this->get('session')->set('talonarios', $talonarios);
        }

        $this->get('session')->set('comprobantes_por_contrato', $comprobantes_por_contrato);
        $this->get('session')->set('comprobantes_generados', $generados);
        $this->get('session')->set('comprobantes_electronicos', $generadosElectronicos);
        $this->get('session')->set('esCupon', $esCupon);

        $bread = $this->base_breadcrumbs;
        $bread['Contratos'] = $this->generateUrl('contrato');
        $bread['Facturaci&oacute;n autom&aacute;tica'] = null;

        return array(
            'tipoContrato' => $this->get('session')->get('tipoContrato'),
            'moneda' => $this->get('session')->get('moneda'),
            'esMCL' => $this->get('session')->get('esMCL'),
            'conceptoPercepcionIIBB' => ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB,
            'conceptoPercepcionIVA' => ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA,
            'mesFacturacion' => $mesFacturacion,
            'comprobantes' => $generados,
            'comprobantesElectronicos' => $generadosElectronicos,
            'breadcrumbs' => $bread,
            'esCupon' => $esCupon,
            'post_step2' => $this->get('session')->get('post_step2'),
            'post_step1' => $this->get('session')->get('post_step1'),
            'page_title' => 'Facturaci&oacute;n autom&aacute;tica'
        );
    }

    /**
     * @Route("/facturaautomaticastep4", name="comprobanteventa_factura_automatica_step_4")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVenta:generacionFacturaAutomatica.step4.html.twig")
     */
//     * @Security("has_role('ROLE_CREAR_MODIFICAR_FACTURAS')")
    public function generarFacturaAutomaticaStep4Action(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $this->get('session')->remove('comprobantes_automaticos');
        $this->get('session')->remove('comprobantes_elegidos');
        $this->get('session')->remove('post_step1');
        $this->get('session')->remove('post_step2');

        $comprobantes_generados = $this->get('session')->get('comprobantes_generados');
        $this->get('session')->remove('comprobantes_generados');
        $comprobantes_electronicos = $this->get('session')->get('comprobantes_electronicos');
        $this->get('session')->remove('comprobantes_electronicos');

        $esCupon = $this->get('session')->get('esCupon');
        $this->get('session')->remove('esCupon');
        $talonarios = $this->get('session')->get('talonarios');
        $this->get('session')->remove('talonarios');
        $comprobantes_por_contrato = $this->get('session')->get('comprobantes_por_contrato');
        $this->get('session')->remove('comprobantes_por_contrato');

        setlocale(LC_ALL, "es_AR.UTF-8");

        $mesFacturacion = ucfirst(strftime("%B %Y", (new DateTime())->getTimestamp()));

        $offsetNumeroAsiento = 0;

        $confirmados = array();

		if ($comprobantes_generados != null) {
			
			foreach ($comprobantes_generados as $comprobante) {

				$comprobanteGenerado = $this->initComprobante($em, $comprobante);

				$confirmados[] = $comprobanteGenerado;

				$comprobanteGenerado->setCodigoBarras($comprobanteGenerado->generarCodigoBarras());

				$em->persist($comprobanteGenerado);

				// Solo genera asientos para facturas
				if (!$esCupon) {
					$this->generarAsientos($comprobanteGenerado, $offsetNumeroAsiento++);
					$this->generarRenglonesPercepcionesComprobanteVenta($em, $comprobanteGenerado);
				}
			}
		}

        if (!$esCupon) {
			if ($talonarios != null) {
				foreach ($talonarios as $talonarios_letra) {
					foreach ($talonarios_letra as $punto_venta) {
						foreach ($punto_venta['talonarios_libres'] as $talonario) {
							/* @var $talonario_original Talonario */
							$talonario_original = $em->getRepository('ADIFContableBundle:Facturacion\Talonario')->find($talonario->getId());
							$talonario_original->setNumeroSiguiente($talonario->getNumeroSiguiente());
						}
					}
				}
			}
        }

        //actualizo la cantidad de pendientes por ciclo de contrato
        $this->actualizarPendientesContratos($comprobantes_por_contrato);

        //generar lotes comprobantes a autorizar
        $lotes = $this->initComprobantesElectronicos($em, $comprobantes_electronicos);
        $em->flush();
        $em->clear();
        $comprobantes_electronicos = null;

        $electronicosConfirmados = array();
        $todosAutorizados = true;
        $errorAfip = '';

        //autorizar lotes con afip
        foreach ($lotes as $lote) {
            $comprobantesPersistidos = array();
            foreach ($lote['comprobantes'] as $comprobanteLote) {
                $comprobantePersistido = $em->getRepository('ADIFContableBundle:Facturacion\FacturaVenta')->find($comprobanteLote->getId());
//                $comprobantePersistido = $em->getRepository('ADIFContableBundle:Facturacion\FacturaVenta')
//                        ->createQueryBuilder('fv')
//                        ->select('partial fv.{id, fechaComprobante, letraComprobante, puntoVenta, caeNumero, caeVencimiento, tipoCambio, total, fechaInicioServicio, fechaFinServicio, numero}, partial rc.{id,montoNeto,alicuotaIva,montoIva}')
//                        ->innerJoin('fv.renglonesComprobante', 'rc')
//                        ->where('fv.id = :id')
//                        ->setParameter('id', $comprobanteLote->getId())
//                        ->getQuery()
//                        ->getSingleResult();
                $comprobantesPersistidos[] = $comprobantePersistido;
            }
            $lote['comprobantes'] = $comprobantesPersistidos;
            $resultadoAutorizacion = $this->autorizarLote($lote);

            $electronicosConfirmados = array_merge($electronicosConfirmados, $resultadoAutorizacion['autorizados']);
            $todosAutorizados &= $resultadoAutorizacion['todosAutorizados'];
            $errorAfip .= $resultadoAutorizacion['errorAfip'];
        }

        $idsConfirmados = array();
        foreach ($confirmados as $comprobanteConfirmado) {
            $idsConfirmados[] = $comprobanteConfirmado->getId();
        }

        $confirmados = $em->getRepository('ADIFContableBundle:Comprobante')->getComprobantesById($idsConfirmados);

        $bread = $this->base_breadcrumbs;
        $bread['Contratos'] = $this->generateUrl('contrato');
        $bread['Facturaci&oacute;n autom&aacute;tica'] = null;

        return array(
            'tipoContrato' => $this->get('session')->get('tipoContrato'),
            'moneda' => $this->get('session')->get('moneda'),
            'esMCL' => $this->get('session')->get('esMCL'),
            'conceptoPercepcionIIBB' => ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB,
            'conceptoPercepcionIVA' => ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA,
            'mesFacturacion' => $mesFacturacion,
            'comprobantes' => $confirmados,
            'comprobantesElectronicos' => $electronicosConfirmados,
            'todosAutorizados' => $todosAutorizados,
            'errorAfip' => $errorAfip,
            'breadcrumbs' => $bread,
            'esCupon' => $esCupon,
            'page_title' => 'Facturaci&oacute;n autom&aacute;tica'
        );
    }

    /**
     * Crea una factura, por medio de un contrato existente.
     * @Route("/crear/{id}", name="comprobanteventa_new")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVentaManual:new.html.twig")
     */
//     * @Security("has_role('ROLE_CREAR_MODIFICAR_FACTURAS')")
    public function newAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $contrato Contrato */
        $contrato = $em->getRepository('ADIFContableBundle:Facturacion\Contrato')
                ->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Contrato.');
        }

        /* @var $clienteProveedor ClienteProveedor */
        $clienteProveedor = $contrato->getCliente()->getClienteProveedor();

        $contratoService = $this->get('adif.contrato_service');

        $letraComprobante = $contratoService->getLetraComprobanteVenta($contrato);

        $alicuotaIVA = $clienteProveedor->getAlicuotaIva();

        $esContratoAlquiler = $contrato->getEsContratoAlquiler();

        $condicionIVA = $clienteProveedor->getCondicionIVA()->getDenominacionTipoResponsable();

        $estaInscriptoIVA = $condicionIVA == ConstanteTipoResponsable::INSCRIPTO;

        $alicuotaIIBB = 0;

        //Verificar exencion
        $this->actualizarErrorIIBB($contrato->getCliente(), false);

        $regimenPercepcionIIBB = $this->get('adif.percepciones_service')
                ->getRegimenIIBB($contrato->getCliente(), $contrato);

        if ($regimenPercepcionIIBB) {
            $porcentajeAlicuota = $this->get('adif.percepciones_service')
                    ->getPorcentajeAlicuotaIIBB($contrato->getCliente());

            $alicuotaIIBB = $porcentajeAlicuota * ($regimenPercepcionIIBB->getAlicuota() / 100);
        }

        $this->mostrarErrorIIBB();

        $alicuotaPercepcionIVA = 0;

        // if ($contrato->getCalculaIVA()) {

        $regimenPercepcionIva = $this->get('adif.percepciones_service')
                ->getRegimenIVA($contrato->getCliente(), $contrato);

        if ($regimenPercepcionIva) {
            $alicuotaPercepcionIVA = $regimenPercepcionIva->getAlicuota() / 100;
        }
        // }

        $idClaseContrato = $contrato->getClaseContrato()->getId();

        $esMCL = $contrato->getTipoMoneda()->getEsMCL();

        $comprobanteVenta = new ComprobanteVenta();

        $comprobanteVenta->setTipoCambio($contrato->getTipoMoneda()->getTipoCambio());

        $form = $this->createCreateForm($comprobanteVenta, $contrato);

        $cantidadPolizasVencidas = $this->cantidadPolizasVencidas($contrato);

        $bread = $this->base_breadcrumbs;
        $bread['Contratos'] = $this->generateUrl('contrato');
        $bread['Crear'] = null;

        return array(
            'entity' => $comprobanteVenta,
            'contrato' => $contrato,
            'id_clase_contrato' => $idClaseContrato,
            'letra_comprobante' => $letraComprobante,
            'calcula_iva' => $contrato->getCalculaIVA(),
            'alicuota_iva' => $alicuotaIVA,
            'es_contrato_alquiler' => $esContratoAlquiler,
            'esta_inscripto_iva' => $estaInscriptoIVA,
            'alicuota_iibb' => $alicuotaIIBB,
            'alicuota_percepcion_iva' => $alicuotaPercepcionIVA,
            'es_mcl' => $esMCL,
            'cantidad_polizas_vencidas' => $cantidadPolizasVencidas,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de venta'
        );
    }

    /**
     * 
     * @param ComprobanteVenta $entity
     * @param Contrato $contrato
     * @return type
     */
    private function createCreateForm(ComprobanteVenta $entity, Contrato $contrato) {
        $form = $this->createForm(new ComprobanteVentaType(), $entity, array(
            'action' => $this->generateUrl('comprobanteventa_create', array('id' => $contrato->getId())),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Creates a new ComprobanteVenta entity.
     *
     * @Route("/insertar/{id}", name="comprobanteventa_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVentaManual:new.html.twig")
     */
    public function createAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $resultado = [
            'numeroAsiento' => 0,
            'redirect' => $this->redirect($this->generateUrl('contrato'))
        ];

        $esCupon = false;

        /* @var $contrato \ADIF\ContableBundle\Entity\Facturacion\ContratoVenta */
        $contrato = $em->getRepository('ADIFContableBundle:Facturacion\ContratoVenta')
                ->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Contrato.');
        }

        $idClaseContrato = $contrato->getClaseContrato()->getId();

        $idTipoComprobante = $request->request->get('adif_contablebundle_comprobanteventa', false)['tipoComprobante'];
#        \Doctrine\Common\Util\Debug::dump($idClaseContrato);
#        \Doctrine\Common\Util\Debug::dump($idTipoComprobante);
#        die();

        $comprobanteVenta = ConstanteTipoComprobanteVenta::getSubclass($idClaseContrato, $idTipoComprobante);

        $formularioValido = true;

        $form = $this->createCreateForm($comprobanteVenta, $contrato);
        $form->handleRequest($request);

        $comprobanteVenta->setContrato($contrato);
        $comprobanteVenta->setSaldo($comprobanteVenta->getTotal());

        if ($form->isValid()) {

            $requestComprobanteVenta = $request->get('adif_contablebundle_comprobanteventa');
            $comprobanteVenta->setFechaVencimiento(DateTime::createFromFormat('d/m/Y', $requestComprobanteVenta['fechaVencimiento']));

            // Seteo el Estado
            $comprobanteVenta->setEstadoComprobante($em->getRepository('ADIFContableBundle:EstadoComprobante')
                            ->find(EstadoComprobante::__ESTADO_INGRESADO));

            // Seteo la Moneda 
            $comprobanteVenta->setTipoMoneda($contrato->getTipoMoneda());

            // Si el comprobante es un Cup&oacute;n
            if (!empty($requestComprobanteVenta['numeroCupon'])) {

                $siguienteNumeroCupon = $this->get('adif.cupon_venta_service')
                        ->getSiguienteNumeroCupon();

                $numeroCuponValido = $em->getRepository('ADIFContableBundle:Facturacion\CuponVenta')
                        ->validarNumeroCuponUnico($siguienteNumeroCupon, $comprobanteVenta->getFechaVencimiento());

                // Si NO existe ya un Cup&oacute;n con igual n&uacute;mero
                if ($numeroCuponValido) {

                    $esCupon = true;

                    $esCuponGarantia = isset($requestComprobanteVenta['esCuponGarantia']) //
                            ? $requestComprobanteVenta['esCuponGarantia'] //
                            : false;

                    $comprobanteVenta->setNumeroCupon($siguienteNumeroCupon);

                    $comprobanteVenta->setEsCuponGarantia($esCuponGarantia);

                    if ($esCuponGarantia) {

                        $resultado = $this->generarComprobanteBase($em, $comprobanteVenta, $idTipoComprobante, 'contrato');
                    }


                    $this->setComprobanteImpresion($comprobanteVenta);

                    $comprobanteVenta->setCodigoBarras($comprobanteVenta->generarCodigoBarras());

                    $em->persist($comprobanteVenta);
                } else {

                    $formularioValido = false;

                    $this->get('session')->getFlashBag()->add(
                            'error', 'El n&uacute;mero de comprobante ya se encuentra en uso.'
                    );

                    $request->attributes->set('form-error', true);
                }
            } else {
                //Verificar exencion
                $this->actualizarErrorIIBB($contrato->getCliente(), $comprobanteVenta->getTotalMCL() > 300);

                $this->mostrarErrorIIBB();

                $montoIntereses = str_replace(',', '.', $request->request->get('montoInteres'));

                $diasAtraso = $request->request->get('diasAtraso');

                // Si el comprobante es una Nota d&eacute;bito intereses
                if ($diasAtraso && $montoIntereses) {

                    /* @var $comprobanteVenta NotaDebitoInteres */
                    $comprobanteVenta->setDiasAtraso($diasAtraso);
                    $comprobanteVenta->setMontoInteres($montoIntereses);
                }

                $resultado = $this->generarComprobanteBase($em, $comprobanteVenta, $idTipoComprobante, 'contrato');

                if (($comprobanteVenta->getEsNotaCredito()) && (!empty($requestComprobanteVenta['comprobanteCancelado']))) {

                    /* @var $comprobanteCancelado ComprobanteVenta */
                    $comprobanteCancelado = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')->find($requestComprobanteVenta['comprobanteCancelado']);

                    $comprobanteCancelado->setEstadoComprobante($em->getRepository('ADIFContableBundle:EstadoComprobante')
                                    ->find(EstadoComprobante::__ESTADO_CANCELADO_NC));

                    $comprobanteVenta->setComprobanteCancelado($comprobanteCancelado);
                    $cicloFacturacion = $comprobanteCancelado->getCicloFacturacion();
                    $cicloFacturacion->setCantidadFacturasPendientes($cicloFacturacion->getCantidadFacturasPendientes() + 1);
                }

                if (!$comprobanteVenta->getEsNotaCredito()) {
                    $comprobanteVenta->setCodigoBarras($comprobanteVenta->generarCodigoBarras());
                }

                $this->setComprobanteImpresion($comprobanteVenta);

                $em->persist($comprobanteVenta);
            }

            if ($formularioValido) {

                if (!$esCupon) {
                    if ($comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES) {
                        $comprobanteVenta->setObservaciones('Intereses resarcitorios correspondients al contrato ' . $contrato->getNumeroContrato());
                    } else {
                        $comprobanteVenta->setObservaciones($contrato->getClaseContrato() . ' - ' . $comprobanteVenta->getTipoComprobante() . ' del contrato ' . $contrato->getNumeroContrato());
                    }
                }

                // Si es un Cupon 
                if ($esCupon) {

                    if ($contrato->getEsContratoVentaPlazo()) {

                        $ciclosFacturacionArray = $contrato->getCiclosFacturacionPendientes()->toArray();
                        $ciclosFacturacion = array_values($ciclosFacturacionArray);

                        if (isset($ciclosFacturacion[0])) {
                            $cicloFacturacion = $ciclosFacturacion[0];
                        } else {
                            $this->get('session')->getFlashBag()->add(
                                    'error', 'No se pueden generar comprobantes para el contrato seleccionado'
                            );

                            return $resultado['redirect'];
                        }

                        $numerosCuotasCanceladas = $this->get('adif.contrato_service')
                                ->getNumerosCuotasVentas(array($cicloFacturacion));

                        if ((isset($numerosCuotasCanceladas[$cicloFacturacion->getId()])) && (count($numerosCuotasCanceladas[$cicloFacturacion->getId()]) > 0)) {
                            $comprobanteVenta->setNumeroCuota(reset($numerosCuotasCanceladas[$cicloFacturacion->getId()]));
                        } else {
                            $comprobanteVenta->setNumeroCuota($contrato->getSiguienteNumeroComprobante());
                        }
                        $comprobanteVenta->setObservaciones($contrato->getClaseContrato() . ' - Correspondiente a la cuota ' . $comprobanteVenta->getNumeroCuota() . ' del contrato ' . $contrato->getNumeroContrato());

                        $cicloFacturacion->setCantidadFacturasPendientes($cicloFacturacion->getCantidadFacturasPendientes() - 1);

                        if ($contrato->getContratoFinalizado()) {
                            $contrato->setEstadoContrato(
                                    $em->getRepository('ADIFContableBundle:Facturacion\EstadoContrato')
                                            ->findOneByCodigo(ConstanteEstadoContrato::FINALIZADO)
                            );
                            $this->mensajeEstado(array($contrato->getId() => $contrato->getNumeroContrato()), 'Finalizado');
                        }

                        $comprobanteVenta->setCicloFacturacion($cicloFacturacion);
                    }

                    $em->flush();
                }

                // Sino, si no hubo errores en los asientos contables y presupuestarios
                if ($resultado['numeroAsiento'] > 0) {

                    $em->flush();

                    $dataArray = [
                        'data-id-comprobante' => $comprobanteVenta->getId()
                    ];

                    $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($resultado['numeroAsiento'], $dataArray);
                }

                if (!($contrato->getTipoMoneda()->getEsMCL())) {
                    $this->get('adif.tipomoneda_service')
                            ->setTipoCambio($contrato->getTipoMoneda()->getId(), $comprobanteVenta->getTipoCambio());
                }

                return $resultado['redirect'];
            }
        } else {
            $request->attributes->set('form-error', true);
        }

        /* @var $clienteProveedor ClienteProveedor */
        $clienteProveedor = $contrato->getCliente()->getClienteProveedor();

        $contratoService = $this->get('adif.contrato_service');

        $letraComprobante = $contratoService->getLetraComprobanteVenta($contrato);

        $alicuotaIVA = $clienteProveedor->getAlicuotaIva();

        $esContratoAlquiler = $contrato->getEsContratoAlquiler();

        $condicionIVA = $clienteProveedor->getCondicionIVA()->getDenominacionTipoResponsable();

        $estaInscriptoIVA = $condicionIVA == ConstanteTipoResponsable::INSCRIPTO;

        $alicuotaIIBB = 0;

        $regimenPercepcionIIBB = $this->get('adif.percepciones_service')
                ->getRegimenIIBB($comprobanteVenta->getCliente(), $contrato);

        if ($regimenPercepcionIIBB) {
            $porcentajeAlicuota = $this->get('adif.percepciones_service')
                    ->getPorcentajeAlicuotaIIBB($comprobanteVenta->getCliente());

            $alicuotaIIBB = $porcentajeAlicuota * ($regimenPercepcionIIBB->getAlicuota() / 100);
        }

        $alicuotaPercepcionIVA = 0;

        // if ($contrato->getCalculaIVA()) {

        $regimenPercepcionIva = $this->get('adif.percepciones_service')
                ->getRegimenIVA($comprobanteVenta->getCliente(), $contrato);

        if ($regimenPercepcionIva) {
            $alicuotaPercepcionIVA = $regimenPercepcionIva->getAlicuota() / 100;
        }
        // }

        $esMCL = $contrato->getTipoMoneda()->getEsMCL();

        $cantidadPolizasVencidas = $this->cantidadPolizasVencidas($contrato);

        $bread = $this->base_breadcrumbs;
        $bread['Contratos'] = $this->generateUrl('contrato');
        $bread['Crear'] = null;

        return array(
            'entity' => $comprobanteVenta,
            'contrato' => $contrato,
            'id_clase_contrato' => $idClaseContrato,
            'letra_comprobante' => $letraComprobante,
            'calcula_iva' => $contrato->getCalculaIVA(),
            'alicuota_iva' => $alicuotaIVA,
            'es_contrato_alquiler' => $esContratoAlquiler,
            'esta_inscripto_iva' => $estaInscriptoIVA,
            'alicuota_iibb' => $alicuotaIIBB,
            'alicuota_percepcion_iva' => $alicuotaPercepcionIVA,
            'es_mcl' => $esMCL,
            'cantidad_polizas_vencidas' => $cantidadPolizasVencidas,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de venta',
        );
    }

    /**
     *
     * @Route("/pliego_obra/crear", name="comprobanteventa_new_pliego_obra")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVentaManual:new.html.twig")
     */
//     * @Security("has_role('ROLE_CREAR_MODIFICAR_PLIEGOS_OBRA')")
    public function newComprobantePliegoObraAction() {

        $comprobanteVenta = new ComprobanteVenta();

        $form = $this->createCreatePliegoObraForm($comprobanteVenta);
        
        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
        $tipoContrataciones = $emCompras->getRepository('ADIFComprasBundle:TipoContratacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de venta'] = $this->generateUrl('comprobanteventa');
        $bread['Crear comprobante'] = null;

        return array(
            'entity' => $comprobanteVenta,
            'tipoContrataciones' => $tipoContrataciones,
            'id_clase_contrato' => $this->getIdClaseContratoPliego(),
            'es_pliego_obra' => true,
            'es_pliego_compra' => false,
            'es_mcl' => true,
            'cantidad_polizas_vencidas' => 0,
            'alicuota_iva' => self::ALICUOTA_IVA_PLIEGO,
            'calcula_iva' => true,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de venta'
        );
    }

    /**
     * 
     * @param ComprobanteVenta $entity
     * @return type
     */
    private function createCreatePliegoObraForm(ComprobanteVenta $entity) {
        $form = $this->createForm(new ComprobantePliegoObraType(), $entity, array(
            'action' => $this->generateUrl('comprobanteventa_create_pliego'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Creates a new ComprobanteVenta entity.
     *
     * @Route("/pliego/insertar", name="comprobanteventa_create_pliego")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVentaManual:new.html.twig")
     */
    public function createComprobantePliegoAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $resultado = [
            'numeroAsiento' => 0,
            'redirect' => $this->redirect($this->generateUrl('comprobanteventa'))
        ];

        $idTipoComprobante = $request->request->get('adif_contablebundle_comprobanteventa', false)['tipoComprobante'];

        $comprobanteVenta = ConstanteTipoComprobanteVenta::getSubclass(ConstanteClaseContrato::PLIEGO, $idTipoComprobante);

        $form = $this->createCreatePliegoObraForm($comprobanteVenta);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $requestComprobanteVenta = $request->get('adif_contablebundle_comprobanteventa');

            $comprobanteVenta->setFechaVencimiento(DateTime::createFromFormat('d/m/Y', $requestComprobanteVenta['fechaVencimiento']));
            $comprobanteVenta->setFechaComprobante(DateTime::createFromFormat('d/m/Y', $requestComprobanteVenta['fechaComprobante']));
            
            // Seteo el Estado
            $comprobanteVenta->setEstadoComprobante($em->getRepository('ADIFContableBundle:EstadoComprobante')
                            ->find(EstadoComprobante::__ESTADO_INGRESADO));

            $comprobanteVenta->setSaldo($comprobanteVenta->getTotal());

            // Seteo la Licitacion
            if ($requestComprobanteVenta['idLicitacion']) {

                $licitacion = $em->getRepository('ADIFContableBundle:Licitacion')
                        ->find($requestComprobanteVenta['idLicitacion']);

                $comprobanteVenta->setLicitacion($licitacion);
            }

            // Seteo el Cliente
            if ($requestComprobanteVenta['idCliente']) {

                $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')
                        ->find($requestComprobanteVenta['idCliente']);

                $comprobanteVenta->setCliente($cliente);

                $this->setComprobanteImpresion($comprobanteVenta);
            }

            // Si el comprobante es un Cup&oacute;n
            if ($requestComprobanteVenta['numeroCupon']) {

                $comprobanteVenta->setNumeroCupon($this->get('adif.cupon_venta_service')
                                ->getSiguienteNumeroCupon());

                $comprobanteVenta->setCodigoBarras($comprobanteVenta->generarCodigoBarras());

                $em->persist($comprobanteVenta);

                $em->flush();

                $this->get('session')->getFlashBag()->add(
                        'success', 'El alta se realiz&oacute; con &eacute;xito.'
                );

                return $resultado['redirect'];
            } else {

                $resultado = $this->generarComprobanteBase($em, $comprobanteVenta, $idTipoComprobante, 'comprobanteventa');

                if (!$comprobanteVenta->getEsNotaCredito()) {
                    $comprobanteVenta->setCodigoBarras($comprobanteVenta->generarCodigoBarras());
                }

                $em->persist($comprobanteVenta);

                if ($resultado['numeroAsiento'] > 0) {

                    $em->flush();

                    $this->get('session')->getFlashBag()->add(
                            'success', 'El alta se realiz&oacute; con &eacute;xito.'
                    );

                    $dataArray = [
                        'data-id-comprobante' => $comprobanteVenta->getId()
                    ];

                    $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($resultado['numeroAsiento'], $dataArray);
                }

                return $resultado['redirect'];
            }
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de venta'] = $this->generateUrl('comprobanteventa');
        $bread['Crear comprobante'] = null;
        
        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
        $tipoContrataciones = $emCompras->getRepository('ADIFComprasBundle:TipoContratacion')->findAll();

        return array(
            'entity' => $comprobanteVenta,
            'tipoContrataciones' => $tipoContrataciones,
            'id_clase_contrato' => $this->getIdClaseContratoPliego(),
            'es_pliego_obra' => true,
            'es_pliego_compra' => false,
            'es_mcl' => true,
            'cantidad_polizas_vencidas' => 0,
            'alicuota_iva' => self::ALICUOTA_IVA_PLIEGO,
            'calcula_iva' => true,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de venta'
        );
    }

    /**
     *
     * @Route("/pliego_compra/crear", name="comprobanteventa_new_pliego_compra")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVentaManual:new.html.twig")
     */
//     * @Security("has_role('ROLE_CREAR_MODIFICAR_PLIEGOS_COMPRA')")
    public function newComprobantePliegoCompraAction() {

        $comprobanteVenta = new ComprobanteVenta();

        $form = $this->createCreatePliegoCompraForm($comprobanteVenta);
        
        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
        $tipoContrataciones = $emCompras->getRepository('ADIFComprasBundle:TipoContratacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de venta'] = $this->generateUrl('comprobanteventa');
        $bread['Crear comprobante'] = null;

        return array(
            'entity' => $comprobanteVenta,
            'tipoContrataciones' => $tipoContrataciones,
            'id_clase_contrato' => $this->getIdClaseContratoPliego(),
            'es_pliego_obra' => false,
            'es_pliego_compra' => true,
            'es_mcl' => true,
            'cantidad_polizas_vencidas' => 0,
            'alicuota_iva' => self::ALICUOTA_IVA_PLIEGO,
            'calcula_iva' => true,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de venta'
        );
    }

    /**
     * 
     * @param ComprobanteVenta $entity
     * @return type
     */
    private function createCreatePliegoCompraForm(ComprobanteVenta $entity) {
        $form = $this->createForm(new ComprobantePliegoCompraType(), $entity, array(
            'action' => $this->generateUrl('comprobanteventa_create_pliego'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * 
     * @param Request $request
     * 
     * @Route("/puntos_venta", name="comprobanteventa_puntos_venta")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVentaManual:new.html.twig")
     */
    public function getPuntosVentaByTipoComprobanteYLetra(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idClaseContrato = $request->get('claseContrato');
        $montoNeto = $request->get('montoNeto');
        $idTipoComprobante = $request->get('tipoComprobante');
        $idLetraComprobante = $request->get('letraComprobante');

        $claseContrato = $em->getRepository('ADIFContableBundle:Facturacion\ClaseContrato')
                ->find($idClaseContrato);

        $data = array();
        $data['puntos_venta'] = array();
        $data['electronico'] = false;

        if ($claseContrato) {

            /* @var $puntoVenta \ADIF\ContableBundle\Entity\Facturacion\PuntoVenta */
            $puntoVenta = $em->getRepository('ADIFContableBundle:Facturacion\PuntoVenta')->getPuntoVentaByClaseContratoYMonto($claseContrato, $montoNeto);

            if ($puntoVenta != null) {
                if ($puntoVenta->getGeneraComprobanteElectronico()) {
                    $data['electronico'] = true;
                    $data['puntos_venta'][] = array(
                        "id" => $puntoVenta->getId(),
                        "numero" => $puntoVenta->getNumero()
                    );
                } else {

                    if ($idTipoComprobante == ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES) {
                        $tipoComprobante = $em->getRepository('ADIFContableBundle:TipoComprobante')
                                ->find(ConstanteTipoComprobanteVenta::NOTA_DEBITO);
                    } else {
                        $tipoComprobante = $em->getRepository('ADIFContableBundle:TipoComprobante')
                                ->find($idTipoComprobante);
                    }

                    if (!$tipoComprobante) {
                        throw $this->createNotFoundException('No se puede encontrar la entidad TipoComprobante.');
                    }

                    $letraComprobante = $em->getRepository('ADIFContableBundle:LetraComprobante')
                            ->find($idLetraComprobante);

                    if (!$letraComprobante) {
                        throw $this->createNotFoundException('No se puede encontrar la entidad LetraComprobante.');
                    }

                    $talonarios = $em->getRepository('ADIFContableBundle:Facturacion\Talonario')
                            ->getTalonariosByDatosComprobanteVenta($claseContrato, $montoNeto, $tipoComprobante, $letraComprobante);


                    foreach ($talonarios as $talonario) {

                        /* @var $talonario Talonario */

                        $data['puntos_venta'][] = array(
                            "id" => $talonario->getPuntoVenta()->getId(),
                            "numero" => $talonario->getPuntoVenta()->getNumero()
                        );
                    }
                }
            }
        } else {

            $puntoVenta = $em->getRepository('ADIFContableBundle:Facturacion\PuntoVenta')
                    ->findOneByNumero(self::NUMERO_PUNTO_VENTA_POR_DEFECTO);

            if ($puntoVenta) {

                $data['puntos_venta'][] = array(
                    "id" => $puntoVenta->getId(),
                    "numero" => $puntoVenta->getNumero()
                );
            }
        }

        return new JsonResponse($data);
    }

    /**
     *
     * @Route("/venta_general/crear", name="comprobanteventa_new_venta_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVentaManual:new.html.twig")
     */
//     * @Security("has_role('ROLE_CREAR_MODIFICAR_FACTURAS')")
    public function newComprobanteVentaGeneralAction() {

        $comprobanteVenta = new ComprobanteVenta();

        $form = $this->createCreateVentaGeneralForm($comprobanteVenta);

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de venta'] = $this->generateUrl('comprobanteventa');
        $bread['Crear comprobante'] = null;

        return array(
            'entity' => $comprobanteVenta,
            'id_clase_contrato' => $this->getIdClaseContratoVentaGeneral(),
            'es_pliego_obra' => false,
            'es_pliego_compra' => false,
            'es_venta_general' => true,
            'alicuota_iva' => self::ALICUOTA_IVA_GENERAL,
            'calcula_iva' => true,
            'es_mcl' => true,
            'cantidad_polizas_vencidas' => 0,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de venta'
        );
    }

    /**
     * 
     * @param ComprobanteVenta $entity
     * @return type
     */
    private function createCreateVentaGeneralForm(ComprobanteVenta $entity) {
        $form = $this->createForm(new ComprobanteVentaGeneralType(), $entity, array(
            'action' => $this->generateUrl('comprobanteventa_create_venta_general'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Creates a new ComprobanteVentaGeneral entity.
     *
     * @Route("/venta_general/insertar", name="comprobanteventa_create_venta_general")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVentaManual:new.html.twig")
     */
    public function createComprobanteVentaGeneralAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $resultado = [
            'numeroAsiento' => 0,
            'redirect' => $this->redirect($this->generateUrl('comprobanteventa'))
        ];

        $idTipoComprobante = $request->request->get('adif_contablebundle_comprobanteventa', false)['tipoComprobante'];

        $comprobanteVenta = ConstanteTipoComprobanteVenta::getSubclass(ConstanteClaseContrato::VENTA_GENERAL, $idTipoComprobante);

        $form = $this->createCreateVentaGeneralForm($comprobanteVenta);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $requestComprobanteVenta = $request->get('adif_contablebundle_comprobanteventa');

            $comprobanteVenta->setFechaVencimiento(DateTime::createFromFormat('d/m/Y', $requestComprobanteVenta['fechaVencimiento']));

            // Seteo el Estado
            $comprobanteVenta->setEstadoComprobante($em->getRepository('ADIFContableBundle:EstadoComprobante')
                            ->find(EstadoComprobante::__ESTADO_INGRESADO));

            // Seteo el Cliente
            if ($requestComprobanteVenta['idCliente']) {

                $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')
                        ->find($requestComprobanteVenta['idCliente']);

                $comprobanteVenta->setCliente($cliente);

                $this->setComprobanteImpresion($comprobanteVenta);
            }

            // Si el comprobante es un Cupon
            if ( $idTipoComprobante == ConstanteTipoComprobanteVenta::CUPON ) {

                $comprobanteVenta->setNumeroCupon($this->get('adif.cupon_venta_service')
                                ->getSiguienteNumeroCupon());
                
                if (!empty($requestComprobanteVenta['numeroContrato'])) {
                    $comprobanteVenta->setNumeroContrato($requestComprobanteVenta['numeroContrato']);
                }
                if (!empty($requestComprobanteVenta['numeroOnabe'])) {
                    $comprobanteVenta->setNumeroOnabe($requestComprobanteVenta['numeroOnabe']);
                }
                
                if (isset($requestComprobanteVenta['contratoCli']) && !empty($requestComprobanteVenta['contratoCli'])) {
                    // Esto es para los casos de la migraciones para la AABE, que asocio un cupon a un contrato
                    $contrato = $em->getRepository('ADIFContableBundle:Facturacion\ContratoVenta')
                            ->find($requestComprobanteVenta['contratoCli']);
                    $comprobanteVenta->setContrato($contrato);
                    // Le seteo que sea cupon garantia para que me aparezca
                    // en la seccion de cupones de la C/C del cliente
                    $comprobanteVenta->setEsMigracionAabe(true);
                }

                $comprobanteVenta->setCodigoBarras($comprobanteVenta->generarCodigoBarras());
                
                $comprobanteVenta->setSaldo($comprobanteVenta->getTotal());
                
                $em->persist($comprobanteVenta);

                $em->flush();

                $this->get('session')->getFlashBag()->add(
                        'success', 'El alta se realiz&oacute; con &eacute;xito.'
                );

                return $resultado['redirect'];
            } else {

                $resultado = $this->generarComprobanteBase($em, $comprobanteVenta, $idTipoComprobante, 'comprobanteventa');

                if (!$comprobanteVenta->getEsNotaCredito()) {
                    $comprobanteVenta->setCodigoBarras($comprobanteVenta->generarCodigoBarras());
                }
                
                $comprobanteVenta->setSaldo($comprobanteVenta->getTotal());

                $em->persist($comprobanteVenta);

                if ($resultado['numeroAsiento'] > 0) {

                    $em->flush();

                    $this->get('session')->getFlashBag()->add(
                            'success', 'El alta se realiz&oacute; con &eacute;xito.'
                    );

                    $dataArray = [
                        'data-id-comprobante' => $comprobanteVenta->getId()
                    ];

                    $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($resultado['numeroAsiento'], $dataArray);
                }

                return $resultado['redirect'];
            }
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de venta'] = $this->generateUrl('comprobanteventa');
        $bread['Crear comprobante'] = null;

        return array(
            'entity' => $comprobanteVenta,
            'id_clase_contrato' => $this->getIdClaseContratoVentaGeneral(),
            'es_pliego_obra' => false,
            'es_pliego_compra' => false,
            'es_venta_general' => true,
            'calcula_iva' => true,
            'es_mcl' => true,
            'cantidad_polizas_vencidas' => 0,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de venta'
        );
    }

    /**
     * 
     * @param Request $request
     * 
     * @Route("/siguiente_numero_comprobante", name="comprobanteventa_siguiente_numero_comprobante")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVentaManual:new.html.twig")
     */
    public function getNumeroComprobanteByTipoComprobanteYLetraYPuntoVenta(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idTipoComprobante = $request->get('tipoComprobante');
        $idLetraComprobante = $request->get('letraComprobante');
        $puntoVenta = $request->get('puntoVenta');

        if ($idTipoComprobante == ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES) {
            $tipoComprobante = $em->getRepository('ADIFContableBundle:TipoComprobante')
                    ->find(ConstanteTipoComprobanteVenta::NOTA_DEBITO);
        } else {
            $tipoComprobante = $em->getRepository('ADIFContableBundle:TipoComprobante')
                    ->find($idTipoComprobante);
        }

        if (!$tipoComprobante) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoComprobante.');
        }

        $letraComprobante = $em->getRepository('ADIFContableBundle:LetraComprobante')
                ->find($idLetraComprobante);

        if (!$letraComprobante) {
            throw $this->createNotFoundException('No se puede encontrar la entidad LetraComprobante.');
        }

        $talonario = $em->getRepository('ADIFContableBundle:Facturacion\Talonario')
                ->getTalonarioByTipoComprobanteYLetraYPuntoVenta($tipoComprobante, $letraComprobante, $puntoVenta);

        if (!$talonario) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Talonario.');
        }

        $siguienteNumeroComprobante = $this->get('adif.talonario_service')->getSiguienteNumeroComprobante($talonario);

        return new JsonResponse($siguienteNumeroComprobante);
    }

    /**
     * 
     * @return JsonResponse
     * 
     * @Route("/siguiente_numero_cupon", name="comprobanteventa_siguiente_numero_cupon")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVentaManual:new.html.twig")
     */
    public function getSiguienteNumeroCupon() {
        $cuponVentaService = $this->get('adif.cupon_venta_service');

        $siguienteNumero = $cuponVentaService->getSiguienteNumeroCupon();

        return new JsonResponse($siguienteNumero);
    }

    /**
     * Genera la exportacion de comprobantes de venta
     *
     * @Route("/imprimir/{id}", name="comprobanteventa_imprimir")
     * @Method("GET")
     */
    public function imprimirComprobanteVentaAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        /* @var $entity ComprobanteVenta */
        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteVenta.');
        }

        $esFacturaElectronica = $entity->getPuntoVenta() != null ? $entity->getPuntoVenta()->getGeneraComprobanteElectronico() : false;

        $denominacionCondicionIVA = null;

        if (!$entity->getEsCupon()) {

            $clienteDatosImpositivos = $emCompras->getRepository('ADIFComprasBundle:ClienteProveedor')
                    ->getClienteDatosImpositivosByIdCliente($entity->getCliente()->getId());
            
            if ($clienteDatosImpositivos != null && !empty($clienteDatosImpositivos)) {
                $denominacionCondicionIVA = $clienteDatosImpositivos['condicion_iva'];
            } else {
                $denominacionCondicionIVA = ' - ';
            }
        }

        $barCodeNumber = $entity->getCodigoBarrasNacion();
        
        $html = '<html><head><meta charset="utf-8"/><style type="text/css">'
                . $this->renderView('ADIFContableBundle:Facturacion\ComprobanteVenta:imprimir' . ($entity->getEsCupon() ? '_cupon' : '' ) . '.css.twig')
                . '</style></head><body>';

        $html .='<div class="' . ( ($esFacturaElectronica || $entity->esComprobanteRendicionLiquidoProducto()) ? 'comprobante-electronico-content' : '') . '">';

        
        $codigoComprobante = $entity->getLetraComprobante() != null //
                    ? str_pad(ConstanteAfip::getTipoComprobante($entity->getLetraComprobante()->getLetra(), $entity->getTipoComprobante()->getId()), 2, '0', STR_PAD_LEFT) //
                    : '-';
        
        $esLetraB = $entity->getLetraComprobante() != null //
                    ? $entity->getLetraComprobante()->getLetra() == ConstanteLetraComprobante::B //
                    : false;
        
        $html .= $this->renderView('ADIFContableBundle:Facturacion\ComprobanteVenta:imprimir' . ($entity->getEsCupon() ? '_cupon' : '' ) . '.html.twig', array(
            'entity' => $entity,
            'barCode' => '/barcode.php?text=' . $barCodeNumber, //$bcPathAbs,
            'esFacturaElectronica' => $esFacturaElectronica,
            'codigoComprobante' => $codigoComprobante,
            'esLetraB' => $esLetraB,
            'barCodeNumber' => $barCodeNumber,
            'condicionIVA' => $denominacionCondicionIVA,

            'esComprobanteRendicionLiquidoProducto' => $entity->esComprobanteRendicionLiquidoProducto(),
            'observacionesComprobante' => $entity->getObservaciones()
                )
        );

        $html .='</div>';

        $html .= '</body></html>';
		
		//die($html);

        $filename = AdifApi::stringCleaner($entity->getTipoComprobante() . ($entity->getNumero() != null ? $entity->getNumero() : $entity->getNumeroCupon())) . '.pdf';

        $mpdfService = new mPDF('', 'A4');

        $mpdfService->WriteHTML($html);
        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    /**
     *
     * @Route("/imprimir-comprobantes/", name="comprobanteventa_imprimir-comprobantes")
     * @Method("POST")
     */
    public function imprimirComprobantesVentaAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $ids = json_decode($request->request->get('ids'));

        $html = '<html><head><meta charset="utf-8"/>'
                . '<style type="text/css">'
                . $this->renderView('ADIFContableBundle:Facturacion\ComprobanteVenta:imprimir.css.twig')
                . '</style>'
                . '<style type="text/css">'
                . $this->renderView('ADIFContableBundle:Facturacion\ComprobanteVenta:imprimir_cupon.css.twig')
                . '</style>'
                . '</head><body>';

        $index = 0;

        foreach ($ids as $id) {

            /* @var $entity ComprobanteVenta */
            $entity = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteVenta.');
            }

            $barCodeNumber = $entity->getCodigoBarrasNacion();

//            $barCode = new barCode();
//            $barCode->savePath = $this->get('kernel')->getRootDir() . '/../web/uploads/barcodes';
//            $bcPathAbs = $barCode->getBarcodePNGPath($barCodeNumber, 'I25', 1.5, 45);

            $esFacturaElectronica = $entity->getPuntoVenta() != null ? $entity->getPuntoVenta()->getGeneraComprobanteElectronico() : false;

            $denominacionCondicionIVA = null;

            if (!$entity->getEsCupon()) {

                $clienteDatosImpositivos = $emCompras->getRepository('ADIFComprasBundle:ClienteProveedor')
                    ->getClienteDatosImpositivosByIdCliente($entity->getCliente()->getId());

                if ($clienteDatosImpositivos != null && !empty($clienteDatosImpositivos)) {
                    $denominacionCondicionIVA = $clienteDatosImpositivos['condicion_iva'];
                } else {
                    $denominacionCondicionIVA = ' - ';
                }
            }

            $html .='<div class="'
                    . ( ($esFacturaElectronica || $entity->esComprobanteRendicionLiquidoProducto()) ? 'comprobante-electronico-content' : '')
                    . ( ++$index != count($ids) ? ' page-break-after' : '')
                    . '">';

            $codigoComprobante = $entity->getLetraComprobante() != null //
                    ? str_pad(ConstanteAfip::getTipoComprobante($entity->getLetraComprobante()->getLetra(), $entity->getTipoComprobante()->getId()), 2, '0', STR_PAD_LEFT) //
                    : '-';

            $esLetraB = $entity->getLetraComprobante() != null //
                    ? $entity->getLetraComprobante()->getLetra() == ConstanteLetraComprobante::B //
                    : false;

            $html .= $this->renderView('ADIFContableBundle:Facturacion\ComprobanteVenta:imprimir' . ($entity->getEsCupon() ? '_cupon' : '' ) . '.html.twig', array(
                'entity' => $entity,
                'barCode' => '/barcode.php?text=' . $barCodeNumber, //$bcPathAbs,
                'esFacturaElectronica' => $esFacturaElectronica,
                'codigoComprobante' => $codigoComprobante,
                'esLetraB' => $esLetraB,
                'barCodeNumber' => $barCodeNumber,
                'condicionIVA' => $denominacionCondicionIVA,
                'esComprobanteRendicionLiquidoProducto' => $entity->esComprobanteRendicionLiquidoProducto()
                    )
            );

            $html .='</div>';
        }

        $html .= '</body></html>';

        $filename = 'comprobantes.pdf';

        $mpdfService = new mPDF('', 'A4');

        $mpdfService->WriteHTML($html);

        return new Response($mpdfService->Output($filename, 'D'));
    }

    /**
     * Muestra la pantalla de libro IVA ventas.
     *
     * @Route("/libroiva_ventas/", name="comprobanteventa_libroiva_venta")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVenta:reporte.libro_iva_ventas.html.twig")
     */
//     * @Security("has_role('ROLE_EMITIR_LIBRO_IVA_VENTAS')")
    public function libroIVAVentasAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Libro IVA ventas'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Libro IVA ventas'
        );
    }

    /**
     * @Route("/filtrar_libroiva_ventas/", name="comprobanteventa_filtrar_libroiva_venta")
     */
    public function filtrarLibroIVAVentasAction(Request $request) {

        $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaInicio') . ' 00:00:00');
        $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaFin') . ' 23:59:59');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('fecha', 'fecha');
        $rsm->addScalarResult('comprobante', 'comprobante');
        $rsm->addScalarResult('numero', 'numero');
        $rsm->addScalarResult('letra', 'letra');
        $rsm->addScalarResult('tipoComprobante', 'tipoComprobante');
        $rsm->addScalarResult('proveedor', 'proveedor');
        $rsm->addScalarResult('cuit', 'cuit');
        $rsm->addScalarResult('Neto_10_5', 'Neto_10_5');
        $rsm->addScalarResult('Neto_21', 'Neto_21');
        $rsm->addScalarResult('Neto_27', 'Neto_27');
        $rsm->addScalarResult('importeTotalNeto', 'importeTotalNeto');
        $rsm->addScalarResult('totalExento', 'totalExento');
        $rsm->addScalarResult('Iva_10_5', 'Iva_10_5');
        $rsm->addScalarResult('Iva_21', 'Iva_21');
        $rsm->addScalarResult('Iva_27', 'Iva_27');
        $rsm->addScalarResult('totalIVA', 'totalIVA');
        $rsm->addScalarResult('perIIBB', 'perIIBB');
        $rsm->addScalarResult('perIVA', 'perIVA');
        $rsm->addScalarResult('totalComprobante', 'totalComprobante');
        $rsm->addScalarResult('idEstadoComprobante', 'idEstadoComprobante');
        $rsm->addScalarResult('estadoComprobante', 'estadoComprobante');
        $rsm->addScalarResult('idTipoComprobante', 'idTipoComprobante');

        $native_query = $em->createNativeQuery('
            CALL sp_vista_reporteIvaVenta(?,?)         
      
        ', $rsm);

        $native_query->setParameter(1, $fechaInicio, Type::DATETIME);
        $native_query->setParameter(2, $fechaFin, Type::DATETIME);

        $comprobantes = $native_query->getResult();

        $jsonResult = [];

        foreach ($comprobantes as $comprobante) {

            $multiplicador = $comprobante['idTipoComprobante'] == 3 ? -1 : 1;

            $arr_comp = array(
                'id' => $comprobante['id'],
                'fechaComprobante' => $comprobante['fecha'],
                'numeroComprobante' => $comprobante['numero'],
                'tipoComprobante' => $comprobante['tipoComprobante'],
                'letraComprobante' => $comprobante['letra'],
                'razonSocial' => $comprobante['proveedor'],
                'cuit' => $comprobante['cuit'],
                'importeNeto105' => number_format($comprobante['Neto_10_5'] * $multiplicador, 2, ',', '.'),
                'importeNeto21' => number_format($comprobante['Neto_21'] * $multiplicador, 2, ',', '.'),
                'importeNeto27' => number_format($comprobante['Neto_27'] * $multiplicador, 2, ',', '.'),
                'importeTotalNeto' => number_format($comprobante['importeTotalNeto'] * $multiplicador, 2, ',', '.'),
                'importeTotalExento' => number_format($comprobante['totalExento'] * $multiplicador, 2, ',', '.'),
                'iva105' => number_format($comprobante['Iva_10_5'] * $multiplicador, 2, ',', '.'),
                'iva21' => number_format($comprobante['Iva_21'] * $multiplicador, 2, ',', '.'),
                'iva27' => number_format($comprobante['Iva_27'] * $multiplicador, 2, ',', '.'),
                'totalIVA' => number_format($comprobante['totalIVA'] * $multiplicador, 2, ',', '.'),
                'percepcionIIBB' => number_format($comprobante['perIIBB'] * $multiplicador, 2, ',', '.'),
                'percepcionIVA' => number_format($comprobante['perIVA'] * $multiplicador, 2, ',', '.'),
                'totalFactura' => number_format($comprobante['totalComprobante'] * $multiplicador, 2, ',', '.'),
            );

            $jsonResult[] = $arr_comp;

            if ($comprobante['idEstadoComprobante'] == EstadoComprobante::__ESTADO_ANULADO) {

                $multiplicador = $comprobante['idTipoComprobante'] == 3 ? 1 : -1;

                $arr_comp = array(
                    'id' => $comprobante['id'],
                    'fechaComprobante' => $comprobante['fecha'],
                    'numeroComprobante' => $comprobante['numero'],
                    'tipoComprobante' => $comprobante['tipoComprobante'],
                    'letraComprobante' => $comprobante['letra'],
                    'razonSocial' => $comprobante['proveedor'],
                    'cuit' => $comprobante['cuit'],
                    'importeNeto105' => number_format($comprobante['Neto_10_5'] * $multiplicador, 2, ',', '.'),
                    'importeNeto21' => number_format($comprobante['Neto_21'] * $multiplicador, 2, ',', '.'),
                    'importeNeto27' => number_format($comprobante['Neto_27'] * $multiplicador, 2, ',', '.'),
                    'importeTotalNeto' => number_format($comprobante['importeTotalNeto'] * $multiplicador, 2, ',', '.'),
                    'importeTotalExento' => number_format($comprobante['totalExento'] * $multiplicador, 2, ',', '.'),
                    'iva105' => number_format($comprobante['Iva_10_5'] * $multiplicador, 2, ',', '.'),
                    'iva21' => number_format($comprobante['Iva_21'] * $multiplicador, 2, ',', '.'),
                    'iva27' => number_format($comprobante['Iva_27'] * $multiplicador, 2, ',', '.'),
                    'totalIVA' => number_format($comprobante['totalIVA'] * $multiplicador, 2, ',', '.'),
                    'percepcionIIBB' => number_format($comprobante['perIIBB'] * $multiplicador, 2, ',', '.'),
                    'percepcionIVA' => number_format($comprobante['perIVA'] * $multiplicador, 2, ',', '.'),
                    'totalFactura' => number_format($comprobante['totalComprobante'] * $multiplicador, 2, ',', '.'),
                );


                $jsonResult[] = $arr_comp;
            }
        }

        return new JsonResponse($jsonResult);
    }

    /**
     * 
     * @param type $em
     * @param type $comprobantes_electronicos
     * @return type
     */
    private function initComprobantesElectronicos($em, $comprobantes_electronicos) {

        $WSFE = $this->get('adif.wsfe_service');
        $lotesAutorizacion = array();

        $indexLote = 0;
//asignacion de numero
		if ($comprobantes_electronicos != null) {
			
			foreach ($comprobantes_electronicos as $letra => $puntosVenta) {
				foreach ($puntosVenta as $puntoVenta => $comprobantes) {
					$tipo_comprobante = ConstanteAfip::getTipoComprobante($letra, $comprobantes[0]->getTipoComprobante()->getId());
					if ($tipo_comprobante != -1) {
						if ($WSFE->getEstadoServidoresOk()) {
	//N&uacute;mero del &uacute;ltimo comprobante autorizado para el punto de venta y tipo de comprobante dado
							$ult_num_cbte = $WSFE->getUltCbte($puntoVenta, $tipo_comprobante);
							$comprobantesGenerados = array();
							foreach ($comprobantes as $comprobante) {
								$ult_num_cbte++;
								$comprobante->setNumero(str_pad($ult_num_cbte, 8, '0', STR_PAD_LEFT));
								$comprobanteGenerado = $this->initComprobante($em, $comprobante);
								$em->persist($comprobanteGenerado);
								$comprobantesGenerados[] = $comprobanteGenerado;
							}
							$lotesAutorizacion[$indexLote]['comprobantes'] = $comprobantesGenerados;
							$lotesAutorizacion[$indexLote]['puntoVenta'] = $puntoVenta;
							$lotesAutorizacion[$indexLote]['tipoComprobante'] = $tipo_comprobante;
							$indexLote++;
						} else {
							// No se puede establecer conexi&ioacute;n con AFIP. Intente m&aacute;s tarde
						}
					}
				}
			}
		}
		
        return $lotesAutorizacion;
    }

    /**
     * 
     * @param type $loteAutorizacion
     * @return type
     */
    private function autorizarLote($loteAutorizacion) {

        $resultado = [];
        $resultado['todosAutorizados'] = false;
        $resultado['autorizados'] = [];
        $resultado['errorAfip'] = '';

        $WSFE = $this->get('adif.wsfe_service');
        if ($WSFE->getEstadoServidoresOk()) {
            $response = $WSFE->autorizarComprobante($loteAutorizacion['comprobantes'], $loteAutorizacion['puntoVenta'], $loteAutorizacion['tipoComprobante']);
            $respuestaAFIP = json_decode(json_encode($response), true);
            $resultado = $this->resultadoAutorizacion($loteAutorizacion['comprobantes'], $respuestaAFIP);
        } else {
            $resultado['errorAfip'] = 'No se puede establecer conexi&ioacute;n con AFIP. Intente m&aacute;s tarde';
        }

        return $resultado;
    }

    /**
     * 
     * @param type $em
     * @param type $comprobante
     * @return type
     */
    private function initComprobante($em, ComprobanteVenta $comprobanteGenerado) {
        $idTipoComprobante = $comprobanteGenerado->getTipoComprobante()->getId();

        $comprobanteGenerado->setFechaCreacion(new DateTime());
        $comprobanteGenerado->setFechaUltimaActualizacion(new DateTime());

        //fix
        $comprobanteGenerado->setTipoComprobante($em->getRepository('ADIFContableBundle:TipoComprobante')->find($idTipoComprobante));
        $comprobanteGenerado->setEstadoComprobante($em->getRepository('ADIFContableBundle:EstadoComprobante')->find(EstadoComprobante::__ESTADO_INGRESADO));

        /* @var $renglon RenglonComprobante */
        foreach ($comprobanteGenerado->getRenglonesComprobante() as $renglon) {
            $renglon->setFechaCreacion(new DateTime());
            $renglon->setFechaUltimaActualizacion(new DateTime());
        }

        /* @var $renglon RenglonPercepcion */
        foreach ($comprobanteGenerado->getRenglonesPercepcion() as $renglon) {
            $renglon->setFechaCreacion(new DateTime());
            $renglon->setFechaUltimaActualizacion(new DateTime());
        }

        //NO hacer ningun cambio antes de hacer persist
        $comprobanteGenerado = $em->merge($comprobanteGenerado);
        return $comprobanteGenerado;
    }

    /**
     * 
     * @param type $comprobantes
     * @param type $respuestaAFIP
     * @return type
     */
    private function resultadoAutorizacion($comprobantes, $respuestaAFIP) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $numeroAsiento = 0;

        $comprobantesAutorizados = array();
        $todosAutorizados = true;
        $resultado = array();

        $offsetNumeroAsiento = 0;

        $comprobantesPorContrato = array();

        $errorAfip = '';

        if (sizeof($comprobantes) > 1) {

            foreach ($respuestaAFIP["FeDetResp"]["FECAEDetResponse"] as $indice => $comprobanteResponseAfip) {

                /* @var $comprobante FacturaVenta */
                $comprobante = $comprobantes[$indice];

                if ($comprobanteResponseAfip["Resultado"] == "A") {
                    $comprobante->setCaeNumero($comprobanteResponseAfip["CAE"]);
                    $comprobante->setCaeVencimiento(DateTime::createFromFormat('Ymd', $comprobanteResponseAfip["CAEFchVto"]));
                    $comprobante->setCodigoBarras($comprobante->generarCodigoBarras());
                    $comprobantesAutorizados[] = $comprobante;
                    // Genero asientos presupuestarios y contables
                    $numeroAsiento = $this->generarAsientos($comprobante, $offsetNumeroAsiento++);
                    $this->generarRenglonesPercepcionesComprobanteVenta($em, $comprobante);
                    if (!isset($comprobantesPorContrato[$comprobante->getContrato()->getId()])) {
                        $comprobantesPorContrato[$comprobante->getContrato()->getId()] = 0;
                    }
                    $comprobantesPorContrato[$comprobante->getContrato()->getId()] ++;
                    $this->setComprobanteImpresion($comprobante);
                } else {
                    //ver error de $respuestaAFIP
                    if (isset($comprobanteResponseAfip['Observaciones'])) {
                        foreach ($comprobanteResponseAfip['Observaciones']['Obs'] as $indiceObs => $obs) {
                            if ($indiceObs == 'Msg') {
								if (!is_array($obs)) {
									$errorAfip .= $obs . ' ';
								} else {
									$errorAfip .= implode(', ', $obs) . ' ';
								}
                            }
                        }
                    }
                    $todosAutorizados = false;
                    $em->remove($comprobante);
                }
            }
        } else {
            $resultadoElectronico = $this->handlerUnicoResultadoAutorizacionElectronicaAFIP($em, $respuestaAFIP, $comprobantes[0]);
            if ($resultadoElectronico['aprobado']) {
                $comprobantesAutorizados[] = $comprobantes[0];
                $numeroAsiento = $resultadoElectronico['numeroAsiento'];
                if (!isset($comprobantesPorContrato[$comprobantes[0]->getContrato()->getId()])) {
                    $comprobantesPorContrato[$comprobantes[0]->getContrato()->getId()] = 0;
                }
                $comprobantesPorContrato[$comprobantes[0]->getContrato()->getId()] ++;
                $this->setComprobanteImpresion($comprobantes[0]);
            } else {
                $em->remove($comprobantes[0]);
                $todosAutorizados = false;
                $errorAfip = $resultadoElectronico['errorAfip'];
            }
        }

        $resultado['todosAutorizados'] = $todosAutorizados;
        $resultado['autorizados'] = $comprobantesAutorizados;
        $resultado['errorAfip'] = $errorAfip;

        $this->actualizarPendientesContratos($comprobantesPorContrato);

        $em->flush();
        $em->clear();
        if ($numeroAsiento > 0) {

            $this->get('adif.asiento_service')
                    ->showMensajeFlashAsientoContable($numeroAsiento, array());
        }

        return $resultado;
    }

    /**
     * 
     * @param type $comprobanteGenerado
     * @param type $offsetNumeroAsiento
     * @param type $esContraAsiento
     * @return type
     */
    private function generarAsientos($comprobanteGenerado, $offsetNumeroAsiento = 0, $esContraAsiento = false) {

        // Si NO es un comprobante de venta general
        if (!$comprobanteGenerado->esComprobanteVentaGeneral() && $comprobanteGenerado->getTipoComprobante()->getId() != ConstanteTipoComprobanteVenta::RENDICION_LIQUIDO_PRODUCTO) {

            // Genero el asiento contable y presupuestario
            $numeroAsiento = $this->get('adif.asiento_service')
                    ->generarAsientosComprobantesVenta($comprobanteGenerado, $this->getUser(), $offsetNumeroAsiento, $esContraAsiento);
        } else {

            // Genero el asiento contable y presupuestario
            $numeroAsiento = $this->get('adif.asiento_service')
                    ->generarAsientosComprobanteVentaGeneral($comprobanteGenerado, $this->getUser(), $offsetNumeroAsiento, $esContraAsiento);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param type $comprobantes_por_contrato
     */
    private function actualizarPendientesContratos($comprobantes_por_contrato) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

		if ($comprobantes_por_contrato != null) {
			
			foreach ($comprobantes_por_contrato as $id => $cantidad) {
				/* @var $contrato Contrato */

				$contrato = $em->getRepository('ADIFContableBundle:Facturacion\Contrato')->find($id);

				foreach ($contrato->getCiclosFacturacionPendientes() as $ciclo) {
					/* @var $ciclo CicloFacturacion */
					if ($cantidad > 0) {
						$generadas = ($cantidad >= $ciclo->getCantidadFacturasPendientes()) ? $ciclo->getCantidadFacturasPendientes() : $cantidad;
						$ciclo->setCantidadFacturasPendientes($ciclo->getCantidadFacturasPendientes() - $generadas);
						$cantidad -= $generadas;
					}
				}

				if ($contrato->getContratoFinalizado()) {
					$contrato->setEstadoContrato(
							$em->getRepository('ADIFContableBundle:Facturacion\EstadoContrato')
									->findOneByCodigo(ConstanteEstadoContrato::FINALIZADO)
					);
				}
			}
		}
    }

    /**
     * Tabla para cobranzas.
     *
     * @Route("/index_table_comprobantes/", name="cobranzas_index_table_index_table_comprobantes")
     * @Method("GET|POST")
     * 
     */
    public function indexTableComprobantesAction(Request $request) 
	{
		if ($request->query->get('fake') == 'true') {
			$result = array();
			return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_comprobantes.html.twig', array('entities' => $result));
		}

        $fecha_inicio = DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_desde') . ' 00:00:00');
        $fecha_fin = DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_hasta') . ' 23:59:59');
        
        $result = $this->obtenerComprobantes(
			$NC = false, 
			$filtrarXsaldo = true, 
			$fecha_inicio, 
			$fecha_fin, 
			$filtrarXimputacion = false, 
            $sinNC = true,
			$referencia = null
		);
		
        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_comprobantes.html.twig', array('entities' => $result));
    }
	
    /**
     * Tabla para cobranzas.
     *
     * @Route("/index_table_notas_credito/", name="cobranzas_index_table_notas_credito")
     * @Method("GET|POST")
     * 
     */
    public function indexTableNotasCreditoAction(Request $request) 
	{
		if ($request->query->get('fake') == 'true') {
			$result = array();
			return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_notas_credito.html.twig', array('entities' => $result));
		}
		
		$fecha_inicio = DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_desde') . ' 00:00:00');
        $fecha_fin = DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_hasta') . ' 23:59:59');

		$result = $this->obtenerComprobantes(
			$soloNotaCredito = true, 
			$filtrarXsaldo = false, 
			$fecha_inicio, 
			$fecha_fin, 
			$filtrarXimputacion = false, 
            $sinNC = false,
			$referencia = null
		);
		
        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_notas_credito.html.twig', array('entities' => $result));
    }

    /**
     * Tabla para cobranzas imputadas (tercer pesta&nacute;a).
     *
     * @Route("/index_table_comprobantes_con_imputaciones/", name="index_table_comprobantes_con_imputaciones")
     * @Method("GET|POST")
     * 
     */
    public function indexTableComprobantesConImputacionesAction(Request $request) 
	{
		if ($request->query->get('fake') == 'true') {
			$result = array();
			return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_comprobantes_con_imputaciones.html.twig', array('entities' => $result));
		}
		
        $fecha_inicio = DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_desde') . ' 00:00:00');
        $fecha_fin = DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_hasta') . ' 23:59:59');
        $referencia = $request->query->get('referencia');
        $result = $this->obtenerComprobantes(
                $soloNotaCredito = false, 
                $conSaldo = false, 
                $fecha_inicio, 
                $fecha_fin, 
                $filtrarCobro = true, 
                $sinNC = false,
                trim($referencia)
        );
        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_comprobantes_con_imputaciones.html.twig', array('entities' => $result));
    }

    private function obtenerComprobantes($soloNotaCredito, $conSaldo, $fechaInicio, $fechaFin, $filtrarCobro, $sinNC, $referencia, $offset = 0, $limit = 1000) 
	{
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
		
		$strFechaInicio = $fechaInicio->format('Y-m-d') . ' 00:00:00';
		$strFechaFin = $fechaFin->format('Y-m-d') . ' 23:59:59';
		$filtrarCobro = (int) $filtrarCobro;
		$conSaldo = (int) $conSaldo;
		$connection = $em->getConnection();

        if($referencia != null && !empty($referencia)) {
            $sql = "CALL sp_get_comprobantes_venta('$strFechaInicio', '$strFechaFin', $offset, $limit, $filtrarCobro, $conSaldo, '$referencia')";
        } else {
            $sql = "CALL sp_get_comprobantes_venta('$strFechaInicio', '$strFechaFin', $offset, $limit, $filtrarCobro, $conSaldo, NULL)";
        }
        
//        \Doctrine\Common\Util\Debug::dump( $sql ); exit;

        
		$statement = $connection->prepare($sql);
		$statement->execute();
		$comprobantes = $statement->fetchAll();
		
		return $this->getDataComprobantes($comprobantes, $soloNotaCredito, $sinNC);
    }
	
	private function getDataComprobantes($comprobantes, $soloNotaCredito, $sinNC)
	{
		$resultado = array();
				
		if (!empty($comprobantes)) {
			
			for($i = 0; $i < count($comprobantes); $i++) {
				
				$comprobante = $comprobantes[$i];
				
				if ($comprobante['codigo_barras'] == null) {
					
					$comprobante['codigo_barras'] = $this->get('adif.cobranza_service')
						->generarCodigoBarras(
							$comprobante['discriminador'], 
							$comprobante['codigo_barras'],
							$comprobante['punto_venta'], 
							$comprobante['numero'],
							$comprobante['id_tipo_comprobante'],
							$comprobante['letra'],
							$comprobante['numero_contrato'],
							$comprobante['fecha_vencimiento'],
							$comprobante['contrato_fecha_fin'],
							$comprobante['numero_cupon'],
							$comprobante['fecha_comprobante'],
							$comprobante['licitacion_fecha_apertura']
						);
				}
				
                if ($sinNC) {
                    // Todo pero sin NC
					if ($comprobante['id_tipo_comprobante'] != 3 ) {
						$resultado[$i] = $comprobante;
					}
				} else if ($soloNotaCredito) {
                    // Solo notas de creditos o cupones con saldo negativo
					if ( ($comprobante['id_tipo_comprobante'] == 3 && $comprobante['saldo'] != 0 ) || ($comprobante['id_tipo_comprobante'] == 7 && $comprobante['saldo'] < 0)) {
						$resultado[$i] = $comprobante;
					}
				} else {
					$resultado[$i] = $comprobante;
				}
			}
		}
		
       
		return $resultado;
	}

    /**
     * 
     * @param Contrato $contrato
     * @return int
     */
    private function cantidadPolizasVencidas(Contrato $contrato) {

        $cantidadPolizasVencidas = 0;

        $today = new \DateTime();

        foreach ($contrato->getPolizasSeguro() as $polizaSeguro) {

            /* @var $polizaSeguro \ADIF\ContableBundle\Entity\Facturacion\PolizaSeguroContrato */

            $polizaSeguro->getFechaVencimiento();

            if ($polizaSeguro->getFechaVencimiento()->format("Y-m-d") < $today->format("Y-m-d")) {

                $cantidadPolizasVencidas++;
            }
        }

        return $cantidadPolizasVencidas;
    }

    /**
     * Anula el comprobante de venta si no posee cobros
     *
     * @Route("/anular/{id}", name="comprobanteventa_anular")
     * @Method("GET")
     */
//     * @Security("has_role('ROLE_ANULAR_FACTURAS')")
    public function anularComprobanteVenta(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $entity ComprobanteVenta */
        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteVenta.');
        }

        $estadoAnulado = $em->getRepository('ADIFContableBundle:EstadoComprobante')->find(EstadoComprobante::__ESTADO_ANULADO);

        $rsp = $entity->getEstadoComprobante() == $estadoAnulado 
			? 'El comprobante fue anulado el ' . $entity->getFechaAnulacion()->format('d/m/Y') 
			: '';

        $fecha_hoy = new \DateTime();

        $rsp = ($rsp == '') ? $entity->anular() : $rsp;

        if ($rsp == '') {

            $entity->setEstadoComprobante($estadoAnulado);
            $entity->setFechaAnulacion($fecha_hoy);

            $ciclo_facturacion = $entity->getCicloFacturacion();

            if ($ciclo_facturacion != null) {
                /* @var $ciclo_facturacion CicloFacturacion */
                $ciclo_facturacion->setCantidadFacturasPendientes($ciclo_facturacion->getCantidadFacturasPendientes() + 1);
                if (!$ciclo_facturacion->getContrato()->getContratoFinalizado()) {
                    $ciclo_facturacion->getContrato()->setEstadoContrato(
                            $em->getRepository('ADIFContableBundle:Facturacion\EstadoContrato')
                                    ->findOneByCodigo(ConstanteEstadoContrato::ACTIVO_OK)
                    );
                    $this->mensajeEstado(array($ciclo_facturacion->getContrato()->getId() => $ciclo_facturacion->getContrato()->getNumeroContrato()), 'Activo');
                }
            }
            
            $numeroAsiento = 0;

            if (!$entity->getEsCupon()) {

                $numeroAsiento = $this->generarAsientos($entity, 0, true);

                if ($numeroAsiento > 0) {

                    $em->flush();

                    $this->get('session')->getFlashBag()->add('success', 'El comprobante fue anulado.');

                    $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($numeroAsiento, array());
                } else {
                    $this->get('session')->getFlashBag()->add('error', 'El comprobante no pudo ser anulado por un error de los asientos.');
                }
            } //.
            else {

                $em->flush();
            }
        } //.
        else {
            $this->get('session')->getFlashBag()->add('error', $rsp);
        }

        return $this->redirect($this->generateUrl('comprobanteventa'));
    }

    /**
     * Finds and displays a ComprobanteCompra entity.
     *
     * @Route("/{id}", name="comprobanteventa_show")
     * @Method("GET")
     * @Template()
     */
//     * @Security("has_role('ROLE_VISUALIZAR_FACTURAS')")
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteVenta.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de venta'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver comprobante de venta'
        );
    }

    private function actualizarErrorIIBB($beneficiario, $limita) {

        /* @var $datosImpositivos \ADIF\ComprasBundle\Entity\DatosImpositivos */
        $datosImpositivos = $beneficiario->getDatosImpositivos();

        $exencion = $beneficiario->getCertificadoExencionIngresosBrutos();
        $exento = $datosImpositivos->getExentoIngresosBrutos();


        if ($exento) {
            if ($exencion != null) {
                if ($exencion->getFechaHasta() <= new \DateTime()) {
                    if (!isset($this->erroresIIBB[$beneficiario->getId()])) {
                        $this->erroresIIBB[$beneficiario->getId()] = $beneficiario;
                    }
                    $this->errorIIBB = 1;
                    if ($limita) {
                        $this->limitaGeneracion = 1;
                    }
                }
            }
        }
    }

    private function mostrarErrorIIBB() {

        if ($this->errorIIBB) {
            $errorMsg = '<span> El certificado de exenci&oacute;n de IIBB se encuentra vencido para los siguientes clientes:</span>';
            $errorMsg .= '<div style="padding-left: 3em; margin-top: .5em">';
            $errorMsg .= '<ul>';
            foreach ($this->erroresIIBB as $cliente) {
                $errorMsg .= '<li>' . $cliente . ' </li>';
            }
            $errorMsg .= '</ul>';
            $errorMsg .= '</div>';
            if ($this->limitaGeneracion) {
                $this->get('session')->getFlashBag()
                        ->add('error', "<span>No se pueden generar las facturas.</span>" . $errorMsg);
                return $this->redirect($this->generateUrl('contrato'));
            } else {
                $this->get('session')->getFlashBag()
                        ->add('warning', "<span>Si bien no corresponde retenci&oacute;n.</span>" . $errorMsg);
            }
        }
    }

    /**
     * 
     * @return type
     */
    private function getIdClaseContratoPliego() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $claseContrato = $em->getRepository('ADIFContableBundle:Facturacion\ClaseContrato')
                ->findOneByCodigo(ConstanteClaseContrato::PLIEGO);

        if ($claseContrato) {
            return $claseContrato->getId();
        }

        return null;
    }

    /**
     * 
     * @return type
     */
    private function getIdClaseContratoVentaGeneral() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $claseContrato = $em->getRepository('ADIFContableBundle:Facturacion\ClaseContrato')
                ->findOneByCodigo(ConstanteClaseContrato::VENTA_GENERAL);

        if ($claseContrato) {
            return $claseContrato->getId();
        }

        return null;
    }

    /**
     * @Route("/reenvioComprobantesElectronicos/", name="comprobanteventa_reenvio_facturacion_electronica")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVenta:reenvioComprobantesElectronicos.html.twig")
     */
//     * @Security("has_role('ROLE_CREAR_MODIFICAR_FACTURAS')")
    public function reenvioFacturacionElectronicaAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $pendientesAutorizacion = $em->getRepository('ADIFContableBundle:Facturacion\FacturaAlquiler')
                ->createQueryBuilder('fa')
                ->innerJoin('fa.puntoVenta', 'pv')
                ->where('pv.generaComprobanteElectronico = 1')
                ->andWhere('(fa.caeNumero IS NULL) or (fa.caeVencimiento IS NULL)')
                ->getQuery()
                ->getResult();

        $bread = $this->base_breadcrumbs;
        $bread['Contratos'] = $this->generateUrl('contrato');
        $bread['Reenv&iacute;o de facturaci&oacute;n electr&oacute;nica'] = null;

        return array(
            'conceptoPercepcionIIBB' => ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB,
            'conceptoPercepcionIVA' => ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA,
            'comprobantes' => $pendientesAutorizacion,
            'breadcrumbs' => $bread,
            'page_title' => 'Facturaci&oacute;n autom&aacute;tica'
        );
    }

    /**
     * @Route("/reenvioComprobantesElectronicosDescartar/", name="comprobanteventa_reenvio_facturacion_electronica_descartar")
     * @Method("POST")
     */
    public function descartarFacturacionElectronicaAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $pendientesAutorizacion = $em->getRepository('ADIFContableBundle:Facturacion\FacturaAlquiler')
                ->createQueryBuilder('fa')
                ->innerJoin('fa.puntoVenta', 'pv')
                ->where('pv.generaComprobanteElectronico = 1')
                ->andWhere('(fa.caeNumero IS NULL) or (fa.caeVencimiento IS NULL)')
                ->getQuery()
                ->getResult();

        /* @var $comprobante \ADIF\ContableBundle\Entity\Facturacion\FacturaAlquiler */
        foreach ($pendientesAutorizacion as $comprobante) {
            $em->remove($comprobante);
        }

        $em->flush();

        $this->get('session')->getFlashBag()
                ->add('success', 'Comprobantes electr&oacute;nicos descartados');

        return $this->redirect($this->generateUrl('contrato'));
    }

    /**
     * @Route("/reempujarComprobantesElectronicos/", name="comprobanteventa_reempujar_facturacion_electronica")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVenta:reenvioComprobantesElectronicosResultado.html.twig")
     */
    public function reempujarFacturacionElectronicaAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $comprobantes_electronicos = $em->getRepository('ADIFContableBundle:Facturacion\FacturaAlquiler')
                ->createQueryBuilder('fa')
                ->innerJoin('fa.puntoVenta', 'pv')
                ->where('pv.generaComprobanteElectronico = 1')
                ->andWhere('(fa.caeNumero IS NULL) or (fa.caeVencimiento IS NULL)')
                ->andWhere(' fa.id IN (130971,130972)')
                ->getQuery()
                ->getResult();

        $WSFE = $this->get('adif.wsfe_service');
        $comprobantesAutorizados = [];
        $comprobantesBorrados = [];
        $numeroAsiento = 0;
        $todosAutorizados = true;
        $errorAfip = '';

        foreach ($comprobantes_electronicos as $comprobante) {
            $response = $WSFE->consultarComprobante($comprobante->getId()); //id del comprobante a autorizar

            if (!$this->handlerErroresAFIP($respuestaAFIP, $comprobante)) {
                if ($respuestaAFIP["ResultGet"]["Resultado"] == 'A') {
                    $comprobante->setCaeNumero($respuestaAFIP["ResultGet"]["CodAutorizacion"]);
                    $comprobante->setCaeVencimiento(DateTime::createFromFormat('Ymd', $respuestaAFIP["ResultGet"]["FchVto"]));
                    $comprobantesAutorizados[] = $comprobante;
                    // Genero asientos presupuestarios y contables
                    $numeroAsiento = $this->generarAsientos($comprobante);
                    //DDJJ
                    $this->generarRenglonesPercepcionesComprobanteVenta($em, $comprobante);

                    $comprobante->getCicloFacturacion()->setCantidadFacturasPendientes($comprobante->getCicloFacturacion()->getCantidadFacturasPendientes() - 1);

                    if ($comprobante->getCicloFacturacion()->getContrato()->getContratoFinalizado()) {
                        $comprobante->getCicloFacturacion()->getContrato()->setEstadoContrato(
                                $em->getRepository('ADIFContableBundle:Facturacion\EstadoContrato')
                                        ->findOneByCodigo(ConstanteEstadoContrato::FINALIZADO)
                        );
                    }

                } else {

                    #$comprobantesBorrados[] = $comprobante;
                    #$em->remove($comprobante);
                }

                $em->flush();
            } else {
                $WSFE->autorizarComprobante($comprobantes_electronicos, $comprobante->getPuntoVenta->getId(), $comprobantegetTipoComprobante());
            }

            $electronicosConfirmados = array_merge($electronicosConfirmados, $resultadoAutorizacion['autorizados']);
            $todosAutorizados &= $resultadoAutorizacion['todosAutorizados'];
            $errorAfip .= $resultadoAutorizacion['errorAfip'];
        }

        $idsConfirmados = array();

        foreach ($confirmados as $comprobanteConfirmado) {
            $idsConfirmados[] = $comprobanteConfirmado->getId();
        }

        $confirmados = $em->getRepository('ADIFContableBundle:Comprobante')->getComprobantesById($idsConfirmados);

        if ($numeroAsiento > 0) {

            $this->get('adif.asiento_service')
                    ->showMensajeFlashAsientoContable($numeroAsiento, array());
        }

        $bread = $this->base_breadcrumbs;
        $bread['Contratos'] = $this->generateUrl('contrato');
        $bread['Resultado reenv&iacute;o de facturaci&oacute;n autom&aacute;tica'] = null;

        return array(
            'conceptoPercepcionIIBB' => \ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB,
            'conceptoPercepcionIVA' => \ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA,
            'comprobantes' => $comprobantesAutorizados,
            'comprobantesBorrados' => $comprobantesBorrados,
            'breadcrumbs' => $bread,
            'page_title' => 'Facturaci&oacute;n autom&aacute;tica'
        );
    }

    /**
     * @Route("/reenvioComprobantesElectronicosConfirmar/", name="comprobanteventa_reenvio_facturacion_electronica_reenviar")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ComprobanteVenta:reenvioComprobantesElectronicosResultado.html.twig")
     */
    public function confirmarFacturacionElectronicaAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $pendientesAutorizacion = $em->getRepository('ADIFContableBundle:Facturacion\FacturaAlquiler')
                ->createQueryBuilder('fa')
                ->innerJoin('fa.puntoVenta', 'pv')
                ->where('pv.generaComprobanteElectronico = 1')
                ->andWhere('(fa.caeNumero IS NULL) or (fa.caeVencimiento IS NULL)')
                ->getQuery()
                ->getResult();

        $WSFE = $this->get('adif.wsfe_service');
        $comprobantesAutorizados = [];
        $comprobantesBorrados = [];
//        $comprobantesPorContrato = [];
        $numeroAsiento = 0;

        /* @var $comprobante \ADIF\ContableBundle\Entity\Facturacion\FacturaAlquiler */
        foreach ($pendientesAutorizacion as $comprobante) {
            $response = $WSFE->consultarComprobante($comprobante->getId()); //id del comprobante a autorizar
			/*
			* Array de pruebas, lo dejo comentado que sirve para probar - gluis
/**/
/*
			$response['ResultGet'] = array(
				'Concepto' => 2,
				"DocTipo"=> 96,
				"DocNro"=> 13,
				"CbteDesde"=> 2737,
				"CbteHasta"=> 2737,
				"CbteFch"=> "20161101",
				"ImpTotal"=> 270,
				"ImpTotConc"=> 0,
				"ImpNeto"=> 0,
				"ImpOpEx"=> 270,
				"ImpTrib"=> 0,
				"ImpIVA"=> 0,
				"FchServDesde"=> "20151101",
				"FchServHasta"=> "20151201",
				"FchVtoPago"=> "20161105",
				"MonId"=> "PES",
				"MonCotiz"=> 1,
				"Resultado"=> "A",
				"CodAutorizacion"=> "66443004482118",
				"EmisionTipo"=> "CAE",
				"FchVto"=> "20161111",
				"FchProceso"=> "20161101170403",
				"PtoVta"=> 12,
				"CbteTipo"=> 6
			);
/**/
            $respuestaAFIP = json_decode(json_encode($response), true);

            if (!$this->handlerErroresAFIP($respuestaAFIP, $comprobante)) {
                if ($respuestaAFIP["ResultGet"]["Resultado"] == 'A') {
                    $comprobante->setCaeNumero($respuestaAFIP["ResultGet"]["CodAutorizacion"]);
                    $comprobante->setCaeVencimiento(DateTime::createFromFormat('Ymd', $respuestaAFIP["ResultGet"]["FchVto"]));
                    $comprobantesAutorizados[] = $comprobante;
                    // Genero asientos presupuestarios y contables
                    $numeroAsiento = $this->generarAsientos($comprobante);
                    //DDJJ
                    $this->generarRenglonesPercepcionesComprobanteVenta($em, $comprobante);

                    $comprobante->getCicloFacturacion()->setCantidadFacturasPendientes($comprobante->getCicloFacturacion()->getCantidadFacturasPendientes() - 1);

                    if ($comprobante->getCicloFacturacion()->getContrato()->getContratoFinalizado()) {
                        $comprobante->getCicloFacturacion()->getContrato()->setEstadoContrato(
                                $em->getRepository('ADIFContableBundle:Facturacion\EstadoContrato')
                                        ->findOneByCodigo(ConstanteEstadoContrato::FINALIZADO)
                        );
                    }

//                    if (!isset($comprobantesPorContrato[$comprobante->getContrato()->getId()])) {
//                        $comprobantesPorContrato[$comprobante->getContrato()->getId()] = 0;
//                    }
//                    $comprobantesPorContrato[$comprobante->getContrato()->getId()] ++;
                } else {
                    $comprobantesBorrados[] = $comprobante;
                    $em->remove($comprobante);
                }

                $em->flush();
            }
        }
        if ($numeroAsiento > 0) {

            $this->get('adif.asiento_service')
                    ->showMensajeFlashAsientoContable($numeroAsiento, array());
        }

        $bread = $this->base_breadcrumbs;
        $bread['Contratos'] = $this->generateUrl('contrato');
        $bread['Resultado reenv&iacute;o de facturaci&oacute;n autom&aacute;tica'] = null;

        return array(
            'conceptoPercepcionIIBB' => \ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB,
            'conceptoPercepcionIVA' => \ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA,
            'comprobantes' => $comprobantesAutorizados,
            'comprobantesBorrados' => $comprobantesBorrados,
            'breadcrumbs' => $bread,
            'page_title' => 'Facturaci&oacute;n autom&aacute;tica'
        );
    }

    /**
     * 
     * @param type $em
     * @param type $respuestaAFIP
     * @param FacturaVenta $comprobante
     * @return type
     */
    private function handlerUnicoResultadoAutorizacionElectronicaAFIP($em, $respuestaAFIP, $comprobante, $generacionAutomatica = false) {

        $respuesta = [
            'numeroAsiento' => 0,
            'aprobado' => false,
            'errorAfip' => ''
        ];

		//var_dump($respuestaAFIP);
		//exit;
        $comprobanteResponseAfip = $respuestaAFIP["FeDetResp"]["FECAEDetResponse"];
        /* @var $comprobante FacturaVenta */
        if ($comprobanteResponseAfip["Resultado"] == "A") {
            $comprobante->setCaeNumero($comprobanteResponseAfip["CAE"]);
            $comprobante->setCaeVencimiento(DateTime::createFromFormat('Ymd', $comprobanteResponseAfip["CAEFchVto"]));
            $comprobante->setCodigoBarras($comprobante->generarCodigoBarras());
            $respuesta['aprobado'] = true;

            // Genero asientos presupuestarios y contables
            $respuesta['numeroAsiento'] = $this->generarAsientos($comprobante, 0);

            $this->generarRenglonesPercepcionesComprobanteVenta($em, $comprobante);
        } else {
            //ver error de $respuestaAFIP
            if (isset($comprobanteResponseAfip['Observaciones'])) {

                $observaciones = $comprobanteResponseAfip['Observaciones']['Obs'];

                $observacionesArray = !is_array($observaciones) //
                        ? $observacionesArray = array($observaciones) //
                        : $observaciones;

                foreach ($observacionesArray as $indiceObs => $obs) {
                    if ($indiceObs == 'Msg') {
                        if ($generacionAutomatica) {
                            $respuesta['errorAfip'] = $obs;
                        } else {
							if (!is_array($obs)) {
								$this->get('session')->getFlashBag()
                                    ->add('error', 'No se pudo autorizar el comprobante. Rechazado por afip. ' . $obs);
							} else {
								$this->get('session')->getFlashBag()
                                    ->add('error', 'No se pudo autorizar el comprobante. Rechazado por afip. ' . implode('<br>', $obs));
							}
                        }
                    }
                }
            }
        }
        return $respuesta;
    }

    /**
     * 
     * @param type $em
     * @param type $comprobanteVenta
     */
    private function generarRenglonesPercepcionesComprobanteVenta($em, $comprobanteVenta) {

        foreach ($comprobanteVenta->getRenglonesPercepcion() as $renglonPercepcion) {

            if ($renglonPercepcion->getConceptoPercepcion()->getDenominacion() == ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB) {

                $regimenPercepcion = $this->get('adif.percepciones_service')->getRegimenIIBB($comprobanteVenta->getCliente(), $comprobanteVenta->getContrato());
				
                // Creo el Renglon de DDJJ asociado
                $renglonDDJJ = $this->crearRenglonDeclaracionJurada($comprobanteVenta, $regimenPercepcion, $renglonPercepcion, ConstanteTipoImpuesto::IIBB);

                $em->persist($renglonDDJJ);

                $renglonPercepcion->setRenglonDeclaracionJurada($renglonDDJJ);
            }
            if ($renglonPercepcion->getConceptoPercepcion()->getDenominacion() == ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA) {

                $regimenPercepcion = $this->get('adif.percepciones_service')
                        ->getRegimenIVA($comprobanteVenta->getCliente(), $comprobanteVenta->getContrato());

                // Creo el Renglon de DDJJ asociado
                $renglonDDJJ = $this->crearRenglonDeclaracionJurada($comprobanteVenta, $regimenPercepcion, $renglonPercepcion, ConstanteTipoImpuesto::IVA);

                $em->persist($renglonDDJJ);

                $renglonPercepcion->setRenglonDeclaracionJurada($renglonDDJJ);
            }
        }
    }

    /**
     * 
     * @param type $em
     * @param type $comprobanteVenta
     * @param type $idTipoComprobante
     * @param type $path
     * @return type
     * @throws type
     */
    private function generarComprobanteBase($em, $comprobanteVenta, $idTipoComprobante, $path) {

        $respuesta = [
            'numeroAsiento' => 0,
            'redirect' => $this->redirect($this->generateUrl('comprobanteventa'))
        ];

        $tipo_comprobante = null;

        if ($comprobanteVenta->getEsCupon() || $comprobanteVenta->getEsRendicionLiquidoProducto() || ($comprobanteVenta->getPuntoVenta() != null && !$comprobanteVenta->getPuntoVenta()->getGeneraComprobanteElectronico())) {

            if (!$comprobanteVenta->getEsCupon()) {

                if ($idTipoComprobante == ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES) {
                    $tipoComprobante = $em->getRepository('ADIFContableBundle:TipoComprobante')
                            ->find(ConstanteTipoComprobanteVenta::NOTA_DEBITO);
                } else {
                    $tipoComprobante = $em->getRepository('ADIFContableBundle:TipoComprobante')
                            ->find($idTipoComprobante);
                }

                // Obtengo el talonario
                if(!$comprobanteVenta->getEsRendicionLiquidoProducto()) {
                    $talonario = $em->getRepository('ADIFContableBundle:Facturacion\Talonario')
                            ->getTalonarioByTipoComprobanteYLetraYPuntoVenta($tipoComprobante, $comprobanteVenta->getLetraComprobante(), $comprobanteVenta->getPuntoVenta());

                    if (!$talonario) {
                        throw $this->createNotFoundException('No se puede encontrar la entidad Talonario.');
                    }

                    $this->get('adif.talonario_service')
                            ->getSiguienteNumeroComprobante($talonario);

                    $em->persist($talonario);
                }
            }

            // Genero asientos presupuestarios y contables
            $respuesta['numeroAsiento'] = $this->generarAsientos($comprobanteVenta, 0);

            // Renglones percepcion
            $this->generarRenglonesPercepcionesComprobanteVenta($em, $comprobanteVenta);
        } else {

            $WSFE = $this->get('adif.wsfe_service');

            $tipo_comprobante = ConstanteAfip::getTipoComprobante($comprobanteVenta->getLetraComprobante(), $comprobanteVenta->getTipoComprobante()->getId());

            if ($tipo_comprobante != -1) {

                if ($WSFE->getEstadoServidoresOk()) {

                    //N&uacute;mero del &uacute;ltimo comprobante autorizado para el punto de venta y tipo de comprobante dado
                    $ult_num_cbte = $WSFE->getUltCbte($comprobanteVenta->getPuntoVenta()->getNumero(), $tipo_comprobante) + 1;

                    $comprobanteVenta->setNumero(str_pad($ult_num_cbte, 8, '0', STR_PAD_LEFT));

                    //autorizar electronicamente
                    $response = $WSFE->autorizarComprobante(array($comprobanteVenta), $comprobanteVenta->getPuntoVenta()->getNumero(), $tipo_comprobante);

                    $respuestaAFIP = json_decode(json_encode($response), true);

                    $resultado = $this->handlerUnicoResultadoAutorizacionElectronicaAFIP($em, $respuestaAFIP, $comprobanteVenta);

                    if ($resultado['aprobado']) {

                        $em->persist($comprobanteVenta);

                        $respuesta['numeroAsiento'] = $resultado['numeroAsiento'];
                    } else {
                        $respuesta['redirect'] = $this->redirect($this->generateUrl($path));
                    }
                } else {
                    $this->get('session')->getFlashBag()
                            ->add('error', 'No se puede establecer conexi&ioacute;n con AFIP. Intente m&aacute;s tarde');

                    $respuesta['redirect'] = $this->redirect($this->generateUrl($path));
                }
            } else {

                $this->get('session')->getFlashBag()
                        ->add('error', 'No se puede generar este tipo de comprobante electr&oacute;nicamente');

                $respuesta['redirect'] = $this->redirect($this->generateUrl($path));
            }
        }

        return $respuesta;
    }

    /**
     * @Route("/generarAsientos/", name="comprobanteventa_asientos")
     * @Method("PUT|GET")     
     */
    public function generarAsientosComprobantesVenta() {

        gc_enable();

        $parcial = false;

        $offset = 0;
        $limit = 20;
        $i = 1;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $comprobantesImportados = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')
                ->createQueryBuilder('cv')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->orderBy('cv.id', 'asc')
                ->getQuery()
                ->getResult();

        $offset = $limit * $i;
        $i++;
        while (count($comprobantesImportados) > 0) {

            foreach ($comprobantesImportados as $comprobanteImportado) {
                // Genero el definitivo asociado
                $this->generarAsientos($comprobanteImportado);
                //$this->generarRenglonesPercepcionesComprobanteVenta($em, $comprobanteImportado);
            }
            unset($comprobantesImportados);
            $em->flush();
            $em->clear();
            gc_collect_cycles();
            $comprobantesImportados = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')
                    ->createQueryBuilder('cv')
                    ->setFirstResult($offset)
                    ->setMaxResults($limit)
                    ->orderBy('cv.id', 'asc')
                    ->getQuery()
                    ->getResult();
            $offset = $limit * $i;
            $i++;
        }
        unset($comprobantesImportados);
        $em->clear();
        unset($em);
        gc_collect_cycles();

        if (!$parcial) {
            $this->get('session')->getFlashBag()->add('success', 'Generacion de asientos de Comprobantes de Venta exitosa');
        }

        return $this->redirect($this->generateUrl('comprobanteventa'));
    }

    /**
     * 
     * @param type $respuestaAFIP
     * @return booleanErrores generales de AFIP
     */
    private function handlerErroresAFIP($respuestaAFIP, $comprobante) {
        if (isset($respuestaAFIP["Errors"])) {
            foreach ($respuestaAFIP["Errors"] as $error) {
                $this->get('session')->getFlashBag()
                        ->add('error', 'Error al enviar a AFIP el comprobante (' . $comprobante->getObservaciones() . ') - ' . $error['Msg']);
            }
            return true;
        }
        return false;
    }

    private function mensajeEstado($arrayNroContrato, $estado) {

        if (count($arrayNroContrato) > 1) {
            $msg = 'Se cambiaron los estados de los siguientes contratos a ' . $estado . ': ';
            $last_key = end(array_keys($arrayNroContrato));
            foreach ($arrayNroContrato as $id => $nro) {
                if ($id == $last_key) {
                    $msg .= '<a target="_blank"  href="' . $this->generateUrl('contrato_show', array('id' => $id)) . '">' . $nro . '</a>';
                } else {
                    $msg .= '<a target="_blank"  href="' . $this->generateUrl('contrato_show', array('id' => $id)) . '">' . $nro . '</a>, ';
                }
            }
        } else {
            $index = array_keys($arrayNroContrato)[0];
            $msg = 'Se cambi&oacute; el estado del contrato <a target="_blank"  href="' . $this->generateUrl('contrato_show', array('id' => $index)) . '">' . $arrayNroContrato[$index] . '</a> a ' . $estado;
        }

        $this->get('session')->getFlashBag()->add(
                'success', $msg
        );
    }

    /**
     * Tabla para CuponVenta de garant&iacute;a.
     *
     * @Route("/index_table_cupones/", name="comprobanteventa_index_table_cupones")
     * @Method("GET|POST")
     */
    public function indexTableCuponesAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $cupones = $em->getRepository('ADIFContableBundle:Facturacion\CuponVenta')
                ->createQueryBuilder('cv')
                ->innerJoin('cv.contrato', 'c')
                ->where('c.idCliente = :idCliente')
                ->setParameter('idCliente', $request->query->get('id_cliente'))
                ->orderBy('cv.numeroCupon', 'ASC')
                ->getQuery()
                ->getResult();

        $cuponesFiltrados = array_filter($cupones, function($cupon) {
            return $cupon->getEsCuponGarantia() && $cupon->getSaldoCuponGarantia() > 0;
        });

        return $this->render('ADIFContableBundle:Facturacion\ComprobanteVenta:index_table_cupones.html.twig', array('cupones' => $cuponesFiltrados));
    }

    /**
     * 
     * @param ComprobanteVenta $comprobanteVenta
     */
    private function setComprobanteImpresion(ComprobanteVenta $comprobanteVenta) {

        $comprobanteImpresion = new \ADIF\ContableBundle\Entity\ComprobanteImpresion();

        /* @var $cliente \ADIF\ComprasBundle\Entity\Cliente */
        $cliente = $comprobanteVenta->getCliente();

        $domicilioLegal = $cliente->getClienteProveedor()->getDomicilioLegal();

        $comprobanteImpresion
                ->setRazonSocial($cliente->getRazonSocial());

        $comprobanteImpresion
                ->setNumeroDocumento($cliente->getNroDocumento());

        $comprobanteImpresion
                ->setProvincia($domicilioLegal->getLocalidad()->getProvincia());

        $comprobanteImpresion
                ->setLocalidad($domicilioLegal->getLocalidad()->getNombre());

        $comprobanteImpresion
                ->setCodigoPostal($domicilioLegal->getCodPostal());

        $comprobanteImpresion
                ->setDomicilioLegal($domicilioLegal->__toString());

        $comprobanteImpresion
                ->setCondicionIVA($cliente->getClienteProveedor()
                        ->getDatosImpositivos()->getCondicionIVA()
                        ->getDenominacionTipoResponsable());

        $comprobanteImpresion
                ->setPeriodo($comprobanteVenta->getPeriodo());

        $comprobanteVenta
                ->setComprobanteImpresion($comprobanteImpresion);
    }

//    /**
//     * Finds and displays a  entity.
//     *
//     * @Route("/generarComprobanteImpresion/", name="comprobanteventa_generar_impresion")
//     * @Method("GET")
//     */
//    public function generarComprobanteImpresionAction() {
//        gc_enable();
//
//        $parcial = false;
//
//        $offset = 0;
//        $limit = 20;
//        $i = 1;
//
//        $em = $this->getDoctrine()->getManager($this->getEntityManager());
//        $comprobantesImportados = $em->getRepository('ADIFContableBundle:Facturacion\FacturaAlquiler')
//                ->createQueryBuilder('cv')
//                ->where("cv.fechaComprobante between '2015-08-01' and '2015-12-31'")
//                ->andWhere('cv.comprobanteImpresion is null')
//                ->andWhere('cv.caeVencimiento is not null')
//                ->setFirstResult($offset)
//                ->setMaxResults($limit)
//                ->getQuery()
//                ->getResult();
//
//        $offset = $limit * $i;
//        $i++;
//        while (count($comprobantesImportados) > 0) {
//
//            foreach ($comprobantesImportados as $comprobanteImportado) {
//                $this->setComprobanteImpresion($comprobanteImportado);
//            }
//            unset($comprobantesImportados);
//            $em->flush();
//            $em->clear();
//            gc_collect_cycles();
//            $comprobantesImportados = $em->getRepository('ADIFContableBundle:Facturacion\FacturaAlquiler')
//                    ->createQueryBuilder('cv')
//                    ->where("cv.fechaComprobante between '2015-08-01' and '2015-12-31'")
//                    ->andWhere('cv.comprobanteImpresion is null')
//                    ->andWhere('cv.caeVencimiento is not null')
//                    ->setFirstResult($offset)
//                    ->setMaxResults($limit)
//                    ->getQuery()
//                    ->getResult();
//            $offset = $limit * $i;
//            $i++;
//        }
//        unset($comprobantesImportados);
//        $em->clear();
//        unset($em);
//        gc_collect_cycles();
//
//        if (!$parcial) {
//            $this->get('session')->getFlashBag()->add('success', 'Generacion de asientos impresion exitosa');
//        }
//
//        return $this->redirect($this->generateUrl('comprobanteventa'));
//    }

	/**
     * Finds and displays a  entity.
     *
     * @Route("/ultimoComprobanteAutorizadoAFIP/{puntoVenta}/{tipoComprobante}", name="comprobanteventa_ultimo_comprobante_autorizado_afip")
     * @Method("GET")
     */
	public function getUltimoComprobanteAutorizadoAFIPAction()
	{
		$WSFE = $this->get('adif.wsfe_service');
		
		/*
		const FACTURA_A = 1;
		const NOTA_DEBITO_A = 2;
		const NOTA_CREDITO_A = 3;
		const FACTURA_B = 6;
		const NOTA_DEBITO_B = 7;
		const NOTA_CREDITO_B = 8;
		const RECIBO_A = 4;
		//const NOTAS_VENTA_CONTADO_A = 5;
		const RECIBOS_B = 9;
		*/
		
		$ult = $WSFE->getUltCbte('0012','6');
		var_dump($ult);
		die();
	}
    
     /**
     * 
     * @param Request $request
     * 
     * @Route("/get_contratos_by_idCliente", name="comprobanteventa_get_contratos_by_idCliente")
     * @Method("POST")
     */
    public function getContratosByIdCliente(Request $request) 
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idCliente = $request->get('idCliente');
        
        $contratos = $em->getRepository('ADIFContableBundle:Facturacion\ContratoVenta')
                        ->getContratosByIdCliente($idCliente);
        
        $arrayContratos = array();
        foreach($contratos as $i => $contrato) {
            $arrayContratos[$i]['id'] = $contrato->getId();
            $arrayContratos[$i]['numeroContrato'] = $contrato->getNumeroContrato();
        }
        
        return new JsonResponse($arrayContratos);
    }
}
