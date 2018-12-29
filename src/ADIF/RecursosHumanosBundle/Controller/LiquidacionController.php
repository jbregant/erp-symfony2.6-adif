<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\BaseBundle\Entity\AdifApi;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Entity\BeneficiarioLiquidacion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\OrdenPagoCargasSociales;
use ADIF\ContableBundle\Entity\RenglonDeclaracionJuradaLiquidacion;
use ADIF\ContableBundle\Entity\RenglonRetencionLiquidacion;
use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\RecursosHumanosBundle\Entity\Concepto;
use ADIF\RecursosHumanosBundle\Entity\Constantes\ConstanteSirhu;
use ADIF\RecursosHumanosBundle\Entity\Convenio;
use ADIF\RecursosHumanosBundle\Entity\Empleado;
use ADIF\RecursosHumanosBundle\Entity\Liquidacion;
use ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado;
use ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleadoConcepto;
use ADIF\RecursosHumanosBundle\Entity\Persona;
use ADIF\RecursosHumanosBundle\Entity\TipoConcepto;
use ADIF\RecursosHumanosBundle\Entity\TipoLiquidacion;
use ADIF\RecursosHumanosBundle\Form\LiquidacionType;
use ADIF\RecursosHumanosBundle\Helper\DocumentoLiquidacionHelper;
use ADIF\RecursosHumanosBundle\Helper\LiquidacionHelper;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use PHPExcel_Settings;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Fill;
use PHPExcel_Style_NumberFormat;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ZipArchive;
use ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia;
use ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572;
use ADIF\RecursosHumanosBundle\Entity\Formulario572;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoContrato;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * Liquidacion controller.
 *
 * @Route("/liquidaciones")
 * @Security("has_role('ROLE_RRHH_VISTA_LIQUIDACIONES')")
 */
class LiquidacionController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Liquidaciones' => $this->generateUrl('liquidaciones')
        );
    }

    /**
     * Lists all Liquidacion entities.
     *
     * @Route("/", name="liquidaciones")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
                ->createQueryBuilder('l')
                ->where('l.numero <> -1')
                ->getQuery()
                ->getResult();


        $bread = $this->base_breadcrumbs;
        $bread['Liquidaciones'] = null;

        return array(
            'entities' => $entities,
            'tipo_habitual' => TipoLiquidacion::__HABITUAL,
            'breadcrumbs' => $bread,
            'page_title' => 'Liquidaciones',
            'page_info' => 'Lista de liquidaciones'
        );
    }

    /**
     * Muestra la liquidación en forma de recibos
     *
     * @Route("/liquidacion/vista2/{id}", name="liquidaciones_show_vista2", defaults={"id" = null})
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Liquidacion:show_vista_2.html.twig")
     * @Security("has_role('ROLE_IMPRIMIR_RECIBOS_SUELDOS')")
     */
    public function showVistaReciboAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if ($id) {
            $en_sesion = false;
            $liqEmpleados = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
                    ->createQueryBuilder('le')
                    ->select('le, e, p, s, lec, cv, en, l, g, sg, c, etc, tc, b, con')
                    ->innerJoin('le.empleado', 'e')
                    ->innerJoin('le.liquidacion', 'l')
                    ->innerJoin('le.liquidacionEmpleadoConceptos', 'lec')
                    ->innerJoin('e.persona', 'p')
                    ->innerJoin('e.idSubcategoria', 's')
                    ->leftJoin('e.idGerencia', 'g')
                    ->leftJoin('e.idSubgerencia', 'sg')
                    ->innerJoin('e.tiposContrato', 'etc')
                    ->leftJoin('l.bancoAporte', 'b')
                    ->innerJoin('lec.conceptoVersion', 'cv')
                    ->leftJoin('lec.empleadoNovedad', 'en')
                    ->leftJoin('s.idCategoria', 'c')
                    ->innerJoin('etc.tipoContrato', 'tc')
                    ->leftJoin('c.idConvenio', 'con')
                    ->where('l.id = :idLiquidacion')
                    ->setParameter('idLiquidacion', $id)
                    ->orderBy('e.nroLegajo', 'DESC')
                    ->addOrderBy('cv.codigo * 1', 'ASC')
                    ->getQuery()
                    ->getResult();

            if (!$liqEmpleados) {
                throw $this->createNotFoundException('No se puede encontrar la entidad LiquidacionEmpleado.');
            }

            $liquidacion = $liqEmpleados[0]->getLiquidacion();

//            \Doctrine\Common\Util\Debug::dump($liquidacion);
        } else {
            $en_sesion = true;
            $liquidacion = $this->get('session')->get('liquidacion');
            $liqEmpleados = $this->get('session')->get('liquidacion')->getLiquidacionEmpleados();

            if (!$liqEmpleados) {
                throw $this->createNotFoundException('No se puede encontrar la entidad LiquidacionEmpleado.');
            }
        }

        setlocale(LC_ALL, "es_AR.UTF-8");
        $nombre_liquidacion = ucfirst(strftime("%B %Y", $liquidacion->getFechaCierreNovedades()->getTimestamp()));

        $es_sac = $liquidacion->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC;

        $periodo_liquidacion = strtoupper($es_sac ? 'SAC ' : '') . strftime("%B %Y", $liquidacion->getFechaCierreNovedades()->getTimestamp());

        $bread = $this->base_breadcrumbs;
        $bread['N&ordm; ' . $liquidacion->getNumero() . ' - ' . $nombre_liquidacion] = null;


        return array(
            'breadcrumbs' => $bread,
            'entity' => $liquidacion,
            'liqEmpleados' => $liqEmpleados,
            'es_habitual' => $liquidacion->getTipoLiquidacion()->getId() == TipoLiquidacion::__HABITUAL,
            'en_sesion' => $en_sesion,
            'periodo_liquidacion' => $periodo_liquidacion,
            'tc_remunerativo' => TipoConcepto::__REMUNERATIVO,
            'tc_no_remunerativo' => TipoConcepto::__NO_REMUNERATIVO,
            'tc_aporte' => TipoConcepto::__APORTE,
            'tc_cuota_sindical_aportes' => TipoConcepto::__CUOTA_SINDICAL_APORTES,
            'tc_descuento' => TipoConcepto::__DESCUENTO,
            'tc_ganancias' => TipoConcepto::__CALCULO_GANANCIAS,
            'tc_contribuciones' => TipoConcepto::__CONTRIBUCIONES,
            'page_title' => 'Ver liquidaci&oacute;n',
        );
    }

    /**
     * Finds and displays a Liquidacion entity.
     *
     * @Route("/liquidacion_generar_asiento/{id}", name="liquidaciones_crear_asiento", requirements={"id" = "\d+"}, defaults={"id" = null})
     * @Method("GET")
     * @Template()
     */
    public function crearAsientoAction($id) {
        /* @var $liquidacion Liquidacion */
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if ($id) {
            $liquidacion = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
                    ->createQueryBuilder('l')
                    ->select('l, le, lec, e, p, s, cat, con, g, a, f649, cv, c')
                    ->innerJoin('l.liquidacionEmpleados', 'le')
                    ->innerJoin('le.liquidacionEmpleadoConceptos', 'lec')
                    ->innerJoin('le.empleado', 'e')
                    ->innerJoin('e.persona', 'p')
                    ->innerJoin('e.idSubcategoria', 's')
                    ->innerJoin('s.idCategoria', 'cat')
                    ->innerJoin('cat.idConvenio', 'con')
                    ->leftJoin('e.idGerencia', 'g')
                    ->leftJoin('e.idArea', 'a')
                    ->leftJoin('e.formulario649', 'f649')
                    ->innerJoin('lec.conceptoVersion', 'cv')
                    ->innerJoin('cv.concepto', 'c')
                    ->where('l.id = :idLiquidacion')->setParameter('idLiquidacion', $id)
                    ->orderBy('e.nroLegajo * 1', 'ASC')
                    ->getQuery()
                    ->getSingleResult();
        }

        $this->get('adif.asiento_service')->generarAsientoSueldos($liquidacion, $this->getUser());
    }

    /**
     * Finds and displays a Liquidacion entity.
     *
     * @Route("/liquidacion/{id}", name="liquidaciones_show", requirements={"id" = "\d+"}, defaults={"id" = null})
     * @Method("GET")
     * @Security("has_role('ROLE_IMPRIMIR_RECIBOS_SUELDOS')")
     * @Template()
     */
    public function showAction($id) {
        /* @var $liquidacion Liquidacion */
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if ($id) {
            $en_sesion = false;
            $liquidacion = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
                    ->createQueryBuilder('l')
                    ->select('l, le, lec, e, p, s, cat, con, g, a, f649, cv, c', 'sg')
                    ->innerJoin('l.liquidacionEmpleados', 'le')
                    ->innerJoin('le.liquidacionEmpleadoConceptos', 'lec')
                    ->innerJoin('le.empleado', 'e')
                    ->innerJoin('e.persona', 'p')
                    ->innerJoin('e.idSubcategoria', 's')
                    ->innerJoin('s.idCategoria', 'cat')
                    ->innerJoin('cat.idConvenio', 'con')
                    ->leftJoin('e.idGerencia', 'g')
                    ->leftJoin('e.idArea', 'a')
                    ->leftJoin('e.formulario649', 'f649')
                    ->innerJoin('lec.conceptoVersion', 'cv')
                    ->innerJoin('cv.concepto', 'c')
                    ->leftJoin('e.idSubgerencia', 'sg')
                    ->where('l.id = :idLiquidacion')->setParameter('idLiquidacion', $id)
                    ->orderBy('e.nroLegajo * 1', 'ASC')
                    ->getQuery()
//                    ->useQueryCache(true)
                    ->getSingleResult();
        } else {
            $en_sesion = true;
            $liquidacion = $this->get('session')->get('liquidacion');
        }

        if (!$liquidacion) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Liquidacion.');
        }

        //echo $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getSicossByLiquidacion($liquidacion->getId());die;
        //$emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
//        $renglonDDJJSicore = $this->crearRenglonDeclaracionJurada($liquidacion, ConstanteTipoImpuesto::Ganancias, $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getGananciaByLiquidacion($liquidacion->getId()));
//        $liquidacion->setIdRenglonDeclaracionJuradaSicore($renglonDDJJSicore->getId());
//        $renglonDDJJSicoss = $this->crearRenglonDeclaracionJurada($liquidacion, ConstanteTipoImpuesto::SICOSS, $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getSicossByLiquidacion($liquidacion->getId()));
//        $liquidacion->setIdRenglonDeclaracionJuradaSicoss($renglonDDJJSicoss->getId());
        // Se genera el renglón de retencion
        //$this->crearRenglonesRetencion($liquidacion);
