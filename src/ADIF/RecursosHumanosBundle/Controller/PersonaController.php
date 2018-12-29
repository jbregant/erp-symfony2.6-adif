<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Persona;
use ADIF\RecursosHumanosBundle\Form\PersonaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Persona controller.
 *
 * @Route("/personas")
 * @Security("has_role('ROLE_VISTA_EMPLEADOS')")
 */
class PersonaController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Persona' => $this->generateUrl('personas')
        );
    }

    /**
     * Lists all Persona entities.
     *
     * @Route("/", name="personas")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Persona')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Persona'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Persona',
            'page_info' => 'Lista de persona'
        );
    }

    /**
     * Creates a new Persona entity.
     *
     * @Route("/insertar", name="personas_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Persona:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Persona();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('personas'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Persona',
        );
    }

    /**
     * Creates a form to create a Persona entity.
     *
     * @param Persona $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Persona $entity) {
        $form = $this->createForm(new PersonaType($this->getDoctrine()->getManager($this->getEntityManager())), $entity, array(
            'action' => $this->generateUrl('personas_create'),
            'method' => 'POST',
            'entity_manager_rrhh' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Persona entity.
     *
     * @Route("/crear", name="personas_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Persona();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Persona'
        );
    }

    /**
     * Finds and displays a Persona entity.
     *
     * @Route("/{id}", name="personas_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Persona')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Persona.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Persona'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Persona'
        );
    }

    /**
     * Displays a form to edit an existing Persona entity.
     *
     * @Route("/editar/{id}", name="personas_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Persona:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Persona')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Persona.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Persona'
        );
    }

    /**
     * Creates a form to edit a Persona entity.
     *
     * @param Persona $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Persona $entity) {
        $form = $this->createForm(new PersonaType($this->getDoctrine()->getManager($this->getEntityManager())), $entity, array(
            'action' => $this->generateUrl('personas_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_rrhh' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Persona entity.
     *
     * @Route("/actualizar/{id}", name="personas_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Persona:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Persona')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Persona.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('personas'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Persona'
        );
    }

    /**
     * Deletes a Persona entity.
     *
     * @Route("/borrar/{id}", name="personas_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Persona')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Persona.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('personas'));
    }

}
