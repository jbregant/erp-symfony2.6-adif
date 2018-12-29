<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Comprobante;
use ADIF\ContableBundle\Form\ComprobanteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use Symfony\Component\HttpFoundation\Response;
use ADIF\ContableBundle\Entity\ComprobanteCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteJurisdiccion;
use DateTime;

/**
 * Comprobante controller.
 *
 * @Route("/comprobantes")
 */
class ComprobanteController extends BaseController {

    /**
     *
     * @var type 
     */
    private $base_breadcrumbs;

    /**
     * MODULO_COMPRAS
     */
    const MODULO_COMPRAS = "Compras";

    /**
     * MODULO_SERVICIOS
     */
    const MODULO_SERVICIOS = "Servicios";

    /**
     * MODULO_OBRAS
     */
    const MODULO_OBRAS = "Obras";

    /**
     * MODULO_CONSULTORIA
     */
    const MODULO_CONSULTORIA = "Consultor&iacute;a";

    /**
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Comprobantes' => $this->generateUrl('comprobantes')
        );
    }

    /**
     * Lists all Comprobante entities.
     *
     * @Route("/", name="comprobantes")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Comprobante')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Comprobantes',
            'page_info' => 'Lista de comprobante'
        );
    }

    /**
     * Creates a new Comprobante entity.
     *
     * @Route("/insertar", name="comprobantes_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Comprobante:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Comprobante();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('comprobantes'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante',
        );
    }

    /**
     * Creates a form to create a Comprobante entity.
     *
     * @param Comprobante $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Comprobante $entity) {
        $form = $this->createForm(new ComprobanteType(), $entity, array(
            'action' => $this->generateUrl('comprobantes_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Comprobante entity.
     *
     * @Route("/crear", name="comprobantes_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Comprobante();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante'
        );
    }

    /**
     * Finds and displays a Comprobante entity.
     *
     * @Route("/{id}", name="comprobantes_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Comprobante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Comprobante.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Comprobante'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver comprobante'
        );
    }

    /**
     * Displays a form to edit an existing Comprobante entity.
     *
     * @Route("/editar/{id}", name="comprobantes_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Comprobante:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Comprobante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Comprobante.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar comprobante'
        );
    }

    /**
     * Creates a form to edit a Comprobante entity.
     *
     * @param Comprobante $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Comprobante $entity) {
        $form = $this->createForm(new ComprobanteType(), $entity, array(
            'action' => $this->generateUrl('comprobantes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Comprobante entity.
     *
     * @Route("/actualizar/{id}", name="comprobantes_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Comprobante:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Comprobante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Comprobante.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('comprobantes'));
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
            'page_title' => 'Editar comprobante'
        );
    }

    /**
     * Deletes a Comprobante entity.
     *
     * @Route("/borrar/{id}", name="comprobantes_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Comprobante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Comprobante.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('comprobantes'));
    }

    /**
     *
     * @Route("/editar_fecha/", name="comprobantes_editar_fecha")
     */
    public function updateFechaAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $numeroAsiento = $request->request->get('numero_asiento');

        $fecha = $request->request->get('fecha');

        $idComprobante = $request->request->get('id_comprobante');

        $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')
                ->findOneByNumeroAsiento($numeroAsiento);

