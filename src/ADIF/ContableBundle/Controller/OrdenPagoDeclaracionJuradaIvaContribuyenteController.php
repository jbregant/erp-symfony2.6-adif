<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use ADIF\ContableBundle\Entity\AdifDatos;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * OrdenPagoDeclaracionJuradaIvaContribuyenteController controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoDeclaracionJuradaIvaContribuyenteController extends OrdenPagoBaseController {

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
     * @Route("/declaracionesjuradasivacontribuyente/{id}", name="ordenpagodeclaracionjuradaivacontribuyente_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/declaracionesjuradasivacontribuyente/pagar", name="ordenpagodeclaracionjuradaivacontribuyente_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print a OrdenPagoDeclaracionJuradaIvaContribuyente entity.
     *
     * @Route("/declaracionesjuradasivacontribuyente/print/{id}", name="ordenpagodeclaracionjuradaivacontribuyente_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoDeclaracionJuradaIvaContribuyente
     *
     * @Route("/declaracionesjuradasivacontribuyente/reemplazar_pago", name="ordenpagodeclaracionjuradaivacontribuyente_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/declaracionesjuradasivacontribuyente/{id}/anular", name="ordenpagodeclaracionjuradaivacontribuyente_anular")
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
        return 'ADIFContableBundle:OrdenPagoDeclaracionJuradaIvaContribuyente';
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
                        ->generarAsientoPagoDeclaracionJuradaIvaContribuyente($ordenPago, $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {
        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoDeclaracionJuradaIvaContribuyente($ordenPago, $user);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/declaracionesjuradasivacontribuyente';
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

        //$declaracionJuradaIvaContribuyenteNueva = clone $ordenPago->getDeclaracionJuradaIvaContribuyente();

        //$autorizacionContable->setDeclaracionJuradaIvaContribuyente($declaracionJuradaIvaContribuyenteNueva);

        //$declaracionJuradaIvaContribuyenteNueva->setOrdenPago($autorizacionContable);
		
		$ddjj = $ordenPago->getDeclaracionJuradaIvaContribuyente();
		
		$ddjj->setOrdenPago($autorizacionContable);
		
		$emContable->persist($ddjj);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/declaracionesjuradasivacontribuyente/form_pagar", name="ordenpagodeclaracionjuradaivacontribuyente_form_pagar")
     * @Method("POST")   
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @Route("/declaracionesjuradasivacontribuyente/{id}/historico_general", name="ordenpagodeclaracionjuradaivacontribuyente_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

}
