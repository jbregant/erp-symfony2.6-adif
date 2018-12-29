<?php

namespace ADIF\ContableBundle\Controller\Facturacion;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Facturacion\ConceptoVentaGeneral;
use ADIF\ContableBundle\Form\Facturacion\ConceptoVentaGeneralType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Facturacion\ConceptoVentaGeneral controller.
 *
 * @Route("/concepto_venta_general")
 */
class ConceptoVentaGeneralController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Conceptos de venta general' => $this->generateUrl('concepto_venta_general')
        );
    }

    /**
     * Lists all Facturacion\ConceptoVentaGeneral entities.
     *
     * @Route("/", name="concepto_venta_general")
     * @Method("GET")
     * @Template()
	 * @Security("has_role('ROLE_VISUALIZAR_CONCEPTO_VENTA_GENERAL')")
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Conceptos de venta general'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Facturacion\ConceptoVentaGeneral',
            'page_info' => 'Lista de facturacion\conceptoventageneral'
        );
    }

    /**
     * Tabla para Facturacion\ConceptoVentaGeneral .
     *
     * @Route("/index_table/", name="concepto_venta_general_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Facturacion\ConceptoVentaGeneral')
			->findByActivo(1);

        $bread = $this->base_breadcrumbs;
        $bread['Conceptos de venta general'] = null;

        return $this->render('ADIFContableBundle:Facturacion/ConceptoVentaGeneral:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new Facturacion\ConceptoVentaGeneral entity.
     *
     * @Route("/insertar", name="concepto_venta_general_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ConceptoVentaGeneral:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ConceptoVentaGeneral();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('concepto_venta_general'));
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
            'page_title' => 'Crear concepto de venta general',
        );
    }

    /**
     * Creates a form to create a Facturacion\ConceptoVentaGeneral entity.
     *
     * @param ConceptoVentaGeneral $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ConceptoVentaGeneral $entity) {
        $form = $this->createForm(new ConceptoVentaGeneralType(), $entity, array(
            'action' => $this->generateUrl('concepto_venta_general_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Facturacion\ConceptoVentaGeneral entity.
     *
     * @Route("/crear", name="concepto_venta_general_new")
     * @Method("GET")
     * @Template()
	 * @Security("has_role('ROLE_CREAR_MODIFICAR_CONCEPTO_VENTA_GENERAL')")
     */
    public function newAction() {
        $entity = new ConceptoVentaGeneral();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear concepto de venta general'
        );
    }

    /**
     * Finds and displays a Facturacion\ConceptoVentaGeneral entity.
     *
     * @Route("/{id}", name="concepto_venta_general_show")
     * @Method("GET")
     * @Template()
	 * @Security("has_role('ROLE_VISUALIZAR_CONCEPTO_VENTA_GENERAL')")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ConceptoVentaGeneral')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\ConceptoVentaGeneral.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['cConcepto de venta general'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver concepto de venta general'
        );
    }

    /**
     * Displays a form to edit an existing Facturacion\ConceptoVentaGeneral entity.
     *
     * @Route("/editar/{id}", name="concepto_venta_general_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\ConceptoVentaGeneral:new.html.twig")
	 * @Security("has_role('ROLE_CREAR_MODIFICAR_CONCEPTO_VENTA_GENERAL')")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ConceptoVentaGeneral')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\ConceptoVentaGeneral.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar concepto de venta general'
        );
    }

    /**
     * Creates a form to edit a Facturacion\ConceptoVentaGeneral entity.
     *
     * @param ConceptoVentaGeneral $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ConceptoVentaGeneral $entity) {
        $form = $this->createForm(new ConceptoVentaGeneralType(), $entity, array(
            'action' => $this->generateUrl('concepto_venta_general_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Facturacion\ConceptoVentaGeneral entity.
     *
     * @Route("/actualizar/{id}", name="concepto_venta_general_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Facturacion\ConceptoVentaGeneral:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ConceptoVentaGeneral')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\ConceptoVentaGeneral.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('concepto_venta_general'));
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
            'page_title' => 'Editar concepto de venta general'
        );
    }

    /**
     * Deletes a Facturacion\ConceptoVentaGeneral entity.
     *
     * @Route("/borrar/{id}", name="concepto_venta_general_delete")
     * @Method("DELETE")
	 * @Security("has_role('ROLE_CREAR_MODIFICAR_CONCEPTO_VENTA_GENERAL')")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ConceptoVentaGeneral')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\ConceptoVentaGeneral.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('concepto_venta_general'));
    }

}
