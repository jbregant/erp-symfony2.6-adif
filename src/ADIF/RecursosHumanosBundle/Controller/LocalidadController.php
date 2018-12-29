<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Localidad;
use ADIF\RecursosHumanosBundle\Form\LocalidadType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Localidad controller.
 *
 * @Route("/localidades")
 * @Security("has_role('ROLE_LOCALIDADES')")
 */
class LocalidadController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Localidad' => $this->generateUrl('localidades')
        );
    }

    /**
     * Lists all Localidad entities.
     *
     * @Route("/", name="localidades")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        
        $bread = $this->base_breadcrumbs;
        $bread['Localidades'] = null;

        return array(
            //'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Localidades',
            'page_info' => 'Lista de localidades'
        );
    }

    /**
     * Tabla para Localidades
     *
     * @Route("/index_table/", name="localidades_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Localidad')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Localidades'] = null;

        return $this->render('ADIFRecursosHumanosBundle:Localidad:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }    

    /**
     * Creates a new Localidad entity.
     *
     * @Route("/insertar", name="localidades_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Localidad:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Localidad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('localidades'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Localidad',
        );
    }

    /**
     * Creates a form to create a Localidad entity.
     *
     * @param Localidad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Localidad $entity) {
        $form = $this->createForm(new LocalidadType(), $entity, array(
            'action' => $this->generateUrl('localidades_create'),
            'method' => 'POST',
            //'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Localidad entity.
     *
     * @Route("/crear", name="localidades_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Localidad();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Localidad'
        );
    }

    /**
     * Finds and displays a Localidad entity.
     *
     * @Route("/{id}", name="localidades_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Localidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Localidad.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Localidad'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Localidad'
        );
    }

    /**
     * Displays a form to edit an existing Localidad entity.
     *
     * @Route("/editar/{id}", name="localidades_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Localidad:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Localidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Localidad.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Localidad'
        );
    }

    /**
     * Creates a form to edit a Localidad entity.
     *
     * @param Localidad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Localidad $entity) {
        $form = $this->createForm(new LocalidadType(), $entity, array(
            'action' => $this->generateUrl('localidades_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            //'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Localidad entity.
     *
     * @Route("/actualizar/{id}", name="localidades_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Localidad:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Localidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Localidad.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('localidades'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Localidad'
        );
    }

    /**
     * Deletes a Localidad entity.
     *
     * @Route("/borrar/{id}", name="localidades_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Localidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Localidad.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('localidades'));
    }

    /**
     * @Route("/lista_localidades", name="lista_localidades")
     * @Security("has_role('ROLE_USER')")
     */
    public function listaLocalidadesAction(Request $request) {
        $id_provincia = $request->request->get('id_provincia');
        
        $repository = $this->getDoctrine()->getRepository('ADIFRecursosHumanosBundle:Localidad', $this->getEntityManager());

        $query = $repository->createQueryBuilder('l')
                ->select('l.id', 'l.nombre')
                ->where('l.provincia =  :provincia')
                ->setParameter('provincia', $id_provincia)
                ->orderBy('l.nombre', 'ASC')
                ->getQuery();

        return new JsonResponse($query->getResult());
    }

}
