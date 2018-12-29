<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\EntidadAutorizante;
use ADIF\ComprasBundle\Form\EntidadAutorizanteType;

/**
 * EntidadAutorizante controller.
 *
 * @Route("/entidadautorizante")
 */
class EntidadAutorizanteController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Entidades autorizantes' => $this->generateUrl('entidadautorizante')
        );
    }

    /**
     * Lists all EntidadAutorizante entities.
     *
     * @Route("/", name="entidadautorizante")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:EntidadAutorizante')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Entidades autorizantes'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Entidad autorizante',
            'page_info' => 'Lista de entidades autorizantes'
        );
    }

    /**
     * Creates a new EntidadAutorizante entity.
     *
     * @Route("/insertar", name="entidadautorizante_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:EntidadAutorizante:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new EntidadAutorizante();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('entidadautorizante'));
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
            'page_title' => 'Crear entidad autorizante',
        );
    }

    /**
     * Creates a form to create a EntidadAutorizante entity.
     *
     * @param EntidadAutorizante $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EntidadAutorizante $entity) {
        $form = $this->createForm(new EntidadAutorizanteType(), $entity, array(
            'action' => $this->generateUrl('entidadautorizante_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EntidadAutorizante entity.
     *
     * @Route("/crear", name="entidadautorizante_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new EntidadAutorizante();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear entidad autorizante'
        );
    }

    /**
     * Finds and displays a EntidadAutorizante entity.
     *
     * @Route("/{id}", name="entidadautorizante_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:EntidadAutorizante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EntidadAutorizante.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionEntidadAutorizante()] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver entidad autorizante'
        );
    }

    /**
     * Displays a form to edit an existing EntidadAutorizante entity.
     *
     * @Route("/editar/{id}", name="entidadautorizante_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:EntidadAutorizante:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:EntidadAutorizante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EntidadAutorizante.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionEntidadAutorizante()] = $this->generateUrl('entidadautorizante_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar entidad autorizante'
        );
    }

    /**
     * Creates a form to edit a EntidadAutorizante entity.
     *
     * @param EntidadAutorizante $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EntidadAutorizante $entity) {
        $form = $this->createForm(new EntidadAutorizanteType(), $entity, array(
            'action' => $this->generateUrl('entidadautorizante_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing EntidadAutorizante entity.
     *
     * @Route("/actualizar/{id}", name="entidadautorizante_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:EntidadAutorizante:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:EntidadAutorizante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EntidadAutorizante.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('entidadautorizante'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionEntidadAutorizante()] = $this->generateUrl('entidadautorizante_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar entidad autorizante'
        );
    }

    /**
     * Deletes a EntidadAutorizante entity.
     *
     * @Route("/borrar/{id}", name="entidadautorizante_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

}
