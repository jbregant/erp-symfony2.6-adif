<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\CuentaBancaria;
use ADIF\RecursosHumanosBundle\Form\CuentaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Cuenta controller.
 *
 * @Route("/cuentas")
 * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
 */
class CuentaBancariaController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Cuenta' => $this->generateUrl('cuentas')
        );
    }

    /**
     * Lists all Cuenta entities.
     *
     * @Route("/", name="cuentas")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancaria')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Cuenta'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Cuenta',
            'page_info' => 'Lista de cuenta'
        );
    }

    /**
     * Creates a new Cuenta entity.
     *
     * @Route("/insertar", name="cuentas_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Cuenta:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new CuentaBancaria();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cuentas'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Cuenta',
        );
    }

    /**
     * Creates a form to create a Cuenta entity.
     *
     * @param CuentaBancaria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CuentaBancaria $entity) {
        $form = $this->createForm(new CuentaType($this->getDoctrine()->getManager($this->getEntityManager())), $entity, array(
            'action' => $this->generateUrl('cuentas_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Cuenta entity.
     *
     * @Route("/crear", name="cuentas_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new CuentaBancaria();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Cuenta'
        );
    }

    /**
     * Finds and displays a Cuenta entity.
     *
     * @Route("/{id}", name="cuentas_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cuenta.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Cuenta'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Cuenta'
        );
    }

    /**
     * Displays a form to edit an existing Cuenta entity.
     *
     * @Route("/editar/{id}", name="cuentas_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Cuenta:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cuenta.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Cuenta'
        );
    }

    /**
     * Creates a form to edit a Cuenta entity.
     *
     * @param CuentaBancaria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CuentaBancaria $entity) {
        $form = $this->createForm(new CuentaType($this->getDoctrine()->getManager($this->getEntityManager())), $entity, array(
            'action' => $this->generateUrl('cuentas_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Cuenta entity.
     *
     * @Route("/actualizar/{id}", name="cuentas_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Cuenta:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cuenta.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('cuentas'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Cuenta'
        );
    }

    /**
     * Deletes a Cuenta entity.
     *
     * @Route("/borrar/{id}", name="cuentas_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cuenta.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('cuentas'));
    }

    /**
     * @Route("/filtrar_cbus/", name="cuentas_filtrar_cbus")
     */
    public function filtrarCBUs(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $banco = $request->get('banco');

        $tipoCuenta = $request->get('tipo_cuenta');

        $cuentas = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancaria')->findBy(
                array(
                    'idTipoCuenta' => $tipoCuenta,
                    'idBanco' => $banco,
        ));

        $jsonResult = [];

        if ($cuentas !== null) {
            foreach ($cuentas as $cuenta) {

                $jsonResult[] = $cuenta->getCbu();
            }
        }

        return new JsonResponse($jsonResult);
    }

}
