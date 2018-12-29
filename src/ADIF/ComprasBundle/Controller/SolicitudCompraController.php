<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\ComprasBundle\Entity\SolicitudCompra;
use ADIF\ComprasBundle\Entity\RenglonSolicitudCompra;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoSolicitud;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoRenglonSolicitud;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoRenglonPedidoInterno;
use ADIF\ComprasBundle\Entity\HistoricoSolicitudCompra;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Form\SolicitudCompraType;
use mPDF;
use ADIF\BaseBundle\Session\EmpresaSession;

/**
 * SolicitudCompra controller.
 *
 * @Security("has_role('ROLE_USER')")
 * @Route("/solicitudcompra")
 */
class SolicitudCompraController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Solicitudes de compra' => $this->generateUrl('solicitudcompra')
        );
    }

    /**
     * Lists all SolicitudCompra entities.
     *
     * @Route("/", name="solicitudcompra")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $securityContext = $this->get('security.context');

        // Si el usuario logueado puede administrar PedidosInternos 
        $puedeAdministrarPedidos = $securityContext->isGranted('ROLE_COMPRAS_ADMINISTRA_PEDIDO_INTERNO');

        // Si el usuario logueado puede enviar las SolicitudCompra a autorizar
        $puedeEnviarSolicitudes = $securityContext->isGranted('ROLE_COMPRAS_ENVIO_SOLICITUD');

        // Si el usuario logueado puede visar las SolicitudCompra
        $puedeVisar = $securityContext->isGranted('ROLE_COMPRAS_VISAR_SOLICITUD');

        // Si el usuario logueado pertenece a una EntidadAutorizante
        $esEntidadAutorizante = $securityContext->isGranted('ROLE_COMPRAS_ENTIDAD_AUTORIZANTE');

        $tiposCompra = $this->getTiposSolicitudCompra();

        $estados = $this->getEstadosSolicitudCompra();

        $bread = $this->base_breadcrumbs;
        $bread['Solicitudes de compra'] = null;

        $returnArray = array(
            'puede_administrar_pedidos' => $puedeAdministrarPedidos,
            'es_entidad_autorizante' => $esEntidadAutorizante,
            'puede_enviar_solicitudes' => $puedeEnviarSolicitudes,
            'puede_visar' => $puedeVisar,
            'tiposcompra' => $tiposCompra,
            'estados' => $estados,
            'breadcrumbs' => $bread,
            'page_title' => 'Solicitud de compra',
            'page_info' => 'Lista de solicitudes'
        );

        if (true === $securityContext->isGranted('ROLE_COMPRAS_PANEL_CONTROL')) {

            $usuario = $this->getUser();

            $returnArray['cantidad_solicitudes_pendientes'] = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')
                    ->getCantidadRenglonesSolicitudCompraSupervisados();

            $returnArray['cantidad_requerimientos_pendientes'] = $em->getRepository('ADIFComprasBundle:Requerimiento')
                    ->getCantidadRequerimientosPendientesCotizacionByUsuario($usuario->getId());

            $returnArray['cantidad_oc_pendientes'] = $em->getRepository('ADIFComprasBundle:OrdenCompra')
                    ->getCantidadOrdenesCompraPendientes();
        }

        return $returnArray;
    }

    /**
     * Tabla para la solapa "Pedidos"
     *
     * @Route("/index_table_renglon_pedido_interno/", name="solicitudcompra_index_table_renglon_pedido_interno")
     * @Method("GET|POST")
     */
    public function indexTableRenglonPedidoInternoAction() {

        $securityContext = $this->get('security.context');

        $renglonesPedidoParaUsuario = array();

        $puedeAdministrarPedidos = false;

        // Si el usuario logueado puede visar las SolicitudCompra       
        $puedeVisar = $securityContext->isGranted('ROLE_COMPRAS_VISAR_SOLICITUD');

        // Si el usuario logueado pertenece a una EntidadAutorizante
        $esEntidadAutorizante = $securityContext->isGranted('ROLE_COMPRAS_ENTIDAD_AUTORIZANTE');

        // Si el usuario logueado puede administrar PedidosInternos 
        if (true === $securityContext->isGranted('ROLE_COMPRAS_ADMINISTRA_PEDIDO_INTERNO')) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $puedeAdministrarPedidos = true;

            $idArea = $this->getUser()->getArea()->getId();

            $renglonesPedidoParaUsuario = $em->getRepository('ADIFComprasBundle:RenglonPedidoInterno')->
                    getRenglonPedidoInternoByArea($idArea);
        }

        return $this->render('ADIFComprasBundle:SolicitudCompra:index_table_renglones_pedidos.html.twig', array(
                    'renglonesPedidoInterno' => $renglonesPedidoParaUsuario,
                    'tipo' => 'enviadas',
                    'puede_visar' => $puedeVisar,
                    'es_entidad_autorizante' => $esEntidadAutorizante
        ));
    }

    /**
     * Tabla para SolicitudCompra.
     *
     * @Route("/index_table/", name="solicitudcompra_index_table")
     * @Method("GET|POST")
     */
    public function indexTableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $tipo = $request->query->get('tipo');

        $securityContext = $this->get('security.context');

        // Si el usuario logueado puede visar las SolicitudCompra       
        $puedeVisar = $securityContext->isGranted('ROLE_COMPRAS_VISAR_SOLICITUD');

        // Si el usuario logueado pertenece a una EntidadAutorizante
        $esEntidadAutorizante = $securityContext->isGranted('ROLE_COMPRAS_ENTIDAD_AUTORIZANTE');

        // Si el usuario logueado puede enviar las SolicitudCompra a autorizar
        $puedeEnviarSolicitudes = $securityContext->isGranted('ROLE_COMPRAS_ENVIO_SOLICITUD');
        
        $idUsuario = $this->getUser()->getId();
        
        $solicitudesDelArea = $em->getRepository('ADIFComprasBundle:SolicitudCompra')
                    ->getSolicitudesCompra($idUsuario);

        if ($tipo == 'enviadas') {
    
            return $this->render('ADIFComprasBundle:SolicitudCompra:index_table_solicitudes.html.twig', array(
                        'solicitudes' => $solicitudesDelArea,
                        'tipo' => 'enviadas',
                        'puede_visar' => $puedeVisar,
                        'es_entidad_autorizante' => $esEntidadAutorizante
            ));
        } //.        
        elseif ($tipo == 'pendientes') {

            $solicitudesParaAutorizar = array();
            $solicitudesParaEnviar = array();
            $solicitudesParaVisar = array();
            $solicitudesEntidadAutorizantePendientes = array();

            // Si el usuario puede enviar o visar solicitudes, o es Entidad Autorizante
            if ($puedeEnviarSolicitudes || $puedeVisar || $esEntidadAutorizante) {

                // Si el usuario logueado puede enviar las SolicitudCompra a autorizar
                if ($puedeEnviarSolicitudes) {

                    // Elimino aquellas SolicitudCompra con estado == "Borrador"
                    $solicitudesDelAreaSinBorrador = array_filter($solicitudesDelArea, function($solicitud) {

                        if ($solicitud['estado'] == ConstanteEstadoSolicitud::ESTADO_SOLICITUD_BORRADOR) {
                            return false;
                        }
                        return true;
                    });


                    // Elimino aquellas SolicitudCompra con estado != "Pendiente Envio"
                    $solicitudesParaEnviar = array_filter($solicitudesDelAreaSinBorrador, function($solicitud) {

                        if ($solicitud['estado'] != ConstanteEstadoSolicitud::ESTADO_SOLICITUD_PENDIENTE_ENVIO) {
                            return false;
                        }
                        return true;
                    });
                }

                // Si el usuario puede visar las Soliciutdes
                if ($puedeVisar) {

                    // Obtengo todas las Solicitudes con estado == Aprobada
                    $solicitudesParaVisar = $em->getRepository('ADIFComprasBundle:SolicitudCompra')
                            ->getSolicitudesCompraAprobadas();
                }

                // Si el usuario es una Entidad Autorizante
                if ($esEntidadAutorizante) {

                    // Obtengo las SolicitudCompra asociadas a la EA logueada cuyo estado == "Pendiente Autorización"
                    $solicitudesEntidadAutorizantePendientes = $em->getRepository('ADIFComprasBundle:SolicitudCompra')
                        ->getSolicitudesCompraByEntidadAutorizante($idUsuario);
                }

                $arrayMergeados = array_merge(array_merge($solicitudesParaEnviar, $solicitudesParaVisar), $solicitudesEntidadAutorizantePendientes);
                $solicitudesParaAutorizar = new ArrayCollection(array_unique($arrayMergeados, SORT_REGULAR));
            }

            return $this->render('ADIFComprasBundle:SolicitudCompra:index_table_solicitudes.html.twig', array(
                        'solicitudes' => $solicitudesParaAutorizar,
                        'tipo' => 'pendientes',
                        'puede_visar' => $puedeVisar,
                        'es_entidad_autorizante' => $esEntidadAutorizante
            ));
        } //.
        elseif ($tipo == 'todas') {

            $todasSolicitudes = array();
            $solicitudesDelAreaSinBorrador = array();
            $todasSolicitudesSupervisor = array();
            $solicitudesNoBorradorEntidadAutorizante = array();

            // Si el usuario puede enviar o visar solicitudes, o es Entidad Autorizante
            if ($puedeEnviarSolicitudes || $puedeVisar || $esEntidadAutorizante) {

                // Si el usuario logueado puede enviar las SolicitudCompra a autorizar
                if ($puedeEnviarSolicitudes) {

                    // Elimino aquellas SolicitudCompra con estado == "Borrador"
                    $solicitudesDelAreaSinBorrador = array_filter($solicitudesDelArea, function($solicitud) {

                        if ($solicitud['estado'] == ConstanteEstadoSolicitud::ESTADO_SOLICITUD_BORRADOR) {
                            return false;
                        }
                        return true;
                    });
                }

                // Si el usuario puede visar las Soliciutdes
                if ($puedeVisar) {

                    // Obtengo todas las Solicitudes con estado != Borrador
                    $todasSolicitudesSupervisor = $em->getRepository('ADIFComprasBundle:SolicitudCompra')
                            ->getSolicitudesCompraNoBorrador();
                }

                // Si el usuario es una Entidad Autorizante
                if ($esEntidadAutorizante) {

                    // Elimino aquellas SolicitudCompra con estado == "Borrador"
                    $solicitudesNoBorradorEntidadAutorizante = $em->getRepository('ADIFComprasBundle:SolicitudCompra')
                        ->getSolicitudesCompraByEntidadAutorizante($idUsuario, false);
                }

                $arrayMergeados = array_merge(array_merge($solicitudesDelAreaSinBorrador, $todasSolicitudesSupervisor), $solicitudesNoBorradorEntidadAutorizante);
                $todasSolicitudes = new ArrayCollection(array_unique($arrayMergeados, SORT_REGULAR));
                
                return $this->render('ADIFComprasBundle:SolicitudCompra:index_table_solicitudes.html.twig', array(
                            'solicitudes' => $todasSolicitudes,
                            'tipo' => 'todas',
                            'puede_visar' => $puedeVisar,
                            'es_entidad_autorizante' => $esEntidadAutorizante
                ));
            }
        }
    }

    /**
     * Creates a new SolicitudCompra entity.
     *
     * @Route("/insertar", name="solicitudcompra_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:SolicitudCompra:new.html.twig")
     */
    public function createAction(Request $request) {

        $solicitudCompra = new SolicitudCompra();

        $form = $this->createCreateForm($solicitudCompra);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // A la SolicitudCompra le seteo el Usuario
            $solicitudCompra->setUsuario($this->getUser());

            // A la SolicitudCompra le seteo la Justificacion
            $this->setJustificacionASolicitudCompra($solicitudCompra);

            // Inicializo los renglones de la SolicitudCompra
            $this->initRenglonesSolicitudCompra($request, $solicitudCompra);

            // Inicializo  la SolicitudCompra
            $this->initSolicitudCompra($request, $solicitudCompra);

            // Actualizo la cantidad pendiente de los pedidos internos relacionados
            $this->actualizarCantidadPendientePedidoInterno($solicitudCompra);

            $em->persist($solicitudCompra);
            $em->flush();

            return $this->redirect($this->generateUrl('solicitudcompra'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $solicitudCompra,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear solicitud de compra',
        );
    }

    /**
     * Creates a form to create a SolicitudCompra entity.
     *
     * @param SolicitudCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SolicitudCompra $entity) {

        $form = $this->createForm(new SolicitudCompraType($this->get('security.context')), $entity, array(
            'action' => $this->generateUrl('solicitudcompra_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form
                ->add('save', 'submit', array(
                    'label' => 'Guardar borrador'
                ))
                ->add('close', 'submit', array(
                    'label' => 'Finalizar solicitud'
                ))
        ;

        return $form;
    }

    /**
     * Displays a form to create a new SolicitudCompra entity.
     *
     * @Route("/crear", name="solicitudcompra_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {

        $solicitudCompra = new SolicitudCompra();

        $form = $this->createCreateForm($solicitudCompra);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $solicitudCompra,
            'incluyePedidos' => false,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear solicitud de compra'
        );
    }

    /**
     * Finds and displays a SolicitudCompra entity.
     *
     * @Route("/{id}", name="solicitudcompra_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $securityContext = $this->get('security.context');

        $entity = $em->getRepository('ADIFComprasBundle:SolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad SolicitudCompra.');
        }

        $puedeEnviarSolicitudes = false;
        $puedeVisar = false;
        $esEntidadAutorizante = false;

        // Si el usuario logueado puede enviar las SolicitudCompra a autorizar
        if (true === $securityContext->isGranted('ROLE_COMPRAS_ENVIO_SOLICITUD')) {
            $puedeEnviarSolicitudes = true;
        }

        // Si el usuario logueado puede visar las SolicitudCompra
        if (true === $securityContext->isGranted('ROLE_COMPRAS_VISAR_SOLICITUD')) {
            $puedeVisar = true;
        }

        // Si el usuario logueado pertenece a una EntidadAutorizante
        if (true === $securityContext->isGranted('ROLE_COMPRAS_ENTIDAD_AUTORIZANTE')) {
            $esEntidadAutorizante = true;
        }


        $observacionForm = $this->createFormBuilder()
                ->add('observacion', 'text', array(
                    'required' => true,
                    'label' => 'Observación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')
                ))
                ->add('save', 'submit', array(
                    'label' => 'Desaprobar solicitud'
                ))
                ->getForm();

        $observacionForm->bind($this->getRequest());

        $bread = $this->base_breadcrumbs;
        $bread['Solicitud ' . $entity->getNumero()] = null;

        return array(
            'entity' => $entity,
            'puede_enviar_solicitudes' => $puedeEnviarSolicitudes,
            'puede_visar' => $puedeVisar,
            'es_entidad_autorizante' => $esEntidadAutorizante,
            'form' => $observacionForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Ver solicitud de compra'
        );
    }

    /**
     * Finds and displays a HistoricoSolicitudCompra entity.
     *
     * @Route("/{id}/historico", name="solicitudcompra_historico")
     * @Method("GET")
     * @Template("ADIFComprasBundle:SolicitudCompra:historico.html.twig")
     */
    public function showHistoricoAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $solicitudCompra = $em->getRepository('ADIFComprasBundle:SolicitudCompra')->find($id);

        if (!$solicitudCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad SolicitudCompra.');
        }

        // Desactivo el filtro para EstadoSolicitudCompra
        $filter = $em->getFilters()->enable('softdeleteable');
        $filter->disableForEntity('ADIF\ComprasBundle\Entity\EstadoSolicitudCompra');

        // Obtengo la lista de HistoricoSolicitudCompra de la SolicitudCompra
        $historicos = $em->getRepository('ADIFComprasBundle:HistoricoSolicitudCompra')->
                findBy(
                array('solicitudCompra' => $solicitudCompra), //
                array('fechaCambioEstado' => 'desc', 'estadoSolicitudCompra' => 'desc')
        );

        $bread = $this->base_breadcrumbs;
        $bread['Solicitud ' . $solicitudCompra->getNumero()] = $this->generateUrl('solicitudcompra_show', array('id' => $solicitudCompra->getId()));
        $bread['Hist&oacute;rico'] = null;

        return array(
            'entity' => $solicitudCompra,
            'historicos' => $historicos,
            'breadcrumbs' => $bread,
            'page_title' => 'Hist&oacute;rico de solicitud de compra'
        );
    }

    /**
     * Displays a form to edit an existing SolicitudCompra entity.
     *
     * @Route("/editar/{id}", name="solicitudcompra_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:SolicitudCompra:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:SolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad SolicitudCompra.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Solicitud ' . $entity->getNumero()] = $this->generateUrl('solicitudcompra_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar solicitud de compra'
        );
    }

    /**
     * Creates a form to edit a SolicitudCompra entity.
     *
     * @param SolicitudCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(SolicitudCompra $entity) {

        $form = $this->createForm(new SolicitudCompraType($this->get('security.context')), $entity, array(
            'action' => $this->generateUrl('solicitudcompra_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form
                ->add('save', 'submit', array(
                    'label' => 'Guardar borrador'
                ))
                ->add('close', 'submit', array(
                    'label' => 'Finalizar solicitud'
                ))
        ;

        return $form;
    }

    /**
     * Edits an existing SolicitudCompra entity.
     *
     * @Route("/actualizar/{id}", name="solicitudcompra_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:SolicitudCompra:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $solicitudCompra = $em->getRepository('ADIFComprasBundle:SolicitudCompra')->find($id);

        if (!$solicitudCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad SolicitudCompra.');
        }

        $renglonesOriginales = new ArrayCollection();

        // Creo un ArrayCollection de los RenglonSolicitudCompra actuales en la BBDD
        foreach ($solicitudCompra->getRenglonesSolicitudCompra() as $renglonSolicitudCompra) {
            $renglonesOriginales->add($renglonSolicitudCompra);
        }

        $editForm = $this->createEditForm($solicitudCompra);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // A la SolicitudCompra le seteo la Justificacion
            $this->setJustificacionASolicitudCompra($solicitudCompra);

            // Inicializo los renglones de la SolicitudCompra
            $this->initRenglonesSolicitudCompra($request, $solicitudCompra);

            // Inicializo  la SolicitudCompra
            $this->initSolicitudCompra($request, $solicitudCompra);

            // Por cada RenglonSolicitudCompra original
            foreach ($renglonesOriginales as $renglonSolicitudCompra) {

                // Si fue eliminado
                if (false === $solicitudCompra->getRenglonesSolicitudCompra()->contains($renglonSolicitudCompra)) {

                    $solicitudCompra->removeRenglonesSolicitudCompra($renglonSolicitudCompra);

                    $em->remove($renglonSolicitudCompra);
                }
            }

            $em->flush();

            return $this->redirect($this->generateUrl('solicitudcompra'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Solicitud ' . $solicitudCompra->getNumero()] = $this->generateUrl('solicitudcompra_show', array('id' => $solicitudCompra->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $solicitudCompra,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar solicitud de compra'
        );
    }

    /**
     * Deletes a SolicitudCompra entity.
     *
     * @Route("/borrar/{id}", name="solicitudcompra_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:SolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad SolicitudCompra.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('solicitudcompra'));
    }

    /**
     * Setea el EstadoSolicitudCompra a "Supervisado"
     *
     * @Route("/visar/{id}", name="solicitudcompra_visar")
     */
    public function visarSolicitudCompraAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $solicitudCompra = $em->getRepository('ADIFComprasBundle:SolicitudCompra')->find($id);

        if (!$solicitudCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad SolicitudCompra.');
        }


        // Obtengo el EstadoSolicitudCompra cuya denominacion sea igual a "Supervisada"
        $estadoSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoSolicitudCompra' => ConstanteEstadoSolicitud::ESTADO_SOLICITUD_SUPERVISADA), //
                array('id' => 'desc'), 1, 0)
        ;

        $solicitudCompra->setEstadoSolicitudCompra($estadoSolicitudCompra);


        // Obtengo el EstadoRenglonSolicitudCompra cuya denominación sea igual a "Supervisado"
        $estadoRenglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_SUPERVISADO), //
                array('id' => 'desc'), 1, 0)
        ;

        // A cada renglón le asigno el estado "Supervisado"
        foreach ($solicitudCompra->getRenglonesSolicitudCompra() as $renglonSolicitudCompra) {
            $renglonSolicitudCompra->setEstadoRenglonSolicitudCompra($estadoRenglonSolicitudCompra);
        }


        // Creo un HistoricoSolicitudCompra con el nuevo EstadoSolicitudCompra
        $this->saveHistoricoSolicitudCompra($solicitudCompra, $estadoSolicitudCompra);


        $em->persist($solicitudCompra);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'La solicitud fue visada correctamente.');

        return $this->redirect($this->generateUrl('solicitudcompra'));
    }

    /**
     * Aprueba un RenglonSolicitudCompra de una SolicitudCompra.
     *
     * @Route("/aprobarrenglon/{id}", name="solicitudcompra_aprobar_renglon")
     */
    public function aprobarRenglonAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $renglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')->find($id);

        if (!$renglonSolicitudCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonSolicitudCompra.');
        }

        // Obtengo el EstadoSolicitudCompra cuya denominacion sea igual a "Aprobado"
        $estadoRenglon = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_APROBADO), //
                array('id' => 'desc'), 1, 0)
        ;

        $renglonSolicitudCompra->setEstadoRenglonSolicitudCompra($estadoRenglon);

        $em->persist($renglonSolicitudCompra);
        $em->flush();

        return new Response();
    }

    /**
     * Desaprueba un RenglonSolicitudCompra de una SolicitudCompra.
     *
     * @Route("/desaprobarrenglon/{id}", name="solicitudcompra_desaprobar_renglon")
     */
    public function desaprobarRenglonAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $renglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')->find($id);

        if (!$renglonSolicitudCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonSolicitudCompra.');
        }

        // Obtengo el EstadoRenglonSolicitudCompra cuya denominacion sea igual a "Desaprobado"
        $estadoRenglon = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_DESAPROBADO), //
                array('id' => 'desc'), 1, 0)
        ;

        $renglonSolicitudCompra->setEstadoRenglonSolicitudCompra($estadoRenglon);

        $em->persist($renglonSolicitudCompra);
        $em->flush();

        return new Response();
    }

    /**
     * 
     * @param type $request
     * @param type $solicitudCompra
     */
    private function setEstadoSolicitudCompraASolicitudCompra($request, $solicitudCompra, $estadoSolicitudCompra = null) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if (null == $solicitudCompra->getEstadoSolicitudCompra()) {

            // Creo un HistoricoSolicitudCompra con el EstadoSolicitudCompra "Creado"
            $this->saveHistoricoSolicitudCompra($solicitudCompra);
        }

        if (null != $request) {

            $accion = $request->request->get('accion');

            if (null != $accion) {

                // Si se apretó el boton "Guardar Borrador"
                if ('save' == $accion) {

                    // Obtengo el EstadoSolicitudCompra cuya denominacion sea igual a "Borrador"
                    $estadoSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->
                            findOneBy(
                            array('denominacionEstadoSolicitudCompra' => ConstanteEstadoSolicitud::ESTADO_SOLICITUD_BORRADOR), //
                            array('id' => 'desc'), 1, 0)
                    ;

                    $solicitudCompra->setEstadoSolicitudCompra($estadoSolicitudCompra);
                }

                // Si se apretó el boton "Finalizar Solicitud"
                else if ('close' == $accion) {

                    // Si el usuario puede enviar a aprobar las solicitudes
                    if (true == $this->get('security.context')->isGranted('ROLE_COMPRAS_ENVIO_SOLICITUD')) {

                        // Obtengo el EstadoRenglonSolicitudCompra == "Pendiente Autorizacion"
                        $estadoSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->
                                findOneBy(
                                array('denominacionEstadoSolicitudCompra' => ConstanteEstadoSolicitud::ESTADO_SOLICITUD_PENDIENTE_AUTORIZACION), //
                                array('id' => 'desc'), 1, 0)
                        ;
                    } else {

                        // Obtengo el EstadoSolicitudCompra cuya denominacion sea igual a "Pendiente Envío"
                        $estadoSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->
                                findOneBy(
                                array('denominacionEstadoSolicitudCompra' => ConstanteEstadoSolicitud::ESTADO_SOLICITUD_PENDIENTE_ENVIO), //
                                array('id' => 'desc'), 1, 0)
                        ;
                    }
                    $solicitudCompra->setEstadoSolicitudCompra($estadoSolicitudCompra);
                }
            }
        }

        // Creo un HistoricoSolicitudCompra con el nuevo EstadoSolicitudCompra
        $this->saveHistoricoSolicitudCompra($solicitudCompra, $estadoSolicitudCompra);
    }

    /**
     * 
     * @param type $solicitudCompra
     */
    private function setJustificacionASolicitudCompra($solicitudCompra) {

        if (null != $solicitudCompra->getJustificacion() && //
                null != $solicitudCompra->getJustificacion()->getArchivo()) {

            $solicitudCompra->getJustificacion()
                    ->setNombre($solicitudCompra->getJustificacion()->getArchivo()->getClientOriginalName());

            $solicitudCompra->getJustificacion()->setSolicitudCompra($solicitudCompra);
        }
    }

    /**
     * 
     * @param type $solicitudCompra
     */
    private function setEntidadAutorizanteASolicitudCompra($solicitudCompra) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entidadAutorizante = $em->getRepository('ADIFComprasBundle:EntidadAutorizante')->
                getEntidadAutorizanteByMonto($solicitudCompra->getJustiprecio());

        $solicitudCompra->setEntidadAutorizante($entidadAutorizante);
    }

    /**
     * 
     * @param type $solicitudCompra
     * @param type $estadoSolicitudCompra
     */
    private function saveHistoricoSolicitudCompra(SolicitudCompra $solicitudCompra, $estadoSolicitudCompra = null) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $historicoSolicitudCompra = new HistoricoSolicitudCompra();

        $historicoSolicitudCompra->setSolicitudCompra($solicitudCompra);
        $historicoSolicitudCompra->setFechaCambioEstado(new \DateTime());
        $historicoSolicitudCompra->setUsuario($this->getUser());

        if (null == $estadoSolicitudCompra) {

            // Obtengo el EstadoSolicitudCompra cuya denominacion sea igual a "Creado"
            $estadoSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->
                    findOneBy(
                    array('denominacionEstadoSolicitudCompra' => ConstanteEstadoSolicitud::ESTADO_SOLICITUD_CREADO), //
                    array('id' => 'desc'), 1, 0)
            ;
        }

        $historicoSolicitudCompra->setEstadoSolicitudCompra($estadoSolicitudCompra);

        // Creado
        if (ConstanteEstadoSolicitud::ESTADO_SOLICITUD_CREADO == $estadoSolicitudCompra->getDenominacionEstadoSolicitudCompra()) {
            $area = $solicitudCompra->getUsuario()->getArea();
            $historicoSolicitudCompra->setDescripcion("Solicitud de compra generada por el &aacute;rea $area.");
        }

        // Borrador
        elseif (ConstanteEstadoSolicitud::ESTADO_SOLICITUD_BORRADOR == $estadoSolicitudCompra->getDenominacionEstadoSolicitudCompra()) {
            $historicoSolicitudCompra->setDescripcion("Solicitud no enviada, guardada en estado borrador.");
        }

        // Pendiente Envío
        elseif (ConstanteEstadoSolicitud::ESTADO_SOLICITUD_PENDIENTE_ENVIO == $estadoSolicitudCompra->getDenominacionEstadoSolicitudCompra()) {
            $area = $solicitudCompra->getUsuario()->getArea();
            $historicoSolicitudCompra->setDescripcion("Solicitud pendiente de envío por el encargado del &aacute;rea $area.");
        }

        // A Corregir
        elseif (ConstanteEstadoSolicitud::ESTADO_SOLICITUD_A_CORREGIR == $estadoSolicitudCompra->getDenominacionEstadoSolicitudCompra()) {
            $historicoSolicitudCompra->setDescripcion("Solicitud no enviada, debe ser corregida por el usuario.");
        }

        // Anulada
        elseif (ConstanteEstadoSolicitud::ESTADO_SOLICITUD_ANULADA == $estadoSolicitudCompra->getDenominacionEstadoSolicitudCompra()) {
            $historicoSolicitudCompra->setDescripcion("Solicitud anulada.");
        }

        // Pendiente Autorización
        elseif (ConstanteEstadoSolicitud::ESTADO_SOLICITUD_PENDIENTE_AUTORIZACION == $estadoSolicitudCompra->getDenominacionEstadoSolicitudCompra()) {
            $entidadAutorizante = $solicitudCompra->getEntidadAutorizante();
            $historicoSolicitudCompra->setDescripcion("Solicitud pendiente de aprobaci&oacute;n por la entidad autorizante $entidadAutorizante.");
        }

        // Desaprobada
        elseif (ConstanteEstadoSolicitud::ESTADO_SOLICITUD_DESAPROBADA == $estadoSolicitudCompra->getDenominacionEstadoSolicitudCompra()) {
            $motivoDesaprobacion = $solicitudCompra->getObservacion();
            $historicoSolicitudCompra->setDescripcion("Motivo: $motivoDesaprobacion.");
        }

        // Aprobada
        elseif (ConstanteEstadoSolicitud::ESTADO_SOLICITUD_APROBADA == $estadoSolicitudCompra->getDenominacionEstadoSolicitudCompra()) {
            $entidadAutorizante = $solicitudCompra->getEntidadAutorizante();
            $historicoSolicitudCompra->setDescripcion("Solicitud aprobada por la entidad autorizante $entidadAutorizante.");
        }

        // Supervisada
        elseif (ConstanteEstadoSolicitud::ESTADO_SOLICITUD_SUPERVISADA == $estadoSolicitudCompra->getDenominacionEstadoSolicitudCompra()) {
            $historicoSolicitudCompra->setDescripcion("Solicitud supervisada.");
        }

        // 
        else {
            $historicoSolicitudCompra->setDescripcion($solicitudCompra->getDescripcion());
        }

        $em->persist($historicoSolicitudCompra);
    }

    /**
     * 
     * @param type $request
     * @param type $solicitudCompra
     */
    private function initSolicitudCompra($request, $solicitudCompra) {

        // A la SolicitudCompra le seteo la EntidadAutorizante según su Justiprecio
        $this->setEntidadAutorizanteASolicitudCompra($solicitudCompra);

        // A la SolicitudCompra le seteo el EstadoSolicitudCompra
        $this->setEstadoSolicitudCompraASolicitudCompra($request, $solicitudCompra);
    }

    /**
     * 
     * @param type $solicitudCompra
     */
    private function initRenglonesSolicitudCompra(Request $request, $solicitudCompra) {

        foreach ($solicitudCompra->getRenglonesSolicitudCompra() as $renglonSolicitudCompra) {

            // A cada RenglonSolicitudCompra le seteo la SolicitudCompra
            $renglonSolicitudCompra->setSolicitudCompra($solicitudCompra);

            // A cada RenglonPedidoInterno le seteo el estado "Solicitado", si corresponde
            $this->setEstadoARenglonPedidoInterno($renglonSolicitudCompra);

            // A cada RenglonSolicitudCompra le seteo la cantidadPendiente
            $renglonSolicitudCompra->setCantidadPendiente($renglonSolicitudCompra->getCantidadSolicitada());

            // A cada RenglonSolicitudCompra le seteo el EstadoRenglonSolicitudCompra
            $this->setEstadoARenglonSolicitudCompra($request, $renglonSolicitudCompra);


            /*  A cada RenglonSolicitudCompra le seteo la EspecificacionTecnica */
            if (null != $renglonSolicitudCompra->getEspecificacionTecnica() && //
                    null != $renglonSolicitudCompra->getEspecificacionTecnica()->getArchivo()) {

                // A la EspecificacionTecnica, le seteo el RenglonSolicitudCompra
                $renglonSolicitudCompra->getEspecificacionTecnica()
                        ->setRenglonSolicitudCompra($renglonSolicitudCompra);

                $renglonSolicitudCompra->getEspecificacionTecnica()
                        ->setNombre($renglonSolicitudCompra->getEspecificacionTecnica()
                                ->getArchivo()->getClientOriginalName());
            }
        }
    }

    /**
     * 
     * @param type $request
     * @param type $renglonSolicitudCompra
     */
    private function setEstadoARenglonSolicitudCompra($request, $renglonSolicitudCompra) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if (null != $request) {

            $accion = $request->request->get('accion');

            if (null != $accion) {

                // Si se apretó el boton "Guardar Borrador"
                if ('save' == $accion) {

                    // Obtengo el EstadoRenglonSolicitudCompra == "Borrador"
                    $estadoRenglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                            findOneBy(
                            array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_BORRADOR), //
                            array('id' => 'desc'), 1, 0)
                    ;

                    $renglonSolicitudCompra->setEstadoRenglonSolicitudCompra($estadoRenglonSolicitudCompra);
                }

                // Si se apretó el boton "Finalizar Solicitud"
                else if ('close' == $accion) {

                    // Si el usuario puede enviar a aprobar las solicitudes
                    if (true == $this->get('security.context')->isGranted('ROLE_COMPRAS_ENVIO_SOLICITUD')) {

                        // Obtengo el EstadoRenglonSolicitudCompra == "Pendiente Autorizacion"
                        $estadoRenglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                                findOneBy(
                                array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_PENDIENTE_AUTORIZACION), //
                                array('id' => 'desc'), 1, 0)
                        ;
                    } else {

                        // Obtengo el EstadoRenglonSolicitudCompra == "Pendiente Envío"
                        $estadoRenglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                                findOneBy(
                                array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_PENDIENTE_ENVIO), //
                                array('id' => 'desc'), 1, 0)
                        ;
                    }

                    $renglonSolicitudCompra->setEstadoRenglonSolicitudCompra($estadoRenglonSolicitudCompra);
                }
            }
        }
    }

    /**
     * 
     * @param type $renglonSolicitudCompra
     */
    public function setEstadoARenglonPedidoInterno($renglonSolicitudCompra) {

        $renglonPedidoInterno = $renglonSolicitudCompra->getRenglonPedidoInterno();

        if (null != $renglonPedidoInterno && $renglonPedidoInterno->getCantidadPendiente() == 0) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Obtengo el EstadoRenglonPedidoInterno == "Solicitado"
            $estadoRenglonPedidoInterno = $em->getRepository('ADIFComprasBundle:EstadoRenglonPedidoInterno')->
                    findOneBy(
                    array('denominacionEstadoRenglonPedidoInterno' => ConstanteEstadoRenglonPedidoInterno::ESTADO_RENGLON_PEDIDO_SOLICITADO), //
                    array('id' => 'desc'), 1, 0);

            $renglonSolicitudCompra->getRenglonPedidoInterno()->setEstadoRenglonPedidoInterno($estadoRenglonPedidoInterno);
        }
    }

    /**
     * Setea el EstadoSolicitudCompra a "Aprobado"
     *
     * @Route("/aprobar/{id}", name="solicitudcompra_aprobar")
     */
    public function aprobarSolicitudCompraAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $solicitudCompra = $em->getRepository('ADIFComprasBundle:SolicitudCompra')->find($id);

        if (!$solicitudCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad SolicitudCompra.');
        }

        // Obtengo el EstadoSolicitudCompra cuya denominacion sea igual a "Aprobada"
        $estadoSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoSolicitudCompra' => ConstanteEstadoSolicitud::ESTADO_SOLICITUD_APROBADA), //
                array('id' => 'desc'), 1, 0)
        ;

        $solicitudCompra->setEstadoSolicitudCompra($estadoSolicitudCompra);


        // Obtengo el EstadoRenglonSolicitudCompra cuya denominación sea igual a "Aprobado"
        $estadoRenglon = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_APROBADO), //
                array('id' => 'desc'), 1, 0)
        ;

        // A cada RenglonSolicitudCompra le asigno el estado "Aprobado"
        foreach ($solicitudCompra->getRenglonesSolicitudCompra() as $renglonSolicitudCompra) {
            $renglonSolicitudCompra->setEstadoRenglonSolicitudCompra($estadoRenglon);
        }


        // Creo un HistoricoSolicitudCompra con el nuevo EstadoSolicitudCompra
        $this->saveHistoricoSolicitudCompra($solicitudCompra, $estadoSolicitudCompra);


        $em->persist($solicitudCompra);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'La solicitud fue aprobada correctamente.');

        return $this->redirect($this->generateUrl('solicitudcompra'));
    }

    /**
     * Setea el EstadoSolicitudCompra a "Desaprobado"
     *
     * @Route("/desaprobar/{id}", name="solicitudcompra_desaprobar")
     */
    public function desaprobarSolicitudCompraAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $solicitudCompra = $em->getRepository('ADIFComprasBundle:SolicitudCompra')->find($id);

        if (!$solicitudCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad SolicitudCompra.');
        }


        // Obtengo el motivo de desaprobación
        $form = $this->getRequest()->request->get('form');

        $observacion = $form['observacion'];

        $solicitudCompra->setObservacion($observacion);


        // Obtengo el EstadoSolicitudCompra cuya denominacion sea igual a "Desaprobada"
        $estadoSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoSolicitudCompra' => ConstanteEstadoSolicitud::ESTADO_SOLICITUD_DESAPROBADA), //
                array('id' => 'desc'), 1, 0)
        ;

        $solicitudCompra->setEstadoSolicitudCompra($estadoSolicitudCompra);


        // Me fijo si hay algún RenglonSolicitudCompra ya "Desaprobado"
        $hayRenglonesDesaprobados = false;

        foreach ($solicitudCompra->getRenglonesSolicitudCompra() as $renglonSolicitudCompra) {

            // Si el RenglonSolicitudCompra está "Desaprobado"
            if (ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_DESAPROBADO ==
                    $renglonSolicitudCompra->getEstadoRenglonSolicitudCompra()->getDenominacionEstadoRenglonSolicitudCompra()) {

                $hayRenglonesDesaprobados = true;
                break;
            }
        }

        // Si NO hay RenglonSolicitudCompra "Desaprobados", entonces desapruebo todos
        if (!$hayRenglonesDesaprobados) {

            // Obtengo el EstadoRenglonSolicitudCompra cuya denominación sea igual a "Desaprobado"
            $estadoRenglon = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                    findOneBy(
                    array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_DESAPROBADO), //
                    array('id' => 'desc'), 1, 0)
            ;

            // A cada RenglonSolicitudCompra le asigno el estado "Desaprobado"
            foreach ($solicitudCompra->getRenglonesSolicitudCompra() as $renglonSolicitudCompra) {
                $renglonSolicitudCompra->setEstadoRenglonSolicitudCompra($estadoRenglon);
            }
        }

        // Creo un HistoricoSolicitudCompra con el nuevo EstadoSolicitudCompra
        $this->saveHistoricoSolicitudCompra($solicitudCompra, $estadoSolicitudCompra);


        $em->persist($solicitudCompra);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'La solicitud fue desaprobada correctamente.');

        return $this->redirect($this->generateUrl('solicitudcompra'));
    }

    /**
     * Setea el EstadoSolicitudCompra a "Anulado"
     *
     * @Route("/anular/{id}", name="solicitudcompra_anular")
     */
    public function anularSolicitudCompraAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $solicitudCompra = $em->getRepository('ADIFComprasBundle:SolicitudCompra')->find($id);

        if (!$solicitudCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad SolicitudCompra.');
        }

        // Obtengo el EstadoSolicitudCompra cuya denominacion sea igual a "Anulada"
        $estadoSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoSolicitudCompra' => ConstanteEstadoSolicitud::ESTADO_SOLICITUD_ANULADA), //
                array('id' => 'desc'), 1, 0)
        ;

        $solicitudCompra->setEstadoSolicitudCompra($estadoSolicitudCompra);


        // Obtengo el EstadoRenglonSolicitudCompra == "Anulado"
        $estadoRenglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_ANULADO), //
                array('id' => 'desc'), 1, 0)
        ;

        // Obtengo el EstadoRenglonPedidoInterno == "Enviado"
        $estadoRenglonPedidoInterno = $em->getRepository('ADIFComprasBundle:EstadoRenglonPedidoInterno')->
                findOneBy(
                array('denominacionEstadoRenglonPedidoInterno' => ConstanteEstadoRenglonPedidoInterno::ESTADO_RENGLON_PEDIDO_ENVIADO), //
                array('id' => 'desc'), 1, 0)
        ;

        // A cada RenglonSolicitudCompra le seteo el estado
        foreach ($solicitudCompra->getRenglonesSolicitudCompra() as $renglonSolicitudCompra) {

            /* @var $renglonSolicitudCompra RenglonSolicitudCompra */
            $renglonSolicitudCompra->setEstadoRenglonSolicitudCompra($estadoRenglonSolicitudCompra);

            // Si el RenglonSolicitudCompra tiene un RenglonPedidoInterno asociado
            if ($renglonSolicitudCompra->getRenglonPedidoInterno() != null) {

                /* @var $renglonPedidoInterno RenglonPedidoInterno */
                $renglonPedidoInterno = $renglonSolicitudCompra->getRenglonPedidoInterno();

                $renglonPedidoInterno->setCantidadPendiente(
                        $renglonPedidoInterno->getCantidadPendiente() + $renglonSolicitudCompra->getCantidadPendiente()
                );

                $renglonPedidoInterno->setEstadoRenglonPedidoInterno($estadoRenglonPedidoInterno);
            }
        }

        // Creo un HistoricoSolicitudCompra con el nuevo EstadoSolicitudCompra
        $this->saveHistoricoSolicitudCompra($solicitudCompra, $estadoSolicitudCompra);

        $em->persist($solicitudCompra);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'La solicitud fue anulada correctamente.');

        return $this->redirect($this->generateUrl('solicitudcompra'));
    }

    /**
     * Setea el EstadoSolicitudCompra a "A Corregir"
     *
     * @Route("/corregir/{id}", name="solicitudcompra_corregir")
     */
    public function corregirSolicitudCompraAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $solicitudCompra = $em->getRepository('ADIFComprasBundle:SolicitudCompra')->find($id);

        if (!$solicitudCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad SolicitudCompra.');
        }

        // Obtengo el EstadoSolicitudCompra cuya denominacion sea igual a "A Corregir"
        $estadoSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoSolicitudCompra' => ConstanteEstadoSolicitud::ESTADO_SOLICITUD_A_CORREGIR), //
                array('id' => 'desc'), 1, 0)
        ;

        $solicitudCompra->setEstadoSolicitudCompra($estadoSolicitudCompra);


        // Obtengo el EstadoRenglonSolicitudCompra == "A Corregir"
        $estadoRenglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_A_CORREGIR), //
                array('id' => 'desc'), 1, 0)
        ;

        // A cada RenglonSolicitudCompra le seteo el estado
        foreach ($solicitudCompra->getRenglonesSolicitudCompra() as $renglonSolicitudCompra) {
            $renglonSolicitudCompra->setEstadoRenglonSolicitudCompra($estadoRenglonSolicitudCompra);
        }

        // Creo un HistoricoSolicitudCompra con el nuevo EstadoSolicitudCompra
        $this->saveHistoricoSolicitudCompra($solicitudCompra, $estadoSolicitudCompra);

        $em->persist($solicitudCompra);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'La solicitud fue enviada a corregir correctamente.');

        return $this->redirect($this->generateUrl('solicitudcompra'));
    }

    /**
     * Setea el EstadoSolicitudCompra a "Pendiente Autorizacion"
     *
     * @Route("/enviar/{id}", name="solicitudcompra_enviar")
     */
    public function enviarSolicitudCompraAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $solicitudCompra = $em->getRepository('ADIFComprasBundle:SolicitudCompra')->find($id);

        if (!$solicitudCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad SolicitudCompra.');
        }

        // Obtengo el EstadoSolicitudCompra cuya denominacion sea igual a "Pendiente Autorizacion"
        $estadoSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoSolicitudCompra' => ConstanteEstadoSolicitud::ESTADO_SOLICITUD_PENDIENTE_AUTORIZACION), //
                array('id' => 'desc'), 1, 0)
        ;

        $solicitudCompra->setEstadoSolicitudCompra($estadoSolicitudCompra);


        // Obtengo el EstadoRenglonSolicitudCompra == "Pendiente Autorización"
        $estadoRenglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_PENDIENTE_AUTORIZACION), //
                array('id' => 'desc'), 1, 0)
        ;

        // Por cada RenglonSolicitudCompra
        foreach ($solicitudCompra->getRenglonesSolicitudCompra() as $renglonSolicitudCompra) {
            $renglonSolicitudCompra->setEstadoRenglonSolicitudCompra($estadoRenglonSolicitudCompra);
        }

        // Creo un HistoricoSolicitudCompra con el nuevo EstadoSolicitudCompra
        $this->saveHistoricoSolicitudCompra($solicitudCompra, $estadoSolicitudCompra);

        $em->persist($solicitudCompra);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'La solicitud fue enviada a autorizar correctamente.');

        return $this->redirect($this->generateUrl('solicitudcompra'));
    }

    /**
     * 
     * Retorna todas las SolicitudCompra cuyo usuario asociado pertenezca 
     * a la misma Area recibida como parámetro.
     * 
     * @param type $idArea
     */
    private function getSolicitudCompraByAreaId($idArea) {

        $emCompras = $this->getDoctrine()->getManager($this->getEntityManager());
        $emAutenticacion = $this->getDoctrine()->getManager(EntityManagers::getEmAutenticacion());

        // Obtengo todos los ids de los usuarios que pertenezcan al área
        $idsUsuarios = $emAutenticacion
                ->createQuery(
                        "SELECT u.id FROM ADIFAutenticacionBundle:Usuario u " .
                        "WHERE u.idArea = :idArea"
                )
                ->setParameter('idArea', $idArea)
                ->getResult()
        ;

        // Obtengo todas las solicitudes cuyo usuario sea alguno de los obtenidos
        $solicitudes = $emCompras
                ->createQuery(
                        "SELECT s FROM ADIFComprasBundle:SolicitudCompra s " .
                        "WHERE s.idUsuario IN (:usuarios)")
                ->setParameter('usuarios', $idsUsuarios, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);


        return $solicitudes->getResult();
    }

    /**
     * @Route("/estados", name="solicitudcompra_estados")
     */
    public function getEstadosSolicitudCompra() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFComprasBundle:EstadoSolicitudCompra', $this->getEntityManager());

        $query = $repository->createQueryBuilder('e')
                ->select('e.id', 'e.denominacionEstadoSolicitudCompra')
                ->orderBy('e.denominacionEstadoSolicitudCompra', 'ASC')
                ->getQuery()
                ->useResultCache(true, 36000, 'solicitudcompra_estados')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $query->getResult();
    }

    /**
     * @Route("/tiposcompra", name="solicitudcompra_tiposcompra")
     */
    public function getTiposSolicitudCompra() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFComprasBundle:TipoSolicitudCompra', $this->getEntityManager());

        $query = $repository->createQueryBuilder('ts')
                ->select('ts.id', 'ts.denominacionTipoSolicitudCompra')
                ->orderBy('ts.denominacionTipoSolicitudCompra', 'ASC')
                ->getQuery()
                ->useResultCache(true, 36000, 'solicitudcompra_tiposcompra')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $query->getResult();
    }

    /**
     * @Route("/crear-solicitud/", name="solicitudcompra_cear_pedidointerno")
     * @Method("POST")
     * @Template("ADIFComprasBundle:SolicitudCompra:new.html.twig")
     */
    public function crearSolicitudCompraAPartirPedidoInternoAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ids = json_decode($request->request->get('ids'));

        $solicitudCompra = new SolicitudCompra();

        foreach ($ids as $id) {

            // Busco RenglonPedidoInterno en la BBDD
            $renglonPedidoInterno = $em
                    ->getRepository('ADIFComprasBundle:RenglonPedidoInterno')
                    ->find($id);

            if (!$renglonPedidoInterno) {
                throw $this->createNotFoundException('No se puede encontrar la entidad RenglonPedidoInterno.');
            }

            // Creo el RenglonSolicitudCompra
            $renglonSolicitudCompra = new RenglonSolicitudCompra();

            $renglonSolicitudCompra->setRenglonPedidoInterno($renglonPedidoInterno);

            $renglonSolicitudCompra->setRubro($renglonPedidoInterno->getRubro());
            $renglonSolicitudCompra->setBienEconomico($renglonPedidoInterno->getBienEconomico());

            $renglonSolicitudCompra->setDescripcion($renglonPedidoInterno->getDescripcion());

            $renglonSolicitudCompra->setCantidadPendiente($renglonPedidoInterno->getCantidadPendiente());
            $renglonSolicitudCompra->setCantidadSolicitada($renglonPedidoInterno->getCantidadPendiente());

            $renglonSolicitudCompra->setUnidadMedida($renglonPedidoInterno->getUnidadMedida());
            $renglonSolicitudCompra->setPrioridad($renglonPedidoInterno->getPrioridad());

            // Agrego RenglonSolicitudCompra a la SolicitudCompra
            $solicitudCompra->addRenglonesSolicitudCompra($renglonSolicitudCompra);
        }

        $form = $this->createCreateForm($solicitudCompra);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $solicitudCompra,
            'incluyePedidos' => true,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear solicitud de compra'
        );
    }

    /**
     * Setea el EstadoSolicitudCompra a "Supervisado" de las SolicitudCompra
     * enviadas por parámetro
     *
     * @Route("/visar-solicitudes/", name="solicitudcompra_visar-solicitudes")
     * @Method("POST")
     */
    public function visarSolicitudesCompraAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ids = json_decode($request->request->get('ids'));

        // Obtengo el EstadoSolicitudCompra cuya denominacion sea igual a "Supervisada"
        $estadoSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoSolicitudCompra' => ConstanteEstadoSolicitud::ESTADO_SOLICITUD_SUPERVISADA), //
                array('id' => 'desc'), 1, 0)
        ;

        // Obtengo el EstadoRenglonSolicitudCompra cuya denominación sea igual a "Supervisado"
        $estadoRenglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                findOneBy(
                array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_SUPERVISADO), //
                array('id' => 'desc'), 1, 0)
        ;

        $statusCode = Response::HTTP_OK;

        foreach ($ids as $id) {

            $solicitudCompra = $em->getRepository('ADIFComprasBundle:SolicitudCompra')->find($id);

            if (!$solicitudCompra) {
                $statusCode = Response::HTTP_NOT_FOUND;
            } //. 
            else {

                if (ConstanteEstadoSolicitud::ESTADO_SOLICITUD_APROBADA == $solicitudCompra->getEstadoSolicitudCompra()) {

                    $solicitudCompra->setEstadoSolicitudCompra($estadoSolicitudCompra);

                    // A cada renglón le asigno el estado "Supervisado"
                    foreach ($solicitudCompra->getRenglonesSolicitudCompra() as $renglonSolicitudCompra) {
                        $renglonSolicitudCompra->setEstadoRenglonSolicitudCompra($estadoRenglonSolicitudCompra);
                    }

                    // Creo un HistoricoSolicitudCompra con el nuevo EstadoSolicitudCompra
                    $this->saveHistoricoSolicitudCompra($solicitudCompra, $estadoSolicitudCompra);

                    $em->persist($solicitudCompra);
                }
            }
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Las solicitudes fueron visadas correctamente.');

        return new Response('', $statusCode);
    }

    /**
     * 
     * @param SolicitudCompra $solicitudCompra
     */
    private function actualizarCantidadPendientePedidoInterno(SolicitudCompra $solicitudCompra) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        foreach ($solicitudCompra->getRenglonesSolicitudCompra() as $renglonSolicitudCompra) {

            $renglonPedidoInterno = $renglonSolicitudCompra->getRenglonPedidoInterno();

            if (null != $renglonPedidoInterno) {
                $renglonPedidoInterno->setCantidadPendiente(
                        $renglonPedidoInterno->getCantidadPendiente() - $renglonSolicitudCompra->getCantidadSolicitada()
                );

                $em->persist($renglonPedidoInterno);
            }
        }

        $em->flush();
    }

    /**
     * Finds and displays a PDF of SolicitudCompra entity.scom
     *
     * @Route("/print/{id}", name="solicitudescompra_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $solicitudCompra = $em->getRepository('ADIFComprasBundle:SolicitudCompra')->find($id);
        /* @var $solicitudCompra SolicitudCompra */
        if (!$solicitudCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad SolicitudCompra.');
        }

        $empresaSession = EmpresaSession::getInstance();
        $idEmpresa = $empresaSession->getIdEmpresa();

        $html = '<html><head><style type="text/css">' . $this->renderView('::PDF/mpdf.default.css.twig') . '</style></head><body>';
        $html .= $this->renderView(
                'ADIFComprasBundle:SolicitudCompra:print.show.html.twig', [
                    'sc' => $solicitudCompra,
                    'idEmpresa' => $idEmpresa
                ]
        );
        $html .= '</body></html>';

        $filename = 'solicitudCompra_' . $solicitudCompra->getNumero() . '.pdf';

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);

        $mpdfService->WriteHTML($html);

        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

}
