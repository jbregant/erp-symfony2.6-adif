<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\ComprobanteCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteJurisdiccion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Form\ComprobanteCompraType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * ComprobanteServicioController controller.
 *
 * @Route("/comprobanteservicio")
 */
class ComprobanteServicioController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Comprobantes de compra' => $this->generateUrl('comprobantes_compra')
        );
    }

    /**
     * Creates a new ComprobanteCompra entity.
     *
     * @Route("/insertar", name="comprobantes_servicio_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:ComprobanteServicio:new.html.twig")
     */
    public function createAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $tipoComprobante = $request->request
                        ->get('adif_contablebundle_comprobantecompra', false)['tipoComprobante'];

        $entity = ConstanteTipoComprobanteCompra::getSubclass($tipoComprobante);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $em_compras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        if ($form->isValid()) {

            $entity->setEstadoComprobante($em->getRepository('ADIFContableBundle:EstadoComprobante')
                            ->find(EstadoComprobante::__ESTADO_INGRESADO));

            $this->get('adif.orden_compra_service')->generarOrdenCompraFromComprobanteCompra($entity, $em_compras);

            foreach ($entity->getRenglonesComprobante() as $renglonComprobanteCompra) {
                /* @var $renglonComprobanteCompra \ADIF\ContableBundle\Entity\RenglonComprobanteCompra */

                $renglonComprobanteCompra->setIdRenglonOrdenCompra($renglonComprobanteCompra->getRenglonOrdenCompra()->getId());

                foreach ($renglonComprobanteCompra->getRenglonComprobanteCompraCentrosDeCosto() as $renglonComprobanteCompraCentroDeCosto) {
                    /* @var $renglonComprobanteCompraCentroDeCosto \ADIF\ContableBundle\Entity\RenglonComprobanteCompraCentroDeCosto */
                    $renglonComprobanteCompraCentroDeCosto->setRenglonComprobanteCompra($renglonComprobanteCompra);
                }
            }

            // Seteo el saldo
            $entity->setSaldo($entity->getTotal());

            // Persisto la entidad
            $em->persist($entity);

            // Persisto los asientos contables y presupuestarios
			$esContraAsiento = $entity->getEsNotaCredito();
            $numeroAsiento = $this->get('adif.asiento_service')
                    ->generarAsientoServicio($entity, $this->getUser(), $esContraAsiento);

            // Si no hubo errores en los asientos
            if ($numeroAsiento != -1) {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {
                    $em->flush();

                    $em_compras->flush();

                    $em->getConnection()->commit();

                    $dataArray = [
                        'data-id-comprobante' => $entity->getId()
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

            $request->attributes->set('form-error', true);

            $entity->setProveedor($em_compras->getRepository('ADIFComprasBundle:Proveedor')
                            ->find($entity->getIdProveedor()));
        }

        $denominacionJurisdiccionCABA = null;

        $jurisdiccionCABA = $em->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de compra',
        );
    }

    /**
     * Creates a form to create a ComprobanteCompra entity.
     *
     * @param ComprobanteCompra $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(ComprobanteCompra $entity) {
        $form = $this->createForm(new ComprobanteCompraType($this->getDoctrine()->getManager($this->getEntityManager()),
                                                            $this->getDoctrine()->getManager(EntityManagers::getEmCompras())
                                                            ), $entity, array(
            'action' => $this->generateUrl('comprobantes_servicio_create'),
            'method' => 'POST',
            
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar e ir a autorizacion contable'));

        return $form;
    }

    /**
     * Displays a form to create a new ComprobanteCompra entity.
     *
     * @Route("/crear", name="comprobantes_servicio_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = new ComprobanteCompra();
        $form = $this->createCreateForm($entity);

        $denominacionJurisdiccionCABA = null;

        $jurisdiccionCABA = $em->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de compra'
        );
    }

    /**
     * Displays a form to edit an existing ComprobanteCompra entity.
     *
     * @Route("/editar/{id}", name="comprobantes_servicio_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:ComprobanteServicio:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ComprobanteCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteCompra.');
        }

        $denominacionJurisdiccionCABA = null;

        $jurisdiccionCABA = $em->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar comprobante de compra'
        );
    }

    /**
     * Creates a form to edit a ComprobanteCompra entity.
     *
     * @param ComprobanteCompra $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(ComprobanteCompra $entity) {
        $form = $this->createForm(new ComprobanteCompraType($this->getDoctrine()->getManager($this->getEntityManager()),
                                                            $this->getDoctrine()->getManager(EntityManagers::getEmCompras())
                                                            ), $entity, array(
            'action' => $this->generateUrl('comprobantes_servicio_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            
            //'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ComprobanteCompra entity.
     *
     * @Route("/actualizar/{id}", name="comprobantes_servicio_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:ComprobanteCompra:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:ComprobanteCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteCompra.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('comprobantes_compra'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar comprobante de compra'
        );
    }

}
