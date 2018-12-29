<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoDeclaracionJuradaContribuyente;
use ADIF\ContableBundle\Entity\DeclaracionJuradaIvaContribuyente;
use ADIF\ContableBundle\Form\DeclaracionJuradaIvaContribuyenteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use ADIF\ContableBundle\Entity\Constantes\ConstanteAlicuotaIva;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteVenta;
use ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoPercepcion;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Entity\OrdenPagoDeclaracionJuradaIvaContribuyente;
use \DateInterval;

/**
 * DeclaracionJuradaIvaContribuyente controller.
 *
 * @Route("/declaracionesjuradasivacontribuyente")
 */
class DeclaracionJuradaIvaContribuyenteController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Declaraciones Juradas Iva' => $this->generateUrl('declaracionesjuradasivacontribuyente')
        );
    }

    /**
     * Lists all DeclaracionJuradaIvaContribuyente entities.
     *
     * @Route("/", name="declaracionesjuradasivacontribuyente")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $bread = $this->base_breadcrumbs;
        $bread['Declaraciones Juradas Iva'] = null;

        $declaracionesAbiertas = $em->getRepository('ADIFContableBundle:DeclaracionJuradaIvaContribuyente')->findBy(array('fechaCierre' => null));

        //\Doctrine\Common\Util\Debug::dump($declaracionesAbiertas);die;

        return array(
            'breadcrumbs' => $bread,
            'existenDeclaracionesAbiertas' => count($declaracionesAbiertas),
            'page_title' => 'Declaracion Jurada Iva',
            'page_info' => 'Lista de declaraciones juradas'
        );
    }

    /**
     * Tabla para DeclaracionJuradaIvaContribuyente .
     *
     * @Route("/index_table/", name="declaracionesjuradasivacontribuyente_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:DeclaracionJuradaIvaContribuyente')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Declaraciones Juradas Iva'] = null;

        return $this->render('ADIFContableBundle:DeclaracionJuradaIvaContribuyente:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new DeclaracionJuradaIvaContribuyente entity.
     *
     * @Route("/insertar", name="declaracionesjuradasivacontribuyente_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:DeclaracionJuradaIvaContribuyente:new.html.twig")
     */
    public function createAction(Request $request) {
        //\Doctrine\Common\Util\Debug::dump($request);die;
        $entity = new DeclaracionJuradaIvaContribuyente();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $entity->setFechaFin(new \DateTime(date('Y-m-t 23:59:59', strtotime($entity->getFechaInicio()->format('Y-m-d')))));
            //\Doctrine\Common\Util\Debug::dump($entity);die;
            $entity->setEstadoDeclaracionJuradaContribuyente($em->getRepository('ADIFContableBundle:EstadoDeclaracionJuradaContribuyente')->findOneByDenominacion(ConstanteEstadoDeclaracionJuradaContribuyente::PENDIENTE));

            $comprobantes_compra = $this->getComprobantesCompra($entity->getFechaInicio(), $entity->getFechaFin());

            $total_iva_105 = 0;
            $total_iva_21 = 0;
            $total_iva_27 = 0;
            $total_percepciones_iva = 0;

            foreach ($comprobantes_compra as $comprobante) {
                /* @var $comprobante \ADIF\ContableBundle\Entity\Comprobante */
                $total_iva_105 += $comprobante->getImporteTotalIVAByAlicuota(ConstanteAlicuotaIva::ALICUOTA_10_5);
                $total_iva_21 += $comprobante->getImporteTotalIVAByAlicuota(ConstanteAlicuotaIva::ALICUOTA_21);
                $total_iva_27 += $comprobante->getImporteTotalIVAByAlicuota(ConstanteAlicuotaIva::ALICUOTA_27);
                $total_percepciones_iva += $comprobante->getImporteTotalPercepcionByConcepto(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA);
            }
            $credito_fiscal = $total_iva_105 + $total_iva_21 + $total_iva_27;

            $comprobantes_venta = $this->getComprobantesVenta($entity->getFechaInicio(), $entity->getFechaFin());
            $debito_fiscal = 0;
            $total_neto = 0;
            $total_exento = 0;
            foreach ($comprobantes_venta as $comprobante) {
                $total_neto += ($comprobante->getImporteTotalNetoByAlicuota(ConstanteAlicuotaIva::ALICUOTA_10_5) + $comprobante->getImporteTotalNetoByAlicuota(ConstanteAlicuotaIva::ALICUOTA_21) + $comprobante->getImporteTotalNetoByAlicuota(ConstanteAlicuotaIva::ALICUOTA_27));
                $total_exento += $comprobante->getImporteTotalExento();
                $debito_fiscal += $comprobante->getImporteTotalIVA();
            }

            $entity->setMontoCreditoFiscal($credito_fiscal);
            $entity->setMontoDebitoFiscal($debito_fiscal);
            $entity->setMontoGravadoFacturado($total_neto);
            $entity->setMontoTotalFacturado($total_neto + $total_exento);
            $entity->setMontoIva105($total_iva_105);
            $entity->setMontoIva21($total_iva_21);
            $entity->setMontoIva27($total_iva_27);
            $entity->setMontoPercepcionesIva($total_percepciones_iva);
            $entity->setMontoRetencionesIva(0);
            $entity->setSaldo(0);
            $entity->setSaldoMesSiguiente(0);

            $em->persist($entity);
            $em->flush();

            //return $this->redirect($this->generateUrl('declaracionesjuradasivacontribuyente'));
            return $this->redirect($this->generateUrl('declaracionesjuradasivacontribuyente_edit', array('id' => $entity->getId())));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear DeclaracionJuradaIvaContribuyente',
        );
    }

    /**
     * Creates a form to create a DeclaracionJuradaIvaContribuyente entity.
     *
     * @param DeclaracionJuradaIvaContribuyente $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(DeclaracionJuradaIvaContribuyente $entity) {
        $form = $this->createForm(new DeclaracionJuradaIvaContribuyenteType(), $entity, array(
            'action' => $this->generateUrl('declaracionesjuradasivacontribuyente_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new DeclaracionJuradaIvaContribuyente entity.
     *
     * @Route("/crear", name="declaracionesjuradasivacontribuyente_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new DeclaracionJuradaIvaContribuyente();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        $ultimoPeriodo = $this->getUltimoPeriodo();

        return array(
            'entity' => $entity,
            'fechaInicioPeriodo' => $ultimoPeriodo ? $ultimoPeriodo->add(new DateInterval('P1M')) : '',
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Declaracion Jurada'
        );
    }

    /**
     * Finds and displays a DeclaracionJuradaIvaContribuyente entity.
     *
     * @Route("/{id}", name="declaracionesjuradasivacontribuyente_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:DeclaracionJuradaIvaContribuyente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DeclaracionJuradaIvaContribuyente.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Declaraciones Juradas Iva'] = null;

        return array(
            'entity' => $entity,
            'saldoMesAnterior' => $this->getSaldoMesAnterior($entity),
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Declaracion Jurada'
        );
    }

    /**
     * Displays a form to edit an existing DeclaracionJuradaIvaContribuyente entity.
     *
     * @Route("/editar/{id}", name="declaracionesjuradasivacontribuyente_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:DeclaracionJuradaIvaContribuyente:edit.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:DeclaracionJuradaIvaContribuyente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DeclaracionJuradaIvaContribuyente.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'periodo' => $entity->getPeriodo(),
            'saldoMesAnterior' => $this->getSaldoMesAnterior($entity),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Declaracion Jurada'
        );
    }

    /**
     * Creates a form to edit a DeclaracionJuradaIvaContribuyente entity.
     *
     * @param DeclaracionJuradaIvaContribuyente $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(DeclaracionJuradaIvaContribuyente $entity) {
        $form = $this->createForm(new DeclaracionJuradaIvaContribuyenteType(), $entity, array(
            'action' => $this->generateUrl('declaracionesjuradasivacontribuyente_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));
        $form->add('cerrar', 'submit', array('label' => 'Cerrar DDJJ'));

        return $form;
    }

    /**
     * Edits an existing DeclaracionJuradaIvaContribuyente entity.
     *
     * @Route("/actualizar/{id}", name="declaracionesjuradasivacontribuyente_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:DeclaracionJuradaIvaContribuyente:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:DeclaracionJuradaIvaContribuyente')->find($id);

        /* @var $entity DeclaracionJuradaIvaContribuyente */
        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DeclaracionJuradaIvaContribuyente.');
        }
        /*
          if($request->request->get('cerrar', null)){
          echo "Cerrar";
          } else {
          echo "NO Cerrar";
          }
          die;
         */
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setEstadoDeclaracionJuradaContribuyente($em->getRepository('ADIFContableBundle:EstadoDeclaracionJuradaContribuyente')->findOneByDenominacion(ConstanteEstadoDeclaracionJuradaContribuyente::CERRADA));
            if ($editForm->get('cerrar')->isClicked()) {
                // Setear fecha de cierre, saldo mes siguiente y AC y asientos
                $entity->setFechaCierre(new \DateTime());
                //\Doctrine\Common\Util\Debug::dump($entity);die;
                if ($entity->getSaldo() > 0) {
                    //Saldo a favor de AFIP
                    $this->createAutorizacionContable($entity);
                    $entity->setSaldoMesSiguiente(0);
                } else {
                    //Saldo a favor de ADIFSE
                    $entity->setSaldoMesSiguiente(abs($entity->getSaldo()));
                }

                //Asiento
                // Genero el asiento contable y presupuestario
                $numeroAsiento = $this->get('adif.asiento_service')->generarAsientoFromDeclaracionJuradaIvaContribuyente($entity, $this->getUser());

                // Si no hubo errores en los asientos
                if ($numeroAsiento != -1) {
                    // Comienzo la transaccion
                    $em->getConnection()->beginTransaction();

                    try {
                        $em->flush();

                        $em->getConnection()->commit();

                        $this->get('session')->getFlashBag()->add('success', "La declaraci&oacute;n jurada se gener&oacute; con &eacute;xito");

                        $dataArray = [
                            'data-id-ddjj' => $entity->getId()
                        ];

                        $this->get('adif.asiento_service')->showMensajeFlashAsientoContable($numeroAsiento, $dataArray);
                    } catch (\Exception $e) {
                        $em->getConnection()->rollback();
                        $em->close();


                        throw $e;
                    }
                }
            } else {
                $em->flush();
            }

            return $this->redirect($this->generateUrl('declaracionesjuradasivacontribuyente'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Declaracion Jurada'
        );
    }

    /**
     * Deletes a DeclaracionJuradaIvaContribuyente entity.
     *
     * @Route("/borrar/{id}", name="declaracionesjuradasivacontribuyente_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:DeclaracionJuradaIvaContribuyente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DeclaracionJuradaIvaContribuyente.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('declaracionesjuradasivacontribuyente'));
    }

    private function getComprobantesCompra($fechaInicio, $fechaFin) {
        $qb = $this->getDoctrine()->getRepository('ADIFContableBundle:ComprobanteCompra', $this->getEntityManager())->createQueryBuilder('c');

        $comprobantes_compra = $qb
                ->where($qb->expr()->between('c.fechaContable', ':fechaInicio', ':fechaFin'))
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATE)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATE)
                ->addOrderBy('c.fechaComprobante', 'ASC')
                ->getQuery()
                ->getResult();

        $qbc = $this->getDoctrine()->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria', $this->getEntityManager())
                ->createQueryBuilder('cc');

        $comprobantes_consultoria = $qbc
                ->where($qbc->expr()->between('cc.fechaContable', ':fechaInicio', ':fechaFin'))
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATE)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATE)
                ->addOrderBy('cc.fechaComprobante', 'ASC')
                ->getQuery()
                ->getResult();


        $qbo = $this->getDoctrine()->getRepository('ADIFContableBundle:Obras\ComprobanteObra', $this->getEntityManager())
                ->createQueryBuilder('co');

        $comprobantes_obra = $qbo
                ->where($qbo->expr()->between('co.fechaContable', ':fechaInicio', ':fechaFin'))
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATE)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATE)
                ->addOrderBy('co.fechaComprobante', 'ASC')
                ->getQuery()
                ->getResult();

        $qbr = $this->getDoctrine()->getRepository('ADIFContableBundle:EgresoValor\ComprobanteEgresoValor', $this->getEntityManager())
                ->createQueryBuilder('co');

        $comprobantes_egreso_valor = $qbr
                ->where($qbr->expr()->between('co.fechaContable', ':fechaInicio', ':fechaFin'))
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATE)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATE)
                ->addOrderBy('co.fechaComprobante', 'ASC')
                ->getQuery()
                ->getResult();

        return array_merge($comprobantes_compra, $comprobantes_consultoria, $comprobantes_obra, $comprobantes_egreso_valor);
    }

    private function getComprobantesVenta($fechaInicio, $fechaFin) {
        $qbv = $this->getDoctrine()->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta', $this->getEntityManager())->createQueryBuilder('c');

        $comprobantes_venta = $qbv
                ->innerJoin('c.tipoComprobante', 't')
                ->innerJoin('c.estadoComprobante', 'e')
                ->where($qbv->expr()->between('c.fechaContable', ':fechaInicio', ':fechaFin'))
                ->andWhere('t.id != :tipoComprobante')
                ->andWhere('e.id != :estadoComprobante')
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATE)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATE)
                ->setParameter('tipoComprobante', ConstanteTipoComprobanteVenta::CUPON, \Doctrine\DBAL\Types\Type::STRING)
                ->setParameter('estadoComprobante', EstadoComprobante::__ESTADO_ANULADO, \Doctrine\DBAL\Types\Type::STRING)
                ->getQuery()
                ->getResult();

        return $comprobantes_venta;
    }

    /**
     * Devuelve el template del detalle de percepciones de IVA
     *
     * @Route("/detalle_percepciones_iva/", name="declaracion_jurada_iva_contribuyente_detalle_percepciones_iva")
     * @Method("POST")   
     * @Template("ADIFContableBundle:DeclaracionJuradaIvaContribuyente:edit.detalle_percepciones_iva.html.twig")
     */
    public function getDetallePercepcionesIvaAction(Request $request) {
        $emContable = $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idDeclaracionJurada = $request->request->get('id');

        /* @var $declaracionJurada \ADIF\ContableBundle\Entity\DeclaracionJuradaIvaContribuyente */
        $declaracionJurada = $emContable->getRepository('ADIFContableBundle:DeclaracionJuradaIvaContribuyente')->find($idDeclaracionJurada);

        $comprobantes_compra = $this->getComprobantesCompra($declaracionJurada->getFechaInicio(), $declaracionJurada->getFechaFin());

        $percepciones = array();

        foreach ($comprobantes_compra as $comprobante) {
            /* @var $comprobante \ADIF\ContableBundle\Entity\Comprobante */
            if ($comprobante->getImporteTotalPercepcionByConcepto(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA) > 0) {
                $percepciones[] = array(
                    'id' => $comprobante->getId(),
                    'CUIT' => $comprobante->getProveedor()->getClienteProveedor()->getCUIT(),
                    'razon_social' => $comprobante->getProveedor()->getClienteProveedor()->getRazonSocial(),
                    'comprobante' => $comprobante->getNumeroCompleto(),
                    'importe' => number_format($comprobante->getImporteTotalPercepcionByConcepto(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA), 2, '.', ',')
                );
            }
        }

        return array(
            'percepciones' => $percepciones
        );
    }

    /**
     * 
     * @param DeclaracionJuradaIvaContribuyente $declaracionJurada
     * @return int
     */
    public function getSaldoMesAnterior(DeclaracionJuradaIvaContribuyente $declaracionJurada) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ddjj = $em->getRepository('ADIFContableBundle:DeclaracionJuradaIVAContribuyente')
                ->createQueryBuilder('d')
                ->where('d.fechaInicio = DATE_SUB(:inicioDDJJ, 1, \'MONTH\')')
                ->setParameter('inicioDDJJ', $declaracionJurada->getFechaInicio()->format('Y-m-d'))
                //->where('d.id <> :idDDJJ')
                //->setParameter('idDDJJ', $declaracionJurada->getId())
                ->orderBy('d.fechaFin', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        if ($ddjj) {
            return $ddjj->getSaldoMesSiguiente();
        } else {
            return 0;
        }
    }

    /**
     * 
     * @return type
     */
    public function getUltimoPeriodo() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ddjj = $em->getRepository('ADIFContableBundle:DeclaracionJuradaIVAContribuyente')
                ->createQueryBuilder('d')
                ->orderBy('d.fechaFin', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        if ($ddjj) {
            return $ddjj->getFechaInicio();
        } else {
            return null;
        }
    }

    /**
     * 
     * @param DeclaracionJuradaIvaContribuyente $declaracionJurada
     */
    private function createAutorizacionContable(DeclaracionJuradaIvaContribuyente $declaracionJurada) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ordenPago = new OrdenPagoDeclaracionJuradaIvaContribuyente();
        $ordenPago->setDeclaracionJuradaIvaContribuyente($declaracionJurada);
        $ordenPago->setImporte(abs($declaracionJurada->getSaldo()));

        $periodo = $declaracionJurada->getPeriodo();

        $this->get('adif.orden_pago_service')->initAutorizacionContable($ordenPago, 'Declaraci&oacute;n jurada IVA contribuyente per&iacute;odo ' . $periodo);

        $em->persist($ordenPago);
    }

}
