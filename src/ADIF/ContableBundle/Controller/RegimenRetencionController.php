<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\RegimenRetencion;
use ADIF\ContableBundle\Form\RegimenRetencionType;

/**
 * RegimenRetencion controller.
 *
 * @Route("/regimenretencion")
 */
class RegimenRetencionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Reg&iacute;menenes de retenci&oacute;n' => $this->generateUrl('regimenretencion')
        );
    }

    /**
     * Lists all RegimenRetencion entities.
     *
     * @Route("/", name="regimenretencion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:RegimenRetencion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Reg&iacute;menenes de retenci&oacute;n'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'R&eacute;gimen de retenci&oacute;n',
            'page_info' => 'Lista de reg&iacute;menes de retenci&oacute;n'
        );
    }

    /**
     * Creates a new RegimenRetencion entity.
     *
     * @Route("/insertar", name="regimenretencion_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:RegimenRetencion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new RegimenRetencion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('regimenretencion'));
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
            'page_title' => 'Crear r&eacute;gimen de retenci&oacute;n',
        );
    }

    /**
     * Creates a form to create a RegimenRetencion entity.
     *
     * @param RegimenRetencion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RegimenRetencion $entity) {
        $form = $this->createForm(new RegimenRetencionType(), $entity, array(
            'action' => $this->generateUrl('regimenretencion_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new RegimenRetencion entity.
     *
     * @Route("/crear", name="regimenretencion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new RegimenRetencion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear r&eacute;gimen de retenci&oacute;n'
        );
    }

    /**
     * Finds and displays a RegimenRetencion entity.
     *
     * @Route("/{id}", name="regimenretencion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:RegimenRetencion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RegimenRetencion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['R&eacute;gimen de retenci&oacute;n'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver r&eacute;gimen de retenci&oacute;n'
        );
    }

    /**
     * Displays a form to edit an existing RegimenRetencion entity.
     *
     * @Route("/editar/{id}", name="regimenretencion_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:RegimenRetencion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:RegimenRetencion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RegimenRetencion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar r&eacute;gimen de retenci&oacute;n'
        );
    }

    /**
     * Creates a form to edit a RegimenRetencion entity.
     *
     * @param RegimenRetencion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(RegimenRetencion $entity) {
        $form = $this->createForm(new RegimenRetencionType(), $entity, array(
            'action' => $this->generateUrl('regimenretencion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing RegimenRetencion entity.
     *
     * @Route("/actualizar/{id}", name="regimenretencion_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:RegimenRetencion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:RegimenRetencion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RegimenRetencion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('regimenretencion'));
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
            'page_title' => 'Editar r&eacute;gimen de retenci&oacute;n'
        );
    }

    /**
     * Deletes a RegimenRetencion entity.
     *
     * @Route("/borrar/{id}", name="regimenretencion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:RegimenRetencion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RegimenRetencion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('regimenretencion'));
    }

}
