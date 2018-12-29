<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\AreaRrhh;
use ADIF\RecursosHumanosBundle\Form\AreaRrhhType;

/**
 * AreaRrhh controller.
 *
 * @Route("/area_rrhh")
  */
class AreaRrhhController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'AreaRrhh' => $this->generateUrl('area_rrhh')
        );
    }
    /**
     * Lists all AreaRrhh entities.
     *
     * @Route("/", name="area_rrhh")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['AreaRrhh'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'AreaRrhh',
            'page_info' => 'Lista de arearrhh'
        );
    }

    /**
     * Tabla para AreaRrhh .
     *
     * @Route("/index_table/", name="area_rrhh_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:AreaRrhh')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['AreaRrhh'] = null;

    return $this->render('ADIFRecursosHumanosBundle:AreaRrhh:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new AreaRrhh entity.
     *
     * @Route("/insertar", name="area_rrhh_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:AreaRrhh:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new AreaRrhh();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('area_rrhh'));
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
            'page_title' => 'Crear AreaRrhh',
        );
    }

    /**
    * Creates a form to create a AreaRrhh entity.
    *
    * @param AreaRrhh $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(AreaRrhh $entity)
    {
        $form = $this->createForm(new AreaRrhhType(), $entity, array(
            'action' => $this->generateUrl('area_rrhh_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new AreaRrhh entity.
     *
     * @Route("/crear", name="area_rrhh_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new AreaRrhh();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear AreaRrhh'
        );
}

    /**
     * Finds and displays a AreaRrhh entity.
     *
     * @Route("/{id}", name="area_rrhh_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:AreaRrhh')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AreaRrhh.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['AreaRrhh'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver AreaRrhh'
        );
    }

    /**
     * Displays a form to edit an existing AreaRrhh entity.
     *
     * @Route("/editar/{id}", name="area_rrhh_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:AreaRrhh:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:AreaRrhh')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AreaRrhh.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar AreaRrhh'
        );
    }

    /**
    * Creates a form to edit a AreaRrhh entity.
    *
    * @param AreaRrhh $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AreaRrhh $entity)
    {
        $form = $this->createForm(new AreaRrhhType(), $entity, array(
            'action' => $this->generateUrl('area_rrhh_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing AreaRrhh entity.
     *
     * @Route("/actualizar/{id}", name="area_rrhh_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:AreaRrhh:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:AreaRrhh')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AreaRrhh.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('area_rrhh'));
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
            'page_title' => 'Editar AreaRrhh'
        );
    }
    /**
     * Deletes a AreaRrhh entity.
     *
     * @Route("/borrar/{id}", name="area_rrhh_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:AreaRrhh')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AreaRrhh.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('area_rrhh'));
    }
}
