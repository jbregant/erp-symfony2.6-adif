<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Convenio;
use ADIF\RecursosHumanosBundle\Form\ConvenioType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Convenio controller.
 *
 * @Route("/convenios")
 * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
 */
class ConvenioController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Convenios' => $this->generateUrl('convenios')
        );
    }

    /**
     * Lists all Convenio entities.
     *
     * @Route("/", name="convenios")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Convenio')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Convenios'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Convenios',
            'page_info' => 'Lista de convenios'
        );
    }

    /**
     * Creates a new Convenio entity.
     *
     * @Route("/insertar", name="convenios_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Convenio:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Convenio();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('convenios'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear convenio',
        );
    }

    /**
     * Creates a form to create a Convenio entity.
     *
     * @param Convenio $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Convenio $entity) {
        $form = $this->createForm(new ConvenioType(), $entity, array(
            'action' => $this->generateUrl('convenios_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Convenio entity.
     *
     * @Route("/crear", name="convenios_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Convenio();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear convenio'
        );
    }

    /**
     * Finds and displays a Convenio entity.
     *
     * @Route("/{id}", name="convenios_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Convenio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Convenio.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Convenio'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver convenio'
        );
    }

    /**
     * Displays a form to edit an existing Convenio entity.
     *
     * @Route("/editar/{id}", name="convenios_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Convenio:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Convenio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Convenio.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar convenio'
        );
    }

    /**
     * Creates a form to edit a Convenio entity.
     *
     * @param Convenio $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Convenio $entity) {
        $form = $this->createForm(new ConvenioType(), $entity, array(
            'action' => $this->generateUrl('convenios_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Convenio entity.
     *
     * @Route("/actualizar/{id}", name="convenios_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Convenio:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Convenio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Convenio.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('convenios'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar convenio'
        );
    }

    /**
     * Deletes a Convenio entity.
     *
     * @Route("/borrar/{id}", name="convenios_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Convenio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Convenio.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('convenios'));
    }
    
    /**
     * @Route("/lista_convenios", name="lista_convenios")
     * @Security("has_role('ROLE_USER')")
     */
    public function listaConveniosAction() {
        $repository = $this->getDoctrine()->getRepository('ADIFRecursosHumanosBundle:Convenio', $this->getEntityManager());

        $query = $repository->createQueryBuilder('c')
                ->select('c.id', 'c.nombre')
                ->orderBy('c.nombre', 'ASC')
                ->getQuery();

        return new \Symfony\Component\HttpFoundation\JsonResponse($query->getResult());
    }

}
