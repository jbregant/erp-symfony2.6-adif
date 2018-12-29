<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Controller\ComprobanteRetencionImpuestoComprasController;

/**
 * OrdenPagoComprobante controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoComprobanteController extends OrdenPagoBaseController {

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
     * @Route("/comprobante/{id}", name="ordenpagocomprobante_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/comprobante/pagar", name="ordenpagocomprobante_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print an OrdenPago entity.
     *
     * @Route("/comprobante/print/{id}", name="ordenpagocomprobante_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printOPCompletaAction($id);
//        return parent::printAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPago
     *
     * @Route("/comprobante/reemplazar_pago", name="ordenpagocomprobante_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/comprobante/{id}/anular", name="ordenpagocomprobante_anular")
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
        return 'ADIFContableBundle:OrdenPagoComprobante';
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
                        ->generarAsientoPagoProveedor($ordenPago, $user, $esContraasiento, false);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {

        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoProveedor($ordenPago, $user, false, false);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/comprobante';
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {
        /* @var $entity  \ADIF\ContableBundle\Entity\OrdenPagoComprobante */

        return array(
            'nombre' => $entity->getProveedor()->getRazonSocial(),
            'labelNombre' => 'Razon Social',
            'identificacion' => $entity->getProveedor()->getCUIT(),
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
     * @Route("/comprobante/form_pagar", name="ordenpagocomprobante_form_pagar")
     * @Method("POST")   
     * @Template("ADIFContableBundle:OrdenPago:pagar_form.html.twig")
     */
    public function getFormPagarAction(Request $request) {

        return parent::getFormPagarAction($request);
    }

    /**
     * 
     * @param type $idOrdenPago
     * @return int
     */
    public function getCantidadAutorizacionesContablesByBeneficiario($idOrdenPago) {

        $emContable = $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ordenPago = $emContable->getRepository($this->getClassName())
                ->find($idOrdenPago);

        return $emContable->getRepository($this->getClassName())
                        ->getCantidadAutorizacionesContablesByBeneficiario($ordenPago);
    }

    public function getRetencionesController() {
        return new ComprobanteRetencionImpuestoComprasController();
    }

//    /**
//     * Print an OrdenPago entity.
//     *
//     * @Route("/comprobante/printOPCompleta/{id}", name="ordenpagocomprobante_print_completa")
//     * @Method("GET")
//     * @Template()
//     */
//    public function printOPCompletaAction($id) {
//        return parent::printOPCompletaAction($id);
//    }

    /**
     * 
     * @Route("/comprobante/{id}/historico_general", name="ordenpagocomprobante_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\OrdenPagoComprobante $ordenPago
     * @param type $resultArray
     * @return type
     */
    public function getHistoricoGeneralResultData($ordenPago, $resultArray) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPagoComprobante */

        $ordenCompra = $ordenPago->getOrdenCompra();
		
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

        $anticipos = $ordenPago->getAnticipos();

        $requerimiento = null;

        if ($ordenCompra != null) {

            $requerimiento = $ordenCompra->getRequerimiento();
        }

        $resultArray['requerimiento'] = $requerimiento;
        $resultArray['ordenCompra'] = $ordenCompra;
        $resultArray['comprobantes'] = $comprobantes;
        $resultArray['anticipos'] = $anticipos;

        return $resultArray;
    }

    /**
     * @Route("/comprobante/generarAsientos/", name="ordenpagocomprobante_asientos")
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
//        $ordenesPagoImportadas = $em->getRepository('ADIFContableBundle:OrdenPagoComprobante')
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
//            /* @var $ordenPagoImportada \ADIF\ContableBundle\Entity\OrdenPagoComprobante */
//            foreach ($ordenesPagoImportadas as $ordenPagoImportada) {
//                $this->get('adif.asiento_service')->generarAsientoPagoProveedor($ordenPagoImportada, $this->getUser(), false, false);
//            }
//            unset($ordenesPagoImportadas);
//            $em->flush();
//            $em->clear();
//            gc_collect_cycles();
//            $ordenesPagoImportadas = $em->getRepository('ADIFContableBundle:OrdenPagoComprobante')
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
//
//        if (!$parcial) {
//            $this->get('session')->getFlashBag()->add('success', 'Generacion de asientos de OP de compras exitosa');
//        }
        
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $ordenPago = $em->getRepository('ADIFContableBundle:OrdenPagoComprobante')->find(3724);
        
        $this->get('adif.asiento_service')->generarAsientoPagoProveedor($ordenPago, $this->getUser(), false, false);
        
        $em->flush();
        $em->clear();

        return $this->redirect($this->generateUrl('ordenpago'));
    }

}
