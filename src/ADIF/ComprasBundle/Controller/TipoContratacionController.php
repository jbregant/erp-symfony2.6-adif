<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\TipoContratacion;
use ADIF\ComprasBundle\Form\TipoContratacionType;

/**
 * TipoContratacion controller.
 *
 * @Route("/tipocontratacion")
 */
class TipoContratacionController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Tipos de contrataci&oacute;n' => $this->generateUrl('tipocontratacion')
        );
    }

    /**
     * Lists all TipoContratacion entities.
     *
     * @Route("/", name="tipocontratacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:TipoContratacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de contrataci&oacute;n'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Tipo Contratación',
            'page_info' => 'Lista de tipos de contrataciones'
        );
    }

    /**
     * Creates a new TipoContratacion entity.
     *
     * @Route("/insertar", name="tipocontratacion_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:TipoContratacion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new TipoContratacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $em->persist($entity);
            $em->flush();

            $cacheDriver = $em->getConfiguration()->getResultCacheImpl();
            $cacheDriver->delete('tipos_contrataciones');

            return $this->redirect($this->generateUrl('tipocontratacion'));
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
            'page_title' => 'Crear tipo de contrataci&oacute;n',
        );
    }

    /**
     * Creates a form to create a TipoContratacion entity.
     *
     * @param TipoContratacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoContratacion $entity) {
        $form = $this->createForm(new TipoContratacionType(), $entity, array(
            'action' => $this->generateUrl('tipocontratacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoContratacion entity.
     *
     * @Route("/crear", name="tipocontratacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new TipoContratacion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tipo de contrataci&oacute;n'
        );
    }

    /**
     * Finds and displays a TipoContratacion entity.
     *
     * @Route("/{id}", name="tipocontratacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:TipoContratacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo Contratación.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionTipoContratacion()] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver tipo de contrataci&oacute;n'
        );
    }

    /**
     * Displays a form to edit an existing TipoContratacion entity.
     *
     * @Route("/editar/{id}", name="tipocontratacion_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:TipoContratacion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:TipoContratacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo Contratación.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionTipoContratacion()] = $this->generateUrl('tipocontratacion_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de contrataci&oacute;n'
        );
    }

    /**
     * Creates a form to edit a TipoContratacion entity.
     *
     * @param TipoContratacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TipoContratacion $entity) {
        $form = $this->createForm(new TipoContratacionType(), $entity, array(
            'action' => $this->generateUrl('tipocontratacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing TipoContratacion entity.
     *
     * @Route("/actualizar/{id}", name="tipocontratacion_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:TipoContratacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:TipoContratacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo Contratación.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $cacheDriver = $em->getConfiguration()->getResultCacheImpl();
            $cacheDriver->delete('tipos_contrataciones');

            return $this->redirect($this->generateUrl('tipocontratacion'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionTipoContratacion()] = $this->generateUrl('tipocontratacion_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de contrataci&oacute;n'
        );
    }

    /**
     * Deletes a TipoContratacion entity.
     *
     * @Route("/borrar/{id}", name="tipocontratacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:TipoContratacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Tipo Contratación.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('tipocontratacion'));
    }

    /**
     * @Route("/tipos-contrataciones", name="tipos-contrataciones")
     */
    public function getTiposContrataciones() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $tiposContrataciones = $em->getRepository('ADIFComprasBundle:TipoContratacion')->getTiposContrataciones();

        $tiposContratacionesArray = [];

        foreach ($tiposContrataciones as $tipoContratacion) {

            $tiposContratacionesArray[] = array(
                'id' => $tipoContratacion->getId(),
                'denominacion' => $tipoContratacion->getDenominacionTipoContratacion(),
                'montoDesde' => $tipoContratacion->getMontoDesde(),
                'montoHasta' => $tipoContratacion->getMontoHasta()
            );
        }

        return new JsonResponse($tiposContratacionesArray);
    }

}
