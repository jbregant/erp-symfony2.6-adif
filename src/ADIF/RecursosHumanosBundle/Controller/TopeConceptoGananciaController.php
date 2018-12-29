<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\TopeConceptoGanancia;
use ADIF\RecursosHumanosBundle\Form\TopeConceptoGananciaType;

/**
 * TopeConceptoGanancia controller.
 *
 * @Route("/ganancia/tope_concepto")
  */
class TopeConceptoGananciaController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'TopeConceptoGanancia' => $this->generateUrl('tope_concepto')
        );
    }
    /**
     * Lists all TopeConceptoGanancia entities.
     *
     * @Route("/", name="tope_concepto")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['TopeConceptoGanancia'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Tope concepto',
            'page_info' => 'Lista de topes conceptos'
        );
    }

    /**
     * Tabla para TopeConceptoGanancia .
     *
     * @Route("/index_table/", name="tope_concepto_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['TopeConceptoGanancia'] = null;

    return $this->render('ADIFRecursosHumanosBundle:TopeConceptoGanancia:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new TopeConceptoGanancia entity.
     *
     * @Route("/insertar", name="tope_concepto_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:TopeConceptoGanancia:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TopeConceptoGanancia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tope_concepto'));
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
            'page_title' => 'Crear tope concepto',
        );
    }

    /**
    * Creates a form to create a TopeConceptoGanancia entity.
    *
    * @param TopeConceptoGanancia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(TopeConceptoGanancia $entity)
    {
        $form = $this->createForm(new TopeConceptoGananciaType(), $entity, array(
            'action' => $this->generateUrl('tope_concepto_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TopeConceptoGanancia entity.
     *
     * @Route("/crear", name="tope_concepto_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TopeConceptoGanancia();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tope concepto'
        );
}

    /**
     * Finds and displays a TopeConceptoGanancia entity.
     *
     * @Route("/{id}", name="tope_concepto_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TopeConceptoGanancia.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['TopeConceptoGanancia'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver tope concepto'
        );
    }

    /**
     * Displays a form to edit an existing TopeConceptoGanancia entity.
     *
     * @Route("/editar/{id}", name="tope_concepto_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:TopeConceptoGanancia:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TopeConceptoGanancia.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tope concepto'
        );
    }

    /**
    * Creates a form to edit a TopeConceptoGanancia entity.
    *
    * @param TopeConceptoGanancia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TopeConceptoGanancia $entity)
    {
        $form = $this->createForm(new TopeConceptoGananciaType(), $entity, array(
            'action' => $this->generateUrl('tope_concepto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing TopeConceptoGanancia entity.
     *
     * @Route("/actualizar/{id}", name="tope_concepto_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:TopeConceptoGanancia:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TopeConceptoGanancia.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tope_concepto'));
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
            'page_title' => 'Editar tope concepto'
        );
    }
    /**
     * Deletes a TopeConceptoGanancia entity.
     *
     * @Route("/borrar/{id}", name="tope_concepto_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TopeConceptoGanancia.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('tope_concepto'));
    }
}
