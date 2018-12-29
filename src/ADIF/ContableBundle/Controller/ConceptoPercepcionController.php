<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\ConceptoPercepcion;
use ADIF\ContableBundle\Form\ConceptoPercepcionType;

/**
 * ConceptoPercepcion controller.
 *
 * @Route("/conceptopercepcion")
 */
class ConceptoPercepcionController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Conceptos de percepci&oacute;n' => $this->generateUrl('conceptopercepcion')
        );
    }

    /**
     * Lists all ConceptoPercepcion entities.
     *
     * @Route("/", name="conceptopercepcion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:ConceptoPercepcion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Conceptos de percepci&oacute;n'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Conceptos de percepci&oacute;n',
            'page_info' => 'Lista de conceptos de percepci&oacute;n'
        );
    }

    /**
     * Creates a new ConceptoPercepcion entity.
     *
     * @Route("/insertar", name="conceptopercepcion_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ConceptoPercepcion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ConceptoPercepcion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('conceptopercepcion'));
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
            'page_title' => 'Crear concepto de percepci&oacute;n',
        );
    }

    /**
     * Creates a form to create a ConceptoPercepcion entity.
     *
     * @param ConceptoPercepcion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ConceptoPercepcion $entity) {
        $form = $this->createForm(new ConceptoPercepcionType(), $entity, array(
            'action' => $this->generateUrl('conceptopercepcion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ConceptoPercepcion entity.
     *
     * @Route("/crear", name="conceptopercepcion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new ConceptoPercepcion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear concepto de percepci&oacute;n'
        );
    }

    /**
     * Finds and displays a ConceptoPercepcion entity.
     *
     * @Route("/{id}", name="conceptopercepcion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoPercepcion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPercepcion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Concepto de percepci&oacute;n'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver concepto de percepci&oacute;n'
        );
    }

    /**
     * Displays a form to edit an existing ConceptoPercepcion entity.
     *
     * @Route("/editar/{id}", name="conceptopercepcion_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ConceptoPercepcion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoPercepcion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPercepcion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar concepto de percepci&oacute;n'
        );
    }

    /**
     * Creates a form to edit a ConceptoPercepcion entity.
     *
     * @param ConceptoPercepcion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ConceptoPercepcion $entity) {
        $form = $this->createForm(new ConceptoPercepcionType(), $entity, array(
            'action' => $this->generateUrl('conceptopercepcion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ConceptoPercepcion entity.
     *
     * @Route("/actualizar/{id}", name="conceptopercepcion_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ConceptoPercepcion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoPercepcion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPercepcion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('conceptopercepcion'));
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
            'page_title' => 'Editar concepto de percepci&oacute;n'
        );
    }

    /**
     * Deletes a ConceptoPercepcion entity.
     *
     * @Route("/borrar/{id}", name="conceptopercepcion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:ConceptoPercepcion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPercepcion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('conceptopercepcion'));
    }

}
