<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\ContactoEmergencia;
use ADIF\RecursosHumanosBundle\Form\ContactoEmergenciaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * ContactoEmergencia controller.
 *
 * @Route("/contactos_emergencia")
 * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
 */
class ContactoEmergenciaController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'ContactoEmergencia' => $this->generateUrl('contactos_emergencia')
        );
    }

    /**
     * Lists all ContactoEmergencia entities.
     *
     * @Route("/", name="contactos_emergencia")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:ContactoEmergencia')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['ContactoEmergencia'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'ContactoEmergencia',
            'page_info' => 'Lista de contactoemergencia'
        );
    }

    /**
     * Creates a new ContactoEmergencia entity.
     *
     * @Route("/insertar", name="contactos_emergencia_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:ContactoEmergencia:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ContactoEmergencia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('contactos_emergencia'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear ContactoEmergencia',
        );
    }

    /**
     * Creates a form to create a ContactoEmergencia entity.
     *
     * @param ContactoEmergencia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ContactoEmergencia $entity) {
        $form = $this->createForm(new ContactoEmergenciaType(), $entity, array(
            'action' => $this->generateUrl('contactos_emergencia_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ContactoEmergencia entity.
     *
     * @Route("/crear", name="contactos_emergencia_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new ContactoEmergencia();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear ContactoEmergencia'
        );
    }

    /**
     * Finds and displays a ContactoEmergencia entity.
     *
     * @Route("/{id}", name="contactos_emergencia_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ContactoEmergencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ContactoEmergencia.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['ContactoEmergencia'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver ContactoEmergencia'
        );
    }

    /**
     * Displays a form to edit an existing ContactoEmergencia entity.
     *
     * @Route("/editar/{id}", name="contactos_emergencia_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:ContactoEmergencia:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ContactoEmergencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ContactoEmergencia.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar ContactoEmergencia'
        );
    }

    /**
     * Creates a form to edit a ContactoEmergencia entity.
     *
     * @param ContactoEmergencia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ContactoEmergencia $entity) {
        $form = $this->createForm(new ContactoEmergenciaType(), $entity, array(
            'action' => $this->generateUrl('contactos_emergencia_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ContactoEmergencia entity.
     *
     * @Route("/actualizar/{id}", name="contactos_emergencia_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:ContactoEmergencia:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ContactoEmergencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ContactoEmergencia.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('contactos_emergencia'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar ContactoEmergencia'
        );
    }

    /**
     * Deletes a ContactoEmergencia entity.
     *
     * @Route("/borrar/{id}", name="contactos_emergencia_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ContactoEmergencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ContactoEmergencia.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('contactos_emergencia'));
    }

}
