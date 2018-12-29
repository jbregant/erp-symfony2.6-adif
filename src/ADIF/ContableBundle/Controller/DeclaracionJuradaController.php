<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoRegimenPercepcion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoPagoACuenta;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoDeclaracionJurada;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteRegimenRetencion;
use ADIF\ContableBundle\Entity\DeclaracionJuradaImpuestoIIBB;
use ADIF\ContableBundle\Entity\DeclaracionJuradaImpuestoSICORE;
use ADIF\ContableBundle\Entity\DeclaracionJuradaImpuestoSICOSS;
use ADIF\ContableBundle\Entity\DeclaracionJuradaImpuestoSIJP;
use ADIF\ContableBundle\Entity\PagoACuenta;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use ZipArchive;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado;
use ADIF\BaseBundle\Entity\AdifApi;
use ADIF\RecursosHumanosBundle\Entity\Convenio;
use ADIF\ContableBundle\Entity\AdifDatos;

/**
 * DeclaracionJuradaController controller.
 *
 * @Route("/declaracion_jurada")
 */
class DeclaracionJuradaController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Declaraciones juradas' => $this->generateUrl('declaracion_jurada')
        );
    }

    /**
     * Lists all DeclaracionJurada entities.
     *
     * @Route("/", name="declaracion_jurada")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Declaraciones juradas'] = null;

        return array(
            'readonly' => false,
            'breadcrumbs' => $bread,
            'page_title' => 'Declaraciones juradas',
            'page_info' => 'Lista de declaraciones juradas'
        );
    }

    /**
     * Lists all DeclaracionJurada entities.
     *
     * @Route("/index_table_renglones_ddjj/", name="index_table_renglones_ddjj")
     * @Method("GET|POST")
     * @Template()
     */
    public function indexTableAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $fechaDesde = new DateTime();
        $fechaHasta = new DateTime();
        $intervalo = new \DateInterval('P3M'); 
        $fechaDesde->sub($intervalo); // 3 meses para atras
        
        $impuestos = $request->query->get('impuestos', '[]');
        // Obtengo los renglones de DDJJ 
        $renglonesDDJJ = $em->getRepository('ADIFContableBundle:RenglonDeclaracionJurada')->
                findByTiposImpuestoAndFechaDesdeAndFechaHasta($impuestos, $fechaDesde, $fechaHasta);

        $result = array();
        
        foreach ($renglonesDDJJ as $renglonDeclaracionJurada) {
            /* @var $renglonDeclaracionJurada \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada */
            if ($renglonDeclaracionJurada->getTipoRenglonDeclaracionJurada()->getCodigo() == ConstanteTipoRenglonDeclaracionJurada::COMPROBANTE_RETENCION_IMPUESTO_COMPRA) {
                /* @var $renglonDeclaracionJurada \ADIF\ContableBundle\Entity\RenglonDeclaracionJuradaComprobanteRetencionImpuesto */

                $ordenesPago = $em->getRepository('ADIFContableBundle:OrdenPago')
                        ->createQueryBuilder('op')
                        ->innerJoin('op.retenciones', 'r')
                        ->innerJoin('r.renglonDeclaracionJurada', 'rdj')
                        ->innerJoin('op.estadoOrdenPago', 'eop')
                        ->where('rdj.id = (:idRenglon)')
                        ->andWhere('eop.denominacionEstado = :estado')
                        ->setParameters(array(
                            'idRenglon' => $renglonDeclaracionJurada->getId(),
                            'estado' => ConstanteEstadoOrdenPago::ESTADO_PAGADA)
                        )
                        ->getQuery()
                        ->getOneOrNullResult();

                /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPago */
                if ($ordenesPago) {
                    //unset($renglonDeclaracionJurada);
                    $result[] = $renglonDeclaracionJurada;
                }
            } else {
                $result[] = $renglonDeclaracionJurada;
            }
        }

//        // Debug
//        foreach($result as $op) {
//            echo "<br>--------------------<br>";
//            echo "OP ID = " . $op->getId() . "<br>";
//            echo "OP get class: " . get_class($op) . "<br>";
//            echo "OP Beneficiario: " . $op->getBeneficiario() . "<br>";
//        }

        return $this->render('ADIFContableBundle:DeclaracionJurada:index_table_retenciones.html.twig', array('entities' => $result));
    }

    /**
     * Lists all PagoACuenta entities.
     *
     * @Route("/index_table_pagos_a_cuenta/", name="index_table_pagos_a_cuenta")
     * @Method("GET|POST")
     * @Template()
     */
    public function indexTablePagosACuentaAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $fechaDesde = new DateTime();
        $fechaHasta = new DateTime();
        $intervalo = new \DateInterval('P3M'); 
        $fechaDesde->sub($intervalo); // 3 meses para atras
        
        $tipo = $request->query->get('tipo', '[]');

        // Obtengo los pagos a cuenta
        $pagosACuenta = $em
                ->getRepository('ADIFContableBundle:PagoACuenta')
                ->findByTipoAndFechaDesdeAndFechaHasta($tipo, $fechaDesde, $fechaHasta);

        return $this->render('ADIFContableBundle:DeclaracionJurada:index_table_pagos_a_cuenta.html.twig', array('entities' => $pagosACuenta));
    }

    public function indexTableIIBBAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $mesActual = (new DateTime())->format('m');

        // Obtengo los renglones de DDJJ de IVA y Ganancias
        $renglonesDDJJIIBB = $em->getRepository('ADIFContableBundle:RenglonDeclaracionJurada')->
                findByTiposImpuestoAndPeriodo(
                array(ConstanteTipoImpuesto::IIBB), $mesActual
        );

        return $this->render('ADIFContableBundle:DeclaracionJurada:index_table_retenciones.html.twig', array('entities' => $renglonesDDJJIIBB));
    }

    /**
     * @Route("/crear-pago-cuenta/", name="declaracion_jurada_crear_pagocuenta")
     * @Method("POST")
     * @Template("ADIFContableBundle:DeclaracionJurada:index.html.twig")
     */
    public function crearPagoCuentaAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $pagoACuenta = new PagoACuenta();

        $ids = json_decode($request->request->get('ids'));

        $tipoDeclaracionJurada = $request->request->get('tipo_declaracion_jurada');

        $importe = 0;

        // Por cada renglon de DDJJ obtenido
        foreach ($ids as $idRenglonDeclaracionJurada) {

            $renglonDeclaracionJurada = $em->getRepository('ADIFContableBundle:RenglonDeclaracionJurada')
                    ->find($idRenglonDeclaracionJurada);

            $renglonDeclaracionJurada->setEstadoRenglonDeclaracionJurada($em->getRepository('ADIFContableBundle:EstadoRenglonDeclaracionJurada')
                            ->findOneByDenominacion(ConstanteEstadoRenglonDeclaracionJurada::CON_PAGO_A_CUENTA));

            $importe += $renglonDeclaracionJurada->getMonto();

            $pagoACuenta->addRenglonesDeclaracionJurada($renglonDeclaracionJurada);
        }

        $pagoACuenta->setEstadoPagoACuenta($em->getRepository('ADIFContableBundle:EstadoPagoACuenta')
                        ->findOneByDenominacion(ConstanteEstadoPagoACuenta::PENDIENTE));

        $pagoACuenta->setTipoDeclaracionJurada($tipoDeclaracionJurada);

        // Genero la AutorizacionContable            
        $ordenPagoService = $this->get('adif.orden_pago_service');

        $concepto = 'Pago a cuenta - Declaración Jurada ' . $tipoDeclaracionJurada
                . ' - ' . $pagoACuenta->getPeriodo();

        $ordenPagoService
                ->crearAutorizacionContablePagoACuenta($em, $pagoACuenta, $importe, $concepto);

        $em->persist($pagoACuenta);

        $em->flush();

        $this->get('session')->getFlashBag()
                ->add('success', "Se gener&oacute; la autorizaci&oacute;n "
                        . "contable con &eacute;xito, con un "
                        . "importe de $ " . number_format($importe, 2, ',', '.'));

        return $this->redirect($this->generateUrl('declaracion_jurada'));
    }
    
    /**
     * @Route("/crear-devolucion/", name="declaracion_jurada_crear_devolucion")
     * @Method("POST")
     * @Template("ADIFContableBundle:DeclaracionJurada:index.html.twig")
     */
    public function crearDevolucionAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id = $request->request->get('id_renglon');        
        $montoDevolucion = str_replace(',', '.', $request->request->get('monto_devolucion'));
        
        $renglonDDJJ = $em->getRepository('ADIFContableBundle:RenglonDeclaracionJurada')->find($id);
        /* @var $renglonDDJJ \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada */
        
        $renglonDDJJ->setMonto($renglonDDJJ->getMonto() - $montoDevolucion);
        
        if($renglonDDJJ->getMonto() == 0){
            $renglonDDJJ->setEstadoRenglonDeclaracionJurada($em->getRepository('ADIFContableBundle:EstadoRenglonDeclaracionJurada')->findOneByDenominacion(ConstanteEstadoRenglonDeclaracionJurada::CON_DEVOLUCION));
        }
        
        $devolucion = new \ADIF\ContableBundle\Entity\DevolucionRenglonDeclaracionJurada();
        
        $devolucion->setFecha(new \DateTime());
        $devolucion->setMonto($montoDevolucion);
        $devolucion->setRenglonDeclaracionJurada($renglonDDJJ);
        
        // Genero la AutorizacionContable            
        $ordenPagoService = $this->get('adif.orden_pago_service');

        $concepto = 'Devoluci&oacute;n retenciones '.$renglonDDJJ->getTipoImpuesto()->getDenominacion();

        $em->persist($devolucion);

        $autorizacionContable = $ordenPagoService->crearAutorizacionContableDevolucionRenglonDeclaracionJurada($em, $devolucion, $montoDevolucion, $concepto);

        $em->flush();

        $this->get('session')->getFlashBag()
                ->add('success', "Se gener&oacute; la autorizaci&oacute;n "
                        . "contable con &eacute;xito, con un "
                        . "importe de $ " . number_format($montoDevolucion, 2, ',', '.'));
        
        $mensajeImprimir = 'Para imprimir la autorizaci&oacute;n contable haga click <a href="'
                    . $this->generateUrl($autorizacionContable->getPathAC()
                            . '_print', ['id' => $autorizacionContable->getId()])
                    . '" class="link-imprimir-op">aqu&iacute;</a>';

        $this->get('session')->getFlashBag()->add('info', $mensajeImprimir);

        return $this->redirect($this->generateUrl('declaracion_jurada'));
    }

    /**
     * @Route("/crear-ddjj/", name="declaracion_jurada_crear_ddjj")
     * @Method("POST")
     * @Template("ADIFContableBundle:DeclaracionJurada:index.html.twig")
     */
    public function crearDeclaracionJuradaAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $renglonesDeclaracionJuradaIds = json_decode($request->request->get('renglones_declaracion_jurada_ids'));

        $pagosACuentaIds = json_decode($request->request->get('renglones_pago_a_cuenta_ids'));

        $tipoDeclaracionJurada = $request->request->get('tipo_declaracion_jurada');

        switch ($tipoDeclaracionJurada) {
            case ConstanteTipoDeclaracionJurada::SICORE:
                $declaracionJurada = new DeclaracionJuradaImpuestoSICORE();
                break;
            case ConstanteTipoDeclaracionJurada::SICOSS:
                $declaracionJurada = new DeclaracionJuradaImpuestoSICOSS();
                break;
            case ConstanteTipoDeclaracionJurada::SIJP:
                $declaracionJurada = new DeclaracionJuradaImpuestoSIJP();
                break;
            case ConstanteTipoDeclaracionJurada::IIBB:
                $declaracionJurada = new DeclaracionJuradaImpuestoIIBB();
                break;
            default:
                break;
        }

        $importeTotalRenglonesDDJJ = 0;

        // Por cada renglon de DDJJ obtenido
        foreach ($renglonesDeclaracionJuradaIds as $idRenglonDeclaracionJurada) {

            $renglonDeclaracionJurada = $em->getRepository('ADIFContableBundle:RenglonDeclaracionJurada')
                    ->find($idRenglonDeclaracionJurada);

            $renglonDeclaracionJurada->setEstadoRenglonDeclaracionJurada($em->getRepository('ADIFContableBundle:EstadoRenglonDeclaracionJurada')
                            ->findOneByDenominacion(ConstanteEstadoRenglonDeclaracionJurada::CON_DDJJ));

            $declaracionJurada->addRenglonesDeclaracionJurada($renglonDeclaracionJurada);

            $importeTotalRenglonesDDJJ += $renglonDeclaracionJurada->getMonto();
        }

        $importeTotalPagoACuenta = 0;

        // Por cada pago a cuenta obtenido
        foreach ($pagosACuentaIds as $pagoACuentaId) {

            $pagoACuenta = $em->getRepository('ADIFContableBundle:PagoACuenta')
                    ->find($pagoACuentaId);

            $pagoACuenta->setEstadoPagoACuenta($em->getRepository('ADIFContableBundle:EstadoPagoACuenta')
                            ->findOneByDenominacion(ConstanteEstadoPagoACuenta::CON_DDJJ));

            $declaracionJurada->addPagosACuentum($pagoACuenta);

            $importeTotalPagoACuenta += $pagoACuenta->getImporte();
        }

        $importeAutorizacionContable = $importeTotalRenglonesDDJJ;

        // Si el importe de la AutorizacionContable fuese mayor a cero, la genero
        if ($importeAutorizacionContable > 0) {

            // Obtengo el servicio
            $ordenPagoService = $this->get('adif.orden_pago_service');

            $concepto = 'Declaración Jurada ' . $tipoDeclaracionJurada
                    . ' - ' . $declaracionJurada->getPeriodo();

            // Genero la AutorizacionContable   
            $ordenPagoService
                    ->crearAutorizacionContableDeclaracionJurada($em, $declaracionJurada, $importeAutorizacionContable, $concepto);
        }

        $em->persist($declaracionJurada);

        $em->flush();

        $this->get('session')->getFlashBag()
                ->add('success', "Se gener&oacute; la declaraci&oacute;n jurada con &eacute;xito.");

        return $this->redirect($this->generateUrl('declaracion_jurada'));
    }

