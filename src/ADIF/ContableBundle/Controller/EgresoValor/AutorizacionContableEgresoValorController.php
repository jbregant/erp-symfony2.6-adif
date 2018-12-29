<?php

namespace ADIF\ContableBundle\Controller\EgresoValor;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoEgresoValor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ADIF\BaseBundle\Session\EmpresaSession;

/**
 * AutorizacionContable controller.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContableEgresoValorController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Anula a OrdenPagoEgresoValor entity.
     *
     * @Route("/egresovalor/anular/{id}", name="autorizacioncontableegresovalor_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoEgresoValor entity.
     *
     * @Route("/egresovalor/visar/{id}", name="autorizacioncontableegresovalor_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPagoEgresoValor entity.
     *
     * @Route("/egresovalor/print/{id}", name="autorizacioncontableegresovalor_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    public function getClassName() {
        return 'ADIFContableBundle:EgresoValor\OrdenPagoEgresoValor';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $em
     */
    public function anularActionCustom($ordenPago, $em) {
        $ultimaOrdenPagoActiva = $em->getRepository('ADIFContableBundle:EgresoValor\OrdenPagoEgresoValor')->getUltimaReposicionPaga($ordenPago->getEgresoValor());
        if ($ultimaOrdenPagoActiva != null) {
            $ordenPago->getEgresoValor()->setResponsableEgresoValor($ultimaOrdenPagoActiva[0]->getReposicionEgresoValor()->getResponsableEgresoValor());
            $ordenPago->getEgresoValor()->setEstadoEgresoValor($em->getRepository('ADIFContableBundle:EgresoValor\EstadoEgresoValor')
                            ->findOneByCodigo(
                                    ConstanteEstadoEgresoValor::ESTADO_ACTIVO));
        } else {
            $ordenPago->getEgresoValor()->setEstadoEgresoValor($em->getRepository('ADIFContableBundle:EgresoValor\EstadoEgresoValor')
                            ->findOneByCodigo(
                                    ConstanteEstadoEgresoValor::ESTADO_INGRESADO));
        }
    }

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function printHTMLAction($ordenPago) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor */

        $arrayResult['op'] = $ordenPago;

        $egresoValor = $ordenPago->getEgresoValor();
        
        $empresaSession = EmpresaSession::getInstance();
        $idEmpresa = $empresaSession->getIdEmpresa();
        
        $arrayResult['idEmpresa'] = $idEmpresa;

        if ($ordenPago->getBeneficiario() != null) {
            $arrayResult['razonSocial'] = $ordenPago->getBeneficiario()->getRazonSocial();
            $arrayResult['concepto'] = $egresoValor->getTipoEgresoValor();
            $arrayResult['beneficiario'] = $egresoValor->getResponsableEgresoValor();
            $arrayResult['origen'] = $egresoValor->getGerencia();
        }

        return $this->renderView('ADIFContableBundle:EgresoValor\AutorizacionContableEgresoValor:print.show.html.twig', $arrayResult);
    }

}
