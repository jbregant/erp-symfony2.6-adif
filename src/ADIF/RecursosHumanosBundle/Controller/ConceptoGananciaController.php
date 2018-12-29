<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia;
use ADIF\RecursosHumanosBundle\Form\ConceptoGananciaType;

/**
 * ConceptoGanancia controller.
 *
 * @Route("/ganancia/concepto")
  */
class ConceptoGananciaController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'ConceptoGanancia' => $this->generateUrl('ganancia_concepto')
        );
    }
    /**
     * Lists all ConceptoGanancia entities.
     *
     * @Route("/", name="ganancia_concepto")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['ConceptoGanancia'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Concepto ganancia',
            'page_info' => 'Lista de concepto ganancia'
        );
    }

    /**
     * Tabla para ConceptoGanancia .
     *
     * @Route("/index_table/", name="ganancia_concepto_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['ConceptoGanancia'] = null;

    return $this->render('ADIFRecursosHumanosBundle:ConceptoGanancia:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new ConceptoGanancia entity.
     *
     * @Route("/insertar", name="ganancia_concepto_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:ConceptoGanancia:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ConceptoGanancia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
			
			/** integer $ordenAplicacion **/
			$ordenAplicacion = $entity->getTipoConceptoGanancia()->getOrdenAplicacion();
			$entity->setOrdenAplicacion($ordenAplicacion);
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ganancia_concepto'));
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
            'page_title' => 'Crear concepto ganancia',
        );
    }

    /**
    * Creates a form to create a ConceptoGanancia entity.
    *
    * @param ConceptoGanancia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(ConceptoGanancia $entity)
    {
        $form = $this->createForm(new ConceptoGananciaType(), $entity, array(
            'action' => $this->generateUrl('ganancia_concepto_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ConceptoGanancia entity.
     *
     * @Route("/crear", name="ganancia_concepto_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ConceptoGanancia();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear concepto ganancia'
        );
}

    /**
     * Finds and displays a ConceptoGanancia entity.
     *
     * @Route("/{id}", name="ganancia_concepto_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoGanancia.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['ConceptoGanancia'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver concepto ganancia'
        );
    }

    /**
     * Displays a form to edit an existing ConceptoGanancia entity.
     *
     * @Route("/editar/{id}", name="ganancia_concepto_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:ConceptoGanancia:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoGanancia.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar concepto ganancia'
        );
    }

    /**
    * Creates a form to edit a ConceptoGanancia entity.
    *
    * @param ConceptoGanancia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ConceptoGanancia $entity)
    {
        $form = $this->createForm(new ConceptoGananciaType(), $entity, array(
            'action' => $this->generateUrl('ganancia_concepto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing ConceptoGanancia entity.
     *
     * @Route("/actualizar/{id}", name="ganancia_concepto_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:ConceptoGanancia:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoGanancia.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            /** integer $ordenAplicacion **/
			$ordenAplicacion = $entity->getTipoConceptoGanancia()->getOrdenAplicacion();
			$entity->setOrdenAplicacion($ordenAplicacion);
            
            $em->flush();

            return $this->redirect($this->generateUrl('ganancia_concepto'));
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
            'page_title' => 'Editar concepto ganancia'
        );
    }
    /**
     * Deletes a ConceptoGanancia entity.
     *
     * @Route("/borrar/{id}", name="ganancia_concepto_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoGanancia.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('ganancia_concepto'));
    }
}
