<?php

namespace ADIF\ContableBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\OrdenPagoGeneral;
use ADIF\ContableBundle\Form\OrdenPagoGeneralType;

/**
 * OrdenPagoGeneral controller.
 *
 * @Route("/orden_pago_general")
 */
class OrdenPagoGeneralController extends OrdenPagoBaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Lists all OrdenPagoGeneral entities.
     *
     * @Route("/", name="orden_pago_general")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Egresos varios'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Egresos varios',
            'page_info' => 'Lista de egresos varios'
        );
    }

    /**
     * Tabla para OrdenPagoGeneral .
     *
     * @Route("/index_table/", name="orden_pago_general_table")
     * @Method("GET|POST")
     */
    public function indexTableAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:OrdenPagoGeneral')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Egresos varios'] = null;

        return $this->render('ADIFContableBundle:OrdenPagoGeneral:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new OrdenPagoGeneral entity.
     *
     * @Route("/insertar", name="orden_pago_general_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:OrdenPagoGeneral:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new OrdenPagoGeneral();

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Genero la AutorizacionContable            
            $ordenPagoService = $this->get('adif.orden_pago_service');

            $idProveedor = $entity->getIdProveedor();

            $fechaAutorizacionContable = $entity->getFechaAutorizacionContable();

            $conceptoOrdenPago = $entity->getConceptoOrdenPago();

            $importe = $entity->getImporte();

            $concepto = 'Pago de ' . $conceptoOrdenPago->getDenominacion()
                    . ' - ' . $entity->getObservaciones();

            /* @var $autorizacionContable OrdenPagoGeneral */
            $autorizacionContable = $ordenPagoService
                    ->crearAutorizacionContableGeneral($em, $conceptoOrdenPago, $importe, $concepto);

            $autorizacionContable->setFechaAutorizacionContable($fechaAutorizacionContable);
            $autorizacionContable->setIdProveedor($idProveedor);

            $em->persist($autorizacionContable);

            // Comienzo la transaccion
            $em->getConnection()->beginTransaction();

            try {
                $em->flush();

                $em->getConnection()->commit();

                $mensajeImprimir = 'Para imprimir la autorizaci&oacute;n contable haga click <a href="'
                        . $this->generateUrl($autorizacionContable->getPathAC() . '_print', ['id' => $autorizacionContable->getId()])
                        . '" class="link-imprimir-op">aqu&iacute;</a>';

                $this->get('session')->getFlashBag()->add('info', $mensajeImprimir);
            } //.
            catch (\Exception $e) {

                $em->getConnection()->rollback();
                $em->close();

                throw $e;
            }


            return $this->redirect($this->generateUrl('orden_pago_general'));
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
            'page_title' => 'Crear &oacute;rden de pago general',
        );
    }

    /**
     * Creates a form to create a OrdenPagoGeneral entity.
     *
     * @param OrdenPagoGeneral $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(OrdenPagoGeneral $entity) {
        $form = $this->createForm(new OrdenPagoGeneralType(), $entity, array(
            'action' => $this->generateUrl('orden_pago_general_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new OrdenPagoGeneral entity.
     *
     * @Route("/crear", name="orden_pago_general_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new OrdenPagoGeneral();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear &oacute;rden de pago general'
        );
    }

    /**
     * Finds and displays a OrdenPagoGeneral entity.
     *
     * @Route("/general/{id}", name="ordenpagogeneral_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/general/pagar", name="ordenpagogeneral_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print a OrdenPagoGeneral entity.
     *
     * @Route("/general/print/{id}", name="ordenpagogeneral_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
//        return parent::printAction($id);
        return parent::printOPCompletaAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoGeneral
     *
     * @Route("/general/reemplazar_pago", name="ordenpagogeneral_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPagoGeneral
     *
     * @Route("/general/{id}/anular", name="ordenpagogeneral_anular")
     * @Method("GET")   
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * 
     * @return string
     */
    public function getClassName() {
        return 'ADIFContableBundle:OrdenPagoGeneral';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @param type $esContraasiento
     * @return type
     */
    public function generarAsientoContableAnular($ordenPago, $user, $esContraasiento) {

        /* Genero el contraasiento */
        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoOrdenPagoGeneral($ordenPago, $user, $esContraasiento, true);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {

        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoOrdenPagoGeneral($ordenPago, $user, false, true);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'orden_pago_general/general';
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {

        /* @var $entity  \ADIF\ContableBundle\Entity\OrdenPagoGeneral */

        return array(
            'nombre' => $entity->getProveedor()->getRazonSocial(),
            'labelNombre' => 'Razon Social',
            'identificacion' => $entity->getProveedor()->getCUIT(),
            'labelIdentificacion' => 'CUIT'
        );
    }

    /**
     * 
     * @return type
     */
    public function getConceptoAsientoReemplazoPago() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        return $em->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                        ->findOneByCodigo('TESORERIA');
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $emContable
     * @param type $autorizacionContable
     */
    public function anularActionCustom($ordenPago, $emContable, $autorizacionContable) {
        //parent::clonar($ordenPago, $emContable, $autorizacionContable);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/general/form_pagar", name="ordenpagogeneral_form_pagar")
     * @Method("POST")   
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")
     */
    public function getFormPagarAction(Request $request) {
        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @Route("/general/{id}/historico_general", name="ordenpagogeneral_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

}
