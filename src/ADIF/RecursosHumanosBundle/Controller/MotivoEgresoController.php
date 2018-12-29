<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\MotivoEgreso;
use ADIF\RecursosHumanosBundle\Form\MotivoEgresoType;

/**
 * MotivoEgreso controller.
 *
 * @Route("/motivos_egreso")
  */
class MotivoEgresoController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'MotivoEgreso' => $this->generateUrl('motivos_egreso')
        );
    }
    /**
     * Lists all MotivoEgreso entities.
     *
     * @Route("/", name="motivos_egreso")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:MotivoEgreso')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['MotivoEgreso'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'MotivoEgreso',
            'page_info' => 'Lista de motivoegreso'
        );
    }
    /**
     * Creates a new MotivoEgreso entity.
     *
     * @Route("/insertar", name="motivos_egreso_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:MotivoEgreso:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new MotivoEgreso();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('motivos_egreso'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear MotivoEgreso',
        );
    }

    /**
    * Creates a form to create a MotivoEgreso entity.
    *
    * @param MotivoEgreso $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(MotivoEgreso $entity)
    {
        $form = $this->createForm(new MotivoEgresoType(), $entity, array(
            'action' => $this->generateUrl('motivos_egreso_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new MotivoEgreso entity.
     *
     * @Route("/crear", name="motivos_egreso_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new MotivoEgreso();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear MotivoEgreso'
        );
}

    /**
     * Finds and displays a MotivoEgreso entity.
     *
     * @Route("/{id}", name="motivos_egreso_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:MotivoEgreso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MotivoEgreso.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['MotivoEgreso'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver MotivoEgreso'
        );
    }

    /**
     * Displays a form to edit an existing MotivoEgreso entity.
     *
     * @Route("/editar/{id}", name="motivos_egreso_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:MotivoEgreso:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:MotivoEgreso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MotivoEgreso.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar MotivoEgreso'
        );
    }

    /**
    * Creates a form to edit a MotivoEgreso entity.
    *
    * @param MotivoEgreso $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MotivoEgreso $entity)
    {
        $form = $this->createForm(new MotivoEgresoType(), $entity, array(
            'action' => $this->generateUrl('motivos_egreso_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing MotivoEgreso entity.
     *
     * @Route("/actualizar/{id}", name="motivos_egreso_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:MotivoEgreso:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:MotivoEgreso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MotivoEgreso.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('motivos_egreso'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar MotivoEgreso'
        );
    }
    /**
     * Deletes a MotivoEgreso entity.
     *
     * @Route("/borrar/{id}", name="motivos_egreso_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:MotivoEgreso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad MotivoEgreso.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('motivos_egreso'));
    }
}
