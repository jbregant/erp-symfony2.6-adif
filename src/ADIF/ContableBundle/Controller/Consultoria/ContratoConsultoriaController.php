<?php

namespace ADIF\ContableBundle\Controller\Consultoria;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCategoriaContrato;
use ADIF\ContableBundle\Entity\Constantes\ConstanteClaseContrato;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoContrato;
use ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria;
use ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion;
use ADIF\ContableBundle\Form\Consultoria\ContratoConsultoriaType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\Expr\Join;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * Consultoria\ContratoConsultoria controller.
 *
 * @Route("/contratoconsultoria")
 */
class ContratoConsultoriaController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Contratos de locaci&oacute;n' => $this->generateUrl('contratoconsultoria')
        );
    }

    /**
     * Lists all Consultoria\ContratoConsultoria entities.
     *
     * @Route("/", name="contratoconsultoria")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Contratos de locaci&oacute;n'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Contratos de locaci&oacute;n',
            'page_info' => 'Lista de contratos de locaci&oacute;n'
        );
    }

    /**
     * Tabla para Consultoria\ContratoConsultoria .
     *
     * @Route("/index_table/", name="contratoconsultoria_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                ->getContratosByNotEstados(array(ConstanteEstadoContrato::ADENDADO, ConstanteEstadoContrato::PRORROGADO));

        $bread = $this->base_breadcrumbs;
        $bread['Contratos de locaci&oacute;n'] = null;

        return $this->render('ADIFContableBundle:Consultoria/ContratoConsultoria:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Tabla para ContratoConsultoria por consultor.
     *
     * @Route("/index_table_por_consultor/", name="ordenescompra_index_table_por_consultor")
     * @Method("GET|POST")
     * 
     */
    public function indexTablePorConsultorAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contratos = $em->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                ->getContratosByNotEstadosAndIdConsultor(array(ConstanteEstadoContrato::ADENDADO, ConstanteEstadoContrato::PRORROGADO), $request->query->get('id_consultor'));

        return $this->render('ADIFContableBundle:Consultoria\ContratoConsultoria:index_table_por_consultor.html.twig', array('contratos' => $contratos));
    }

    /**
     * Creates a new Consultoria\ContratoConsultoria entity.
     *
     * @Route("/insertar", name="contratoconsultoria_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Consultoria\ContratoConsultoria:new.html.twig")
     */
    public function createAction(Request $request) {

        $contrato = new ContratoConsultoria();

        $form = $this->createCreateForm($contrato);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Inicializo cada CicloFacturacion
            $this->initCicloFacturacion($contrato);

            // Actualizo el estado del contrato origen
            if ($contrato->getContratoOrigen() != null) {

                // Si el tipo del contrato es una Adenda
                if ($contrato->getCategoriaContrato()->getCodigo() == ConstanteCategoriaContrato::ADENDA) {
                    $contrato->getContratoOrigen()->setEstadoContrato(
                            $em->getRepository('ADIFContableBundle:Facturacion\EstadoContrato')
                                    ->findOneByCodigo(ConstanteEstadoContrato::ADENDADO)
                    );
                }
            }

            // Persisto la entidad
            $em->persist($contrato);

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestario = $this->get('adif.contabilidad_presupuestaria_service')
                    ->crearDefinitivoFromContratoConsultoria($contrato);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestario != '') {

                $this->get('session')->getFlashBag()
                        ->add('error', $mensajeErrorAsientoPresupuestario);
            }
            // Sino, si no hubo error
            else {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {

                    $em->flush();

                    $em->getConnection()->commit();
                } //.
                catch (\Exception $e) {

                    $em->getConnection()->rollback();
                    $em->close();

                    throw $e;
                }
            }

            return $this->redirect($this->generateUrl('contratoconsultoria'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $contrato,
            'edicion_total' => $contrato->getEsEditableTotalidad(),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear contrato de locaci&oacute;n',
        );
    }

    /**
     * Creates a form to create a Consultoria\ContratoConsultoria entity.
     *
     * @param ContratoConsultoria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ContratoConsultoria $entity) {
        $form = $this->createForm(new ContratoConsultoriaType(), $entity, array(
            'action' => $this->generateUrl('contratoconsultoria_create'),
            'method' => 'POST',
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
            
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Consultoria\ContratoConsultoria entity.
     *
     * @Route("/crear", name="contratoconsultoria_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contrato = new ContratoConsultoria();

        $contrato->setCategoriaContrato($em->getRepository('ADIFContableBundle:Facturacion\CategoriaContrato')
                        ->findOneByCodigo(ConstanteCategoriaContrato::CONTRATO_ORIGINAL)
        );

        $contrato->setClaseContrato($em->getRepository('ADIFContableBundle:Facturacion\ClaseContrato')
                        ->findOneByCodigo(ConstanteClaseContrato::LOCACION_SERVICIOS)
        );

        $contrato->setTipoMoneda($em->getRepository('ADIFContableBundle:TipoMoneda')
                        ->findOneByEsMCL(true)
        );

        $unidadesTiempo = $em->getRepository('ADIFContableBundle:Facturacion\UnidadTiempo')
                ->findAll();

        $unidadesTiempoJson = [];

        foreach ($unidadesTiempo as $unidadTiempo) {
            $unidadesTiempoJson[] = [
                'id' => $unidadTiempo->getId(),
                'denominacion' => $unidadTiempo->getDenominacion(),
                'semanas' => $unidadTiempo->getCantidadSemanas(),
                'meses' => $unidadTiempo->getCantidadMeses()
            ];
        }

        $form = $this->createCreateForm($contrato);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $contrato,
            'edicion_total' => $contrato->getEsEditableTotalidad(),
            'unidadesTiempo' => json_encode($unidadesTiempoJson),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear contrato de locaci&oacute;n'
        );
    }

    /**
     * Finds and displays a Consultoria\ContratoConsultoria entity.
     *
     * @Route("/{id}", name="contratoconsultoria_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultoria\ContratoConsultoria.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Contrato de consultor&iacute;a'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver contrato de locaci&oacute;n'
        );
    }

    /**
     * Displays a form to edit an existing Consultoria\ContratoConsultoria entity.
     *
     * @Route("/editar/{id}", name="contratoconsultoria_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Consultoria\ContratoConsultoria:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contrato = $em->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                ->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ContratoConsultoria.');
        }

        $editForm = $this->createEditForm($contrato);

        $editForm->get('consultor_razonSocial')
                ->setData($contrato->getConsultor()->getRazonSocial());

        $editForm->get('consultor_cuit')
                ->setData($contrato->getConsultor()->getCUIT());

        $unidadesTiempo = $em->getRepository('ADIFContableBundle:Facturacion\UnidadTiempo')
                ->findAll();

        $unidadesTiempoJson = [];

        foreach ($unidadesTiempo as $unidadTiempo) {
            $unidadesTiempoJson[] = [
                'id' => $unidadTiempo->getId(),
                'denominacion' => $unidadTiempo->getDenominacion(),
                'semanas' => $unidadTiempo->getCantidadSemanas(),
                'meses' => $unidadTiempo->getCantidadMeses()
            ];
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $contrato,
            'edicion_total' => $contrato->getEsEditableTotalidad(),
            'unidadesTiempo' => json_encode($unidadesTiempoJson),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar contrato de locaci&oacute;n'
        );
    }

    /**
     * Creates a form to edit a Consultoria\ContratoConsultoria entity.
     *
     * @param ContratoConsultoria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ContratoConsultoria $entity) {
        $form = $this->createForm(new ContratoConsultoriaType(), $entity, array(
            'action' => $this->generateUrl('contratoconsultoria_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Consultoria\ContratoConsultoria entity.
     *
     * @Route("/actualizar/{id}", name="contratoconsultoria_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Consultoria\ContratoConsultoria:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contrato = $em->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultoria\ContratoConsultoria.');
        }

        $editForm = $this->createEditForm($contrato);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // Actualizo el monto del definitivo asociado
            $this->get('adif.contabilidad_presupuestaria_service')
                    ->actualizarDefinitivoFromContratoConsultoria($contrato);

            $em->flush();

            return $this->redirect($this->generateUrl('contratoconsultoria'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $contrato,
            'edicion_total' => $contrato->getEsEditableTotalidad(),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar contrato de locaci&oacute;n'
        );
    }

    /**
     * Deletes a Consultoria\ContratoConsultoria entity.
     *
     * @Route("/borrar/{id}", name="contratoconsultoria_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Consultoria\ContratoConsultoria.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('contratoconsultoria'));
    }

    /**
     * Finds and displays a detalle del saldo de Contrato.
     *
     * @Route("/{id}/detalle_saldo", name="contratoconsultoria_detalle_saldo")
     * @Method("GET")
     * @Template("ADIFContableBundle:Consultoria\ContratoConsultoria:show.detalle_saldo.html.twig")
     */
    public function showDetalleSaldoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contrato = $em->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                ->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ContratoConsultoria.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Contrato ' . $contrato->__toString()] = $this->generateUrl('contratoconsultoria_show', array('id' => $contrato->getId()));
        $bread['Detalle del saldo'] = null;

        return array(
            'entity' => $contrato,
            'breadcrumbs' => $bread,
            'page_title' => 'Detalle del saldo de contrato'
        );
    }

    /**
     *
     * @Route("/{id}/adenda", name="contratoconsultoria_adenda")
     * @Method("GET")
     * @Template("ADIFContableBundle:Consultoria\ContratoConsultoria:new.html.twig")
     */
    public function newAdendaAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contratoOrigen = $em->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                ->find($id);

        if (!$contratoOrigen) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ContratoConsultoria.');
        }

        /* @var $adenda ContratoConsultoria */
        $adenda = clone $contratoOrigen;

        $adenda->setContratoOrigen($contratoOrigen);

        $adenda->setCategoriaContrato($em->getRepository('ADIFContableBundle:Facturacion\CategoriaContrato')
                        ->findOneByCodigo(ConstanteCategoriaContrato::ADENDA)
        );

        $adenda->setEstadoContrato($em->getRepository('ADIFContableBundle:Facturacion\EstadoContrato')
                        ->findOneByCodigo(ConstanteEstadoContrato::ACTIVO_OK)
        );

        foreach ($contratoOrigen->getCiclosFacturacion() as $cicloFacturacion) {

            /* @var $cicloFacturacion CicloFacturacion */

            // Si el ciclo de facturación  tiene facturas pendientes
            if ($cicloFacturacion->getCantidadFacturasPendientes() > 0 //
                    && $cicloFacturacion->getCantidadFacturasPendientes() < $cicloFacturacion->getCantidadFacturas()) {

                // Creo un ciclo de facturacion que no se pude editar
                $cicloFacturacionReadonly = clone $cicloFacturacion;

                $cicloFacturacionReadonly->setCantidadFacturas(
                        $cicloFacturacion->getCantidadFacturas() -
                        $cicloFacturacion->getCantidadFacturasPendientes()
                );

                $cicloFacturacionReadonly->setCantidadFacturasPendientes(0);

                $cicloFacturacionReadonly->setFechaFin($this
                                ->getFechaFinCicloFacturacionReadonly($cicloFacturacion));

                $adenda->addCiclosFacturacion($cicloFacturacionReadonly);

                // Creo un ciclo de facturación que se puede editar
                $cicloFacturacionPendiente = clone $cicloFacturacion;

                $cicloFacturacionPendiente->setFechaInicio($this
                                ->getFechaInicioCicloFacturacionPendiente($cicloFacturacion));

                $cicloFacturacionPendiente->setCantidadFacturas(
                        $cicloFacturacion->getCantidadFacturasPendientes()
                );

                $cicloFacturacionPendiente->setCantidadFacturasPendientes(
                        $cicloFacturacion->getCantidadFacturasPendientes()
                );

                $adenda->addCiclosFacturacion($cicloFacturacionPendiente);

                // Elimino el ciclo de facturación original
                $adenda->removeCiclosFacturacion($cicloFacturacion);
            }
        }

        // Ordeno los ciclos de facturación
        $iterator = $adenda->getCiclosFacturacion()->getIterator();

        $iterator->uasort(function ($cicloFacturacionA, $cicloFacturacionB) {
            return ($cicloFacturacionA->getFechaInicio() < $cicloFacturacionB->getFechaInicio()) ? -1 : 1;
        });

        $cicloFacturacionCollection = new ArrayCollection(iterator_to_array($iterator));

        $adenda->getCiclosFacturacion()->clear();

        foreach ($cicloFacturacionCollection as $cicloFacturacion) {
            $adenda->addCiclosFacturacion($cicloFacturacion);
        }
        // Fin ordenamiento

        $form = $this->createCreateForm($adenda);

        $form->get('consultor_razonSocial')
                ->setData($adenda->getConsultor()->getRazonSocial());

        $form->get('consultor_cuit')
                ->setData($adenda->getConsultor()->getCUIT());

        $unidadesTiempo = $em->getRepository('ADIFContableBundle:Facturacion\UnidadTiempo')
                ->findAll();

        $unidadesTiempoJson = [];

        foreach ($unidadesTiempo as $unidadTiempo) {
            $unidadesTiempoJson[] = [
                'id' => $unidadTiempo->getId(),
                'denominacion' => $unidadTiempo->getDenominacion(),
                'semanas' => $unidadTiempo->getCantidadSemanas(),
                'meses' => $unidadTiempo->getCantidadMeses()
            ];
        }

        $bread = $this->base_breadcrumbs;
        $bread['Adendar'] = null;

        return array(
            'entity' => $adenda,
            'edicion_total' => true,
            'idEstadoContratoActivoComentado' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::ACTIVO_COMENTADO),
            'tipoContrato' => ConstanteCategoriaContrato::ADENDA,
            'unidadesTiempo' => json_encode($unidadesTiempoJson),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Adendar contrato'
        );
    }

    /**
     * Finds and displays a histórico de Contrato.
     *
     * @Route("/{id}/historico", name="contratoconsultoria_historico")
     * @Method("GET")
     * @Template("ADIFContableBundle:Consultoria\ContratoConsultoria:historico.html.twig")
     */
    public function showHistoricoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contrato = $em->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                ->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ContratoConsultoria.');
        }

        // Obtengo el histórico del Contrato
        $historicos = $contrato->getHistorico();

        $bread = $this->base_breadcrumbs;
        $bread['Contrato ' . $contrato->__toString()] = $this->generateUrl('contrato_show', array('id' => $contrato->getId()));
        $bread['Hist&oacute;rico'] = null;

        return array(
            'entity' => $contrato,
            'historicos' => $historicos,
            'breadcrumbs' => $bread,
            'page_title' => 'Hist&oacute;rico de contrato'
        );
    }

    /**
     * 
     * @param type $codigo
     * @return type
     * @throws type
     */
    public function getIdEstadoByCodigo($codigo) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $estadoContrato = $em->getRepository('ADIFContableBundle:Facturacion\EstadoContrato')
                ->findOneByCodigo($codigo);

        if (!$estadoContrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EstadoContrato.');
        }

        return $estadoContrato->getId();
    }

    /**
     * Tabla para Ciclos de Facturacion.
     *
     * @Route("/ciclos_facturacion_pendientes/", name="ciclos_facturacion_pendientes")
     * @Method("GET|POST")
     */
    public function getCiclosFacturacionPendientesByConsultorAction(Request $request) {

        $ciclosFacturacion = $this->get('adif.contrato_service')->getCiclosFacturacionPendientesByIdConsultor($request->query->get('id_consultor'));
        return new JsonResponse($ciclosFacturacion);
    }

    /**
     * 
     * @param Contrato $contrato
     */
    private function initCicloFacturacion(ContratoConsultoria $contrato) {

        foreach ($contrato->getCiclosFacturacion() as $cicloFacturacion) {
            $cicloFacturacion->setContrato($contrato);
            if ($cicloFacturacion->getCantidadFacturasPendientes() > 0) {
                $cicloFacturacion->setCantidadFacturasPendientes($cicloFacturacion->getCantidadFacturas());
            }
        }
    }

    /**
     * @Route("/generarDefinitivos/", name="contratoconsultoria_definitivos")
     * @Method("PUT|GET")     
     */
    public function generarDefinitivosContratosConsultoria() {

        gc_enable();

        $parcial = false;

        $offset = 0;
        $limit = 20;
        $i = 1;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $contratosImportados = $em->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                ->createQueryBuilder('cc')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();

        $offset = $limit * $i;
        $i++;
        while (count($contratosImportados) > 0) {

            foreach ($contratosImportados as $contratoImportado) {
                // Genero el definitivo asociado
                $mensajeError = $this->get('adif.contabilidad_presupuestaria_service')->crearDefinitivoFromContratoConsultoria($contratoImportado);
                if ($mensajeError != '') {
                    $this->get('session')->getFlashBag()->add('error', $mensajeError);
                    $parcial = true;
                }
            }
            unset($contratosImportados);
            $em->flush();
            $em->clear();
            gc_collect_cycles();
            $contratosImportados = $em->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                    ->createQueryBuilder('cc')
                    ->setFirstResult($offset)
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult();
            $offset = $limit * $i;
            $i++;
        }
        unset($contratosImportados);
        $em->clear();
        unset($em);
        gc_collect_cycles();

        if (!$parcial) {
            $this->get('session')->getFlashBag()->add('success', 'Generacion de definitivos de Contratos de Consultoria exitosa');
        }

        return $this->redirect($this->generateUrl('contrato_generar_definitivos'));
    }

    /**
     * 
     * @param CicloFacturacion $cicloFacturacion
     */
    private function getFechaInicioCicloFacturacionPendiente(CicloFacturacion $cicloFacturacion) {

        $cantidadFacturas = $cicloFacturacion->getCantidadFacturas() -
                $cicloFacturacion->getCantidadFacturasPendientes();

        $mesesASumar = $cicloFacturacion->getUnidadTiempo()->getCantidadMeses() //
                * $cicloFacturacion->getCantidadUnidadTiempo() //
                * $cantidadFacturas;

        $nuevaFechaInicio = date('Y-m-d', strtotime("+$mesesASumar months", strtotime($cicloFacturacion->getFechaInicio()->format('Y-m-d'))));

        return \DateTime::createFromFormat('Y-m-d', $nuevaFechaInicio);
    }

    /**
     * 
     * @param CicloFacturacion $cicloFacturacion
     * @return type
     */
    private function getFechaFinCicloFacturacionReadonly(CicloFacturacion $cicloFacturacion) {

        $fechaInicioCicloFacturacionPendiente = $this
                ->getFechaInicioCicloFacturacionPendiente($cicloFacturacion);

        $nuevaFechaInicio = date('Y-m-d', strtotime('-1 day', strtotime($fechaInicioCicloFacturacionPendiente->format('Y-m-d'))));

        return \DateTime::createFromFormat('Y-m-d', $nuevaFechaInicio);
    }

    /**
     * Tabla para comprobantes a cancelar con notas de credito.
     *
     * @Route("/cuotas_cancelables/", name="cuotas_cancelables")
     * @Method("GET|POST")
     */
    public function getCuotasCancelablesByConsultorAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idConsultor = $request->query->get('id_consultor');

        $comprobantes = $em->getRepository('ADIFContableBundle:Consultoria\ComprobanteConsultoria')
                ->createQueryBuilder('cc')
                ->innerJoin('ADIFContableBundle:Consultoria\ContratoConsultoria', 'con', Join::WITH, 'cc.contrato = con.id AND con.idConsultor = :id')
                ->innerJoin('cc.estadoComprobante', 'ec')
                ->leftJoin('ADIFContableBundle:Consultoria\FacturaConsultoria', 'fc', Join::WITH, 'fc.id = cc.id')
                ->leftJoin('ADIFContableBundle:Consultoria\ReciboConsultoria', 'rc', Join::WITH, 'rc.id = cc.id')
                ->leftJoin('ADIFContableBundle:Consultoria\RenglonComprobanteConsultoria', 'rcc', Join::WITH, '(rcc.comprobante = fc.id OR rcc.comprobante = rc.id) AND (rcc.cancelado = 1)')
                ->where('cc.ordenPago IS NULL')
                ->andWhere('ec.id <> :idEstado ')
                ->setParameter('id', $idConsultor)
                ->setParameter('idEstado', EstadoComprobante::__ESTADO_ANULADO)
                ->orderBy('con.id')
                ->getQuery()
                ->getResult();

        $ciclosFacturacion = [];
        setlocale(LC_ALL,"es_AR.UTF-8");

        /* @var $comprobante \ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria */
        foreach ($comprobantes as $comprobante) {

            /* @var $renglon \ADIF\ContableBundle\Entity\Consultoria\RenglonComprobanteConsultoria */
            foreach ($comprobante->getRenglonesComprobante() as $renglon) {
                $mesSiguienteFactura = $renglon->getNumeroCuota();
                if ($mesSiguienteFactura > 12) {
                    $mesSiguienteFactura = $mesSiguienteFactura % 12;
                }
                $mes = ucfirst(strftime('%B', mktime(0, 0, 0, $mesSiguienteFactura)));

                $ciclosFacturacion[] = array(
                    'id' => $renglon->getId(),
                    'idContrato' => $renglon->getComprobante()->getContrato()->getId(),
                    'idCicloFacturacion' => $renglon->getCicloFacturacion()->getId(),
                    'nroContrato' => $renglon->getComprobante()->getContrato()->getNumeroContrato(),
                    'gerencia' => $renglon->getComprobante()->getContrato()->getGerencia()->getNombre(),
                    'area' => $renglon->getComprobante()->getContrato()->getArea()->getNombre(),
                    'mes' => $mes,
                    'importe' => $renglon->getCicloFacturacion()->getImporte(),
                    'numeroCuota' => $renglon->getNumeroCuota()
                );
            }
        }

        return new JsonResponse($ciclosFacturacion);
    }

}
