<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use ADIF\ContableBundle\Entity\AdifDatos;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonRetencionLiquidacion;
use ADIF\ContableBundle\Entity\OrdenPagoRenglonRetencionLiquidacion;
use ADIF\ContableBundle\Entity\RenglonRetencionLiquidacion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * OrdenPagoRenglonRetencionLiquidacionController controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoRenglonRetencionLiquidacionController extends OrdenPagoBaseController {

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
     * @Route("/renglonesretencionliquidacion/{id}", name="ordenpagorenglonretencionliquidacion_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/renglonesretencionliquidacion/pagar", name="ordenpagorenglonretencionliquidacion_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print a OrdenPagoRenglonRetencionLiquidacion entity.
     *
     * @Route("/renglonesretencionliquidacion/print/{id}", name="ordenpagorenglonretencionliquidacion_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoRenglonRetencionLiquidacion
     *
     * @Route("/renglonesretencionliquidacion/reemplazar_pago", name="ordenpagorenglonretencionliquidacion_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/renglonesretencionliquidacion/{id}/anular", name="ordenpagorenglonretencionliquidacion_anular")
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
        return 'ADIFContableBundle:OrdenPagoRenglonRetencionLiquidacion';
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
                        ->generarAsientoPagoRenglonRetencionLiquidacion($ordenPago, $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {
        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoRenglonRetencionLiquidacion($ordenPago, $user);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/renglon_retencion_liquidacion';
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {
        return array(
            'nombre' => $entity->getBeneficiario()->getRazonSocial(),
            'labelNombre' => 'Raz&oacute;n Social',
            'identificacion' => $entity->getBeneficiario()->getCUIT(),
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

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        /* @var $ordenPago OrdenPagoRenglonRetencionLiquidacion */
        /* @var $autorizacionContable OrdenPagoRenglonRetencionLiquidacion */

        foreach ($ordenPago->getRenglonesRetencionLiquidacion() as $renglonRetencionLiquidacion) {

            /* @var $renglonRetencionLiquidacion RenglonRetencionLiquidacion */
			
			$renglonRetencionLiquidacion
                    ->setEstadoRenglonRetencionLiquidacion(
                            $em->getRepository('ADIFContableBundle:EstadoRenglonRetencionLiquidacion')
                            ->findOneByDenominacion(ConstanteEstadoRenglonRetencionLiquidacion::PENDIENTE)
            );

            $autorizacionContable->addRenglonesRetencionLiquidacion($renglonRetencionLiquidacion);
        }
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/renglonesretencionliquidacion/form_pagar", name="ordenpagorenglonretencionliquidacion_form_pagar")
     * @Method("POST")   
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @Route("/renglonesretencionliquidacion/{id}/historico_general", name="ordenpagorenglonretencionliquidacion_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

}
