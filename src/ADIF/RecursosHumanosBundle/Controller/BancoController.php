<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\Banco;
use ADIF\RecursosHumanosBundle\Form\BancoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Banco controller.
 *
 * @Route("/bancos")
 * @Security("has_role('ROLE_USER')")
 */
class BancoController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Bancos' => $this->generateUrl('bancos')
        );
    }

    /**
     * Lists all Banco entities.
     *
     * @Route("/", name="bancos")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Banco')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Bancos'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Bancos',
            'page_info' => 'Lista de bancos'
        );
    }

    /**
     * Creates a new Banco entity.
     *
     * @Route("/insertar", name="bancos_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Banco:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Banco();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('bancos'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear banco',
        );
    }

    /**
     * Creates a form to create a Banco entity.
     *
     * @param Banco $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Banco $entity) {
        $form = $this->createForm(new BancoType(), $entity, array(
            'action' => $this->generateUrl('bancos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Banco entity.
     *
     * @Route("/crear", name="bancos_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Banco();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear banco'
        );
    }

    /**
     * Finds and displays a Banco entity.
     *
     * @Route("/{id}", name="bancos_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Banco')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Banco.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Banco'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver banco'
        );
    }

    /**
     * Displays a form to edit an existing Banco entity.
     *
     * @Route("/editar/{id}", name="bancos_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Banco:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Banco')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Banco.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar banco'
        );
    }

    /**
     * Creates a form to edit a Banco entity.
     *
     * @param Banco $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Banco $entity) {
        $form = $this->createForm(new BancoType(), $entity, array(
            'action' => $this->generateUrl('bancos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Banco entity.
     *
     * @Route("/actualizar/{id}", name="bancos_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Banco:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Banco')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Banco.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('bancos'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar banco'
        );
    }

    /**
     * Deletes a Banco entity.
     *
     * @Route("/borrar/{id}", name="bancos_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

    /**
     * @Route("/lista_bancos", name="lista_bancos")
     * @Security("has_role('ROLE_USER')")
     */
    public function listaBancosAction() {
        $repository = $this->getDoctrine()->getRepository('ADIFRecursosHumanosBundle:Banco', $this->getEntityManager());

        $query = $repository->createQueryBuilder('b')
                ->select('b.id', 'b.nombre')
                ->orderBy('b.nombre', 'ASC')
                ->getQuery();

        return new \Symfony\Component\HttpFoundation\JsonResponse($query->getResult());
    }

    /**
     * 
     * @param type $id
     * @return boolean
     */
    public function validateLocalDeleteById($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $qbLiquidacion = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
                ->createQueryBuilder('l')
                ->select('count(l.id)')
                ->where('l.bancoAporte = :id')
                ->setParameter('id', $id);

        $countLiquidacion = $qbLiquidacion->getQuery()->getSingleScalarResult();

        $qbLiquidacionEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
                ->createQueryBuilder('l')
                ->select('count(l.id)')
                ->where('l.banco = :id')
                ->setParameter('id', $id);

        $countLiquidacionEmpleado = $qbLiquidacionEmpleado->getQuery()->getSingleScalarResult();

        return ($countLiquidacion + $countLiquidacionEmpleado) == 0;
    }

}
