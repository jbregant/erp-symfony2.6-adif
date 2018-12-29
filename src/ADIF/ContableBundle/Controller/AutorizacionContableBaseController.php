<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use mPDF;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use ADIF\BaseBundle\Controller\IContainerAnulable;
use ADIF\ContableBundle\Entity\OrdenPagoLog;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use ADIF\BaseBundle\Session\EmpresaSession;

/**
 * AutorizacionContable controller.
 * 
 * @Route("/autorizacioncontable")
 * 
 */
class AutorizacionContableBaseController extends BaseController implements IContainerAnulable {

    private $base_breadcrumbs;

    public function setContainer(ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Listo todas las autorizaciones contables, es decir, 
     * las ordenes de pago que tengan numeroOP en nulo.
     *
     * @Route("/", name="autorizacioncontable")
     * @Method("GET")
     * @Template("ADIFContableBundle:AutorizacionContable:index.html.twig")
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Autorizaciones contables'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Autorizaciones contables',
            'page_info' => 'Lista de autorizaciones contables'
        );
    }

    /**
     * Tabla para AutorizacionContable.
     *
     * @Route("/index_table/", name="autorizacioncontable_index_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableAction(Request $request) {

        $autorizacionesContables = array();

        if ($request->query->get('fechaInicio') && $request->query->get('fechaFin')) {

            $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaInicio') . ' 00:00:00');
            $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaFin') . ' 23:59:59');
            
            $tieneVisadoRRHH = $this->container->get('security.context')->isGranted('ROLE_VISAR_AUTORIZACION_CONTABLE_RRHH');
            $tieneVisadoEgresoValor = $this->container->get('security.context')->isGranted('ROLE_VISAR_AUTORIZACION_CONTABLE_EGRESO_VALOR');
            $verTodo = $this->container->get('security.context')->isGranted('ROLE_VER_TODO_AUTORIZACION_CONTABLE');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $rsm = new ResultSetMapping();

            $rsm->addScalarResult('id', 'id');
            $rsm->addScalarResult('fechaAutorizacionContable', 'fechaAutorizacionContable');
            $rsm->addScalarResult('numeroAutorizacionContable', 'numeroAutorizacionContable');
            $rsm->addScalarResult('proveedor', 'proveedor');
            $rsm->addScalarResult('concepto', 'concepto');
            $rsm->addScalarResult('totalBruto', 'totalBruto');
            $rsm->addScalarResult('montoRetenciones', 'montoRetenciones');
            $rsm->addScalarResult('montoNeto', 'montoNeto');
            $rsm->addScalarResult('estadoOrdenPago', 'estadoOrdenPago');
            $rsm->addScalarResult('aliasTipoImportanciaEstadoOrdenPago', 'aliasTipoImportanciaEstadoOrdenPago');
            $rsm->addScalarResult('path', 'path');
            $rsm->addScalarResult('pathAC', 'pathAC');
            $rsm->addScalarResult('usuarioCreacion', 'usuarioCreacion');
            $rsm->addScalarResult('requiereVisado', 'requiereVisado');
			// mostrarCeroMontoNeto es un flag solo para pagos parciales
			// si la diferencia entre el importe del pp y la retenciones es menor a cero
			$rsm->addScalarResult('mostrarCeroMontoNeto', 'mostrarCeroMontoNeto');

            $query =  '
                    SELECT
                    id,
                    fechaAutorizacionContable,
                    numeroAutorizacionContable,
                    proveedor,
                    concepto,
                    totalBruto,
                    montoRetenciones,
                    IF (mostrarCeroMontoNeto IS FALSE, IF(montoNeto <= 0.01, 0, montoNeto) , 0) AS montoNeto,
                    estadoOrdenPago,
                    aliasTipoImportanciaEstadoOrdenPago,
                    path,
                    pathAC,
                    usuarioCreacion,
                    requiereVisado,
					mostrarCeroMontoNeto
                FROM
                    vistaordenpago
                WHERE fechaAutorizacionContable BETWEEN ? AND ?
                    AND NOT (numeroOrdenPago IS NOT NULL AND estadoOrdenPago = ?)
            ';
            
            if (!$verTodo) {
                
                if ($tieneVisadoRRHH) {
                    $query .= ' AND path IN (\'ordenpagosueldo\', \'ordenpagoanticiposueldo\', \'ordenpagoconsultoria\' )';
                    $query .= ' AND concepto LIKE \'%sueldo%\' ';
                } else {
                    $query .= ' AND path NOT IN (\'ordenpagosueldo\', \'ordenpagoanticiposueldo\', \'ordenpagoconsultoria\' )';
                    $query .= ' AND concepto NOT LIKE \'%sueldo%\' ';
                }


                if (!$tieneVisadoRRHH && $tieneVisadoEgresoValor) {
                    $query .= ' AND path = \'ordenpagoegresovalor\' ';
                } else {
                    $query .= ' AND path != \'ordenpagoegresovalor\' ';
                }
            }
            
            $native_query = $em->createNativeQuery($query, $rsm);

            $native_query->setParameter(1, $fechaInicio, Type::DATETIME);
            $native_query->setParameter(2, $fechaFin, Type::DATETIME);
            $native_query->setParameter(3, ConstanteEstadoOrdenPago::ESTADO_ANULADA);

            $autorizacionesContables = $native_query->getResult();
        }

        return $this->render('ADIFContableBundle:AutorizacionContable:index_table.html.twig', array(
                    'autorizacionesContables' => $autorizacionesContables
                        )
        );
    }

    /**
     * Creates a new OrdenPago entity.
     *
     * @Route("/insertar", name="autorizacioncontable_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:AutorizacionContable:new.html.twig")
     */
    public function createAction(Request $request) {

        $ordenPago = $this->getOP();

        $ids = $request->request->get('ids');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        foreach ($ids as $idComprobante) {

            $comprobante = $em->getRepository($this->getComprobantesClassName())
                    ->find($idComprobante);

            $ordenPago->addComprobante($comprobante);

            $this->setBeneficiarioCustom($ordenPago, $comprobante);

            $comprobante->setOrdenPago($ordenPago);

            $comprobante->setEstadoComprobante(
                    $em->getRepository('ADIFContableBundle:EstadoComprobante')
                            ->find(EstadoComprobante::__ESTADO_CANCELADO)
            );
        }

        $ordenPagoService = $this->get('adif.orden_pago_service');

        $ordenPagoService->initAutorizacionContable($ordenPago, $this->getConceptoCreacion($ordenPago));

        $error = null;

        try {
            $error = $this->generarRetenciones($ordenPago);
        } //
        catch (Exception $e) {

            $this->get('session')->getFlashBag()
                    ->add('error', "Hubo un error al calcular las retenciones");
        }

        if ($this->mostrarErrorExencion($error, $ordenPago->getBeneficiario()->getControllerPath(), $ordenPago->getBeneficiario()->getId())) {
            return $this->redirect($this->generateUrl($this->getPathComprobantes()));
        }

        $result = $this->newActionCustom($em, $ordenPago, $request);

        if ($result != 0) {
            $this->get('session')->getFlashBag()
                    ->add('error', "No se puede generar la autorización contable. " . $result['error']);
            return $this->redirect($this->generateUrl($this->getPathComprobantes()));
        }

        try {
            $em->persist($ordenPago);
            $em->flush();
        } catch (UniqueConstraintViolationException $e) {
             $this->get('session')->getFlashBag()
                    ->add('error', "No se puede generar la autorización contable. El número de autorización contable " . $ordenPago->getNumeroAutorizacionContable() . ' ya esta siendo utilizado. Por favor intente generar la AC nuevamente.');
            return $this->redirect($this->generateUrl($this->getPathComprobantes()));
        }

        $mensajeImprimir = 'Para imprimir la autorizaci&oacute;n contable ' 
				. $ordenPago->getNumeroAutorizacionContable() . ' haga click <a href="'
                . $this->generateUrl($ordenPago->getPathAC() . '_print', ['id' => $ordenPago->getId()])
                . '" class="link-imprimir-op">aqu&iacute;</a>';

        $this->get('session')->getFlashBag()->add('info', $mensajeImprimir);

		if ($request->request->get('hidden_submit_goto_comprobantes') == 1) {
			if ($this->getPathComprobantes() != null) {
				return $this->redirect($this->generateUrl($this->getPathComprobantes()));
			} else {
				return $this->redirect($this->generateUrl('autorizacioncontable'));
			}
		} else {
			return $this->redirect($this->generateUrl('autorizacioncontable'));
		}
		
        
    }
	/**
	* Metodo para hacer override
	*/
	public function getPathComprobantes()
	{
		return null;
	}

    /**
     * Displays a form to create a new OrdenPagoComprobante entity.
     *
     * @Route("/crear", name="autorizacioncontable_new")
     * @Method("POST")
     * @Template("ADIFContableBundle:AutorizacionContable:new.html.twig")
     */
    public function newAction(Request $request) {

        $ids = $request->request->get('ids');

        $ordenPago = $this->getOP();

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        // Por cada comprobante de compra seleccionado en el index
        foreach ($ids as $idComprobante) {

            $comprobante = $em->getRepository($this->getComprobantesClassName())
                    ->find($idComprobante);

            $ordenPago->addComprobante($comprobante);

            $this->setBeneficiarioCustom($ordenPago, $comprobante);
        }

        // Seteo el concepto
        $ordenPago->setConcepto($this->getConceptoCreacion($ordenPago));

        $estadoOrdenPago = ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_PAGO;

        // Si la AC requiere visado
        if ($ordenPago->getRequiereVisado()) {
            $estadoOrdenPago = ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_AUTORIZACION;
        }

        // Seteo el estado
        $ordenPago->setEstadoOrdenPago(
                $em->getRepository('ADIFContableBundle:EstadoOrdenPago')
                        ->findOneByDenominacionEstado($estadoOrdenPago)
        );

        $error = null;

        try {
            $error = $this->generarRetenciones($ordenPago);
        } //
        catch (Exception $e) {

            $this->get('session')->getFlashBag()
                    ->add('error', "Hubo un error al calcular las retenciones");
        }

        if ($this->mostrarErrorExencion($error, $ordenPago->getBeneficiario()->getControllerPath(), $ordenPago->getBeneficiario()->getId())) {
            return $this->redirect($this->generateUrl($this->getPathComprobantes()));
        }

        $result = $this->newActionCustom($em, $ordenPago, $request);

        if ($result != 0) {
            $this->get('session')->getFlashBag()
                    ->add('error', "No se puede generar la autorización contable. " . $result['error']);
            return $this->redirect($this->generateUrl($this->getPathComprobantes()));
        }

        $form = $this->createCreateForm($ordenPago);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $ordenPago,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'pathComprobantes' => $this->getPathComprobantes(),
            'page_title' => 'Crear autorización contable',
			'logRetencion' => ($this->get('session')->get('logRetencionArchivo') != null) 
				? $this->get('session')->get('logRetencionArchivo')
				: ''
        );
    }

    /**
     * Anular AutorizacionContable
     *
     * @Route("/anular/{id}", name="autorizacioncontable_anular")
     * @Method("GET")
     * -@Security("has_role('ROLE_ANULAR_AUTORIZACION_CONTABLE')")
     */
    public function anularAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ordenPago = $em->getRepository($this->getClassName())->find($id);

        if (!$ordenPago) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ' . $this->getClassName());
        }
		
		if ($ordenPago->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA) {
			 $this->get('session')->getFlashBag()
                ->add('error', "La autorizaci&oacute;n contable ya se encuentra anulado.");
				
			return $this->redirect($this->generateUrl('autorizacioncontable'));
		}

        // Seteo el estado Anulado
        $ordenPago->setEstadoOrdenPago(
                $em->getRepository('ADIFContableBundle:EstadoOrdenPago')
                        ->findOneByDenominacionEstado(
                                ConstanteEstadoOrdenPago::ESTADO_ANULADA
                        )
        );

        // Seteo la fecha de anulación
        $ordenPago->setFechaAnulacion(new DateTime());

        $this->get('session')->getFlashBag()
                ->add('success', "La autorizaci&oacute;n contable se anul&oacute; con &eacute;xito.");

        $this->anularActionCustom($ordenPago, $em);
		
		$this->anularYLiberarComprobantes($ordenPago, $em);

        $em->flush();

        return $this->redirect($this->generateUrl('autorizacioncontable'));
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $em
     */
    public function anularActionCustom($ordenPago, $em) {
        
    }

    /**
     * Print a OrdenPago entity.
     *
     * @Route("/print/{id}", name="autorizacioncontable_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ordenPago = $em->getRepository($this->getClassName())->find($id);

        if (!$ordenPago) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ' . $this->getClassName());
        }

        $html = '<html><head><style type="text/css">' . $this->renderView('::PDF/mpdf.default.css.twig') . '</style></head><body>';
        $html .= $this->printHTMLAction($ordenPago);
        $html .= '</body></html>';

        $filename = 'autorizacion_contable' . $ordenPago->getNumeroAutorizacionContable() . '.pdf';

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);

        if ($ordenPago->getEstaAnulada()) {
            $mpdfService->SetWatermarkText('ANULADA');
            $mpdfService->showWatermarkText = true;
        }

        $mpdfService->WriteHTML($html);
        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    /**
     * 
     * @param type $em
     * @param type $ordenPago
     * @param type $request
     */
    public function newActionCustom($em, $ordenPago, $request) {
        return 0;
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $comprobante
     */
    public function setBeneficiarioCustom($ordenPago, $comprobante) {
        
    }

    /**
     * 
     * @param type $error
     * @param type $path
     * @param type $id
     * @return boolean
     */
    public function mostrarErrorExencion($error, $path, $id) {

        if ($error != null) {
            if ($error['error']) {
                $errorMsg = '<span> Existen certificados de exenci&oacute;n vencidos:</span>';
                $errorMsg .= '<div style="padding-left: 3em; margin-top: .5em">';
                $errorMsg .= '<ul>';

                if ($error[ConstanteTipoImpuesto::Ganancias]) {
                    $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl($path . '_edit', array('id' => $id)) . '#tab_3">' . ConstanteTipoImpuesto::Ganancias . '</a></li>';
                }
                if ($error[ConstanteTipoImpuesto::IVA]) {
                    $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl($path . '_edit', array('id' => $id)) . '#tab_3">' . ConstanteTipoImpuesto::IVA . '</a></li>';
                }
                if ($error[ConstanteTipoImpuesto::SUSS]) {
                    $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl($path . '_edit', array('id' => $id)) . '#tab_3">' . ConstanteTipoImpuesto::SUSS . '</a></li>';
                }
                if ($error[ConstanteTipoImpuesto::IIBB]) {
                    $errorMsg .= '<li><a target="_blank"  href="' . $this->generateUrl($path . '_edit', array('id' => $id)) . '#tab_3">' . ConstanteTipoImpuesto::IIBB . '</a></li>';
                }
                $errorMsg .= '</ul>';
                $errorMsg .= '</div>';
                if ($error['limitaGeneracion']) {
                    $this->get('session')->getFlashBag()
                            ->add('error', "<span>No se puede generar la autorización contable.</span>" . $errorMsg);
                    return true;
                } else {
                    $this->get('session')->getFlashBag()
                            ->add('warning', "<span>Si bien no corresponde retenci&oacute;n.</span>" . $errorMsg);
                    return false;
                }
            }
        }
    }
	
	/** 
	* Lo mantengo el metodo, para mantener la integridad
	*/
	public function clonar() {}

    // /**
     // * 
     // * @param type $ordenPago
     // * @param type $emContable
     // * @param type $autorizacionContable
     // */
    // public function clonar($ordenPago, $em) {

        // foreach ($ordenPago->getComprobantes() as $comprobante) {

            // $comprobante->setEstadoComprobante(
                    // $em->getRepository('ADIFContableBundle:EstadoComprobante')
                            // ->find(EstadoComprobante::__ESTADO_ANULADO)
            // );

            // $nuevoComprobante = clone $comprobante;

            // /* Seteo el asiento contable relacionado a NULL */
            // $nuevoComprobante->setAsientoContable(null);

            // $nuevoComprobante->setOrdenPago(null);

            // $nuevoComprobante->setEstadoComprobante(
                    // $em->getRepository('ADIFContableBundle:EstadoComprobante')
                            // ->find(EstadoComprobante::__ESTADO_INGRESADO)
            // );

            // foreach ($comprobante->getRenglonesPercepcion() as $renglonPercepcion) {

                // $renglonPercepcionNuevo = clone $renglonPercepcion;
                // $renglonPercepcionNuevo->setComprobante($nuevoComprobante);

                // $em->persist($renglonPercepcionNuevo);
            // }

            // foreach ($comprobante->getRenglonesImpuesto() as $renglonImpuesto) {

                // $renglonImpuestoNuevo = clone $renglonImpuesto;
                // $renglonImpuestoNuevo->setComprobante($nuevoComprobante);

                // $em->persist($renglonImpuestoNuevo);
            // }

            // $esComprobanteCompra = $comprobante->getEsComprobanteCompra();

            // foreach ($comprobante->getRenglonesComprobante() as $renglon) {

                // $renglonNuevo = clone $renglon;
                // $renglonNuevo->setComprobante($nuevoComprobante);

                //Si el comprobante es de compra / servicio
                // if ($esComprobanteCompra) {

                    // /* @var $renglon \ADIF\ContableBundle\Entity\RenglonComprobanteCompra */

                    // foreach ($renglon->getRenglonComprobanteCompraCentrosDeCosto() as $renglonComprobanteCompraCentrosDeCosto) {

                        // $renglonComprobanteCompraCentrosDeCostoNuevo = clone $renglonComprobanteCompraCentrosDeCosto;

                        // $renglonComprobanteCompraCentrosDeCostoNuevo->setRenglonComprobanteCompra($renglonNuevo);

                        // $renglonNuevo->addRenglonComprobanteCompraCentrosDeCosto($renglonComprobanteCompraCentrosDeCostoNuevo);
                    // }
                // }

                // $em->persist($renglonNuevo);
            // }

            // /* Seteo el estado del comprobante a "Anulado" */
            // $comprobante->setEstadoComprobante(
                    // $em->getRepository('ADIFContableBundle:EstadoComprobante')
                            // ->find(EstadoComprobante::__ESTADO_ANULADO)
            // );
            
            //Fix: Le doy la baja al viejo comprobante para que no me duplique en la C/C del proveedor  - @gluis - 06/07/2016
            // $comprobante->setFechaBaja(new DateTime());

            // $em->persist($comprobante);

            // $em->persist($nuevoComprobante);

            // foreach ($comprobante->getPagosParciales() as $pagoParcial) {

                // if (!$pagoParcial->getAnulado()) {

                    // $pagoParcialNuevo = clone $pagoParcial;

                    // $pagoParcialNuevo->setComprobante($nuevoComprobante);

                    // $pagoParcial->setAnulado(true);
                    // $pagoParcialNuevo->setAnulado(false);

                    // $pagoParcial->getOrdenPago()->setPagoParcial($pagoParcialNuevo);

                    // $em->persist($pagoParcialNuevo);
                // }
            // }
        // }

        // foreach ($ordenPago->getAnticipos() as $anticipo) {
            // $anticipoNuevo = clone $anticipo;
            // $anticipoNuevo->setOrdenPago(null);
            // $em->persist($anticipoNuevo);
        // }
    // }
	
	/**
	* Este metodo anula la AC, libera los comprobantes y guarda el el log/historico 
	* de los comprobantes de la AC
	*/
	public function anularYLiberarComprobantes($ordenPago, $em)
	{
		$fechaHoy = new \DateTime();
		
		$ordenPagoLog = new OrdenPagoLog();
		
		$ordenPagoLog->setDescripcion('Anulación AC Nro. ' . $ordenPago->getNumeroAutorizacionContable());
		
		$ordenPagoLog->setOrdenPago($ordenPago);
		
		$ordenPagoLog->setTotalBruto($ordenPago->getTotalBruto());
		
		$ordenPagoLog->setTotalNeto($ordenPago->getMontoNeto());
		
		$ordenPagoLog->setTotalRetenciones($ordenPago->getMontoRetenciones());
		
		$estadoOrdenPagoAnulada = $em->getRepository('ADIFContableBundle:EstadoOrdenPago')
										->findOneByDenominacionEstado(ConstanteEstadoOrdenPago::ESTADO_ANULADA);
							
		$ordenPagoLog->setEstadoOrdenPago($estadoOrdenPagoAnulada);
		
		foreach ($ordenPago->getComprobantes() as $comprobante) {
			
			$ordenPagoLog->addComprobante($comprobante);
			
			$comprobante->addOrdenPagoLog($ordenPagoLog);
			
			$comprobante->setOrdenPago(null);
			
			$comprobante->setEstadoComprobante(
                    $em->getRepository('ADIFContableBundle:EstadoComprobante')
                            ->find(EstadoComprobante::__ESTADO_INGRESADO)
            );
			
			$em->persist($comprobante);
			
			$em->persist($ordenPagoLog);
		}
		
		foreach($ordenPago->getRetenciones() as $retencion) {
			/* @var ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto $retencion */
			
			foreach($retencion->getRenglonDeclaracionJurada() as $renglonDDJJ) {
				$renglonDDJJ->setComprobanteRetencionImpuesto(null);
				$em->persist($renglonDDJJ);
			}
			
			$retencion->setOrdenPago(null);
			$ordenPagoLog->addRetencion($retencion);
			
			$em->persist($retencion);
			$em->persist($ordenPagoLog);
		}
		
		foreach ($ordenPago->getAnticipos() as $anticipo) {
			/* @var ADIF\ContableBundle\Entity\Anticipo $anticipo */
			$anticipo->setOrdenPagoCancelada(null);
			$ordenPagoLog->addAnticipo($anticipo);
			
			$em->persist($anticipo);
			$em->persist($ordenPagoLog);
        }
	}

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function printHTMLAction($ordenPago) {

        $arrayResult['op'] = $ordenPago;
        
        $empresaSession = EmpresaSession::getInstance();
        $idEmpresa = $empresaSession->getIdEmpresa();
        
        $arrayResult['idEmpresa'] = $idEmpresa;

        if ($ordenPago->getBeneficiario() != null) {
            $arrayResult['razonSocial'] = $ordenPago->getBeneficiario()->getRazonSocial();
            $arrayResult['tipoDocumento'] = $ordenPago->getBeneficiario()->getTipoDocumento();
            $arrayResult['nroDocumento'] = $ordenPago->getBeneficiario()->getNroDocumento();
            $arrayResult['domicilio'] = $ordenPago->getBeneficiario()->getDomicilio();
            $arrayResult['localidad'] = $ordenPago->getBeneficiario()->getLocalidad();
        }

        return $this->renderView('ADIFContableBundle:AutorizacionContable:print.show.html.twig', $arrayResult);
    }

    /**
     * Visa AutorizacionContable
     *
     * @Route("/visar/{id}", name="autorizacioncontable_visar")
     * @Method("GET")
     */
    public function visarAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ordenPago = $em->getRepository($this->getClassName())->find($id);

        if (!$ordenPago) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ' . $this->getClassName());
        }

        // Seteo el estado Pendiente de Pago
        $ordenPago->setEstadoOrdenPago(
                $em->getRepository('ADIFContableBundle:EstadoOrdenPago')
                        ->findOneByDenominacionEstado(
                                ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_PAGO
                        )
        );

        $this->get('session')->getFlashBag()
                ->add('success', "La autorizaci&oacute;n contable se vis&oacute; con &eacute;xito.");

        $em->flush();

        return $this->redirect($this->generateUrl('autorizacioncontable'));
    }

    /**
     * Setea el EstadoOrdenPago a "Pendiente pago" de las AutorizacionContable
     * recibidas por parámetro
     *
     * @Route("/autorizar-autorizaciones-contables/", name="autorizacioncontable_visar-autorizaciones-contables")
     * @Method("POST")
     */
    public function autorizarAutorizacionesContablesAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ids = json_decode($request->request->get('ids'));

        // Obtengo el EstadoOrdenPago cuya denominacion sea igual a "Pendiente pago"
        $estadoOrdenPago = $em->getRepository('ADIFContableBundle:EstadoOrdenPago')
                ->findOneByDenominacionEstado(ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_PAGO)
        ;

        $statusCode = Response::HTTP_OK;

        foreach ($ids as $id) {

//          $ordenPago = $em->getRepository($this->getClassName())->find($id);
            $ordenPago = $em->getRepository('ADIFContableBundle:OrdenPago')->find($id);

            if (!$ordenPago) {

                $statusCode = Response::HTTP_NOT_FOUND;

                throw $this->createNotFoundException('No se puede encontrar la entidad ' . $this->getClassName() . ' con el id ' . $id);
            } //. 
            else {

                if (ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_AUTORIZACION == $ordenPago->getEstadoOrdenPago()) {

                    $ordenPago->setEstadoOrdenPago($estadoOrdenPago);

                    $em->persist($ordenPago);
                }
            }
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Las autorizaciones contables fueron visadas correctamente.');

        return new Response('', $statusCode);
    }
    
    /**
     * Setea el EstadoOrdenPago a "Net Cash corrida pendiente" de las AutorizacionContable
     * recibidas por parámetro
     *
     * @Route("/agregar-netcash/", name="autorizacioncontable_agregar-netcash")
     * @Method("POST")
     */
    public function agregarNetCashAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ids = json_decode($request->request->get('ids'));

        // Obtengo el EstadoOrdenPago cuya denominacion sea igual a "Pendiente pago"
        $estadoOrdenPago = $em->getRepository('ADIFContableBundle:EstadoOrdenPago')->findOneByDenominacionEstado(ConstanteEstadoOrdenPago::ESTADO_NETCASH_CORRIDA_PENDIENTE);

        $statusCode = Response::HTTP_OK;

        foreach ($ids as $id) {
            $ordenPago = $em->getRepository('ADIFContableBundle:OrdenPago')->find($id);
            if (!$ordenPago) {
                $statusCode = Response::HTTP_NOT_FOUND;
                throw $this->createNotFoundException('No se puede encontrar la entidad ' . $this->getClassName() . ' con el id ' . $id);
            } else {
                if ($ordenPago->getEstadoOrdenPago() == ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_PAGO) {
                    $ordenPago->setEstadoOrdenPago($estadoOrdenPago);
                    $em->persist($ordenPago);
                } else {
                    $statusCode = Response::HTTP_NOT_FOUND;
                    throw $this->createNotFoundException('La autorizacion contable con el id '.$id.' no esta en estado "Pendiente de pago"');
                }
            }
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Las autorizaciones contables fueron agregadas al Net Cash correctamente.');

        return new Response('', $statusCode);
    }
    
    /**
     * Autorizaciones contables pendientes
     *
     * @Route("/autorizaciones_pendientes_sin_ver/", name="autorizacioncontable_autorizaciones_pendientes_sin_ver")
     * @Method("GET|POST")
     */
    public function autorizacionesPendientesSinVerAction(Request $request) {


        $autorizacionesContables = array();

        if ($request->request->get('fechaInicio') && $request->request->get('fechaFin')) {

            $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->request->get('fechaInicio') . ' 00:00:00');
            $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->request->get('fechaFin') . ' 23:59:59');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $rsm = new ResultSetMapping();

            $rsm->addScalarResult('id', 'id');
            $rsm->addScalarResult('fechaAutorizacionContable', 'fechaAutorizacionContable');
            $rsm->addScalarResult('numeroAutorizacionContable', 'numeroAutorizacionContable');
            $rsm->addScalarResult('proveedor', 'proveedor');
            $rsm->addScalarResult('concepto', 'concepto');
            $rsm->addScalarResult('totalBruto', 'totalBruto');
            $rsm->addScalarResult('montoRetenciones', 'montoRetenciones');
            $rsm->addScalarResult('montoNeto', 'montoNeto');
            $rsm->addScalarResult('estadoOrdenPago', 'estadoOrdenPago');
            $rsm->addScalarResult('aliasTipoImportanciaEstadoOrdenPago', 'aliasTipoImportanciaEstadoOrdenPago');
            $rsm->addScalarResult('path', 'path');
            $rsm->addScalarResult('pathAC', 'pathAC');
            $rsm->addScalarResult('usuarioCreacion', 'usuarioCreacion');
            $rsm->addScalarResult('requiereVisado', 'requiereVisado');

            $native_query = $em->createNativeQuery('
                SELECT
                    id,
                    fechaAutorizacionContable,
                    numeroAutorizacionContable,
                    proveedor,
                    concepto,
                    totalBruto,
                    montoRetenciones,
                    montoNeto,
                    estadoOrdenPago,
                    aliasTipoImportanciaEstadoOrdenPago,
                    path,
                    pathAC,
                    usuarioCreacion,
                    requiereVisado,
                    fueVista
                FROM
                    vistaordenpago
                WHERE requiereVisado = true AND estadoOrdenPago = \'' . ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_AUTORIZACION .
                    '\' AND fueVista = false AND fechaAutorizacionContable BETWEEN ? AND ?', $rsm);

            $native_query->setParameter(1, $fechaInicio, Type::DATETIME);
            $native_query->setParameter(2, $fechaFin, Type::DATETIME);

            $autorizacionesContables = $native_query->getResult();
        }

        return $this->render('ADIFContableBundle:AutorizacionContable:index_table.html.twig', array(
                    'autorizacionesContables' => $autorizacionesContables
                        )
        );
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/autorizaciones_vistas/", name="autorizacioncontable_autorizaciones_vistas")
     * @Method("GET|POST")
     */
    public function autorizacionesVistasAction(Request $request) {

        //$ids = json_decode($request->request->get('ids', '[]'));
        $id = $request->request->get('id');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $statusCode = Response::HTTP_OK;

        //foreach ($ids as $id) {

        $ordenPago = $em->getRepository('ADIFContableBundle:OrdenPago')->find($id);

        if (!$ordenPago) {

            $statusCode = Response::HTTP_NOT_FOUND;

            throw $this->createNotFoundException('No se puede encontrar la entidad ' . $this->getClassName() . ' con el id ' . $id);
        } else {

            $ordenPago->setFueVista(true);

            $em->persist($ordenPago);
        }
        //}

        $em->flush();

        return new JsonResponse(array('status' => 'OK'));
    }
	
	/**
     * 
     * Permite bajar el log de retenciones que genera RetencionesService
     * 
     * @Route("/bajar_log_retencion/", name="autorizacioncontable_bajar_log_retencion")
     * @Method("POST")
	 * @Security("has_role('ROLE_BAJAR_LOG_RETENCION')")
     */
	public function bajarLogRetencionAction(Request $request)
	{
		$logRetencionDirectorio =  $this->container->getParameter('directorio_retenciones');
		$logRetencionArchivo =  $this->get('session')->get('logRetencionArchivo');
		
		$response = new Response();
		if ($logRetencionArchivo != null) {
			$response = new BinaryFileResponse($logRetencionDirectorio . $logRetencionArchivo);
			$d = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $logRetencionArchivo);
			$response->headers->set('Content-Type', 'text/plain');
			$response->headers->set('Content-Disposition', $d);
		}
        return $response;
	}

}
