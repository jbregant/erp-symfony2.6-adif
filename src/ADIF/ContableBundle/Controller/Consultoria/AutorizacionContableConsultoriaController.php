<?php

namespace ADIF\ContableBundle\Controller\Consultoria;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria;
use Symfony\Component\HttpFoundation\Request;
use ADIF\ContableBundle\Form\Consultoria\OrdenPagoConsultoriaType;

/**
 * AutorizacionContable controller.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContableConsultoriaController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Creates a new OrdenPagoConsultoria entity.
     *
     * @Route("/consultoria/insertar", name="autorizacioncontableconsultoria_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:AutorizacionContable:new.html.twig")
     */
    public function createAction(Request $request) {
        return parent::createAction($request);
    }

    /**
     * Creates a form to create a OrdenPagoConsultoria entity.
     *
     * @param OrdenPagoConsultoria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    public function createCreateForm(OrdenPagoConsultoria $entity) {
        $form = $this->createForm(new OrdenPagoConsultoriaType($this->getDoctrine()->getManager($this->getEntityManager())), $entity, array(
            'action' => $this->generateUrl('autorizacioncontableconsultoria_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new OrdenPagoConsultoria entity.
     *
     * @Route("/consultoria/crear", name="autorizacioncontableconsultoria_new")
     * @Method("POST")
     * @Template("ADIFContableBundle:AutorizacionContable:new.html.twig")
     */
    public function newAction(Request $request) {

        return parent::newAction($request);
    }

    /**
     * Anula a OrdenPagoConsultoria entity.
     *
     * @Route("/consultoria/anular/{id}", name="autorizacioncontableconsultoria_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoConsultoria entity.
     *
     * @Route("/consultoria/visar/{id}", name="autorizacioncontableconsultoria_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPago entity.
     *
     * @Route("/consultoria/print/{id}", name="autorizacioncontableconsultoria_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    public function getOP() {
        return new OrdenPagoConsultoria();
    }

    public function getComprobantesClassName() {
        return 'ADIFContableBundle:Consultoria\ComprobanteConsultoria';
    }

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function getConceptoCreacion($ordenPago) {
        return "Pago del Contrato de consultoria n&ordm; " . $ordenPago->getContrato();
    }

    public function getClassName() {
        return 'ADIFContableBundle:Consultoria\OrdenPagoConsultoria';
    }

    public function generarRetenciones($ordenPago) {
        return $this->get('adif.retenciones_service')->generarComprobantesRetencionConsultoria($ordenPago);
    }

    /**
     * 
     * @param type $em
     * @param OrdenPagoConsultoria $ordenPago
     * @param type $request
     * @return int
     */
    public function newActionCustom($em, $ordenPago, $request) {
        $ids_anticipos = ($request->request->get('ids_anticipos') == null) ? array() : $request->request->get('ids_anticipos');
        $error = '';
        if (($ordenPago->getTotalBruto() > 400000) && ($ordenPago->getProveedor()->getDatosImpositivos()->getCondicionIVA()
                        ->getDenominacionTipoResponsable() == ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO)) {
            $error .= 'El monto total supera el l&iacute;mite de pago a monotributistas. ';
        }

        foreach ($ids_anticipos as $ids_anticipo) {
            /* @var $anticipo \ADIF\ContableBundle\Entity\AnticipoContratoConsultoria */
            $anticipo = $em->getRepository('ADIFContableBundle:AnticipoContratoConsultoria')
                    ->find($ids_anticipo);
			
			if ($anticipo) {
				$anticipo->setOrdenPagoCancelada($ordenPago);
				/* @var $ordenPago OrdenPagoConsultoria */
				$ordenPago->addAnticipo($anticipo);
			}
			
			// Me fijo si el "anticipo" no es un comprobante de credito y lo agrego para la generacion de la AC/OP  - "Comprobantes cancelados"
			$comprobante = $em->getRepository($this->getComprobantesClassName())
                    ->find($ids_anticipo);
			
			if ($comprobante) {
				$ordenPago->addComprobante($comprobante);
			}
        }

        $arrayPeriodos = [];

        /* @var $comprobante \ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria */
        foreach ($ordenPago->getComprobantes() as $comprobante) {
            $arrayPeriodos[] = $comprobante->getPeriodo();
        }

        $periodo = implode('-', $arrayPeriodos);

        $ordenPago->setPeriodo($periodo);

        if ($ordenPago->getMontoNeto() < 0) {
            $error .= 'El monto de los anticipos supera al de los comprobantes menos las retenciones. ';
        }
        if ($error != '') {
            return array('error' => $error);
        } else {
            return 0;
        }
    }

    public function getPathComprobantes() {
        return 'comprobante_consultoria';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $comprobante
     */
    public function setBeneficiarioCustom($ordenPago, $comprobante) {
        $ordenPago->setContrato($comprobante->getContrato());
    }

    public function anularActionCustom($ordenPago, $em) {

        parent::clonar($ordenPago, $em);
    }

}
