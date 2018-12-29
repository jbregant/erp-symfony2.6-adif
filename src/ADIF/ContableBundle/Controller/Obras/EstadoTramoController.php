<?php

namespace ADIF\ContableBundle\Controller\Obras;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Obras\EstadoTramo;
use ADIF\ContableBundle\Form\Obras\EstadoTramoType;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Obras\EstadoTramo controller.
 *
 * @Route("/estadotramo")
 */
class EstadoTramoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Estados de tramo' => $this->generateUrl('estadotramo')
        );
    }

    /**
     * Lists all Obras\EstadoTramo entities.
     *
     * @Route("/", name="estadotramo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Estados de tramo'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Estados de tramo',
            'page_info' => 'Lista de estados de tramo'
        );
    }

    /**
     * Tabla para Obras\EstadoTramo .
     *
     * @Route("/index_table/", name="estadotramo_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Obras\EstadoTramo')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Estados de tramo'] = null;

        return $this->render('ADIFContableBundle:Obras/EstadoTramo:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new Obras\EstadoTramo entity.
     *
     * @Route("/insertar", name="estadotramo_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Obras\EstadoTramo:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new EstadoTramo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estadotramo'));
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
            'page_title' => 'Crear estado de tramo',
        );
    }

    /**
     * Creates a form to create a Obras\EstadoTramo entity.
     *
     * @param EstadoTramo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EstadoTramo $entity) {
        $form = $this->createForm(new EstadoTramoType(), $entity, array(
            'action' => $this->generateUrl('estadotramo_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager(EntityManagers::getEmCompras()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Obras\EstadoTramo entity.
     *
     * @Route("/crear", name="estadotramo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new EstadoTramo();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear estado de tramo'
        );
    }

    /**
     * Finds and displays a Obras\EstadoTramo entity.
     *
     * @Route("/{id}", name="estadotramo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\EstadoTramo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\EstadoTramo.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Estado de tramo'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver estado de tramo'
        );
    }

    /**
     * Displays a form to edit an existing Obras\EstadoTramo entity.
     *
     * @Route("/editar/{id}", name="estadotramo_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Obras\EstadoTramo:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\EstadoTramo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\EstadoTramo.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar estado de tramo'
        );
    }

    /**
     * Creates a form to edit a Obras\EstadoTramo entity.
     *
     * @param EstadoTramo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EstadoTramo $entity) {
        $form = $this->createForm(new EstadoTramoType(), $entity, array(
            'action' => $this->generateUrl('estadotramo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager(EntityManagers::getEmCompras()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Obras\EstadoTramo entity.
     *
     * @Route("/actualizar/{id}", name="estadotramo_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Obras\EstadoTramo:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\EstadoTramo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\EstadoTramo.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('estadotramo'));
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
            'page_title' => 'Editar estado de tramo'
        );
    }

    /**
     * Deletes a Obras\EstadoTramo entity.
     *
     * @Route("/borrar/{id}", name="estadotramo_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Obras\EstadoTramo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\EstadoTramo.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('estadotramo'));
    }

}
