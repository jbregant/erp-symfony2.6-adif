<?php

namespace ADIF\ContableBundle\Controller\Obras;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Obras\FuenteFinanciamiento;
use ADIF\ContableBundle\Form\Obras\FuenteFinanciamientoType;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Obras\FuenteFinanciamiento controller.
 *
 * @Route("/fuentefinanciamiento")
 */
class FuenteFinanciamientoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Fuentes de financiamiento' => $this->generateUrl('fuentefinanciamiento')
        );
    }

    /**
     * Lists all Obras\FuenteFinanciamiento entities.
     *
     * @Route("/", name="fuentefinanciamiento")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Fuentes de financiamiento'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Fuentes de financiamiento',
            'page_info' => 'Lista de fuentes de financiamiento'
        );
    }

    /**
     * Tabla para Obras\FuenteFinanciamiento .
     *
     * @Route("/index_table/", name="fuentefinanciamiento_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Obras\FuenteFinanciamiento')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Fuentes de financiamiento'] = null;

        return $this->render('ADIFContableBundle:Obras/FuenteFinanciamiento:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new Obras\FuenteFinanciamiento entity.
     *
     * @Route("/insertar", name="fuentefinanciamiento_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Obras\FuenteFinanciamiento:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new FuenteFinanciamiento();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('fuentefinanciamiento'));
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
            'page_title' => 'Crear fuente de financiamiento',
        );
    }

    /**
     * Creates a form to create a Obras\FuenteFinanciamiento entity.
     *
     * @param FuenteFinanciamiento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FuenteFinanciamiento $entity) {
        $form = $this->createForm(new FuenteFinanciamientoType(), $entity, array(
            'action' => $this->generateUrl('fuentefinanciamiento_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Obras\FuenteFinanciamiento entity.
     *
     * @Route("/crear", name="fuentefinanciamiento_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new FuenteFinanciamiento();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear fuente de financiamiento'
        );
    }

    /**
     * Finds and displays a Obras\FuenteFinanciamiento entity.
     *
     * @Route("/{id}", name="fuentefinanciamiento_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\FuenteFinanciamiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\FuenteFinanciamiento.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Fuente de financiamiento'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver fuente de financiamiento'
        );
    }

    /**
     * Displays a form to edit an existing Obras\FuenteFinanciamiento entity.
     *
     * @Route("/editar/{id}", name="fuentefinanciamiento_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Obras\FuenteFinanciamiento:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\FuenteFinanciamiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\FuenteFinanciamiento.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar fuente de financiamiento'
        );
    }

    /**
     * Creates a form to edit a Obras\FuenteFinanciamiento entity.
     *
     * @param FuenteFinanciamiento $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(FuenteFinanciamiento $entity) {
        $form = $this->createForm(new FuenteFinanciamientoType(), $entity, array(
            'action' => $this->generateUrl('fuentefinanciamiento_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Obras\FuenteFinanciamiento entity.
     *
     * @Route("/actualizar/{id}", name="fuentefinanciamiento_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Obras\FuenteFinanciamiento:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\FuenteFinanciamiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\FuenteFinanciamiento.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('fuentefinanciamiento'));
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
            'page_title' => 'Editar fuente de financiamiento'
        );
    }

    /**
     * Deletes a Obras\FuenteFinanciamiento entity.
     *
     * @Route("/borrar/{id}", name="fuentefinanciamiento_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Obras\FuenteFinanciamiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\FuenteFinanciamiento.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('fuentefinanciamiento'));
    }

}
