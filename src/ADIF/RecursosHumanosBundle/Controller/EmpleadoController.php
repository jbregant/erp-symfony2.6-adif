<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\BaseBundle\Entity\AdifApi;
use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\RecursosHumanosBundle\Entity\ContactoEmergencia;
use ADIF\RecursosHumanosBundle\Entity\Empleado;
use ADIF\RecursosHumanosBundle\Entity\EmpleadoNovedad;
use ADIF\RecursosHumanosBundle\Entity\EmpleadoSectorHistorico;
use ADIF\RecursosHumanosBundle\Entity\EmpleadoSubcategoriaHistorico;
use ADIF\RecursosHumanosBundle\Entity\EstudioEmpleado;
use ADIF\RecursosHumanosBundle\Entity\Familiar;
use ADIF\RecursosHumanosBundle\Entity\Persona;
use ADIF\RecursosHumanosBundle\Entity\TipoContrato;
use ADIF\RecursosHumanosBundle\Entity\TipoLiquidacion;
use ADIF\RecursosHumanosBundle\Form\EmpleadoConceptosType;
use ADIF\RecursosHumanosBundle\Form\EmpleadoNovedadesType;
use ADIF\RecursosHumanosBundle\Form\EmpleadoType;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Style_NumberFormat;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Exception\Exception;
use mPDF;
use ADIF\RecursosHumanosBundle\Entity\EmpleadoHistoricoRangoRemuneracion;
use ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia;


/**
 * Empleado controller.
 *
 * @Route("/empleados")
 *@Security("has_role('ROLE_RRHH_VISTA_EMPLEADOS') or has_role('ROLE_MENU_ADMINISTRACION_FONDOS_ANTICIPO')")
 */
class EmpleadoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Empleados' => $this->generateUrl('empleados')
        );
    }

    /**
     * Lists all Empleado entities.
     *
     * @Route("/", name="empleados")
     * @Method("GET")
     * @Template()
     *@Security("has_role('ROLE_RRHH_VISTA_EMPLEADOS')")
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Empleados'] = null;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $novedades = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')->findAll(array('esNovedad' => 1, 'activo' => 1));

        if (!$novedades) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $liqAjuste = $em
                ->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
                ->createQueryBuilder('l')
                ->where('l.tipoLiquidacion = :tipoLiquidacion')->setParameter('tipoLiquidacion', TipoLiquidacion::__HABITUAL)
                ->orderBy('l.fechaCierreNovedades', 'DESC')
                ->getQuery()
                ->getResult();

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Empleados',
            'liquidacion_en_sesion' => $this->get('session')->has('liquidacion'),
            'novedades' => $this->getValorNovedades($novedades),
            'liquidaciones_ajuste' => $liqAjuste,
            'page_info' => 'Lista de empleados'
        );
    }

    /**
     * Tabla para Empleado.
     *
     * @Route("/index_table/", name="empleados_index_table")
     * @Method("GET|POST")
     *@Security("has_role('ROLE_RRHH_VISTA_EMPLEADOS')")
     * 
     */
    public function indexTableAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        // if ($this->getCacheDriver()->){
        // }
        $empleados = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')
                ->createQueryBuilder('e')
                ->select('partial e.{id, nroLegajo}, partial p.{id, nombre, apellido, cuil}, partial con.{id, nombre}, partial cat.{id, nombre}, 
					partial sub.{id, nombre, montoBasico}, partial f649.{id}, partial tc.{id, fechaDesde}, partial rr.{id}, partial ger.{id, nombre}')
                ->innerJoin('e.persona', 'p')
				->innerJoin('e.idGerencia', 'ger')
                ->innerJoin('e.idSubcategoria', 'sub')
                ->innerJoin('sub.idCategoria', 'cat')
                ->innerJoin('e.tiposContrato', 'tc')
                ->leftJoin('cat.idConvenio', 'con')
                ->leftJoin('e.formulario649', 'f649')
                ->leftJoin('e.rangoRemuneracion', 'rr')
                ->where('e.activo = 1')
                ->orderBy('tc.fechaDesde', 'DESC')
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);
        return $this->render('ADIFRecursosHumanosBundle:Empleado:index_table.html.twig', array('empleados' => $empleados));
    }

    /**
     * Lists all Empleado entities.
     *
     * @Route("/historico", name="empleados_historico")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Empleado:index_historico.html.twig")
     *@Security("has_role('ROLE_RRHH_VISTA_EMPLEADOS')")
     */
    public function indexHistoricoAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Empleados Hist&oacute;rico'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Empleados Hist&oacute;rico',
            'page_info' => 'Lista de empleados hist&oacute;ricos'
        );
    }

    /**
     * Tabla para Empleado.
     *
     * @Route("/index_historico_table/", name="empleados_index_historico_table")
     * @Method("GET|POST")
     *@Security("has_role('ROLE_RRHH_VISTA_EMPLEADOS')")
     * 
     */
    public function indexHistoricoTableAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleados = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')
                ->createQueryBuilder('e')
                ->select('partial e.{id, nroLegajo, fechaEgreso}, partial p.{id, nombre, apellido, cuil}, partial con.{id, nombre}, partial cat.{id, nombre}, partial sub.{id, nombre, montoBasico}, partial f649.{id}, partial tc.{id, fechaDesde}')
                ->innerJoin('e.persona', 'p')
                ->innerJoin('e.idSubcategoria', 'sub')
                ->innerJoin('sub.idCategoria', 'cat')
                ->innerJoin('e.tiposContrato', 'tc')
                ->leftJoin('cat.idConvenio', 'con')
                ->leftJoin('e.formulario649', 'f649')
                ->where('e.activo = 0')
                ->orderBy('tc.fechaDesde', 'DESC')
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);
        return $this->render('ADIFRecursosHumanosBundle:Empleado:index_historico_table.html.twig', array('empleados' => $empleados));
    }

    /**
     * Lists all Empleado entities.
     *
     * @Route("/extendido", name="empleados_extendido")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Empleado:index_extendido.html.twig")
     * -@Security("has_role('ROLE_IMPRIMIR_RECIBOS_SUELDOS')")
     */
    public function indexExtendidoAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Vista extendida'] = null;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $novedades = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')->findAll(array('esNovedad' => 1, 'activo' => 1));

        if (!$novedades) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Empleados (vista extendida)',
            'novedades' => $this->getValorNovedades($novedades),
            'page_info' => 'Lista de empleados'
        );
    }

    /**
     * Tabla para Empleado extendida.
     *
     * @Route("/index_extendido_table/", name="empleados_index_extendido_table")
     * @Method("GET|POST")
     *@Security("has_role('ROLE_RRHH_VISTA_EMPLEADOS')")
     * 
     */
    public function indexExtendidoTableAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('id2', 'id2');
        $rsm->addScalarResult('nro_legajo', 'nro_legajo');
        $rsm->addScalarResult('apellido', 'apellido');
        $rsm->addScalarResult('nombre', 'nombre');
        $rsm->addScalarResult('cuil', 'cuil');
        $rsm->addScalarResult('telefono', 'telefono');
        $rsm->addScalarResult('celular', 'celular');
        $rsm->addScalarResult('fecha_nacimiento', 'fecha_nacimiento');
        $rsm->addScalarResult('edad', 'edad');
        $rsm->addScalarResult('estado_civil', 'estado_civil');
        $rsm->addScalarResult('tipo_contrato', 'tipo_contrato');
        $rsm->addScalarResult('categoria', 'categoria');
        $rsm->addScalarResult('subcategoria', 'subcategoria');
        $rsm->addScalarResult('convenio', 'convenio');
        $rsm->addScalarResult('ingreso_planta', 'ingreso_planta');
        $rsm->addScalarResult('fecha_inicio_antiguedad', 'fecha_inicio_antiguedad');
        $rsm->addScalarResult('antiguedad', 'antiguedad');
        $rsm->addScalarResult('titulo', 'titulo');
        $rsm->addScalarResult('nivel_educacion', 'nivel_educacion');
        $rsm->addScalarResult('inicio_primer_contrato', 'inicio_primer_contrato');
        $rsm->addScalarResult('inicio_ultimo_contrato', 'inicio_ultimo_contrato');
        $rsm->addScalarResult('fin_ultimo_contrato', 'fin_ultimo_contrato');
        $rsm->addScalarResult('periodos_contratados', 'periodos_contratados');
        $rsm->addScalarResult('gerencia', 'gerencia');
        $rsm->addScalarResult('subgerencia', 'subgerencia');
        $rsm->addScalarResult('area', 'area');
		
	$rsm->addScalarResult('nivel_organizacional', 'nivel_organizacional');
		
        $rsm->addScalarResult('bruto', 'bruto');
        $rsm->addScalarResult('retencion_ganancias', 'retencion_ganancias');
        $rsm->addScalarResult('neto', 'neto'); 
        $rsm->addScalarResult('rango_remuneracion', 'rango_remuneracion');
        $rsm->addScalarResult('acciones', 'acciones');
        /**
         * Fix: Se agrega obra social a la vista extendida de empleados - 20/01/2015
         * @author gluis
         */
        $rsm->addScalarResult('obra_social', 'obra_social');
        /**
         * Fix: se agregaron 2 columnas: fecha de egreso y motivo de egreso - 28/01/2015
         * @author gluis
         */
        $rsm->addScalarResult('fecha_egreso', 'fecha_egreso');
        $rsm->addScalarResult('motivo_egreso', 'motivo_egreso');

        // Se agregan banco-tipo de cuenta-cbu. 13/03/2015
        $rsm->addScalarResult('banco', 'banco');
        $rsm->addScalarResult('tipo_cuenta', 'tipo_cuenta');
        $rsm->addScalarResult('cbu', 'cbu');

        // Se agregan domicilio y localidad - 30/04/2015
        $rsm->addScalarResult('domicilio', 'domicilio');
        $rsm->addScalarResult('localidad', 'localidad');
		
        $rsm->addScalarResult('puesto', 'puesto');
        $rsm->addScalarResult('superior', 'superior');

        $rsm->addScalarResult('afiliacion_uf', 'afiliacion_uf');
        $rsm->addScalarResult('afiliacion_apdfa', 'afiliacion_apdfa');
        
        $rsm->addScalarResult('area_rrhh', 'area_rrhh');
        $rsm->addScalarResult('tablero_mt', 'tablero_mt');
        $rsm->addScalarResult('email', 'email');

        $tienePermisoVerSueldos = $this->get('security.context')->isGranted('ROLE_IMPRIMIR_RECIBOS_SUELDOS');
        $tienePermisoVerSueldos = ($tienePermisoVerSueldos) ? 1 : 0;
				
        $sql = "CALL sp_empleados_extendido($tienePermisoVerSueldos)";
        //die($sql);
        $query = $em->createNativeQuery($sql, $rsm);
        $empleados = $query->getResult();
        $empleados_json = array();
        foreach ($empleados as $empleado) {
            $empleados_json[] = array_values($empleado);
        }

        return new JsonResponse(array('data' => $empleados_json));