//    /**
//     * Genera el archivo de exportacion de retenciones sicore
//     *
//     * @Route("/generar_sicore/{id}", name="generar_sicore")
//     * @Method("GET")
//     */
//    public function generarSicoreAction($id) {
//        $em = $this->getDoctrine()->getManager($this->getEntityManager());
//        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());
//
//        /* @var $entity \ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto */
//        $entity = $em->getRepository('ADIFContableBundle:DeclaracionJuradaImpuesto')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('No se puede encontrar la entidad DeclaracionJuradaImpuesto.');
//        }
//
//        $filename = 'retencion_sicore_' . $entity->getFecha()->format('Ymd') . '.txt';
//        $path = $this->get('kernel')->getRootDir() . '/../web/uploads/declaraciones_juradas/sicore/' . $filename;
//
//        $char_pad_string = ' ';
//        $char_pad_int = '0';
//
//        $type_pad_string = STR_PAD_RIGHT;
//        $type_pad_int = STR_PAD_LEFT;
//
//        $f = fopen($path, "w");
//
//        //\Doctrine\Common\Util\Debug::dump($this->getRenglonesDDJJ($entity));die;
//
//        foreach ($this->getRenglonesDDJJ($entity) as $renglonDeclaracionJurada) {
//            /* @var $renglonDeclaracionJurada \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada */
//
//            if ($renglonDeclaracionJurada->getTipoRenglonDeclaracionJurada()->getCodigo() == ConstanteTipoRenglonDeclaracionJurada::LIQUIDACION) {
//                // Si es un renglon de liquidacion, saco los datos de la liquidacion                
//
//                $liquidacion = $renglonDeclaracionJurada->getLiquidacion();
//
//                $fecha_recibo = $liquidacion->getFechaPago()->format('d/m/Y');
//                foreach ($liquidacion->getLiquidacionEmpleados() as $liquidacionEmpleado) {
//                    /* @var $liquidacionEmpleado LiquidacionEmpleado */
//                    /* @var $empleado Empleado */
//                    /* @var $persona Persona */
//
//                    $empleado = $liquidacionEmpleado->getEmpleado();
//                    $persona = $empleado->getPersona();
//
//                    //Recibo
//                    $nro_recibo = $liquidacionEmpleado->getId();
//
//                    $imp_retencion = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getGananciaByEmpleado($liquidacionEmpleado->getEmpleado()->getId(), $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades());
//
//                    if ($imp_retencion != 0) {
//                        $imp_retencion = str_replace('.', ',', $imp_retencion);
//
//                        /* 1 */ $cadena = str_pad('07', 2, $char_pad_int, $type_pad_int); //Código de comprobante
//                        /* 2 */ $cadena.= str_pad($fecha_recibo, 10, $char_pad_string, $type_pad_string); //Fecha emision comprobante
//                        /* 3 */ $cadena.= str_pad($nro_recibo, 16, $char_pad_int, $type_pad_int); //Número de comprobante    
//                        /* 4 */ $cadena.= str_pad('0,00', 16, $char_pad_int, $type_pad_int); //Importe del comprobante
//                        /* 5 */ $cadena.= str_pad('217', 3, $char_pad_int, $type_pad_int); //Código de Impuesto
//                        /* 6 */ $cadena.= str_pad('160', 3, $char_pad_int, $type_pad_int); //Código de regimen
//                        /* 7 */ $cadena.= str_pad('1', 1, $char_pad_int, $type_pad_int); //Código de regimen
//                        /* 8 */ $cadena.= str_pad('0,00', 14, $char_pad_int, $type_pad_int); //Base de cálculo
//                        /* 9 */ $cadena.= str_pad($fecha_recibo, 10, $char_pad_string, $type_pad_string); //Fecha emision retención
//                        /* 10 */ $cadena.= str_pad('01', 2, $char_pad_int, $type_pad_int); //Código de condición
//                        /* 11 */ $cadena.= str_pad('0', 1, $char_pad_int, $type_pad_int); //Retención practicada a sujetos suspendidos
//                        /* 12 */ $cadena.= str_pad($imp_retencion, 14, $char_pad_int, $type_pad_int); //Importe de la retención
//                        /* 13 */ $cadena.= str_pad('0,00', 6, $char_pad_int, $char_pad_int); //Porcentaje de exclusion
//                        /* 14 */ $cadena.= str_pad('', 10, $char_pad_string, $type_pad_string); //Fecha emision boletín
//                        /* 15 */ $cadena.= str_pad('80', 2, $char_pad_int, $type_pad_int); //Tipo de documento retenido
//                        /* 16 */ $cadena.= str_pad(strval(str_replace('-', '', $persona->getCuil())), 20, $char_pad_string, $type_pad_string); //Número de documento de retenido                                    
//                        /* 17 */ $cadena.= str_pad('0', 14, $char_pad_int, $type_pad_int); //Número certificado original
//                        /* 18 */ $cadena.= str_pad('', 30, $char_pad_string, $type_pad_string); //Denominación del ordenante
//                        /* 19 */ $cadena.= str_pad('0', 1, $char_pad_int, $type_pad_int); //Acrecentamiento
//                        /* 20 */ $cadena.= str_pad('0', 11, $char_pad_int, $type_pad_int); //Cuit del pais del retenido
//                        /* 21 */ $cadena.= str_pad('0', 11, $char_pad_int, $type_pad_int); //Cuit del ordenante
//                        fwrite($f, $cadena);
//                        fwrite($f, chr(13) . chr(10));
//                    }
//                }
//            } else {
//                // Si no es un renglon de liquidacion, es de un comprobante de retencion
//
//                /* @var $comprobanteRetencion \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto */
//
//                $comprobanteRetencion = $renglonDeclaracionJurada->getComprobanteRetencionImpuesto();
//
//                if ($comprobanteRetencion->getOrdenPago()->getEstadoOrdenPago() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
//                    $comprobantes = $this->get('adif.retenciones_service')->getDatosComprobantesAplicanImpuesto($comprobanteRetencion);
//                    foreach ($comprobantes as $comprobante) {
//                        /* 1 */ $cadena = str_pad('07', 2, $char_pad_int, $type_pad_int); //Código de comprobante
//                        /* 2 */ $cadena.= str_pad($comprobante['fecha'], 10, $char_pad_string, $type_pad_string); //Fecha emision comprobante
//
//                        /* 3 */ $cadena.= str_pad($comprobante['numero'], 16, $char_pad_int, $type_pad_int); //Número de comprobante    
//                        /* 4 */ $cadena.= str_pad($comprobante['importe'], 16, $char_pad_int, $type_pad_int); //Importe del comprobante
//                        /* 5 */ $cadena.= str_pad('217', 3, $char_pad_int, $type_pad_int); //Código de Impuesto
//                        /* 6 */ $cadena.= str_pad('160', 3, $char_pad_int, $type_pad_int); //Código de regimen
//                        /* 7 */ $cadena.= str_pad('1', 1, $char_pad_int, $type_pad_int); //Código de operacion
//                        /* 8 */ $cadena.= str_pad(number_format($comprobante['base_calculo'], 2, ',', ''), 14, $char_pad_int, $type_pad_int); //Base de cálculo
//                        /* 9 */ $cadena.= str_pad($comprobanteRetencion->getOrdenPago()->getFechaOrdenPago()->format('d/m/Y'), 10, $char_pad_string, $type_pad_string); //Fecha emision retención
//                        /* 10 */ $cadena.= str_pad('01', 2, $char_pad_int, $type_pad_int); //Código de condición
//                        /* 11 */ $cadena.= str_pad('0', 1, $char_pad_int, $type_pad_int); //Retención practicada a sujetos suspendidos
//                        /* 12 */ $cadena.= str_pad(number_format($comprobante['retencion'], 2, ',', ''), 14, $char_pad_int, $type_pad_int); //Importe de la retención
//                        /* 13 */ $cadena.= str_pad('0,00', 6, $char_pad_int, $char_pad_int); //Porcentaje de exclusion
//                        /* 14 */ $cadena.= str_pad('', 10, $char_pad_string, $type_pad_string); //Fecha emision boletín
//                        /* 15 */ $cadena.= str_pad('80', 2, $char_pad_int, $type_pad_int); //Tipo de documento retenido
//                        /* 16 */ $cadena.= str_pad(strval(str_replace('-', '', $renglonDeclaracionJurada->getCUITBeneficiario())), 20, $char_pad_string, $type_pad_string); //Número de documento de retenido
//
//                        /* 17 */ $cadena.= str_pad('0', 14, $char_pad_int, $type_pad_int); //Número certificado original
//                        /* 18 */ $cadena.= str_pad('', 30, $char_pad_string, $type_pad_string); //Denominación del ordenante
//                        /* 19 */ $cadena.= str_pad('0', 1, $char_pad_int, $type_pad_int); //Acrecentamiento
//                        /* 20 */ $cadena.= str_pad('0', 11, $char_pad_int, $type_pad_int); //Cuit del pais del retenido
//                        /* 21 */ $cadena.= str_pad('0', 11, $char_pad_int, $type_pad_int); //Cuit del ordenante
//                        fwrite($f, $cadena);
//                        fwrite($f, chr(13) . chr(10));
//                    }
//                }
//            }
//        }
//
//        fclose($f);
//
//        $response = new BinaryFileResponse($path);
//        $d = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
//        $response->headers->set('Content-Type', 'text/plain');
//        $response->headers->set('Content-Disposition', $d);
//
//        return $response;
//    }

    /**
     * Genera el archivo de exportacion de retenciones sicore
     *
     * @Route("/generar_sicoss/{id}", name="generar_sicoss")
     * @Method("GET")
     */
    public function generarSicossAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        /* @var $entity \ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto */
        $entity = $em->getRepository('ADIFContableBundle:DeclaracionJuradaImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DeclaracionJuradaImpuesto.');
        }

        $filename = 'retencion_sicoss_' . $entity->getFecha()->format('Ymd') . '.txt';
        $path = $this->get('kernel')->getRootDir() . '/../web/uploads/declaraciones_juradas/sicoss/' . $filename;

        $char_pad_string = ' ';
        $char_pad_int = '0';

        $type_pad_string = STR_PAD_RIGHT;
        $type_pad_int = STR_PAD_LEFT;

        $f = fopen($path, "w");

        foreach ($this->getRenglonesDDJJ($entity) as $renglonDeclaracionJurada) {
            /* @var $renglonDeclaracionJurada \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada */

            if ($renglonDeclaracionJurada->getTipoRenglonDeclaracionJurada()->getCodigo() == ConstanteTipoRenglonDeclaracionJurada::LIQUIDACION) {
                // Si es un renglon de liquidacion, saco los datos de la liquidacion                

                $liquidacion = $renglonDeclaracionJurada->getLiquidacion();

                foreach ($liquidacion->getLiquidacionEmpleados() as $liquidacionEmpleado) {
                    /* @var $liquidacionEmpleado LiquidacionEmpleado */
                    /* @var $empleado Empleado */
                    /* @var $persona Persona */

                    $empleado = $liquidacionEmpleado->getEmpleado();
                    $persona = $empleado->getPersona();

                    $char_pad_string = ' ';
                    $char_pad_int = '0';
                    $char_pad_importe = ' ';

                    $type_pad_string = STR_PAD_RIGHT;
                    $type_pad_int = STR_PAD_LEFT;
                    $type_pad_importe = STR_PAD_LEFT;

                    $remunerativo_s_t = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getRemunerativoSinTopeByEmpleado($liquidacionEmpleado->getEmpleado()->getId(), $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades());
                    $remunerativo_c_t = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getRemunerativoConTopeByEmpleado($liquidacionEmpleado->getEmpleado()->getId(), $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades());
                    $no_remunerativo = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getNoRemunerativoByEmpleado($liquidacionEmpleado->getEmpleado()->getId(), $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades());

                    $campos = $this->cargarCampos931($liquidacionEmpleado);

                    $fechaInicio = new DateTime(date('Y-m-01', strtotime($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Y-m-d'))));
                    $fechaFin = new DateTime(date('Y-m-t', strtotime($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Y-m-d'))));
                    $licencias = $empleado->getLicenciasFechas($fechaInicio, $fechaFin);

                    /* 1 */ $cadena = str_replace('-', '', $persona->getCuil());
                    /* 2 */ $cadena .= $this->mb_str_pad(substr(AdifApi:: stringCleaner($persona->getApellido()) . ' ' . AdifApi:: stringCleaner($persona->getNombre()), 0, 30), 30, $char_pad_string, $type_pad_string);
                    /* 3 */ $cadena .= $empleado->tieneConyuge();
                    /* 4 */ $cadena .= str_pad(AdifApi:: stringCleaner($empleado->getCantidadHijos()), 2, $char_pad_int, $type_pad_int); //Cantidad de hijos

                    /* Licencias */ $licencia = $this->cargarLicencias($licencias, $fechaInicio, $fechaFin, $char_pad_int, $type_pad_int);

                    /* 5 */ $cadena .= str_pad($this->getCodigoSituacion($licencia), 2, $char_pad_int, $type_pad_int);  //Código de situación
                    /* 6 */ $cadena .= str_pad($empleado->getCondicion()->getCodigo(), 2, $char_pad_int, $type_pad_int);  //Código de condición
                    /* 7 */ $cadena .= str_pad('49', 3, $char_pad_int, $type_pad_int);  //Código de actividad
                    /* 8 */ $cadena .= str_pad('1', 2, $char_pad_int, $type_pad_int);  //Código de zona
                    /* 9 */ $cadena .= str_pad('00,00', 5, $char_pad_importe, $type_pad_importe);  //Porcentaje de aporte adicional SS
                    /* 10 */ $cadena .= str_pad($empleado->getTipoContratacionActual()->getTipoContrato()->getCodigo(), 3, $char_pad_int, $type_pad_int);  //Código de modalidad de contratación
                    /* 11 */ $cadena .= str_pad($empleado->getObraSocialActual()->getObraSocial()->getCodigo(), 6, $char_pad_int, $type_pad_int);  //Código de obra social
                    /* 12 */ $cadena .= str_pad('0', 2, $char_pad_int, $type_pad_int);  //Cantidad de adherentes
                    /* 13 */ $cadena .= str_pad(number_format($remunerativo_s_t + $no_remunerativo, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración total
                    /* 14 */ $cadena .= str_pad(number_format($remunerativo_c_t, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 1
                    /* 15 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Asignaciones familiares pagadas
                    /* 16 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Importe aporte voluntario
                    /* 17 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Importe adicional OS
                    /* 18 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Importe excedentes aportes SS
                    /* 19 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Importe excedentes aportes OS
                    /* 20 */ $cadena .= $this->mb_str_pad(AdifApi:: stringCleaner($persona->getDomicilio()->getLocalidad()->getProvincia()) . ' ' . AdifApi:: stringCleaner($persona->getDomicilio()->getLocalidad()), 50, $char_pad_string, $type_pad_string);
                    /* 21 */ $cadena .= str_pad(number_format($remunerativo_s_t, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 2
                    /* 22 */ $cadena .= str_pad(number_format($remunerativo_s_t, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 3
                    /* 23 */ $cadena .= str_pad(number_format($remunerativo_c_t, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 4
                    /* 24 */ $cadena .= str_pad('0', 2, $char_pad_int, $type_pad_int);  //Código de siniestrado
                    /* 25 */ $cadena .= '0';  //Marca de corresponde reducción
                    /* 26 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Capital de recomposición LRT
                    /* 27 */ $cadena .= str_pad('1', 1, $char_pad_int, $type_pad_int);  //Tipo de empresa
                    /* 28 */ $cadena .= str_pad($campos[28], 9, $char_pad_importe, $type_pad_importe);  //Aporte adicional de obra social
                    /* 29 */ $cadena .= str_pad('1', 1, $char_pad_int, $type_pad_int);  //Régimen            
                    /* 30 */ $cadena .= $licencia;

                    /* 36 */ $cadena .= str_pad(number_format($liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2(), 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Sueldo + adicionales
                    /* 37 */ $cadena .= str_pad($campos[37], 12, $char_pad_importe, $type_pad_importe);  //SAC
                    /* 38 */ $cadena .= str_pad('0,00', 12, $char_pad_importe, $type_pad_importe);  //Horas extras
                    /* 39 */ $cadena .= str_pad('0,00', 12, $char_pad_importe, $type_pad_importe);  //Zona desfavorable
                    /* 40 */ $cadena .= str_pad('0,00', 12, $char_pad_importe, $type_pad_importe);  //Vacaciones
                    /* 41 */ $cadena .= str_pad($empleado->getDiasTrabajados($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()) - $empleado->getDiasLicencia($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades(), $licencia), 9, $char_pad_int, $type_pad_int);  //Días trabajados
                    /* 42 */ $cadena .= str_pad(number_format($remunerativo_c_t, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 5
                    /* 43 */ $cadena .= $empleado->getIdSubcategoria()->getCategoria()->getConvenio()->getId() == Convenio::__FUERA_DE_CONVENIO ? 0 : 1; //Marca convencionado
                    /* 44 */ $cadena .= str_pad(number_format(0, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 6
                    /* 45 */ $cadena .= '0';  //Tipo operación
                    /* 46 */ $cadena .= str_pad('0,00', 12, $char_pad_importe, $type_pad_importe);  //Adicionales
                    /* 47 */ $cadena .= str_pad('0,00', 12, $char_pad_importe, $type_pad_importe);  //Premios
                    /* 48 */ $cadena .= str_pad(number_format($remunerativo_s_t, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Sueldo Dto 788 05 Remuneración 8
                    /* 49 */ $cadena .= str_pad(number_format(0, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 7
                    /* 50 */ $cadena .= str_pad('0', 3, $char_pad_int, $type_pad_int);  //Cantidad horas extra
                    /* 51 */ $cadena .= str_pad(number_format($no_remunerativo, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Conceptos no remunerativos
                    /* 52 */ $cadena .= str_pad('0,00', 12, $char_pad_importe, $type_pad_importe);  //Maternidad
                    /* 53 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Rectificación de remuneración
                    /* 54 */ $cadena .= str_pad(number_format($remunerativo_c_t + $no_remunerativo, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 9
                    /* 55 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Contribución tarea dif
                    /* 56 */ $cadena .= str_pad('0', 3, $char_pad_int, $type_pad_int);  //Horas trabajadas
                    /* 57 */ $cadena .= '1';  //Seguro colectivo de vida obligatorio

                    fwrite($f, $cadena);
                    fwrite($f, chr(13) . chr(10));
                }
            }
        }

        fclose($f);

        $response = new BinaryFileResponse($path);
        $d = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', $d);

        return $response;
    }

    
//    public function generarSijpAction($id) {
//        $em = $this->getDoctrine()->getManager($this->getEntityManager());
//
//        /* @var $entity \ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto */
//        $entity = $em->getRepository('ADIFContableBundle:DeclaracionJuradaImpuesto')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('No se puede encontrar la entidad DeclaracionJuradaImpuesto.');
//        }
//
//        $filename = 'retencion_sijp_' . $entity->getFecha()->format('Ymd') . '.txt';
//        $path = $this->get('kernel')->getRootDir() . '/../web/uploads/declaraciones_juradas/sijp/' . $filename;
//
//        $char_pad_string = ' ';
//        $char_pad_int = '0';
//
//        $type_pad_string = STR_PAD_RIGHT;
//        $type_pad_int = STR_PAD_LEFT;
//
//        $f = fopen($path, "w");
//
//        foreach ($this->getRenglonesDDJJ($entity) as $renglonDeclaracionJurada) {
//            /* @var $renglonDeclaracionJurada \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada */
//
//            /* @var $comprobanteRetencion \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto */
//
//            $comprobanteRetencion = $renglonDeclaracionJurada->getComprobanteRetencionImpuesto();
//
//            /* 1 */ $cadena = str_pad($comprobanteRetencion->getRegimenRetencion()->getCodigoSiap(), 3, $char_pad_int, $type_pad_int); //Código de régimen
//            /* 2 */ $cadena.= str_pad(strval(str_replace('-', '', $renglonDeclaracionJurada->getCUITBeneficiario())), 11, $char_pad_string, $type_pad_string); // CUIT del retenido
//            /* 3 */ $cadena.= str_pad('0,00', 9, $char_pad_int, $char_pad_int); //Importe excedente            
//            /* 4 */ $cadena.= str_pad($comprobanteRetencion->getFechaComprobanteRetencion()->format('d/m/Y'), 10, $char_pad_string, $type_pad_string); //Fecha emision retención            
//            /* 5 */ $cadena.= str_pad(number_format($comprobanteRetencion->getMonto(), 2, ',', ''), 9, $char_pad_int, $type_pad_int); //Importe de la retención
//            /* 6 */ $cadena.= str_pad('0', 14, $char_pad_int, $type_pad_int); //Número certificado original
//
//            fwrite($f, $cadena);
//            fwrite($f, chr(13) . chr(10));
//        }
//
//        fclose($f);
//
//        $response = new BinaryFileResponse($path);
//        $d = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
//        $response->headers->set('Content-Type', 'text/plain');
//        $response->headers->set('Content-Disposition', $d);
//
//        return $response;
//    }
    
    /**
     * Genera el archivo de exportacion de retenciones sijp
     *
     * @Route("/generar_sijp/{id}", name="generar_sijp")
     * @Method("GET")
     */
    public function generarSijpAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $entity \ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto */
        $entity = $em->getRepository('ADIFContableBundle:DeclaracionJuradaImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DeclaracionJuradaImpuesto.');
        }

        $filename = 'retencion_sijp_' . $entity->getFecha()->format('Ymd') . '.txt';
        $path = $this->get('kernel')->getRootDir() . '/../web/uploads/declaraciones_juradas/sijp/' . $filename;

        $char_pad_string = ' ';
        $char_pad_int = '0';

        $type_pad_string = STR_PAD_RIGHT;
        $type_pad_int = STR_PAD_LEFT;

        $f = fopen($path, "w");

        foreach ($this->getRenglonesDDJJ($entity) as $renglonDeclaracionJurada) {
            /* @var $renglonDeclaracionJurada \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada */

            /* @var $comprobanteRetencion \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto */

            $comprobanteRetencion = $renglonDeclaracionJurada->getComprobanteRetencionImpuesto();
            
            if ($comprobanteRetencion->getOrdenPago()->getEstadoOrdenPago() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {

                /* 1 */ $cadena = str_pad('2004', 4, $char_pad_int, $type_pad_int); //Formulario
//                /* 2 */ $cadena.= str_pad('0', 4, $char_pad_int, $type_pad_int); //Versión
                /* 2 */ $cadena.= '0100'; //Versión
                /* 3 */ $cadena.= str_pad('', 10, $char_pad_string, $type_pad_string); //Código de trazabilidad
                /* 4 */ $cadena.= str_pad(strval(str_replace('-', '', AdifDatos::CUIT)), 11, $char_pad_int, $type_pad_int); //CUIT Agente            
                /* 5 */ $cadena.= str_pad($comprobanteRetencion->getRegimenRetencion()->getTipoImpuesto()->getCodigoSiap(), 3, $char_pad_int, $type_pad_int);
                /* 6 */ $cadena.= str_pad($comprobanteRetencion->getRegimenRetencion()->getCodigoSiap(), 3, $char_pad_int, $type_pad_int); //Código de régimen
                /* 7 */ $cadena.= str_pad(strval(str_replace('-', '', $renglonDeclaracionJurada->getCUITBeneficiario())), 11, $char_pad_string, $type_pad_string); // CUIT del retenido
                /* 8 */ $cadena.= str_pad($comprobanteRetencion->getOrdenPago()->getFechaOrdenPago()->format('d/m/Y'), 10, $char_pad_string, $type_pad_string); //Fecha emision retención
                /* 9 */ $cadena.= str_pad('06', 2, $char_pad_int, $type_pad_int); //Tipo de comprobante
                /* 10 */ $cadena.= str_pad($comprobanteRetencion->getOrdenPago()->getFechaOrdenPago()->format('d/m/Y'), 10, $char_pad_string, $type_pad_string); //Fecha emision retención            
                /* 11 */ $cadena.= str_pad($comprobanteRetencion->getNumeroComprobanteRetencion(), 16, $char_pad_string, $type_pad_string); //Número de comprobante  
                /* 12 */ $cadena.= str_pad(number_format($comprobanteRetencion->getMonto(), 2, ',', ''), 14, $char_pad_int, $type_pad_int); //Importe comprobante
                /* 13 */ $cadena.= str_pad(number_format($comprobanteRetencion->getMonto(), 2, ',', ''), 14, $char_pad_int, $type_pad_int); //Importe retención
                /* 14 */ $cadena.= str_pad('', 25, $char_pad_string, $type_pad_string); //Número certificado original
                /* 15 */ $cadena.= str_pad('', 10, $char_pad_string, $type_pad_string); //Fecha reten certificado original
                /* 16 */ $cadena.= str_pad('', 14, $char_pad_string, $type_pad_string); //Importe certificado original
                /* 17 */ $cadena.= str_pad('', 30, $char_pad_string, $type_pad_string); //Otros datos

                fwrite($f, $cadena);
                fwrite($f, chr(13) . chr(10));
            }
        }

        fclose($f);

        $response = new BinaryFileResponse($path);
        $d = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', $d);

        return $response;
    }

    /**
     * Genera el archivo de exportacion de retenciones arciba
     *
     * @Route("/generar_arciba/{id}", name="generar_arciba")
     * @Method("GET")
     */
    public function generarArcibaAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $entity \ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto */
        $entity = $em->getRepository('ADIFContableBundle:DeclaracionJuradaImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DeclaracionJuradaImpuesto.');
        }

        $filename_general = 'retencion_arciba_general_' . time() . '.txt';
        $filename_nc = 'retencion_arciba_nc_' . time() . '.txt';

        $path_general = $this->get('kernel')->getRootDir() . '/../web/uploads/declaraciones_juradas/arciba/' . $filename_general;
        $path_nc = $this->get('kernel')->getRootDir() . '/../web/uploads/declaraciones_juradas/arciba/' . $filename_nc;

        $char_pad_string = ' ';
        $char_pad_int = '0';

        $type_pad_string = STR_PAD_RIGHT;
        $type_pad_int = STR_PAD_LEFT;

        $f_gral = fopen($path_general, "w");
        $f_nc = fopen($path_nc, "w");

        $cadena_gral = '';
        $cadena_nc = '';
        
        $crear_archivo_gral = false;
        $crear_archivo_nc = false;

        foreach ($this->getRenglonesDDJJ($entity) as $renglonDeclaracionJurada) {
            /* @var $renglonDeclaracionJurada \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada */

            if ($renglonDeclaracionJurada->getTipoRenglonDeclaracionJurada()->getCodigo() == ConstanteTipoRenglonDeclaracionJurada::RENGLON_PERCEPCION) {

                $tipoOperacion = 2;
                $tipoComprobante = '01';
                
                $cadena_gral = '';
                $cadena_nc = '';
                
                /* @var $renglon_percepcion \ADIF\ContableBundle\Entity\RenglonPercepcion */
                $renglon_percepcion = $renglonDeclaracionJurada->getRenglonPercepcion();

                /* @var $comprobante \ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta */
                $comprobante = $renglon_percepcion->getComprobante();

                /* @var $cliente \ADIF\ComprasBundle\Entity\Cliente */
                $cliente = $comprobante->getCliente();
                $clienteProveedor = $cliente->getClienteProveedor();

                $alicuota = $renglonDeclaracionJurada->getRegimenPercepcion()->getAlicuota();

                if ($comprobante->getEsNotaCredito()) {
                    $cadena_nc = $this->getCabeceraLineaArcibaNC($renglonDeclaracionJurada, $tipoOperacion, $tipoComprobante, $clienteProveedor, $comprobante, $renglonDeclaracionJurada->getRegimenPercepcion()->getCodigo(), $comprobante->getComprobanteCancelado());

                    /* 12 */ $cadena_nc .= str_pad(number_format($renglon_percepcion->getMonto(), 2, ',', ''), 16, $char_pad_int, $type_pad_int); // Monto percepcion
                    /* 13 */ $cadena_nc .= str_pad(number_format($alicuota, 2, ',', ''), 5, $char_pad_int, $type_pad_int); // Alícuota
                } else {
                    $cadena_gral = $this->getCabeceraLineaArciba($renglonDeclaracionJurada, $tipoOperacion, $tipoComprobante, $clienteProveedor, $comprobante, $renglonDeclaracionJurada->getRegimenPercepcion()->getCodigo());

                    /* 18 */ $cadena_gral .= str_pad(number_format($comprobante->getImporteTotalNeto(), 2, ',', ''), 16, $char_pad_int, $type_pad_int); // Monto sujeto a retencion/percepcion
                    /* 19 */ $cadena_gral .= str_pad(number_format($alicuota, 2, ',', ''), 5, $char_pad_int, $type_pad_int); // Alícuota
                    /* 20 */ $cadena_gral .= str_pad(number_format($renglon_percepcion->getMonto(), 2, ',', ''), 16, $char_pad_int, $type_pad_int); // Retención
                    /* 21 */ $cadena_gral .= str_pad(number_format($renglon_percepcion->getMonto(), 2, ',', ''), 16, $char_pad_int, $type_pad_int); // Retención
                }
                if ($cadena_gral != '') {
                    $crear_archivo_gral = true;
                    fwrite($f_gral, $cadena_gral);
                    fwrite($f_gral, chr(13) . chr(10));
                }

                if ($cadena_nc != '') {
                    $crear_archivo_nc = true;
                    fwrite($f_nc, $cadena_nc);
                    fwrite($f_nc, chr(13) . chr(10));
                }
            } else {
                $tipoOperacion = 1;
                $tipoComprobante = '03';

                /* @var $comprobanteRetencion \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoCompras */

                $comprobanteRetencion = $renglonDeclaracionJurada->getComprobanteRetencionImpuesto();
                $comprobantes_compra = $this->get('adif.retenciones_service')->getDatosComprobantesAplicanImpuesto($comprobanteRetencion);
                
                foreach ($comprobantes_compra as $comprobante_compra) {
                    $cadena_gral = '';
                    $cadena_nc = '';
                    
                    /* @var $comprobante \ADIF\ContableBundle\Entity\ComprobanteCompra */
                    $comprobante = $comprobante_compra['comprobante'];
                    if($comprobante_compra['tipo'] == 'CONSULTORIA'){
                        $beneficiario = $comprobante->getConsultor();
                    } else {
                        /* @var $proveedor \ADIF\ComprasBundle\Entity\Proveedor */                    
                        $proveedor = $comprobante->getProveedor();
                        $beneficiario = $proveedor->getClienteProveedor();
                    }                    

                    if ($comprobante->getEsNotaCredito()) {
                        $cadena_nc = $this->getCabeceraLineaArcibaNC($renglonDeclaracionJurada, $tipoOperacion, $tipoComprobante, $beneficiario, $comprobante, $comprobante_compra['codigo_regimen']);

                        /* 12 */ $cadena_nc .= str_pad(number_format(abs($comprobante_compra['retencion']), 2, ',', ''), 16, $char_pad_int, $type_pad_int); // Monto percepción
                        /* 13 */ $cadena_nc .= str_pad($comprobante_compra['alicuota'], 5, $char_pad_int, $type_pad_int); // Alícuota
                    } else {
                        $cadena_gral = $this->getCabeceraLineaArciba($renglonDeclaracionJurada, $tipoOperacion, $tipoComprobante, $beneficiario, $comprobante, $comprobante_compra['codigo_regimen']);

                        /* 18 */ $cadena_gral .= str_pad($comprobante_compra['monto_sujeto_retencion'], 16, $char_pad_int, $type_pad_int); // Monto sujeto a retencion/percepcion
                        /* 19 */ $cadena_gral .= str_pad($comprobante_compra['alicuota'], 5, $char_pad_int, $type_pad_int); // Alícuota
                        /* 20 */ $cadena_gral .= str_pad(number_format($comprobante_compra['retencion'], 2, ',', ''), 16, $char_pad_int, $type_pad_int); // Retención
                        /* 21 */ $cadena_gral .= str_pad(number_format($comprobante_compra['retencion'], 2, ',', ''), 16, $char_pad_int, $type_pad_int); // Retención  
                    }
                    if ($cadena_gral != '') {
                        $crear_archivo_gral = true;
                        fwrite($f_gral, $cadena_gral);
                        fwrite($f_gral, chr(13) . chr(10));
                    }

                    if ($cadena_nc != '') {
                        $crear_archivo_nc = true;
                        fwrite($f_nc, $cadena_nc);
                        fwrite($f_nc, chr(13) . chr(10));
                    }
                }
            }
        }        

        fclose($f_gral);
        fclose($f_nc);

        if (!$crear_archivo_gral) {
            unlink($path_general);
        }

        if (!$crear_archivo_nc) {
            unlink($path_nc);
        }

        // Los meto en un zip
        $zippath = $this->get('kernel')->getRootDir() . '/../web/uploads/declaraciones_juradas/arciba/retencion_arciba_' . date('Ymd') . '.zip';

        if (file_exists($zippath)) {
            unlink($zippath);
        }

        $zip = new ZipArchive;
        $zip->open($zippath, ZipArchive::CREATE);

        if ($crear_archivo_gral) {
            $zip->addFile($path_general, $filename_general);
        }
        if ($crear_archivo_nc) {
            $zip->addFile($path_nc, $filename_nc);
        }

        $zip->close();

        if (!$crear_archivo_gral) {
            unlink($path_general);
        }
        if (!$crear_archivo_nc) {
            unlink($path_nc);
        }


        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($zippath));
        $response->headers->set('Content-Disposition', 'filename="' . basename($zippath) . '"');
        $response->headers->set('Content-length', filesize($zippath));

        $response->setContent(file_get_contents($zippath));

        return $response;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto $declaracionJurada
     * @return type
     */
    private function getRenglonesDDJJ(\ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto $declaracionJurada) {

        $renglones_declaracion = array();

        foreach ($declaracionJurada->getPagosACuenta() as $pago_a_cuenta) {
            $renglones_declaracion = array_merge($renglones_declaracion, $pago_a_cuenta->getRenglonesDeclaracionJurada()->toArray());
        }

        $renglones_declaracion = array_merge($renglones_declaracion, $declaracionJurada->getRenglonesDeclaracionJurada()->toArray());

        return $renglones_declaracion;
    }

    /**
     * 
     * @param type $clienteProveedor
     * @return int
     */
    private function getCodigoSituacionIIBB($clienteProveedor) {
        if ($clienteProveedor->getCondicionIngresosBrutos() == ConstanteTipoResponsable::INSCRIPTO || $clienteProveedor->getCondicionIngresosBrutos() == ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO) {
            return 1;
        } else {
            if ($clienteProveedor->getCondicionIngresosBrutos() == ConstanteTipoResponsable::CONVENIO_MULTILATERAL) {
                return 2;
            } else {
                if ($clienteProveedor->getCondicionIngresosBrutos() == ConstanteTipoResponsable::SUJETO_NO_CATEGORIZADO || $clienteProveedor->getCondicionIngresosBrutos() == ConstanteTipoResponsable::CONSUMIDOR_FINAL) {
                    return 4;
                }
            }
        }
        return 0;
    }
    
    /**
     * 
     * @param type $comprobante
     * @return int
     */
    private function getTipoComprobante($comprobante) {
        if ($comprobante->getTipoComprobante()->getId() == \ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra::FACTURA || $comprobante->getTipoComprobante()->getId() == \ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra::NOTA_DEBITO) {
            return '0'.$comprobante->getTipoComprobante()->getId();
        } 
        
        return '03';
    }
    
    /**
     * 
     * @param type $tipoOperacion
     * @param type $codigo_regimen
     * @return int
     */
    private function getCodigoNorma($tipoOperacion, $codigo_regimen) {
        $codigo_norma = 0;
        if($tipoOperacion == 1){
            // Retención
            $codigo_norma = 8;
            if($codigo_regimen == ConstanteRegimenRetencion::CODIGO_MAGNITUDES_SUPERADAS){
                $codigo_norma = 18;
            } else {
                if($codigo_regimen == ConstanteRegimenRetencion::CODIGO_RIESGO_FISCAL){
                    $codigo_norma = 16;
                }
            }
        } else {
            switch ($codigo_regimen){
                case ConstanteCodigoRegimenPercepcion::CODIGO_GENERAL: $codigo_norma = 14; break;
                case ConstanteCodigoRegimenPercepcion::CODIGO_ALQUILERES: $codigo_norma = 17; break;
                case ConstanteCodigoRegimenPercepcion::CODIGO_RIESGO_FISCAL: $codigo_norma = 16; break;
                case ConstanteCodigoRegimenPercepcion::CODIGO_MAGNITUDES_SUPERADAS: $codigo_norma = 18; break;
            }
        }
        return $codigo_norma;
    }

    /**
     * 
     * @param type $clienteProveedor
     * @return int
     */
    private function getCodigoSituacionIVA($clienteProveedor) {
        if ($clienteProveedor->getCondicionIVA() == ConstanteTipoResponsable::INSCRIPTO) {
            return 1;
        } else {
            if ($clienteProveedor->getCondicionIVA() == ConstanteTipoResponsable::SUJETO_NO_CATEGORIZADO || $clienteProveedor->getCondicionIVA() == ConstanteTipoResponsable::CONSUMIDOR_FINAL) {
                return 2;
            } else {
                if ($clienteProveedor->getCondicionIVA() == ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO) {
                    return 4;
                }
            }
        }
        return 0;
    }

    /**
     * 
     * @param type $renglonDeclaracionJurada
     * @param type $tipoOperacion
     * @param type $tipoComprobante
     * @param type $beneficiario
     * @param type $comprobante
     * @param type $codigo_regimen
     * @return type
     */
    private function getCabeceraLineaArciba($renglonDeclaracionJurada, $tipoOperacion, $tipoComprobante, $beneficiario, $comprobante, $codigo_regimen) {
        $char_pad_string = ' ';
        $char_pad_int = '0';

        $type_pad_string = STR_PAD_RIGHT;
        $type_pad_int = STR_PAD_LEFT;

        /* 1 */ $cadena = $tipoOperacion; // Tipo de operación
        /* 2 */ $cadena.= str_pad($this->getCodigoNorma($tipoOperacion, $codigo_regimen), 3, $char_pad_int, $type_pad_int); // Código de norma
        /* 3 */ $cadena.= str_pad($renglonDeclaracionJurada->getFecha()->format('d/m/Y'), 10, $char_pad_string, $type_pad_string); //Fecha emision retención
        /* 4 */ $cadena.= $this->getTipoComprobante($comprobante); // Tipo de comprobante
        /* 5 */ $cadena.= $comprobante->getLetraComprobante()->getLetra() == ConstanteLetraComprobante::A_CON_LEYENDA ? 'A' : $comprobante->getLetraComprobante()->getLetra(); // Letra del comprobante
        /* 6 */ $cadena.= str_pad($comprobante->getPuntoVenta() . $comprobante->getNumero(), 16, $char_pad_int, $type_pad_int); //Número comprobante
        /* 7 */ $cadena.= str_pad($comprobante->getFechaComprobante()->format('d/m/Y'), 10, $char_pad_string, $type_pad_string); //Fecha comprobante
        /* 8 */ $cadena.= str_pad(number_format($comprobante->getTotal() - ($tipoOperacion == 2 ? $comprobante->getImporteTotalPercepcion() : 0), 2, ',', ''), 16, $char_pad_int, $type_pad_int); // Monto del comprobante
        /* 9 */ $cadena.= str_pad(($tipoOperacion == 1 ? ($comprobante->getPuntoVenta() . $comprobante->getNumero()) : ''), 16, $char_pad_string, $type_pad_string); //Número comprobante
        /* 10 */ $cadena.= 3; // Tipo de documento
        /* 11 */ $cadena.= str_pad(strval(str_replace('-', '', $beneficiario->getCUIT())), 11, $char_pad_string, $type_pad_string); // CUIT del retenido
        /* 12 */ $cadena.= $this->getCodigoSituacionIIBB($beneficiario); // Situación IIBB
        /* 13 */ $cadena.= str_pad(strval(str_replace('-', '', $beneficiario->getNumeroIngresosBrutos())), 11, $char_pad_int, $type_pad_int); // Nro inscripción IIBB
        /* 14 */ $cadena.= $this->getCodigoSituacionIVA($beneficiario); // Situación IVA
        /* 15 */ $cadena.= str_pad(substr(AdifApi::stringCleaner($beneficiario->getRazonSocial()), 0, 30), 30, $char_pad_string, $type_pad_string); // Razón social del retenido
        /* 16 */ $cadena.= str_pad(number_format(($comprobante->getImporteTotalImpuesto() + ($tipoOperacion == 1 ? $comprobante->getImporteTotalPercepcion() : 0)), 2, ',', ''), 16, $char_pad_int, $type_pad_int); // Importe otros conceptos
        /* 17 */ $cadena.= str_pad(number_format($comprobante->getImporteTotalIVA(), 2, ',', ''), 16, $char_pad_int, $type_pad_int); // Importe IVA

        return $cadena;
    }

    /**
     * 
     * @param type $renglonDeclaracionJurada
     * @param type $tipoOperacion
     * @param type $tipoComprobante
     * @param type $beneficiario
     * @param type $comprobante
     * @param type $codigo_regimen
     * @param type $comprobanteCancelado
     * @return type
     */
    private function getCabeceraLineaArcibaNC($renglonDeclaracionJurada, $tipoOperacion, $tipoComprobante, $beneficiario, $comprobante, $codigo_regimen, $comprobanteCancelado = null) {
        $char_pad_string = ' ';
        $char_pad_int = '0';

        $type_pad_string = STR_PAD_RIGHT;
        $type_pad_int = STR_PAD_LEFT;
        
        $comprobanteOrigen = $comprobanteCancelado ? $comprobanteCancelado : $comprobante;

        /* 1 */ $cadena = $tipoOperacion; // Tipo de operación
        /* 2 */ $cadena.= str_pad($comprobante->getNumero(), 12, $char_pad_int, $type_pad_int); //Número comprobante
        /* 3 */ $cadena.= str_pad($comprobante->getFechaComprobante()->format('d/m/Y'), 10, $char_pad_string, $type_pad_string); //Fecha comprobante
        /* 4 */ $cadena.= str_pad(number_format(abs($comprobante->getImporteTotalNeto()), 2, ',', ''), 16, $char_pad_int, $type_pad_int); // Monto del comprobante
        /* 5 */ $cadena.= str_pad($comprobante->getPuntoVenta() . $comprobante->getNumero(), 16, $char_pad_int, $type_pad_int); //Número comprobante
        /* 6 */ $cadena.= $this->getTipoComprobante($comprobanteOrigen);; // Tipo de comprobante
        /* 7 */ $cadena.= $comprobanteOrigen->getLetraComprobante()->getLetra() == ConstanteLetraComprobante::A_CON_LEYENDA ? 'A' : $comprobanteOrigen->getLetraComprobante()->getLetra(); // Letra del comprobante
        /* 8 */ $cadena.= str_pad($comprobanteOrigen->getPuntoVenta() . $comprobanteOrigen->getNumero(), 16, $char_pad_int, $type_pad_int); //Número comprobante
        /* 9 */ $cadena.= str_pad(strval(str_replace('-', '', $beneficiario->getCUIT())), 11, $char_pad_string, $type_pad_string); // CUIT del retenido
        /* 10 */ $cadena.= str_pad($this->getCodigoNorma($tipoOperacion, $codigo_regimen), 3, $char_pad_int, $type_pad_int); // Código de norma
        /* 11 */ $cadena.= str_pad($renglonDeclaracionJurada->getFecha()->format('d/m/Y'), 10, $char_pad_string, $type_pad_string); //Fecha emisión retención
        return $cadena;
    }

    /**
     * Lists all DeclaracionJurada entities.
     *
     * @Route("/historico", name="declaracion_jurada_historico")
     * @Method("GET")
     * @Template("ADIFContableBundle:DeclaracionJurada:index.historico.html.twig")
     */
    public function historicoAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Hist&oacute;rico'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Hist&oacute;rico de declaraciones juradas',
            'page_info' => 'Hist&oacute;rico de declaraciones juradas'
        );
    }

    /**
     * Lists all DeclaracionJurada entities.
     *
     * @Route("/historico/index_table/", name="index_table_historico")
     * @Method("GET|POST")
     * @Template()
     */
    public function indexTableHistoricoAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $declaracionesJuradas = array();

        $tipoDDJJ = $request->query->get('tipoDDJJ');

        switch ($tipoDDJJ) {
            case ConstanteTipoDeclaracionJurada::SICORE:

                $declaracionesJuradas = $em
                        ->getRepository('ADIFContableBundle:DeclaracionJuradaImpuestoSICORE')
                        ->findAll();

                break;

            case ConstanteTipoDeclaracionJurada::SIJP:

                $declaracionesJuradas = $em
                        ->getRepository('ADIFContableBundle:DeclaracionJuradaImpuestoSIJP')
                        ->findAll();

                break;

            case ConstanteTipoDeclaracionJurada::IIBB:

                $declaracionesJuradas = $em
                        ->getRepository('ADIFContableBundle:DeclaracionJuradaImpuestoIIBB')
                        ->findAll();

                break;

            case ConstanteTipoDeclaracionJurada::SICOSS:

                $declaracionesJuradas = $em
                        ->getRepository('ADIFContableBundle:DeclaracionJuradaImpuestoSICOSS')
                        ->findAll();

                break;
            default:
                break;
        }

        return $this->render('ADIFContableBundle:DeclaracionJurada:index_table_declaraciones_juradas.html.twig', array('entities' => $declaracionesJuradas));
    }

    /**
     * Devuelve el template del detalle de una DDJJ
     *
     * @Route("/form_detalle/", name="declaracion_jurada_form_detalle")
     * @Method("POST")   
     * @Template("ADIFContableBundle:DeclaracionJurada:index.show.tablas.html.twig")
     */
    public function getFormDetalleDeclaracionJuradaAction(Request $request) {

        $emContable = $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idDeclaracionJurada = $request->request->get('id');

        /* @var $declaracionJurada \ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto */
        $declaracionJurada = $emContable->getRepository('ADIFContableBundle:DeclaracionJuradaImpuesto')
                ->find($idDeclaracionJurada);

        return array(
            'readonly' => true,
            'impuesto' => $declaracionJurada->getImpuestoLabel(),
            'tipo_ddjj' => $declaracionJurada->getTipoDeclaracionJurada(),
            'renglonesDeclaracionJurada' => $this->getRenglonesDDJJ($declaracionJurada),
            'pagosACuenta' => $declaracionJurada->getPagosACuenta()
        );
    }

    private function cargarCampos931(LiquidacionEmpleado $liquidacionEmpleado) {
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());
        $liquidacionRepo = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Liquidacion');
        $idEmpleado = $liquidacionEmpleado->getEmpleado()->getId();
        $fechaCierreNovedades = $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades();
        return array(
            28 => $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, '101.1', $fechaCierreNovedades),
            37 => $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 51, $fechaCierreNovedades) + $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 52, $fechaCierreNovedades)
        );
    }

    private function cargarLicencias($licencias, $fechaInicio, $fechaFin, $char_pad_int, $type_pad_int) {
        $result = '';
        if ($licencias->isEmpty()) {
            /* 30 */ $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);  //Situación 1
            /* 31 */ $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);  //Dia 1
            /* 32 */ $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);  //Situación 2
            /* 33 */ $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);  //Dia 2
            /* 34 */ $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);  //Situación 3
            /* 35 */ $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);  //Dia 3    
        } else {
            $licencias = $licencias->toArray();
            $licencias = array_values($licencias);

            $situacion_2 = false;
            $situacion_3 = false;
            // Licencia 1
            $empleadoLicencia = $licencias[0];

            if ($empleadoLicencia->getFechaDesde() <= $fechaInicio) {
                /* 30 */ $result .= str_pad($empleadoLicencia->getTipoLicencia()->getCodigo(), 2, $char_pad_int, $type_pad_int);
                /* 31 */ $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
            } else {
                /* 30 */ $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
                /* 31 */ $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
                /* 32 */ $result .= str_pad($empleadoLicencia->getTipoLicencia()->getCodigo(), 2, $char_pad_int, $type_pad_int);
                /* 33 */ $result .= str_pad($empleadoLicencia->getFechaDesde()->format('d'), 2, $char_pad_int, $type_pad_int);
                $situacion_2 = true;
            }
            $ultima_fecha_hasta = clone($empleadoLicencia->getFechaHasta());
            $ultima_fecha_hasta->add(new DateInterval('P1D'));

            if (isset($licencias[1])) {
                // Licencia 2
                $empleadoLicencia = $licencias[1];
                if ($empleadoLicencia->getFechaDesde() != $ultima_fecha_hasta) {
                    // Hay un bache entre las dos licencias, lleno con activo
                    $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
                    $result .= str_pad($ultima_fecha_hasta->format('d'), 2, $char_pad_int, $type_pad_int);
                    if (!$situacion_2) {
                        // Si no estaba cargada la 2, tengo lugar, sino ya completé las 3
                        $result .= str_pad($empleadoLicencia->getTipoLicencia()->getCodigo(), 2, $char_pad_int, $type_pad_int);
                        $result .= str_pad($empleadoLicencia->getFechaDesde()->format('d'), 2, $char_pad_int, $type_pad_int);
                    }
                    $situacion_3 = true;
                } else {
                    $result .= str_pad($empleadoLicencia->getTipoLicencia()->getCodigo(), 2, $char_pad_int, $type_pad_int);
                    $result .= str_pad($empleadoLicencia->getFechaDesde()->format('d'), 2, $char_pad_int, $type_pad_int);
                    if ($situacion_2) {
                        $situacion_3 = true;
                    }
                }
                $ultima_fecha_hasta = clone($empleadoLicencia->getFechaHasta());
                $ultima_fecha_hasta->add(new DateInterval('P1D'));
            } else {
                if ($licencias[0]->getFechaHasta() < $fechaFin) {
                    $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
                    $result .= str_pad($ultima_fecha_hasta->format('d'), 2, $char_pad_int, $type_pad_int);
                } else {
                    $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);
                    $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);
                }
                if (!$situacion_2) {
                    $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);
                    $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);
                }
                $situacion_3 = true;
            }

            if (isset($licencias[2])) {
                if (!$situacion_3) {
                    // Licencia 3
                    $empleadoLicencia = $licencias[2];
                    if ($empleadoLicencia->getFechaDesde() != $ultima_fecha_hasta) {
                        // Hay un bache entre las dos licencias, lleno con activo
                        $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
                        $result .= str_pad($ultima_fecha_hasta->format('d'), 2, $char_pad_int, $type_pad_int);
                    } else {
                        $result .= str_pad($empleadoLicencia->getTipoLicencia()->getCodigo(), 2, $char_pad_int, $type_pad_int);
                        $result .= str_pad($empleadoLicencia->getFechaDesde()->format('d'), 2, $char_pad_int, $type_pad_int);
                    }
                }
            } else {
                if (!$situacion_3) {
                    if (isset($licencias[1]) && $licencias[1]->getFechaHasta() < $fechaFin) {
                        $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
                        $result .= str_pad($ultima_fecha_hasta->format('d'), 2, $char_pad_int, $type_pad_int);
                    } else {
                        $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);
                        $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);
                    }
                }
            }
        }
        return $result;
    }

    private function getCodigoSituacion($licencias) {
        $situacion1 = substr($licencias, 0, 2);
        $situacion2 = substr($licencias, 4, 2);
        $situacion3 = substr($licencias, 8, 2);
        if ($situacion3 != '00') {
            return $situacion3;
        }
        if ($situacion2 != '00') {
            return $situacion2;
        }
        return $situacion1;
    }

    private function mb_str_pad($input, $pad_length, $pad_string, $pad_type) {
        $diff = strlen($input) - mb_strlen($input, 'UTF8');
        return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
    }

    /**
     * Genera el archivo de exportacion de retenciones sicore
     *
     * @Route("/generar_sicore/{id}", name="generar_sicore")
     * @Method("GET")
     */
    public function generarSicoreAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        /* @var $entity \ADIF\ContableBundle\Entity\DeclaracionJuradaImpuesto */
        $entity = $em->getRepository('ADIFContableBundle:DeclaracionJuradaImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DeclaracionJuradaImpuesto.');
        }

        $filename = 'retencion_sicore_' . $entity->getFecha()->format('Ymd') . '.txt';
        $path = $this->get('kernel')->getRootDir() . '/../web/uploads/declaraciones_juradas/sicore/' . $filename;

        $char_pad_string = ' ';
        $char_pad_int = '0';

        $type_pad_string = STR_PAD_RIGHT;
        $type_pad_int = STR_PAD_LEFT;

        $f = fopen($path, "w");

        //\Doctrine\Common\Util\Debug::dump($this->getRenglonesDDJJ($entity));die;

        foreach ($this->getRenglonesDDJJ($entity) as $renglonDeclaracionJurada) {
            /* @var $renglonDeclaracionJurada \ADIF\ContableBundle\Entity\RenglonDeclaracionJurada */

            if ($renglonDeclaracionJurada->getTipoRenglonDeclaracionJurada()->getCodigo() == ConstanteTipoRenglonDeclaracionJurada::LIQUIDACION) {
                // Si es un renglon de liquidacion, saco los datos de la liquidacion                

                $liquidacion = $renglonDeclaracionJurada->getLiquidacion();

                $fecha_recibo = $liquidacion->getFechaPago()->format('d/m/Y');
                foreach ($liquidacion->getLiquidacionEmpleados() as $liquidacionEmpleado) {
                    /* @var $liquidacionEmpleado LiquidacionEmpleado */
                    /* @var $empleado Empleado */
                    /* @var $persona Persona */

                    $empleado = $liquidacionEmpleado->getEmpleado();
                    $persona = $empleado->getPersona();

                    //Recibo
                    $nro_recibo = $liquidacionEmpleado->getId();

                    $imp_retencion = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getGananciaByEmpleado($liquidacionEmpleado->getEmpleado()->getId(), $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades());

                    if ($imp_retencion != 0) {
                        $imp_retencion = str_replace('.', ',', $imp_retencion);

                        /* 1 */ $cadena = str_pad('07', 2, $char_pad_int, $type_pad_int); //Código de comprobante
                        /* 2 */ $cadena.= str_pad($fecha_recibo, 10, $char_pad_string, $type_pad_string); //Fecha emision comprobante
                        /* 3 */ $cadena.= str_pad($nro_recibo, 16, $char_pad_int, $type_pad_int); //Número de comprobante    
                        /* 4 */ $cadena.= str_pad('0,00', 16, $char_pad_int, $type_pad_int); //Importe del comprobante
                        /* 5 */ $cadena.= str_pad('217', 3, $char_pad_int, $type_pad_int); //Código de Impuesto
                        /* 6 */ $cadena.= str_pad('160', 3, $char_pad_int, $type_pad_int); //Código de regimen
                        /* 7 */ $cadena.= str_pad('1', 1, $char_pad_int, $type_pad_int); //Código de regimen
                        /* 8 */ $cadena.= str_pad('0,00', 14, $char_pad_int, $type_pad_int); //Base de cálculo
                        /* 9 */ $cadena.= str_pad($fecha_recibo, 10, $char_pad_string, $type_pad_string); //Fecha emision retención
                        /* 10 */ $cadena.= str_pad('01', 2, $char_pad_int, $type_pad_int); //Código de condición
                        /* 11 */ $cadena.= str_pad('0', 1, $char_pad_int, $type_pad_int); //Retención practicada a sujetos suspendidos
                        /* 12 */ $cadena.= str_pad($imp_retencion, 14, $char_pad_int, $type_pad_int); //Importe de la retención
                        /* 13 */ $cadena.= str_pad('0,00', 6, $char_pad_int, $char_pad_int); //Porcentaje de exclusion
                        /* 14 */ $cadena.= str_pad('', 10, $char_pad_string, $type_pad_string); //Fecha emision boletín
                        /* 15 */ $cadena.= str_pad('80', 2, $char_pad_int, $type_pad_int); //Tipo de documento retenido
                        /* 16 */ $cadena.= str_pad(strval(str_replace('-', '', $persona->getCuil())), 20, $char_pad_string, $type_pad_string); //Número de documento de retenido                                    
                        /* 17 */ $cadena.= str_pad('0', 14, $char_pad_int, $type_pad_int); //Número certificado original
                        /* 18 */ $cadena.= str_pad('', 30, $char_pad_string, $type_pad_string); //Denominación del ordenante
                        /* 19 */ $cadena.= str_pad('0', 1, $char_pad_int, $type_pad_int); //Acrecentamiento
                        /* 20 */ $cadena.= str_pad('0', 11, $char_pad_int, $type_pad_int); //Cuit del pais del retenido
                        /* 21 */ $cadena.= str_pad('0', 11, $char_pad_int, $type_pad_int); //Cuit del ordenante
                        fwrite($f, $cadena);
                        fwrite($f, chr(13) . chr(10));
                    }
                }
            } else {
                // Si no es un renglon de liquidacion, es de un comprobante de retencion

                /* @var $comprobanteRetencion \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto */

                $comprobanteRetencion = $renglonDeclaracionJurada->getComprobanteRetencionImpuesto();

                if ($comprobanteRetencion->getOrdenPago()->getEstadoOrdenPago() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {
                    /* 1 */ $cadena = str_pad('06', 2, $char_pad_int, $type_pad_int); //Código de comprobante
                    /* 2 */ $cadena.= str_pad($comprobanteRetencion->getOrdenPago()->getFechaOrdenPago()->format('d/m/Y'), 10, $char_pad_string, $type_pad_string); //Fecha emision comprobante

                    /* 3 */ $cadena.= str_pad($comprobanteRetencion->getNumeroComprobanteRetencion(), 16, $char_pad_int, $type_pad_int); //Número de comprobante    
                    /* 4 */ $cadena.= str_pad(number_format($comprobanteRetencion->getBaseImponible(), 2, ',', ''), 16, $char_pad_int, $type_pad_int); //Importe del comprobante
                    /* 5 */ //$cadena.= str_pad('217', 3, $char_pad_int, $type_pad_int); //Código de Impuesto
                    $cadena.= str_pad($comprobanteRetencion->getRegimenRetencion()->getTipoImpuesto()->getCodigoSiap(), 3, $char_pad_int, $type_pad_int);
                    /* 6 */ $cadena.= str_pad($comprobanteRetencion->getRegimenRetencion()->getCodigoSiap(), 3, $char_pad_int, $type_pad_int); //Código de régimen
                    /* 7 */ $cadena.= str_pad('1', 1, $char_pad_int, $type_pad_int); //Código de operacion
                    /* 8 */ $cadena.= str_pad(number_format($comprobanteRetencion->getBaseImponible(), 2, ',', ''), 14, $char_pad_int, $type_pad_int); //Base de cálculo
                    /* 9 */ $cadena.= str_pad($comprobanteRetencion->getOrdenPago()->getFechaOrdenPago()->format('d/m/Y'), 10, $char_pad_string, $type_pad_string); //Fecha emision retención
                    /* 10 */ $cadena.= str_pad('01', 2, $char_pad_int, $type_pad_int); //Código de condición
                    /* 11 */ $cadena.= str_pad('0', 1, $char_pad_int, $type_pad_int); //Retención practicada a sujetos suspendidos
                    /* 12 */ $cadena.= str_pad(number_format($comprobanteRetencion->getMonto(), 2, ',', ''), 14, $char_pad_int, $type_pad_int); //Importe de la retención
                    /* 13 */ $cadena.= str_pad('0,00', 6, $char_pad_int, $char_pad_int); //Porcentaje de exclusion
                    /* 14 */ $cadena.= str_pad('', 10, $char_pad_string, $type_pad_string); //Fecha emision boletín
                    /* 15 */ $cadena.= str_pad('80', 2, $char_pad_int, $type_pad_int); //Tipo de documento retenido
                    /* 16 */ $cadena.= str_pad(strval(str_replace('-', '', $renglonDeclaracionJurada->getCUITBeneficiario())), 20, $char_pad_string, $type_pad_string); //Número de documento de retenido

                    /* 17 */ $cadena.= str_pad('0', 14, $char_pad_int, $type_pad_int); //Número certificado original
                    /* 18 */ $cadena.= str_pad('', 30, $char_pad_string, $type_pad_string); //Denominación del ordenante
                    /* 19 */ $cadena.= str_pad('0', 1, $char_pad_int, $type_pad_int); //Acrecentamiento
                    /* 20 */ $cadena.= str_pad('0', 11, $char_pad_int, $type_pad_int); //Cuit del pais del retenido
                    /* 21 */ $cadena.= str_pad('0', 11, $char_pad_int, $type_pad_int); //Cuit del ordenante
                    fwrite($f, $cadena);
                    fwrite($f, chr(13) . chr(10));
                }
            }
        }

        fclose($f);

        $response = new BinaryFileResponse($path);
        $d = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', $d);

        return $response;
    }

}
