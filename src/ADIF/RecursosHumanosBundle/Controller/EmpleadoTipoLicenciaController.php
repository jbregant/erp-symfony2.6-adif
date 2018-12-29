<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\EmpleadoTipoLicencia;
use ADIF\RecursosHumanosBundle\Form\EmpleadoTipoLicenciaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * EmpleadoTipoLicencia controller.
 *
 * @Route("/empleados_tipolicencia")
 * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
 */
class EmpleadoTipoLicenciaController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'EmpleadoTipoLicencia' => $this->generateUrl('empleados_tipolicencia')
        );
    }
    /**
     * Lists all EmpleadoTipoLicencia entities.
     *
     * @Route("/", name="empleados_tipolicencia")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoTipoLicencia')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['EmpleadoTipoLicencia'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'EmpleadoTipoLicencia',
            'page_info' => 'Lista de empleadotipolicencia'
        );
    }
    /**
     * Creates a new EmpleadoTipoLicencia entity.
     *
     * @Route("/insertar", name="empleados_tipolicencia_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:EmpleadoTipoLicencia:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new EmpleadoTipoLicencia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('empleados_tipolicencia'));
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
            'page_title' => 'Crear EmpleadoTipoLicencia',
        );
    }

    /**
    * Creates a form to create a EmpleadoTipoLicencia entity.
    *
    * @param EmpleadoTipoLicencia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(EmpleadoTipoLicencia $entity)
    {
        $form = $this->createForm(new EmpleadoTipoLicenciaType($this->getDoctrine()->getManager($this->getEntityManager())), $entity, array(
            'action' => $this->generateUrl('empleados_tipolicencia_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EmpleadoTipoLicencia entity.
     *
     * @Route("/crear", name="empleados_tipolicencia_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new EmpleadoTipoLicencia();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear EmpleadoTipoLicencia'
        );
}

    /**
     * Finds and displays a EmpleadoTipoLicencia entity.
     *
     * @Route("/{id}", name="empleados_tipolicencia_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoTipoLicencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoTipoLicencia.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['EmpleadoTipoLicencia'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver EmpleadoTipoLicencia'
        );
    }

    /**
     * Displays a form to edit an existing EmpleadoTipoLicencia entity.
     *
     * @Route("/editar/{id}", name="empleados_tipolicencia_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:EmpleadoTipoLicencia:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoTipoLicencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoTipoLicencia.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar EmpleadoTipoLicencia'
        );
    }

    /**
    * Creates a form to edit a EmpleadoTipoLicencia entity.
    *
    * @param EmpleadoTipoLicencia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EmpleadoTipoLicencia $entity)
    {
        $form = $this->createForm(new EmpleadoTipoLicenciaType($this->getDoctrine()->getManager($this->getEntityManager())), $entity, array(
            'action' => $this->generateUrl('empleados_tipolicencia_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing EmpleadoTipoLicencia entity.
     *
     * @Route("/actualizar/{id}", name="empleados_tipolicencia_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:EmpleadoTipoLicencia:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoTipoLicencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoTipoLicencia.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('empleados_tipolicencia'));
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
            'page_title' => 'Editar EmpleadoTipoLicencia'
        );
    }
    /**
     * Deletes a EmpleadoTipoLicencia entity.
     *
     * @Route("/borrar/{id}", name="empleados_tipolicencia_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoTipoLicencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoTipoLicencia.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('empleados_tipolicencia'));
    }
}
