<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * AutorizacionContableCargasSociales controller.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContableCargasSocialesController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Anula a OrdenPagoCargasSociales entity.
     *
     * @Route("/cargassociales/anular/{id}", name="autorizacioncontablecargassociales_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoCargasSociales entity.
     *
     * @Route("/cargassociales/visar/{id}", name="autorizacioncontablecargassociales_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPagoCargasSociales entity.
     *
     * @Route("/cargassociales/print/{id}", name="autorizacioncontablecargassociales_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    public function getClassName() {
        return 'ADIFContableBundle:OrdenPagoCargasSociales';
    }

}
