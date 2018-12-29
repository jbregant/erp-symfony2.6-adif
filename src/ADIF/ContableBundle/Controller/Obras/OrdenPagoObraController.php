<?php

namespace ADIF\ContableBundle\Controller\Obras;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Controller\Obras\ComprobanteRetencionImpuestoObrasController;

/**
 * OrdenPagoComprobante controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoObraController extends OrdenPagoBaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Finds and displays a OrdenPagoComprobante entity.
     *
     * @Route("/obra/{id}", name="ordenpagoobra_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/obra/pagar", name="ordenpagoobra_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print a OrdenPagoObra entity.
     *
     * @Route("/obra/print/{id}", name="ordenpagoobra_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
//        return parent::printAction($id);
        return parent::printOPCompletaAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPagoObra
     *
     * @Route("/obra/reemplazar_pago", name="ordenpagoobra_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/obra/{id}/anular", name="ordenpagoobra_anular")
     * @Method("GET")   
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * 
     * @return string
     */
    public function getClassName() {
        return 'ADIFContableBundle:Obras\OrdenPagoObra';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @param type $esContraasiento
     * @return type
     */
    public function generarAsientoContableAnular($ordenPago, $user, $esContraasiento) {

        /* Genero el contraasiento */
        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoProveedor($ordenPago, $user, $esContraasiento, true);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {

        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoProveedor($ordenPago, $user, false, true);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/obra';
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {

        /* @var $entity  \ADIF\ContableBundle\Entity\OrdenPagoObra */

        return array(
            'nombre' => $entity->getProveedor()->getClienteProveedor()->getRazonSocial(),
            'labelNombre' => 'Razon Social',
            'identificacion' => $entity->getProveedor()->getClienteProveedor()->getCuit(),
            'labelIdentificacion' => 'CUIT'
        );
    }

    /**
     * 
     * @return type
     */
    public function getConceptoAsientoReemplazoPago() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        return $em->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                        ->findOneByCodigo('PAGO_PROVEEDORES');
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $emContable
     * @param type $autorizacionContable
     */
    public function anularActionCustom($ordenPago, $emContable, $autorizacionContable) {

        //parent::clonar($ordenPago, $emContable, $autorizacionContable);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/obra/form_pagar", name="ordenpagoobra_form_pagar")
     * @Method("POST")   
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    public function getRetencionesController() {
        return new ComprobanteRetencionImpuestoObrasController();
    }

//    /**
//     * Print an OrdenPago entity.
//     *
//     * @Route("/obra/printOPCompleta/{id}", name="ordenpagoobra_print_completa")
//     * @Method("GET")
//     * @Template()
//     */
//    public function printOPCompletaAction($id) {
//        return parent::printOPCompletaAction($id);
//    }
    /**
     * 
     * @Route("/obra/{id}/historico_general", name="ordenpagoobra_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\Obras\OrdenPagoObra $ordenPago
     * @param type $resultArray
     * @return type
     */
    public function getHistoricoGeneralResultData($ordenPago, $resultArray) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\Obras\OrdenPagoObra */

        $tramo = $ordenPago->getTramo();
		
		$comprobantes = array();
		if ($ordenPago->getEstaAnulada()) {
			$em = $this->getDoctrine()->getManager($this->getEntityManager());
			$ordenPagoLog = $em->getRepository('ADIFContableBundle:OrdenPagoLog')->findOneByOrdenPago($ordenPago);
			if ($ordenPagoLog) {
				$comprobantes = $ordenPagoLog->getComprobantes();
			}
		} else {
			$comprobantes = $ordenPago->getComprobantes();
		}

        $documentosFinancieros = [];

        foreach ($comprobantes as $comprobante) {
            $documentosFinancieros[] = $comprobante->getDocumentoFinanciero();
        }

        $anticipos = $ordenPago->getAnticipos();

        $resultArray['tramo'] = $tramo;
        $resultArray['documentosFinancieros'] = $documentosFinancieros;
        $resultArray['comprobantes'] = $comprobantes;
        $resultArray['anticipos'] = $anticipos;

        return $resultArray;
    }

    /**
     * @Route("/obra/generarAsientos/", name="ordenpagoobra_asientos")
     * @Method("PUT|GET")     
     */
    public function generarAsientosComprobantesObra() {

//        gc_enable();
//
//        $parcial = false;
//
//        $offset = 0;
//        $limit = 20;
//        $i = 1;
//
//        $em = $this->getDoctrine()->getManager($this->getEntityManager());
//        $ordenesPagoImportadas = $em->getRepository('ADIFContableBundle:Obras\OrdenPagoObra')
//                ->createQueryBuilder('op')
//                ->setFirstResult($offset)
//                ->setMaxResults($limit)
//                ->orderBy('op.id', 'asc')
//                ->getQuery()
//                ->getResult();
//
//        $offset = $limit * $i;
//        $i++;
//        while (count($ordenesPagoImportadas) > 0) {
//            /* @var $ordenPagoImportada \ADIF\ContableBundle\Entity\Obras\OrdenPagoObra */
//            foreach ($ordenesPagoImportadas as $ordenPagoImportada) {
//                $this->get('adif.asiento_service')->generarAsientoPagoProveedor($ordenPagoImportada, $this->getUser(), false, true);
//            }
//            unset($ordenesPagoImportadas);
//            $em->flush();
//            $em->clear();
//            gc_collect_cycles();
//            $ordenesPagoImportadas = $em->getRepository('ADIFContableBundle:Obras\OrdenPagoObra')
//                    ->createQueryBuilder('op')
//                    ->setFirstResult($offset)
//                    ->setMaxResults($limit)
//                    ->orderBy('op.id', 'asc')
//                    ->getQuery()
//                    ->getResult();
//            $offset = $limit * $i;
//            $i++;
//        }
//        unset($ordenesPagoImportadas);
//        $em->clear();
//        unset($em);
//        gc_collect_cycles();
        
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $em->clear();
        $ordenPago = $em->getRepository('ADIFContableBundle:Obras\OrdenPagoObra')->find(4257);
        $this->get('adif.asiento_service')->generarAsientoPagoProveedor($ordenPago, $this->getUser(), false, true);
        $em->flush();
        $em->clear();
        //die;

//        if (!$parcial) {
//            $this->get('session')->getFlashBag()->add('success', 'Generacion de asientos de OP de obras exitosa');
//        }

        return $this->redirect($this->generateUrl('ordenpago'));
    }

}
