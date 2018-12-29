<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Puesto;
use ADIF\RecursosHumanosBundle\Form\PuestoType;

/**
 * Puesto controller.
 *
 * @Route("/puesto")
  */
class PuestoController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Puesto' => $this->generateUrl('puesto')
        );
    }
    /**
     * Lists all Puesto entities.
     *
     * @Route("/", name="puesto")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Puesto'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Puesto',
            'page_info' => 'Lista de puesto'
        );
    }

    /**
     * Tabla para Puesto .
     *
     * @Route("/index_table/", name="puesto_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Puesto')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['Puesto'] = null;

    return $this->render('ADIFRecursosHumanosBundle:Puesto:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new Puesto entity.
     *
     * @Route("/insertar", name="puesto_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Puesto:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Puesto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('puesto'));
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
            'page_title' => 'Crear Puesto',
        );
    }

    /**
    * Creates a form to create a Puesto entity.
    *
    * @param Puesto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Puesto $entity)
    {
        $form = $this->createForm(new PuestoType(), $entity, array(
            'action' => $this->generateUrl('puesto_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Puesto entity.
     *
     * @Route("/crear", name="puesto_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Puesto();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Puesto'
        );
}

    /**
     * Finds and displays a Puesto entity.
     *
     * @Route("/{id}", name="puesto_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Puesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Puesto.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['Puesto'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Puesto'
        );
    }

    /**
     * Displays a form to edit an existing Puesto entity.
     *
     * @Route("/editar/{id}", name="puesto_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Puesto:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Puesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Puesto.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Puesto'
        );
    }

    /**
    * Creates a form to edit a Puesto entity.
    *
    * @param Puesto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Puesto $entity)
    {
        $form = $this->createForm(new PuestoType(), $entity, array(
            'action' => $this->generateUrl('puesto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Puesto entity.
     *
     * @Route("/actualizar/{id}", name="puesto_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Puesto:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Puesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Puesto.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('puesto'));
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
            'page_title' => 'Editar Puesto'
        );
    }
    /**
     * Deletes a Puesto entity.
     *
     * @Route("/borrar/{id}", name="puesto_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Puesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Puesto.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('puesto'));
    }
}
