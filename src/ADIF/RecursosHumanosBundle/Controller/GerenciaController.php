<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Gerencia;
use ADIF\RecursosHumanosBundle\Form\GerenciaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Gerencia controller.
 *
 * @Route("/gerencias")
 * @Security("has_role('ROLE_RRHH_CONFIGURACION') or has_role('ROLE_MENU_ADMINISTRACION_FONDOS_EGRESO_VALOR')")
 */
class GerenciaController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Gerencias' => $this->generateUrl('gerencias')
        );
    }

    /**
     * Lists all Gerencia entities.
     *
     * @Route("/", name="gerencias")
     * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Gerencia')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Gerencias'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Gerencias',
            'page_info' => 'Lista de gerencias'
        );
    }

    /**
     * Creates a new Gerencia entity.
     *
     * @Route("/insertar", name="gerencias_create")
     * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Gerencia:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Gerencia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gerencias'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear gerencia',
        );
    }

    /**
     * Creates a form to create a Gerencia entity.
     *
     * @param Gerencia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Gerencia $entity) {
        $form = $this->createForm(new GerenciaType(), $entity, array(
            'action' => $this->generateUrl('gerencias_create'),
            'method' => 'POST',
            'entity_manager_rrhh' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_conta' => $this->getDoctrine()->getManager(EntityManagers::getEmContable())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Gerencia entity.
     *
     * @Route("/crear", name="gerencias_new")
     * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Gerencia();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear gerencia'
        );
    }

    /**
     * Finds and displays a Gerencia entity.
     *
     * @Route("/{id}", name="gerencias_show")
     * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Gerencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Gerencia.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Gerencia'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver gerencia'
        );
    }

    /**
     * Displays a form to edit an existing Gerencia entity.
     *
     * @Route("/editar/{id}", name="gerencias_edit")
     * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Gerencia:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Gerencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Gerencia.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar gerencia'
        );
    }

    /**
     * Creates a form to edit a Gerencia entity.
     *
     * @param Gerencia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Gerencia $entity) {
        $form = $this->createForm(new GerenciaType(), $entity, array(
            'action' => $this->generateUrl('gerencias_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_rrhh' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_conta' => $this->getDoctrine()->getManager(EntityManagers::getEmContable())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Gerencia entity.
     *
     * @Route("/actualizar/{id}", name="gerencias_update")
     * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Gerencia:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Gerencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Gerencia.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gerencias'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar gerencia'
        );
    }

    /**
     * Deletes a Gerencia entity.
     *
     * @Route("/borrar/{id}", name="gerencias_delete")
     * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }
    
    
    /**
     * @Route("/nombres", name="gerencias_nombres")
     * @Security("has_role('ROLE_RRHH_CONFIGURACION') or has_role('ROLE_MENU_ADMINISTRACION_FONDOS_EGRESO_VALOR')")
     */
    public function listaNombresAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFRecursosHumanosBundle:Gerencia', $this->getEntityManager());

        $query = $repository->createQueryBuilder('e')
                ->select('e.id', 'e.nombre')
                ->orderBy('e.nombre', 'ASC')
                ->getQuery()
                ->useResultCache(true, 36000, 'gerencias_nombres')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return new JsonResponse($query->getResult());
    }

}
