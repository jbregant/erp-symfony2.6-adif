<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\RenglonRequerimiento;
use ADIF\ComprasBundle\Form\RenglonRequerimientoType;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * RenglonRequerimiento controller.
 *
 * @Route("/renglonrequerimiento")
 */
class RenglonRequerimientoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'RenglonRequerimiento' => $this->generateUrl('renglonrequerimiento')
        );
    }

    /**
     * Lists all RenglonRequerimiento entities.
     *
     * @Route("/", name="renglonrequerimiento")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:RenglonRequerimiento')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['RenglonRequerimiento'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'RenglonRequerimiento',
            'page_info' => 'Lista de renglonrequerimiento'
        );
    }

    /**
     * Creates a new RenglonRequerimiento entity.
     *
     * @Route("/insertar", name="renglonrequerimiento_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:RenglonRequerimiento:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new RenglonRequerimiento();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('renglonrequerimiento'));
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
            'page_title' => 'Crear rengl&oacute;n requerimiento',
        );
    }

    /**
     * Creates a form to create a RenglonRequerimiento entity.
     *
     * @param RenglonRequerimiento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RenglonRequerimiento $entity) {
        $form = $this->createForm(new RenglonRequerimientoType($this->getDoctrine()->getManager($this->getEntityManager())), $entity, array(
            'action' => $this->generateUrl('renglonrequerimiento_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new RenglonRequerimiento entity.
     *
     * @Route("/crear", name="renglonrequerimiento_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new RenglonRequerimiento();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear rengl&oacute;n requerimiento'
        );
    }

    /**
     * Finds and displays a RenglonRequerimiento entity.
     *
     * @Route("/{id}", name="renglonrequerimiento_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:RenglonRequerimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonRequerimiento.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['RenglonRequerimiento'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver rengl&oacute;n requerimiento'
        );
    }

    /**
     * Displays a form to edit an existing RenglonRequerimiento entity.
     *
     * @Route("/editar/{id}", name="renglonrequerimiento_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:RenglonRequerimiento:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:RenglonRequerimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonRequerimiento.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar rengl&oacute;n requerimiento'
        );
    }

    /**
     * Creates a form to edit a RenglonRequerimiento entity.
     *
     * @param RenglonRequerimiento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(RenglonRequerimiento $entity) {
        $form = $this->createForm(new RenglonRequerimientoType($this->getDoctrine()->getManager($this->getEntityManager())), $entity, array(
            'action' => $this->generateUrl('renglonrequerimiento_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing RenglonRequerimiento entity.
     *
     * @Route("/actualizar/{id}", name="renglonrequerimiento_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:RenglonRequerimiento:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:RenglonRequerimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonRequerimiento.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('renglonrequerimiento'));
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
            'page_title' => 'Editar rengl&oacute;n requerimiento'
        );
    }

    /**
     * Deletes a RenglonRequerimiento entity.
     *
     * @Route("/borrar/{id}", name="renglonrequerimiento_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:RenglonRequerimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonRequerimiento.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('renglonrequerimiento'));
    }

    /**
     * Obtiene el justiprecio del RenglonRequerimiento
     *
     * @Route("/justiprecio", name="renglonrequerimiento_justiprecio")
     */
    public function getJustiprecioAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idRequerimiento = $request->request->get('id_renglon_requerimiento');

        /* @var $renglonRequerimiento RenglonRequerimiento */
        $renglonRequerimiento = $em->getRepository('ADIFComprasBundle:RenglonRequerimiento')
                ->find($idRequerimiento);

        if (!$renglonRequerimiento) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonRequerimiento.');
        }

        return new JsonResponse($renglonRequerimiento->getJustiprecioUnitario());
    }

}
