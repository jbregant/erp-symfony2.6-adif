<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\TipoMoneda;
use ADIF\ContableBundle\Form\TipoMonedaType;

/**
 * TipoMoneda controller.
 *
 * @Route("/tipomoneda")
 */
class TipoMonedaController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Tipos de moneda' => $this->generateUrl('tipomoneda')
        );
    }

    /**
     * Lists all TipoMoneda entities.
     *
     * @Route("/", name="tipomoneda")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:TipoMoneda')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de moneda'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Tipos de moneda',
            'page_info' => 'Lista de tipos de moneda'
        );
    }

    /**
     * Creates a new TipoMoneda entity.
     *
     * @Route("/insertar", name="tipomoneda_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:TipoMoneda:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new TipoMoneda();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipomoneda'));
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
            'page_title' => 'Crear tipo de moneda',
        );
    }

    /**
     * Creates a form to create a TipoMoneda entity.
     *
     * @param TipoMoneda $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoMoneda $entity) {
        $form = $this->createForm(new TipoMonedaType(), $entity, array(
            'action' => $this->generateUrl('tipomoneda_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoMoneda entity.
     *
     * @Route("/crear", name="tipomoneda_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new TipoMoneda();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tipo de moneda'
        );
    }

    /**
     * Finds and displays a TipoMoneda entity.
     *
     * @Route("/{id}", name="tipomoneda_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:TipoMoneda')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoMoneda.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Tipo de moneda'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver tipo de moneda'
        );
    }

    /**
     * Displays a form to edit an existing TipoMoneda entity.
     *
     * @Route("/editar/{id}", name="tipomoneda_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:TipoMoneda:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:TipoMoneda')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoMoneda.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de moneda'
        );
    }

    /**
     * Creates a form to edit a TipoMoneda entity.
     *
     * @param TipoMoneda $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TipoMoneda $entity) {
        $form = $this->createForm(new TipoMonedaType(), $entity, array(
            'action' => $this->generateUrl('tipomoneda_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing TipoMoneda entity.
     *
     * @Route("/actualizar/{id}", name="tipomoneda_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:TipoMoneda:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:TipoMoneda')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoMoneda.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipomoneda'));
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
            'page_title' => 'Editar tipo de moneda'
        );
    }

    /**
     * Deletes a TipoMoneda entity.
     *
     * @Route("/borrar/{id}", name="tipomoneda_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:TipoMoneda')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoMoneda.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('tipomoneda'));
    }

    /**
     * @Route("/lista", name="tipomoneda_lista")
     */
    public function listaTipoMonedaAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:TipoMoneda', $this->getEntityManager());

        $query = $repository->createQueryBuilder('tm')
                ->select('tm.id', 'tm.codigoTipoMoneda', 'tm.denominacionTipoMoneda')
                ->orderBy('tm.id', 'ASC')
                ->getQuery()
                ->useResultCache(true, 7200, 'tipo_moneda_lista');

        return new JsonResponse($query->getResult());
    }

    /**
     * @Route("/lista_mcl", name="tipomoneda_lista_mcl")
     */
    public function listaTipoMonedaMCLAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:TipoMoneda', $this->getEntityManager());

        $query = $repository->createQueryBuilder('tm')
                ->select('tm.id', 'tm.codigoTipoMoneda', 'tm.denominacionTipoMoneda')
                ->where('tm.esMCL = 1')
                ->orderBy('tm.id', 'ASC')
                ->getQuery()
                ->useResultCache(true, 7200, 'tipo_moneda_lista_mcl');

        return new JsonResponse($query->getResult());
    }

}
