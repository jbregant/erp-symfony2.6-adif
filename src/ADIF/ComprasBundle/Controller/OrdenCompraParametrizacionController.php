<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\OrdenCompraParametrizacion;
use ADIF\ComprasBundle\Form\OrdenCompraParametrizacionType;

/**
 * OrdenCompraParametrizacion controller.
 *
 * @Route("/orden_compra_parametrizacion")
 */
class OrdenCompraParametrizacionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Parametrizaci&oacute;n de orden de compra' => $this->generateUrl('orden_compra_parametrizacion')
        );
    }

    /**
     * Lists all OrdenCompraParametrizacion entities.
     *
     * @Route("/", name="orden_compra_parametrizacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:OrdenCompraParametrizacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Parametrizaci&oacute;n de orden de compra'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Parametrizaci&oacute;n de orden de compra',
            'page_info' => 'Parametrizaci&oacute;n de orden de compra'
        );
    }

    /**
     * Creates a new OrdenCompraParametrizacion entity.
     *
     * @Route("/insertar", name="orden_compra_parametrizacion_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:OrdenCompraParametrizacion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new OrdenCompraParametrizacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('orden_compra_parametrizacion'));
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
            'page_title' => 'Configurar parametrizaci&oacute;n de orden de compra',
        );
    }

    /**
     * Creates a form to create a OrdenCompraParametrizacion entity.
     *
     * @param OrdenCompraParametrizacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(OrdenCompraParametrizacion $entity) {
        $form = $this->createForm(new OrdenCompraParametrizacionType(), $entity, array(
            'action' => $this->generateUrl('orden_compra_parametrizacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new OrdenCompraParametrizacion entity.
     *
     * @Route("/crear", name="orden_compra_parametrizacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new OrdenCompraParametrizacion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Configurar parametrizaci&oacute;n de orden de compra'
        );
    }

    /**
     * Finds and displays a OrdenCompraParametrizacion entity.
     *
     * @Route("/{id}", name="orden_compra_parametrizacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:OrdenCompraParametrizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompraParametrizacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Detalle'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver parametrizaci&oacute;n de orden de compra'
        );
    }

    /**
     * Displays a form to edit an existing OrdenCompraParametrizacion entity.
     *
     * @Route("/editar/{id}", name="orden_compra_parametrizacion_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:OrdenCompraParametrizacion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:OrdenCompraParametrizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompraParametrizacion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Configurar parametrizaci&oacute;n de orden de compra'
        );
    }

    /**
     * Creates a form to edit a OrdenCompraParametrizacion entity.
     *
     * @param OrdenCompraParametrizacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(OrdenCompraParametrizacion $entity) {
        $form = $this->createForm(new OrdenCompraParametrizacionType(), $entity, array(
            'action' => $this->generateUrl('orden_compra_parametrizacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing OrdenCompraParametrizacion entity.
     *
     * @Route("/actualizar/{id}", name="orden_compra_parametrizacion_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:OrdenCompraParametrizacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:OrdenCompraParametrizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompraParametrizacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('orden_compra_parametrizacion'));
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
            'page_title' => 'Configurar parametrizaci&oacute;n de orden de compra'
        );
    }

    /**
     * Deletes a OrdenCompraParametrizacion entity.
     *
     * @Route("/borrar/{id}", name="orden_compra_parametrizacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:OrdenCompraParametrizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompraParametrizacion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('orden_compra_parametrizacion'));
    }

}
