<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use ADIF\ContableBundle\Entity\AdifDatos;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * OrdenPagoAnticipoSueldoController controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoAnticipoSueldoController extends OrdenPagoBaseController {

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
     * @Route("/anticiposueldo/{id}", name="ordenpagoanticiposueldo_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/anticiposueldo/pagar", name="ordenpagoanticiposueldo_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print a OrdenPagoSueldo entity.
     *
     * @Route("/anticiposueldo/print/{id}", name="ordenpagoanticiposueldo_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoAnticipoSueldo
     *
     * @Route("/anticiposueldo/reemplazar_pago", name="ordenpagonticiposueldo_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/anticiposueldo/{id}/anular", name="ordenpagoanticiposueldo_anular")
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
        return 'ADIFContableBundle:OrdenPagoAnticipoSueldo';
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
                        ->generarAsientoPagoAnticipoSueldo($ordenPago, $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {
        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoAnticipoSueldo($ordenPago, $user);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/anticiposueldo';
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {

        /* @var $entity  \ADIF\ContableBundle\Entity\OrdenPagoAnticipoSueldo */

        return array(
            'nombre' => $entity->getEmpleado()->getRazonSocial(),
            'labelNombre' => 'Raz&oacute;n Social',
            'identificacion' => $entity->getEmpleado()->getNroDocumento(),
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

        //$anticipoNuevo = clone $ordenPago->getAnticipoSueldo();

        //$autorizacionContable->setAnticipoSueldo($anticipoNuevo);

        //$anticipoNuevo->setOrdenPago($autorizacionContable);
		
		$anticipo = $ordenPago->getAnticipoSueldo();
		
		$anticipo->setOrdenPago($autorizacionContable);
		
		$emContable->persist($anticipo);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/anticiposueldo/form_pagar", name="ordenpagoanticiposueldo_pagar_form")
     * @Method("POST") 
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")  
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @Route("/anticiposueldo/{id}/historico_general", name="ordenpagoanticiposueldo_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\OrdenPagoAnticipoSueldo $ordenPago
     * @param array $resultArray
     * @return type
     */
    public function getHistoricoGeneralResultData($ordenPago, $resultArray) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPagoAnticipoSueldo */

        $empleado = $ordenPago->getEmpleado();

        $resultArray['empleado'] = $empleado;

        return $resultArray;
    }

}
