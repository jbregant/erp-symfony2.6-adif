<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\TituloUniversitario;
use ADIF\RecursosHumanosBundle\Form\TituloUniversitarioType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * TituloUniversitario controller.
 *
 * @Route("/titulos_universitarios")
 */
class TituloUniversitarioController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'T&iacute;tulos universitarios' => $this->generateUrl('titulos_universitarios')
        );
    }

    /**
     * Lists all TituloUniversitario entities.
     *
     * @Route("/", name="titulos_universitarios")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:TituloUniversitario')->findAll();

        $bread = $this->base_breadcrumbs;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'T&iacute;tulos universitarios',
            'page_info' => 'Lista de t&iacute;tulos universitarios'
        );
    }

    /**
     * Creates a new TituloUniversitario entity.
     *
     * @Route("/insertar", name="titulos_universitarios_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:TituloUniversitario:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new TituloUniversitario();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            // Es un popup
            if (!empty($request->request->get('adif_recursoshumanosbundle_titulouniversitario')['submit']) && $request->request->get('adif_recursoshumanosbundle_titulouniversitario')['submit'] == 'popup') {

                return $this->render('::base_iframe.html.twig', array(
                            'response' => 'OK',
                            'response_id' => $entity->getId())
                );
            }
            // No es popup, es el guardar común, redirijo al index
            else {
                return $this->redirect($this->generateUrl('titulos_universitarios'));
            }
        } else {
            $request->attributes->set('form-error', true);

            // Es un popup
            if (!empty($request->request->get('adif_recursoshumanosbundle_titulouniversitario')['submit']) && $request->request->get('adif_recursoshumanosbundle_titulouniversitario')['submit'] == 'popup') {

                $request->attributes->set('popup', true);
            }
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear t&iacute;tulo universitario',
        );
    }

    /**
     * Creates a form to create a TituloUniversitario entity.
     *
     * @param TituloUniversitario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TituloUniversitario $entity) {
        $form = $this->createForm(new TituloUniversitarioType(), $entity, array(
            'action' => $this->generateUrl('titulos_universitarios_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TituloUniversitario entity.
     *
     * @Route("/crear", name="titulos_universitarios_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new TituloUniversitario();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear t&iacute;tulo universitario'
        );
    }

    /**
     * Finds and displays a TituloUniversitario entity.
     *
     * @Route("/{id}", name="titulos_universitarios_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TituloUniversitario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Titulo Universitario.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['T&iacute;tulo universitario'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver t&iacute;tulo universitario'
        );
    }

    /**
     * Displays a form to edit an existing TituloUniversitario entity.
     *
     * @Route("/editar/{id}", name="titulos_universitarios_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:TituloUniversitario:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TituloUniversitario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Titulo Universitario.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar t&iacute;tulo universitario'
        );
    }

    /**
     * Creates a form to edit a TituloUniversitario entity.
     *
     * @param TituloUniversitario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TituloUniversitario $entity) {
        $form = $this->createForm(new TituloUniversitarioType(), $entity, array(
            'action' => $this->generateUrl('titulos_universitarios_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing TituloUniversitario entity.
     *
     * @Route("/actualizar/{id}", name="titulos_universitarios_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:TituloUniversitario:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TituloUniversitario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Titulo Universitario.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('titulos_universitarios'));
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
            'page_title' => 'Editar t&iacute;tulo universitario'
        );
    }

    /**
     * Deletes a TituloUniversitario entity.
     *
     * @Route("/borrar/{id}", name="titulos_universitarios_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {
        return parent::baseDeleteAction($id);
    }

    /**
     * @Route("/lista_titulos", name="lista_titulos")
     * @Security("has_role('ROLE_USER')")
     */
    public function listaTitulosAction() {
        $repository = $this->getDoctrine()->getRepository('ADIFRecursosHumanosBundle:TituloUniversitario', $this->getEntityManager());

        $query = $repository->createQueryBuilder('n')
                ->select('n.id', 'n.nombre')
                ->orderBy('n.nombre', 'ASC')
                ->getQuery();

        return new JsonResponse($query->getResult());
    }

}
