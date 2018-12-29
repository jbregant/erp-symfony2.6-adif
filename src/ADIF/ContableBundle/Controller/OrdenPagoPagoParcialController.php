<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use ADIF\ContableBundle\Controller\Obras\ComprobanteRetencionImpuestoObrasController;
use ADIF\ContableBundle\Controller\ComprobanteRetencionImpuestoComprasController;

/**
 * OrdenPagoPagoParcialController controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoPagoParcialController extends OrdenPagoBaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Finds and displays a OrdenPagoPagoParcial entity.
     *
     * @Route("/pagoparcial/{id}", name="ordenpagopagoparcial_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/pagoparcial/pagar", name="ordenpagopagoparcial_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print a OrdenPagoPagoParcial entity.
     *
     * @Route("/pagoparcial/print/{id}", name="ordenpagopagoparcial_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        //return parent::printAction($id);
		return parent::printOPCompletaAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoPagoParcial
     *
     * @Route("/pagoparcial/reemplazar_pago", name="ordenpagopagoparcial_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/pagoparcial/{id}/anular", name="ordenpagopagoparcial_anular")
     * @Method("GET")   
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    public function getClassName() {
        return 'ADIFContableBundle:OrdenPagoPagoParcial';
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
                        ->generarAsientoPagoPagoParcial($ordenPago, $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {
        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoPagoParcial($ordenPago, $user);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/pagoparcial';
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {

        return array(
            'nombre' => $entity->getProveedor()->getRazonSocial(),
            'labelNombre' => 'Raz&oacute;n Social',
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
                        ->findOneByCodigo('PAGO_ANTICIPO_SUELDOS');
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $emContable
     * @param type $autorizacionContable
     */
    public function anularActionCustom($ordenPago, $emContable, $autorizacionContable) {

        //$pagoParcial = $ordenPago->getPagoParcial();

        //$pagoParcialNuevo = clone $pagoParcial;

        //$pagoParcial->setAnulado(true);

        //$autorizacionContable->setPagoParcial($pagoParcialNuevo);

        //$pagoParcialNuevo->setOrdenPago($autorizacionContable);

        //$emContable->persist($pagoParcialNuevo);
		
		$pagoParcial = $ordenPago->getPagoParcial();
		
		$pagoParcial->setAnulado(true);
		
		$autorizacionContable->setPagoParcial($pagoParcial);
		
		$pagoParcial->setOrdenPago($autorizacionContable);
		
		$comprobante = $pagoParcial->getComprobante();
		
		// Devuelvo el saldo
		$comprobante->setSaldo($comprobante->getSaldo() + $pagoParcial->getTotalNeto());

		$emContable->persist($comprobante);
		$emContable->persist($pagoParcial);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/pagoparcial/form_pagar", name="ordenpagopagoparcial_form_pagar")
     * @Method("POST")  
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig") 
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @Route("/pagoparcial/{id}/historico_general", name="ordenpagopagoparcial_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }
	
	public function pagarActionCustom($ordenPago, $emContable)
	{
		$pagoParcial = $ordenPago->getPagoParcial();
		$comprobante = $pagoParcial->getComprobante();
		
		// Le saco saldo al comprobante del pago parcial
		$comprobante->setSaldo($comprobante->getSaldo() - $pagoParcial->getTotalNeto());
		
		$pagoParcial->setAnulado(false);
		
		$emContable->persist($comprobante);
		$emContable->persist($pagoParcial);
	}
	
	public function getRetencionesController($comprobantes) 
	{
		$comprobante = $comprobantes->first();
		
		if ($comprobante->getEsComprobanteObra()) {
			return new ComprobanteRetencionImpuestoObrasController();
		} else {
			return new ComprobanteRetencionImpuestoComprasController();  
		}
    }

}
