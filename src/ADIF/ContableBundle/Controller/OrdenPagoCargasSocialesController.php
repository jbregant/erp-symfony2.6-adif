<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use ADIF\ContableBundle\Entity\AdifDatos;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * OrdenPagoCargasSociales controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoCargasSocialesController extends OrdenPagoBaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Finds and displays a OrdenPagoCargasSociales entity.
     *
     * @Route("/cargassociales/{id}", name="ordenpagocargassociales_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/cargassociales/pagar", name="ordenpagocargassociales_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print a OrdenPagoCargasSociales entity.
     *
     * @Route("/cargassociales/print/{id}", name="ordenpagocargassociales_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoCargasSociales
     *
     * @Route("/cargassociales/reemplazar_pago", name="ordenpagocargassociales_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/cargassociales/{id}/anular", name="ordenpagocargassociales_anular")
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
        return 'ADIFContableBundle:OrdenPagoCargasSociales';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @param type $esContraasiento
     * @return type
     */
    public function generarAsientoContableAnular($ordenPago, $user, $esContraasiento) {
        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoCargasSociales($ordenPago, $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {
        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoCargasSociales($ordenPago, $user);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/cargassociales';
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {

        return array(
            'nombre' => AdifDatos::RAZON_SOCIAL,
            'labelNombre' => 'Raz&oacute;n Social',
            'identificacion' => AdifDatos::CUIT,
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
                        ->findOneByCodigo('PAGO_SUELDOS');
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/cargassociales/form_pagar", name="ordenpagocargassociales_form_pagar")
     * @Method("POST")   
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @Route("/cargassociales/{id}/historico_general", name="ordenpagocargassociales_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

}
