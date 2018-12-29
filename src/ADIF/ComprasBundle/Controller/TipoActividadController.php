<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\TipoActividad;
use ADIF\ComprasBundle\Form\TipoActividadType;

/**
 * TipoActividad controller.
 *
 * @Route("/tipoactividad")
 */
class TipoActividadController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Tipos de actividad' => $this->generateUrl('tipoactividad')
        );
    }

    /**
     * Lists all TipoActividad entities.
     *
     * @Route("/", name="tipoactividad")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:TipoActividad')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de actividad'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Tipo de actividad',
            'page_info' => 'Lista de tipos de actividad'
        );
    }

    /**
     * Creates a new TipoActividad entity.
     *
     * @Route("/insertar", name="tipoactividad_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:TipoActividad:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new TipoActividad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipoactividad'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tipo de actividad',
        );
    }

    /**
     * Creates a form to create a TipoActividad entity.
     *
     * @param TipoActividad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoActividad $entity) {
        $form = $this->createForm(new TipoActividadType(), $entity, array(
            'action' => $this->generateUrl('tipoactividad_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoActividad entity.
     *
     * @Route("/crear", name="tipoactividad_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new TipoActividad();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tipo de actividad'
        );
    }

    /**
     * Finds and displays a TipoActividad entity.
     *
     * @Route("/{id}", name="tipoactividad_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:TipoActividad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoActividad.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacion()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver tipo de actividad'
        );
    }

    /**
     * Displays a form to edit an existing TipoActividad entity.
     *
     * @Route("/editar/{id}", name="tipoactividad_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:TipoActividad:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:TipoActividad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoActividad.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacion()] = $this->generateUrl('tipoactividad_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de actividad'
        );
    }

    /**
     * Creates a form to edit a TipoActividad entity.
     *
     * @param TipoActividad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TipoActividad $entity) {
        $form = $this->createForm(new TipoActividadType(), $entity, array(
            'action' => $this->generateUrl('tipoactividad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing TipoActividad entity.
     *
     * @Route("/actualizar/{id}", name="tipoactividad_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:TipoActividad:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:TipoActividad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoActividad.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipoactividad'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacion()] = $this->generateUrl('tipoactividad_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de actividad'
        );
    }

    /**
     * Deletes a TipoActividad entity.
     *
     * @Route("/borrar/{id}", name="tipoactividad_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }
    
    /**
     * Tabla para TipoActividad.
     *
     * @Route("/index_table/", name="tipoactividad_index_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:TipoActividad')->findAll();

        return $this->render('ADIFComprasBundle:TipoActividad:index_table.html.twig', //
                        array('entities' => $entities)
        );
    }

}