        // Si existe el asiento Y el usuario logueado genera asientos contables
        if (!$asientoContable && false === $this->get('security.context')->isGranted('ROLE_DETACH_ASIENTO')) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
        }

        /* @var $comprobante Comprobante */
        $comprobante = $em->getRepository('ADIFContableBundle:Comprobante')
                ->find($idComprobante);

        $comprobante->setFechaContable(\DateTime::createFromFormat('d/m/Y', $fecha));

        $em->persist($comprobante);

        $em->flush();

        return new Response();
    }

    /**
     * Reporte general de comprobantes.
     *
     * @Route("/reporte_general/", name="comprobantes_reporte_general")
     * @Method("GET")
     * @Template("ADIFContableBundle:Comprobante:reporte_general.html.twig")
     * 
     */
    public function reporteGeneralComprobanteAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Reporte general'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Reporte general'
        );
    }

    /** Filtra el reporte general de comprobantes.
     *
     * @Route("/filtrar_reporte_general/", name="comprobantes_filtrar_reporte_general")
     * 
     */
    public function filtrarReporteGeneralComprobanteAction(Request $request) {

        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $terminoBusqueda = $request->get('termino_busqueda');
        $fechaFiltro = $request->get('fechaRadio');

        $fechaInicio = DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaInicio') . ' 00:00:00');
        $fechaFin = DateTime::createFromFormat('d/m/Y H:i:s', $request->get('fechaFin') . ' 23:59:59');
        
        $terminoBusquedaSinGuiones = str_replace('-', '', $terminoBusqueda);

        $jsonResult = [];

        // COMPROBANTES DE COMPRA *****/
        
        $idProveedores = $emCompras->getRepository('ADIFComprasBundle:Proveedor')
                ->createQueryBuilder('p')
                ->select('p.id')
                ->innerJoin('p.clienteProveedor', 'cp')
                ->where('upper(cp.razonSocial) LIKE \'%' . strtoupper($terminoBusqueda) . '%\'')
                ->orWhere('cp.CUIT LIKE \'%' . strtoupper($terminoBusqueda) . '%\'')
                ->orWhere('cp.CUIT LIKE \'%' . strtoupper($terminoBusquedaSinGuiones) . '%\'')
                ->orderBy('cp.razonSocial', 'DESC')
                ->getQuery()
                ->getResult();

        if (empty($idProveedores)) {
            $idProveedores[] = -1;
        }

        $comprobanteCompras = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:ComprobanteCompra', $this->getEntityManager())
                ->createQueryBuilder('c')
                ->where('c.idProveedor IN (:idProveedores)')
                ->andWhere('c.'.$fechaFiltro.' between :fechaInicio AND :fechaFin')
                ->setParameter('idProveedores', $idProveedores, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                ->orderBy('c.id', 'DESC')
                ->getQuery()
                ->getResult();

        // Por cada comprobante de compra
        foreach ($comprobanteCompras as $comprobante) {

            /* @var $comprobante \ADIF\ContableBundle\Entity\ComprobanteCompra */

            $ordenPago = $comprobante->getOrdenPago();

            // Si NO tiene AC/OP o tiene y NO está ANULADA
            if ($ordenPago == null || ($ordenPago != null && !$ordenPago->getEstaAnulada())) {

                $beneficiario = $comprobante->getProveedor();

                $modulo = self::MODULO_COMPRAS;

                if ($comprobante->getEsComprobanteServicio()) {

                    $modulo = self::MODULO_SERVICIOS;
                }

                $showPath = $this->generateUrl('comprobantes_compra_show', array('id' => $comprobante->getId()));

                $jsonResult[] = $this->getComprobanteData($comprobante, $beneficiario, $modulo, $showPath);
            }
        }

        // COMPROBANTES DE OBRA *****/

        $comprobantesObra = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:Obras\ComprobanteObra', $this->getEntityManager())
                ->createQueryBuilder('c')
                ->where('c.idProveedor IN (:idProveedores)')
                ->andWhere('c.'.$fechaFiltro.' between :fechaInicio AND :fechaFin')
                ->setParameter('idProveedores', $idProveedores, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                ->orderBy('c.id', 'DESC')
                ->getQuery()
                ->getResult();

        // Por cada comprobante de obra
        foreach ($comprobantesObra as $comprobante) {

            $ordenPago = $comprobante->getOrdenPago();

            // Si NO tiene AC/OP o tiene y NO está ANULADA
            if ($ordenPago == null || ($ordenPago != null && !$ordenPago->getEstaAnulada())) {

                $beneficiario = $comprobante->getProveedor();

                $modulo = self::MODULO_OBRAS;

                $showPath = $this->generateUrl('comprobanteobra_show', array('id' => $comprobante->getId()));

                $jsonResult[] = $this->getComprobanteData($comprobante, $beneficiario, $modulo, $showPath);
            }
        }

        // COMPROBANTES DE CONSULTORIA *****/

        $idConsultores = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')
                ->createQueryBuilder('c')
                ->select('c.id')
                ->where('upper(c.razonSocial) LIKE \'%' . strtoupper($terminoBusqueda) . '%\'')
                ->orWhere('c.CUIT LIKE \'%' . strtoupper($terminoBusqueda) . '%\'')
                ->orWhere('c.CUIT LIKE \'%' . strtoupper($terminoBusquedaSinGuiones) . '%\'')
                ->getQuery()
                ->getResult();

        if (empty($idConsultores)) {
            $idConsultores[] = -1;
        }

        $comprobantesConsultoria = $this->getDoctrine()
                ->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria', $this->getEntityManager())
                ->createQueryBuilder('cc')
                ->innerJoin('cc.contrato', 'c')
                ->where('c.idConsultor IN (:idConsultores)')
                ->andWhere('cc.'.$fechaFiltro.' between :fechaInicio AND :fechaFin')
                ->setParameter('idConsultores', $idConsultores, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->setParameter('fechaInicio', $fechaInicio, \Doctrine\DBAL\Types\Type::DATETIME)
                ->setParameter('fechaFin', $fechaFin, \Doctrine\DBAL\Types\Type::DATETIME)
                ->orderBy('c.id', 'DESC')
                ->getQuery()
                ->getResult();

        // Por cada comprobante de consultoria
        foreach ($comprobantesConsultoria as $comprobante) {

            $ordenPago = $comprobante->getOrdenPago();

            // Si NO tiene AC/OP o tiene y NO está ANULADA
            if ($ordenPago == null || ($ordenPago != null && !$ordenPago->getEstaAnulada())) {

                $beneficiario = $comprobante->getConsultor();

                $modulo = self::MODULO_CONSULTORIA;

                $showPath = $this->generateUrl('comprobante_consultoria_show', array('id' => $comprobante->getId()));

                $jsonResult[] = $this->getComprobanteData($comprobante, $beneficiario, $modulo, $showPath);
            }
        }

        return new JsonResponse($jsonResult);
    }

    
    /**
     * 
     * @param type $comprobante
     * @param type $beneficiario
     * @param type $modulo
     * @param type $showPath
     * @return type
     */
    private function getComprobanteData($comprobante, $beneficiario, $modulo, $showPath) {
        
        $em_autenticacion = $this->getDoctrine()->getManager(EntityManagers::getEmAutenticacion());
                
        $usuarioCreacion = ( $comprobante->getIdUsuarioCreacion() != null ) ? $em_autenticacion->getRepository('ADIFAutenticacionBundle:Usuario')->find($comprobante->getIdUsuarioCreacion())->getUsername() : "-";
        
        $ordenPago = $comprobante->getOrdenPago();

        $nombreComprobante = $comprobante->getTipoComprobante()->__toString();
		
		if ($comprobante->getLetraComprobante() != null) {
		//	$nombreComprobante .= ' (' . $comprobante->getLetraComprobante()->__toString() . ') ';
		}
		
		if ($comprobante->getNumeroCompleto() != null) {
		//	$nombreComprobante .= ' - ' . $comprobante->getNumeroCompleto();
		}
		
        $numeroOrdenCompra = $modulo == self::MODULO_COMPRAS //
                ? ($comprobante->getOrdenCompra() != null) //
                        ? $comprobante->getOrdenCompra()->getNumeroOrdenCompra() //
                        : '-'//
                : '-';

        $numeroOrdenPago = $ordenPago != null ? (
                !$ordenPago->getEsAutorizacionContable() //
                        ? $ordenPago->getNumeroOrdenPago() //
                        : ($ordenPago->getNumeroAutorizacionContable() . ' (AC)')
                ) //
                : '-';

        $fechaOrdenPago = $ordenPago != null ? (
                !$ordenPago->getEsAutorizacionContable() //
                        ? $ordenPago->getFechaOrdenPago()->format('d/m/Y') //
                        : ($ordenPago->getFechaAutorizacionContable()->format('d/m/Y') . ' (AC)')
                ) //
                : '-';

        $pago = $ordenPago != null && $ordenPago->getPagoOrdenPago() != null //
                ? $ordenPago->getPagoOrdenPago()->getDetallePagos() //
                : '-';

		$idAsientoContableComprobante = $comprobante->getAsientoContable() != null
			? $comprobante->getAsientoContable()->getId()
			: '-';

        $idAsientoOP = $ordenPago != null && $ordenPago->getAsientoContable() != null //
                ? $ordenPago->getAsientoContable()->getId() //
                : '-';

        return array(
            'id' => $comprobante->getId(),
            'fechaCreacion' => $comprobante->getFechaCreacion()->format('d/m/Y'),
            'usuario' => $comprobante->getUsuarioCreacion() != null ? $comprobante->getUsuarioCreacion()->getUsername() : '-',
            'beneficiario' => $beneficiario->getRazonSocial(),
            'cuit' => $beneficiario->getCUIT(),
            'comprobante' => $nombreComprobante,
			'idAsientoContableComprobante' => $idAsientoContableComprobante,
            'fechaComprobante' => $comprobante->getFechaComprobante()->format('d/m/Y'),
            'fechaIngresoADIF' => ($comprobante->getFechaIngresoADIF() != null) 
				? $comprobante->getFechaIngresoADIF()->format('d/m/Y') 
				: $comprobante->getFechaIngresoADIFDocumentoFinanciero()->format('d/m/Y') . ' (documento financiero)',
            'numeroReferencia' => $comprobante->getNumeroReferencia() != null ? $comprobante->getNumeroReferencia() : '-',
            'importeTotal' => $comprobante->getTotal(),
            'modulo' => $modulo,
            'ordenCompra' => $numeroOrdenCompra,
            'ordenPago' => $numeroOrdenPago,
            'fechaOrdenPago' => $fechaOrdenPago,
            'idAsientoOP' => $idAsientoOP,
            'fechaContable' => $comprobante->getFechaContable()->format('d/m/Y'),
            'pago' => $pago,
            'showPath' => $showPath,
            'comprobanteAnulado' => $comprobante->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO,
            'fechaVencimiento'  => !is_null($comprobante->getFechaVencimiento()) 
                                    ? $comprobante->getFechaVencimiento()->format('d/m/Y')
                                    : '-',
			'descripcionComprobante' => $comprobante->getPrimeraDescripcionRenglon(),
            'nombreCbte' => $comprobante->getTipoComprobante()->__toString(),

            'letra' => ( $comprobante->getLetraComprobante() != NULL ) 
                        ? $comprobante->getLetraComprobante()->__toString() 
                        : '-',
            'nroCbte' => $comprobante->getNumeroCompleto(),
            'usuarioName' => $usuarioCreacion,
        );
    }

    /**
     *
     * @Route("/validar_pago_parcial/", name="comprobantes_validar_pago_parcial")
     */
    public function validarComprobanteSinPagoParcialPendientePagoAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idsComprobantes = $request->request->get('ids_comprobantes');

        $isComprobantesSinPagosParciales = true;

        foreach ($idsComprobantes as $idComprobante) {

            /* @var $comprobante Comprobante */
            $comprobante = $em->getRepository('ADIFContableBundle:Comprobante')
                    ->find($idComprobante);

            if ($comprobante->getTienePagoParcialPendientePago()) {

                $isComprobantesSinPagosParciales = false;

                break;
            }
        }

        return new JsonResponse($isComprobantesSinPagosParciales);
    }

    /**
     *
     * @Route("/editar_fecha_anulacion/", name="comprobantes_editar_fecha_anulacion")
     */
    public function updateFechaAnulacionAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $numeroAsiento = $request->request->get('numero_asiento');

        $fecha = $request->request->get('fecha');

        $idComprobante = $request->request->get('id_comprobante');

        $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')
                ->findOneByNumeroAsiento($numeroAsiento);

        // Si existe el asiento Y el usuario logueado genera asientos contables
        if (!$asientoContable && false === $this->get('security.context')->isGranted('ROLE_DETACH_ASIENTO')) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
        }

        $comprobante = $em->getRepository('ADIFContableBundle:Comprobante')
                ->find($idComprobante);

        $comprobante->setFechaAnulacion(\DateTime::createFromFormat('d/m/Y', $fecha));

        $em->persist($comprobante);

        $em->flush();

        return new Response();
    }
    
}
