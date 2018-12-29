<?php

namespace ADIF\ContableBundle\Controller\Cobranza;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza;
use ADIF\ContableBundle\Entity\Cobranza\CobroNotaCreditoVenta;
use ADIF\ContableBundle\Entity\Cobranza\CobroCuponCredito;
use ADIF\ContableBundle\Entity\Cobranza\CobroAnticipoCliente;
use ADIF\ContableBundle\Entity\Cobranza\CobroRetencionCliente;
use ADIF\ContableBundle\Entity\Cobranza\AnticipoCliente;
use ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaBanco;
use ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque;
use ADIF\ContableBundle\Form\Cobranza\RenglonCobranzaBancoType;
use ADIF\ContableBundle\Form\Cobranza\RenglonCobranzaChequeType;
use ADIF\ContableBundle\Entity\Cobranza\RetencionCliente;
use ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza;
use ADIF\ContableBundle\Form\Cobranza\RetencionClienteType;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonCobranza;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
//use BG\BarcodeBundle\Util\Base1DBarcode as barCode;
use mPDF;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Types\Type;


/**
 * Cobranza\RenglonCobranza controller.
 *
 * @Route("/rengloncobranza")
 */
class RenglonCobranzaController extends BaseController {

    /**
     * ID_TAB_1
     */
    const ID_TAB_1 = "#tab_1";

    /**
     * ID_TAB_2
     */
    const ID_TAB_2 = "#tab_2";

