<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Familiar;
use ADIF\RecursosHumanosBundle\Form\FamiliarType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Familiar controller.
 *
 * @Route("/familiares")
 * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
 */
class FamiliarController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Familiar' => $this->generateUrl('familiares')
        );
    }

    /**
     * Lists all Familiar entities.
     *
     * @Route("/", name="familiares")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Familiar')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Familiar'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Familiar',
            'page_info' => 'Lista de familiar'
        );
    }

    /**
     * Creates a new Familiar entity.
     *
     * @Route("/insertar", name="familiares_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Familiar:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Familiar();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('familiares'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Familiar',
        );
    }

    /**
     * Creates a form to create a Familiar entity.
     *
     * @param Familiar $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Familiar $entity) {
        $form = $this->createForm(new FamiliarType(), $entity, array(
            'action' => $this->generateUrl('familiares_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Familiar entity.
     *
     * @Route("/crear", name="familiares_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Familiar();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Familiar'
        );
    }

    /**
     * Finds and displays a Familiar entity.
     *
     * @Route("/{id}", name="familiares_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Familiar')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Familiar.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Familiar'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Familiar'
        );
    }

    /**
     * Displays a form to edit an existing Familiar entity.
     *
     * @Route("/editar/{id}", name="familiares_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Familiar:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Familiar')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Familiar.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Familiar'
        );
    }

    /**
     * Creates a form to edit a Familiar entity.
     *
     * @param Familiar $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Familiar $entity) {
        $form = $this->createForm(new FamiliarType(), $entity, array(
            'action' => $this->generateUrl('familiares_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Familiar entity.
     *
     * @Route("/actualizar/{id}", name="familiares_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Familiar:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Familiar')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Familiar.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('familiares'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Familiar'
        );
    }

    /**
     * Deletes a Familiar entity.
     *
     * @Route("/borrar/{id}", name="familiares_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Familiar')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Familiar.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('familiares'));
    }

}
