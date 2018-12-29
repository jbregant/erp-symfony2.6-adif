<?php

namespace ADIF\ContableBundle\Controller\Obras;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use ADIF\ContableBundle\Entity\Obras\OrdenPagoObra;
use ADIF\ContableBundle\Form\Obras\OrdenPagoObraType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * AutorizacionContable controller.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContableObraController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Creates a new OrdenPagoObra entity.
     *
     * @Route("/obra/insertar", name="autorizacioncontableobra_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:AutorizacionContable:new.html.twig")
     */
    public function createAction(Request $request) {

        return parent::createAction($request);
    }

    /**
     * Creates a form to create a OrdenPagoObra entity.
     *
     * @param OrdenPagoObra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    public function createCreateForm(OrdenPagoObra $entity) {
        $form = $this->createForm(new OrdenPagoObraType(), $entity, array(
            'action' => $this->generateUrl('autorizacioncontableobra_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar e ir a autorizacion contable'));

        return $form;
    }

    /**
     * Displays a form to create a new OrdenPagoObra entity.
     *
     * @Route("/obra/crear", name="autorizacioncontableobra_new")
     * @Method("POST")
     * @Template("ADIFContableBundle:AutorizacionContable:new.html.twig")
     */
    public function newAction(Request $request) {

        return parent::newAction($request);
    }

    /**
     * Anula a OrdenPagoObra entity.
     *
     * @Route("/obra/anular/{id}", name="autorizacioncontableobra_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoObra entity.
     *
     * @Route("/obra/visar/{id}", name="autorizacioncontableobra_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPagoObra entity.
     *
     * @Route("/obra/print/{id}", name="autorizacioncontableobra_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    public function getOP() {
        return new OrdenPagoObra();
    }

    public function getComprobantesClassName() {
        return 'ADIFContableBundle:Obras\ComprobanteObra';
    }

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function getConceptoCreacion($ordenPago) {
        return "Pago del Tramo " . $ordenPago->getTramo();
    }

    public function getClassName() {
        return 'ADIFContableBundle:Obras\OrdenPagoObra';
    }

    public function generarRetenciones($ordenPago) {
        return $this->get('adif.retenciones_service')->generarComprobantesRetencionObras($ordenPago);
    }

    /**
     * 
     * @param type $em
     * @param OrdenPagoObra $ordenPago
     * @param type $request
     * @return int
     */
    public function newActionCustom($em, $ordenPago, $request) {

        $error = '';

        if (($ordenPago->getTotalBruto() > 600000) && ($ordenPago->getProveedor()->getClienteProveedor()->getCondicionIVA()
                        ->getDenominacionTipoResponsable() == ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO)) {

            $error .= 'El monto total supera el l&iacute;mite de pago a monotributistas. ';
        }

        if ($ordenPago->getMontoNeto() < 0) {
            $error .= 'El monto de los anticipos supera al de los comprobantes menos las retenciones. ';
        }

        if ($error != '') {
            return array('error' => $error);
        } else {
            return 0;
        }
    }

    public function getPathComprobantes() {
        return 'comprobanteobra';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $comprobante
     */
    public function setBeneficiarioCustom($ordenPago, $comprobante) {
        $ordenPago->setProveedor($comprobante->getProveedor());
    }

    public function anularActionCustom($ordenPago, $em) {

        parent::clonar($ordenPago, $em);
    }

}
