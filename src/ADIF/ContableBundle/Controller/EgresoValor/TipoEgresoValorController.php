<?php

namespace ADIF\ContableBundle\Controller\EgresoValor;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\EgresoValor\TipoEgresoValor;
use ADIF\ContableBundle\Form\EgresoValor\TipoEgresoValorType;

/**
 * EgresoValor\TipoEgresoValor controller.
 *
 * @Route("/tipoegresovalor")
 */
class TipoEgresoValorController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Tipos de egreso de valor' => $this->generateUrl('tipoegresovalor')
        );
    }

    /**
     * Lists all EgresoValor\TipoEgresoValor entities.
     *
     * @Route("/", name="tipoegresovalor")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:EgresoValor\TipoEgresoValor')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de egreso de valor'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Tipos de egreso de valor',
            'page_info' => 'Lista de tipos de egreso de valor'
        );
    }

    /**
     * Creates a new EgresoValor\TipoEgresoValor entity.
     *
     * @Route("/insertar", name="tipoegresovalor_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:EgresoValor\TipoEgresoValor:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new TipoEgresoValor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipoegresovalor'));
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
            'page_title' => 'Crear tipo de egreso de valor',
        );
    }

    /**
     * Creates a form to create a EgresoValor\TipoEgresoValor entity.
     *
     * @param TipoEgresoValor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoEgresoValor $entity) {
        $form = $this->createForm(new TipoEgresoValorType(), $entity, array(
            'action' => $this->generateUrl('tipoegresovalor_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new EgresoValor\TipoEgresoValor entity.
     *
     * @Route("/crear", name="tipoegresovalor_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new TipoEgresoValor();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tipo de egreso de valor'
        );
    }

    /**
     * Finds and displays a EgresoValor\TipoEgresoValor entity.
     *
     * @Route("/{id}", name="tipoegresovalor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\TipoEgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\TipoEgresoValor.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->__toString()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver tipo de egreso de valor'
        );
    }

    /**
     * Displays a form to edit an existing EgresoValor\TipoEgresoValor entity.
     *
     * @Route("/editar/{id}", name="tipoegresovalor_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:EgresoValor\TipoEgresoValor:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\TipoEgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\TipoEgresoValor.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de egreso de valor'
        );
    }

    /**
     * Creates a form to edit a EgresoValor\TipoEgresoValor entity.
     *
     * @param TipoEgresoValor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TipoEgresoValor $entity) {
        $form = $this->createForm(new TipoEgresoValorType(), $entity, array(
            'action' => $this->generateUrl('tipoegresovalor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing EgresoValor\TipoEgresoValor entity.
     *
     * @Route("/actualizar/{id}", name="tipoegresovalor_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:EgresoValor\TipoEgresoValor:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\TipoEgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\TipoEgresoValor.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipoegresovalor'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de egreso de valor'
        );
    }

    /**
     * Deletes a EgresoValor\TipoEgresoValor entity.
     *
     * @Route("/borrar/{id}", name="tipoegresovalor_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\TipoEgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\TipoEgresoValor.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('tipoegresovalor'));
    }

}