    /**
     *
     * @var type 
     */
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Cobranzas' => $this->generateUrl('rengloncobranza')
        );
    }

    /**
     * Lists all Cobranza\RenglonCobranza entities.
     *
     * @Route("/", name="rengloncobranza")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')
                ->findByEstaActiva(true);
//              ->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Cuentas bancarias'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Lista de cuentas para registrar cobranzas',
            'page_info' => 'Lista de cuentas para registrar cobranzas'
        );
    }

    /**
     * Tabla para Cobranza\RenglonCobranza .
     *
     * @Route("/index_table_banco/", name="rengloncobranza_table_banco")
     * @Method("GET|POST")
     */
    public function indexTableBancoAction(Request $request) {
        $id_cuenta = $request->query->get('id_cuenta');
        $estado = ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE;
        $entities = $this->obtenerRenglonesCobranzas(' = ' . $id_cuenta, $estado, 'Banco', null, null);

        $bread = $this->base_breadcrumbs;
        $bread ['Cobranzas'] = null;

        $clientes = $this->obtenerClientesSugeridos($entities);

        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_banco.html.twig', array('entities' => $entities, 'clientes' => $clientes)
        );
    }

    public function obtenerClientesSugeridos($registros_banco) {
        $clientes = array();
        foreach ($registros_banco as $registro) {
            $codigo = $registro->getCodigo();
            $codigo_comprobante = substr($codigo, 4, 12);

            $codigo_contrato = substr($codigo, 4, 14);
            $clientes[$registro->getId()] = 'hola';
        }
        return $clientes;
    }

    /**
     * Tabla para Cobranza\RenglonCobranza .
     *
     * @Route("/index_table_banco_a_imputar/", name="rengloncobranza_table_banco_a_imputar")
     * @Method("GET|POST")
     */
    public function indexTableBancoAImputarAction(Request $request) {
        $id_cuenta = $request->query->get('id_cuenta');
        $estado = ConstanteEstadoRenglonCobranza::ESTADO_A_IMPUTAR;
        $entities = $this->obtenerRenglonesCobranzas(' = ' . $id_cuenta, $estado, 'Banco', null, null);

        $bread = $this->base_breadcrumbs;
        $bread ['Cobranzas'] = null;
        $clientes = $this->obtenerClientesSugeridos($entities);

        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_banco.html.twig', array('entities' => $entities, 'clientes' => $clientes)
        );
    }

    /**
     * Tabla para AnticipoCliente.
     *
     * @Route("/index_table_anticipos/", name="rengloncobranza_index_table_anticipos")
     * @Method("GET|POST")
     */
    public function indexTableAnticiposAction(Request $request) {
        $id_cuenta = $request->query->get('id_cuenta');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $repository = $em->getRepository('ADIFContableBundle:Cobranza\AnticipoCliente');
        $query = $repository->createQueryBuilder('a')
                ->innerJoin('a.cobroRenglonCobranza', 'c')
                ->leftJoin('c.renglonesCobranza', 'r')
                ->leftJoin('c.cheques', 'ch')
                //->innerJoin('a.cobrosAnticipoCliente', 'c')             
                ->where('r.idCuentaBancaria = ' . $id_cuenta . ' OR r.idCuentaBancaria IS NULL')
                //->andWhere('c NOT INSTANCE OF ADIFContableBundle:Cobranza\CobroAnticipoCliente')              
                //->andWhere('c NOT INSTANCE OF ADIFContableBundle:Cobranza\CobroNotaCreditoVenta')              
                ->andWhere('a.saldo > 0')//->where('a.cobrosAnticipoCliente IS EMPTY')                
				->andWhere('r.esMigracion = false')
                ->orderBy('c.fecha', 'ASC')
                ->getQuery();

        $entities = $query->getResult();


        $bread = $this->base_breadcrumbs;
        $bread ['Cobranzas'] = null;

        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_anticipos.html.twig', array('entities' => $entities)
        );
    }

    /**
     * Creates a new Cobranza\RenglonCobranza entity.
     *
     * @Route("/insertar", name="rengloncobranza_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:Cobranza\RenglonCobranza:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new RenglonCobranzaBanco();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('rengloncobranza'));
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
            'page_title' => 'Crear cobranza',
        );
    }

    /**
     * Creates a form to create a Cobranza\RenglonCobranza entity.
     *
     * @param RenglonCobranzaBanco $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RenglonCobranzaBanco $entity) {
        $form = $this->createForm(new RenglonCobranzaBancoType(), $entity, array(
            'action' => $this->generateUrl('rengloncobranza_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Creates a form to create a Cobranza\RenglonCobranza entity.
     *
     * @param RenglonCobranzaCheque $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateFormCheque(RenglonCobranzaCheque $entity) {
        $form = $this->createForm(new RenglonCobranzaChequeType(), $entity, array(
            'action' => $this->generateUrl('rengloncobranza_create'),
            'method' => 'POST',
            'entity_manager_conta' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh())
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Creates a form to create a Cobranza\RenglonCobranza entity.
     *
     * @param RetencionCliente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateFormRetencion(RetencionCliente $entity) {
        $form = $this->createForm(new RetencionClienteType($this->getDoctrine()->getManager($this->getEntityManager())), $entity, array(
            'action' => $this->generateUrl('rengloncobranza_create'),
            'method' => 'POST',
            //'entity_manager_conta' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Cobranza\RenglonCobranza entity.
     *
     * @Route("/crear", name="rengloncobranza_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new RenglonCobranzaBanco();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear cobranza'
        );
    }

    private function esCuentaBancoNacion($cuenta) {
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());
        $banco = strtoupper($emRRHH->getRepository('ADIFRecursosHumanosBundle:Banco')->find($cuenta->getIdBanco())->getNombre());
        return (substr_count($banco, 'NACION') || substr_count($banco, 'NACIÓN') > 0);
    }

    /**
     * Finds and displays a Cobranza\RenglonCobranza entity.
     *
     * @Route("/cuenta/{id}", name="rengloncobranza_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $renglon_nuevo = new RenglonCobranzaBanco();
        $form = $this->createCreateForm($renglon_nuevo);

        $renglon_nuevo2 = new RenglonCobranzaCheque();
        $form2 = $this->createCreateFormCheque($renglon_nuevo2);

        $renglon_nuevo3 = new RetencionCliente();
        $form3 = $this->createCreateFormRetencion($renglon_nuevo3);

        $entity = $this->getCuentaBancariaAdif($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CuentaBancariaADIF.');
        }

        $esNacion = $this->esCuentaBancoNacion($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Detalle de cuenta'] = null;

        $clientes = $em->getRepository('ADIFComprasBundle:Cliente')->findAll();

        return array(
            'entity' => $entity,
            'renglon_nuevo' => $renglon_nuevo,
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'form3' => $form3->createView(),
            'esNacion' => $esNacion,
            'clientes' => $this->getClientesDisponibles($clientes),
            'breadcrumbs' => $bread,
            'page_title' => 'Cobranzas'
        );
    }

    /**
     * Displays a form to edit an existing Cobranza\RenglonCobranza entity.
     *
     * @Route("/editar/{id}", name="rengloncobranza_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:Cobranza\RenglonCobranza:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cobranza\RenglonCobranza.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar cobranza'
        );
    }

    /**
     * Creates a form to edit a Cobranza\RenglonCobranza entity.
     *
     * @param RenglonCobranza $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(RenglonCobranza $entity) {
        $form = $this->createForm(new RenglonCobranzaType(), $entity, array(
            'action' => $this->generateUrl('rengloncobranza_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Cobranza\RenglonCobranza entity.
     *
     * @Route("/actualizar/{id}", name="rengloncobranza_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:Cobranza\RenglonCobranza:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cobranza\RenglonCobranza.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('rengloncobranza'));
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
            'page_title' => 'Editar cobranza'
        );
    }

    /**
     * Deletes a Cobranza\RenglonCobranza entity.
     *
     * @Route("/borrar/{id}", name="rengloncobranza_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranza')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cobranza\RenglonCobranza.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('rengloncobranza'));
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/eliminar_movimientos/", name="rengloncobranza_eliminar_movimientos")
     * @Method("GET|POST")
     */
    public function eliminarMovimientosAction(Request $request) {
        $idsMovimientos = json_decode($request->request->get('ids', '[]'));

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $estado_pendiente = ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE;

        foreach ($idsMovimientos as $idMovimiento) {
            $movimiento = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranza')->find($idMovimiento);

            if (!$movimiento) {
                //throw $this->createNotFoundException('No se puede encontrar el RenglonConciliacion.');
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'El cobro no se encuentra.'));
            }
            if ($movimiento->getEstadoRenglonCobranza()->getDenominacion() != $estado_pendiente) {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'El cobro no est&aacute; pendiente, tiene otro estado.'));
            }

            $em->remove($movimiento);
        }
        $em->flush();
        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito.'));
    }

    private function getCuentaBancariaAdif($id_cuenta) {
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        return $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')
                        ->find($id_cuenta);
    }

    private function crearRenglonCobranzaBanco($fecha, $monto, $codigo, $esManual, $cuenta, $estado, $transaccion, $esOnabe = false) {
        $renglonCobranza = new RenglonCobranzaBanco();
        $renglonCobranza->setFecha($fecha);
        $renglonCobranza->setMonto($monto);
        $renglonCobranza->setCodigo($codigo);
        $renglonCobranza->setEsManual($esManual);
        $renglonCobranza->setCuentaBancaria($cuenta);
        $renglonCobranza->setEstadoRenglonCobranza($estado);
        $renglonCobranza->setNumeroTransaccion($transaccion);
        $renglonCobranza->setEsOnabe($esOnabe);
        return $renglonCobranza;
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/agregar_movimiento/", name="rengloncobranza_agregar_movimiento")
     * @Method("GET|POST")
     */
    public function agregarMovimientoAction(Request $request) {

        $fechaRequest = $request->request->get('fecha');
        $fechaRequestString = substr($fechaRequest, 6, 4) . '-' . substr($fechaRequest, 3, 2) . '-' . substr($fechaRequest, 0, 2);
        $fecha = new DateTime(date('Y-m-d', strtotime($fechaRequestString)));
        $referencia = $request->request->get('referencia');
        $monto = $request->request->get('monto');
        $observacion = $request->request->get('observacion');
        $tipoRegistro = $request->request->get('tipoRegistro');

        if (strlen($referencia) > 8) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'El n&uacute;mero de referencia debe contener un m&aacute;ximo de 8 caracteres'));
        }

        $emContable = $this->getDoctrine()->getManager($this->getEntityManager());
        $estado = $emContable->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')->findOneByDenominacion(ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE);
        if (!$estado) {
            throw $this->createNotFoundException('No se encuentran los estados en la base de datos.');
        }

        $cuenta = $this->getCuentaBancariaAdif($request->request->get('id_cuenta'));
        $transaccion = $referencia; //? str_pad($referencia, 8, '0', STR_PAD_LEFT) : $referencia;
        $esOnabe = ($tipoRegistro == 4) ? true : false; // Si es la opcion 4: "Cobro ONABE"
        $renglonCobranza = $this->crearRenglonCobranzaBanco($fecha, $monto, null, true, $cuenta, $estado, $transaccion, $esOnabe);
        $renglonCobranza->setObservacion($observacion);
        $emContable->persist($renglonCobranza);
        $emContable->flush();

        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito.'));
    }

    /**
     * Obtiene los RenglonesCobranza de un determinado estado para una cuenta bancaria.
     * 
     */
    private function obtenerRenglonesCobranzas($id_cuenta, $estado, $tipo, $fecha_inicio, $fecha_fin) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $repository = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranza' . $tipo);
        $query = $repository->createQueryBuilder('r')
                ->innerJoin('r.estadoRenglonCobranza', 'e')
                ->where('r.idCuentaBancaria' . $id_cuenta)
                ->andWhere('e.denominacion = \'' . $estado . '\'')
                ->andWhere('r.esMigracion = false')
                ->orderBy('r.fecha', 'ASC');

        if ($fecha_inicio) {
            $query = $query->andWhere('r.fechaRegistro >= \'' . $fecha_inicio->format('Y-m-d H:i:s') . '\'');
        }
        if ($fecha_fin) {
            $query = $query->andWhere('r.fechaRegistro <= \'' . $fecha_fin->format('Y-m-d H:i:s') . '\'');
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Procesamiento del extracto bancario.
     *
     * @Route("/cargar_archivo_txt/", name="rengloncobranza_cargar_archivo_txt")
     * @Method("POST")
     * @Template("ADIFContableBundle:Cobranza/RenglonCobranza:reporte_cobranzas_rechazadas.html.twig")
     */
    public function cargarArchivoTxtAction(Request $request) {
        $cuenta = $this->getCuentaBancariaAdif($request->request->get('id_cuenta'));
        $tipoRegistro = $request->request->get('tipoRegistro');
        $basura = "C:\\fakepath\\"; //lo agregan antes del nombre del archivo los navegadores como chrome menos firefox
        $file_name = str_replace($basura, '', $request->request->get('nombre_archivo'));
        $nombre_archivo = $cuenta->getCbu() . '_' . $file_name;
        $archivo = $request->files->get('archivo');

        if ($archivo) {
            $error = false;
            $uploadDir = dirname($this->container->getParameter('kernel.root_dir')) . '/web/uploads/cobranzas/archivos';
            $archivo->move($uploadDir, $nombre_archivo);
            $ruta = $uploadDir . '/' . $nombre_archivo;
            try {
                //$contenido = file_get_contents($ruta);
                $contenido = fopen($ruta, "r");
            } catch (Exception $e) {
                $error = true;
            }
            //var_dump($error);
            $excel_tabsheet_1 = array();
            $excel_tabsheet_2 = array();

            $emContable = $this->getDoctrine()->getManager($this->getEntityManager());
            $estado = $emContable->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')->findOneByDenominacion(ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE);

            while (!feof($contenido) && !$error) {
                $linea = fgets($contenido);
                $longitud = strlen($linea);
                if ($longitud > 0) {

                    $fecha_aux = substr($linea, 18, 8); //substr($linea, 26, 8);

                    $transaccion = substr($linea, 34, 8);

                    $codigo = substr($linea, 58, 26);

                    $numero_cheque = substr($linea, 138, 16);

                    $letra = substr($linea, 154, 1);

                    $monto = floatval(substr($linea, 42, 13) . '.' . substr($linea, 55, 2));

                    $error = $longitud < 160 || !is_numeric($codigo) || !is_numeric($numero_cheque) || !is_numeric($fecha_aux) || !is_numeric($transaccion) || !is_numeric($monto); //if ($longitud < 84) {
//                    if ($error) {
//                        var_dump(strlen($linea)==0);
//                    }

                    try {
                        $fecha = new DateTime(date('Y-m-d', strtotime(substr($fecha_aux, 0, 4) . '-' . substr($fecha_aux, 4, 2) . '-' . substr($fecha_aux, 6, 2))));
                    } catch (Exception $e) {
                        $error = $error || true;
                    }
                    //var_dump($error);

                    $codigo_comprobante = substr($codigo, 14, 2);

                    $numero_comprobante = substr($codigo, 4, 10);

                    if (!$error) {
                        $esOnabe = ($tipoRegistro == 4) ? true : false; // Si es la opcion 4: "Cobro ONABE"
                        if ($numero_cheque == '0000000000000000') { //NO ES CHEQUE
                            $renglonCobranza = $this->crearRenglonCobranzaBanco($fecha, $monto, $codigo, false, $cuenta, $estado, $transaccion, $esOnabe);
                            $emContable->persist($renglonCobranza);
                        } else {

                            if ($letra == 'P') {
                                if ($codigo_comprobante != '04') { //NO ES RECIBO
                                    $renglonCobranza = $this->crearRenglonCobranzaBanco($fecha, $monto, $codigo, false, $cuenta, $estado, $transaccion, $esOnabe);
                                    $emContable->persist($renglonCobranza);
                                } else { //ES RECIBO
                                    //LA INFO ES PARA DEPOSITAR EL CHEQUE
                                    $excel_tabsheet_2[] = array(
                                        'fecha' => $fecha,
                                        'codigo' => $codigo,
                                        'importe' => $monto,
                                        'recibo' => $numero_comprobante,
                                        'cheque' => $numero_cheque,
                                        'estado' => 'ENTREGADO'
                                    );
                                }
                            } else {
                                if ($letra == 'R') {
                                    if ($codigo_comprobante != '04') { //NO ES RECIBO
                                        //LA INFO ES PARA DESIMPUTAR/DESHACER EL COBRO
//                                        $renglonCobranzaBanco = $emContable->getRepository('ADIFContableBundle:Cobranza\RenglonCobranzaBanco')->findOneBy(
//                                            array('fecha' => $fecha . ' 00:00:00', 'codigo' => $codigo, 'monto' => $monto, 'numeroTransaccion' => $transaccion)
//                                        );
//                                        if ($renglonCobranzaBanco != null) {
//                                            $renglonCobranzaBanco->setFueRechazada(true);
//                                        } // por el sino ponerle una columna mas al excel de no encontrada
                                        $excel_tabsheet_1[] = array(
                                            'fecha' => $fecha,
                                            'codigo' => $codigo,
                                            'importe' => $monto,
                                            'referencia' => $transaccion
                                        );
                                    } else { //ES RECIBO
                                        //LA INFO ES PARA DESHACER EL DEPOSITO DEL CHEQUE
//                                        $renglonCobranzaCheque = $emContable->getRepository('ADIFContableBundle:Cobranza\RenglonCobranzaBanco')->findOneBy(
//                                            array('fecha' => $fecha . ' 00:00:00', 'monto' => $monto)
//                                        );
//                                        if ($renglonCobranzaCheque != null && $renglonCobranzaCheque->getReciboCheque() != null && $renglonCobranzaCheque->getReciboCheque()->getCodigoBarras() == $codigo) {
//                                            $renglonCobranzaCheque->setFueRechazada(true);
//                                        } // por el sino ponerle una columna mas al excel de no encontrada                                   
                                        $excel_tabsheet_2[] = array(
                                            'fecha' => $fecha,
                                            'codigo' => $codigo,
                                            'importe' => $monto,
                                            'recibo' => $numero_comprobante,
                                            'cheque' => $numero_cheque,
                                            'estado' => 'RECHAZADO'
                                        );
                                    }
                                }
                                // LO QUE ES L SE IGNORA
                            }
                        }
                    }
                }
            }

            fclose($contenido);
            if (!$error) {
                $emContable->flush();
            }
            //var_dump($error);die();
            return array('error' => $error, 'file_name' => $file_name, 'renglones_banco' => $excel_tabsheet_1, 'renglones_cheque' => $excel_tabsheet_2);
        } else {
            return array('error' => true, 'file_name' => $file_name, 'renglones_banco' => array(), 'renglones_cheque' => array());
        }
    }

    /**
     * Realiza la cancelación de los comprobantes que se pueda, genera anticipo si existe y deja algunos cobros en cobros a imputar
     *
     * @Route("/imputar_automaticamente/", name="rengloncobranza_imputar_automaticamente")
     * @Method("POST")
     */
    public function imputarAutomaticamenteAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $numerosAsientos = [];

        $estado_imputado = ConstanteEstadoRenglonCobranza::ESTADO_IMPUTADO;
        $estado_pendiente = ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE;

        $estado = $em->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')
                ->findOneByDenominacion($estado_imputado);

        $estadoComprobanteCancelado = $em->getRepository('ADIFContableBundle:EstadoComprobante')
                ->find(EstadoComprobante::__ESTADO_CANCELADO);

        $estadoComprobanteAnulado = $em->getRepository('ADIFContableBundle:EstadoComprobante')
                ->find(EstadoComprobante::__ESTADO_ANULADO);

        $ids_banco = json_decode($request->request->get('ids_banco', '[]'));
        $ids_comprobantes = json_decode($request->request->get('ids_comprobantes', '[]'));

        $cant_renglones = sizeof($ids_banco);
        $cant_comprobantes = sizeof($ids_comprobantes);

        $hacerFlush = false;

        $idsMovimientos = array();
        $renglonesCobranzas = array();

        $offsetNumeroAsiento = 0;

        $cobranzaService = $this->get('adif.cobranza_service');
        $numeroBaseRecibo = $cobranzaService->getSiguienteNumeroRecibo();

		// Todas las cobranzas se van a imputar al dia de la fecha y sus respectivos asientos tambien (no al de la cobranza) - gluis - 23/11/2016
        $fecha_hoy = new DateTime();

        if ($cant_renglones >= $cant_comprobantes) {

            for ($index = 0; $index < $cant_comprobantes; $index++) {

                $comprobante_venta = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')
                        ->find($ids_comprobantes[$index]);

                if ($comprobante_venta->getEstadoComprobante() == $estadoComprobanteAnulado) {
                    $error_aux = ($cant_comprobantes == 1 //
                                    ? 'El comprobante fue anulado' //
                                    : 'Uno de los comprobantes fue anulado') . ', por favor actualice el listado';

                    return new JsonResponse(array(
                        'status' => 'ERROR',
                        'message' => 'Se produjo un error. ' . $error_aux)
                    );
                }

                $cliente = $comprobante_venta->getCliente();

                $saldo = $comprobante_venta->getSaldo();
                $monto_total = 0;
                $cobro_renglon_cobranza = new CobroRenglonCobranza();

                //$cobro_renglon_cobranza->setMontoCheques(0);

                $reciboCobranza = new ReciboCobranza();
                $reciboCobranza->setNumero($numeroBaseRecibo + $index);
                $reciboCobranza->setCliente($cliente);
                $reciboCobranza->addComprobante($comprobante_venta);
                $reciboCobranza->setCodigoBarras($reciboCobranza->generarCodigoBarras());

                for ($index2 = 0; $index2 < $cant_renglones; $index2++) {

                    $renglon_cobranza = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranza')
                            ->find($ids_banco[$index2]);

                    $monto = $renglon_cobranza->getMonto();

                    if ($renglon_cobranza->getEstadoRenglonCobranza()->getDenominacion() == $estado_pendiente &&
                            $comprobante_venta->getCodigoBarrasNacion() != '' && //o preguntar por si el renglon no es manual
                            $renglon_cobranza->getCodigo() == $comprobante_venta->getCodigoBarrasNacion() &&
                            $saldo > 0 && $monto > 0) {

                        $monto_total += $monto;
                        $renglon_cobranza->setEstadoRenglonCobranza($estado);
                        $renglon_cobranza->setNumeroRecibo($numeroBaseRecibo + $index);

                        $fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $renglon_cobranza->getFecha()->format('Y-m-d') . ' ' . $fecha_hoy->format('H:i:s'));
                        $renglon_cobranza->setFechaRegistro($fecha);

                        $cobro_renglon_cobranza->addRenglonesCobranza($renglon_cobranza);

                        $idsMovimientos[] = $renglon_cobranza->getId();

                        $reciboCobranza->addRenglonesBanco($renglon_cobranza);
                    }
                }

                $reciboCobranza->setFecha($fecha);
                $cobro_renglon_cobranza->setFecha($fecha);

                if ($monto_total > 0) {

                    $hacerFlush = true;

                    if ($saldo - $monto_total < 0) {
                        $anticipo_cliente = new AnticipoCliente();
                        $anticipo_cliente->setCliente($comprobante_venta->getCliente());
                        $anticipo_cliente->setMonto(($saldo - $monto_total) * (-1));
                        $anticipo_cliente->setSaldo(($saldo - $monto_total) * (-1));
                        $anticipo_cliente->setCobroRenglonCobranza($cobro_renglon_cobranza);
                        $cobro_renglon_cobranza->setAnticipoCliente($anticipo_cliente);

                        $em->persist($anticipo_cliente);

                        $comprobante_venta->setSaldo(0);
                        $cobro_renglon_cobranza->setMonto($saldo);
                    } else {

                        $comprobante_venta->setSaldo($saldo - $monto_total);
                        $cobro_renglon_cobranza->setMonto($monto_total);
                        $em->persist($cobro_renglon_cobranza);
                    }

                    if ($comprobante_venta->getSaldo() == 0) {
                        $comprobante_venta->setEstadoComprobante($estadoComprobanteCancelado);
                    }

                    $comprobante_venta->addCobro($cobro_renglon_cobranza);
                    $cobro_renglon_cobranza->addComprobante($comprobante_venta);

                    $renglonesCobranzas[] = $cobro_renglon_cobranza;

                    // Persisto los asientos contables y presupuestarios
                    $resultArray = $this->get('adif.asiento_service')->
                            generarAsientosContablesParaCobranzaImputada($fecha, $cobro_renglon_cobranza, $this->getUser(), true, false, $offsetNumeroAsiento++);

                    // Si el asiento presupuestario falló
                    if ($resultArray['mensajeErrorPresupuestario'] != null) {

                        return new JsonResponse(array(
                            'status' => 'ERROR',
                            'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario'])
                        );
                    }

                    // Si el asiento contable falló
                    if ($resultArray['mensajeErrorContable'] != null) {

                        return new JsonResponse(array(
                            'status' => 'ERROR',
                            'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable'])
                        );
                    }

                    // Si no hubo errores en los asientos
                    if ($resultArray['numeroAsiento'] != -1) {

                        $numerosAsientos[] = $resultArray['numeroAsiento'];
                    }
                }
            }

            if ($hacerFlush) {


                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                // Si no hubo errores en los asientos
                if (!empty($numerosAsientos)) {

                    try {
                        $em->flush();

                        $em->getConnection()->commit();
                        $idsCobros = array();

                        foreach ($renglonesCobranzas as $renglonCobranza) {
                            $idsCobros[] = $renglonCobranza->getId();
                        }

                        $dataArray = [
                            'data-id-cobros' => implode(',', $idsCobros),
                            'data-id-movimientos' => implode(',', $idsMovimientos)
                        ];



                        $mensajeFlash = $this->get('adif.asiento_service')
                                ->showMensajeFlashColeccionAsientosContables($numerosAsientos, $dataArray, true);

                        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito', 'mensajeAsiento' => $mensajeFlash));
                    } catch (\Exception $e) {

                        $em->getConnection()->rollback();

                        $em->close();

                        return new JsonResponse(array('status' => 'ERROR', 'message' => 'Ocurri&oacute; un error. Por favor intente realizar la operaci&oacute;n nuevamente', 'mensajeAsiento' => ''));
                    }
                }

                return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito'));
            } else {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'No se encontraron coincidencias'));
            }
        } else {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'La cantidad de cobranzas es inferior a la cantidad de comprobantes'));
        }
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/enviar_cobranzas_a_imputar/", name="rengloncobranza_enviar_cobranzas_a_imputar")
     * @Method("GET|POST")
     */
    public function enviarCobranzasAImputarAction(Request $request) {
        $idsMovimientos = json_decode($request->request->get('ids', '[]'));

        $tipo = $request->request->get('tipo');

        return $this->actualizarRenglonesBanco($idsMovimientos, ConstanteEstadoRenglonCobranza::ESTADO_A_IMPUTAR, $tipo);
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/enviar_cobranzas_a_pendientes/", name="rengloncobranza_enviar_cobranzas_a_pendientes")
     * @Method("GET|POST")
     */
    public function enviarCobranzasAPendientesAction(Request $request) {

        $idsMovimientos = json_decode($request->request->get('ids', '[]'));
        $tipo = $request->request->get('tipo');

        return $this->actualizarRenglonesBanco($idsMovimientos, ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE, $tipo);
    }

    /**
     * 
     * @param type $idsMovimientos
     * @param type $estado
     * @return JsonResponse
     */
    private function actualizarRenglonesBanco($idsMovimientos, $estado, $tipo) {
        if ($estado == ConstanteEstadoRenglonCobranza::ESTADO_A_IMPUTAR) {
            $cobranzaService = $this->get('adif.cobranza_service');
            $numeroBaseRecibo = $cobranzaService->getSiguienteNumeroRecibo();
            $indice = 0;
        }

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        //$offsetNumeroAsiento = 0;

        $renglones = array();

        $fecha_hoy = new DateTime();

        foreach ($idsMovimientos as $idMovimiento) {

            $movimiento = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranza')
                    ->find($idMovimiento);

            if (!$movimiento) {

                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'No se puede encontrar una de las cobranzas seleccionadas'));
            }

            if (($tipo == 'cheque') && ($movimiento->getCuenta() != null || $movimiento->getFechaDeposito() != null)) {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'El cheque est&aacute; depositado'));
            }

            //el if va a cambiar cuando se quiera desimputar desde la pestaña 3, agregar otra verificación
            $estadoActualDeseado = ($estado == ConstanteEstadoRenglonCobranza::ESTADO_A_IMPUTAR) ? ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE : ConstanteEstadoRenglonCobranza::ESTADO_A_IMPUTAR;

            if ($movimiento->getEstadoRenglonCobranza()->getDenominacion() != $estadoActualDeseado) {

                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'Una de las cobranzas seleccionadas no está en el estado correcto'));
            }

            if (($estado == ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE) && ($movimiento->estaConciliado())) {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'Una de las cobranzas forma parte de una conciliación bancaria'));
            }

            $nuevo_estado = $em->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')
                    ->findOneByDenominacion($estado);

            $movimiento->setEstadoRenglonCobranza($nuevo_estado);

            if ($estado == ConstanteEstadoRenglonCobranza::ESTADO_A_IMPUTAR) {
                $fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $movimiento->getFecha()->format('Y-m-d') . ' ' . $fecha_hoy->format('H:i:s'));
                $reciboCobranza = new ReciboCobranza();
                $reciboCobranza->setNumero($numeroBaseRecibo + $indice);
                $reciboCobranza->setFecha($fecha);
                $reciboCobranza->setCodigoBarras($reciboCobranza->generarCodigoBarras());

                if ($tipo == 'cheque') {
                    $reciboCobranza->addRenglonesCheque($movimiento);
                } else {
                    $reciboCobranza->addRenglonesBanco($movimiento);
                }

                $movimiento->setFechaRegistro($fecha);
                $movimiento->setNumeroRecibo($numeroBaseRecibo + $indice);
                $indice++;
            } else {
                $fecha = $fecha_hoy;
            } 

            if ($estado == ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE) {
                //ACA DEBERIA QUITARLE EL RECIBO PARA DEJARLO ANULADO
                if ($tipo == 'cheque') {
                    $reciboCobranza = $movimiento->getReciboCheque();
                    if ($reciboCobranza) {
                        $reciboCobranza->removeRenglonesCheque($movimiento);
                    }
                } else {
                    $reciboCobranza = $movimiento->getReciboBanco();
                    if ($reciboCobranza) {
                        $reciboCobranza->removeRenglonesBanco($movimiento);
                    }
                }

                $movimiento->setFechaRegistro(null);
                $movimiento->setNumeroRecibo(null);
            }

            $renglones [] = $movimiento;
        }

        // Persisto los asientos contables y presupuestarios
        $resultArray = $this->get('adif.asiento_service')
                ->generarAsientosContablesParaCobranzaPreImputada($fecha, $renglones, $this->getUser(), $estado == ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE, $tipo); //, $offsetNumeroAsiento++);
        // Si el asiento presupuestario falló
        if ($resultArray['mensajeErrorPresupuestario'] != null) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario']));
        }

        // Si el asiento contable falló
        if ($resultArray['mensajeErrorContable'] != null) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable']));
        }

        // Si no hubo errores en los asientos
        if ($resultArray['numeroAsiento'] != -1) {

            // Comienzo la transaccion
            $em->getConnection()->beginTransaction();

            try {
                $em->flush();

                $em->getConnection()->commit();

                $dataArray = array();

                if ($estado == ConstanteEstadoRenglonCobranza::ESTADO_A_IMPUTAR) {
                    $dataArray = [
                        'data-id-movimientos' => implode(',', $idsMovimientos)
                    ];
                }

                $mensajeFlash = $this->get('adif.asiento_service')
                        ->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], $dataArray, true);

                return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito', 'mensajeAsiento' => $mensajeFlash));
            } catch (\Exception $e) {

                $em->getConnection()->rollback();

                $em->close();

                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Ocurri&oacute; un error. Por favor intente realizar la operaci&oacute;n nuevamente', 'mensajeAsiento' => ''));
            }
        }

        return new JsonResponse(array(
            'status' => 'OK',
            'message' => 'Operaci&oacute;n realizada con &eacute;xito')
        );
    }

    /**
     * @Route("/imputar_manualmente/", name="rengloncobranza_imputar_manualmente")
     * @Method("POST")
     */
    public function imputarManualmenteAction(Request $request) {

        //Agregar verificación de del cliente
        $id_pago = $request->request->get('id_pago');
        $tipo_pago = $request->request->get('tipo_pago');
        $tab = $request->request->get('tab');
        $ids_comprobantes = json_decode($request->request->get('ids_comprobantes', '[]'));
        $montos_comprobantes = json_decode($request->request->get('montos_comprobantes', '[]'));
        $cant_comprobantes = sizeof($ids_comprobantes);

        
//        \Doctrine\Common\Util\Debug::dump( "imputarManualmenteAction" ); exit;

        $idCobros = [];

        $resultArray = [
            'numeroAsiento' => -1,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $estadoComprobanteCancelado = $em->getRepository('ADIFContableBundle:EstadoComprobante')
                ->find(EstadoComprobante::__ESTADO_CANCELADO);

        $estadoComprobanteAnulado = $em->getRepository('ADIFContableBundle:EstadoComprobante')
                ->find(EstadoComprobante::__ESTADO_ANULADO);

        //print_r($ids_comprobantes); print_r($montos_comprobantes);die();
        if ($cant_comprobantes == sizeof($montos_comprobantes)) {

            $fecha_hoy = new DateTime();

            $cobros = array();

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $estado_imputado = ConstanteEstadoRenglonCobranza::ESTADO_IMPUTADO;

            $estado = $em->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')->findOneByDenominacion($estado_imputado);

            switch ($tipo_pago) {
                case 1:
                    $pago = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranzaBanco')->find($id_pago);
                    $a_favor = $pago->getMonto();
                    if ($pago->getEstadoRenglonCobranza()->getDenominacion() == $estado_imputado) {
                        return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. El cobro seleccionado ya fue imputado'));
                    }
                    //$cobranzaService = $this->get('adif.cobranza_service');
                    //$numeroBaseRecibo = $cobranzaService->getSiguienteNumeroRecibo();
                    //$pago->setNumeroRecibo($numeroBaseRecibo);
                    //$pago->setFechaRegistro(new DateTime());
                    break;
                case 2:
                    $pago = $em->getRepository('ADIFContableBundle:Facturacion\NotaCreditoVenta')->find($id_pago);
                    $a_favor = $pago->getSaldo();
                    if ($a_favor == 0) {
                        return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. La nota de crédito seleccionada no tiene más saldo'));
                    }
                    break;
                case 3:
                    $pago = $em->getRepository('ADIFContableBundle:Cobranza\AnticipoCliente')->find($id_pago);
                    $a_favor = $pago->getSaldo();
                    if ($a_favor == 0) {
                        return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. El anticipo seleccionado no tiene más saldo'));
                    }
                    break;
                case 4:
                    $pago = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranzaCheque')->find($id_pago);
                    $a_favor = $pago->getMonto();
                    if ($pago->getEstadoRenglonCobranza()->getDenominacion() == $estado_imputado) {
                        return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. El cobro seleccionado ya fue imputado'));
                    }
//                    $cobranzaService = $this->get('adif.cobranza_service');
//                    $numeroBaseRecibo = $cobranzaService->getSiguienteNumeroRecibo();
//                    $pago->setNumeroRecibo($numeroBaseRecibo);
//                    $pago->setFechaRegistro(new DateTime());
                    break;
                case 5:
                    $pago = $em->getRepository('ADIFContableBundle:Cobranza\RetencionCliente')->find($id_pago);
                    $a_favor = $pago->getMonto();
                    if ($pago->getImputada()) {
                        return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. La retenci&oacute;n ya fue imputada'));
                    }
                    break;
            }

			$fecha_pago = $tipo_pago == 2 ? $pago->getFechaComprobante() : $pago->getFecha();

            if (($tab == self::ID_TAB_1) && ($tipo_pago == 1 || $tipo_pago == 4)) {//|| ($tipo_pago == 5)) {
                $fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $fecha_pago->format('Y-m-d') . ' ' . $fecha_hoy->format('H:i:s'));
            } else {
                $fecha = $fecha_hoy;
            }

            if ((($tipo_pago == 1 || $tipo_pago == 4) && ($tab == self::ID_TAB_1)) || ($tipo_pago == 5)) { //si es retencion crea recibo, si no es lo crea si es tab 1 banco o cheque
                $cobranzaService = $this->get('adif.cobranza_service');
                $numeroRecibo = $cobranzaService->getSiguienteNumeroRecibo();


                $reciboCobranza = new ReciboCobranza();
                $reciboCobranza->setNumero($numeroRecibo);
                $reciboCobranza->setFecha($fecha);
                $reciboCobranza->setCodigoBarras($reciboCobranza->generarCodigoBarras());

                switch ($tipo_pago) {
                    case 1:
                        $reciboCobranza->addRenglonesBanco($pago);
                        break;
                    case 4:
                        $reciboCobranza->addRenglonesCheque($pago);
                        break;
                    case 5:
                        $reciboCobranza->addRenglonesRetencion($pago);
                        break;
                }

                $pago->setNumeroRecibo($numeroRecibo);
                //if ($tipo_pago != 5) {
                $pago->setFechaRegistro($fecha);
                //}
            } else {
                if (($tipo_pago == 1 || $tipo_pago == 4) && ($tab == self::ID_TAB_2)) {
                    switch ($tipo_pago) {
                        case 1:
                            $reciboCobranza = $pago->getReciboBanco();
                            break;
                        case 4:
                            $reciboCobranza = $pago->getReciboCheque();
                            break;
                        case 5:
                            $reciboCobranza = $pago->getReciboRetencion();
                            break;
                    }
                }
            }


            $suma_valores = 0;
            $deuda = 0;

            for ($index = 0; $index < $cant_comprobantes; $index++) {

                $comprobante_venta = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')->find($ids_comprobantes[$index]);
                if ($comprobante_venta->getEstadoComprobante() == $estadoComprobanteAnulado) {
                    $error_aux = ($cant_comprobantes == 1 ? 'El comprobante fue anulado' : 'Uno de los comprobantes fue anulado') . ', por favor actualice el listado';
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. ' . $error_aux));
                }

                if ($tipo_pago == 5 && $comprobante_venta->tieneRetencionClienteImputada($pago->getTipoImpuesto())) {
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. Hay al menos un comprobante que tiene el mismo tipo de retención ya imputada'));
                }

                $clienteComp = $comprobante_venta->getCliente();

//                if ($reciboCobranza->getCliente() != null && $reciboCobranza->getCliente() != $clienteComp) {
//                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. Los comprobantes no todos los comprobantes son del mismo cliente'));
//                }

                if ($tipo_pago != 2 && $tipo_pago != 3 && $reciboCobranza) {
                    $reciboCobranza->setCliente($clienteComp);
                    $reciboCobranza->addComprobante($comprobante_venta);
                }

                $saldo = $comprobante_venta->getSaldo();
                $valor = $montos_comprobantes[$index];

                if ($saldo > 0 && $valor <= $saldo) {
                    $suma_valores += $valor;
                    $deuda += $saldo;
                    $comprobante_venta->setSaldo($saldo - $valor);

                    if ($comprobante_venta->getSaldo() == 0) {
                        $comprobante_venta->setEstadoComprobante($estadoComprobanteCancelado);
                    }
                    //$comprobante_venta->addCobro($cobro);
                } else {
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. No se puede imputar un valor mayor al saldo de un comprobante'));
                }

                switch ($tipo_pago) {
                    case 1:
                        $cobro = new CobroRenglonCobranza();
                        $pago->setEstadoRenglonCobranza($estado);
                        $cobro->addRenglonesCobranza($pago);
                        $pago->addCobrosRenglonCobranza($cobro);

                        break;
                    case 2:
                        $cobro = new CobroNotaCreditoVenta();
                        $cobro->addNotasCreditoVenta($pago);
                        $pago->addCobrosNotaCreditoVenta($cobro);
                        //$clienteNC = $pago->getContrato()->getCliente();
                        break;
                    case 3:
                        $cobro = new CobroAnticipoCliente();
                        $cobro->addAnticiposCliente($pago);
                        $pago->addCobrosAnticipoCliente($cobro);
                        break;
                    case 4:
                        $cobro = new CobroRenglonCobranza();

                        $pago->setEstadoRenglonCobranza($estado);
                        $cobro->setMontoCheques($valor);
                        $cobro->addCheques($pago);
                        $pago->addCobrosRenglonCobranzaCheque($cobro);
                        break;
                    case 5:
                        $cobro = new CobroRetencionCliente();
                        //
                        $cobro->addRetencionesCliente($pago);
                        $pago->addCobrosRetencionCliente($cobro);
                        break;
                }

                $cobro->setFecha($fecha);
                $cobro->setMonto($valor);

                $em->persist($cobro);

                $comprobante_venta->addCobro($cobro);
                $cobro->addComprobante($comprobante_venta);

                $cobros [] = $cobro;

                //PRIMER PARTE
            }
            if ($a_favor < $suma_valores) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. Se quiere imputar más de lo que se tiene a favor')); // que $deuda < $suma_valores ya fue verificado por cada factura
            } else {
                if ($suma_valores < $a_favor && $tipo_pago != 5) { //GENERA UN ANTICIPO, es lo mismo que $deuda < $a_favor
                    if (($tipo_pago == 1) || ($tipo_pago == 4)) { //se lo agrego al último cobro (el del último comprobante que se intentó cancelar con el popup)
                        $anticipo_cliente = new AnticipoCliente();
                        $anticipo_cliente->setCliente($clienteComp);
                        $anticipo_cliente->setMonto($a_favor - $suma_valores); //(($saldo_total - $a_favor) * (-1));
                        $anticipo_cliente->setSaldo($a_favor - $suma_valores);
                        $anticipo_cliente->setCobroRenglonCobranza($cobro);
                        $cobro->setAnticipoCliente($anticipo_cliente);
                        $em->persist($anticipo_cliente);
                        //$cobro->setMontoCheques($tipo_pago==4?$cobro->getMonto():0);
                    } else {
                        $pago->setSaldo($a_favor - $suma_valores); //(($saldo_total - $a_favor) * (-1));
                    }
                } else {
                    if ($tipo_pago != 1 && $tipo_pago != 4 && $tipo_pago != 5) {
                        $pago->setSaldo(0);
                    }
//                    else {
//                        if ($tipo_pago== 4) $cobro->setMontoCheques($a_favor);
//                    }
                }

                //SEGUNDA PARTE
            }

            // UNION ENTRE PRIMERA Y SEGUNDA PARTE

            if ($tipo_pago != 2 && $tipo_pago != 3) { // GENERAN ASIENTOS BANCO, CHEQUE Y RETENCION
                $usuario = $this->getUser();
                if ($tipo_pago == 1 || $tipo_pago == 4) { // el pago es con una cobranza banco o cheque
                    // Persisto los asientos contables y presupuestarios
					if ($tipo_pago == 1) {
						// Cobranza Banco y Cobranza ONABE
						$resultArray = $this->get('adif.asiento_service')->
                            generarAsientosContablesParaImputaciones($fecha, array(), array(), $cobros, $usuario, $tab == self::ID_TAB_1, $pago->getEsOnabe());
					} else {
						$resultArray = $this->get('adif.asiento_service')->
                            generarAsientosContablesParaImputaciones($fecha, array(), array(), $cobros, $usuario, $tab == self::ID_TAB_1);
					}
                    
                    //generarAsientosContablesParaCobranzaImputada($cobros, $usuario, $tab == self::ID_TAB_1, false);//, $offsetNumeroAsiento++);                    
                }
                // El pago es con un anticipo o retención
                else {
//                    if ($tipo_pago == 3) {
//                        // Persisto los asientos contables y presupuestarios
//                        $resultArray = $this->get('adif.asiento_service')
//                                ->generarAsientosContablesParaImputaciones(array(), $cobros, array(), $usuario, $tab == self::ID_TAB_1);
//                        //->generarAsientosContablesParaCobranzaImputadaConAnticipo($cobros, $usuario, false);//, $offsetNumeroAsiento++);
//                    } else {
                    // Persisto los asientos contables y presupuestarios
                    $resultArray = $this->get('adif.asiento_service')
                            ->generarAsientosContablesParaImputaciones($fecha, $cobros, array(), array(), $usuario, $tab == self::ID_TAB_1);
                    //->generarAsientosContablesParaRetencion($cobros, $usuario, false);//, $offsetNumeroAsiento++);
//                    }
                }

                // Si el asiento presupuestario falló
                if ($resultArray['mensajeErrorPresupuestario'] != null) {
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario']));
                }

                // Si el asiento contable falló
                if ($resultArray['mensajeErrorContable'] != null) {
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable']));
                }
            }
            if ($tipo_pago != 2 && $tipo_pago != 3) {
                // Si no hubo errores en los asientos
                if ($resultArray['numeroAsiento'] != -1) {

                    // Comienzo la transaccion
                    $em->getConnection()->beginTransaction();

                    try {
                        $em->flush();

                        $em->getConnection()->commit();

                        foreach ($cobros as $cobro) {
                            $idCobros[] = $cobro->getId();
                        }

                        if ($tab == self::ID_TAB_1 && ($tipo_pago == 1 || $tipo_pago == 4)) {
                            $dataArray = [
                                'data-id-cobros' => implode(',', $idCobros),
                                'data-id-movimientos' => $id_pago
                            ];
                        } else {
                            $dataArray = [
                                'data-id-cobros' => implode(',', $idCobros)
                            ];
                        }

                        $mensajeFlash = $this->get('adif.asiento_service')
                                ->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], array(), true);

                        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito', 'mensajeAsiento' => $mensajeFlash));
                    } catch (\Exception $e) {

                        $em->getConnection()->rollback();

                        $em->close();

                        return new JsonResponse(array('status' => 'ERROR', 'message' => 'Ocurri&oacute; un error. Por favor intente realizar la operaci&oacute;n nuevamente', 'mensajeAsiento' => ''));
                    }
                }
            } else {
                $em->flush();
            }

            return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito'));
        } else {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. Se envi&oacute; una cantidad distinta de comprobantes y montos a imputar'));
        }
    }

    /**
     * @Route("/imputar_manualmente_mezclado_tab2/", name="rengloncobranza_imputar_manualmente_mezclado_tab2")
     * @Method("POST")
     */
    public function imputarManualmenteMezcladoAction(Request $request) {
        $soloNC = true; // si es sólo nc o anticipo, que son los que no hacen asientos
        $estado_imputado = ConstanteEstadoRenglonCobranza::ESTADO_IMPUTADO;
        //$estado_pendiente = ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE;
        $estado_a_imputar = ConstanteEstadoRenglonCobranza::ESTADO_A_IMPUTAR;
//        \Doctrine\Common\Util\Debug::dump( "imputarManualmenteMezcladoAction" ); exit;

        $resultArray = [
            'numeroAsiento' => -1,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $tab = "#tab_2";

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $estado = $em->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')->findOneByDenominacion($estado_imputado);

        $estadoComprobanteCancelado = $em->getRepository('ADIFContableBundle:EstadoComprobante')
                ->find(EstadoComprobante::__ESTADO_CANCELADO);
        $estadoComprobanteAnulado = $em->getRepository('ADIFContableBundle:EstadoComprobante')
                ->find(EstadoComprobante::__ESTADO_ANULADO);

        $id_comprobante = $request->request->get('id_comprobante');
        $comprobante_venta = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')
                ->find($id_comprobante);

        if ($comprobante_venta->getEstadoComprobante() == $estadoComprobanteAnulado) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. El comprobante fue anulado, por favor actualice el listado'));
        }

        $saldo_comprobante = $comprobante_venta->getSaldo();
        $cliente = $comprobante_venta->getCliente(); //VERIFICAR EL CLIENTE COMO EN LA IMPUTACION MANUAL PESTAÑA 1
        //$cuenta = $this->getCuentaBancariaAdif($request->request->get('id_cuenta')); //VERIFICAR LA CUENTA COMO EN LA IMPUTACION MANUAL PESTAÑA 1

        $totalR = 0;
        $ids_retenciones = json_decode($request->request->get('ids_retenciones', '[]'));
        $cant_retenciones = sizeof($ids_retenciones);
        $offsetNumeroAsiento = 0;
        $cobrosRetencion = array();
        $fecha_hoy = new DateTime();

        if (($cant_retenciones > 0) && ($saldo_comprobante <> 0)) {

            //ESTE RECIBO ES SOLO PARA RETENCION XQ ESTANDO EN TAB2 SOLO SE PUEDEN HABER SELECCIONADO BANCO Y CHEQUE A IMPUTAR 
            //A ESTOS (BANCO Y CHEQUE) SOLO SE LES DEBE PONER EL CLIENTE Y AGREGAR EL COMPROBANTE A SU RECIBO (INDIVIDUAL POR CADA RENGLON)

            $cobranzaService = $this->get('adif.cobranza_service');
            $numeroBaseRecibo = $cobranzaService->getSiguienteNumeroRecibo();

            $soloNC = false;
            $index = 0;
            while (($saldo_comprobante <> 0) && ($index < $cant_retenciones)) {
                $entity = $em->getRepository('ADIFContableBundle:Cobranza\RetencionCliente')->find($ids_retenciones[$index]);

                if ($comprobante_venta->tieneRetencionClienteImputada($entity->getTipoImpuesto())) {
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. El comprobante tiene imputado el tipo de retenci&oacute;n'));
                }

                //$fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $entity->getFecha()->format('Y-m-d') . ' ' . $fecha_hoy->format('H:i:s'));
                $fecha = $fecha_hoy;
                $entity->setFechaRegistro($fecha);
                $entity->setNumeroRecibo($numeroBaseRecibo + $index);

                $montoR = $entity->getMonto();
                if (!$entity->getImputada()) {
                    $reciboCobranza = new ReciboCobranza();
                    $reciboCobranza->setNumero($numeroBaseRecibo + $index);
                    $reciboCobranza->setFecha($fecha);
                    $reciboCobranza->addComprobante($comprobante_venta);
                    $reciboCobranza->addRenglonesRetencion($entity);
                    $reciboCobranza->setCodigoBarras($reciboCobranza->generarCodigoBarras());
                    $em->persist($reciboCobranza);

                    $cobroR = new CobroRetencionCliente();
                    $cobroR->setMonto($montoR);
                    $totalR += $montoR;

                    $cobroR->addRetencionesCliente($entity);
                    $entity->addCobrosRetencionCliente($cobroR);
                    $cobroR->setFecha($fecha);
                    $em->persist($cobroR);
                    $comprobante_venta->addCobro($cobroR);
                    $cobroR->addComprobante($comprobante_venta);
                    if ($montoR <= $comprobante_venta->getSaldo()) {
                        $comprobante_venta->setSaldo($comprobante_venta->getSaldo() - $montoR);
                    } else {
                        return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. Las retenciones de cliente deben imputarse por completo'));
                    }
                    $cobrosRetencion [] = $cobroR;
                } else {
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. La retenci&oacute;n ya fue imputada'));
                }

                $index += 1;
            }
        }

        $total_NC = 0;

        $ids_notas_credito = json_decode($request->request->get('ids_notas_credito', '[]'));
        $cant_notas_credito = sizeof($ids_notas_credito);
        $offsetNumeroAsiento = 0;

        $cobrosNotaCredito = array();

        if (($cant_notas_credito > 0) && ($saldo_comprobante <> 0)) {
            $index = 0;
            while (($saldo_comprobante <> 0) && ($index < $cant_notas_credito)) {
                $entity = $em->getRepository('ADIFContableBundle:Facturacion\NotaCreditoVenta')->find($ids_notas_credito[$index]);
                $fecha = $fecha_hoy;
                //$fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $entity->getFechaComprobante()->format('Y-m-d') . ' ' . $fecha_hoy->format('H:i:s'));
                $saldoNC = $entity->getSaldo();
                if ($saldoNC > 0) {
                    $cobroNC = new CobroNotaCreditoVenta();

                    if ($saldo_comprobante < $saldoNC) {
                        $diferencia = $saldoNC - $saldo_comprobante;
                        $entity->setSaldo($diferencia);
                        $cobroNC->setMonto($saldo_comprobante);
                        $saldo_comprobante = 0;
                    } else {
                        $saldo_comprobante = $saldo_comprobante - $saldoNC;
                        $cobroNC->setMonto($saldoNC);
                        $entity->setSaldo(0);
                    }
                    $cobroNC->addNotasCreditoVenta($entity);
                    $entity->addCobrosNotaCreditoVenta($cobroNC);
                    $cobroNC->setFecha($fecha);
                    $em->persist($cobroNC);
                    $comprobante_venta->addCobro($cobroNC);
                    $cobroNC->addComprobante($comprobante_venta);
                    $comprobante_venta->setSaldo($saldo_comprobante);

                    $cobrosNotaCredito[] = $cobroNC;

                    //Las NC no generan asientos contables                  
                } else {
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. La nota de cr&eacute;dito no tiene saldo'));
                }

                $index += 1;
            }
        }

        $total_A = 0;

        $ids_anticipos = json_decode($request->request->get('ids_anticipos', '[]'));

        $cant_anticipos = sizeof($ids_anticipos);
        $cobrosAnticipo = array();
        if (($cant_anticipos > 0) && ($saldo_comprobante <> 0)) {
            //$soloNC = false;
            $index = 0;

            while (($saldo_comprobante <> 0) && ($index < $cant_anticipos)) {

                $entity = $em->getRepository('ADIFContableBundle:Cobranza\AnticipoCliente')->find($ids_anticipos[$index]);
                //$fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $entity->getFecha()->format('Y-m-d') . ' ' . $fecha_hoy->format('H:i:s'));
                $fecha = $fecha_hoy;
                $saldoA = $entity->getSaldo();

                if ($saldoA > 0) {
                    $cobroA = new CobroAnticipoCliente();

                    if ($saldo_comprobante < $saldoA) {
                        $diferencia = $saldoA - $saldo_comprobante;
                        $entity->setSaldo($diferencia);
                        $cobroA->setMonto($saldo_comprobante);
                        $saldo_comprobante = 0;
                    } else {
                        $saldo_comprobante = $saldo_comprobante - $saldoA;
                        $cobroA->setMonto($saldoA);
                        $entity->setSaldo(0);
                    }

                    $cobroA->addAnticiposCliente($entity);
                    $entity->addCobrosAnticipoCliente($cobroA);
                    $cobroA->setFecha($fecha);

                    $em->persist($cobroA);

                    $comprobante_venta->addCobro($cobroA);
                    $cobroA->addComprobante($comprobante_venta);
                    $comprobante_venta->setSaldo($saldo_comprobante);

                    $cobrosAnticipo[] = $cobroA;
                } else {
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. El anticipo de cliente no tiene saldo'));
                }

                $index += 1;
            }
        }

        $total_C = 0;
        $total_cheques = 0;
        $ids_cobranzas_a_imputar = json_decode($request->request->get('ids_cobranzas_a_imputar', '[]'));
        $ids_cheques_a_imputar = json_decode($request->request->get('ids_cheques_a_imputar', '[]'));
        $cant_cobranzas_a_imputar = sizeof($ids_cobranzas_a_imputar);
        $cant_cheques_a_imputar = sizeof($ids_cheques_a_imputar);

        $cobrosRenglonCobranza = array();

        $idMovimientos = array();

        if (($cant_cobranzas_a_imputar > 0 || $cant_cheques_a_imputar > 0) && ($saldo_comprobante <> 0)) {
            $soloNC = false;
            //$cobranzaService = $this->get('adif.cobranza_service');
            //$numeroRecibo = $cobranzaService->getSiguienteNumeroRecibo();
            $cobroCRC_banco = new CobroRenglonCobranza();

            $index = 0;
            while (($saldo_comprobante > $total_C) && ($index < $cant_cobranzas_a_imputar)) {
                //for ($index = 0; $index < $cant_cobranzas_a_imputar; $index++) {
                $entity = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranzaBanco')->find($ids_cobranzas_a_imputar[$index]);
                //$fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $entity->getFechaRegistro()->format('Y-m-d') . ' ' . $fecha_hoy->format('H:i:s'));
//                $fecha = $fecha_hoy;
                $monto = $entity->getMonto();

                $reciboBanco = $entity->getReciboBanco();
                if ($reciboBanco) {
                    $reciboBanco->setCliente($cliente);
                    $reciboBanco->addComprobante($comprobante_venta);
                }

                if ($entity->getEstadoRenglonCobranza()->getDenominacion() == $estado_a_imputar && $monto > 0) {
                    $entity->setEstadoRenglonCobranza($estado);
                    //$entity->setNumeroRecibo($numeroRecibo);
                    //$entity->setFechaRegistro(new DateTime());
                    $total_C += $monto;
                    $cobroCRC_banco->addRenglonesCobranza($entity);

                    $idMovimientos[] = $entity->getId();
                }

                $index += 1;
            }
            $cobroCRC_banco->setFecha($fecha);

            if ($saldo_comprobante < $total_C) { //generar anticipo x la diferencia
                $diferencia = $total_C - $saldo_comprobante;
                $anticipo_cliente = new AnticipoCliente();
                $anticipo_cliente->setCliente($comprobante_venta->getCliente());
                $anticipo_cliente->setMonto($diferencia);
                $anticipo_cliente->setSaldo($diferencia);
                $anticipo_cliente->setCobroRenglonCobranza($cobroCRC_banco);
                $cobroCRC_banco->setAnticipoCliente($anticipo_cliente);
                $em->persist($anticipo_cliente);
                $cobroCRC_banco->setMonto($saldo_comprobante);
                $saldo_comprobante = 0;
            } else {
                $saldo_comprobante -= $total_C;
                $cobroCRC_banco->setMonto($total_C);
                $em->persist($cobroCRC_banco);
            }
            $comprobante_venta->setSaldo($saldo_comprobante);

            if ($comprobante_venta->getSaldo() == 0) {
                $comprobante_venta->setEstadoComprobante($estadoComprobanteCancelado);
            }

            $comprobante_venta->addCobro($cobroCRC_banco);
            $cobroCRC_banco->addComprobante($comprobante_venta);
            $cobrosRenglonCobranza[] = $cobroCRC_banco;

            if ($saldo_comprobante > 0) {

                $cobroCRC_cheque = new CobroRenglonCobranza();

                $index = 0;
                while (($saldo_comprobante > $total_cheques) && ($index < $cant_cheques_a_imputar)) {
                    $entity = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranzaCheque')->find($ids_cheques_a_imputar[$index]);
                    //$fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $entity->getFechaRegistro()->format('Y-m-d') . ' ' . $fecha_hoy->format('H:i:s'));
                    $fecha = $fecha_hoy;
                    $monto = $entity->getMonto();

                    $reciboCheque = $entity->getReciboCheque();
                    if ($reciboCheque) {
                        $reciboCheque->setCliente($cliente);
                        $reciboCheque->addComprobante($comprobante_venta);
                    }

                    if ($entity->getEstadoRenglonCobranza()->getDenominacion() == $estado_a_imputar && $monto > 0) {
                        $entity->setEstadoRenglonCobranza($estado);
                        //$entity->setNumeroRecibo($numeroRecibo);
                        //$entity->setFechaRegistro(new DateTime());
                        $total_cheques += $monto;
                        $cobroCRC_cheque->addCheques($entity);

                        $idMovimientos[] = $entity->getId();
                    }

                    $index += 1;
                }
                $cobroCRC_cheque->setFecha($fecha);

                if ($saldo_comprobante < $total_cheques) { //generar anticipo x la diferencia
                    $diferencia = $total_cheques - $saldo_comprobante;
                    $anticipo_cliente = new AnticipoCliente();
                    $anticipo_cliente->setCliente($comprobante_venta->getCliente());
                    $anticipo_cliente->setMonto($diferencia);
                    $anticipo_cliente->setSaldo($diferencia);
                    $anticipo_cliente->setCobroRenglonCobranza($cobroCRC_cheque);
                    $cobroCRC_cheque->setAnticipoCliente($anticipo_cliente);
                    $em->persist($anticipo_cliente);
                    $cobroCRC_cheque->setMonto($saldo_comprobante);
                    $cobroCRC_cheque->setMontoCheques($saldo_comprobante);
                    $saldo_comprobante = 0;
                } else {
                    $saldo_comprobante -= $total_cheques;
                    $cobroCRC_cheque->setMonto($total_cheques);
                    $cobroCRC_cheque->setMontoCheques($total_cheques);
                    $em->persist($cobroCRC_cheque);
                }
                $comprobante_venta->setSaldo($saldo_comprobante);

                if ($comprobante_venta->getSaldo() == 0) {
                    $comprobante_venta->setEstadoComprobante($estadoComprobanteCancelado);
                }

                $comprobante_venta->addCobro($cobroCRC_cheque);
                $cobroCRC_cheque->addComprobante($comprobante_venta);

                $cobrosRenglonCobranza[] = $cobroCRC_cheque;
            }
        }
        if (!$soloNC) {
            $resultArray = $this->get('adif.asiento_service')->
                    generarAsientosContablesParaImputaciones($fecha, $cobrosRetencion, array(), $cobrosRenglonCobranza, $this->getUser(), false, false);
            //generarAsientosContablesParaImputaciones($cobrosRetencion, $cobrosAnticipo, $cobrosRenglonCobranza, $this->getUser(), false, false);
            // Si el asiento presupuestario falló
            if ($resultArray['mensajeErrorPresupuestario'] != null) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario']));
            }

            // Si el asiento contable falló
            if ($resultArray['mensajeErrorContable'] != null) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable']));
            }
            // Si no hubo errores en los asientos
            if ($resultArray['numeroAsiento'] != -1) {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {
                    $em->flush();

                    $em->getConnection()->commit();

                    $idCobros = [];

                    foreach ($cobrosRenglonCobranza as $cobroRenglonCobranza) {
                        $idCobros[] = $cobroRenglonCobranza->getId();
                    }

//                    foreach ($cobrosAnticipo as $cobroAnticipo) {
//                        $idCobros[] = $cobroAnticipo->getId();
//                    }

//                    foreach ($cobrosNotaCredito as $cobroNotaCredito) {
//                        $idCobros[] = $cobroNotaCredito->getId();
//                    }

                    foreach ($cobrosRetencion as $cobroRetencion) {
                        $idCobros[] = $cobroRetencion->getId();
                    }

                    if ($tab == self::ID_TAB_1) {
                        $dataArray = [
                            'data-id-cobros' => implode(',', $idCobros),
                            'data-id-movimientos' => implode(',', $idMovimientos)
                        ];
                    } else {
                        $dataArray = [
                            'data-id-cobros' => implode(',', $idCobros)
                        ];
                    }

                    $mensajeFlash = $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], $dataArray, true);

                    return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito', 'mensajeAsiento' => $mensajeFlash));
                } catch (\Exception $e) {

                    $em->getConnection()->rollback();

                    $em->close();

                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Ocurri&oacute; un error. Por favor intente realizar la operaci&oacute;n nuevamente', 'mensajeAsiento' => ''));
                }
            }
        } else {
            $em->flush();
        }

        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito'));
    }

    /**
     * @Route("/imputar_manualmente_1a1/", name="rengloncobranza_imputar_manualmente_1a1")
     * @Method("POST")
     */
    public function imputarManualmente1a1Action(Request $request) {

        //Agregar verificación de cuenta x las dudas y descomentar la validacion del cliente
        $id_pago = $request->request->get('id_pago');
        $tipo_pago = $request->request->get('tipo_pago');
        $tab = $request->request->get('tab');
        $id_comprobante = $request->request->get('id_comprobante');
//        \Doctrine\Common\Util\Debug::dump( "imputarManualmente1a1Action" ); exit;

        $resultArray = [
            'numeroAsiento' => -1,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $estadoComprobanteCancelado = $em->getRepository('ADIFContableBundle:EstadoComprobante')
                ->find(EstadoComprobante::__ESTADO_CANCELADO);
        $estadoComprobanteAnulado = $em->getRepository('ADIFContableBundle:EstadoComprobante')->find(EstadoComprobante::__ESTADO_ANULADO);
        $estado_imputado = ConstanteEstadoRenglonCobranza::ESTADO_IMPUTADO;
        //$estado_pendiente = ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE;
        $estado = $em->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')->findOneByDenominacion($estado_imputado);

        /**
         * Tipo de pago 1: Banco
         * Tipo de pago 2: Notas de credito
         * Tipo de pago 3: Anticipos
         * Tipo de pago 4: Cheques
         * Tipo de pago 5: Retenciones
         */
        $pago = null;
        $a_favor = null;
        switch ($tipo_pago) {
            case 1:
                $pago = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranzaBanco')->find($id_pago);
                $a_favor = $pago->getMonto();
                if ($pago->getEstadoRenglonCobranza()->getDenominacion() == $estado_imputado) {
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. El cobro seleccionado ya fue imputado'));
                }
//                $cobranzaService = $this->get('adif.cobranza_service');
//                $numeroRecibo = $cobranzaService->getSiguienteNumeroRecibo();
                break;
            case 2:
                $pago = $em->getRepository('ADIFContableBundle:Facturacion\NotaCreditoVenta')->find($id_pago);
                if (!$pago) {
                    // Si no encuentra la nota de credito, me fijo si es un cupon negativo
                    $pago = $em->getRepository('ADIFContableBundle:Facturacion\CuponVenta')->find($id_pago);
                    if (!$pago) {
                        return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. No se puede encontrar un crédito contra que imputar.'));
                    }
                    $a_favor = abs($pago->getSaldo());
                } else {
                    $a_favor = $pago->getSaldo();
                }
                
                if ($a_favor == 0) {
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. La nota de crédito seleccionada no tiene más saldo'));
                }
                break;
            case 3:
                $pago = $em->getRepository('ADIFContableBundle:Cobranza\AnticipoCliente')->find($id_pago);
                $a_favor = $pago->getSaldo();
                if ($a_favor == 0) {
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. El anticipo seleccionado no tiene más saldo'));
                }
                break;
            case 4:
                $pago = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranzaCheque')->find($id_pago);
                $a_favor = $pago->getMonto();
                if ($pago->getEstadoRenglonCobranza()->getDenominacion() == $estado_imputado) {
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. El cobro seleccionado ya fue imputado'));
                }
//                $cobranzaService = $this->get('adif.cobranza_service');
//                $numeroRecibo = $cobranzaService->getSiguienteNumeroRecibo();
                break;
            case 5:
                $pago = $em->getRepository('ADIFContableBundle:Cobranza\RetencionCliente')->find($id_pago);
                $a_favor = $pago->getMonto();
                if ($pago->getImputada()) {
                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. La retenci&oacute;n ya fue imputada'));
                }
                break;
        }

        $fecha_hoy = new DateTime();
        $fecha_pago = $tipo_pago == 2 ? $pago->getFechaComprobante() : $pago->getFecha();

        if (($tab == self::ID_TAB_1) && ($tipo_pago == 1 || $tipo_pago == 4)) {//|| ($tipo_pago == 5)) {
            $fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $fecha_pago->format('Y-m-d') . ' ' . $fecha_hoy->format('H:i:s'));
        } else {
            $fecha = $fecha_hoy;
        }

        $comprobante_venta = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')->find($id_comprobante);
        if ($comprobante_venta->getEstadoComprobante() == $estadoComprobanteAnulado) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. El comprobante fue anulado, por favor actualice el listado'));
        }
        $clienteComp = $comprobante_venta->getCliente();
        $deuda = $comprobante_venta->getSaldo();
        if ($deuda == 0) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. El comprobante no tiene saldo para cancelar'));
        }

        if ($tipo_pago == 5 && $comprobante_venta->tieneRetencionClienteImputada($pago->getTipoImpuesto())) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Se produjo un error. Hay al menos un comprobante que tiene el mismo tipo de retención ya imputada'));
        }

        switch ($tipo_pago) {
            case 1:
                $cobro = new CobroRenglonCobranza();
                $pago->setEstadoRenglonCobranza($estado);
                $cobro->addRenglonesCobranza($pago);

                //$pago->setNumeroRecibo($numeroRecibo);
                //$pago->setFechaRegistro(new DateTime());
                break;
            case 2:
                if (!$pago->getEsCupon()) {
                    $cobro = new CobroNotaCreditoVenta();
                    $cobro->addNotasCreditoVenta($pago);
                } else {
                    // Es cupon "credito" negativo, que son los que se van a migrar a la AABE
                    $cobro = new CobroCuponCredito();
                    $cobro->addCuponesCreditoVenta($pago);
                }
                break;
            case 3:
                $cobro = new CobroAnticipoCliente();
                $cobro->addAnticiposCliente($pago);
                break;
            case 4:
                $cobro = new CobroRenglonCobranza();
                $pago->setEstadoRenglonCobranza($estado);
                $cobro->addCheques($pago);

                //$pago->setNumeroRecibo($numeroRecibo);
                //$pago->setFechaRegistro(new DateTime());
                break;
            case 5:
                $cobro = new CobroRetencionCliente();
                $cobro->addRetencionesCliente($pago);

                $pago->addCobrosRetencionCliente($cobro);
                break;
        }

        if ((($tab == self::ID_TAB_1) && ($tipo_pago == 1 || $tipo_pago == 4)) || ($tipo_pago == 5)) {
            $cobranzaService = $this->get('adif.cobranza_service');
            $numeroRecibo = $cobranzaService->getSiguienteNumeroRecibo();

            $reciboCobranza = new ReciboCobranza();
            $reciboCobranza->setNumero($numeroRecibo);
            $reciboCobranza->setFecha($fecha);
            $reciboCobranza->setCodigoBarras($reciboCobranza->generarCodigoBarras());

            switch ($tipo_pago) {
                case 1:
                    $reciboCobranza->addRenglonesBanco($pago);
                    break;
                case 4:
                    $reciboCobranza->addRenglonesCheque($pago);
                    break;
                case 5:
                    $reciboCobranza->addRenglonesRetencion($pago);
                    break;
            }

            $pago->setNumeroRecibo($numeroRecibo);
            //if ($tipo_pago != 5)
            $pago->setFechaRegistro($fecha);
        } else {
            if (($tipo_pago == 1 || $tipo_pago == 4) && ($tab == self::ID_TAB_2)) {
                switch ($tipo_pago) {
                    case 1:
                        $reciboCobranza = $pago->getReciboBanco();
                        break;
                    case 4:
                        $reciboCobranza = $pago->getReciboCheque();
                        break;
                    case 5:
                        $reciboCobranza = $pago->getReciboRetencion();
                        break;
                }
            }
        }

        if ($tipo_pago != 2 && $tipo_pago != 3 && $reciboCobranza) {
            $reciboCobranza->setCliente($clienteComp);
            $reciboCobranza->addComprobante($comprobante_venta);
        }

        $cobro->setFecha($fecha);
        $cobro->setMonto(0);

        $em->persist($cobro);

        $comprobante_venta->addCobro($cobro);
        $cobro->addComprobante($comprobante_venta);

        if ($deuda < $a_favor && $tipo_pago != 5) { //GENERA UN ANTICIPO
            $comprobante_venta->setSaldo(0);

            if ($tipo_pago == 1 || $tipo_pago == 4) { //banco o cheque
                $anticipo_cliente = new AnticipoCliente();
                $anticipo_cliente->setCliente($clienteComp);
                $anticipo_cliente->setMonto($a_favor - $deuda); //(($saldo_total - $a_favor) * (-1));
                $anticipo_cliente->setSaldo($a_favor - $deuda);
                $anticipo_cliente->setCobroRenglonCobranza($cobro);
                $cobro->setAnticipoCliente($anticipo_cliente);

                $em->persist($anticipo_cliente);

                //$cobro->setMonto($deuda);
                $cobro->setMontoCheques($tipo_pago == 4 ? $deuda : 0);
            } else { //2->NC y 3->Anticipo
                $pago->setSaldo($a_favor - $deuda);
            }

            $cobro->setMonto($deuda);
        } else {

            $comprobante_venta->setSaldo($deuda - $a_favor);

            if ($tipo_pago != 1 && $tipo_pago != 4 && $tipo_pago != 5) {
                $pago->setSaldo(0);
            } else {
                if ($tipo_pago == 4) {
                    $cobro->setMontoCheques($a_favor);
                }
            }
            $cobro->setMonto($a_favor);

            $em->persist($cobro);
        }

        if ($comprobante_venta->getSaldo() == 0) {
            $comprobante_venta->setEstadoComprobante($estadoComprobanteCancelado);
        }

        if ($tipo_pago != 2 && $tipo_pago != 3) { //La forma de pago es a través de una cobranza o un anticipo o un cheque o retención
            $usuario = $this->getUser();

            if ($tipo_pago == 1 || $tipo_pago == 4) { // el pago es con una cobranza o un cheque
                if ($tab == self::ID_TAB_1) {

					// Persisto los asientos contables y presupuestarios
					if ($tipo_pago == 1) {
						// Cobro Banco y Cobro ONABE	
						$resultArray = $this->get('adif.asiento_service')->
                            generarAsientosContablesParaCobranzaImputada($fecha, $cobro, $usuario, true, false, 0, $pago->getEsOnabe());
					} else {
						$resultArray = $this->get('adif.asiento_service')->
                            generarAsientosContablesParaCobranzaImputada($fecha, $cobro, $usuario, true, false, 0);
					}
                    
                }
                // Vengo de '#tab_2'
                else {

                    // Persisto los asientos contables y presupuestarios
                    $resultArray = $this->get('adif.asiento_service')->
                            generarAsientosContablesParaCobranzaImputada($fecha, $cobro, $usuario, false, false, 0);
                }
            }
            // El pago es con un anticipo o con retención
            else {
//                if ($tipo_pago == 3) {
//                    // Persisto los asientos contables y presupuestarios
//                    $resultArray = $this->get('adif.asiento_service')
//                            ->generarAsientosContablesParaCobranzaImputadaConAnticipo($cobro, $usuario, false);
//                }
//                // Persisto los asientos contables y presupuestarios
//                else {
                $resultArray = $this->get('adif.asiento_service')
                        ->generarAsientosContablesParaRetencion($fecha, $cobro, $usuario, false);
//                }
            }

            // Si el asiento presupuestario falló
            if ($resultArray['mensajeErrorPresupuestario'] != null) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario']));
            }

            // Si el asiento contable falló
            if ($resultArray['mensajeErrorContable'] != null) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable']));
            }

            // Si no hubo errores en los asientos
            if ($resultArray['numeroAsiento'] != -1) {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {
                    $em->flush();

                    $em->getConnection()->commit();

                    if ($tab == self::ID_TAB_1 && ($tipo_pago == 1 || $tipo_pago == 4)) {
                        $dataArray = [
                            'data-id-cobros' => $cobro->getId(),
                            'data-id-movimientos' => $id_pago
                        ];
                    } else {
                        $dataArray = [
                            'data-id-cobros' => $cobro->getId()
                        ];
                    }

                    $mensajeFlash = $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], $dataArray, true);

                    return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito', 'mensajeAsiento' => $mensajeFlash));
                } catch (\Exception $e) {

                    $em->getConnection()->rollback();

                    $em->close();

                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Ocurri&oacute; un error. Por favor intente realizar la operaci&oacute;n nuevamente', 'mensajeAsiento' => ''));
                }
            }
        } else {
            $em->flush();
        }

        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito'));
    }

    /**
     * Armar el detalle de los cobros de un comprobante
     *
     * @Route("/index_table_cobros_por_comprobante/", name="index_table_cobros_por_comprobante")
     * @Method("GET|POST")
     */
    public function indexTableCobrosPorComprobanteAction(Request $request) {
        $id_comprobante = $request->query->get('id_comprobante');
        $entities = array();
        if ($id_comprobante != null) {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $comprobanteVenta = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')
                    ->find($id_comprobante);
            

            if (!$comprobanteVenta->getEsMigracionAabe()) {
           
                $repository = $em->getRepository('ADIFContableBundle:Cobranza\Cobro');
                $query = $repository->createQueryBuilder('cobro')
                        ->innerJoin('cobro.comprobantes', 'comprobante')
                        ->where('comprobante.id = ' . $id_comprobante)
                        ->andWhere('cobro.monto > 0')
                        ->orderBy('cobro.fecha', 'ASC')
                        ->getQuery();
                $entities = $query->getResult();
                
            } else {

                if ($comprobanteVenta->getEsCupon()) {
                    // Es migracion AABE y es cupon
                    $entities = $comprobanteVenta->getCobrosCuponesCredito();
                }
            }
        } 

        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_cobros_por_comprobante.html.twig', array('entities' => $entities)
        );
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/desimputar_cobro/", name="rengloncobranza_desimputar_cobro")
     * @Method("GET|POST")
     */
    public function desimputarCobroAction(Request $request) {
        $id_cobro = $request->request->get('id_cobro');
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $cobro = $em->getRepository('ADIFContableBundle:Cobranza\Cobro')->find($id_cobro);

        //$em = $this->getDoctrine()->getManager($this->getEntityManager());
        $estadoComprobanteIngresado = $em->getRepository('ADIFContableBundle:EstadoComprobante')
                ->find(EstadoComprobante::__ESTADO_INGRESADO);

        if (!$cobro) {
            return new JsonResponse(array(
                'status' => 'ERROR',
                'message' => 'El cobro no se encuentra'));
        }

        if ($cobro->tipo() != 'CobroRetencionCliente') {
            $comprobante = $cobro->getComprobantes()[0];
            $nuevo_saldo = $comprobante->getSaldo() + $cobro->getMonto();
        }

        $esNC = false; // es nota de credito o anticipo de cliente, que son los que no generar asientos
        switch ($cobro->tipo()) {
            case 'CobroNotaCreditoVenta':
            case 'CobroCuponCredito':
                $msj = $cobro->desimputar();
                $esNC = true;
                break;
            case 'CobroRenglonCobranza':
//                $msj = $cobro->desimputar();
//                if ($msj != '') {
//                    return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; la operaci&oacute;n. ' . $msj));
//                }
                $resultArray = $this->desimputarCobroRenglonCobranza($cobro, $em);
                $esNC = true;
                break;
            case 'CobroAnticipoCliente':
                $msj = $cobro->desimputar();
                $esNC = true;
//                if ($msj == '') {
//                    // Persisto los asientos contables y presupuestarios
//                    $resultArray = $this->get('adif.asiento_service')
//                            ->generarAsientosContablesParaCobranzaImputadaConAnticipo($cobro, $this->getUser(), true);
//                }
                break;
            case 'CobroRetencionCliente':
                $resultArray = $this->desimputarCobroRetencionCliente($cobro);
                break;
        }
        if ($cobro->tipo() != 'CobroRetencionCliente') {
            $comprobante->setSaldo($nuevo_saldo);

            $comprobante->setEstadoComprobante($estadoComprobanteIngresado);

            $comprobante->removeCobro($cobro);
            $cobro->removeComprobante($comprobante);

            if ($cobro->tipo() != 'CobroRenglonCobranza' || $this->verificar_caso($cobro)) {
                $em->remove($cobro);
            }
        }

        if (!$esNC) { //La forma de pago es a través de una cobranza o un cheque
            // Si el asiento presupuestario falló
            if (is_a($resultArray, 'Symfony\Component\HttpFoundation\JsonResponse')) {
                $resultArray = json_decode($resultArray->getContent(),true);
                $resultArray['mensajeErrorPresupuestario'] = $resultArray['message'];
            }
            if ($resultArray['mensajeErrorPresupuestario'] != null) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario']));
            }

            // Si el asiento contable falló
            if ($resultArray['mensajeErrorContable'] != null) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable']));
            }

            // Si no hubo errores en los asientos
            if ($resultArray['numeroAsiento'] != -1) {
                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {

                    $idAnticipo = null;

                    if ($cobro->tipo() == 'CobroRenglonCobranza' && $cobro->getAnticipoCliente() != null) {
                        $idAnticipo = $cobro->getAnticipoCliente()->getId();
                    }

                    $em->flush();

                    $em->getConnection()->commit();

                    $dataArray = array();

                    if ($idAnticipo == null) {
                        $dataArray = [
                            'data-id-cobros' => $id_cobro
                        ];
                    }

                    $mensajeFlash = $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], $dataArray, true);

                    return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito', 'mensajeAsiento' => $mensajeFlash));
                } catch (\Exception $e) {

                    $em->getConnection()->rollback();

                    //$em->close();

                    throw $e;
                }
            }
        } else {
            $em->flush();
        }

        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito'));
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/crear_anticipo/", name="rengloncobranza_crear_anticipo")
     * @Method("GET|POST")
     */
    public function crearAnticipoAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $tab = $request->request->get('tab');

        $estado_imputado = ConstanteEstadoRenglonCobranza::ESTADO_IMPUTADO;
        $estado_pendiente = ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE;
        $estado_a_imputar = ConstanteEstadoRenglonCobranza::ESTADO_A_IMPUTAR;

        $estado_esperado = $tab == self::ID_TAB_1 ? $estado_pendiente : $estado_a_imputar;

        $estado = $em->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')->findOneByDenominacion($estado_imputado);

        $idsMovimientos = json_decode($request->request->get('ids', '[]'));
        $idsCheques = json_decode($request->request->get('ids_cheques', '[]'));
        $idCliente = json_decode($request->request->get('id_cliente'));

        $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->findOneById($idCliente);

        if (!$cliente) {
            return new JsonResponse(array(
                'status' => 'ERROR',
                'message' => 'No se puede encontrar el cliente'));
        }

        $fecha_hoy = new DateTime();

        if ($tab == self::ID_TAB_1) {

            $cobranzaService = $this->get('adif.cobranza_service');
            $numeroRecibo = $cobranzaService->getSiguienteNumeroRecibo();

            $reciboCobranza = new ReciboCobranza();
            $reciboCobranza->setNumero($numeroRecibo);
            $reciboCobranza->setCodigoBarras($reciboCobranza->generarCodigoBarras());
            //$reciboCobranza->setFecha(new DateTime());
        }

        $monto_total = 0;
        $cobro_renglon_cobranza = new CobroRenglonCobranza();


        foreach ($idsMovimientos as $idMovimiento) {
            $movimiento = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranzaBanco')->find($idMovimiento);

            if ($tab == self::ID_TAB_1) {
                $fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $movimiento->getFecha()->format('Y-m-d') . ' ' . $fecha_hoy->format('H:i:s'));
                $movimiento->setNumeroRecibo($numeroRecibo);
                $movimiento->setFechaRegistro($fecha);

                $reciboCobranza->setCliente($cliente);
                $reciboCobranza->addRenglonesBanco($movimiento);
            } else {
                $fecha = $fecha_hoy;
                $reciboCobranza = $movimiento->getReciboBanco();
                if ($reciboCobranza) {
                    $reciboCobranza->setCliente($cliente);
                }
            }

            if (!$movimiento) {
                //throw $this->createNotFoundException('No se puede encontrar el RenglonConciliacion.');
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'El movimiento no se encuentra'));
            }
            if ($movimiento->getEstadoRenglonCobranza()->getDenominacion() != $estado_esperado) {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'El movimiento no est&aacute; en el estado correspondiente'));
            }

            $monto_total += $movimiento->getMonto();


            $movimiento->setEstadoRenglonCobranza($estado);
            $cobro_renglon_cobranza->addRenglonesCobranza($movimiento);
        }


        $monto_cheques = 0;

        foreach ($idsCheques as $idCheque) {
            $cheque = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranzaCheque')->find($idCheque);

            if ($tab == self::ID_TAB_1) {
                $fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $cheque->getFecha()->format('Y-m-d') . ' ' . $fecha_hoy->format('H:i:s'));

                $cheque->setNumeroRecibo($numeroRecibo);
                $cheque->setFechaRegistro($fecha);

                $reciboCobranza->setCliente($cliente);
                $reciboCobranza->addRenglonesCheque($cheque);
            } else {
                $fecha = $fecha_hoy;
                $reciboCobranza = $movimiento->getReciboCheque();
                if ($reciboCobranza) {
                    $reciboCobranza->setCliente($cliente);
                }
            }

            if (!$cheque) {
                //throw $this->createNotFoundException('No se puede encontrar el RenglonConciliacion.');
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'El movimiento no se encuentra'));
            }
            if ($cheque->getEstadoRenglonCobranza()->getDenominacion() != $estado_esperado) {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'El movimiento no est&aacute; en el estado correspondiente'));
            }

            $monto_cheques += $cheque->getMonto();


            $cheque->setEstadoRenglonCobranza($estado);
            $cobro_renglon_cobranza->addCheques($cheque);
        }

        $cobro_renglon_cobranza->setFecha($fecha);
        if ($tab == self::ID_TAB_1) {
            $reciboCobranza->setFecha($fecha);
        }

        $cobro_renglon_cobranza->setMonto(0);
        $cobro_renglon_cobranza->setMontoCheques($monto_cheques);
        $anticipo_cliente = new AnticipoCliente();
        $anticipo_cliente->setCliente($cliente);
        $anticipo_cliente->setMonto($monto_total + $monto_cheques);
        $anticipo_cliente->setSaldo($monto_total + $monto_cheques);
        $anticipo_cliente->setCobroRenglonCobranza($cobro_renglon_cobranza);
        $cobro_renglon_cobranza->setAnticipoCliente($anticipo_cliente);

        $em->persist($anticipo_cliente);

        // Persisto los asientos contables y presupuestarios
        $resultArray = $this->get('adif.asiento_service')
                ->generarAsientosContablesParaAnticipoCreado($fecha, $cobro_renglon_cobranza, $this->getUser(), $tab == self::ID_TAB_1, false);

        // Si el asiento presupuestario falló
        if ($resultArray['mensajeErrorPresupuestario'] != null) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario']));
        }

        // Si el asiento contable falló
        if ($resultArray['mensajeErrorContable'] != null) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable']));
        }



        // Si no hubo errores en los asientos
        if ($resultArray['numeroAsiento'] != -1) {

            // Comienzo la transaccion
            $em->getConnection()->beginTransaction();

            try {
                $em->flush();

                $em->getConnection()->commit();

                if ($tab == self::ID_TAB_1) {
                    $dataArray = [
                        'data-id-cobros' => $cobro_renglon_cobranza->getId(),
                        'data-id-movimientos' => implode(',', array_merge($idsCheques, $idsMovimientos))
                    ];
                } else {
                    $dataArray = [
                        'data-id-cobros' => $cobro_renglon_cobranza->getId()
                    ];
                }

                $mensajeFlash = $this->get('adif.asiento_service')
                        ->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], $dataArray, true);
                //\Doctrine\Common\Util\Debug::dump($mensajeFlash);die;

                return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito', 'mensajeAsiento' => $mensajeFlash));
            } catch (\Exception $e) {

                $em->getConnection()->rollback();

                //$em->close();

                throw $e;
            }
        }

        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito'));
    }

    /**
     * Anula el anticipo de cliente si no posee cobros
     *
     * @Route("/deshacer_anticipo/", name="deshacer_anticipo")
     * @Method("GET|POST")
     */
    public function deshacerAnticipoAction(Request $request) {
        $id = $request->request->get('id_anticipo');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $estado_imputado = ConstanteEstadoRenglonCobranza::ESTADO_IMPUTADO;
        //$estado_pendiente = ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE;
        $estado_a_imputar = ConstanteEstadoRenglonCobranza::ESTADO_A_IMPUTAR;

        $estado = $em->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')
                ->findOneByDenominacion($estado_a_imputar);

        $anticipo = $em->getRepository('ADIFContableBundle:Cobranza\AnticipoCliente')->find($id);

        if (!$anticipo) {
            throw $this->createNotFoundException('No se puede encontrar la entidad AnticipoCliente.');
        }

        $cobrosAnticipo = $anticipo->getCobrosAnticipoCliente();

        //se podría verificar que sea manual el anticipo ($anticipo->getCobroRenglonCobranza()->getMonto() == 0)
        if (($cobrosAnticipo != null) && (sizeOf($cobrosAnticipo) > 0)) {
            $detalle = '<ul>';

            foreach ($cobrosAnticipo as $cobro) {
                $comprobante = $cobro->getComprobantes()[0];
                $letra = $comprobante->getLetraComprobante();
                $detalle_aux = $comprobante->getTipoComprobante() . ($letra ? ' (' . $letra . ')' : '') . ' N° ' . $comprobante->getNumeroCompleto();
                $detalle .= '<li>' . $detalle_aux . '</li>';
            }

            $detalle .= '</ul>';

            return new JsonResponse(array(
                'status' => 'ERROR',
                'message' => 'No se puede deshacer el anticipo porque fue utilizado para cancelar el saldo de otros comprobantes: <br/>' . $detalle));
        } else {

            $cobranzaService = $this->get('adif.cobranza_service');
            $numeroBaseRecibo = $cobranzaService->getSiguienteNumeroRecibo();
            $indice = 0;

            $cobro_renglon_cobranza = $anticipo->getCobroRenglonCobranza();

            $fecha_hoy = new DateTime();
            $fecha_pago = $cobro_renglon_cobranza->getRenglonesCobranza()->first()->getFecha();
            $fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $fecha_pago->format('Y-m-d') . ' ' . $fecha_hoy->format('H:i:s'));

            // Persisto los asientos contables y presupuestarios
            $resultArray = $this->get('adif.asiento_service')->generarAsientosContablesParaAnticipoCreado($fecha_hoy, $cobro_renglon_cobranza, $this->getUser(), true, true); //el primer true solo se usa al crear anticipo no al desimputar

            foreach ($cobro_renglon_cobranza->getRenglonesCobranza() as $renglon_cobranza) {

                if ($renglon_cobranza->getEstadoRenglonCobranza()->getDenominacion() != $estado_imputado) {
                    return new JsonResponse(array(
                        'status' => 'ERROR',
                        'message' => 'El movimiento no est&aacute; imputado, tiene otro estado'));
                }
                //$renglon_cobranza->setNumeroRecibo(null);

                $renglon_cobranza->setEstadoRenglonCobranza($estado);
                $cobro_renglon_cobranza->removeRenglonesCobranza($renglon_cobranza);

                $reciboCobranza = $renglon_cobranza->getReciboBanco();
                if ($reciboCobranza) {
                    $reciboCobranza->removeRenglonesBanco($renglon_cobranza);
                }

                //CREARLE UN RECIBO INDIVIDUAL COMO COBRANZA A IMPUTAR
                $nuevoReciboCobranza = new ReciboCobranza();
                $nuevoReciboCobranza->setNumero($numeroBaseRecibo + $indice);
                //$nuevoReciboCobranza->setFecha($reciboCobranza->getFecha());
                $nuevoReciboCobranza->setFecha($renglon_cobranza->getFechaRegistro());
                $nuevoReciboCobranza->addRenglonesBanco($renglon_cobranza);
                $nuevoReciboCobranza->setCodigoBarras($nuevoReciboCobranza->generarCodigoBarras());

                $em->persist($nuevoReciboCobranza);

                $indice++;
            }

            foreach ($cobro_renglon_cobranza->getCheques() as $cheque) {

                if ($cheque->getEstadoRenglonCobranza()->getDenominacion() != $estado_imputado) {
                    return new JsonResponse(array(
                        'status' => 'ERROR',
                        'message' => 'El movimiento no est&aacute; imputado, tiene otro estado'));
                }
                //$cheque->setNumeroRecibo(null);
                $cheque->setEstadoRenglonCobranza($estado);
                $cobro_renglon_cobranza->removeCheques($cheque);

                $reciboCobranza = $cheque->getReciboBanco();
                if ($reciboCobranza) {
                    $reciboCobranza->removeRenglonesCheque($cheque);
                }

                //CREARLE UN RECIBO INDIVIDUAL COMO COBRANZA A IMPUTAR               
                $nuevoReciboCobranza = new ReciboCobranza();
                $nuevoReciboCobranza->setNumero($numeroBaseRecibo + $indice);
                //$nuevoReciboCobranza->setFecha($reciboCobranza->getFecha());
                $nuevoReciboCobranza->setFecha($cheque->getFechaRegistro());
                $nuevoReciboCobranza->addRenglonesBanco($cheque);
                $nuevoReciboCobranza->setCodigoBarras($nuevoReciboCobranza->generarCodigoBarras());

                $em->persist($nuevoReciboCobranza);

                $indice++;
            }

            $em->remove($anticipo);
            $em->remove($cobro_renglon_cobranza);


            // Si el asiento presupuestario falló
            if ($resultArray['mensajeErrorPresupuestario'] != null) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario']));
            }

            // Si el asiento contable falló
            if ($resultArray['mensajeErrorContable'] != null) {
                return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable']));
            }

            // Si no hubo errores en los asientos
            if ($resultArray['numeroAsiento'] != -1) {

                // Comienzo la transaccion
                $em->getConnection()->beginTransaction();

                try {
                    $em->flush();

                    $em->getConnection()->commit();

                    $mensajeFlash = $this->get('adif.asiento_service')
                            ->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], array(), true);

                    return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito', 'mensajeAsiento' => $mensajeFlash));
                } catch (\Exception $e) {

                    $em->getConnection()->rollback();
                    $em->close();

                    throw $e;
                }
            }

            return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito'));
        }
    }

    /**
     * Devuelve un array de los conceptos que puede tener un RenglonConciliacion
     * 
     */
    private function getClientesDisponibles($clientes) {
        $clientesArray = [];
        foreach ($clientes as $cliente) {
            $clientesArray[$cliente->getId()] = $cliente->getClienteProveedor()->getRazonSocial() . ' ~ ' . $cliente->getClienteProveedor()->getCuit();
        }
        return $clientesArray;
    }

    /**
     * @Route("/anticipos_detalle/", name="anticipos_detalle")
     * @Method("GET|POST")
     */
    public function anticiposDetalleAction(Request $request) {
        //$id_cuenta = $request->query->get('id_cuenta');
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $repository = $em->getRepository('ADIFContableBundle:Cobranza\AnticipoCliente');
        $query = $repository->createQueryBuilder('a')
                ->innerJoin('a.cobroRenglonCobranza', 'c')
                //->innerJoin('c.renglonesCobranza', 'r')
                ->where('c.monto = 0') //son los generados en forma manual                          
                //->andWhere('r.idCuentaBancaria = ' . $id_cuenta)
                ->orderBy('c.fecha', 'ASC')
                ->getQuery();

        $entities = $query->getResult();
        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_anticipos_manual.html.twig', array('entities' => $entities));
    }

    /**
     * Interfaz para desimputar un cobro.
     *
     * @Route("/desimputar/", name="rengloncobranza_desimputar")
     * @Template("ADIFContableBundle:Cobranza\RenglonCobranza:desimputar.html.twig")
     */
    public function desimputarAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Desimputar'] = null;

        return array(
            'esNacion' => true,
            'breadcrumbs' => $bread,
            'page_title' => 'Desimputaciones'
        );
    }

    /**
     * Lista todos los recibos de cobranzas.
     *
     * @Route("/recibos/", name="rengloncobranza_recibos")
     * @Template("ADIFContableBundle:Cobranza\RenglonCobranza:table_recibos_cobranza.html.twig")
     */
    public function recibosAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Recibos'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Recibos'
        );
    }

    /**
     * @Route("/index_table_recibos_cobranza/", name="index_table_recibos_cobranza")
     * @Method("GET|POST")
     */
    public function indexTableRecibosCobranzaAction(Request $request) {

        $fecha_inicio = DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_desde') . ' 00:00:00');
        $fecha_fin = DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_hasta') . ' 23:59:59');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $repository = $em->getRepository('ADIFContableBundle:Cobranza\ReciboCobranza');

        $query = $repository->createQueryBuilder('recibo');

        if ($fecha_inicio) {
            $query = $query->andWhere('recibo.fecha >= \'' . $fecha_inicio->format('Y-m-d H:i:s') . '\'');
        }
        if ($fecha_fin) {
            $query = $query->andWhere('recibo.fecha <= \'' . $fecha_fin->format('Y-m-d H:i:s') . '\'');
        }

        $entities = $query->getQuery()->getResult();

        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_recibos_cobranza.html.twig', array('recibos' => $entities));
    }

    /**
     * Procesamiento del extracto bancario.
     *
     * @Route("/cargar_archivo_xls/", name="rengloncobranza_cargar_archivo_xls")
     * @Method("POST")
     * 
     */
    public function cargarArchivoXlsAction(Request $request) {
        $cuenta = $this->getCuentaBancariaAdif($request->request->get('id_cuenta'));
        $basura = "C:\\fakepath\\"; //lo agregan antes del nombre del archivo los navegadores como chrome menos firefox
        $nombre_archivo = $cuenta->getCbu() . '_' . str_replace($basura, '', $request->request->get('nombre_archivo'));
        $archivo = $request->files->get('archivo');

        if ($archivo) {
            $uploadDir = dirname($this->container->getParameter('kernel.root_dir')) . '/web/uploads/cobranzas/archivos';

            $archivo->move($uploadDir, $nombre_archivo);
            $ruta = $uploadDir . '/' . $nombre_archivo;

            $objReader = PHPExcel_IOFactory::createReaderForFile($ruta);

            if ($objReader->canRead($ruta)) {
                $objReader->setReadDataOnly(true);

                try {
                    $objPHPExcel = $objReader->load($ruta);
                } catch (Exception $e) {
                    return new JsonResponse(array(
                        'status' => 'ERROR',
                        'message' => $e->printStackTrace()));
                }
                $sheet = $objPHPExcel->getActiveSheet();

                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $emContable = $this->getDoctrine()->getManager($this->getEntityManager());
                $estado = $emContable->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')->findOneByDenominacion(ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE);

                for ($row = 2; $row <= $highestRow; $row++) {
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                    $datos = $rowData[0];

                    if (empty($datos[0]) || empty($datos[1]) || empty($datos[2])) {
                        //Si falta algún dato en la fila
                        return new JsonResponse(array(
                            'status' => 'ERROR',
                            'message' => "Datos invalidos en la fila " . $row)
                        );
                    } else {
                        if (strlen($datos[1]) > 8) {
                            // El número de referencia tiene mas de 8 caracteres
                            return new JsonResponse(array(
                                'status' => 'ERROR',
                                'message' => "Error en la fila " . $row . '. El n&uacute;mero de referencia debe contener un m&aacute;ximo de 8 caracteres')
                            );
                        }

                        $fecha = new \DateTime(gmdate('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($datos[0])));

                        //$fecha = new \DateTime(date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($datos[0])));
                        $tipoRegistro = $request->request->get('tipoRegistro');
                        $esOnabe = ($tipoRegistro == 4) ? true : false; // Si es la opcion 4: "Cobro ONABE"
                        $renglonCobranza = $this->crearRenglonCobranzaBanco($fecha, $datos[2], null, false, $cuenta, $estado, $datos[1], $esOnabe);
                        $emContable->persist($renglonCobranza);
                    }
                }
                $emContable->flush();
                return new JsonResponse(array('status' => 'OK', 'message' => 'Archivo cargado con &eacute;xito.'));
            } else {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => "Formato de archivo inv&aacute;lido"));
            }
        } else {
            return new JsonResponse(array(
                'status' => 'ERROR',
                'message' => "No se que paso"));
        }
    }

    /**
     * Tabla para Cobranza\RenglonCobranza .
     *
     * @Route("/index_table_cheques/", name="rengloncobranza_table_cheques")
     * @Method("GET|POST")
     */
    public function indexTableChequesAction(Request $request) {
        $tab = $request->query->get('tab');

        $estado = ($tab == 1 ? ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE : ConstanteEstadoRenglonCobranza::ESTADO_A_IMPUTAR);

        $entities = $this->obtenerRenglonesCobranzas(' IS NULL', $estado, 'Cheque', null, null);

        $bread = $this->base_breadcrumbs;
        $bread ['Cobranzas'] = null;

        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_cheques.html.twig', array('entities' => $entities)
        );
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/agregar_cheque/", name="rengloncobranza_agregar_cheque")
     * @Method("GET|POST")
     */
    public function agregarChequeAction(Request $request) {

        $fechaRequest = $request->request->get('fecha');
        $fechaRequestString = substr($fechaRequest, 6, 4) . '-' . substr($fechaRequest, 3, 2) . '-' . substr($fechaRequest, 0, 2);
        $fecha = new DateTime(date('Y-m-d', strtotime($fechaRequestString)));
        $referencia = $request->request->get('referencia');
        $monto = $request->request->get('monto');
        $observacion = $request->request->get('observacion');
        $id_banco = $request->request->get('id_banco');

        if (strlen($referencia) > 15) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'El n&uacute;mero debe contener un m&aacute;ximo de 15 caracteres'));
        }

        $emContable = $this->getDoctrine()->getManager($this->getEntityManager());
        $estado = $emContable->getRepository('ADIFContableBundle:Cobranza\EstadoRenglonCobranza')->findOneByDenominacion(ConstanteEstadoRenglonCobranza::ESTADO_PENDIENTE);
        if (!$estado) {
            throw $this->createNotFoundException('No se encuentran los estados en la base de datos.');
        }

        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());
        $banco = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Banco')->find($id_banco);

        if (!$banco) {
            throw $this->createNotFoundException('No se encuentra el banco en la base de datos.');
        }
        $renglonCobranzaCheque = new RenglonCobranzaCheque();
        $renglonCobranzaCheque->setFecha($fecha);
        $renglonCobranzaCheque->setMonto($monto);

        $renglonCobranzaCheque->setBanco($banco);
        $renglonCobranzaCheque->setEstadoRenglonCobranza($estado);
        $renglonCobranzaCheque->setNumero($referencia);
        $renglonCobranzaCheque->setObservacion($observacion);
        $emContable->persist($renglonCobranzaCheque);
        $emContable->flush();

        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito.'));
    }

    /**
     * Tabla para Cobranza\RenglonCobranza .
     *
     * @Route("/index_table_retenciones/", name="rengloncobranza_table_retenciones")
     * @Method("GET|POST")
     */
    public function indexTableRetencionesAction(Request $request) {

        $bread = $this->base_breadcrumbs;
        $bread ['Cobranzas'] = null;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

//        $repository1 = $em->getRepository('ADIFContableBundle:Cobranza\CobroRetencionCliente');
//        $query1 = $repository1->createQueryBuilder('crc')
//		->select('crc.id')
//                ->getQuery();
//        $ids = $query1->getResult();
//
//        $repository2 = $em->getRepository('ADIFContableBundle:Cobranza\RetencionCliente');
//        $query2 = $repository2->createQueryBuilder('rc');
//        if (sizeof($ids) > 0) {
//            $query2->leftJoin('rc.cobroRetencionCliente', 'crc')
//            ->where('rc.id NOT IN(:ids)')
//            //->where('crc IS NULL')
//            ->setParameter('ids', array_values($ids));           
//        }        
//        $query2 = $query2->orderBy('rc.fecha', 'ASC')
//                ->getQuery();        
//        $entities = $query2->getResult();

        $repository2 = $em->getRepository('ADIFContableBundle:Cobranza\RetencionCliente');
        $query2 = $repository2->createQueryBuilder('rc')
                ->where('rc.cobrosRetencionCliente IS EMPTY')
                ->orderBy('rc.fecha', 'ASC')
                ->getQuery();
        $entities = $query2->getResult();
        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_retenciones.html.twig', array('entities' => $entities));
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/agregar_retencion/", name="rengloncobranza_agregar_retencion")
     * @Method("GET|POST")
     */
    public function agregarRetencionAction(Request $request) {

        $fechaRequest = $request->request->get('fecha');
        $fechaRequestString = substr($fechaRequest, 6, 4) . '-' . substr($fechaRequest, 3, 2) . '-' . substr($fechaRequest, 0, 2);
        $fecha = new DateTime(date('Y-m-d', strtotime($fechaRequestString)));
        $referencia = $request->request->get('referencia');
        $monto = $request->request->get('monto');
        $id_cliente = $request->request->get('id_cliente');
        $id_impuesto = $request->request->get('id_impuesto');

        if (strlen($referencia) > 15) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'El n&uacute;mero debe contener un m&aacute;ximo de 15 caracteres'));
        }

