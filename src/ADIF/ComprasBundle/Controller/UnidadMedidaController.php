<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\UnidadMedida;
use ADIF\ComprasBundle\Form\UnidadMedidaType;

/**
 * UnidadMedida controller.
 *
 * @Route("/unidadmedida")
 */
class UnidadMedidaController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventario' => '',
            'Configuración' => '',
            'Unidades de medida' => $this->generateUrl('unidadmedida')
        );
    }

    /**
     * Lists all UnidadMedida entities.
     *
     * @Route("/", name="unidadmedida")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:UnidadMedida')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Unidades de medida'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Unidad de medida',
            'page_info' => 'Lista de Unidades de Medida'
        );
    }

    /**
     * Creates a new UnidadMedida entity.
     *
     * @Route("/insertar", name="unidadmedida_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:UnidadMedida:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new UnidadMedida();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('unidadmedida'));
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
            'page_title' => 'Crear Unidad de Medida',
        );
    }

    /**
     * Creates a form to create a UnidadMedida entity.
     *
     * @param UnidadMedida $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(UnidadMedida $entity) {
        $form = $this->createForm(new UnidadMedidaType(), $entity, array(
            'action' => $this->generateUrl('unidadmedida_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new UnidadMedida entity.
     *
     * @Route("/crear", name="unidadmedida_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new UnidadMedida();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Unidad de Medida'
        );
    }

    /**
     * Finds and displays a UnidadMedida entity.
     *
     * @Route("/{id}", name="unidadmedida_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:UnidadMedida')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Unidad de Medida.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionUnidadMedida()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Unidad de Medida'
        );
    }

    /**
     * Displays a form to edit an existing UnidadMedida entity.
     *
     * @Route("/editar/{id}", name="unidadmedida_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:UnidadMedida:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:UnidadMedida')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Unidad de Medida.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionUnidadMedida()] = $this->generateUrl('unidadmedida_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Unidad de Medida'
        );
    }

    /**
     * Creates a form to edit a UnidadMedida entity.
     *
     * @param UnidadMedida $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(UnidadMedida $entity) {
        $form = $this->createForm(new UnidadMedidaType(), $entity, array(
            'action' => $this->generateUrl('unidadmedida_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing UnidadMedida entity.
     *
     * @Route("/actualizar/{id}", name="unidadmedida_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:UnidadMedida:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:UnidadMedida')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Unidad de Medida.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('unidadmedida'));
        } //.
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionUnidadMedida()] = $this->generateUrl('unidadmedida_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Unidad de Medida'
        );
    }

    /**
     * Deletes a UnidadMedida entity.
     *
     * @Route("/borrar/{id}", name="unidadmedida_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

}
