<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\TipoImpuesto;
use ADIF\ContableBundle\Form\TipoImpuestoType;

/**
 * TipoImpuesto controller.
 *
 * @Route("/impuestos")
 */
class TipoImpuestoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Tipos de impuesto' => $this->generateUrl('impuestos')
        );
    }

    /**
     * Lists all TipoImpuesto entities.
     *
     * @Route("/", name="impuestos")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:TipoImpuesto')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de impuesto'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Tipos de impuesto',
            'page_info' => 'Lista de tipos de impuesto'
        );
    }

    /**
     * Creates a new TipoImpuesto entity.
     *
     * @Route("/insertar", name="impuestos_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:TipoImpuesto:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new TipoImpuesto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('impuestos'));
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
            'page_title' => 'Crear tipo de impuesto',
        );
    }

    /**
     * Creates a form to create a TipoImpuesto entity.
     *
     * @param TipoImpuesto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoImpuesto $entity) {
        $form = $this->createForm(new TipoImpuestoType(), $entity, array(
            'action' => $this->generateUrl('impuestos_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoImpuesto entity.
     *
     * @Route("/crear", name="impuestos_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new TipoImpuesto();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tipo de impuesto'
        );
    }

    /**
     * Finds and displays a TipoImpuesto entity.
     *
     * @Route("/{id}", name="impuestos_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:TipoImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoImpuesto.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['TipoImpuesto'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver tipo de impuesto'
        );
    }

    /**
     * Displays a form to edit an existing TipoImpuesto entity.
     *
     * @Route("/editar/{id}", name="impuestos_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:TipoImpuesto:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:TipoImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoImpuesto.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de impuesto'
        );
    }

    /**
     * Creates a form to edit a TipoImpuesto entity.
     *
     * @param TipoImpuesto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TipoImpuesto $entity) {
        $form = $this->createForm(new TipoImpuestoType(), $entity, array(
            'action' => $this->generateUrl('impuestos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing TipoImpuesto entity.
     *
     * @Route("/actualizar/{id}", name="impuestos_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:TipoImpuesto:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:TipoImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoImpuesto.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('impuestos'));
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
            'page_title' => 'Editar tipo de impuesto'
        );
    }

    /**
     * Deletes a TipoImpuesto entity.
     *
     * @Route("/borrar/{id}", name="impuestos_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:TipoImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoImpuesto.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('impuestos'));
    }

}
