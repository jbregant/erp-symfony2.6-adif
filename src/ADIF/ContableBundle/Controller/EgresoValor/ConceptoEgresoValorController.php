<?php

namespace ADIF\ContableBundle\Controller\EgresoValor;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\EgresoValor\ConceptoEgresoValor;
use ADIF\ContableBundle\Form\EgresoValor\ConceptoEgresoValorType;

/**
 * EgresoValor\ConceptoEgresoValor controller.
 *
 * @Route("/conceptoegresovalor")
 */
class ConceptoEgresoValorController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Conceptos de egreso de valor' => $this->generateUrl('conceptoegresovalor')
        );
    }

    /**
     * Lists all EgresoValor\ConceptoEgresoValor entities.
     *
     * @Route("/", name="conceptoegresovalor")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:EgresoValor\ConceptoEgresoValor')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Conceptos de egreso de valor'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Conceptos de egreso de valor',
            'page_info' => 'Lista de conceptos de egreso de valor'
        );
    }

    /**
     * Creates a new EgresoValor\ConceptoEgresoValor entity.
     *
     * @Route("/insertar", name="conceptoegresovalor_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:EgresoValor\ConceptoEgresoValor:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ConceptoEgresoValor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('conceptoegresovalor'));
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
            'page_title' => 'Crear concepto de egreso de valor',
        );
    }

    /**
     * Creates a form to create a EgresoValor\ConceptoEgresoValor entity.
     *
     * @param ConceptoEgresoValor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ConceptoEgresoValor $entity) {
        $form = $this->createForm(new ConceptoEgresoValorType(), $entity, array(
            'action' => $this->generateUrl('conceptoegresovalor_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EgresoValor\ConceptoEgresoValor entity.
     *
     * @Route("/crear", name="conceptoegresovalor_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new ConceptoEgresoValor();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear concepto de egreso de valor'
        );
    }

    /**
     * Finds and displays a EgresoValor\ConceptoEgresoValor entity.
     *
     * @Route("/{id}", name="conceptoegresovalor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\ConceptoEgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\ConceptoEgresoValor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->__toString()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver concepto de egreso de valor'
        );
    }

    /**
     * Displays a form to edit an existing EgresoValor\ConceptoEgresoValor entity.
     *
     * @Route("/editar/{id}", name="conceptoegresovalor_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:EgresoValor\ConceptoEgresoValor:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\ConceptoEgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\ConceptoEgresoValor.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar concepto de egreso de valor'
        );
    }

    /**
     * Creates a form to edit a EgresoValor\ConceptoEgresoValor entity.
     *
     * @param ConceptoEgresoValor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ConceptoEgresoValor $entity) {
        $form = $this->createForm(new ConceptoEgresoValorType(), $entity, array(
            'action' => $this->generateUrl('conceptoegresovalor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing EgresoValor\ConceptoEgresoValor entity.
     *
     * @Route("/actualizar/{id}", name="conceptoegresovalor_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:EgresoValor\ConceptoEgresoValor:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\ConceptoEgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\ConceptoEgresoValor.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('conceptoegresovalor'));
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
            'page_title' => 'Editar concepto de egreso de valor'
        );
    }

    /**
     * Deletes a EgresoValor\ConceptoEgresoValor entity.
     *
     * @Route("/borrar/{id}", name="conceptoegresovalor_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\ConceptoEgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\ConceptoEgresoValor.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('conceptoegresovalor'));
    }

}
