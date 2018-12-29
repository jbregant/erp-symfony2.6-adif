<?php

namespace ADIF\RecursosHumanosBundle\Helper;

use ADIF\RecursosHumanosBundle\Controller\GananciaController;
use ADIF\RecursosHumanosBundle\Controller\LiquidacionController;
use ADIF\RecursosHumanosBundle\Entity\Concepto;
use ADIF\RecursosHumanosBundle\Entity\Empleado;
use ADIF\RecursosHumanosBundle\Entity\EmpleadoNovedad;
use ADIF\RecursosHumanosBundle\Entity\Liquidacion;
use ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado;
use ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleadoConcepto;
use ADIF\RecursosHumanosBundle\Entity\TipoConcepto;
use ADIF\RecursosHumanosBundle\Entity\TipoLiquidacion;
use ADIF\RecursosHumanosBundle\Repository\ConceptosRepository;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Helper de cálculo de liquidación
 *
 * @author eprimost
 */
class LiquidacionHelper {
    /* Tipos de cálculos */

    const __TIPO_CONCEPTO = 1;
    const __TIPO_NOVEDAD = 2;
    const __TIPO_AMBOS = 3;

    /* Códigos de resultados */
    const __TIPO_RESULT_OK = 'OK';
    const __TIPO_RESULT_ERROR = 'ERROR';
    
    const __CODIGO_CONCEPTO_ANTIGUEDAD__ = 4;
    
    /**
     *
     * @var EntityManager
     */
    private $em;

    /**
     *
     * @var LiquidacionController
     */
    private $controller;

    /**
     *
     * @var Logger
     */
    private $logger;

    /**
     *
     * @var ContainerAware
     */
    private $container;

    /**
     * 
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    function __construct(LiquidacionController $controller) {
        $this->controller = $controller;
        $this->em = $controller->getDoctrine()->getManager($controller->getEntityManager());
        $this->expressionLanguage = new ExpressionLanguage();
        $this->logger = new Logger('liquidacion');

        $monologFormat = "%message%\n";
//        $monologFormat = "[%datetime%] %level_name%: %message%\n";
        $dateFormat = "Y/m/d H:i:s";
        $monologLineFormat = new LineFormatter($monologFormat, $dateFormat);

        $streamHandler = new StreamHandler($controller->get('kernel')->getRootDir() . '/logs/liquidacion_' . date('Y_m_d__H_i_s') . '.log', Logger::INFO);
        $streamHandler->setFormatter($monologLineFormat);

        $this->logger->pushHandler($streamHandler);
    }

    /**
     * 
     * @param ContainerAware $container
     * @param array $ids Empleados a liquidar
     * @param string $fecha
     * @return array(
     *              'result' => (__TIPO_RESULT_OK|__TIPO_RESULT_ERROR)
     *              'msg'    => (Opcional) Mensaje de error
     *          )
     * 
     */
    public function liquidar($container, $ids = null, $tipoLiquidacion = TipoLiquidacion::__HABITUAL, $fecha = null, $idsConceptosAdicionales = null, $aplicaGanancias) {
        if (!$ids) {
            //$this->controller->get('session')->getFlashBag()->add('error', 'Debe seleccionar al menos un empleado para liquidar.');
            return array(
                'result' => self::__TIPO_RESULT_ERROR,
                'msg' => 'Debe seleccionar al menos un empleado para liquidar.'
            );
        }

        if (!$fecha) {
            $this->controller->get('session')->getFlashBag()->add('error', 'Debe seleccionar una fecha de cierre de novedades.');
            return array(
                'result' => self::__TIPO_RESULT_ERROR,
                'msg' => 'Debe seleccionar una fecha de cierre de novedades.'
            );
        }

        $this->container = $container;

        $this->logInfo("LIQUIDACIÓN " . date('d/m/Y H:i:s'));
        $this->logInfo("TIPO: " . new TipoLiquidacion($tipoLiquidacion));

        // Empleados a liquidar
        $idsEmpleados = json_decode($ids, '[]');

        $fechaCierreString = substr($fecha, 6, 4) . '-' . substr($fecha, 3, 2) . '-' . substr($fecha, 0, 2);
        $fechaCierreNovedades = new DateTime(date('Y-m-d', strtotime($fechaCierreString)));
        $idUsuarioLiquidacion = $this->controller->get('security.context')->getToken()->getUser()->getId();
		$mes = $fechaCierreNovedades->format('m');
        $anio = $fechaCierreNovedades->format('Y');
		
//		if ($fechaCierreString == '2018-07-02') {
//			// Fix 1 SAC 2018 - 01/07/2018
//			$fechaCierreString = '2018-06-28';
//			$fechaCierreNovedades = new DateTime(date('Y-m-d', strtotime($fechaCierreString)));
//			$mes = 6;
//		}
		
        // Obtengo el último número de liquidación
        $siguienteNroLiquidacion = $this->em->createQuery('SELECT MAX(l.numero)+1 AS siguiente_nro_liquidacion FROM ADIFRecursosHumanosBundle:Liquidacion l ')->getSingleScalarResult();
        $numeroLiquidacion = $siguienteNroLiquidacion ? $siguienteNroLiquidacion : 1;

        // Liquidación 
        $liquidacion = new Liquidacion();
        $liquidacion
                ->setFechaCierreNovedades($fechaCierreNovedades)
                ->setIdUsuario($idUsuarioLiquidacion)
                ->setNumero($numeroLiquidacion)
                ->setTipoLiquidacion($this->em->getRepository('ADIFRecursosHumanosBundle:TipoLiquidacion')->find($tipoLiquidacion));

        // Todos los conceptos que entran en el bruto 1.  
        /* @var $conceptosRepo ConceptosRepository */
        $conceptosRepo = $this->em->getRepository('ADIFRecursosHumanosBundle:Concepto');
        $parametrosLiquidacionRepo = $this->em->getRepository('ADIFRecursosHumanosBundle:ParametrosLiquidacion');


//      Default $opc_globales
        $opc_globales = array(
            'conceptos_bruto_1' => array(),
            'conceptos_bruto_2' => array(),
            'conceptos_descuentos' => array(),
            'conceptos_no_remunerativos' => array(),
            'conceptos_ganancias' => array(),
            'conceptos_contribuciones' => array()
        );

//      Si es adicional, se filtran los conceptos que puede elegir al liquidar
//      Hasta ahora solo no remunerativos (Códigos: 64, 65, 66 y 66.1)
        if ($tipoLiquidacion == TipoLiquidacion::__ADICIONAL) {
            //Buscar conceptos y versiones de los seleccionados
            $idsConceptosAdicionales = json_decode($idsConceptosAdicionales, '[]');
            $opc_globales['conceptos_no_remunerativos'] = $conceptosRepo->findAllByIds($idsConceptosAdicionales);
            if ($aplicaGanancias) {
                // Si la liquidación está marcada para retener ganancias
                $opc_globales['conceptos_ganancias'] = $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__CALCULO_GANANCIAS));
            }
			
