<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Cheque;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoChequera;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoNetCash;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoPago;
use ADIF\ContableBundle\Entity\PagoOrdenPago;
use ADIF\ContableBundle\Entity\TransferenciaBancaria;
use ADIF\ContableBundle\Entity\NetCash;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use mPDF;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Types\Type;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use ADIF\ContableBundle\Service\AsientoContableServiceException;
use ADIF\ContableBundle\Service\ContabilidadPresupuestariaServiceException;
use ADIF\BaseBundle\Controller\IContainerAnulable;
use ADIF\ContableBundle\Entity\OrdenPagoLog;
use ADIF\ContableBundle\Entity\OrdenPagoPagoParcial;
use ADIF\BaseBundle\Session\EmpresaSession;


/**
 * OrdenPago controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoBaseController extends BaseController {

    private $base_breadcrumbs;

    /**
     * FORMA_PAGO_CHEQUE
     */
    const FORMA_PAGO_CHEQUE = 1;
    const FORMA_PAGO_TRANSFERENCIA = 2;
    const FORMA_PAGO_NETCASH = 3;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Lists all OrdenPago entities.
     *
     * @Route("/", name="ordenpago")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:index.html.twig")
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['&Oacute;rdenes de pago'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => '&Oacute;rdenes de pago',
            'page_info' => 'Lista de órdenes de pago'
        );
    }

    /**
     * Tabla para OrdenPago.
     *
     * @Route("/index_table/", name="ordenpago_index_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableAction(Request $request) {

        $ordenesPago = array();

        if ($request->query->get('fechaInicio') && $request->query->get('fechaFin')) {

            $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaInicio') . ' 00:00:00');
            $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaFin') . ' 23:59:59');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $rsm = new ResultSetMapping();

            $rsm->addScalarResult('id', 'id');
            $rsm->addScalarResult('fechaOrdenPago', 'fechaOrdenPago');
            $rsm->addScalarResult('numeroOrdenPago', 'numeroOrdenPago');
            $rsm->addScalarResult('proveedor', 'proveedor');
            $rsm->addScalarResult('proveedorCUIT', 'proveedorCUIT');
            $rsm->addScalarResult('concepto', 'concepto');
            //$rsm->addScalarResult('cuentaBancariaADIFString', 'cuentaBancariaADIFString');
            $rsm->addScalarResult('pagos', 'pagos');
            $rsm->addScalarResult('totalBruto', 'totalBruto');
            $rsm->addScalarResult('montoRetenciones', 'montoRetenciones');
            $rsm->addScalarResult('montoNeto', 'montoNeto');
            //$rsm->addScalarResult('estadoPago', 'estadoPago');
            //$rsm->addScalarResult('aliasTipoImportanciaEstadoPago', 'aliasTipoImportanciaEstadoPago');
            $rsm->addScalarResult('path', 'path');
            $rsm->addScalarResult('usuarioCreacion', 'usuarioCreacion');
            $rsm->addScalarResult('estadoOrdenPago', 'estadoOrdenPago');
            $rsm->addScalarResult('aliasTipoImportanciaEstadoOrdenPago', 'aliasTipoImportanciaEstadoOrdenPago');

            $native_query = $em->createNativeQuery('
                SELECT
                    id,
                    fechaOrdenPago,
                    numeroOrdenPago,
                    proveedor,
                    proveedorCUIT,
                    concepto,
                    -- cuentaBancariaADIFString,
                    pagos,
                    totalBruto,
                    montoRetenciones,
                    montoNeto,
                   -- estadoPago,
                   -- aliasTipoImportanciaEstadoPago,
                    path,
                    usuarioCreacion,
                    estadoOrdenPago,
                    aliasTipoImportanciaEstadoOrdenPago
                FROM
                    vistaordenpago
                WHERE fechaOrdenPago BETWEEN ? AND ?
            ', $rsm);

            $native_query->setParameter(1, $fechaInicio, Type::DATETIME);
            $native_query->setParameter(2, $fechaFin, Type::DATETIME);

            $ordenesPago = $native_query->getResult();
        }

        return $this->render('ADIFContableBundle:OrdenPago:index_table.html.twig', array(
                    'ordenesPago' => $ordenesPago,
                        )
        );
    }

    /**
     * @Route("/estados", name="orden_pago_estados")
     */
    public function listaEstadoOrdenPagoAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:EstadoOrdenPago', $this->getEntityManager());

        $query = $repository->createQueryBuilder('e')
                ->select('e.id', 'e.denominacionEstado')
                ->orderBy('e.denominacionEstado', 'ASC')
                ->getQuery()
                ->useResultCache(true, 36000, 'orden_pago_estados')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return new JsonResponse($query->getResult());
    }

    /**
     * @Route("/pago_estados", name="pago_estados")
     */
    public function listaEstadoPagoAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:EstadoPago', $this->getEntityManager());

        $query = $repository->createQueryBuilder('e')
                ->select('e.id', 'e.denominacionEstado')
                ->orderBy('e.denominacionEstado', 'ASC')
                ->getQuery()
                ->useResultCache(true, 36000, 'pago_estados')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return new JsonResponse($query->getResult());
    }

    /**
     * Devuelve el template para pagar ordenes de pago
     *
     * @Route("/form_pagar", name="ordenpago_pagar_form")
     * @Method("POST")   
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")
     */
    public function getFormPagarAction(Request $request) {

        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());
        $emContable = $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $cuentasBancoAdif = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->findByEstaActiva(true);
