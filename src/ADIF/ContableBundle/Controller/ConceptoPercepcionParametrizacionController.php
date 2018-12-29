<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\ConceptoPercepcionParametrizacion;
use ADIF\ContableBundle\Form\ConceptoPercepcionParametrizacionType;

/**
 * ConceptoPercepcionParametrizacion controller.
 *
 * @Route("/concepto_percepcion_parametrizacion")
 */
class ConceptoPercepcionParametrizacionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Parametrización de conceptos de percepción' => $this->generateUrl('concepto_percepcion_parametrizacion')
        );
    }

    /**
     * Lists all ConceptoPercepcionParametrizacion entities.
     *
     * @Route("/", name="concepto_percepcion_parametrizacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Parametrización de conceptos de percepción'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Parametrización de conceptos de percepción',
            'page_info' => 'Parametrización de conceptos de percepción'
        );
    }

    /**
     * Tabla para ConceptoPercepcionParametrizacion .
     *
     * @Route("/index_table/", name="concepto_percepcion_parametrizacion_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Parametrización de conceptos de percepción'] = null;

        return $this->render('ADIFContableBundle:ConceptoPercepcionParametrizacion:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new ConceptoPercepcionParametrizacion entity.
     *
     * @Route("/insertar", name="concepto_percepcion_parametrizacion_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ConceptoPercepcionParametrizacion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ConceptoPercepcionParametrizacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('concepto_percepcion_parametrizacion'));
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
            'page_title' => 'Crear parametrización de concepto de percepción',
        );
    }

    /**
     * Creates a form to create a ConceptoPercepcionParametrizacion entity.
     *
     * @param ConceptoPercepcionParametrizacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ConceptoPercepcionParametrizacion $entity) {
        $form = $this->createForm(new ConceptoPercepcionParametrizacionType(), $entity, array(
            'action' => $this->generateUrl('concepto_percepcion_parametrizacion_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ConceptoPercepcionParametrizacion entity.
     *
     * @Route("/crear", name="concepto_percepcion_parametrizacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new ConceptoPercepcionParametrizacion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear parametrización de concepto de percepción'
        );
    }

    /**
     * Finds and displays a ConceptoPercepcionParametrizacion entity.
     *
     * @Route("/{id}", name="concepto_percepcion_parametrizacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPercepcionParametrizacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Parametrización de concepto de percepción'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver parametrización de concepto de percepción'
        );
    }

    /**
     * Displays a form to edit an existing ConceptoPercepcionParametrizacion entity.
     *
     * @Route("/editar/{id}", name="concepto_percepcion_parametrizacion_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ConceptoPercepcionParametrizacion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPercepcionParametrizacion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar parametrización de concepto de percepción'
        );
    }

    /**
     * Creates a form to edit a ConceptoPercepcionParametrizacion entity.
     *
     * @param ConceptoPercepcionParametrizacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ConceptoPercepcionParametrizacion $entity) {
        $form = $this->createForm(new ConceptoPercepcionParametrizacionType(), $entity, array(
            'action' => $this->generateUrl('concepto_percepcion_parametrizacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ConceptoPercepcionParametrizacion entity.
     *
     * @Route("/actualizar/{id}", name="concepto_percepcion_parametrizacion_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ConceptoPercepcionParametrizacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPercepcionParametrizacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('concepto_percepcion_parametrizacion'));
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
            'page_title' => 'Editar parametrización de concepto de percepción'
        );
    }

    /**
     * Deletes a ConceptoPercepcionParametrizacion entity.
     *
     * @Route("/borrar/{id}", name="concepto_percepcion_parametrizacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ConceptoPercepcionParametrizacion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('concepto_percepcion_parametrizacion'));
    }

}