//        return $this->render('ADIFRecursosHumanosBundle:Empleado:index_extendido_table.html.twig', array('empleados' => $empleados));
    }

    /**
     * Creates a new Empleado entity.
     *
     * @Route("/insertar", name="empleados_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Empleado:new.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
     */
    public function createAction(Request $request) {
		
		$em = $this->getDoctrine()->getManager($this->getEntityManager());
		
        $empleado = new Empleado();
        $form = $this->createCreateForm($empleado);
        $form->handleRequest($request);
		
		// Superior / Jefe inmediato
		$empleado->setSuperior(null); // por defecto le seteo null, ya que es opcional
		$idSuperior = $request->get('adif_recursoshumanosbundle_empleado')['superior'];
        $fotoSuperior = false;
		if ($idSuperior != null) {
			$superior = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idSuperior);
            
			if ($superior) {
				$empleado->setSuperior($superior);
                
                if ($superior->getFoto() != null) {
                    $fotoSuperior = true;
                }
			} 
		}
        
        // Work-around de foto superior para que no explote
        if (!$fotoSuperior) {
            $formularioValido = $form->isValid();
        } else {
            $formularioValido = true;
        }

        if ($formularioValido) {
            
            $em->getConnection()->beginTransaction(); // suspend auto-commit

            try {
                // Seteo el numero de legajo del empleado
                $empleado->setNroLegajo($this->getUltimoNroLegajo($empleado));

                //if (!$request->request->get('adif_recursoshumanosbundle_empleado_idCuenta_cargar', false)) {
                if (!isset($request->request->get('adif_recursoshumanosbundle_empleado')['idCuenta']['cargar'])) {
                    $empleado->setIdCuenta(null);
                }

                if (($empleado->getFormulario649() != null) && ($empleado->getFormulario649()->getGananciaAcumulada() != null)) {
                    $empleado->getFormulario649()->setEmpleado($empleado);
                } else {
                    $empleado->setFormulario649(null);
                }

                $em->persist($empleado);

                // Estudios
                $estudios_empleado = $request->request->get('adif_recursoshumanosbundle_empleado_estudio') ? $request->request->get('adif_recursoshumanosbundle_empleado_estudio') : array();
                $this->actualizarEstudiosEmpleado($empleado, $estudios_empleado, $em);

                // Familiares
                $familiares_empleado = $request->request->get('adif_recursoshumanosbundle_empleado_familiar') ? $request->request->get('adif_recursoshumanosbundle_empleado_familiar') : array();
                $this->actualizarFamiliaresEmpleado($empleado, $familiares_empleado, $em);

                // Contactos
                $contactos_empleado = $request->request->get('adif_recursoshumanosbundle_empleado_contacto') ? $request->request->get('adif_recursoshumanosbundle_empleado_contacto') : array();
                $this->actualizarContactosEmpleado($empleado, $contactos_empleado, $em);

                // Imagen
                $this->guardarFoto($empleado);

                // Creo/actualizo históricos de rango remuneracion
                $this->chequearHistoricoRangoRemuneracion($em, $empleado);
                
                $em->flush();
                $em->getConnection()->commit();
                $this->addSuccessFlash('Se dio de alta correctamente al empleado.');
                
            } catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $eUnique) {
                
                $em->getConnection()->rollback();
                $em->close();
                
                $mensajeError = 'Hubo un error al dar de alta al empleado - ';
                if (preg_match('/cuenta/', $eUnique->getMessage())) {
                    $mensajeError .= 'El número de cbu ya se encuentra en uso';
                }
                
                $this->addErrorFlash($mensajeError);
                
            } catch (Exception $e) {
                $em->getConnection()->rollback();
                $em->close();
                $this->addErrorFlash('Hubo un error al dar de alta al empleado.');
            }
            
            return $this->redirect($this->generateUrl('empleados'));   
        } 

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'empleado' => $empleado,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear empleado',
        );
    }

    /**
     * Creates a form to create a Empleado entity.
     *
     * @param Empleado $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Empleado $entity) {
        $form = $this->createForm(new EmpleadoType(), $entity, array(
            'action' => $this->generateUrl('empleados_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Empleado entity.
     *
     * @Route("/crear", name="empleados_new")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Empleado:new.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
     */
    public function newAction() {
        $empleado = new Empleado();
        $form = $this->createCreateForm($empleado);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'empleado' => $empleado,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear empleado'
        );
    }

    /**
     * Finds and displays a Empleado entity.
     *
     * @Route("/{id}", name="empleados_show")
     * @Method("GET")
     * @Template()
     *@Security("has_role('ROLE_RRHH_VISTA_EMPLEADOS')")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->__toString()] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver empleado'
        );
    }

    /**
     * Displays a form to edit an existing Empleado entity.
     *
     * @Route("/editar/{id}", name="empleados_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Empleado:new.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
     */
    public function editAction($id) {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($id);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $editForm = $this->createEditForm($empleado);

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('empleados_show', array('id' => $empleado->getId()));
        $bread['Editar'] = null;

        return array(
            'empleado' => $empleado,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar empleado'
        );
    }

    /**
     * Creates a form to edit a Empleado entity.
     *
     * @param Empleado $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Empleado $entity) {
        $form = $this->createForm(new EmpleadoType(), $entity, array(
            'action' => $this->generateUrl('empleados_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Empleado entity.
     *
     * @Route("/actualizar/{id}", name="empleados_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Empleado:new.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($id);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $idRangoRemuneracion = $empleado->getRangoRemuneracion()->getId();

        $empleadoViejo = clone ($empleado);

        $nro_legajo = $empleado->getNroLegajo();

        $tipoContratoAnterior = $empleado->getTipoContratacionActual()->getTipoContrato();
		
		// Superior / Jefe inmediato
		$empleado->setSuperior(null); // por defecto le seteo null, ya que es opcional
		$idSuperior = $request->get('adif_recursoshumanosbundle_empleado')['superior'];
        $fotoSuperior = false;
		if (!empty($idSuperior) ) {
			$superior = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idSuperior);
			if ($superior) {
				$empleado->setSuperior($superior);
                
                if ($superior->getFoto() != null) {
                    $fotoSuperior = true;
                }
			} 
		}
        
        try {
		
            $editForm = $this->createEditForm($empleado);
            $editForm->handleRequest($request);

            // Work-around de foto superior para que no explote
            if (!$fotoSuperior) {
                $formularioValido = $editForm->isValid();
            } else {
                $formularioValido = true;
            }
		
            if ($formularioValido) {
                if (($empleado->getFormulario649() != null) && ($empleado->getFormulario649()->getGananciaAcumulada() != null)) {
                    $empleado->getFormulario649()->setEmpleado($empleado);
                } else {
                    $empleado->setFormulario649(null);
                }

                if (!isset($request->request->get('adif_recursoshumanosbundle_empleado')['idCuenta']['cargar'])) {
                    if ($empleado->getIdCuenta()) {
                        $em->remove($empleado->getIdCuenta());
                    }
                    $empleado->setIdCuenta(null);
                }

                $estudios_empleado = $request->request->get('adif_recursoshumanosbundle_empleado_estudio') ? $request->request->get('adif_recursoshumanosbundle_empleado_estudio') : array();
                $this->actualizarEstudiosEmpleado($empleado, $estudios_empleado, $em);

                $familiares_empleado = $request->request->get('adif_recursoshumanosbundle_empleado_familiar') ? $request->request->get('adif_recursoshumanosbundle_empleado_familiar') : array();
                $this->actualizarFamiliaresEmpleado($empleado, $familiares_empleado, $em);

                $contactos_empleado = $request->request->get('adif_recursoshumanosbundle_empleado_contacto') ? $request->request->get('adif_recursoshumanosbundle_empleado_contacto') : array();
                $this->actualizarContactosEmpleado($empleado, $contactos_empleado, $em);

                // Imagen
                $this->guardarFoto($empleado);

                if ($tipoContratoAnterior != $empleado->getTipoContratacionActual()->getTipoContrato()) {
                    $nro_legajo = $this->getUltimoNroLegajo($empleado);
                }

                $empleado->setNroLegajo($nro_legajo);

    //          Cambios en sector/area/gerencia/subgerencia
                $this->actualizarDatosPuesto($empleadoViejo, $empleado);

    //          Cambios en la subcategoria
                $fecha_cambio_sub = $request->request->get('adif_recursoshumanosbundle_empleado_idSubcategoria_fecha') ?
                        new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $request->request->get('adif_recursoshumanosbundle_empleado_idSubcategoria_fecha'))))) : new \DateTime();
                $this->actualizarDatosSubcategoria($empleadoViejo, $empleado, $fecha_cambio_sub);

                // Creo/actualizo históricos de rango remuneracion
                $this->chequearHistoricoRangoRemuneracion($em, $empleado, $idRangoRemuneracion);

                $idSubcategoria = isset($request->get('adif_recursoshumanosbundle_empleado')['idSubcategoria']) 
                    ? isset($request->get('adif_recursoshumanosbundle_empleado')['idSubcategoria']) 
                    : '';


                if (empty($idSubcategoria)) {

                    $this->container->get('request')->getSession()->getFlashBag()
                            ->add('error', 'La subcategoria es obligatoria.');

                    $request->attributes->set('form-error', true);

                    return $this->redirect($this->generateUrl('empleados_edit',  array('id' => $empleado->getId()) ));
                }

                // Fix para que no me pise la foto si no se sube desde el formulario
                if (!$this->subioFoto() && $empleado->getFoto() == null) {
                    // Busco en la base de datos si el campo foto diferente de null
                    $empleado->setFoto($empleadoViejo->getFoto());
                }

                $em->flush();

                $this->addSuccessFlash('Se modifico correctamente al empleado.');

                return $this->redirect($this->generateUrl('empleados'));

            } else {

                //Debug::dump($editForm->getErrorsAsString());

                $this->container->get('request')->getSession()->getFlashBag()
                            ->add('error', $editForm->getErrorsAsString());

                $request->attributes->set('form-error', true);

                $this->addErrorFlash('Hubo un error al modificar al empleado.');

                return $this->redirect($this->generateUrl('empleados_edit',  array('id' => $empleado->getId()) ));
            }
            
        } catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $eUnique) {
            
            $mensajeError = 'Hubo un error al modificar al empleado - ';
            if (preg_match('/cuenta/', $eUnique->getMessage())) {
                $mensajeError .= 'El número de cbu ya se encuentra en uso';
            }
            
            $this->addErrorFlash($mensajeError);
            return $this->redirect($this->generateUrl('empleados_edit',  array('id' => $empleado->getId()) ));
            
        } catch(\Exception $e) {
            
            $this->addErrorFlash('Hubo un error al modificar al empleado.');
            return $this->redirect($this->generateUrl('empleados_edit',  array('id' => $empleado->getId()) ));
        }
    }

    /**
     * Cambia el estado activo del empleado.
     *
     * @Route("/activar/{id}/{activo}", name="empleados_activar")
     * @Method("GET")
     * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
     */
    public function activarAction($id, $activo = 1) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        /* @var $empleado Empleado */
        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($id);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $empleado->setActivo($activo == 1);

        $em->flush();


        return $this->redirect($this->generateUrl('empleados'));
    }

    /**
     * Deletes a Empleado entity.
     *
     * @Route("/borrar/{id}", name="empleados_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_RRHH_ALTA_EMPLEADOS')")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('empleados'));
    }

    private function actualizarEstudiosEmpleado($empleado, $estudios_empleado, $em) {
        // Recorro los estudios originales para eliminar los que no me vinieron en el post
        $estudios_originales = $empleado->getEstudios();
        foreach ($estudios_originales as $estudio_original) {
            $existe = false;
            foreach ($estudios_empleado as $estudio_empleado) {
                if ($estudio_original->getId() == $estudio_empleado['id']) {
                    $existe = true;
                }
            }
            if (!$existe) {
                $em->remove($estudio_original);
            }
        }

        //Recorro los estudios para asignarselos al empleado
        $estudios = new ArrayCollection();

        foreach ($estudios_empleado as $estudio_empleado) {
            if (!$estudio_empleado['id']) {
                //Si no existe lo agrego, sino ya está en la coleccion
                $e_e = new EstudioEmpleado();

                $e_e->setIdEmpleado($empleado);
                $e_e->setIdNivelEstudio($em->getRepository('ADIFRecursosHumanosBundle:NivelEstudio')->find($estudio_empleado['id_nivel_estudio']));
                $e_e->setTitulo($em->getRepository('ADIFRecursosHumanosBundle:TituloUniversitario')->find($estudio_empleado['id_titulo_universitario']));
                $e_e->setEstablecimiento($estudio_empleado['establecimiento']);

                $e_e->setFechaDesde(($estudio_empleado['fecha_desde'] == '') ? null : new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $estudio_empleado['fecha_desde'])))));
                $e_e->setFechaHasta(($estudio_empleado['fecha_hasta'] == '') ? null : new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $estudio_empleado['fecha_hasta'])))));
                $estudios->add($e_e);
            }
        }

        $empleado->setEstudios($estudios);
    }

    private function actualizarFamiliaresEmpleado($empleado, $familiares_empleado, $em) {
        // Recorro los familiares originales para eliminar los que no me vinieron en el post
        $familiares_originales = $empleado->getFamiliares();
        foreach ($familiares_originales as $familiar_original) {
            $existe = false;
            foreach ($familiares_empleado as $familiar_empleado) {
                if ($familiar_original->getId() == $familiar_empleado['id']) {
                    $existe = true;
                }
            }
            if (!$existe) {
                $em->remove($familiar_original);
            }
        }

        //Recorro los familiares para asignarselos al empleado
        $familiares = new ArrayCollection();
        foreach ($familiares_empleado as $familiar_empleado) {
            if (!$familiar_empleado['id']) {
                //Si no existe lo agrego, sino ya está en la coleccion
                $familiar = new Familiar();
                $familiar->setIdEmpleado($empleado);

                $familiar->setIdTipoRelacion($em->getRepository('ADIFRecursosHumanosBundle:TipoRelacion')->find($familiar_empleado['id_tipo_relacion']));

                $persona = new Persona();
                $persona->setApellido($familiar_empleado['apellido']);
                $persona->setNombre($familiar_empleado['nombre']);
                $persona->setIdTipoDocumento($em->getRepository('ADIFRecursosHumanosBundle:TipoDocumento')->find($familiar_empleado['id_tipo_documento']));
                $persona->setNroDocumento($familiar_empleado['documento']);
                $persona->setSexo($familiar_empleado['sexo']);
                $persona->setFechaNacimiento(new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $familiar_empleado['fecha_nacimiento'])))));

                $em->persist($persona);

                $familiar->setPersona($persona);
                $familiar->setAnioCursa($familiar_empleado['anio_cursa']);
                $familiar->setEscolaridad($familiar_empleado['escolaridad']);
                $familiar->setEnGuarderia($familiar_empleado['en_guarderia']);
                $familiar->setACargoOS($familiar_empleado['a_cargo_os']);

                $familiares->add($familiar);
            }
        }

        $empleado->setFamiliares($familiares);
    }

    private function actualizarContactosEmpleado($empleado, $contactos_empleado, $em) {
        // Recorro los familiares originales para eliminar los que no me vinieron en el post
        $contactos_originales = $empleado->getContactosEmergencia();
        foreach ($contactos_originales as $contacto_original) {
            $existe = false;
            foreach ($contactos_empleado as $contacto_empleado) {
                if ($contacto_original->getId() == $contacto_empleado['id']) {
                    $existe = true;
                }
            }
            if (!$existe) {
                $em->remove($contacto_original);
            }
        }

        //Recorro los contactos para asignarselos al empleado
        $contactos = new ArrayCollection();
        foreach ($contactos_empleado as $contacto_empleado) {
            if (!$contacto_empleado['id']) {
                //Si no existe lo agrego, sino ya está en la coleccion
                $c_e = new ContactoEmergencia();
                $c_e->setIdEmpleado($empleado);
                $c_e->setApellido($contacto_empleado['apellido']);
                $c_e->setNombre($contacto_empleado['nombre']);
                $c_e->setDomicilio($contacto_empleado['domicilio']);
                $c_e->setTelefono($contacto_empleado['telefono']);
                $c_e->setIdTipoRelacion($em->getRepository('ADIFRecursosHumanosBundle:TipoRelacion')->find($contacto_empleado['id_tipo_relacion']));

                $contactos->add($c_e);
            }
        }

        $empleado->setContactosEmergencia($contactos);
    }

    private function guardarFoto(Empleado $empleado) {
	
		if ($this->subioFoto($empleado) && $empleado->getFoto() != null) {
			$filename = sha1(uniqid(mt_rand(), true)) . '.' . $empleado->getFoto()->guessExtension();
			$empleado->getFoto()->move(__DIR__ . '/../../../../web/uploads/empleados/fotos', $filename);
			$empleado->setFoto($filename);
		}
        
    }

    /**
     * Se asignan los conceptos al empleado
     *
     * @Route("/asignarconceptos/{idEmpleado}", name="empleados_asignar_conceptos")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Empleado:asignar_conceptos.html.twig")
     * @Security("has_role('ROLE_RRHH_ASIGNACION_CONCEPTOS')") 
     */
    public function asignarConceptosAction($idEmpleado) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $editForm = $this->createConceptosForm($empleado);

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('empleados_show', array('id' => $empleado->getId()));
        $bread['Asignar Conceptos'] = null;

        return array(
            'empleado' => $empleado,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Asignar conceptos al empleado'
        );
    }

    /**
     * Crea un formulario para asignar conceptos a los empleados.
     *
     * @param Empleado $entity The entity
     *
     * @return Form The form
     */
    private function createConceptosForm(Empleado $entity) {
        $form = $this->createForm(new EmpleadoConceptosType($entity), $entity, array(
            'action' => $this->generateUrl('empleados_asignar_conceptos_update', array('idEmpleado' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Guarda los conceptos del empleado
     *
     * @Route("/asignarconceptos/{idEmpleado}/guardar", name="empleados_asignar_conceptos_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Empleado:asignar_conceptos.html.twig")
     * @Security("has_role('ROLE_RRHH_ASIGNACION_CONCEPTOS')")
     */
    public function asignarConceptosUpdateAction(Request $request, $idEmpleado) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $editForm = $this->createConceptosForm($empleado);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('empleados'));
        } else {
            foreach ($editForm->getErrors() as $error) {
                Debug::dump($error);
            }
        }

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('empleados_show', array('id' => $empleado->getId()));
        $bread['Asignar Conceptos'] = null;

        return array(
            'empleado' => $empleado,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Asignar conceptos al empleado'
        );
    }

    /**
     * Se asignan novedades al empleado
     *
     * @Route("/asignarnovedades/{idEmpleado}", name="empleados_asignar_novedades")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Empleado:asignar_novedades.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_NOVEDADES')")
     */
    public function asignarNovedadesAction($idEmpleado) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $novedades = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')->findAllNovedadesByConvenio($empleado->getConvenio())->getQuery()->getResult();

        if (!$novedades) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $novedad = new EmpleadoNovedad();
        $novedad->setIdEmpleado($empleado);

        $editForm = $this->createNovedadesForm($empleado, $novedad);

        $bread = $this->base_breadcrumbs;
        $bread[$empleado->__toString()] = $this->generateUrl('empleados_show', array('id' => $empleado->getId()));
        $bread['Novedades'] = null;

        return array(
            'empleado' => $empleado,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'novedades' => $this->getValorNovedades($novedades),
            'page_title' => 'Novedades del empleado'
        );
    }

    /**
     * Crea un formulario para asignar novedades a los empleados.
     *
     * @param Empleado $entity The entity
     *
     * @return Form The form
     */
    private function createNovedadesForm(Empleado $entity, EmpleadoNovedad $novedad) {
        $form = $this->createForm(new EmpleadoNovedadesType($entity), $novedad, array(
            'action' => $this->generateUrl('empleados_asignar_novedades_update', array('idEmpleado' => $entity->getId())),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Guarda las novedades del empleado
     *
     * @Route("/asignarnovedades/{idEmpleado}/guardar", name="empleados_asignar_novedades_update")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Empleado:asignar_conceptos.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_NOVEDADES')")
     */
    public function asignarNovedadesUpdateAction(Request $request, $idEmpleado) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $novedad = new EmpleadoNovedad();
        $novedad->setIdEmpleado($empleado);

        $editForm = $this->createNovedadesForm($empleado, $novedad);
        $editForm->handleRequest($request);


        $html = '';
        if ($editForm->isValid()) {

            $empleado->addNovedad($novedad);

            $em->flush();

            $html .= $novedad->getId() . '|';
            $html .= '<div class="checker hover"><span>'
                    . '<input type="checkbox" class="checkboxes" value="" />'
                    . '</span></div> |';
            $html .= $novedad->getIdConcepto() . '|';
            $html .= $novedad->getFechaAlta()->format('d/m/Y') . '|';
            $html .= $novedad->getIdConcepto()->getEsPorcentaje() ? ' %|' : '|';
            $html .= $novedad->getValor() . '|';
            $html .= '<a style="margin-right: 3px;" href="' . $this->generateUrl('empleados_asignar_novedades_editar', array('idEmpleado' => $idEmpleado, 'idNovedad' => $novedad->getId())) . '" class="edit btn btn-xs green tooltips" data-original-title="Editar">'
                    . '<i class="fa fa-pencil"></i></a>'
                    . '<a href="' . $this->generateUrl('empleados_asignar_novedades_borrar', array('idEmpleado' => $idEmpleado, 'idNovedad' => $novedad->getId())) . '" class="delete btn btn-xs red tooltips" data-original-title="Borrar">'
                    . '<i class="fa fa-trash-o"></i></a>';
        }

        $response = new Response();
        $response->setContent($html);
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

    /**
     * Modificación de una novedad del empleado
     *
     * @Route("/asignarnovedades/{idEmpleado}/{idNovedad}/editar", name="empleados_asignar_novedades_editar")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Empleado:asignar_conceptos.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_NOVEDADES')")
     */
    public function asignarNovedadesEditarAction(Request $request, $idEmpleado, $idNovedad) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $novedad = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoNovedad')->find($idNovedad);

        if (!$novedad) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoNovedad.');
        }

        $array_request = ($request->get('adif_recursoshumanosbundle_empleado_novedades'));

        $html = '';

        if ($novedad->getIdEmpleado()->getId() == $idEmpleado) {

            $novedad->setFechaAlta(new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $array_request['fechaAlta'])))));

            $novedad->setValor(str_replace(',', '.', $array_request['valor']));

            $em->flush();


            $html .= $novedad->getId() . '|';
            $html .= '<div class="checker hover"><span>'
                    . '<input type="checkbox" class="checkboxes" value="" />'
                    . '</span></div> |';
            $html .= $novedad->getIdConcepto() . '|';
            $html .= $novedad->getFechaAlta()->format('d/m/Y') . '|';
            $html .= $novedad->getIdConcepto()->getEsPorcentaje() ? ' %|' : '|';
            $html .= $novedad->getValor() . '|';
            $html .= '<a style="margin-right: 3px;" href="' . $this->generateUrl('empleados_asignar_novedades_editar', array('idEmpleado' => $idEmpleado, 'idNovedad' => $novedad->getId())) . '" class="edit btn btn-xs green tooltips" data-original-title="Editar">'
                    . '<i class="fa fa-pencil"></i></a>'
                    . '<a href="' . $this->generateUrl('empleados_asignar_novedades_borrar', array('idEmpleado' => $idEmpleado, 'idNovedad' => $novedad->getId())) . '" class="delete btn btn-xs red tooltips" data-original-title="Borrar">'
                    . '<i class="fa fa-trash-o"></i></a>';
        }


        $response = new Response();
        $response->setContent($html);
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

    /**
     * Baja logica de una novedad del empleado
     *
     * @Route("/asignarnovedades/{idEmpleado}/{idNovedad}/borrar", name="empleados_asignar_novedades_borrar")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Empleado:asignar_conceptos.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_NOVEDADES')")
     */
    public function asignarNovedadesBorrarAction(Request $request, $idEmpleado, $idNovedad) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $novedad = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoNovedad')->find($idNovedad);

        if (!$novedad) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EmpleadoNovedad.');
        }

        $html = '';

        if ($novedad->getIdEmpleado()->getId() == $idEmpleado) {
            $novedad->setFechaBaja(new DateTime());

            $em->flush();

            $html = 'ok';
        }

        $response = new Response();
        $response->setContent($html);
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

    /**
     * Devuelve para cada novedad si es porcentaje
     * 
     */
    private function getValorNovedades($novedades) {

        $novedadesArray = [];

        foreach ($novedades as $novedad) {
            $novedadesArray[$novedad->getId()] = $novedad->getEsPorcentaje();
        }

        return $novedadesArray;
    }

    /**
     * Finds and displays a Empleado entity.
     *
     * @Route("/pdf/{id}", name="empleados_show_pdf")
     * @Method("GET")
     */
    public function showPDFAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $html = $this->renderView(
                'ADIFRecursosHumanosBundle:Empleado:showPDF.html.twig', array(
            'entity' => $entity
                )
        );

        AdifApi::printPDF($html, 'Legajo ' . $entity->__toString(), 'Legajo ' . $entity->__toString());
    }

    /**
     * Muestra conceptos asociados a empleados.
     *
     * @Route("/conceptos/", name="empleados_conceptos")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Empleado:conceptos.html.twig")
     * @Security("has_role('ROLE_RRHH_VISTA_CONCEPTOS')")
     */
    public function conceptosEmpleadosAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $conceptos = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')
                ->createQueryBuilder('c')
                ->select('partial c.{id, codigo, descripcion}')
                ->where('c.esNovedad = :esNovedad')->setParameter('esNovedad', false)
                ->orderBy('c.codigo * 1')
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);

        $this->get('session')->set('empleados_conceptos', $conceptos);

        $bread = $this->base_breadcrumbs;
        $bread['Empleados'] = $this->generateUrl('empleados');
        $bread['Conceptos por empleado'] = null;

        return array(
            'breadcrumbs' => $bread,
            'conceptos' => $conceptos,
            'page_title' => 'Conceptos por empleado',
            'page_info' => 'Lista de conceptos asociados a los empleados'
        );
    }

    /**
     * Muestra conceptos asociados a empleados.
     *
     * @Route("/novedades/", name="empleados_novedades")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Empleado:novedades.html.twig")
     * @Security("has_role('ROLE_RRHH_VISTA_CONCEPTOS')")
     */
    public function novedadesEmpleadosAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata('ADIFRecursosHumanosBundle:Concepto', 'c');


        $query = $em->createNativeQuery('
                    SELECT DISTINCT c.id, c.codigo, c.descripcion
                    FROM empleado_novedad en
                        INNER JOIN concepto c ON c.id = en.id_concepto
                    WHERE en.fecha_baja IS NULL
                    ORDER BY c.codigo * 1', $rsm);

        $novedades = $query->getResult();

        $this->get('session')->set('empleados_novedades', $novedades);

        $bread = $this->base_breadcrumbs;
        $bread['Empleados'] = $this->generateUrl('empleados');
        $bread['Novedades por empleado'] = null;

        return array(
            'breadcrumbs' => $bread,
            'novedades' => $novedades,
            'page_title' => 'Novedades por empleado',
            'page_info' => 'Lista de novedades asociadas a los empleados'
        );
    }

    /**
     * Tabla para Empleado.
     *
     * @Route("/novedades_table/", name="empleados_novedades_table")
     * @Method("GET|POST")
     * @Security("has_role('ROLE_RRHH_VISTA_CONCEPTOS')")
     */
    public function novedadesTableAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $empleados = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')
                ->createQueryBuilder('e')
                ->select('
                    partial e.{id, nroLegajo, idSubcategoria}, 
                    partial p.{id, cuil, apellido, nombre}, 
                    partial sub.{id, idCategoria},
                    partial cat.{id, idConvenio},  
                    partial con.{id, nombre},
                    GROUP_CONCAT(c.id),
                    GROUP_CONCAT(en.valor)
                ')
                ->innerJoin('e.persona', 'p')
                ->innerJoin('e.idSubcategoria', 'sub')
                ->innerJoin('sub.idCategoria', 'cat')
                ->leftJoin('cat.idConvenio', 'con')
                ->leftJoin('e.novedades', 'en', 'WITH', 'en.fechaBaja IS NULL')
                ->leftJoin('en.idConcepto', 'c')
                ->where('e.activo = 1')
                ->groupBy('e.id')
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);

        $novedades = $this->get('session')->get('empleados_novedades');

        return $this->render('ADIFRecursosHumanosBundle:Empleado:novedades_table.html.twig', array(
                    'empleados' => $empleados,
                    'novedades' => $novedades
        ));
    }

    /**
     * Tabla para Empleado.
     *
     * @Route("/conceptos_table/", name="empleados_conceptos_table")
     * @Method("GET|POST")
     * @Security("has_role('ROLE_RRHH_VISTA_CONCEPTOS')")
     */
    public function conceptosTableAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $empleados = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')
                ->createQueryBuilder('e')
                ->select('
                    partial e.{id, nroLegajo, idSubcategoria}, 
                    partial p.{id, cuil, apellido, nombre}, 
                    partial sub.{id, idCategoria},
                    partial cat.{id, idConvenio},  
                    partial con.{id, nombre},
                    GROUP_CONCAT(ec.id)')
                ->innerJoin('e.persona', 'p')
                ->leftJoin('e.conceptos', 'ec')
                ->innerJoin('e.idSubcategoria', 'sub')
                ->innerJoin('sub.idCategoria', 'cat')
                ->leftJoin('cat.idConvenio', 'con')
                ->where('e.activo = 1')
                ->groupBy('e.id')
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);

        $conceptos = $this->get('session')->get('empleados_conceptos');

        return $this->render('ADIFRecursosHumanosBundle:Empleado:conceptos_table.html.twig', array(
                    'empleados' => $empleados,
                    'conceptos' => $conceptos
        ));
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/asignarMultipleConcepto/", name="asignar_conceptos_multiple")
     * @Method("GET|POST")
     * @Security("has_role('ROLE_RRHH_ASIGNACION_CONCEPTOS')")
     */
    public function setConceptoMultiplesEmpleados(Request $request) {
        if ((!$request->request->get('ids')) || (!$request->request->get('concepto'))) {
            $this->get('session')->getFlashBag()->add(
                    'error', 'Debe seleccionar al menos un empleado y un concepto para realizar la asignación.'
            );
            return $this->redirect($this->generateUrl('empleados'));
        }
        // empleados
        $idsEmpleados = json_decode($request->request->get('ids', '[]'));
        $idConcepto = json_decode($request->request->get('concepto'));

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $concepto = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')->find($idConcepto);

        if (!$concepto) {
            throw $this->createNotFoundException('No se puede encontrar el Concepto.');
        }

        foreach ($idsEmpleados as $idEmpleado) {
            $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);

            if (!$empleado) {
                throw $this->createNotFoundException('No se puede encontrar el Empleado.');
            }

            // Si no lo tiene
            if (false === $empleado->getConceptos()->contains($concepto)) {

                $empleado->addConcepto($concepto);
            }
        }

        $em->flush();

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/asignarMultipleNovedad", name="asignar_novedades_multiple")
     * @Method("GET|POST")
     * @Security("has_role('ROLE_RRHH_ASIGNACION_CONCEPTOS')")
     */
    public function setNovedadMultiplesEmpleados(Request $request) {
        if (
                (!$request->request->has('ids')) ||
                (!$request->request->has('concepto')) ||
                (!$request->request->has('fecha')) ||
                (!$request->request->has('valor'))) {
            return new JsonResponse([
                'status' => 'ERROR',
                'msg' => 'Debe seleccionar al menos un empleado y una novedad para realizar la asignación.'
            ]);
        }

        // empleados
        $idsEmpleados = json_decode($request->request->get('ids', '[]'));
        $idConcepto = json_decode($request->request->get('concepto'));
        $valor = str_replace(',', '.', $request->request->get('valor'));
        $fechaRequest = $request->request->get('fecha');
        $fechaRequestString = substr($fechaRequest, 6, 4) . '-' . substr($fechaRequest, 3, 2) . '-' . substr($fechaRequest, 0, 2);
        $fecha = new DateTime(date('Y-m-d', strtotime($fechaRequestString)));

        $diasAjuste = $request->request->get('dias_ajuste', 0);
        $liquidacionAjuste = $request->request->get('liquidacion_ajuste', null);

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $concepto = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')->find($idConcepto);

        $liquidacionAjuste = $liquidacionAjuste ? $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find($liquidacionAjuste) : null;

        if (!$concepto) {
            throw $this->createNotFoundException('No se puede encontrar el Concepto.');
        }

        foreach ($idsEmpleados as $idEmpleado) {
            $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($idEmpleado);

            if (!$empleado) {
                throw $this->createNotFoundException('No se puede encontrar el Empleado.');
            }

            $novedad = new EmpleadoNovedad();
            $novedad->setIdEmpleado($empleado);
            $novedad->setIdConcepto($concepto);
            $novedad->setValor($valor);
            $novedad->setFechaAlta($fecha);
            $novedad->setDias($diasAjuste);
            $novedad->setLiquidacionAjuste($liquidacionAjuste);

            $empleado->addNovedad($novedad);
        }

        $em->flush();

        return new JsonResponse([
            'status' => 'OK'
        ]);
    }

    /**
     * Genera el Informe Estimado de Impuesto a las Ganancias en un Excel.
     *
     * @Route("/{id}/excel/{enSesion}/{anio}", name="impuesto_ganancias_excel")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Empleado:show.html.twig")
     * @Security("has_role('ROLE_RRHH_VISTA_GANANCIAS')")
     */
    public function getEstimadoImpuestoGananciasExcel(Request $request, $id, $enSesion, $anio = null) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($id);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar el Empleado.');
        }

        // Si la exportación NO es por sesión.
        if (!$enSesion) {

//            $anio = (new DateTime())->format("Y");

            $gananciaEmpleadoArray = $em->getRepository('ADIFRecursosHumanosBundle:GananciaEmpleado')
                    ->getGananciaEmpleadoByEmpleado($empleado, $anio);
            if (empty($gananciaEmpleadoArray)) {
                $this->get('session')->getFlashBag()->add(
                        'warning', 'No se puede obtener el Impuesto a las Ganancias de ese año'
                );
                return $this->redirect($request->headers->get('referer'));
            }
        } else {
            $gananciaEmpleadoArray = new ArrayCollection();

            $liquidacion = $this->get('session')->get('liquidacion');

            if (null != $liquidacion) {
                foreach ($liquidacion->getLiquidacionEmpleados() as $liquidacionEmpleado) {
                    if ($id == $liquidacionEmpleado->getEmpleado()->getId()) {
                        if (null != $liquidacionEmpleado->getGananciaEmpleado()) {
                            $gananciaEmpleadoArray->add($liquidacionEmpleado->getGananciaEmpleado());
                        }
                        break;
                    }
                }
            }
        }

        /*         * ****** CONFIGURACION EXCEL ************* */

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()
                ->setCreator("Impuesto a las Ganancias")
                ->setLastModifiedBy("Impuesto Ganancias")
                ->setTitle("Impuesto Ganancias")
                ->setSubject("Impuesto a las Ganancias")
                ->setDescription("Impuesto a las Ganancias.")
                ->setKeywords("impuesto ganancias")
                ->setCategory("Impuesto a las Ganancias");

        $phpExcelObject->getActiveSheet()->setTitle('Impuesto a las Ganancias');


        /*         * ****** PARTE ESTATICA DEL EXCEL ************* */

        $nombreMesArray = array(
            'D' => 'ENERO',
            'E' => 'FEBRERO',
            'F' => 'MARZO',
            'G' => 'ABRIL',
            'H' => 'MAYO',
            'I' => 'JUNIO',
            'J' => 'SAC 1ER SEMESTRE',
            'K' => 'JULIO',
            'L' => 'AGOSTO',
            'M' => 'SEPTIEMBRE',
            'N' => 'OCTUBRE',
            'O' => 'NOVIEMBRE',
            'P' => 'SAC 2DO SEMESTRE',
            'Q' => 'DICIEMBRE',
        );

        $mesArray = array(
            '1' => 'D',
            '2' => 'E',
            '3' => 'F',
            '4' => 'G',
            '5' => 'H',
            '6' => 'I',
            '7' => 'J',
            '8' => 'K',
            '9' => 'L',
            '10' => 'M',
            '11' => 'N',
            '12' => 'O',
            '13' => 'P',
            '14' => 'Q',
        );

        $styleBGLightGray = array(
            'font' => array(
                'color' => array('rgb' => '000000')
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'F9F9F9')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $phpExcelObject->getActiveSheet()

                // 1
                ->mergeCells('A1:O1')
                ->setCellValue('A1', 'IMPUESTO A LAS GANANCIAS')

                // 2
                ->mergeCells('A2:O2')

                // 3                
                ->setCellValue('A3', 'APELLIDO Y NOMBRE')
                ->mergeCells('B3:D3')
                ->setCellValue('B3', $empleado->getPersona()->__toString())

                // 4                
                ->setCellValue('A4', 'LEGAJO')
                ->mergeCells('B4:D4')
                ->setCellValue('B4', $empleado->getNroLegajo())


                // 5                
                ->setCellValue('A5', 'CUIL')
                ->mergeCells('B5:D5')
                ->setCellValue('B5', $empleado->getPersona()->getCuil())

                // 6               
                ->setCellValue('A6', 'RANGO')
                ->mergeCells('B6:D6');


        //Rango remuneracion correspondiente al anio solicitado
        if (($anio != null) && ($anio != (new DateTime())->format("Y"))) {
            $claseRangoRemuneracion = $this->get('adif.empleado_historico_rango_remuneracion_service')->getRangoRemuneracionByEmpleadoAndAnio($empleado, $anio);
        } else {
            $claseRangoRemuneracion = $empleado->getRangoRemuneracion();
        }
        $phpExcelObject->getActiveSheet()
                ->setCellValue('B6', '$ ' . number_format($claseRangoRemuneracion->getMontoDesde(), 2, ',', '.')
                        . ' a ' .
                        '$ ' . number_format($claseRangoRemuneracion->getMontoHasta(), 2, ',', '.'));

        // 7                
        $phpExcelObject->getActiveSheet()
                ->setCellValue('A7', 'FECHA')
                ->mergeCells('B7:D7')
                ->setCellValue('B7', (new DateTime())->format("d/m/Y"));

        // 10 
        for ($col = 'D'; $col !== 'R'; $col ++) {
            $phpExcelObject->getActiveSheet()->setCellValue($col . '10', 'MES');
        }

        // 11
        foreach ($nombreMesArray as $col => $mes) {
            $phpExcelObject->getActiveSheet()->setCellValue($col . '11', $mes);
        }

        // 13 - HABER NETO MES
        $phpExcelObject->getActiveSheet()
                ->mergeCells('A13:C13')
                ->setCellValue('A13', 'HABER NETO MES');

        $index = 14;

        // $indexTipoConceptoGananciaOrdenAplicacionCero ;

        $indexTipoConceptoGananciaOrdenAplicacionCero = $index;

        $tipoConceptoGananciaOrdenAplicacionCero = $em->getRepository('ADIFRecursosHumanosBundle:TipoConceptoGanancia')
                ->getTipoConceptoGananciaByOrdenAplicacion(0);

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index, $tipoConceptoGananciaOrdenAplicacionCero->getDenominacion());

        $conceptoGananciaOrdenAplicacionCero = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                ->getConceptoGananciaByOrdenAplicacion(0);

        $cantidadConceptosOrdenAplicacionCero = count($conceptoGananciaOrdenAplicacionCero);

        $index++;

        foreach ($conceptoGananciaOrdenAplicacionCero as $conceptoGanancia) {
            $phpExcelObject->getActiveSheet()
                    ->mergeCells('A' . $index . ':C' . $index)
                    ->setCellValue('A' . $index, $conceptoGanancia->getDenominacion());

            $phpExcelObject->getActiveSheet()
                    ->getStyle('A' . $index)->applyFromArray($styleBGLightGray);

            $index++;
        }

        // HABERES NETOS ACUMULADOS
        $indexHaberNetoMes = $index;

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'HABERES NETOS ACUMULADOS');


        $indexTipoConceptoGananciaOrdenAplicacionUno = $index;

        $tipoConceptoGananciaOrdenAplicacionUno = $em->getRepository('ADIFRecursosHumanosBundle:TipoConceptoGanancia')
                ->getTipoConceptoGananciaByOrdenAplicacion(1);

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index, $tipoConceptoGananciaOrdenAplicacionUno->getDenominacion());

        $conceptoGananciaOrdenAplicacionUno = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                ->getConceptoGananciaByOrdenAplicacion(1);

        $cantidadConceptosOrdenAplicacionUno = count($conceptoGananciaOrdenAplicacionUno);

        $index++;

        foreach ($conceptoGananciaOrdenAplicacionUno as $conceptoGanancia) {
            $phpExcelObject->getActiveSheet()
                    ->mergeCells('A' . $index . ':C' . $index)
                    ->setCellValue('A' . $index, $conceptoGanancia->getDenominacion());

            $phpExcelObject->getActiveSheet()
                    ->getStyle('A' . $index)->applyFromArray($styleBGLightGray);

            $index++;
        }

        $indexTipoConceptoGananciaOrdenAplicacionDos = $index;

        $tipoConceptoGananciaOrdenAplicacionDos = $em->getRepository('ADIFRecursosHumanosBundle:TipoConceptoGanancia')
                ->getTipoConceptoGananciaByOrdenAplicacion(2);

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index, $tipoConceptoGananciaOrdenAplicacionDos->getDenominacion());

        $conceptoGananciaOrdenAplicacionDos = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                ->getConceptoGananciaByOrdenAplicacion(2);

        $cantidadConceptosOrdenAplicacionDos = count($conceptoGananciaOrdenAplicacionDos);

        $index++;

        foreach ($conceptoGananciaOrdenAplicacionDos as $conceptoGanancia) {
            $phpExcelObject->getActiveSheet()
                    ->mergeCells('A' . $index . ':C' . $index)
                    ->setCellValue('A' . $index, $conceptoGanancia->getDenominacion());

            $phpExcelObject->getActiveSheet()
                    ->getStyle('A' . $index)->applyFromArray($styleBGLightGray);

            $index++;
        }

        $indexTipoConceptoGananciaOrdenAplicacionTres = $index;

        $tipoConceptoGananciaOrdenAplicacionTres = $em->getRepository('ADIFRecursosHumanosBundle:TipoConceptoGanancia')
                ->getTipoConceptoGananciaByOrdenAplicacion(3);

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index, $tipoConceptoGananciaOrdenAplicacionTres->getDenominacion());

        $conceptoGananciaOrdenAplicacionTres = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                ->getConceptoGananciaByOrdenAplicacion(3);

        $cantidadConceptosOrdenAplicacionTres = count($conceptoGananciaOrdenAplicacionTres);

        $index++;

        foreach ($conceptoGananciaOrdenAplicacionTres as $conceptoGanancia) {
            $phpExcelObject->getActiveSheet()
                    ->mergeCells('A' . $index . ':C' . $index)
                    ->setCellValue('A' . $index, $conceptoGanancia->getDenominacion());

            $phpExcelObject->getActiveSheet()
                    ->getStyle('A' . $index)->applyFromArray($styleBGLightGray);

            $index++;
        }

