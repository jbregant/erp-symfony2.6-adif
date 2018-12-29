<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\NivelOrganizacional;
use ADIF\RecursosHumanosBundle\Form\NivelOrganizacionalType;

/**
 * NivelOrganizacional controller.
 *
 * @Route("/empleados")
  */
class NivelOrganizacionalController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Nivel organizacional' => $this->generateUrl('empleados_nivel_organizacional')
        );
    }
    /**
     * Lists all NivelOrganizacional entities.
     *
     * @Route("/nivel_organizacional/", name="empleados_nivel_organizacional")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['NivelOrganizacional'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Nivel organizacional',
            'page_info' => 'Lista de nivel organizacional'
        );
    }

    /**
     * Tabla para NivelOrganizacional .
     *
     * @Route("/nivel_organizacional/index_table/", name="empleados_nivel_organizacional_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:NivelOrganizacional')->findAll();
        
        $bread = $this->base_breadcrumbs;
        $bread['NivelOrganizacional'] = null;

    return $this->render('ADIFRecursosHumanosBundle:NivelOrganizacional:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new NivelOrganizacional entity.
     *
     * @Route("/nivel_organizacional/insertar", name="empleados_nivel_organizacional_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:NivelOrganizacional:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new NivelOrganizacional();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('empleados_nivel_organizacional'));
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
            'page_title' => 'Crear nivel organizacional',
        );
    }

    /**
    * Creates a form to create a NivelOrganizacional entity.
    *
    * @param NivelOrganizacional $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(NivelOrganizacional $entity)
    {
        $form = $this->createForm(new NivelOrganizacionalType(), $entity, array(
            'action' => $this->generateUrl('empleados_nivel_organizacional_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new NivelOrganizacional entity.
     *
     * @Route("/nivel_organizacional/crear", name="empleados_nivel_organizacional_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new NivelOrganizacional();
        $form   = $this->createCreateForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear nivel organizacional'
        );
}

    /**
     * Finds and displays a NivelOrganizacional entity.
     *
     * @Route("/nivel_organizacional/{id}", name="empleados_nivel_organizacional_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:NivelOrganizacional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad NivelOrganizacional.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['NivelOrganizacional'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver nivel organizacional'
        );
    }

    /**
     * Displays a form to edit an existing NivelOrganizacional entity.
     *
     * @Route("/nivel_organizacional/editar/{id}", name="empleados_nivel_organizacional_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:NivelOrganizacional:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:NivelOrganizacional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad NivelOrganizacional.');
        }

        $editForm = $this->createEditForm($entity);
        
        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar nivel organizacional'
        );
    }

    /**
    * Creates a form to edit a NivelOrganizacional entity.
    *
    * @param NivelOrganizacional $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(NivelOrganizacional $entity)
    {
        $form = $this->createForm(new NivelOrganizacionalType(), $entity, array(
            'action' => $this->generateUrl('empleados_nivel_organizacional_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing NivelOrganizacional entity.
     *
     * @Route("/nivel_organizacional/actualizar/{id}", name="empleados_nivel_organizacional_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:NivelOrganizacional:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:NivelOrganizacional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad NivelOrganizacional.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('empleados_nivel_organizacional'));
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
            'page_title' => 'Editar nivel organizacional'
        );
    }
    /**
     * Deletes a NivelOrganizacional entity.
     *
     * @Route("/nivel_organizacional/borrar/{id}", name="empleados_nivel_organizacional_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:NivelOrganizacional')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad NivelOrganizacional.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('empleados_nivel_organizacional'));
    }
}