			// Fix temporal para que me tome la gratificacion solidaria 66.1 (ID = 551) como contribucion art variable
			if ($fechaCierreNovedades->format('Y') == 2017 && $fechaCierreNovedades->format('m') == 2) {
				foreach($idsConceptosAdicionales as $idConceptoAdicional) {
					
					if ($idConceptoAdicional == 551) {
						$opc_globales['conceptos_contribuciones'] = $conceptosRepo->findByCodigo(Concepto::__CODIGO_ART_VARIABLE);
					}
				}
			}
        } else {
            if ($tipoLiquidacion == TipoLiquidacion::__SAC) {
                // Sólo marcar en conceptos bruto 1, el concepto SAC que corresponda (1er o 2do semestre)
                if ($fechaCierreNovedades->format('n') == 6) {
                    $codigoSAC = Concepto::__CODIGO_SAC_1_SEMESTRE;
                } else {
                    if ($fechaCierreNovedades->format('n') == 12) {
                        $codigoSAC = Concepto::__CODIGO_SAC_2_SEMESTRE;
                    } else {
                        $this->logError("FECHA DE CIERRE LIQUIDACIÓN SAC FUERA DE LOS MESES JUNIO/DICIEMBRE");
                        return array(
                            'result' => self::__TIPO_RESULT_ERROR,
                            'msg' => 'La fecha de cierre seleccionada est&aacute; fuera de los meses requeridos (<b>Junio</b> o <b>Diciembre</b>).'
                        );
                    }
                }
                $opc_globales['conceptos_bruto_1'] = $conceptosRepo->findAllByCodigos(array($codigoSAC));
                $opc_globales['concepto_SAC'] = $opc_globales['conceptos_bruto_1'][0];

                $conceptos_aportes_sac = $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__APORTE));
                $conceptos_descuentos_sac = $conceptosRepo->findAllByCodigos(array(Concepto::__CODIGO_ANTICIPO_SUELDO, Concepto::__CODIGO_EMBARGO, Concepto::__CODIGO_ANTICIPO_NETO_NEGATIVO));
                
                $opc_globales['conceptos_descuentos'] = array_merge($conceptos_aportes_sac, $conceptos_descuentos_sac);
                
                if ($aplicaGanancias) {
                    // Si la liquidación está marcada para retener ganancias
                    $opc_globales['conceptos_ganancias'] = $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__CALCULO_GANANCIAS));
                }
                
                if ($mes == 12 && $anio == 2017) {
                    $opc_globales['conceptos_ganancias'] = $conceptosRepo->findAllByCodigos(array(Concepto::__CODIGO_998));
                }
                
                $opc_globales['conceptos_contribuciones'] = $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__CONTRIBUCIONES, TipoConcepto::__CUOTA_SINDICAL_CONTRIBUCIONES), null, array(Concepto::__CODIGO_ART_FIJA, Concepto::__CODIGO_SEGURO_VIDA));
                
                /**
                 * Saco la contribucion "Contribución Patronal UF" => concepto codigo 208 para la liquidacion del tipo SAC
                 * @gluis - 14/12/2017
                 */
                $contribucionesArrayColection = new ArrayCollection($opc_globales['conceptos_contribuciones']);
                $contribucionesSinContribucionUF = $contribucionesArrayColection
                    ->filter(function($concepto) {
                        return $concepto->getCodigo() != Concepto::__CODIGO_CONTRIBUCION_UF;
                });
                        
                $opc_globales['conceptos_contribuciones'] = $contribucionesSinContribucionUF->toArray();
                
            } else {
                $opc_globales = array(
                    'conceptos_bruto_1' => $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__REMUNERATIVO), false),
                    'conceptos_bruto_2' => $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__REMUNERATIVO), true),
                    'conceptos_no_remunerativos' => $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__NO_REMUNERATIVO)),
                    'conceptos_descuentos' => $conceptosRepo->findAllByTipoConceptoAndNovedad(array(
                        TipoConcepto::__APORTE,
                        TipoConcepto::__DESCUENTO,
                        TipoConcepto::__CUOTA_SINDICAL_APORTES)),
                    'conceptos_ganancias' => $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__CALCULO_GANANCIAS)),                
                    'conceptos_contribuciones' => $conceptosRepo->findAllByTipoConceptoAndNovedad(array(TipoConcepto::__CONTRIBUCIONES, TipoConcepto::__CUOTA_SINDICAL_CONTRIBUCIONES))
                );
            }
            
        }

        // Opciones para todos los empleados
        $opc_globales['parametros_liquidacion'] = new ArrayCollection($parametrosLiquidacionRepo->createQueryBuilder('pl')->getQuery()->getResult());
        $opc_globales['fecha_ultimo_cierre'] = $this->getFechaUltimoCierre();
        $opc_globales['fecha_cierre'] = $fechaCierreNovedades;

        $tope_sin_sac = $opc_globales['parametros_liquidacion']->filter(function($entry) {
            return in_array($entry->getNombre(), array('#tope_sin_sac_maximo#'));
        });

        $tope_con_sac = $opc_globales['parametros_liquidacion']->filter(function($entry) {
            return in_array($entry->getNombre(), array('#tope_con_sac_maximo#'));
        });

        if ($tope_sin_sac->isEmpty()) {
            $this->logError('No existe el par&aacute;metro de liquidaci&oacute;n #tope_sin_sac_maximo# en la configuraci&oacute;n.');
            $tope = 0;
        } else {
            $tope = $tope_sin_sac->first()->getValor();
            if ($tipoLiquidacion == TipoLiquidacion::__SAC) {
                if ($tope_con_sac->isEmpty()) {
                    $this->logError('No existe el par&aacute;metro de liquidaci&oacute;n #tope_con_sac_maximo# en la configuraci&oacute;n.');
                    $tope = 0;
                } else {
                    $tope = $tope_con_sac->first()->getValor() - $tope;
                }
            }
        }

        $opc_globales['tope_maximo'] = $tope;

        $empRepo = $this->em->getRepository('ADIFRecursosHumanosBundle:Empleado');
        
        $empQB = $empRepo
                ->createQueryBuilder('e')
                ->select('partial e.{id, idCuenta, nroLegajo, acdt, fechaEgreso, aplicaEscalaDiciembre, fechaInicioAntiguedad}, '
                        . 'partial c2.{id, cbu, idBanco}, partial p.{id, apellido, nombre, cuil}, partial s.{id, nombre, idCategoria, montoBasico, categoriaRecibo}, '
                        . 'partial cat.{id, nombre, idConvenio}, partial con.{id, nombre}, partial sub.{id, nombre}, n, c, b, f, '
                        . 'partial ger.{id, nombre}, partial con.{id, nombre}, partial tc.{id, fechaBaja, fechaDesde}, partial tcon.{id, nombre, codigo}'
                )
                ->innerJoin('e.persona', 'p')
                ->innerJoin('e.idSubcategoria', 's')
                ->innerJoin('s.idCategoria', 'cat')
                ->innerJoin('cat.idConvenio', 'con')
                ->leftJoin('e.idSubgerencia', 'sub')
                ->leftJoin('e.conceptos', 'c')
                ->leftJoin('e.novedades ', 'n', Join::WITH, 'n.fechaBaja is null')
                ->leftJoin('e.idCuenta', 'c2')
                ->leftJoin('c2.idBanco', 'b')
                ->leftJoin('e.formulario649', 'f')
                ->leftJoin('e.idGerencia', 'ger')
                ->leftJoin('e.tiposContrato', 'tc')
                ->leftJoin('tc.tipoContrato', 'tcon')
                ->where('e.id IN (:ids)')->setParameter('ids', $idsEmpleados);
        
