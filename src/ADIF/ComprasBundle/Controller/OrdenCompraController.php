<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoOrdenCompra;
use ADIF\ComprasBundle\Entity\OrdenCompra;
use ADIF\ComprasBundle\Entity\RenglonOrdenCompra;
use ADIF\ComprasBundle\Form\OrdenCompraType;
use ADIF\ComprasBundle\Form\RenglonOrdenCompraType;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\AutenticacionBundle\Entity\Usuario;
use mPDF;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Types\Type;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoMoneda;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ADIF\BaseBundle\Session\EmpresaSession;
/**
 * OrdenCompra controller.
 *
 * @Route("/ordenescompra")
 */
class OrdenCompraController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Ordenes de compra' => $this->generateUrl('ordenescompra')
        );
    }

    /**
     * Lists all OrdenCompra entities.
     *
     * @Route("/", name="ordenescompra")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $securityContext = $this->get('security.context');

        $bread = $this->base_breadcrumbs;
        $bread['Ordenes de compra'] = null;

        $returnArray = array(
            'breadcrumbs' => $bread,
            'page_title' => 'Ordenes de compra',
            'page_info' => 'Lista de ordenes de compra'
        );

        if (true === $securityContext->isGranted('ROLE_COMPRAS_PANEL_CONTROL')) {

            $returnArray['cantidad_solicitudes_pendientes'] = $em->getRepository('ADIFComprasBundle:RenglonSolicitudCompra')
                    ->getCantidadRenglonesSolicitudCompraSupervisados();

            $returnArray['cantidad_requerimientos_pendientes'] = $em->getRepository('ADIFComprasBundle:Requerimiento')
                    ->getCantidadRequerimientosPendientesCotizacionByUsuario($this->getUser()->getId());

            $returnArray['cantidad_oc_pendientes'] = $em->getRepository('ADIFComprasBundle:OrdenCompra')
                    ->getCantidadOrdenesCompraPendientes();
        }

        return $returnArray;
    }

    /**
     * Tabla para OrdenCompra.
     *
     * @Route("/index_table_principal/", name="ordenescompra_index_table_principal")
     * @Method("GET|POST")
     */
    public function indexTablePrincipalAction(Request $request) {

        $tipo = $request->query->get('tipo');

        $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaInicio') . ' 00:00:00');

        $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaFin') . ' 23:59:59');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if ($tipo == 'pendientes-generacion') {

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id', 'id');
            $rsm->addScalarResult('numeroOrdenCompra', 'numeroOrdenCompra');
            $rsm->addScalarResult('numeroCalipso', 'numeroCalipso');
            $rsm->addScalarResult('fechaOrdenCompra', 'fechaOrdenCompra');
            $rsm->addScalarResult('proveedor', 'proveedor');
            $rsm->addScalarResult('cotizacion', 'cotizacion');
            $rsm->addScalarResult('idRequerimiento', 'idRequerimiento');
            $rsm->addScalarResult('descripcionRequerimiento', 'descripcionRequerimiento');
            $rsm->addScalarResult('monto', 'monto');
            $rsm->addScalarResult('saldo', 'saldo');
            $rsm->addScalarResult('estadoOrdenCompra', 'estadoOrdenCompra');
            $rsm->addScalarResult('nombreUsuario', 'nombreUsuario');
            $rsm->addScalarResult('aliasTipoImportancia', 'aliasTipoImportancia');
            $rsm->addScalarResult('simboloTipoMoneda', 'simboloTipoMoneda');
            $rsm->addScalarResult('idordenCompraOriginal', 'idordenCompraOriginal');
            $rsm->addScalarResult('muestraReporteDesvio', 'muestraReporteDesvio');
            $rsm->addScalarResult('esServicio', 'esServicio');
            $rsm->addScalarResult('rubros', 'rubros');
            $rsm->addScalarResult('bienes', 'bienes');


            $native_query = $em->createNativeQuery('
                 call sp_vista_ordenesCompra(?,?,?)
             ', $rsm);

            $native_query->setParameter(1, 'pendientes-generacion');
            $native_query->setParameter(2, $fechaInicio, Type::DATETIME);
            $native_query->setParameter(3, $fechaFin, Type::DATETIME);
            $ordenesCompraSinNumero = $native_query->getResult();


            return $this->render('ADIFComprasBundle:OrdenCompra:index_table.html.twig', array(
                        'entities' => $ordenesCompraSinNumero,
                        //  'saldosOrdenenesCompra' => null,
                        'muestraSaldo' => false,
                        'tipo' => 'pendientes-generacion'
            ));
        } //
        elseif ($tipo == 'generadas') {

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('id', 'id');
            $rsm->addScalarResult('numeroOrdenCompra', 'numeroOrdenCompra');
            $rsm->addScalarResult('numeroCalipso', 'numeroCalipso');
            $rsm->addScalarResult('fechaOrdenCompra', 'fechaOrdenCompra');
            $rsm->addScalarResult('proveedor', 'proveedor');
            $rsm->addScalarResult('cotizacion', 'cotizacion');
            $rsm->addScalarResult('idRequerimiento', 'idRequerimiento');
            $rsm->addScalarResult('descripcionRequerimiento', 'descripcionRequerimiento');
            $rsm->addScalarResult('monto', 'monto');
            $rsm->addScalarResult('saldo', 'saldo');
            $rsm->addScalarResult('nombreUsuario', 'nombreUsuario');
            $rsm->addScalarResult('estadoOrdenCompra', 'estadoOrdenCompra');
            $rsm->addScalarResult('aliasTipoImportancia', 'aliasTipoImportancia');
            $rsm->addScalarResult('simboloTipoMoneda', 'simboloTipoMoneda');
            $rsm->addScalarResult('idordenCompraOriginal', 'idordenCompraOriginal');
            $rsm->addScalarResult('muestraReporteDesvio', 'muestraReporteDesvio');
            $rsm->addScalarResult('esServicio', 'esServicio');
            $rsm->addScalarResult('rubros', 'rubros');
            $rsm->addScalarResult('bienes', 'bienes');


            $native_query = $em->createNativeQuery('
                 call sp_vista_ordenesCompra(?,?,?)
             ', $rsm);

            $native_query->setParameter(1, 'generadas');
            $native_query->setParameter(2, $fechaInicio, Type::DATETIME);
            $native_query->setParameter(3, $fechaFin, Type::DATETIME);
            $ordenesCompraConNumero = $native_query->getResult();

            return $this->render('ADIFComprasBundle:OrdenCompra:index_table.html.twig', array(
                        'entities' => $ordenesCompraConNumero,
                        //'saldosOrdenenesCompra' => $saldosOrdenenesCompra,
                        'muestraSaldo' => true,
                        'tipo' => 'generadas'
            ));
        }
    }

    /**
     * Creates a new OrdenCompra entity.
     *
     * @Route("/insertar", name="ordenescompra_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:OrdenCompra:new.html.twig")
     */
    public function createAction(Request $request) {

        $ordenCompra = new OrdenCompra();

        $form = $this->createCreateForm($ordenCompra);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Seteo el estado
            $ordenCompra->setEstadoOrdenCompra(
                    $em->getRepository('ADIFComprasBundle:EstadoOrdenCompra')
                            ->findOneByDenominacionEstado(ConstanteEstadoOrdenCompra::ESTADO_OC_BORRADOR)
            );

            // Seteo la observacion por defecto
            $parametrizacionOrdenCompra = $em->getRepository('ADIFComprasBundle:OrdenCompraParametrizacion')
                    ->findOneBy(array(), array('id' => 'DESC'));

            if (null != $parametrizacionOrdenCompra) {
                $ordenCompra->setObservacion($parametrizacionOrdenCompra->getObservacion());
            }

            $em->persist($ordenCompra);
            $em->flush();

            return $this->redirect($this->generateUrl('ordenescompra'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $ordenCompra,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear orden de compra',
        );
    }

    /**
     * Creates a form to create a OrdenCompra entity.
     *
     * @param OrdenCompra $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(OrdenCompra $entity) {
        $form = $this->createForm(new OrdenCompraType(), $entity, array(
            'action' => $this->generateUrl('ordenescompra_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
            'entity_manager_conta' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
        ));

        $form
                ->add('save', 'submit', array(
                    'label' => 'Guardar borrador'
                ))
                ->add('generate', 'submit', array(
                    'label' => 'Generar orden de compra'
                ))
        ;

        return $form;
    }

    /**
     * Displays a form to create a new OrdenCompra entity.
     *
     * @Route("/crear", name="ordenescompra_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new OrdenCompra();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear orden de compra'
        );
    }

    /**
     * Finds and displays a OrdenCompra entity.
     *
     * @Route("/{id}", name="ordenescompra_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:OrdenCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompra.');
        }

        $detalleOrdenCompra = $entity->getNumero() != null ? 'Orden de compra ' . $entity->getNumero() : 'Detalle';

        $bread = $this->base_breadcrumbs;
        $bread[$detalleOrdenCompra] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver orden de compra'
        );
    }

    /**
     * Edit observaciones from OrdenCompra entity.
     *
     * @Route("/editar_observaciones", name="edit_observaciones_ordenescompra")
     * @Method("POST")
     */
    public function editObservacionOC(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id = $request->get('id');
        $observaciones = trim($request->get('observaciones'), null);

        $entity = $em->getRepository('ADIFComprasBundle:OrdenCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompra.');
        }

        $entity->setObservacion($observaciones);

        try {

            $em->persist($entity);
            $em->flush();

            return new JsonResponse([
                'title' => 'Aviso',
                'msg' => 'Se modificaron las observaciones correctamente.'
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'title' => 'Error',
                'msg' => 'No se pudo modificar la observación de la orden de compra. Intente más tarde nuevamente.'
            ]);
        }
    }

    /**
     * Displays a form to edit an existing OrdenCompra entity.
     *
     * @Route("/editar/{id}", name="ordenescompra_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:OrdenCompra:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:OrdenCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompra.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar orden de compra'
        );
    }

    /**
     * Creates a form to edit a OrdenCompra entity.
     *
     * @param OrdenCompra $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(OrdenCompra $entity) {
        $form = $this->createForm(new OrdenCompraType(), $entity, array(
            'action' => $this->generateUrl('ordenescompra_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
            'entity_manager_conta' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),

        ));

        $form
                ->add('save', 'submit', array(
                    'label' => 'Guardar borrador'
                ))
                ->add('generate', 'submit', array(
                    'label' => 'Generar orden de compra'
                ))
        ;

        return $form;
    }

    /**
     * Edits an existing OrdenCompra entity.
     *
     * @Route("/actualizar/{id}", name="ordenescompra_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:OrdenCompra:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ordenCompra = $em->getRepository('ADIFComprasBundle:OrdenCompra')->find($id);

        if (!$ordenCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompra.');
        }

        $editForm = $this->createEditForm($ordenCompra);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $ordenCompra->setFechaUltimaActualizacion(new \DateTime());

            // Le seteo el estado a la OrdenCompra
            $hayError = $this->setEstadoAOrdenCompra($request, $ordenCompra);
			


            if (!$hayError) {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {

                    $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

                    $em->flush();

                    $emContable->flush();

                    $em->getConnection()->commit();
					
					$this->get('session')->getFlashBag()
						->add('success', 'La orden de compra se generó con éxito. Se genero el nro: ' . $ordenCompra->getNumeroOrdenCompra());
					
                } //.
                catch (\Exception $e) {

                    $em->getConnection()->rollback();
                    $em->close();

                    throw $e;
                }
            }//. 
            else {
                $request->attributes->set('form-error', true);
            }

            return $this->redirect($this->generateUrl('ordenescompra'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $ordenCompra,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar orden de compra'
        );
    }

    /**
     * Deletes a OrdenCompra entity.
     *
     * @Route("/borrar/{id}", name="ordenescompra_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:OrdenCompra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompra.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('ordenescompra'));
    }

    /**
     * Tabla para OrdenCompra.
     *
     * @Route("/index_table/", name="ordenescompra_index_table")
     * @Method("GET|POST")
     * 
     */
    public function indexTableAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $incluirOCSinSaldo = (bool) $request->query->get('oc_sin_saldo');
        
        $idProveedor = $request->query->get('id_proveedor');

        $ordenes_compra = $em
                ->getRepository('ADIFComprasBundle:OrdenCompra')
                ->getOrdenesCompra($idProveedor, $incluirOCSinSaldo);

        return $this->render('ADIFComprasBundle:OrdenCompra:index_table_por_proveedor.html.twig', array('ordenes_compra' => $ordenes_compra));
    }

    /**
     * @Route("/renglones_orden_compra/", name="renglones_orden_compra")
     * @Method("GET|POST")
     */
    public function getRenglonesOrdenCompraAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
		$emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $idOC = $request->request->get('id_orden_compra');

        $ordenCompra = $em->getRepository('ADIFComprasBundle:OrdenCompra')->find($idOC);

        if (!$ordenCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompra.');
        }

        $esNotaCredito = $request->request->get('es_nota_credito');

        $renglonesOrdenCompra = $em->getRepository('ADIFComprasBundle:RenglonOrdenCompra')
                ->getRenglonesOrdenCompra($idOC, $esNotaCredito);
				
		//\Doctrine\Common\Util\Debug::dump( $renglonesOrdenCompra ); exit; 	

        // Si el tipo de comprobante es una NotaCredito, filtro los renglones
        if ($esNotaCredito == 1) {

            $renglonesOrdenCompraFiltrados = array_filter($renglonesOrdenCompra, function($renglonOrdenCompra) {

                $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

                $filterReturn = false;

                $renglonesComprobanteCompra = $emContable->getRepository('ADIFContableBundle:RenglonComprobanteCompra')
                        ->findByIdRenglonOrdenCompra($renglonOrdenCompra['id']);

                foreach ($renglonesComprobanteCompra as $renglonComprobanteCompra) {


                    // Si el comprobante asociado NO es una NotaCredito
                    if ($renglonComprobanteCompra->getComprobante() != null //
                            && !$renglonComprobanteCompra->getComprobante()->getEsNotaCredito()) {

                        // Incluyo el RenglonOrdenCompra en el resultado
                        $filterReturn = true;

                        break;
                    }
                }


                return $filterReturn;
            });
        }
        // Sino, muestro todos los RenglonOrdenCompra
        else {
            $renglonesOrdenCompraFiltrados = $renglonesOrdenCompra;
        }
		
		for($i = 0; $i < count($renglonesOrdenCompraFiltrados); $i++) {
			
			if ($ordenCompra->getTipoMoneda() != null) {
				$renglonesOrdenCompraFiltrados[$i]['simboloTipoMoneda'] = $ordenCompra->getSimboloTipoMoneda();
				$renglonesOrdenCompraFiltrados[$i]['idTipoMoneda'] = $ordenCompra->getIdTipoMoneda();
				$renglonesOrdenCompraFiltrados[$i]['codigoTipoMoneda'] = $ordenCompra->getTipoMoneda()->getCodigoTipoMoneda();
				$renglonesOrdenCompraFiltrados[$i]['strTipoMoneda'] = $ordenCompra->getTipoMoneda()->__toString();
				
			} else {
				
				// por default PESO_ARGENTINO
				$tipoMoneda = $emContable->getRepository('ADIFContableBundle:TipoMoneda')->findOneBy(array(
					'codigoTipoMoneda' => ConstanteTipoMoneda::PESO_ARGENTINO
				));
				
				$renglonesOrdenCompraFiltrados[$i]['simboloTipoMoneda'] = $tipoMoneda->getSimboloTipoMoneda();
				$renglonesOrdenCompraFiltrados[$i]['idTipoMoneda'] = $tipoMoneda->getId();
				$renglonesOrdenCompraFiltrados[$i]['codigoTipoMoneda'] = ConstanteTipoMoneda::PESO_ARGENTINO; 
				$renglonesOrdenCompraFiltrados[$i]['strTipoMoneda'] = $tipoMoneda->__toString();
			}
		}
		
        return new JsonResponse($renglonesOrdenCompraFiltrados);
    }

    /**
     * @Route("/adicionales_cotizacion/", name="adicionales_cotizacion")
     * @Method("GET|POST")
     */
    public function getAdicionalesCotizacionAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        return new JsonResponse($em->getRepository('ADIFComprasBundle:OrdenCompra')->getAdicionalesCotizacion($request->request->get('id_orden_compra')));
    }

    /**
     * 
     * @param Request $request
     * @param OrdenCompra $ordenCompra
     * @return boolean
     */
    private function setEstadoAOrdenCompra(Request $request, OrdenCompra $ordenCompra) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $accion = $request->request->get('accion');

        $hayError = false;

        if (null != $accion) {

            // Si se apretó el boton "Guardar borrador"
            if ('save' == $accion) {

                // Obtengo el EstadoOrdenCompra cuya denominacion sea igual a "Borrador"
                $estadoOrdenCompra = $em->getRepository('ADIFComprasBundle:EstadoOrdenCompra')->
                        findOneByDenominacionEstado(ConstanteEstadoOrdenCompra::ESTADO_OC_BORRADOR);

                $ordenCompra->setEstadoOrdenCompra($estadoOrdenCompra);
            }

            // Si se apretó el boton "Generar orden de compra"
            else if ('generate' == $accion) {

                $ordenCompraService = $this->get('adif.orden_compra_service');

                $ordenCompra->setNumeroOrdenCompra($ordenCompraService->getSiguienteNumeroOrdenCompra());

                // Obtengo el EstadoOrdenCompra cuya denominacion sea igual a "Generada"
                $estadoOrdenCompra = $em->getRepository('ADIFComprasBundle:EstadoOrdenCompra')->
                        findOneByDenominacionEstado(ConstanteEstadoOrdenCompra::ESTADO_OC_GENERADA);

                $ordenCompra->setEstadoOrdenCompra($estadoOrdenCompra);

                // A la OrdenCompra le seteo el Usuario
                $ordenCompra->setUsuario($this->getUser());
	
				// Persisto los asientos presupuestarios
                $mensajeErrorAsientoPresupuestario = $this->get('adif.contabilidad_presupuestaria_service')
                        ->crearDefinitivoFromOrdenCompra($ordenCompra);

						
                if ($ordenCompra->getOrdenCompraOriginal() == null) {
                    // Seteo la orden de compra en su estado original
                    $ordenCompra->setOrdenCompraOriginal($this->getOrdenCompraOriginal($ordenCompra));
                }

                // Si el asiento presupuestario falló
                if ($mensajeErrorAsientoPresupuestario != '') {
                    $this->get('session')->getFlashBag()->add('error', $mensajeErrorAsientoPresupuestario);

                    $hayError = true;
                }
            }
        } else {
            $hayError = true;
        }

        return $hayError;
    }

    /**
     * Print an OrdenCompra entity.
     *
     * @Route("/print/{id}", name="ordenescompra_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $ordenCompra OrdenCompra */
        $ordenCompra = $em->getRepository('ADIFComprasBundle:OrdenCompra')->find($id);

        if (!$ordenCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompra.');
        }

        $usuarioCreacionOrdenCompraUsername = '';
        $emAutenticacion = $this->getDoctrine()->getManager(EntityManagers::getEmAutenticacion());
        $usuarioCreacionOrdenCompraUsername = $emAutenticacion
                ->createQuery(
                        "SELECT u.username FROM ADIFAutenticacionBundle:Usuario u " .
                        "WHERE u.id = :idUsuario"
                )
                ->setParameter('idUsuario', $ordenCompra->getIdUsuarioCreacion())
                ->getResult()
        ;

        $filename = 'ordenCompra_' . $ordenCompra->getNumero() . '.pdf';
        $versiones = array('ORIGINAL', 'DUPLICADO 1', 'DUPLICADO 2');

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);
        $footer = '';
        $v = count($versiones);
        $i = 1;
        
        $empresaSession = EmpresaSession::getInstance();
        $idEmpresa = $empresaSession->getIdEmpresa();

        foreach ($versiones as $leyenda) {

            $html = '<html><head><style type="text/css">' . $this->renderView('::PDF/mpdf.default.css.twig') . '</style></head><body>';
            $html .= $this->renderView(
                    'ADIFComprasBundle:OrdenCompra:print.show.html.twig', [
                'oc' => $ordenCompra,
                'leyenda' => $leyenda,
                'idEmpresa' => $idEmpresa,
                'usuarioCreacion' => $usuarioCreacionOrdenCompraUsername[0]['username']
                    ]
            );

            if ($mpdfService->y >= 212) {
                $mpdfService->AddPage();
            }

            //$footer = $this->renderView('ADIFComprasBundle:OrdenCompra:footer.html.twig');
            //$mpdfService->SetHTMLFooter($footer);

            $html .= '</body></html>';

            $mpdfService->WriteHTML($html);
            if( $i < $v ) {
                $mpdfService->AddPage();
            }
            $i++;
        }



        if ($ordenCompra->getEsBorrador()) {
            $mpdfService->SetWatermarkText('BORRADOR');
            $mpdfService->showWatermarkText = true;
        } //.
        else if ($ordenCompra->getEstaAnulada()) {
            $mpdfService->SetWatermarkText('ANULADA');
            $mpdfService->showWatermarkText = true;
        }

        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    /**
     * Encuentra y anula una OrdenCompra.
     *
     * @Route("/anular/{id}", name="ordenescompra_anular")
     * @Method("GET")
     * @Template()
     */
    public function anularAction(Request $request, $id) {

        $emCompras = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $ordenCompra OrdenCompra */
        $ordenCompra = $emCompras->getRepository('ADIFComprasBundle:OrdenCompra')->find($id);

        if (!$ordenCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompra.');
        }

        // Valido que la OC se pueda eliminar
        if ($this->validarEliminacionOrdenCompra($ordenCompra->getId())) {

            $contabilidadPresupuestariaService = $this->get('adif.contabilidad_presupuestaria_service');

            $ordenCompraService = $this->get('adif.orden_compra_service');

            $estadoOriginal = $ordenCompra->getEstadoOrdenCompra()->getDenominacionEstado();

            // Seteo el estado de la OC a "Anulada"
            $this->setEstadoOrdenCompra($emCompras, $ordenCompra, ConstanteEstadoOrdenCompra::ESTADO_OC_ANULADA);

            // Seteo la fecha de anulación
            $ordenCompra->setFechaAnulacion(new \DateTime());

            // Seteo el motivo de anulación
            $ordenCompra->setMotivoAnulacion($request->query->get('motivo_anulacion'));

            // Elimino los Definitivos asociados a la OrdenCompra
            $contabilidadPresupuestariaService->eliminarDefinitivoFromOrdenCompra($ordenCompra);

            // Si la OrdenCompra tiene una Cotizacion asociada
            if (null != $ordenCompra->getCotizacion()) {

                $idProveedor = $ordenCompra->getProveedor()->getId();
                $idRequerimiento = $ordenCompra->getCotizacion()->getRequerimiento()->getId();
                $idCotizacion = $ordenCompra->getCotizacion()->getId();
                $idsRenglonCotizacion = $ordenCompra->getCotizacion()->getRenglonCotizacionIds();

                // Si la OrdenCompra esta en estado BORRADOR
                if ($estadoOriginal == ConstanteEstadoOrdenCompra::ESTADO_OC_BORRADOR) {

                    foreach ($ordenCompra->getCotizacion()->getRenglonesCotizacion() as $renglonCotizacion) {

                        /* @var $renglonCotizacion \ADIF\ComprasBundle\Entity\RenglonCotizacion */
                        $renglonCotizacion->setCotizacionElegida(false);
                    }

                    foreach ($ordenCompra->getCotizacion()->getAdicionalesCotizacion() as $adicionalCotizacion) {

                        /* @var $adicionalCotizacion \ADIF\ComprasBundle\Entity\AdicionalCotizacion */
                        $adicionalCotizacion->setAdicionalElegido(false);
                    }

                    $emCompras->flush();
                } else {

                    // Genero la nueva OrdenCompra
                    $ordenCompraService
                            ->generarOrdenCompraFromCotizacion($idProveedor, $idRequerimiento, $idCotizacion, $idsRenglonCotizacion, $ordenCompra);
                }
            } else {

                // Comienzo la transaccion
                $emCompras->getConnection()->beginTransaction();

                try {

                    $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

                    $emCompras->flush();

                    $emContable->flush();

                    $emCompras->getConnection()->commit();
                } //.
                catch (\Exception $e) {

                    $emCompras->getConnection()->rollback();
                    $emCompras->close();

                    throw $e;
                }
            }

            $this->get('session')->getFlashBag()
                    ->add('success', 'La orden de compra se anuló con éxito.');
        } else {
            $this->get('session')->getFlashBag()
                    ->add('error', 'La orden de compra no se pudo anular ya que se encuentra asociada a un comprobante.');
        }

        return $this->redirect($this->generateUrl('ordenescompra'));
    }

    /**
     * 
     * @param type $emCompras
     * @param type $ordenCompra
     * @param type $denominacionEstado
     */
    private function setEstadoOrdenCompra($emCompras, $ordenCompra, $denominacionEstado) {

        $estadoOrdenCompra = $emCompras->getRepository('ADIFComprasBundle:EstadoOrdenCompra')
                ->findOneByDenominacionEstado($denominacionEstado);

        $ordenCompra->setEstadoOrdenCompra($estadoOrdenCompra);
    }

    /**
     * Retorna <code>true</code> si se puede eliminar la OrdenCompra, 
     * caso contrario retorna <code>false</code>.
     * 
     * @param type $idOrdenCompra
     * @return boolean
     */
    private function validarEliminacionOrdenCompra($idOrdenCompra) {

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        // Valido que no haya ComprobanteCompra asociados a la OrdenCompra
        $countComprobanteCompra = $emContable
                ->getRepository('ADIFContableBundle:ComprobanteCompra')
                ->createQueryBuilder('cc')
                ->select('count(cc.id)')
                ->where('cc.idOrdenCompra = :id')->setParameter('id', $idOrdenCompra)
				->andWhere('cc.fechaAnulacion IS NULL')
                ->getQuery()
                ->getSingleScalarResult();

        return $countComprobanteCompra == 0;
    }

    /**
     * Devuelve el template para modificar los saldos
     *
     * @Route("/editar_saldo/", name="ordenescompra_editar_saldo")
     * @Method("POST")   
     * @Template("ADIFComprasBundle:OrdenCompra:saldo_edit.html.twig")
     */
    public function getEditSaldoFormAction(Request $request) {

        $tipoAdicional = array();
        $alicuotaIva = array();

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        /* @var $adicional \ADIF\ComprasBundle\Entity\TipoAdicional */
        $adicionales = $emCompras->getRepository('ADIFComprasBundle:TipoAdicional')->findAll();

        foreach ($adicionales as $adicional) {
            $tipoAdicional[$adicional->getId()] = $adicional->getDenominacionAdicional();
        }

        /* @var $alicuota \ADIF\ContableBundle\Entity\AlicuotaIva */
        $alicuotas = $emContable->getRepository('ADIFContableBundle:AlicuotaIva')->findAll();

        foreach ($alicuotas as $alicuota) {
            $alicuotaIva[$alicuota->getId()] = $alicuota->getValor();
        }

        $renglonOrdenCompraForm = $this->createRenglonOrdenCompraForm(new RenglonOrdenCompra());

        return array(
            'tipoAdicional' => $tipoAdicional,
            'alicuotaIva' => $alicuotaIva,
            'form_renglon' => $renglonOrdenCompraForm->createView()
        );
    }

    /**
     * @Route("/modificar_renglon/", name="ordenescompra_modificar_renglon")
     * @Method("POST")
     */
    public function modificarRenglonAction(Request $request) {

        $emCompras = $this->getDoctrine()->getManager($this->getEntityManager());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $id_renglon = $request->request->get('id');

        $cantidad = floatval(str_replace(',', '.', $request->request->get('cantidad')));

        $precioUnitario = $request->request->get('precioUnitario');

        /* @var $renglon RenglonOrdenCompra */
        $renglon = $emCompras->getRepository('ADIFComprasBundle:RenglonOrdenCompra')
                ->find($id_renglon);

        if (!$renglon) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonOrdenCompra.');
        }

        $montoRenglonOriginal = $renglon->getPrecioTotalProrrateado();

        $diferencia = $renglon->getRestante() - $cantidad;

        $renglon->setCantidad($renglon->getCantidad() - $diferencia);
        $renglon->setRestante($cantidad);
        $renglon->setPrecioUnitario(floatval(str_replace(',', '.', $precioUnitario)));

        $nuevoMontoRenglon = $renglon->getPrecioTotalProrrateado();

        $error = false;

        $emCompras->getConnection()->beginTransaction();

        /* @var $definitivo \ADIF\ContableBundle\Entity\DefinitivoCompra */
        $definitivo = $emContable->getRepository('ADIFContableBundle:DefinitivoCompra')
                ->findOneByIdRenglonOrdenCompra($id_renglon);

        if ($montoRenglonOriginal > $nuevoMontoRenglon) {
            $nuevoMonto = $definitivo->getMonto() - ($montoRenglonOriginal - $nuevoMontoRenglon);
        } else {
            $nuevoMonto = $definitivo->getMonto() + ($nuevoMontoRenglon - $montoRenglonOriginal);
        }

        $definitivo->setMonto($nuevoMonto);

        try {

            $emCompras->flush();

            $emContable->flush();

            $emCompras->getConnection()->commit();
        } //.
        catch (\Exception $e) {

            $emCompras->getConnection()->rollback();
            $emCompras->close();

            $error = true;
        }

        if ($error) {
            $this->get('session')->getFlashBag()
                    ->add('error', 'No se pudo modificar el renglón correctamente.');
        } else {
            $this->get('session')->getFlashBag()
                    ->add('success', 'El renglón de la orden de compra se modificó con éxito.');
        }

        return new JsonResponse('ok');
    }

    /**
     * @Route("/agregar_renglon/", name="ordenescompra_agregar_renglon")
     * @Method("POST")
     */
    public function agregarRenglonAction(Request $request) {

        $emCompras = $this->getDoctrine()->getManager($this->getEntityManager());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $idOrdenCompra = $request->request->get('idOrdenCompra');

        /* @var $ordenCompra OrdenCompra */
        $ordenCompra = $emCompras->getRepository('ADIFComprasBundle:OrdenCompra')
                ->find($idOrdenCompra);

        if (!$ordenCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompra.');
        }

        $renglonOrdenCompra = new RenglonOrdenCompra();

        $renglonOrdenCompra->setOrdenCompra($ordenCompra);

        $centroCosto = $emContable->getRepository('ADIFContableBundle:CentroCosto')
                ->find($request->request->get('idCentroCosto'));

        $renglonOrdenCompra->setCentroCosto($centroCosto);

        $bienEconomico = $emCompras->getRepository('ADIFComprasBundle:BienEconomico')
                ->find($request->request->get('idBienEconomico'));

        $renglonOrdenCompra->setBienEconomico($bienEconomico);


        $unidadMedida = $emCompras->getRepository('ADIFComprasBundle:UnidadMedida')
                ->find($request->request->get('idUnidadMedida'));

        $renglonOrdenCompra->setUnidadMedida($unidadMedida);

        $renglonOrdenCompra->setCantidad($request->request->get('cantidad'));
        $renglonOrdenCompra->setRestante($request->request->get('cantidad'));

        $renglonOrdenCompra->setPrecioUnitario(floatval(str_replace(',', '.', $request->request->get('precioUnitario'))));

        $renglonOrdenCompra->setTipoCambio(floatval(str_replace(',', '.', $request->request->get('tipoCambio'))));

        $renglonOrdenCompra->setIdAlicuotaIva($request->request->get('idAlicuotaIva'));

        $renglonOrdenCompra->setEsAmpliacion(true);

        $ordenCompra->addRenglon($renglonOrdenCompra);

        $emCompras->getConnection()->beginTransaction();

        $emCompras->persist($renglonOrdenCompra);

        try {

            $emCompras->flush();

            $mensajeError = $this->get('adif.contabilidad_presupuestaria_service')
                    ->crearDefinitivoFromRenglonOrdenCompra($renglonOrdenCompra, $bienEconomico, $centroCosto);

            $emContable->flush();

            $emCompras->getConnection()->commit();

            $idRenglonOrdenCompra = $renglonOrdenCompra->getId();
        } //.
        catch (\Exception $e) {

            $emCompras->getConnection()->rollback();

            $emCompras->close();

            $idRenglonOrdenCompra = null;
        }

        if ($idRenglonOrdenCompra) {

            $mensaje = 'El renglón de la orden de compra se agregó con éxito.';
        } else {

            $mensaje = 'No se pudo guardar el renglón de la orden de compra. '
                    . $mensajeError;
        }

        $jsonResponse = array(
            'id' => $idRenglonOrdenCompra,
            'mensaje' => $mensaje
        );

        return new JsonResponse($jsonResponse);
    }

    /**
     * @Route("/eliminar_renglon/", name="ordenescompra_eliminar_renglon")
     * @Method("POST")
     */
    public function eliminarRenglonAction(Request $request) {

        $emCompras = $this->getDoctrine()->getManager($this->getEntityManager());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $id_renglon = $request->request->get('id');

        /* @var $renglon RenglonOrdenCompra */
        $renglon = $emCompras->getRepository('ADIFComprasBundle:RenglonOrdenCompra')->find($id_renglon);

        if (!$renglon) {
            throw $this->createNotFoundException('No se puede encontrar la entidad RenglonOrdenCompra.');
        }

        $renglon->setRestante(0);

        /* @var $definitivo \ADIF\ContableBundle\Entity\DefinitivoCompra */
        $definitivo = $emContable->getRepository('ADIFContableBundle:DefinitivoCompra')
                ->findOneByIdRenglonOrdenCompra($id_renglon);

        if (!$definitivo) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DefinitivoCompra.');
        }

        $definitivo->setMonto($definitivo->getMonto() - $renglon->getPrecioTotalProrrateado());

        $error = false;

        $emCompras->getConnection()->beginTransaction();

        try {

            $emCompras->flush();

            $emContable->flush();

            $emCompras->getConnection()->commit();
        } //.
        catch (\Exception $e) {

            $emCompras->getConnection()->rollback();
            $emCompras->close();

            $error = true;
        }

        if ($error) {
            $this->get('session')->getFlashBag()->add('error', 'No se pudo eliminar el renglón de la orden de compra');
        } else {
            $this->get('session')->getFlashBag()
                    ->add('success', 'El renglón de la orden de compra se eliminó con éxito.');
        }

        return new JsonResponse('ok');
    }

    /**
     * @Route("/{id}/reporte_desvio/", name="ordenescompra_reporte_desvio")
     * @Method("GET")
     * @Template("")
     */
    public function reporteDesvioAction($id) {

        $emCompras = $this->getDoctrine()->getManager($this->getEntityManager());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $detalleCuentasPresupuestariasProvisorio = array();
        $detalleCuentasPresupuestariasDefinitivo = array();

        /* @var $ordenCompra OrdenCompra */
        $ordenCompra = $emCompras->getRepository('ADIFComprasBundle:OrdenCompra')
                ->find($id);

        if (!$ordenCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompra.');
        }

        if (!$ordenCompra->getEsServicio()) {

            $requerimiento = $ordenCompra->getRequerimiento();

            if ($requerimiento) {

                $totalProvisorios = 0;
                $totalDefinitivos = 0;

                foreach ($requerimiento->getRenglonesRequerimiento() as $renglonRequerimiento) {

                    // Get el Provisorio asociado
                    $provisorio = $emContable->getRepository('ADIFContableBundle:ProvisorioCompra')
                            ->findOneByIdRenglonRequerimiento($renglonRequerimiento->getId());

                    if (null != $provisorio) {

                        $cuentaPresupuestariaEconomicaProvisorio = $provisorio->getCuentaPresupuestariaEconomica();

                        if (!isset($detalleCuentasPresupuestariasProvisorio[$cuentaPresupuestariaEconomicaProvisorio->getId()])) {

                            $detalleCuentasPresupuestariasProvisorio[$cuentaPresupuestariaEconomicaProvisorio->getId()] = array();
                            $detalleCuentasPresupuestariasProvisorio[$cuentaPresupuestariaEconomicaProvisorio->getId()]['denominacionCuentaEconomica'] = $cuentaPresupuestariaEconomicaProvisorio->__toString();
                            $detalleCuentasPresupuestariasProvisorio[$cuentaPresupuestariaEconomicaProvisorio->getId()]['total'] = 0;
                        }

                        $detalleCuentasPresupuestariasProvisorio[$cuentaPresupuestariaEconomicaProvisorio->getId()]['total'] += $provisorio->getMonto();

                        $totalProvisorios+= $provisorio->getMonto();
                    }
                }

                foreach ($ordenCompra->getRenglones() as $renglonOrdenCompra) {

                    // Get el Definitivo asociado
                    $definitivo = $emContable->getRepository('ADIFContableBundle:DefinitivoCompra')
                            ->findOneByIdRenglonOrdenCompra($renglonOrdenCompra->getId());

                    if (null != $definitivo) {

                        $cuentaPresupuestariaEconomicaDefinitivo = $definitivo->getCuentaPresupuestariaEconomica();

                        if (!isset($detalleCuentasPresupuestariasDefinitivo[$cuentaPresupuestariaEconomicaDefinitivo->getId()])) {

                            $detalleCuentasPresupuestariasDefinitivo[$cuentaPresupuestariaEconomicaDefinitivo->getId()] = array();
                            $detalleCuentasPresupuestariasDefinitivo[$cuentaPresupuestariaEconomicaDefinitivo->getId()]['denominacionCuentaEconomica'] = $cuentaPresupuestariaEconomicaDefinitivo->__toString();
                            $detalleCuentasPresupuestariasDefinitivo[$cuentaPresupuestariaEconomicaDefinitivo->getId()]['total'] = 0;
                        }

                        $detalleCuentasPresupuestariasDefinitivo[$cuentaPresupuestariaEconomicaDefinitivo->getId()]['total'] += $definitivo->getMonto();

                        $totalDefinitivos += $definitivo->getMonto();
                    }
                }

                if ($totalDefinitivos == 0) {

                    $this->get('session')->getFlashBag()
                            ->add('error', 'Hubo un error al generar el reporte de desv&iacute;o. El total del definitivo de la orden de compra ' . $ordenCompra->getNumero() . ' es igual a cero.');

                    return $this->redirect($this->generateUrl('ordenescompra'));
                }
            }
        } else {

            $this->get('session')->getFlashBag()
                    ->add('error', 'Este reporte no es válido para comprobantes de servicio');

            return $this->redirect($this->generateUrl('ordenescompra'));
        }




        $html = '<html><head><style type="text/css">' . $this->renderView('::PDF/mpdf.default.css.twig') . '</style></head><body>';
        $html .= $this->renderView('ADIFComprasBundle:OrdenCompra:print.reporte_desvio.html.twig', [
            'requerimiento' => $requerimiento,
            'detalleCuentasPresupuestariasProvisorio' => $detalleCuentasPresupuestariasProvisorio,
            'totalProvisorios' => $totalProvisorios,
            'ordenCompra' => $ordenCompra,
            'detalleCuentasPresupuestariasDefinitivo' => $detalleCuentasPresupuestariasDefinitivo,
            'totalDefinitivos' => $totalDefinitivos
                ]
        );
        $html .= '</body></html>';

        $filename = 'Reporte-desvio_Orden-compra_' . $ordenCompra->getNumero() . '.pdf';

        $mpdfService = new mPDF('', 'A4', 0, '', 10, 10, 30, 16, 10, 10);

        $mpdfService->WriteHTML($html);

        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    /**
     * @Route("/cantidad_comprobantes_debito/", name="ordenescompra_cantidad_comprobantes_debito")
     * @Method("POST")
     */
    public function getCantidadComprobantesDebitoAction(Request $request) {

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $idOrdenCompra = $request->request->get('id_oc');

        $cantidadComprobantesDebito = 0;

        $comprobantesCompra = $emContable->getRepository('ADIFContableBundle:ComprobanteCompra')
                ->createQueryBuilder('c')
                ->innerJoin('c.estadoComprobante', 'e')
                ->where('c.idOrdenCompra = :idOrdenCompra')
                ->andWhere('e.id != :estadoComprobante')
                ->setParameters(array('idOrdenCompra' => $idOrdenCompra))
                ->setParameter('estadoComprobante', EstadoComprobante::__ESTADO_ANULADO, \Doctrine\DBAL\Types\ Type::STRING)
                ->getQuery()
                ->getResult();

        foreach ($comprobantesCompra as $comprobanteCompra) {

            /* @var $comprobanteCompra \ADIF\ContableBundle\Entity\ComprobanteCompra */
            if (!$comprobanteCompra->getEsNotaCredito()) {
                $cantidadComprobantesDebito++;
            }
        }

        return new JsonResponse($cantidadComprobantesDebito);
    }

    /**
     * @Route("/importe_total_comprobantes_debito/", name="ordenescompra_importe_total_comprobantes_debito")
     * @Method("POST")
     */
    public function getImporteTotalComprobantesDebitoAction(Request $request) {

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $idOrdenCompra = $request->request->get('id_oc');

        $importeTotalComprobantesDebito = 0;

        $comprobantesCompra = $emContable->getRepository('ADIFContableBundle:ComprobanteCompra')
                ->createQueryBuilder('c')
                ->where('c.idOrdenCompra = :idOrdenCompra')
                ->setParameters(array('idOrdenCompra' => $idOrdenCompra))
				->andWhere('c.fechaAnulacion IS NULL')
				->andWhere('c.fechaBaja IS NULL')
                ->getQuery()
                ->getResult();

        foreach ($comprobantesCompra as $comprobanteCompra) {

            /* @var $comprobanteCompra \ADIF\ContableBundle\Entity\ComprobanteCompra */
			if ($comprobanteCompra->getTotalMonedaExtranjera() == null) {
				
				if (!$comprobanteCompra->getEsNotaCredito()) {
					$importeTotalComprobantesDebito += $comprobanteCompra->getTotal();
				} else {
					$importeTotalComprobantesDebito -=$comprobanteCompra->getTotal();
				}
				
			} else {
				
				$importeTotalComprobantesDebito += $comprobanteCompra->getTotalMonedaExtranjera();
				
			}
			
        }
		
		$epsilon = 0.00001;
		if ($importeTotalComprobantesDebito <= $epsilon) {
			$importeTotalComprobantesDebito = 0;
		}

        return new JsonResponse($importeTotalComprobantesDebito);
    }

    /**
     * @Route("/generarDefinitivos/", name="ordenescompra_generar_definitivos")
     * @Method("GET")     
     */
    public function generarDefinitivosOrdenCompra() {

        gc_enable();

        $parcial = false;

        $limit = 20;
        $i = 1;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $ordenesCompraImportadas = $em->getRepository('ADIFComprasBundle:OrdenCompra')
                ->createQueryBuilder('oc')
                ->where('oc.fechaOrdenCompra >= :fecha')->setFirstResult(0)
                ->setMaxResults($limit)
                ->setParameter('fecha', '2015-08-01 00:00:00')
                ->getQuery()
                ->getResult();

        $offset = $limit * $i;
        $i++;
        while (count($ordenesCompraImportadas) > 0) {

            foreach ($ordenesCompraImportadas as $ordenCompraImportada) {
                // Genero el definitivo asociado
                $mensajeError = $this->get('adif.contabilidad_presupuestaria_service')->crearDefinitivoFromOrdenCompra($ordenCompraImportada);
                if ($mensajeError != '') {
                    $this->get('session')->getFlashBag()->add('error', $mensajeError);
                    $parcial = true;
                }
            }
            unset($ordenesCompraImportadas);
            $emContable->flush();
            $emContable->clear();
            $em->clear();
            gc_collect_cycles();
            $ordenesCompraImportadas = $em->getRepository('ADIFComprasBundle:OrdenCompra')
                    ->createQueryBuilder('oc')
                    ->where('oc.fechaOrdenCompra >= :fecha')
                    ->setFirstResult($offset)
                    ->setMaxResults($limit)
                    ->setParameter('fecha', '2015-08-01 00:00:00')
                    ->getQuery()
                    ->getResult();
            $offset = $limit * $i;
            $i++;
        }
        unset($ordenesCompraImportadas);
        $em->clear();
        unset($em);
        $emContable->clear();
        unset($emContable);
        gc_collect_cycles();

        if (!$parcial) {
            $this->get('session')->getFlashBag()->add('success', 'Generacion de definitivos de Ordenes de Compra exitosa');
        }

        return $this->redirect($this->generateUrl(
                                'contratoconsultoria'));
    }

    /**
     * 
     * @param OrdenCompra $ordenCompra
     * @return OrdenCompra
     */
    private function getOrdenCompraOriginal(OrdenCompra $ordenCompra) {

        if ($ordenCompra->getOrdenCompraOriginal() == null) {

            $ordenCompraOriginal = clone $ordenCompra;
			
			//$fecha = new \DateTime();
			
			// Le seteo la fecha de la OC a la actual
			//$ordenCompra->setFechaOrdenCompra($fecha);

            // Por cada renglon original
            foreach ($ordenCompra->getRenglones() as $renglonOrdenCompra) {

                /* @var $renglonOrdenCompraOriginal RenglonOrdenCompra */

                $renglonOrdenCompraOriginal = clone $renglonOrdenCompra;

                $renglonOrdenCompraOriginal->setOrdenCompra($ordenCompraOriginal);

                $ordenCompraOriginal->addRenglon($renglonOrdenCompraOriginal);
            }

            return $ordenCompraOriginal;
        }


        return $ordenCompra->getOrdenCompraOriginal();
    }

    /**
     * @Route("/generarOrdenCompraOriginal/{id}", name="ordenescompra_generar_oc_original")
     * @Method("GET")     
     */
    public function generarOrdenCompraOriginal($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $ordenCompraImportada = $em->getRepository('ADIFComprasBundle:OrdenCompra')->find($id);
        $ordenCompraImportada->setOrdenCompraOriginal($this->getOrdenCompraOriginal($ordenCompraImportada));
        $em->flush();
        $em->clear();

        return $this->redirect($this->generateUrl('ordenescompra'));
    }

    /**
     * 
     * @param RenglonOrdenCompra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createRenglonOrdenCompraForm(RenglonOrdenCompra $entity) {

        $form = $this->createForm(new RenglonOrdenCompraType( $this->getDoctrine()->getManager($this->getEntityManager()),
                                                              $this->getDoctrine()->getManager(EntityManagers::getEmContable())
                                                            ), $entity, array('method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
            'entity_manager_conta' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
            
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

	/**
     * Crea una nueva de orden de compra abierta
     *
     * @Route("/oc_abierta/{id}", name="oc_abierta")
     * @Method("GET")
     * @Template("ADIFComprasBundle:OrdenCompra:oc_abierta.html.twig")
	 * @Security("has_role('ROLE_COMPRAS_ORDEN_COMPRA_ABIERTA')")
     */
	public function OcAbiertaAction($id)
	{
		$em = $this->getDoctrine()->getManager($this->getEntityManager());

        $ordenCompra = $em->getRepository('ADIFComprasBundle:OrdenCompra')->find($id);

        if (!$ordenCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompra.');
        }
		
        $detalleOrdenCompra = $ordenCompra->getNumero() != null 
			? 'Orden de compra ' . $ordenCompra->getNumero() 
			: 'Detalle';

        $bread = $this->base_breadcrumbs;
        $bread[$detalleOrdenCompra] = null;

        return array(
            'ordenCompra' => $ordenCompra,
			'montoOc' => $ordenCompra->getEsOcAbierta() 
				? $ordenCompra->getTotalActual() 
				: $ordenCompra->getTotalOriginal(),
            'breadcrumbs' => $bread,
            'page_title' => 'Desglosar orden de compra'
        );
	}
	
	/**
     * Guarda una orden de compra abierta
     *
     * @Route("/oc_abierta/guardar", name="oc_abierta_save")
     * @Method("POST")
     * @Template("ADIFComprasBundle:OrdenCompra:oc_abierta.html.twig")
	 * @Security("has_role('ROLE_COMPRAS_ORDEN_COMPRA_ABIERTA')")
     */
	public function OcAbiertaGuardarAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager($this->getEntityManager());
		$emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

		$id = $request->request->get('id');
		
		if (!$id) {
			throw $this->createNotFoundException('No se puede encontrar el ID.');
		}
		
		$ordenCompra = $em->getRepository('ADIFComprasBundle:OrdenCompra')->find($id);
        
        if (!$ordenCompra) {
            throw $this->createNotFoundException('No se puede encontrar la entidad OrdenCompra.');
        }
		
		$accion = $request->get('accion');
		$cantidades = $request->get('renglon_cantidad');
		$preciosUnitarios = $request->get('renglon_precioUnitario');
		$idRenglones = $request->get('idRenglon');
		
		//var_dump($accion, $idRenglones);exit;
		
		$ordenCompra->setEsOcAbierta(true);
		
		$ordenCompra->setTotalOriginal( $ordenCompra->getMonto(true) );
		
		$fecha = new \DateTime();
			
		// Le seteo la fecha de la OC a la actual
		$ordenCompra->setFechaOrdenCompra($fecha);
		
		$em->getConnection()->beginTransaction();
		
		try {
			
			if ($accion == 'desglose') {
				// El desglose consiste en un renglon seleccionado y que se puede abrir en varios
				
				$idRenglon = $idRenglones[0];
				
				$renglon = $em->getRepository('ADIFComprasBundle:RenglonOrdenCompra')->find($idRenglon);
				
				for($i = 0; $i < count($idRenglones); $i++) {
					
					$renglonClonado = clone $renglon;
					
					$cantidad = $cantidades[$i];
					$precioUnitario = $preciosUnitarios[$i];
					
					$cantidad = str_replace(',', '.', $cantidad);
					$precioUnitario = str_replace(',', '.', $precioUnitario);
					
					$renglonClonado->setCantidad($cantidad);
					$renglonClonado->setPrecioUnitario($precioUnitario);
					$renglonClonado->setRestante($cantidad);
					
					$em->persist($renglonClonado);
					
					$ordenCompra->addRenglon($renglonClonado);
				}
				
				$renglon->setEsDesglosado(true);
				
				$em->persist($renglon);
				
				$ordenCompra->setTotalActual( $ordenCompra->getMonto(true) );
				
				// Busco el definitivo del renglon y lo actualizo al total actual
				$definitivo = $emContable->getRepository('ADIFContableBundle:DefinitivoCompra')
					->findOneByIdRenglonOrdenCompra($idRenglon);

				if (!$definitivo) {
					throw $this->createNotFoundException('No se puede encontrar la entidad DefinitivoCompra.');
				}

				$definitivo->setMonto( $ordenCompra->getTotalActual() );
				
				$em->persist($ordenCompra);
			
				$emContable->persist($definitivo);
			}
			
			$em->flush();

			$emContable->flush();

			$em->getConnection()->commit();
			
			$this->container->get('request')->getSession()->getFlashBag()
					->add('success', 'Se ha creado la OC abierta con exito.');
				
		} catch (\Exception $e) {

			$em->getConnection()->rollback();
			$em->close();
			
			$this->container->get('request')->getSession()->getFlashBag()
					->add('error', 'Hubo un error al guardar la OC abierta.');
			
		}
			
			
		return $this->redirect($this->generateUrl('ordenescompra'));
	}
	
}
