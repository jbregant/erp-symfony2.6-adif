<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\TipoLiquidacion;
use ADIF\RecursosHumanosBundle\Form\TipoLiquidacionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * TipoLiquidacion controller.
 *
 * @Route("/tipos_liquidacion")
 * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
 */
class TipoLiquidacionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'TipoLiquidacion' => $this->generateUrl('tipos_liquidacion')
        );
    }

    /**
     * Lists all TipoLiquidacion entities.
     *
     * @Route("/", name="tipos_liquidacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:TipoLiquidacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['TipoLiquidacion'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'TipoLiquidacion',
            'page_info' => 'Lista de tipoliquidacion'
        );
    }

    /**
     * Creates a new TipoLiquidacion entity.
     *
     * @Route("/insertar", name="tipos_liquidacion_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:TipoLiquidacion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new TipoLiquidacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipos_liquidacion'));
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
            'page_title' => 'Crear TipoLiquidacion',
        );
    }

    /**
     * Creates a form to create a TipoLiquidacion entity.
     *
     * @param TipoLiquidacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoLiquidacion $entity) {
        $form = $this->createForm(new TipoLiquidacionType(), $entity, array(
            'action' => $this->generateUrl('tipos_liquidacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoLiquidacion entity.
     *
     * @Route("/crear", name="tipos_liquidacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new TipoLiquidacion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear TipoLiquidacion'
        );
    }

    /**
     * Finds and displays a TipoLiquidacion entity.
     *
     * @Route("/{id}", name="tipos_liquidacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TipoLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoLiquidacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['TipoLiquidacion'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver TipoLiquidacion'
        );
    }

    /**
     * Displays a form to edit an existing TipoLiquidacion entity.
     *
     * @Route("/editar/{id}", name="tipos_liquidacion_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:TipoLiquidacion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TipoLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoLiquidacion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar TipoLiquidacion'
        );
    }

    /**
     * Creates a form to edit a TipoLiquidacion entity.
     *
     * @param TipoLiquidacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TipoLiquidacion $entity) {
        $form = $this->createForm(new TipoLiquidacionType(), $entity, array(
            'action' => $this->generateUrl('tipos_liquidacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing TipoLiquidacion entity.
     *
     * @Route("/actualizar/{id}", name="tipos_liquidacion_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:TipoLiquidacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TipoLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoLiquidacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipos_liquidacion'));
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
            'page_title' => 'Editar TipoLiquidacion'
        );
    }

    /**
     * Deletes a TipoLiquidacion entity.
     *
     * @Route("/borrar/{id}", name="tipos_liquidacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TipoLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoLiquidacion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('tipos_liquidacion'));
    }

    /**
     * @Route("/lista_tipos_liquidacion", name="lista_tipos_liquidacion")
     * @Security("has_role('ROLE_USER')")
     */
    public function listaTiposLiquidacionAction() {
        $repository = $this->getDoctrine()->getRepository('ADIFRecursosHumanosBundle:TipoLiquidacion', $this->getEntityManager());

        $query = $repository->createQueryBuilder('t')
                ->select('t.id', 't.nombre')
                ->orderBy('t.id', 'ASC')
                ->getQuery();

        return new JsonResponse($query->getResult());
    }

}
