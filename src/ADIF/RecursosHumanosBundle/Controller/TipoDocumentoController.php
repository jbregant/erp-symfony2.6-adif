<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\TipoDocumento;
use ADIF\RecursosHumanosBundle\Form\TipoDocumentoType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * TipoDocumento controller.
 *
 * @Route("/tipos_documento")
 * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
 */
class TipoDocumentoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'TipoDocumento' => $this->generateUrl('tipos_documento')
        );
    }

    /**
     * Lists all TipoDocumento entities.
     *
     * @Route("/", name="tipos_documento")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:TipoDocumento')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['TipoDocumento'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'TipoDocumento',
            'page_info' => 'Lista de tipodocumento'
        );
    }

    /**
     * Creates a new TipoDocumento entity.
     *
     * @Route("/insertar", name="tipos_documento_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:TipoDocumento:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new TipoDocumento();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipos_documento'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear TipoDocumento',
        );
    }

    /**
     * Creates a form to create a TipoDocumento entity.
     *
     * @param TipoDocumento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoDocumento $entity) {
        $form = $this->createForm(new TipoDocumentoType(), $entity, array(
            'action' => $this->generateUrl('tipos_documento_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoDocumento entity.
     *
     * @Route("/crear", name="tipos_documento_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new TipoDocumento();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear TipoDocumento'
        );
    }

    /**
     * Finds and displays a TipoDocumento entity.
     *
     * @Route("/{id}", name="tipos_documento_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TipoDocumento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoDocumento.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['TipoDocumento'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver TipoDocumento'
        );
    }

    /**
     * Displays a form to edit an existing TipoDocumento entity.
     *
     * @Route("/editar/{id}", name="tipos_documento_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:TipoDocumento:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TipoDocumento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoDocumento.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar TipoDocumento'
        );
    }

    /**
     * Creates a form to edit a TipoDocumento entity.
     *
     * @param TipoDocumento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TipoDocumento $entity) {
        $form = $this->createForm(new TipoDocumentoType(), $entity, array(
            'action' => $this->generateUrl('tipos_documento_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing TipoDocumento entity.
     *
     * @Route("/actualizar/{id}", name="tipos_documento_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:TipoDocumento:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TipoDocumento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoDocumento.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipos_documento'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar TipoDocumento'
        );
    }

    /**
     * Deletes a TipoDocumento entity.
     *
     * @Route("/borrar/{id}", name="tipos_documento_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TipoDocumento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoDocumento.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('tipos_documento'));
    }

    /**
     * @Route("/lista_tipos_documento", name="lista_tipos_documento")
     * @Security("has_role('ROLE_USER')")
     */
    public function listaTiposDocumentoAction() {
        $repository = $this->getDoctrine()->getRepository('ADIFRecursosHumanosBundle:TipoDocumento', $this->getEntityManager());

        $query = $repository->createQueryBuilder('t')
                ->select('t.id', 't.nombre')
                ->orderBy('t.nombre', 'ASC')
                ->getQuery();

        return new JsonResponse($query->getResult());
    }

}
