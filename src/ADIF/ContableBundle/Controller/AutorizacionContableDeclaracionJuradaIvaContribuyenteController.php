<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use ADIF\ContableBundle\Entity\AdifDatos;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * AutorizacionContableDeclaracionJuradaIvaContribuyenteController.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContableDeclaracionJuradaIvaContribuyenteController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Anula a OrdenPagoDeclaracionJuradaIvaContribuyente entity.
     *
     * @Route("/declaracionesjuradasivacontribuyente/anular/{id}", name="autorizacioncontabledeclaracionjuradaivacontribuyente_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoDeclaracionJuradaIvaContribuyente entity.
     *
     * @Route("/declaracionesjuradasivacontribuyente/visar/{id}", name="autorizacioncontabledeclaracionjuradaivacontribuyente_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPagoDeclaracionJuradaIvaContribuyente entity.
     *
     * @Route("/declaracionesjuradasivacontribuyente/print/{id}", name="autorizacioncontabledeclaracionjuradaivacontribuyente_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    public function getClassName() {
        return 'ADIFContableBundle:OrdenPagoDeclaracionJuradaIvaContribuyente';
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

}
