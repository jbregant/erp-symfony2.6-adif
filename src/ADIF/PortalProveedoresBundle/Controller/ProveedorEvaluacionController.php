<?php

namespace ADIF\PortalProveedoresBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Controller\ProveedorController;
use ADIF\ComprasBundle\Entity\Cliente;
use ADIF\ComprasBundle\Entity\ClienteProveedor;
use ADIF\ComprasBundle\Entity\ContactoProveedor;
use ADIF\ComprasBundle\Entity\DatoContacto;
use ADIF\ComprasBundle\Entity\DatosImpositivos;
use ADIF\ComprasBundle\Entity\EvaluacionAspectoProveedor;
use ADIF\ComprasBundle\Entity\Proveedor;
use ADIF\ComprasBundle\Entity\ProveedorUTE;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoInternoCuentaContable;
use ADIF\ContableBundle\Entity\TipoMoneda;
use ADIF\PortalProveedoresBundle\Controller\BaseController;
use ADIF\PortalProveedoresBundle\Entity\Constantes\ConstanteTipoDomicilio;
use ADIF\PortalProveedoresBundle\Entity\ObservacionEvaluacion;
use ADIF\PortalProveedoresBundle\Entity\ProveedorDatoBancario;
use ADIF\PortalProveedoresBundle\Entity\ProveedorDatoContacto;
use ADIF\PortalProveedoresBundle\Entity\ProveedorEvaluacion;
use ADIF\PortalProveedoresBundle\Entity\ProveedorEvaluacionLog;
use ADIF\PortalProveedoresBundle\Entity\TipoPersona;
use ADIF\PortalProveedoresBundle\Form\ProveedorEvaluacionType;
use ADIF\RecursosHumanosBundle\Entity\Banco;
use ADIF\RecursosHumanosBundle\Entity\CuentaBancaria;
use ADIF\RecursosHumanosBundle\Entity\Domicilio;
use ADIF\RecursosHumanosBundle\Entity\CuentaBancariaPersona;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\PortalProveedoresBundle\Entity\EstadoEvaluacion;
use ADIF\PortalProveedoresBundle\Entity\ProveedorTimeline;
use ADIF\PortalProveedoresBundle\Entity\TipoTimeline;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * ProveedorEvaluacion controller.
 *
 * @Route("/proveedorevaluacion")
 */