//        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
//        $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->findOneById($id_cliente);
//
//        if (!$cliente) {
//            return new JsonResponse(array(
//                'status' => 'ERROR',
//                'message' => 'No se puede encontrar el cliente'));
//        }

        $emContable = $this->getDoctrine()->getManager($this->getEntityManager());
        $impuesto = $emContable->getRepository('ADIFContableBundle:RetencionClienteParametrizacion')->find($id_impuesto);

        if (!$impuesto) {
            throw $this->createNotFoundException('No se encuentra el impuesto en la base de datos.');
        }
        $retencionCliente = new RetencionCliente();
        $retencionCliente->setFecha($fecha);
        $retencionCliente->setMonto($monto);
        $retencionCliente->setNumero($referencia);

        //$retencionCliente->setCliente($cliente);
        $retencionCliente->setTipoImpuesto($impuesto);


        $emContable->persist($retencionCliente);
        $emContable->flush();

        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito.'));
    }

    /**
     * Lista todos los cheques para depositar
     *
     * @Route("/cheques_para_depositar/", name="cheques_para_depositar")
     * @Template("ADIFContableBundle:Cobranza\RenglonCobranza:table_cheques_para_depositar.html.twig")
     */
    public function chequesParaDepositarAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Cheques'] = null;

        $em = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $cuentas = $em->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')
                ->findByEstaActiva(true);
