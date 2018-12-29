<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\TableroMt;
use ADIF\RecursosHumanosBundle\Form\TableroMtType;

/**
 * TableroMt controller.
 *
 * @Route("/tablero_mt")
  */
class TableroMtController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'TableroMt' => $this->generateUrl('tablero_mt')
        );
    }
    /**
     * Lists all TableroMt entities.
     *
     * @Route("/", name="tablero_mt")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['TableroMt'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'TableroMt',
            'page_info' => 'Lista de tableromt'
        );
    }

    /**
     * Tabla para TableroMt .
     *
     * @Route("/index_table/", name="tablero_mt_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:TableroMt')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['TableroMt'] = null;

    return $this->render('ADIFRecursosHumanosBundle:TableroMt:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new TableroMt entity.
     *
     * @Route("/insertar", name="tablero_mt_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:TableroMt:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TableroMt();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tablero_mt'));
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
            'page_title' => 'Crear TableroMt',
        );
    }

    /**
    * Creates a form to create a TableroMt entity.
    *
    * @param TableroMt $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(TableroMt $entity)
    {
        $form = $this->createForm(new TableroMtType(), $entity, array(
            'action' => $this->generateUrl('tablero_mt_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TableroMt entity.
     *
     * @Route("/crear", name="tablero_mt_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TableroMt();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear TableroMt'
        );
}

    /**
     * Finds and displays a TableroMt entity.
     *
     * @Route("/{id}", name="tablero_mt_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TableroMt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TableroMt.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['TableroMt'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver TableroMt'
        );
    }

    /**
     * Displays a form to edit an existing TableroMt entity.
     *
     * @Route("/editar/{id}", name="tablero_mt_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:TableroMt:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TableroMt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TableroMt.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar TableroMt'
        );
    }

    /**
    * Creates a form to edit a TableroMt entity.
    *
    * @param TableroMt $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TableroMt $entity)
    {
        $form = $this->createForm(new TableroMtType(), $entity, array(
            'action' => $this->generateUrl('tablero_mt_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing TableroMt entity.
     *
     * @Route("/actualizar/{id}", name="tablero_mt_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:TableroMt:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TableroMt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TableroMt.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tablero_mt'));
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
            'page_title' => 'Editar TableroMt'
        );
    }
    /**
     * Deletes a TableroMt entity.
     *
     * @Route("/borrar/{id}", name="tablero_mt_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TableroMt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TableroMt.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('tablero_mt'));
    }
}
