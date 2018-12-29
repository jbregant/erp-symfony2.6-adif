<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\RegimenPercepcion;
use ADIF\ContableBundle\Form\RegimenPercepcionType;

/**
 * RegimenPercepcion controller.
 *
 * @Route("/regimenespercepcion")
 */
class RegimenPercepcionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'RegimenPercepcion' => $this->generateUrl('regimenespercepcion')
        );
    }

    /**
     * Lists all RegimenPercepcion entities.
     *
     * @Route("/", name="regimenespercepcion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['RegimenPercepcion'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'RegimenPercepcion',
            'page_info' => 'Lista de regimenpercepcion'
        );
    }

    /**
     * Tabla para RegimenPercepcion .
     *
     * @Route("/index_table/", name="regimenespercepcion_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:RegimenPercepcion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['RegimenPercepcion'] = null;

        return $this->render('ADIFContableBundle:RegimenPercepcion:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new RegimenPercepcion entity.
     *
     * @Route("/insertar", name="regimenespercepcion_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:RegimenPercepcion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new RegimenPercepcion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('regimenespercepcion'));
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
            'page_title' => 'Crear RegimenPercepcion',
        );
    }

    /**
     * Creates a form to create a RegimenPercepcion entity.
     *
     * @param RegimenPercepcion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RegimenPercepcion $entity) {
        $form = $this->createForm(new RegimenPercepcionType(), $entity, array(
            'action' => $this->generateUrl('regimenespercepcion_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new RegimenPercepcion entity.
     *
     * @Route("/crear", name="regimenespercepcion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new RegimenPercepcion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear RegimenPercepcion'
        );
    }

    /**
     * Finds and displays a RegimenPercepcion entity.
     *
     * @Route("/{id}", name="regimenespercepcion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:RegimenPercepcion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RegimenPercepcion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['RegimenPercepcion'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver RegimenPercepcion'
        );
    }

    /**
     * Displays a form to edit an existing RegimenPercepcion entity.
     *
     * @Route("/editar/{id}", name="regimenespercepcion_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:RegimenPercepcion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:RegimenPercepcion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RegimenPercepcion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar RegimenPercepcion'
        );
    }

    /**
     * Creates a form to edit a RegimenPercepcion entity.
     *
     * @param RegimenPercepcion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(RegimenPercepcion $entity) {
        $form = $this->createForm(new RegimenPercepcionType(), $entity, array(
            'action' => $this->generateUrl('regimenespercepcion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing RegimenPercepcion entity.
     *
     * @Route("/actualizar/{id}", name="regimenespercepcion_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:RegimenPercepcion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:RegimenPercepcion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RegimenPercepcion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('regimenespercepcion'));
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
            'page_title' => 'Editar RegimenPercepcion'
        );
    }

    /**
     * Deletes a RegimenPercepcion entity.
     *
     * @Route("/borrar/{id}", name="regimenespercepcion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:RegimenPercepcion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RegimenPercepcion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('regimenespercepcion'));
    }

}
