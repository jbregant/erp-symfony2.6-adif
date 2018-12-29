<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\TipoSolicitudCompra;
use ADIF\ComprasBundle\Form\TipoSolicitudCompraType;

/**
 * TipoSolicitudCompra controller.
 *
 * @Route("/tiposolicitudcompra")
 */
class TipoSolicitudCompraController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Tipos de solicitudes de compra' => $this->generateUrl('tiposolicitudcompra')
        );
    }

    /**
     * Lists all TipoSolicitudCompra entities.
     *
     * @Route("/", name="tiposolicitudcompra")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:TipoSolicitudCompra')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de solicitudes de compra'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Tipo de solicitud de compra',
            'page_info' => 'Lista de tipos de solicitud compra'
        );
    }

    /**
     * Creates a new TipoSolicitudCompra entity.
     *
     * @Route("/insertar", name="tiposolicitudcompra_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:TipoSolicitudCompra:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new TipoSolicitudCompra();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tiposolicitudcompra'));
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
            'page_title' => 'Crear tipo de solicitud de compra',
        );
    }

    /**
     * Creates a form to create a TipoSolicitudCompra entity.
     *
     * @param TipoSolicitudCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoSolicitudCompra $entity) {
        $form = $this->createForm(new TipoSolicitudCompraType(), $entity, array(
            'action' => $this->generateUrl('tiposolicitudcompra_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoSolicitudCompra entity.
     *
     * @Route("/crear", name="tiposolicitudcompra_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new TipoSolicitudCompra();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tipo de solicitud de compra'
        );
    }

    /**
     * Finds and displays a TipoSolicitudCompra entity.
     *
     * @Route("/{id}", name="tiposolicitudcompra_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:TipoSolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo Solicitud Compra.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionTipoSolicitudCompra()] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver tipo de solicitud de compra'
        );
    }

    /**
     * Displays a form to edit an existing TipoSolicitudCompra entity.
     *
     * @Route("/editar/{id}", name="tiposolicitudcompra_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:TipoSolicitudCompra:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:TipoSolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo Solicitud Compra.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionTipoSolicitudCompra()] = $this->generateUrl('tiposolicitudcompra_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de solicitud de compra'
        );
    }

    /**
     * Creates a form to edit a TipoSolicitudCompra entity.
     *
     * @param TipoSolicitudCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TipoSolicitudCompra $entity) {
        $form = $this->createForm(new TipoSolicitudCompraType(), $entity, array(
            'action' => $this->generateUrl('tiposolicitudcompra_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing TipoSolicitudCompra entity.
     *
     * @Route("/actualizar/{id}", name="tiposolicitudcompra_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:TipoSolicitudCompra:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:TipoSolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo Solicitud Compra.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tiposolicitudcompra'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionTipoSolicitudCompra()] = $this->generateUrl('tiposolicitudcompra_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de solicitud de compra'
        );
    }

    /**
     * Deletes a TipoSolicitudCompra entity.
     *
     * @Route("/borrar/{id}", name="tiposolicitudcompra_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:TipoSolicitudCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo Solicitud Compra.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('tiposolicitudcompra'));
    }

}
