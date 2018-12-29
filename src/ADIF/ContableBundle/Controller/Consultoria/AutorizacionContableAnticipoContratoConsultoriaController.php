<?php

namespace ADIF\ContableBundle\Controller\Consultoria;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * AutorizacionContable controller.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContableAnticipoContratoConsultoriaController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Anula a OrdenPagoProveedor entity.
     *
     * @Route("/anticipocontratoconsultoria/anular/{id}", name="autorizacioncontableanticipocontratoconsultoria_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoProveedor entity.
     *
     * @Route("/anticipocontratoconsultoria/visar/{id}", name="autorizacioncontableanticipocontratoconsultoria_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPagoProveedor entity.
     *
     * @Route("/anticipocontratoconsultoria/print/{id}", name="autorizacioncontableanticipocontratoconsultoria_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    public function getClassName() {
        return 'ADIFContableBundle:OrdenPagoAnticipoContratoConsultoria';
    }

}
