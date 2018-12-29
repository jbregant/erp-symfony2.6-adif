<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\EmpleadoTipoContrato;
use ADIF\RecursosHumanosBundle\Form\EmpleadoTipoContratoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * EmpleadoTipoContrato controller.
 *
 * @Route("/empleados_tipocontrato")
 * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
 */
class EmpleadoTipoContratoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'EmpleadoTipoContrato' => $this->generateUrl('empleados_tipocontrato')
        );
    }

    /**
     * Lists all EmpleadoTipoContrato entities.
     *
     * @Route("/", name="empleados_tipocontrato")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoTipoContrato')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['EmpleadoTipoContrato'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'EmpleadoTipoContrato',
            'page_info' => 'Lista de empleadotipocontrato'
        );
    }

    /**
     * Creates a new EmpleadoTipoContrato entity.
     *
     * @Route("/insertar", name="empleados_tipocontrato_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:EmpleadoTipoContrato:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new EmpleadoTipoContrato();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('empleados_tipocontrato'));
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
            'page_title' => 'Crear EmpleadoTipoContrato',
        );
    }

    /**
     * Creates a form to create a EmpleadoTipoContrato entity.
     *
     * @param EmpleadoTipoContrato $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EmpleadoTipoContrato $entity) {
        $form = $this->createForm(new EmpleadoTipoContratoType($this->getDoctrine()->getManager($this->getEntityManager())), $entity, array(
            'action' => $this->generateUrl('empleados_tipocontrato_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EmpleadoTipoContrato entity.
     *
     * @Route("/crear", name="empleados_tipocontrato_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new EmpleadoTipoContrato();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear EmpleadoTipoContrato'
        );
    }

    /**
     * Finds and displays a EmpleadoTipoContrato entity.
     *
     * @Route("/{id}", name="empleados_tipocontrato_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoTipoContrato')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoTipoContrato.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['EmpleadoTipoContrato'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver EmpleadoTipoContrato'
        );
    }

    /**
     * Displays a form to edit an existing EmpleadoTipoContrato entity.
     *
     * @Route("/editar/{id}", name="empleados_tipocontrato_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:EmpleadoTipoContrato:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoTipoContrato')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoTipoContrato.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar EmpleadoTipoContrato'
        );
    }

    /**
     * Creates a form to edit a EmpleadoTipoContrato entity.
     *
     * @param EmpleadoTipoContrato $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EmpleadoTipoContrato $entity) {
        $form = $this->createForm(new EmpleadoTipoContratoType($this->getDoctrine()->getManager($this->getEntityManager())), $entity, array(
            'action' => $this->generateUrl('empleados_tipocontrato_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing EmpleadoTipoContrato entity.
     *
     * @Route("/actualizar/{id}", name="empleados_tipocontrato_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:EmpleadoTipoContrato:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoTipoContrato')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoTipoContrato.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('empleados_tipocontrato'));
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
            'page_title' => 'Editar EmpleadoTipoContrato'
        );
    }

    /**
     * Deletes a EmpleadoTipoContrato entity.
     *
     * @Route("/borrar/{id}", name="empleados_tipocontrato_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoTipoContrato')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoTipoContrato.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('empleados_tipocontrato'));
    }

}
