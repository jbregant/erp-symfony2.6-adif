<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\EstudioEmpleado;
use ADIF\RecursosHumanosBundle\Form\EstudioEmpleadoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * EstudioEmpleado controller.
 *
 * @Route("/estudiosempleado")
 * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
 */
class EstudioEmpleadoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'EstudioEmpleado' => $this->generateUrl('estudiosempleado')
        );
    }

    /**
     * Lists all EstudioEmpleado entities.
     *
     * @Route("/", name="estudiosempleado")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:EstudioEmpleado')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['EstudioEmpleado'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'EstudioEmpleado',
            'page_info' => 'Lista de estudioempleado'
        );
    }

    /**
     * Creates a new EstudioEmpleado entity.
     *
     * @Route("/insertar", name="estudiosempleado_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:EstudioEmpleado:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new EstudioEmpleado();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estudiosempleado'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear EstudioEmpleado',
        );
    }

    /**
     * Creates a form to create a EstudioEmpleado entity.
     *
     * @param EstudioEmpleado $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EstudioEmpleado $entity) {
        $form = $this->createForm(new EstudioEmpleadoType(), $entity, array(
            'action' => $this->generateUrl('estudiosempleado_create'),
            'method' => 'POST',
            'entity_manager_rrhh' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EstudioEmpleado entity.
     *
     * @Route("/crear", name="estudiosempleado_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new EstudioEmpleado();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear EstudioEmpleado'
        );
    }

    /**
     * Finds and displays a EstudioEmpleado entity.
     *
     * @Route("/{id}", name="estudiosempleado_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EstudioEmpleado')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstudioEmpleado.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['EstudioEmpleado'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver EstudioEmpleado'
        );
    }

    /**
     * Displays a form to edit an existing EstudioEmpleado entity.
     *
     * @Route("/editar/{id}", name="estudiosempleado_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:EstudioEmpleado:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EstudioEmpleado')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstudioEmpleado.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar EstudioEmpleado'
        );
    }

    /**
     * Creates a form to edit a EstudioEmpleado entity.
     *
     * @param EstudioEmpleado $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EstudioEmpleado $entity) {
        $form = $this->createForm(new EstudioEmpleadoType(), $entity, array(
            'action' => $this->generateUrl('estudiosempleado_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_rrhh' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing EstudioEmpleado entity.
     *
     * @Route("/actualizar/{id}", name="estudiosempleado_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:EstudioEmpleado:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EstudioEmpleado')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstudioEmpleado.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('estudiosempleado'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar EstudioEmpleado'
        );
    }

    /**
     * Deletes a EstudioEmpleado entity.
     *
     * @Route("/borrar/{id}", name="estudiosempleado_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EstudioEmpleado')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstudioEmpleado.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('estudiosempleado'));
    }

}
