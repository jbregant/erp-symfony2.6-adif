<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ComprasBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\EstadoBienEconomico;
use ADIF\ComprasBundle\Form\EstadoBienEconomicoType;

/**
 * EstadoBienEconomico controller.
 *
 * @Route("/estadobieneconomico")
 */
class EstadoBienEconomicoController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Estados de bienes econ&oacute;micos' => $this->generateUrl('estadobieneconomico')
        );
    }

    /**
     * Lists all EstadoBienEconomico entities.
     *
     * @Route("/", name="estadobieneconomico")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:EstadoBienEconomico')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Estados de bienes econ&oacute;micos'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Estado bien econ&oacute;mico',
            'page_info' => 'Lista de estados'
        );
    }

    /**
     * Creates a new EstadoBienEconomico entity.
     *
     * @Route("/insertar", name="estadobieneconomico_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:EstadoBienEconomico:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new EstadoBienEconomico();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estadobieneconomico'));
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
            'page_title' => 'Crear estado de bien econ&oacute;mico',
        );
    }

    /**
     * Creates a form to create a EstadoBienEconomico entity.
     *
     * @param EstadoBienEconomico $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EstadoBienEconomico $entity) {
        $form = $this->createForm(new EstadoBienEconomicoType(), $entity, array(
            'action' => $this->generateUrl('estadobieneconomico_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EstadoBienEconomico entity.
     *
     * @Route("/crear", name="estadobieneconomico_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new EstadoBienEconomico();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear estado de bien econ&oacute;mico'
        );
    }

    /**
     * Finds and displays a EstadoBienEconomico entity.
     *
     * @Route("/{id}", name="estadobieneconomico_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:EstadoBienEconomico')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Estado Bien Económico.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionEstadoBienEconomico()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver estado de bien econ&oacute;mico'
        );
    }

    /**
     * Displays a form to edit an existing EstadoBienEconomico entity.
     *
     * @Route("/editar/{id}", name="estadobieneconomico_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:EstadoBienEconomico:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:EstadoBienEconomico')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Estado Bien Económico.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionEstadoBienEconomico()] = $this->generateUrl('estadobieneconomico_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar estado de bien econ&oacute;mico'
        );
    }

    /**
     * Creates a form to edit a EstadoBienEconomico entity.
     *
     * @param EstadoBienEconomico $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EstadoBienEconomico $entity) {
        $form = $this->createForm(new EstadoBienEconomicoType(), $entity, array(
            'action' => $this->generateUrl('estadobieneconomico_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing EstadoBienEconomico entity.
     *
     * @Route("/actualizar/{id}", name="estadobieneconomico_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:EstadoBienEconomico:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:EstadoBienEconomico')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Estado Bien Económico.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('estadobieneconomico'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionEstadoBienEconomico()] = $this->generateUrl('estadobieneconomico_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar estado de bien econ&oacute;mico'
        );
    }

    /**
     * Deletes a EstadoBienEconomico entity.
     *
     * @Route("/borrar/{id}", name="estadobieneconomico_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

}