//        // Devolucion
//        $phpExcelObject->getActiveSheet()
//                ->mergeCells('A' . $index . ':C' . $index)
//                ->setCellValue('A' . $index++, 'Devolución');
        // TOTAL DEDUCCIONES
        $indexTotalDeducciones = $index;

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'TOTAL DEDUCCIONES');

        // GANANCIA SUJETA A IMPUESTO
        $indexGananciaSujetaImpuesto = $index;

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index, 'GANANCIA SUJETA A IMPUESTO');

        $index += 2;

        // PORCENTAJE TABLA
        $indexPorcentajeTabla = $index;

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'PORCENTAJE TABLA');

        // FIJO TABLA
        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'FIJO TABLA');

        // MONTO SIN EXCEDENTE
        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'MONTO SIN EXCEDENTE');

        // MONTO EXCEDENTE
        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'MONTO EXCEDENTE');

        // TOTAL IMPUESTO DETERMINADO
        $indexTotalImpuestoDeterminado = $index;

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'TOTAL IMPUESTO DETERMINADO');

        // IMPUESTO RETENIDO ANUAL
        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'IMPUESTO RETENIDO ANUAL');
        
        // RETENCIONES OTROS EMPLEOS
        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'RETENCIONES OTROS EMPLEOS');

        // SALDO IMPUESTO MES
        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index, 'SALDO IMPUESTO MES');

        /*         * ****** FIN PARTE ESTATICA DEL EXCEL ************* */



        /*         * ****** PARTE DINAMICA DEL EXCEL ************* */

        // Si el empleado tiene al menos una GananciaEmpleado asociada
        if (!empty($gananciaEmpleadoArray)) {
            $this->initMontosACero(
                    $phpExcelObject, //
                    $cantidadConceptosOrdenAplicacionCero, //
                    $cantidadConceptosOrdenAplicacionUno, //
                    $cantidadConceptosOrdenAplicacionDos, //
                    $cantidadConceptosOrdenAplicacionTres
            );
            foreach ($gananciaEmpleadoArray as $gananciaEmpleado) {
                $this->setMontoConteptosGanancia(
                        $phpExcelObject, //
                        $enSesion, //
                        $gananciaEmpleado, //
                        $mesArray, //
                        $conceptoGananciaOrdenAplicacionCero, //
                        $conceptoGananciaOrdenAplicacionUno, //
                        $conceptoGananciaOrdenAplicacionDos, //
                        $conceptoGananciaOrdenAplicacionTres);
            }
        }

        // ESTILOS

        $styleAllSheet = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleTitle = array(
            'font' => array(
                'bold' => true,
                'size' => 13,
                'color' => array('rgb' => '000000')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleBGGray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '000000')
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'D8D8D8')
            )
        );

        $styleCenterRed = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FF0000')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleCenterGreen = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '00B050')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleTituloConcepto = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '000000')
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'EAF1DD')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleCenterYellow = array(
            'font' => array(
                'bold' => true,
                'size' => 8,
                'color' => array('rgb' => '000000')
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'FFFFCC')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleBoldBlue = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '0000FF')
            )
        );

        $phpExcelObject->getDefaultStyle()->applyFromArray($styleAllSheet);

        for ($col = 'A'; $col !== 'R'; $col ++) {
            $phpExcelObject->getActiveSheet()
                    ->getColumnDimension($col)->setAutoSize(true);

            $phpExcelObject->getActiveSheet()
                    ->getStyle($col)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }

        for ($index = 3; $index <= 12; $index++) {
            $phpExcelObject->getActiveSheet()
                    ->mergeCells('B' . $index . ':C' . $index);
        }

        // 1
        $phpExcelObject->getActiveSheet()->getStyle('A1')->applyFromArray($styleTitle);
        // 3
        $phpExcelObject->getActiveSheet()->getStyle('A3')->applyFromArray($styleBGGray);
        // 4
        $phpExcelObject->getActiveSheet()->getStyle('A4')->applyFromArray($styleBGGray);

        $phpExcelObject->getActiveSheet()
                ->getStyle('B4')->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // 5
        $phpExcelObject->getActiveSheet()
                ->getStyle('A5')->applyFromArray($styleBGGray);

        // 6
        $phpExcelObject->getActiveSheet()
                ->getStyle('A6')->applyFromArray($styleBGGray);

        // 7
        $phpExcelObject->getActiveSheet()
                ->getStyle('A7')->applyFromArray($styleBGGray);

        // 10 
        for ($col = 'D'; $col !== 'R'; $col ++) {
            $phpExcelObject->getActiveSheet()->getStyle($col . '10')->applyFromArray($styleCenterYellow);
        }

        // 11 
        for ($col = 'D'; $col !== 'R'; $col ++) {
            $phpExcelObject->getActiveSheet()->getStyle($col . '11')->applyFromArray($styleBGGray);

            $phpExcelObject->getActiveSheet()
                    ->getStyle($col . '11:' . $col . '60')->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $phpExcelObject->getActiveSheet()
                    ->getStyle($col . '11:' . $col . '60')
                    ->getNumberFormat()->setFormatCode('"$" #,##0.00');
        }

        // 13 - HABER NETO MES
        $phpExcelObject->getActiveSheet()->getStyle('A13:Q13')->applyFromArray($styleBoldBlue);

        $phpExcelObject->getActiveSheet()
                ->getStyle('A13:Q13')
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // $indexTipoConceptoGananciaOrdenAplicacionCero
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexTipoConceptoGananciaOrdenAplicacionCero . ':Q' . $indexTipoConceptoGananciaOrdenAplicacionCero)
                ->applyFromArray($styleTituloConcepto);

        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexTipoConceptoGananciaOrdenAplicacionCero . ':Q' . $indexTipoConceptoGananciaOrdenAplicacionCero)
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // HABERES NETOS ACUMULADOS
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexHaberNetoMes . ':Q' . $indexHaberNetoMes)
                ->applyFromArray($styleCenterRed);

        // $indexTipoConceptoGananciaOrdenAplicacionUno
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexTipoConceptoGananciaOrdenAplicacionUno . ':Q' . $indexTipoConceptoGananciaOrdenAplicacionUno)
                ->applyFromArray($styleTituloConcepto);

        // $indexTipoConceptoGananciaOrdenAplicacionDos
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexTipoConceptoGananciaOrdenAplicacionDos . ':Q' . $indexTipoConceptoGananciaOrdenAplicacionDos)
                ->applyFromArray($styleTituloConcepto);

        // $indexTipoConceptoGananciaOrdenAplicacionTres
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexTipoConceptoGananciaOrdenAplicacionTres . ':Q' . $indexTipoConceptoGananciaOrdenAplicacionTres)
                ->applyFromArray($styleTituloConcepto);

        // TOTAL DEDUCCIONES     
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexTotalDeducciones . ':Q' . $indexTotalDeducciones)
                ->applyFromArray($styleCenterGreen);

        // GANANCIA SUJETA IMPUESTO
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexGananciaSujetaImpuesto . ':Q' . $indexGananciaSujetaImpuesto)
                ->getFont()->setBold(true);

        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexGananciaSujetaImpuesto . ':Q' . $indexGananciaSujetaImpuesto)
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // PORCENTAJE TABLA
        $phpExcelObject->getActiveSheet()
                ->getStyle('D' . $indexPorcentajeTabla . ':Q' . $indexPorcentajeTabla)
                ->getNumberFormat()->applyFromArray(
                array(
                    'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                )
        );

        // 
        for ($index = $indexPorcentajeTabla; $index < $indexPorcentajeTabla + 4; $index++) {
            // Negrita en azul para: "PORCENTAJE TABLA", "FIJO TABLA",
            // "MONTO SIN EXCEDENTE", "MONTO EXCEDENTE"
            $phpExcelObject->getActiveSheet()
                    ->getStyle('A' . $index . ':Q' . $index)
                    ->applyFromArray($styleBoldBlue);
        }

        for ($index = $indexPorcentajeTabla; $index < $indexTotalImpuestoDeterminado + 4; $index++) {
            // Negrita para: "TOTAL IMPUESTO DETERMINADO", "IMPUESTO RETENIDO ANUAL", 
            // "RETENCIONES OTROS EMPLEOS", "SALDO IMPUESTO MES"
            $phpExcelObject->getActiveSheet()
                    ->getStyle('A' . $index . ':Q' . $index)
                    ->getFont()->setBold(true);
        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');

        $response = $this->get('phpexcel')->createStreamedResponse($writer);

        $nombreArchivo = AdifApi::stringCleaner('IG_' . $empleado->getPersona() . '_' . (new DateTime())->format("d-m-Y"));

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=' . $nombreArchivo . '.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    /**
     * 
     * @param type $phpExcelObject
     * @param type $enSesion
     * @param type $gananciaEmpleado
     * @param type $mesArray
     * @param type $conceptoGananciaOrdenAplicacionCero
     * @param type $conceptoGananciaOrdenAplicacionUno
     * @param type $conceptoGananciaOrdenAplicacionDos
     * @param type $conceptoGananciaOrdenAplicacionTres
     */
    private function setMontoConteptosGanancia($phpExcelObject, $enSesion, $gananciaEmpleado, $mesArray, $conceptoGananciaOrdenAplicacionCero, $conceptoGananciaOrdenAplicacionUno, $conceptoGananciaOrdenAplicacionDos, $conceptoGananciaOrdenAplicacionTres) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $sumarMesesSac = 0;

        // Si la exportación NO es por sesión.
        if (!$enSesion) {
            $liquidacion = $gananciaEmpleado->getLiquidacionEmpleado()->getLiquidacion();
        } else {
            $liquidacion = $this->get('session')->get('liquidacion');
        }
        switch ($liquidacion->getFechaCierreNovedades()->format("n")) {
            case 6:
                if ($liquidacion->getTipoLiquidacion()->getId() === TipoLiquidacion::__SAC) {
                    $sumarMesesSac = 1;
                }
                break;
            case in_array($liquidacion->getFechaCierreNovedades()->format("n"), range(7, 11)):
                $sumarMesesSac = 1;
                break;
            case 12:
                if ($liquidacion->getTipoLiquidacion()->getId() === TipoLiquidacion::__SAC) {
                    $sumarMesesSac = 1;
                } else if ($liquidacion->getTipoLiquidacion()->getId() === TipoLiquidacion::__HABITUAL) {
                    $sumarMesesSac = 2;
                }
                break;
        }

        $mesLiquidacion = $liquidacion->getFechaCierreNovedades()->format("n") + $sumarMesesSac;

        $index = 15;

        $sumaOtrosIngresos = 0;

        foreach ($conceptoGananciaOrdenAplicacionCero as $conceptoGanancia) {
            // Si la exportación NO es por sesión.
            if (!$enSesion) {
                $conceptoGananciaCalculadoArray = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGananciaCalculado')
                        ->getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia);
            } else {
                $conceptoGananciaCalculadoArray = $this->
                        getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia);
            }

            $monto = 0;

            foreach ($conceptoGananciaCalculadoArray as $conceptoGananciaCalculado) {
                $monto += $conceptoGananciaCalculado->getMonto();
            }

            $phpExcelObject->getActiveSheet()->setCellValue($mesArray[$mesLiquidacion] . $index, $monto);

            $sumaOtrosIngresos += $monto;
            $index++;
        }

        // 13 - HABER NETO MES
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . '13', //
                        $gananciaEmpleado->getHaberNeto() - $sumaOtrosIngresos);


        // HABERES NETOS ACUMULADOS
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, //
                        $gananciaEmpleado->getHaberNetoAcumulado());

        $index++;

        foreach ($conceptoGananciaOrdenAplicacionUno as $conceptoGanancia) {
            // Si la exportación NO es por sesión.
            if (!$enSesion) {
                $conceptoGananciaCalculadoArray = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGananciaCalculado')
                        ->getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia);
            } else {
                $conceptoGananciaCalculadoArray = $this->
                        getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia);
            }

            $monto = 0;

            foreach ($conceptoGananciaCalculadoArray as $conceptoGananciaCalculado) {
                $monto += $conceptoGananciaCalculado->getMonto();
            }

            $phpExcelObject->getActiveSheet()
                    ->setCellValue($mesArray[$mesLiquidacion] . $index, $monto);

            $index++;
        }

        // RESULTADO NETO
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getResultadoNeto());

        foreach ($conceptoGananciaOrdenAplicacionDos as $conceptoGanancia) {
            // Si la exportación NO es por sesión.
            if (!$enSesion) {
                $conceptoGananciaCalculadoArray = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGananciaCalculado')
                        ->getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia);
            } else {
                $conceptoGananciaCalculadoArray = $this->
                        getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia);
            }

            $monto = 0;

            foreach ($conceptoGananciaCalculadoArray as $conceptoGananciaCalculado) {
                $monto += $conceptoGananciaCalculado->getMonto();
            }

            $phpExcelObject->getActiveSheet()
                    ->setCellValue($mesArray[$mesLiquidacion] . $index, $monto);

            $index++;
        }

        // DIFERENCIA
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getDiferencia());


        foreach ($conceptoGananciaOrdenAplicacionTres as $conceptoGanancia) {
            // Si la exportación NO es por sesión.
            if (!$enSesion) {
                $conceptoGananciaCalculadoArray = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGananciaCalculado')
                        ->getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia);
            } else {
                $conceptoGananciaCalculadoArray = $this->
                        getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia);
            }

            $monto = 0;

            foreach ($conceptoGananciaCalculadoArray as $conceptoGananciaCalculado) {
                $monto += $conceptoGananciaCalculado->getMonto();
            }

            $phpExcelObject->getActiveSheet()
                    ->setCellValue($mesArray[$mesLiquidacion] . $index, $monto);

            $index++;
        }

