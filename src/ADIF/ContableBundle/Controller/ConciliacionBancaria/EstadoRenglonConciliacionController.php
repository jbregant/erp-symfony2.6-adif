<?php

namespace ADIF\ContableBundle\Controller\ConciliacionBancaria;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\ConciliacionBancaria\EstadoRenglonConciliacion;
use ADIF\ContableBundle\Form\ConciliacionBancaria\EstadoRenglonConciliacionType;

/**
 * ConciliacionBancaria\EstadoRenglonConciliacion controller.
 *
 * @Route("/estadorenglonconciliacion")
 */
class EstadoRenglonConciliacionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Estados de rengl&oacute;n de conciliaci&oacute;n' => $this->generateUrl('estadorenglonconciliacion')
        );
    }

    /**
     * Lists all ConciliacionBancaria\EstadoRenglonConciliacion entities.
     *
     * @Route("/", name="estadorenglonconciliacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\EstadoRenglonConciliacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Estados de rengl&oacute;n de conciliaci&oacute;n'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Estado de rengl&oacute;n de conciliaci&oacute;n',
            'page_info' => 'Lista de estados de rengl&oacute;n de conciliaci&oacute;n'
        );
    }

    /**
     * Creates a new ConciliacionBancaria\EstadoRenglonConciliacion entity.
     *
     * @Route("/insertar", name="estadorenglonconciliacion_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ConciliacionBancaria\EstadoRenglonConciliacion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new EstadoRenglonConciliacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estadorenglonconciliacion'));
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
            'page_title' => 'Crear estado de rengl&oacute;n de conciliaci&oacute;n',
        );
    }

    /**
     * Creates a form to create a ConciliacionBancaria\EstadoRenglonConciliacion entity.
     *
     * @param EstadoRenglonConciliacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EstadoRenglonConciliacion $entity) {
        $form = $this->createForm(new EstadoRenglonConciliacionType(), $entity, array(
            'action' => $this->generateUrl('estadorenglonconciliacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ConciliacionBancaria\EstadoRenglonConciliacion entity.
     *
     * @Route("/crear", name="estadorenglonconciliacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new EstadoRenglonConciliacion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear estado de rengl&oacute;n de conciliaci&oacute;n'
        );
    }

    /**
     * Finds and displays a ConciliacionBancaria\EstadoRenglonConciliacion entity.
     *
     * @Route("/{id}", name="estadorenglonconciliacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\EstadoRenglonConciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\EstadoRenglonConciliacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacion()] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver estado de rengl&oacute;n de conciliaci&oacute;n'
        );
    }

    /**
     * Displays a form to edit an existing ConciliacionBancaria\EstadoRenglonConciliacion entity.
     *
     * @Route("/editar/{id}", name="estadorenglonconciliacion_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ConciliacionBancaria\EstadoRenglonConciliacion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\EstadoRenglonConciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\EstadoRenglonConciliacion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar estado de rengl&oacute;n de conciliaci&oacute;n'
        );
    }

    /**
     * Creates a form to edit a ConciliacionBancaria\EstadoRenglonConciliacion entity.
     *
     * @param EstadoRenglonConciliacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EstadoRenglonConciliacion $entity) {
        $form = $this->createForm(new EstadoRenglonConciliacionType(), $entity, array(
            'action' => $this->generateUrl('estadorenglonconciliacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ConciliacionBancaria\EstadoRenglonConciliacion entity.
     *
     * @Route("/actualizar/{id}", name="estadorenglonconciliacion_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ConciliacionBancaria\EstadoRenglonConciliacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\EstadoRenglonConciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\EstadoRenglonConciliacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('estadorenglonconciliacion'));
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
            'page_title' => 'Editar estado de rengl&oacute;n de conciliaci&oacute;n'
        );
    }

    /**
     * Deletes a ConciliacionBancaria\EstadoRenglonConciliacion entity.
     *
     * @Route("/borrar/{id}", name="estadorenglonconciliacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\EstadoRenglonConciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\EstadoRenglonConciliacion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('estadorenglonconciliacion'));
    }

}
