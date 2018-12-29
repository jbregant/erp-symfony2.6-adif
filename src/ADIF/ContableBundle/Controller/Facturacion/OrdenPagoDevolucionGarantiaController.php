<?php

namespace ADIF\ContableBundle\Controller\Facturacion;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoAsientoContable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * OrdenPagoDevolucionGarantia controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoDevolucionGarantiaController extends OrdenPagoBaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Finds and displays a OrdenPagoDevolucionGarantia entity.
     *
     * @Route("/devolucion_garantia/{id}", name="ordenpagodevoluciongarantia_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/devolucion_garantia/pagar", name="ordenpagodevoluciongarantia_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print an OrdenPagoDevolucionGarantia entity.
     *
     * @Route("/devolucion_garantia/print/{id}", name="ordenpagodevoluciongarantia_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoDevolucionGarantia
     *
     * @Route("/devolucion_garantia/reemplazar_pago", name="ordenpagodevoluciongarantia_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPagoDevolucionGarantia
     *
     * @Route("/devolucion_garantia/{id}/anular", name="ordenpagodevoluciongarantia_anular")
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
        return 'ADIFContableBundle:Facturacion\OrdenPagoDevolucionGarantia';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @param type $esContraasiento
     * @return type
     */
    public function generarAsientoContableAnular($ordenPago, $user, $esContraasiento) {

        /* Genero el contraasiento */
        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoDevolucionGarantia($ordenPago, $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {

        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoDevolucionGarantia($ordenPago, $user);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/devolucion_garantia';
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {

        /* @var $entity  \ADIF\ContableBundle\Entity\Facturacion\OrdenPagoDevolucionGarantia */
        
        $cliente = $entity->getDevolucionGarantia()->getCuponGarantia()->getCliente();
        
        return array(
            'nombre' => $cliente,
            'labelNombre' => 'Responsable',
            'identificacion' => $cliente->getNroDocumento(),
            'labelIdentificacion' => $cliente->getTipoDocumento()
        );
    }

    /**
     * 
     * @return type
     */
    public function getConceptoAsientoReemplazoPago() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        return $em->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                        ->findOneByCodigo(ConstanteConceptoAsientoContable::FINANCIERO);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/devolucion_garantia/form_pagar", name="ordenpagodevoluciongarantia_form_pagar")
     * @Method("POST")   
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @Route("/devolucion_garantia/{id}/historico_general", name="ordenpagodevoluciongarantia_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

}
