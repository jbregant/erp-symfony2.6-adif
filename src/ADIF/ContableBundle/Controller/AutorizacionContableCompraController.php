<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use ADIF\ContableBundle\Entity\OrdenPagoComprobante;
use ADIF\ContableBundle\Form\OrdenPagoComprobanteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * AutorizacionContable controller.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContableCompraController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Creates a new OrdenPagoComprobante entity.
     *
     * @Route("/autorizacioncontable/compra/insertar", name="autorizacioncontablecompra_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:AutorizacionContable:new.html.twig")
     */
    public function createAction(Request $request) {

        return parent::createAction($request);
    }

    /**
     * Creates a form to create a OrdenPagoComprobante entity.
     *
     * @param OrdenPagoComprobante $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    public function createCreateForm(OrdenPagoComprobante $entity) {
        $form = $this->createForm(new OrdenPagoComprobanteType($this->getDoctrine()->getManager($this->getEntityManager())), $entity, array(
            'action' => $this->generateUrl('autorizacioncontablecompra_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar e ir a autorizacion contable'));

        return $form;
    }

    /**
     * Displays a form to create a new OrdenPagoComprobante entity.
     *
     * @Route("/autorizacioncontable/compra/crear", name="autorizacioncontablecompra_new")
     * @Method("POST")
     * @Template("ADIFContableBundle:AutorizacionContable:new.html.twig")
     */
    public function newAction(Request $request) {

        return parent::newAction($request);
    }

    /**
     * Anula a OrdenPagoComprobante entity.
     *
     * @Route("/autorizacioncontable/compra/anular/{id}", name="autorizacioncontablecompra_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoComprobante entity.
     *
     * @Route("/autorizacioncontable/compra/visar/{id}", name="autorizacioncontablecompra_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPago entity.
     *
     * @Route("/autorizacioncontable/compra/print/{id}", name="autorizacioncontablecompra_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * 
     * @return OrdenPagoComprobante
     */
    public function getOP() {
        return new OrdenPagoComprobante();
    }

    /**
     * 
     * @return string
     */
    public function getComprobantesClassName() {
        return 'ADIFContableBundle:ComprobanteCompra';
    }

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function getConceptoCreacion($ordenPago) {
        return "Pago de la OC n&ordm; " . $ordenPago->getOrdenCompra();
    }

    /**
     * 
     * @return string
     */
    public function getClassName() {
        return 'ADIFContableBundle:OrdenPagoComprobante';
    }

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function generarRetenciones($ordenPago) {
        return $this->get('adif.retenciones_service')->generarComprobantesRetencionCompras($ordenPago);
    }

    /**
     * 
     * @param type $em
     * @param OrdenPagoComprobante $ordenPago
     * @param type $request
     * @return int
     */
    public function newActionCustom($em, $ordenPago, $request) {

        $ids_anticipos = $request->request->get('ids_anticipos', []);

        $error = '';

        if (($ordenPago->getTotalBruto() > 600000) && ($ordenPago->getProveedor()->getClienteProveedor()->getCondicionIVA()
                        ->getDenominacionTipoResponsable() == ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO)) {

            $error .= 'El monto total supera el l&iacute;mite de pago a monotributistas. ';
        }

        foreach ($ids_anticipos as $ids_anticipo) {

            /* @var $anticipo \ADIF\ContableBundle\Entity\AnticipoProveedor */
            $anticipo = $em->getRepository('ADIFContableBundle:AnticipoProveedor')
                    ->find($ids_anticipo);

            $anticipo->setOrdenPagoCancelada($ordenPago);
            /* @var $ordenPago OrdenPagoComprobante */
            $ordenPago->addAnticipo($anticipo);
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

    /**
     * 
     * @return string
     */
    public function getPathComprobantes() {
        return 'comprobantes_compra';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $comprobante
     */
    public function setBeneficiarioCustom($ordenPago, $comprobante) {
        $ordenPago->setProveedor($comprobante->getProveedor());
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $em
     */
    public function anularActionCustom($ordenPago, $em) {

        parent::clonar($ordenPago, $em);
    }

}
