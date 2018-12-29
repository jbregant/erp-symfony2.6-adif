<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonRetencionLiquidacion;
use ADIF\ContableBundle\Entity\OrdenPagoRenglonRetencionLiquidacion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * AutorizacionContableRenglonRetencionLiquidacionController.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContableRenglonRetencionLiquidacionController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Anula a OrdenPagoRenglonRetencionLiquidacion entity.
     *
     * @Route("/renglonesretencionliquidacion/anular/{id}", name="autorizacioncontablerenglonretencionliquidacion_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoRenglonRetencionLiquidacion entity.
     *
     * @Route("/renglonesretencionliquidacion/visar/{id}", name="autorizacioncontablerenglonretencionliquidacion_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPagoRenglonRetencionLiquidacion entity.
     *
     * @Route("/renglonesretencionliquidacion/print/{id}", name="autorizacioncontablerenglonretencionliquidacion_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * 
     * @return string
     */
    public function getClassName() {
        return 'ADIFContableBundle:OrdenPagoRenglonRetencionLiquidacion';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $em
     */
    public function anularActionCustom($ordenPago, $em) {

        /* @var $ordenPago OrdenPagoRenglonRetencionLiquidacion */

        foreach ($ordenPago->getRenglonesRetencionLiquidacion() as $renglonRetencionLiquidacion) {

            $renglonRetencionLiquidacion
                    ->setEstadoRenglonRetencionLiquidacion(
                            $em->getRepository('ADIFContableBundle:EstadoRenglonRetencionLiquidacion')
                            ->findOneByDenominacion(ConstanteEstadoRenglonRetencionLiquidacion::PENDIENTE)
            );
        }

        $em->flush();
    }

}
