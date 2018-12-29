<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\ComprobanteCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteJurisdiccion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Form\ComprobanteCompraCreateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use ADIF\ContableBundle\Entity\NotaCreditoComprobante;
use ADIF\ContableBundle\Entity\RenglonComprobanteCompraCentroDeCosto;
use ADIF\ContableBundle\Form\Obras\ComprobanteObraCreateType;
use ADIF\ContableBundle\Entity\Obras\ComprobanteObra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteObra;

/**
 * NotaCredito controller.
 *
 * @Route("/notascredito")
 */
class NotaCreditoController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Notas crédito' => ''//$this->generateUrl('notascredito')
        );
    }

    //COMPRAS///////////////////////////////////////////////////////////////////

    /**
     * Creates a new ComprobanteCompra entity.
     *
     * @Route("/compras/insertar", name="notascredito_compras_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:NotaCredito:new.compras.html.twig")
     */
    public function createComprasAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $em->clear();
        $em_compras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $requestComprobanteCompra = $request->request->get('adif_contablebundle_comprobantecompra');

        $requestRenglonComprobanteCompra = $requestComprobanteCompra["renglonesComprobante"];

        $tipoComprobante = $request->request->get('adif_contablebundle_comprobantecompra', false)['tipoComprobante'];

        $comprobanteCompra = ConstanteTipoComprobanteCompra::getSubclass($tipoComprobante);

        /* @var $comprobanteCompra \ADIF\ContableBundle\Entity\NotaCredito */

        $form = $this->createCreateComprasForm($comprobanteCompra);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $comprobanteCompra->setEstadoComprobante($em->getRepository('ADIFContableBundle:EstadoComprobante')
                            ->find(EstadoComprobante::__ESTADO_CANCELADO));

            $notasCreditoComprobante = array();

            foreach ($comprobanteCompra->getRenglonesComprobante() as $index => $renglonComprobanteCompra) {

                /* @var $renglonComprobanteCompra \ADIF\ContableBundle\Entity\RenglonComprobanteCompra */

                $renglonComprobanteActual = $requestRenglonComprobanteCompra[++$index];

                if (!isset($notasCreditoComprobante[$renglonComprobanteActual['idComprobante']])) {
                    $notasCreditoComprobante[$renglonComprobanteActual['idComprobante']] = 0;
                }

                $notasCreditoComprobante[$renglonComprobanteActual['idComprobante']] += $renglonComprobanteCompra->getMontoBruto();

                $renglonOrdenCompra = $em_compras->getRepository('ADIFComprasBundle:RenglonOrdenCompra')
                        ->find($renglonComprobanteCompra->getIdRenglonOrdenCompra());

                $renglonComprobanteCompra->setBienEconomico($renglonOrdenCompra->getBienEconomico());

                /* @var $renglonComprobanteCompraCentroDeCosto \ADIF\ContableBundle\Entity\RenglonComprobanteCompraCentroDeCosto */
                $renglonComprobante = $em->getRepository('ADIFContableBundle:RenglonComprobanteCompra')
                        ->createQueryBuilder('r')
                        ->select('partial r.{id}, partial rcc.{id, centroDeCosto, porcentaje, renglonComprobanteCompra}')
                        ->leftJoin('r.renglonComprobanteCompraCentrosDeCosto', 'rcc')
                        ->where('r.id = :id')
                        ->setParameter('id', $renglonComprobanteActual['idRenglonComprobante'])
                        ->getQuery()
                        ->getOneOrNullResult();

                foreach ($renglonComprobante->getRenglonComprobanteCompraCentrosDeCosto() as $renglonComprobanteCompraCentroDeCosto) {
                    $renglonCentroCosto = new RenglonComprobanteCompraCentroDeCosto();
                    $renglonCentroCosto->setCentroDeCosto($renglonComprobanteCompraCentroDeCosto->getCentroDeCosto());
                    $renglonCentroCosto->setPorcentaje($renglonComprobanteCompraCentroDeCosto->getPorcentaje());
                    $renglonCentroCosto->setRenglonComprobanteCompra($renglonComprobanteCompraCentroDeCosto->getRenglonComprobanteCompra());
                    $renglonComprobanteCompra->addRenglonComprobanteCompraCentrosDeCosto($renglonCentroCosto);
                }

                //
                $renglonComprobanteCompra->setRenglonAcreditado($renglonComprobante);
                //
            }

            foreach ($notasCreditoComprobante as $index => $monto) {
                $notaCreditoComprobante = new NotaCreditoComprobante();
                $notaCreditoComprobante->setMonto($monto);
                $notaCreditoComprobante->setNotaCredito($comprobanteCompra);
                /* @var $comprobanteAcreditado ComprobanteCompra */
                $comprobanteAcreditado = $em->getRepository('ADIFContableBundle:ComprobanteCompra')->find($index);
                $comprobanteAcreditado->setSaldo($comprobanteAcreditado->getSaldo() - $monto);
                $notaCreditoComprobante->setComprobante($comprobanteAcreditado);
                $em->persist($notaCreditoComprobante);
            }

            $comprobanteCompra->setSaldo(0);

            // Persisto la entidad
            $em->persist($comprobanteCompra);

            // Persisto los asientos contables y presupuestarios
            $numeroAsiento = $this->get('adif.asiento_service')
                    ->generarAsientoNotaCreditoCompras($comprobanteCompra, $this->getUser());

            // Si no hubo errores en los asientos
            if ($numeroAsiento != -1) {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {
                    $em->flush();

                    $em_compras->flush();

                    $em->getConnection()->commit();

                    $dataArray = [
                        'data-id-comprobante' => $comprobanteCompra->getId()
                    ];

                    $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($numeroAsiento, $dataArray);
                } //.
                catch (\Exception $e) {

                    $em->getConnection()->rollback();
                    $em->close();

                    throw $e;
                }
            }

            return $this->redirect($this->generateUrl('comprobantes_compra'));
        } else {
            $comprobanteCompra->setProveedor($em_compras->getRepository('ADIFComprasBundle:Proveedor')
                            ->find($comprobanteCompra->getIdProveedor()));

            $request->attributes->set('form-error', true);
        }

        $jurisdiccionCABA = $em->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $comprobanteCompra,
            'form' => $form->createView(),
            'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de compra',
        );
    }

    /**
     * Creates a form to create a ComprobanteCompra entity.
     *
     * @param ComprobanteCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateComprasForm(ComprobanteCompra $entity) {
        $form = $this->createForm(new ComprobanteCompraCreateType( $this->getDoctrine()->getManager($this->getEntityManager()),
                                                                   $this->getDoctrine()->getManager(EntityManagers::getEmCompras())
                                                                 ), $entity, array(
            'action' => $this->generateUrl('notascredito_compras_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new NotaCredito entity.
     *
     * @Route("/compras/crear", name="notascredito_compras_new")
     * @Method("GET")
     * @Template("ADIFContableBundle:NotaCredito:new.compras.html.twig")
     */
    public function newComprasAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $comprobanteCompra = new ComprobanteCompra();

        $form = $this->createCreateComprasForm($comprobanteCompra);

        $denominacionJurisdiccionCABA = null;

        $jurisdiccionCABA = $em->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $comprobanteCompra,
            'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear nota de crédito de compras'
        );
    }

    //OBRAS/////////////////////////////////////////////////////////////////////

    /**
     * Creates a new ComprobanteObra entity.
     *
     * @Route("/obras/insertar", name="notascredito_obras_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:NotaCredito:new.obras.html.twig")
     */
    public function createObrasAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $em->clear();
        $em_obras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $requestComprobanteObra = $request->request->get('adif_contablebundle_comprobanteobra');

        $requestRenglonComprobanteObra = $requestComprobanteObra["renglonesComprobante"];

        $tipoComprobante = $request->request->get('adif_contablebundle_comprobanteobra', false)['tipoComprobante'];

        $comprobanteObra = ConstanteTipoComprobanteObra::getSubclass($tipoComprobante);

        /* @var $comprobanteObra \ADIF\ContableBundle\Entity\NotaCredito */

        $form = $this->createCreateObrasForm($comprobanteObra);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $comprobanteObra->setEstadoComprobante($em->getRepository('ADIFContableBundle:EstadoComprobante')
                            ->find(EstadoComprobante::__ESTADO_CANCELADO));

            $notasCreditoComprobante = array();

            foreach ($comprobanteObra->getRenglonesComprobante() as $index => $renglonComprobanteObra) {

                /* @var $renglonComprobanteObra \ADIF\ContableBundle\Entity\Obras\RenglonComprobanteObra */

                $renglonComprobanteActual = $requestRenglonComprobanteObra[++$index];

                if (!isset($notasCreditoComprobante[$renglonComprobanteActual['idComprobante']])) {
                    $notasCreditoComprobante[$renglonComprobanteActual['idComprobante']] = 0;
                }

                $notasCreditoComprobante[$renglonComprobanteActual['idComprobante']] += $renglonComprobanteObra->getMontoBruto();


                /* @var $renglonComprobante \ADIF\ContableBundle\Entity\Obras\RenglonComprobanteObra */
                $renglonComprobante = $em->getRepository('ADIFContableBundle:Obras\RenglonComprobanteObra')->find($renglonComprobanteActual['idRenglonComprobante']);

                //
                $renglonComprobanteObra->setRenglonAcreditado($renglonComprobante);
                //
            }
            foreach ($notasCreditoComprobante as $index => $monto) {
                $notaCreditoComprobante = new NotaCreditoComprobante();
                $notaCreditoComprobante->setMonto($monto);
                $notaCreditoComprobante->setNotaCredito($comprobanteObra);
                /* @var $comprobanteAcreditado ComprobanteObra */
                $comprobanteAcreditado = $em->getRepository('ADIFContableBundle:Obras\ComprobanteObra')->find($index);
                $comprobanteAcreditado->setSaldo($comprobanteAcreditado->getSaldo() - $monto);
                $notaCreditoComprobante->setComprobante($comprobanteAcreditado);
                $em->persist($notaCreditoComprobante);
            }

            $comprobanteObra->setSaldo(0);

            // Persisto la entidad
            $em->persist($comprobanteObra);

            // Persisto los asientos contables y presupuestarios
            $numeroAsiento = $this->get('adif.asiento_service')
                    ->generarAsientoNotaCreditoObras($comprobanteObra, $this->getUser());

            // Si no hubo errores en los asientos
            if ($numeroAsiento != -1) {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {
                    $em->flush();

                    $em_obras->flush();

                    $em->getConnection()->commit();

                    $dataArray = [
                        'data-id-comprobante' => $comprobanteObra->getId()
                    ];

                    $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($numeroAsiento, $dataArray);
                } //.
                catch (\Exception $e) {

                    $em->getConnection()->rollback();
                    $em->close();

                    throw $e;
                }
            }

            return $this->redirect($this->generateUrl('comprobanteobra'));
        } else {
            $comprobanteObra->setProveedor($em_obras->getRepository('ADIFComprasBundle:Proveedor')
                            ->find($comprobanteObra->getIdProveedor()));

            $request->attributes->set('form-error', true);
        }

        $jurisdiccionCABA = $em->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $comprobanteObra,
            'form' => $form->createView(),
            'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de obra',
        );
    }

    /**
     * Creates a form to create a ComprobanteObra entity.
     *
     * @param ComprobanteObra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateObrasForm(ComprobanteObra $entity) {
        $form = $this->createForm(new ComprobanteObraCreateType(), $entity, array(
            'action' => $this->generateUrl('notascredito_obras_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_compras' => $this->getDoctrine()->getManager(EntityManagers::getEmCompras())

        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new NotaCredito entity.
     *
     * @Route("/obras/crear", name="notascredito_obras_new")
     * @Method("GET")
     * @Template("ADIFContableBundle:NotaCredito:new.obras.html.twig")
     */
    public function newObrasAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $comprobanteObra = new ComprobanteObra();

        $form = $this->createCreateObrasForm($comprobanteObra);

        $denominacionJurisdiccionCABA = null;

        $jurisdiccionCABA = $em->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $comprobanteObra,
            'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear nota de crédito de obras'
        );
    }

}
