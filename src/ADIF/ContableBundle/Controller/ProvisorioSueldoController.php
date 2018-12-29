<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\ProvisorioSueldo;
use ADIF\ContableBundle\Form\ProvisorioSueldoType;

/**
 * ProvisorioSueldo controller.
 *
 * @Route("/provisoriosueldo")
 */
class ProvisorioSueldoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Provisorio de sueldos' => $this->generateUrl('provisoriosueldo')
        );
    }

    /**
     * Lists all ProvisorioSueldo entities.
     *
     * @Route("/", name="provisoriosueldo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:ProvisorioSueldo')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Provisorio de sueldos'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Provisorio de sueldos',
            'page_info' => 'Lista de provisorio de sueldos'
        );
    }

    /**
     * Creates a new ProvisorioSueldo entity.
     *
     * @Route("/insertar", name="provisoriosueldo_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ProvisorioSueldo:new.html.twig")
     */
    public function createAction(Request $request) {

        $provisorio = new ProvisorioSueldo();

        $form = $this->createCreateForm($provisorio);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $erroresCuentaPresupuestariaEconomicaSinSaldoArray = array();

            /* @var CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->get('adif.contabilidad_presupuestaria_service')
                    ->getCuentaPresupuestaria(
                    $provisorio->getFechaProvisorio(), //
                    $provisorio->getCuentaPresupuestariaEconomica()
            );

            $provisorio->setCuentaPresupuestaria($cuentaPresupuestaria);

            if (!$cuentaPresupuestaria->getTieneSaldo()) {

                $erroresCuentaPresupuestariaEconomicaSinSaldoArray[$cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getId()] = 'La cuenta econ&oacute;mica '
                        . $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()
                        . ' no presenta saldo para el ejercicio '
                        . $cuentaPresupuestaria->getEjercicioContable()
                        . '.';

                // Si existen CuentasPresupuestariasEconomicas SIN saldo
                if (!empty($erroresCuentaPresupuestariaEconomicaSinSaldoArray)) {

                    $this->get('adif.contabilidad_presupuestaria_service')
                            ->mostrarMensajeErrorCuentaPresupuestariaEconomicaSinSaldo($erroresCuentaPresupuestariaEconomicaSinSaldoArray);
                }
            }

            // Creo el historico relacionado
            $this->crearProvisorioSueldoHistorico($provisorio);

            // Persisto la entidad
            $em->persist($provisorio);

            $em->flush();

            return $this->redirect($this->generateUrl('provisoriosueldo'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $provisorio,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear provisorio de sueldos',
        );
    }

    /**
     * Creates a form to create a ProvisorioSueldo entity.
     *
     * @param ProvisorioSueldo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ProvisorioSueldo $entity) {
        $form = $this->createForm(new ProvisorioSueldoType(), $entity, array(
            'action' => $this->generateUrl('provisoriosueldo_create'),
            'method' => 'POST',
            'entity_manager_contable' => $this->getDoctrine()->getManager($this->getEntityManager())

        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ProvisorioSueldo entity.
     *
     * @Route("/crear", name="provisoriosueldo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new ProvisorioSueldo();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear provisorio de sueldos'
        );
    }

    /**
     * Finds and displays a ProvisorioSueldo entity.
     *
     * @Route("/{id}", name="provisoriosueldo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ProvisorioSueldo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ProvisorioSueldo.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Detalle'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver provisorio de sueldos'
        );
    }

    /**
     * Displays a form to edit an existing ProvisorioSueldo entity.
     *
     * @Route("/editar/{id}", name="provisoriosueldo_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ProvisorioSueldo:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ProvisorioSueldo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ProvisorioSueldo.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar provisorio de sueldos'
        );
    }

    /**
     * Creates a form to edit a ProvisorioSueldo entity.
     *
     * @param ProvisorioSueldo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ProvisorioSueldo $entity) {
        $form = $this->createForm(new ProvisorioSueldoType(), $entity, array(
            'action' => $this->generateUrl('provisoriosueldo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_contable' => $this->getDoctrine()->getManager($this->getEntityManager())

        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ProvisorioSueldo entity.
     *
     * @Route("/actualizar/{id}", name="provisoriosueldo_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ProvisorioSueldo:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $provisorio = $em->getRepository('ADIFContableBundle:ProvisorioSueldo')
                ->find($id);

        if (!$provisorio) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ProvisorioSueldo.');
        }

        $editForm = $this->createEditForm($provisorio);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // Creo el historico relacionado
            $this->crearProvisorioSueldoHistorico($provisorio);

            $em->flush();

            return $this->redirect($this->generateUrl('provisoriosueldo'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $provisorio,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar provisorio de sueldos'
        );
    }

    /**
     * Deletes a ProvisorioSueldo entity.
     *
     * @Route("/borrar/{id}", name="provisoriosueldo_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:ProvisorioSueldo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ProvisorioSueldo.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('provisoriosueldo'));
    }

    /**
     * 
     * @param ProvisorioSueldo $provisorio
     */
    private function crearProvisorioSueldoHistorico(ProvisorioSueldo $provisorio) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $historico = new \ADIF\ContableBundle\Entity\ProvisorioSueldoHistorico();

        $historico->setProvisorio($provisorio);

        // Seteo el Usuario logueado
        $historico->setUsuario($this->getUser());

        $historico->setMonto($provisorio->getMonto());
        $historico->setDetalle($provisorio->getDetalle());

        $em->persist($historico);
    }

    /**
     *
     * @Route("/{id}/historico", name="provisoriosueldo_historico")
     * @Method("GET")
     * @Template("ADIFContableBundle:ProvisorioSueldo:historico.html.twig")
     */
    public function showHistoricoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $provisorio = $em->getRepository('ADIFContableBundle:ProvisorioSueldo')
                ->find($id);

        if (!$provisorio) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ProvisorioSueldo.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Hist&oacute;rico'] = null;

        return array(
            'entity' => $provisorio,
            'historicos' => $provisorio->getHistoricos(),
            'breadcrumbs' => $bread,
            'page_title' => 'Hist&oacute;rico de provisorio de sueldos'
        );
    }

}
