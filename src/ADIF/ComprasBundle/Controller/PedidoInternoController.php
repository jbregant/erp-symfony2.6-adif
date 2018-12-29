<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoPedidoInterno;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoRenglonPedidoInterno;
use ADIF\ComprasBundle\Entity\PedidoInterno;
use ADIF\ComprasBundle\Form\PedidoInternoType;


/**
 * PedidoInterno controller.
 *
 * @Route("/pedidointerno")
 */
class PedidoInternoController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Pedidos internos' => $this->generateUrl('pedidointerno')
        );
    }

    /**
     * Lists all PedidoInterno entities.
     *
     * @Route("/", name="pedidointerno")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Pedidos internos'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Pedido interno',
            'page_info' => 'Lista de pedidos internos'
        );
    }

    /**
     * Tabla para PedidoInterno.
     *
     * @Route("/index_table/", name="pedidointerno_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        // Obtengo los pedidos creados por el usuario logueado
        $pedidosInternosEnviadosUsuario = $em->getRepository('ADIFComprasBundle:PedidoInterno')->
                findBy(array('idUsuario' => $this->getUser()->getId()));

        $bread = $this->base_breadcrumbs;
        $bread['Pedidos internos'] = null;

        return $this->render('ADIFComprasBundle:PedidoInterno:index_table.html.twig', array(
                    'entities' => $pedidosInternosEnviadosUsuario
                        )
        );
    }

    /**
     * Creates a new PedidoInterno entity.
     *
     * @Route("/insertar", name="pedidointerno_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:PedidoInterno:new.html.twig")
     */
    public function createAction(Request $request) {

        $pedidoInterno = new PedidoInterno();
        $form = $this->createCreateForm($pedidoInterno);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            //Al PedidoInterno le seteo el Usuario
            $pedidoInterno->setUsuario($this->getUser());

            // Inicializo los renglones del PedidoInterno
            $this->initRenglonesPedidoInterno($request, $pedidoInterno);

            // Inicializo el PedidoInterno
            $this->initPedidoInterno($request, $pedidoInterno);

            $em->persist($pedidoInterno);
            $em->flush();

            return $this->redirect($this->generateUrl('pedidointerno'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $pedidoInterno,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear pedido interno',
        );
    }

    /**
     * Creates a form to create a PedidoInterno entity.
     *
     * @param PedidoInterno $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PedidoInterno $entity) {
        $form = $this->createForm(new PedidoInternoType(), $entity, array(
            'action' => $this->generateUrl('pedidointerno_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager(EntityManagers::getEmCompras()),
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
        ));

        $form
                ->add('save', 'submit', array(
                    'label' => 'Guardar borrador'
                ))
                ->add('send', 'submit', array(
                    'label' => 'Enviar pedido'
                ))
        ;

        return $form;
    }

    /**
     * Displays a form to create a new PedidoInterno entity.
     *
     * @Route("/crear", name="pedidointerno_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new PedidoInterno();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear pedido interno'
        );
    }

    /**
     * Finds and displays a PedidoInterno entity.
     *
     * @Route("/detalle/{id}", name="pedidointerno_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:PedidoInterno')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PedidoInterno.');
        }

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
        $centroCosto = $emContable->getRepository('ADIFContableBundle:CentroCosto')->find($entity->getIdCentroCosto());

        if (!$centroCosto) {
            throw $this->createNotFoundException('No se puede encontrar el centro de costo.');
        } else{
            $entity->setCentroCosto($centroCosto);

            if ($entity->getIdCentroCosto() != null) {
                $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
                $centroCosto = $emContable->getRepository('ADIFContableBundle:CentroCosto')->find($entity->getIdCentroCosto());

                if (!$centroCosto) {
                    throw $this->createNotFoundException('No se puede encontrar el centro de costo.');
                } else {
                    $entity->setCentroCosto($centroCosto);
                }
            }

            $bread = $this->base_breadcrumbs;
            $bread['Pedido ' . $entity->getFechaPedido()->format("d/m/Y")] = null;

            return array(
                'entity' => $entity,
                'breadcrumbs' => $bread,
                'page_title' => 'Ver pedido interno'
            );
        }
    }

    /**
     * Displays a form to edit an existing PedidoInterno entity.
     *
     * @Route("/editar/{id}", name="pedidointerno_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:PedidoInterno:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:PedidoInterno')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PedidoInterno.');
        }

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
        $centroCosto = $emContable->getRepository('ADIFContableBundle:CentroCosto')->find($entity->getIdCentroCosto());
        if ($entity->getIdCentroCosto() != null) {
            $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

            $centroCosto = $emContable->getRepository('ADIFContableBundle:CentroCosto')->find($entity->getIdCentroCosto());

            if (!$centroCosto) {
                throw $this->createNotFoundException('No se puede encontrar el centro de costo.');
            } else{
                $entity->setCentroCosto($centroCosto);
            }
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar pedido interno'
        );
    }

    /**
     * Creates a form to edit a PedidoInterno entity.
     *
     * @param PedidoInterno $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PedidoInterno $entity) {
        $form = $this->createForm(new PedidoInternoType(), $entity, array(
            'action' => $this->generateUrl('pedidointerno_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
        ));

        $form
                ->add('save', 'submit', array(
                    'label' => 'Guardar borrador'
                ))
                ->add('send', 'submit', array(
                    'label' => 'Enviar pedido'
                ))
        ;

        return $form;
    }

    /**
     * Edits an existing PedidoInterno entity.
     *
     * @Route("/actualizar/{id}", name="pedidointerno_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:PedidoInterno:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $pedidoInterno = $em->getRepository('ADIFComprasBundle:PedidoInterno')->find($id);

        if (!$pedidoInterno) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PedidoInterno.');
        }

        $renglonesOriginales = new ArrayCollection();

        // Creo un ArrayCollection de los RenglonPedidoInterno actuales en la BBDD
        foreach ($pedidoInterno->getRenglonesPedidoInterno() as $renglonPedidoInterno) {
            $renglonesOriginales->add($renglonPedidoInterno);
        }

        $editForm = $this->createEditForm($pedidoInterno);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // Inicializo los renglones del PedidoInterno
            $this->initRenglonesPedidoInterno($request, $pedidoInterno);

            // Inicializo el PedidoInterno
            $this->initPedidoInterno($request, $pedidoInterno);

            // Por cada RenglonSolicitudCompra original
            foreach ($renglonesOriginales as $renglonPedidoInterno) {

                // Si fue eliminado
                if (false === $pedidoInterno->getRenglonesPedidoInterno()->contains($renglonPedidoInterno)) {

                    $pedidoInterno->removeRenglonesPedidoInterno($renglonPedidoInterno);

                    $em->remove($renglonPedidoInterno);
                }
            }

            $em->flush();

            return $this->redirect($this->generateUrl('pedidointerno'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $pedidoInterno,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar pedido interno'
        );
    }

    /**
     * Deletes a PedidoInterno entity.
     *
     * @Route("/borrar/{id}", name="pedidointerno_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:PedidoInterno')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PedidoInterno.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('pedidointerno'));
    }

    /**
     * 
     * @param type $request
     * @param type $pedidoInterno
     */
    private function initPedidoInterno($request, $pedidoInterno) {

        // Al PedidoInterno le seteo el EstadoPedidoInterno
        $this->setEstadoAPedidoInterno($request, $pedidoInterno);

        // Al PedidoInterno le seteo la Justificacion
        $this->setJustificacionAPedidoInterno($pedidoInterno);
    }

    /**
     * 
     * @param type $request
     * @param type $pedidoInterno
     * @param type $estadoPedidoInterno
     */
    private function setEstadoAPedidoInterno($request, $pedidoInterno, $estadoPedidoInterno = null) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if (null != $request) {

            $accion = $request->request->get('accion');

            if (null != $accion) {

                // Si se apretó el boton "Guardar borrador"
                if ('save' == $accion) {

                    // Obtengo el EstadoPedidoInterno cuya denominacion sea igual a "Borrador"
                    $estadoPedidoInterno = $em->getRepository('ADIFComprasBundle:EstadoPedidoInterno')->
                            findOneBy(
                            array('denominacionEstadoPedidoInterno' => ConstanteEstadoPedidoInterno::ESTADO_PEDIDO_BORRADOR), //
                            array('id' => 'desc'), 1, 0)
                    ;

                    $pedidoInterno->setEstadoPedidoInterno($estadoPedidoInterno);
                }

                // Si se apretó el boton "Enviar pedido"
                else if ('send' == $accion) {

                    // Obtengo el EstadoPedidoInterno cuya denominacion sea igual a "Enviado"
                    $estadoPedidoInterno = $em->getRepository('ADIFComprasBundle:EstadoPedidoInterno')->
                            findOneBy(
                            array('denominacionEstadoPedidoInterno' => ConstanteEstadoPedidoInterno::ESTADO_PEDIDO_ENVIADO), //
                            array('id' => 'desc'), 1, 0)
                    ;

                    $pedidoInterno->setEstadoPedidoInterno($estadoPedidoInterno);
                }
            }
        }
    }

    /**
     * 
     * @param type $pedidoInterno
     */
    private function setJustificacionAPedidoInterno($pedidoInterno) {

        if (null != $pedidoInterno->getJustificacion() && //
                null != $pedidoInterno->getJustificacion()->getArchivo()) {

            $pedidoInterno->getJustificacion()
                    ->setNombre($pedidoInterno->getJustificacion()->getArchivo()->getClientOriginalName());

            $pedidoInterno->getJustificacion()->setPedidoInterno($pedidoInterno);
        }
    }

    /**
     * 
     * @param type $pedidoInterno
     */
    private function initRenglonesPedidoInterno(Request $request, $pedidoInterno) {

        foreach ($pedidoInterno->getRenglonesPedidoInterno() as $renglonPedidoInterno) {

            // A cada RenglonPedidoInterno le seteo el PedidoInterno
            $renglonPedidoInterno->setPedidoInterno($pedidoInterno);

            // A cada RenglonPedidoInterno le seteo el EstadoRenglonPedidoInterno
            $this->setEstadoARenglonPedidoInterno($request, $renglonPedidoInterno);

            // Inicializo la cantidad pendiente
            $renglonPedidoInterno->setCantidadPendiente($renglonPedidoInterno->getCantidadSolicitada());
        }
    }

    /**
     * 
     * @param type $request
     * @param type $renglonPedidoInterno
     */
    private function setEstadoARenglonPedidoInterno($request, $renglonPedidoInterno) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if (null != $request) {

            $accion = $request->request->get('accion');

            if (null != $accion) {

                // Si se apretó el boton "Guardar borrador"
                if ('save' == $accion) {

                    // Obtengo el EstadoRenglonPedidoInterno == "Borrador"
                    $estadoRenglonPedidoInterno = $em->getRepository('ADIFComprasBundle:EstadoRenglonPedidoInterno')->
                            findOneBy(
                            array('denominacionEstadoRenglonPedidoInterno' => ConstanteEstadoRenglonPedidoInterno::ESTADO_RENGLON_PEDIDO_BORRADOR), //
                            array('id' => 'desc'), 1, 0)
                    ;

                    $renglonPedidoInterno->setEstadoRenglonPedidoInterno($estadoRenglonPedidoInterno);
                }

                // Si se apretó el boton "Enviar pedido"
                else if ('send' == $accion) {

                    // Obtengo el EstadoRenglonPedidoInterno == "Pendiente Envío"
                    $estadoRenglonPedidoInterno = $em->getRepository('ADIFComprasBundle:EstadoRenglonPedidoInterno')->
                            findOneBy(
                            array('denominacionEstadoRenglonPedidoInterno' => ConstanteEstadoRenglonPedidoInterno::ESTADO_RENGLON_PEDIDO_ENVIADO), //
                            array('id' => 'desc'), 1, 0)
                    ;

                    $renglonPedidoInterno->setEstadoRenglonPedidoInterno($estadoRenglonPedidoInterno);
                }
            }
        }
    }

    /**
     * @Route("/estados/", name="pedidointerno_estados")
     */
    public function listaEstadoPedidoInternoAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFComprasBundle:EstadoPedidoInterno', $this->getEntityManager());

        $query = $repository->createQueryBuilder('e')
                ->select('e.id', 'e.denominacionEstadoPedidoInterno')
                ->orderBy('e.denominacionEstadoPedidoInterno', 'ASC')
                ->getQuery()
                ->useResultCache(true, 36000, 'pedidointerno_estados')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return new JsonResponse($query->getResult());
    }

    /**
     * @Route("/actualizar_cantidades/", name="pedidointerno_actualizar_cantidades")
     */
    public function actualizarCantidadPendienteAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idRenglonPedidoInterno = json_decode($request->request->get('id'));

        $renglonPedidoInterno = $em->getRepository('ADIFComprasBundle:RenglonPedidoInterno')->find($idRenglonPedidoInterno);

        if (!$renglonPedidoInterno) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonPedidoInterno.');
        }

        $cantidadActual = json_decode($request->request->get('cantidad'));

        $renglonPedidoInterno->setCantidadPendiente($cantidadActual);

        if ($cantidadActual == 0) {

            // Obtengo el EstadoRenglonPedidoInterno == "Con Stock"
            $estadoRenglonPedidoInterno = $em->getRepository('ADIFComprasBundle:EstadoRenglonPedidoInterno')->
                    findOneBy(
                    array('denominacionEstadoRenglonPedidoInterno' => ConstanteEstadoRenglonPedidoInterno::ESTADO_RENGLON_PEDIDO_CON_STOCK), //
                    array('id' => 'desc'), 1, 0)
            ;

            $renglonPedidoInterno->setEstadoRenglonPedidoInterno($estadoRenglonPedidoInterno);


            // Obtengo el EstadoPedidoInterno == "Con Stock"
            $estadoPedidoInterno = $em->getRepository('ADIFComprasBundle:EstadoPedidoInterno')->
                    findOneBy(
                    array('denominacionEstadoPedidoInterno' => ConstanteEstadoPedidoInterno::ESTADO_PEDIDO_CON_STOCK), //
                    array('id' => 'desc'), 1, 0)
            ;

            $renglonPedidoInterno->getPedidoInterno()->setEstadoPedidoInterno($estadoPedidoInterno);
        }

        $em->persist($renglonPedidoInterno);
        $em->flush();

        return new JsonResponse();
    }

    /**
     * Setea el EstadoPedidoInterno a "Anulado"
     *
     * @Route("/anular/{id}", name="pedidointerno_anular")
     */
    public function anularPedidoInternoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $pedidoInterno = $em->getRepository('ADIFComprasBundle:PedidoInterno')->find($id);

        if (!$pedidoInterno) {
            throw $this->createNotFoundException('No se puede encontrar la entidad PedidoInterno.');
        }

        // Obtengo el EstadoPedidoInterno cuya denominacion sea igual a "Anulada"
        $estadoPedidoInterno = $em->getRepository('ADIFComprasBundle:EstadoPedidoInterno')->
                findOneBy(
                array('denominacionEstadoPedidoInterno' => ConstanteEstadoPedidoInterno::ESTADO_PEDIDO_ANULADO), //
                array('id' => 'desc'), 1, 0)
        ;

        $pedidoInterno->setEstadoPedidoInterno($estadoPedidoInterno);


        // Obtengo el EstadoRenglonPedidoInterno == "Anulado"
        $estadoRenglonPedidoInterno = $em->getRepository('ADIFComprasBundle:EstadoRenglonPedidoInterno')->
                findOneBy(
                array('denominacionEstadoRenglonPedidoInterno' => ConstanteEstadoRenglonPedidoInterno::ESTADO_RENGLON_PEDIDO_ANULADO), //
                array('id' => 'desc'), 1, 0)
        ;

        // A cada RenglonPedidoInterno le seteo el estado
        foreach ($pedidoInterno->getRenglonesPedidoInterno() as $renglonPedidoInterno) {
            $renglonPedidoInterno->setEstadoRenglonPedidoInterno($estadoRenglonPedidoInterno);
        }

        $em->persist($pedidoInterno);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'El pedido interno fue anulado correctamente.');

        return $this->redirect($this->generateUrl('pedidointerno'));
    }

}
