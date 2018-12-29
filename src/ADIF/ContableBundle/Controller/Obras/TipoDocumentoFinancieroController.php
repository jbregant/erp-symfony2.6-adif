<?php

namespace ADIF\ContableBundle\Controller\Obras;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Obras\TipoDocumentoFinanciero;
use ADIF\ContableBundle\Form\Obras\TipoDocumentoFinancieroType;

/**
 * Obras\TipoDocumentoFinanciero controller.
 *
 * @Route("/tipodocumentofinanciero")
 */
class TipoDocumentoFinancieroController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Tipos de documentos financieros' => $this->generateUrl('tipodocumentofinanciero')
        );
    }

    /**
     * Lists all Obras\TipoDocumentoFinanciero entities.
     *
     * @Route("/", name="tipodocumentofinanciero")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Tipos de documentos financieros'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Tipos de documentos financieros',
            'page_info' => 'Lista de tipos de documentos financieros'
        );
    }

    /**
     * Tabla para Obras\TipoDocumentoFinanciero .
     *
     * @Route("/index_table/", name="tipodocumentofinanciero_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Obras\TipoDocumentoFinanciero')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de documentos financieros'] = null;

        return $this->render('ADIFContableBundle:Obras/TipoDocumentoFinanciero:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new Obras\TipoDocumentoFinanciero entity.
     *
     * @Route("/insertar", name="tipodocumentofinanciero_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Obras\TipoDocumentoFinanciero:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new TipoDocumentoFinanciero();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipodocumentofinanciero'));
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
            'page_title' => 'Crear tipo de documento financiero',
        );
    }

    /**
     * Creates a form to create a Obras\TipoDocumentoFinanciero entity.
     *
     * @param TipoDocumentoFinanciero $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoDocumentoFinanciero $entity) {
        $form = $this->createForm(new TipoDocumentoFinancieroType(), $entity, array(
            'action' => $this->generateUrl('tipodocumentofinanciero_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Obras\TipoDocumentoFinanciero entity.
     *
     * @Route("/crear", name="tipodocumentofinanciero_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new TipoDocumentoFinanciero();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear tipo de documento financiero'
        );
    }

    /**
     * Finds and displays a Obras\TipoDocumentoFinanciero entity.
     *
     * @Route("/{id}", name="tipodocumentofinanciero_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\TipoDocumentoFinanciero')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\TipoDocumentoFinanciero.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Tipo de documento financiero'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver tipo de documento financiero'
        );
    }

    /**
     * Displays a form to edit an existing Obras\TipoDocumentoFinanciero entity.
     *
     * @Route("/editar/{id}", name="tipodocumentofinanciero_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Obras\TipoDocumentoFinanciero:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\TipoDocumentoFinanciero')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\TipoDocumentoFinanciero.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar tipo de documento financiero'
        );
    }

    /**
     * Creates a form to edit a Obras\TipoDocumentoFinanciero entity.
     *
     * @param TipoDocumentoFinanciero $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TipoDocumentoFinanciero $entity) {
        $form = $this->createForm(new TipoDocumentoFinancieroType(), $entity, array(
            'action' => $this->generateUrl('tipodocumentofinanciero_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Obras\TipoDocumentoFinanciero entity.
     *
     * @Route("/actualizar/{id}", name="tipodocumentofinanciero_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Obras\TipoDocumentoFinanciero:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\TipoDocumentoFinanciero')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\TipoDocumentoFinanciero.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipodocumentofinanciero'));
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
            'page_title' => 'Editar tipo de documento financiero'
        );
    }

    /**
     * Deletes a Obras\TipoDocumentoFinanciero entity.
     *
     * @Route("/borrar/{id}", name="tipodocumentofinanciero_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Obras\TipoDocumentoFinanciero')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\TipoDocumentoFinanciero.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('tipodocumentofinanciero'));
    }

}
