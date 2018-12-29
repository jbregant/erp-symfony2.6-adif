<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\CentroCosto;
use ADIF\ContableBundle\Form\CentroCostoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * CentroCosto controller.
 *
 * @Route("/centrocosto")
 */
class CentroCostoController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Centros de costo' => $this->generateUrl('centrocosto')
        );
    }

    /**
     * Lists all CentroCosto entities.
     *
     * @Route("/", name="centrocosto")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Centros de costo'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Centros de costo',
            'page_info' => 'Lista de centros de costo'
        );
    }

    /**
     * Tabla para CentroCosto .
     *
     * @Route("/index_table/", name="centrocosto_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:CentroCosto')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Centros de costo'] = null;

        return $this->render('ADIFContableBundle:CentroCosto:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new CentroCosto entity.
     *
     * @Route("/insertar", name="centrocosto_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:CentroCosto:new.html.twig")
     * @Security("has_role('ROLE_MENU_CONTABILIDAD_ADMINISTRAR_CENTRO_COSTO')")
     */
    public function createAction(Request $request) {
        $entity = new CentroCosto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('centrocosto'));
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
            'page_title' => 'Crear centro de costo',
        );
    }

    /**
     * Creates a form to create a CentroCosto entity.
     *
     * @param CentroCosto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CentroCosto $entity) {
        $form = $this->createForm(new CentroCostoType(), $entity, array(
            'action' => $this->generateUrl('centrocosto_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new CentroCosto entity.
     *
     * @Route("/crear", name="centrocosto_new")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_MENU_CONTABILIDAD_ADMINISTRAR_CENTRO_COSTO')")
     */
    public function newAction() {
        $entity = new CentroCosto();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear centro de costo'
        );
    }

    /**
     * Finds and displays a CentroCosto entity.
     *
     * @Route("/{id}", name="centrocosto_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CentroCosto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CentroCosto.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['CentroCosto'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver centro de costo'
        );
    }

    /**
     * Displays a form to edit an existing CentroCosto entity.
     *
     * @Route("/editar/{id}", name="centrocosto_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:CentroCosto:new.html.twig")
     * @Security("has_role('ROLE_MENU_CONTABILIDAD_ADMINISTRAR_CENTRO_COSTO')")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CentroCosto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CentroCosto.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar centro de costo'
        );
    }

    /**
     * Creates a form to edit a CentroCosto entity.
     *
     * @param CentroCosto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CentroCosto $entity) {
        $form = $this->createForm(new CentroCostoType(), $entity, array(
            'action' => $this->generateUrl('centrocosto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing CentroCosto entity.
     *
     * @Route("/actualizar/{id}", name="centrocosto_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:CentroCosto:new.html.twig")
     * @Security("has_role('ROLE_MENU_CONTABILIDAD_ADMINISTRAR_CENTRO_COSTO')")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CentroCosto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CentroCosto.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('centrocosto'));
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
            'page_title' => 'Editar centro de costo'
        );
    }

    /**
     * Deletes a CentroCosto entity.
     *
     * @Route("/borrar/{id}", name="centrocosto_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_MENU_CONTABILIDAD_ADMINISTRAR_CENTRO_COSTO')")
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
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        // RenglonOrdenCompra
        $qbRenglonOrdenCompra = $emCompras
                ->getRepository('ADIFComprasBundle:RenglonOrdenCompra')
                ->createQueryBuilder('u')
                ->select('count(u.id)')
                ->where('u.idCentroCosto = :id')
                ->setParameter('id', $id);

        $countRenglonOrdenCompra = $qbRenglonOrdenCompra->getQuery()->getSingleScalarResult();


        // Area
        $qbArea = $emRRHH
                ->getRepository('ADIFRecursosHumanosBundle:Area')
                ->createQueryBuilder('a')
                ->select('count(a.id)')
                ->where('a.idCentroCosto = :id')
                ->setParameter('id', $id);

        $countArea = $qbArea->getQuery()->getSingleScalarResult();


        // Gerencia
        $qbGerencia = $emRRHH
                ->getRepository('ADIFRecursosHumanosBundle:Gerencia')
                ->createQueryBuilder('g')
                ->select('count(g.id)')
                ->where('g.idCentroCosto = :id')
                ->setParameter('id', $id);

        $countGerencia = $qbGerencia->getQuery()->getSingleScalarResult();

        return ($countRenglonOrdenCompra + $countArea + $countGerencia) == 0;
    }

    /**
     * 
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el centro de costo '
                . 'ya que es referenciado por otras entidades.';
    }

}
