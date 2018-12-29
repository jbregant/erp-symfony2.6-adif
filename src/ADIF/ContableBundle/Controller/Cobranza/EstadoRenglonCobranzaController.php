<?php

namespace ADIF\ContableBundle\Controller\Cobranza;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Cobranza\EstadoRenglonCobranza;
use ADIF\ContableBundle\Form\Cobranza\EstadoRenglonCobranzaType;

/**
 * Cobranza\EstadoRenglonCobranza controller.
 *
 * @Route("/estadorengloncobranza")
 */
class EstadoRenglonCobranzaController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Estados de rengl&oacute;n cobranza' => $this->generateUrl('estadorengloncobranza')
        );
    }

    /**
     * Lists all Cobranza\EstadoRenglonCobranza entities.
     *
     * @Route("/", name="estadorengloncobranza")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Estados de rengl&oacute;n cobranza'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Estados de rengl&oacute;n cobranza',
            'page_info' => 'Lista de estados de rengl&oacute;n cobranza'
        );
    }

    /**
     * Tabla para Cobranza\EstadoRenglonCobranza .
     *
     * @Route("/index_table/", name="estadorengloncobranza_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Estados de rengl&oacute;n cobranza'] = null;

        return $this->render('ADIFContableBundle:Cobranza/EstadoRenglonCobranza:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new Cobranza\EstadoRenglonCobranza entity.
     *
     * @Route("/insertar", name="estadorengloncobranza_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Cobranza\EstadoRenglonCobranza:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new EstadoRenglonCobranza();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estadorengloncobranza'));
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
            'page_title' => 'Crear estado de rengl&oacute;n cobranza',
        );
    }

    /**
     * Creates a form to create a Cobranza\EstadoRenglonCobranza entity.
     *
     * @param EstadoRenglonCobranza $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EstadoRenglonCobranza $entity) {
        $form = $this->createForm(new EstadoRenglonCobranzaType(), $entity, array(
            'action' => $this->generateUrl('estadorengloncobranza_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Cobranza\EstadoRenglonCobranza entity.
     *
     * @Route("/crear", name="estadorengloncobranza_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new EstadoRenglonCobranza();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear estado de rengl&oacute;n cobranza'
        );
    }

    /**
     * Finds and displays a Cobranza\EstadoRenglonCobranza entity.
     *
     * @Route("/{id}", name="estadorengloncobranza_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cobranza\EstadoRenglonCobranza.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Estado de rengl&oacute;n cobranza'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver estado de rengl&oacute;n cobranza'
        );
    }

    /**
     * Displays a form to edit an existing Cobranza\EstadoRenglonCobranza entity.
     *
     * @Route("/editar/{id}", name="estadorengloncobranza_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Cobranza\EstadoRenglonCobranza:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cobranza\EstadoRenglonCobranza.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar estado de rengl&oacute;n cobranza'
        );
    }

    /**
     * Creates a form to edit a Cobranza\EstadoRenglonCobranza entity.
     *
     * @param EstadoRenglonCobranza $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EstadoRenglonCobranza $entity) {
        $form = $this->createForm(new EstadoRenglonCobranzaType(), $entity, array(
            'action' => $this->generateUrl('estadorengloncobranza_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Cobranza\EstadoRenglonCobranza entity.
     *
     * @Route("/actualizar/{id}", name="estadorengloncobranza_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Cobranza\EstadoRenglonCobranza:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cobranza\EstadoRenglonCobranza.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('estadorengloncobranza'));
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
            'page_title' => 'Editar estado de rengl&oacute;n cobranza'
        );
    }

    /**
     * Deletes a Cobranza\EstadoRenglonCobranza entity.
     *
     * @Route("/borrar/{id}", name="estadorengloncobranza_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cobranza\EstadoRenglonCobranza.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('estadorengloncobranza'));
    }

}
