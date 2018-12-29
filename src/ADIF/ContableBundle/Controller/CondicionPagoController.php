<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\CondicionPago;
use ADIF\ContableBundle\Form\CondicionPagoType;
use ADIF\BaseBundle\Entity\EntityManagers;

/**
 * CondicionPago controller.
 *
 * @Route("/condicionpago")
 */
class CondicionPagoController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Condiciones de pago' => $this->generateUrl('condicionpago')
        );
    }

    /**
     * Lists all CondicionPago entities.
     *
     * @Route("/", name="condicionpago")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:CondicionPago')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Condiciones de pago'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Condiciones de pago',
            'page_info' => 'Lista de condiciones de pago'
        );
    }

    /**
     * Creates a new CondicionPago entity.
     *
     * @Route("/insertar", name="condicionpago_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:CondicionPago:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new CondicionPago();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('condicionpago'));
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
            'page_title' => 'Crear condici&oacute;n de pago',
        );
    }

    /**
     * Creates a form to create a CondicionPago entity.
     *
     * @param CondicionPago $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CondicionPago $entity) {
        $form = $this->createForm(new CondicionPagoType(), $entity, array(
            'action' => $this->generateUrl('condicionpago_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new CondicionPago entity.
     *
     * @Route("/crear", name="condicionpago_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new CondicionPago();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear condici&oacute;n de pago'
        );
    }

    /**
     * Finds and displays a CondicionPago entity.
     *
     * @Route("/{id}", name="condicionpago_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CondicionPago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CondicionPago.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['CondicionPago'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver condici&oacute;n de pago'
        );
    }

    /**
     * Displays a form to edit an existing CondicionPago entity.
     *
     * @Route("/editar/{id}", name="condicionpago_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:CondicionPago:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CondicionPago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CondicionPago.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar condici&oacute;n de pago'
        );
    }

    /**
     * Creates a form to edit a CondicionPago entity.
     *
     * @param CondicionPago $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CondicionPago $entity) {
        $form = $this->createForm(new CondicionPagoType(), $entity, array(
            'action' => $this->generateUrl('condicionpago_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing CondicionPago entity.
     *
     * @Route("/actualizar/{id}", name="condicionpago_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:CondicionPago:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CondicionPago')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CondicionPago.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('condicionpago'));
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
            'page_title' => 'Editar condici&oacute;n de pago'
        );
    }

    /**
     * Deletes a CondicionPago entity.
     *
     * @Route("/borrar/{id}", name="condicionpago_delete")
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

        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $qbOrdenCompra = $emCompras
                ->getRepository('ADIFComprasBundle:OrdenCompra')
                ->createQueryBuilder('oc')
                ->select('count(oc.id)')
                ->where('oc.idCondicionPago = :id')
                ->setParameter('id', $id);

        $countOrdenesCompra = $qbOrdenCompra->getQuery()->getSingleScalarResult();

        return $countOrdenesCompra == 0;
    }

    /**
     * 
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar la condici&oacute;n de pago '
                . 'ya que es referenciada por otras entidades.';
    }

}
