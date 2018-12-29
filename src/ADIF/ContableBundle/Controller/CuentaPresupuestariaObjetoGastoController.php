<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto;
use ADIF\ContableBundle\Form\CuentaPresupuestariaObjetoGastoType;

/**
 * CuentaPresupuestariaObjetoGasto controller.
 *
 * @Route("/cuentapresupuestariaobjetogasto")
 */
class CuentaPresupuestariaObjetoGastoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Cuentas presupuestarias de objeto de gasto' => $this->generateUrl('cuentapresupuestariaobjetogasto')
        );
    }

    /**
     * Lists all CuentaPresupuestariaObjetoGasto entities.
     *
     * @Route("/", name="cuentapresupuestariaobjetogasto")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Cuentas presupuestarias de objeto de gasto'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Cuentas presupuestarias de objeto de gasto',
            'page_info' => 'Lista de cuentas presupuestarias de objeto de gasto'
        );
    }

    /**
     * Tabla para CuentaPresupuestariaObjetoGasto.
     *
     * @Route("/index_table/", name="cuentapresupuestariaobjetogasto_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaObjetoGasto')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Cuentas presupuestarias de objeto de gasto'] = null;

        return $this->render('ADIFContableBundle:CuentaPresupuestariaObjetoGasto:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new CuentaPresupuestariaObjetoGasto entity.
     *
     * @Route("/insertar", name="cuentapresupuestariaobjetogasto_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:CuentaPresupuestariaObjetoGasto:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new CuentaPresupuestariaObjetoGasto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cuentapresupuestariaobjetogasto'));
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
            'page_title' => 'Crear cuenta presupuestaria de objeto de gasto',
        );
    }

    /**
     * Creates a form to create a CuentaPresupuestariaObjetoGasto entity.
     *
     * @param CuentaPresupuestariaObjetoGasto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CuentaPresupuestariaObjetoGasto $entity) {
        $form = $this->createForm(new CuentaPresupuestariaObjetoGastoType(), $entity, array(
            'action' => $this->generateUrl('cuentapresupuestariaobjetogasto_create'),
            'method' => 'POST',
            'entity_manager_contable' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new CuentaPresupuestariaObjetoGasto entity.
     *
     * @Route("/crear", name="cuentapresupuestariaobjetogasto_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new CuentaPresupuestariaObjetoGasto();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear cuenta presupuestaria de objeto de gasto'
        );
    }

    /**
     * Finds and displays a CuentaPresupuestariaObjetoGasto entity.
     *
     * @Route("/{id}", name="cuentapresupuestariaobjetogasto_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaObjetoGasto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaPresupuestariaObjetoGasto.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['CuentaPresupuestariaObjetoGasto'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver cuenta presupuestaria de objeto de gasto'
        );
    }

    /**
     * Displays a form to edit an existing CuentaPresupuestariaObjetoGasto entity.
     *
     * @Route("/editar/{id}", name="cuentapresupuestariaobjetogasto_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:CuentaPresupuestariaObjetoGasto:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaObjetoGasto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaPresupuestariaObjetoGasto.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar cuenta presupuestaria de objeto de gasto'
        );
    }

    /**
     * Creates a form to edit a CuentaPresupuestariaObjetoGasto entity.
     *
     * @param CuentaPresupuestariaObjetoGasto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CuentaPresupuestariaObjetoGasto $entity) {
        $form = $this->createForm(new CuentaPresupuestariaObjetoGastoType(), $entity, array(
            'action' => $this->generateUrl('cuentapresupuestariaobjetogasto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_contable' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing CuentaPresupuestariaObjetoGasto entity.
     *
     * @Route("/actualizar/{id}", name="cuentapresupuestariaobjetogasto_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:CuentaPresupuestariaObjetoGasto:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaObjetoGasto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaPresupuestariaObjetoGasto.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('cuentapresupuestariaobjetogasto'));
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
            'page_title' => 'Editar cuenta presupuestaria de objeto de gasto'
        );
    }

    /**
     * Deletes a CuentaPresupuestariaObjetoGasto entity.
     *
     * @Route("/borrar/{id}", name="cuentapresupuestariaobjetogasto_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaObjetoGasto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaPresupuestariaObjetoGasto.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('cuentapresupuestariaobjetogasto'));
    }

    /**
     * @Route("/lista_cuentas", name="lista_cuentas")
     */
    public function getCuentasPresupuestariasObjetoGastoByCuentaPresupuestariaEconomicaAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idCuentaPresupuestariaEconomica = $request->request->get('id_cuenta_presupuestaria_economica');

        $cuentaPresupuestariaEconomica = $em->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')
                ->find($idCuentaPresupuestariaEconomica);

        if (!$cuentaPresupuestariaEconomica) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaPresupuestariaEconomica.');
        }

        $repository = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:CuentaPresupuestariaObjetoGasto', $this->getEntityManager());

        $query = $repository->createQueryBuilder('c')
                ->select('c.id', 'c.codigo', 'c.denominacion')
                ->where('c.cuentaPresupuestariaEconomica =  :cuentaPresupuestariaEconomica')
                ->setParameter('cuentaPresupuestariaEconomica', $cuentaPresupuestariaEconomica)
                ->orderBy('c.codigo', 'ASC')
                ->getQuery();

        return new JsonResponse($query->getResult());
    }

}
