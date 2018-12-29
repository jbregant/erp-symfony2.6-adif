<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Subgerencia;
use ADIF\RecursosHumanosBundle\Form\SubgerenciaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Subgerencia controller.
 *
 * @Route("/subgerencias")
 * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
 */
class SubgerenciaController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Subgerencias' => $this->generateUrl('subgerencias')
        );
    }

    /**
     * Lists all Subgerencia entities.
     *
     * @Route("/", name="subgerencias")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Subgerencia')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Subgerencias'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Subgerencias',
            'page_info' => 'Lista de subgerencias'
        );
    }

    /**
     * Creates a new Subgerencia entity.
     *
     * @Route("/insertar", name="subgerencias_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Subgerencia:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Subgerencia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('subgerencias'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear subgerencia',
        );
    }

    /**
     * Creates a form to create a Subgerencia entity.
     *
     * @param Subgerencia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Subgerencia $entity) {
        $form = $this->createForm(new SubgerenciaType(), $entity, array(
            'action' => $this->generateUrl('subgerencias_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Subgerencia entity.
     *
     * @Route("/crear", name="subgerencias_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Subgerencia();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear subgerencia'
        );
    }

    /**
     * Finds and displays a Subgerencia entity.
     *
     * @Route("/{id}", name="subgerencias_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Subgerencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Subgerencia.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Subgerencia'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver subgerencia'
        );
    }

    /**
     * Displays a form to edit an existing Subgerencia entity.
     *
     * @Route("/editar/{id}", name="subgerencias_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Subgerencia:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Subgerencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Subgerencia.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar subgerencia'
        );
    }

    /**
     * Creates a form to edit a Subgerencia entity.
     *
     * @param Subgerencia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Subgerencia $entity) {
        $form = $this->createForm(new SubgerenciaType(), $entity, array(
            'action' => $this->generateUrl('subgerencias_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Subgerencia entity.
     *
     * @Route("/actualizar/{id}", name="subgerencias_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Subgerencia:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Subgerencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Subgerencia.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('subgerencias'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar subgerencia'
        );
    }

    /**
     * Deletes a Subgerencia entity.
     *
     * @Route("/borrar/{id}", name="subgerencias_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

}
