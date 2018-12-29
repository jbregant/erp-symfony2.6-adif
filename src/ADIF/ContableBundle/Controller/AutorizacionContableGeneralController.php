<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\AutorizacionContableBaseController;
use ADIF\ContableBundle\Entity\OrdenPagoGeneral;
use ADIF\ContableBundle\Form\OrdenPagoGeneralType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * AutorizacionContable controller.
 *
 * @Route("/autorizacioncontable")
 */
class AutorizacionContableGeneralController extends AutorizacionContableBaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Autorizaciones contables' => $this->generateUrl('autorizacioncontable')
        );
    }

    /**
     * Creates a new OrdenPagoGeneral entity.
     *
     * @Route("/general/insertar", name="autorizacioncontablegeneral_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:AutorizacionContable:new.html.twig")
     */
    public function createAction(Request $request) {
        return parent::createAction($request);
    }

    /**
     * Creates a form to create a OrdenPagoGeneral entity.
     *
     * @param OrdenPagoGeneral $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    public function createCreateForm(OrdenPagoGeneral $entity) {
        $form = $this->createForm(new OrdenPagoGeneralType(), $entity, array(
            'action' => $this->generateUrl('autorizacioncontablegeneral_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new OrdenPagoGeneral entity.
     *
     * @Route("/general/crear", name="autorizacioncontablegeneral_new")
     * @Method("POST")
     * @Template("ADIFContableBundle:AutorizacionContable:new.html.twig")
     */
    public function newAction(Request $request) {
        return parent::newAction($request);
    }

    /**
     * Anula a OrdenPagoGeneral entity.
     *
     * @Route("/general/anular/{id}", name="autorizacioncontablegeneral_anular")
     * @Method("GET")
     */
    public function anularAction($id) {
        return parent::anularAction($id);
    }

    /**
     * Visa a OrdenPagoGeneral entity.
     *
     * @Route("/general/visar/{id}", name="autorizacioncontablegeneral_visar")
     * @Method("GET")
     */
    public function visarAction($id) {
        return parent::visarAction($id);
    }

    /**
     * Print a OrdenPagoGeneral entity.
     *
     * @Route("/general/print/{id}", name="autorizacioncontablegeneral_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {
        return parent::printAction($id);
    }

    /**
     * 
     * @return OrdenPagoGeneral
     */
    public function getOP() {
        return new OrdenPagoGeneral();
    }

    /**
     * 
     * @param type $ordenPago
     * @return type
     */
    public function getConceptoCreacion($ordenPago) {
        return "Pago de " . $ordenPago->getConceptoOrdenPago();
    }

    /**
     * 
     * @return string
     */
    public function getClassName() {
        return 'ADIFContableBundle:OrdenPagoGeneral';
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $comprobante
     */
    public function setBeneficiarioCustom($ordenPago, $comprobante) {
        $ordenPago->setProveedor($comprobante->getProveedor());
    }

    /**
     * 
     * @param type $ordenPago
     * @param type $em
     */
    public function anularActionCustom($ordenPago, $em) {

        parent::clonar($ordenPago, $em);
    }

}
