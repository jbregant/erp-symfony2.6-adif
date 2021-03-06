<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use ADIF\ContableBundle\Entity\AdifDatos;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;

/**
 * OrdenPagoAnticipoProveedorController controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoAnticipoProveedorController extends OrdenPagoBaseController {

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
     * @Route("/anticipoproveedor/{id}", name="ordenpagoanticipoproveedor_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/anticipoproveedor/pagar", name="ordenpagoanticipoproveedor_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print a OrdenPagoProveedor entity.
     *
     * @Route("/anticipoproveedor/print/{id}", name="ordenpagoanticipoproveedor_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoAnticipoProveedor
     *
     * @Route("/anticipoproveedor/reemplazar_pago", name="ordenpagonticipoproveedor_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/anticipoproveedor/{id}/anular", name="ordenpagoanticipoproveedor_anular")
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
        return 'ADIFContableBundle:OrdenPagoAnticipoProveedor';
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
        return 'ordenpago/anticipoproveedor';
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {
        /* @var $entity  \ADIF\ContableBundle\Entity\OrdenPagoAnticipoProveedor */

        return array(
            'nombre' => $entity->getProveedor()->getClienteProveedor()->getRazonSocial(),
            'labelNombre' => 'Razon Social',
            'identificacion' => $entity->getProveedor()->getClienteProveedor()->getCuit(),
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
                        ->findOneByCodigo('PAGO_ANTICIPO_PROVEEDOR');
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $emContable
     * @param type $autorizacionContable
     */
    public function anularActionCustom($ordenPago, $emContable, $autorizacionContable) 
	{
        //$anticipoNuevo = clone $ordenPago->getAnticipoProveedor();
        
        //$autorizacionContable->setAnticipoProveedor($anticipoNuevo);
        
        //$anticipoNuevo->setOrdenPago($autorizacionContable);
		
		/*
		$anticipo = $ordenPago->getAnticipoProveedor();
		
		$anticipo->setOrdenPago($autorizacionContable);
		
		$emContable->persist($anticipo);
		*/
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/anticipoproveedor/form_pagar", name="ordenpagoanticipoproveedor_form_pagar")
     * @Method("POST")  
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig") 
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @Route("/anticipoproveedor/{id}/historico_general", name="ordenpagoanticipoproveedor_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\OrdenPagoComprobante $ordenPago
     * @param type $resultArray
     * @return type
     */
    public function getHistoricoGeneralResultData($ordenPago, $resultArray) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPagoAnticipoProveedor */

        $ordenCompra = $ordenPago->getOrdenCompra();

        $requerimiento = null;

        if ($ordenCompra != null) {
            $requerimiento = $ordenCompra->getRequerimiento();
        }

        $resultArray['requerimiento'] = $requerimiento;
        $resultArray['ordenCompra'] = $ordenCompra;

        return $resultArray;
    }
	
	public function validacionesCustom($ordenPago = null)
	{
		$anticipo = $ordenPago->getAnticipoProveedor();
		
		if ($anticipo != null) {
			
			$ordenPagoCancelada = $anticipo->getOrdenPagoCancelada();
			
			if ($ordenPagoCancelada != null && $ordenPagoCancelada->getEstadoOrdenPago()->getDenominacionEstado() != ConstanteEstadoOrdenPago::ESTADO_ANULADA) {
				
				$mensaje = 'No se puede anular la orden de pago, porque el anticipo de $ ';
				$mensaje .= $anticipo->getMonto() . ' esta aplicado en la ';
				
				if ($ordenPagoCancelada->getNumeroOrdenPago() != null) {
					$mensaje .= 'orden de pago n&uacute;mero ' . $ordenPagoCancelada->getNumeroOrdenPago();
					$mensaje .= ' con fecha ' . $ordenPago->getFechaOrdenPago()->format('d/m/Y');
				} else {
					$mensaje .= 'autorizaci&oacute;n contable n&uacute;mero ' . $ordenPagoCancelada->getNumeroAutorizacionContable();
					$mensaje .= ' con fecha ' . $ordenPago->getFechaAutorizacionContable()->format('d/m/Y');
				}
				
				$this->get('session')->getFlashBag()->add('error', $mensaje);
				
				return false;
			}
		}
		
		return true;
	}

}
