<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\EstadoProveedor;
use ADIF\ComprasBundle\Form\EstadoProveedorType;

/**
 * EstadoProveedor controller.
 *
 * @Route("/estadoproveedor")
 */
class EstadoProveedorController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Estados de proveedores' => $this->generateUrl('estadoproveedor')
        );
    }

    /**
     * Lists all EstadoProveedor entities.
     *
     * @Route("/", name="estadoproveedor")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:EstadoProveedor')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Estados de proveedores'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Estado proveedor',
            'page_info' => 'Lista de estados de proveedores'
        );
    }

    /**
     * Creates a new EstadoProveedor entity.
     *
     * @Route("/insertar", name="estadoproveedor_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:EstadoProveedor:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new EstadoProveedor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estadoproveedor'));
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
            'page_title' => 'Crear estado de proveedor',
        );
    }

    /**
     * Creates a form to create a EstadoProveedor entity.
     *
     * @param EstadoProveedor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EstadoProveedor $entity) {
        $form = $this->createForm(new EstadoProveedorType(), $entity, array(
            'action' => $this->generateUrl('estadoproveedor_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EstadoProveedor entity.
     *
     * @Route("/crear", name="estadoproveedor_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new EstadoProveedor();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear estado de proveedor'
        );
    }

    /**
     * Finds and displays a EstadoProveedor entity.
     *
     * @Route("/{id}", name="estadoproveedor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:EstadoProveedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoProveedor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionEstadoProveedor()] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver estado de proveedor'
        );
    }

    /**
     * Displays a form to edit an existing EstadoProveedor entity.
     *
     * @Route("/editar/{id}", name="estadoproveedor_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:EstadoProveedor:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:EstadoProveedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoProveedor.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionEstadoProveedor()] = $this->generateUrl('estadoproveedor_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar estado de proveedor'
        );
    }

    /**
     * Creates a form to edit a EstadoProveedor entity.
     *
     * @param EstadoProveedor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EstadoProveedor $entity) {
        $form = $this->createForm(new EstadoProveedorType(), $entity, array(
            'action' => $this->generateUrl('estadoproveedor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing EstadoProveedor entity.
     *
     * @Route("/actualizar/{id}", name="estadoproveedor_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:EstadoProveedor:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:EstadoProveedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoProveedor.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('estadoproveedor'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionEstadoProveedor()] = $this->generateUrl('estadoproveedor_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar estado de proveedor'
        );
    }

    /**
     * Deletes a EstadoProveedor entity.
     *
     * @Route("/borrar/{id}", name="estadoproveedor_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

}
