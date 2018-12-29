<?php

namespace ADIF\ContableBundle\Controller\Obras;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Obras\CategoriaObra;
use ADIF\ContableBundle\Form\Obras\CategoriaObraType;

/**
 * Obras\CategoriaObra controller.
 *
 * @Route("/categoriaobra")
 */
class CategoriaObraController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Categor&iacute;as de obra' => $this->generateUrl('categoriaobra')
        );
    }

    /**
     * Lists all Obras\CategoriaObra entities.
     *
     * @Route("/", name="categoriaobra")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Categor&iacute;as de obra'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Categor&iacute;as de obra',
            'page_info' => 'Lista de categor&iacute;as de obra'
        );
    }

    /**
     * Tabla para Obras\CategoriaObra .
     *
     * @Route("/index_table/", name="categoriaobra_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Obras\CategoriaObra')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Categor&iacute;as de obra'] = null;

        return $this->render('ADIFContableBundle:Obras/CategoriaObra:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new Obras\CategoriaObra entity.
     *
     * @Route("/insertar", name="categoriaobra_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Obras\CategoriaObra:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new CategoriaObra();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categoriaobra'));
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
            'page_title' => 'Crear categor&iacute;a de obra',
        );
    }

    /**
     * Creates a form to create a Obras\CategoriaObra entity.
     *
     * @param CategoriaObra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CategoriaObra $entity) {
        $form = $this->createForm(new CategoriaObraType(), $entity, array(
            'action' => $this->generateUrl('categoriaobra_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Obras\CategoriaObra entity.
     *
     * @Route("/crear", name="categoriaobra_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new CategoriaObra();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear categor&iacute;a de obra'
        );
    }

    /**
     * Finds and displays a Obras\CategoriaObra entity.
     *
     * @Route("/{id}", name="categoriaobra_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\CategoriaObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\CategoriaObra.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Categor&iacute;a de obra'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver categor&iacute;a de obra'
        );
    }

    /**
     * Displays a form to edit an existing Obras\CategoriaObra entity.
     *
     * @Route("/editar/{id}", name="categoriaobra_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Obras\CategoriaObra:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\CategoriaObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\CategoriaObra.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar categor&iacute;a de obra'
        );
    }

    /**
     * Creates a form to edit a Obras\CategoriaObra entity.
     *
     * @param CategoriaObra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CategoriaObra $entity) {
        $form = $this->createForm(new CategoriaObraType(), $entity, array(
            'action' => $this->generateUrl('categoriaobra_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Obras\CategoriaObra entity.
     *
     * @Route("/actualizar/{id}", name="categoriaobra_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Obras\CategoriaObra:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\CategoriaObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\CategoriaObra.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('categoriaobra'));
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
            'page_title' => 'Editar categor&iacute;a de obra'
        );
    }

    /**
     * Deletes a Obras\CategoriaObra entity.
     *
     * @Route("/borrar/{id}", name="categoriaobra_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Obras\CategoriaObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Obras\CategoriaObra.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('categoriaobra'));
    }

}
