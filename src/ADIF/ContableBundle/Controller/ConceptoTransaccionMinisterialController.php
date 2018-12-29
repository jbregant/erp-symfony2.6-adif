<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\ConceptoTransaccionMinisterial;
use ADIF\ContableBundle\Form\ConceptoTransaccionMinisterialType;

/**
 * ConceptoTransaccionMinisterial controller.
 *
 * @Route("/conceptotransaccionministerial")
 */
class ConceptoTransaccionMinisterialController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Conceptos de transaccion ministerial' => $this->generateUrl('conceptotransaccionministerial')
        );
    }

    /**
     * Lists all ConceptoTransaccionMinisterial entities.
     *
     * @Route("/", name="conceptotransaccionministerial")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:ConceptoTransaccionMinisterial')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['ConceptoTransaccionMinisterial'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Conceptos de transaccion ministerial',
            'page_info' => 'Lista de conceptos de transaccion ministerial'
        );
    }

    /**
     * Creates a new ConceptoTransaccionMinisterial entity.
     *
     * @Route("/insertar", name="conceptotransaccionministerial_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ConceptoTransaccionMinisterial:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ConceptoTransaccionMinisterial();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('conceptotransaccionministerial'));
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
            'page_title' => 'Crear concepto de transacci&oacute;n ministerial',
        );
    }

    /**
     * Creates a form to create a ConceptoTransaccionMinisterial entity.
     *
     * @param ConceptoTransaccionMinisterial $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ConceptoTransaccionMinisterial $entity) {
        $form = $this->createForm(new ConceptoTransaccionMinisterialType(), $entity, array(
            'action' => $this->generateUrl('conceptotransaccionministerial_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ConceptoTransaccionMinisterial entity.
     *
     * @Route("/crear", name="conceptotransaccionministerial_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new ConceptoTransaccionMinisterial();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear concepto de transacci&oacute;n ministerial'
        );
    }

    /**
     * Finds and displays a ConceptoTransaccionMinisterial entity.
     *
     * @Route("/{id}", name="conceptotransaccionministerial_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoTransaccionMinisterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoTransaccionMinisterial.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Concepto de transacci&oacute;n ministerial'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver concepto de transacci&oacute;n ministerial'
        );
    }

    /**
     * Displays a form to edit an existing ConceptoTransaccionMinisterial entity.
     *
     * @Route("/editar/{id}", name="conceptotransaccionministerial_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ConceptoTransaccionMinisterial:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoTransaccionMinisterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoTransaccionMinisterial.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar concepto de transacci&oacute;n ministerial'
        );
    }

    /**
     * Creates a form to edit a ConceptoTransaccionMinisterial entity.
     *
     * @param ConceptoTransaccionMinisterial $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ConceptoTransaccionMinisterial $entity) {
        $form = $this->createForm(new ConceptoTransaccionMinisterialType(), $entity, array(
            'action' => $this->generateUrl('conceptotransaccionministerial_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ConceptoTransaccionMinisterial entity.
     *
     * @Route("/actualizar/{id}", name="conceptotransaccionministerial_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ConceptoTransaccionMinisterial:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoTransaccionMinisterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoTransaccionMinisterial.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('conceptotransaccionministerial'));
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
            'page_title' => 'Editar concepto de transacci&oacute;n ministerial'
        );
    }

    /**
     * Deletes a ConceptoTransaccionMinisterial entity.
     *
     * @Route("/borrar/{id}", name="conceptotransaccionministerial_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:ConceptoTransaccionMinisterial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoTransaccionMinisterial.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('conceptotransaccionministerial'));
    }

}
