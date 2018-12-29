<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\EstadoRevisionPoliza;
use ADIF\ContableBundle\Form\EstadoRevisionPolizaType;

/**
 * EstadoRevisionPoliza controller.
 *
 * 
 * @Route("/estado_revision_poliza")
  */
class EstadoRevisionPolizaController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'EstadoRevisionPoliza' => $this->generateUrl('estado_revision_poliza')
        );
    }
    /**
     * Lists all EstadoRevisionPoliza entities.
     *
     * @Route("/", name="estado_revision_poliza")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['EstadoRevisionPoliza'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'estado revisi&oacute;n poliza',
            'page_info' => 'Lista de estados de revisi&oacute;n de polizas'
        );
    }

    /**
     * Tabla para EstadoRevisionPoliza .
     *
     * @Route("/index_table/", name="estado_revision_poliza_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:EstadoRevisionPoliza')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['EstadoRevisionPoliza'] = null;

    return $this->render('ADIFContableBundle:EstadoRevisionPoliza:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new EstadoRevisionPoliza entity.
     *
     * @Route("/insertar", name="estado_revision_poliza_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:EstadoRevisionPoliza:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new EstadoRevisionPoliza();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estado_revision_poliza'));
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
            'page_title' => 'Crear estado revisi&oacute;n poliza',
        );
    }

    /**
    * Creates a form to create a EstadoRevisionPoliza entity.
    *
    * @param EstadoRevisionPoliza $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(EstadoRevisionPoliza $entity)
    {
        $form = $this->createForm(new EstadoRevisionPolizaType(), $entity, array(
            'action' => $this->generateUrl('estado_revision_poliza_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EstadoRevisionPoliza entity.
     *
     * @Route("/crear", name="estado_revision_poliza_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new EstadoRevisionPoliza();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear estado revisi&oacute;n poliza'
        );
}

    /**
     * Finds and displays a EstadoRevisionPoliza entity.
     *
     * @Route("/{id}", name="estado_revision_poliza_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EstadoRevisionPoliza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoRevisionPoliza.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['EstadoRevisionPoliza'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver estado revisi&oacute;n poliza'
        );
    }

    /**
     * Displays a form to edit an existing EstadoRevisionPoliza entity.
     *
     * @Route("/editar/{id}", name="estado_revision_poliza_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:EstadoRevisionPoliza:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EstadoRevisionPoliza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoRevisionPoliza.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar estado revisi&oacute;n poliza'
        );
    }

    /**
    * Creates a form to edit a EstadoRevisionPoliza entity.
    *
    * @param EstadoRevisionPoliza $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EstadoRevisionPoliza $entity)
    {
        $form = $this->createForm(new EstadoRevisionPolizaType(), $entity, array(
            'action' => $this->generateUrl('estado_revision_poliza_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing EstadoRevisionPoliza entity.
     *
     * @Route("/actualizar/{id}", name="estado_revision_poliza_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:EstadoRevisionPoliza:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EstadoRevisionPoliza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoRevisionPoliza.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('estado_revision_poliza'));
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
            'page_title' => 'Editar estado revisi&oacute;n poliza'
        );
    }
    /**
     * Deletes a EstadoRevisionPoliza entity.
     *
     * @Route("/borrar/{id}", name="estado_revision_poliza_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:EstadoRevisionPoliza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoRevisionPoliza.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('estado_revision_poliza'));
    }
}
