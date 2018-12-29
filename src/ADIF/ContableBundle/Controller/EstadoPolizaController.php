<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\EstadoPoliza;
use ADIF\ContableBundle\Form\EstadoPolizaType;

/**
 * EstadoPoliza controller.
 *
 * 
 * @Route("/estado_poliza")
  */
class EstadoPolizaController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'EstadoPoliza' => $this->generateUrl('estado_poliza')
        );
    }
    /**
     * Lists all EstadoPoliza entities.
     *
     * @Route("/", name="estado_poliza")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['EstadoPoliza'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'EstadoPoliza',
            'page_info' => 'Lista de estados de polizas'
        );
    }

    /**
     * Tabla para EstadoPoliza .
     *
     * @Route("/index_table/", name="estado_poliza_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:EstadoPoliza')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['EstadoPoliza'] = null;

    return $this->render('ADIFContableBundle:EstadoPoliza:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new EstadoPoliza entity.
     *
     * @Route("/insertar", name="estado_poliza_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:EstadoPoliza:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new EstadoPoliza();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estado_poliza'));
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
            'page_title' => 'Crear estado poliza',
        );
    }

    /**
    * Creates a form to create a EstadoPoliza entity.
    *
    * @param EstadoPoliza $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(EstadoPoliza $entity)
    {
        $form = $this->createForm(new EstadoPolizaType(), $entity, array(
            'action' => $this->generateUrl('estado_poliza_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EstadoPoliza entity.
     *
     * @Route("/crear", name="estado_poliza_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new EstadoPoliza();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear estado poliza'
        );
}

    /**
     * Finds and displays a EstadoPoliza entity.
     *
     * @Route("/{id}", name="estado_poliza_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EstadoPoliza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoPoliza.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['EstadoPoliza'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver estado poliza'
        );
    }

    /**
     * Displays a form to edit an existing EstadoPoliza entity.
     *
     * @Route("/editar/{id}", name="estado_poliza_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:EstadoPoliza:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EstadoPoliza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoPoliza.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar estado poliza'
        );
    }

    /**
    * Creates a form to edit a EstadoPoliza entity.
    *
    * @param EstadoPoliza $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EstadoPoliza $entity)
    {
        $form = $this->createForm(new EstadoPolizaType(), $entity, array(
            'action' => $this->generateUrl('estado_poliza_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing EstadoPoliza entity.
     *
     * @Route("/actualizar/{id}", name="estado_poliza_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:EstadoPoliza:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EstadoPoliza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoPoliza.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('estado_poliza'));
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
            'page_title' => 'Editar estado poliza'
        );
    }
    /**
     * Deletes a EstadoPoliza entity.
     *
     * @Route("/borrar/{id}", name="estado_poliza_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:EstadoPoliza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoPoliza.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('estado_poliza'));
    }
}