//        // Devolucion
//
//        $rangoRemuneracion = $gananciaEmpleado->getLiquidacionEmpleado()
//                        ->getEmpleado()->getRangoRemuneracion();
//
//        if ($rangoRemuneracion == null || $rangoRemuneracion->getAplicaGanancias()) {
//            $devolucion = 0;
//        } else {
//            $devolucion = $gananciaEmpleado->getDiferencia() - $gananciaEmpleado->getTotalDeducciones();
//        }
//
//        $phpExcelObject->getActiveSheet()
//                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $devolucion);
        // TOTAL DEDUCCIONES
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getTotalDeducciones());

        // GANANCIA SUJETA A IMPUESTO 
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index, $gananciaEmpleado->getGananciaSujetaImpuesto());

        $index += 2;

        // PORCENTAJE TABLA
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getPorcentajeASumar());

        // FIJO TABLA
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getMontoFijo());


        // MONTO SIN EXCEDENTE
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getMontoSinExcedente());

        // MONTO EXCEDENTE
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getExcedente());

        // TOTAL IMPUESTO DETERMINADO
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getTotalImpuesto());

        // IMPUESTO RETENIDO ANUAL
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, //
                        $gananciaEmpleado->getImpuestoRetenidoAnual() - $gananciaEmpleado->getSaldoImpuestoMes());
        
        /** RETENCIONES OTROS EMPLEOS F572 **/
        $totalRetencionOtrosEmpleos = 0;
        if (!$enSesion) {
            // Lo saco por base 
            $formulario572 = $gananciaEmpleado->getLiquidacionEmpleado()->getEmpleado()->getFormulario572();
            if ($formulario572 != null) {
                $fechaF572 = $formulario572->getFechaFormulario();

                $ordenAplicacionRetencionOtrosEmpleos = 5;
                $conceptosFormulario572 = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoFormulario572')
                        ->getConceptosFormulario572ByOrdenAplicacionTipoConcepto($formulario572, $ordenAplicacionRetencionOtrosEmpleos);

                foreach ($conceptosFormulario572 as $conceptoFormulario572) {

                    $montoASumar = $conceptoFormulario572->getMonto();

                    $conceptoGanancia = $conceptoFormulario572->getConceptoGanancia();

                    if ($conceptoFormulario572->getDetalleConceptoFormulario572Aplicado() != null) {
                        if ($conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->getAplicado()) {
                            $montoASumar = 0;
                        } else {
                            $montoASumar = $conceptoFormulario572->getMonto() - $conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->getMontoAplicado();
                        }
                    }

                    if ($fechaF572->format('n') == $mesLiquidacion) {
                        $totalRetencionOtrosEmpleos += $montoASumar;
                    }
                }
            }
        } else {
            // de la session
            $conceptoGanancia = $em
                ->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                ->findOneByCodigo572(ConceptoGanancia::__CODIGO_RETENCION_OTROS_EMPLEADOR);
            
            $conceptoGananciaCalculadoArray = $this->
                    getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia);
            
             foreach ($conceptoGananciaCalculadoArray as $conceptoGananciaCalculado) {
                $totalRetencionOtrosEmpleos += $conceptoGananciaCalculado->getMonto();
            }
        }
        
        // Seteo el valor
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $totalRetencionOtrosEmpleos);
        
        /** FIN RETENCIONES OTROS EMPLEOS **/

        // SALDO IMPUESTO MES
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index, $gananciaEmpleado->getSaldoImpuestoMes());
    }

    /**
     * 
     * @param type $phpExcelObject
     * @param type $cantidadConceptosOrdenAplicacionCero
     * @param type $cantidadConceptosOrdenAplicacionUno
     * @param type $cantidadConceptosOrdenAplicacionDos
     * @param type $cantidadConceptosOrdenAplicacionTres
     */
    private function initMontosACero($phpExcelObject, $cantidadConceptosOrdenAplicacionCero, $cantidadConceptosOrdenAplicacionUno, $cantidadConceptosOrdenAplicacionDos, $cantidadConceptosOrdenAplicacionTres) {

        $cantidadTotalConceptos = $cantidadConceptosOrdenAplicacionCero +
                $cantidadConceptosOrdenAplicacionUno +
                $cantidadConceptosOrdenAplicacionDos +
                $cantidadConceptosOrdenAplicacionTres;

        $rowOtrosIngresos = 14;

        $rowDeduccionesGenerales = $rowOtrosIngresos + $cantidadConceptosOrdenAplicacionCero + 2;

        $rowInicio = 13;

        $rowEspacioEnBlanco = $rowInicio + $cantidadTotalConceptos + 8;

        $rowFinal = $rowInicio + $cantidadTotalConceptos + 17;

        for ($index = $rowInicio; $index < $rowFinal; $index++) {
            for ($col = 'D'; $col !== 'R'; $col ++) {

                if ($index != $rowOtrosIngresos && $index != $rowDeduccionesGenerales && $index != $rowEspacioEnBlanco) {
                    $phpExcelObject->getActiveSheet()
                            ->setCellValue($col . $index, '0');
                }
            }
        }
    }

    /**
     * Retorna el número de legajo correspondiente según el último tipo de contrato
     * asignado al <code>empleado</code> recibido como parámetro.
     * 
     * @param type $empleado
     * @return type
     */
    private function getUltimoNroLegajo($empleado) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('siguiente_nro_legajo', 'siguiente_nro_legajo');

        $query = $em->createNativeQuery('
                    SELECT IFNULL(MAX(e.nro_legajo)+1, 1) AS siguiente_nro_legajo
                    FROM empleado e 
                        INNER JOIN (SELECT etc_1.id_empleado, id_tipo_contrato
                                    FROM empleado_tipo_contrato etc_1
                                        INNER JOIN (
                                            SELECT id_empleado, MAX(fecha_desde) AS fecha
                                            FROM empleado_tipo_contrato
                                            WHERE fecha_baja IS NULL
                                            GROUP BY id_empleado) etc_2 ON etc_1.id_empleado = etc_2.id_empleado AND etc_1.fecha_desde = etc_2.fecha
                                    ) etc ON e.id = etc.id_empleado                              
                    WHERE etc.id_tipo_contrato = ?', $rsm);

        $query->setParameter(1, $empleado->getTipoContratacionActual()->getTipoContrato()->getId());

        $result = $query->getResult();

        return $result[0]['siguiente_nro_legajo'];
    }

    /**
     * 
     * @param type $gananciaEmpleado
     * @param type $conceptoGanancia
     * @return type
     */
    private function getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia) {

        $conceptosGananciaCalculado = $gananciaEmpleado->getConceptos()->toArray();

        return array_filter($conceptosGananciaCalculado, function($conceptoGananciaCalculado) use($conceptoGanancia) {

            $idConceptoGanancia = $conceptoGananciaCalculado->getConceptoGanancia()->getId();

            if ($conceptoGanancia->getId() != $idConceptoGanancia) {
                return false;
            }

            return true;
        });
    }

    /**
     * Actualiza el histórico del pueso del empleado (Área, Sector, Gerencia y Subgerencia) si hubo algún cambio
     * 
     * @param Empleado $empleadoViejo
     * @param Empleado $empleadoNuevo
     */
    private function actualizarDatosPuesto(Empleado $empleadoViejo, Empleado $empleadoNuevo) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $persist = false;

        $eSH = new EmpleadoSectorHistorico();
        if ($empleadoViejo->getArea() !== $empleadoNuevo->getArea()) {
            $eSH->setArea($empleadoViejo->getArea());
            $persist = true;
        }

        if ($empleadoViejo->getSector() !== $empleadoNuevo->getSector()) {
            $eSH->setSector($empleadoViejo->getSector());
            $persist = true;
        }

        if ($empleadoViejo->getGerencia() !== $empleadoNuevo->getGerencia()) {
            $eSH->setGerencia($empleadoViejo->getGerencia());
            $persist = true;
        }

        if ($empleadoViejo->getSubgerencia() !== $empleadoNuevo->getSubgerencia()) {
            $eSH->setSubgerencia($empleadoViejo->getSubgerencia());
            $persist = true;
        }

