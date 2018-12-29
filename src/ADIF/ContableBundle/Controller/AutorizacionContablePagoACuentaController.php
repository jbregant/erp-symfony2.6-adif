<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonDeclaracionJurada;
use ADIF\BaseBundle\Session\EmpresaSession;

/**
 * AutorizacionContablePagoACuentaController.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContablePagoACuentaController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Anula a OrdenPagoPagoACuenta entity.
     *
     * @Route("/pago_a_cuenta/anular/{id}", name="autorizacioncontablepagoacuenta_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoPagoACuenta entity.
     *
     * @Route("/pago_a_cuenta/visar/{id}", name="autorizacioncontablepagoacuenta_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPagoPagoACuenta entity.
     *
     * @Route("/pago_a_cuenta/print/{id}", name="autorizacioncontablepagoacuenta_print")
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
        return 'ADIFContableBundle:OrdenPagoPagoACuenta';
    }

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function printHTMLAction($ordenPago) {

        $arrayResult['op'] = $ordenPago;
        
        $empresaSession = EmpresaSession::getInstance();
        $idEmpresa = $empresaSession->getIdEmpresa();
        
        $arrayResult['idEmpresa'] = $idEmpresa;

        if ($ordenPago->getBeneficiario() != null) {
            $arrayResult['razonSocial'] = 'AFIP';
            $arrayResult['tipoDocumento'] = 'CUIT';
            $arrayResult['nroDocumento'] = '33-69345023-9';
            $arrayResult['domicilio'] = 'YRIGOYEN HIPOLITO 370 Piso:4 Dpto:4752 1086';
            $arrayResult['localidad'] = 'CIUDAD AUTONOMA BUENOS AIRES';
        }

        return $this->renderView('ADIFContableBundle:AutorizacionContable:print.show.html.twig', $arrayResult);
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosBeneficiaro() {
        return array(
            'razonSocial' => 'AFIP',
            'cuit' => '33-69345023-9'
        );
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $em
     */
    public function anularActionCustom($ordenPago, $em) {

        $pagoACuenta = $ordenPago->getPagoACuenta();

        foreach ($pagoACuenta->getRenglonesDeclaracionJurada() as $renglon) {
            $renglon->setEstadoRenglonDeclaracionJurada(
                    $em->getRepository('ADIFContableBundle:EstadoRenglonDeclaracionJurada')
                            ->findOneByDenominacion(ConstanteEstadoRenglonDeclaracionJurada::PENDIENTE));
        }

        $em->remove($pagoACuenta);
        $em->flush();
    }

}