//        // Se generan los asientos de devengamiento de sueldos
//        $total_asiento = 0;
//        $this->get('adif.asiento_service')->generarAsientoSueldos($liquidacion, $this->getUser(), $total_asiento);
//        $this->get('adif.contabilidad_presupuestaria_service')->crearDevengadoSueldosFromLiquidacion($liquidacion);
//        $this->get('adif.contabilidad_presupuestaria_service')->crearEjecutadoSueldosFromLiquidacion($liquidacion);
//        // Se genera la autorización contable de cargas sociales
//        $this->generarAutorizacionContableCargasSociales($liquidacion, $total_asiento);
        //$emContable->flush();
        //die('LISTOOOO LU');        
        // Todos los conceptos que entran en el bruto 1.  
        $conceptosRepo = $em->getRepository('ADIFRecursosHumanosBundle:Concepto');

        $conceptos_bruto_1 = array();
        $conceptos_bruto_2 = array();
        $conceptos_descuentos = array();
        $conceptos_no_remunerativos = array();
        $conceptos_ganancias = $conceptosRepo->findAllByCodigos(array(Concepto::__CODIGO_GANANCIAS));

        if ($liquidacion->getTipoLiquidacion()->getId() == TipoLiquidacion::__ADICIONAL) {
            $conceptos_no_remunerativos = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')
                    ->createQueryBuilder('c')
                    ->select('c')
                    ->innerJoin('c.conceptoLiquidacionAdicional', 'cla')
                    ->getQuery()
                    ->getResult();
        } else {
            if ($liquidacion->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC) {
                // Sólo marcar en conceptos bruto 1, el concepto SAC que corresponda (1er o 2do semestre)
                if ($liquidacion->getFechaCierreNovedades()->format('n') == 6) {
                    $codigoSAC = Concepto::__CODIGO_SAC_1_SEMESTRE;
                } else {
                    if ($liquidacion->getFechaCierreNovedades()->format('n') == 12) {
                        $codigoSAC = Concepto::__CODIGO_SAC_2_SEMESTRE;
                    }
                }
                $conceptos_bruto_1 = $conceptosRepo->findAllByCodigos(array($codigoSAC));

                //$conceptos_descuentos = $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__APORTE));

                $conceptos_aportes_sac = $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__APORTE));
                $conceptos_descuentos_sac = $conceptosRepo->findAllByCodigos(array(Concepto::__CODIGO_ANTICIPO_SUELDO, Concepto::__CODIGO_EMBARGO));
                $conceptos_descuentos = array_merge($conceptos_aportes_sac, $conceptos_descuentos_sac);
            } else {
                $conceptos_bruto_1 = $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__REMUNERATIVO), false);
                $conceptos_bruto_2 = $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__REMUNERATIVO), true);
                $conceptos_descuentos = $conceptosRepo->findAllByTipoConceptoAndNovedad(array(
                    TipoConcepto::__APORTE,
                    TipoConcepto::__DESCUENTO,
                    TipoConcepto::__CUOTA_SINDICAL_APORTES
                ));
                $conceptos_no_remunerativos = $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__NO_REMUNERATIVO));
            }
            $conceptos_ganancias = $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__CALCULO_GANANCIAS));
        }

        // Headers fijos del legajo del empleado
        $headers = array('Legajo', 'CUIL', 'Apellido', 'Nombre', 'Convenio', 'Categor&iacute;a', 'Subcategor&iacute;a', 'Gerencia', 'Subgerencia', 'Area', 'B&aacute;sico', 'Inicio &Uacute;ltimo Contrato');
        $data = array();

        // Recorro los conceptos y los seteo como headers de la tabla
        foreach ($conceptos_bruto_1 as $concepto) {
            /* @var $concepto Concepto */
            $headers[] = $concepto->getDescripcion();
        }
        $headers[] = 'BRUTO 1';

        foreach ($conceptos_bruto_2 as $concepto) {
            /* @var $concepto Concepto */
            $headers[] = $concepto->getDescripcion();
        }
        $headers[] = 'BRUTO 2';
        $headers[] = 'TOTAL REMUNERATIVO CON TOPE';

        foreach ($conceptos_descuentos as $concepto) {
            /* @var $concepto Concepto */
            $headers[] = $concepto->getDescripcion();
        }
        $headers[] = 'TOTAL DESCUENTOS';

        foreach ($conceptos_no_remunerativos as $concepto) {
            /* @var $concepto Concepto */
            $headers[] = $concepto->getDescripcion();
        }
        $headers[] = 'TOTAL NO REMUNERATIVOS';

        foreach ($conceptos_ganancias as $concepto) {
            /* @var $concepto Concepto */
            $headers[] = $concepto->getDescripcion();
        }

        $headers[] = 'REDONDEO';

        $headers[] = 'NETO';

        // Recorro cada empleado de la liquidacion para setear los datos
        foreach ($liquidacion->getLiquidacionEmpleados() as $liquidacionEmpleado) {
            /* @var $liquidacionEmpleado LiquidacionEmpleado */

            // Datos del legajo
            $empleado = $liquidacionEmpleado->getEmpleado();
            $persona = $empleado->getPersona();
            $gerencia = $empleado->getIdGerencia() ? $em->getRepository('ADIFRecursosHumanosBundle:Gerencia')->find($empleado->getIdGerencia()->getId())->getNombre() : '';
            $subgerencia = $empleado->getIdSubgerencia() ? $em->getRepository('ADIFRecursosHumanosBundle:Subgerencia')->find($empleado->getIdSubgerencia()->getId())->getNombre() : '';
            //\Doctrine\Common\Util\Debug::dump( $empleado->getTipoContratacionActual()->getFechaDesde()->format('d/m/Y') ); exit;
            $inicio_ultimo_contrato = $empleado->getTipoContratacionActual()->getFechaDesde()->format('d/m/Y');
            $area = $empleado->getIdArea() ? $em->getRepository('ADIFRecursosHumanosBundle:Area')->find($empleado->getIdArea()->getId())->getNombre() : '';

            $celda_empleado = array($liquidacionEmpleado->getId(), $empleado->getNroLegajo(), $persona->getCuil(), $persona->getApellido(), $persona->getNombre(), $empleado->getSubcategoria()->getCategoria()->getIdConvenio()->getNombre(), $empleado->getSubcategoria()->getCategoria()->getNombre(), $empleado->getSubcategoria()->getNombre(), $gerencia, $subgerencia, $area, number_format($liquidacionEmpleado->getBasico(), 2, ',', '.'), $inicio_ultimo_contrato);

            $conceptosEmpleado = $liquidacionEmpleado->getLiquidacionEmpleadoConceptos();

            foreach ($conceptos_bruto_1 as $concepto) {
                $codigoConcepto = $concepto->getCodigo();
                $conceptoEmpleado = $conceptosEmpleado->filter(
                        function($liquidacionEmpleadoConcepto) use ($codigoConcepto) {
                    return in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getCodigo(), array($codigoConcepto));
                }
                );
                $celda_empleado[] = $conceptoEmpleado->isEmpty() ? 0 : number_format($conceptoEmpleado->first()->getMonto(), 2, ',', '.');
            }
            $celda_empleado[] = number_format($liquidacionEmpleado->getBruto1(), 2, ',', '.');

            foreach ($conceptos_bruto_2 as $concepto) {
                $codigoConcepto = $concepto->getCodigo();
                $conceptoEmpleado = $conceptosEmpleado->filter(
                        function($liquidacionEmpleadoConcepto) use ($codigoConcepto) {
                    return in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getCodigo(), array($codigoConcepto));
                }
                );

                // Por si tiene seteado mas de una novedad del mismo concepto
                $monto_total_concepto = 0;
                if (!$conceptoEmpleado->isEmpty()) {
                    foreach ($conceptoEmpleado as $concepto) {
                        $monto_total_concepto += $concepto->getMonto();
                    }
                }
                $celda_empleado[] = $conceptoEmpleado->isEmpty() ? 0 : number_format($monto_total_concepto, 2, ',', '.');
            }
            $celda_empleado[] = number_format($liquidacionEmpleado->getBruto2(), 2, ',', '.');
            $celda_empleado[] = number_format($liquidacionEmpleado->getMontoRemunerativoConTope(), 2, ',', '.');

            foreach ($conceptos_descuentos as $concepto) {
                $codigoConcepto = $concepto->getCodigo();
                $conceptoEmpleado = $conceptosEmpleado->filter(
                        function($liquidacionEmpleadoConcepto) use ($codigoConcepto) {
                    return in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getCodigo(), array($codigoConcepto));
                }
                );
                $celda_empleado[] = $conceptoEmpleado->isEmpty() ? 0 : number_format($conceptoEmpleado->first()->getMonto(), 2, ',', '.');
            }
            $celda_empleado[] = number_format($liquidacionEmpleado->getDescuentos(), 2, ',', '.');

            foreach ($conceptos_no_remunerativos as $concepto) {
                $codigoConcepto = $concepto->getCodigo();
                $conceptoEmpleado = $conceptosEmpleado->filter(
                        function($liquidacionEmpleadoConcepto) use ($codigoConcepto) {
                    return in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getCodigo(), array($codigoConcepto));
                }
                );
                $celda_empleado[] = $conceptoEmpleado->isEmpty() ? 0 : number_format($conceptoEmpleado->first()->getMonto(), 2, ',', '.');
            }
            $celda_empleado[] = number_format($liquidacionEmpleado->getNoRemunerativo(), 2, ',', '.');

            foreach ($conceptos_ganancias as $concepto) {
                $codigoConcepto = $concepto->getCodigo();
                $conceptoEmpleado = $conceptosEmpleado->filter(
                        function($liquidacionEmpleadoConcepto) use ($codigoConcepto) {
                    return in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getCodigo(), array($codigoConcepto));
                }
                );
                $celda_empleado[] = $conceptoEmpleado->isEmpty() ? 0 : number_format($conceptoEmpleado->first()->getMonto(), 2, ',', '.');
            }

            $celda_empleado[] = number_format($liquidacionEmpleado->getRedondeo(), 2, ',', '.');
            $celda_empleado[] = number_format($liquidacionEmpleado->getNeto(), 2, ',', '.');

            if ($en_sesion) {
                $celda_empleado[] = $liquidacionEmpleado->getEmpleado()->getId();
            }

            $data[] = $celda_empleado;
        }

        // PARA LA CARGA POSTERIOR CON AJAX
        $this->get('session')->set('liquidacion_table_data', $data);

        setlocale(LC_ALL, "es_AR.UTF-8");
        $nombre_liquidacion = ucfirst(strftime("%B %Y", $liquidacion->getFechaCierreNovedades()->getTimestamp()));

        $bread = $this->base_breadcrumbs;
        $bread['N&ordm; ' . $liquidacion->getNumero() . ' - ' . $nombre_liquidacion] = null;

        return array(
            'entity' => $liquidacion,
            // 'es_habitual' => $liquidacion->getTipoLiquidacion()->getId() == TipoLiquidacion::__HABITUAL,
            'en_sesion' => $en_sesion,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver liquidaci&oacute;n',
            'headers' => $headers,
            'data' => $data
        );
    }

    /**
     * Muestra los aportes y contribuciones de una liquidacion.
     *
     * @Route("/liquidacion/contribuciones/{id}", name="liquidaciones_show_contribuciones", defaults={"id" = null})
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_RRHH_VISTA_CONTRIBUCIONES')")
     */
    public function showContribucionesAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if ($id) {
            $en_sesion = false;
            $entity = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find($id);
        } else {
            $en_sesion = true;
            $entity = $this->get('session')->get('liquidacion');
        }

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Liquidacion.');
        }

        // Todos los conceptos de contribuciones
        $conceptosRepo = $em->getRepository('ADIFRecursosHumanosBundle:Concepto');

        $conceptos_contribuciones = $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__CONTRIBUCIONES, TipoConcepto::__CUOTA_SINDICAL_CONTRIBUCIONES));

        $conceptos_aportes = $conceptosRepo->findAllByCodigos(array(100, 101, '101.1', '101.2', 102));

        // Headers fijos del legajo del empleado
        $headers = array('CUIL', 'Apellido', 'Nombre', 'Convenio', 'Categor&iacute;a', 'Subcategor&iacute;a', 'B&aacute;sico', 'BRUTO 1', 'BRUTO 2', 'TOTAL REMUNERATIVO CON TOPE', 'NO REMUNERATIVOS');
        $data = array();

        // Recorro los conceptos y los seteo como headers de la tabla
        foreach ($conceptos_aportes as $concepto) {
            /* @var $concepto Concepto */
            $headers[] = $concepto->getDescripcion();
        }

        // Recorro los conceptos y los seteo como headers de la tabla
        foreach ($conceptos_contribuciones as $concepto) {
            /* @var $concepto Concepto */
            $headers[] = $concepto->getDescripcion();
        }

        $headers[] = 'TOTAL CONTRIBUCIONES';

        // Recorro cada empleado de la liquidacion para setear los datos
        foreach ($entity->getLiquidacionEmpleados() as $liquidacionEmpleado) {
            /* @var $liquidacionEmpleado LiquidacionEmpleado */

            // Datos del legajo
            $empleado = $liquidacionEmpleado->getEmpleado();
            $persona = $empleado->getPersona();
            $celda_empleado = array($liquidacionEmpleado->getId(), $persona->getCuil(), $persona->getApellido(), $persona->getNombre(), $empleado->getSubcategoria()->getCategoria()->getIdConvenio()->getNombre(), $empleado->getSubcategoria()->getCategoria()->getNombre(), $empleado->getSubcategoria()->getNombre(), number_format($liquidacionEmpleado->getBasico(), 2, ',', '.'), number_format($liquidacionEmpleado->getBruto1(), 2, ',', '.'), number_format($liquidacionEmpleado->getBruto2(), 2, ',', '.'), number_format($liquidacionEmpleado->getMontoRemunerativoConTope(), 2, ',', '.'), number_format($liquidacionEmpleado->getNoRemunerativo(), 2, ',', '.'));

            $conceptosEmpleado = $liquidacionEmpleado->getLiquidacionEmpleadoConceptos();

            foreach ($conceptos_aportes as $concepto) {
                $codigoConcepto = $concepto->getCodigo();
                $conceptoEmpleado = $conceptosEmpleado->filter(
                        function($liquidacionEmpleadoConcepto) use ($codigoConcepto) {
                    return in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getCodigo(), array($codigoConcepto));
                }
                );
                $monto_concepto = $conceptoEmpleado->isEmpty() ? 0 : $conceptoEmpleado->first()->getMonto();
                $celda_empleado[] = number_format($monto_concepto, 2, ',', '.');
            }

            $total_contribuciones = 0;
            foreach ($conceptos_contribuciones as $concepto) {
                $codigoConcepto = $concepto->getCodigo();
                $conceptoEmpleado = $conceptosEmpleado->filter(
                        function($liquidacionEmpleadoConcepto) use ($codigoConcepto) {
                    return in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getCodigo(), array($codigoConcepto));
                }
                );
                $monto_contribuciones = $conceptoEmpleado->isEmpty() ? 0 : $conceptoEmpleado->first()->getMonto();
                $total_contribuciones += $monto_contribuciones;
                $celda_empleado[] = number_format($monto_contribuciones, 2, ',', '.');
            }

            $celda_empleado[] = number_format($total_contribuciones, 2, ',', '.');

            $data[] = $celda_empleado;
        }

        // PARA LA CARGA POSTERIOR CON AJAX
        $this->get('session')->set('liquidacion_contribuciones_table_data', $data);

        setlocale(LC_ALL, "es_AR.UTF-8");
        $nombre_liquidacion = ucfirst(strftime("%B %Y", $entity->getFechaCierreNovedades()->getTimestamp()));

        $bread = $this->base_breadcrumbs;
        $bread['N&ordm; ' . $entity->getNumero() . ' - ' . $nombre_liquidacion] = null;

        return array(
            'entity' => $entity,
            'en_sesion' => $en_sesion,
            'nombre_liquidacion' => $nombre_liquidacion,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver contribuciones',
            'headers' => $headers,
            'data' => $data
        );
    }

    /**
     * Cierra y persiste una liquidación.
     *
     * @Route("/cerrar", name="liquidaciones_cerrar")     
     * @Template()
     * @Security("has_role('ROLE_RRHH_ALTA_LIQUIDACIONES')")
     */
    public function cerrarAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $liquidacion = $this->get('session')->get('liquidacion');

        if (!$liquidacion) {
            throw $this->createNotFoundException('No se puede encontrar la Liquidacion.');
        }

        /* @var $liquidacion_mergeada Liquidacion */
        $liquidacion_mergeada = $em->merge($liquidacion);

        if (!$request->request->get('es_sac')) {
            $banco = $em->getRepository('ADIFRecursosHumanosBundle:Banco')->find($request->request->get('banco_aporte'));
            if (!$banco) {
                throw $this->createNotFoundException('No se especifica el Banco.');
            }

            // DATOS ADICIONALES LIQUIDACION
            $liquidacion_mergeada->setObservacion($request->request->get('observacion'));
            $liquidacion_mergeada->setLugarPago($request->request->get('lugar_de_pago'));
            $liquidacion_mergeada->setFechaPago(DateTime::createFromFormat('d/m/Y', $request->request->get('fecha_pago')));
            $liquidacion_mergeada->setFechaUltimoAporte(DateTime::createFromFormat('Y-n-d', $request->request->get('fecha_ultimo_aporte')));
            $liquidacion_mergeada->setBancoAporte($banco);
            $liquidacion_mergeada->setFechaDepositoAporte(DateTime::createFromFormat('d/m/Y', $request->request->get('fecha_deposito_aporte')));
        }

        // Doy de baja las novedades aplicadas al empleado
        foreach ($liquidacion_mergeada->getLiquidacionEmpleados() as $liquidacionEmpleado) {
            /* @var $liquidacionEmpleado LiquidacionEmpleado */
            foreach ($liquidacionEmpleado->getLiquidacionEmpleadoConceptos() as $liquidacionEmpleadoConcepto) {
                /* @var $liquidacionEmpleadoConcepto LiquidacionEmpleadoConcepto */
                if ($liquidacionEmpleadoConcepto->getEmpleadoNovedad()) {
                    $novedadOriginal = $em->getRepository('ADIFRecursosHumanosBundle:EmpleadoNovedad')->find($liquidacionEmpleadoConcepto->getEmpleadoNovedad()->getId());
                    $novedadOriginal->setFechaBaja(new DateTime);
                }
            }
            if (($liquidacionEmpleado->getGananciaEmpleado() != null) && ($liquidacionEmpleado->getEmpleado()->getFormulario572() != null)) {
                $conceptosAplicables = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoFormulario572')->getConceptosFormulario572Aplicables($liquidacionEmpleado->getEmpleado()->getFormulario572());
                foreach ($conceptosAplicables as $conceptoFormulario572) {
                    if ($conceptoFormulario572->getDetalleConceptoFormulario572Aplicado() != null) {
                        if (!($conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->getAplicado())) {
                            $conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->setMontoAplicado($conceptoFormulario572->getMonto() - $conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->getMontoAplicado());
                            $conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->setAplicado(true);
                        }
                    }
                }
            }
            if (($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() === TipoLiquidacion::__SAC) && ($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('m') == 6)) {
                $empleado = $liquidacionEmpleado->getEmpleado();

                $conceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneByCodigo572(ConceptoGanancia::__CODIGO_SAC);
                $conceptoF572Sac = new ConceptoFormulario572();
                $conceptoF572Sac->setConceptoGanancia($conceptoGanancia)
                        ->setMonto($liquidacionEmpleado->getNeto())
                        ->setMesDesde(1)
                        ->setMesHasta(12);
                // Ya tiene f572
                if ($empleado->getFormulario572() != null) {
                    $formulario572 = $empleado->getFormulario572();
                } else {
                    // no tiene f572                    
                    $formulario572 = new Formulario572();
                    $formulario572->setAnio($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Y'))
                            ->setEmpleado($empleado);
                    $em->persist($formulario572);
                }
                $conceptoF572Sac->setFormulario572($formulario572);
                $formulario572->addConcepto($conceptoF572Sac);
            }
        }

        $em->persist($liquidacion_mergeada);
        $em->flush();

        $this->container->get('request')->getSession()->getFlashBag()
                        ->add('success', 'Liquidaci&oacute;n cerrada con &eacute;xito.');
        
        $idLiquidacion = $liquidacion_mergeada->getId();

        $em->clear();
        $this->get('session')->remove('liquidacion');

        /* @var $liquidacion_persistida Liquidacion */
        $liquidacion_persistida = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find($idLiquidacion);

        // Comienzo la transaccion
        $emContable->getConnection()->beginTransaction();

        try {
            // Se genera el renglón de declaracion jurada
            // Creo el Renglon de DDJJ asociado
            $renglonDDJJSicore = $this->crearRenglonDeclaracionJurada($liquidacion_persistida, ConstanteTipoImpuesto::Ganancias, $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getGananciaByLiquidacion($liquidacion_persistida->getId()));
            $liquidacion_persistida->setIdRenglonDeclaracionJuradaSicore($renglonDDJJSicore->getId());
            $renglonDDJJSicoss = $this->crearRenglonDeclaracionJurada($liquidacion_persistida, ConstanteTipoImpuesto::SICOSS, $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getSicossByLiquidacion($liquidacion_persistida->getId()));
            $liquidacion_persistida->setIdRenglonDeclaracionJuradaSicoss($renglonDDJJSicoss->getId());

            // Se genera el renglón de retencion
            $this->crearRenglonesRetencion($liquidacion_persistida);

            // Se generan los asientos de devengamiento de sueldos
            $total_asiento = 0;
			$numerosAsientos = $ids = array();
            $mensajeErrorAsientos = $this->get('adif.asiento_service')->generarAsientoSueldos($liquidacion_persistida, $this->getUser(), $total_asiento, $numerosAsientos, $ids);
            $mensajeErrorAsientoDevengado = $this->get('adif.contabilidad_presupuestaria_service')->crearDevengadoSueldosFromLiquidacion($liquidacion_persistida);
			$mensajeErrorAsientoEjecutado = $this->get('adif.contabilidad_presupuestaria_service')->crearEjecutadoSueldosFromLiquidacion($liquidacion_persistida);
			
			// Si los asientos fallaron
            if ($mensajeErrorAsientos != '') {
                $this->container->get('request')->getSession()->getFlashBag()
                        ->add('error', $mensajeErrorAsientos);
            }
			
            // Si el asiento presupuestario devengado fallo
            if ($mensajeErrorAsientoDevengado != '') {
                $this->container->get('request')->getSession()->getFlashBag()
                        ->add('error', $mensajeErrorAsientoDevengado);
            }
            
			// Si el asiento presupuestario ejecutado fallo
            if ($mensajeErrorAsientoEjecutado != '') {
                $this->container->get('request')->getSession()->getFlashBag()
                        ->add('error', $mensajeErrorAsientoEjecutado);
            }

            if ($mensajeErrorAsientos == '' && $mensajeErrorAsientoDevengado == '' && $mensajeErrorAsientoEjecutado == '') {

                $emContable->flush();
                $emContable->getConnection()->commit();

                $em->flush();
				
				$this->container->get('request')->getSession()->getFlashBag()
                        ->add('success', 'Se ha creado con exito los asientos contables y los devengados y ejecutados presupuestarios.');
				
				$dataArray = [
					'data-id-asiento-sueldo' => implode(',', $ids)
				];
				
				$mensajeFlash = $this->get('adif.asiento_service')
                                ->showMensajeFlashColeccionAsientosContables($numerosAsientos, $dataArray, false);
				
            }
			
        } catch (\Exception $e) {

            $emContable->getConnection()->rollback();
            $emContable->close();

            throw $e;
        }

        $em->clear();
        $emContable->clear();

        return $this->redirect($this->generateUrl('liquidaciones'));
    }

    /**
     * Tabla para Liquidacion.
     *
     * @Route("/liquidacion_table", name="liquidacion_table")
     * @Method("POST|GET")
     */
    public function showLiquidacionTableAction(Request $request) {
        return $this->render(
                        'ADIFRecursosHumanosBundle:Liquidacion:liquidacion_table.html.twig', array(
                    'data' => $this->get('session')->get('liquidacion_table_data')
                        )
        );
    }

    /**
     * Tabla para las contribuciones Liquidacion.
     *
     * @Route("/liquidacion_contribuciones_table", name="liquidacion_contribuciones_table")
     * @Method("POST|GET")
     */
    public function showLiquidacionContribucionesTableAction(Request $request) {
        return $this->render(
                        'ADIFRecursosHumanosBundle:Liquidacion:liquidacion_contribuciones_table.html.twig', array(
                    'data' => $this->get('session')->get('liquidacion_contribuciones_table_data')
                        )
        );
    }

    /**
     * Deletes a Liquidacion entity.
     *
     * @Route("/borrar/{id}", name="liquidaciones_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_RRHH_ALTA_LIQUIDACIONES')")
     */
    public function deleteAction($id) {
        
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/liquidar/", name="liquidaciones_liquidar")
     * @Method("GET|POST")
     * @Security("has_role('ROLE_RRHH_ALTA_LIQUIDACIONES')")
     */
    public function liquidarAction(Request $request) {
        $liquidacionHelper = new LiquidacionHelper($this);
        $aplica_ganancias = $request->request->get('aplica_ganancias') == 'true';
        try {
            if ($request->request->get('id_tipo_liquidacion') == TipoLiquidacion::__ADICIONAL) {
                $resultLiquidacion = $liquidacionHelper->liquidar(
                        $this->container, $request->request->get('ids'), TipoLiquidacion::__ADICIONAL, $request->request->get('fecha'), $request->request->get('ids_conceptos_adicionales'), $aplica_ganancias);
            } else {
                $resultLiquidacion = $liquidacionHelper->liquidar(
                        $this->container, $request->request->get('ids'), $request->request->get('id_tipo_liquidacion'), $request->request->get('fecha'), null, $aplica_ganancias
                );
            }
        } catch (\Exception $e) {
            $resultLiquidacion = array(
                'result' => LiquidacionHelper::__TIPO_RESULT_ERROR,
                'msg' => $e->getMessage()
            );
        }

        return new JsonResponse($resultLiquidacion);
    }

    /**
     * Genera el archivo de exportacion 931
     *
     * @Route("/liquidacion/exportar931/{id}", name="liquidaciones_exportar_931")
     * @Method("GET")
     * @Security("has_role('ROLE_RRHH_EXPORTAR_F931')")
     */
    public function exportar931Action($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Liquidacion.');
        }

        $filename = 'exp931_v37_' . $entity->getNumero() . '.txt';
        $path = $this->get('kernel')->getRootDir() . '/../web/uploads/formularios_931/' . $filename;

        $f = fopen($path, "w");

        foreach ($entity->getLiquidacionEmpleados() as $liquidacionEmpleado) {
            /* @var $liquidacionEmpleado LiquidacionEmpleado */
            /* @var $empleado Empleado */
            /* @var $persona Persona */

            $empleado = $liquidacionEmpleado->getEmpleado();
            $persona = $empleado->getPersona();
			$liquidacion = $liquidacionEmpleado->getLiquidacion();

            $char_pad_string = ' ';
            $char_pad_int = '0';
            $char_pad_importe = ' ';

            $type_pad_string = STR_PAD_RIGHT;
            $type_pad_int = STR_PAD_LEFT;
            $type_pad_importe = STR_PAD_LEFT;

            /*
              $remunerativo_s_t = $liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2();
              $remunerativo_c_t = $liquidacionEmpleado->getMontoRemunerativoConTope();
              $no_remunerativo = $liquidacionEmpleado->getNoRemunerativo();
             */

            $remunerativo_s_t = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getRemunerativoSinTopeByEmpleado($liquidacionEmpleado->getEmpleado()->getId(), $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades());
            $remunerativo_c_t = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getRemunerativoConTopeByEmpleado($liquidacionEmpleado->getEmpleado()->getId(), $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades());
            $no_remunerativo = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getNoRemunerativoByEmpleado($liquidacionEmpleado->getEmpleado()->getId(), $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades());
			$remuneracion9ART = 0;
			
            $campos = $this->cargarCampos931($liquidacionEmpleado);

            $fechaInicio = new DateTime(date('Y-m-01', strtotime($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Y-m-d'))));
            $fechaFin = new DateTime(date('Y-m-t', strtotime($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Y-m-d'))));
            $licencias = $empleado->getLicenciasFechas($fechaInicio, $fechaFin);

            /* 1 */ $cadena = str_replace('-', '', $persona->getCuil());
            /* 2 */ $cadena .= $this->mb_str_pad(substr(AdifApi:: stringCleaner($persona->getApellido()) . ' ' . AdifApi:: stringCleaner($persona->getNombre()), 0, 30), 30, $char_pad_string, $type_pad_string);
            /* 3 */ $cadena .= $empleado->tieneConyuge();
            /* 4 */ $cadena .= str_pad(AdifApi:: stringCleaner($empleado->getCantidadHijos()), 2, $char_pad_int, $type_pad_int); //Cantidad de hijos

            /* Licencias */ $licencia = $this->cargarLicencias($licencias, $fechaInicio, $fechaFin, $char_pad_int, $type_pad_int);

            /* 5 */ $cadena .= str_pad($this->getCodigoSituacion($licencia), 2, $char_pad_int, $type_pad_int);  //Código de situación
            /* 6 */ $cadena .= str_pad($empleado->getCondicion()->getCodigo(), 2, $char_pad_int, $type_pad_int);  //Código de condición
            /* 7 */ $cadena .= str_pad('49', 3, $char_pad_int, $type_pad_int);  //Código de actividad
            /* 8 */ $cadena .= str_pad('1', 2, $char_pad_int, $type_pad_int);  //Código de zona
            /* 9 */ $cadena .= str_pad('00,00', 5, $char_pad_importe, $type_pad_importe);  //Porcentaje de aporte adicional SS
            /* 10 */ $cadena .= str_pad($empleado->getTipoContratacionActual()->getTipoContrato()->getCodigo(), 3, $char_pad_int, $type_pad_int);  //Código de modalidad de contratación
            /* 11 */ $cadena .= str_pad($empleado->getObraSocialActual()->getObraSocial()->getCodigo(), 6, $char_pad_int, $type_pad_int);  //Código de obra social
            /* 12 */ $cadena .= str_pad('0', 2, $char_pad_int, $type_pad_int);  //Cantidad de adherentes
            /* 13 */ $cadena .= str_pad(number_format($remunerativo_s_t + $no_remunerativo, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración total
            /* 14 */ $cadena .= str_pad(number_format($remunerativo_c_t, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 1
            /* 15 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Asignaciones familiares pagadas
            /* 16 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Importe aporte voluntario
            /* 17 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Importe adicional OS
            /* 18 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Importe excedentes aportes SS
            /* 19 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Importe excedentes aportes OS
            /* 20 */ $cadena .= $this->mb_str_pad(AdifApi:: stringCleaner($persona->getDomicilio()->getLocalidad()->getProvincia()) . ' ' . AdifApi:: stringCleaner($persona->getDomicilio()->getLocalidad()), 50, $char_pad_string, $type_pad_string);
            /* 21 */ $cadena .= str_pad(number_format($remunerativo_s_t, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 2
            /* 22 */ $cadena .= str_pad(number_format($remunerativo_s_t, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 3
            /* 23 */ $cadena .= str_pad(number_format($remunerativo_c_t, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 4
            /* 24 */ $cadena .= str_pad('0', 2, $char_pad_int, $type_pad_int);  //Código de siniestrado
            /* 25 */ $cadena .= '0';  //Marca de corresponde reducción
            /* 26 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Capital de recomposición LRT
            /* 27 */ $cadena .= str_pad('1', 1, $char_pad_int, $type_pad_int);  //Tipo de empresa
            /* 28 */ $cadena .= str_pad($campos[28], 9, $char_pad_importe, $type_pad_importe);  //Aporte adicional de obra social
            /* 29 */ $cadena .= str_pad('1', 1, $char_pad_int, $type_pad_int);  //Régimen            
            /* 30 */ $cadena .= $licencia;

            /* 36 */ $cadena .= str_pad(number_format($liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2(), 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Sueldo + adicionales
            /* 37 */ $cadena .= str_pad($campos[37], 12, $char_pad_importe, $type_pad_importe);  //SAC
            /* 38 */ $cadena .= str_pad('0,00', 12, $char_pad_importe, $type_pad_importe);  //Horas extras
            /* 39 */ $cadena .= str_pad('0,00', 12, $char_pad_importe, $type_pad_importe);  //Zona desfavorable
            /* 40 */ $cadena .= str_pad('0,00', 12, $char_pad_importe, $type_pad_importe);  //Vacaciones
            /* 41 */ $cadena .= str_pad($empleado->getDiasTrabajados($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()) - $empleado->getDiasLicencia($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades(), $licencia), 9, $char_pad_int, $type_pad_int);  //Días trabajados
            /* 42 */ $cadena .= str_pad(number_format($remunerativo_c_t, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 5
            /* 43 */ $cadena .= $empleado->getIdSubcategoria()->getCategoria()->getConvenio()->getId() == Convenio::__FUERA_DE_CONVENIO ? 0 : 1; //Marca convencionado
            /* 44 */ $cadena .= str_pad(number_format(0, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 6
            /* 45 */ $cadena .= '0';  //Tipo operación
            /* 46 */ $cadena .= str_pad('0,00', 12, $char_pad_importe, $type_pad_importe);  //Adicionales
            /* 47 */ $cadena .= str_pad('0,00', 12, $char_pad_importe, $type_pad_importe);  //Premios
            /* 48 */ $cadena .= str_pad(number_format($remunerativo_s_t, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Sueldo Dto 788 05 Remuneración 8
            /* 49 */ $cadena .= str_pad(number_format(0, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 7
            /* 50 */ $cadena .= str_pad('0', 3, $char_pad_int, $type_pad_int);  //Cantidad horas extra
            /* 51 */ $cadena .= str_pad(number_format($no_remunerativo, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Conceptos no remunerativos
            /* 52 */ $cadena .= str_pad('0,00', 12, $char_pad_importe, $type_pad_importe);  //Maternidad
            /* 53 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Rectificación de remuneración
			
			$montoIndeminatorios = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
				->getNoRemunerativoIndemnizatorioByLiquidacionYEmpleado($liquidacion, $empleado);
			
			$remuneracion9ART = $remunerativo_s_t + $no_remunerativo - $montoIndeminatorios;
            /* 54 */ $cadena .= str_pad(number_format($remuneracion9ART, 2, ',', ''), 12, $char_pad_importe, $type_pad_importe);  //Remuneración imponible 9
			
            /* 55 */ $cadena .= str_pad('0,00', 9, $char_pad_importe, $type_pad_importe);  //Contribución tarea dif
            /* 56 */ $cadena .= str_pad('0', 3, $char_pad_int, $type_pad_int);  //Horas trabajadas
            /* 57 */ $cadena .= '1';  //Seguro colectivo de vida obligatorio

            fwrite($f, $cadena);
            fwrite($f, chr(13) . chr(10));
        }

        fclose($f);

        $response = new BinaryFileResponse($path);
        $d = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename
        );
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', $d);

        return $response;
    }

    /**
     * Genera el archivo de exportacion de retenciones
     *
     * @Route("/liquidacion/exportarRetenciones/{id}", name="liquidaciones_exportar_retenciones")
     * @Method("GET")
     * @Security("has_role('ROLE_RRHH_EXPORTAR_F931')")
     */
    public function exportarRetencionesAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Liquidacion.');
        }

        $filename = 'reten_v8_' . $entity->getNumero() . '.txt';
        $path = $this->get('kernel')->getRootDir() . '/../web/uploads/retenciones/' . $filename;

        $char_pad_string = ' ';
        $char_pad_int = '0';
        $char_pad_importe = ' ';

        $type_pad_string = STR_PAD_RIGHT;
        $type_pad_int = STR_PAD_LEFT;
        $type_pad_importe = STR_PAD_LEFT;

        $f = fopen($path, "w");

        $fecha_recibo = $entity->getFechaPago()->format('d/m/Y');

        foreach ($entity->getLiquidacionEmpleados() as $liquidacionEmpleado) {

            /* @var $liquidacionEmpleado LiquidacionEmpleado */
            /* @var $empleado Empleado */
            /* @var $persona Persona */

            $empleado = $liquidacionEmpleado->getEmpleado();
            $persona = $empleado->getPersona();

            //Recibo
            $nro_recibo = $liquidacionEmpleado->getId();

            $imp_retencion = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getGananciaByEmpleado($liquidacionEmpleado->getEmpleado()->getId(), $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades());

            if ($imp_retencion != 0) {
                $imp_retencion = str_replace('.', ',', $imp_retencion);

                /* 1 */ $cadena = str_pad('07', 2, $char_pad_int, $type_pad_int); //Código de comprobante
                /* 2 */ $cadena.= str_pad($fecha_recibo, 10, $char_pad_string, $type_pad_string); //Fecha emision comprobante
                /* 3 */ $cadena.= str_pad($nro_recibo, 16, $char_pad_int, $type_pad_int); //Número de comprobante    
                /* 4 */ $cadena.= str_pad('0,00', 16, $char_pad_int, $type_pad_int); //Importe del comprobante
                /* 5 */ $cadena.= str_pad('217', 3, $char_pad_int, $type_pad_int); //Código de Impuesto
                /* 6 */ $cadena.= str_pad('160', 3, $char_pad_int, $type_pad_int); //Código de regimen
                /* 7 */ $cadena.= str_pad('1', 1, $char_pad_int, $type_pad_int); //Código de regimen
                /* 8 */ $cadena.= str_pad('0,00', 14, $char_pad_int, $type_pad_int); //Base de cálculo
                /* 9 */ $cadena.= str_pad($fecha_recibo, 10, $char_pad_string, $type_pad_string); //Fecha emision retención
                /* 10 */ $cadena.= str_pad('01', 2, $char_pad_int, $type_pad_int); //Código de condición
                /* 11 */ $cadena.= str_pad('0', 1, $char_pad_int, $type_pad_int); //Retención practicada a sujetos suspendidos
                /* 12 */ $cadena.= str_pad($imp_retencion, 14, $char_pad_int, $type_pad_int); //Importe de la retención
                /* 13 */ $cadena.= str_pad('0,00', 6, $char_pad_int, $char_pad_int); //Porcentaje de exclusion
                /* 14 */ $cadena.= str_pad('', 10, $char_pad_string, $type_pad_string); //Fecha emision boletín
                /* 15 */ $cadena.= str_pad('86', 2, $char_pad_int, $type_pad_int); //Tipo de documento retenido
                /* 16 */ $cadena.= str_pad(strval(str_replace('-', '', $persona->getCuil())), 20, $char_pad_string, $type_pad_string); //Número de documento de retenido                                    
                /* 17 */ $cadena.= str_pad('0', 14, $char_pad_int, $type_pad_int); //Número certificado original
                /* 18 */ $cadena.= str_pad('', 30, $char_pad_string, $type_pad_string); //Denominación del ordenante
                /* 19 */ $cadena.= str_pad('0', 1, $char_pad_int, $type_pad_int); //Acrecentamiento
                /* 20 */ $cadena.= str_pad('0', 11, $char_pad_int, $type_pad_int); //Cuit del pais del retenido
                /* 21 */ $cadena.= str_pad('0', 11, $char_pad_int, $type_pad_int); //Cuit del ordenante
                fwrite($f, $cadena);
                fwrite($f, chr(13) . chr(10));
            }
        }

        fclose($f);

        $response = new BinaryFileResponse($path);
        $d = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', $d);

        return $response;
    }

    /**
     * Genera el archivo de exportacion de sirhu
     *
     * @Route("/liquidacion/exportarSirhu/{id}", name="liquidaciones_exportar_sirhu")
     * @Method("GET")
     * @Security("has_role('ROLE_RRHH_EXPORTAR_NETCASH')")
     */
    public function exportarSirhuAction($id) {

        function generarArchivo($str, $name, $path) {
            $f = fopen($path . $name, "w");
            fwrite($f, $str);
            fclose($f);

            return $path . $name;
        }

        function generarLote($nroliq, $path) {

            $file = fopen($path . 'lote.nro', "a+");
            $vec = array();
            $sig = null;
            while (($datos = fgetcsv($file, 1000, ",")) !== FALSE) {
                $vec = $datos;
            }

            if (isset($vec[0])) {
                if ($vec[0] < $nroliq) {
                    $sig = str_pad($vec[1] + 1, 3, 0, STR_PAD_LEFT);

                    fwrite($file, "$nroliq,$sig" . chr(10));
                } else {
                    if (is_numeric($vec[0])) {
                        $sig = str_pad($vec[0], 3, 0, STR_PAD_LEFT);
                    } else {
                        fwrite($file, "$nroliq,1" . chr(10));
                    }
                }
            }

            fclose($file);

            return $sig;
        }

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Liquidacion.');
        }

        $char_pad_string = ' ';
        $char_pad_int = '0';
        $char_pad_lstring = ' ';

        $type_pad_string = STR_PAD_RIGHT;
        $type_pad_int = STR_PAD_LEFT;
        $type_pad_lstring = STR_PAD_LEFT;

        $str_qidp1 = '';
        $str_qidp3 = '';
        $str_qilh1 = '';
        $str_qilh2 = '';
        $str_qich1 = '';
        $str_qior1 = '';

        foreach ($entity->getLiquidacionEmpleados() as $liquidacionEmpleado) {

            $empleado = $liquidacionEmpleado->getEmpleado();
            $persona = $empleado->getPersona();
            $persona_id_documento = $persona->getIdTipoDocumento()->getId();
            $persona_nro_documento = $persona->getNroDocumento();

            //Estado del empleado
            $fechaInicio = new DateTime(date('Y-m-01', strtotime($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Y-m-d'))));
            $fechaFin = new DateTime(date('Y-m-t', strtotime($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Y-m-d'))));
            $licencias = $empleado->getLicenciasFechas($fechaInicio, $fechaFin);

            if ($licencias->isEmpty()) {
                $empleado_licencia = 1; //Activo
            } else {
                $empleado_licencia = $licencias->last()->getTipoLicencia()->getId();
            }

            if ($empleado->getEstudios()->isEmpty()) {
                $empleado_titulo = 1;
                $empleado_nivelestudio = 4;
            } else {
                $empleado_titulo = $empleado->getEstudios()->last()->getTitulo();
                $empleado_nivelestudio = $empleado->getEstudios()->last()->getIdNivelEstudio()->getId();
            }

            //Si el escalafón es 397 pero son contratados se pone nuevo escalafon (Plazo Fijos con convenio y escalafon 397)
            $cod_escalafon = ConstanteSirhu::getCodigoSirhu($empleado->getIdSubcategoria()->getId(), 15);
            if ($cod_escalafon == '397' && $empleado->getTipoContratacionActual()->getTipoContrato()->getId() == 1) {
                $cod_escalafon = '1397';
            }


            /* 1 */ $str_qidp1.= str_pad(ConstanteSirhu::getCodigoSirhu($persona_id_documento, 3), 4, $char_pad_string, $type_pad_string); //Tipo de Documento
            /* 2 */ $str_qidp1.= str_pad($persona_nro_documento, 16, $char_pad_int, $type_pad_int); //Número de documento
            /* 3 */ $str_qidp1.= $this->mb_str_pad(str_replace('-', ' ', substr(AdifApi:: stringCleaner($persona->getApellido()) . ' ' . AdifApi:: stringCleaner($persona->getNombre()), 0, 30)), 40, $char_pad_string, $type_pad_string); //Apellido y Nombre de la Persona
            /* 4 */ $str_qidp1.= str_pad($persona->getFechaNacimiento()->format('Ymd'), 8, $char_pad_string, $type_pad_string); //Fecha de Nacimiento
            /* 5 */ $str_qidp1.= str_pad(ConstanteSirhu::getCodigoSirhu($persona->getSexo(), 0), 4, $char_pad_string, $type_pad_string); //Codigo de Sexo
            /* 6 */ $str_qidp1.= str_pad(ConstanteSirhu::getCodigoSirhu($persona->getIdEstadoCivil()->getId(), 10), 4, $char_pad_string, $type_pad_string); //Codigo de Sexo
            /* 7 */ $str_qidp1.= str_pad('', 1, $char_pad_string, $type_pad_string); //Blancos
            /* 8 */ $str_qidp1.= str_pad('QI', 2, $char_pad_string, $type_pad_string); //Código de institución
            /* 7 */ $str_qidp1.= str_pad('', 2, $char_pad_string, $type_pad_string); //Blancos
            /* 8 */ $str_qidp1.= str_pad($empleado->getFechaInicioAntiguedad()->format('Ym'), 6, $char_pad_string, $type_pad_string); //Fecha de Ingreso a la APN
            /* 9 */ $str_qidp1.= str_pad(ConstanteSirhu::getCodigoSirhu($persona->getIdNacionalidad()->getId(), 1), 2, $char_pad_string, $type_pad_string); //Codigo de nacionalidad
            /* 10 */$str_qidp1.= str_pad(ConstanteSirhu::getCodigoSirhu($empleado_nivelestudio, 6), 2, $char_pad_string, $type_pad_string); //Codigo nivel de educacion
            /* 11 */$str_qidp1.= str_pad(substr($empleado_titulo, 0, 30), 30, $char_pad_string, $type_pad_string); //Descripcion título obtenido
            /* 12 */$str_qidp1.= str_pad(str_replace('-', '', $persona->getCuil()), 11, $char_pad_string, $type_pad_string); //CUIT
            /* 13 */$str_qidp1.= str_pad('R', 1, $char_pad_string, $type_pad_string); //Sistema previsional
            /* 14 */$str_qidp1.= str_pad('4', 4, $char_pad_int, $type_pad_int); //Codigo Sistema previsional
            /* 15 */$str_qidp1.= str_pad($empleado->getObraSocialActual()->getObraSocial()->getCodigo(), 10, $char_pad_int, $type_pad_int); //Código Obra Social
            /* 16 */$str_qidp1.= str_pad('0', 15, $char_pad_int, $type_pad_int); //Número de afiliacion
            /* 17 */$str_qidp1.= str_pad('8', 1, $char_pad_int, $type_pad_int); //Tipo de Horario

            $str_qidp1.= chr(13) . chr(10);

            /* 1 */ $str_qidp3.= str_pad('QI', 4, $char_pad_string, $type_pad_string); //Código de Institución
            /* 2 */ $str_qidp3.= str_pad(ConstanteSirhu::getCodigoSirhu($persona_id_documento, 3), 4, $char_pad_string, $type_pad_string); //Tipo de Documento
            /* 3 */ $str_qidp3.= str_pad($persona_nro_documento, 16, $char_pad_int, $type_pad_int); //Número de documento
            /* 4 */ $str_qidp3.= str_pad('', 5, $char_pad_string, $type_pad_string); //Blancos
            /* 5 */ $str_qidp3.= str_pad(substr($empleado_titulo, 0, 47), 47, $char_pad_string, $type_pad_string); //Descripcion título obtenido
            /* 6 */ $str_qidp3.= str_pad(ConstanteSirhu::getCodigoSirhu($empleado_nivelestudio, 11), 1, $char_pad_string, $type_pad_string); //Codigo nivel de educacion
            /* 7 */ $str_qidp3.= str_pad('', 8, $char_pad_string, $type_pad_string); //Fecha del título
            /* 8 */ $str_qidp3.= str_pad('', 8, $char_pad_string, $type_pad_string); //Blancos
            /* 9 */ $str_qidp3.= str_pad('', 60, $char_pad_string, $type_pad_string); //Descripcion de Entidad Educativa
            /* 10 */$str_qidp3.= str_pad('', 3, $char_pad_string, $type_pad_string); //Duracion en meses
            /* 11 */$str_qidp3.= str_pad('', 5, $char_pad_string, $type_pad_string); //Blancos

            $str_qidp3.= chr(13) . chr(10);




            /* 1 */ $str_qilh1.= str_pad('QI', 2, $char_pad_string, $type_pad_string); //Código de Institución
            /* 2 */ $str_qilh1.= str_pad('', 2, $char_pad_string, $type_pad_string); //Blancos
            /* 3 */ $str_qilh1.= str_pad(ConstanteSirhu::getCodigoSirhu($persona_id_documento, 3), 4, $char_pad_string, $type_pad_string); //Tipo de Documento
            /* 4 */ $str_qilh1.= str_pad($persona_nro_documento, 16, $char_pad_int, $type_pad_int); //Número de documento
            /* 5 */ $str_qilh1.= str_pad($cod_escalafon, 4, $char_pad_int, $type_pad_int); //Código de Escalafon
            /* 6 */ $str_qilh1.= str_pad('G', 4, $char_pad_string, $type_pad_string); //Código de Agrupamiento
            /* 7 */ $str_qilh1.= str_pad('O', 4, $char_pad_string, $type_pad_string); //Código de Nivel
            /* 8 */ $str_qilh1.= str_pad(ConstanteSirhu::getCodigoSirhu($empleado->getIdSubcategoria()->getId(), 14), 4, $char_pad_string, $type_pad_string); //Código de Grado o Categoría
            /* 9 */ $str_qilh1.= str_pad('0', 14, $char_pad_int, $type_pad_int); //Código Unidad/Nudo
            /* 10 */ $str_qilh1.= str_pad('30', 2, $char_pad_string, $type_pad_string); //Código Jurisdiccion
            /* 11 */ $str_qilh1.= str_pad('0', 2, $char_pad_int, $type_pad_int); //Código Sub-Jurisdiccion
            /* 12 */ $str_qilh1.= str_pad('0', 3, $char_pad_int, $type_pad_int); //Código Entidad
            /* 13 */ $str_qilh1.= str_pad('0', 2, $char_pad_int, $type_pad_int); //Código de Programa
            /* 14 */ $str_qilh1.= str_pad('0', 2, $char_pad_int, $type_pad_int); //Código de SubPrograma
            /* 15 */ $str_qilh1.= str_pad('0', 2, $char_pad_int, $type_pad_int); //Código de Proyecto
            /* 16 */ $str_qilh1.= str_pad('0', 2, $char_pad_int, $type_pad_int); //Código de Actividad
            /* 17 */ $str_qilh1.= str_pad('02', 2, $char_pad_int, $type_pad_int); //Código de Ubicación Geografica
            /* 18 */ $str_qilh1.= str_pad('', 6, $char_pad_string, $type_pad_string); //Blancos
            /* 19 */ $str_qilh1.= str_pad($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Ym'), 6, $char_pad_string, $type_pad_string); //Período correspondiente a la informacion
            /* 20 */ $str_qilh1.= str_pad(ConstanteSirhu::getCodigoSirhu($empleado->getTipoContratacionActual()->getTipoContrato()->getId(), 2), 1, $char_pad_string, $type_pad_string); //Tipo Planta
            /* 21 */ $str_qilh1.= str_pad($empleado->getFechaIngreso()->format('Ymd'), 8, $char_pad_string, $type_pad_string); //Fecha de ultimo ingreso
            /* 22 */ $str_qilh1.= str_pad('12', 4, $char_pad_int, $type_pad_int); //Código fuente de financiamiento
            /* 23 */ $str_qilh1.= str_pad(ConstanteSirhu::getCodigoSirhu($empleado_licencia, 12), 1, $char_pad_string, $type_pad_string); //Marca de Estado

            $str_qilh1.= chr(13) . chr(10);


            /*
             * Se pidio que se agregue para todos los empleados en el archivo "qilh2", los basicos de todos los empleados
             * con el codigo "000001", pero el mismo no esta en la tabla de "concepto" ni "concepto_version", asi que
             * lo que hice, es ponerlo en el primer renglon para cada empleado, osea el codigo + el monto basico.
             * @author gluis - 18/03/2015
             */
            $basico_empleado = $liquidacionEmpleado->getBasico();
            $basico_empleado = number_format($basico_empleado, 2, '.', '');
            $basico_empleado = str_pad($basico_empleado, 15, $char_pad_int, $type_pad_int);
            $basico_codigo_concepto = '000001';
            $basico_descripcion = 'Salario Basico';

            /* 1 */ $str_qilh2.= str_pad('QI', 2, $char_pad_string, $type_pad_string); //Código de Institución
            /* 2 */ $str_qilh2.= str_pad('', 2, $char_pad_string, $type_pad_string); //Blancos
            /* 3 */ $str_qilh2.= str_pad(ConstanteSirhu::getCodigoSirhu($persona_id_documento, 3), 4, $char_pad_string, $type_pad_string); //Tipo de Documento
            /* 4 */ $str_qilh2.= str_pad($persona_nro_documento, 16, $char_pad_int, $type_pad_int); //Número de documento
            /* 5 */ $str_qilh2.= str_pad($cod_escalafon, 4, $char_pad_int, $type_pad_int); //Código de Escalafon
            /* 6 */ $str_qilh2.= $basico_codigo_concepto; //Código de Concepto
            /* 7 */ $str_qilh2.= $basico_empleado; //Monto  Concepto
            /* 8 */ $str_qilh2.= str_pad('99', 2, $char_pad_int, $type_pad_int); //Tipo unidad fisica
            /* 9 */ $str_qilh2.= str_pad('1', 6, $char_pad_int, $type_pad_int); //Unidades Fisicas
            /* 10 */ $str_qilh2.= str_pad($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Ym'), 6, $char_pad_string, $type_pad_string); //Período de la info
            /*
              echo "codigo institucion: " . str_pad('QI', 2, $char_pad_string, $type_pad_string) . '<br/>';
              echo "blancos: " . str_pad('', 2, $char_pad_string, $type_pad_string) . '<br/>';
              echo "tipo doc: " . str_pad(ConstanteSirhu::getCodigoSirhu($persona_id_documento, 3), 4, $char_pad_string, $type_pad_string) . '<br/>';
              echo "documento: " . str_pad($persona_nro_documento, 16, $char_pad_int, $type_pad_int) . '<br/>';
              echo "cod escalafon: " . str_pad($cod_escalafon, 4, $char_pad_int, $type_pad_int) . '<br>';
              echo "cod concepto: " . $concepto_basico . "<br>";
              echo "monto concepto: " . $basico_empleado . "<br>";
              echo "unidad fisica: " . str_pad('99', 2, $char_pad_int, $type_pad_int) . '<br/>';
              echo "unidades fisicas: " . str_pad('1', 6, $char_pad_int, $type_pad_int) . "<br/>";
              echo "periodo info: " . str_pad($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Ym'), 6, $char_pad_string, $type_pad_string) . "<br/>";

              echo ("resultante LH2: $str_qilh2");
             */
            $str_qilh2.= chr(13) . chr(10);

            /* 1 */ $str_qich1 = str_pad('QI', 2, $char_pad_string, $type_pad_string); //Código de Institución
            /* 2 */ $str_qich1.= str_pad($cod_escalafon, 4, $char_pad_int, $type_pad_int); //Código de Escalafon
            /* 3 */ $str_qich1.= $basico_codigo_concepto; //Código de Concepto
            /* 4 */ $str_qich1.= str_pad(substr($basico_descripcion, 0, 40), 40, $char_pad_string, $type_pad_string);
            ; //Descripción Concepto
            /* 5 */ $str_qich1.= 1; //Caracter remunerativo/bonificable
            /* 6 */ $str_qich1.= 1; //Tipo Concepto
            $str_qich1.= chr(13) . chr(10);

            $vec_q1ch1[] = $str_qich1;
            //echo "Qich1: $str_qich1"; exit;
            //Por cada concepto...
            foreach ($liquidacionEmpleado->getLiquidacionEmpleadoConceptos() as $concepto) {

                $cod_concepto = explode('.', $concepto->getConceptoVersion()->getCodigo());
                $mon_concepto = number_format($concepto->getMonto(), 2, '.', '');
                $des_concepto = str_replace('-', ' ', AdifApi::stringCleaner($concepto->getConceptoVersion()->getDescripcion()));

                $tipo_concepto = $concepto->getConceptoVersion()->getIdTipoConcepto();

                //Agrego signo al monto de concepto dependiendoo si suma o resta
                if (ConstanteSirhu::getCodigoSirhu($tipo_concepto, 16) == '-') {
                    $mon_concepto = '-' . str_pad($mon_concepto, 14, $char_pad_int, $type_pad_int); //quito un lugar para el signo
                } else {
                    $mon_concepto = str_pad($mon_concepto, 15, $char_pad_int, $type_pad_int);
                }
                //Si el escalafón es 397 pero son contratados se pone nuevo escalafon (Plazo Fijos con convenio y escalafon 397)
                $cod_escalafon = ConstanteSirhu::getCodigoSirhu($empleado->getIdSubcategoria()->getId(), 15);
                if ($cod_escalafon == '397' && $empleado->getTipoContratacionActual()->getTipoContrato()->getId() == 1) {
                    $cod_escalafon = '1397';
                }

                /* 1 */ $str_qilh2.= str_pad('QI', 2, $char_pad_string, $type_pad_string); //Código de Institución
                /* 2 */ $str_qilh2.= str_pad('', 2, $char_pad_string, $type_pad_string); //Blancos
                /* 3 */ $str_qilh2.= str_pad(ConstanteSirhu::getCodigoSirhu($persona_id_documento, 3), 4, $char_pad_string, $type_pad_string); //Tipo de Documento
                /* 4 */ $str_qilh2.= str_pad($persona_nro_documento, 16, $char_pad_int, $type_pad_int); //Número de documento
                /* 5 */ $str_qilh2.= str_pad($cod_escalafon, 4, $char_pad_int, $type_pad_int); //Código de Escalafon
                /* 6 */ $str_qilh2.= str_pad($cod_concepto[0], 6, $char_pad_int, $type_pad_int); //Código de Concepto 
                /* 7 */ $str_qilh2.= $mon_concepto; //Monto  Concepto    
                /* 8 */ $str_qilh2.= str_pad('99', 2, $char_pad_int, $type_pad_int); //Tipo unidad fisica   
                /* 9 */ $str_qilh2.= str_pad('1', 6, $char_pad_int, $type_pad_int); //Unidades Fisicas
                /* 10 */ $str_qilh2.= str_pad($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Ym'), 6, $char_pad_string, $type_pad_string); //Período de la info

                $str_qilh2.= chr(13) . chr(10);

                /* 1 */ $str_qich1 = str_pad('QI', 2, $char_pad_string, $type_pad_string); //Código de Institución
                /* 2 */ $str_qich1.= str_pad($cod_escalafon, 4, $char_pad_int, $type_pad_int); //Código de Escalafon
                /* 3 */ $str_qich1.= str_pad($cod_concepto[0], 6, $char_pad_int, $type_pad_int); //Código de Concepto 
                /* 4 */ $str_qich1.= str_pad(substr($des_concepto, 0, 40), 40, $char_pad_string, $type_pad_string); //Descripción Concepto
                /* 5 */ $str_qich1.= str_pad(($tipo_concepto == 1 || $tipo_concepto == 2 ? ConstanteSirhu::getCodigoSirhu($tipo_concepto, 13) : 1), 1, $char_pad_int, $type_pad_int); //Caracter remunerativo/bonificable
                /* 6 */ $str_qich1.= str_pad(ConstanteSirhu::getCodigoSirhu($tipo_concepto, 5), 1, $char_pad_string, $type_pad_string); //Tipo Concepto
                $str_qich1.= chr(13) . chr(10);

                $vec_q1ch1[] = $str_qich1;
            }
            //Elimino conceptos repetidos
            $vec_q1ch1 = array_unique($vec_q1ch1);
            $str_qich1 = implode($vec_q1ch1);

            //Liquidación de plazos fijos, se usará mas adelante
            //   /* 1 */  $str_qior1.=  str_pad('QI', 2, $char_pad_string, $type_pad_string); //Código de Institución
            //   /* 2 */  $str_qior1.=  str_pad('', 1, $char_pad_string, $type_pad_string); //Número de Inciso
            //   /* 3 */  $str_qior1.=  str_pad('', 1, $char_pad_int, $type_pad_int); //Número de Orden
            //   /* 4 */  $str_qior1.=  str_pad('', 5, $char_pad_int, $type_pad_int); //Número de Orden
            //   /* 5 */  $str_qior1.=  str_pad('', 10, $char_pad_int, $type_pad_int); //Importe de la Orden 
            //   $str_qior1.= chr(13) . chr(10);
        }

        ##  Cambios del SIRHU correspondientes a los contratados  - @gluis - 01/10/2015 ##
        // Armo el archivo CH1 - Agrego el concepto "Salario Basico" para el escalafon 920:
        /* 1 */ $str_qich1.= str_pad('QI', 2, $char_pad_string, $type_pad_string); //CÃ³digo de InstituciÃ³n
        /* 2 */ $str_qich1.= str_pad(ConstanteSirhu::__ESCALAFON_CONTRATO_LOCACION_SERVICIOS__, 4, $char_pad_int, $type_pad_int); //CÃ³digo de Escalafon
        /* 3 */ $str_qich1.= str_pad(1, 6, $char_pad_int, $type_pad_int); //CÃ³digo de Concepto 
        /* 4 */ $str_qich1.= str_pad('Salario Basico', 40, $char_pad_string, $type_pad_string); //DescripciÃ³n Concepto
        /* 5 */ $str_qich1.= str_pad(1, 1, $char_pad_int, $type_pad_int); //Caracter remunerativo/bonificable
        /* 6 */ $str_qich1.= str_pad(1, 1, $char_pad_string, $type_pad_string); //Tipo Concepto
        $str_qich1.= chr(13) . chr(10);

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
        $consultores = $em->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->findAll();
        $remuneracionContrato = null;
        $dtAhora = new DateTime();
        foreach ($consultores as $consultor) {

            $contratos = $emContable
                    ->getRepository('ADIFContableBundle:Consultoria\ContratoConsultoria')
                    ->getContratosByNotEstadosAndIdConsultor(
                    array(
                ConstanteEstadoContrato::ADENDADO, ConstanteEstadoContrato::PRORROGADO), $consultor->getId()
            );

            foreach ($contratos as $contrato) {
                if ($contrato->getFechaFin() >= $dtAhora) {
                    // Solo los contratos actuales
                    foreach ($contrato->getCiclosFacturacionPendientes() as $cicloFacturacion) {
                        // Me importa solo el ultimo
                        $remuneracionContrato = $cicloFacturacion->getImporte();
                    }

                    // Armo el archivo LH1:
                    /* 1 */ $str_qilh1.= str_pad('QI', 2, $char_pad_string, $type_pad_string); //CÃ³digo de InstituciÃ³n
                    /* 2 */ $str_qilh1.= str_pad('', 2, $char_pad_string, $type_pad_string); //Blancos
                    // Le seteo para los contratados, todos DNI o sea el primera parametro 1
                    /* 3 */ $str_qilh1.= str_pad(ConstanteSirhu::getCodigoSirhu(1, 3), 4, $char_pad_string, $type_pad_string); //Tipo de Documento
                    /* 4 */ $str_qilh1.= str_pad($consultor->getDNI(), 16, $char_pad_int, $type_pad_int); //NÃºmero de documento
                    /* 5 */ $str_qilh1.= str_pad(ConstanteSirhu::__ESCALAFON_CONTRATO_LOCACION_SERVICIOS__, 4, $char_pad_int, $type_pad_int); //CÃ³digo de Escalafon
                    /* 6 */ $str_qilh1.= str_pad('G', 4, $char_pad_string, $type_pad_string); //CÃ³digo de Agrupamiento
                    /* 7 */ $str_qilh1.= str_pad('O', 4, $char_pad_string, $type_pad_string); //CÃ³digo de Nivel
                    // Me fijo el grado o categoria para el contratado
                    $categoria = ConstanteSirhu::getCategoriaContratoLocacionServicios($remuneracionContrato);
                    /* 8 */ $str_qilh1.= str_pad($categoria['cat'], 4, $char_pad_string, $type_pad_string); //CÃ³digo de Grado o CategorÃ­a
                    /* 9 */ $str_qilh1.= str_pad('0', 14, $char_pad_int, $type_pad_int); //CÃ³digo Unidad/Nudo
                    /* 10 */ $str_qilh1.= str_pad('30', 2, $char_pad_string, $type_pad_string); //CÃ³digo Jurisdiccion
                    /* 11 */ $str_qilh1.= str_pad('0', 2, $char_pad_int, $type_pad_int); //CÃ³digo Sub-Jurisdiccion
                    /* 12 */ $str_qilh1.= str_pad('0', 3, $char_pad_int, $type_pad_int); //CÃ³digo Entidad
                    /* 13 */ $str_qilh1.= str_pad('0', 2, $char_pad_int, $type_pad_int); //CÃ³digo de Programa
                    /* 14 */ $str_qilh1.= str_pad('0', 2, $char_pad_int, $type_pad_int); //CÃ³digo de SubPrograma
                    /* 15 */ $str_qilh1.= str_pad('0', 2, $char_pad_int, $type_pad_int); //CÃ³digo de Proyecto
                    /* 16 */ $str_qilh1.= str_pad('0', 2, $char_pad_int, $type_pad_int); //CÃ³digo de Actividad
                    /* 17 */ $str_qilh1.= str_pad('02', 2, $char_pad_int, $type_pad_int); //CÃ³digo de UbicaciÃ³n Geografica
                    /* 18 */ $str_qilh1.= str_pad('', 6, $char_pad_string, $type_pad_string); //Blancos
                    /* 19 */ $str_qilh1.= str_pad($entity->getFechaCierreNovedades()->format('Ym'), 6, $char_pad_string, $type_pad_string); //PerÃ­odo correspondiente a la informacion
                    // A Tipo Planta le harcodeo una "T", ya que representa "TEMPORARIO/CONTRATADO"
                    /* 20 */ $str_qilh1.= str_pad('T', 1, $char_pad_string, $type_pad_string); //Tipo Planta
                    /* 21 */ $str_qilh1.= str_pad($contrato->getFechaInicio()->format('Ymd'), 8, $char_pad_string, $type_pad_string); //Fecha de ultimo ingreso
                    /* 22 */ $str_qilh1.= str_pad('12', 4, $char_pad_int, $type_pad_int); //CÃ³digo fuente de financiamiento
                    // A "Marca de Estado" le harcodeo una "A" => "ADCRIPTO RECIBIDO"
                    /* 23 */ $str_qilh1.= str_pad('A', 1, $char_pad_string, $type_pad_string); //Marca de Estado
                    $str_qilh1.= chr(13) . chr(10);

                    // Armo el archivo LH2:
                    /* 1 */ $str_qilh2.= str_pad('QI', 2, $char_pad_string, $type_pad_string); //CÃ³digo de InstituciÃ³n
                    /* 2 */ $str_qilh2.= str_pad('', 2, $char_pad_string, $type_pad_string); //Blancos
                    /* 3 */ $str_qilh2.= str_pad(ConstanteSirhu::getCodigoSirhu(1, 3), 4, $char_pad_string, $type_pad_string); //Tipo de Documento
                    /* 4 */ $str_qilh2.= str_pad($consultor->getDNI(), 16, $char_pad_int, $type_pad_int); //NÃºmero de documento
                    /* 5 */ $str_qilh2.= str_pad(ConstanteSirhu::__ESCALAFON_CONTRATO_LOCACION_SERVICIOS__, 4, $char_pad_int, $type_pad_int); //CÃ³digo de Escalafon
                    /* 6 */ $str_qilh2.= str_pad(1, 6, $char_pad_int, $type_pad_int); //CÃ³digo de Concepto 
                    $mon_concepto = number_format($remuneracionContrato, 2, '.', '');
                    $mon_concepto = str_pad($mon_concepto, 15, $char_pad_int, $type_pad_int);
                    /* 7 */ $str_qilh2.= $mon_concepto; //Monto  Concepto    
                    /* 8 */ $str_qilh2.= str_pad('99', 2, $char_pad_int, $type_pad_int); //Tipo unidad fisica   
                    /* 9 */ $str_qilh2.= str_pad('1', 6, $char_pad_int, $type_pad_int); //Unidades Fisicas
                    /* 10 */ $str_qilh2.= str_pad($entity->getFechaCierreNovedades()->format('Ym'), 6, $char_pad_string, $type_pad_string); //PerÃ­odo de la info
                    $str_qilh2.= chr(13) . chr(10);

                    // Armo archivo DP1:
                    /* 1 */ $str_qidp1.= str_pad(ConstanteSirhu::getCodigoSirhu(1, 3), 4, $char_pad_string, $type_pad_string); //Tipo de Documento
                    /* 2 */ $str_qidp1.= str_pad($consultor->getDNI(), 16, $char_pad_int, $type_pad_int); //NÃºmero de documento
                    /* 3 */ $str_qidp1.= $this->mb_str_pad(str_replace('-', ' ', substr(AdifApi:: stringCleaner(strtoupper($consultor->getRazonSocial())), 0, 30)), 40, $char_pad_string, $type_pad_string); //Apellido y Nombre de la Persona
                    /* 4 */ $str_qidp1.= str_pad('', 8, $char_pad_string, $type_pad_string); //Fecha de Nacimiento
                    /* 5 */ $str_qidp1.= str_pad('', 4, $char_pad_string, $type_pad_string); //Codigo de Sexo
                    /* 6 */ $str_qidp1.= str_pad('', 4, $char_pad_string, $type_pad_string); //Codigo de Sexo
                    /* 7 */ $str_qidp1.= str_pad('', 1, $char_pad_string, $type_pad_string); //Blancos
                    /* 8 */ $str_qidp1.= str_pad('QI', 2, $char_pad_string, $type_pad_string); //CÃ³digo de instituciÃ³n
                    /* 7 */ $str_qidp1.= str_pad('', 2, $char_pad_string, $type_pad_string); //Blancos
                    /* 8 */ $str_qidp1.= str_pad($contrato->getFechaInicio()->format('Ym'), 6, $char_pad_string, $type_pad_string); //Fecha de Ingreso a la APN
                    /* 9 */ $str_qidp1.= str_pad('0', 2, $char_pad_string, $type_pad_string); //Codigo de nacionalidad
                    /* 10 */$str_qidp1.= str_pad('', 2, $char_pad_string, $type_pad_string); //Codigo nivel de educacion
                    /* 11 */$str_qidp1.= str_pad('', 30, $char_pad_string, $type_pad_string); //Descripcion tÃ­tulo obtenido
                    /* 12 */$str_qidp1.= str_pad(str_replace('-', '', $consultor->getCUIT()), 11, $char_pad_string, $type_pad_string); //CUIT
                    /* 13 */$str_qidp1.= str_pad('R', 1, $char_pad_string, $type_pad_string); //Sistema previsional
                    /* 14 */$str_qidp1.= str_pad('4', 4, $char_pad_int, $type_pad_int); //Codigo Sistema previsional
                    /* 15 */$str_qidp1.= str_pad('0', 10, $char_pad_int, $type_pad_int); //CÃ³digo Obra Social
                    /* 16 */$str_qidp1.= str_pad('0', 15, $char_pad_int, $type_pad_int); //NÃºmero de afiliacion
                    /* 17 */$str_qidp1.= str_pad('8', 1, $char_pad_int, $type_pad_int); //Tipo de Horario
                    $str_qidp1.= chr(13) . chr(10);

                    // Armo archivo DP3:
                    /* 1 */ $str_qidp3.= str_pad('QI', 4, $char_pad_string, $type_pad_string); //CÃ³digo de InstituciÃ³n
                    /* 2 */ $str_qidp3.= str_pad(ConstanteSirhu::getCodigoSirhu(1, 3), 4, $char_pad_string, $type_pad_string); //Tipo de Documento
                    /* 3 */ $str_qidp3.= str_pad($consultor->getDNI(), 16, $char_pad_int, $type_pad_int); //NÃºmero de documento
                    /* 4 */ $str_qidp3.= str_pad('', 5, $char_pad_string, $type_pad_string); //Blancos
                    /* 5 */ $str_qidp3.= str_pad('', 47, $char_pad_string, $type_pad_string); //Descripcion tÃ­tulo obtenido
                    /* 6 */ $str_qidp3.= str_pad('', 1, $char_pad_string, $type_pad_string); //Codigo nivel de educacion
                    /* 7 */ $str_qidp3.= str_pad('', 8, $char_pad_string, $type_pad_string); //Fecha del tÃ­tulo
                    /* 8 */ $str_qidp3.= str_pad('', 8, $char_pad_string, $type_pad_string); //Blancos
                    /* 9 */ $str_qidp3.= str_pad('', 60, $char_pad_string, $type_pad_string); //Descripcion de Entidad Educativa
                    /* 10 */$str_qidp3.= str_pad('', 3, $char_pad_string, $type_pad_string); //Duracion en meses
                    /* 11 */$str_qidp3.= str_pad('', 5, $char_pad_string, $type_pad_string); //Blancos
                    $str_qidp3.= chr(13) . chr(10);

                    // Armo el archivo de "ordenes"
                    /* 1 */ $str_qior1.= str_pad('QI', 2, $char_pad_string, $type_pad_string); //Código de Institución
                    /* 2 */ $str_qior1.= str_pad('', 1, $char_pad_string, $type_pad_string); //Número de Inciso
                    /* 3 */ $str_qior1.= str_pad('0', 6, $char_pad_int, $type_pad_int); //Número de Orden
                    $importeOrden = number_format($remuneracionContrato, 2, '.', '');
                    /* 4 */ $str_qior1.= str_pad($importeOrden, 10, $char_pad_int, $type_pad_int); //Importe de la Orden 
                    $str_qior1.= chr(13) . chr(10);
                }
            }
        }

        //var_dump($str_qilh1);exit;

        $rootpath = $this->get('kernel')->getRootDir() . '/../web/uploads/sirhu/';
        $zippath = $rootpath . $entity->getNumero() . '.zip';


        //Generación de lote
        $lote = generarLote($entity->getNumero(), $rootpath);
        $ext = '.SIR';

        //Borro Zip si existe
        if (is_file($zippath)) {
            unlink($zippath);
        }


        $zip = new ZipArchive;
        $zip->open($zippath, ZipArchive::CREATE);
        $zip->addFile(generarArchivo($str_qidp1, 'qidp1' . $lote . $ext, $rootpath), 'qidp1' . $lote . $ext);
        $zip->addFile(generarArchivo($str_qidp3, 'qidp3' . $lote . $ext, $rootpath), 'qidp3' . $lote . $ext);
        $zip->addFile(generarArchivo($str_qilh1, 'qilh1' . $lote . $ext, $rootpath), 'qilh1' . $lote . $ext);
        $zip->addFile(generarArchivo($str_qilh2, 'qilh2' . $lote . $ext, $rootpath), 'qilh2' . $lote . $ext);
        $zip->addFile(generarArchivo($str_qich1, 'qich1' . $lote . $ext, $rootpath), 'qich1' . $lote . $ext);
        $zip->addFile(generarArchivo($str_qior1, 'qior1' . $lote . $ext, $rootpath), 'qior1' . $lote . $ext);
        $zip->close();

        //Borro archivos temporales SIR 
        unlink($rootpath . 'qidp1' . $lote . $ext);
        unlink($rootpath . 'qidp3' . $lote . $ext);
        unlink($rootpath . 'qilh1' . $lote . $ext);
        unlink($rootpath . 'qilh2' . $lote . $ext);
        unlink($rootpath . 'qich1' . $lote . $ext);



        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($zippath));
        $response->headers->set('Content-Disposition', 'filename="' . basename($zippath) . '"');
        $response->headers->set('Content-length', filesize($zippath));

        $response->setContent(file_get_contents($zippath));

        return $response;
    }

    private function formatConcepto(LiquidacionEmpleadoConcepto $liquidacionEmpleadoConcepto = null) {
        if (!$liquidacionEmpleadoConcepto || ($liquidacionEmpleadoConcepto && in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getCodigo(), array(30, 31, 32, 33, 34, 35, 36, 37, 38, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 53, 54, 95, 96, 97, 98)))) {
            return 0;
        } else {
            return $liquidacionEmpleadoConcepto->getMonto();
        }
    }

    private function cargarCampos931(LiquidacionEmpleado $liquidacionEmpleado) {
        /*
          return array(
          28 => $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo('101.1')),
          37 => $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(51)) + $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(52)) + $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(53)),
          38 => $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(30)) + $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(31)) + $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(32)) + $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(33)) + $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(34)) + $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(35)) + $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(36)) + $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(37)),
          40 => $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(48)) + $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(49)),
          52 => $this->formatConcepto($liquidacionEmpleado->getConceptoCodigo(72))
          );
         */
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $liquidacionRepo = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion');
        $idEmpleado = $liquidacionEmpleado->getEmpleado()->getId();
        $fechaCierreNovedades = $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades();
        return array(
            28 => $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, '101.1', $fechaCierreNovedades),
            37 => $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 51, $fechaCierreNovedades) + $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 52, $fechaCierreNovedades)
                //38 => $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 30, $fechaCierreNovedades) + $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 31, $fechaCierreNovedades) + $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 32, $fechaCierreNovedades) + $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 33, $fechaCierreNovedades) + $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 34, $fechaCierreNovedades) + $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 35, $fechaCierreNovedades) +$liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 36, $fechaCierreNovedades) + $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 37, $fechaCierreNovedades),
                //40 => $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 48, $fechaCierreNovedades) + $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 49, $fechaCierreNovedades),
                //52 => $liquidacionRepo->getConceptoByCodigoAndMes($idEmpleado, 72, $fechaCierreNovedades)
        );
    }

    private function cargarLicencias($licencias, $fechaInicio, $fechaFin, $char_pad_int, $type_pad_int) {
        $result = '';
        if ($licencias->isEmpty()) {
            /* 30 */ $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);  //Situación 1
            /* 31 */ $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);  //Dia 1
            /* 32 */ $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);  //Situación 2
            /* 33 */ $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);  //Dia 2
            /* 34 */ $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);  //Situación 3
            /* 35 */ $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);  //Dia 3    
        } else {
            $licencias = $licencias->toArray();
            $licencias = array_values($licencias);

            $situacion_2 = false;
            $situacion_3 = false;
            // Licencia 1
            $empleadoLicencia = $licencias[0];

            if ($empleadoLicencia->getFechaDesde() <= $fechaInicio) {
                /* 30 */ $result .= str_pad($empleadoLicencia->getTipoLicencia()->getCodigo(), 2, $char_pad_int, $type_pad_int);
                /* 31 */ $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
            } else {
                /* 30 */ $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
                /* 31 */ $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
                /* 32 */ $result .= str_pad($empleadoLicencia->getTipoLicencia()->getCodigo(), 2, $char_pad_int, $type_pad_int);
                /* 33 */ $result .= str_pad($empleadoLicencia->getFechaDesde()->format('d'), 2, $char_pad_int, $type_pad_int);
                $situacion_2 = true;
            }
            $ultima_fecha_hasta = clone($empleadoLicencia->getFechaHasta());
            $ultima_fecha_hasta->add(new DateInterval('P1D'));

            if (isset($licencias[1])) {
                // Licencia 2
                $empleadoLicencia = $licencias[1];
                if ($empleadoLicencia->getFechaDesde() != $ultima_fecha_hasta) {
                    // Hay un bache entre las dos licencias, lleno con activo
                    $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
                    $result .= str_pad($ultima_fecha_hasta->format('d'), 2, $char_pad_int, $type_pad_int);
                    if (!$situacion_2) {
                        // Si no estaba cargada la 2, tengo lugar, sino ya completé las 3
                        $result .= str_pad($empleadoLicencia->getTipoLicencia()->getCodigo(), 2, $char_pad_int, $type_pad_int);
                        $result .= str_pad($empleadoLicencia->getFechaDesde()->format('d'), 2, $char_pad_int, $type_pad_int);
                    }
                    $situacion_3 = true;
                } else {
                    $result .= str_pad($empleadoLicencia->getTipoLicencia()->getCodigo(), 2, $char_pad_int, $type_pad_int);
                    $result .= str_pad($empleadoLicencia->getFechaDesde()->format('d'), 2, $char_pad_int, $type_pad_int);
                    if ($situacion_2) {
                        $situacion_3 = true;
                    }
                }
                $ultima_fecha_hasta = clone($empleadoLicencia->getFechaHasta());
                $ultima_fecha_hasta->add(new DateInterval('P1D'));
            } else {
                if ($licencias[0]->getFechaHasta() < $fechaFin) {
                    $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
                    $result .= str_pad($ultima_fecha_hasta->format('d'), 2, $char_pad_int, $type_pad_int);
                } else {
                    $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);
                    $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);
                }
                if (!$situacion_2) {
                    $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);
                    $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);
                }
                $situacion_3 = true;
            }

            if (isset($licencias[2])) {
                if (!$situacion_3) {
                    // Licencia 3
                    $empleadoLicencia = $licencias[2];
                    if ($empleadoLicencia->getFechaDesde() != $ultima_fecha_hasta) {
                        // Hay un bache entre las dos licencias, lleno con activo
                        $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
                        $result .= str_pad($ultima_fecha_hasta->format('d'), 2, $char_pad_int, $type_pad_int);
                    } else {
                        $result .= str_pad($empleadoLicencia->getTipoLicencia()->getCodigo(), 2, $char_pad_int, $type_pad_int);
                        $result .= str_pad($empleadoLicencia->getFechaDesde()->format('d'), 2, $char_pad_int, $type_pad_int);
                    }
                }
            } else {
                if (!$situacion_3) {
                    if (isset($licencias[1]) && $licencias[1]->getFechaHasta() < $fechaFin) {
                        $result .= str_pad('1', 2, $char_pad_int, $type_pad_int);
                        $result .= str_pad($ultima_fecha_hasta->format('d'), 2, $char_pad_int, $type_pad_int);
                    } else {
                        $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);
                        $result .= str_pad('0', 2, $char_pad_int, $type_pad_int);
                    }
                }
            }
        }
        return $result;
    }

    private function getCodigoSituacion($licencias) {
        $situacion1 = substr($licencias, 0, 2);
        $situacion2 = substr($licencias, 4, 2);
        $situacion3 = substr($licencias, 8, 2);
        if ($situacion3 != '00') {
            return $situacion3;
        }
        if ($situacion2 != '00') {
            return $situacion2;
        }
        return $situacion1;
    }

    private function mb_str_pad($input, $pad_length, $pad_string, $pad_type) {
        $diff = strlen($input) - mb_strlen($input, 'UTF8');
        return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
    }

    /**
     * Genera el archivo de exportacion netcash
     *
     * @Route("/liquidacion/exportarnetcash/{id}", name="liquidaciones_exportar_netcash")
     * @Method("POST")
     * @Security("has_role('ROLE_RRHH_EXPORTAR_NETCASH')")
     */
    public function exportarNetcash(Request $request, $id) {
        $fecha_bbva = $request->request->get('fechabbva');
        $fecha_otros = $request->request->get('fechaotros');
        $fecha_venc_frances = $request->request->get('fechavencimientobbva');
        $fecha_venc_otros = $request->request->get('fechavencimientootros');

        $docLiquidacionHelper = new DocumentoLiquidacionHelper($this);
        return $docLiquidacionHelper->exportarNetcash(
                        $this->container, $id, $fecha_bbva, $fecha_otros, $fecha_venc_frances, $fecha_venc_otros
        );
    }

    /**
     * Genera los recibos de sueldos
     *
     * @Route("/recibos/imprimir/{ids}", name="liquidaciones_recibos_imprimir")
     * @Method("GET|POST")
     * @Security("has_role('ROLE_IMPRIMIR_RECIBOS_SUELDOS')")
     */
    public function imprimirReciboAction(Request $request, $ids = null) {
        if (!$ids) {
            if (!$request->request->get('ids')) {
                throw $this->createNotFoundException('No se recibieron los parametros necesarios.');
            }
            $ids = $request->request->get('ids');
        }

        $docLiquidacionHelper = new DocumentoLiquidacionHelper($this);
        return $docLiquidacionHelper->imprimirRecibos($this->container, $ids);
    }
    
    /**
     * Devuelve el template para cerrar la liquidación
     *
     * @Route("/liquidacion/form_imprimir_recibos_session", name="liquidaciones_form_imprimir_recibos_session")
     * @Method("POST")   
     * @Template("ADIFRecursosHumanosBundle:Liquidacion:form_imprimir_recibos_session.html.twig")
     * @Security("has_role('ROLE_IMPRIMIR_RECIBOS_SUELDOS')")
     */
    public function getFormImprimirRecibosSessionAction() {
        return array();
    }
    
    /**
     * Genera los recibos de sueldos
     *
     * @Route("/session/recibos/imprimir", name="liquidaciones_en_sesion_recibos_imprimir")
     * @Method("GET|POST")
     * @Security("has_role('ROLE_IMPRIMIR_RECIBOS_SUELDOS')")
     */
    public function imprimirReciboEnSessionAction(Request $request)
    {
        $liquidacion = $this->get('session')->get('liquidacion');
        if ($liquidacion && !empty($liquidacion)) {
            $docLiquidacionHelper = new DocumentoLiquidacionHelper($this);
            
            $lugarPago = $request->get('lugar_de_pago', null);
            $fechaPago = $request->get('fecha_pago', null);
            $dtFechaPago = \DateTime::createFromFormat('d/m/Y', $fechaPago);
            $parametrosLiquidacionSession = array(
                'lugarPago' => $lugarPago,
                'fechaPago' => $dtFechaPago->format('Y-m-d')
            );
            
            return $docLiquidacionHelper->imprimirRecibos($this->container, $ids = null, $liquidacion, $parametrosLiquidacionSession);
        } else {
            throw $this->createNotFoundException('No hay datos en sesión para imprimir el/los recibo/s.');
        }
    }

    /**
     * Genera el libro sueldos
     *
     * @Route("/librosueldos/imprimir/{idLiquidacion}", name="liquidaciones_librosueldos_imprimir")
     * @Method("GET|POST")
     * @Security("has_role('ROLE_RRHH_ALTA_LIQUIDACIONES')")
     */
    public function imprimirLibroSueldosAction(Request $request, $idLiquidacion = null) {
        if (!$idLiquidacion) {
            if (!$request->request->get('idLiquidacion')) {
                throw $this->createNotFoundException('No se recibieron los parametros necesarios.');
            }
            $ids = $request->request->get('idLiquidacion');
        }

        $docLiquidacionHelper = new DocumentoLiquidacionHelper($this);
        return $docLiquidacionHelper->imprimirLibroSueldos($this->container, $idLiquidacion);

        // return sfView::NONE;
    }

    /**
     * Genera el header para libro sueldos
     *
     * @Route("/librosueldos/imprimir_header/{idLiquidacion}", name="liquidaciones_librosueldos_imprimir_header")
     * @Method("GET|POST")
     * @Security("has_role('ROLE_RRHH_ALTA_LIQUIDACIONES')")
     */
    public function imprimirLibroSueldosHeaderAction(Request $request, $idLiquidacion = null) {
        if (!$idLiquidacion) {
            if (!$request->request->get('idLiquidacion')) {
                throw $this->createNotFoundException('No se recibieron los parametros necesarios.');
            }
            $ids = $request->request->get('idLiquidacion');
        }

        $docLiquidacionHelper = new DocumentoLiquidacionHelper($this);
        return $docLiquidacionHelper->imprimirLibroSueldosHeader($this->container, $idLiquidacion);
    }

    /**
     * Devuelve el template para cerrar la liquidación
     *
     * @Route("/liquidacion/form_cerrar", name="liquidaciones_form_cerrar")
     * @Method("POST")   
     * @Template("ADIFRecursosHumanosBundle:Liquidacion:cerrar_form.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_LIQUIDACIONES')")
     */
    public function getFormCerrarAction() {
        return array();
    }

    /**
     * Devuelve el template para comenzar la liquidación
     *
     * @Route("/liquidacion/form_liquidar", name="liquidaciones_form_liquidar")
     * @Method("POST")   
     * @Template("ADIFRecursosHumanosBundle:Liquidacion:liquidar_form.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_LIQUIDACIONES')")
     */
    public function getFormLiquidarAction() {
        //$idsConceptosAdicionales = [362,363,456];

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $conceptosAdicionales = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')
                ->createQueryBuilder('c')
                ->select('c')
                ->innerJoin('c.conceptoLiquidacionAdicional', 'cla')
                ->getQuery()
                ->getResult();


        $tiposLiquidacion = $em->getRepository('ADIFRecursosHumanosBundle:TipoLiquidacion')->findAll();
        //$conceptosAdicionales = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')->findAllByIds($idsConceptosAdicionales);
        return array(
            'tiposLiquidacion' => $tiposLiquidacion,
            'conceptosAdicionales' => $conceptosAdicionales
        );
    }

    private function generarAutorizacionContableCargasSociales(Liquidacion $liquidacion, $montoCargas) {
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $ordenPago = new OrdenPagoCargasSociales();
        $ordenPago->setIdLiquidacion($liquidacion->getId())
                ->setImporte($montoCargas);
        setlocale(LC_ALL, "es_AR.UTF-8");
        $nombre_liquidacion = ucfirst(strftime("%B %Y", $liquidacion->getFechaCierreNovedades()->getTimestamp()));
        $this->get('adif.orden_pago_service')->initAutorizacionContable($ordenPago, 'Cargas sociales liquidación ' . $nombre_liquidacion);

        $emContable->persist($ordenPago);
    }

    /**
     * 
     * @param type $liquidacion
     * @return RenglonDeclaracionJurada
     */
    private function crearRenglonDeclaracionJurada(Liquidacion $liquidacion, $tipoImpuesto, $monto) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $renglonDDJJ = new RenglonDeclaracionJuradaLiquidacion();

        $renglonDDJJ->setIdLiquidacion($liquidacion->getId());

        $renglonDDJJ->setFecha($liquidacion->getFechaCierreNovedades());

        $renglonDDJJ->setTipoRenglonDeclaracionJurada(
                $emContable->getRepository('ADIFContableBundle:TipoRenglonDeclaracionJurada')
                        ->findOneByCodigo(ConstanteTipoRenglonDeclaracionJurada::LIQUIDACION)
        );

        $renglonDDJJ->setTipoImpuesto($emContable->getRepository('ADIFContableBundle:TipoImpuesto')
                        ->findOneByDenominacion($tipoImpuesto)
        );

        $renglonDDJJ->setEstadoRenglonDeclaracionJurada($emContable->getRepository('ADIFContableBundle:EstadoRenglonDeclaracionJurada')
                        ->findOneByDenominacion(ConstanteEstadoRenglonDeclaracionJurada::PENDIENTE));

        $renglonDDJJ->setMonto($monto);
        $renglonDDJJ->setMontoOriginal($monto);

        $emContable->persist($renglonDDJJ);
        $emContable->flush();

        return $renglonDDJJ;
    }

    /**
     * 
     * @param type $liquidacion
     * @return RenglonDeclaracionJurada
     */
    private function crearRenglonesRetencion(Liquidacion $liquidacion) {
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $renglones = array();

        //Obtengo las cuentas contables de todos los beneficiarios
        $cuentas_retenciones_liquidacion = array();
        $cuentas_beneficiarios = array();
        $beneficiarios_liquidacion = $emContable->getRepository('ADIFContableBundle:BeneficiarioLiquidacion')->findAll();

        foreach ($beneficiarios_liquidacion as $beneficiario_liquidacion) {
            /* @var $beneficiario_liquidacion BeneficiarioLiquidacion */
            foreach ($beneficiario_liquidacion->getCuentasContables() as $cuenta_beneficiario) {
                $cuentas_retenciones_liquidacion[] = $cuenta_beneficiario->getId();
                $cuentas_beneficiarios[$cuenta_beneficiario->getId()] = $beneficiario_liquidacion;
            }
        }

        foreach ($liquidacion->getLiquidacionEmpleados() as $liquidacionEmpleado) {
            /* @var $liquidacionEmpleado LiquidacionEmpleado */
            foreach ($liquidacionEmpleado->getLiquidacionEmpleadoConceptos() as $liquidacionEmpleadoConcepto) {
                /* @var $liquidacionEmpleadoConcepto LiquidacionEmpleadoConcepto */
                $concepto = $liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto();
                if ($concepto->getCuentaContable() == null) {
                    var_dump($concepto->getId());
                    var_dump($concepto->__toString());
                    die;
                }
                if (in_array($concepto->getCuentaContable()->getId(), $cuentas_retenciones_liquidacion)) {
                    //Si la cuenta de concepto es alguna de las cuentas de los beneficiarios
                    if (!isset($renglones[$concepto->getId()])) {
                        $renglones[$concepto->getId()] = array(
                            'beneficiario' => $cuentas_beneficiarios[$concepto->getCuentaContable()->getId()],
                            'cuentaContable' => $concepto->getCuentaContable(),
                            'conceptoVersion' => $liquidacionEmpleadoConcepto->getConceptoVersion()->getId(),
                            'monto' => 0
                        );
                    }
                    $renglones[$concepto->getId()]['monto'] += $liquidacionEmpleadoConcepto->getMonto();
                }
            }
        }
        //Debug::dump($renglones);die;
        foreach ($renglones as $renglon) {
            $renglonRetencion = new RenglonRetencionLiquidacion();

            $renglonRetencion->setBeneficiarioLiquidacion($renglon['beneficiario']);
            $renglonRetencion->setEstadoRenglonRetencionLiquidacion($emContable->getRepository('ADIFContableBundle:EstadoRenglonRetencionLiquidacion')->findOneByDenominacion(\ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonRetencionLiquidacion::PENDIENTE));
            $renglonRetencion->setIdConceptoVersion($renglon['conceptoVersion']);
            $renglonRetencion->setCuentaContable($renglon['cuentaContable']);
            $renglonRetencion->setIdLiquidacion($liquidacion->getId());
            $renglonRetencion->setMonto($renglon['monto']);

            $emContable->persist($renglonRetencion);
        }

        $emContable->flush();
    }

    /**
     * Recalculo de ganancias
     *
     * @Route("/recalcularGananciasRetroactivas/{anio}/{mesDesde}/{mesHasta}", name="liquidaciones_recalcular_ganancias_retroactivas")
     * @Security("has_role('ROLE_RRHH_ALTA_LIQUIDACIONES')")
     */
    public function recalcularGananciasRetroactivasAction(Request $request, $anio, $mesDesde, $mesHasta) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        for ($i = $mesDesde; $i <= $mesHasta; $i++) {
            /* @var $liquidacion Liquidacion */
            $liquidacion = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
                    ->createQueryBuilder('l')
                    ->innerJoin('l.liquidacionEmpleados', 'le')
                    ->innerJoin('le.empleado', 'e')
                    ->where('YEAR(l.fechaCierreNovedades) = ' . $anio)
                    ->andWhere('MONTH(l.fechaCierreNovedades) = ' . $i)
                    ->getQuery()
                    ->getOneOrNullResult();
            foreach ($liquidacion->getLiquidacionEmpleados() as $liquidacionEmpleado) {
                /* @var $liquidacionEmpleado LiquidacionEmpleado */
                if /* (($liquidacionEmpleado->getEmpleado()->getRangoRemuneracion()->getId() > 12) 
                  && */
                ($liquidacionEmpleado->getEmpleado()->getId() == 148) {
                    $gController = new GananciaController();
                    $gController->setContainer($this->container);
                    $gController->calculoGanancias($liquidacionEmpleado);
                }
            }
            $em->flush();
        }
        return $this->redirect($this->generateUrl('liquidaciones'));
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/diasCalculadosSac/", name="dias_calculados_sac")
     * @Method("GET|POST")
     * @Security("has_role('ROLE_RRHH_ALTA_LIQUIDACIONES')")
     */
    public function getDiasCalculadosSAC(Request $request) {
        $liquidacionHelper = new LiquidacionHelper($this);
        return new Response($liquidacionHelper->getDiasCalculadosSAC());
    }
    
     /**
     * Reporte de mejor remuneracion final por empleado por semestre y anio + dias calculados SAC
     * 
     * @Route("/reporte/mejor_remunerativo", name="reporte_mejor_remunerativo")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Liquidacion:reporte.mejor_remuneracion.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_LIQUIDACIONES')")
     */
    public function reporteIndexMejorRemunerativoAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Liquidaciones'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Mejor Remunerativo',
            'page_info' => 'Lista de mejor remunerativo'
        );
    }
    
    /**
     * Reporte de mejor remuneracion final por empleado por semestre y anio + dias calculados SAC
     * 
     * @Route("/reporte/get_mejor_remunerativo", name="mejor_remunerativo_dias_calculados_sac")
     * @Method("POST")
     * @Security("has_role('ROLE_RRHH_ALTA_LIQUIDACIONES')")
     */
    public function getReporteMejorRemunerativoAction(Request $request)
    {
        $resultado = array();
        $semestre = $request->get('semestre');
        $anio = $request->get('anio');
        $mesInicio = $mesFin = 0;
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        switch ($semestre) {
            case 1:
                // Mejor (BRUTO1 + BRUTO2) en los meses del 01 al 06 del año en curso.
                $mesInicio = 1;
                $mesFin = 6;
                $resultado = $em
                        ->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
                        ->mejorBrutoByPeriodo($mesInicio, $mesFin, $anio);
                break;
            case 2:
                // Mejor (BRUTO1 + BRUTO2) en los meses del 07 al 12. Si la liquidación se hace en enero, el período es del año anterior, no en curso.
                $mesInicio = 7;
                $mesFin = 12;
                $resultado = $em
                        ->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
                        ->mejorBrutoByPeriodo($mesInicio, $mesFin, $anio);
                break;
        }
        
        if (!empty($resultado)) {
            for($i = 0; $i < count($resultado); $i++) {
                $item = $resultado[$i];
                $basicoEmpleado = $item['monto_basico'];
                $mejorRemuneracion = $item['mejor_remuneracion'];
                $mejorRemuneracionFinal = $mejorRemuneracion ? ($mejorRemuneracion < $basicoEmpleado ? $basicoEmpleado : $mejorRemuneracion) : $basicoEmpleado;
                $resultado[$i]['mejor_remuneracion_final'] = $mejorRemuneracionFinal;
                $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($item['id_empleado']);
                
                $dias_trabajados = $dias_licencia = $dias_licencia_sin_liquidar = $dias = 0;
                $liquidacionHelper = new LiquidacionHelper($this);
                
                switch ($semestre) {
                    case 1:
                        $fechaDesde = new \DateTime($anio . '-01-01');
                        $fechaHasta = new \DateTime(date('Y-m-d', strtotime((new \DateTime($anio . '-06-01'))->format("Y-m-t"))));
                        
                        $dias_trabajados = $empleado->getDiasTrabajadosSemestre($fechaDesde, $fechaHasta);
                        $dias_licencia = $liquidacionHelper->getDiasLicencia($empleado, $fechaDesde, $fechaHasta);
                        $dias_licencia_sin_liquidar = $liquidacionHelper->getDiasLicenciaSinLiquidar($empleado, new \DateTime($anio . '-06-01'), $fechaHasta);
                        $dias = $dias_trabajados - $dias_licencia - $dias_licencia_sin_liquidar;            
                        break;
                    case 2: 
                        $fechaDesde = new \DateTime($anio . '-07-01');
                        $fechaHasta = new \DateTime(date('Y-m-d', strtotime((new \DateTime($anio . '-12-01'))->format("Y-m-t"))));
                        
                        $dias_trabajados = $empleado->getDiasTrabajadosSemestre($fechaDesde, $fechaHasta);
                        $dias_licencia = $liquidacionHelper->getDiasLicencia($empleado, $fechaDesde, $fechaHasta);
                        $dias_licencia_sin_liquidar = $liquidacionHelper->getDiasLicenciaSinLiquidar($empleado, new \DateTime($anio . '-12-01'), $fechaHasta);
                        $dias = $dias_trabajados - $dias_licencia - $dias_licencia_sin_liquidar;            
                        break;
                }
                
                $resultado[$i]['dias_calculo_sac'] = $dias;
                $resultado[$i]['path_empleado'] = $this->generateUrl('reporte_mejor_remunerativo_detalle', 
                        array(
                            'mesInicio' => $mesInicio,
                            'mesFin'    => $mesFin,
                            'anio'      => $anio,
                            'idEmpleado' => $item['id_empleado']
                        )
                );
            }
        }
        
        return new JsonResponse($resultado);
    }
    
    /**
     * Reporte detalle de los mejores remunerativos por empleado mes a mes
     * 
     * @Route("/reporte/mejor_remunerativo/{mesInicio}/{mesFin}/{anio}/{idEmpleado}", name="reporte_mejor_remunerativo_detalle")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Liquidacion:reporte.mejor_remuneracion_detalle.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_LIQUIDACIONES')")
     */
    public function reporteIndexMejorRemunerativoDetalleAction($mesInicio, $mesFin, $anio, $idEmpleado)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $resultado = $em
                        ->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
                        ->mejorBrutoByPeriodoAndIdEmpleado($mesInicio, $mesFin, $anio, $idEmpleado);
        
        
        $bread = $this->base_breadcrumbs;
        $bread['Reporte mejor remunerativo'] = $this->generateUrl('reporte_mejor_remunerativo');

        return array(
            'entities' => $resultado,
            'breadcrumbs' => $bread,
            'page_title' => 'Mejor Remunerativo detalle',
            'page_info' => 'Mejor remunerativo detalle'
        );
    }

//    /**
//     * @Route("/generarRetencionesLiquidacion/", name="liquidaciones_generar_retenciones_liquidacion")
//     * @Method("GET")     
//     * @Security("has_role('ROLE_MENU_SEGURIDAD')")
//     */
//    public function generarRetencionesLiquidacion() {
//        $em = $this->getDoctrine()->getManager($this->getEntityManager());
//
//        $liquidacion = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find(26);
//        /* $liquidacion = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
//          ->createQueryBuilder('l')
//          ->select('l, le, lec, e, p, s, cat, con, g, a, f649, cv, c', 'sg')
//          ->innerJoin('l.liquidacionEmpleados', 'le')
//          ->innerJoin('le.liquidacionEmpleadoConceptos', 'lec')
//          ->innerJoin('le.empleado', 'e')
//          ->innerJoin('e.persona', 'p')
//          ->innerJoin('e.idSubcategoria', 's')
//          ->innerJoin('s.idCategoria', 'cat')
//          ->innerJoin('cat.idConvenio', 'con')
//          ->leftJoin('e.idGerencia', 'g')
//          ->leftJoin('e.idArea', 'a')
//          ->leftJoin('e.formulario649', 'f649')
//          ->innerJoin('lec.conceptoVersion', 'cv')
//          ->innerJoin('cv.concepto', 'c')
//          ->leftJoin('e.idSubgerencia', 'sg')
//          ->where('l.id = :idLiquidacion')->setParameter('idLiquidacion', 27)
//          ->orderBy('e.nroLegajo * 1', 'ASC')
//          ->getQuery()
//          ->getSingleResult(); */
//
//        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());
//        $renglonDDJJSicore = $this->crearRenglonDeclaracionJurada($liquidacion, ConstanteTipoImpuesto::Ganancias, $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getGananciaByLiquidacion($liquidacion->getId()));
//        $liquidacion->setIdRenglonDeclaracionJuradaSicore($renglonDDJJSicore->getId());
//        $renglonDDJJSicoss = $this->crearRenglonDeclaracionJurada($liquidacion, ConstanteTipoImpuesto::SICOSS, $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getSicossByLiquidacion($liquidacion->getId()));
//        $liquidacion->setIdRenglonDeclaracionJuradaSicoss($renglonDDJJSicoss->getId());
//        //Se genera el renglón de retencion
//        $this->crearRenglonesRetencion($liquidacion);
//
//        $em->flush();
//
//        // Se generan los asientos de devengamiento de sueldos
//        $total_asiento = 0;
//        $this->get('adif.asiento_service')->generarAsientoSueldos($liquidacion, $this->getUser(), $total_asiento);
//        $this->get('adif.contabilidad_presupuestaria_service')->crearDevengadoSueldosFromLiquidacion($liquidacion);
//        $this->get('adif.contabilidad_presupuestaria_service')->crearEjecutadoSueldosFromLiquidacion($liquidacion);
//        // Se genera la autorización contable de cargas sociales
//        $this->generarAutorizacionContableCargasSociales($liquidacion, $total_asiento);
//        $emContable->flush();
//        return $this->redirect($this->generateUrl('liquidaciones'));
//    }

    /**
     * Finds and displays a Liquidacion entity.
     *
     * @Route("/generar_asientos/{id}", name="liquidaciones_generar_asientos", requirements={"id" = "\d+"}, defaults={"id" = null})
     * @Method("GET")
     * @Template()
     */
    public function generarAsientosAction($id) {
        /* @var $liquidacion Liquidacion */
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $liquidacion_persistida = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find($id);

        if (!$liquidacion_persistida) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Liquidacion.');
        }
		
		// Comienzo la transaccion
        $emContable->getConnection()->beginTransaction();
		
        try {
            // Se genera el renglón de declaracion jurada
            // Creo el Renglon de DDJJ asociado
            $renglonDDJJSicore = $this->crearRenglonDeclaracionJurada($liquidacion_persistida, ConstanteTipoImpuesto::Ganancias, $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getGananciaByLiquidacion($liquidacion_persistida->getId()));
            $liquidacion_persistida->setIdRenglonDeclaracionJuradaSicore($renglonDDJJSicore->getId());
            $renglonDDJJSicoss = $this->crearRenglonDeclaracionJurada($liquidacion_persistida, ConstanteTipoImpuesto::SICOSS, $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getSicossByLiquidacion($liquidacion_persistida->getId()));
            $liquidacion_persistida->setIdRenglonDeclaracionJuradaSicoss($renglonDDJJSicoss->getId());

            // Se genera el renglón de retencion
            $this->crearRenglonesRetencion($liquidacion_persistida);

            // Se generan los asientos de devengamiento de sueldos
			$total_asiento = 0;
			$numerosAsientos = $ids = array();
            $mensajeErrorAsientos = $this->get('adif.asiento_service')->generarAsientoSueldos($liquidacion_persistida, $this->getUser(), $total_asiento, $numerosAsientos, $ids);
            $mensajeErrorAsientoDevengado = $this->get('adif.contabilidad_presupuestaria_service')->crearDevengadoSueldosFromLiquidacion($liquidacion_persistida);
			$mensajeErrorAsientoEjecutado = $this->get('adif.contabilidad_presupuestaria_service')->crearEjecutadoSueldosFromLiquidacion($liquidacion_persistida);
			
			// Si los asientos fallaron
            if ($mensajeErrorAsientos != '') {
                $this->container->get('request')->getSession()->getFlashBag()
                        ->add('error', $mensajeErrorAsientos);
            }
			
            // Si el asiento presupuestario devengado fallo
            if ($mensajeErrorAsientoDevengado != '') {
                $this->container->get('request')->getSession()->getFlashBag()
                        ->add('error', $mensajeErrorAsientoDevengado);
            }
            
			// Si el asiento presupuestario ejecutado fallo
            if ($mensajeErrorAsientoEjecutado != '') {
                $this->container->get('request')->getSession()->getFlashBag()
                        ->add('error', $mensajeErrorAsientoEjecutado);
            }

            if ($mensajeErrorAsientos == '' && $mensajeErrorAsientoDevengado == '' && $mensajeErrorAsientoEjecutado == '') {

                $emContable->flush();
                $emContable->getConnection()->commit();

                $em->flush();
				
				$this->container->get('request')->getSession()->getFlashBag()
                        ->add('success', 'Se ha creado con exito los asientos contables y los devengados y ejecutados presupuestarios.');
						
				$dataArray = [
					'data-id-asiento-sueldo' => implode(',', $ids)
				];
				
				$mensajeFlash = $this->get('adif.asiento_service')
                                ->showMensajeFlashColeccionAsientosContables($numerosAsientos, $dataArray, false);
            }
        } catch (\Exception $e) {

            $emContable->getConnection()->rollback();
            $emContable->close();

            throw $e;
        }

        $em->clear();
        $emContable->clear();
		
		

        return $this->redirect($this->generateUrl('liquidaciones'));
    }
    
     /**
     * Arregla el impuesto retenido anual, de la liquidacion id, por error del concepto 994 que no acumula
     *
     * @Route("/arreglar_impuesto_retenido_anual/{id}", name="arreglar_impuesto_retenido_anual")
     * @Method("GET")
     * @Template()
     * @author gluis
     */
    public function arreglarImpuestoRetenidoAnualLiquidacionAction($id)
    {
        //die("hola");
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        //$liquidacionActual = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find($id);
        $liquidacionAnteriorId = $id - 1;
        
        // Voy a buscar el saldo impuesto mes anterior
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('nro_legajo', 'nro_legajo');
        $rsm->addScalarResult('saldo_impuesto_mes', 'saldo_impuesto_mes');
        
        $sql = "SELECT id, nro_legajo, saldo_impuesto_mes FROM vw_arreglo_impuesto_retenido_anual_liquidacion WHERE id = $liquidacionAnteriorId";
        
        $query = $em->createNativeQuery($sql, $rsm);
        $gananciaEmpleadosAnterior = $query->getResult();
        
        $saldoImpuestoMesAnterior = array();
        foreach($gananciaEmpleadosAnterior as $gananciaEmpleadoLiquidacion) {
            $saldoImpuestoMesAnterior[ $gananciaEmpleadoLiquidacion['nro_legajo'] ] = $gananciaEmpleadoLiquidacion['saldo_impuesto_mes'];
        }
        
        // Voy a buscar el saldo impuesto actual
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('ganancia_empleado_id', 'ganancia_empleado_id');
        $rsm->addScalarResult('nro_legajo', 'nro_legajo');
        $rsm->addScalarResult('nombre', 'nombre');
        $rsm->addScalarResult('apellido', 'apellido');
        $rsm->addScalarResult('saldo_impuesto_mes', 'saldo_impuesto_mes');
        
        $sql = "SELECT id, ganancia_empleado_id, nro_legajo, nombre, apellido, saldo_impuesto_mes FROM vw_arreglo_impuesto_retenido_anual_liquidacion WHERE id = $id";
        
        $query = $em->createNativeQuery($sql, $rsm);
        $gananciaEmpleadosActual = $query->getResult();
        
        $saldoImpuestoMesActual = array();
        $gananciaEmpleadoIds = array();
        $empleados = array();
        foreach($gananciaEmpleadosActual as $gananciaEmpleadoLiquidacion) {
            $saldoImpuestoMesActual[ $gananciaEmpleadoLiquidacion['nro_legajo'] ] = $gananciaEmpleadoLiquidacion['saldo_impuesto_mes'];
            $gananciaEmpleadoIds[ $gananciaEmpleadoLiquidacion['nro_legajo'] ] = $gananciaEmpleadoLiquidacion['ganancia_empleado_id'];
            $empleados[ $gananciaEmpleadoLiquidacion['nro_legajo'] ] = $gananciaEmpleadoLiquidacion['apellido'] . ', ' . $gananciaEmpleadoLiquidacion['nombre'];
        }
        
        // Voy arreglando uno a uno
        $html = '<table border="1"><tr><th>Legajo</th><th>Apellido y Nombre</th><th>Impuesto Retenido Anual Anterior</th><th>Impuesto Retenido Anual Arreglado</th></tr>';
        if (count($saldoImpuestoMesAnterior) == count($saldoImpuestoMesActual)) {
            foreach($saldoImpuestoMesAnterior as $_nroLegajoAnterior => $_saldoImpuestoMesAnterior) {
                if (isset($saldoImpuestoMesActual[$_nroLegajoAnterior]) && isset($gananciaEmpleadoIds[$_nroLegajoAnterior])) {
                    
                    $_saldoImpuestoMesActual = $saldoImpuestoMesActual[$_nroLegajoAnterior];
                    $_gananciaEmpleadoIdActual = $gananciaEmpleadoIds[$_nroLegajoAnterior];
                    $_empleado = $empleados[$_nroLegajoAnterior];
                    $impuestoRetenidoAnualActualArreglado = $_saldoImpuestoMesAnterior + $_saldoImpuestoMesActual;
                    $gananciaEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:GananciaEmpleado')->find($_gananciaEmpleadoIdActual);
                    $_impuestoRetenidoAnual = $gananciaEmpleado->getImpuestoRetenidoAnual();
                    $gananciaEmpleado->setImpuestoRetenidoAnual($impuestoRetenidoAnualActualArreglado);
                    $em->persist($gananciaEmpleado);
                    $em->flush();
                    
                    $html .= '<tr>';
                    $html .= '<td>' . $_nroLegajoAnterior . '</td>';
                    $html .= '<td>' . $_empleado . '</td>';
                    $html .= '<td>' . $_impuestoRetenidoAnual . '</td>';
                    $html .= '<td>' . $impuestoRetenidoAnualActualArreglado . '</td>';
                    $html .= '</tr>';
                }
            }
        }
        
        $html .= '</table>';
        
        return new Response($html);
    }
	
	/**
     * Arregla no remunerativo de junio mal sumado al haber neto
     *
     * @Route("/arreglar_no_remunerativo_julio_2016/{actualizar}", name="arreglar_no_remunerativo")
     * @Method("GET")
     * @Template()
     * @author gluis
     */
    public function arreglarNoRemunerativoAction($actualizar)
    {
        
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
		
		$empleados = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->findBy(
			array('activo' => 1)
			//array('nroLegajo' => 'ASC')
		);
		
		$html = '<table border="1">';
		$html .= '<tr><th>Legajo</th><th>Apellido</th><th>Nombre</th><th>No Remunerativo</th><th>Neto acumulado</th><th>Neto acumulado corregido</th><th>Actualizo?</th></tr>';
		
		$em->getConnection()->beginTransaction();
		
		try {
			
			foreach($empleados as $empleado) {
				$liquidacionEspecialAdicional = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
													->createQueryBuilder('le')
													->select('le')
													->innerJoin('le.liquidacion', 'l')
													->where('l.id = 42') // l.id = 42 es la liquidacion Adicional, la ultima
													->andWhere('le.empleado = :empleado')->setParameter('empleado', $empleado)
													->getQuery()
													->getResult();
				
				$concepto66 = 0;
				if (!empty($liquidacionEspecialAdicional)) {
					$concepto66 = $liquidacionEspecialAdicional[0]->getNoRemunerativo();
				}
				
				/** ultima liquidacion **/
				$ultimaLiquidacionEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
													->createQueryBuilder('le')
													->select('le, ge')
													->innerJoin('le.liquidacion', 'l')
													->innerJoin('le.gananciaEmpleado', 'ge')
													->where('l.id = 43') 
													->andWhere('le.empleado = :empleado')->setParameter('empleado', $empleado)
													->getQuery()
													->getResult();
													
				$netoAcumulado = isset( $ultimaLiquidacionEmpleado[0] ) 
					? $ultimaLiquidacionEmpleado[0]->getGananciaEmpleado()->getHaberNetoAcumulado()
					: 0;
				
				$haberNetoAcumuladoArreglado = $netoAcumulado - $concepto66;
				
				$actualizo = 'No';
				
				if ($actualizar == 1 && !empty($concepto66) && !empty($liquidacionEspecialAdicional)) {
					
					$IdGananciaEmpleado = $ultimaLiquidacionEmpleado[0]->getGananciaEmpleado()->getId();
				
					$gananciaEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:GananciaEmpleado')->find($IdGananciaEmpleado);
				
					$gananciaEmpleado->setHaberNetoAcumulado($haberNetoAcumuladoArreglado);
				
					$em->persist($gananciaEmpleado);
				
					$em->flush();
					
					$actualizo = 'Si';
				}
				
				$html .= '<tr>';
				$html .= '<td>' . $empleado->getNroLegajo() . '</td>';
				$html .= '<td>' . $empleado->getPersona()->getApellido() . '</td>';
				$html .= '<td>' . $empleado->getPersona()->getNombre() . '</td>';
				$html .= '<td>' . $concepto66 . '</td>';
				$html .= '<td>' . $netoAcumulado . '</td>';
				$html .= '<td>' . $haberNetoAcumuladoArreglado . '</td>';
				$html .= '<td>' . $actualizo . '</td>';
				$html .= '</tr>';
				
			}
			
			$em->getConnection()->commit();
			
		} catch(\Exception $e) {

            $em->getConnection()->rollback();
            $em->close();

            throw $e;
			
		}
        
        
        $html .= '</table>';
        
        return new Response($html);
    }
	
	/**
	* Devolucion de ganancias por blanqueo de bienes personales segun Ley. 27.260 
	* que en lo que refiere a los beneficiarios de esta excepción, tiene vigencia a partir del 16/08
	*
	* @Route("/devolver_ganancias_blanqueo_bienes/{nroLegajo}/{actualizar}", name="devolver_ig_blanqueo_bienes")
    * @Method("GET")
	* @author gluis
	*/
	public function devolverIGPorBlanqueoBienes($nroLegajo, $actualizar)
	{
		
		$nroLegajos = explode(',', $nroLegajo);
		
		//var_dump($nroLegajos, $nroLegajo); exit;
		
		$em = $this->getDoctrine()->getManager($this->getEntityManager());
		
		$empleados = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->findBy(
				array('activo' => 1, 'nroLegajo' => $nroLegajos)
		);
		
		$maxLiquidacionId = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getMaxLiquidacionId();
		
		$html = '<table border="1">';
		$html .= '<tr><th>Indice</th><th>Legajo</th><th>Apellido</th><th>Nombre</th>';
		$html .= '<th>Haber neto acumulado</th><th>Cuota junio</th><th>Cuota sumadas</th><th>Monto total a restar</th><th>Haber neto acumulado corregido</th><th>¿Actualizo?</th></tr>';
		
		//\Doctrine\Common\Util\Debug::dump( $empleados ); exit;
		
		try {
			
			$actualizo = 'No';
			$i = 0;
			$em->getConnection()->beginTransaction();
			
			foreach($empleados as $empleado) {
				
				// Busco el haber neto acumulado de la liquidacion de Junio (SAC)
				$rsm = new ResultSetMapping();

				$rsm->addScalarResult('id', 'id');
				$rsm->addScalarResult('haber_neto', 'haber_neto');
				
				$sql = "
					SELECT e.id, ge.haber_neto
					FROM empleado e
					LEFT JOIN liquidacion_empleado le ON e.id = le.id_empleado
					LEFT JOIN g_ganancia_empleado ge ON le.id_ganancia_empleado = ge.id
					WHERE e.nro_legajo = ?
					AND le.id_liquidacion = 41
				";
        
				$query = $em->createNativeQuery($sql, $rsm);
				$query->setParameter(1, $empleado->getNroLegajo());
				
				
				$haberNetoSAC = $query->getOneOrNullResult();
				$cuotaIGJunio = 0;
				if ($haberNetoSAC != null && isset($haberNetoSAC['haber_neto'])) {
					$cuotaIGJunio = $haberNetoSAC['haber_neto'];
				}
				
				// Busco el concepto de ganancia "SAC primer semestre" del mes de Julio (ultima liquidacion)
				//var_dump($maxLiquidacionId);exit;
				$maxLiquidacionId++;
				$cuotaIG = 0;
				$haberNetoAcumulado = 0;
				$idGananciaEmpleado = 0;
				$fechasCierresLiquidacion = array();
				for($idLiquidacion = 43; $idLiquidacion < $maxLiquidacionId; $idLiquidacion++) {
					
					$rsm = new ResultSetMapping();

					$rsm->addScalarResult('id', 'id');
					$rsm->addScalarResult('monto', 'monto');
					$rsm->addScalarResult('id_ganancia_empleado', 'id_ganancia_empleado');
					$rsm->addScalarResult('haber_neto_acumulado', 'haber_neto_acumulado');
					$rsm->addScalarResult('fecha_cierre_novedades', 'fecha_cierre_novedades');
					
					$sql = "
						SELECT e.id, cgc.monto, ge.id AS id_ganancia_empleado, ge.haber_neto_acumulado,
							l.fecha_cierre_novedades
						FROM empleado e
						LEFT JOIN liquidacion_empleado le ON e.id = le.id_empleado
						LEFT JOIN liquidacion l ON le.id_liquidacion = l.id
						LEFT JOIN g_ganancia_empleado ge ON le.id_ganancia_empleado = ge.id
						LEFT JOIN g_concepto_ganancia_calculado cgc ON ge.id = cgc.id_ganancia_empleado
						LEFT JOIN g_concepto_ganancia cg ON cgc.id_concepto_ganancia = cg.id
						WHERE e.nro_legajo = ?
						AND le.id_liquidacion = $idLiquidacion
						AND cg.id = 59 -- SAC primer semestre
					";
        
					$query = $em->createNativeQuery($sql, $rsm);
					$query->setParameter(1, $empleado->getNroLegajo());
				
				
					$conceptoSAC = $query->getOneOrNullResult();
					if ($conceptoSAC != null && isset($conceptoSAC['monto'])) {
						$cuotaIG += number_format($conceptoSAC['monto'], 2, '.', '');
						$idGananciaEmpleado = $conceptoSAC['id_ganancia_empleado'];
						$haberNetoAcumulado = $conceptoSAC['haber_neto_acumulado'];
						$fechasCierresLiquidacion[] = $conceptoSAC['fecha_cierre_novedades'];
					}
				}
			
				$montoTotalRestar = number_format($cuotaIGJunio + $cuotaIG, 2, '.', '');
				
				$haberNetoAcumuladoArreglado = $haberNetoAcumulado - $montoTotalRestar;
			
				// Busco la ultima ganancia empleado
				$gananciaEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:GananciaEmpleado')->find($idGananciaEmpleado);
				
				if ($actualizar == 1 && $gananciaEmpleado && !empty($haberNetoAcumuladoArreglado)) {
					
					$gananciaEmpleado->setHaberNetoAcumulado($haberNetoAcumuladoArreglado);
				
					$em->persist($gananciaEmpleado);
				
					$em->flush();
					
					$actualizo = 'Si';
				} else {
					$actualizo = 'No';
				}
			
				$html .= '<tr>';
				$html .= '<td>' . $i . '</td>';
				$html .= '<td>' . $empleado->getNroLegajo() . '</td>';
				$html .= '<td>' . $empleado->getPersona()->getApellido() . '</td>';
				$html .= '<td>' . $empleado->getPersona()->getNombre() . '</td>';
				$html .= '<td>' . $haberNetoAcumulado . '</td>';
				$html .= '<td>' . $cuotaIGJunio . '</td>';
				$html .= '<td>' . $cuotaIG . '</td>';
				$html .= '<td>' . $montoTotalRestar . '</td>';
				$html .= '<td>' . $haberNetoAcumuladoArreglado . '</td>';
				$html .= '<td>' . $actualizo . '</td>';
				$html .= '</tr>';
				
				$i++;
			}
			
			$em->getConnection()->commit();
			
			
		} catch(\Exception $e) {

            $em->getConnection()->rollback();
            $em->close();

            throw $e;
		}
        
        $html .= '</table><br/><br/>';
		$html .= '(*) Se ha ejecutado los meses de ';
		
		setlocale(LC_ALL, "es_AR.UTF-8");
		
		$meses = array();
		foreach($fechasCierresLiquidacion as $fecha) {
			$meses[] = ucfirst(strftime("%B %Y", strtotime($fecha) ));
		}
		
		$html .= implode(', ', $meses);
        
        return new Response($html);
	}

}