//      Si cambió algo  
        if ($persist) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $ultimoSHFecha = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoSectorHistorico')
                    ->createQueryBuilder('esh')
                    ->select('MAX(esh.fechaHasta)')
                    ->where('esh.empleado = :idEmpleado')->setParameter('idEmpleado', $empleadoViejo->getId())
                    ->getQuery()
                    ->getSingleScalarResult();

            $eSH->setFechaDesde($ultimoSHFecha ? new DateTime(date('Y-m-d', strtotime($ultimoSHFecha))) : $empleadoViejo->getFechaIngreso());
            $eSH->setFechaHasta(new DateTime());

            $eSH->setEmpleado($empleadoNuevo);

            $em->persist($eSH);
            $em->flush();
        }
    }

    /**
     * Actualiza el histórico de la subcategoría del empleado (Subcategoria) si hubo algún cambio
     * 
     * @param Empleado $empleadoViejo
     * @param Empleado $empleadoNuevo
     */
    private function actualizarDatosSubcategoria(Empleado $empleadoViejo, Empleado $empleadoNuevo, $fechaCambio) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $eSH = new EmpleadoSubcategoriaHistorico();
        if ($empleadoViejo->getSubcategoria() !== $empleadoNuevo->getSubcategoria()) {
            $eSH->setSubcategoria($empleadoViejo->getSubcategoria());

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $ultimoSHFecha = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoSubcategoriaHistorico')
                    ->createQueryBuilder('esh')
                    ->select('MAX(esh.fechaHasta)')
                    ->where('esh.empleado = :idEmpleado')->setParameter('idEmpleado', $empleadoViejo->getId())
                    ->getQuery()
                    ->getSingleScalarResult();

            $eSH->setFechaDesde($ultimoSHFecha ? new DateTime(date('Y-m-d', strtotime($ultimoSHFecha))) : $empleadoViejo->getFechaIngreso());
            $eSH->setFechaHasta($fechaCambio);

            $eSH->setEmpleado($empleadoNuevo);

            $em->persist($eSH);
            $em->flush();
        }
    }

    /**
     * Imprime los recibos anteriores del empleado
     *
     * @Route("/{id}/recibos", name="empleados_recibos")
     * @Method("GET")
     * @Security("has_role('ROLE_IMPRIMIR_RECIBOS_SUELDOS')")
     */
    public function imprimirRecibos($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($id);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar el Empleado.');
        }

        $liqEmpleados = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
                ->createQueryBuilder('le')
                ->select('GROUP_CONCAT(le.id) AS ids_le')
                ->innerJoin('le.empleado', 'e')
                ->innerJoin('le.liquidacion', 'l')
                ->where('e.id = (:idEmpleado)')
                ->andWhere('l.numero <> -1')
                ->setParameter('idEmpleado', $empleado->getId())
                ->groupBy('e.id')
                ->getQuery()
                //->getSingleResult();
                ->getOneOrNullResult();
        
        if ($liqEmpleados == null) {
            $link = $this->generateUrl('empleados');
            $mensaje = "No existe recibos de sueldos para el empleado.<br><br>";
            $mensaje .= '<a href="' . $link . '">Volver</a>';
            return new Response($mensaje);
        }

        return $this->forward('ADIFRecursosHumanosBundle:Liquidacion:imprimirRecibo', array(
                    'ids' => json_encode(explode(',', $liqEmpleados['ids_le']))
        ));
    }

    /**
     * 
     * @param type $em
     * @param Empleado $empleado
     * @param type $idRangoRemuneracion
     */
    private function chequearHistoricoRangoRemuneracion($em, Empleado $empleado, $idRangoRemuneracion = null) {
        if ($idRangoRemuneracion == null) {
            $this->crearHistoricoRangoRemuneracion($em, $empleado);
        } else if ($idRangoRemuneracion != $empleado->getRangoRemuneracion()->getId()) {
            $this->actualizarHistoricoRangoRemuneracion($em, $empleado);
            $this->actualizarMontosCargasFamiliares572($em, $empleado);
        }
    }

    /**
     * 
     * @param type $em
     * @param Empleado $empleado
     */
    private function crearHistoricoRangoRemuneracion($em, Empleado $empleado) {
        $historicoRangoRemuneracion = new EmpleadoHistoricoRangoRemuneracion();
        $historicoRangoRemuneracion->setFechaDesde(new DateTime())
                ->setEmpleado($empleado)
                ->setRangoRemuneracion($empleado->getRangoRemuneracion());
        $em->persist($historicoRangoRemuneracion);
    }

    /**
     * 
     * @param type $em
     * @param Empleado $empleado
     */
    private function actualizarHistoricoRangoRemuneracion($em, Empleado $empleado) {

        $now = new DateTime();

        $historicoRangoRemuneracionActual = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoHistoricoRangoRemuneracion')->findOneBy(
                array('empleado' => $empleado, 'fechaHasta' => null), array('id' => 'desc')
        );

        $historicoRangoRemuneracionActual->setFechaHasta($now);
        $em->persist($historicoRangoRemuneracionActual);

        $this->crearHistoricoRangoRemuneracion($em, $empleado);
    }

    /**
     * 
     * @param type $em
     * @param Empleado $empleado
     */
    private function actualizarMontosCargasFamiliares572($em, Empleado $empleado) {
        if ($empleado->getFormulario572() != null) {
            foreach ($empleado->getFormulario572()->getCargasFamiliares() as $conceptoFormulario572) {
                /* @var $conceptoFormulario572 \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 */
                $conceptoFormulario572->setMonto($em->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')->
                                findOneBy(
                                        array(
                                            'rangoRemuneracion' => $empleado->getRangoRemuneracion(),
                                            'conceptoGanancia' => $conceptoFormulario572->getConceptoGanancia()
//                                            'vigente' => 1
                                        )
                                )->getValorTope() / 12
                );
            }
        }
    }

    /**
     * @Route("/autocomplete/form", name="autocomplete_empleado")
     *@Security("has_role('ROLE_RRHH_VISTA_EMPLEADOS') or has_role('ROLE_MENU_ADMINISTRACION_FONDOS_ANTICIPO')")
     */
    public function getEmpleadosAction(Request $request) {
        $term = $request->query->get('term', null);

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleados = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')
                ->createQueryBuilder('e')
                ->innerJoin('e.persona', 'p')
                ->where('upper(p.apellido) LIKE :term')
                ->orWhere('upper(p.nombre) LIKE :term')
                ->orWhere('e.nroLegajo LIKE :term')
                ->orderBy('p.apellido, p.nombre', 'DESC')
                ->setParameter('term', '%' . strtoupper($term) . '%')
                ->getQuery()
                ->getResult();

        $jsonResult = [];

        foreach ($empleados as $empleado) {
            $jsonResult[] = array(
                'id' => $empleado->getId(),
                'razonSocial' => $empleado->getPersona()->getApellido() . ', ' . $empleado->getPersona()->getNombre(),
                'dni' => $empleado->getPersona()->getNroDocumento(),
                'legajo' => $empleado->getNroLegajo()
            );
        }

        return new JsonResponse($jsonResult);
    }

    /**
     * Genera el Informe Estimado de Impuesto a las Ganancias en un Excel.
     *
     * @Route("/{id}/excel_res", name="impuesto_ganancias_excel_resolucion")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Empleado:show.html.twig")
     * @Security("has_role('ROLE_RRHH_VISTA_GANANCIAS')")
     */
    public function getEstimadoImpuestoGananciasExcelResolucion(Request $request, $id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($id);

        $anio = 2015;

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar el Empleado.');
        }



        $gananciaEmpleadoArray = $em->getRepository('ADIFRecursosHumanosBundle:GananciaEmpleadoResolucion')
                ->createQueryBuilder('ger')
                ->innerJoin('ger.liquidacionEmpleado', 'le')
                ->innerJoin('le.liquidacion', 'l')
                ->innerJoin('le.empleado', 'e')
                ->where('YEAR(l.fechaCierreNovedades) = ' . $anio)
                ->andWhere('e.id = ' . $id)
                ->getQuery()
                ->getResult();
        if (empty($gananciaEmpleadoArray)) {
            $this->get('session')->getFlashBag()->add(
                    'warning', 'No se puede obtener el Impuesto a las Ganancias de ese año'
            );
            return $this->redirect($request->headers->get('referer'));
        }

        /*         * ****** CONFIGURACION EXCEL ************* */

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()
                ->setCreator("Impuesto a las Ganancias")
                ->setLastModifiedBy("Impuesto Ganancias")
                ->setTitle("Impuesto Ganancias")
                ->setSubject("Impuesto a las Ganancias")
                ->setDescription("Impuesto a las Ganancias.")
                ->setKeywords("impuesto ganancias")
                ->setCategory("Impuesto a las Ganancias");

        $phpExcelObject->getActiveSheet()->setTitle('Impuesto a las Ganancias');


        /*         * ****** PARTE ESTATICA DEL EXCEL ************* */

        $nombreMesArray = array(
            'D' => 'ENERO',
            'E' => 'FEBRERO',
            'F' => 'MARZO',
            'G' => 'ABRIL',
            'H' => 'MAYO',
            'I' => 'JUNIO',
            'J' => 'SAC 1ER SEMESTRE',
            'K' => 'JULIO',
            'L' => 'AGOSTO',
            'M' => 'SEPTIEMBRE',
            'N' => 'OCTUBRE',
            'O' => 'NOVIEMBRE',
            'P' => 'SAC 2DO SEMESTRE',
            'Q' => 'DICIEMBRE',
        );

        $mesArray = array(
            '1' => 'D',
            '2' => 'E',
            '3' => 'F',
            '4' => 'G',
            '5' => 'H',
            '6' => 'I',
            '7' => 'J',
            '8' => 'K',
            '9' => 'L',
            '10' => 'M',
            '11' => 'N',
            '12' => 'O',
            '13' => 'P',
            '14' => 'Q',
        );

        $styleBGLightGray = array(
            'font' => array(
                'color' => array('rgb' => '000000')
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'F9F9F9')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $phpExcelObject->getActiveSheet()

                // 1
                ->mergeCells('A1:O1')
                ->setCellValue('A1', 'IMPUESTO A LAS GANANCIAS')

                // 2
                ->mergeCells('A2:O2')

                // 3                
                ->setCellValue('A3', 'APELLIDO Y NOMBRE')
                ->mergeCells('B3:D3')
                ->setCellValue('B3', $empleado->getPersona()->__toString())

                // 4                
                ->setCellValue('A4', 'LEGAJO')
                ->mergeCells('B4:D4')
                ->setCellValue('B4', $empleado->getNroLegajo())


                // 5                
                ->setCellValue('A5', 'CUIL')
                ->mergeCells('B5:D5')
                ->setCellValue('B5', $empleado->getPersona()->getCuil())

                // 6               
                ->setCellValue('A6', 'RANGO')
                ->mergeCells('B6:D6');


        $claseRangoRemuneracion = $this->get('adif.empleado_historico_rango_remuneracion_service')->getRangoRemuneracionByEmpleadoAndAnio($empleado, 2014);

        $phpExcelObject->getActiveSheet()
                ->setCellValue('B6', '$ ' . number_format($claseRangoRemuneracion->getMontoDesde(), 2, ',', '.')
                        . ' a ' .
                        '$ ' . number_format($claseRangoRemuneracion->getMontoHasta(), 2, ',', '.'));

        // 7                
        $phpExcelObject->getActiveSheet()
                ->setCellValue('A7', 'FECHA')
                ->mergeCells('B7:D7')
                ->setCellValue('B7', (new DateTime())->format("d/m/Y"));

        // 10 
        for ($col = 'D'; $col !== 'R'; $col ++) {
            $phpExcelObject->getActiveSheet()->setCellValue($col . '10', 'MES');
        }

        // 11
        foreach ($nombreMesArray as $col => $mes) {
            $phpExcelObject->getActiveSheet()->setCellValue($col . '11', $mes);
        }

        // 13 - HABER NETO MES
        $phpExcelObject->getActiveSheet()
                ->mergeCells('A13:C13')
                ->setCellValue('A13', 'HABER NETO MES');

        $index = 14;

        // $indexTipoConceptoGananciaOrdenAplicacionCero ;

        $indexTipoConceptoGananciaOrdenAplicacionCero = $index;

        $tipoConceptoGananciaOrdenAplicacionCero = $em->getRepository('ADIFRecursosHumanosBundle:TipoConceptoGanancia')
                ->getTipoConceptoGananciaByOrdenAplicacion(0);

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index, $tipoConceptoGananciaOrdenAplicacionCero->getDenominacion());

        $conceptoGananciaOrdenAplicacionCero = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                ->getConceptoGananciaByOrdenAplicacion(0);

        $cantidadConceptosOrdenAplicacionCero = count($conceptoGananciaOrdenAplicacionCero);

        $index++;

        foreach ($conceptoGananciaOrdenAplicacionCero as $conceptoGanancia) {
            $phpExcelObject->getActiveSheet()
                    ->mergeCells('A' . $index . ':C' . $index)
                    ->setCellValue('A' . $index, $conceptoGanancia->getDenominacion());

            $phpExcelObject->getActiveSheet()
                    ->getStyle('A' . $index)->applyFromArray($styleBGLightGray);

            $index++;
        }

        // HABERES NETOS ACUMULADOS
        $indexHaberNetoMes = $index;

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'HABERES NETOS ACUMULADOS');


        $indexTipoConceptoGananciaOrdenAplicacionUno = $index;

        $tipoConceptoGananciaOrdenAplicacionUno = $em->getRepository('ADIFRecursosHumanosBundle:TipoConceptoGanancia')
                ->getTipoConceptoGananciaByOrdenAplicacion(1);

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index, $tipoConceptoGananciaOrdenAplicacionUno->getDenominacion());

        $conceptoGananciaOrdenAplicacionUno = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                ->getConceptoGananciaByOrdenAplicacion(1);

        $cantidadConceptosOrdenAplicacionUno = count($conceptoGananciaOrdenAplicacionUno);

        $index++;

        foreach ($conceptoGananciaOrdenAplicacionUno as $conceptoGanancia) {
            $phpExcelObject->getActiveSheet()
                    ->mergeCells('A' . $index . ':C' . $index)
                    ->setCellValue('A' . $index, $conceptoGanancia->getDenominacion());

            $phpExcelObject->getActiveSheet()
                    ->getStyle('A' . $index)->applyFromArray($styleBGLightGray);

            $index++;
        }

        $indexTipoConceptoGananciaOrdenAplicacionDos = $index;

        $tipoConceptoGananciaOrdenAplicacionDos = $em->getRepository('ADIFRecursosHumanosBundle:TipoConceptoGanancia')
                ->getTipoConceptoGananciaByOrdenAplicacion(2);

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index, $tipoConceptoGananciaOrdenAplicacionDos->getDenominacion());

        $conceptoGananciaOrdenAplicacionDos = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                ->getConceptoGananciaByOrdenAplicacion(2);

        $cantidadConceptosOrdenAplicacionDos = count($conceptoGananciaOrdenAplicacionDos);

        $index++;

        foreach ($conceptoGananciaOrdenAplicacionDos as $conceptoGanancia) {
            $phpExcelObject->getActiveSheet()
                    ->mergeCells('A' . $index . ':C' . $index)
                    ->setCellValue('A' . $index, $conceptoGanancia->getDenominacion());

            $phpExcelObject->getActiveSheet()
                    ->getStyle('A' . $index)->applyFromArray($styleBGLightGray);

            $index++;
        }

        $indexTipoConceptoGananciaOrdenAplicacionTres = $index;

        $tipoConceptoGananciaOrdenAplicacionTres = $em->getRepository('ADIFRecursosHumanosBundle:TipoConceptoGanancia')
                ->getTipoConceptoGananciaByOrdenAplicacion(3);

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index, $tipoConceptoGananciaOrdenAplicacionTres->getDenominacion());

        $conceptoGananciaOrdenAplicacionTres = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')
                ->getConceptoGananciaByOrdenAplicacion(3);

        $cantidadConceptosOrdenAplicacionTres = count($conceptoGananciaOrdenAplicacionTres);

        $index++;

        foreach ($conceptoGananciaOrdenAplicacionTres as $conceptoGanancia) {
            $phpExcelObject->getActiveSheet()
                    ->mergeCells('A' . $index . ':C' . $index)
                    ->setCellValue('A' . $index, $conceptoGanancia->getDenominacion());

            $phpExcelObject->getActiveSheet()
                    ->getStyle('A' . $index)->applyFromArray($styleBGLightGray);

            $index++;
        }

