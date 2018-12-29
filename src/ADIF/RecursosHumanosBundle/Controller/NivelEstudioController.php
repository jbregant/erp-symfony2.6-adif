<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\NivelEstudio;
use ADIF\RecursosHumanosBundle\Form\NivelEstudioType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * NivelEstudio controller.
 *
 * @Route("/nivelesestudio")
 * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
 */
class NivelEstudioController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'NivelEstudio' => $this->generateUrl('nivelestudio')
        );
    }

    /**
     * Lists all NivelEstudio entities.
     *
     * @Route("/", name="nivelestudio")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:NivelEstudio')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['NivelEstudio'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'NivelEstudio',
            'page_info' => 'Lista de nivelestudio'
        );
    }

    /**
     * Creates a new NivelEstudio entity.
     *
     * @Route("/insertar", name="nivelestudio_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:NivelEstudio:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new NivelEstudio();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('nivelestudio'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear NivelEstudio',
        );
    }

    /**
     * Creates a form to create a NivelEstudio entity.
     *
     * @param NivelEstudio $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(NivelEstudio $entity) {
        $form = $this->createForm(new NivelEstudioType(), $entity, array(
            'action' => $this->generateUrl('nivelestudio_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new NivelEstudio entity.
     *
     * @Route("/crear", name="nivelestudio_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new NivelEstudio();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear NivelEstudio'
        );
    }

    /**
     * Finds and displays a NivelEstudio entity.
     *
     * @Route("/{id}", name="nivelestudio_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:NivelEstudio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad NivelEstudio.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['NivelEstudio'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver NivelEstudio'
        );
    }

    /**
     * Displays a form to edit an existing NivelEstudio entity.
     *
     * @Route("/editar/{id}", name="nivelestudio_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:NivelEstudio:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:NivelEstudio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad NivelEstudio.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar NivelEstudio'
        );
    }

    /**
     * Creates a form to edit a NivelEstudio entity.
     *
     * @param NivelEstudio $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(NivelEstudio $entity) {
        $form = $this->createForm(new NivelEstudioType(), $entity, array(
            'action' => $this->generateUrl('nivelestudio_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing NivelEstudio entity.
     *
     * @Route("/actualizar/{id}", name="nivelestudio_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:NivelEstudio:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:NivelEstudio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad NivelEstudio.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('nivelestudio'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar NivelEstudio'
        );
    }

    /**
     * Deletes a NivelEstudio entity.
     *
     * @Route("/borrar/{id}", name="nivelestudio_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:NivelEstudio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad NivelEstudio.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('nivelestudio'));
    }

    /**
     * @Route("/lista_niveles", name="lista_niveles")
     * @Security("has_role('ROLE_USER')")
     */
    public function listaNivelesAction() {
        $repository = $this->getDoctrine()->getRepository('ADIFRecursosHumanosBundle:NivelEstudio', $this->getEntityManager());

        $query = $repository->createQueryBuilder('n')
                ->select('n.id', 'n.nombre')
                ->orderBy('n.nombre', 'ASC')
                ->getQuery();

        return new JsonResponse($query->getResult());
    }

}
