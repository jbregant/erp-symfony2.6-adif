<?php

namespace ADIF\ContableBundle\Controller\EgresoValor;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoReconocimientoEgresoValor;

/**
 * OrdenPagoReconocimientoEgresoValor controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoReconocimientoController extends OrdenPagoBaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Finds and displays a OrdenPagoReconocimientoEgresoValor entity.
     *
     * @Route("/reconocimiento/{id}", name="ordenpagoreconocimiento_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/reconocimiento/pagar", name="ordenpagoreconocimiento_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print an OrdenPago entity.
     *
     * @Route("/reconocimiento/print/{id}", name="ordenpagoreconocimiento_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPago
     *
     * @Route("/reconocimiento/reemplazar_pago", name="ordenpagoreconocimiento_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/reconocimiento/{id}/anular", name="ordenpagoreconocimiento_anular")
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
        return 'ADIFContableBundle:EgresoValor\OrdenPagoReconocimientoEgresoValor';
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
                        ->generarAsientoPagoReconocimientoEgresoValor($ordenPago, $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {

        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoReconocimientoEgresoValor($ordenPago, $user);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/reconocimiento';
    }

    /**
     * 
     * @param type $ordenPagoPendientePago
     * @param type $emContable
     */
    public function pagarActionCustom($ordenPagoPendientePago, $emContable) {

        /* @var $ordenPagoPendientePago  \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoReconocimientoEgresoValor */

        $ordenPagoPendientePago->getReconocimientoEgresoValor()->setEstadoReconocimientoEgresoValor(
                $emContable->getRepository('ADIFContableBundle:EgresoValor\EstadoReconocimientoEgresoValor')
                        ->findOneByCodigo(ConstanteEstadoReconocimientoEgresoValor::ESTADO_RECONOCIDO)
        );

        $ordenPagoPendientePago->getReconocimientoEgresoValor()->getEgresoValor()->setEstadoEgresoValor(
                $emContable->getRepository('ADIFContableBundle:EgresoValor\EstadoEgresoValor')
                        ->findOneByCodigo(ConstanteEstadoEgresoValor::ESTADO_ACTIVO)
        );
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {
        /* @var $entity  \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoReconocimientoEgresoValor */

        return array(
            'nombre' => $entity->getReconocimientoEgresoValor()->getEgresoValor()
                    ->getResponsableEgresoValor(),
            'labelNombre' => 'Responsable',
            'identificacion' => $entity->getReconocimientoEgresoValor()
                    ->getEgresoValor()->getResponsableEgresoValor()->getNroDocumento(),
            'labelIdentificacion' => $entity->getReconocimientoEgresoValor()
                    ->getEgresoValor()->getResponsableEgresoValor()->getTipoDocumento()
        );
    }

    /**
     * 
     */
    public function getConceptoAsientoReemplazoPago() {
        
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $emContable
     * @param type $autorizacionContable
     */
    public function anularActionCustom($ordenPago, $emContable, $autorizacionContable) {

        /* @var $autorizacionContable  \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoReconocimientoEgresoValor */

        /* @var $ordenPago  \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoReconocimientoEgresoValor */

        // Seteo el EstadoEgresoValor
        $ordenPago->getReconocimientoEgresoValor()->getEgresoValor()->setEstadoEgresoValor(
                $emContable->getRepository('ADIFContableBundle:EgresoValor\EstadoEgresoValor')
                        ->findOneByCodigo(ConstanteEstadoEgresoValor::ESTADO_CON_AUTORIZACION_CONTABLE)
        );

        /* @var $reconocimientoEgresoValor \ADIF\ContableBundle\Entity\EgresoValor\ReconocimientoEgresoValor */
        $reconocimientoEgresoValor = $ordenPago->getReconocimientoEgresoValor();

        $reconocimientoEgresoValor->setEstadoReconocimientoEgresoValor($emContable->getRepository('ADIFContableBundle:EgresoValor\EstadoReconocimientoEgresoValor')
                        ->findOneByCodigo(ConstanteEstadoReconocimientoEgresoValor::ESTADO_GENERADO)
        );

        $autorizacionContable->setReconocimientoEgresoValor($reconocimientoEgresoValor);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/reconocimiento/form_pagar", name="ordenpagoreconocimiento_form_pagar")
     * @Method("POST")  
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig") 
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @Route("/reconocimiento/{id}/historico_general", name="ordenpagoreconocimiento_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoReconocimientoEgresoValor $ordenPago
     * @param array $resultArray
     * @return type
     */
    public function getHistoricoGeneralResultData($ordenPago, $resultArray) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoReconocimientoEgresoValor */

        $egresoValor = $ordenPago->getEgresoValor();

        $resultArray['egresoValor'] = $egresoValor;

        return $resultArray;
    }

}