//        // Devolucion
//        $phpExcelObject->getActiveSheet()
//                ->mergeCells('A' . $index . ':C' . $index)
//                ->setCellValue('A' . $index++, 'Devolución');
        // TOTAL DEDUCCIONES
        $indexTotalDeducciones = $index;

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'TOTAL DEDUCCIONES');

        // GANANCIA SUJETA A IMPUESTO
        $indexGananciaSujetaImpuesto = $index;

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index, 'GANANCIA SUJETA A IMPUESTO');

        $index += 2;

        // PORCENTAJE TABLA
        $indexPorcentajeTabla = $index;

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'PORCENTAJE TABLA');

        // FIJO TABLA
        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'FIJO TABLA');

        // MONTO SIN EXCEDENTE
        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'MONTO SIN EXCEDENTE');

        // MONTO EXCEDENTE
        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'MONTO EXCEDENTE');

        // TOTAL IMPUESTO DETERMINADO
        $indexTotalImpuestoDeterminado = $index;

        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'TOTAL IMPUESTO DETERMINADO');

        // IMPUESTO RETENIDO ANUAL
        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index++, 'IMPUESTO RETENIDO ANUAL');

        // SALDO IMPUESTO MES
        $phpExcelObject->getActiveSheet()
                ->mergeCells('A' . $index . ':C' . $index)
                ->setCellValue('A' . $index, 'SALDO IMPUESTO MES');

        /*         * ****** FIN PARTE ESTATICA DEL EXCEL ************* */



        /*         * ****** PARTE DINAMICA DEL EXCEL ************* */

        // Si el empleado tiene al menos una GananciaEmpleado asociada
        if (!empty($gananciaEmpleadoArray)) {
            $this->initMontosACero(
                    $phpExcelObject, //
                    $cantidadConceptosOrdenAplicacionCero, //
                    $cantidadConceptosOrdenAplicacionUno, //
                    $cantidadConceptosOrdenAplicacionDos, //
                    $cantidadConceptosOrdenAplicacionTres
            );
            foreach ($gananciaEmpleadoArray as $gananciaEmpleado) {
                $this->setMontoConteptosGananciaResolucion(
                        $phpExcelObject, //
                        false, //
                        $gananciaEmpleado, //
                        $mesArray, //
                        $conceptoGananciaOrdenAplicacionCero, //
                        $conceptoGananciaOrdenAplicacionUno, //
                        $conceptoGananciaOrdenAplicacionDos, //
                        $conceptoGananciaOrdenAplicacionTres);
            }
        }

        // ESTILOS

        $styleAllSheet = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleTitle = array(
            'font' => array(
                'bold' => true,
                'size' => 13,
                'color' => array('rgb' => '000000')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleBGGray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '000000')
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'D8D8D8')
            )
        );

        $styleCenterRed = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FF0000')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleCenterGreen = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '00B050')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleTituloConcepto = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '000000')
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'EAF1DD')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleCenterYellow = array(
            'font' => array(
                'bold' => true,
                'size' => 8,
                'color' => array('rgb' => '000000')
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'FFFFCC')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $styleBoldBlue = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '0000FF')
            )
        );

        $phpExcelObject->getDefaultStyle()->applyFromArray($styleAllSheet);

        for ($col = 'A'; $col !== 'R'; $col ++) {
            $phpExcelObject->getActiveSheet()
                    ->getColumnDimension($col)->setAutoSize(true);

            $phpExcelObject->getActiveSheet()
                    ->getStyle($col)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }

        for ($index = 3; $index <= 12; $index++) {
            $phpExcelObject->getActiveSheet()
                    ->mergeCells('B' . $index . ':C' . $index);
        }

        // 1
        $phpExcelObject->getActiveSheet()->getStyle('A1')->applyFromArray($styleTitle);
        // 3
        $phpExcelObject->getActiveSheet()->getStyle('A3')->applyFromArray($styleBGGray);
        // 4
        $phpExcelObject->getActiveSheet()->getStyle('A4')->applyFromArray($styleBGGray);

        $phpExcelObject->getActiveSheet()
                ->getStyle('B4')->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // 5
        $phpExcelObject->getActiveSheet()
                ->getStyle('A5')->applyFromArray($styleBGGray);

        // 6
        $phpExcelObject->getActiveSheet()
                ->getStyle('A6')->applyFromArray($styleBGGray);

        // 7
        $phpExcelObject->getActiveSheet()
                ->getStyle('A7')->applyFromArray($styleBGGray);

        // 10 
        for ($col = 'D'; $col !== 'R'; $col ++) {
            $phpExcelObject->getActiveSheet()->getStyle($col . '10')->applyFromArray($styleCenterYellow);
        }

        // 11 
        for ($col = 'D'; $col !== 'R'; $col ++) {
            $phpExcelObject->getActiveSheet()->getStyle($col . '11')->applyFromArray($styleBGGray);

            $phpExcelObject->getActiveSheet()
                    ->getStyle($col . '11:' . $col . '60')->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $phpExcelObject->getActiveSheet()
                    ->getStyle($col . '11:' . $col . '60')
                    ->getNumberFormat()->setFormatCode('"$" #,##0.00');
        }

        // 13 - HABER NETO MES
        $phpExcelObject->getActiveSheet()->getStyle('A13:Q13')->applyFromArray($styleBoldBlue);

        $phpExcelObject->getActiveSheet()
                ->getStyle('A13:Q13')
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // $indexTipoConceptoGananciaOrdenAplicacionCero
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexTipoConceptoGananciaOrdenAplicacionCero . ':Q' . $indexTipoConceptoGananciaOrdenAplicacionCero)
                ->applyFromArray($styleTituloConcepto);

        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexTipoConceptoGananciaOrdenAplicacionCero . ':Q' . $indexTipoConceptoGananciaOrdenAplicacionCero)
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // HABERES NETOS ACUMULADOS
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexHaberNetoMes . ':Q' . $indexHaberNetoMes)
                ->applyFromArray($styleCenterRed);

        // $indexTipoConceptoGananciaOrdenAplicacionUno
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexTipoConceptoGananciaOrdenAplicacionUno . ':Q' . $indexTipoConceptoGananciaOrdenAplicacionUno)
                ->applyFromArray($styleTituloConcepto);

        // $indexTipoConceptoGananciaOrdenAplicacionDos
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexTipoConceptoGananciaOrdenAplicacionDos . ':Q' . $indexTipoConceptoGananciaOrdenAplicacionDos)
                ->applyFromArray($styleTituloConcepto);

        // $indexTipoConceptoGananciaOrdenAplicacionTres
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexTipoConceptoGananciaOrdenAplicacionTres . ':Q' . $indexTipoConceptoGananciaOrdenAplicacionTres)
                ->applyFromArray($styleTituloConcepto);

        // TOTAL DEDUCCIONES     
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexTotalDeducciones . ':Q' . $indexTotalDeducciones)
                ->applyFromArray($styleCenterGreen);

        // GANANCIA SUJETA IMPUESTO
        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexGananciaSujetaImpuesto . ':Q' . $indexGananciaSujetaImpuesto)
                ->getFont()->setBold(true);

        $phpExcelObject->getActiveSheet()
                ->getStyle('A' . $indexGananciaSujetaImpuesto . ':Q' . $indexGananciaSujetaImpuesto)
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // PORCENTAJE TABLA
        $phpExcelObject->getActiveSheet()
                ->getStyle('D' . $indexPorcentajeTabla . ':Q' . $indexPorcentajeTabla)
                ->getNumberFormat()->applyFromArray(
                array(
                    'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                )
        );

        // 
        for ($index = $indexPorcentajeTabla; $index < $indexPorcentajeTabla + 4; $index++) {
            $phpExcelObject->getActiveSheet()
                    ->getStyle('A' . $index . ':Q' . $index)
                    ->applyFromArray($styleBoldBlue);
        }

        for ($index = $indexPorcentajeTabla; $index < $indexTotalImpuestoDeterminado + 3; $index++) {
            $phpExcelObject->getActiveSheet()
                    ->getStyle('A' . $index . ':Q' . $index)
                    ->getFont()->setBold(true);
        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');

        $response = $this->get('phpexcel')->createStreamedResponse($writer);

        $nombreArchivo = AdifApi::stringCleaner('IG_Resolucion_2013_' . $empleado->getPersona() . '_' . (new DateTime())->format("d-m-Y"));

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=' . $nombreArchivo . '.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    /**
     * 
     * @param type $phpExcelObject
     * @param type $enSesion
     * @param type $gananciaEmpleado
     * @param type $mesArray
     * @param type $conceptoGananciaOrdenAplicacionCero
     * @param type $conceptoGananciaOrdenAplicacionUno
     * @param type $conceptoGananciaOrdenAplicacionDos
     * @param type $conceptoGananciaOrdenAplicacionTres
     */
    private function setMontoConteptosGananciaResolucion($phpExcelObject, $enSesion, $gananciaEmpleado, $mesArray, $conceptoGananciaOrdenAplicacionCero, $conceptoGananciaOrdenAplicacionUno, $conceptoGananciaOrdenAplicacionDos, $conceptoGananciaOrdenAplicacionTres) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $sumarMesesSac = 0;

        $liquidacion = $gananciaEmpleado->getLiquidacionEmpleado()->getLiquidacion();
        switch ($liquidacion->getFechaCierreNovedades()->format("n")) {
            case 6:
                if ($liquidacion->getTipoLiquidacion()->getId() === TipoLiquidacion::__SAC) {
                    $sumarMesesSac = 1;
                }
                break;
            case in_array($liquidacion->getFechaCierreNovedades()->format("n"), range(7, 11)):
                $sumarMesesSac = 1;
                break;
            case 12:
                if ($liquidacion->getTipoLiquidacion()->getId() === TipoLiquidacion::__SAC) {
                    $sumarMesesSac = 1;
                } else if ($liquidacion->getTipoLiquidacion()->getId() === TipoLiquidacion::__HABITUAL) {
                    $sumarMesesSac = 2;
                }
                break;
        }

        $mesLiquidacion = $liquidacion->getFechaCierreNovedades()->format("n") + $sumarMesesSac;

        $index = 15;

        $sumaOtrosIngresos = 0;

        foreach ($conceptoGananciaOrdenAplicacionCero as $conceptoGanancia) {
                $conceptoGananciaCalculadoArray = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGananciaCalculadoResolucion')
                        ->getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia);
            $monto = 0;

            foreach ($conceptoGananciaCalculadoArray as $conceptoGananciaCalculado) {
                $monto += $conceptoGananciaCalculado->getMonto();
            }

            $phpExcelObject->getActiveSheet()->setCellValue($mesArray[$mesLiquidacion] . $index, $monto);

            $sumaOtrosIngresos += $monto;
            $index++;
        }

        // 13 - HABER NETO MES
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . '13', //
                        $gananciaEmpleado->getHaberNeto() - $sumaOtrosIngresos);


        // HABERES NETOS ACUMULADOS
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, //
                        $gananciaEmpleado->getHaberNetoAcumulado());

        $index++;

        foreach ($conceptoGananciaOrdenAplicacionUno as $conceptoGanancia) {
                $conceptoGananciaCalculadoArray = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGananciaCalculadoResolucion')
                        ->getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia);

            $monto = 0;

            foreach ($conceptoGananciaCalculadoArray as $conceptoGananciaCalculado) {
                $monto += $conceptoGananciaCalculado->getMonto();
            }

            $phpExcelObject->getActiveSheet()
                    ->setCellValue($mesArray[$mesLiquidacion] . $index, $monto);

            $index++;
        }

        // RESULTADO NETO
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getResultadoNeto());

        foreach ($conceptoGananciaOrdenAplicacionDos as $conceptoGanancia) {
                $conceptoGananciaCalculadoArray = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGananciaCalculadoResolucion')
                        ->getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia);

            $monto = 0;

            foreach ($conceptoGananciaCalculadoArray as $conceptoGananciaCalculado) {
                $monto += $conceptoGananciaCalculado->getMonto();
            }

            $phpExcelObject->getActiveSheet()
                    ->setCellValue($mesArray[$mesLiquidacion] . $index, $monto);

            $index++;
        }

        // DIFERENCIA
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getDiferencia());


        foreach ($conceptoGananciaOrdenAplicacionTres as $conceptoGanancia) {
                $conceptoGananciaCalculadoArray = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGananciaCalculadoResolucion')
                        ->getConceptoGananciaCalculadoByGananciaEmpleadoAndConceptoGanancia($gananciaEmpleado, $conceptoGanancia);

            $monto = 0;

            foreach ($conceptoGananciaCalculadoArray as $conceptoGananciaCalculado) {
                $monto += $conceptoGananciaCalculado->getMonto();
            }

            $phpExcelObject->getActiveSheet()
                    ->setCellValue($mesArray[$mesLiquidacion] . $index, $monto);

            $index++;
        }

