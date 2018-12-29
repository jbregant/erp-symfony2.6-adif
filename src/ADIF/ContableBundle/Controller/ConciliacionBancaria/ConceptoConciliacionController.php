<?php

namespace ADIF\ContableBundle\Controller\ConciliacionBancaria;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\ConciliacionBancaria\ConceptoConciliacion;
use ADIF\ContableBundle\Form\ConciliacionBancaria\ConceptoConciliacionType;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * ConciliacionBancaria\ConceptoConciliacion controller.
 *
 * @Route("/conceptoconciliacion")
 */
class ConceptoConciliacionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Conceptos de conciliaci&oacute;n' => $this->generateUrl('conceptoconciliacion')
        );
    }

    /**
     * Lists all ConciliacionBancaria\ConceptoConciliacion entities.
     *
     * @Route("/", name="conceptoconciliacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\ConceptoConciliacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Conceptos de conciliaci&oacute;n'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Conceptos de conciliaci&oacute;n',
            'page_info' => 'Lista de conceptos de conciliaci&oacute;n'
        );
    }

    /**
     * Creates a new ConciliacionBancaria\ConceptoConciliacion entity.
     *
     * @Route("/insertar", name="conceptoconciliacion_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ConciliacionBancaria\ConceptoConciliacion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ConceptoConciliacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('conceptoconciliacion'));
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
            'page_title' => 'Crear concepto de conciliaci&oacute;n',
        );
    }

    /**
     * Creates a form to create a ConciliacionBancaria\ConceptoConciliacion entity.
     *
     * @param ConceptoConciliacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ConceptoConciliacion $entity) {
        $form = $this->createForm(new ConceptoConciliacionType(), $entity, array(
            'action' => $this->generateUrl('conceptoconciliacion_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ConciliacionBancaria\ConceptoConciliacion entity.
     *
     * @Route("/crear", name="conceptoconciliacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new ConceptoConciliacion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear concepto de conciliaci&oacute;n'
        );
    }

    /**
     * Finds and displays a ConciliacionBancaria\ConceptoConciliacion entity.
     *
     * @Route("/{id}", name="conceptoconciliacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\ConceptoConciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\ConceptoConciliacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Concepto de conciliaci&oacute;n'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver concepto de conciliaci&oacute;n'
        );
    }

    /**
     * Displays a form to edit an existing ConciliacionBancaria\ConceptoConciliacion entity.
     *
     * @Route("/editar/{id}", name="conceptoconciliacion_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ConciliacionBancaria\ConceptoConciliacion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\ConceptoConciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\ConceptoConciliacion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar concepto de conciliaci&oacute;n'
        );
    }

    /**
     * Creates a form to edit a ConciliacionBancaria\ConceptoConciliacion entity.
     *
     * @param ConceptoConciliacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ConceptoConciliacion $entity) {
        $form = $this->createForm(new ConceptoConciliacionType(), $entity, array(
            'action' => $this->generateUrl('conceptoconciliacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ConciliacionBancaria\ConceptoConciliacion entity.
     *
     * @Route("/actualizar/{id}", name="conceptoconciliacion_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ConciliacionBancaria\ConceptoConciliacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\ConceptoConciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\ConceptoConciliacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('conceptoconciliacion'));
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
            'page_title' => 'Editar concepto de conciliaci&oacute;n'
        );
    }

    /**
     * Deletes a ConciliacionBancaria\ConceptoConciliacion entity.
     *
     * @Route("/borrar/{id}", name="conceptoconciliacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:ConciliacionBancaria\ConceptoConciliacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConciliacionBancaria\ConceptoConciliacion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('conceptoconciliacion'));
    }

}
