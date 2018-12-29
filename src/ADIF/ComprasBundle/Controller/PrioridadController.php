<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\Prioridad;
use ADIF\ComprasBundle\Form\PrioridadType;

/**
 * Prioridad controller.
 *
 * @Route("/prioridad")
 */
class PrioridadController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Prioridades' => $this->generateUrl('prioridad')
        );
    }

    /**
     * Lists all Prioridad entities.
     *
     * @Route("/", name="prioridad")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:Prioridad')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Prioridades'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Prioridad',
            'page_info' => 'Lista de prioridades'
        );
    }

    /**
     * Creates a new Prioridad entity.
     *
     * @Route("/insertar", name="prioridad_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:Prioridad:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Prioridad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('prioridad'));
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
            'page_title' => 'Crear prioridad',
        );
    }

    /**
     * Creates a form to create a Prioridad entity.
     *
     * @param Prioridad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Prioridad $entity) {
        $form = $this->createForm(new PrioridadType(), $entity, array(
            'action' => $this->generateUrl('prioridad_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Prioridad entity.
     *
     * @Route("/crear", name="prioridad_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Prioridad();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear prioridad'
        );
    }

    /**
     * Finds and displays a Prioridad entity.
     *
     * @Route("/{id}", name="prioridad_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:Prioridad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Prioridad.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionPrioridad()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver prioridad'
        );
    }

    /**
     * Displays a form to edit an existing Prioridad entity.
     *
     * @Route("/editar/{id}", name="prioridad_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Prioridad:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:Prioridad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Prioridad.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionPrioridad()] = $this->generateUrl('prioridad_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar prioridad'
        );
    }

    /**
     * Creates a form to edit a Prioridad entity.
     *
     * @param Prioridad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Prioridad $entity) {
        $form = $this->createForm(new PrioridadType(), $entity, array(
            'action' => $this->generateUrl('prioridad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Prioridad entity.
     *
     * @Route("/actualizar/{id}", name="prioridad_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:Prioridad:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:Prioridad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Prioridad.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('prioridad'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionPrioridad()] = $this->generateUrl('prioridad_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar prioridad'
        );
    }

    /**
     * Deletes a Prioridad entity.
     *
     * @Route("/borrar/{id}", name="prioridad_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

}
