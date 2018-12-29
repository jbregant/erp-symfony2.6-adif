<?php

namespace ADIF\ContableBundle\Controller\Facturacion;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Facturacion\ClaseContrato;
use ADIF\ContableBundle\Form\Facturacion\ClaseContratoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\ContableBundle\Entity\Constantes\ConstanteClaseContrato;
use Doctrine\DBAL\Connection;

/**
 * Facturacion\ClaseContrato controller.
 *
 * @Route("/clasecontrato")
 */
class ClaseContratoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Clases de contrato' => $this->generateUrl('clasecontrato')
        );
    }

    /**
     * Lists all Facturacion\ClaseContrato entities.
     *
     * @Route("/", name="clasecontrato")
     * @Method("GET")
     * @Template()
	 * @Security("has_role('ROLE_VISUALIZAR_CLASE_CONTRATOS')")
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Clases de contrato'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Clase de contrato',
            'page_info' => 'Lista de clases de contrato'
        );
    }

    /**
     * Tabla para Facturacion\ClaseContrato .
     *
     * @Route("/index_table/", name="clasecontrato_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $clasesContratoOcultas = [
            ConstanteClaseContrato::VENTA_GENERAL
        ];

        $entities = $em->getRepository('ADIFContableBundle:Facturacion\ClaseContrato')
            ->createQueryBuilder('c')
            ->select('c')
            ->where('c.codigo NOT IN (:codigo)')
			->andWhere('c.activo = TRUE')
            ->setParameter('codigo', $clasesContratoOcultas, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();

        $bread = $this->base_breadcrumbs;
        $bread['Clase de contrato'] = null;

        return $this->render('ADIFContableBundle:Facturacion/ClaseContrato:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new Facturacion\ClaseContrato entity.
     *
     * @Route("/insertar", name="clasecontrato_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\ClaseContrato:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ClaseContrato();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('clasecontrato'));
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
            'page_title' => 'Crear clase de contrato',
        );
    }

    /**
     * Creates a form to create a Facturacion\ClaseContrato entity.
     *
     * @param ClaseContrato $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ClaseContrato $entity) {
        $form = $this->createForm(new ClaseContratoType(), $entity, array(
            'action' => $this->generateUrl('clasecontrato_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Facturacion\ClaseContrato entity.
     *
     * @Route("/crear", name="clasecontrato_new")
     * @Method("GET")
     * @Template()
	 * @Security("has_role('ROLE_CREAR_MODIFICAR_CLASE_CONTRATOS')")
     */
    public function newAction() {
        $entity = new ClaseContrato();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear clase de contrato'
        );
    }

    /**
     * Finds and displays a Facturacion\ClaseContrato entity.
     *
     * @Route("/{id}", name="clasecontrato_show")
     * @Method("GET")
     * @Template()
	 * @Security("has_role('ROLE_VISUALIZAR_CLASE_CONTRATOS')")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ClaseContrato')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\ClaseContrato.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Clase de contrato'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver clase de contrato'
        );
    }

    /**
     * Displays a form to edit an existing Facturacion\ClaseContrato entity.
     *
     * @Route("/editar/{id}", name="clasecontrato_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\ClaseContrato:new.html.twig")
	 * @Security("has_role('ROLE_CREAR_MODIFICAR_CLASE_CONTRATOS')")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ClaseContrato')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\ClaseContrato.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar clase de contrato'
        );
    }

    /**
     * Creates a form to edit a Facturacion\ClaseContrato entity.
     *
     * @param ClaseContrato $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ClaseContrato $entity) {
        $form = $this->createForm(new ClaseContratoType(), $entity, array(
            'action' => $this->generateUrl('clasecontrato_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Facturacion\ClaseContrato entity.
     *
     * @Route("/actualizar/{id}", name="clasecontrato_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Facturacion\ClaseContrato:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ClaseContrato')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\ClaseContrato.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('clasecontrato'));
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
            'page_title' => 'Editar clase de contrato'
        );
    }

    /**
     * Deletes a Facturacion\ClaseContrato entity.
     *
     * @Route("/borrar/{id}", name="clasecontrato_delete")
     * @Method("DELETE")
	 * @Security("has_role('ROLE_CREAR_MODIFICAR_CLASE_CONTRATOS')")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Facturacion\ClaseContrato')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\ClaseContrato.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('clasecontrato'));
    }

}
