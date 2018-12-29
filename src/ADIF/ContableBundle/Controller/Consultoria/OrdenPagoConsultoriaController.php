<?php

namespace ADIF\ContableBundle\Controller\Consultoria;

use ADIF\ContableBundle\Controller\OrdenPagoBaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Controller\Consultoria\ComprobanteRetencionImpuestoConsultoriaController;

/**
 * OrdenPagoConsultoria controller.
 *
 * @Route("/ordenpago")
 */
class OrdenPagoConsultoriaController extends OrdenPagoBaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => ''
        );
    }

    /**
     * Finds and displays a OrdenPagoConsultoria entity.
     *
     * @Route("/consultoria/{id}", name="ordenpagoconsultoria_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:OrdenPago:show.html.twig")
     */
    public function showAction($id) {

        return parent::showAction($id);
    }

    /**
     * Pagar autorizacion contable
     *
     * @Route("/consultoria/pagar", name="ordenpagoconsultoria_pagar")
     * @Method("POST")   
     */
    public function pagarAction(Request $request) {

        return parent::pagarAction($request);
    }

    /**
     * Print an OrdenPago entity.
     *
     * @Route("/consultoria/print/{id}", name="ordenpagoconsultoria_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
//        return parent::printAction($id);
        return parent::printOPCompletaAction($id);
    }

    /**
     * Reemplazar pago de la OrdenPago
     *
     * @Route("/consultoria/reemplazar_pago", name="ordenpagoconsultoria_reemplazar_pago")
     * @Method("POST")   
     */
    public function reemplazarPagoAction(Request $request) {
        return parent::reemplazarPagoAction($request);
    }

    /**
     * Anular OrdenPago
     *
     * @Route("/consultoria/{id}/anular", name="ordenpagoconsultoria_anular")
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
        return 'ADIFContableBundle:Consultoria\OrdenPagoConsultoria';
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
                        ->generarAsientoPagoConsultor($ordenPago, $user, $esContraasiento);
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $user
     * @return type
     */
    public function generarAsientoContablePagar($ordenPago, $user) {

        return $this->get('adif.asiento_service')
                        ->generarAsientoPagoConsultor($ordenPago, $user);
    }

    /**
     * 
     * @return string
     */
    public function getPathReemplazarPago() {
        return 'ordenpago/consultoria';
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    public function getDatosProveedor($entity) {

        return array(
            'nombre' => $entity->getContrato()->getConsultor()->getRazonSocial(),
            'labelNombre' => 'Razon Social',
            'identificacion' => $entity->getContrato()->getConsultor()->getCuit(),
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
                        ->findOneByCodigo('PAGO_CONTRATO_LOCACION_SERVICIO');
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
     * @Route("/consultoria/form_pagar", name="ordenpagoconsultoria_form_pagar")
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
        return new ComprobanteRetencionImpuestoConsultoriaController();
    }

//    /**
//     * Print an OrdenPago entity.
//     *
//     * @Route("/consultoria/printOPCompleta/{id}", name="ordenpagoconsultoria_print_completa")
//     * @Method("GET")
//     * @Template()
//     */
//    public function printOPCompletaAction($id) {
//        return parent::printOPCompletaAction($id);
//    }

    /**
     * 
     * @Route("/consultoria/{id}/historico_general", name="ordenpagoconsultoria_historico_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:HistoricoOrdenPago:historico.html.twig")
     */
    public function showHistoricoGeneralAction($id) {
        return parent::showHistoricoGeneralAction($id);
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria $ordenPago
     * @param type $resultArray
     * @return type
     */
    public function getHistoricoGeneralResultData($ordenPago, $resultArray) {

        /* @var $ordenPago \ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria */

        $contrato = $ordenPago->getContrato();

        $comprobantes = $ordenPago->getComprobantes();

        $anticipos = $ordenPago->getAnticipos();

        $resultArray['contratoConsultoria'] = $contrato;
        $resultArray['comprobantes'] = $comprobantes;
        $resultArray['anticipos'] = $anticipos;

        return $resultArray;
    }

    /**
     * @Route("/consultoria/generarAsientos/", name="ordenpagoconsultoria_asientos")
     * @Method("PUT|GET")     
     */
    public function generarAsientosConsultoria() {

        gc_enable();

        $parcial = false;

        $offset = 0;
        $limit = 20;
        $i = 1;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $opsConsultoria = $em->getRepository('ADIFContableBundle:Consultoria\OrdenPagoConsultoria')
                ->createQueryBuilder('opc')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->orderBy('opc.id', 'asc')
                ->getQuery()
                ->getResult();

        $offset = $limit * $i;
        $i++;
        while (count($opsConsultoria) > 0) {
            /* @var $opConsultoria \ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria */
            foreach ($opsConsultoria as $opConsultoria) {
                $this->get('adif.asiento_service')->generarAsientoPagoConsultor($opConsultoria, $this->getUser());
            }
            unset($opsConsultoria);
            $em->flush();
            $em->clear();
            gc_collect_cycles();
            $opsConsultoria = $em->getRepository('ADIFContableBundle:Consultoria\OrdenPagoConsultoria')
                    ->createQueryBuilder('opc')
                    ->setFirstResult($offset)
                    ->setMaxResults($limit)
                    ->orderBy('opc.id', 'asc')
                    ->getQuery()
                    ->getResult();
            $offset = $limit * $i;
            $i++;
        }
        unset($opsConsultoria);
        $em->clear();
        unset($em);
        gc_collect_cycles();

        if (!$parcial) {
            $this->get('session')->getFlashBag()->add('success', 'Generacion de asientos de OP de Consultoria exitosa');
        }

        return $this->redirect($this->generateUrl('contratoconsultoria'));
    }

}