class ProveedorEvaluacionController extends BaseController
{

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio'                 => '',
            'Evaluacion Interesados' => $this->generateUrl('proveedor_evaluacion'),
        );
    }

    /**
     * Lists all Proveedor entities.
     *
     * @Route("/", name="proveedor_evaluacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread                = $this->base_breadcrumbs;
        $bread['Interesados'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title'  => 'Interesados',
            'page_info'   => 'Lista de interesados',
        );
    }

    /**
     * Tabla para Interesados
     *
     * @Route("/index_table", name="proveedor_evaluacion_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $interesados = $em->getRepository('ADIFPortalProveedoresBundle:ProveedorEvaluacion')->findBy(['estadoEvaluacion' => [1,2,3,4]]);

//        \Doctrine\Common\Util\Debug::dump($interesados);die;
//        \Doctrine\Common\Util\Debug::dump($interesados[0]);die;
        $bread                = $this->base_breadcrumbs;
        $bread['Interesados'] = null;

        return $this->render('ADIFPortalProveedoresBundle:ProveedorEvaluacion:index_table.html.twig', array('entities' => $interesados)
        );
    }

    /**
     *
     * Show the detail of evaluation of Interested for GAF Impuestos
     *
     * @Route("/galo/{id}", name="proveedor_evaluacion_show_galo")
     * @Method("GET|POST")
     * @Template("ADIFPortalProveedoresBundle:ProveedorEvaluacion:show_galo.html.twig")
     */
    public function showGaloAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFPortalProveedoresBundle:ProveedorDatoPersonal')->find($id);

        $observacionesRepo = $em->getRepository('ADIFPortalProveedoresBundle:TipoObservacion')->findAll();
        $tipoObservacion = array();
        $editParam = $request->query->get('edit');

        if ( isset($editParam) ){
            $solicitaModificacion = true;
        } else {
            $solicitaModificacion = false;
        }

        foreach ($observacionesRepo as $observacion) {
            $tipoObservacion[$observacion->getId()] = str_replace('_', ' ', $observacion->getDenominacion());
        }

        if (!$entity) {
            throw $this->createNotFoundException(
                'No product found for id ' . $entity
            );
        }

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $datos = $request->request->get('adif_portalProveedoresBundle_proveedorEvaluacion');
            $datosRechazo = $request->request->all();

            $existenObservaciones = false;
            $observaciones['gerencia'] = 'Gerencia Galo';
            $observaciones['observaciones'] = array();

            // guardar observaciones
            foreach ($datos['observacionEvaluacion'] as $value) {
                // Guarda las observaciones enviadas en el form
                if (!empty($value['observaciones'])) {
                    // Si existen observaciones en algunos de lo form entra directamente en el
                    if($value['observaciones']){
                        $observaciones['observaciones'][] = array('denominacion' => $tipoObservacion[$value['tipoObservacion']], 'detalleObservacion' => $value['observaciones']);
                        $this->guardarEvaluacion($value['observaciones'], $value['tipoObservacion'], $value['proveedorEvaluacion']);
                        $existenObservaciones = true;
                    }
                }
            }

            // estado_evaluacion_gerencia 2 APROBADO
            if ( isset ($datos['aprobar']) ) {
                $nuevoEstadoId = 2;
                $dataTwig = array('gerencia' => 'Galo', 'accion' => 'aprobada');
                $this->enviarEmail($entity->getUsername(), $dataTwig, 'aprobador');
                // estado_evaluacion_gerencia 3 RECHAZADO
            } else if ( isset($datosRechazo['motivo_rechazo']) && isset($datosRechazo['motivo_rechazo_interno']) ) {
                $nuevoEstadoId = 3;
                //se envia email con el rechazo y el motivo
//                $dataTwig = array('gerencia' => 'Gcshm', 'accion' => 'rechazada', 'observacion' => $datosRechazo['motivo_rechazo']);
//                $this->enviarEmail($entity->getUsername(), $dataTwig, 'aprobador');
                // estado_evaluacion_gerencia 4 OBSERVADO
            } else {
                if ($existenObservaciones) {
                    $nuevoEstadoId = 4;
                    //se envia el email con las observaciones
                    $this->enviarEmail($entity->getUsername(), $observaciones, 'observacion');
                } else {
                    $nuevoEstadoId = null;
                }
            }

            // Si se observo algun item cambio el estado a "observado"  en  la entidad EstadoEvaluacion
            if (isset($nuevoEstadoId)) {
                if ( isset($datosRechazo['motivo_rechazo']) && isset($datosRechazo['motivo_rechazo_interno']) ){
                    $motivosRechazo = array('motivo_rechazo' => $datosRechazo['motivo_rechazo'], 'motivo_rechazo_interno' => $datosRechazo['motivo_rechazo_interno']);
                    $this->cambiarEstadoEvaluacion($entity->getProveedorEvaluacion()->getId(), 2, $nuevoEstadoId, $motivosRechazo);
                } else {
                    $this->cambiarEstadoEvaluacion($entity->getProveedorEvaluacion()->getId(), 2, $nuevoEstadoId);
                }

                return $this->redirect($this->generateUrl('proveedor_evaluacion'));
            } else {
                $this->addWarningFlash('No hay cambios para guardar');
            }
        }

        return $this->render('ADIFPortalProveedoresBundle:ProveedorEvaluacion:show_galo.html.twig', array(
            'datosUsuario' => $entity,
            'form'         => $form->createView(),
            'solicitaModificacion' =>  $solicitaModificacion,
        ));
    }

    /**
     *
     * Show the detail of evaluation of Interested for GAF Impuestos
     *
     * @Route("/gaf-finanzas/{id}", name="proveedor_evaluacion_show_gaf_finanzas")
     * @Method("GET|POST")
     * @Template("ADIFPortalProveedoresBundle:ProveedorEvaluacion:show_gaf_finanzas.html.twig")
     */
    public function showGafFinanzasAction($id, Request $request)
    {
        $em        = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity    = $em->getRepository('ADIFPortalProveedoresBundle:ProveedorDatoPersonal')->find($id);
        $observacionesRepo = $em->getRepository('ADIFPortalProveedoresBundle:TipoObservacion')->findAll();
        $tipoObservacion = array();
        $editParam = $request->query->get('edit');

        if ( isset($editParam) ){
            $solicitaModificacion = true;
        } else {
            $solicitaModificacion = false;
        }

        foreach ($observacionesRepo as $observacion) {
            $tipoObservacion[$observacion->getId()] = str_replace('_', ' ', $observacion->getDenominacion());
        }

        if (!$entity) {
            throw $this->createNotFoundException(
                'No product found for id ' . $entity
            );
        }

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $datos = $request->request->get('adif_portalProveedoresBundle_proveedorEvaluacion');
            $datosRechazo = $request->request->all();

            $existenObservaciones = false;
            $observaciones['gerencia'] = 'Gerencia Gaf Finanzas';
            $observaciones['observaciones'] = array();

            // guardar observaciones
            foreach ($datos['observacionEvaluacion'] as $value) {
                // Guarda las observaciones enviadas en el form
                if (!empty($value['observaciones'])) {
                    $observaciones['observaciones'][] = array('denominacion' => $tipoObservacion[$value['tipoObservacion']], 'detalleObservacion' => $value['observaciones']);
                    $this->guardarEvaluacion($value['observaciones'], $value['tipoObservacion'], $value['proveedorEvaluacion']);
                    $existenObservaciones = true;
                }
            }

            // estado_evaluacion_gerencia 2 APROBADO
            if ( isset ($datos['aprobar']) ) {
                $nuevoEstadoId = 2;
                $dataTwig = array('gerencia' => 'Gaf Finanzas', 'accion' => 'aprobada');
                $this->enviarEmail($entity->getUsername(), $dataTwig, 'aprobador');
                // estado_evaluacion_gerencia 3 RECHAZADO
            } else if ( isset($datosRechazo['motivo_rechazo']) && isset($datosRechazo['motivo_rechazo_interno']) ) {
                $nuevoEstadoId = 3;
                //se envia email con el rechazo y el motivo
//                $dataTwig = array('gerencia' => 'Gcshm', 'accion' => 'rechazada', 'observacion' => $datosRechazo['motivo_rechazo']);
//                $this->enviarEmail($entity->getUsername(), $dataTwig, 'aprobador');
                // estado_evaluacion_gerencia 4 OBSERVADO
            } else {
                if ($existenObservaciones) {
                    $nuevoEstadoId = 4;
                    //se envia el email con las observaciones
                    $this->enviarEmail($entity->getUsername(), $observaciones, 'observacion');
                } else {
                    $nuevoEstadoId = null;
                }
            }

            // Si se observo algun item cambio el estado a "observado"  en  la entidad EstadoEvaluacion
            if (isset($nuevoEstadoId)) {
                if ( isset($datosRechazo['motivo_rechazo']) && isset($datosRechazo['motivo_rechazo_interno']) ){
                    $motivosRechazo = array('motivo_rechazo' => $datosRechazo['motivo_rechazo'], 'motivo_rechazo_interno' => $datosRechazo['motivo_rechazo_interno']);
                    $this->cambiarEstadoEvaluacion($entity->getProveedorEvaluacion()->getId(), 4, $nuevoEstadoId, $motivosRechazo);
                } else {
                    $this->cambiarEstadoEvaluacion($entity->getProveedorEvaluacion()->getId(), 4, $nuevoEstadoId);
                }

                return $this->redirect($this->generateUrl('proveedor_evaluacion'));
            } else {
                $this->addWarningFlash('No hay cambios para guardar');
            }
        }

        return $this->render('ADIFPortalProveedoresBundle:ProveedorEvaluacion:show_gaf_finanzas.html.twig', array(
            'datosUsuario' => $entity,
            'form'         => $form->createView(),
            'solicitaModificacion' =>  $solicitaModificacion,
        ));
    }

    /**
     *
     * Show the detail of evaluation of Interested for GAF Impuestos
     *
     * @Route("/gaf-impuestos/{id}", name="proveedor_evaluacion_show_gaf_impuestos")
     * @Method("GET|POST")
     * @Template("ADIFPortalProveedoresBundle:ProveedorEvaluacion:show_gaf_impuestos.html.twig")
     */
    public function showGafImpuestosAction($id, Request $request)
    {
        $em     = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity    = $em->getRepository('ADIFPortalProveedoresBundle:ProveedorDatoPersonal')->find($id);
        $observacionesRepo = $em->getRepository('ADIFPortalProveedoresBundle:TipoObservacion')->findAll();
        $tipoObservacion = array();
        $editParam = $request->query->get('edit');

        if ( isset($editParam) ){
            $solicitaModificacion = true;
        } else {
            $solicitaModificacion = false;
        }

        foreach ($observacionesRepo as $observacion) {
            $tipoObservacion[$observacion->getId()] = str_replace('_', ' ', $observacion->getDenominacion());
        }

        if (!$entity) {
            throw $this->createNotFoundException(
                'No product found for id ' . $entity
            );
        }

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $datos = $request->request->get('adif_portalProveedoresBundle_proveedorEvaluacion');
            $datosRechazo = $request->request->all();

            $existenObservaciones = false;
            $observaciones['gerencia'] = 'Gerencia Gaf Impuestos';
            $observaciones['observaciones'] = array();

            // guardar observaciones
            foreach ($datos['observacionEvaluacion'] as $value) {
                // Guarda las observaciones enviadas en el form
                if (!empty($value['observaciones'])) {
                    $observaciones['observaciones'][] = array('denominacion' => $tipoObservacion[$value['tipoObservacion']], 'detalleObservacion' => $value['observaciones']);
                    $this->guardarEvaluacion($value['observaciones'], $value['tipoObservacion'], $value['proveedorEvaluacion']);
                    $existenObservaciones = true;
                }
            }

            // estado_evaluacion_gerencia 2 APROBADO
            if ( isset ($datos['aprobar']) ) {
                $nuevoEstadoId = 2;
                $dataTwig = array('gerencia' => 'Gaf Impuestos', 'accion' => 'aprobada');
                $this->enviarEmail($entity->getUsername(), $dataTwig, 'aprobador');
                // estado_evaluacion_gerencia 3 RECHAZADO
            } else if ( isset($datosRechazo['motivo_rechazo']) && isset($datosRechazo['motivo_rechazo_interno']) ) {
                $nuevoEstadoId = 3;
                //se envia email con el rechazo y el motivo
//                $dataTwig = array('gerencia' => 'Gcshm', 'accion' => 'rechazada', 'observacion' => $datosRechazo['motivo_rechazo']);
//                $this->enviarEmail($entity->getUsername(), $dataTwig, 'aprobador');
                // estado_evaluacion_gerencia 4 OBSERVADO
            } else {
                if ($existenObservaciones) {
                    $nuevoEstadoId = 4;
                    //se envia el email con las observaciones
                    $this->enviarEmail($entity->getUsername(), $observaciones, 'observacion');
                } else {
                    $nuevoEstadoId = null;
                }
            }

            // Si se observo algun item cambio el estado a "observado"  en  la entidad EstadoEvaluacion
            if (isset($nuevoEstadoId)) {
                if ( isset($datosRechazo['motivo_rechazo']) && isset($datosRechazo['motivo_rechazo_interno']) ){
                    $motivosRechazo = array('motivo_rechazo' => $datosRechazo['motivo_rechazo'], 'motivo_rechazo_interno' => $datosRechazo['motivo_rechazo_interno']);
                    $this->cambiarEstadoEvaluacion($entity->getProveedorEvaluacion()->getId(), 5, $nuevoEstadoId, $motivosRechazo);
                } else {
                    $this->cambiarEstadoEvaluacion($entity->getProveedorEvaluacion()->getId(), 5, $nuevoEstadoId);
                }

                return $this->redirect($this->generateUrl('proveedor_evaluacion'));
            } else {
                $this->addWarningFlash('No hay cambios para guardar');
            }
        }

        return $this->render('ADIFPortalProveedoresBundle:ProveedorEvaluacion:show_gaf_impuestos.html.twig', array(
            'datosUsuario' => $entity,
            'form'         => $form->createView(),
            'solicitaModificacion' =>  $solicitaModificacion,
        ));
    }

    /**
     *
     * Show the detail of evaluation of Interested for GAF Impuestos
     *
     * @Route("/gcshm/{id}", name="proveedor_evaluacion_show_gcshm")
     * @Method("GET|POST")
     * @Template("ADIFPortalProveedoresBundle:ProveedorEvaluacion:show_gcshm.html.twig")
     */
    public function showGcshmAction($id, Request $request)
    {
        $em     = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity    = $em->getRepository('ADIFPortalProveedoresBundle:ProveedorDatoPersonal')->find($id);
        $observacionesRepo = $em->getRepository('ADIFPortalProveedoresBundle:TipoObservacion')->findAll();
        $tipoObservacion = array();
        $editParam = $request->query->get('edit');

        if ( isset($editParam) ){
            $solicitaModificacion = true;
        } else {
            $solicitaModificacion = false;
        }

        foreach ($observacionesRepo as $observacion) {
            $tipoObservacion[$observacion->getId()] = str_replace('_', ' ', $observacion->getDenominacion());
        }

        if (!$entity) {
            throw $this->createNotFoundException(
                'No product found for id ' . $entity
            );
        }

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $datos = $request->request->get('adif_portalProveedoresBundle_proveedorEvaluacion');
            $datosRechazo = $request->request->all();

            $existenObservaciones = false;
            $observaciones['gerencia'] = 'Gerencia Gcshm';
            $observaciones['observaciones'] = array();

            // guardar observaciones
            foreach ($datos['observacionEvaluacion'] as $value) {
                // Guarda las observaciones enviadas en el form
                if (!empty($value['observaciones'])) {
                    $observaciones['observaciones'][] = array('denominacion' => $tipoObservacion[$value['tipoObservacion']], 'detalleObservacion' => $value['observaciones']);
                    $this->guardarEvaluacion($value['observaciones'], $value['tipoObservacion'], $value['proveedorEvaluacion']);
                    $existenObservaciones = true;
                }
            }

            // estado_evaluacion_gerencia 2 APROBADO
            if ( isset ($datos['aprobar']) ) {
                $nuevoEstadoId = 2;
                $dataTwig = array('gerencia' => 'Gcshm', 'accion' => 'aprobada');
                $this->enviarEmail($entity->getUsername(), $dataTwig, 'aprobador');
                // estado_evaluacion_gerencia 3 RECHAZADO
            } else if ( isset($datosRechazo['motivo_rechazo']) && isset($datosRechazo['motivo_rechazo_interno']) ) {
                $nuevoEstadoId = 3;
                //se envia email con el rechazo y el motivo
//                $dataTwig = array('gerencia' => 'Gcshm', 'accion' => 'rechazada', 'observacion' => $datosRechazo['motivo_rechazo']);
//                $this->enviarEmail($entity->getUsername(), $dataTwig, 'aprobador');
                // estado_evaluacion_gerencia 4 OBSERVADO
            } else {
                if ($existenObservaciones) {
                    $nuevoEstadoId = 4;
                    //se envia el email con las observaciones
                    $this->enviarEmail($entity->getUsername(), $observaciones, 'observacion');
                } else {
                    $nuevoEstadoId = null;
                }
            }



            // Si se observo algun item cambio el estado a "observado"  en  la entidad EstadoEvaluacion
            if (isset($nuevoEstadoId)) {
                if ( isset($datosRechazo['motivo_rechazo']) && isset($datosRechazo['motivo_rechazo_interno']) ){
                    $motivosRechazo = array('motivo_rechazo' => $datosRechazo['motivo_rechazo'], 'motivo_rechazo_interno' => $datosRechazo['motivo_rechazo_interno']);
                    $this->cambiarEstadoEvaluacion($entity->getProveedorEvaluacion()->getId(), 3, $nuevoEstadoId, $motivosRechazo);
                } else {
                    $this->cambiarEstadoEvaluacion($entity->getProveedorEvaluacion()->getId(), 3, $nuevoEstadoId);
                }

                return $this->redirect($this->generateUrl('proveedor_evaluacion'));
            } else {
                $this->addWarningFlash('No hay cambios para guardar');
            }
        }

        return $this->render('ADIFPortalProveedoresBundle:ProveedorEvaluacion:show_gcshm.html.twig', array(
            'datosUsuario' => $entity,
            'form'         => $form->createView(),
            'solicitaModificacion' =>  $solicitaModificacion,
        ));
    }

    private function createCreateForm($entity)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        // Si ya hay Observaciones anteriores, las traemos
        $idProveedorEvaluacion = $entity->getProveedorEvaluacion()->getId();
        $Observaciones         = $em->getRepository('ADIFPortalProveedoresBundle:ObservacionEvaluacion')->findBy(array('proveedorEvaluacion' => $idProveedorEvaluacion));

        //genero el array key:value idtipoobservacion:textoobservacion
        $antObs = array();

        foreach ($Observaciones as  $observacion) {
            $antObs[$observacion->getTipoObservacion()->getId()][] = array('observacion' => $observacion->getObservaciones(), 'fechaCreacion' => $observacion->getFechaCreacion());
        }

        //ordeno para mostrar en el form
        $antObsSorted = array();

        foreach ($antObs as $key => $obs) {
            $antObsSorted[$key] = array_reverse($obs, true);
        }

        // Obtengo todos los tipos de Bloques de la entidad "TipoObservacion"
        $tiposObservacion = $em->getRepository('ADIFPortalProveedoresBundle:TipoObservacion')->findAll();

        // Agrego al  Form la data para cada bloque
        $evaluacion = new ProveedorEvaluacion();

        foreach ($tiposObservacion as $tipoObservacion) {
            $obs = new ObservacionEvaluacion();
            $obs->setTipoObservacion($tipoObservacion->getId());

            if (array_key_exists($tipoObservacion->getId(), $antObs)) {
                $obs->setObservacionesHistorico($antObsSorted[$tipoObservacion->getId()]);
            }
            $evaluacion->getObservacionEvaluacion()->add($obs);
        }

        return $form = $this->createForm(new ProveedorEvaluacionType(), $evaluacion);
    }

    /**
     * Guarda una Observacion.
     *
     * @param String $observacion
     * @param Integer $idTipoObservacion
     * @param Integer $idProveedorEvaluacion
     */
    private function guardarEvaluacion($observacion, $idTipoObservacion, $idProveedorEvaluacion)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $observacionEvaluacion = new ObservacionEvaluacion();

        $tipoObservacion     = $em->getRepository('ADIFPortalProveedoresBundle:TipoObservacion')->findOneById($idTipoObservacion);
        $proveedorEvaluacion = $em->getRepository('ADIFPortalProveedoresBundle:ProveedorEvaluacion')->findOneById($idProveedorEvaluacion);
        $estadoEvaluacion = $em->getRepository('ADIFPortalProveedoresBundle:EstadoEvaluacion')->findOneBy(['denominacion' => 'Observado']);

        //cambio el estado de evaluaciónGral a Observado
        $proveedorEvaluacion->setEstadoEvaluacion($estadoEvaluacion);
        $em->persist($proveedorEvaluacion);
        $em->flush();

        $observacionEvaluacion->setTipoObservacion($tipoObservacion);
        $observacionEvaluacion->setProveedorEvaluacion($proveedorEvaluacion);
        $observacionEvaluacion->setObservaciones($observacion);

        $em->persist($observacionEvaluacion);
        $em->flush();

        $this->guardarTimeline($tipoObservacion->getDenominacion(), $proveedorEvaluacion->getIdDatoPersonal()->getId());
    }

    /**
     * Cambia el estado de una evaluacion.
     *
     * @param Integer $idProveedorEvaluacion
     * @param Integer $idEstado
     * @param Integer $idGerencia
     */
    private function cambiarEstadoEvaluacion($idProveedorEvaluacion, $idGerencia, $idEstado, $motivoRechazo = null)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $proveedorEvaluacion = $em->getRepository('ADIFPortalProveedoresBundle:ProveedorEvaluacion')->find($idProveedorEvaluacion);
        if (!$proveedorEvaluacion) {
            throw $this->createNotFoundException(
                'No product found for id ' . $proveedorEvaluacion
            );
        }
        $estadoEvaluacion = $em->getRepository('ADIFPortalProveedoresBundle:EstadoEvaluacionGerencia')->findOneById($idEstado);

        switch ($idGerencia) {
            case 1:
                $proveedorEvaluacion->getEstadoEvaluacionGal($estadoEvaluacion);
                break;

            case 2:
                if ($idEstado == 3 && isset($motivoRechazo)){
                    $proveedorEvaluacion->setMotivoRechazoGalo($motivoRechazo['motivo_rechazo']);
                    $proveedorEvaluacion->setMotivoRechazoInternoGalo($motivoRechazo['motivo_rechazo_interno']);
                }
                $proveedorEvaluacion->setEstadoEvaluacionGalo($estadoEvaluacion);
                break;

            case 3:
                if ($idEstado == 3 && isset($motivoRechazo)){
                    $proveedorEvaluacion->setMotivoRechazoGcshm($motivoRechazo['motivo_rechazo']);
                    $proveedorEvaluacion->setMotivoRechazoInternoGcshm($motivoRechazo['motivo_rechazo_interno']);
                }
                $proveedorEvaluacion->setEstadoEvaluacionGcshm($estadoEvaluacion);
                break;

            case 4:
                if ($idEstado == 3 && isset($motivoRechazo)){
                    $proveedorEvaluacion->setMotivoRechazoGafFinanzas($motivoRechazo['motivo_rechazo']);
                    $proveedorEvaluacion->setMotivoRechazoInternoGafFinanzas($motivoRechazo['motivo_rechazo_interno']);
                }
                $proveedorEvaluacion->setEstadoEvaluacionGafFinanzas($estadoEvaluacion);
                break;

            case 5:
                if ($idEstado == 3 && isset($motivoRechazo)){
                    $proveedorEvaluacion->setMotivoRechazoGafImpuestos($motivoRechazo['motivo_rechazo']);
                    $proveedorEvaluacion->setMotivoRechazoInternoGafImpuestos($motivoRechazo['motivo_rechazo_interno']);
                }
                $proveedorEvaluacion->setEstadoEvaluacionGafImpuestos($estadoEvaluacion);
                break;
        }

        $em->persist($proveedorEvaluacion);
        $em->flush();
    }

    /**
     *
     *  Rechaza el formulario de la agencia dada
     *
     * @Route("/rechazarGerencia/{idEvaluacion}/{idGerencia}", name="proveedor_evaluacion_rechazar_gerencia")
     * @Method("GET")
     */
    public function rechazarGerenciaAction($idEvaluacion, $idGerencia)
    {
        $this->cambiarEstadoEvaluacion($idEvaluacion, $idGerencia, 3);
        return $this->redirect($this->generateUrl('proveedor_evaluacion'));
    }

    /**
     *
     *  Aprueba el formulario de la agencia dada
     *
     * @Route("/aprovarGerencia/{idEvaluacion}/{idGerencia}", name="proveedor_evaluacion_aprobar_gerencia")
     * @Method("GET")
     */
    public function aprobarGerenciaAction($idEvaluacion, $idGerencia)
    {
        $this->cambiarEstadoEvaluacion($idEvaluacion, $idGerencia, 2);
        return $this->redirect($this->generateUrl('proveedor_evaluacion'));
    }

    /**
     * Envia Email.
     *
     * @param String $setTo
     * @param Array $observaciones
     *
     */
    private function enviarEmail($setTo, $data, $type = null)
    {
        if ($type){
            $message = \Swift_Message::newInstance()
                ->setSubject('ADIFSE:Administrador')
                ->setFrom($this->container->getParameter('mailer_user'))
                ->setTo($setTo)
                ->setBody(
                    $this->renderView(
                        'ADIFPortalProveedoresBundle:ProveedorEvaluacion:email_'.$type.'.txt.twig', array('nombre' => $setTo, 'data' => $data)
                    ), 'text/html'
                );
            $this->get('mailer')->send($message);
        }
        return;
    }

    /**
     *
     *  Aprueba un interesado y lo da de alta como proveedor
     *
     * @Route("/altaproveedor", name="proveedor_evaluacion_alta_proveedor")
     * @Method("GET|POST")
     */
    public function altaProveedorAction(Request $request)
    {
        $ids           = $request->request->get('ids');
        $flagProveedor = $request->request->get('flag');

        $emProveedores = $this->getDoctrine()->getManager($this->getEntityManager());
        $emContable    = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
        $emCompras     = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
        $emRrhh        = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());
        $responseMessage  = "";

        //todos los interesados en ser proveedores
        foreach ($ids as $id) {

            $proveedorInteresado = $id;
//            $interesado          = $emProveedores->getRepository('ADIFPortalProveedoresBundle:Usuario')->find($id);
            $interesado          = $emProveedores->getRepository('ADIFPortalProveedoresBundle:ProveedorDatoPersonal')->find($id);

            $proveedorEvaluacion = $interesado->getProveedorEvaluacion();

            //si todas las gerencias estan aprobadas
            if ($proveedorEvaluacion->getEstadoEvaluacionGalo()->getId() == 2 &&
                $proveedorEvaluacion->getEstadoEvaluacionGafFinanzas()->getId() == 2 &&
                $proveedorEvaluacion->getEstadoEvaluacionGafImpuestos()->getId() == 2 &&
                $proveedorEvaluacion->getEstadoEvaluacionGcshm()->getId() == 2) {

                $errorActividades = 0;
                $errorLocalidades = 0;
                $errorRubros      = 0;

                $interesadoActividades = array();
                $interesadoDomicilios  = array();
                $interesadoRubros      = array();

                $proveedorActividades = $interesado->getProveedorActividad();
                $proveedorDomicilios  = $interesado->getProveedorDomicilio();
                $proveedorRubros      = $interesado->getProveedorRubro();

                //obtener las actividades del proveedor
                foreach ($proveedorActividades as $proveedorActividad) {
                    if ($proveedorActividad->getFechaBaja() == null) {
                        $codigo = $proveedorActividad->getTipoActividad()->getCodigo();
                        $denominacion = $proveedorActividad->getTipoActividad()->getDenominacion();
                        $idTipoActividad = $emProveedores->getRepository('ADIFPortalProveedoresBundle:ProveedorEvaluacion')->getTipoActividad($codigo, $denominacion);

                        //si hay actividades que no existen en SIGA
                        if (!$idTipoActividad) {
                            $errorActividades++;
                            $proveedorEvaluacionLog = new ProveedorEvaluacionLog();
                            $proveedorEvaluacionLog->setProveedorEvaluacion($proveedorEvaluacion);
                            $proveedorEvaluacionLog->setDescripcion('Actividades incompletas: codigo(' . $codigo . ') - denominacion(' . $denominacion . ')');
                            $proveedorEvaluacionLog->setActivo(true);

                            $emProveedores->persist($proveedorEvaluacionLog);
                        } else {
                            foreach ($idTipoActividad as $id) {
                                $interesadoActividades[] = array(
                                    'idTipoActividad' => $id,
                                );
                            }
                        }
                    }
                }

                //obtener los domicilios del proveedor
                foreach ($proveedorDomicilios as $proveedorDomicilio) {
                    $tipoDomicilio = $proveedorDomicilio->getTipoDomicilio();

                    //domicilio comercial o legal
                    switch ($tipoDomicilio->getId()) {
                        case ConstanteTipoDomicilio::DOMICILIO_FISCAL:
                            $pais         = $proveedorDomicilio->getPais();
                            $provincia    = $proveedorDomicilio->getProvincia();
                            $localidad    = $proveedorDomicilio->getLocalidad();
                            $codigoPostal = $proveedorDomicilio->getCodigoPostal();
                            $calle        = $proveedorDomicilio->getCalle();
                            $departamento = $proveedorDomicilio->getDepartamento();
                            $piso         = $proveedorDomicilio->getPiso();
                            $telefono     = $proveedorDomicilio->getTelefono();
                            $idLocalidad  = $emProveedores->getRepository('ADIFPortalProveedoresBundle:ProveedorEvaluacion')->getLocalidad($provincia, $localidad);

                            //si hay localidades que no existen en SIGA
                            if (!$idLocalidad) {
                                $errorLocalidades++;
                                $proveedorEvaluacionLog = new ProveedorEvaluacionLog();
                                $proveedorEvaluacionLog->setProveedorEvaluacion($proveedorEvaluacion);
                                $proveedorEvaluacionLog->setDescripcion('Provincias/localidades incompletas: provincia(' . $provincia . ') - localidad(' . $localidad . ')');
                                $proveedorEvaluacionLog->setActivo(true);

                                $emProveedores->persist($proveedorEvaluacionLog);
                            } else {
                                $interesadoDomicilios[] = array(
                                    'idTipoDomicilio' => $tipoDomicilio->getId(),
                                    'idLocalidad'     => $idLocalidad,
                                    'codigoPostal'    => $codigoPostal,
                                    'calle'           => $calle,
                                    'departamento'    => $departamento,
                                    'piso'            => $piso,
                                    'numero'          => 0,
                                );
                            }
                            break;
                        case ConstanteTipoDomicilio::DOMICILIO_REAL:
                            $pais         = $proveedorDomicilio->getPais();
                            $provincia    = $proveedorDomicilio->getProvincia();
                            $localidad    = $proveedorDomicilio->getLocalidad();
                            $codigoPostal = $proveedorDomicilio->getCodigoPostal();
                            $calle        = $proveedorDomicilio->getCalle();
                            $departamento = $proveedorDomicilio->getDepartamento();
                            $piso         = $proveedorDomicilio->getPiso();
                            $telefono     = $proveedorDomicilio->getTelefono();
                            $idLocalidad  = $emProveedores->getRepository('ADIFPortalProveedoresBundle:ProveedorEvaluacion')->getLocalidad($provincia, $localidad);
                            //si hay localidades que no existen en SIGA
                            if (!$idLocalidad) {
                                $errorLocalidades++;
                                $proveedorEvaluacionLog = new ProveedorEvaluacionLog();
                                $proveedorEvaluacionLog->setProveedorEvaluacion($proveedorEvaluacion);
                                $proveedorEvaluacionLog->setDescripcion('Provincias/localidades incompletas: provincia(' . $provincia . ') - localidad(' . $localidad . ')');
                                $proveedorEvaluacionLog->setActivo(true);

                                $emProveedores->persist($proveedorEvaluacionLog);
                            } else {
                                $interesadoDomicilios[] = array(
                                    'idTipoDomicilio' => $tipoDomicilio->getId(),
                                    'idLocalidad'     => $idLocalidad,
                                    'codigoPostal'    => $codigoPostal,
                                    'calle'           => $calle,
                                    'departamento'    => $departamento,
                                    'piso'            => $piso,
                                    'numero'          => 0,
                                );
                            }
                            break;
                        case ConstanteTipoDomicilio::DOMICILIO_LEGAL:
                            $pais         = $proveedorDomicilio->getPais();
                            $provincia    = $proveedorDomicilio->getProvincia();
                            $localidad    = $proveedorDomicilio->getLocalidad();
                            $codigoPostal = $proveedorDomicilio->getCodigoPostal();
                            $calle        = $proveedorDomicilio->getCalle();
                            $departamento = $proveedorDomicilio->getDepartamento();
                            $piso         = $proveedorDomicilio->getPiso();
                            $telefono     = $proveedorDomicilio->getTelefono();
                            $idLocalidad  = $emProveedores->getRepository('ADIFPortalProveedoresBundle:ProveedorEvaluacion')->getLocalidad($provincia, $localidad);
                            //si hay localidades que no existen en SIGA
                            if (!$idLocalidad) {
                                $errorLocalidades++;
                                $proveedorEvaluacionLog = new ProveedorEvaluacionLog();
                                $proveedorEvaluacionLog->setProveedorEvaluacion($proveedorEvaluacion);
                                $proveedorEvaluacionLog->setDescripcion('Provincias/localidades incompletas: provincia(' . $provincia . ') - localidad(' . $localidad . ')');
                                $proveedorEvaluacionLog->setActivo(true);

                                $emProveedores->persist($proveedorEvaluacionLog);
                            } else {
                                $interesadoDomicilios[] = array(
                                    'idTipoDomicilio' => $tipoDomicilio->getId(),
                                    'idLocalidad'     => $idLocalidad,
                                    'codigoPostal'    => $codigoPostal,
                                    'calle'           => $calle,
                                    'departamento'    => $departamento,
                                    'piso'            => $piso,
                                    'numero'          => 0,
                                );
                            }
                            break;
                        case ConstanteTipoDomicilio::DOMICILIO_EXTERIOR:
                            $pais         = $proveedorDomicilio->getPais();
                            $provincia    = $proveedorDomicilio->getProvincia();
                            $localidad    = $proveedorDomicilio->getLocalidad();
                            $codigoPostal = $proveedorDomicilio->getCodigoPostal();
                            $calle        = $proveedorDomicilio->getCalle();
                            $departamento = $proveedorDomicilio->getDepartamento();
                            $piso         = $proveedorDomicilio->getPiso();
                            $telefono     = $proveedorDomicilio->getTelefono();
                            $idLocalidad  = $emProveedores->getRepository('ADIFPortalProveedoresBundle:ProveedorEvaluacion')->getLocalidad($provincia, $localidad);

                            /*Reflejo 03/10 se charlo este tema, y se llego a la conclusion de que no se debe
                            verificar la localidad en un DOMICILIO_EXTERIOR dejo el false para que quede
                            constancia de como se realizaba anteriormente por si se debe en algun momento
                            recuperar.
                            */
                            //si hay localidades que no existen en SIGA
                            if (false) {
                                $errorLocalidades++;
                                $proveedorEvaluacionLog = new ProveedorEvaluacionLog();
                                $proveedorEvaluacionLog->setProveedorEvaluacion($proveedorEvaluacion);
                                $proveedorEvaluacionLog->setDescripcion('Provincias/localidades incompletas: provincia(' . $provincia . ') - localidad(' . $localidad . ')');
                                $proveedorEvaluacionLog->setActivo(true);

                                $emProveedores->persist($proveedorEvaluacionLog);
                            } else {
                                $interesadoDomicilios[] = array(
                                    'idTipoDomicilio' => $tipoDomicilio->getId(),
                                    'idLocalidad'     => $idLocalidad,
                                    'codigoPostal'    => $codigoPostal,
                                    'calle'           => $calle,
                                    'departamento'    => $departamento,
                                    'piso'            => $piso,
                                    'numero'          => 0,
                                );
                            }
                            break;
                    }
                }

                //obtener los rubros del proveedor
                foreach ($proveedorRubros as $proveedorRubro) {
                    if($proveedorRubro->getFechaBaja() == NULL){
                        $rubro   = $proveedorRubro->getRubroClase()->getRubro()->getDenominacion();
                        $idRubro = $emProveedores->getRepository('ADIFPortalProveedoresBundle:ProveedorEvaluacion')->getRubro($rubro);

                        //si hay rubros que no existen en SIGA
                        if (!$idRubro) {
                            $errorRubros++;
                            $proveedorEvaluacionLog = new ProveedorEvaluacionLog();
                            $proveedorEvaluacionLog->setProveedorEvaluacion($proveedorEvaluacion);
                            $proveedorEvaluacionLog->setDescripcion('Rubros incompletos: rubro(' . $rubro . '), Fecha: '.date('d/m/Y H:i:s').'');
                            $proveedorEvaluacionLog->setActivo(true);

                            $emProveedores->persist($proveedorEvaluacionLog);
                        } else {
                            foreach ($idRubro as $id) {
                                $interesadoRubros[] = array(
                                    'idRubro' => $id,
                                );
                            }
                        }
                    }
                }

                //si hay errores en los datos ingresados en preinscripcion (no existen en SIGA)
                if ($errorActividades > 0 || $errorLocalidades > 0 || $errorRubros > 0) {

                    $emProveedores->flush();

                    $descripcion            = '';
                    $proveedorEvaluacionLog = $proveedorEvaluacion->getProveedorEvaluacionLog(array('activo' => false));
                    foreach ($proveedorEvaluacionLog as $log) {
                        $descripcion .= $log->getDescripcion() . "<br>";
                    }

                    $responseMessage .=$descripcion;

                } else {

                    $interesadoDatoContactos = $interesado->getProveedorDatoContacto();

                    $clienteProveedorService = $this->get('adif.cliente_proveedor_service');

                    if($flagProveedor == 1 || !empty($interesado->getIdProveedorAsoc())){
                        $idAsoc = $interesado->getIdProveedorAsoc();
                        $proveedorNew        = $emCompras->getRepository('ADIFComprasBundle:Proveedor')->find($idAsoc);
                        $clienteProveedorNew = $emCompras->getRepository('ADIFComprasBundle:ClienteProveedor')->find($proveedorNew->getClienteProveedor()->getId());
                        $cliente = new Cliente();
                    } else {
                        $proveedorNew        = new Proveedor();
                        $clienteProveedorNew = new ClienteProveedor();
                        $cliente             = new Cliente();
                    }

                    $clienteProveedorNew->setCodigo($clienteProveedorService->getSiguienteCodigoClienteProveedor());

                    //es proveedor extranjero
                    if ($interesado->getTipoProveedor()->esExtranjero()) {
                        $clienteProveedorNew->setCodigoIdentificacion($interesado->getNumeroIdTributaria());
                        $clienteProveedorNew->setEsExtranjero(true);
                        $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                            ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::PROVEEDOR_EXTRANJERO);
                    } else {
                        $clienteProveedorNew->setCUIT($interesado->getCuit());
                        $clienteProveedorNew->setEsExtranjero(false);
                        $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                            ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::PROVEEDOR_NACIONAL);

                        //Reflejo 14/09 DatosImpositivos de Preinscripcion a DatosImpositivos de Proveedores.
                        $vec_tipoIva = [
                            1 => 11,
                            2 => 14,
                            3 => 12
                        ];
                        //IVA.
                        $proveedorIva = $interesado->getProveedorDatoImpositivo()->getProveedorIva();

                        $tipoivaIva = $vec_tipoIva[$proveedorIva->getTipoIva()->getId()];
                        $exentoIva = $proveedorIva->getExento(); //OK
                        $retencionIva = $proveedorIva->getRetencion(); //?
                        $otrosIva = $proveedorIva->getOtros(); //SUPUESTO

                        //GANANCIAS.
                        $proveedorGanancias = $interesado->getProveedorDatoImpositivo()->getProveedorGanancias();

                        $tipoivaGanancias = $vec_tipoIva[$proveedorGanancias->getTipoIva()->getId()];
                        $exentoGanancias = $proveedorGanancias->getExento(); //OK
                        $retencionGanancias = $proveedorGanancias->getRetencion(); //?
                        $otrosGanancias = $proveedorGanancias->getOtros(); //SUPUESTO

                        //BRUTOS.
                        $proveedorIibb = $interesado->getProveedorDatoImpositivo()->getProveedorIibb();

                        $tipoivaBrutos = $vec_tipoIva[$proveedorIibb->getTipoIva()->getId()];
                        $exentoBrutos = $proveedorIibb->getExento(); //OK
                        $numeroBrutos = $proveedorIibb->getNumeroInscripcion(); //SUPUESTO
                        $retencionBrutos = $proveedorIibb->getRetencion(); //?
                        $otrosBrutos = $proveedorIibb->getOtros(); //SUPUESTO

                        //SUSS
                        $proveedorSuss = $interesado->getProveedorDatoImpositivo()->getProveedorSuss();

                        $tipoivaSuss = $vec_tipoIva[$proveedorSuss->getTipoIva()->getId()];
                        $personalSuss = $proveedorSuss->getPersonalACargo(); //?
                        $exentoSuss = $proveedorSuss->getExento(); //OK
                        $retencionSuss = $proveedorSuss->getRetencion(); //?

                        $DatosImpositivos = new DatosImpositivos();
                        $DatosImpositivos->setCondicionIVA($emContable->getRepository('ADIFContableBundle:TipoResponsable')->find($tipoivaIva)); //SUPUESTO
                        $DatosImpositivos->setExentoIVA($exentoIva); //OK
                        $DatosImpositivos->setObservacionExentoIVA($otrosIva); //SUPUESTO
                        $DatosImpositivos->setCondicionGanancias($emContable->getRepository('ADIFContableBundle:TipoResponsable')->find($tipoivaGanancias)); //SUPUESTO
                        $DatosImpositivos->setExentoGanancias($exentoGanancias); // OK
                        $DatosImpositivos->setNumeroIngresosBrutos($numeroBrutos); //SUPUESTO
                        $DatosImpositivos->setExentoIngresosBrutos($exentoBrutos); //OK
                        $DatosImpositivos->setCondicionIngresosBrutos($emContable->getRepository('ADIFContableBundle:TipoResponsable')->find($tipoivaBrutos)); //SUPUESTO
                        $DatosImpositivos->setCondicionSUSS($emContable->getRepository('ADIFContableBundle:TipoResponsable')->find($tipoivaSuss)); //SUPUESTO
                        $DatosImpositivos->setExentoSUSS($exentoSuss); //OK
                        $clienteProveedorNew->setDatosImpositivos($DatosImpositivos);

                        //Reflejo 14/09 entidadBancaria de Preinscripcion -> Cuenta de Proveedor.
                        $idbancopre = $interesado->getProveedorDatoBancario()->getIdEntidadBancaria();
                        $cbupre = $interesado->getProveedorDatoBancario()->getCbu();
                        $nsucursalpre = $interesado->getProveedorDatoBancario()->getNumeroSucursal();
                        $ncuentapre = $interesado->getProveedorDatoBancario()->getNumeroCuenta();

                        $flagErrorCuentaBancaria = 0;
                        if($flagProveedor == 1){
                            $cuentaBancaria = $emRrhh->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaPersona')->findOneBy(array('cbu' => $cbupre));

                            if($cuentaBancaria == null){
                                $flagErrorCuentaBancaria = 1;
                                $cuentaBancaria = new CuentaBancariaPersona();
                            }
                        }else{
                            $cuentaBancaria = new CuentaBancariaPersona();
                        }

                        $cuentaBancaria->setIdBanco($emRrhh->getRepository('ADIFRecursosHumanosBundle:Banco')->findOneById($idbancopre));
                        $cuentaBancaria->setIdTipoCuenta($emRrhh->getRepository('ADIFRecursosHumanosBundle:TipoCuenta')->find(1)); //Hardcodeado.

                        //Reflejo 17/09 se catchea la posible excepcion de un CBU ya en uso.
                        try {
                            $cuentaBancaria->setCbu($cbupre);
                            if($cbupre != $cuentaBancaria->getCbu()){
                                if($flagProveedor != "1" || $flagErrorCuentaBancaria = 1){

                                    $emRrhh->persist($cuentaBancaria);
                                }
                                $emRrhh->flush();
                            }
                        }
                        catch(UniqueConstraintViolationException $e)
                        {
                            $response = array('result' => "NOK", 'message' => "El CBU ingresado se encuentra en uso");
                            return new JsonResponse($response);
                        }
                        $proveedorNew->setCuenta($cuentaBancaria);
                        $emRrhh->persist($cuentaBancaria);
                        $emRrhh->flush();
                    }

                    //Si es persona física
                    if ($interesado->getTipoPersona() instanceof TipoPersona &&  $interesado->getTipoPersona()->getId() == 1) {
                        $razonSocial = $interesado->getNombre() . ' ' . $interesado->getApellido();
                    } else {
                        $razonSocial = $interesado->getRazonSocial();
                    }

                    $clienteProveedorNew->setRazonSocial($razonSocial);

                    // Obtengo el TipoMoneda de curso legal
                    $tipoMonedaMCL = $emContable->getRepository('ADIFContableBundle:TipoMoneda')->
                    findOneBy(array('esMCL' => true), array('id' => 'desc'), 1, 0);

                    // Obtengo el EstadoProveedor cuya denominacion sea igual a "Activo"
                    $estadoProveedorActivo = $emCompras->getRepository('ADIFComprasBundle:EstadoProveedor')->
                    findOneBy(array('denominacionEstadoProveedor' => 'Activo'), array('id' => 'desc'), 1, 0);

                    if (!$interesado->getTipoProveedor()->esExtranjero())
                        $proveedorNew->setIdCuenta($cuentaBancaria->getId());
                    $proveedorNew->setCuentaContable($cuentaContable);
                    $proveedorNew->setEstadoProveedor($estadoProveedorActivo);
                    $proveedorNew->setNacionalidad($interesado->getPaisRadicacion());
                    $proveedorNew->setTipoMoneda($tipoMonedaMCL);

                    //datos de contacto del proveedor
                    $interesadoContactosProveedor[] = array(
                        'nombre'         => $interesado->getNombre() . ' ' . $interesado->getApellido(),
                        'idTipoContacto' => 3, //email
                        'descripcion'    => $interesado->getEmail(),
                        'observaciones'  => null,
                    );

                    //datos de contacto de contactos del proveedor
                    foreach ($interesadoDatoContactos as $datoContacto) {
                        $interesadoContactosProveedor[] = array(
                            'nombre'         => $datoContacto->getNombre() . ' ' . $datoContacto->getApellido(),
                            'idTipoContacto' => 1, //telefono
                            'descripcion'    => $datoContacto->getTelefono(),
                            'observaciones'  => $datoContacto->getArea() . ' - ' . $datoContacto->getPosicion(),
                        );
                    }

                    if($flagProveedor == "1"){
                        $contactosProveedorOriginales = $proveedorNew->getContactosProveedor();

                        if($contactosProveedorOriginales){
                            // Por cada contactoProveedor original
                            foreach ($contactosProveedorOriginales as $contactoProveedor) {

                                $proveedorNew->removeContactosProveedor($contactoProveedor);

                                $contactoProveedor->getDatosContacto()->clear();

                                $emCompras->remove($contactoProveedor);
                            }

                            $emCompras->persist($proveedorNew);
                        }
                    }


                    //agregar datos de contacto
                    foreach ($interesadoContactosProveedor as $contacto) {

                        $contactoProveedor = new ContactoProveedor();
                        $datoContacto      = new DatoContacto();
                        $tipoContacto      = $emCompras->getRepository('ADIFComprasBundle:TipoContacto')->findBy(array('id' => $contacto['idTipoContacto']));

                        $datoContacto->setTipoContacto($tipoContacto[0]);
                        $datoContacto->setDescripcionDatoContacto($contacto['descripcion']);

                        $contactoProveedor->setNombre($contacto['nombre']);
                        $contactoProveedor->setObservacion($contacto['observaciones']);
                        $contactoProveedor->setProveedor($proveedorNew);

                        $contactoProveedor->addDatosContacto($datoContacto);
                        $datoContacto->addContactoProveedor($contactoProveedor);

                        $emCompras->persist($contactoProveedor);
                        $emCompras->persist($datoContacto);
                    }

                    $arrActividadesClienteProveedor = [];
                    $actividadesClienteProveedor = $clienteProveedorNew->getActividades();

                    if($actividadesClienteProveedor){
                        foreach($actividadesClienteProveedor as $actividadClienteProveedor){
                            array_push($arrActividadesClienteProveedor, $actividadClienteProveedor->getId());
                        }
                    }

                    //agregar actividades del proveedor
                    foreach ($interesadoActividades as $actividad) {

                        if(!in_array($actividad['idTipoActividad']["id"], $arrActividadesClienteProveedor)){
                            unset( $arrActividadesClienteProveedor[array_search($actividad['idTipoActividad']["id"], $arrActividadesClienteProveedor)]);
                            $tipoActividad = $emCompras->getRepository('ADIFComprasBundle:TipoActividad')->findBy(array('id' => $actividad['idTipoActividad']));
                            $clienteProveedorNew->addActividad($tipoActividad[0]);
                        }
                    }

                    //Elimino las actividades que no van mas
                    foreach($arrActividadesClienteProveedor as $actividadcp){
                        $tipoActividad = $emCompras->getRepository('ADIFComprasBundle:TipoActividad')->findBy(array('id' => $actividadcp));
                        $clienteProveedorNew->removeActividad($tipoActividad[0]);
                    }

                    $arrRubrosProveedor = [];
                    $rubrosProveedor = $proveedorNew->getRubros();

                    if($rubrosProveedor){
                        foreach($rubrosProveedor as $rubroProveedor){
                            array_push($arrRubrosProveedor, $rubroProveedor->getId());
                        }
                    }

                    //agregar rubros del proveedor
                    foreach ($interesadoRubros as $rubro) {

                        if(!in_array($rubro['idRubro']["id"], $arrRubrosProveedor)){
                            unset( $arrRubrosProveedor[array_search($rubro['idRubro']["id"], $arrRubrosProveedor)]);
                            $rubro = $emCompras->getRepository('ADIFComprasBundle:Rubro')->findBy(array('id' => $rubro['idRubro']));
                            $proveedorNew->addRubro($rubro[0]);
                        }
                    }

                    //Elimino los rubros
                    foreach($arrRubrosProveedor as $rubrocp){
                        $rubro = $emCompras->getRepository('ADIFComprasBundle:Rubro')->findBy(array('id' => $rubrocp));
                        $proveedorNew->removeRubro($rubro[0]);
                    }

                    //agregar domicilios
                    foreach ($interesadoDomicilios as $domicilio) {
                        $domicilioNew = new Domicilio();
                        $localidad    = $emRrhh->getRepository('ADIFRecursosHumanosBundle:Localidad')->findBy(array('id' => $domicilio['idLocalidad']));

                        $domicilioNew->setCalle($domicilio['calle']);
                        $domicilioNew->setNumero($domicilio['numero']);
                        $domicilioNew->setPiso($domicilio['piso']);
                        $domicilioNew->setDepto($domicilio['departamento']);
                        if($localidad == NULL)
                            $domicilioNew->setLocalidad = NULL;
                        else
                            $domicilioNew->setLocalidad($localidad[0]);
                        $emRrhh->persist($domicilioNew);
                        $emRrhh->flush();

                        switch ($interesado->getTipoProveedor()->getId()){
                            case 1:
                                switch ($domicilio['idTipoDomicilio']) {
                                    case ConstanteTipoDomicilio::DOMICILIO_FISCAL:
                                        $clienteProveedorNew->setDomicilioLegal($domicilioNew);
                                        $clienteProveedorNew->setIdDomicilioLegal($domicilioNew->getId());
                                        break;
                                    case ConstanteTipoDomicilio::DOMICILIO_REAL:
                                        $clienteProveedorNew->setDomicilioComercial($domicilioNew);
                                        $clienteProveedorNew->setIdDomicilioComercial($domicilioNew->getId());
                                        break;
                                }
                                break;
                            case 2:
                            case 3:
                                switch ($domicilio['idTipoDomicilio']) {
                                    case ConstanteTipoDomicilio::DOMICILIO_FISCAL:
                                        $clienteProveedorNew->setDomicilioComercial($domicilioNew);
                                        $clienteProveedorNew->setIdDomicilioComercial($domicilioNew->getId());
                                        break;
                                    case ConstanteTipoDomicilio::DOMICILIO_LEGAL:
                                        $clienteProveedorNew->setDomicilioLegal($domicilioNew);
                                        $clienteProveedorNew->setIdDomicilioLegal($domicilioNew->getId());
                                }
                                break;
                            case 4:
                            case 5:
                                switch ($domicilio['idTipoDomicilio']) {
                                    case ConstanteTipoDomicilio::DOMICILIO_FISCAL:
                                        $clienteProveedorNew->setDomicilioLegal($domicilioNew);
                                        $clienteProveedorNew->setIdDomicilioLegal($domicilioNew->getId());
                                        break;
                                    case ConstanteTipoDomicilio::DOMICILIO_EXTERIOR:
                                        $clienteProveedorNew->setDomicilioComercial($domicilioNew);
                                        $clienteProveedorNew->setIdDomicilioComercial($domicilioNew->getId());
                                }
                                break;
                        }
                    }

                    //obtengo los proveedores que conforman la UTE
                    $interesadoProveedorUte = $interesado->getProveedorUte();
                    //si es UTE
                    if ($interesadoProveedorUte) {
                        $proveedorNew->setEsUTE(true);
                        $miembrosUte = $interesadoProveedorUte->getProveedorUteMiembros();
                        foreach ($miembrosUte as $miembroUte) {
                            $clienteProveedor    = $emCompras->getRepository('ADIFComprasBundle:ClienteProveedor')->findOneBy(array('CUIT' => $miembroUte->getCuit()));
                            $proveedorMiembroUte = $clienteProveedor->getProveedores()->first();

                            if($flagProveedor == 1){
                                $proveedorUTE = $emCompras->getRepository('ADIFComprasBundle:ProveedorUTE')->find($interesado->getProveedorUte()->getId());
                            }else{
                                $proveedorUTE = new ProveedorUTE();
                            }
                            $proveedorUTE->setProveedorUTE($proveedorNew);
                            $proveedorUTE->setProveedor($proveedorMiembroUte);
                            $proveedorUTE->setPorcentajeRemuneracion($miembroUte->getParticipacionRemunerativa());
                            $proveedorUTE->setPorcentajeGanancia($miembroUte->getParticipacionGanancias());
                            if($flagProveedor != "1"){
                                $emCompras->persist($proveedorUTE);
                            }
                        }
                    } else {
                        $proveedorNew->setEsUTE(false);
                    }

                    if($flagProveedor != "1"){
                        $emCompras->persist($clienteProveedorNew);
                    }

                    $estado = $emProveedores->getRepository('ADIFPortalProveedoresBundle:EstadoEvaluacion')->findOneById(2); //Estado Aprobado
                    $proveedorEvaluacion->setEstadoEvaluacion($estado);

                    $proveedorEvaluacionLog = $proveedorEvaluacion->getProveedorEvaluacionLog();
                    foreach ($proveedorEvaluacionLog as $log) {
                        $log->setActivo(false);
                    }
                    
                    if($flagProveedor != "1"){
                        $evaluacionProveedor = $proveedorNew->getEvaluacionProveedor();

                        // Obtengo los AspectoEvaluacion cargados en la BBDD
                        $aspectosEvaluacion = $emCompras->getRepository('ADIFComprasBundle:AspectoEvaluacion')->findAll();

                        // Por cada AspectoEvaluacion, creo un EvaluacionAspectoProveedor
                        foreach ($aspectosEvaluacion as $aspectoEvaluacion) {

                            $evaluacionAspectoProveedor = new EvaluacionAspectoProveedor();

                            $evaluacionAspectoProveedor->setEvaluacionProveedor($evaluacionProveedor);
                            $evaluacionAspectoProveedor->setAspectoEvaluacion($aspectoEvaluacion);

                            $evaluacionProveedor->addEvaluacionesAspecto($evaluacionAspectoProveedor);
                        }
                        $proveedorNew->getEvaluacionProveedor()->setProveedor($proveedorNew);
                        $emCompras->persist($evaluacionProveedor);
                    }

                    $interesado->setProveedor(true);

                    $proveedorNew->setClienteProveedor($clienteProveedorNew);

                    $emProveedores->persist($proveedorEvaluacion);

                    $tipoMoneda = $emContable->getRepository(TipoMoneda::class)->find(21);
                    $cliente->setClienteProveedor($clienteProveedorNew);
                    $cliente->setTipoMoneda($tipoMoneda);

                    if($flagProveedor != "1"){
                        $emCompras->persist($proveedorNew);
                        $emCompras->persist($cliente);
                    }
                    $emCompras->persist($proveedorNew);
                    $emCompras->flush();

                    /**
                     * creo el historico fiscal, lo que verga sea
                     */

                    $this->crearHistoricoCondicionFiscal($emCompras, $proveedorNew);

                    $interesado->setProveedorAsoc($proveedorNew);
                    if($flagProveedor != "1"){
                        $emProveedores->persist($interesado);
                    }
                    $emProveedores->flush();
                    //En reemplazo de $interesado->setProveedorAsoc();
                    $sql = "UPDATE adif_proveedores.proveedor_dato_personal SET proveedor_id = :proveedor_id WHERE id = :aux";
                    $params = array('proveedor_id' => $proveedorNew->getId(), 'aux'=> $interesado->getId());
                    $em = $this->getDoctrine()->getManager($this->getEntityManager());
                    $stmt = $em->getConnection()->prepare($sql);
                    $stmt->execute($params);

                    //se envia email al interesado
                    $dataTwig = array('aprobarProveedor' => true);
                    $this->enviarEmail($interesado->getUsername(),$dataTwig, 'aprobador');

                }

            } else {

                $identificador = $interesado->getRazonSocial() .' - ' . $interesado->getCuit();

                if ($proveedorEvaluacion->getEstadoEvaluacionGalo()->getId() != 2 ){
                    $responseMessage .= 'Para el interesado '.  $identificador .' es necesario aprobar la gerencia GALO.<br>';
                }

                if ($proveedorEvaluacion->getEstadoEvaluacionGafFinanzas()->getId() != 2) {
                    $responseMessage .= 'Para el interesado '.  $identificador .' es necesario aprobar la gerencia GAF Finanzas.<br>';
                }

                if ($proveedorEvaluacion->getEstadoEvaluacionGafImpuestos()->getId() != 2){
                    $responseMessage .= 'Para el interesado '.  $identificador .' es necesario aprobar la gerencia GAF Impuestos.<br>';
                }
                if ($proveedorEvaluacion->getEstadoEvaluacionGcshm()->getId() != 2) {
                    $responseMessage .= 'Para el interesado '.  $identificador .' es necesario aprobar la gerencia GCSHM.<br>';
                }

            }

        }

        // establecemos el código de respuesta
        if ($responseMessage == ""){
            $responseCode = "OK";
        } else {
            $responseCode = "NOK";
        }

        $response = array('result' => $responseCode, 'message' => $responseMessage);

        return new JsonResponse($response);
    }

    /**
     *
     *  Rechaza un Interesado
     *
     * @Route("/rechazarproveedor", name="proveedor_evaluacion_rechazar_proveedor")
     * @Method("GET|POST")
     */
    public function rechazarProveedorAction(Request $request)
    {
        $ids = $request->request->get('ids');
        $msg = $request->request->get('motivoRechazo');
        $msgInterno = $request->request->get('motivoRechazoInterno');
        $em  = $this->getDoctrine()->getManager($this->getEntityManager());

        foreach ($ids as $value) {
            $entity              = $em->getRepository('ADIFPortalProveedoresBundle:ProveedorDatoPersonal')->find($value);

            $ProveedorEvaluacion = $entity->getProveedorEvaluacion();

            $estado = $em->getRepository('ADIFPortalProveedoresBundle:EstadoEvaluacion')->findOneById(3); // Estado Rechazado
            $ProveedorEvaluacion->setEstadoEvaluacion($estado);
            $ProveedorEvaluacion->setMotivoRechazo($msg);
            $ProveedorEvaluacion->setMotivoRechazoInterno($msgInterno);

            $em->persist($ProveedorEvaluacion);
            $em->flush();

            //se envia email al interesado
            $dataTwig = array('rechazarProveedor' => true);
            $this->enviarEmail($entity->getUsername(),$dataTwig, 'aprobador');

            $response = array('result' => 'OK');
        }

        return new JsonResponse($response);
    }

    /**
     *
     *  Asociar un contacto a un Interesado
     *
     * @Route("/asociarcontactoproveedor", name="proveedor_evaluacion_asociar_contacto")
     * @Method("POST")
     */
    public function asociarContactoAction(Request $request)
    {
        $ids = $request->request->get('ids');
        $data = $request->request->all();
        $em  = $this->getDoctrine()->getManager($this->getEntityManager());

        foreach ($ids as $value) {
            $provDatoPersonal = $em->getRepository('ADIFPortalProveedoresBundle:ProveedorDatoPersonal')->find($value);
            $idUsuario = $provDatoPersonal->getUsuario();


            $provDatoContacto = new ProveedorDatoContacto();
//            $provDatoContacto->setUsuario($idUsuario);
            $provDatoContacto->setNombre($data['nombre']);
            $provDatoContacto->setApellido($data['apellido']);
            $provDatoContacto->setArea($data['area']);
            $provDatoContacto->setPosicion($data['posicion']);
            $provDatoContacto->setEmail($data['email']);
            $provDatoContacto->setTelefono($data['telefono']);
            $provDatoContacto->setIdDatoPersonal($provDatoPersonal);

            $em->persist($provDatoContacto);
            $em->flush();

            $response = array('result' => 'OK');
        }

        return new JsonResponse($response);
    }

    /**
     * Cambia el estado del Timeline a incompleto cuando se observa un bloque.
     *
     * @param Integer $idTipoObservacion
     * @param Integer $idProveedorEvaluacion
     */
    private function guardarTimeline($tipoObservacion, $idDatoPersonal)
    {
        switch ($tipoObservacion)
        {
            case 'datos_contacto':
                $denTimeline = 'timeline_dato_contacto';
                break;
            case 'rubro':
                $denTimeline = 'timeline_rubro';
                break;
            case 'actividad':
                $denTimeline = 'timeline_actividades';
                break;
            case 'domicilio_real':
                $denTimeline = 'timeline_domicilio_real';
                break;
            case 'domicilio_legal':
                $denTimeline = 'timeline_domicilio_legal';
                break;
            case 'domicilio_fiscal':
                $denTimeline = 'timeline_domicilio_fiscal';
                break;
            case 'domicilio_contractual':
                $denTimeline = 'timeline_domicilio_contractual';
                break;
            case 'domicilio_exterior':
                $denTimeline = 'timeline_domicilio_exterior';
                break;
            case 'representante_apoderado':
                $denTimeline = 'timeline_representantes_apoderados';
                break;
            case 'datos_bancarios':
                $denTimeline = 'timeline_datos_bancarios';
                break;
            case 'datos_persona_fisica':
                $denTimeline = 'timeline_persona_fisica';
                break;
            case 'datos_persona_juridica':
                $denTimeline = 'timeline_persona_juridica';
                break;
            case 'datos_ute':
                $denTimeline = 'timeline_contratos_ute';
                break;
            case 'datos_persona_fisica_extranjera':
                $denTimeline = 'timeline_persona_fisica_extranjera';
                break;
            case 'datos_persona_Juridica_extranjera':
                $denTimeline = 'timeline_persona_juridica_extranjera';
                break;
            case 'datos_impositivos':
            case 'iva':
            case 'suss':
            case 'ganancias':
            case 'ingresos_brutos':
            case 'cae_cai':
                $denTimeline = 'timeline_datos_impositivos';
                break;
            case 'datos_gcshm':
                $denTimeline = 'timeline_gcshm';
                break;

            default:
                $denTimeline = null;
                break;
        }
        if ($denTimeline)
        {
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $tipoTimeline = $em->getRepository('ADIFPortalProveedoresBundle:TipoTimeline')->findOneBy(['denominacion' => $denTimeline]);

            $proveedorTimeline = $em->getRepository('ADIFPortalProveedoresBundle:ProveedorTimeline')->findOneBy([
                'denominacion' => $tipoTimeline->getId(),
                'idDatoPersonal' => $idDatoPersonal
            ]);

            if ($proveedorTimeline instanceof ProveedorTimeline)
            {
                $proveedorTimeline->setStatus('incompleto');
                $proveedorTimeline->setIdStatus(2);

                $em->persist($proveedorTimeline);
                $em->flush();
            }
        }
    }

    /**
     *
     * @param type $em
     * @param Proveedor $proveedor
     */
    private function crearHistoricoCondicionFiscal($em, Proveedor $proveedor) {

// ClienteProveedorHistoricoGanancias
        $historicoProveedorGanancias = new \ADIF\ComprasBundle\Entity\ClienteProveedorHistoricoGanancias();

        $historicoProveedorGanancias->setFechaDesde(new \DateTime());
        $historicoProveedorGanancias->setClienteProveedor($proveedor->getClienteProveedor());

        $historicoProveedorGanancias->setCondicion($proveedor->getClienteProveedor()->getCondicionGanancias());
        $historicoProveedorGanancias->setExento($proveedor->getClienteProveedor()->getExentoGanancias());
        $historicoProveedorGanancias->setPasibleRetencion($proveedor->getPasibleRetencionGanancias());

        if (null != $proveedor->getCertificadoExencionGanancias()) {
            $historicoProveedorGanancias->setCertificadoExencion(clone $proveedor->getCertificadoExencionGanancias());
        }

        $em->persist($historicoProveedorGanancias);
        /* FIN ClienteProveedorHistoricoGanancias */


// HistoricoProveedorIIBB
        $historicoProveedorIIBB = new \ADIF\ComprasBundle\Entity\ClienteProveedorHistoricoIIBB();

        $historicoProveedorIIBB->setFechaDesde(new \DateTime());
        $historicoProveedorIIBB->setClienteProveedor($proveedor->getClienteProveedor());

        $historicoProveedorIIBB->setCondicion($proveedor->getClienteProveedor()->getCondicionIngresosBrutos());
        $historicoProveedorIIBB->setExento($proveedor->getClienteProveedor()->getExentoIngresosBrutos());
        $historicoProveedorIIBB->setPasibleRetencion($proveedor->getPasibleRetencionIngresosBrutos());

        if (null != $proveedor->getCertificadoExencionIngresosBrutos()) {
            $historicoProveedorIIBB->setCertificadoExencion(clone $proveedor->getCertificadoExencionIngresosBrutos());
        }

        $convenioMultilateral = $proveedor->getClienteProveedor()
            ->getConvenioMultilateralIngresosBrutos();

        if (null != $convenioMultilateral) {

            $historicoProveedorIIBB->setJurisdiccion($convenioMultilateral->getJurisdiccion());
            $historicoProveedorIIBB->setPorcentajeAplicacionCABA($convenioMultilateral
                ->getPorcentajeAplicacionCABA()
            );
        }

        $em->persist($historicoProveedorIIBB);
        /* FIN HistoricoProveedorIIBB */


// ClienteProveedorHistoricoIVA
        $historicoProveedorIVA = new \ADIF\ComprasBundle\Entity\ClienteProveedorHistoricoIVA();

        $historicoProveedorIVA->setFechaDesde(new \DateTime());
        $historicoProveedorIVA->setClienteProveedor($proveedor->getClienteProveedor());

        $historicoProveedorIVA->setCondicion($proveedor->getClienteProveedor()->getCondicionIVA());
        $historicoProveedorIVA->setExento($proveedor->getClienteProveedor()->getExentoIVA());
        $historicoProveedorIVA->setPasibleRetencion($proveedor->getPasibleRetencionIVA());

        if (null != $proveedor->getCertificadoExencionIVA()) {
            $historicoProveedorIVA->setCertificadoExencion(clone $proveedor->getCertificadoExencionIVA());
        }

        $em->persist($historicoProveedorIVA);
        /* FIN ClienteProveedorHistoricoIVA */


// ClienteProveedorHistoricoSUSS
        $historicoProveedorSUSS = new \ADIF\ComprasBundle\Entity\ClienteProveedorHistoricoSUSS();

        $historicoProveedorSUSS->setFechaDesde(new \DateTime());
        $historicoProveedorSUSS->setClienteProveedor($proveedor->getClienteProveedor());

        $historicoProveedorSUSS->setCondicion($proveedor->getClienteProveedor()->getCondicionSUSS());
        $historicoProveedorSUSS->setExento($proveedor->getClienteProveedor()->getExentoSUSS());
        $historicoProveedorSUSS->setPasibleRetencion($proveedor->getPasibleRetencionSUSS());

        if (null != $proveedor->getCertificadoExencionSUSS()) {
            $historicoProveedorSUSS->setCertificadoExencion(clone $proveedor->getCertificadoExencionSUSS());
        }

        $em->persist($historicoProveedorSUSS);
        /* FIN ClienteProveedorHistoricoSUSS */
        $em->flush();
    }
}