//              ->findAll();

        $cuentasArray = [];

        foreach ($cuentas as $cuenta) {
            $cuentasArray[$cuenta->getId()] = $cuenta->__toString();
        }

        return array(
            'cuentas' => $cuentasArray,
            'breadcrumbs' => $bread,
            'page_title' => 'Cheques'
        );
    }

    /**
     * @Route("/index_cheques_para_depositar/", name="index_cheques_para_depositar")
     * @Method("GET|POST")
     */
    public function indexChequesParaDepositarAction(Request $request) {

        $fecha_inicio = DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_desde') . ' 00:00:00');
        $fecha_fin = DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fecha_hasta') . ' 23:59:59');
        $entities = $this->obtenerRenglonesCobranzas(' IS NULL', ConstanteEstadoRenglonCobranza::ESTADO_A_IMPUTAR . '\' OR e.denominacion = \'' . ConstanteEstadoRenglonCobranza::ESTADO_IMPUTADO, 'Cheque', $fecha_inicio, $fecha_fin);

        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_cheques_para_depositar.html.twig', array('entities' => $entities));
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/depositar_cheque/", name="rengloncobranza_depositar_cheque")
     * @Method("GET|POST")
     */
    public function depositarChequeAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $fecha = null;
        $depositar = $request->request->get('depositar');
        $ids_cheque = json_decode($request->request->get('ids_cheque', '[]'));
        $cheques = array();

        $fecha_hoy = new DateTime();
        $fechaRequest = $request->request->get('fecha');
        $fechaRequestString = substr($fechaRequest, 6, 4) . '-' . substr($fechaRequest, 3, 2) . '-' . substr($fechaRequest, 0, 2);
        //$fecha = new DateTime(date('Y-m-d', strtotime($fechaRequestString)));
        $fecha = \DateTime::createFromFormat('Y-m-d H:i:s', $fechaRequestString . ' ' . $fecha_hoy->format('H:i:s'));

        foreach ($ids_cheque as $id_cheque) {

            $cheque = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranzaCheque')->find($id_cheque);
            if (!$cheque) {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'No se puede encontrar el cheque'));
            }
            if ($depositar == 1) {

                if ($cheque->getCuenta() != null) {
                    return new JsonResponse(array(
                        'status' => 'ERROR',
                        'message' => 'El cheque ya est&aacute; depositado'));
                }

                $id_cuenta = $request->request->get('cuenta');

                $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

                $cuenta = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')
                        ->findOneById($id_cuenta);

                if (!$cuenta) {
                    return new JsonResponse(array(
                        'status' => 'ERROR',
                        'message' => 'No se puede encontrar la cuenta'));
                }

                $cheque->setCuenta($cuenta);
            }

            $estado_actual = $cheque->getEstadoRenglonCobranza()->getDenominacion();
            if ($estado_actual != ConstanteEstadoRenglonCobranza::ESTADO_IMPUTADO && $estado_actual != ConstanteEstadoRenglonCobranza::ESTADO_A_IMPUTAR) {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'El cheque no está en el estado correcto'));
            }

            if (($depositar == 0) && ($cheque->getConciliacion() != null || sizeOf($cheque->getConciliaciones()) > 0)) {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'El cheque está involucrado en conciliaciones bancarias'));
            }

            if ($depositar == 0 && $cheque->getCuenta() == null) {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'El cheque no est&aacute; depositado'));
            }

            $fecha = $depositar == 1 ? $fecha : $cheque->getFechaDeposito();

            $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')->getEjercicioContableByFecha($fecha);
            if ($ejercicioContable->getEstaCerrado()) {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'El ejercicio contable est&aacute; cerrado'));
            }

            if (!$ejercicioContable->getMesEjercicioHabilitado($fecha->format('m'))) {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'El mes del ejercicio contable no est&aacute; habilitado'));
            }

            $cheques[] = $cheque;
        }

        $resultArray = $this->get('adif.asiento_service')
                ->generarAsientosContablesParaChequeDepositado($cheques, $this->getUser(), $fecha, $depositar == 0);

        // Si el asiento presupuestario falló
        if ($resultArray['mensajeErrorPresupuestario'] != null) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario']));
        }

        // Si el asiento contable falló
        if ($resultArray['mensajeErrorContable'] != null) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable']));
        }

        // Si no hubo errores en los asientos
        if ($resultArray['numeroAsiento'] != -1) {

            foreach ($cheques as $cheque) {

                if ($depositar == 1) {
                    $cheque->setCuenta($cuenta);
                    $cheque->setFechaDeposito($fecha);
                } else {
                    $cheque->setCuenta(null);
                    $cheque->setFechaDeposito(null);
                }
            }

            // Comienzo la transaccion
            $em->getConnection()->beginTransaction();

            try {
                $em->flush();

                $em->getConnection()->commit();

                $dataArray = [
                    'data-id-cheques' => implode(',', $ids_cheque),
                    'data-es-deposito' => $depositar
                ];

                $mensajeFlash = $this->get('adif.asiento_service')
                        ->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], $dataArray, true);

                return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito', 'mensajeAsiento' => $mensajeFlash));
            } catch (\Exception $e) {

                $em->getConnection()->rollback();

                //$em->close();

                throw $e;
            }
        }

        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito' . (sizeOf($cheque->getConciliaciones() > 0) ? '. Tenga en cuenta que el cheque tiene partidas conciliatorias asociadas' : '')));
    }

    public function desimputarCobroRetencionCliente($cobro) {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $estadoComprobanteIngresado = $em->getRepository('ADIFContableBundle:EstadoComprobante')
                ->find(EstadoComprobante::__ESTADO_INGRESADO);
        $resultArray = [
            'numeroAsiento' => -1,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];
        $retencionCliente = $cobro->getRetencionesCliente()->first();
        $fecha = $retencionCliente->getFecha();
        $cobrosRetencionCliente = $retencionCliente->getCobrosRetencionCliente();
        $offsetNumeroAsiento = 0;
		$fechaHoy = new DateTime();

        //foreach ($cobrosRetencionCliente as $cobro) {
        // Persisto los asientos contables y presupuestarios
        $resultArray = $this->get('adif.asiento_service')
                ->generarAsientosContablesParaImputaciones($fechaHoy, $cobrosRetencionCliente, array(), array(), $usuario, true, true); //primer boolean para retencion no se usa
        //->generarAsientosContablesParaRetencion($cobro, $usuario, true, $offsetNumeroAsiento++);
        // Si el asiento presupuestario falló
        if ($resultArray['mensajeErrorPresupuestario'] != null) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario']));
        }

        // Si el asiento contable falló
        if ($resultArray['mensajeErrorContable'] != null) {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable']));
        }
        //}    
        foreach ($cobrosRetencionCliente as $cobro) {
            $retencion_involucrada = $cobro->getRetencionesCliente()->first();
            $recibo_retencion_involucrada = $retencion_involucrada->getReciboRetencion();
            $recibo_retencion_involucrada->removeRenglonesRetencion($retencion_involucrada);

            $comprobante = $cobro->getComprobantes()[0];
            $comprobante->setSaldo($comprobante->getSaldo() + $cobro->getMonto());

            $comprobante->setEstadoComprobante($estadoComprobanteIngresado);
            $comprobante->removeCobro($cobro);
            $cobro->removeComprobante($comprobante);
            $em->remove($cobro);
        }
        return $resultArray;
    }

    private function verificar_caso($cobro) {
        $renglones = $cobro->getRenglonesCobranza();
        $cheques = $cobro->getCheques();
        $caso_raro = false; // es para solo cobro renglon cobranza pero se lo necesita inicializar para que funcione el if para el resto de los casos
        if (sizeOf($renglones) == 1 && sizeOf($cheques) == 0) { //si tiene más de un renglón sólo se debió imputar a un único comprobante, no es el caso raro
            $cobros_de_renglon = $renglones->first()->getCobrosRenglonCobranza();

            $caso_raro = sizeOf($cobros_de_renglon) > 1; //no es una imputación 1 a 1, es un caso raro si es 1 cobro, n comprobantes
        } else {
            if (sizeOf($renglones) == 0 && sizeOf($cheques) == 1) { //si tiene más de un renglón sólo se debió imputar a un único comprobante, no es el caso raro
                $cobros_de_renglon = $cheques->first()->getCobrosRenglonCobranzaCheque();

                $caso_raro = sizeOf($cobros_de_renglon) > 1; //no es una imputación 1 a 1, es un caso raro (1 cobro, n comprobantes)
            }
        }
        return $caso_raro;
    }

    /**
     * 
     * @param type $cobro
     * @param type $em
     * @return JsonResponse
     */
    public function desimputarCobroRenglonCobranza($cobro, $em) {
        $usuario = $this->getUser();

        $offsetNumeroAsiento = 0;

        $caso_raro = $this->verificar_caso($cobro);

//        $resultArray = $this->get('adif.asiento_service')
//                ->generarAsientosContablesParaDesimputacionRenglonCobranza($cobro, $usuario, $offsetNumeroAsiento++);
//
//        // Si alguno de los asientos falló abortar
//        if ($resultArray['mensajeErrorPresupuestario'] != null || $resultArray['mensajeErrorContable'] != null) {
//            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Fall&oacute; el registro de los asientos <br/>' . ($resultArray['mensajeErrorPresupuestario'] != null ? $resultArray['mensajeErrorPresupuestario'] : $resultArray['mensajeErrorContable'])));
//        }
        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        if (!$caso_raro) {

            $anticipo = $cobro->getAnticipoCliente();

            if ($anticipo == null) {
                //contraasiento de cobranza sin anticipo generado
                $anticipo = new AnticipoCliente();
                $anticipo->setCliente($cobro->getComprobantes()->first()->getCliente());
                $anticipo->setMonto($cobro->getMonto());
                $anticipo->setSaldo($cobro->getMonto());
                $anticipo->setCobroRenglonCobranza($cobro);
                $cobro->setAnticipoCliente($anticipo);
                $cobro->setMonto(0);
                $em->persist($anticipo);
            } else {
                //contraasiento de cobranza con anticipo generado
                $anticipo->setMonto($anticipo->getMonto() + $cobro->getMonto());
                $anticipo->setSaldo($anticipo->getSaldo() + $cobro->getMonto());
                $cobro->setMonto(0);
            }
        } else {
            $anticipo = null;
            $algun_otro_cobro = null;
            $anticipo_del_cobro_a_desimputar = false;
            $cobros_de_renglon = sizeOf($cobro->getRenglonesCobranza()) > 0 ? $cobro->getRenglonesCobranza()->first()->getCobrosRenglonCobranza() : $cobro->getCheques()->first()->getCobrosRenglonCobranzaCheque();

            foreach ($cobros_de_renglon as $cobro_aux) { //busco si en otro de los cobros hay anticipo
                $es_el_cobro_a_desimputar = $cobro_aux->getId() == $cobro->getId();

                if ($algun_otro_cobro == null && !$es_el_cobro_a_desimputar) {
                    $algun_otro_cobro = $cobro_aux;
                }

                if ($anticipo == null && $cobro_aux->getAnticipoCliente() != null) {
                    $anticipo = $cobro_aux->getAnticipoCliente();
                    $anticipo_del_cobro_a_desimputar = $es_el_cobro_a_desimputar;
                }
            }

            if ($anticipo != null) { //si tiene anticipo sumarle el monto y..
                $anticipo->setMonto($anticipo->getMonto() + $cobro->getMonto()); //le sumo al monto y saldo del anticipo el monto del cobro a desimputar
                $anticipo->setSaldo($anticipo->getSaldo() + $cobro->getMonto());
                if ($anticipo_del_cobro_a_desimputar) { //pasárle el anticipo a otro cobro
                    $cobro->setAnticipoCliente(null);
                    $anticipo->setCobroRenglonCobranza($algun_otro_cobro);
                    $algun_otro_cobro->setAnticipoCliente($anticipo);
                }
            } else { //si no tiene anticipo creo uno y se lo agrego a cualquiera de los otros cobros que no sea el que se va a desimputar                   
                $anticipo = new AnticipoCliente();
                $anticipo->setCliente($cobro->getComprobantes()->first()->getCliente());
                $anticipo->setMonto($cobro->getMonto()); //le pongo monto y saldo al anticipo igual al monto del cobro a desimputar
                $anticipo->setSaldo($cobro->getMonto());
                $anticipo->setCobroRenglonCobranza($algun_otro_cobro);
                $algun_otro_cobro->setAnticipoCliente($anticipo);
                $em->persist($anticipo);
            }
        }
        return $resultArray;
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/eliminar_retenciones/", name="rengloncobranza_eliminar_retenciones")
     * @Method("GET|POST")
     */
    public function eliminarRetencionesAction(Request $request) {
        $ids_retenciones = json_decode($request->request->get('ids', '[]'));

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        foreach ($ids_retenciones as $id_retencion) {
            $retencion = $em->getRepository('ADIFContableBundle:Cobranza\RetencionCliente')->find($id_retencion);

            if (!$retencion) {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'La retenci&oacute;n no se encuentra.'));
            }
            if (sizeOf($retencion->getCobrosRetencionCliente()) > 0) {
                return new JsonResponse(array(
                    'status' => 'ERROR',
                    'message' => 'La retenci&oacute;n es&aacute; imputada.'));
            }

            $em->remove($retencion);
        }
        $em->flush();
        return new JsonResponse(array('status' => 'OK', 'message' => 'Operaci&oacute;n realizada con &eacute;xito.'));
    }

    /**
     *
     * @Route("/editar_fecha_cobros/", name="rengloncobranza_editar_fecha_cobros")
     */
    public function updateFechaCobrosAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

