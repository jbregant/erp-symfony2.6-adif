<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Session\EmpresaSession;

/**
 * OrdenPagoPagoACuentaController controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoPagoACuentaController extends OrdenPagoBaseController {

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
     * @Route("/pago_a_cuenta/{id}", name="ordenpagopagoacuenta_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/pago_a_cuenta/pagar", name="ordenpagopagoacuenta_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print a OrdenPagoPagoACuenta entity.
     *
     * @Route("/pago_a_cuenta/print/{id}", name="ordenpagopagoacuenta_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoPagoACuenta
     *
     * @Route("/pago_a_cuenta/reemplazar_pago", name="ordenpagopagoacuenta_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/pago_a_cuenta/{id}/anular", name="ordenpagopagoacuenta_anular")
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
        return 'ADIFContableBundle:OrdenPagoPagoACuenta';
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
                        ->generarAsientoPagoPagoACuenta($ordenPago, $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {
        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoPagoACuenta($ordenPago, $user);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/pago_a_cuenta';
    }

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function printHTMLAction($ordenPago) {

        $arrayResult['op'] = $ordenPago;

        $empresaSession = EmpresaSession::getInstance();
        $idEmpresa = $empresaSession->getIdEmpresa();
        
        $arrayResult['idEmpresa'] = $idEmpresa;
        
        if ($ordenPago->getBeneficiario() != null) {
            $arrayResult['razonSocial'] = 'AFIP';
            $arrayResult['tipoDocumento'] = 'CUIT';
            $arrayResult['nroDocumento'] = '33-69345023-9';
            $arrayResult['domicilio'] = 'YRIGOYEN HIPOLITO 370 Piso:4 Dpto:4752 1086';
            $arrayResult['localidad'] = 'CIUDAD AUTONOMA BUENOS AIRES';
        }

        return $this->renderView('ADIFContableBundle:OrdenPago:print.show.html.twig', $arrayResult);
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {

        return array(
            'nombre' => 'AFIP',
            'labelNombre' => 'Raz&oacute;n Social',
            'identificacion' => '33-69345023-9',
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

        //$pagoACuentaNueva = clone $ordenPago->getPagoACuenta();

        //$autorizacionContable->setPagoACuenta($pagoACuentaNueva);

        //$pagoACuentaNueva->setOrdenPago($autorizacionContable);
		
		$pagoACuenta = $ordenPago->getPagoACuenta();
		
		$pagoACuenta->setOrdenPago($autorizacionContable);
		
		$emContable->persist($pagoACuenta);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/pago_a_cuenta/form_pagar", name="ordenpagopagoacuenta_form_pagar")
     * @Method("POST")  
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig") 
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @Route("/pago_a_cuenta/{id}/historico_general", name="ordenpagopagoacuenta_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\OrdenPagoPagoACuenta $ordenPago
     * @param array $resultArray
     * @return type
     */
    public function getHistoricoGeneralResultData($ordenPago, $resultArray) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPagoPagoACuenta */

        $pagoACuenta = $ordenPago->getPagoACuenta();

        $resultArray['pagoACuenta'] = $pagoACuenta;

        return $resultArray;
    }

}
