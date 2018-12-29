<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\EscalaImpuesto;
use ADIF\RecursosHumanosBundle\Form\EscalaImpuestoType;

/**
 * EscalaImpuesto controller.
 *
 * @Route("/ganancia/escala_impuesto_mes")
  */
class EscalaImpuestoController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'EscalaImpuesto' => $this->generateUrl('escala_impuesto_mes')
        );
    }
    /**
     * Lists all EscalaImpuesto entities.
     *
     * @Route("/", name="escala_impuesto_mes")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['EscalaImpuesto'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Escala impuesto',
            'page_info' => 'Lista de escala impuesto'
        );
    }

    /**
     * Tabla para EscalaImpuesto .
     *
     * @Route("/index_table/", name="escala_impuesto_mes_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:EscalaImpuesto')->findAll(array(), array('mes' => 'ASC')) ;
        
        $bread = $this->base_breadcrumbs;
        $bread['EscalaImpuesto'] = null;

    return $this->render('ADIFRecursosHumanosBundle:EscalaImpuesto:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new EscalaImpuesto entity.
     *
     * @Route("/insertar", name="escala_impuesto_mes_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:EscalaImpuesto:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new EscalaImpuesto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('escala_impuesto_mes'));
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
            'page_title' => 'Crear escala impuesto',
        );
    }

    /**
    * Creates a form to create a EscalaImpuesto entity.
    *
    * @param EscalaImpuesto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(EscalaImpuesto $entity)
    {
        $form = $this->createForm(new EscalaImpuestoType(), $entity, array(
            'action' => $this->generateUrl('escala_impuesto_mes_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EscalaImpuesto entity.
     *
     * @Route("/crear", name="escala_impuesto_mes_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new EscalaImpuesto();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear escala impuesto'
        );
}

    /**
     * Finds and displays a EscalaImpuesto entity.
     *
     * @Route("/{id}", name="escala_impuesto_mes_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EscalaImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EscalaImpuesto.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['EscalaImpuesto'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver escala impuesto'
        );
    }

    /**
     * Displays a form to edit an existing EscalaImpuesto entity.
     *
     * @Route("/editar/{id}", name="escala_impuesto_mes_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:EscalaImpuesto:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EscalaImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EscalaImpuesto.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar escala impuesto'
        );
    }

    /**
    * Creates a form to edit a EscalaImpuesto entity.
    *
    * @param EscalaImpuesto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EscalaImpuesto $entity)
    {
        $form = $this->createForm(new EscalaImpuestoType(), $entity, array(
            'action' => $this->generateUrl('escala_impuesto_mes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing EscalaImpuesto entity.
     *
     * @Route("/actualizar/{id}", name="escala_impuesto_mes_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:EscalaImpuesto:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EscalaImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EscalaImpuesto.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('escala_impuesto_mes'));
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
            'page_title' => 'Editar EscalaImpuesto'
        );
    }
    /**
     * Deletes a EscalaImpuesto entity.
     *
     * @Route("/borrar/{id}", name="escala_impuesto_mes_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EscalaImpuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EscalaImpuesto.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('escala_impuesto_mes'));
    }
}