//        $numeroAsiento = $request->request->get('numero_asiento');

        $fecha = $request->request->get('fecha');

        $idCobros = explode(',', $request->request->get('id_cobros'));

//        $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')
//                ->findOneByNumeroAsiento($numeroAsiento);
//
//        if (!$asientoContable) {
//            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
//        }

        $nueva_fecha = \DateTime::createFromFormat('d/m/Y', $fecha);

        foreach ($idCobros as $idCobro) {

            /* @var $cobro \ADIF\ContableBundle\Entity\Cobranza\Cobro */

            $cobro = $em->getRepository('ADIFContableBundle:Cobranza\Cobro')
                    ->find($idCobro);
            //var_dump($cobro->getMonto());die();
            $cobro->setFecha($nueva_fecha);

            if ($cobro->getEsCobroRetencionCliente()) {
                $retencion_involucrada = $cobro->getRetencionesCliente()->first();
                $retencion_involucrada->setFechaRegistro($nueva_fecha);
                $retencion_involucrada->getReciboRetencion()->setFecha($nueva_fecha);
            }

            $em->persist($cobro);
        }
        $em->flush();

        return new Response();
    }

    /**
     *
     * @Route("/editar_fecha_movimientos/", name="rengloncobranza_editar_fecha_movimientos")
     */
    public function updateFechaMovimientosAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

