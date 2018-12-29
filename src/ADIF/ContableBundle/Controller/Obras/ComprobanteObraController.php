<?php

namespace ADIF\ContableBundle\Controller\Obras;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\ComprobanteImpresion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteObra;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Entity\Obras\ComprobanteObra;
use ADIF\ContableBundle\Entity\Obras\DocumentoFinanciero;
use ADIF\ContableBundle\Form\Obras\ComprobanteObraCreateType;
use ADIF\ContableBundle\Form\Obras\ComprobanteObraType;
use Doctrine\ORM\Query\ResultSetMapping;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ComprobanteObra controller.
 *
 * @Route("/comprobanteobra")
 */
class ComprobanteObraController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Comprobantes de obra' => $this->generateUrl('comprobanteobra')
        );
    }

    /**
     * Lists all ComprobanteObra entities.
     *
     * @Route("/", name="comprobanteobra")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de obra'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Comprobante de obra',
            'page_info' => 'Lista de comprobantes de obra'
        );
    }

    /**
     * Creates a new ComprobanteObra entity.
     *
     * @Route("/insertar", name="comprobanteobra_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Obras\ComprobanteObra:new.html.twig")
     */
    public function createAction(Request $request) {

        $tipoComprobante = $request->request->get('adif_contablebundle_comprobanteobra', false)['tipoComprobante'];
        $comprobanteObra = ConstanteTipoComprobanteObra::getSubclass($tipoComprobante);

        $form = $this->createCreateForm($comprobanteObra);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Seteo el Estado
            $comprobanteObra->setEstadoComprobante($em->getRepository('ADIFContableBundle:EstadoComprobante')
                            ->find(EstadoComprobante::__ESTADO_INGRESADO));

            $this->setComprobanteImpresion($comprobanteObra);

            // Seteo el saldo
            $comprobanteObra->setSaldo($comprobanteObra->getTotal());

            // Persisto la entidad
            $em->persist($comprobanteObra);

            $esContraAsiento = false;

            if ($comprobanteObra->getEsNotaCredito()) {
                $esContraAsiento = true;
            }

            // Persisto los asientos contables y presupuestarios
            $numeroAsiento = $this->get('adif.asiento_service')
                    ->generarAsientoComprobanteObra($comprobanteObra, $this->getUser(), $esContraAsiento);

            // Si no hubo errores en los asientos
            if ($numeroAsiento != -1) {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {
                    $em->flush();

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
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $comprobanteObra,
            'form' => $form->createView(),
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
    private function createCreateForm(ComprobanteObra $entity) {
        $form = $this->createForm(new ComprobanteObraCreateType(), $entity, array(
            'action' => $this->generateUrl('comprobanteobra_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_compras' => $this->getDoctrine()->getManager(EntityManagers::getEmCompras())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ComprobanteObra entity.
     *
     * @Route("/crear", name="comprobanteobra_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new ComprobanteObra();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de obra'
        );
    }

    /**
     * Displays a form to create a new ComprobanteObra entity.
     *
     * @Route("/{documentoFinanciero}/crear", name="comprobanteobra_new_from_documento_financiero")
     * @Method("GET")
     * @Template("ADIFContableBundle:Obras\ComprobanteObra:new.html.twig")
     */
    public function newFromDocumentoFinancieroAction(DocumentoFinanciero $documentoFinanciero) {
        $entity = new ComprobanteObra();

        $entity->setDocumentoFinanciero($documentoFinanciero);

        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de obra'
        );
    }

    /**
     * Finds and displays a ComprobanteObra entity.
     *
     * @Route("/{id}", name="comprobanteobra_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\ComprobanteObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteObra.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Detalle'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver comprobante de obra'
        );
    }

    /**
     * Displays a form to edit an existing ComprobanteObra entity.
     *
     * @Route("/editar/{id}", name="comprobanteobra_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Obras\ComprobanteObra:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Obras\ComprobanteObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteObra.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar comprobante de obra'
        );
    }

    /**
     * Creates a form to edit a ComprobanteObra entity.
     *
     * @param ComprobanteObra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ComprobanteObra $entity) {
        $form = $this->createForm(new ComprobanteObraType(), $entity, array(
            'action' => $this->generateUrl('comprobanteobra_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_compras' => $this->getDoctrine()->getManager(EntityManagers::getEmCompras())
            
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ComprobanteObra entity.
     *
     * @Route("/actualizar/{id}", name="comprobanteobra_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Obras\ComprobanteObra:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em->clear();

        /* @var $comprobanteObra ComprobanteObra */
        $comprobanteObra = $em->getRepository('ADIFContableBundle:Obras\ComprobanteObra')->find($id);

        if (!$comprobanteObra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteObra.');
        }

        $requestComprobante = $request->request->get('adif_contablebundle_comprobanteobra', false);

        $comprobanteObra->setObservaciones($requestComprobante['observaciones']);
        
        $comprobanteObra->setNumeroReferencia($requestComprobante['numeroReferencia']);

        $renglones = $requestComprobante['renglonesComprobante'];

        if ($renglones != null) {

            /* @var $renglonComprobante \ADIF\ContableBundle\Entity\Obras\RenglonComprobanteObra */

            foreach ($renglones as $renglonComprobante) {

                /* @var $renglon \ADIF\ContableBundle\Entity\Obras\RenglonComprobanteObra */

                $renglon = $em->getRepository('ADIFContableBundle:Obras\RenglonComprobanteObra')
                        ->find($renglonComprobante['idRenglon']);

                if (!$renglon) {
                    throw $this->createNotFoundException('No se puede encontrar la entidad RenglonComprobanteObra.');
                }

                if ($renglonComprobante['regimenRetencionSUSS'] != 0) {

                    $renglon->setRegimenRetencionSUSS($em->getRepository('ADIFContableBundle:RegimenRetencion')
                                    ->find($renglonComprobante['regimenRetencionSUSS']));
                }

                if ($renglonComprobante['regimenRetencionIVA'] != 0) {

                    $renglon->setRegimenRetencionIVA($em->getRepository('ADIFContableBundle:RegimenRetencion')
                                    ->find($renglonComprobante['regimenRetencionIVA']));
                }

                if ($renglonComprobante['regimenRetencionIIBB'] != 0) {

                    $renglon->setRegimenRetencionIIBB($em->getRepository('ADIFContableBundle:RegimenRetencion')
                                    ->find($renglonComprobante['regimenRetencionIIBB']));
                }

                if ($renglonComprobante['regimenRetencionGanancias'] != 0) {

                    $renglon->setRegimenRetencionGanancias($em->getRepository('ADIFContableBundle:RegimenRetencion')
                                    ->find($renglonComprobante['regimenRetencionGanancias']));
                }
            }

            $em->flush();

            return $this->redirect($this->generateUrl('comprobanteobra'));
        } else {
            // ERROR
            $this->get('session')->getFlashBag()->add('error', 'El comprobante debe tener renglones');
        }

        return $this->redirect($this->generateUrl('comprobanteobra'));
    }

    /**
     * Deletes a ComprobanteObra entity.
     *
     * @Route("/borrar/{id}", name="comprobanteobra_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Obras\ComprobanteObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteObra.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('comprobanteobra'));
    }

    /**
     * Tabla para ComprobanteObra
     * .
     *
     * @Route("/index_table/", name="comprobanteobra_index_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('fechaComprobante', 'fechaComprobante');
        $rsm->addScalarResult('tipoComprobante', 'tipoComprobante');
        $rsm->addScalarResult('letraComprobante', 'letraComprobante');
        $rsm->addScalarResult('puntoVenta', 'puntoVenta');
        $rsm->addScalarResult('numero', 'numero');
        $rsm->addScalarResult('cuitAndRazonSocial', 'cuitAndRazonSocial');
        $rsm->addScalarResult('tipoDocumentoFinanciero', 'tipoDocumentoFinanciero');
        $rsm->addScalarResult('correspondePago', 'correspondePago');
        $rsm->addScalarResult('importePendientePago', 'importePendientePago');
        $rsm->addScalarResult('idEstadoComprobante', 'idEstadoComprobante');
        $rsm->addScalarResult('tramo', 'tramo');
        $rsm->addScalarResult('idTramo', 'idTramo');
        $rsm->addScalarResult('total', 'total');

        $native_query = $em->createNativeQuery('
            SELECT
              id,
              fechaComprobante,
              tipoComprobante,
              letraComprobante,
              puntoVenta,
              numero,
              cuitAndRazonSocial,
              tipoDocumentoFinanciero,
              correspondePago,
              importePendientePago,
              idEstadoComprobante,
              tramo,
              idTramo,
              total
            FROM
                vistacomprobantesobra           
        ', $rsm);

        $comprobantes = $native_query->getResult();


        return $this->render('ADIFContableBundle:Obras\ComprobanteObra:index_table.html.twig', array(
                    'entities' => $comprobantes)
        );
    }

    /**
     *
     * @Route("/index_table_comprobante_credito/", name="comprobanteobra_index_table_comprobante_credito")
     * @Method("GET|POST")
     */
    public function indexTableComprobantesCreditoAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('fecha', 'fecha');
        $rsm->addScalarResult('tipo', 'tipo');
        $rsm->addScalarResult('cuitAndRazonSocial', 'cuitAndRazonSocial');
        $rsm->addScalarResult('correspondePago', 'correspondePago');
        $rsm->addScalarResult('tramo', 'tramo');
        $rsm->addScalarResult('idTramo', 'idTramo');
        $rsm->addScalarResult('idTipo', 'idTipo');
        $rsm->addScalarResult('pagada', 'pagada');
        $rsm->addScalarResult('valido', 'valido');
        $rsm->addScalarResult('monto', 'monto');
        $rsm->addScalarResult('idEstadoComprobante', 'idEstadoComprobante');
        $rsm->addScalarResult('yaUtilizada', 'yaUtilizada');

        $native_query = $em->createNativeQuery('
            SELECT
                id,
                fecha,
                tipo,
                cuitAndRazonSocial,
                correspondePago,
                tramo,
                idTramo,
                idTipo,
                pagada,
                valido,
                monto,
                idEstadoComprobante,
                yaUtilizada
            FROM
                vistacomprobantesobracredito
           ', $rsm);


        $comprobantesCredito = $native_query->getResult();

        return $this->render('ADIFContableBundle:Obras\ComprobanteObra:index_table_comprobante_credito.html.twig', array(
                    'entities' => $comprobantesCredito,
                        )
        );
    }

    /**
     * Anula el comprobante
     *
     * @Route("/anular/{id}", name="comprobanteobra_anular")
     * @Method("GET")
     */
    public function anularComprobanteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $comprobanteObra ComprobanteObra */
        $comprobanteObra = $em->getRepository('ADIFContableBundle:Obras\ComprobanteObra')->find($id);

        if (!$comprobanteObra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteObra.');
        }

        $estadoAnulado = $em->getRepository('ADIFContableBundle:EstadoComprobante')->find(EstadoComprobante::__ESTADO_ANULADO);

        if ($comprobanteObra->getEstadoComprobante() == $estadoAnulado) {
            $em_autenticacion = $this->getDoctrine()->getManager(EntityManagers::getEmAutenticacion());
            $usuarioUltimaModificacion = $em_autenticacion->getRepository('ADIFAutenticacionBundle:Usuario')->find($comprobanteObra->getIdUsuarioUltimaModificacion())->getNombreCompleto();

            $this->get('session')->getFlashBag()->add('error', 'El comprobante ya ha sido anulado el '.$comprobanteObra->getFechaAnulacion()->format('d/m/Y H:i:s') . ' por el usuario '. $usuarioUltimaModificacion);
            return $this->redirect($this->generateUrl('comprobanteobra'));
        }

        //$fechaContable = $comprobanteObra->getFechaContable();
		    $fecha_hoy = new \DateTime();
        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')->getEjercicioContableByFecha($fecha_hoy);

        if ($ejercicioContable->getEstaCerrado() || !$ejercicioContable->getMesEjercicioHabilitado($fecha_hoy->format('m'))) {
            $this->get('session')->getFlashBag()->add('error', 'El ejercicio contable est&aacute; cerrado o el mes correspondiente a la fecha contable del comprobante no est&aacute; habilitado');
        } else {

            //verifico los pagos parciales
            $pagosParcialesAnulados = true;
            foreach ($comprobanteObra->getPagosParciales() as $pagoParcial) {
                $pagosParcialesAnulados &= $pagoParcial->getAnulado();
            }

            if (!$pagosParcialesAnulados) {
                $this->get('session')->getFlashBag()->add('error', 'No se puede anular el comprobante. Existen pagos parciales sin anular.');
                return $this->redirect($this->generateUrl('comprobantes_compra'));
            }

            //$fecha_anulacion = $fecha_hoy->format('Ym') == $fecha_hoy->format('Ym') ? $fecha_hoy : $fechaContable;

            $comprobanteObra->setEstadoComprobante($estadoAnulado);
            $comprobanteObra->setFechaAnulacion($fecha_hoy);

            $esContraAsiento = false;

            if (!$comprobanteObra->getEsNotaCredito()) {
                $esContraAsiento = true;
            }

            // Persisto los asientos contables y presupuestarios
            $numeroAsiento = $this->get('adif.asiento_service')
                    ->generarAsientoComprobanteObra($comprobanteObra, $this->getUser(), $esContraAsiento, $fecha_hoy);

            // Si no hubo errores en los asientos
            if ($numeroAsiento != -1) {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {
                    $em->flush();

                    $em->getConnection()->commit();

                    $this->get('session')->getFlashBag()->add('success', 'El comprobante fue anulado');

                    $dataArray = [
                        'data-id-comprobante' => $comprobanteObra->getId(),
                        'data-fecha-asiento' => $fecha_hoy->format('d/m/Y'),
                        'data-es-anulacion' => 1
                    ];

                    $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($numeroAsiento, $dataArray);
                } //.
                catch (\Exception $e) {

                    $em->getConnection()->rollback();
                    $em->close();

                    $this->get('session')->getFlashBag()->add('error', 'El comprobante no se pudo anular');

                    throw $e;
                }
            }
        }

        return $this->redirect($this->generateUrl('comprobanteobra'));
    }

    /**
     * @Route("/generarAsientos/", name="comprobanteobra_asientos")
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
//        $comprobantesImportados = $em->getRepository('ADIFContableBundle:Obras\ComprobanteObra')
//                ->createQueryBuilder('co')
//                ->where('co.fechaContable >= :fecha')
//                ->setFirstResult($offset)
//                ->setMaxResults($limit)
//                ->setParameter('fecha', '2015-08-01 00:00:00')
//                ->orderBy('co.id', 'asc')
//                ->getQuery()
//                ->getResult();
//
//        $offset = $limit * $i;
//        $i++;
//        while (count($comprobantesImportados) > 0) {
//            /* @var $comprobanteImportado ComprobanteObra */
//            foreach ($comprobantesImportados as $comprobanteImportado) {
//                // Genero el definitivo asociado
//                $this->get('adif.asiento_service')->generarAsientoComprobanteObra($comprobanteImportado, $this->getUser(), $comprobanteImportado->getEsNotaCredito());
//            }
//            unset($comprobantesImportados);
//            $em->flush();
//            $em->clear();
//            gc_collect_cycles();
//            $comprobantesImportados = $em->getRepository('ADIFContableBundle:Obras\ComprobanteObra')
//                    ->createQueryBuilder('co')
//                    ->where('co.fechaContable >= :fecha')
//                    ->setFirstResult($offset)
//                    ->setMaxResults($limit)
//                    ->setParameter('fecha', '2015-08-01 00:00:00')
//                    ->orderBy('co.id', 'asc')
//                    ->getQuery()
//                    ->getResult();
//            $offset = $limit * $i;
//            $i++;
//        }
//        unset($comprobantesImportados);
//        $em->clear();
//        unset($em);
//        gc_collect_cycles();
//
//        if (!$parcial) {
//            $this->get('session')->getFlashBag()->add('success', 'Generacion de asientos de Comprobantes de Obra exitosa');
//        }

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $comprobanteObra = $em->getRepository('ADIFContableBundle:Obras\ComprobanteObra')->find(35887);
        $this->get('adif.asiento_service')->generarAsientoComprobanteObra($comprobanteObra, $this->getUser(), $comprobanteObra->getEsNotaCredito());
        $em->flush();
        $em->clear();

        return $this->redirect($this->generateUrl('comprobanteobra'));
    }

    /**
     * 
     * @param ComprobanteObra $comprobanteObra
     */
    private function setComprobanteImpresion(ComprobanteObra $comprobanteObra) {

        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $comprobanteImpresion = new ComprobanteImpresion();

        $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')
                ->find($comprobanteObra->getIdProveedor());

        $domicilioLegal = $proveedor->getClienteProveedor()->getDomicilioLegal();

        $comprobanteImpresion
                ->setRazonSocial($proveedor->getRazonSocial());

        $comprobanteImpresion
                ->setNumeroDocumento($proveedor->getNroDocumento());

        $comprobanteImpresion
                ->setProvincia($domicilioLegal->getLocalidad()->getProvincia());

        $comprobanteImpresion
                ->setLocalidad($domicilioLegal->getLocalidad()->getNombre());

        $comprobanteImpresion
                ->setCodigoPostal($domicilioLegal->getCodPostal());

        $comprobanteImpresion
                ->setDomicilioLegal($domicilioLegal->__toString());

        $comprobanteObra
                ->setComprobanteImpresion($comprobanteImpresion);
    }

    /**
     * Tabla para Nota credito.
     *
     * @Route("/index_table_comprobantes/", name="comprobanteobra_index_table_comprobantes")
     * @Method("GET|POST")
     */
    public function indexComprobantesObraProveedorAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('fechaComprobante', 'fechaComprobante');
        $rsm->addScalarResult('tipoComprobante', 'tipoComprobante');
        $rsm->addScalarResult('letraComprobante', 'letraComprobante');
        $rsm->addScalarResult('puntoVenta', 'puntoVenta');
        $rsm->addScalarResult('numero', 'numero');
        $rsm->addScalarResult('cuitAndRazonSocial', 'cuitAndRazonSocial');
        $rsm->addScalarResult('tipoDocumentoFinanciero', 'tipoDocumentoFinanciero');
        $rsm->addScalarResult('correspondePago', 'correspondePago');
        $rsm->addScalarResult('importePendientePago', 'importePendientePago');
        $rsm->addScalarResult('idEstadoComprobante', 'idEstadoComprobante');
        $rsm->addScalarResult('tramo', 'tramo');
        $rsm->addScalarResult('idTramo', 'idTramo');
        $rsm->addScalarResult('total', 'total');

        $native_query = $em->createNativeQuery('
            SELECT
              id,
              fechaComprobante,
              tipoComprobante,
              letraComprobante,
              puntoVenta,
              numero,
              cuitAndRazonSocial,
              tipoDocumentoFinanciero,
              correspondePago,
              importePendientePago,
              idEstadoComprobante,
              tramo,
              idTramo,
              total
            FROM
                vistacomprobantesobra           
            WHERE 
                importePendientePago > 0
                AND
                idProveedor = ' . $request->query->get('id_proveedor')
                , $rsm);

        $comprobantes = $native_query->getResult();

        return $this->render('ADIFContableBundle:Obras\ComprobanteObra:index_table_por_proveedor.html.twig', array('comprobantes' => $comprobantes));
    }

    /**
     * Tabla para Nota credito.
     *
     * @Route("/index_table_renglones_comprobantes/", name="comprobanteobra_index_table_renglones_comprobantes")
     * @Method("GET|POST")
     */
    public function indexRenglonesComprobantesObraAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $renglones = array();

        $renglonesComprobanteCompra = $em->getRepository('ADIFContableBundle:Obras\RenglonComprobanteObra')
                ->createQueryBuilder('r')
                ->innerJoin('r.comprobante', 'c')
                ->where('c.id IN (:ids)')
                ->setParameter('ids', json_decode($request->request->get('ids_comprobantes', [])))
                ->getQuery()
                ->getResult();

        /* @var $renglon \ADIF\ContableBundle\Entity\Obras\RenglonComprobanteObra */
        foreach ($renglonesComprobanteCompra as $renglon) {
            $renglones[] = array(
                'id' => $renglon->getId(),
                'descripcion' => $renglon->getDescripcion(),
                'cantidad' => $renglon->getCantidad(),
                'precioUnitario' => $renglon->getPrecioUnitario(),
                'montoNeto' => $renglon->getMontoNeto(),
                'idAlicuotaIva' => $renglon->getAlicuotaIva()->getId(),
                'montoIva' => $renglon->getMontoIva(),
                'idRegimenIVA' => ($renglon->getRegimenRetencionIVA() == null) ? null : $renglon->getRegimenRetencionIVA()->getId(),
                'idRegimenIIBB' => ($renglon->getRegimenRetencionIIBB() == null) ? null : $renglon->getRegimenRetencionIIBB()->getId(),
                'idRegimenGanancias' => ($renglon->getRegimenRetencionGanancias() == null) ? null : $renglon->getRegimenRetencionGanancias()->getId(),
                'idRegimenSUSS' => ($renglon->getRegimenRetencionSUSS() == null) ? null : $renglon->getRegimenRetencionSUSS()->getId(),
                'idComprobante' => $renglon->getComprobante()->getId(),
                'idDocumentoFinanciero' => $renglon->getComprobante()->getDocumentoFinanciero()->getId(),
                'comprobante' => $renglon->getComprobante()->getPuntoVenta() . '-' . $renglon->getComprobante()->getNumero()
            );
        }

        return new JsonResponse($renglones);
    }

}
