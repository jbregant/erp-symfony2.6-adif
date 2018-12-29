<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ComprasBundle\Entity\AspectoEvaluacion;
use ADIF\ComprasBundle\Form\AspectoEvaluacionType;

/**
 * AspectoEvaluacion controller.
 *
 * @Route("/aspectoevaluacion")
 */
class AspectoEvaluacionController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Aspectos de evaluación' => $this->generateUrl('aspectoevaluacion')
        );
    }

    /**
     * Lists all AspectoEvaluacion entities.
     *
     * @Route("/", name="aspectoevaluacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFComprasBundle:AspectoEvaluacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Aspectos de evaluación'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Aspecto de evaluación',
            'page_info' => 'Lista de aspectos de evaluación'
        );
    }

    /**
     * Creates a new AspectoEvaluacion entity.
     *
     * @Route("/insertar", name="aspectoevaluacion_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:AspectoEvaluacion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new AspectoEvaluacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('aspectoevaluacion'));
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
            'page_title' => 'Crear aspecto de evaluación',
        );
    }

    /**
     * Creates a form to create a AspectoEvaluacion entity.
     *
     * @param AspectoEvaluacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AspectoEvaluacion $entity) {
        $form = $this->createForm(new AspectoEvaluacionType(), $entity, array(
            'action' => $this->generateUrl('aspectoevaluacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new AspectoEvaluacion entity.
     *
     * @Route("/crear", name="aspectoevaluacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new AspectoEvaluacion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear aspecto de evaluación'
        );
    }

    /**
     * Finds and displays a AspectoEvaluacion entity.
     *
     * @Route("/{id}", name="aspectoevaluacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:AspectoEvaluacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AspectoEvaluacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionAspectoEvaluacion()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver aspecto de evaluación'
        );
    }

    /**
     * Displays a form to edit an existing AspectoEvaluacion entity.
     *
     * @Route("/editar/{id}", name="aspectoevaluacion_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:AspectoEvaluacion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:AspectoEvaluacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AspectoEvaluacion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionAspectoEvaluacion()] = $this->generateUrl('aspectoevaluacion_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar aspecto de evaluación'
        );
    }

    /**
     * Creates a form to edit a AspectoEvaluacion entity.
     *
     * @param AspectoEvaluacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(AspectoEvaluacion $entity) {
        $form = $this->createForm(new AspectoEvaluacionType(), $entity, array(
            'action' => $this->generateUrl('aspectoevaluacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing AspectoEvaluacion entity.
     *
     * @Route("/actualizar/{id}", name="aspectoevaluacion_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:AspectoEvaluacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:AspectoEvaluacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AspectoEvaluacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('aspectoevaluacion'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getDenominacionAspectoEvaluacion()] = $this->generateUrl('aspectoevaluacion_show', array('id' => $entity->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar aspecto de evaluación'
        );
    }

    /**
     * Deletes a AspectoEvaluacion entity.
     *
     * @Route("/borrar/{id}", name="aspectoevaluacion_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

}
