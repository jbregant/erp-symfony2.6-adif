<?php

namespace ADIF\ContableBundle\Controller\Obras;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Obras\TipoObra;
use ADIF\ContableBundle\Form\Obras\TipoObraType;

/**
 * Obras\TipoObra controller.
 *
 * @Route("/obras_tiposobra")
 */
class TipoObraController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Tipos de obra' => $this->generateUrl('obras_tiposobra')
        );
    }

    /**
     * Lists all Obras\TipoObra entities.
     *
     * @Route("/", name="obras_tiposobra")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Obras\TipoObra')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de obra'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Tipos de obra',
            'page_info' => 'Lista de tipos de obra'
        );
    }

    /**
     * Creates a new Obras\TipoObra entity.
     *
     * @Route("/insertar", name="obras_tiposobra_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Obras\TipoObra:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new TipoObra();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('obras_tiposobra'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tipo de obra',
        );
    }

    /**
     * Creates a form to create a Obras\TipoObra entity.
     *
     * @param TipoObra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoObra $entity) {
        $form = $this->createForm(new TipoObraType(), $entity, array(
            'action' => $this->generateUrl('obras_tiposobra_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Obras\TipoObra entity.
     *
     * @Route("/crear", name="obras_tiposobra_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new TipoObra();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tipo de obra'
        );
    }

    /**
     * Finds and displays a Obras\TipoObra entity.
     *
     * @Route("/{id}", name="obras_tiposobra_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\TipoObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoObra.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Tipo Obra'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver tipo de obra'
        );
    }

    /**
     * Displays a form to edit an existing Obras\TipoObra entity.
     *
     * @Route("/editar/{id}", name="obras_tiposobra_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Obras\TipoObra:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\TipoObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoObra.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de obra'
        );
    }

    /**
     * Creates a form to edit a Obras\TipoObra entity.
     *
     * @param TipoObra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TipoObra $entity) {
        $form = $this->createForm(new TipoObraType(), $entity, array(
            'action' => $this->generateUrl('obras_tiposobra_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Obras\TipoObra entity.
     *
     * @Route("/actualizar/{id}", name="obras_tiposobra_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Obras\TipoObra:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\TipoObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoObra.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('obras_tiposobra'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de obra'
        );
    }

    /**
     * Deletes a Obras\TipoObra entity.
     *
     * @Route("/borrar/{id}", name="obras_tiposobra_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Obras\TipoObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoObra.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('obras_tiposobra'));
    }

}
