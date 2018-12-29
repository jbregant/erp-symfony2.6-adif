<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use ADIF\ContableBundle\Entity\AdifDatos;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * OrdenPagoDeclaracionJuradaController controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoDevolucionRenglonDeclaracionJuradaController extends OrdenPagoBaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Finds and displays a OrdenPagoComprobante entity.
     *
     * @Route("/devolucion_renglon_declaracion_jurada/{id}", name="ordenpagodevolucionrenglondeclaracionjurada_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/devolucion_renglon_declaracion_jurada/pagar", name="ordenpagodevolucionrenglondeclaracionjurada_pagar")
     * @Method("POST")
     * -@Security("has_role('ROLE_TESORERIA')")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print a OrdenPagoDeclaracionJurada entity.
     *
     * @Route("/devolucion_renglon_declaracion_jurada/print/{id}", name="ordenpagodevolucionrenglondeclaracionjurada_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoDeclaracionJurada
     *
     * @Route("/devolucion_renglon_declaracion_jurada/reemplazar_pago", name="ordenpagodevolucionrenglondeclaracionjurada_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/devolucion_renglon_declaracion_jurada/{id}/anular", name="ordenpagodevolucionrenglondeclaracionjurada_anular")
     * @Method("GET")
     * -@Security("has_role('ROLE_VISAR_AUTORIZACION_CONTABLE')")   
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * 
     * @return string
     */
    public function getClassName() {
        return 'ADIFContableBundle:OrdenPagoDevolucionRenglonDeclaracionJurada';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @param type $esContraasiento
     * @return type
     */
    public function generarAsientoContableAnular($ordenPago, $user, $esContraasiento) {
        return $this->get('adif.asiento_service')->generarAsientoPagoDevolucionRenglonDeclaracionJurada($ordenPago, $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {
        return $this->get('adif.asiento_service')->generarAsientoPagoDevolucionRenglonDeclaracionJurada($ordenPago, $user);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/devolucion_renglon_declaracion_jurada';
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
                        ->findOneByCodigo('PAGO_ANTICIPO_SUELDOS');
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $emContable
     * @param type $autorizacionContable
     */
    public function anularActionCustom($ordenPago, $emContable, $autorizacionContable) {

        //$declaracionJuradaNueva = clone $ordenPago->getDevolucionRenglonDeclaracionJurada();

        //$autorizacionContable->setDevolucionRenglonDeclaracionJurada($declaracionJuradaNueva);

        //$declaracionJuradaNueva->setOrdenPago($autorizacionContable);
		
		$ddjj = $ordenPago->getDevolucionRenglonDeclaracionJurada();
		
		$ddjj->setOrdenPago($autorizacionContable);
		
		$emContable->persist($ddjj);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/devolucion_renglon_declaracion_jurada/form_pagar", name="ordenpagodevolucionrenglondeclaracionjurada_form_pagar")
     * @Method("POST")   
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @Route("/devolucion_renglon_declaracion_jurada/{id}/historico_general", name="ordenpagodevolucionrenglondeclaracionjurada_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

}
