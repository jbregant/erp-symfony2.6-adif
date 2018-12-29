<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\ConfiguracionCuentaContableSueldos;
use ADIF\RecursosHumanosBundle\Form\ConfiguracionCuentaContableSueldosType;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * ConfiguracionCuentaContableSueldos controller.
 *
 * @Route("/configuracion_cuenta_contable_sueldos")
 */
class ConfiguracionCuentaContableSueldosController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Configuracion Cuenta Contable Sueldos' => $this->generateUrl('configuracion_cuenta_contable_sueldos')
        );
    }

    /**
     * Lists all ConfiguracionCuentaContableSueldos entities.
     *
     * @Route("/", name="configuracion_cuenta_contable_sueldos")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Configuracion Cuenta Contable Sueldos'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Configuracion Cuenta Contable Sueldos',
            'page_info' => 'Configuracion Cuenta Contable Sueldos'
        );
    }

    /**
     * Tabla para ConfiguracionCuentaContableSueldos .
     *
     * @Route("/index_table/", name="configuracion_cuenta_contable_sueldos_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['ConfiguracionCuentaContableSueldos'] = null;

        return $this->render('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new ConfiguracionCuentaContableSueldos entity.
     *
     * @Route("/insertar", name="configuracion_cuenta_contable_sueldos_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ConfiguracionCuentaContableSueldos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('configuracion_cuenta_contable_sueldos'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Configuracion Cuenta Contable Sueldos',
        );
    }

    /**
     * Creates a form to create a ConfiguracionCuentaContableSueldos entity.
     *
     * @param ConfiguracionCuentaContableSueldos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ConfiguracionCuentaContableSueldos $entity) {
        $form = $this->createForm(new ConfiguracionCuentaContableSueldosType(), $entity, array(
            'action' => $this->generateUrl('configuracion_cuenta_contable_sueldos_create'),
            'method' => 'POST',
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ConfiguracionCuentaContableSueldos entity.
     *
     * @Route("/crear", name="configuracion_cuenta_contable_sueldos_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new ConfiguracionCuentaContableSueldos();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Configuracion Cuenta Contable Sueldos'
        );
    }

    /**
     * Finds and displays a ConfiguracionCuentaContableSueldos entity.
     *
     * @Route("/{id}", name="configuracion_cuenta_contable_sueldos_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConfiguracionCuentaContableSueldos.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['ConfiguracionCuentaContableSueldos'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Configuracion Cuenta Contable Sueldos'
        );
    }

    /**
     * Displays a form to edit an existing ConfiguracionCuentaContableSueldos entity.
     *
     * @Route("/editar/{id}", name="configuracion_cuenta_contable_sueldos_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConfiguracionCuentaContableSueldos.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Configuracion Cuenta Contable Sueldos'
        );
    }

    /**
     * Creates a form to edit a ConfiguracionCuentaContableSueldos entity.
     *
     * @param ConfiguracionCuentaContableSueldos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ConfiguracionCuentaContableSueldos $entity) {
        $form = $this->createForm(new ConfiguracionCuentaContableSueldosType(), $entity, array(
            'action' => $this->generateUrl('configuracion_cuenta_contable_sueldos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ConfiguracionCuentaContableSueldos entity.
     *
     * @Route("/actualizar/{id}", name="configuracion_cuenta_contable_sueldos_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConfiguracionCuentaContableSueldos.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('configuracion_cuenta_contable_sueldos'));
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
            'page_title' => 'Configuracion Cuenta Contable Sueldos'
        );
    }

    /**
     * Deletes a ConfiguracionCuentaContableSueldos entity.
     *
     * @Route("/borrar/{id}", name="configuracion_cuenta_contable_sueldos_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConfiguracionCuentaContableSueldos.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('configuracion_cuenta_contable_sueldos'));
    }

}
