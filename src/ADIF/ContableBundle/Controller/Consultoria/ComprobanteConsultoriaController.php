<?php

namespace ADIF\ContableBundle\Controller\Consultoria;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteConsultoria;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Form\Consultoria\ComprobanteConsultoriaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Consultoria\ComprobanteConsultoria controller.
 *
 * @Route("/comprobante_consultoria")
 */
class ComprobanteConsultoriaController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Comprobantes de locaci&oacute;n' => $this->generateUrl('comprobante_consultoria')
        );
    }

    /**
     * Lists all Consultoria\ComprobanteConsultoria entities.
     *
     * @Route("/", name="comprobante_consultoria")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de locaci&oacute;n'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Comprobantes de locaci&oacute;n',
            'page_info' => 'Lista de comprobantes de locaci&oacute;n'
        );
    }

    /**
     * Tabla para Consultoria\ComprobanteConsultoria .
     *
     * @Route("/index_table/", name="comprobante_consultoria_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')->findBy(
                array('ordenPago' => null));

        $comprobantesFiltrados = array_filter($entities, function($comprobante) {
            return !$comprobante->getEsNotaCredito();
        });

        return $this->render('ADIFContableBundle:Consultoria/ComprobanteConsultoria:index_table.html.twig', array(
                    'entities' => $comprobantesFiltrados
                        )
        );
    }

    /**
     * Creates a new Consultoria\ComprobanteConsultoria entity.
     *
     * @Route("/insertar", name="comprobante_consultoria_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Consultoria\ComprobanteConsultoria:new.html.twig")
     */
    public function createAction(Request $request) {
        $tipoComprobante = $request->request->get('adif_contablebundle_comprobanteconsultoria', false)['tipoComprobante'];
        $comprobanteConsultoria = ConstanteTipoComprobanteConsultoria::getSubclass($tipoComprobante);

        $form = $this->createCreateForm($comprobanteConsultoria);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $requestComprobanteConsultoria = $request->request
                    ->get('adif_contablebundle_comprobanteconsultoria');

            $ciclosFacturacionActualizados = $request->request->get('ciclos_facturacion');

            $idContrato = $requestComprobanteConsultoria['idContrato'];

            $contrato = $em->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                    ->find($idContrato);

            // Seteo el Estado
            $comprobanteConsultoria->setEstadoComprobante($em->getRepository('ADIFContableBundle:EstadoComprobante')
                            ->find(EstadoComprobante::__ESTADO_INGRESADO));

            // Seteo el Contrato
            $comprobanteConsultoria->setContrato($contrato);

            // Valido duplicidad del comprobante
            $criteria = array(
                'contrato' => $contrato,
                'fechaComprobante' => $comprobanteConsultoria->getFechaComprobante(),
                'letraComprobante' => $comprobanteConsultoria->getLetraComprobante(),
                'puntoVenta' => $comprobanteConsultoria->getPuntoVenta(),
                'numero' => $comprobanteConsultoria->getNumero(),
                'tipoComprobante' => $comprobanteConsultoria->getTipoComprobante(),
            );

            $comprobantesDuplicadosArray = $em->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')
                    ->validarNumeroComprobanteUnico($criteria);

            // Si no existen comprobantes duplicados
            if (count($comprobantesDuplicadosArray) == 0) {

                $esContraAsiento = false;

                //SI ES NOTA DE CREDITO
                if ($comprobanteConsultoria->getEsNotaCredito()) {
                    $esContraAsiento = true;
                    $observacion = 'Nota de cr&eacute;dito del contrato '
                            . $contrato->getNumeroContrato();
                } else {
                    // Actualizo cantidades pendientes
                    $this->updateCiclosFacturacionPendientes($contrato, $ciclosFacturacionActualizados);
                    $observacion = $contrato->getClaseContrato()
                            . ' - Correspondiente a la cuota '
                            . $contrato->getSiguienteNumeroComprobante()
                            . ' del contrato '
                            . $contrato->getNumeroContrato();
                }

                $arrayPeriodos = [];

                foreach ($requestComprobanteConsultoria['renglonesComprobante'] as $renglon) {
                    $arrayPeriodos[] = $renglon['mes'];
                }

                $periodo = implode('-', array_reverse($arrayPeriodos));

                $comprobanteConsultoria->setPeriodo($periodo);

                // Seteo la observacion
                $comprobanteConsultoria->setObservaciones($observacion);

                // Seteo el saldo
                $comprobanteConsultoria->setSaldo($comprobanteConsultoria->getTotal());

                // Persisto la entidad
                $em->persist($comprobanteConsultoria);


                // Persisto los asientos contables y presupuestarios
                $numeroAsiento = $this->get('adif.asiento_service')
                        ->generarAsientoComprobanteConsultoria($comprobanteConsultoria, $this->getUser(), $esContraAsiento);

                // Si no hubo errores en los asientos
                if ($numeroAsiento != -1) {

                    // Comienzo la transaccion
                    $em->getConnection()->beginTransaction();

                    try {

                        $em->flush();

                        $em->getConnection()->commit();

                        $dataArray = [
                            'data-id-comprobante' => $comprobanteConsultoria->getId()
                        ];

                        $this->get('adif.asiento_service')
                                ->showMensajeFlashAsientoContable($numeroAsiento, $dataArray);

                        return $this->redirect($this->generateUrl('comprobante_consultoria'));
                    } //.
                    catch (\Exception $e) {

                        $em->getConnection()->rollback();
                        $em->close();

                        throw $e;
                    }
                }
            } else {

                $this->get('session')->getFlashBag()->add(
                        'error', 'El n&uacute;mero de comprobante ya se encuentra en uso.'
                );

                $request->attributes->set('form-error', true);
            }
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $comprobanteConsultoria,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de locaci&oacute;n',
        );
    }

    /**
     * Creates a form to create a Consultoria\ComprobanteConsultoria entity.
     *
     * @param ComprobanteConsultoria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ComprobanteConsultoria $entity) {
        $form = $this->createForm(new ComprobanteConsultoriaType(), $entity, array(
            'action' => $this->generateUrl('comprobante_consultoria_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Consultoria\ComprobanteConsultoria entity.
     *
     * @Route("/crear", name="comprobante_consultoria_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new ComprobanteConsultoria();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de locaci&oacute;n'
        );
    }

    /**
     * Finds and displays a Consultoria\ComprobanteConsultoria entity.
     *
     * @Route("/{id}", name="comprobante_consultoria_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultoria\ComprobanteConsultoria.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Comprobante de locaci&oacute;n'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver comprobante de locaci&oacute;n'
        );
    }

    /**
     * Displays a form to edit an existing Consultoria\ComprobanteConsultoria entity.
     *
     * @Route("/editar/{id}", name="comprobante_consultoria_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Consultoria\ComprobanteConsultoria:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $comprobante = $em->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')
                ->find($id);

        if (!$comprobante) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultoria\ComprobanteConsultoria.');
        }

        $editForm = $this->createEditForm($comprobante);

        $editForm->get('idContrato')
                ->setData($comprobante->getContrato()->getId());

        $editForm->get('consultor_razonSocial')
                ->setData($comprobante->getConsultor()->getRazonSocial());

        $editForm->get('consultor_cuit')
                ->setData($comprobante->getConsultor()->getCUIT());

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $comprobante,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar comprobante de locaci&oacute;n'
        );
    }

    /**
     * Creates a form to edit a Consultoria\ComprobanteConsultoria entity.
     *
     * @param ComprobanteConsultoria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ComprobanteConsultoria $entity) {
        $form = $this->createForm(new ComprobanteConsultoriaType(), $entity, array(
            'action' => $this->generateUrl('comprobante_consultoria_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Consultoria\ComprobanteConsultoria entity.
     *
     * @Route("/actualizar/{id}", name="comprobante_consultoria_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Consultoria\ComprobanteConsultoria:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultoria\ComprobanteConsultoria.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('comprobante_consultoria'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar comprobante de locaci&oacute;n'
        );
    }

    /**
     * Deletes a Consultoria\ComprobanteConsultoria entity.
     *
     * @Route("/borrar/{id}", name="comprobante_consultoria_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultoria\ComprobanteConsultoria.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('comprobante_consultoria'));
    }

    /**
     * 
     * @param type $contrato
     * @param type $ciclosFacturacionActualizados
     */
    private function updateCiclosFacturacionPendientes($contrato, $ciclosFacturacionActualizados) {

        foreach ($ciclosFacturacionActualizados as $id => $cantidad) {

            foreach ($contrato->getCiclosFacturacionPendientes() as $cicloFacturacion) {

                if ($id == $cicloFacturacion->getId()) {

                    //Decremento la cantidad de facturas pendientes del ciclo de facturacion
                    $cicloFacturacion->setCantidadFacturasPendientes($cicloFacturacion->getCantidadFacturasPendientes() - $cantidad);

                    break;
                }
            }
        }
    }

    /**
     * Tabla para AnticipoConsultoria
     * .
     *
     * @Route("/index_table_anticipos/", name="comprobante_consultoria_index_table_anticipos")
     * @Method("GET|POST")
     */
    public function indexTableAnticiposAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $comprobantesCreditoArray = [];

        $anticipos = $em->getRepository('ADIFContableBundle:AnticipoContratoConsultoria')->findBy(
                array('ordenPagoCancelada' => null));

        foreach ($anticipos as $anticipo) {

            /* @var $anticipo \ADIF\ContableBundle\Entity\AnticipoContratoConsultoria */

            if ($anticipo->getOrdenPago()->getEstadoOrdenPago()
                            ->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_PAGADA) {

                $comprobantesCreditoArray[] = array(
                    'id' => $anticipo->getId(),
                    'fecha' => $anticipo->getFecha(),
                    'tipo' => 'Anticipo de orden de consultoria',
                    'idTipo' => 1,
                    'consultor' => $anticipo->getConsultor(),
                    'contrato' => $anticipo->getContrato(),
                    'idContratoInicial' => $anticipo->getContrato()->getIdContratoInicial(),
                    'ordenPago' => $anticipo->getOrdenPago(),
                    'monto' => $anticipo->getMonto(),
                    'estadoComprobante' => false,
                    'esAnticipo' => true
                );
            }
        }

        $comprobantes = $em->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')->findBy(
                array('ordenPago' => null));

        $notasCredito = array_filter($comprobantes, function($comprobante) {
            return $comprobante->getEsNotaCredito();
        });

        foreach ($notasCredito as $notaCredito) {

            /* @var $notaCredito \ADIF\ContableBundle\Entity\Consultoria\NotaCreditoConsultoria */

            $tipo = $notaCredito->getTipoComprobante()
                    . ' (' . $notaCredito->getLetraComprobante() . ') - '
                    . $notaCredito->getNumeroCompleto();

            $comprobantesCreditoArray[] = array(
                'id' => $notaCredito->getId(),
                'fecha' => $notaCredito->getFechaComprobante(),
                'tipo' => $tipo,
                'idTipo' => 2,
                'consultor' => $notaCredito->getConsultor(),
                'contrato' => $notaCredito->getContrato(),
                'idContratoInicial' => $notaCredito->getContrato()->getIdContratoInicial(),
                'ordenPago' => $notaCredito->getOrdenPago(),
                'monto' => $notaCredito->getTotal(),
                'estadoComprobante' => $notaCredito->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO,
                'esAnticipo' => false
            );
        }


        return $this->render('ADIFContableBundle:Consultoria\ComprobanteConsultoria:index_table_anticipos.html.twig', array(
                    'entities' => $comprobantesCreditoArray,
                        )
        );
    }

    /**
     * Anula el comprobante
     *
     * @Route("/anular/{id}", name="comprobante_consultoria_anular")
     * @Method("GET")
     */
    public function anularComprobanteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        /* @var $entity ComprobanteConsultoria */
        $entity = $em->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteConsultoria.');
        }

        $estadoAnulado = $em->getRepository('ADIFContableBundle:EstadoComprobante')->find(EstadoComprobante::__ESTADO_ANULADO);

        //$fechaContable = $entity->getFechaContable();
		$fecha_hoy = new \DateTime();
        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')->getEjercicioContableByFecha($fecha_hoy);
        

        if ($ejercicioContable->getEstaCerrado() || !$ejercicioContable->getMesEjercicioHabilitado($fecha_hoy->format('m'))) {
            $this->get('session')->getFlashBag()->add('error', 'El ejercicio contable est&aacute; cerrado o el mes correspondiente a la fecha contable del comprobante no est&aacute; habilitado');
        } else {

            //$fecha_anulacion = $fecha_hoy->format('Ym') == $fecha_hoy->format('Ym') ? $fecha_hoy : $fechaContable;

            $entity->setEstadoComprobante($estadoAnulado);
            $entity->setFechaAnulacion($fecha_hoy);

            $esNotaCredito = $entity->getEsNotaCredito();
            $esContraAsiento = !$esNotaCredito;

            $consultor = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($entity->getContrato()->getIdConsultor());

            $monotributista = $consultor->getDatosImpositivos()->getCondicionIVA()->getDenominacionTipoResponsable() == ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO;

            //SI ES NOTA DE CREDITO
            if (!$esNotaCredito) {
                // Actualizo cantidades pendientes
                if (($entity->getTipoComprobante()->getId() == ConstanteTipoComprobanteConsultoria::FACTURA) //
                        || (($entity->getTipoComprobante()->getId() == ConstanteTipoComprobanteConsultoria::RECIBO)//
                        && (($entity->getLetraComprobante()->getLetra() == ConstanteLetraComprobante::C && $monotributista)//
                        || ((!$monotributista) && ( ($entity->getLetraComprobante()->getLetra() == ConstanteLetraComprobante::A) || ($entity->getLetraComprobante()->getLetra() == ConstanteLetraComprobante::B) ))))) {
                    /* @var $renglonConsultoria \ADIF\ContableBundle\Entity\Consultoria\RenglonComprobanteConsultoria */
                    foreach ($entity->getRenglonesComprobante() as $renglonConsultoria) {
                        $renglonConsultoria->getCicloFacturacion()->setCantidadFacturasPendientes($renglonConsultoria->getCicloFacturacion()->getCantidadFacturasPendientes() + 1);
                    }
                }
            }

            // Persisto los asientos contables y presupuestarios
            $numeroAsiento = $this->get('adif.asiento_service')
                    ->generarAsientoComprobanteConsultoria($entity, $this->getUser(), $esContraAsiento, $fecha_hoy);

            // Si no hubo errores en los asientos
            if ($numeroAsiento != -1) {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {

                    $em->flush();

                    $em->getConnection()->commit();

                    $this->get('session')->getFlashBag()->add('success', 'El comprobante fue anulado');

                    $dataArray = [
                        'data-id-comprobante' => $entity->getId(),
                        'data-fecha-asiento' => $fecha_hoy->format('d/m/Y'),
                        'data-es-anulacion' => 1
                    ];

                    $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($numeroAsiento, $dataArray);

                    return $this->redirect($this->generateUrl('comprobante_consultoria'));
                } //.
                catch (\Exception $e) {

                    $em->getConnection()->rollback();
                    $em->close();
                    $this->get('session')->getFlashBag()->add('error', 'El comprobante no se pudo anular');

                    throw $e;
                }
            }
        }

        return $this->redirect($this->generateUrl('comprobante_consultoria'));
    }

    /**
     * @Route("/generarAsientos/", name="comprobante_consultoria_asientos")
     * @Method("PUT|GET")     
     */
    public function generarAsientosComprobantesConsultoria() {

//        gc_enable();
//
//        $parcial = false;
//
//        $offset = 0;
//        $limit = 20;
//        $i = 1;
//
//        $em = $this->getDoctrine()->getManager($this->getEntityManager());
//        $comprobantesImportados = $em->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')
//                ->createQueryBuilder('cc')
//                ->where('cc.fechaContable >= :fecha')
//                ->setFirstResult($offset)
//                ->setMaxResults($limit)
//                ->setParameter('fecha', '2015-08-01 00:00:00')
//                ->getQuery()
//                ->getResult();
//
//        $offset = $limit * $i;
//        $i++;
//        while (count($comprobantesImportados) > 0) {
//
//            foreach ($comprobantesImportados as $comprobanteImportado) {
//                // Genero el definitivo asociado
//                $this->get('adif.asiento_service')->generarAsientoComprobanteConsultoria($comprobanteImportado, $this->getUser(), $comprobanteImportado->getEsNotaCredito());
//            }
//            unset($comprobantesImportados);
//            $em->flush();
//            $em->clear();
//            gc_collect_cycles();
//            $comprobantesImportados = $em->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')
//                    ->createQueryBuilder('cc')
//                    ->where('cc.fechaContable >= :fecha')
//                    ->setFirstResult($offset)
//                    ->setMaxResults($limit)
//                    ->setParameter('fecha', '2015-08-01 00:00:00')
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
//            $this->get('session')->getFlashBag()->add('success', 'Generacion de asientos de Comprobantes de Consultoria exitosa');
//        }

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $comprobanteConsultoria = $em->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')->find(34850);
        $this->get('adif.asiento_service')->generarAsientoComprobanteConsultoria($comprobanteConsultoria, $this->getUser(), $comprobanteConsultoria->getEsNotaCredito());
        $em->flush();
        $em->clear();

        return $this->redirect($this->generateUrl('comprobante_consultoria'));
    }

}
