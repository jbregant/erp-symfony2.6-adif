<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\ConceptoOrdenPago;
use ADIF\ContableBundle\Form\ConceptoOrdenPagoType;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * ConceptoOrdenPago controller.
 *
 * @Route("/concepto_orden_pago")
 */
class ConceptoOrdenPagoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Conceptos de orden de pago' => $this->generateUrl('concepto_orden_pago')
        );
    }

    /**
     * Lists all ConceptoOrdenPago entities.
     *
     * @Route("/", name="concepto_orden_pago")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Conceptos de orden de pago'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Conceptos de orden de pago',
            'page_info' => 'Lista de conceptos de orden de pago'
        );
    }

    /**
     * Tabla para ConceptoOrdenPago .
     *
     * @Route("/index_table/", name="concepto_orden_pago_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:ConceptoOrdenPago')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Conceptos de orden de pago'] = null;

        return $this->render('ADIFContableBundle:ConceptoOrdenPago:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new ConceptoOrdenPago entity.
     *
     * @Route("/insertar", name="concepto_orden_pago_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ConceptoOrdenPago:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ConceptoOrdenPago();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('concepto_orden_pago'));
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
            'page_title' => 'Crear concepto de orden de pago',
        );
    }

    /**
     * Creates a form to create a ConceptoOrdenPago entity.
     *
     * @param ConceptoOrdenPago $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ConceptoOrdenPago $entity) {
        $form = $this->createForm(new ConceptoOrdenPagoType(), $entity, array(
            'action' => $this->generateUrl('concepto_orden_pago_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ConceptoOrdenPago entity.
     *
     * @Route("/crear", name="concepto_orden_pago_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new ConceptoOrdenPago();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear concepto de orden de pago'
        );
    }

    /**
     * Finds and displays a ConceptoOrdenPago entity.
     *
     * @Route("/{id}", name="concepto_orden_pago_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoOrdenPago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoOrdenPago.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Concepto de orden de pago'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver concepto de orden de pago'
        );
    }

    /**
     * Displays a form to edit an existing ConceptoOrdenPago entity.
     *
     * @Route("/editar/{id}", name="concepto_orden_pago_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ConceptoOrdenPago:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoOrdenPago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoOrdenPago.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar concepto de orden de pago'
        );
    }

    /**
     * Creates a form to edit a ConceptoOrdenPago entity.
     *
     * @param ConceptoOrdenPago $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ConceptoOrdenPago $entity) {
        $form = $this->createForm(new ConceptoOrdenPagoType(), $entity, array(
            'action' => $this->generateUrl('concepto_orden_pago_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ConceptoOrdenPago entity.
     *
     * @Route("/actualizar/{id}", name="concepto_orden_pago_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ConceptoOrdenPago:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoOrdenPago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoOrdenPago.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('concepto_orden_pago'));
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
            'page_title' => 'Editar concepto de orden de pago'
        );
    }

    /**
     * Deletes a ConceptoOrdenPago entity.
     *
     * @Route("/borrar/{id}", name="concepto_orden_pago_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:ConceptoOrdenPago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoOrdenPago.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('concepto_orden_pago'));
    }

}