//        // Devolucion
//
//        $rangoRemuneracion = $gananciaEmpleado->getLiquidacionEmpleado()
//                        ->getEmpleado()->getRangoRemuneracion();
//
//        if ($rangoRemuneracion == null || $rangoRemuneracion->getAplicaGanancias()) {
//            $devolucion = 0;
//        } else {
//            $devolucion = $gananciaEmpleado->getDiferencia() - $gananciaEmpleado->getTotalDeducciones();
//        }
//
//        $phpExcelObject->getActiveSheet()
//                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $devolucion);
        // TOTAL DEDUCCIONES
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getTotalDeducciones());

        // GANANCIA SUJETA A IMPUESTO 
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index, $gananciaEmpleado->getGananciaSujetaImpuesto());

        $index += 2;

        // PORCENTAJE TABLA
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getPorcentajeASumar());

        // FIJO TABLA
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getMontoFijo());


        // MONTO SIN EXCEDENTE
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getMontoSinExcedente());

        // MONTO EXCEDENTE
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getExcedente());

        // TOTAL IMPUESTO DETERMINADO
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, $gananciaEmpleado->getTotalImpuesto());

        // IMPUESTO RETENIDO ANUAL
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index++, //
                        $gananciaEmpleado->getImpuestoRetenidoAnual() - $gananciaEmpleado->getSaldoImpuestoMes());

        // SALDO IMPUESTO MES
        $phpExcelObject->getActiveSheet()
                ->setCellValue($mesArray[$mesLiquidacion] . $index, $gananciaEmpleado->getSaldoImpuestoMes());
    }
	
	/**
	* @Route("/relacionar_puestos_superiores/", name="relacionar_puestos_superiores")
	* /empleados/relacionar_puestos_superiores/
	*/
	public function relacionarPuestosSuperioresAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('legajo', 'legajo');
		$rsm->addScalarResult('puesto', 'puesto');
		$rsm->addScalarResult('id_superior', 'id_superior');

		$sql = "
			SELECT 	
				i.id,
				i.legajo,
				i.puesto,
				esup.id AS id_superior
			FROM importacion_relaciones_puesto_superior i
			LEFT JOIN empleado esup ON i.legajo_superior = esup.nro_legajo
			LEFT JOIN persona psup ON esup.id_persona = psup.id
			";
		
		$em->getConnection()->beginTransaction();
		
		try {
		
			$query = $em->createNativeQuery($sql, $rsm);
			
			$result = $query->getResult();
			
			foreach($result as $i => $item) {
				
				$empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->findOneByNroLegajo($item['legajo']);
				$puesto = $superior = null;
				
				if ($empleado) {
					
					if ($item['puesto'] != null) {
						$puesto = $em->getRepository('ADIFRecursosHumanosBundle:Puesto')->findOneByDenominacion($item['puesto']);
					}
					
					if ($item['id_superior'] != null) {
						$superior = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($item['id_superior']);
					}
					
					if ($puesto) {
						$empleado->setPuesto($puesto);
					}
					
					if ($superior) {
						$empleado->setSuperior($superior);
					}
					
					
					$em->persist($empleado);
				}
			}
			
			$em->flush();
			$em->getConnection()->commit();
			
			return new Response("Proceso exitoso.");
			
		} catch (Exception $e) {
		
			$em->getConnection()->rollback();
			$em->close();
			
			return new Response("Fallo.");
		}
		
    }

	/**
	* Metodo que comprueba si se subio alguna foto por el formulario de alta/edicion 
	* de empleados
	*/
	private function subioFoto()
	{
		$arrFoto = isset($_FILES['adif_recursoshumanosbundle_empleado']) 
			? $_FILES['adif_recursoshumanosbundle_empleado'] : array();
			
		//var_dump( (isset($arrFoto['size']['foto']) && $arrFoto['size']['foto'] > 0), $empleado->getFoto() );exit;
		
		return (isset($arrFoto['size']['foto']) && $arrFoto['size']['foto'] > 0) ? true : false;
	}

}
