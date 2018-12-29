<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\TipoRelacion;
use ADIF\RecursosHumanosBundle\Form\TipoRelacionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * TipoRelacion controller.
 *
 * @Route("/tipos_relacion")
 * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
 */
class TipoRelacionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'TipoRelacion' => $this->generateUrl('tipos_relacion')
        );
    }

    /**
     * Lists all TipoRelacion entities.
     *
     * @Route("/", name="tipos_relacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:TipoRelacion')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['TipoRelacion'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'TipoRelacion',
            'page_info' => 'Lista de tiporelacion'
        );
    }

    /**
     * Creates a new TipoRelacion entity.
     *
     * @Route("/insertar", name="tipos_relacion_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:TipoRelacion:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new TipoRelacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipos_relacion'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear TipoRelacion',
        );
    }

    /**
     * Creates a form to create a TipoRelacion entity.
     *
     * @param TipoRelacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoRelacion $entity) {
        $form = $this->createForm(new TipoRelacionType(), $entity, array(
            'action' => $this->generateUrl('tipos_relacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoRelacion entity.
     *
     * @Route("/crear", name="tipos_relacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new TipoRelacion();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear TipoRelacion'
        );
    }

    /**
     * Finds and displays a TipoRelacion entity.
     *
     * @Route("/{id}", name="tipos_relacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TipoRelacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoRelacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['TipoRelacion'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver TipoRelacion'
        );
    }

    /**
     * Displays a form to edit an existing TipoRelacion entity.
     *
     * @Route("/editar/{id}", name="tipos_relacion_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:TipoRelacion:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TipoRelacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoRelacion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar TipoRelacion'
        );
    }

    /**
     * Creates a form to edit a TipoRelacion entity.
     *
     * @param TipoRelacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TipoRelacion $entity) {
        $form = $this->createForm(new TipoRelacionType(), $entity, array(
            'action' => $this->generateUrl('tipos_relacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing TipoRelacion entity.
     *
     * @Route("/actualizar/{id}", name="tipos_relacion_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:TipoRelacion:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TipoRelacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoRelacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipos_relacion'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar TipoRelacion'
        );
    }

    /**
     * Deletes a TipoRelacion entity.
     *
     * @Route("/borrar/{id}", name="tipos_relacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TipoRelacion', $this->getEntityManager())->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoRelacion.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('tipos_relacion'));
    }

    /**
     * @Route("/lista_tipos", name="lista_tipos")
     * @Security("has_role('ROLE_USER')")
     */
    public function listaTiposRelacionAction() {
        $repository = $this->getDoctrine()->getRepository('ADIFRecursosHumanosBundle:TipoRelacion', $this->getEntityManager());

        $query = $repository->createQueryBuilder('t')
                ->select('t.id', 't.nombre')
                ->orderBy('t.nombre', 'ASC')
                ->getQuery();

        return new JsonResponse($query->getResult());
    }

}
