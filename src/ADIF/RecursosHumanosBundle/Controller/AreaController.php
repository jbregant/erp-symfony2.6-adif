<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\BaseBundle\Entity\EntityManagers;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Area;
use ADIF\RecursosHumanosBundle\Form\AreaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * Area controller.
 *
 * @Route("/areas")
 * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
 */
class AreaController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            '&Aacute;reas' => $this->generateUrl('areas')
        );
    }

    /**
     * Lists all Area entities.
     *
     * @Route("/", name="areas")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Area')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['&Aacute;reas'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => '&Aacute;reas',
            'page_info' => 'Lista de &aacute;reas'
        );
    }

    /**
     * Creates a new Area entity.
     *
     * @Route("/insertar", name="areas_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Area:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Area();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('areas'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear &aacute;rea',
        );
    }

    /**
     * Creates a form to create a Area entity.
     *
     * @param Area $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Area $entity) {
        $form = $this->createForm(new AreaType(), $entity, array(
            'action' => $this->generateUrl('areas_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
            'entity_manager_RRHH' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),

        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Area entity.
     *
     * @Route("/crear", name="areas_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Area();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear &aacute;rea'
        );
    }

    /**
     * Finds and displays a Area entity.
     *
     * @Route("/{id}", name="areas_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Area')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Area.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Area'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver &aacute;rea'
        );
    }

    /**
     * Displays a form to edit an existing Area entity.
     *
     * @Route("/editar/{id}", name="areas_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Area:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Area')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Area.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar &aacute;rea'
        );
    }

    /**
     * Creates a form to edit a Area entity.
     *
     * @param Area $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Area $entity) {
        $form = $this->createForm(new AreaType(), $entity, array(
            'action' => $this->generateUrl('areas_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
            'entity_manager_RRHH' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),

        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Area entity.
     *
     * @Route("/actualizar/{id}", name="areas_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Area:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Area')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Area.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('areas'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar &aacute;rea'
        );
    }

    /**
     * Deletes a Area entity.
     *
     * @Route("/borrar/{id}", name="areas_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

    /**
     * 
     * @param type $id
     * @return boolean
     */
    public function validateLocalDeleteById($id) {

        $emAutenticacion = $this->getDoctrine()->getManager(EntityManagers::getEmAutenticacion());

        $qbUsuario = $emAutenticacion
                ->getRepository('ADIFAutenticacionBundle:Usuario')
                ->createQueryBuilder('u')
                ->select('count(u.id)')
                ->where('u.idArea = :id')
                ->setParameter('id', $id);

        $countUsuarios = $qbUsuario->getQuery()->getSingleScalarResult();

        return $countUsuarios == 0;
    }

    /**
     * 
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el &aacute;rea '
                . 'ya que es referenciado por otras entidades.';
    }

}
