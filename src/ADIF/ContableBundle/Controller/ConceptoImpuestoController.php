<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\ConceptoImpuesto;
use ADIF\ContableBundle\Form\ConceptoImpuestoType;

/**
 * ConceptoImpuesto controller.
 *
 * @Route("/conceptoimpuesto")
 */
class ConceptoImpuestoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Conceptos de impuesto' => $this->generateUrl('conceptoimpuesto')
        );
    }

    /**
     * Lists all ConceptoImpuesto entities.
     *
     * @Route("/", name="conceptoimpuesto")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:ConceptoImpuesto')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Conceptos de impuesto'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Conceptos de impuesto',
            'page_info' => 'Lista de conceptos de impuesto'
        );
    }

    /**
     * Creates a new ConceptoImpuesto entity.
     *
     * @Route("/insertar", name="conceptoimpuesto_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ConceptoImpuesto:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ConceptoImpuesto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('conceptoimpuesto'));
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
            'page_title' => 'Crear concepto de impuesto',
        );
    }

    /**
     * Creates a form to create a ConceptoImpuesto entity.
     *
     * @param ConceptoImpuesto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ConceptoImpuesto $entity) {
        $form = $this->createForm(new ConceptoImpuestoType(), $entity, array(
            'action' => $this->generateUrl('conceptoimpuesto_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ConceptoImpuesto entity.
     *
     * @Route("/crear", name="conceptoimpuesto_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new ConceptoImpuesto();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear concepto de impuesto'
        );
    }

    /**
     * Finds and displays a ConceptoImpuesto entity.
     *
     * @Route("/{id}", name="conceptoimpuesto_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoImpuesto.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Concepto de impuesto'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver concepto de impuesto'
        );
    }

    /**
     * Displays a form to edit an existing ConceptoImpuesto entity.
     *
     * @Route("/editar/{id}", name="conceptoimpuesto_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ConceptoImpuesto:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoImpuesto.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar concepto de impuesto'
        );
    }

    /**
     * Creates a form to edit a ConceptoImpuesto entity.
     *
     * @param ConceptoImpuesto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ConceptoImpuesto $entity) {
        $form = $this->createForm(new ConceptoImpuestoType(), $entity, array(
            'action' => $this->generateUrl('conceptoimpuesto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ConceptoImpuesto entity.
     *
     * @Route("/actualizar/{id}", name="conceptoimpuesto_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ConceptoImpuesto:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoImpuesto.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('conceptoimpuesto'));
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
            'page_title' => 'Editar concepto de impuesto'
        );
    }

    /**
     * Deletes a ConceptoImpuesto entity.
     *
     * @Route("/borrar/{id}", name="conceptoimpuesto_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:ConceptoImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoImpuesto.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('conceptoimpuesto'));
    }

}
