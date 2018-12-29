<?php

namespace ADIF\ContableBundle\Controller\Consultoria;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * OrdenPagoAnticipoContratoConsultoriaController controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoAnticipoContratoConsultoriaController extends OrdenPagoBaseController {

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
     * @Route("/anticipocontratoconsultoria/{id}", name="ordenpagoanticipocontratoconsultoria_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/anticipocontratoconsultoria/pagar", name="ordenpagoanticipocontratoconsultoria_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print a OrdenPagoProveedor entity.
     *
     * @Route("/anticipocontratoconsultoria/print/{id}", name="ordenpagoanticipocontratoconsultoria_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoAnticipoContratoConsultoriaController
     *
     * @Route("/anticipocontratoconsultoria/reemplazar_pago", name="ordenpagonticipoproveedor_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/anticipocontratoconsultoria/{id}/anular", name="ordenpagoanticipocontratoconsultoria_anular")
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
        return 'ADIFContableBundle:OrdenPagoAnticipoContratoConsultoria';
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
                        ->generarAsientoPagoAnticipoProveedor($ordenPago, $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {
        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoAnticipoProveedor($ordenPago, $user);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/anticipocontratoconsultoria';
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {
        /* @var $entity  \ADIF\ContableBundle\Entity\OrdenPagoAnticipoContratoConsultoriaController */

        return array(
            'nombre' => $entity->getConsultor()->getRazonSocial(),
            'labelNombre' => 'Razon Social',
            'identificacion' => $entity->getConsultor()->getCuit(),
            'labelIdentificacion' => 'CUIT'
        );
    }

    /**
     * 
     * @return type
     */
    public function getConceptoAsientoReemplazoPago() {
//        $em = $this->getDoctrine()->getManager($this->getEntityManager());
//
//        return $em->getRepository('ADIFContableBundle:ConceptoAsientoContable')
//                        ->findOneByCodigo('PAGO_ANTICIPO_PROVEEDOR');
        return '';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $emContable
     * @param type $autorizacionContable
     */
    public function anularActionCustom($ordenPago, $emContable, $autorizacionContable) {

        //$anticipoContratoConsultoriaNuevo = clone $ordenPago->getAnticipoContratoConsultoria();

        //$autorizacionContable->setAnticipoContratoConsultoria($anticipoContratoConsultoriaNuevo);

        //$anticipoContratoConsultoriaNuevo->setOrdenPago($autorizacionContable);
		
		$anticipo = $ordenPago->getAnticipoContratoConsultoria();
		
		$autorizacionContable->setAnticipoContratoConsultoria($anticipo);
		
		$anticipo->setOrdenPago($autorizacionContable);
		
		$emContable->persist($anticipo);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/anticipocontratoconsultoria/form_pagar", name="ordenpagoanticipocontratoconsultoria_form_pagar")
     * @Method("POST")   
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @Route("/anticipocontratoconsultoria/{id}/historico_general", name="ordenpagoanticipocontratoconsultoria_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\OrdenPagoAnticipoContratoConsultoria $ordenPago
     * @param array $resultArray
     * @return type
     */
    public function getHistoricoGeneralResultData($ordenPago, $resultArray) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPagoAnticipoContratoConsultoria */

        $contrato = $ordenPago->getContrato();

        $resultArray['contratoConsultoria'] = $contrato;

        return $resultArray;
    }

}
