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
 * OrdenPagoMovimientoBancario controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoMovimientoBancarioController extends OrdenPagoBaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Finds and displays a OrdenPagoMovimientoBancario entity.
     *
     * @Route("/movimientobancario/{id}", name="ordenpagomovimientobancario_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/movimientobancario/pagar", name="ordenpagomovimientobancario_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print an OrdenPagoMovimientoBancario entity.
     *
     * @Route("/movimientobancario/print/{id}", name="ordenpagomovimientobancario_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoMovimientoBancario
     *
     * @Route("/movimientobancario/reemplazar_pago", name="ordenpagomovimientobancario_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPagoMovimientoBancario
     *
     * @Route("/movimientobancario/{id}/anular", name="ordenpagomovimientobancario_anular")
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
        return 'ADIFContableBundle:OrdenPagoMovimientoBancario';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @param type $esContraasiento
     * @return type
     */
    public function generarAsientoContableAnular($ordenPago, $user, $esContraasiento) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPagoMovimientoBancario */

        /* Genero el contraasiento */
        return $this->get('adif.asiento_service')
                        ->generarAsientoMovimientoBancario($ordenPago->getMovimientoBancario(), $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPagoMovimientoBancario */

        return $this->get('adif.asiento_service')
                        ->generarAsientoMovimientoBancario($ordenPago->getMovimientoBancario(), $user);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/movimientobancario';
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
     * @Route("/movimientobancario/form_pagar", name="ordenpagomovimientobancario_form_pagar")
     * @Method("POST")   
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")
     */
    public function getFormPagarAction(Request $request) {

        $emContable = $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idOrdenPago = $request->request->get('id');

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPagoMovimientoBancario */

        $ordenPago = $em->getRepository($this->getClassName())->find($idOrdenPago);

        $cuentaBancariaADIFOrigen = $ordenPago->getMovimientoBancario()->getCuentaOrigen();

        $chequeras = $emContable->getRepository('ADIFContableBundle:Chequera')
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
     * @Route("/movimientobancario/{id}/historico_general", name="ordenpagomovimientobancario_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

}