//        $numeroAsiento = $request->request->get('numero_asiento');

        $fecha = $request->request->get('fecha');

        $idMovimientos = explode(',', $request->request->get('id_movimientos'));

//        $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')
//                ->findOneByNumeroAsiento($numeroAsiento);
//
//        if (!$asientoContable) {
//            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
//        }
        $nueva_fecha = \DateTime::createFromFormat('d/m/Y', $fecha);
        foreach ($idMovimientos as $idMovimiento) {

            /* @var $movimiento \ADIF\ContableBundle\Entity\Cobranza\RenglonCobranza */

            $movimiento = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranza')
                    ->find($idMovimiento);

            $movimiento->setFechaRegistro($nueva_fecha);

            $movimiento->getRecibo()->setFecha($nueva_fecha); //Este método devuelve el recibo según corresponda (si es cheque o banco)

            $em->persist($movimiento);
        }
        $em->flush();
        //die();

        return new Response();
    }

    /**
     *
     * @Route("/editar_fecha_cheques/", name="rengloncobranza_editar_fecha_cheques")
     */
    public function updateFechaChequesAction(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

//        $numeroAsiento = $request->request->get('numero_asiento');

        $fecha = $request->request->get('fecha');

        $idCheques = explode(',', $request->request->get('id_cheques'));

//        $asientoContable = $em->getRepository('ADIFContableBundle:AsientoContable')
//                ->findOneByNumeroAsiento($numeroAsiento);
//
//        if (!$asientoContable) {
//            throw $this->createNotFoundException('No se puede encontrar la entidad AsientoContable.');
//        }

        foreach ($idCheques as $idCheque) {

            /* @var $cheque \ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque */

            $cheque = $em->getRepository('ADIFContableBundle:Cobranza\RenglonCobranzaCheque')
                    ->find($idCheque);

            $cheque->setFechaDeposito(\DateTime::createFromFormat('d/m/Y', $fecha));

            $em->persist($cheque);
        }

        $em->flush();

        return new Response();
    }

    /**
     * Print ReciboCobranza.
     *
     * @Route("/imprimir/{id}", name="recibo_cobranza_print")
     * @Method("GET")
     * @Template("")
     */
    public function imprimirAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $entity ComprobanteVenta */
        $recibo = $em->getRepository('ADIFContableBundle:Cobranza\ReciboCobranza')->find($id);

        if (!$recibo) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ReciboCobranza.');
        }

        $barCodeNumber = $recibo->getCodigoBarrasNacion();