//              ->findAll();

        $chequeras = $emContable->getRepository('ADIFContableBundle:Chequera')->getChequerasByEstado(ConstanteEstadoChequera::ESTADO_CHEQUERA_HABILITADA_ACTIVA);

        $chequerasArray = [];
        $chequeraCuentaArray = [];

        foreach ($cuentasBancoAdif as $cuentaBancoAdif) {
            $chequeraCuentaArray[$cuentaBancoAdif->getId()] = [];
        }

        foreach ($chequeras as $chequera) {
            $chequerasArray[$chequera->getId()] = array('chequera' => $chequera->__toString(), 'numeroSiguiente' => $chequera->getNumeroSiguiente());
            $chequeraCuentaArray[$chequera->getIdCuenta()][] = $chequera->getId();
        }

        $idOrdenPago = $request->request->get('id');

        return array(
            'cuentasBancoAdif' => $cuentasBancoAdif,
            'chequeras' => $chequerasArray,
            'chequerasEntities' => $chequeras,
            'chequeraCuenta' => $chequeraCuentaArray,
            'cantidadAutorizacionesContables' => $this->getCantidadAutorizacionesContablesByBeneficiario($idOrdenPago)
        );
    }

    /**
     * Finds and displays a OrdenPago entity.
     *
     * @Route("/{id}", name="ordenpago_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository($this->getClassName())->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ' . $this->getClassName());
        }

        $bread = $this->base_breadcrumbs;
        if ($entity->getNumeroOrdenPago() == null) {
            $bread['Autorizaciones contables'] = $this->generateUrl('autorizacioncontable');
            $bread['Autorizaci&oacute;n contable'] = null;
            $page_title = 'Ver Autorizaci&oacute;n contable';
        } else {
            $bread['&Oacute;rdenes de pago'] = $this->generateUrl('ordenpago');
            $bread['Orden de Pago'] = null;
            $page_title = 'Ver orden de pago';
        }
		
		$ordenPagoLog = null;
		if ($entity->getEstaAnulada()) {
			$ordenPagoLog = $em->getRepository('ADIFContableBundle:OrdenPagoLog')
				->findOneByOrdenPago($entity->getId());
		}
		
        return array(
            'entity' => $entity,
            'proveedor' => $this->getDatosProveedor($entity),
            'pathReemplazarPago' => $this->getPathReemplazarPago(),
			'estaAnulada' => $entity->getEstaAnulada(),
			'ordenPagoLog' => $ordenPagoLog,
            'breadcrumbs' => $bread,
            'page_title' => $page_title
        );
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/pagar", name="ordenpago_pagar")
     * @Method("POST")   
     * -@Security("has_role('ROLE_TESORERIA')")
     */
    public function pagarAction(Request $request) {

        $id = $request->request->get('id');
        $pago = $request->request->get('pago');

        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());
        $emContable = $this->getDoctrine()->getManager($this->getEntityManager());

        $ordenPagoPendientePago = $emContable->getRepository($this->getClassName())->find($id);
       // var_dump($ordenPagoPendientePago->getPagoOrdenPago());
       // exit;
        /* @var $ordenPagoPendientePago \ADIF\ContableBundle\Entity\OrdenPago */

        if ($ordenPagoPendientePago->getPagoOrdenPago() != null) {
            $resultPago = array(
                'result' => 'ERROR',
                'msg' => 'La autorizaci&oacute;n contable ya fue pagada con anterioridad'
            );
            return new JsonResponse($resultPago);
        }

        //estado pagada
        $ordenPagoPendientePago->setEstadoOrdenPago(
                $emContable->getRepository('ADIFContableBundle:EstadoOrdenPago')
                        ->findOneByDenominacionEstado(ConstanteEstadoOrdenPago::ESTADO_PAGADA)
        );

        $ordenPagoPendientePago->setFechaOrdenPago(new DateTime());
        $ordenPagoPendientePago->setFechaContable(new DateTime());

        $pagoOrdenPago = new PagoOrdenPago();
        $pagoOrdenPago->setMonto($ordenPagoPendientePago->getTotalBruto());
        $pagoOrdenPago->addOrdenesPago($ordenPagoPendientePago);

        try { 
            $ordenPagoPendientePago->setNumeroOrdenPago( $this->get('adif.orden_pago_service')->getSiguienteNumeroOrdenPago() );
        } catch (Exception $e) {

            $resultPago = array(
                'result' => 'ERROR',
                'msg' => 'Hubo un error al asignar el número de orden de pago'
            );

            return new JsonResponse($resultPago);
        }

        // Obtengo el EstadoPago igual a "Creado"
        $estadoPagoCreado = $emContable->getRepository('ADIFContableBundle:EstadoPago')->
                findOneBy(
                array('denominacionEstado' => ConstanteEstadoPago::ESTADO_PAGO_CREADO), //
                array('id' => 'desc'), 1, 0);

        foreach ($pago['renglones'] as $renglonPago) {
            $formaPago = $renglonPago['forma_pago'];
            $cuentaBancaria = $renglonPago['cuenta_bancaria'];
            $chequera = isset($renglonPago['chequera']) ? $renglonPago['chequera'] : null;
            $cheque = $renglonPago['cheque'];
            $transferencia = $renglonPago['transferencia'];
            $monto = floatval(str_replace(',', '.', $renglonPago['monto']));

            // Cheque
            if ($formaPago == self::FORMA_PAGO_CHEQUE) {
                if (empty($cheque)) {
                    return new JsonResponse(array('status' => 'ERROR', 'msg' => 'Debe indicar n&uacute;mero de cheque'));
                } else {
                    if (!is_numeric($cheque)) {
                        return new JsonResponse(array('status' => 'ERROR', 'msg' => 'Debe indicar un n&uacute;mero de cheque v&aacute;lido'));
                    }

                    /* @var $chequeraEntity \ADIF\ContableBundle\Entity\Chequera */
                    $chequeraEntity = $emContable->getRepository('ADIFContableBundle:Chequera')
                            ->find($chequera);
//            $chequeraService = $this->get('adif.chequera_service');

                    if ($cheque < $chequeraEntity->getNumeroInicial() || $cheque > $chequeraEntity->getNumeroFinal()) {
                        return new JsonResponse(array('status' => 'ERROR', 'msg' => 'El n&uacute;mero de cheque indicado (' . $cheque . ') no se encuentra dentro del rango de la chequera seleccionada (' . $chequeraEntity->getNumeroInicial() . ' - ' . $chequeraEntity->getNumeroFinal() . ')'));
                    }

                    $chequeExistente = $emContable->getRepository('ADIFContableBundle:Cheque')->findBy(array('numeroCheque' => $cheque));

                    if ($chequeExistente) {
                        return new JsonResponse(array('status' => 'ERROR', 'msg' => 'El n&uacute;mero de cheque ya fue utilizado'));
                    }

                    $chequeEntity = new Cheque();
                    $chequeEntity->setEstadoPago($estadoPagoCreado);
                    $chequeEntity->setChequera($chequeraEntity);

                    $chequeEntity->setNumeroCheque($cheque);

                    $this->setHistoricoEstadoPago($chequeEntity, $formaPago);

                    $chequeEntity->setMonto($monto);
                    $chequeEntity->setPagoOrdenPago($pagoOrdenPago);
                    $pagoOrdenPago->addCheque($chequeEntity);
                }
            } else {

                // Transferencia
                $transferenciaAnterior = $emContable->getRepository('ADIFContableBundle:TransferenciaBancaria')
                        ->findOneBy(array(
                    'numeroTransferencia' => $transferencia, //
                    'idCuenta' => $cuentaBancaria)
                );

                if ($transferenciaAnterior != null) {

                    $resultPago = array(
                        'result' => 'ERROR',
                        'msg' => 'El n&uacute;mero de transferencia ya se encuentra en uso'
                    );

                    return new JsonResponse($resultPago);
                }

                $transferenciaEntity = new TransferenciaBancaria();
                $transferenciaEntity->setEstadoPago($estadoPagoCreado);
                $cuentasBancoAdif = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')
                        ->find($cuentaBancaria);
                $transferenciaEntity->setCuenta($cuentasBancoAdif);
                $transferenciaEntity->setNumeroTransferencia($transferencia);

                $this->setHistoricoEstadoPago($transferenciaEntity, $formaPago);

                $transferenciaEntity->setMonto($monto);
                $transferenciaEntity->setPagoOrdenPago($pagoOrdenPago);
                $pagoOrdenPago->addTransferencia($transferenciaEntity);
            }
        }

        $ordenPagoPendientePago->setPagoOrdenPago($pagoOrdenPago);

        foreach ($ordenPagoPendientePago->getRetenciones() as $comprobanteRetencionImpuestoCompras) {
            /* @var $comprobanteRetencionImpuestoCompras \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto */

            $comprobanteRetencionImpuestoCompras->setNumeroComprobanteRetencion(
                    $this->get('adif.comprobante_retencion_service')
                            ->getSiguienteNumeroComprobanteRetencionPorImpuesto($comprobanteRetencionImpuestoCompras->getRegimenRetencion()->getTipoImpuesto()
                            )
            );

            $comprobanteRetencionImpuestoCompras->setFechaComprobanteRetencion(new DateTime());
            $comprobanteRetencionImpuestoCompras->getRenglonDeclaracionJurada()->setFecha(new DateTime());
        }

        // Persisto la entidad
        $emContable->persist($pagoOrdenPago);

        $this->pagarActionCustom($ordenPagoPendientePago, $emContable);

        /* Genero el asiento contable y presupuestario */
		try {
			
			$resultArray = $this->generarAsientoContablePagar($ordenPagoPendientePago, $this->getUser());
		
		} catch(ContabilidadPresupuestariaServiceException $cpe) {
			
			$result = array(
                'result' => 'ERROR PRESUPUESTARIO',
                'msg' => $cpe->getMessage()
            );

            return new JsonResponse($result);
		
		} catch (Exception $e) {
			
			$result = array(
                'result' => 'ERROR',
                'msg' => 'Fall&oacute; el registro de los asientos'
            );

            return new JsonResponse($result);
		}
        

        // Si el asiento presupuestario falló
        if ($resultArray['mensajeErrorPresupuestario'] != null) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario']));
        }

