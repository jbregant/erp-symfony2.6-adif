<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\EstadoSolicitudCompra;
use ADIF\ComprasBundle\Form\EstadoSolicitudCompraType;

/**
 * EstadoSolicitudCompra controller.
 *
 * @Route("/estadosolicitudcompra")
 */
class EstadoSolicitudCompraController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Estados de solicitudes de compra' => $this->generateUrl('estadosolicitudcompra')
        );
    }

    /**
     * Lists all EstadoSolicitudCompra entities.
     *
     * @Route("/", name="estadosolicitudcompra")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Estados de solicitudes de compra'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Estado de solicitud de compra',
            'page_info' => 'Lista de estados de solicitud de compra'
        );
    }

    /**
     * Creates a new EstadoSolicitudCompra entity.
     *
     * @Route("/insertar", name="estadosolicitudcompra_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:EstadoSolicitudCompra:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new EstadoSolicitudCompra();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estadosolicitudcompra'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear estado de solicitud de compra',
        );
    }

    /**
     * Creates a form to create a EstadoSolicitudCompra entity.
     *
     * @param EstadoSolicitudCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EstadoSolicitudCompra $entity) {
        $form = $this->createForm(new EstadoSolicitudCompraType(), $entity, array(
            'action' => $this->generateUrl('estadosolicitudcompra_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EstadoSolicitudCompra entity.
     *
     * @Route("/crear", name="estadosolicitudcompra_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new EstadoSolicitudCompra();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear estado de solicitud de compra'
        );
    }

    /**
     * Finds and displays a EstadoSolicitudCompra entity.
     *
     * @Route("/{id}", name="estadosolicitudcompra_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoSolicitudCompra.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionEstadoSolicitudCompra()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver estado de solicitud de compra'
        );
    }

    /**
     * Displays a form to edit an existing EstadoSolicitudCompra entity.
     *
     * @Route("/editar/{id}", name="estadosolicitudcompra_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:EstadoSolicitudCompra:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoSolicitudCompra.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionEstadoSolicitudCompra()] = $this->generateUrl('estadosolicitudcompra_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar estado de solicitud de compra'
        );
    }

    /**
     * Creates a form to edit a EstadoSolicitudCompra entity.
     *
     * @param EstadoSolicitudCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EstadoSolicitudCompra $entity) {
        $form = $this->createForm(new EstadoSolicitudCompraType(), $entity, array(
            'action' => $this->generateUrl('estadosolicitudcompra_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing EstadoSolicitudCompra entity.
     *
     * @Route("/actualizar/{id}", name="estadosolicitudcompra_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:EstadoSolicitudCompra:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:EstadoSolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoSolicitudCompra.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('estadosolicitudcompra'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionEstadoSolicitudCompra()] = $this->generateUrl('estadosolicitudcompra_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar estado de solicitud de compra'
        );
    }

    /**
     * Deletes a EstadoSolicitudCompra entity.
     *
     * @Route("/borrar/{id}", name="estadosolicitudcompra_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

}
