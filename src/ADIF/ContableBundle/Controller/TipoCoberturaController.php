<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\TipoCobertura;
use ADIF\ContableBundle\Form\TipoCoberturaType;

/**
 * TipoCobertura controller.
 *
 * 
 * @Route("/tipocobertura")
  */
class TipoCoberturaController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'TipoCobertura' => $this->generateUrl('tipo_cobertura')
        );
    }
    /**
     * Lists all TipoCobertura entities.
     *
     * @Route("/", name="tipo_cobertura")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['TipoCobertura'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'TipoCobertura',
            'page_info' => 'Lista de tipocobertura'
        );
    }

    /**
     * Tabla para TipoCobertura .
     *
     * @Route("/index_table/", name="tipo_cobertura_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:TipoCobertura')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['TipoCobertura'] = null;

    return $this->render('ADIFContableBundle:TipoCobertura:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new TipoCobertura entity.
     *
     * @Route("/insertar", name="tipo_cobertura_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:TipoCobertura:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TipoCobertura();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipo_cobertura'));
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
            'page_title' => 'Crear TipoCobertura',
        );
    }

    /**
    * Creates a form to create a TipoCobertura entity.
    *
    * @param TipoCobertura $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(TipoCobertura $entity)
    {
        $form = $this->createForm(new TipoCoberturaType(), $entity, array(
            'action' => $this->generateUrl('tipo_cobertura_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoCobertura entity.
     *
     * @Route("/crear", name="tipo_cobertura_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TipoCobertura();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear TipoCobertura'
        );
}

    /**
     * Finds and displays a TipoCobertura entity.
     *
     * @Route("/{id}", name="tipo_cobertura_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:TipoCobertura')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoCobertura.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['TipoCobertura'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver TipoCobertura'
        );
    }

    /**
     * Displays a form to edit an existing TipoCobertura entity.
     *
     * @Route("/editar/{id}", name="tipo_cobertura_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:TipoCobertura:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:TipoCobertura')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoCobertura.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar TipoCobertura'
        );
    }

    /**
    * Creates a form to edit a TipoCobertura entity.
    *
    * @param TipoCobertura $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoCobertura $entity)
    {
        $form = $this->createForm(new TipoCoberturaType(), $entity, array(
            'action' => $this->generateUrl('tipo_cobertura_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing TipoCobertura entity.
     *
     * @Route("/actualizar/{id}", name="tipo_cobertura_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:TipoCobertura:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:TipoCobertura')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoCobertura.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipo_cobertura'));
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
            'page_title' => 'Editar TipoCobertura'
        );
    }
    /**
     * Deletes a TipoCobertura entity.
     *
     * @Route("/borrar/{id}", name="tipo_cobertura_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:TipoCobertura')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoCobertura.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('tipo_cobertura'));
    }
}