//        $emContable->flush();
        // Si el asiento contable falló
        if ($resultArray['mensajeErrorContable'] != null) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable']));
        }

        // Si no hubo errores en los asientos
        if ($resultArray['numeroAsiento'] != -1) {


            // Comienzo la transaccion
            $emContable->getConnection()->beginTransaction();
            
            try {
                $emContable->flush();

                $emContable->getConnection()->commit();

                $dataArray = [
                    'data-id-orden-pago' => $id
                ];

                $mensajeFlash = $this->get('adif.asiento_service')
                        ->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], $dataArray, true);

                $mensajeImprimir = 'Para imprimir la orden de pago haga click <a href="' . $this->generateUrl($ordenPagoPendientePago->getPath() . '_print', ['id' => $ordenPagoPendientePago->getId()]) . '" class="link-imprimir-op">aquí</a>';

                return new JsonResponse(array('result' => 'OK', 'msg' => $mensajeFlash, 'imprimir' => $mensajeImprimir));
                
             } catch (UniqueConstraintViolationException $e2) {
                 
                $emContable->getConnection()->rollback();
                $emContable->close();
                
                $resultPago = array(
                    'result' => 'NOK',
                    'msg' => 'El número de orden de pago: ' . $ordenPagoPendientePago->getNumeroOrdenPago() . ' ya esta siendo utilizado. <br/>Por favor intente generar la OP nuevamente.',
                    'debug' => $e2->getMessage()
                );
                
                return new JsonResponse($resultPago);
                
            } catch (\Exception $e) {
                
                $emContable->getConnection()->rollback();
                $emContable->close();
                
                throw $e;
            }
        }

        $resultPago = array(
            'result' => 'OK'
        );

        return new JsonResponse($resultPago);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/{id}/anular", name="ordenpago_anular")
     * @Method("GET")
     * -@Security("has_role('ROLE_VISAR_AUTORIZACION_CONTABLE')")   
     */
    public function anularAction($id) 
	{
        $emContable = $this->getDoctrine()->getManager($this->getEntityManager());

        $ordenPago = $emContable->getRepository($this->getClassName())->find($id);

        if (!$ordenPago) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ' . $this->getClassName());
        }
		
		if ($ordenPago->getEstadoOrdenPago()->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA) {
			 $this->get('session')->getFlashBag()
                ->add('error', "La orden de pago ya se encuentra anulada.");
				
			return $this->redirect($this->generateUrl('ordenpago'));
		}
		
		if ($this->validacionesCustom($ordenPago)) {

			/* Seteo el pago (Cheque/Transferencia) de la OP a "Anulado" */
			$ordenPago->getPagoOrdenPago()->setEstadoPago(
					$emContable->getRepository('ADIFContableBundle:EstadoPago')
							->findOneByDenominacionEstado(ConstanteEstadoPago::ESTADO_PAGO_ANULADO)
			);

			$fecha_hoy = new DateTime();

			/* Seteo el estado de la OP a "Anulada" */
			$ordenPago->setEstadoOrdenPago(
					$emContable->getRepository('ADIFContableBundle:EstadoOrdenPago')
							->findOneByDenominacionEstado(ConstanteEstadoOrdenPago::ESTADO_ANULADA)
			);
			/* Seteo la fecha de anulacion de la op anulada */
			$ordenPago->setFechaAnulacion($fecha_hoy);

			/* @var $autorizacionContable \ADIF\ContableBundle\Entity\Obras\OrdenPagoObra */
			$autorizacionContable = clone $ordenPago;

			
			/*  Seteo el nº y fecha de OP de la Autorización Contable a NULL */
			$autorizacionContable->setNumeroOrdenPago(null);
			$autorizacionContable->setFechaOrdenPago(null);
			$autorizacionContable->setFechaAnulacion(null);

			/* Seteo el asiento contable relacionado a NULL */
			$autorizacionContable->setAsientoContable(null);

			/* Datos auditoria */
			$autorizacionContable->setFechaUltimaActualizacion($fecha_hoy);
			$autorizacionContable->setUsuarioUltimaModificacion($this->getUser());
			
			/* Seteo a hoy la fecha de AC */
			$autorizacionContable->setFechaAutorizacionContable($fecha_hoy);
			$autorizacionContable->setFechaCreacion($fecha_hoy);

			/* Seteo el Pago de la Autorización Contable a NULL */
			$autorizacionContable->setPagoOrdenPago(null);

			/* Seteo el estado de la Autorización Contable a "Pendiente pago" */
			$autorizacionContable->setEstadoOrdenPago(
					$emContable->getRepository('ADIFContableBundle:EstadoOrdenPago')
							->findOneByDenominacionEstado(ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_PAGO)
			);

			/* Genero el asiento contable y presupuestario */
			$resultArray = $this->generarAsientoContableAnular($ordenPago, $this->getUser(), true);

			//$this->anularActionCustom($ordenPago, $emContable, $autorizacionContable);
			
			$this->anularYLiberarComprobantes($ordenPago, $autorizacionContable, $emContable);
			
			$this->anularActionCustom($ordenPago, $emContable, $autorizacionContable);

			// Persisto la entidad
			$emContable->persist($autorizacionContable);

			// Si el asiento presupuestario falló
			if (!empty($resultArray['mensajeErrorPresupuestario'])) {
				$this->get('session')->getFlashBag()->add('error', $resultArray['mensajeErrorPresupuestario']);
			}

			// Si el asiento contable falló
			if (!empty($resultArray['mensajeErrorContable'])) {
				$this->get('session')->getFlashBag()->add('error', $resultArray['mensajeErrorContable']);
			}

			// Si no hubo errores en los asientos
			if ($resultArray['numeroAsiento'] != -1) {

				$emContable->flush();

				$this->get('session')->getFlashBag()->add('success', 'La anulación del pago se realizó con éxito.');

				$dataArray = [
					'data-id-orden-pago' => $id,
					'data-fecha-asiento' => $ordenPago->getFechaAnulacion()->format('d/m/Y'),
					'data-es-anulacion' => 1
				];

				$this->get('adif.asiento_service')->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], $dataArray);
			}
		}

		return $this->redirect($this->generateUrl('ordenpago'));
    }
	
    /**
     * Print a OrdenPago entity.
     *
     * @Route("/print/{id}", name="ordenpago_print")
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

        $filename = 'orden_pago' . $ordenPago->getNumeroOrdenPago() . '.pdf';

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPago */

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
     * Reemplazar pago de la OrdenPago
     *
     * @Route("/reemplazar_pago", name="ordenpago_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        $id = $request->request->get('idOrdenPago');
        $idPago = $request->request->get('id');
        $forma_pago_original = $request->request->get('forma_pago');
        $pago = $request->request->get('pago');

        $renglonPago = $pago['renglones'][array_keys($pago['renglones'])[0]];

        $formaPago = $renglonPago['forma_pago'];
        $cuentaBancaria = $renglonPago['cuenta_bancaria'];
        $chequera = isset($renglonPago['chequera']) ? $renglonPago['chequera'] : null;
        $cheque = $renglonPago['cheque'];
        $transferencia = $renglonPago['transferencia'];

        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());
        $emContable = $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $chequeEntity = null;
        $transferenciaEntity = null;

        $ordenPago = $emContable->getRepository($this->getClassName())->find($id);

        /* 1 - Seteo el estado "Reemplazado" al antiguo pago */

        // Obtengo el EstadoPago igual a "Reemplazado"
        $estadoPagoAnulado = $em->getRepository('ADIFContableBundle:EstadoPago')->
                findOneBy(
                array('denominacionEstado' => ConstanteEstadoPago::ESTADO_REEMPLAZADO), //
                array('id' => 'desc'), 1, 0);

        // Si el pago fue con un Cheque
        if ($forma_pago_original == 'cheque') {
            $pago = $em->getRepository('ADIFContableBundle:Cheque')->find($idPago);
        }
        // Sino, si el pago fue con una Transferencia
        else {
            $pago = $em->getRepository('ADIFContableBundle:Transferencia')->find($idPago);
        }
        $pago->setEstadoPago($estadoPagoAnulado);
        /* FIN 1 */

        $pagoOrdenPagoAnterior = $ordenPago->getPagoOrdenPago();

        /* 2 - Seteo a la OrdenPago el nuevo pago */
        $pagoOrdenPagoNuevo = new PagoOrdenPago();
        $pagoOrdenPagoNuevo->addOrdenesPago($ordenPago);
        $pagoOrdenPagoNuevo->setFechaPago($ordenPago->getPagoOrdenPago()->getFechaPago());

        // Obtengo el EstadoPago igual a "Creado"
        $estadoPagoCreado = $em->getRepository('ADIFContableBundle:EstadoPago')->
                findOneBy(
                array('denominacionEstado' => ConstanteEstadoPago::ESTADO_PAGO_CREADO), //
                array('id' => 'desc'), 1, 0);

        // Cheque
        if ($formaPago == self::FORMA_PAGO_CHEQUE) {
            if (empty($cheque)) {
                return new JsonResponse(array('status' => 'ERROR', 'msg' => 'Debe indicar n&uacute;mero de cheque'));
            } else {
                if (!is_numeric($cheque)) {
                    return new JsonResponse(array('status' => 'ERROR', 'msg' => 'Debe indicar un n&uacute;mero de cheque v&aacute;lido'));
                }

                //$chequeraService = $this->get('adif.chequera_service');

                $chequeraEntity = $emContable->getRepository('ADIFContableBundle:Chequera')->find($chequera);

                if ($cheque < $chequeraEntity->getNumeroInicial() || $cheque > $chequeraEntity->getNumeroFinal()) {
                    return new JsonResponse(array('status' => 'ERROR', 'msg' => 'Debe indicar un n&uacute;mero de cheque dentro del rango de la chequera (' . $chequeraEntity->getNumeroInicial() . ' - ' . $chequeraEntity->getNumeroFinal() . ')'));
                }

                $chequeExistente = $emContable->getRepository('ADIFContableBundle:Cheque')->findBy(array('numeroCheque' => $cheque));

                if ($chequeExistente) {
                    return new JsonResponse(array('status' => 'ERROR', 'msg' => 'El n&uacute;mero de cheque ya fue utilizado'));
                }

                $chequeEntity = new Cheque();
                $chequeEntity->setEstadoPago($estadoPagoCreado);
                $chequeEntity->setChequera($chequeraEntity);
                $chequeEntity->setNumeroCheque($cheque);
                $chequeEntity->setMonto($pago->getMonto());

                //$chequeEntity->setNumeroCheque($chequeraService->getSiguienteNumeroCheque($emContable, $chequeraEntity));
//                $pagoAnterior = $pagoOrdenPagoNuevo->getCheque();
//                $pagoNuevo = $chequeEntity;

                $pagoOrdenPagoNuevo->addCheque($chequeEntity);
                $chequeEntity->setPagoOrdenPago($pagoOrdenPagoNuevo);
            }
        }
        // Transferencia
        else {
            $transferenciaExistente = $emContable->getRepository('ADIFContableBundle:TransferenciaBancaria')->findOneBy(array(
                'numeroTransferencia' => $transferencia, //
                'idCuenta' => $cuentaBancaria)
            );
            if ($transferenciaExistente != null) {
                $resultPago = array(
                    'result' => 'ERROR',
                    'msg' => 'El n&uacute;mero de transferencia ya se encuentra en uso'
                );
                return new JsonResponse($resultPago);
            }

            $transferenciaEntity = new TransferenciaBancaria();
            $transferenciaEntity->setEstadoPago($estadoPagoCreado);
            $cuentasBancoAdif = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($cuentaBancaria);
            $transferenciaEntity->setCuenta($cuentasBancoAdif);
            $transferenciaEntity->setMonto($pago->getMonto());
            $transferenciaEntity->setNumeroTransferencia($transferencia);

//            $pagoAnterior = $pagoOrdenPagoNuevo->getTransferencia();
//            $pagoNuevo = $transferenciaEntity;

            $pagoOrdenPagoNuevo->addTransferencia($transferenciaEntity);
            $transferenciaEntity->setPagoOrdenPago($pagoOrdenPagoNuevo);
        }

        foreach ($pagoOrdenPagoAnterior->getCheques() as $cheque) {
            if ($cheque->getId() != $idPago) {
                $cheque->setPagoOrdenPago($pagoOrdenPagoNuevo);
            }
        }

        foreach ($pagoOrdenPagoAnterior->getTransferencias() as $transferencia) {
            if ($transferencia->getId() != $idPago) {
                $transferencia->setPagoOrdenPago($pagoOrdenPagoNuevo);
            }
        }

        $pagoOrdenPagoNuevo->setMonto($pagoOrdenPagoAnterior->getMonto());
        $ordenPago->setPagoOrdenPago($pagoOrdenPagoNuevo);
        /* FIN 2 */

        // Persisto la entidad
        $emContable->persist($pagoOrdenPagoNuevo);

        /* 3 - Genero los asientos contables correspondientes */

        $numeroAsiento = $this->get('adif.asiento_service')->generarAsientoReemplazoPago($ordenPago, ($forma_pago_original == 'cheque' ? $pago : null), $chequeEntity, ($forma_pago_original != 'cheque' ? $pago : null), $transferenciaEntity, $this->getUser(), $this->getConceptoAsientoReemplazoPago());

        // Si el asiento no contable falló
        if ($numeroAsiento != -1) {

            $emContable->flush();

            $this->get('session')->getFlashBag()->add('success', 'El reemplazo del pago se realizó con éxito.');

            $dataArray = ['data-id-orden-pago' => $ordenPago->getId()];

            $this->get('adif.asiento_service')->showMensajeFlashAsientoContable($numeroAsiento, $dataArray);
        }

        /* FIN 3 */

        $output = array(
            'result' => 'OK'
        );

        return new JsonResponse($output);
    }

    /**
     * 
     * @param type $ordenPagoPendientePago
     * @param type $emContable
     */
    public function pagarActionCustom($ordenPagoPendientePago, $emContable) {
        
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $emContable
     * @param type $autorizacionContable
     */
    public function anularActionCustom($ordenPago, $emContable, $autorizacionContable) {
        
    }

    /**
     * 
     * @param type $idOrdenPago
     * @return int
     */
    public function getCantidadAutorizacionesContablesByBeneficiario($idOrdenPago) {

        return 0;
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago';
    }

    /**
     *
     * @Route("/editar_fecha/", name="ordenpago_editar_fecha")
     */
    public function updateFechaAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $numeroAsiento = $request->request->get('numero_asiento');

        $fecha = $request->request->get('fecha');

        $idOrdenPago = $request->request->get('id_orden_pago');

        $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')
                ->findOneByNumeroAsiento($numeroAsiento);

        // Si existe el asiento Y el usuario logueado genera asientos contables
        if (!$asientoContable && false === $this->get('security.context')->isGranted('ROLE_DETACH_ASIENTO')) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
        }

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPago */
        $ordenPago = $em->getRepository('ADIFContableBundle:OrdenPago')->find($idOrdenPago);

        $ordenPago->setFechaContable(\DateTime::createFromFormat('d/m/Y', $fecha));

        foreach ($ordenPago->getRetenciones() as $comprobanteRetencionImpuesto) {
            /* @var $comprobanteRetencionImpuesto \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto */

            $comprobanteRetencionImpuesto->getRenglonDeclaracionJurada()->setFecha(\DateTime::createFromFormat('d/m/Y', $fecha));
        }

        $em->persist($ordenPago);

        $em->flush();

        return new Response();
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

        return $this->renderView('ADIFContableBundle:OrdenPago:print.show.html.twig', $arrayResult);
    }

    /**
     * Print a OrdenPago entity.
     *
     * @Route("/printOPCompleta/{id}", name="ordenpago_print_completa")
     * @Method("GET")
     * @Template()
     */
    public function printOPCompletaAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ordenPago = $em->getRepository($this->getClassName())->find($id);

        if (!$ordenPago) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ' . $this->getClassName());
        }

        $html = '<html><head><style type="text/css">' . $this->renderView('::PDF/mpdf.default.css.twig') . '</style></head><body>';
        $html .= $this->printHTMLAction($ordenPago);

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPagoComprobante */
        if ($ordenPago->getBeneficiario() != null) {
            if (!$ordenPago->getRetenciones()->isEmpty()) {

                $controllerRetencion = $this->getRetencionesController( $ordenPago->getComprobantes() );
                $controllerRetencion->setContainer($this->container);

                foreach ($ordenPago->getRetenciones() as $retencion) {
                    $html .= '<div style="page-break-before: always;">';
                    $html .= $controllerRetencion->printHTMLAction($retencion);
                    $html .= '</div>';
                }
            }
        }

        $html .= '</body></html>';
        //die($html); exit;
        $filename = 'orden_pago' . $ordenPago->getNumeroOrdenPago() . '.pdf';

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);
        $mpdfService->showImageErrors = true;
        $mpdfService->WriteHTML($html);
        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    /**
     * 
     * @Route("/{id}/historico_general", name="ordenpago_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ordenPago = $em->getRepository($this->getClassName())->find($id);

        $resultArray = [];

        if (!$ordenPago) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ' . $this->getClassName());
        }

        $bread = $this->base_breadcrumbs;

        if ($ordenPago->getNumeroOrdenPago() == null) {
            $bread['Autorizaciones contables'] = $this->generateUrl('autorizacioncontable');
            $bread['Autorizaci&oacute;n contable n&deg; ' . $ordenPago->getNumeroAutorizacionContable()] = null;
            $bread['Hist&oacute;rico general'] = null;

            $page_title = 'Ver hist&oacute;rico general de autorizaci&oacute;n contable';

            $resultArray['title'] = 'Hist&oacute;rico general de autorizaci&oacute;n contable';
        } else {
            $bread['&Oacute;rdenes de pago'] = $this->generateUrl('ordenpago');
            $bread['Orden de pago n&deg; ' . $ordenPago->getNumeroOrdenPago()] = null;
            $bread['Hist&oacute;rico general'] = null;

            $page_title = 'Ver hist&oacute;rico general de orden de pago';

            $resultArray['title'] = 'Hist&oacute;rico general de orden de pago';
        }

        $resultArray['ordenPago'] = $ordenPago;

        $resultArray['breadcrumbs'] = $bread;
        $resultArray['page_title'] = $page_title;

        $resultArray = $this->getHistoricoGeneralResultData($ordenPago, $resultArray);
        
		return $this->getOrdenPagoLogData($ordenPago, $resultArray);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $resultArray
     * @return type
     */
    public function getHistoricoGeneralResultData($ordenPago, $resultArray) {

        return $resultArray;
    }
	
	public function getOrdenPagoLogData($ordenPago, $resultArray)
	{
		if ($ordenPago->getEstaAnulada()) {
			$em = $this->getDoctrine()->getManager($this->getEntityManager());
			$ordenPagoLog = $em->getRepository('ADIFContableBundle:OrdenPagoLog')->findOneByOrdenPago($ordenPago);
			$resultArray['ordenPagoLog'] = null;
			if ($ordenPagoLog) {
				$resultArray['ordenPagoLog'] = $ordenPagoLog;
			}  
		} 
		
		return $resultArray;
	}
	
	/** 
	* Lo mantengo el metodo, para mantener la integridad
	*/
	public function clonar() {}
	
    /**
     * 
     * @param type $ordenPago
     * @param type $emContable
     * @param type $autorizacionContable
     */
    // public function clonar($ordenPago, $emContable, $autorizacionContable) {
        // foreach ($ordenPago->getComprobantes() as $comprobante) {

            // $comprobanteNuevo = clone $comprobante;

            // /* Seteo el asiento contable relacionado a NULL */
            // $comprobanteNuevo->setAsientoContable(null);

            // $comprobanteNuevo->setOrdenPago($autorizacionContable);

            // foreach ($comprobante->getRenglonesPercepcion() as $renglonPercepcion) {

                // $renglonPercepcionNuevo = clone $renglonPercepcion;
                // $renglonPercepcionNuevo->setComprobante($comprobanteNuevo);

                // $emContable->persist($renglonPercepcionNuevo);
            // }

            // foreach ($comprobante->getRenglonesImpuesto() as $renglonImpuesto) {

                // $renglonImpuestoNuevo = clone $renglonImpuesto;
                // $renglonImpuestoNuevo->setComprobante($comprobanteNuevo);

                // $emContable->persist($renglonImpuestoNuevo);
            // }

            // $esComprobanteCompra = $comprobante->getEsComprobanteCompra();

            // foreach ($comprobante->getRenglonesComprobante() as $renglon) {

                // $renglonNuevo = clone $renglon;
                // $renglonNuevo->setComprobante($comprobanteNuevo);

                // // Si el comprobante es de compra / servicio
                // if ($esComprobanteCompra) {

                    // /* @var $renglon \ADIF\ContableBundle\Entity\RenglonComprobanteCompra */

                    // foreach ($renglon->getRenglonComprobanteCompraCentrosDeCosto() as $renglonComprobanteCompraCentrosDeCosto) {

                        // $renglonComprobanteCompraCentrosDeCostoNuevo = clone $renglonComprobanteCompraCentrosDeCosto;

                        // $renglonComprobanteCompraCentrosDeCostoNuevo->setRenglonComprobanteCompra($renglonNuevo);

                        // $renglonNuevo->addRenglonComprobanteCompraCentrosDeCosto($renglonComprobanteCompraCentrosDeCostoNuevo);
                    // }
                // }

                // $emContable->persist($renglonNuevo);
            // }

            // /* Seteo el estado del comprobante a "Anulado" */
            // $comprobante->setEstadoComprobante(
                    // $emContable->getRepository('ADIFContableBundle:EstadoComprobante')
                            // ->find(EstadoComprobante::__ESTADO_ANULADO)
            // );
            // /* Seteo la fecha de anulacion */
            // $comprobante->setFechaAnulacion($ordenPago->getFechaAnulacion());

            // $emContable->persist($comprobanteNuevo);

            // foreach ($comprobante->getPagosParciales() as $pagoParcial) {
                // /* @var $pagoParcial \ADIF\ContableBundle\Entity\PagoParcial */
                // if (!$pagoParcial->getAnulado()) {

                    // $pagoParcialNuevo = clone $pagoParcial;

                    // $pagoParcialNuevo->setComprobante($comprobanteNuevo);

                    // $pagoParcial->setAnulado(true);
                    // $pagoParcialNuevo->setAnulado(false);

                    // $pagoParcial->getOrdenPago()->setPagoParcial($pagoParcialNuevo);

                    // $emContable->persist($pagoParcialNuevo);
                // }
            // }
        // }
        // foreach ($ordenPago->getRetenciones() as $retencion) {
            // $retencionNueva = clone $retencion;
            // $retencionNueva->setOrdenPago($autorizacionContable);
            // $retencion->getRenglonDeclaracionJurada()->setComprobanteRetencionImpuesto($retencionNueva);
            // $retencion->setRenglonDeclaracionJurada(null);
            // $emContable->persist($retencionNueva);
        // }
        // foreach ($ordenPago->getAnticipos() as $anticipo) {
            // $anticipoNuevo = clone $anticipo;
            // $anticipoNuevo->setOrdenPago($autorizacionContable);
            // $emContable->persist($anticipoNuevo);
        // }
    // }
	
	/**
	* Este metodo anula la AC, libera los comprobantes y guarda el el log/historico 
	* de los comprobantes de la AC
	*/
	public function anularYLiberarComprobantes($ordenPago, $autorizacionContable, $em)
	{
		$fechaHoy = new \DateTime();
		
		$ordenPagoLog = new OrdenPagoLog();
		
		$ordenPagoLog->setDescripcion('Anulación OP Nro. ' . $ordenPago->getNumeroOrdenPago());
		
		$ordenPagoLog->setOrdenPago($ordenPago);
		
		$ordenPagoLog->setTotalBruto($ordenPago->getTotalBruto());
		
		$ordenPagoLog->setTotalNeto($ordenPago->getMontoNeto());
		
		$ordenPagoLog->setTotalRetenciones($ordenPago->getMontoRetenciones());
		
		$estadoOrdenPagoAnulada = $em->getRepository('ADIFContableBundle:EstadoOrdenPago')
										->findOneByDenominacionEstado(ConstanteEstadoOrdenPago::ESTADO_ANULADA);
							
		$ordenPagoLog->setEstadoOrdenPago($estadoOrdenPagoAnulada);
		
		if (!$autorizacionContable instanceof OrdenPagoPagoParcial) {
			
			foreach ($ordenPago->getComprobantes() as $comprobante) {
			
				$ordenPagoLog->addComprobante($comprobante);
				
				$comprobante->addOrdenPagoLog($ordenPagoLog);
				
				$comprobante->setOrdenPago($autorizacionContable);
				
				// Le seteo el estado cobrado
				$comprobante->setEstadoComprobante(
						$em->getRepository('ADIFContableBundle:EstadoComprobante')
								->find(EstadoComprobante::__ESTADO_CANCELADO)
				);
				
				$em->persist($comprobante);
				
				$em->persist($ordenPagoLog);
			}
			
		} else {
			
			$comprobante = $autorizacionContable->getPagoParcial()->getComprobante();
			
			$ordenPagoLog->addComprobante($comprobante);
				
			$comprobante->addOrdenPagoLog($ordenPagoLog);
			
			// Le seteo el estado cobrado
			$comprobante->setEstadoComprobante(
					$em->getRepository('ADIFContableBundle:EstadoComprobante')
							->find(EstadoComprobante::__ESTADO_CANCELADO)
			);
			
			$em->persist($comprobante);
				
			$em->persist($ordenPagoLog);
		}
		
		foreach($ordenPago->getRetenciones() as $retencion) {
			/* @var ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto $retencion */
			$retencion->setOrdenPago($autorizacionContable);
			$ordenPagoLog->addRetencion($retencion);
			
			$em->persist($retencion);
			$em->persist($ordenPagoLog);
		}
		
		foreach ($ordenPago->getAnticipos() as $anticipo) {
			/* @var ADIF\ContableBundle\Entity\Anticipo $anticipo */
			$anticipo->setOrdenPagoCancelada($autorizacionContable);
			$ordenPagoLog->addAnticipo($anticipo);
			
			$em->persist($anticipo);
			$em->persist($ordenPagoLog);
        }
	}

    /**
     * 
     * @param type $pago
     * @param type $formaPago
     */
    private function setHistoricoEstadoPago($pago, $formaPago) {

        $estadoPagoHistorico = new \ADIF\ContableBundle\Entity\EstadoPagoHistorico();

        $estadoPagoHistorico->setUsuario($this->getUser());

        if ($formaPago == self::FORMA_PAGO_CHEQUE) {
            $estadoPagoHistorico->setCheque($pago);
        } else {
            $estadoPagoHistorico->setTransferencia($pago);
        }

        $estadoPagoHistorico->setEstadoPago($pago->getEstadoPago());

        $pago->addHistoricoEstado($estadoPagoHistorico);
    }

    /**
     *
     * @Route("/editar_fecha_anulacion/", name="ordenpago_editar_fecha_anulacion")
     */
    public function updateFechaAnulacionAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $numeroAsiento = $request->request->get('numero_asiento');

        $fecha = $request->request->get('fecha');

        $idOrdenPago = $request->request->get('id_orden_pago');

        $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')
                ->findOneByNumeroAsiento($numeroAsiento);

        // Si existe el asiento Y el usuario logueado genera asientos contables
        if (!$asientoContable && false === $this->get('security.context')->isGranted('ROLE_DETACH_ASIENTO')) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
        }

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPago */
        $ordenPago = $em->getRepository('ADIFContableBundle:OrdenPago')->find($idOrdenPago);

        $ordenPago->setFechaAnulacion(\DateTime::createFromFormat('d/m/Y', $fecha));

        $em->persist($ordenPago);

        $em->flush();

        return new Response();
    }
	
	/**
     * Modifica c/u de los items de los montos de retencion
	 * 
     * @Route("/modficar_suss", name="ordenpago_editar_suss")
     */
	public function modificarSUSS(Request $request)
	{
		$idOrdenPago = $request->get('idOrdenPago');
		$idRetencion = $request->get('idRetencion');
		$suss = $request->get('suss');
		
		$suss = str_replace(',', '.', $suss);
		
		//var_dump($idOrdenPago);exit;
		
		$em = $this->getDoctrine()->getManager($this->getEntityManager());
		
		$ordenPago = $em->getRepository('ADIFContableBundle:OrdenPago')->find($idOrdenPago);
		
		$estadoPendienteAutorizacion = $em->getRepository('ADIFContableBundle:EstadoOrdenPago')
			->findOneByDenominacionEstado(ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_AUTORIZACION);
			
		if (!$ordenPago) {
			$res['status'] = 'nok';
			$res['msg'] = 'No se encontro la orden de pago.';
			return new JsonResponse($res);
		}
		
		if ($ordenPago->getEstadoOrdenPago()->getId() != $estadoPendienteAutorizacion->getId()) {
			$res['status'] = 'nok';
			$res['msg'] = 'Para actualizar SUSS, la orden de pago tiene que estar en estado "Pendiente autorización".';
			return new JsonResponse($res);
		}
		
		$comprobanteRetencionImpuesto = $em->getRepository('ADIFContableBundle:ComprobanteRetencionImpuesto')->find($idRetencion);
		
		$res = array();
		
		if (!$comprobanteRetencionImpuesto) {
			$res['status'] = 'nok';
			$res['msg'] = 'No se encontro el comprobante de retencion.';
			return new JsonResponse($res);
		}
		
		try {
		
			$comprobanteRetencionImpuesto->setMonto($suss); 
			
			$em->persist($comprobanteRetencionImpuesto);
			
			$em->flush();
			
			$res['status'] = 'ok';
			
		} catch(Exception $e) {
			
			$res['status'] = 'nok';
			$res['msg'] = $e->getMessage();
		}
		
		return new JsonResponse($res);
		
	}
	
	/**
	* Metodo para overradear con validacion customizadas por cada OP
	* @return boolean
	*/
	public function validacionesCustom($ordenPago = null)
	{
		return true;
	}
}
