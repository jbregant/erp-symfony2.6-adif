<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use ADIF\ContableBundle\Entity\AdifDatos;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoPagoACuenta;

/**
 * AutorizacionContableDeclaracionJuradaController.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContableDeclaracionJuradaController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Anula a OrdenPagoDeclaracionJurada entity.
     *
     * @Route("/declaracion_jurada/anular/{id}", name="autorizacioncontabledeclaracionjurada_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoDeclaracionJurada entity.
     *
     * @Route("/declaracion_jurada/visar/{id}", name="autorizacioncontabledeclaracionjurada_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPagoDeclaracionJurada entity.
     *
     * @Route("/declaracion_jurada/print/{id}", name="autorizacioncontabledeclaracionjurada_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    public function getClassName() {
        return 'ADIFContableBundle:OrdenPagoDeclaracionJurada';
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosBeneficiaro() {
        return array(
            'razonSocial' => AdifDatos::RAZON_SOCIAL,
            'cuit' => AdifDatos::CUIT
        );
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $em
     */
    public function anularActionCustom($ordenPago, $em) {

        $declaracionJurada = $ordenPago->getDeclaracionJurada();

        foreach ($declaracionJurada->getRenglonesDeclaracionJurada() as $renglonDDJJ) {
            $renglonDDJJ->setEstadoRenglonDeclaracionJurada(
                    $em->getRepository('ADIFContableBundle:EstadoRenglonDeclaracionJurada')
                            ->findOneByDenominacion(ConstanteEstadoRenglonDeclaracionJurada::PENDIENTE));
        }

        /* @var $pagoACuenta \ADIF\ContableBundle\Entity\PagoACuenta */
        foreach ($declaracionJurada->getPagosACuenta() as $pagoACuenta) {
            $pagoACuenta->setEstadoPagoACuenta($em->getRepository('ADIFContableBundle:EstadoPagoACuenta')
                            ->findOneByDenominacion(ConstanteEstadoPagoACuenta::PENDIENTE));
        }

        $em->remove($declaracionJurada);
        $em->flush();
    }

}