//        $empQB = $empRepo
//                ->createQueryBuilder('e')
//                ->select('e, c, p, s, cat, con, n, c2, b')
//                ->innerJoin('e.persona', 'p')
//                ->innerJoin('e.idSubcategoria', 's')
//                ->innerJoin('s.idCategoria', 'cat')
//                ->innerJoin('cat.idConvenio', 'con')
//                ->leftJoin('e.conceptos', 'c')
//                ->leftJoin('e.novedades', 'n', Join::WITH, 'n.fechaBaja is null')
//                ->leftJoin('e.idCuenta', 'c2')
//                ->leftJoin('c2.idBanco', 'b')
//                ->where('e.id IN (:ids)')->setParameter('ids', $idsEmpleados)
//                ->orderBy('e.persona', 'DESC');
        
        $empleados = $empQB->getQuery()->getResult();

        foreach ($empleados as $empleado) {
            if ($this->validarEmpleado($empleado, $fechaCierreNovedades)) {
                /* @var $empleado Empleado */
                $this->logInfo("");
                $this->logInfo("------------------------------------------------------------------------------");
                $this->logInfo("Empleado : " . $empleado);
                $this->logInfo("------------------------------------------------------------------------------");

                // Asociación LIQUIDACION-EMPLEADO
                $liquidacionEmpleado = new LiquidacionEmpleado();
                $liquidacionEmpleado
                        ->setLiquidacion($liquidacion)
                        ->setEmpleado($empleado)
                        ->setBanco($empleado->getCuenta() ? $empleado->getCuenta()->getIdBanco() : null)
                        ->setCbu($empleado->getCuenta() ? $empleado->getCuenta()->getCbu() : null);

                //          Si es adicional, el básico no se tiene en cuenta
                if ($tipoLiquidacion == TipoLiquidacion::__ADICIONAL || $tipoLiquidacion == TipoLiquidacion::__SAC) {
                    $liquidacionEmpleado->setBasico(0);
                } else {
                    $liquidacionEmpleado->setBasico($this->proratearValorConcepto($empleado, $fechaCierreNovedades, $empleado->getSubcategoria()->getMontoBasico()));
                }

                $this->logInfo("Básico : " . $liquidacionEmpleado->getBasico());
                $this->logInfo("------------------------------------------------------------------------------");

                // Opciones de parseo. Inicializar con cada empleado. Se mezclan con las globales
                $opc = array_merge($opc_globales, array(
                    // Empleado procesado actualmente
                    'empleado' => $empleado,
                    // Concepto actualmente procesado
                    'concepto' => null,
                    // Novedad actualmente procesada
                    'novedad' => null,
                    // Valor del concepto actual
                    'concepto_valor' => 0,
                    // Parametros encontrados 
                    'parametros_formula' => array(),
                    // Parametros que ya fueron calculados. Inicialmente ninguno.
                    'parametros_calculados' => array(),
                    // LiquidacionEmpleado actual
                    'liquidacion_empleado' => $liquidacionEmpleado,
                    // Subtotal Bruto 1
                    'bruto_1' => 0,
                    // Subtotal Bruto 2
                    'bruto_2' => 0,
                    // Monto remunerativo con tope
                    'monto_remunerativo_con_tope' => 0,
                    // Subtotal NO REMUNERATIVOS
                    'monto_no_remunerativos' => 0,
                    // Subtotal CONTRIBUCIONES
                    'monto_contribuciones' => 0,
                    // Subtotal diferencia a restar por conceptos que no se deben descontar de ganancias
                    'diferencia_ganancias' => 0,
					'aportes' => 0,
					'montoNoSumaEnAportes' => 0
                ));

                if ($tipoLiquidacion == TipoLiquidacion::__SAC) {
                    // Si el tipo de liquidacion es SAC, le asocio el concepto correspondiente al empleado
                    $empleado->addConcepto($opc['concepto_SAC']);
                }

                // Traer primer subtotal remunerativo. BRUTO1.
                $this->logInfo("");
                $this->logInfo("");
                $this->logInfo("INICIO BRUTO 1");
                $montoBruto1 = $this->getMontoBruto1($opc);
                $liquidacionEmpleado->setBruto1($montoBruto1);
                $liquidacionEmpleado->setBruto1Ganancias(($montoBruto1 - $opc['diferencia_ganancias']));

                $opc['bruto_1'] = $montoBruto1;
                $this->logInfo("------------------------------------------------------------------------------");
                $this->logInfo("BRUTO 1: " . $montoBruto1);
                $this->logInfo("BRUTO 1 GANANCIAS: " . ($montoBruto1 - $opc['diferencia_ganancias']));
                $this->logInfo("------------------------------------------------------------------------------");

                //$empleado->addConcepto($opc['concepto_SAC']);
                // Traer segundo subtotal remunerativo. Segundo BRUTO.
                $this->logInfo("");
                $this->logInfo("");
                $this->logInfo("INICIO BRUTO 2");
                $montoBruto2 = $this->getMontoBruto2($opc);
                $liquidacionEmpleado->setBruto2($montoBruto2);
                $liquidacionEmpleado->setBruto2Ganancias(($montoBruto2 - $opc['diferencia_ganancias']));
                $opc['bruto_2'] = $montoBruto2;
                $this->logInfo("------------------------------------------------------------------------------");
                $this->logInfo("BRUTO 2: " . $montoBruto2);
                $this->logInfo("BRUTO 2 GANANCIAS: " . ($montoBruto2 - $opc['diferencia_ganancias']));
                $this->logInfo("------------------------------------------------------------------------------");

                // Setear el monto remunerativo con tope
                $this->logInfo("");
                $this->logInfo("");
                $this->logInfo("INICIO MONTO REMUNERATIVO CON TOPE");
				
				$epsilon = 0.00001;
				$sumatoriaBrutos = $montoBruto1 + $montoBruto2;
                
                if ($sumatoriaBrutos < $epsilon) {
					$sumatoriaBrutos = 0;
				}
				
				if ($sumatoriaBrutos < 0) {
					// Si por esas casualidades de la vida, la sumatoria de brutos da negativo, (porque matematicamente es posible)
					// devuelvo error
					$mensaje = "Error: La sumatoria de brutos ($sumatoriaBrutos) es negativa para el empleado: ";
					$mensaje .= '<b>' . $opc['empleado']->getPersona()->getApellido() . ', '. $opc['empleado']->getPersona()->getNombre() . 
							' (' . $opc['empleado']->getNroLegajo() . ') - ID ' . $opc['empleado']->getId() . '<b>';
					throw new \Exception($mensaje);
				}
				
                $montoRemunerativoConTope = $sumatoriaBrutos > $opc_globales['tope_maximo'] ? $opc_globales['tope_maximo'] : $sumatoriaBrutos;
                $liquidacionEmpleado->setMontoRemunerativoConTope($montoRemunerativoConTope);
                $opc['monto_remunerativo_con_tope'] = $montoRemunerativoConTope;
                $this->logInfo("------------------------------------------------------------------------------");
                $this->logInfo("MONTO REMUNERATIVO CON TOPE: " . $montoRemunerativoConTope);
                $this->logInfo("------------------------------------------------------------------------------");

                // Trae los descuentos.
                $this->logInfo("");
                $this->logInfo("");
                $this->logInfo("INICIO DESCUENTOS");
                $montoDescuentos = $this->getMontoDescuentos($opc);
                $liquidacionEmpleado->setDescuentos($montoDescuentos);
                $this->logInfo("------------------------------------------------------------------------------");
                $this->logInfo("DESCUENTOS: " . $montoDescuentos);
                $this->logInfo("------------------------------------------------------------------------------");
				
				// Trae los conceptos del tipo "aportes".
                $this->logInfo("");
                $this->logInfo("");
                $this->logInfo("INICIO CONCEPTOS TIPO \"APORTES\"");
				$aportes = $opc['aportes'];
				$liquidacionEmpleado->setAportes($aportes);
                $this->logInfo("------------------------------------------------------------------------------");
                $this->logInfo("APORTES: " . $aportes);
                $this->logInfo("------------------------------------------------------------------------------");
				
				$montoNoSumaEnAportes = $opc['montoNoSumaEnAportes'];
				$this->logInfo("------------------------------------------------------------------------------");
                $this->logInfo("MONTO EN CONCEPTOS QUE NO SUMAN AL PRORRATEO SAC (conceptos 2.3, 2.4, 54 y 55): " . $montoNoSumaEnAportes);
                $this->logInfo("------------------------------------------------------------------------------");
				
				// Calculo el monto remunerativo con tope - aportes.
                $this->logInfo("");
                $this->logInfo("");
                $this->logInfo("INICIO MONTO REMUNERATIVO CON TOPE MENOS LOS APORTES");
				$montoRemunerativoConTopeMenosAportes = $sumatoriaBrutos - $aportes - $montoNoSumaEnAportes;
				$liquidacionEmpleado->setMontoRemunerativoConTopeMenosAportes($montoRemunerativoConTopeMenosAportes);
                $this->logInfo("------------------------------------------------------------------------------");
                $this->logInfo("MONTO REMUNERATIVO CON TOPE MENOS LOS APORTES: " . $montoRemunerativoConTopeMenosAportes);
                $this->logInfo("------------------------------------------------------------------------------");
				
				// Calculo el prorrateo del sac.
                $this->logInfo("");
                $this->logInfo("");
                $this->logInfo("INICIO PRORRATEO DEL SAC");
				$prorrateoSac = 0;
				if ($empleado->getFechaEgreso() == null && $tipoLiquidacion != TipoLiquidacion::__SAC) {
					// Calculo el prorrateo del sac: 
					// cuando el empleado no esta dado de baja y cuando no sea SAC
					$prorrateoSac = $montoRemunerativoConTopeMenosAportes / 12;
				}
				$liquidacionEmpleado->setProrrateoSac($prorrateoSac);
                $this->logInfo("------------------------------------------------------------------------------");
                $this->logInfo("PRORRATEO DEL SAC: " . $prorrateoSac);
                $this->logInfo("------------------------------------------------------------------------------");
				
                // Trae subtotal NO remunerativo.
                $this->logInfo("");
                $this->logInfo("");
                $this->logInfo("INICIO NO REMUNERATIVOS");
                $montoNoRemunerativos = $this->getMontoNoRemunerativos($opc);
                $liquidacionEmpleado->setNoRemunerativo($montoNoRemunerativos);
                $liquidacionEmpleado->setNoRemunerativoGanancias(($montoNoRemunerativos - $opc['diferencia_ganancias']));
                $opc['monto_no_remunerativos'] = $montoNoRemunerativos;
                $this->logInfo("------------------------------------------------------------------------------");
                $this->logInfo("NO REMUNERATIVOS: " . $montoNoRemunerativos);
                $this->logInfo("NO REMUNERATIVOS GANANCIAS: " . ($montoNoRemunerativos - $opc['diferencia_ganancias']));
                $this->logInfo("------------------------------------------------------------------------------");

                // Trae subtotal Ganancias
                $this->logInfo("");
                $this->logInfo("");
                $this->logInfo("INICIO GANANCIAS");
                $retencionGanancias = $this->getRetencionGanancias($opc);
                $this->logInfo("------------------------------------------------------------------------------");
                $this->logInfo("RETENCION GANANCIAS: " . $retencionGanancias);
                $this->logInfo("------------------------------------------------------------------------------");

                $neto = $montoBruto1 + $montoBruto2 - $montoDescuentos + $montoNoRemunerativos - $retencionGanancias;
                $neto_redondeado = ceil($neto);
                $this->logInfo("");
                $this->logInfo("");
                $this->logInfo("NETO SIN REDONDEAR: " . $neto);
                $this->logInfo("REDONDEO: " . ($neto_redondeado - $neto));
                $this->logInfo("NETO: " . $neto_redondeado);
                $this->logInfo("------------------------------------------------------------------------------");

				$liquidacionEmpleado->setNeto($neto_redondeado);
				$liquidacionEmpleado->setRedondeo($neto_redondeado - $neto);
                
                
                /**
                 * Como el SAC de diciembre del 2017, no va a aplicar ganancias, pero si
                 * tiene que acumular el neto del SAC para el haber neto acumulado de la proxima 
                 * liquidacion (o sea la liq de diciembre)
                 * @gluis - 12/12/2017
                 */
                if ($mes == 12 && $anio == 2017 && $tipoLiquidacion == TipoLiquidacion::__SAC && !$aplicaGanancias) {
                    $gananciaEmpleado = new \ADIF\RecursosHumanosBundle\Entity\GananciaEmpleado();
                    $gananciaEmpleado->setHaberNeto($neto_redondeado);
                    // Voy a buscar el ultimo haber neto acumulado del empleado
                    $ultimoHaberNetoAcumulado = $this->em
                            ->getRepository('ADIFRecursosHumanosBundle:GananciaEmpleado')
                            ->getUltimoHaberNetoAcumulado($empleado->getId());
                    
                    $gananciaEmpleado->setHaberNetoAcumulado($ultimoHaberNetoAcumulado['haber_neto_acumulado'] + $gananciaEmpleado->getHaberNeto());
                    $gananciaEmpleado->setResultadoNeto($gananciaEmpleado->getHaberNetoAcumulado());
                    $gananciaEmpleado->setDiferencia($gananciaEmpleado->getHaberNetoAcumulado());
                    $gananciaEmpleado->setTotalDeducciones(0);
                    $gananciaEmpleado->setGananciaSujetaImpuesto(0);
                    $gananciaEmpleado->setPorcentajeASumar(0);
                    $gananciaEmpleado->setMontoFijo(0);
                    $gananciaEmpleado->setMontoSinExcedente(0);
                    $gananciaEmpleado->setTotalImpuesto(0);
                    $gananciaEmpleado->setExcedente(0);
                    $gananciaEmpleado->setSaldoImpuestoMes(0);
                    $liquidacionEmpleado->setGananciaEmpleado($gananciaEmpleado);
                }

                // Traer subtotal CONTRIBUCIONES.
                $this->logInfo("");
                $this->logInfo("INICIO CONTRIBUCIONES");
                $montoContribuciones = $this->getMontoContribuciones($opc, $sumatoriaBrutos);
                $liquidacionEmpleado->setContribuciones($montoContribuciones);
                $opc['monto_contribuciones'] = $montoContribuciones;
                $this->logInfo("------------------------------------------------------------------------------");
                $this->logInfo("CONTRIBUCIONES: " . $montoContribuciones);
                $this->logInfo("------------------------------------------------------------------------------");

                $liquidacion->getLiquidacionEmpleados()->add($liquidacionEmpleado);
            } else {
                $this->logInfo("");
                $this->logInfo("------------------------------------------------------------------------------");
                $this->logInfo("Empleado : " . $empleado);
                $this->logInfo("El empleado no cumple las condiciones para ser liquidado");
                $this->logInfo("------------------------------------------------------------------------------");
            }
        }

        $this->logInfo("------------------------------------------------------------------------------");
        $this->logInfo("FIN LIQUIDACIÓN");

        $this->controller->get('session')->set('liquidacion', $liquidacion);
                
        return array(
            'result' => self::__TIPO_RESULT_OK
        );
    }

    /**
     * Calcula el monto del bruto 1
     * @param array $opc Opciones globales
     * @return float bruto 1
     */
    private function getMontoBruto1(&$opc) {
        /* @var $opc['liquidacion_empleado'] LiquidacionEmpleado */
        return $opc['liquidacion_empleado']->getBasico() +
                $this->calcularConceptos($opc, $opc['conceptos_bruto_1'], self::__TIPO_CONCEPTO, $opc['liquidacion_empleado']->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC ? false : true);
    }

    /**
     * Calcula el monto del bruto 2
     * @param array $opc Opciones globales
     * @return double bruto 2
     */
    private function getMontoBruto2(&$opc) {
        return $this->calcularConceptos($opc, $opc['conceptos_bruto_2'], self::__TIPO_NOVEDAD);
    }

    /**
     * Calcula el monto por descuentos
     * @param array $opc Opciones globales
     * @return double por descuentos
     */
    private function getMontoDescuentos(&$opc) {
        return $this->calcularConceptos($opc, $opc['conceptos_descuentos'], self::__TIPO_AMBOS);
    }
	
    /**
     * Calcula el monto no remunerativo
     * @param array $opc Opciones globales
     * @return double no remunerativo
     */
    private function getMontoNoRemunerativos(&$opc) {
        return $this->calcularConceptos($opc, $opc['conceptos_no_remunerativos'], self::__TIPO_AMBOS);
    }

    /**
     * Calcula el monto de ganancias
     * @param array $opc Opciones globales
     * @return double ganancias
     */
    private function getRetencionGanancias(&$opc) {
        return $this->calcularConceptos($opc, $opc['conceptos_ganancias'], self::__TIPO_AMBOS);
    }

    /**
     * Calcula el monto de contribuciones
     * @param array $opc Opciones globales
     * @return double contribuciones
     */
    private function getMontoContribuciones(&$opc, $sumatoriaBrutos = null) {
        return $this->calcularConceptos($opc, $opc['conceptos_contribuciones'], self::__TIPO_AMBOS, false, $sumatoriaBrutos);
    }

    /**
     * Calcula aquellos conceptos/novedades que estén asociados al empleado
     * 
     * @param array $opc Opciones globales
     * @param array $conceptos Conceptos a filtrar en el empleado
     * @param int $tipo Tipo de concepto a buscar
     * @return double
     */
    private function calcularConceptos(&$opc, $conceptos = array(), $tipo = self::__TIPO_CONCEPTO, $proratear = false, $sumatoriaBrutos = null) {
        $result = 0;
        
        $opc['diferencia_ganancias'] = 0;
		$opc['aportes'] = 0;
        $dif_gcias = 0;
		$aportes = 0;
		$montoNoSumaEnAportes = 0;
        // echo 'RESE';
        foreach ($conceptos as $concepto) {
            $opc['concepto'] = null;
            $opc['novedad'] = null;
            $opc['novedades'] = null;

			$this->setearConceptoNovedadEmpleado($opc, $concepto, $tipo);
			
            // Si los dos son nulos, el empleado no tiene asociado el concepto/novedad actual
            if (!($opc['concepto'] == null && $opc['novedades'] == null)) {
                $formula = $opc['concepto']->getFormula();
                $i = 0;
                do {
                    $opc['novedad'] = $opc['novedades'] ? $opc['novedades'][$i] : null;
                    
                    // Fix: casos excepciones que el empleado tenga fecha de egreso a mitad de mes 
                    // y que se tenga que prorratear el concepto - @gluis - 19/01/2016
                    $proratearConcepto = true;
                    if (!is_null($opc['empleado']->getFechaEgreso())) {
                        $diafechaEgreso = $opc['empleado']->getFechaEgreso()->format('d');
                        $diaUltimoMesFechaEgreso = $opc['empleado']->getFechaEgreso()->format('t');
                        if ($diafechaEgreso < $diaUltimoMesFechaEgreso) {
                            if ($concepto->getCodigo() == self::__CODIGO_CONCEPTO_ANTIGUEDAD__) {
                                    $proratearConcepto = false;
                            }
                        } 
                    }
                    
                    $this->logInfo("------------------------------------------------------------------------------");
                    $this->logInfo(($opc['novedad'] ? 'Novedad: ' : 'Concepto: ' ) . $opc['concepto'] . ' (' . $opc['concepto']->getCodigo() . ')');
                    $this->logInfo("Original : " . $formula);

                    $this->calcularParametros($opc);

                    // Acá se hace realmente el cálculo de la fórmula si todo está bien
                    $conceptoParseado = str_replace(array_keys($opc['parametros_calculados']), $opc['parametros_calculados'], $formula);
                    
                    try {
                        $resultEvalConcepto = $this->expressionLanguage->evaluate($conceptoParseado);
                    } catch (SyntaxError $e) {
						
                        $this->logError(
                                'Error al evaluar la f&oacute;rmula: ' . $conceptoParseado . ' del concepto ' . $opc['concepto'] . '\r\n' .
                                'Error PHP: ' . $e->getTraceAsString());
                        throw new \Exception(
                        'Error al evaluar el concepto: <b>' . $opc['concepto']->getCodigo() . ' - ' . $opc['concepto'] . '</b><br />
                            F&oacute;rmula err&oacute;nea original: <b>' . $formula . '</b></br>
                            F&oacute;rmula err&oacute;nea interpretada: <b>' . $conceptoParseado . '</b><br />
							Para el empleado <b>' . $opc['empleado']->getPersona()->getApellido() . ', '. $opc['empleado']->getPersona()->getNombre() . 
							' (' . $opc['empleado']->getNroLegajo() . ') - ID ' . $opc['empleado']->getId() . '<b><br/>
                            <a target="_blank" href="' . $this->controller->generateUrl('conceptos_edit', ['id' => $opc['concepto']->getId()]) . '">Revisar concepto</a>', 1);
                    }
                    
                    if ($proratearConcepto) {
                        $montoConceptoEmpleado = $proratear 
                                                    ? $this->proratearValorConcepto($opc['empleado'], $opc['fecha_cierre'], $resultEvalConcepto) 
                                                    : $resultEvalConcepto;
                    } else {
                        $montoConceptoEmpleado = $resultEvalConcepto;
                    }
                    
                    $montoConceptoEmpleado = $this->corregirValorConcepto($concepto, $montoConceptoEmpleado);

                    // Si no integra ganancias no sumar al neto de la liquidacion
                    if (!$concepto->getIntegraIg()) {
                        // Acumular la diferencia a restar
                        $dif_gcias += $montoConceptoEmpleado;
                    }
                    
                    $conceptosFullTime = array('101', '101.2', '204');
                    $idSubcategoria = $opc['empleado']->getIdSubcategoria()->getId();
                    $subCategoria = $this->em->getRepository('ADIFRecursosHumanosBundle:Subcategoria')
                            ->findAsArray($idSubcategoria);
                    
                    $esTiempoCompleto = ($subCategoria['es_tiempo_completo'] == 1) ? true : false; 
                    if (!$esTiempoCompleto && in_array($opc['concepto']->getCodigo(), $conceptosFullTime)) {
                        // Si el empleado es part time se descuenta estos conceptos 101, 101.2 y 204 como si fuera full time
                        $montoConceptoEmpleado *= 2;
                        $this->logInfo("Parseado (part time): " . $conceptoParseado . ' * 2');
                    } else {
                        $this->logInfo("Parseado : " . $conceptoParseado);
                    }
                    
                    $this->logInfo("Evaluado : " . $montoConceptoEmpleado);
                    
                    /** Detraccion tributaria mensual hasta $2400 para el calculo de contribuciones - 30/05/2018 **/                    
                    $excepcionesConceptosDetraccionTributaria = array(
                        Concepto::__CODIGO_ART_FIJA, // 205
                        Concepto::__CODIGO_SEGURO_VIDA // 207
                    );
                    
                    if (!is_null($sumatoriaBrutos) && $sumatoriaBrutos <= 2400 && $concepto->getIdTipoConcepto()->getId() == TipoConcepto::__CONTRIBUCIONES) {
                        if (!in_array($concepto->getCodigo(), $excepcionesConceptosDetraccionTributaria)) {
                            $montoConceptoEmpleado = 0;
                            $this->logInfo("Evaluado por detraccion tributaria menor o igual a $2400: " . $montoConceptoEmpleado);
                        }
                    }
                    /** Fin detraccion tributaria mensual **/
                    
                    // Seteo en la entity LiquidacionEmpleadoConcepto los montos de los conceptos calculados
                    $this->asociarLiquidacionEmpleadoConcepto($opc['liquidacion_empleado'], $concepto, $montoConceptoEmpleado, $opc['novedad'] ? $opc['novedad'] : null);

                    $result += $montoConceptoEmpleado;
					
					if ($concepto->getIdTipoConcepto()->getId() == TipoConcepto::__APORTE) {
						$aportes += $montoConceptoEmpleado;
					}
					
					/*
					* Conceptos que no se tienen en cuenta para el prorrateo del sac
					* 2.3: Ajuste Sueldo diciembre
					* 2.4: Ajuste Sueldo noviembre
					* 54: Ajuste SAC
					* 55: Grossing Up
					*/
					$conceptosNoSumanAportes = array('2.3', '2.4', '54', '55');
					if (in_array($concepto->getCodigo(), $conceptosNoSumanAportes)) {
						$opc['montoNoSumaEnAportes'] += $montoConceptoEmpleado;
					}
					
                } while ($opc['novedades'] && ++$i < $opc['novedades']->count());
            }
        }
        $opc['diferencia_ganancias'] = $dif_gcias;
		$opc['aportes'] = $aportes;
        return $result;
    }

    /**
     * Setea en $opc['concepto'] y $opc['novedad'] el concepto/novedad respectivamente buscado si es que el empleado lo tiene asociado
     * 
     * @param array $opc Opciones globales
     * @param Concepto $concepto Concepto a buscar
     * @param int $tipo Tipo de concepto a buscar
     */
    private function setearConceptoNovedadEmpleado(&$opc, $concepto, $tipo = self::__TIPO_CONCEPTO) {
        if ($tipo == self::__TIPO_NOVEDAD) {
            $conceptoAsociado = $opc['empleado']->getNovedadesCodigo($concepto->getCodigo(), $opc['fecha_ultimo_cierre'], $opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades());
        } else {
            $conceptoAsociado = $opc['empleado']->getConceptoCodigo($concepto->getCodigo());
        }

        if ($conceptoAsociado !== null) {
            if ($tipo === self::__TIPO_NOVEDAD) {
                $opc['concepto'] = $conceptoAsociado->first()->getConcepto();
                $opc['novedades'] = $conceptoAsociado;
            } else {
                $opc['concepto'] = $conceptoAsociado;
            }
        } elseif ($tipo === self::__TIPO_AMBOS) {
            // Verificar si es una novedad
            $conceptoAsociado = $opc['empleado']->getNovedadesCodigo($concepto->getCodigo(), $opc['fecha_ultimo_cierre'], $opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades());
            if ($conceptoAsociado !== null) {
                $opc['concepto'] = $conceptoAsociado->first()->getConcepto();
                $opc['novedades'] = $conceptoAsociado;
            }
        }
    }

    /**
     * get Parámetro Empleado. 
     * @param array $opc = array (
     *      'empleado' => Empleado
     *      'concepto_valor' => Valor del concepto
     *      'parametros_formula' => array de paramtros con el '#' incluido (Ej.: array([0] => '#monto_basico#'))
     *  
     * @return float Valores asociado al parámetro ingresado.
     */
    private function calcularParametros(&$opc = array()) {
        $opc['parametros_formula'] = $this->getTagsEnFormula($opc['concepto']->getFormula());

        foreach ($opc['parametros_formula'] as $parametroFormula) {
            $valorCalculado = 0;

            // Si ya fue calculado el parametro para éste empleado, se continúa.
            if (!isset($opc['parametros_calculados'][$parametroFormula]) || $parametroFormula === '#valor#') {
                switch ($parametroFormula) {
                    case '#acdt#':
                        $valorCalculado = $opc['empleado']->getAcdt();
                        break;
                    case '#anios_antiguedad#':
                        $valorCalculado = $opc['empleado']->getAniosAntiguedad($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades());
                        break;
                    case '#anios_antiguedad_indemnizacion#':
                        $valorCalculado = $opc['empleado']->getAniosAntiguedadIndemnizacion($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades());
                        break;
                    case '#basico#':
                        //$valorCalculado = $opc['empleado']->getSubcategoria()->getMontoBasico();
                        $valorCalculado = $opc['liquidacion_empleado']->getBasico();
                        break;
                    case '#bruto_1#':
                        $valorCalculado = $opc['bruto_1'];
                        break;
                    case '#bruto_2#':
                        $valorCalculado = $opc['bruto_2'];
                        break;
                    case '#cantidad_hijos_en_guarderia#':
                        $valorCalculado = $opc['empleado']->getCantidadHijosEnGuarderia();
                        break;
                    case '#devolucion_ganancia_anual#':
                        $gController = new GananciaController();
                        $gController->setContainer($this->container);
                        $valorCalculado = $gController->getDevolucionGananciaAnual($opc['empleado'], $opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y') - 1);
                        break;
                    case '#dias_del_mes#':                        
                        $valorCalculado = cal_days_in_month(CAL_GREGORIAN, $opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('n'), $opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y'));
                        if ($opc['empleado']->getFechaEgreso() != null) {
                            // Cuando es baja, los dias del mes tiene que ser hasta el dia que trabajo el empleado - @gluis - 13/12/2017
                            $valorCalculado = $opc['empleado']->getFechaEgreso()->format('d');
                        }
                        break;
                    case '#dias_habiles_mes_anterior#':
                        // Parámetro de configuración de liquidación
                        $param = $opc['parametros_liquidacion']->filter(
                            function($entry) {
                                return in_array($entry->getNombre(), array('#dias_habiles_mes_anterior#'));
                            }
                        );

                        if (!$param) {
                            $this->logError('No existe el par&aacute;metro de liquidaci&oacute;n #dias_habiles_mes_anterior# en la configuraci&oacute;n.');
                            $valorCalculado = -1;
                        } else {
                            $valorCalculado = $param[0]->getValor();
                        }
                        break;
                    case '#dias_liquidados_01_06#':
                        // Cantidad de dias trabajados en el primer semestre
                        $dias_trabajados = $opc['empleado']->getDiasTrabajadosSemestre(new \DateTime($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y') . '-01-01'), new \DateTime($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y') . '-06-30'));
                        $dias_licencia = $this->getDiasLicencia($opc['empleado'], new \DateTime($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y') . '-01-01'), new \DateTime($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y') . '-06-30'));
                        $dias_licencia_sin_liquidar = $this->getDiasLicenciaSinLiquidar($opc['empleado'], new \DateTime($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y') . '-06-01'), new \DateTime($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y') . '-06-30'));
                        $this->logInfo('Dias trabajados: ' . $dias_trabajados);
                        $this->logInfo('Dias licencia: ' . $dias_licencia);
                        $this->logInfo('Dias licencia sin liquidar: ' . $dias_licencia_sin_liquidar);
                        $valorCalculado = $dias_trabajados - $dias_licencia - $dias_licencia_sin_liquidar;
                        break;
                    case '#dias_liquidados_07_12#':
                        // Cantidad de dias trabajados en el segundo semestre
                        $dias_trabajados = $opc['empleado']->getDiasTrabajadosSemestre(new \DateTime($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y') . '-07-01'), new \DateTime($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y') . '-12-31'));
                        $dias_licencia = $this->getDiasLicencia($opc['empleado'], new \DateTime($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y') . '-07-01'), new \DateTime($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y') . '-12-31'));
                        $dias_licencia_sin_liquidar = $this->getDiasLicenciaSinLiquidar($opc['empleado'], new \DateTime($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y') . '-12-01'), new \DateTime($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()->format('Y') . '-12-31'));
                        $this->logInfo('Dias trabajados: ' . $dias_trabajados);
                        $this->logInfo('Dias licencia: ' . $dias_licencia);
                        $this->logInfo('Dias licencia sin liquidar: ' . $dias_licencia_sin_liquidar);
                        $valorCalculado = $dias_trabajados - $dias_licencia - $dias_licencia_sin_liquidar;
                        break;
                    case '#dias_totales_01_06#':
                        // Cantidad de dias del mes primer semestre
                        $valorCalculado = date('L', mktime(1, 1, 1, 1, 1, date('Y'))) ? 182 : 181;
                        break;
                    case '#dias_totales_07_12#':
                        // Cantidad de dias del mes segundo semestre
                        $valorCalculado = 184;
                        break;
                    case '#familiares_a_cargo_os#':
                        $valorCalculado = $opc['empleado']->getFamiliaresACargoOS();
                        break;
                    case '#mejor_remuneracion_habitual#':
                        // Mejor BRUTO1 de los ultimos 12 meses mas el promedio del BRUTO2 de los ultimos 12 meses
                        $valorCalculado = $this->em
                                ->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
                                ->mejorRemuneracionHabitualByEmpleado($opc['empleado']->getId());
                        break;
                    case '#mejor_remunerativo_01_06#':
                        // Mejor (BRUTO1 + BRUTO2) en los meses del 01 al 06 del año en curso.
                        $valorCalculado = $this->em
                                ->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
                                ->mejorBrutoByEmpleadoAndPeriodo($opc['empleado']->getId(), 1, 6, date('Y'));
                        
                        if (!$opc['empleado']->tieneLiquidacionCerrada()) {
                            // Si el empleado no tiene todavia una liquidacion hecha, se tiene que hacer la validacion 
                            // si el valor calculado es mas chico que el basico de la subcategoria
                            $basicoEmpleado = $opc['empleado']->getSubcategoria()->getMontoBasico();
                            $valorCalculado = $valorCalculado ? ($valorCalculado < $basicoEmpleado ? $basicoEmpleado : $valorCalculado) : $basicoEmpleado;
                        }
                        break;
                    case '#mejor_remunerativo_07_12#':
                        // Mejor (BRUTO1 + BRUTO2) en los meses del 07 al 12. Si la liquidación se hace en enero, el período es del año anterior, no en curso.
                        $valorCalculado = $this->em
                                ->getRepository('ADIFRecursosHumanosBundle:Liquidacion')
                                ->mejorBrutoByEmpleadoAndPeriodo($opc['empleado']->getId(), 7, 12, date('n') == 1 ? date('Y') - 1 : date('Y'));
                        
                        if (!$opc['empleado']->tieneLiquidacionCerrada()) {
                            // Si el empleado no tiene todavia una liquidacion hecha, se tiene que hacer la validacion 
                            // si el valor calculado es mas chico que el basico de la subcategoria
                            $basicoEmpleado = $opc['empleado']->getSubcategoria()->getMontoBasico();
                            $valorCalculado = $valorCalculado ? ($valorCalculado < $basicoEmpleado ? $basicoEmpleado : $valorCalculado) : $basicoEmpleado;
                        }
                        break;
                    case '#monto_remunerativo_con_tope#':
                        $valorCalculado = $opc['monto_remunerativo_con_tope'];
                        break;
                    case '#multiplicador_antiguedad_indemnizacion_sustitutiva#':
                        // 1 Si los años de anitigüedad <= 5 y 2 si > 5
                        $valorCalculado = $opc['empleado']->getAniosAntiguedad($opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades()) <= 5 ? 1 : 2;
                        break;
                    case '#retenciones_ganancias#':
                        try {
                            $gController = new GananciaController();
                            $gController->setContainer($this->container);
                            //$novedad_ajuste_ganancias_sac = $opc['empleado']->getNovedadCodigo(Concepto::__CODIGO_AJUSTE_GANANCIAS_SAC, $opc['fecha_ultimo_cierre'], $opc['liquidacion_empleado']->getLiquidacion()->getFechaCierreNovedades());
                            // La variable $novedad_ajuste_ganancias_sac la dejo en null para que acumule siempre, 
                            // ya que el concepto 994, se esta usando para otro proposito ahora.
                            $novedad_ajuste_ganancias_sac = null; 
                            $valorCalculado = $gController->calculoGanancias($opc['liquidacion_empleado'], $novedad_ajuste_ganancias_sac == null);
                        } catch (\Exception $e) {
                            $this->logError(
                                    'Error al calcular ganancias. Concepto ' . $opc['concepto'] . '\r\n' .
                                    'Error PHP: ' . $e->getTraceAsString());
                            throw new \Exception(
								'Error al evaluar el concepto: <b>' . $opc['concepto']->getCodigo() . ' - ' . $opc['concepto'] . '</b><br />' . 
								'Error: <b>' . $e->getMessage() . '</b><br />' .  
								'Error para el empleado ' . $opc['empleado']->getPersona()->getApellido() . ', ' . 
								$opc['empleado']->getPersona()->getNombre() . ' (Legajo: ' . $opc['empleado']->getNroLegajo() . ') <br />
                                <a target="_blank" href="' . $this->controller->generateUrl('conceptos_edit', ['id' => $opc['concepto']->getId()]) . '">Revisar concepto</a>', 1);
                            return false;
                        }
                        break;
                    case '#total_no_remunerativo#':
                        $valorCalculado = $opc['monto_no_remunerativos'];
                        break;
                    case '#total_no_remunerativo_indemnizatorio#':
                        $valorCalculado = $this->getMontoNoRemunerativosIndemnizatorios($opc['liquidacion_empleado']);
                        break;
                    case '#valor#':
                        // Si es una novedad se saca de su valor. Sino buscarlo en el concepto.
                        $valorCalculadoTemp = isset($opc['novedad']) ? $opc['novedad']->getValor() : $opc['concepto']->getValor();
                        $valorCalculado = $opc['concepto']->getEsPorcentaje() ? $valorCalculadoTemp / 100 : $valorCalculadoTemp;
                        break;
                    default:
                        // Puede que el campo que venga sea una referencia a una valor de un concepto ya calculado. Ej.: #concepto_23# + #valor#
						// var_dump( preg_match("/^#concepto_(\d.*)#/", $parametroFormula), $parametroFormula ); exit;
                        if (preg_match("/^#concepto_(\d.*)#/", $parametroFormula)) {
                            // Busco el valor evaluado del concepto en la coleccion de conceptos ya evaluados para el empleado
                            $codigoConcepto = explode('_', str_replace('#', '', $parametroFormula))[1];
                            $conceptoEvaluado = $opc['liquidacion_empleado']->getConceptoCodigo($codigoConcepto);
                            if ($conceptoEvaluado) {
                                $valorCalculado = $conceptoEvaluado->getMonto();
                            } else {
                                $this->logError('No existe el concepto referenciado ' . $parametroFormula . ' en los c&aacute;lculos anteriores.');
                            }
                        } else {
                            $this->logError('No existe el par&aacute;metro ' . $parametroFormula . ' en la configuraci&oacute;n.');
                        }
                        break;
                }

                $opc['parametros_calculados'][$parametroFormula] = $valorCalculado;
            }
        }
    }

    /**
     * Get tags de fórmula. Parsea, busca y retorna los valores asociados a los parámetros ingresados.
     * 
     * @param string $formula
     * @return array Tags encontrados en $formula
     */
    private function getTagsEnFormula($formula) {
        // Saco los parametros #....#
        $parametrosFormula = array();
        preg_match_all('/\#([\w|\.])+\#/i', $formula, $parametrosFormula, PREG_PATTERN_ORDER);

        // En parametrosFormula se enecuentran en orden de aparición los parametros #....#
        return $parametrosFormula[0];
    }

    /**
     * Asocia un concepto calculado a un empleado de la liquidación
     * 
     * @param LiquidacionEmpleado $liquidacionEmpleado
     * @param Concepto $concepto
     * @param double $montoConceptoEmpleado
     * @param EmpleadoNovedad $empleadoNovedad
     */
    private function asociarLiquidacionEmpleadoConcepto(LiquidacionEmpleado $liquidacionEmpleado, $concepto, $montoConceptoEmpleado, $empleadoNovedad = null) {
        // Crear liquidacionEmpleadoConcepto
        $liquidacionEmpleadoConcepto = new LiquidacionEmpleadoConcepto();
        $liquidacionEmpleadoConcepto
                ->setLiquidacionEmpleado($liquidacionEmpleado)
                ->setConceptoVersion($concepto->getUltimaVersion())
                // No formatear al guardar porque despues se usa, redondear
                ->setMonto(round($montoConceptoEmpleado, 2));

        if ($empleadoNovedad) {
            $liquidacionEmpleadoConcepto->setEmpleadoNovedad($empleadoNovedad);
        }

        $liquidacionEmpleado->getLiquidacionEmpleadoConceptos()->add($liquidacionEmpleadoConcepto);
    }

    /**
     * Retorna la fecha de cierre de la última liquidación
     * @return \DateTime 
     */
    private function getFechaUltimoCierre() {
        $ultimoCierre = $this->em->createQuery('SELECT MAX(l.fechaCierreNovedades) AS ultimo_cierre FROM ADIFRecursosHumanosBundle:Liquidacion l WHERE l.tipoLiquidacion = ' . TipoLiquidacion::__HABITUAL)->getSingleScalarResult();
        if ($ultimoCierre) {
            $ultimoCierreDate = new DateTime($ultimoCierre);
            return $ultimoCierreDate->add(new DateInterval('P1D'));
        }
        return new DateTime('2014-01-01');
    }

    /**
     * Devuelve el valor del concepto actual prorateado en base a las fechas de ingreso/egreso del empleado
     * @param Empleado $empleado
     * @param DateTime $fechaCierreNovedades
     * @param float $valorConcepto
     * @return float
     */
    private function proratearValorConcepto(Empleado $empleado, DateTime $fechaCierreNovedades, $valorConcepto = 0) {
        $diasTrabajadosEmpleado = $empleado->getDiasTrabajados($fechaCierreNovedades);
        return $valorConcepto * $diasTrabajadosEmpleado / $fechaCierreNovedades->format('t');
    }

    /**
     * Método para corregir el valor excepcional de algunos conceptos
     * @param Concepto $concepto
     * @param float $valorConcepto
     * @return float
     */
    private function corregirValorConcepto(Concepto $concepto, $valorConcepto = 0) {
        // Para el caso del concepto 206, si el valor es negativo, devuelve 0
        if ($concepto->getCodigo() == '206') {
            return $valorConcepto < 0 ? 0 : $valorConcepto;
        }
        return $valorConcepto;
    }

    /**
     * Método para validar si un empleado debe liquidarse de acuerdo a las fechas de contrato
     * @param Empleado $empleado
     * @param DateTime $fechaCierreNovedades
     * @return boolean
     */
    private function validarEmpleado(Empleado $empleado, DateTime $fechaCierreNovedades) {
        $fechaIngreso = $empleado->getFechaIngreso();
        return $fechaIngreso <= new DateTime(date($fechaCierreNovedades->format('Y-m-t')));
    }

    private function logInfo($text = "") {
        //if ($this->container->getParameter('kernel.environment') !== 'dev') {
        // $this->logger->info(date('d/m/Y H:i:s').' -- '.$text);
        $this->logger->info($text);
        //}
    }

    private function logError($text = "") {
        //if ($this->container->getParameter('kernel.environment') !== 'dev') {
        $this->logger->error($text);
        //}
    }

    /**
     * Retorna la cantidad de días de licencia en el semestre
     * Tiene en cuenta la fecha de ingreso y egreso del empleado
     * @return integer
     */
    public function getDiasLicencia($empleado, $fechaInicio, $fechaFin) {
		$codigosNoSumanLicencias = array(
			'76.1', // Ajuste Ausencia sin Justificación
			'73', // Ajuste Licencia por Maternidad
			'71' // Ajuste Licencia sin Goce de Haberes
		);
        $conceptos_licencia = $this->em->getRepository('ADIFRecursosHumanosBundle:Concepto')
                ->createQueryBuilder('c')
                ->select('c.id')
                ->innerJoin('c.conceptoLicenciaSAC', 'cls')
				->where('c.codigo NOT IN (:codigosNoSumanLicencias)')
				->setParameter('codigosNoSumanLicencias', $codigosNoSumanLicencias)
                ->getQuery()
                ->getScalarResult();
        $ids_conceptos_licencia = array_map('current', $conceptos_licencia);

        $dias_licencia = $this->em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleadoConcepto')
                ->createQueryBuilder('lec')
                ->select('SUM(IFNULL(en.dias, en.valor))')
                ->innerJoin('lec.empleadoNovedad', 'en')
                ->innerJoin('lec.liquidacionEmpleado', 'le')
                ->innerJoin('le.empleado', 'e')
                ->innerJoin('le.liquidacion', 'l')
                ->where('e.id = (:idEmpleado)')
                ->andWhere('l.fechaCierreNovedades BETWEEN (:fechaDesde) AND (:fechaHasta)')
                ->andWhere('en.idConcepto IN (:idConceptosLicencia)')
                ->andWhere('en.activo = 1')
                ->setParameters(array('idEmpleado' => $empleado->getId(), 'fechaDesde' => $fechaInicio, 'fechaHasta' => $fechaFin, 'idConceptosLicencia' => $ids_conceptos_licencia))
                ->getQuery()
                ->getSingleScalarResult();
		
        return $dias_licencia ? $dias_licencia * (-1) : 0;
    }

    /**
     * Retorna la cantidad de días de licencia sin liquidar para el cálculo de sac
     * Tiene en cuenta la fecha de ingreso y egreso del empleado
     * @return integer
     */
    public function getDiasLicenciaSinLiquidar($empleado, $fechaInicio, $fechaFin) {
        $conceptos_licencia = $this->em->getRepository('ADIFRecursosHumanosBundle:Concepto')
                ->createQueryBuilder('c')
                ->select('c.id')
                ->innerJoin('c.conceptoLicenciaSAC', 'cls')
                ->getQuery()
                ->getScalarResult();
        $ids_conceptos_licencia = array_map('current', $conceptos_licencia);

        $dias_licencia = $this->em->getRepository('ADIFRecursosHumanosBundle:EmpleadoNovedad')
                ->createQueryBuilder('en')
                ->select('SUM(en.valor)')
                ->innerJoin('en.idEmpleado', 'e')
                ->where('e.id = (:idEmpleado)')
                ->andWhere('en.fechaAlta BETWEEN (:fechaDesde) AND (:fechaHasta)')
                ->andWhere('en.idConcepto IN (:idConceptosLicencia)')
                ->andWhere('en.fechaBaja IS NULL')
                ->setParameters(array('idEmpleado' => $empleado->getId(), 'fechaDesde' => $fechaInicio, 'fechaHasta' => $fechaFin, 'idConceptosLicencia' => $ids_conceptos_licencia))
                ->getQuery()
                ->getSingleScalarResult();

        return $dias_licencia ? $dias_licencia * (-1) : 0;
    }

    /**
     * Calcula el monto no remunerativo indemnizatorios
     * @param LiquidacionEmpleado $liquidacionEmpleado
     * @return double no remunerativo
     */
    private function getMontoNoRemunerativosIndemnizatorios(LiquidacionEmpleado $liquidacionEmpleado) {
        $total = 0;
        foreach ($liquidacionEmpleado->getLiquidacionEmpleadoConceptos() as $liquidacionEmpleadoConcepto) {
            /* @var $liquidacionEmpleadoConcepto LiquidacionEmpleadoConcepto */
            $conceptoVersion = $liquidacionEmpleadoConcepto->getConceptoVersion();
            if ($conceptoVersion->getIdTipoConcepto() == TipoConcepto::__NO_REMUNERATIVO &&
                    $conceptoVersion->getEsIndemnizatorio() == true) {
                $total += $liquidacionEmpleadoConcepto->getMonto();
            }
        }
        return $total;
    }
    
    public function getDiasCalculadosSAC() {
        $empleados = $this->em->getRepository('ADIFRecursosHumanosBundle:Empleado')
                ->createQueryBuilder('e')
                ->innerJoin('e.persona', 'p')
                ->orderBy('p.apellido, p.nombre')
				->where('e.activo = :activo')->setParameter('activo', 1)
				->orderBy('p.apellido, p.nombre')
                ->getQuery()
                ->getResult();
                
        $html = '<table><thead>'
                . '<tr>'
                . '<th>Legajo</th>'
                . '<th>CUIL</th>'
                . '<th>Empleado</th>'
                . '<th>D&iacute;as para el c&aacute;lculo</th>'
                . '</tr>'
                . '</thead><tbody>';

        /* @var $empleado Empleado */
        foreach ($empleados as $empleado) {
            $dias_trabajados = $empleado->getDiasTrabajadosSemestre(new \DateTime('2015-07-01'), new \DateTime('2015-12-31'));
            $dias_licencia = $this->getDiasLicencia($empleado, new \DateTime('2015-07-01'), new \DateTime('2015-12-31'));
            $dias_licencia_sin_liquidar = $this->getDiasLicenciaSinLiquidar($empleado, new \DateTime('2015-12-01'), new \DateTime('2015-12-31'));
            $valorCalculado = $dias_trabajados - $dias_licencia - $dias_licencia_sin_liquidar;            
            $html .= '<tr>';
            $html .= '<td>' . $empleado->getNroLegajo() . '</td>';
            $html .= '<td>' . $empleado->getPersona()->getCuil() . '</td>';
            $html .= '<td>' . $empleado->getPersona()->__toString() . '</td>';
            $html .= '<td>' . $valorCalculado . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        return $html;
        
    }

}
