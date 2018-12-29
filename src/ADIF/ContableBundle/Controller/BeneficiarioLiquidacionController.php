<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\BeneficiarioLiquidacion;
use ADIF\ContableBundle\Form\BeneficiarioLiquidacionType;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * BeneficiarioLiquidacion controller.
 *
 * @Route("/beneficiarios_liquidacion")
 */
class BeneficiarioLiquidacionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'BeneficiarioLiquidacion' => $this->generateUrl('beneficiarios_liquidacion')
        );
    }

    /**
     * Lists all BeneficiarioLiquidacion entities.
     *
     * @Route("/", name="beneficiarios_liquidacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['BeneficiarioLiquidacion'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'BeneficiarioLiquidacion',
            'page_info' => 'Lista de beneficiarioliquidacion'
        );
    }

    /**
     * Tabla para BeneficiarioLiquidacion .
     *
     * @Route("/index_table/", name="beneficiarios_liquidacion_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:BeneficiarioLiquidacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['BeneficiarioLiquidacion'] = null;

        return $this->render('ADIFContableBundle:BeneficiarioLiquidacion:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new BeneficiarioLiquidacion entity.
     *
     * @Route("/insertar", name="beneficiarios_liquidacion_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:BeneficiarioLiquidacion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new BeneficiarioLiquidacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('beneficiarios_liquidacion'));
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
            'page_title' => 'Crear BeneficiarioLiquidacion',
        );
    }

    /**
     * Creates a form to create a BeneficiarioLiquidacion entity.
     *
     * @param BeneficiarioLiquidacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BeneficiarioLiquidacion $entity) {
        $form = $this->createForm(new BeneficiarioLiquidacionType(), $entity, array(
            'action' => $this->generateUrl('beneficiarios_liquidacion_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())        
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new BeneficiarioLiquidacion entity.
     *
     * @Route("/crear", name="beneficiarios_liquidacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new BeneficiarioLiquidacion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear BeneficiarioLiquidacion'
        );
    }

    /**
     * Finds and displays a BeneficiarioLiquidacion entity.
     *
     * @Route("/{id}", name="beneficiarios_liquidacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:BeneficiarioLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad BeneficiarioLiquidacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['BeneficiarioLiquidacion'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver BeneficiarioLiquidacion'
        );
    }

    /**
     * Displays a form to edit an existing BeneficiarioLiquidacion entity.
     *
     * @Route("/editar/{id}", name="beneficiarios_liquidacion_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:BeneficiarioLiquidacion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:BeneficiarioLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad BeneficiarioLiquidacion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar BeneficiarioLiquidacion'
        );
    }

    /**
     * Creates a form to edit a BeneficiarioLiquidacion entity.
     *
     * @param BeneficiarioLiquidacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BeneficiarioLiquidacion $entity) {
        $form = $this->createForm(new BeneficiarioLiquidacionType(), $entity, array(
            'action' => $this->generateUrl('beneficiarios_liquidacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing BeneficiarioLiquidacion entity.
     *
     * @Route("/actualizar/{id}", name="beneficiarios_liquidacion_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:BeneficiarioLiquidacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:BeneficiarioLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad BeneficiarioLiquidacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if ($editForm->isValid()) {            
            $em->flush();

            return $this->redirect($this->generateUrl('beneficiarios_liquidacion'));
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
            'page_title' => 'Editar BeneficiarioLiquidacion'
        );
    }

    /**
     * Deletes a BeneficiarioLiquidacion entity.
     *
     * @Route("/borrar/{id}", name="beneficiarios_liquidacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:BeneficiarioLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad BeneficiarioLiquidacion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('beneficiarios_liquidacion'));
    }

}
