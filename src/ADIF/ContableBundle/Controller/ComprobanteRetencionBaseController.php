<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use mPDF;
use ADIF\ContableBundle\Entity\Constantes\ConstanteRegimenRetencion;
use DateTime;

/**
 * OrdenPago controller.
 *
 * @Route("/comprobanteretencion")
 */
class ComprobanteRetencionBaseController extends BaseController {

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);
    }

    /**
     * @Route("/print/{id}", name="comprobanteretencion_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $comprobanteRetencionImpuesto = $em->getRepository($this->getClassName())->find($id);

        if (!$comprobanteRetencionImpuesto) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ' . $this->getClassName());
        }

        $html = '<html><head><style type="text/css">' . $this->renderView('::PDF/mpdf.default.css.twig') . '</style></head><body>';
        $html .= $this->printHTMLAction($comprobanteRetencionImpuesto);
        $html .= '</body></html>';

        $filename = 'retencion_' . $comprobanteRetencionImpuesto->getNumeroComprobanteRetencion() . '.pdf';

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);

        $mpdfService->WriteHTML($html);

        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    public function printHTMLAction($comprobanteRetencionImpuesto) {

        $fechaFirmaLaura = new DateTime('2016/10/12'); // fecha a partir de la cual Laura Aguirre firma los certificados de retencion
        if ($comprobanteRetencionImpuesto->getOrdenPago()->getFechaCreacion()->getTimestamp() < $fechaFirmaLaura->getTimestamp()) { // certificados de retenciones SIN la firma de laura

    		$esIibbCaba = ($comprobanteRetencionImpuesto->getRegimenRetencion()->getCodigo() == ConstanteRegimenRetencion::CODIGO_IIBB_CABA);
    		$alicuotaIibbCaba = 0;
    		$baseImponibleComprobanteRetencionImpuesto = $comprobanteRetencionImpuesto->getBaseImponible();
    		if ($esIibbCaba) {
    			if (
    					$comprobanteRetencionImpuesto->getOrdenPago() != null &&
    					$comprobanteRetencionImpuesto->getOrdenPago()->getBeneficiario() != null &&
    					$comprobanteRetencionImpuesto->getOrdenPago()->getBeneficiario()->getIibbCaba() != null &&
    					$comprobanteRetencionImpuesto->getOrdenPago()->getBeneficiario()->getIibbCaba()->getAlicuota() != null ) {

    					$alicuotaIibbCaba = $comprobanteRetencionImpuesto->getOrdenPago()->getBeneficiario()->getIibbCaba()->getAlicuota();
    			}
    		}


            return $this->renderView(
    			'ADIFContableBundle:ComprobanteRetencionImpuesto:show.print.html.twig', [
                        'cr' => $comprobanteRetencionImpuesto,
                        'comprobantes' => $this->getComprobantesAplicanImpuesto($comprobanteRetencionImpuesto),
    					'esIibbCaba' => $esIibbCaba,
    					'alicuotaIibbCaba' => $alicuotaIibbCaba,
    					'baseImponibleComprobanteRetencionImpuesto' => $baseImponibleComprobanteRetencionImpuesto
    			]
            );
        } else { //certificados de retencion CON la firma de laura

            $clienteProveedor = '';
            $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

            if ( is_a($comprobanteRetencionImpuesto, 'ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoConsultoria') == false && $comprobanteRetencionImpuesto->getProveedor() != null && $comprobanteRetencionImpuesto->getProveedor()->getClienteProveedor() != null ) {
                $clienteProveedor = $emCompras->getRepository('ADIFComprasBundle:ClienteProveedor')->find($comprobanteRetencionImpuesto->getProveedor()->getClienteProveedor()->getId());
            } else {
                if ( $comprobanteRetencionImpuesto->getProveedor() == null && $comprobanteRetencionImpuesto->getOrdenPago()->getBeneficiario() ) {
                    $clienteProveedor = $emCompras->getRepository('ADIFComprasBundle:ClienteProveedor')->find($comprobanteRetencionImpuesto->getOrdenPago()->getBeneficiario()->getClienteProveedor()->getId());
                } else {
                    if (is_a($comprobanteRetencionImpuesto, 'ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoConsultoria')) {
                        $clienteProveedor['esExtranjero'] = false;

                    } else {
                    throw $this->createNotFoundException('No se puede encontrar la entidad clienteProveedor ('.$comprobanteRetencionImpuesto->getOrdenPago().').');
                    }
                }
            }

            $baseImponibleComprobanteRetencionImpuesto = $comprobanteRetencionImpuesto->getBaseImponible();

            return $this->renderView(
                'ADIFContableBundle:ComprobanteRetencionImpuesto:show.print.laura.html.twig', [
                        'cr' => $comprobanteRetencionImpuesto,
                        'cp' => $clienteProveedor,
                        'comprobantes' => $this->getComprobantesAplicanImpuesto($comprobanteRetencionImpuesto),
                        'baseImponibleComprobanteRetencionImpuesto' => $baseImponibleComprobanteRetencionImpuesto
                ]
            );
        }
    }
}
