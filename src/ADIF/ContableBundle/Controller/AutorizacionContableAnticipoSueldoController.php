<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\BaseBundle\Session\EmpresaSession;

/**
 * AutorizacionContable controller.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContableAnticipoSueldoController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Anula a OrdenPagoSueldo entity.
     *
     * @Route("/anticiposueldo/anular/{id}", name="autorizacioncontableanticiposueldo_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoSueldo entity.
     *
     * @Route("/anticiposueldo/visar/{id}", name="autorizacioncontableanticiposueldo_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPagoSueldo entity.
     *
     * @Route("/anticiposueldo/print/{id}", name="autorizacioncontableanticiposueldo_print")
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
        return 'ADIFContableBundle:OrdenPagoAnticipoSueldo';
    }

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function printHTMLAction($ordenPago) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPagoAnticipoSueldo */

        $arrayResult['op'] = $ordenPago;
        
        $empresaSession = EmpresaSession::getInstance();
        $idEmpresa = $empresaSession->getIdEmpresa();
        
        $arrayResult['idEmpresa'] = $idEmpresa;

        if ($ordenPago->getBeneficiario() != null) {
            $arrayResult['razonSocial'] = $ordenPago->getBeneficiario()->getRazonSocial();
            $arrayResult['tipoDocumento'] = $ordenPago->getBeneficiario()->getTipoDocumento();
            $arrayResult['nroDocumento'] = $ordenPago->getBeneficiario()->getNroDocumento();
            $arrayResult['domicilio'] = $ordenPago->getBeneficiario()->getDomicilio();
            $arrayResult['localidad'] = $ordenPago->getBeneficiario()->getLocalidad();
        }

        return $this->renderView('ADIFContableBundle:AutorizacionContable:print.show.html.twig', $arrayResult);
    }

}
