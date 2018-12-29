<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use ADIF\ContableBundle\Entity\AdifDatos;
use ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoAsientoContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoChequera;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * OrdenPagoMovimientoMinisterial controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoMovimientoMinisterialController extends OrdenPagoBaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Finds and displays a OrdenPagoMovimientoMinisterial entity.
     *
     * @Route("/movimientoministerial/{id}", name="ordenpagomovimientoministerial_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/movimientoministerial/pagar", name="ordenpagomovimientoministerial_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print an OrdenPagoMovimientoMinisterial entity.
     *
     * @Route("/movimientoministerial/print/{id}", name="ordenpagomovimientoministerial_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoMovimientoMinisterial
     *
     * @Route("/movimientoministerial/reemplazar_pago", name="ordenpagomovimientoministerial_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPagoMovimientoMinisterial
     *
     * @Route("/movimientoministerial/{id}/anular", name="ordenpagomovimientoministerial_anular")
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
        return 'ADIFContableBundle:OrdenPagoMovimientoMinisterial';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @param type $esContraasiento
     * @return type
     */
    public function generarAsientoContableAnular($ordenPago, $user, $esContraasiento) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPagoMovimientoMinisterial */

        /* Genero el contraasiento */
        return $this->get('adif.asiento_service')
                        ->generarAsientoMovimientoMinisterial($ordenPago->getMovimientoMinisterial(), $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPagoMovimientoMinisterial */

        return $this->get('adif.asiento_service')
                        ->generarAsientoMovimientoMinisterial($ordenPago->getMovimientoMinisterial(), $user, false);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/movimientoministerial';
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
                        ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/movimientoministerial/form_pagar", name="ordenpagomovimientoministerial_form_pagar")
     * @Method("POST")   
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")
     */
    public function getFormPagarAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idOrdenPago = $request->request->get('id');

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPagoMovimientoMinisterial */

        $ordenPago = $em->getRepository($this->getClassName())->find($idOrdenPago);

        $cuentaBancariaADIFOrigen = $ordenPago->getMovimientoMinisterial()->getCuentaBancariaADIF();

        $chequeras = $em->getRepository('ADIFContableBundle:Chequera')
                ->getChequerasByEstado(ConstanteEstadoChequera::ESTADO_CHEQUERA_HABILITADA_ACTIVA);

        $chequerasArray = [];
        $chequeraCuentaArray = [];

        $chequeraCuentaArray[$cuentaBancariaADIFOrigen->getId()] = [];

        foreach ($chequeras as $chequera) {
            $chequerasArray[$chequera->getId()] = array(
                'chequera' => $chequera->__toString(),
                'numeroSiguiente' => $chequera->getNumeroSiguiente()
            );

            $chequeraCuentaArray[$chequera->getIdCuenta()][] = $chequera->getId();
        }

        return array(
            'cuentasBancoAdif' => array($cuentaBancariaADIFOrigen),
            'chequeras' => $chequerasArray,
            'chequerasEntities' => $chequeras,
            'chequeraCuenta' => $chequeraCuentaArray,
            'cantidadAutorizacionesContables' => $this->getCantidadAutorizacionesContablesByBeneficiario($idOrdenPago)
        );
    }

    /**
     * 
     * @Route("/movimientoministerial/{id}/historico_general", name="ordenpagomovimientoministerial_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

}
