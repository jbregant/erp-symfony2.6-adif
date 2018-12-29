<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Sector;
use ADIF\RecursosHumanosBundle\Form\SectorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Sector controller.
 *
 * @Route("/sectores")
 * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
 */
class SectorController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Sectores' => $this->generateUrl('sectores')
        );
    }

    /**
     * Lists all Sector entities.
     *
     * @Route("/", name="sectores")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Sector')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Sectores'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Sectores',
            'page_info' => 'Lista de sectores'
        );
    }

    /**
     * Creates a new Sector entity.
     *
     * @Route("/insertar", name="sectores_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Sector:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Sector();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('sectores'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear sector',
        );
    }

    /**
     * Creates a form to create a Sector entity.
     *
     * @param Sector $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Sector $entity) {
        $form = $this->createForm(new SectorType(), $entity, array(
            'action' => $this->generateUrl('sectores_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Sector entity.
     *
     * @Route("/crear", name="sectores_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Sector();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear sector'
        );
    }

    /**
     * Finds and displays a Sector entity.
     *
     * @Route("/{id}", name="sectores_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Sector')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Sector.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Sector'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver sector'
        );
    }

    /**
     * Displays a form to edit an existing Sector entity.
     *
     * @Route("/editar/{id}", name="sectores_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Sector:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Sector')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Sector.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar sector'
        );
    }

    /**
     * Creates a form to edit a Sector entity.
     *
     * @param Sector $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Sector $entity) {
        $form = $this->createForm(new SectorType(), $entity, array(
            'action' => $this->generateUrl('sectores_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Sector entity.
     *
     * @Route("/actualizar/{id}", name="sectores_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Sector:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Sector')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Sector.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('sectores'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar sector'
        );
    }

    /**
     * Deletes a Sector entity.
     *
     * @Route("/borrar/{id}", name="sectores_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

}
