<?php

namespace ADIF\ContableBundle\Controller\EgresoValor;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoEgresoValor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * AutorizacionContable controller.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContableReconocimientoController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Anula a OrdenPagoReconocimientoEgresoValor entity.
     *
     * @Route("/reconocimientovalor/anular/{id}", name="autorizacioncontablereconocimiento_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoReconocimientoEgresoValor entity.
     *
     * @Route("/reconocimientovalor/visar/{id}", name="autorizacioncontablereconocimiento_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPagoReconocimientoEgresoValor entity.
     *
     * @Route("/reconocimientovalor/print/{id}", name="autorizacioncontablereconocimiento_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    public function getClassName() {
        return 'ADIFContableBundle:EgresoValor\OrdenPagoReconocimientoEgresoValor';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $em
     */
    public function anularActionCustom($ordenPago, $em) {
        $ordenPago->getReconocimientoEgresoValor()->getEgresoValor()->setEstadoEgresoValor($em->getRepository('ADIFContableBundle:EgresoValor\EstadoEgresoValor')
                        ->findOneByCodigo(
                                ConstanteEstadoEgresoValor::ESTADO_ACTIVO));
    }

}