//        $barCode = new barCode();
//        $barCode->savePath = $this->get('kernel')->getRootDir() . '/../web/uploads/barcodes';
//        $bcPathAbs = $barCode->getBarcodePNGPath($barCodeNumber, 'I25', 1.5, 25);

        $html = '<html><head><meta charset="utf-8"/><style type="text/css">'
                . $this->renderView('ADIFContableBundle:Cobranza\RenglonCobranza:recibo_cobranza.css.twig')
                . '</style></head><body>';

        $html .='<div class="comprobante-electronico-content">';

        $html .= $this->renderView('ADIFContableBundle:Cobranza\RenglonCobranza:recibo_cobranza.html.twig', array(
            'fecha' => $recibo->getFecha(),
            'numero_recibo' => $recibo->getNumeroRecibo(),
            'cliente' => $recibo->getCliente(),
            'comprobantes' => $recibo->getComprobantes(),
            'renglones_banco' => $recibo->getRenglonesBanco(),
            'renglones_cheque' => $recibo->getRenglonesCheque(),
            'renglones_retencion' => $recibo->getRenglonesRetencion(),
            'importe_cobranzas' => $recibo->getImporteTotal(),
            'importe_comprobantes' => $recibo->getImporteComprobantes(),
            'barCode' => '/barcode.php?text=' . $barCodeNumber, //$bcPathAbs,
            'barCodeNumber' => $barCodeNumber,
            'detalle' => $recibo->getDetalle())
        );

        $html .='</div>';

        $html .= '</body></html>';

        $filename = 'Recibo Cobranza nro. ' . $recibo->getNumeroRecibo() . '.pdf';

        $mpdfService = new mPDF('', 'A4');

        $mpdfService->WriteHTML($html);
        return new Response(
                $mpdfService->Output($filename, 'D')
        );
    }

    /**
     * Muestra el detalle del saldo de un anticipo
     *
     * @Route("/detalleSaldoAnticipo/", name="detalleSaldoAnticipo")
     */
    public function detalleSaldoAnticipoAction(Request $request) {
        $id_anticipo = $request->request->get('id_anticipo');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $anticipo = $em->getRepository('ADIFContableBundle:Cobranza\AnticipoCliente')->find($id_anticipo);

        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:detalle_saldo_anticipo.html.twig', array('anticipo' => $anticipo));
    }

    /**
     * Muestra los comprobantes de venta que tienen un código de barras
     *
     * @Route("/buscarComprobante/", name="buscarComprobante")
     */
    public function buscarComprobanteAction(Request $request) {
        $codigo = $request->request->get('codigo');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $comprobantes = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')->findBy(array('codigoBarras' => $codigo));

        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:comprobantes_por_codigo_barras.html.twig', array('comprobantes' => $comprobantes));
    }
    
    /**
     * Reporte de cobranzas AABE y ADIF.
     *
     * @Route("/reportecobranzas", name="reportecobranzas")
     * @Method("GET")
     * @Template()
     */
    public function reporteCobranzasAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Reporte de Cobranzas'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Cobranzas',
            'page_info' => 'Reporte de cobranzas'
        );
    }

    /**
     * Tabla para Reporte de cobranzas AABE y ADIF .
     *
     * @Route("/index_table_reportecobranzas/", name="index_table_reportecobranzas")
     * @Method("GET|POST")
     */
    public function indexTableReporteAction(Request $request) {

        $cobranzas = array();

        if ($request->query->get('fechaInicio') && $request->query->get('fechaFin')) {

            $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaInicio') . ' 00:00:00');
            $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaFin') . ' 23:59:59');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $rsm = new ResultSetMapping();

            $rsm->addScalarResult('id', 'id');
            $rsm->addScalarResult('tipo', 'tipo');
            $rsm->addScalarResult('numero_recibo', 'numeroRecibo');
            $rsm->addScalarResult('fecha_recibo', 'fechaRecibo');
            $rsm->addScalarResult('referencia', 'referencia');
            $rsm->addScalarResult('fecha_cobranza', 'fechaCobranza');            
            $rsm->addScalarResult('fecha_comprobante', 'fechaComprobante');            
            $rsm->addScalarResult('tipo_comprobante', 'tipoComprobante');
            $rsm->addScalarResult('letra_comprobante', 'letraComprobante');
            $rsm->addScalarResult('punto_venta_comprobante', 'puntoVentaComprobante');
            $rsm->addScalarResult('numero_comprobante', 'numeroComprobante');
            $rsm->addScalarResult('contrato', 'contrato');
            $rsm->addScalarResult('cliente', 'cliente');
            $rsm->addScalarResult('concepto', 'concepto');
            $rsm->addScalarResult('cuenta_contable', 'cuentaContable');
            $rsm->addScalarResult('importe', 'importe');
            $rsm->addScalarResult('observaciones', 'observaciones');

            $native_query = $em->createNativeQuery('
            SELECT
                id,
                tipo,
                numero_recibo,
                fecha_recibo,
                referencia,
                fecha_cobranza,
                fecha_comprobante,
                tipo_comprobante,
                letra_comprobante,
                punto_venta_comprobante,
                numero_comprobante,
                contrato,
                cliente,
                concepto,
                cuenta_contable,
                importe,
                observaciones
            FROM
                vistareportecobranzas
            WHERE fecha_cobranza BETWEEN ? AND ?
        ', $rsm);

            $native_query->setParameter(1, $fechaInicio, Type::DATETIME);
            $native_query->setParameter(2, $fechaFin, Type::DATETIME);

            $cobranzas = $native_query->getResult();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Reporte AABE y ADIF'] = null;

        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_reportecobranzas.html.twig', array(
            'entities' => $cobranzas)
        );
    }
    
    /**
     * Reporte de cobranzas AABE y ADIF.
     *
     * @Route("/reporte_cobranzas_aabe_adif", name="reporte_cobranzas_aabe_adif")
     * @Method("GET")
     * @Template()
     */
    public function reporteCobranzasAabeAdifAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Reporte de Cobranzas'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Cobranzas',
            'page_info' => 'Reporte de cobranzas AABE y ADIF'
        );
    }  
    
    /**
     * Tabla para Reporte de cobranzas AABE y ADIF .
     *
     * @Route("/index_table_reporte_cobranzas_aabe_adif/", name="index_table_reporte_cobranzas_aabe_adif")
     * @Method("GET|POST")
     */
    public function indexTableReporteAabeAdifAction(Request $request) {

        $cobranzas = array();

        if ($request->query->get('fechaInicio') && $request->query->get('fechaFin')) {

            $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaInicio') . ' 00:00:00');
            $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaFin') . ' 23:59:59');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $rsm = new ResultSetMapping();

            $rsm->addScalarResult('id', 'id');
            $rsm->addScalarResult('fecha_comprobante', 'fechaComprobante');            
            $rsm->addScalarResult('tipo_comprobante', 'tipoComprobante');
            $rsm->addScalarResult('letra_comprobante', 'letraComprobante');
            $rsm->addScalarResult('punto_venta_comprobante', 'puntoVentaComprobante');
            $rsm->addScalarResult('numero_comprobante', 'numeroComprobante');
            $rsm->addScalarResult('numero_cupon', 'numeroCupon');
            $rsm->addScalarResult('numero_contrato', 'numeroContrato');
            $rsm->addScalarResult('clase_contrato', 'claseContrato');
            $rsm->addScalarResult('cliente', 'cliente');
            $rsm->addScalarResult('monto_aabe', 'montoAABE');
            $rsm->addScalarResult('monto_adif', 'montoADIF');

            $native_query = $em->createNativeQuery('
            SELECT
                id,
                fecha_comprobante,
                tipo_comprobante,
                letra_comprobante,
                punto_venta_comprobante,
                numero_comprobante,
                numero_cupon,
                numero_contrato,
                clase_contrato,
                cliente,
                monto_aabe,
                monto_adif
            FROM
                vistareportecobranzasaabeadif
            WHERE fecha_comprobante BETWEEN ? AND ?
        ', $rsm);

            $native_query->setParameter(1, $fechaInicio, Type::DATETIME);
            $native_query->setParameter(2, $fechaFin, Type::DATETIME);

            $cobranzas = $native_query->getResult();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Reporte AABE y ADIF'] = null;

        return $this->render('ADIFContableBundle:Cobranza/RenglonCobranza:index_table_reporte_cobranzas_aabe_adif.html.twig', array(
            'entities' => $cobranzas)
        );
    }    
    

}
