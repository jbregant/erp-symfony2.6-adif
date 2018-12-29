<?php

namespace ADIF\ContableBundle\Controller\Facturacion;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCategoriaContrato;
use ADIF\ContableBundle\Entity\Constantes\ConstanteClaseContrato;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoContrato;
use ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion;
use ADIF\ContableBundle\Entity\Facturacion\Contrato;
use ADIF\ContableBundle\Entity\Facturacion\ContratoVenta;
use ADIF\ContableBundle\Form\Facturacion\ContratoVentaType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoPercepcion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Facturacion\Contrato controller.
 *
 * @Route("/contrato")
 */
class ContratoController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Contratos' => $this->generateUrl('contrato')
        );
    }

    /**
     * Lists all Facturacion\Contrato entities.
     *
     * @Route("/", name="contrato")
     * @Method("GET")
     * @Template()
     * -@Security("has_role('ROLE_VISUALIZAR_CONTRATOS')")
     */
    public function indexAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $pendientesAutorizacion = $em->getRepository('ADIFContableBundle:Facturacion\FacturaAlquiler')
                ->createQueryBuilder('fa')
                ->innerJoin('fa.puntoVenta', 'pv')
                ->where('pv.generaComprobanteElectronico = 1')
                ->andWhere('(fa.caeNumero IS NULL) or (fa.caeVencimiento IS NULL)')
                ->getQuery()
                ->getResult();

        if (count($pendientesAutorizacion) > 0) {

            $mensajeError = '<span>Existen comprobantes electr&oacute;nicos pendientes de autorizaci&oacute;n.</span>';
            $mensajeError .= '<span> Para verlos haga click <b><a href="' . $this->generateUrl('comprobanteventa_reenvio_facturacion_electronica') . '">aqu&iacute;.</a></b></span>';

            $this->get('session')->getFlashBag()->add('error', $mensajeError);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Contratos'] = null;

        return array(
            'tipoCambioPorTipoMoneda' => $this->get('adif.tipomoneda_service')->getTiposDeMoneda(),
            'breadcrumbs' => $bread,
            'page_title' => 'Contratos',
            'page_info' => 'Lista de contratos'
        );
    }

    /**
     * Tabla para Facturacion\Contrato.
     *
     * @Route("/index_table/", name="contrato_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

//        $entities = $em->getRepository('ADIFContableBundle:Facturacion\ContratoVenta')
//                ->getContratosByNotEstados(array(ConstanteEstadoContrato::ADENDADO, ConstanteEstadoContrato::PRORROGADO));
        $entities = $em->getRepository('ADIFContableBundle:Vistas\VistaContratoVenta')
                ->createQueryBuilder('c')
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $this->render('ADIFContableBundle:Facturacion\Contrato:index_table.html.twig', array(
                    'entities' => $entities
        ));
    }

    /**
     * Creates a new Facturacion\Contrato entity.
     *
     * @Route("/insertar", name="contrato_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Facturacion\Contrato:new.html.twig")
     */
    public function createAction(Request $request) {

        $contratoRequest = $request->request
                ->get('adif_contablebundle_facturacion_contrato', false);

        $idClaseContrato = $contratoRequest['claseContrato'];

        $contrato = ConstanteClaseContrato::getSubclass($idClaseContrato);

        $form = $this->createCreateForm($contrato);
        $form->handleRequest($request);

        $emContable = $this->getDoctrine()->getManager($this->getEntityManager());

        if ($form->isValid()) {

            // Si el contrato es de "Chatarra"
            if ($idClaseContrato == ConstanteClaseContrato::CHATARRA) {

                $numeroLicitacion = $contratoRequest['numeroLicitacion'];

                if ($numeroLicitacion) {

                    $licitacion = new \ADIF\ContableBundle\Entity\LicitacionChatarra();

                    $fechaApertura = \DateTime::createFromFormat('d/m/Y', $contratoRequest['fechaApertura']);

                    $licitacion->setNumero($numeroLicitacion);
                    $licitacion->setFechaApertura($fechaApertura);

                    $licitacion->setIdCliente($contrato->getIdCliente());
                    $licitacion->setImporteLicitacion($contrato->getImporteTotal());

                    $contrato->setLicitacion($licitacion);
                }
            }

            // Si el contrato indica nro de inmueble
            if ($contrato->getIndicaNumeroInmueble()) {

                $numeroInmueble = $contratoRequest['numeroInmueble'];

                $contrato->setNumeroInmueble($numeroInmueble);
            }

            // Inicializo cada CicloFacturacion
            $this->initCicloFacturacion($contrato);

            // A cada PolizaSeguroContrato, le seteo el Contrato
            foreach ($contrato->getPolizasSeguro() as $polizaSeguro) {
                $polizaSeguro->setContrato($contrato);
            }

            // Actualizo el estado del contrato origen
            if ($contrato->getContratoOrigen() != null) {

                // Si el tipo del contrato es una Prorroga
                if ($contrato->getCategoriaContrato()->getCodigo() == ConstanteCategoriaContrato::PRORROGA) {
                    $contrato->getContratoOrigen()->setEstadoContrato(
                            $emContable->getRepository('ADIFContableBundle:Facturacion\EstadoContrato')
                                    ->findOneByCodigo(ConstanteEstadoContrato::PRORROGADO)
                    );
                }
                // Sino, si es una Adenda
                else if ($contrato->getCategoriaContrato()->getCodigo() == ConstanteCategoriaContrato::ADENDA) {

                    $contrato->getContratoOrigen()->setEstadoContrato(
                            $emContable->getRepository('ADIFContableBundle:Facturacion\EstadoContrato')
                                    ->findOneByCodigo(ConstanteEstadoContrato::ADENDADO)
                    );
                }
            }

            // Persisto la entidad
            $emContable->persist($contrato);


            // Actualizo el saldo del definitivo asociado al contrato origen
            if ($contrato->getContratoOrigen() != null) {

                $this->get('adif.contabilidad_presupuestaria_service')
                        ->saldarDefinitivoFromContratoVenta($contrato->getContratoOrigen());
            }

            // Genero el definitivo asociado
            $mensajeError = $this->get('adif.contabilidad_presupuestaria_service')
                    ->crearDefinitivoFromContratoVenta($contrato);

            // Si hubo un error
            if ($mensajeError != '') {

                $this->get('session')->getFlashBag()->add('error', $mensajeError);

                $request->attributes->set('form-error', true);
            } else {

                // Comienzo la transaccion
                $emContable->getConnection()->beginTransaction();

                try {

                    $emContable->flush();

                    $emContable->getConnection()->commit();
                } //.
                catch (\Exception $e) {

                    $emContable->getConnection()->rollback();
                    $emContable->close();

                    throw $e;
                }
            }

            return $this->redirect($this->generateUrl('contrato'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $unidadesTiempo = $emContable->getRepository('ADIFContableBundle:Facturacion\UnidadTiempo')->findAll();

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
        $bread['Crear'] = null;

        return array(
            'entity' => $contrato,
            'edicion_total' => $contrato->getEsEditableTotalidad(),
            'idEstadoContratoActivoComentado' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::ACTIVO_COMENTADO),
            'idEstadoContratoInactivo' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::INACTIVO),
            'idEstadoContratoDesocupado' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::DESOCUPADO),
            'unidadesTiempo' => json_encode($unidadesTiempoJson),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear contrato',
        );
    }

    /**
     * Creates a form to create a Facturacion\Contrato entity.
     *
     * @param Contrato $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ContratoVenta $entity) {
        $form = $this->createForm(new ContratoVentaType(), $entity, array(
            'action' => $this->generateUrl('contrato_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Facturacion\Contrato entity.
     *
     * @Route("/crear", name="contrato_new")
     * @Method("GET")
     * @Template()
     * -@Security("has_role('ROLE_CREAR_MODIFICAR_CONTRATOS')")
     */
    public function newAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contrato = new ContratoVenta();

        $contrato->setCategoriaContrato($em->getRepository('ADIFContableBundle:Facturacion\CategoriaContrato')
                        ->findOneByCodigo(ConstanteCategoriaContrato::CONTRATO_ORIGINAL)
        );

        $form = $this->createCreateForm($contrato);

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
        $bread['Crear'] = null;

        return array(
            'entity' => $contrato,
            'edicion_total' => $contrato->getEsEditableTotalidad(),
            'idEstadoContratoActivoComentado' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::ACTIVO_COMENTADO),
            'idEstadoContratoInactivo' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::INACTIVO),
            'idEstadoContratoDesocupado' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::DESOCUPADO),            
            'unidadesTiempo' => json_encode($unidadesTiempoJson),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear contrato'
        );
    }

    /**
     * Finds and displays a Facturacion\Contrato entity.
     *
     * @Route("/{id}", name="contrato_show")
     * @Method("GET")
     * @Template()
     * -@Security("has_role('ROLE_VISUALIZAR_CONTRATOS')")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contrato = $em->getRepository('ADIFContableBundle:Facturacion\Contrato')->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Contrato.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Contrato ' . $contrato->__toString()] = null;


        return array(
            'entity' => $contrato,
            'conceptoPercepcionIIBB' => \ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB,
            'conceptoPercepcionIVA' => \ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA,
            'tipoContrato' => ConstanteCategoriaContrato::CONTRATO_ORIGINAL,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver contrato'
        );
    }

    /**
     * Displays a form to edit an existing Facturacion\Contrato entity.
     *
     * @Route("/editar/{id}", name="contrato_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\Contrato:new.html.twig")
     * -@Security("has_role('ROLE_CREAR_MODIFICAR_CONTRATOS')")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contrato = $em->getRepository('ADIFContableBundle:Facturacion\Contrato')
                ->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Contrato.');
        }

        $editForm = $this->createEditForm($contrato);

        $editForm->get('cliente_razonSocial')
                ->setData($contrato->getCliente()->getRazonSocial());

        $editForm->get('cliente_cuit')
                ->setData($contrato->getCliente()->getCUIT());

        if (method_exists($contrato, 'getLicitacion')) {

            $editForm->get('numeroLicitacion')
                    ->setData($contrato->getLicitacion()->getNumero());

            $editForm->get('fechaApertura')
                    ->setData($contrato->getLicitacion()->getFechaApertura());
        }

        if ($contrato->getIndicaNumeroInmueble()) {
            $editForm->get('numeroInmueble')
                    ->setData($contrato->getNumeroInmueble());
        }

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
            'idEstadoContratoActivoComentado' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::ACTIVO_COMENTADO),
            'idEstadoContratoInactivo' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::INACTIVO),
            'idEstadoContratoDesocupado' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::DESOCUPADO),            
            'unidadesTiempo' => json_encode($unidadesTiempoJson),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar contrato'
        );
    }

    /**
     * Creates a form to edit a Facturacion\Contrato entity.
     *
     * @param Contrato $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Contrato $entity, $action = 'contrato_update') {
        $form = $this->createForm(new ContratoVentaType(), $entity, array(
            'action' => $this->generateUrl($action, array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Facturacion\Contrato entity.
     *
     * @Route("/actualizar/{id}", name="contrato_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Facturacion\Contrato:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $contrato Contrato */
        $contrato = $em->getRepository('ADIFContableBundle:Facturacion\Contrato')
                ->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Contrato.');
        }

        $ciclosFacturacionOriginales = new ArrayCollection();

        $polizasOriginales = new ArrayCollection();

        // Creo un ArrayCollection de los CicloFacturacion actuales en la BBDD
        foreach ($contrato->getCiclosFacturacion() as $cicloFacturacion) {
            $ciclosFacturacionOriginales->add($cicloFacturacion);
        }

        // Creo un ArrayCollection de las PolizaSeguroContrato actuales en la BBDD
        foreach ($contrato->getPolizasSeguro() as $polizaSeguro) {
            $polizasOriginales->add($polizaSeguro);
        }

        $editForm = $this->createEditForm($contrato);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // Actualizo los CicloFacturacion
            $this->initCicloFacturacion($contrato);

            // A cada PolizaSeguroContrato, le seteo el Contrato
            foreach ($contrato->getPolizasSeguro() as $polizaSeguro) {
                $polizaSeguro->setContrato($contrato);
            }

            // Por cada CicloFacturacion original
            foreach ($ciclosFacturacionOriginales as $cicloFacturacion) {

                // Si fue eliminado
                if (false === $contrato->getCiclosFacturacion()->contains($cicloFacturacion)) {
                    $contrato->removeCiclosFacturacion($cicloFacturacion);
                    $em->remove($cicloFacturacion);
                }
            }

            // Por cada PolizaSeguroContrato original
            foreach ($polizasOriginales as $polizaSeguro) {

                // Si fue eliminado
                if (false === $contrato->getPolizasSeguro()->contains($polizaSeguro)) {
                    $contrato->removePolizasSeguro($polizaSeguro);
                    $em->remove($polizaSeguro);
                }
            }

            // Actualizo el monto del definitivo asociado
            $this->get('adif.contabilidad_presupuestaria_service')
                    ->actualizarDefinitivoFromContratoVenta($contrato);

            $em->flush();

            return $this->redirect($this->generateUrl('contrato'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $unidadesTiempo = $em->getRepository('ADIFContableBundle:Facturacion\UnidadTiempo')->findAll();

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
            'idEstadoContratoActivoComentado' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::ACTIVO_COMENTADO),
            'idEstadoContratoInactivo' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::INACTIVO),
            'idEstadoContratoDesocupado' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::DESOCUPADO),            
            'unidadesTiempo' => json_encode($unidadesTiempoJson),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar contrato'
        );
    }

    /**
     * Deletes a Facturacion\Contrato entity.
     *
     * @Route("/borrar/{id}", name="contrato_delete")
     * @Method("GET")
     * -@Security("has_role('ROLE_CREAR_MODIFICAR_CONTRATOS')")
     */
    public function deleteAction($id) {

        return parent::baseDeleteAction($id);
    }

    /**
     * 
     * @param type $id
     * @return boolean
     */
    public function validateLocalDeleteById($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $contrato Contrato */
        $contrato = $em->getRepository('ADIFContableBundle:Facturacion\Contrato')
                ->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Contrato.');
        }

        $eliminable = true;

        foreach ($contrato->getCiclosFacturacion() as $ciclo) {
            /* @var $ciclo CicloFacturacion */
            $eliminable &= ($ciclo->getCantidadFacturas() == $ciclo->getCantidadFacturasPendientes());
        }

        return $eliminable;
    }

    /**
     * 
     * @param type $entity
     * @return boolean
     */
    function validateGeneralDeleteByEntity($entity) {
        return true;
    }

    /**
     * 
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el contrato ya que posee facturas emitidas.';
    }

    /**
     * 
     * @param type $em
     * @param type $entity
     */
    public function customRemove($em, $entity) {

        $em->remove($entity);

        $this->get('adif.contabilidad_presupuestaria_service')
                ->crearDefinitivoFromContratoVenta($entity, true);
    }

    /**
     * 
     * @param Contrato $contrato
     */
    private function initCicloFacturacion(Contrato $contrato) {

        foreach ($contrato->getCiclosFacturacion() as $cicloFacturacion) {
            $cicloFacturacion->setContrato($contrato);
        }
    }

    /**
     *
     * @Route("/{id}/prorroga", name="contrato_prorroga")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\Contrato:new.html.twig")
     * -@Security("has_role('ROLE_CREAR_MODIFICAR_CONTRATOS')")
     */
    public function newProrrogaAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contratoOrigen = $em->getRepository('ADIFContableBundle:Facturacion\ContratoVenta')
                ->find($id);

        if (!$contratoOrigen) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Contrato.');
        }

        /* @var $prorroga Contrato */
        $prorroga = clone $contratoOrigen;

        $prorroga->setNumeroContrato(
                $contratoOrigen->getNumeroProrroga()
        );

        $prorroga->setImporteTotal(null);

        // Actualizo las fechas de la prorroga
        $prorroga->setFechaInicio(
                $contratoOrigen->getFechaFin()->modify('+1 day')
        );
        $prorroga->setFechaFin(null);


        // Actualizo los ciclos de facturación
        $ultimoCicloFacturacion = $prorroga->getCiclosFacturacion()->last();
        $ultimoCicloFacturacion->setFechaInicio($prorroga->getFechaInicio());
        $ultimoCicloFacturacion->setFechaFin(null);
        $ultimoCicloFacturacion->setCantidadFacturas(null);
        $ultimoCicloFacturacion->setCantidadFacturasPendientes(null);

        $prorroga->getCiclosFacturacion()->clear();

        $prorroga->addCiclosFacturacion($ultimoCicloFacturacion);

        $prorroga->setContratoOrigen($contratoOrigen);

        $prorroga->setCategoriaContrato($em->getRepository('ADIFContableBundle:Facturacion\CategoriaContrato')
                        ->findOneByCodigo(ConstanteCategoriaContrato::PRORROGA)
        );

        $prorroga->setEstadoContrato($em->getRepository('ADIFContableBundle:Facturacion\EstadoContrato')
                        ->findOneByCodigo(ConstanteEstadoContrato::ACTIVO_OK)
        );

        $form = $this->createCreateForm($prorroga);

        $form->get('cliente_razonSocial')
                ->setData($prorroga->getCliente()->getRazonSocial());

        $form->get('cliente_cuit')
                ->setData($prorroga->getCliente()->getCUIT());

        if (method_exists($prorroga, 'getLicitacion')) {

            $form->get('numeroLicitacion')
                    ->setData($prorroga->getLicitacion()->getNumero());

            $form->get('fechaApertura')
                    ->setData($prorroga->getLicitacion()->getFechaApertura());
        }

        if ($prorroga->getIndicaNumeroInmueble()) {
            $form->get('numeroInmueble')
                    ->setData($prorroga->getNumeroInmueble());
        }

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
        $bread['Prorrogar'] = null;

        return array(
            'entity' => $prorroga,
            'edicion_total' => true,
            'idEstadoContratoActivoComentado' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::ACTIVO_COMENTADO),
            'idEstadoContratoInactivo' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::INACTIVO),
            'idEstadoContratoDesocupado' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::DESOCUPADO),            
            'tipoContrato' => ConstanteCategoriaContrato::PRORROGA,
            'unidadesTiempo' => json_encode($unidadesTiempoJson),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Prorrogar contrato'
        );
    }

    /**
     *
     * @Route("/{id}/adenda", name="contrato_adenda")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\Contrato:new.html.twig")
     * -@Security("has_role('ROLE_CREAR_MODIFICAR_CONTRATOS')")
     */
    public function newAdendaAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contratoOrigen = $em->getRepository('ADIFContableBundle:Facturacion\Contrato')
                ->find($id);

        if (!$contratoOrigen) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Contrato.');
        }

        /* @var $adenda ContratoVenta */
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

                $cantidadFacturas = $cicloFacturacion->getCantidadFacturas() -
                        $cicloFacturacion->getCantidadFacturasPendientes();

                $cicloFacturacionReadonly->setCantidadFacturas($cantidadFacturas);

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

        $form->get('cliente_razonSocial')
                ->setData($adenda->getCliente()->getRazonSocial());

        $form->get('cliente_cuit')
                ->setData($adenda->getCliente()->getCUIT());

        if (method_exists($adenda, 'getLicitacion')) {

            $form->get('numeroLicitacion')
                    ->setData($adenda->getLicitacion()->getNumero());

            $form->get('fechaApertura')
                    ->setData($adenda->getLicitacion()->getFechaApertura());
        }

        if ($adenda->getIndicaNumeroInmueble()) {
            $form->get('numeroInmueble')
                    ->setData($adenda->getNumeroInmueble());
        }

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
            'idEstadoContratoInactivo' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::INACTIVO),
            'idEstadoContratoDesocupado' => $this->getIdEstadoByCodigo(ConstanteEstadoContrato::DESOCUPADO),            
            'tipoContrato' => ConstanteCategoriaContrato::ADENDA,
            'unidadesTiempo' => json_encode($unidadesTiempoJson),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Adendar contrato'
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
     * Finds and displays a histórico de Contrato.
     *
     * @Route("/{id}/historico", name="contrato_historico")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\Contrato:historico.html.twig")
     * -@Security("has_role('ROLE_VISUALIZAR_CONTRATOS')")
     */
    public function showHistoricoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contrato = $em->getRepository('ADIFContableBundle:Facturacion\Contrato')
                ->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Contrato.');
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
     * Finds and displays a detalle del saldo de Contrato.
     *
     * @Route("/{id}/detalle_saldo", name="contrato_detalle_saldo")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\Contrato:show.detalle_saldo.html.twig")
     * -@Security("has_role('ROLE_VISUALIZAR_CONTRATOS')")
     */
    public function showDetalleSaldoAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contrato = $em->getRepository('ADIFContableBundle:Facturacion\Contrato')
                ->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Contrato.');
        }

        $comprobantesModificanSaldo = $contrato->getComprobantesModificanSaldo();
        $comprobantesNoModificanSaldo = $contrato->getComprobantesNoModificanSaldo();

        $bread = $this->base_breadcrumbs;
        $bread['Contrato ' . $contrato->__toString()] = $this->generateUrl('contrato_show', array('id' => $contrato->getId()));
        $bread['Detalle del saldo'] = null;

        return array(
            'entity' => $contrato,
            'conceptoPercepcionIIBB' => \ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB,
            'conceptoPercepcionIVA' => \ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA,
            'comprobantesModificanSaldo' => $comprobantesModificanSaldo,
            'comprobantesNoModificanSaldo' => $comprobantesNoModificanSaldo,
            'breadcrumbs' => $bread,
            'page_title' => 'Detalle del saldo de contrato'
        );
    }

    /**
     * Se agrega poliza al contrato
     *
     * @Route("/agregarPoliza/{id}", name="contrato_agregar_poliza")
     * @Method("GET")
     * @Template("ADIFContableBundle:Facturacion\Contrato:show.html.twig")
     * -@Security("has_role('ROLE_CREAR_MODIFICAR_CONTRATOS')")
     */
    public function agregarPolizaAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $contrato = $em->getRepository('ADIFContableBundle:Facturacion\Contrato')->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Contrato.');
        }

        $editForm = $this->createEditForm($contrato, 'contrato_update_polizas');

        $bread = $this->base_breadcrumbs;
        $bread['Contrato ' . $contrato->__toString()] = null;

        return array(
            'entity' => $contrato,
            'form' => $editForm->createView(),
            'tipoContrato' => ConstanteCategoriaContrato::CONTRATO_ORIGINAL,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver contrato'
        );
    }

    /**
     * @Route("/actualizarPolizas/{id}", name="contrato_update_polizas")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Facturacion\Contrato:show.html.twig")
     */
    public function updatePolizasAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $contrato Contrato */
        $contrato = $em->getRepository('ADIFContableBundle:Facturacion\Contrato')
                ->find($id);

        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Contrato.');
        }

        $idCliente = $contrato->getIdCliente();

        $polizasOriginales = new ArrayCollection();

        // Creo un ArrayCollection de las PolizaSeguroContrato actuales en la BBDD
        foreach ($contrato->getPolizasSeguro() as $polizaSeguro) {
            $polizasOriginales->add($polizaSeguro);
        }

        $editForm = $this->createEditForm($contrato, 'contrato_update_polizas');
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // A cada PolizaSeguroContrato, le seteo el Contrato
            foreach ($contrato->getPolizasSeguro() as $polizaSeguro) {
                $polizaSeguro->setContrato($contrato);
            }

            // Por cada PolizaSeguroContrato original
            foreach ($polizasOriginales as $polizaSeguro) {

                // Si fue eliminado
                if (false === $contrato->getPolizasSeguro()->contains($polizaSeguro)) {
                    $contrato->removePolizasSeguro($polizaSeguro);
                    $em->remove($polizaSeguro);
                }
            }

            $contrato->setIdCliente($idCliente);

            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Polizas modificadas con &eacute;xito.');

            return $this->redirect($this->generateUrl('contrato'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Contrato ' . $contrato->__toString()] = null;

        return array(
            'entity' => $contrato,
            'form' => $editForm->createView(),
            'tipoContrato' => ConstanteCategoriaContrato::CONTRATO_ORIGINAL,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver contrato'
        );
    }

    /**
     * @Route("/generarDefinitivos/", name="contrato_generar_definitivos")
     * @Method("PUT|GET")     
     */
    public function generarDefinitivosContratosVenta() {

        gc_enable();

        $parcial = false;

        $offset = 0;
        $limit = 20;
        $i = 1;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $contratosImportados = $em->getRepository('ADIFContableBundle:Facturacion\ContratoVenta')
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
                $mensajeError = $this->get('adif.contabilidad_presupuestaria_service')->crearDefinitivoFromContratoVenta($contratoImportado);
                if ($mensajeError != '') {
                    $this->get('session')->getFlashBag()->add('error', $mensajeError);
                    $parcial = true;
                }
            }
            unset($contratosImportados);
            $em->flush();
            $em->clear();
            gc_collect_cycles();
            $contratosImportados = $em->getRepository('ADIFContableBundle:Facturacion\ContratoVenta')
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
            $this->get('session')->getFlashBag()->add('success', 'Generacion de definitivos de Contratos de Venta exitosa');
        }

        return $this->redirect($this->generateUrl('ordenescompra_generar_definitivos'));
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
     * @Route("/cuotas_cancelables/", name="cuotas_venta_cancelables")
     * @Method("GET|POST")
     */
    public function getCuotasCancelablesByContratoAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idContrato = $request->query->get('id_contrato');

        $cuotasCancelables = [];

        $comprobantes = $em->getRepository('ADIFContableBundle:Facturacion\FacturaVenta')
                ->createQueryBuilder('fv')
                ->innerJoin('fv.contrato', 'cv')
                ->innerJoin('fv.estadoComprobante', 'ec')
                ->andWhere('cv.id = :id')
                ->andWhere('ec.id = :idEstado')
                ->setParameter('id', $idContrato)
                ->setParameter('idEstado', EstadoComprobante::__ESTADO_INGRESADO)
                ->orderBy('fv.numeroCuota')
                ->getQuery()
                ->getResult();

        /* @var $comprobante \ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta */
        foreach ($comprobantes as $comprobante) {
            $cuotasCancelables[] = array(
                'id' => $comprobante->getId(),
                'tipo' => 'Factura',
                'letra' => $comprobante->getLetraComprobante()->getLetra(),
                'observaciones' => $comprobante->getObservaciones(),
                'numero' => $comprobante->getNumeroCompleto(),
                'numeroCuota' => $comprobante->getNumeroCuota(),
                'montoNeto' => $comprobante->getImporteTotalNeto(),
                'montoIVA' => $comprobante->getImporteTotalIVA(),
                'idAlicuotaIva' => $comprobante->getRenglonesComprobante()->first()->getAlicuotaIVA()->getId(),
                'montoPercIIBB' => $comprobante->getImporteTotalPercepcionByConcepto(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IIBB),
                'montoPercIVA' => $comprobante->getImporteTotalPercepcionByConcepto(ConstanteConceptoPercepcion::CONCEPTO_PERCEPCION_IVA),
                'total' => $comprobante->getTotalMCL(),
                'totalOrigen' => $comprobante->getTotalMO(),
                'fecha' => $comprobante->getFechaComprobante()->format('d/m/Y'),
            );
        }

        return new JsonResponse($cuotasCancelables);
    }

}
