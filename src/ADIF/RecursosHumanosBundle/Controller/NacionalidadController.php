<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Nacionalidad;
use ADIF\RecursosHumanosBundle\Form\NacionalidadType;

/**
 * Nacionalidad controller.
 *
 * @Route("/nacionalidades")
 */
class NacionalidadController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Nacionalidad' => $this->generateUrl('nacionalidades')
        );
    }

    /**
     * Lists all Nacionalidad entities.
     *
     * @Route("/", name="nacionalidades")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Nacionalidad')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Nacionalidad'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Nacionalidad',
            'page_info' => 'Lista de nacionalidades'
        );
    }

    /**
     * Creates a new Nacionalidad entity.
     *
     * @Route("/insertar", name="nacionalidades_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Nacionalidad:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Nacionalidad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('nacionalidades'));
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
            'page_title' => 'Crear Nacionalidad',
        );
    }

    /**
     * Creates a form to create a Nacionalidad entity.
     *
     * @param Nacionalidad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Nacionalidad $entity) {
        $form = $this->createForm(new NacionalidadType(), $entity, array(
            'action' => $this->generateUrl('nacionalidades_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Nacionalidad entity.
     *
     * @Route("/crear", name="nacionalidades_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Nacionalidad();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Nacionalidad'
        );
    }

    /**
     * Finds and displays a Nacionalidad entity.
     *
     * @Route("/{id}", name="nacionalidades_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Nacionalidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Nacionalidad.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Nacionalidad'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Nacionalidad'
        );
    }

    /**
     * Displays a form to edit an existing Nacionalidad entity.
     *
     * @Route("/editar/{id}", name="nacionalidades_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Nacionalidad:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Nacionalidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Nacionalidad.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Nacionalidad'
        );
    }

    /**
     * Creates a form to edit a Nacionalidad entity.
     *
     * @param Nacionalidad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Nacionalidad $entity) {
        $form = $this->createForm(new NacionalidadType(), $entity, array(
            'action' => $this->generateUrl('nacionalidades_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Nacionalidad entity.
     *
     * @Route("/actualizar/{id}", name="nacionalidades_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Nacionalidad:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Nacionalidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Nacionalidad.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('nacionalidades'));
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
            'page_title' => 'Editar Nacionalidad'
        );
    }

    /**
     * Deletes a Nacionalidad entity.
     *
     * @Route("/borrar/{id}", name="nacionalidades_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Nacionalidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Nacionalidad.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('nacionalidades'));
    }

}
