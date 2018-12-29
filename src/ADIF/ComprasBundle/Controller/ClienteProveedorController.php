<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\ClienteProveedor;
use ADIF\ComprasBundle\Form\ClienteProveedorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Prioridad controller.
 *
 * @Route("/clienteproveedor")
 */
class ClienteProveedorController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Displays a form to create a new ClienteProveedor entity.
     *
     * @Route("/crear", name="cliente_proveedor_new")
     * @Method("GET")
     * @Template()
	 * @Security("has_role('ROLE_CREAR_MODIFICAR_CLIENTES')")
     */
    public function newAction(Request $request) {

        $entity = new ClienteProveedor();

        $this->get('session')
                ->set('tipo_cliente_proveedor', $request->query->get('tipo_cliente_proveedor'));

        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;

        if ($request->query->get('tipo_cliente_proveedor') == 'tipo_cliente') {
            $bread['Clientes'] = $this->generateUrl('cliente');
        } else {
            $bread['Proveedores'] = $this->generateUrl('proveedor');
        }

        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => ($request->query->get('tipo_cliente_proveedor') == 'tipo_cliente') ? 'Crear Cliente' : 'Crear Proveedor',
            'body_page_title' => ($request->query->get('tipo_cliente_proveedor') == 'tipo_cliente') ? 'Cliente' : 'Proveedor',
            'back_path' => ($request->query->get('tipo_cliente_proveedor') == 'tipo_cliente') ? 'cliente' : 'proveedor'
        );
    }

    /**
     * Creates a form to create a Prioridad entity.
     *
     * @param Prioridad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ClienteProveedor $entity) {
        $form = $this->createForm(new ClienteProveedorType($this->getDoctrine()->getManager($this->getEntityManager()),
                                                           $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
                                                           $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())
                ), $entity, array(
            'action' => $this->generateUrl('cliente_proveedor_create'),
            'method' => 'POST',
            //'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Continuar'));

        return $form;
    }

    /**
     * Creates a new entity.
     *
     * @Route("/insertar", name="cliente_proveedor_create")
     * @Method("POST")
     */
    public function createAction(Request $request) {
        $entity = new ClienteProveedor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $tipo = $this->get('session')->get('tipo_cliente_proveedor');
        $this->get('session')->remove('tipo_cliente_proveedor');

        $identificacion = $entity->getEsExtranjero() ? ($entity->getDNI() !== null ? $entity->getDNI() : $entity->getCodigoIdentificacion()) : ($entity->getDNI() !== null ? $entity->getDNI() : $entity->getCUIT());

        if ($tipo == 'tipo_cliente') {
            return $this->redirect($this->generateUrl('cliente_new', array('identificacion' => $identificacion, 'esExtranjero' => $entity->getEsExtranjero(), 'DNI' => $entity->getDNI())));
        } else {
            return $this->redirect($this->generateUrl('proveedor_new', array('identificacion' => $identificacion, 'esExtranjero' => $entity->getEsExtranjero())));
        }
    }

}
