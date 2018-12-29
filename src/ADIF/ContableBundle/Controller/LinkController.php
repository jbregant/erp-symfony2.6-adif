<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Link;
use ADIF\ContableBundle\Form\LinkType;

/**
 * Link controller.
 *
 * @Route("/link")
 */
class LinkController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Links' => $this->generateUrl('link')
        );
    }

    /**
     * Lists all Link entities.
     *
     * @Route("/", name="link")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Links'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Links',
            'page_info' => 'Lista de links'
        );
    }

    /**
     * Tabla para Link .
     *
     * @Route("/index_table/", name="link_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Link')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Links'] = null;

        return $this->render('ADIFContableBundle:Link:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new Link entity.
     *
     * @Route("/insertar", name="link_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Link:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Link();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('link'));
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
            'page_title' => 'Crear link',
        );
    }

    /**
     * Creates a form to create a Link entity.
     *
     * @param Link $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Link $entity) {
        $form = $this->createForm(new LinkType(), $entity, array(
            'action' => $this->generateUrl('link_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Link entity.
     *
     * @Route("/crear", name="link_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Link();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear link'
        );
    }

    /**
     * Finds and displays a Link entity.
     *
     * @Route("/{id}", name="link_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Link')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Link.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Link'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver link'
        );
    }

    /**
     * Displays a form to edit an existing Link entity.
     *
     * @Route("/editar/{id}", name="link_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Link:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Link')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Link.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar link'
        );
    }

    /**
     * Creates a form to edit a Link entity.
     *
     * @param Link $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Link $entity) {
        $form = $this->createForm(new LinkType(), $entity, array(
            'action' => $this->generateUrl('link_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Link entity.
     *
     * @Route("/actualizar/{id}", name="link_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Link:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Link')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Link.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('link'));
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
            'page_title' => 'Editar link'
        );
    }

    /**
     * Deletes a Link entity.
     *
     * @Route("/borrar/{id}", name="link_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Link')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Link.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('link'));
    }

}
