<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use ADIF\ContableBundle\Entity\OrdenPagoPagoParcial;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * AutorizacionContable controller.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContablePagoParcialController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Anula a OrdenPagoPagoParcial entity.
     *
     * @Route("/pagoparcial/anular/{id}", name="autorizacioncontablepagoparcial_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoPagoParcial entity.
     *
     * @Route("/pagoparcial/visar/{id}", name="autorizacioncontablepagoparcial_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPagoPagoParcial entity.
     *
     * @Route("/pagoparcial/print/{id}", name="autorizacioncontablepagoparcial_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * 
     * @return OrdenPagoPagoParcial
     */
    public function getOP() {
        return new OrdenPagoPagoParcial();
    }

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function getConceptoCreacion($ordenPago) {

        /* @var $ordenPago OrdenPagoPagoParcial */

        return $ordenPago->getConcepto();
    }

    /**
     * 
     * @return string
     */
    public function getClassName() {
        return 'ADIFContableBundle:OrdenPagoPagoParcial';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $em
     */
    public function anularActionCustom($ordenPago, $em) {
        
		$pagoParcial = $ordenPago->getPagoParcial();
		
		$pagoParcial->setAnulado(true);
		
		$em->persist($pagoParcial);
    }

}
