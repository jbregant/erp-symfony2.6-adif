<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoRenglonSolicitud;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoRequerimiento;
use ADIF\ComprasBundle\Entity\RenglonRequerimiento;
use ADIF\ComprasBundle\Entity\Requerimiento;
use ADIF\ComprasBundle\Form\RequerimientoType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use mPDF;
use ADIF\BaseBundle\Session\EmpresaSession;

/**
 * Requerimiento controller.
 *
 * @Route("/requerimiento")
 */
class RequerimientoController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Requerimientos' => $this->generateUrl('requerimiento')
        );
    }

    /**
     * Lists all Requerimiento entities.
     *
     * @Route("/", name="requerimiento")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $securityContext = $this->get('security.context');

        $puedeCrearRequerimientos = $securityContext->isGranted('ROLE_COMPRAS_CREACION_REQUERIMIENTO');
        $puedeEnviarRequerimientos = $securityContext->isGranted('ROLE_COMPRAS_ENVIO_REQUERIMIENTO');
        $puedeAprobarRequerimientosContablemente = $securityContext->isGranted('ROLE_COMPRAS_APROBACION_CONTABLE_REQUERIMIENTO');

        $bread = $this->base_breadcrumbs;
        $bread['Requerimientos'] = null;

        $returnArray = array(
            'tipos_contratacion' => $this->listaTipoContratacionAction(),
            'estados_requerimiento' => $this->listaEstadoRequerimientoAction(),
            'puede_crear_requerimientos' => $puedeCrearRequerimientos,
            'puede_enviar_requerimientos' => $puedeEnviarRequerimientos,
            'puede_aprobar_requerimientos_contablemente' => $puedeAprobarRequerimientosContablemente,
            'breadcrumbs' => $bread,
            'page_title' => 'Requerimiento',
            'page_info' => 'Lista de requerimiento'
        );

        if (true === $securityContext->isGranted('ROLE_COMPRAS_PANEL_CONTROL')) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $returnArray['cantidad_solicitudes_pendientes'] = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')
                    ->getCantidadRenglonesSolicitudCompraSupervisados();

            $returnArray['cantidad_requerimientos_pendientes'] = $em->getRepository('ADIFComprasBundle:Requerimiento')
                    ->getCantidadRequerimientosPendientesCotizacionByUsuario($this->getUser()->getId());

            $returnArray['cantidad_oc_pendientes'] = $em->getRepository('ADIFComprasBundle:OrdenCompra')
                    ->getCantidadOrdenesCompraPendientes();
        }

        return $returnArray;
    }

    /**
     * Tabla para RenglonSolicitudCompra.
     *
     * @Route("/index_table_renglon_solicitud/", name="requerimiento_index_table_renglon_solicitud")
     * @Method("GET|POST")
     */
    public function indexTableRenglonSolicitudAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        // Obtengo los RenglonSolicitudCompra
        $renglonesSolicitudCompra = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')
                ->getRenglonesSolicitudCompraSupervisados();

        return $this->render('ADIFComprasBundle:Requerimiento:index_table_renglones_solicitud.html.twig', array(
                    'renglonesSolicitudCompra' => $renglonesSolicitudCompra,
                    'showAcciones' => false
        ));
    }

    /**
     * Tabla para Requerimiento.
     *
     * @Route("/index_table/", name="requerimiento_index_table")
     * @Method("GET|POST")
     */
    public function indexTableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $tipo = $request->query->get('tipo');

        $securityContext = $this->get('security.context');

        // Si el usuario logueado puede autorizar los Requerimiento contablemente
        $puedeAprobarRequerimientosContablemente = $securityContext->isGranted('ROLE_COMPRAS_APROBACION_CONTABLE_REQUERIMIENTO');

        if ($tipo == 'enviados') {

//            Obtengo los Requerimiento creados por el usuario logueado
//            $requerimientosEnviadosUsuario = $em->getRepository('ADIFComprasBundle:Requerimiento')->
//                    findBy(
//                    array('idUsuario' => $this->getUser()->getId()), // 
//                    array('fechaRequerimiento' => 'DESC', 'id' => 'DESC')
//            );

            $idArea = $this->getUser()->getArea()->getId();

            // Obtengo todos los Requerimientos del área del usuario logueado
            $requerimientosDelArea = $this->getRequerimientoByAreaId($idArea);

            return $this->render('ADIFComprasBundle:Requerimiento:index_table.html.twig', array(
                        'requerimientos' => $requerimientosDelArea,
                        'puede_aprobar_requerimientos_contablemente' => $puedeAprobarRequerimientosContablemente,
                        'tipo' => 'enviados'
            ));
        } //.
        elseif ($tipo == 'pendientes-aprobacion') {

            $requerimientosParaAprobarContablemente = array();

            // Si el usuario logueado puede autorizar los Requerimiento contablemente
            if (true === $securityContext->isGranted('ROLE_COMPRAS_APROBACION_CONTABLE_REQUERIMIENTO')) {

                // Obtengo todos los Requerimientos que pueden ver el area Contable
                $requerimientosParaAprobarContablemente = $em->getRepository('ADIFComprasBundle:Requerimiento')
                        ->getRequerimientoByEstadoNotEqual(array(
                    ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_CREADO,
                    ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_BORRADOR,
                    ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_PENDIENTE_ENVIO,
                    ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_A_CORREGIR,
                    ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_ANULADO,
                    ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_ARCHIVADO
                        )
                );
            }

            return $this->render('ADIFComprasBundle:Requerimiento:index_table.html.twig', array(
                        'requerimientos' => $requerimientosParaAprobarContablemente,
                        'puede_aprobar_requerimientos_contablemente' => $puedeAprobarRequerimientosContablemente,
                        'tipo' => 'pendientes-aprobacion'
            ));
        } //.
        elseif ($tipo == 'pendientes-envio') {

            $requerimientosParaEnviar = array();

            // Si el usuario logueado puede enviar los Requerimiento a autorizar contablemente
            if (true === $securityContext->isGranted('ROLE_COMPRAS_ENVIO_REQUERIMIENTO')) {

                // Obtengo todos los Requerimientos con estado == Pendiente Envío
                $requerimientosParaEnviar = $em->getRepository('ADIFComprasBundle:Requerimiento')
                        ->getRequerimientoByEstadoEqual(ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_PENDIENTE_ENVIO);
            }

            return $this->render('ADIFComprasBundle:Requerimiento:index_table.html.twig', array(
                        'requerimientos' => $requerimientosParaEnviar,
                        'puede_aprobar_requerimientos_contablemente' => $puedeAprobarRequerimientosContablemente,
                        'tipo' => 'pendientes-envio'
            ));
        } //.
        elseif ($tipo == 'todos') {

            $requerimientosTodos = array();

            // Si el usuario logueado puede enviar los Requerimiento a autorizar contablemente
            if (true === $securityContext->isGranted('ROLE_COMPRAS_ENVIO_REQUERIMIENTO')) {

                // Obtengo todos los Requerimiento con estado != Borrador y != Archivado
                $requerimientosTodos = $em->getRepository('ADIFComprasBundle:Requerimiento')
                        ->getRequerimientoByEstadoNotEqual(array(ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_BORRADOR, ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_ARCHIVADO));
            }

            return $this->render('ADIFComprasBundle:Requerimiento:index_table.html.twig', array(
                        'requerimientos' => $requerimientosTodos,
                        'puede_aprobar_requerimientos_contablemente' => $puedeAprobarRequerimientosContablemente,
                        'tipo' => 'todos'
            ));
        }
    }

    /**
     * Creates a new Requerimiento entity.
     *
     * @Route("/insertar", name="requerimiento_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:Requerimiento:new.html.twig")
     */
    public function createAction(Request $request) {

        $requerimiento = new Requerimiento();

        $form = $this->createCreateForm($requerimiento);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Al Requerimiento le seteo el Usuario
            $requerimiento->setUsuario($this->getUser());

            // Al Requerimiento le seteo el estado
            $this->setEstadoARequerimiento($request, $requerimiento);

            $renglones = $request->request->get('renglones');

            foreach ($renglones as $renglon) {

                $renglonRequerimiento = new RenglonRequerimiento();

                // Obtengo el renglonSolicitudCompra correspondiente
                $renglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')
                        ->find($renglon['id']);

                if (!$renglonSolicitudCompra) {
                    throw $this->createNotFoundException('No se puede encontrar la entidad RenglonSolicitudCompra.');
                }

                $renglonRequerimiento->setRenglonSolicitudCompra($renglonSolicitudCompra);

                // Si la cantidad a solicitar es mayor que la Cantidad Pendiente
                if ($renglonSolicitudCompra->getCantidadPendiente() < $renglon['cantidad']) {
                    // Usa la cantidad pendiente total
                    $cantidadACotizar = $renglonSolicitudCompra->getCantidadPendiente();
                } //. 
                else {
                    // Usa la cantidad que trae de la tabla de RenglonesRequerimientos
                    $cantidadACotizar = $renglon['cantidad'];
                }

                $renglonRequerimiento->setCantidad($cantidadACotizar);

                $renglonRequerimiento->setJustiprecioUnitario($renglon['justiprecio']);

                $renglonRequerimiento->setRequerimiento($requerimiento);

                $requerimiento->addRenglonesRequerimiento($renglonRequerimiento);

                // Actualizo la cantidad pendiente en renglonSolicitudCompra
                $cantidadPendiente = $renglonSolicitudCompra
                                ->getCantidadPendiente() - $renglonRequerimiento->getCantidad();

                $renglonSolicitudCompra->setCantidadPendiente($cantidadPendiente);

                $this->setEstadoARenglonSolicitudCompra($renglonSolicitudCompra);
            }

            $em->persist($requerimiento);

            $em->flush();

            return $this->redirect($this->generateUrl('requerimiento'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'requerimiento' => $requerimiento,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear requerimiento',
        );
    }

    /**
     * Creates a form to create a Requerimiento entity.
     *
     * @param Requerimiento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Requerimiento $entity) {

        $form = $this->createForm(new RequerimientoType(), $entity, array(
            'action' => $this->generateUrl('requerimiento_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form
                ->add('save', 'submit', array(
                    'label' => 'Guardar borrador'
                ))
                ->add('close', 'submit', array(
                    'label' => 'Finalizar requerimiento'
                ))
        ;

        return $form;
    }

    /**
     * Displays a form to create a new Requerimiento entity.
     *
     * @Route("/crear", name="requerimiento_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = new Requerimiento();

        $form = $this->createCreateForm($requerimiento);

        $renglonesSolicitudCompra = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')
                ->getRenglonesSolicitudCompraSupervisados();

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'requerimiento' => $requerimiento,
            'renglonesSolicitudCompra' => $renglonesSolicitudCompra,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear requerimiento'
        );
    }

    /**
     * Finds and displays a Requerimiento entity.
     *
     * @Route("/{id}", name="requerimiento_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($id);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        $securityContext = $this->get('security.context');

        $puedeEnviarRequerimientos = false;
        $puedeAprobarRequerimientosContablemente = false;

        // Si el usuario logueado puede enviar los Requerimiento a autorizar
        if (true === $securityContext->isGranted('ROLE_COMPRAS_ENVIO_REQUERIMIENTO')) {
            $puedeEnviarRequerimientos = true;
        }

        // Si el usuario logueado puede autorizar los Requerimiento contablemente
        if (true === $securityContext->isGranted('ROLE_COMPRAS_APROBACION_CONTABLE_REQUERIMIENTO')) {
            $puedeAprobarRequerimientosContablemente = true;
        }

        $bread = $this->base_breadcrumbs;
        $bread['Requerimiento ' . $requerimiento->getNumero()] = null;

        return array(
            'requerimiento' => $requerimiento,
            'puede_enviar_requerimientos' => $puedeEnviarRequerimientos,
            'puede_aprobar_requerimientos_contablemente' => $puedeAprobarRequerimientosContablemente,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver requerimiento'
        );
    }

    /**
     * Displays a form to edit an existing Requerimiento entity.
     *
     * @Route("/editar/{id}", name="requerimiento_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Requerimiento:new.html.twig")
     */
    public function editAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $requerimiento Requerimiento */
        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($id);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        $renglonesSolicitudCompra = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')
                ->getRenglonesSolicitudCompraSupervisados();

        $em->flush();

        $editForm = $this->createEditForm($requerimiento);

        $bread = $this->base_breadcrumbs;
        $bread['Requerimiento ' . $requerimiento->getNumero()] = $this->generateUrl('requerimiento_show', array('id' => $requerimiento->getId()));
        $bread['Editar'] = null;

        return array(
            'requerimiento' => $requerimiento,
            'renglonesSolicitudCompra' => $renglonesSolicitudCompra,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar requerimiento'
        );
    }

    /**
     * Creates a form to edit a Requerimiento entity.
     *
     * @param Requerimiento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Requerimiento $entity) {

        $form = $this->createForm(new RequerimientoType(), $entity, array(
            'action' => $this->generateUrl('requerimiento_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form
                ->add('save', 'submit', array(
                    'label' => 'Guardar borrador'
                ))
                ->add('close', 'submit', array(
                    'label' => 'Finalizar requerimiento'
                ))
        ;

        return $form;
    }

    /**
     * Edits an existing Requerimiento entity.
     *
     * @Route("/actualizar/{id}", name="requerimiento_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:Requerimiento:new.html.twig")
     */
    public function updateAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($id);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        $renglonesOriginales = new ArrayCollection();

        // Creo un ArrayCollection de los RenglonRequerimiento actuales en la BBDD
        foreach ($requerimiento->getRenglonesRequerimiento() as $renglonRequerimiento) {
            $renglonesOriginales->add($renglonRequerimiento);
        }

        $editForm = $this->createEditForm($requerimiento);

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // Le seteo el estado al Requerimiento
            $this->setEstadoARequerimiento($request, $requerimiento);

            // Obtengo los renglones de la tabla Renglones Requerimiento
            $renglones = $request->request->get('renglones');

            // Si Renglones está vacío solo verifico si tengo que eliminar 
            if (!$renglones) {

                // Por cada renglon Original
                foreach ($renglonesOriginales as $renglonOriginal) {

                    // Actualizo la cantidad pendiente en renglonSolicitudCompra
                    $cantidadPendiente = $renglonOriginal->getRenglonSolicitudCompra()
                                    ->getCantidadPendiente() + $renglonOriginal->getCantidad();

                    $renglonOriginal->getRenglonSolicitudCompra()->setCantidadPendiente($cantidadPendiente);

                    // Elimino el renglon del requerimiento
                    $requerimiento->removeRenglonesRequerimiento($renglonOriginal);

                    $em->remove($renglonOriginal);
                }
            } else {
                // Por cada renglon Original
                foreach ($renglonesOriginales as $renglonOriginal) {

                    $encontrado = false;

                    // Verifico si está en renglones
                    foreach ($renglones as $key => $renglon) {

                        // Si lo encuentro
                        if ($renglon['id'] == $renglonOriginal->getRenglonSolicitudCompra()->getId()) {

                            // Controlo si hay que modificar la cantidad
                            if ($renglonOriginal->getCantidad() != $renglon['cantidad']) {

                                $cantidadACotizarOriginal = $renglonOriginal->getCantidad();

                                $cantidadACotizarNueva = $renglon['cantidad'];

                                $cantidadPendiente = $renglonOriginal->getRenglonSolicitudCompra()->getCantidadPendiente();

                                // Controlo que la nueva cantidad no supere la cantidad pendiente
                                if (($cantidadACotizarOriginal + $cantidadPendiente) >= $cantidadACotizarNueva) {

                                    $renglonOriginal->setCantidad($cantidadACotizarNueva);

                                    // Actualizo la cantidad pendiente en renglonSolicitudCompra
                                    $cantidadPendiente = $cantidadPendiente + $cantidadACotizarOriginal - $cantidadACotizarNueva;

                                    $renglonOriginal->getRenglonSolicitudCompra()->setCantidadPendiente($cantidadPendiente);

                                    $this->setEstadoARenglonSolicitudCompra($renglonOriginal->getRenglonSolicitudCompra());
                                }
                            }

                            // Controlo si hay que modificar el justiprecio unitario
                            if ($renglonOriginal->getJustiprecioUnitario() != $renglon['justiprecio']) {
                                $renglonOriginal->setJustiprecioUnitario($renglon['justiprecio']);
                            }

                            // Indico que el renglón procesado fue encontrado
                            $renglones[$key]['encontrado'] = 1;

                            // Aviso que el renglón fue encontrado
                            $encontrado = true;
                        }
                    }

                    // Si no fue encontrado debo eliminarlo
                    if (!$encontrado) {

                        // Actualizo la cantidad pendiente en renglonSolicitudCompra
                        $cantidadPendiente = $renglonOriginal->getRenglonSolicitudCompra()
                                        ->getCantidadPendiente() + $renglonOriginal->getCantidad();

                        $renglonOriginal->getRenglonSolicitudCompra()->setCantidadPendiente($cantidadPendiente);

                        $this->setEstadoARenglonSolicitudCompra($renglonOriginal->getRenglonSolicitudCompra());

                        // Elimino el renglon del requerimiento
                        $requerimiento->removeRenglonesRequerimiento($renglonOriginal);

                        $em->remove($renglonOriginal);
                    }
                }
            }

            // Debo agregar los renglones nuevos
            if ($renglones) {

                foreach ($renglones as $renglon) {

                    if (empty($renglon['encontrado'])) {

                        $renglonRequerimientoNuevo = new RenglonRequerimiento();

                        // Obtengo el renglonSolicitudCompra correspondiente
                        $renglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')->find($renglon['id']);

                        $renglonRequerimientoNuevo->setRenglonSolicitudCompra($renglonSolicitudCompra);

                        // Si la cantidad a solicitar es mayor que la Cantidad Pendiente
                        if ($renglonSolicitudCompra->getCantidadPendiente() < $renglon['cantidad']) {

                            // Usa la cantidad pendiente total
                            $cantidadACotizar = $renglonSolicitudCompra->getCantidadPendiente();
                        } //. 
                        else {
                            // Usa la cantidad que trae de la tabla de RenglonesRequerimientos
                            $cantidadACotizar = $renglon['cantidad'];
                        }

                        $renglonRequerimientoNuevo->setCantidad($cantidadACotizar);

                        $renglonRequerimientoNuevo->setJustiprecioUnitario($renglonSolicitudCompra->getJustiprecioUnitario());

                        $renglonRequerimientoNuevo->setRequerimiento($requerimiento);

                        $requerimiento->addRenglonesRequerimiento($renglonRequerimientoNuevo);

                        // Actualizo la cantidad pendiente en renglonSolicitudCompra
                        $cantidadPendiente = $renglonSolicitudCompra
                                        ->getCantidadPendiente() - $renglonRequerimientoNuevo->getCantidad();

                        $renglonSolicitudCompra->setCantidadPendiente($cantidadPendiente);

                        $this->setEstadoARenglonSolicitudCompra($renglonSolicitudCompra);
                    }
                }
            }

            $em->flush();

            return $this->redirect($this->generateUrl('requerimiento'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $renglonesSolicitudCompra = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')
                ->getRenglonesSolicitudCompraSupervisados();

        $bread = $this->base_breadcrumbs;
        $bread['Requerimiento ' . $requerimiento->getNumero()] = $this->generateUrl('requerimiento_show', array('id' => $requerimiento->getId()));
        $bread['Editar'] = null;

        return array(
            'requerimiento' => $requerimiento,
            'renglonesSolicitudCompra' => $renglonesSolicitudCompra,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar requerimiento'
        );
    }

    /**
     * Deletes a Requerimiento entity.
     *
     * @Route("/borrar/{id}", name="requerimiento_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('requerimiento'));
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \ADIF\ComprasBundle\Entity\Requerimiento $requerimiento
     */
    private function setEstadoARequerimiento(Request $request, Requerimiento $requerimiento) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $accion = $request->request->get('accion');

        if (null != $accion) {

            // Si se apretó el boton "Guardar Borrador"
            if ('save' == $accion) {

                // Obtengo el EstadoRequerimiento cuya denominacion sea igual a "Borrador"
                $estadoRequerimiento = $em->getRepository('ADIFComprasBundle:EstadoRequerimiento')->
                        findOneBy(
                        array('denominacionEstadoRequerimiento' => ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_BORRADOR), //
                        array('id' => 'desc'), 1, 0)
                ;

                $requerimiento->setEstadoRequerimiento($estadoRequerimiento);
            }

            // Si se apretó el boton "Finalizar Requerimiento"
            else if ('close' == $accion) {

                // Obtengo el EstadoRequerimiento cuya denominacion sea igual a "Pendiente Envio"
                $estadoRequerimiento = $em->getRepository('ADIFComprasBundle:EstadoRequerimiento')->
                        findOneBy(
                        array('denominacionEstadoRequerimiento' => ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_PENDIENTE_ENVIO), //
                        array('id' => 'desc'), 1, 0)
                ;

                $requerimiento->setEstadoRequerimiento($estadoRequerimiento);
            }
        }
    }

    /**
     * Setea el EstadoRequerimiento a "Anulado"
     *
     * @Route("/anular/{id}", name="requerimiento_anular")
     */
    public function anularRequerimientoAction($id) {

        $emCompras = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $requerimiento Requerimiento */
        $requerimiento = $emCompras->getRepository('ADIFComprasBundle:Requerimiento')->find($id);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        if ($requerimiento->getEsAnulable()) {

            // Elimino los provisorios asociados al Requerimiento
            $this->get('adif.contabilidad_presupuestaria_service')
                    ->eliminarProvisorioFromRequerimiento($requerimiento);

            // Seteo el estado "Anulado" al Requerimiento
            $requerimiento->setEstadoRequerimiento(
                    $emCompras->getRepository('ADIFComprasBundle:EstadoRequerimiento')
                            ->findOneBy(
                                    array('denominacionEstadoRequerimiento' => ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_ANULADO), //
                                    array('id' => 'desc'), 1, 0));


            // Por cada renglon del requerimiento
            foreach ($requerimiento->getRenglonesRequerimiento() as $renglonRequerimiento) {

                /* @var $renglonSolicitudCompra \ADIF\ComprasBundle\Entity\RenglonSolicitudCompra */
                $renglonSolicitudCompra = $renglonRequerimiento->getRenglonSolicitudCompra();

                // Seteo el estado a cada renglon de la solicitud asociada                
                $this->setEstadoARenglonSolicitudCompra($renglonSolicitudCompra, true);

                // Seteo la cantidad pendiente del renglon de solicitud de compra  
                $renglonSolicitudCompra->setCantidadPendiente(
                        $renglonSolicitudCompra->getCantidadPendiente() + $renglonRequerimiento->getCantidad()
                );
            }

            $emCompras->persist($requerimiento);

            // Comienzo la transaccion
            $emCompras->getConnection()->beginTransaction();

            try {

                $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

                $emCompras->flush();

                $emContable->flush();

                $this->get('session')->getFlashBag()->add('success', 'El requerimiento fue anulado correctamente.');
					
				$this->get('session')->getFlashBag()->add('success', 'Se ha liberado provisorio de la partida presupuestaria.');

                $emCompras->getConnection()->commit();
            } //.
            catch (\Exception $e) {

                $emCompras->getConnection()->rollback();
                $emCompras->close();

                throw $e;
            }
        } else {

            $this->get('session')->getFlashBag()->add('error', 'No se pudo anular el requerimiento ya que posee &oacute;rdenes de compra asociadas.');
        }

        return $this->redirect($this->generateUrl('requerimiento'));
    }

    /**
     * Setea el EstadoRequerimiento a "A Corregir"
     *
     * @Route("/corregir/{id}", name="requerimiento_corregir")
     */
    public function corregirRequerimientoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($id);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        // Obtengo el EstadoRequerimiento cuya denominacion sea igual a "A Corregir"
        $estadoRequerimiento = $em->getRepository('ADIFComprasBundle:EstadoRequerimiento')->
                findOneBy(
                array('denominacionEstadoRequerimiento' => ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_A_CORREGIR), //
                array('id' => 'desc'), 1, 0)
        ;

        $requerimiento->setEstadoRequerimiento($estadoRequerimiento);

        $em->persist($requerimiento);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'El requerimiento fue enviado a corregir correctamente.');

        return $this->redirect($this->generateUrl('requerimiento'));
    }

    /**
     * Setea el EstadoRequerimiento a "Pendiente Aprobacion Contable"
     *
     * @Route("/enviar/{id}", name="requerimiento_enviar")
     */
    public function enviarRequerimientoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($id);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        // Obtengo el EstadoRequerimiento cuya denominacion sea igual a "Pendiente Aprobacion Contable"
        $estadoRequerimiento = $em->getRepository('ADIFComprasBundle:EstadoRequerimiento')->
                findOneBy(
                array('denominacionEstadoRequerimiento' => ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_PENDIENTE_APROBACION_CONTABLE), //
                array('id' => 'desc'), 1, 0)
        ;

        $requerimiento->setEstadoRequerimiento($estadoRequerimiento);

        $em->persist($requerimiento);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'El requerimiento fue enviado a aprobar contablemente correctamente.');

        return $this->redirect($this->generateUrl('requerimiento'));
    }

    /**
     * Setea el EstadoRequerimiento a "Pendiente Cotizacion" y 
     * genera el Provisorio correspondiente.
     *
     * @Route("/aprobar/{id}", name="requerimiento_aprobar")
     */
    public function aprobarRequerimientoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($id);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        // Obtengo el EstadoRequerimiento cuya denominacion sea igual a "Pendiente Cotizacion"
        $estadoRequerimiento = $em->getRepository('ADIFComprasBundle:EstadoRequerimiento')->
                findOneBy(
                array('denominacionEstadoRequerimiento' => ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_PENDIENTE_COTIZACION), //
                array('id' => 'desc'), 1, 0)
        ;

        $requerimiento->setEstadoRequerimiento($estadoRequerimiento);

        $mensajeError = $this->get('adif.contabilidad_presupuestaria_service')
                ->crearProvisorioFromRequerimiento($requerimiento);

        // Si hubo un error
        if ($mensajeError != '') {

            $this->get('session')->getFlashBag()
                    ->add('error', $mensajeError);

            return $this->redirect($this->generateUrl('requerimiento_show', array('id' => $requerimiento->getId())));
        } else {

            $em->persist($requerimiento);

            // Comienzo la transaccion
            $em->getConnection()->beginTransaction();

            try {

                $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

                $em->flush();

                $emContable->flush();

                $em->getConnection()->commit();

				$this->get('session')->getFlashBag()
                        ->add('success', 'El requerimiento fue aprobado correctamente.');
						
                $this->get('session')->getFlashBag()
                        ->add('success', 'Se ha comprometido provisorio de la partida presupuestaria.');
            } //.
            catch (\Exception $e) {

                $em->getConnection()->rollback();
                $em->close();

                throw $e;
            }

            return $this->redirect($this->generateUrl('requerimiento'));
        }
    }

    /**
     * Setea el EstadoRequerimiento a "Desaprobado"
     *
     * @Route("/desaprobar/{id}", name="requerimiento_desaprobar")
     */
    public function desaprobarRequerimientoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($id);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        // Obtengo el EstadoRequerimiento cuya denominacion sea igual a "Desaprobado"
        $estadoRequerimiento = $em->getRepository('ADIFComprasBundle:EstadoRequerimiento')->
                findOneBy(
                array('denominacionEstadoRequerimiento' => ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_DESAPROBADO), //
                array('id' => 'desc'), 1, 0)
        ;

        $requerimiento->setEstadoRequerimiento($estadoRequerimiento);

        $em->persist($requerimiento);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'El requerimiento fue desaprobado correctamente.');

        return $this->redirect($this->generateUrl('requerimiento'));
    }

    /**
     * 
     * @param type $renglonSolicitudCompra
     * @param type $esAnulacionRequerimiento
     */
    private function setEstadoARenglonSolicitudCompra($renglonSolicitudCompra, $esAnulacionRequerimiento = false) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        // Si el Requerimiento relacionado está anulado
        if ($esAnulacionRequerimiento) {

            // Obtengo el EstadoRenglonSolicitudCompra cuya denominacion sea igual a "Supervisado"
            $estadoRenglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                    findOneBy(
                    array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_SUPERVISADO), //
                    array('id' => 'desc'), 1, 0)
            ;
        } else {

            if ($renglonSolicitudCompra->getCantidadPendiente() == 0) {

                // Obtengo el EstadoRenglonSolicitudCompra cuya denominacion sea igual a "Requerimiento Total"
                $estadoRenglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                        findOneBy(
                        array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_CON_REQUERIMIENTO_TOTAL), //
                        array('id' => 'desc'), 1, 0)
                ;
            } //.
            else {

                // Obtengo el EstadoRenglonSolicitudCompra cuya denominacion sea igual a "Requerimiento Parcial"
                $estadoRenglonSolicitudCompra = $em->getRepository('ADIFComprasBundle:EstadoRenglonSolicitudCompra')->
                        findOneBy(
                        array('denominacionEstadoRenglonSolicitudCompra' => ConstanteEstadoRenglonSolicitud::ESTADO_RENGLON_SOLICITUD_CON_REQUERIMIENTO_PARCIAL), //
                        array('id' => 'desc'), 1, 0)
                ;
            }
        }

        $renglonSolicitudCompra->setEstadoRenglonSolicitudCompra($estadoRenglonSolicitudCompra);
    }

    /**
     * Print a Requerimiento entity.
     *
     * @Route("/print/{id}", name="requerimiento_print")
     * @Method("GET")
     */
    public function printAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($id);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        $empresaSession = EmpresaSession::getInstance();
        $idEmpresa = $empresaSession->getIdEmpresa();
        
        $html = '<html><head><style type="text/css">' . $this->renderView('::PDF/mpdf.default.css.twig') . '</style></head><body>';
        $html .= $this->renderView(
                'ADIFComprasBundle:Requerimiento:print.show.html.twig', [
                    'entity' => $requerimiento,
                    'idEmpresa' => $idEmpresa
                ]
        );
        $html .= '</body></html>';

        $filename = 'requerimiento_' . $requerimiento->getNumero() . '.pdf';

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);

        $mpdfService->WriteHTML($html);
        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    /**
     * Valida que la fecha de Requerimiento sea posterior a los RenglonesSolicitur asociados
     *
     * @Route("/validar_fecha", name="requerimiento_validar_fecha")
     */
    public function validarFechaRequerimientoAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $renglonSolicitudCompraIds = array();

        $renglones = $request->request->get('renglones');

        if ($renglones) {
            foreach ($renglones as $renglon) {
                $renglonSolicitudCompraIds[] = $renglon["id"];
            }

            // A partir de los ids de los RenglonSolicitud obtener la Solicitud con mayor fecha
            $solicitud = $em->getRepository('ADIFComprasBundle:SolicitudCompra')
                    ->getSolicitudCompraMayorFechaByRenglonId($renglonSolicitudCompraIds);

            return new JsonResponse($solicitud->getFechaSolicitud()->format("d/m/Y"));
        } //. 
        else {
            return new JsonResponse();
        }
    }

    /**
     * @Route("/estados", name="requerimiento_estados")
     */
    public function listaEstadoRequerimientoAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFComprasBundle:EstadoRequerimiento', $this->getEntityManager());

        $query = $repository->createQueryBuilder('e')
                ->select('e.id', 'e.denominacionEstadoRequerimiento')
                ->orderBy('e.denominacionEstadoRequerimiento', 'ASC')
                ->getQuery()
                ->useResultCache(true, 36000, 'requerimiento_estados')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $query->getResult();
    }

    /**
     * @Route("/tiposcontratacion/", name="requerimiento_tiposcontratacion")
     */
    public function listaTipoContratacionAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFComprasBundle:TipoContratacion', $this->getEntityManager());

        $qb = $repository->createQueryBuilder('tc');

        $query = $qb
                ->add('select', "tc.id, CONCAT(tc.denominacionTipoContratacion, CONCAT(' (', CONCAT(tc.montoDesde, CONCAT(' - ', CONCAT(tc.montoHasta,')'))))) AS denominacion")
                ->addOrderBy('tc.montoDesde', 'ASC')
                ->addOrderBy('tc.montoHasta', 'ASC')
                ->getQuery()
                ->useResultCache(true, 36000, 'requerimiento_tiposcontratacion')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $query->getResult();
    }

    /**
     * Setea el EstadoRequerimiento a "Archivado"
     *
     * @Route("/archivar/{id}", name="requerimiento_archivar")
     */
    public function archivarRequerimientoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $requerimiento = $em->getRepository('ADIFComprasBundle:Requerimiento')->find($id);

        if (!$requerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Requerimiento.');
        }

        // Obtengo el EstadoRequerimiento cuya denominacion sea igual a "Desaprobado"
        $estadoRequerimiento = $em->getRepository('ADIFComprasBundle:EstadoRequerimiento')->
                findOneBy(
                array('denominacionEstadoRequerimiento' => ConstanteEstadoRequerimiento::ESTADO_REQUERIMIENTO_ARCHIVADO), //
                array('id' => 'desc'), 1, 0)
        ;

        $requerimiento->setEstadoRequerimiento($estadoRequerimiento);

        $em->persist($requerimiento);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'El requerimiento fue archivado correctamente.');

        return $this->redirect($this->generateUrl('requerimiento'));
    }

    /**
     * 
     * Retorna todos los Requerimiento cuyo usuario asociado pertenezca 
     * a la misma Area recibida como parámetro.
     * 
     * @param type $idArea
     */
    private function getRequerimientoByAreaId($idArea) {

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

        // Obtengo todos los requerimientos cuyo usuario sea alguno de los obtenidos
        $requerimientos = $emCompras
                ->createQuery(
                        "SELECT r FROM ADIFComprasBundle:Requerimiento r " .
                        "WHERE r.idUsuario IN (:usuarios)")
                ->setParameter('usuarios', $idsUsuarios, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);


        return $requerimientos->getResult();
    }

}
