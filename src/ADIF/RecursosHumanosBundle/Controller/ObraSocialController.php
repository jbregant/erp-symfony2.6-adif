<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\ObraSocial;
use ADIF\RecursosHumanosBundle\Form\ObraSocialType;

/**
 * ObraSocial controller.
 *
 * @Route("/obra_social")
  */
class ObraSocialController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'ObraSocial' => $this->generateUrl('obra_social')
        );
    }
    /**
     * Lists all ObraSocial entities.
     *
     * @Route("/", name="obra_social")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['ObraSocial'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'ObraSocial',
            'page_info' => 'Lista de obras sociales'
        );
    }

    /**
     * Tabla para ObraSocial .
     *
     * @Route("/index_table/", name="obra_social_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:ObraSocial')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['ObraSocial'] = null;

    return $this->render('ADIFRecursosHumanosBundle:ObraSocial:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new ObraSocial entity.
     *
     * @Route("/insertar", name="obra_social_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:ObraSocial:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ObraSocial();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
			
			try {
				$em->flush();
				$this->get('session')->getFlashBag() 
							->add('success', "Se ha creado exitosamente la obra social.");
			} catch (Exception $e) {
				$this->get('session')->getFlashBag() 
							->add('error', "Error al crear la obra social.");
			}
            
            return $this->redirect($this->generateUrl('obra_social'));
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
            'page_title' => 'Crear Obra Social',
        );
    }

    /**
    * Creates a form to create a ObraSocial entity.
    *
    * @param ObraSocial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(ObraSocial $entity)
    {
        $form = $this->createForm(new ObraSocialType(), $entity, array(
            'action' => $this->generateUrl('obra_social_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ObraSocial entity.
     *
     * @Route("/crear", name="obra_social_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ObraSocial();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Obra Social'
        );
}

    /**
     * Finds and displays a ObraSocial entity.
     *
     * @Route("/{id}", name="obra_social_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ObraSocial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ObraSocial.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['ObraSocial'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver ObraSocial'
        );
    }

    /**
     * Displays a form to edit an existing ObraSocial entity.
     *
     * @Route("/editar/{id}", name="obra_social_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:ObraSocial:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ObraSocial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ObraSocial.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Obra Social'
        );
    }

    /**
    * Creates a form to edit a ObraSocial entity.
    *
    * @param ObraSocial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ObraSocial $entity)
    {
        $form = $this->createForm(new ObraSocialType(), $entity, array(
            'action' => $this->generateUrl('obra_social_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing ObraSocial entity.
     *
     * @Route("/actualizar/{id}", name="obra_social_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:ObraSocial:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ObraSocial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ObraSocial.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
			
			try {
				$em->flush();
				$this->get('session')->getFlashBag() 
							->add('success', "Se ha modificado exitosamente la obra social.");
			} catch (Exception $e) {
				$this->get('session')->getFlashBag() 
							->add('error', "Error al modificar la obra social.");
			}

            return $this->redirect($this->generateUrl('obra_social'));
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
            'page_title' => 'Editar ObraSocial'
        );
    }
}
