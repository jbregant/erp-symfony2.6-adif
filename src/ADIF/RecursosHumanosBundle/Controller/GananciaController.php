<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ADIF\RecursosHumanosBundle\Entity\ConceptoGananciaCalculado;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use ADIF\RecursosHumanosBundle\Entity\GananciaEmpleado;
use ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use mPDF;
use DateTime;
use ADIF\BaseBundle\Entity\AdifApi;
use Symfony\Component\HttpFoundation\Response;
use ADIF\RecursosHumanosBundle\Entity\TipoLiquidacion;
use ADIF\RecursosHumanosBundle\Entity\Concepto;
use ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia;
use ADIF\RecursosHumanosBundle\Entity\TipoConceptoGanancia;
use Symfony\Component\HttpFoundation\Request;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Symfony\Bridge\Monolog\Logger;
use ADIF\RecursosHumanosBundle\Entity\Empleado;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * GananciaController
 * 
 * @author Manuel Becerra
 * @author Nahuel Sangiacomo
 * created 21/07/2014
 * @Security("has_role('ROLE_RRHH_LIQUIDACION')")
 * @Route("/ganancia")
 */
class GananciaController extends BaseController {

    private $logger;
	
	private $conceptosGananciasDeprecadosIds = array(
		59 // SAC primer semestre
	);

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado $liquidacionEmpleado
     * @return int
     */
    public function calculoGanancias(LiquidacionEmpleado $liquidacionEmpleado, $acumula = true) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $this->logger = new Logger('ganancia');

        $monologFormat = "%message%\n";
        $dateFormat = "Y/m/d H:i:s";
        $monologLineFormat = new LineFormatter($monologFormat, $dateFormat);

        $streamHandler = new StreamHandler($this->get('kernel')->getRootDir() . '/logs/ganancia_' . date('Y_m_d__H_i_s') . '.log', Logger::INFO);
        $streamHandler->setFormatter($monologLineFormat);

        $this->logger->pushHandler($streamHandler);

        // EL mes no puede superar el mes actual en el cual se está calculando 
        // y en el caso que la Liquidacion contemple un rango,
        // debe ser el mes de la "fechaFin" de la Liquidacion
        $mes = $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('n');
        $anio = $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Y');
        $fechaCierreLiquidacionAnterior = $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades();
        $esSAC = ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() === TipoLiquidacion::__SAC);

        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("Cálculo de Ganancias del mes " . $mes . " año " . $anio);

        $gananciaEmpleado = new GananciaEmpleado();

        $empleado = $liquidacionEmpleado->getEmpleado();

        $gananciaEmpleado->setLiquidacionEmpleado($liquidacionEmpleado);

        $impuestoRetenidoAnual = 0;
        $haberNetoAcumuladoAnterior = 0;
		$noRemunerativo = 0;
		$liquidacionEmpleadoAnterior = null;
		$liquidacionEmpleadoAnteriorAdicional = null;
        
        $f572 = $empleado->getFormulario572($anio);
        $rangoRemuneracion = $empleado->getRangoRemuneracion();

		$liquidacionEmpleadoAnterior = $this
                    ->getLiquidacionEmpleadoAnterior($empleado, $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades(), 
						($liquidacionEmpleado->getGananciaEmpleado() != null) ? $liquidacionEmpleado->getId() : null);

          // Si NO es Enero
        if ($mes != 1) {
            //cambio para calcular retroactivos
			
            $f649 = $empleado->getFormulario649();

            // Existe liquidacion anterior
            if (null != $liquidacionEmpleadoAnterior && $liquidacionEmpleadoAnterior->getLiquidacion()->getFechaCierreNovedades()->format('Y') == $anio) {
                // Si se calculó ganancia correctamente
                if (null != $liquidacionEmpleadoAnterior->getGananciaEmpleado()) {
                    // ****** Impuesto Retenido Anual ******** \\
                    $impuestoRetenidoAnual = $liquidacionEmpleadoAnterior->getGananciaEmpleado()->getImpuestoRetenidoAnual();
                    // ****** Haber neto acumulado ******** \\
                    if (($liquidacionEmpleadoAnterior->getLiquidacion()->getTipoLiquidacion()->getId() === TipoLiquidacion::__SAC) && ($liquidacionEmpleadoAnterior->getGananciaEmpleado()->getTotalImpuesto() != $liquidacionEmpleadoAnterior->getGananciaEmpleado()->getImpuestoRetenidoAnual())) {
                        $haberNetoAcumuladoAnterior = $liquidacionEmpleadoAnterior->getGananciaEmpleado()->getHaberNetoAcumulado() - $liquidacionEmpleadoAnterior->getGananciaEmpleado()->getHaberNeto();
                    } else {
                        $haberNetoAcumuladoAnterior = $liquidacionEmpleadoAnterior->getGananciaEmpleado()->getHaberNetoAcumulado();
                    }
                    $fechaCierreLiquidacionAnterior = $liquidacionEmpleadoAnterior->getLiquidacion()->getFechaCierreNovedades();
                    //si no se aplicó aÃºn el f649, se toman sus datos
                    if ((null != $f649) && (($f649->getFechaFormulario() > $fechaCierreLiquidacionAnterior) && ($f649->getFechaFormulario() < $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()))) {
                        $impuestoRetenidoAnual += $f649->getTotalImpuestoDeterminado();
                        $haberNetoAcumuladoAnterior += $f649->getGananciaAcumulada();
                    }
                }
            } else if ((null != $f649) and ( ($f649->getFechaFormulario()->format('Y') == $anio) && ($f649->getFechaFormulario() < $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()))) {
                $impuestoRetenidoAnual = $f649->getTotalImpuestoDeterminado();
                $haberNetoAcumuladoAnterior = $f649->getGananciaAcumulada();
            }
            
			// Me fijo si la liquidacion anterior es una adicional de una suma no remunerativa
			$liquidacionEmpleadoAnteriorAdicional = $this->getLiquidacionEmpleadoAnteriorAdicional($empleado, $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades(), 
						($liquidacionEmpleado->getGananciaEmpleado() != null) ? $liquidacionEmpleado->getId() : null);
			
/*			
			if ($liquidacionEmpleadoAnteriorAdicional != null) {
				$noRemunerativo = $liquidacionEmpleadoAnteriorAdicional->getNoRemunerativo();
				$noRemunerativo = ($noRemunerativo == null) ? 0 : $noRemunerativo;
				$haberNetoAcumuladoAnterior += $noRemunerativo;
			}
*/			
			
        } else {
			// Si es enero....
            $fechaCierreLiquidacionAnterior = new DateTime($anio . '-01-01');
			
			// Me fijo si hubo una liquidacion previa a la liquidacion de enero excepcional como puede ser un pago de un bono o similar (suma no remunerativa)
			if ($liquidacionEmpleadoAnterior != null) {
				$haberNetoAcumuladoAnteriorEnero = $liquidacionEmpleadoAnterior->getGananciaEmpleado()->getHaberNetoAcumulado();
				$noRemunerativo = $liquidacionEmpleadoAnterior->getNoRemunerativo();
				if ($haberNetoAcumuladoAnteriorEnero == $noRemunerativo) {
					$haberNetoAcumuladoAnterior = $noRemunerativo;
				}
			}
        }
        		
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("Empleado: " . $empleado);


        // ****** HABER NETO ******** \\

        //$haberNeto = round($liquidacionEmpleado->getHaberNetoGanancia(), 2);
		// Lo hago para que me prorratee la base imponible del SAC en 7, revisar si para los meses 
		// posteriores a la liquidacion, corresponde esta cuota. - @gluis - 28/06/2016
//		if ($empleado->getFechaEgreso() == null) {
//			$haberNeto = round($liquidacionEmpleado->getHaberNetoGanancia(), 2);
//        } else {
//            $haberNeto = round($liquidacionEmpleado->getHaberNetoGanancia(), 2);
//        }
        
        $haberNeto = round($liquidacionEmpleado->getHaberNetoGanancia(), 2);
        $this->logger->info("Haber Neto SIN sumar otros ingresos: " . $haberNeto);
        $this->logger->info("------------------------------------------------------------------------------");
		
		
        // ****** SUMO al HABER NETO los OTROS INGRESOS ******** \\
        $montoOtrosIngresos = $this->calcularOtrosIngresos($gananciaEmpleado, $mes);

        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("Monto total a sumar por Otros Ingresos: " . $montoOtrosIngresos);

        $haberNeto += $montoOtrosIngresos;
		
		/****** PRORRATEO DEL SAC ******/
		$prorrateoSac = round($liquidacionEmpleado->getProrrateoSac(), 2);
		$this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("Monto total a sumar por prorrateo del SAC: " . $prorrateoSac);
		
		/**
		* Doy de alta el concepto ganancia calculado, para que aparezca en el excel de IG
		* Concepto ganancia: Prorrateo SAC
		* Tipo de concepto: Otros Ingresos
		* Orden de aplicacion: 0
		* Codigo 572: e_6
		*/
		$conceptoGananciaProrrateoSac = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findOneBy(
			array('codigo572' => ConceptoGanancia::__CODIGO_PRORRATEO_SAC)
		);
		
		$conceptoGananciaCalculado = new ConceptoGananciaCalculado();
		$conceptoGananciaCalculado->setConceptoGanancia($conceptoGananciaProrrateoSac);
                            //->setMonto($prorrateoSac);
        

		if ( ($empleado->getFechaEgreso() != null && $mes != 1 && $mes != 7) || $esSAC || $mes == 12 || ($mes == 6 && $anio == 2018) ) {
			// Si es baja/SAC, tengo que restar la sumatoria de prorrateo de sac de todo el año
			$semestre = 0;
			if ($mes > 0 && $mes < 7) { 
				// Estamos en el primer semestre
				$semestre = 1;
			} else {
				// Estamos en el segundo semestre
				$semestre = 2;
			}
			
			if ($mes == 6 && $anio == 2018) {
				// Fix por 1 SAC 2018 - 01/07/2018
				$semestre = 1;
			}
			
			$sumaProrateoSac = $em
								->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
								->getSumaProrrateoSac($empleado, $anio, $semestre);
            					
			$this->logger->info("------------------------------------------------------------------------------");
			$this->logger->info("Se resta la suma del semestre $semestre de prorrateo de SAC por baja y/o liquidacion SAC: " . $sumaProrateoSac);
			
			//$haberNeto -= $sumaProrateoSac;
			$prorrateoSac = $sumaProrateoSac * -1;
		}
		
		$conceptoGananciaCalculado->setMonto($prorrateoSac);
		
		$gananciaEmpleado->addConcepto($conceptoGananciaCalculado);
		
		$haberNeto += $prorrateoSac;
		/****** FIN PRORRATEO DEL SAC ******/
        
        $gananciaEmpleado->setHaberNeto($haberNeto);

		if ($liquidacionEmpleadoAnteriorAdicional != null) {
			$this->logger->info("------------------------------------------------------------------------------");
			$this->logger->info("La liquidacion anterior fue una adicional");
			$this->logger->info("No remunerativo que se suma al haber neto acumulado anterior: " . $noRemunerativo);
			$this->logger->info("------------------------------------------------------------------------------");
		}
		
		
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("Haber Neto de la Liquidación: " . $haberNeto);
        $this->logger->info("Haber Neto Acumulado hasta el mes anterior: " . $haberNetoAcumuladoAnterior);
        $this->logger->info("------------------------------------------------------------------------------");
		
        $saldoImpuestoMes = 0;

        if (null != $rangoRemuneracion) {
			
			if (!$rangoRemuneracion->getVigente()) {
				$mensaje = "El rango de remuneraci&oacute;n \"$rangoRemuneracion\" no es un rango vigente. ";
				$mensaje .= "Por favor seleccione un rango vigente para el empleado.";
				throw new \Exception($mensaje);
			}

            $liquidacionEmpleado->setGananciaEmpleado($gananciaEmpleado);

            // ****** RESULTADO NETO ******** \\

            $resultadoNeto = $haberNeto + $haberNetoAcumuladoAnterior;
            // Se le restan las deducciones
            $resultadoNeto -= $this->sumarConceptos($gananciaEmpleado, $mes, $rangoRemuneracion, 1);

            $gananciaEmpleado->setResultadoNeto($resultadoNeto);

            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("Resultado Neto: " . $resultadoNeto);

            // ****** DIFERENCIA ******** \\

            $diferencia = $resultadoNeto;
            $diferencia -= $this->sumarConceptos($gananciaEmpleado, $mes, $rangoRemuneracion, 2, $resultadoNeto);

            $gananciaEmpleado->setDiferencia($diferencia);

            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("Diferencia: " . $diferencia);


            // ****** Conceptos que NO están en el F. 572 ******** \\

            $tipoConceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:TipoConceptoGanancia')->
                    findOneBy(array('ordenAplicacion' => 3));

            $conceptosNoAplicanFormulario572 = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findBy(
                    array(
                'tipoConceptoGanancia' => $tipoConceptoGanancia, //
                'aplicaEnFormulario572' => false), //
                    array('ordenAplicacion' => 'ASC'))
            ;

            $this->logger->info("------------------------------------------------------------------------------");

            $totalDeducciones = 0;

			// sac toma tabla noviembre para deducciones personales
			$mesDeduccionesPersonales = (($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() === TipoLiquidacion::__SAC) && ($mes == 12)) ? 11 : $mes;
			
            // Por cada ConceptoGanancia que NO aplica al F. 572
            foreach ($conceptosNoAplicanFormulario572 as $conceptoGanancia) {

                $this->logger->info("Concepto: " . $conceptoGanancia);

                $topeConceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')->
                        findOneBy(
                        array(
                            'rangoRemuneracion' => $rangoRemuneracion,
                            'conceptoGanancia' => $conceptoGanancia,
							'vigente' => 1
                        )
                );

                // Si el ConceptoGanancia tiene asociado un Tope
                if (null != $topeConceptoGanancia) {
					
					// Aca entran los topes comunes para todos como: "Deducción especial" y "MÃ­nimo no imponible"
					
					$this->logger->info("ID Tope = " . $topeConceptoGanancia->getId());
					$this->logger->info("Tope (comun a todos) total = " . $topeConceptoGanancia->getValorTope());

                    //si el empleado esta dado de baja se toma la tabla de diciembre
                    if (($gananciaEmpleado->getLiquidacionEmpleado()->getEmpleado()->getFechaEgreso() !== null) && ($gananciaEmpleado->getLiquidacionEmpleado()->getEmpleado()->getAplicaEscalaDiciembre())) {
                        $mesDeduccionesPersonales = 12;
                    }

                    $montoARestar = (($topeConceptoGanancia->getEsValorAnual()) ? $topeConceptoGanancia->getValorTope() / 12 : $topeConceptoGanancia->getValorTope()) * $mesDeduccionesPersonales;

					
                    $this->logger->info("Monto Restado: $" . $montoARestar);
                    $totalDeducciones += $montoARestar;

                    $conceptoGananciaCalculado = new ConceptoGananciaCalculado();
                    $conceptoGananciaCalculado
                            ->setConceptoGanancia($conceptoGanancia)
                            ->setMonto($montoARestar);

                    $gananciaEmpleado->addConcepto($conceptoGananciaCalculado);
					$this->logger->info("---");
                }
            }
            // FIN foreach.   


            /* ****** TOTAL DEDUCCIONES ******** */

            $totalDeducciones += $this->sumarConceptos($gananciaEmpleado, $mesDeduccionesPersonales, $rangoRemuneracion, 3);
            $gananciaSujetaImpuesto = $diferencia - $totalDeducciones;

            /**  Si NO aplica ganancias */
            if (!$rangoRemuneracion->getAplicaGanancias()) {

                $deduccionEspecial = $gananciaEmpleado->getConceptos()->filter(
                        function($entry) {
                    return in_array($entry->getConceptoGanancia()->getCodigo572(), array(ConceptoGanancia::__CODIGO_DEDUCCION_ESPECIAL));
                });

                $deduccionEspecial->first()->setMonto($gananciaSujetaImpuesto);
                $totalDeducciones += $gananciaSujetaImpuesto;
                $gananciaSujetaImpuesto = 0;
            }

            $gananciaEmpleado->setTotalDeducciones($totalDeducciones);

            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("Total Deducciones: $" . $totalDeducciones);

            // ****** Ganancia Sujeta a Impuesto ******** \\

            $gananciaEmpleado->setGananciaSujetaImpuesto($gananciaSujetaImpuesto);

            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("Ganancia Sujeta a Impuesto: $" . $gananciaSujetaImpuesto);

			if ($empleado->getFechaEgreso() != null) {
				// Si se da el caso de baja, tien que ir a buscar a la tabla de escala_impuesto por el mes de diciembre
				$mes = 12;
				$this->logger->info("Va a tomar la escala del impuesto del mes de diciembre (12) - Por baja del empleado");
			}
			
			/** Me fijo si el empleado tiene conceptos que no modifican la escala **/
			$conceptosNoCambianEscala = $gananciaEmpleado->getLiquidacionEmpleado()->getLiquidacionEmpleadoConceptos()->filter(
				function($entry) {
					return in_array(
						!$entry->getConceptoVersion()->getCambiaEscalaImpuesto() && $entry->getConceptoVersion()->getIntegraIg()
						,array(true)
					);
			});
			
			$montoConceptosNoCambianEscala = 0;
			if ($conceptosNoCambianEscala != null && !empty($conceptosNoCambianEscala)) {
				foreach($conceptosNoCambianEscala as $concepto) {
					$montoConceptosNoCambianEscala += $concepto->getMonto();
				}
			}
			
			// Si tiene monto, le resto, para que no me salte de escala de categoria a un empleado que tenga estos conceptos
//			if ($montoConceptosNoCambianEscala > 0) {
//				$this->logger->info("Monto de conceptos que tiene que restar para que no modifique la escala: $" . $montoConceptosNoCambianEscala);
//				$gananciaSujetaImpuesto -= $montoConceptosNoCambianEscala;
//				$this->logger->info("Ganancia Sujeta a Impuesto restado el monto que no modifican escala: $" . $gananciaSujetaImpuesto);
//			}
			
			/** ACUMULO CONCEPTOS QUE NO CAMBIAN LA ESCALA **/
			/** 27-03-2017 **/
			
			$acumuladoConceptosNoCambianEscala = $gananciaEmpleado->getAcumuladoConceptosNoCambianEscala();
			$acumuladoConceptosNoCambianEscala = ($acumuladoConceptosNoCambianEscala != null) ? $acumuladoConceptosNoCambianEscala : 0;
			$this->logger->info("Monto de conceptos que tiene que restar para que no modifique la escala (este mes): $" . $montoConceptosNoCambianEscala);
			$this->logger->info("Acumulado de conceptos de meses anteriores, que tiene que restar para que no modifique la escala: $" . $acumuladoConceptosNoCambianEscala);
			$acumuladoConceptosNoCambianEscala += $montoConceptosNoCambianEscala;
			$gananciaSujetaImpuesto -= $acumuladoConceptosNoCambianEscala;
			$this->logger->info("Ganancia Sujeta a Impuesto restado el monto que no modifican escala: $" . $gananciaSujetaImpuesto);
			
			$gananciaEmpleado->setAcumuladoConceptosNoCambianEscala($acumuladoConceptosNoCambianEscala);
			
			
			$this->logger->info("------------------------------------------------------------------------------");
			$this->logger->info("Acumulado total a guardar de conceptos que no cambiar escala: $" . $acumuladoConceptosNoCambianEscala);
			$this->logger->info("------------------------------------------------------------------------------");
			
			/** FIN ACUMULO CONCEPTOS QUE NO CAMBIAN LA ESCALA **/
			
			//$gananciaSujetaImpuesto = $gananciaSujetaImpuesto < 0 ? 0 : $gananciaSujetaImpuesto;
			
			$escalaImpuesto = null;
			if ($gananciaSujetaImpuesto >= 0) {
				$escalaImpuesto = $em->getRepository('ADIFRecursosHumanosBundle:EscalaImpuesto')
                    ->getEscalaImpuestoByMesYMontoYVigencia($mes, $gananciaSujetaImpuesto);
			} else {
				// casos en negativo, que vaya a la primera escala
				$escalaImpuesto = $em->getRepository('ADIFRecursosHumanosBundle:EscalaImpuesto')
                    ->getEscalaImpuestoByMesYMontoYVigencia($mes, 0);
			}
            
					
            // ****** Porcentaje a Sumar ******** \\

            $porcentajeASumar = $escalaImpuesto->getPorcentajeASumar();

            $gananciaEmpleado->setPorcentajeASumar($porcentajeASumar);

            $this->logger->info("------------------------------------------------------------------------------");
			$this->logger->info("ID g_escala_impuesto = " . $escalaImpuesto->getId());
            $this->logger->info("Porcentaje a Sumar: " . $porcentajeASumar * 100 . " %");
			
			// Restauro la ganancia sujeta impuesto, ya que solo es a efectos de que no me salte de escala de categoria
			$gananciaSujetaImpuesto += $acumuladoConceptosNoCambianEscala;
			$this->logger->info("Ganancia Sujeta a Impuesto restaurado original: $" . $gananciaSujetaImpuesto);

            // ****** Monto Fijo ******** \\

            $montoFijo = $escalaImpuesto->getMontoFijo();

            $gananciaEmpleado->setMontoFijo($montoFijo);

            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("Monto Fijo: $" . $montoFijo);

            // ****** Monto sin Excedente ******** \\

            $montoSinExcedente = $escalaImpuesto->getMontoDesde();

            $gananciaEmpleado->setMontoSinExcedente($montoSinExcedente);

            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("Monto sin Excedente (monto desde de la escala): $" . $montoSinExcedente);

            // ****** Excedente ******** \\

            $excedente = ($gananciaSujetaImpuesto - $montoSinExcedente) * $porcentajeASumar;
			
			$this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("Calculo del excedente");
			$this->logger->info("excedente = (ganancia sujeta impuesto - monto sin excedente) * porcentaje a sumar");
			$this->logger->info("excedente = ($gananciaSujetaImpuesto - $montoSinExcedente) * $porcentajeASumar");

            $gananciaEmpleado->setExcedente($excedente);

            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("Excedente: $" . $excedente);

            // ****** Total Impuesto ******** \\

            $totalImpuesto = $montoFijo + $excedente;
            
            $gananciaEmpleado->setTotalImpuesto($totalImpuesto);

            $this->logger->info("------------------------------------------------------------------------------");
			$this->logger->info("Calculo de total impuesto");
			$this->logger->info("total impuesto = monto fijo + excedente");
			$this->logger->info("total impuesto = $montoFijo + $excedente");
            $this->logger->info("Total Impuesto: $" . $totalImpuesto);

            // ****** Impuesto Retenido Anual ******** \
            
             if ($mes == 12 && $anio == 2017) {
                // Como el SAC de diciembre 2017 no aplico ganancias, me trae en 0 todos los datos
                // Me tengo que fijar la ultima liquidacion habitual para ir a buscar el impuesto retenido anual
                $fechaCierre = new \DateTime('2017-11-29');
                $liquidacionHabitualAnterior = $this->getLiquidacionEmpleadoAnterior($empleado, $fechaCierre);
                if($liquidacionHabitualAnterior && $liquidacionHabitualAnterior->getGananciaEmpleado() != null) {
                    $impuestoRetenidoAnual = $liquidacionHabitualAnterior->getGananciaEmpleado()->getTotalImpuesto();
                }
            }
			
            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("Impuesto Retenido hasta el mes anterior: $" . $impuestoRetenidoAnual);
			
            // ****** Saldo Impuesto Mes ******** \\

			if ($empleado->getFechaEgreso() != null && $totalImpuesto < 0) {
				// Casos de baja
				$this->logger->info("------------------------------------------------------------------------------");
				$this->logger->info("Casos de baja con total impuesto negativo.");
				$this->logger->info("Se deja el total impuesto y excedente en cero.");
				$this->logger->info("------------------------------------------------------------------------------");
				$totalImpuesto = 0;
				$gananciaEmpleado->setExcedente(0);
				$gananciaEmpleado->setTotalImpuesto(0);
			}
			
            $saldoImpuestoMes = $totalImpuesto - $impuestoRetenidoAnual;

            if (($gananciaEmpleado->getLiquidacionEmpleado()->getEmpleado()->getFechaEgreso() !== null) && ($gananciaEmpleado->getLiquidacionEmpleado()->getEmpleado()->getAplicaEscalaDiciembre())) {
                $form572 = $gananciaEmpleado->getLiquidacionEmpleado()->getEmpleado()->getFormulario572();
                $percepciones = $form572 == null ? 0 : round($form572->getPercepciones(), 2);
                $this->logger->info("------------------------------------------------------------------------------");
                $this->logger->info("Percepciones anuales: " . $percepciones);
                $this->logger->info("------------------------------------------------------------------------------");

                $saldoImpuestoMes -= $percepciones;
            }
            
            /** Conceptos de ganancia F572 - "Retenciones otros empleos" **/
            $retencionesOtrosEmpleos = $this->calcularConceptoRetencionOtrosEmpleos($gananciaEmpleado, $mes);
            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("Retenciones otros empleos del F572: " . $retencionesOtrosEmpleos);
            $this->logger->info("------------------------------------------------------------------------------");
            
            $saldoImpuestoMes -= $retencionesOtrosEmpleos;
            /** Fin de Conceptos de ganancia F572 - "Retenciones otros empleos" **/

			$saldoImpuestoMes = round($saldoImpuestoMes,2);
			if (abs($saldoImpuestoMes) == 0) {
				$saldoImpuestoMes = 0;
			}
			
			if ($gananciaSujetaImpuesto < 0 && $empleado->getFechaEgreso() == null) {
				$this->logger->info("------------------------------------------------------------------------------");
				$this->logger->info("No se corresponde retener ganancias a este empleado.");
				$this->logger->info("Se deja en cero total impuesto determinado, excedente y saldo impuesto mes.");
				$this->logger->info("------------------------------------------------------------------------------");
				$saldoImpuestoMes = 0;
				$gananciaEmpleado->setTotalImpuesto(0);
				$gananciaEmpleado->setExcedente(0);
			}

			$gananciaEmpleado->setSaldoImpuestoMes($saldoImpuestoMes);
            

            $gananciaEmpleado->setImpuestoRetenidoAnual($impuestoRetenidoAnual + ( ($acumula) ? $saldoImpuestoMes : 0 ));
            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("Nota para el programador: INSERT \"g_ganancia_empleado.impuesto_retenido_anual\" para el mes $mes: " . $gananciaEmpleado->getImpuestoRetenidoAnual());
            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("------------------------------------------------------------------------------");

            $gananciaEmpleado->setHaberNetoAcumulado($gananciaEmpleado->getHaberNeto() + $haberNetoAcumuladoAnterior);

            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("Impuesto Retenido del mes: " . $saldoImpuestoMes);
            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("------------------------------------------------------------------------------");
        }
        // FIN --> if (null != $rangoRemuneracion) 
        else {
            $this->logger->error('No está definido el rango de remuneración de ganancia.');
            $this->logger->error('No se puede calcular ganancia');
        }

        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("Fin Ganancia");

        return $saldoImpuestoMes;
    }

    /**
     * Suma los conceptos de ganancia
     * 
     * @param type $gananciaEmpleado
     * @param type $mes
     * @param type $rangoRemuneracion
     * @param type $ordenAplicacionTipoConcepto
     * @param type $valorReferenciaPorcentaje
     * @return type
     */
    private function sumarConceptos($gananciaEmpleado, $mes, $rangoRemuneracion, $ordenAplicacionTipoConcepto, $valorReferenciaPorcentaje = null) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $total = 0;

        $formulario572 = $gananciaEmpleado->getLiquidacionEmpleado()->getEmpleado()->getFormulario572();
		$empleado = $gananciaEmpleado->getLiquidacionEmpleado()->getEmpleado();

        $conceptosFormulario572 = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoFormulario572')
                ->getConceptosFormulario572ByOrdenAplicacionTipoConcepto($formulario572, $ordenAplicacionTipoConcepto);

        $conceptosFormulario572ArrayCollection = new ArrayCollection($conceptosFormulario572);

        //conceptos que son carga familiar
        $conceptosFormulario572CargaFamiliar = $conceptosFormulario572ArrayCollection->filter(
                function($entry) {
            return in_array($entry->getConceptoGanancia()->getEsCargaFamiliar(), array(true));
        });

        $total += $this->sumarConceptosCargaFamiliar($conceptosFormulario572CargaFamiliar, $gananciaEmpleado, $mes, $rangoRemuneracion);

        $conceptosFormulario572NoCargaFamiliar = $conceptosFormulario572ArrayCollection->filter(
                function($entry) {
            return in_array($entry->getConceptoGanancia()->getEsCargaFamiliar(), array(false));
        });

        $total += $this->sumarConceptosNoCargaFamiliar($conceptosFormulario572NoCargaFamiliar, $gananciaEmpleado, $mes, $rangoRemuneracion, $empleado, $valorReferenciaPorcentaje);

        $this->logger->info("------------------------------------------------------------------------------");

        return $total;
    }

    /**
     * Suma los conceptos del f572 que no son carga familiar
     * 
     * @param type $conceptosFormulario572
     * @param type $gananciaEmpleado
     * @param type $mes
     * @param type $rangoRemuneracion
     * @param type $valorReferenciaPorcentaje
     */
    private function sumarConceptosNoCargaFamiliar($conceptosFormulario572, $gananciaEmpleado, $mes, $rangoRemuneracion, $empleado, $valorReferenciaPorcentaje = null) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $total = 0;

        $arrayDeConceptos = array();
        foreach ($conceptosFormulario572 as $conceptoFormulario572) {
            $monto = ((($mes > $conceptoFormulario572->getMesHasta()) ? $conceptoFormulario572->getMesHasta() : $mes ) - $conceptoFormulario572->getMesDesde() + 1 ) * $conceptoFormulario572->getMonto();
            if (!isset($arrayDeConceptos[$conceptoFormulario572->getConceptoGanancia()->getId()])) {
                $arrayDeConceptos[$conceptoFormulario572->getConceptoGanancia()->getId()] = array(
                    'concepto' => $conceptoFormulario572->getConceptoGanancia(),
                    'monto' => $monto
                );
            } else {
                $arrayDeConceptos[$conceptoFormulario572->getConceptoGanancia()->getId()]['monto'] += $monto;
            }
        }

        foreach ($arrayDeConceptos as $itemArray) {

            $montoConcepto = $itemArray['monto'];

            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("Concepto: " . $itemArray['concepto']);

            $this->logger->info("Monto Cargado Acumulado: $" . number_format($montoConcepto, 2));

            $montoARestar = 0;

            if ($montoConcepto > 0) {

                $topeConceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')
                        ->createQueryBuilder('tcg')
                        ->where('tcg.rangoRemuneracion = :rangoRemuneracion')
                        ->andWhere('tcg.conceptoGanancia = :conceptoGanancia')
                        ->andWhere('tcg.vigente = :vigente')
                        ->setParameter('rangoRemuneracion', $rangoRemuneracion)
                        ->setParameter('conceptoGanancia', $itemArray['concepto'])
                        ->setParameter('vigente', 1)
                        ->getQuery()
                        ->getOneOrNullResult()
                ;


                if (null != $topeConceptoGanancia) {

					$this->logger->info("ID Tope = " . $topeConceptoGanancia->getId());
					$this->logger->info("Tope (no carga familiar) total = " . $topeConceptoGanancia->getValorTope());
					
                    if ($topeConceptoGanancia->getEsPorcentaje() && null != $valorReferenciaPorcentaje) {
						
                        $valorTopeMensual = $topeConceptoGanancia->getValorTope() * $valorReferenciaPorcentaje;
                        $this->logger->info("Porcentaje tope del Concepto: %" . $topeConceptoGanancia->getValorTope() * 100);
                        $this->logger->info("Valor Tope del Concepto: $" . $valorTopeMensual);
						
                    } else {
						
                        $valorTopeMensual = 
								( ($topeConceptoGanancia->getEsValorAnual() && $empleado->getFechaEgreso() == null)
									? $topeConceptoGanancia->getValorTope() / 12 
									: $topeConceptoGanancia->getValorTope() // Si tiene fecha de egreso toma todo el tope
								) * ( ($empleado->getFechaEgreso() == null) ? $mes : 1 );
						
                        $this->logger->info("Valor Tope del Concepto Mensual: $" . $valorTopeMensual);
						
                    }

                    $montoARestar = $montoConcepto;
                    if ($montoConcepto > $valorTopeMensual) {
                        $montoARestar = $valorTopeMensual;
                    }

                    $total += $montoARestar;

                    $this->logger->info("Monto Restado: " . $montoARestar);
                }
            }

            $conceptoGananciaCalculado = new ConceptoGananciaCalculado();
            $conceptoGananciaCalculado
                    ->setConceptoGanancia($itemArray['concepto'])
                    ->setMonto($montoARestar);

            $gananciaEmpleado->addConcepto($conceptoGananciaCalculado);
        }
        return $total;
    }

    /**
     * Suma los conceptos del f572 que son carga familiar
     * 
     * @param type $conceptosFormulario572
     * @param type $gananciaEmpleado
     * @param type $mes
     * @param type $rangoRemuneracion
     * @return type
     */
    private function sumarConceptosCargaFamiliar($conceptosFormulario572, $gananciaEmpleado, $mes, $rangoRemuneracion) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $total = 0;

        foreach ($conceptosFormulario572 as $conceptoFormulario572) {

            $montoARestar = 0;

            $topeConceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')->
                    findOneBy(
                    array(
                        'rangoRemuneracion' => $rangoRemuneracion,
                        'conceptoGanancia' => $conceptoFormulario572->getConceptoGanancia(),
						'vigente' => 1
                    )
            );

            if (null != $topeConceptoGanancia) {

                $valorTopeMensual = ($topeConceptoGanancia->getEsValorAnual()) ? $topeConceptoGanancia->getValorTope() / 12 : $topeConceptoGanancia->getValorTope();
                $this->logger->info("------------------------------------------------------------------------------");
				$this->logger->info("ID Tope = " . $topeConceptoGanancia->getId());
				$this->logger->info("Tope (carga familiar) total = " . $topeConceptoGanancia->getValorTope());
                $this->logger->info("Concepto: " . $conceptoFormulario572->getConceptoGanancia());
                $this->logger->info("Monto Cargado Mensual: $" . $valorTopeMensual);
                $this->logger->info("Valor Tope del Concepto Mensual: $" . $valorTopeMensual);

                $aplica = true;

                if ($gananciaEmpleado->getLiquidacionEmpleado()->getEmpleado()->getFechaEgreso() === null) {
                    if (!($mes >= $conceptoFormulario572->getMesDesde() && $mes <= $conceptoFormulario572->getMesHasta())) {
                        $aplica = false;
                    }
                }
                //si el empleado está dado de baja, se toma la tabla de diciembre
                else if ($gananciaEmpleado->getLiquidacionEmpleado()->getEmpleado()->getAplicaEscalaDiciembre()) {
                    $mes = 12;
                }

                if ($aplica) {
                    $montoARestar = $valorTopeMensual * ($mes - $conceptoFormulario572->getMesDesde() + 1);
                }

                $total += $montoARestar;

                $this->logger->info("Monto Restado: " . $montoARestar);
            }

            $conceptoGananciaCalculado = new ConceptoGananciaCalculado();
            $conceptoGananciaCalculado
                    ->setConceptoGanancia($conceptoFormulario572->getConceptoGanancia())
                    ->setMonto($montoARestar);

            $gananciaEmpleado->addConcepto($conceptoGananciaCalculado);
        }

        return $total;
    }

    /**
     * 
     * Se obtiene la liquidacion anterior
     * puede ser habitual, adicional, o sac.
     * Se debe pasar por parámetro el tipo
     * Nota: Si la liquidacion anterior es adicional y no genero ganancias, no te lo devuelve por el inner en gananciaEmpleado
     * @param type $empleado
     * @param type $fechaCierre
     * @return type
     */
    private function getLiquidacionEmpleadoAnterior($empleado, $fechaCierre, $idLiquidacionEmpleado = null) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $liquidacionEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
                ->createQueryBuilder('le')
                ->select('le')
                ->innerJoin('le.liquidacion', 'li')
                ->innerJoin('le.gananciaEmpleado', 'ge')
                ->where('le.empleado = :empleado')
                ->andWhere('li.fechaCierreNovedades <= :fechaCierre')
                ->andWhere('le.id <> :idLiquidacionEmpleado')
                ->orderBy('li.fechaCierreNovedades', 'DESC')
                ->setMaxResults(1)
                ->setParameters(new ArrayCollection(array(
                    new Parameter('empleado', $empleado),
                    new Parameter('fechaCierre', $fechaCierre),
                    new Parameter('idLiquidacionEmpleado', $idLiquidacionEmpleado != null ? $idLiquidacionEmpleado : -1)
                        ))
                )
                ->getQuery()
                ->getOneOrNullResult();

        return $liquidacionEmpleado;
    }
	
	private function getLiquidacionEmpleadoAnteriorAdicional($empleado, $fechaCierre, $idLiquidacionEmpleado = null)
	{
		$em = $this->getDoctrine()->getManager($this->getEntityManager());

        $liquidacionEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
                ->createQueryBuilder('le')
                ->select('le')
                ->innerJoin('le.liquidacion', 'li')
				->innerJoin('li.tipoLiquidacion', 'tl')
                ->leftJoin('le.gananciaEmpleado', 'ge')
                ->where('le.empleado = :empleado')
                ->andWhere('li.fechaCierreNovedades <= :fechaCierre')
                ->andWhere('le.id <> :idLiquidacionEmpleado')
				->andWhere('tl.id = :idTipoLiquidacion')
                ->orderBy('li.fechaCierreNovedades', 'DESC')
                ->setMaxResults(1)
                ->setParameters(new ArrayCollection(array(
                    new Parameter('empleado', $empleado),
                    new Parameter('fechaCierre', $fechaCierre),
                    new Parameter('idLiquidacionEmpleado', $idLiquidacionEmpleado != null ? $idLiquidacionEmpleado : -1),
					new Parameter('idTipoLiquidacion', TipoLiquidacion::__ADICIONAL)
					))
                )
                ->getQuery()
                ->getOneOrNullResult();

        return $liquidacionEmpleado;
	}

    /**
     * 
     * Se obtienen las liquidaciones en el rango especificado
     * 
     * @param type $empleado
     * @param type $fechaAnterior
     * @param type $fechaActual
     * @param type $tiposLiquidacion
     * @return type
     */
    private function getLiquidacionEmpleadoAnterioresRango($empleado, $fechaAnterior, $fechaActual, $tiposLiquidacion) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $liquidacionesEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
                ->createQueryBuilder('le')
                ->select('le')
                ->leftJoin('le.liquidacion', 'li')
                ->where('le.empleado = :empleado')
                ->andWhere('li.tipoLiquidacion in (:tiposLiquidacion)')
                ->andWhere('li.fechaCierreNovedades BETWEEN :fechaAnterior AND :fechaActual')
                ->orderBy('le.id', 'DESC')
                ->setParameters(new ArrayCollection(array(
                    new Parameter('empleado', $empleado),
                    new Parameter('tiposLiquidacion', $tiposLiquidacion),
                    new Parameter('fechaAnterior', $fechaAnterior),
                    new Parameter('fechaActual', $fechaActual)))
                )
                ->getQuery()
                ->getResult();
        return $liquidacionesEmpleado;
    }

    /**
     * Calcula los concepto ganancia del tipo de "otros ingresos" (orden de aplicacion 0) y lo deja aplicado para que
     * no se vuelva a liquidar y calculado para que aparezca en el excel de IG. 
     * Se usa para la liquidacion mensual
     * @param type $gananciaEmpleado
     * @param type $mes
     * @return float
     */
    private function calcularOtrosIngresos($gananciaEmpleado, $mes) 
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $formulario572 = $gananciaEmpleado->getLiquidacionEmpleado()->getEmpleado()->getFormulario572();

        $conceptosFormulario572 = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoFormulario572')
                ->getConceptosFormulario572ByOrdenAplicacionTipoConcepto($formulario572, 0);

        $totalOtrosIngresos = 0;
        
        $conceptosGananciaOtrosEmpleadoresRestan = array(
            ConceptoGanancia::__CODIGO_JUBILACION_OTRO_EMPLEADOR,
            ConceptoGanancia::__CODIGO_OBRA_SOCIAL_OTRO_EMPLEADOR,
            ConceptoGanancia::__CODIGO_SINDICAL_OTRO_EMPLEADOR,
            //ConceptoGanancia::__CODIGO_RETENCION_OTROS_EMPLEADOR
        );
		
		$empleado = $gananciaEmpleado->getLiquidacionEmpleado()->getEmpleado();

        foreach ($conceptosFormulario572 as $conceptoFormulario572) {

            $montoASumar = $conceptoFormulario572->getMonto();
            
            $conceptoGanancia = $conceptoFormulario572->getConceptoGanancia();

            if ($gananciaEmpleado->getLiquidacionEmpleado()->getLiquidacion()->getTipoLiquidacion()->getId() != TipoLiquidacion::__SAC && $conceptoFormulario572->getConceptoGanancia()->getIndicaSAC() && $mes >= 6 && $mes <= 12) {
                $montoASumar /= 7;
                if ($empleado->getFechaEgreso() !== null) {
                    $montoASumar *= ( 13 - $empleado->getFechaEgreso()->format('m'));
                }
            } else {
                $montoASumar = 0;
            }

            if ($conceptoFormulario572->getDetalleConceptoFormulario572Aplicado() != null) {
                if ($conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->getAplicado()) {
                    $montoASumar = 0;
                } else {
                    $montoASumar = $conceptoFormulario572->getMonto() - $conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->getMontoAplicado();
                    if (in_array($conceptoGanancia->getCodigo572(), $conceptosGananciaOtrosEmpleadoresRestan)) {
                        $montoASumar *= -1;
                    }
                    $conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->setMontoAplicado($montoASumar);
                    $conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->setAplicado(true);
                }
            }

			if (!in_array($conceptoGanancia->getId(), $this->conceptosGananciasDeprecadosIds)) {
				
				$totalOtrosIngresos += $montoASumar;

				$this->logger->info("------------------------------------------------------------------------------");
                $this->logger->info("Concepto ID: " . $conceptoGanancia->getId()); 
                $this->logger->info("Concepto Form 572 ID: " . $conceptoFormulario572->getId()); // 
				$this->logger->info("Concepto: " . $conceptoGanancia);
				$this->logger->info("Monto a sumar: $" . number_format($montoASumar, 2));
				$this->logger->info("------------------------------------------------------------------------------");

				$conceptoGananciaCalculado = new ConceptoGananciaCalculado();
				$conceptoGananciaCalculado
						->setConceptoGanancia($conceptoGanancia)
						->setMonto($montoASumar);

				$gananciaEmpleado->addConcepto($conceptoGananciaCalculado);
								
			} else {
				
				$this->logger->info("------------------------------------------------------------------------------");
				$this->logger->info("Concepto deprecado: " . $conceptoFormulario572->getConceptoGanancia());
				$this->logger->info("Monto que hubiese sumado: $" . number_format($montoASumar, 2));
				$this->logger->info("------------------------------------------------------------------------------");
				
//				$dt = new \DateTime();
//				
//				if ($dt->format('n') == 7 && $dt->format('Y') == 2018) {
//					// Fix 1 SAC 2018 - 01/07/2018
//					
//					 $liquidacionEmpleadoFixSac = $em
//						->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
//						->createQueryBuilder('le')
//						->select('le')
//						->innerJoin('le.liquidacion', 'l')
//						->where('le.empleado = :empleado')
//						->andWhere('l.id = 75')
//						->setParameter('empleado', $empleado)
//						->getQuery()
//						->getOneOrNullResult();
//						
//					if ($liquidacionEmpleadoFixSac) {
//						
//						$monto = $liquidacionEmpleadoFixSac->getNeto() - $liquidacionEmpleadoFixSac->getRedondeo();
//						
//						$this->logger->info("------------------------------------------------------------------------------");
//						$this->logger->info("Fix por 1 SAC 2018");
//						$this->logger->info("Concepto ID: " . $conceptoGanancia->getId()); 
//						$this->logger->info("Concepto Form 572 ID: " . $conceptoFormulario572->getId()); // 
//						$this->logger->info("Concepto: " . $conceptoGanancia);
//						$this->logger->info("Monto a sumar: $" . number_format($monto, 2));
//						$this->logger->info("------------------------------------------------------------------------------");
//						
//						$conceptoGananciaCalculado = new ConceptoGananciaCalculado();
//						$conceptoGananciaCalculado
//							->setConceptoGanancia($conceptoGanancia)
//							->setMonto($monto);
//
//						$gananciaEmpleado->addConcepto($conceptoGananciaCalculado);
//						
//						$totalOtrosIngresos += $monto;
//					}
//				} //
			}
        }

        return $totalOtrosIngresos;
    }

    /**
     * Genera el formulario 649
     *
     * @Route("/formulario649/{id}/{anio}", name="exportacionF649")
     * @Method("GET")
     * @Security("has_role('ROLE_RRHH_ALTA_F649')")
     */
    public function exportarF649(Request $request, $id, $anio = null) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleado = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->find($id);

        if (!$empleado) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $html = '<html><head><meta charset="utf-8"/><style type="text/css">' . $this->renderView('ADIFRecursosHumanosBundle:Empleado:exportacion649.css.twig') . '</style></head><body>';

        $htmlTemp = '';
        
        if ($anio >= 2017) {
            $htmlTemp = $this->getDatos649Anio2018($empleado, $anio);
        } else {
            $htmlTemp = $this->getDatos649($empleado, $anio, true);
        }

        if ($htmlTemp == null) {
            $this->get('session')->getFlashBag()->add(
                    'warning', 'No se puede exportar el formulario 649 de ese año.'
            );
            return $this->redirect($request->headers->get('referer'));
        }

        $html .= $htmlTemp; // Pagina 1 - Original empleado
        $html .= $htmlTemp; // Pagina 2 - Duplicado - empleador
        $html .= '</body></html>';
		//die($html);

        $filename = 'Formulario_649_' . AdifApi::stringCleaner($empleado->__toString()) . '.pdf';

        $snappy = $this->get('knp_snappy.pdf');
        $snappy->setOption('lowquality', false);
		
		// Fix impresion para Debian - @gluis
		$snappy->setOption('zoom', '0.7518');

        return new Response(
                $snappy->getOutputFromHtml($html), 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                )
        );
    }

    /**
     * 
     * Get montos f572 de conceptos del tipo otros ingresos dado el codigo
     * 
     * @param type $f572
     * @param type $codigo
     * @return type
     */
    private function getMontoOtrosIngresosConceptoF572($f572, $codigo) {
        $total = 0;
        if ($f572 != null) {
            $conceptosF572 = $this->getConceptosFormulario572ByCodigo($f572, $codigo);
            if ($conceptosF572->count() > 0) {
                foreach ($conceptosF572 as $conceptoFormulario572) {
                    $total += $conceptoFormulario572->getMonto() * ($conceptoFormulario572->getMesHasta() - $conceptoFormulario572->getMesDesde() + 1);
                }
            }
        }
        return $total;
    }
    
    private function getMontoRemuneracionesOtrosEmpleador($f572, $codigo) 
    {
        $total = 0;
        if ($codigo == 'e_2') {
            if ($f572 != null) {
                $conceptosF572 = $this->getConceptosFormulario572ByCodigo($f572, $codigo);
                if ($conceptosF572->count() > 0) {
                    foreach ($conceptosF572 as $conceptoFormulario572) {
                        $total += $conceptoFormulario572->getMonto();
                    }
                }
            }
        }
        return $total;
    }

    /**
     * 
     * Filtra de todos los conceptos, el que se pasa por parametro
     * 
     * @param type $conceptos
     * @param type $codigo
     * @return type
     */
    private function getMontoConceptoLiquidacion($conceptos, $codigo) {
        $total = 0;
        $colection = $conceptos->filter(
                function($entry) use ($codigo) {
            return in_array($entry->getConceptoVersion()->getCodigo(), array($codigo));
        });
        foreach ($colection as $element) {
            $total += $element->getMonto();
        }
        return $total;
    }
	
	/**
     * 
     * Filtra de todos los conceptos, los que se pasa por parametro
     * 
     * @param type $conceptos
     * @param array $codigo
     * @return type
     */
    private function getMontoConceptoLiquidacionArray($conceptos, array $codigos) {
        $total = 0;
		$colection = $conceptos->filter(
                function($entry) use ($codigos) {
            return in_array($entry->getConceptoVersion()->getCodigo(), $codigos);
        });
		foreach ($colection as $element) {
            $total += $element->getMonto();
        }
        return $total;
    }
    
    /**
     * Sumarizo los conceptos que no integran IG quea sean remunerativos y no remunerativos y que esten activos
     * Esto por ahora aplica para el año 2017
     * @param type $conceptos
     */
    public function getMontoExentosIG($conceptos) 
    {
        $total = 0;
		$colection = $conceptos->filter(
            function($entry) {
                $conceptoVersion = $entry->getConceptoVersion();
                $idsTipoConceptosPermitidos = array(1,2); // Remunerativos y no remunerativos
                if ($conceptoVersion->getActivo() && !$conceptoVersion->getIntegraIg() && in_array($conceptoVersion->getIdTipoConcepto(), $idsTipoConceptosPermitidos)) {
                    return true;
                }
        });
        
		foreach ($colection as $element) {
            
//            echo '<br>---------------------<br>';
//            echo 'Concepto: ' . $element->getConceptoVersion()->getLeyenda() .'<br/>';
//            echo 'Codigo: ' . $element->getConceptoVersion()->getCodigo() .'<br/>';
//            echo 'Monto: ' . $element->getMonto() .'<br/>';
//            echo '<br>---------------------<br>';
            
            $total += $element->getMonto();
        }
        
        return $total;
    }

    /**
     * 
     * Tope de concepto de formulario 572 para conceptos que no son carga familiar
     * 
     * @param type $conceptosFormulario572
     * @param type $rangoRemuneracion
     * @param type $valorReferenciaPorcentaje
     * @return type
     */
    private function determinarTopeConceptoF572($f572, $codigo, $rangoRemuneracion, $mes, $valorReferenciaPorcentaje = null, $anio = null) {

        $tope = 0;

        if ($f572 != null) {
            $conceptosFormulario572 = $this->getConceptosFormulario572ByCodigo($f572, $codigo);
            if ($conceptosFormulario572->count() > 0) {

                $em = $this->getDoctrine()->getManager($this->getEntityManager());

                $topeConceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')
					->findBy(
                        array(
                            'rangoRemuneracion' => $rangoRemuneracion,
                            'conceptoGanancia' => $conceptosFormulario572->first()->getConceptoGanancia(),
							//'vigente' => 1
                        )
                );
				
				if ($anio != null) {
					$topeConceptoGanancia = new ArrayCollection($topeConceptoGanancia);
					$topeConceptoGanancia = $topeConceptoGanancia->filter(
						function($entry) use ($anio) {
							return $entry->getFechaDesde()->format('Y') == $anio;
					});
					
					$topeConceptoGanancia = $topeConceptoGanancia->first();
				} else {
					$topeConceptoGanancia = $topeConceptoGanancia[0];
				}
				
                if (null != $topeConceptoGanancia) {

                    $valorTope = ($topeConceptoGanancia->getEsValorAnual()) ? $topeConceptoGanancia->getValorTope() : $topeConceptoGanancia->getValorTope() * 12;

                    if ($topeConceptoGanancia->getEsPorcentaje() && null != $valorReferenciaPorcentaje) {
                        $valorTope *= $valorReferenciaPorcentaje;
                    } else {
                        $valorTope *= $mes / 12;
                    }

                    $monto = 0;

                    $valorTopeTempAcumulado = 0;

                    foreach ($conceptosFormulario572 as $conceptoFormulario572) {
                        $montoCargado = ($conceptoFormulario572->getMesHasta() - $conceptoFormulario572->getMesDesde() + 1) * $conceptoFormulario572->getMonto();
                        $monto += $montoCargado;
                    }
                    $tope += min($monto, $valorTope);
                }
            }
        }
        return $tope;
    }

    /**
     * 
     * Tope de concepto de formulario 572 para conceptos que son carga familiar
     * 
     * @param type $conceptosFormulario572
     * @param type $rangoRemuneracion
     * @param type $valorReferenciaPorcentaje
     * @return type
     */
    private function determinarTopeCargaFamiliarConceptoF572($f572, $codigo, $rangoRemuneracion, $mes, $anio = null) {

        $tope = 0;

        if ($f572 != null) {
            $conceptosFormulario572 = $this->getConceptosFormulario572ByCodigo($f572, $codigo);
            if ($conceptosFormulario572->count() > 0) {

                $em = $this->getDoctrine()->getManager($this->getEntityManager());

                $topeConceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')
					->findBy(
                        array(
                            'rangoRemuneracion' => $rangoRemuneracion,
                            'conceptoGanancia' => $conceptosFormulario572->first()->getConceptoGanancia(),
							//'vigente' => 1
                        )
                );
				
				if ($anio != null) {
					$topeConceptoGanancia = new ArrayCollection($topeConceptoGanancia);
					$topeConceptoGanancia = $topeConceptoGanancia->filter(
						function($entry) use ($anio) {
							return $entry->getFechaDesde()->format('Y') == $anio;
					});
					
					$topeConceptoGanancia = $topeConceptoGanancia->first();
				} else {
					$topeConceptoGanancia = $topeConceptoGanancia[0];
				}
				
                if (null != $topeConceptoGanancia) {

                    $valorTope = ($topeConceptoGanancia->getEsValorAnual()) ? $topeConceptoGanancia->getValorTope() / 12 : $topeConceptoGanancia->getValorTope();

                    foreach ($conceptosFormulario572 as $conceptoFormulario572) {
                        // @TODO: revisar aca porque le saca toda la carga familiar cuando no es todo el año - @gluis
                        $meses = ( min($conceptoFormulario572->getMesHasta(), $mes) - $conceptoFormulario572->getMesDesde() + 1);
                        $montoCargado = $meses * $valorTope;
                        $tope += $montoCargado;
//                        echo '<br>------------------<br>';
//                        echo 'Concepto ID: ' . $conceptoFormulario572->getId();
//                        echo '<br>------------------<br>';
//                        echo 'Carga familiar: ' . $conceptoFormulario572->__toString();
//                        echo '<br>------------------<br>';
//                        echo 'Valor: ' . $valorTope;
//                        echo '<br>------------------<br>';
//                        echo 'Meses calculados: ' . $meses;
//                        echo '<br>------------------<br>';
//                        echo 'Mes desde: ' . $conceptoFormulario572->getMesDesde();
//                        echo '<br>------------------<br>';
//                        echo 'Mes hasta: ' . $conceptoFormulario572->getMesHasta();
//                        echo '<br>------------------<br>';
//                        echo "Meses * Valor => $meses * $valorTope = " . $montoCargado;
//                        echo '<br>------------------<br>';
//                        echo 'Tope (return): ' . $tope;
//                        echo '<br>------------------<br>';
                    }
                }
            }
        }
        return $tope;
    }

    /**
     * 
     * @param type $conceptos
     * @param type $codigo
     * @return type
     */
    private function getTopeConceptoGanancia($codigo, $mes, $rangoRemuneracion, $anio = null) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $concepto = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoGanancia')->findBy(array('codigo572' => $codigo));

        $topeConceptoGanancia = $em->getRepository('ADIFRecursosHumanosBundle:TopeConceptoGanancia')
			->findBy(
                array(
                    'rangoRemuneracion' => $rangoRemuneracion,
                    'conceptoGanancia' => $concepto[0],
					//'vigente' => 1
                )
        );
		
		if ($anio != null) {
			$topeConceptoGanancia = new ArrayCollection($topeConceptoGanancia);
			$topeConceptoGanancia = $topeConceptoGanancia->filter(
				function($entry) use ($anio) {
					return $entry->getFechaDesde()->format('Y') == $anio;
			});
			
			$topeConceptoGanancia = $topeConceptoGanancia->first();
		} else {
			$topeConceptoGanancia = $topeConceptoGanancia[0];
		}
		
        return $topeConceptoGanancia->getValorTope() * $mes / 12;
    }
	
	public function getTopeConceptoGananciaDeduccionEspecialAnio2016($empleado) 
	{
		$em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('monto', 'monto');

		$sql = "
			SELECT 
				cgc.monto
			FROM empleado e
			INNER JOIN liquidacion_empleado le ON e.id = le.id_empleado
			INNER JOIN liquidacion l ON le.id_liquidacion = l.id
			INNER JOIN g_ganancia_empleado ge ON le.id_ganancia_empleado = ge.id
			LEFT JOIN g_concepto_ganancia_calculado cgc ON ge.id = cgc.id_ganancia_empleado
			LEFT JOIN g_tope_concepto_ganancia tc ON cgc.id_tope_concepto_ganancia = tc.id
			LEFT JOIN g_concepto_ganancia cg ON cgc.id_concepto_ganancia = cg.id
			WHERE e.id = " . $empleado->getId() . "
			AND l.id = 49 -- ultima liquidacion diciembre
			AND cg.id = 53 -- concepto deduccion especial
			";
		
		$query = $em->createNativeQuery($sql, $rsm);
		
		$result = $query->getOneOrNullResult();
		return (!is_null($result) && isset($result['monto'])) ? $result['monto'] : null; 
	}

    /**
     * 
     * Get detalle de concepto572 dado el codigo y el formulario
     * 
     * @param type $f572
     * @param type $codigo
     * @return type
     */
    private function getDetalleF572($f572, $codigo) {
        $conceptos = array(
            'total' => 0,
            'conceptos' => new ArrayCollection()
        );
        if ($f572 != null) {
            $conceptosF572 = $this->getConceptosFormulario572ByCodigo($f572, $codigo);
            $conceptos = $this->detalleConceptoF572($conceptosF572);
        }
        return $conceptos;
    }

    /**
     * 
     * Detalle y tope de concepto de formulario 572
     * 
     * @param type $conceptosFormulario572
     * @return type
     */
    private function detalleConceptoF572($conceptosFormulario572) {

        $conceptosArrayTotal = new ArrayCollection();
        $conceptos = new ArrayCollection();

        $total = 0;

        foreach ($conceptosFormulario572 as $conceptoFormulario572) {

            $montoConcepto = $conceptoFormulario572->getMonto() * ($conceptoFormulario572->getMesHasta() - $conceptoFormulario572->getMesDesde() + 1);

            $conceptos[] = array(
                'cuit' => ($conceptoFormulario572->getDetalleConceptoFormulario572() != null) ? $conceptoFormulario572->getDetalleConceptoFormulario572()->getCuit() : '',
                'detalle' => ($conceptoFormulario572->getDetalleConceptoFormulario572() != null) ? $conceptoFormulario572->getDetalleConceptoFormulario572()->getDetalle() : '',
                'monto' => $montoConcepto,
            );

            $total += $montoConcepto;
        }

        $conceptosArrayTotal['total'] = $total;
        $conceptosArrayTotal['conceptos'] = $conceptos;

        return $conceptosArrayTotal;
    }

    /**
     * Obtienen los conceptos del f572 de acuerdo al código
     * 
     * @param type $f572
     * @param type $codigo
     */
    private function getConceptosFormulario572ByCodigo($f572, $codigo) {
        return $f572->getConceptos()->filter(
                        function($entry) use ($codigo) {
                    return in_array($entry->getConceptoGanancia()->getCodigo572(), array($codigo));
                });
    }

    /**
     * Genera el formulario 649
     *
     * @Route("/formulario649masivo/{anio}", name="exportacionF649_masivo")
     * @Method("GET")
     * @Security("has_role('ROLE_RRHH_ALTA_F649')")
     */
    public function exportarF649Masivo(Request $request, $anio) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

//        $tope = 20;
//        $offset = 1;

        $empleados = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')
                ->createQueryBuilder('e')
                ->innerJoin('e.persona', 'p')
                ->where('e.fechaEgreso IS NULL')
                ->orWhere('e.fechaEgreso IS NOT NULL AND YEAR(e.fechaEgreso) > :anio')
//                ->setFirstResult($offset)
//                ->setMaxResults($tope)
                ->orderBy('p.apellido', 'ASC')
                ->setParameter('anio', $anio)
                ->getQuery()
                ->getResult();

        if (!$empleados) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $html = '<html><head><meta charset="utf-8"/><style type="text/css">' . $this->renderView('ADIFRecursosHumanosBundle:Empleado:exportacion649.css.twig') . '</style></head><body>';
        $pagina1 = '';
        $pagina2 = '';
        foreach ($empleados as $empleado) {
            
             if ($anio >= 2017) {
                $pagina1 = $this->getDatos649Anio2018($empleado, $anio); // Pagina 1
                $pagina2 = $pagina1; // Pagina 2
                $html .= $pagina1 . $pagina2;
             } else {
                $html .=$this->getDatos649($empleado, $anio);
                $html .= '<div id="pagebreak" style="page-break-before:always;"></div>';
             }
        }

        $html .= '</body></html>';
        $filename = 'Formulario_649_Nomina_Completa.pdf';

        $snappy = $this->get('knp_snappy.pdf');
        $snappy->getInternalGenerator()->setTimeout(2 * 3600);
        $snappy->setOption('lowquality', false);
		
		// Fix impresion para Debian - @gluis
		$snappy->setOption('zoom', '0.7518');

        return new Response(
                $snappy->getOutputFromHtml($html), 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                )
        );
    }
	
	/**
     * Exporta la columna solo de "Totales del Rubro 1" de Importe bruto de las ganancias del formulario 649 para c/u de los empleados.
     *
     * @Route("/formulario649masivoImporteBrutoGanancias/{anio}", name="exportacionF649ImporteBrutoGanancias_masivo")
     * @Method("GET")
     * @Security("has_role('ROLE_RRHH_ALTA_F649')")
     */
	public function exportacionF649ImporteBrutoGananciasAction($anio)
	{
		$em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleados = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')
                ->createQueryBuilder('e')
                ->innerJoin('e.persona', 'p')
                ->where('e.fechaEgreso IS NULL')
                ->orWhere('e.fechaEgreso IS NOT NULL AND YEAR(e.fechaEgreso) > :anio')
                ->orderBy('p.apellido', 'ASC')
                ->setParameter('anio', $anio)
                ->getQuery()
                ->getResult();

        if (!$empleados) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }
		
		$html = '<table>
				<tr>
					<th>Legajo</th>
					<th>CUIL</th>
					<th>Apellido</th>
					<th>Nombre</th>
					<th>Totales del Rubro 1</th>
				</tr>';
				
		foreach ($empleados as $empleado) {
			$html .= '<tr>';
			$html .= '<td>' . $empleado->getNroLegajo() . '</td>';
			$html .= '<td>' . $empleado->getPersona()->getCuil() . '</td>';	
			$html .= '<td>' . $empleado->getPersona()->getApellido() . '</td>';	
			$html .= '<td>' . $empleado->getPersona()->getNombre() . '</td>';	
			$html .= '<td>' . $this->getDatos649TotalRubro1($empleado, $anio) . '</td>';	
			$html .= '</tr>';
		}
			
		$html .= '</table>';
		return new Response($html);
	}

    /**
     * 
     * @param Empleado $empleado
     * @param type $anio
     * @param type $redirect
     * @return string
     */
    private function getDatos649(Empleado $empleado, $anio, $redirect = false, 
		$returnConceptosF649 = false, $returnFormulario572 = false, $returnConceptosLiquidacion = false
	) {
        $html = '';

        //datos adif
        $datosADIF = array(
            'nombre' => 'ADIF S.E',
            'cuil' => '30-71069599-3',
            'direccion' => 'AV. RAMOS MEJIA 1302 - Capital Federal'
        );

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        //fecha y motivo de f649
        $fechaInicio = ($empleado->getFechaIngreso() > (new DateTime(date($anio . '-01-01')))) ? $empleado->getFechaIngreso()->format('d.m.Y') : '01.01.' . $anio;
        $motivo = 'Anual';
        $fechaFin = '31.12.' . $anio;
        $mes = 12;
        $mesDeduccionesPersonales = 12;
        if (($empleado->getFechaEgreso() !== null) && ( $empleado->getFechaEgreso()->format('Y') === $anio)) {
            $motivo = 'Baja';
            $fechaFin = $empleado->getFechaEgreso()->format('d.m.Y');
            $mes = $empleado->getFechaEgreso()->format('n');
            $mesDeduccionesPersonales = ($empleado->getAplicaEscalaDiciembre()) ? 12 : $empleado->getFechaEgreso()->format('n');
        }
        
        $mesF572 = $mes;
        if ($empleado->getFechaEgreso() != null) {
            $mesF572 = 12;
        }
        
        $liqEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
            ->getLiquidacionEmpleadoByEmpleadoAndAnio($empleado, $anio);
        
        if ($liqEmpleado) {

            $netoAcumulado = 0;
            $jubilacion = 0;
            $obra_social = 0;
            $ley_19032 = 0;
            $cuota_sindical = 0;
            $apdfa_cuota_sindical = 0;
            $apoc_cuota_sindical = 0;
            $promocion = 0;
            $retenidoAnual = 0;
            $monto_SAC = 0;
            $otro_empleador = 0;
            
            $concepto_998_2014 = 0;
            $concepto_994_diciembre = 0;
            $sac_2 = 0;
            $otro_empleador_649 = 0;
			$montoRemuneracionesExentasIG = 0;
			$brutoJunio2016 = 0;
            $indemizacionAntiguedad = 0;
			
			$legajosEmpleadosBlanqueoBienes2016 = array(1497,1449,1532,1458,1226,1450,1454,1503,1554,1393,1553,1193,6,1697,1091,1105,1082,1309,1586,1590,1580,1164,1396,1357,1083,1065,1104,1194,1588,1364,1584,1407,1267,1642,1368,1519,1490,1177,1085,1048,1574,1171,1411,1610,1154,1455,1026,1562,1216,1067,1124,1047,12);
			$legajosEmpleadosBlanqueoBienes2016Dic2016 = array(1444,1707,1578,1348,1587,1231,1079,1316,1229,1280,1049,1252,1593,1246,1399,1573,1448,1698,1106,1044,1156);
			$legajosEmpleadosBlanqueoBienes2016 = array_merge($legajosEmpleadosBlanqueoBienes2016,$legajosEmpleadosBlanqueoBienes2016Dic2016);
			
			$haberNetoGananciaDic2016 = 0;
			$gratificacionEspecialExtraordinaria = 0;

            $f649 = $empleado->getFormulario649();
            $f572 = $empleado->getFormulario572($anio);
            
            //if ($f649 != null) {
			if ($f649 != null && $anio == $f649->getFechaFormulario()->format('Y')) {
                $otro_empleador_649 = $f649->getGananciaAcumulada();
                $netoAcumulado += $otro_empleador_649;
                $retenidoAnual += $f649->getTotalImpuestoDeterminado();
            }
            
            //$retenidoAnual += $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_RETENCION_OTROS_EMPLEADOR);

            foreach ($liqEmpleado as $liquidacionEmpleado) {
                /* @var $liquidacionEmpleado LiquidacionEmpleado */
                //conceptos de liquidacion
                $conceptosLiq = $liquidacionEmpleado->getLiquidacionEmpleadoConceptos();
                $netoAcumulado += $liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2() + $liquidacionEmpleado->getNoRemunerativo();
                $jubilacion += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_JUBILACION) + $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_1011);
                $obra_social += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_OBRA_SOCIAL_3) + $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_1012);
                $ley_19032 += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_LEY_19032);
                $cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_CUOTA_SINDICAL_UF);
                $apdfa_cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_APDFA_CUOTA_SINDICAL);
                $apoc_cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_CUOTA_SINDICAL_APOC);
                $retenidoAnual += ($liquidacionEmpleado->getGananciaEmpleado() !== null) ? $liquidacionEmpleado->getGananciaEmpleado()->getSaldoImpuestoMes() : 0;
                if ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC) {
                    $monto_SAC += $liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2() + $liquidacionEmpleado->getNoRemunerativo();
                }
                if ($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Y') == 2014) {
                    if (($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('n') <= 8) && ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__HABITUAL)) {
                        $concepto_998_2014 += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_998);
                    }
                }
                if (($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('n') == 12) && ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC)) {
                    $concepto_994_diciembre = $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_AJUSTE_GANANCIAS_SAC);
                    $sac_2 = ($concepto_994_diciembre != 0) ? $liquidacionEmpleado->getNeto() : 0;
                }
                
                //devolucion
                $retenidoAnual += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_DEVOLUCION_649);
				
                #################################
                ## IG PARTICULARIDADES POR AÑO ##
                #################################
				if ($anio == 2016) {
					/**
					* Le resto del neto acumulado los siguientes conceptos que no aplican IG 2016:
					* 0065 Ajuste Paritarias Año 2016 cuota 2/3
					* 65.1 Ajuste Paritarias Año 2016 cuota 2/2
					* 65.2 Ajuste Paritarias Año 2016 cuota 3/3
					* 66 Suma no remunerativa 20 de Julio
					* gluis - 07/12/2016
					*/
					$novedadesAjustesParitarias2016 = array('65', '65.1', '65.2', '66');
					$montoRemuneracionesExentasIG += $this->getMontoConceptoLiquidacionArray($conceptosLiq, $novedadesAjustesParitarias2016);
					if (in_array($empleado->getNroLegajo(), $legajosEmpleadosBlanqueoBienes2016)  
							&& $liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('m') == 6 
							&& $liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC ) {
								// El empleado presento blanqueo de bienes y es sueldo SAC mes 6
								$brutoJunio2016 = $liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2();
								//$jubilacion -= $brutoJunio2016 * 0.14; // Le resto el 14% de jubilacion del SAC
								//$obra_social -= $brutoJunio2016 * 0.03; // Le resto el 3% de obra social del SAC
								
								// Le resto estos conceptos para este mes
								$jubilacion -= $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_JUBILACION) + $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_1011);
								$obra_social -= $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_OBRA_SOCIAL_3) + $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_1012);
								$ley_19032 -= $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_LEY_19032);
								$cuota_sindical -= $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_CUOTA_SINDICAL_UF);
								$apdfa_cuota_sindical -= $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_APDFA_CUOTA_SINDICAL);
								
					}
					
					if ($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('m') == 12 
						&& $liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC) {
							$haberNetoGananciaDic2016 = $liquidacionEmpleado->getHaberNetoGanancia();
						}
				}
                
                if ($anio == 2017) {
                    $montoRemuneracionesExentasIG += $this->getMontoExentosIG($conceptosLiq);
                }
                
                #####################################
                ## FIN IG PARTICULARIDADES POR AÑO ##
                #####################################
				
				// Para todos los F649 y para todos los años, resto el concepto 99.1
				$gratificacionEspecialExtraordinaria += $this->getMontoConceptoLiquidacion($conceptosLiq, '99.1');
                
                if ($empleado->getFechaEgreso() != null) {
                    $indemizacionAntiguedad += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_INDEMIZACION_ANTIGUEDAD);
                }
            }
            
            $netoAcumulado -= $brutoJunio2016;
            $netoAcumulado -= $indemizacionAntiguedad;
            $netoAcumulado -= $gratificacionEspecialExtraordinaria;
            $netoAcumulado -= $montoRemuneracionesExentasIG;
            			
            if ($anio != (new DateTime())->format("Y")) {
				if ($anio <= 2015) {
					$rangoRemuneracion = $this->get('adif.empleado_historico_rango_remuneracion_service')->getRangoRemuneracionByEmpleadoAndAnio($empleado, $anio);
				} else {
					// A partir del año 2016 el rango de remuneracion es el ID = 19
					$rangoRemuneracion = $empleado->getRangoRemuneracion();
				}
            } else {
                $rangoRemuneracion = $empleado->getRangoRemuneracion();
            }
            
            if ($rangoRemuneracion == null) {
                die($empleado->getId() . ' - ' . $empleado->getPersona()->getNombreCompleto() . ' no tienen rango de remuneracion para el año ' . $anio);
            }

			
			$esBeneficiarioDecretoSAC2016 = false;
			if ($anio == 2016) {
				// Me fijo si fue beneficiario del decreto 2016 de excension de ganancias SAC 2016
				$esBeneficiarioDecretoSAC2016 = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getEsBeneficiarioDecretoSACDiciembre2016($empleado);
			}
            
            $conceptosLiquidacion = array(
                'netoAcumulado' => $netoAcumulado,
                'jubilacion' => $jubilacion,
                'obra_social' => $obra_social,
                'ley19032' => $ley_19032,
                'cuota_sindical' => $cuota_sindical,
                'apdfa_cuota_sindical' => $apdfa_cuota_sindical,
                'apoc_cuota_sindical' => $apoc_cuota_sindical,
                'retenidoAnual' => $retenidoAnual,
                'promocion' => $promocion,
                'monto_SAC' => $monto_SAC,
                'otro_empleador_649' => $otro_empleador_649,
				'esBeneficiarioDecretoSAC2016' => $esBeneficiarioDecretoSAC2016,
				'montoSACDiciemnbre2016' => $haberNetoGananciaDic2016,
            );
            
            
            $otro_empleador = $this->getMontoRemuneracionesOtrosEmpleador($f572, ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR);
            $otro_empleador -= $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_JUBILACION_OTRO_EMPLEADOR);
            $otro_empleador -= $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_OBRA_SOCIAL_OTRO_EMPLEADOR);
            $otro_empleador -= $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_SINDICAL_OTRO_EMPLEADOR);
//            
            
            $otro_empleador += $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_AJUSTE_REINTEGRO);
            $otro_empleador += $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_AJUSTE_RETROACTIVO);
            $netoAcumulado += $otro_empleador;
			
			$anioTope = ($anio <= 2015) ? null : $anio;

			$valorReferenciaPorcentaje = null;
            //datos del f572  
            $formulario572 = array(
                //orden 0
                //'SAC_primer_semestre' => $monto_SAC,
                'otro_empleador' => $otro_empleador,
                'percepciones' => ($f572 != null) ? $f572->getPercepciones() : 0,
                //orden 1
				// determinarTopeConceptoF572($f572, $codigo, $rangoRemuneracion, $mes, $valorReferenciaPorcentaje = null, $anio = null)
                'primas' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_PRIMAS_DE_SEGURO, $rangoRemuneracion, $mesF572, $valorReferenciaPorcentaje, $anioTope),
                'sepelio' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_SEPELIO, $rangoRemuneracion, $mesF572, $valorReferenciaPorcentaje, $anioTope),
                'retiro' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_RETIRO, $rangoRemuneracion, $mesF572, $valorReferenciaPorcentaje, $anioTope),
                'domestico' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_SERVICIO_DOMESTICO, $rangoRemuneracion, $mesF572, $valorReferenciaPorcentaje, $anioTope),
                'jubilatorios' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_JUBILATORIO, $rangoRemuneracion, $mesF572, $valorReferenciaPorcentaje, $anioTope),
                'obra_social' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_OBRA_SOCIAL, $rangoRemuneracion, $mesF572, $valorReferenciaPorcentaje, $anioTope),
                'alquiler' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_ALQUILER, $rangoRemuneracion, $mesF572, $valorReferenciaPorcentaje, $anioTope),
                
                // orden 2
                'hipotecarios' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_HIPOTECARIO, $rangoRemuneracion, $mesF572, $valorReferenciaPorcentaje, $anioTope),
				
                //orden 3
                'conyuge' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_CONYUGE, $rangoRemuneracion, $mesDeduccionesPersonales, $anioTope),
                'hijos' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_HIJOS, $rangoRemuneracion, $mesDeduccionesPersonales, $anioTope),
                'otras_cargas' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_OTRAS_CARGAS, $rangoRemuneracion, $mesDeduccionesPersonales, $anioTope),
            );

            $conceptosF649['rubro1'] = $netoAcumulado;

            $deducciones = $jubilacion + $ley_19032 + $obra_social + $formulario572['obra_social'] + $formulario572['primas'] + $formulario572['sepelio'] + $formulario572['alquiler'];
            $otras_deducciones = $formulario572['retiro'] + $formulario572['domestico'] + $formulario572['jubilatorios'] + $formulario572['hipotecarios'] + $cuota_sindical + $apdfa_cuota_sindical + $apoc_cuota_sindical;
			$netoDonaciones = $netoAcumulado; 
			/*
			echo '<br>------<br>';
			var_dump("Neto acumulado = " . $netoAcumulado);
			echo '<br>------<br>';
			echo "Para el neto acumulado se le resta la suma de deducciones + otras_deducciones<br>";
			echo "Para el neto de donaciones solo le resto deducciones";
			echo '<br>------<br>';
			var_dump("deducciones = " . $deducciones);
			echo '<br>------<br>';
			var_dump("Otras deducciones = " . $otras_deducciones);
			echo '<br>------<br>';
			*/
            
            $netoAcumulado -= $deducciones + $otras_deducciones;
			$netoDonaciones -= ($deducciones + $cuota_sindical + $apdfa_cuota_sindical + $apoc_cuota_sindical);
            $formulario572['medica_asistencial'] = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_CUOTA_MEDICA_ASISTENCIAL, $rangoRemuneracion, $mes, $netoAcumulado, $anioTope);
            $formulario572['asistencia_sanitaria_medica_paramedica'] = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_ASISTENCIA_SANITARIA, $rangoRemuneracion, $mes, $netoAcumulado, $anioTope);

            $otras_deducciones += $formulario572['asistencia_sanitaria_medica_paramedica'];

            $conceptosF649['otras_deducciones'] = $otras_deducciones;
            $conceptosF649['rubro2'] = $deducciones + $formulario572['medica_asistencial'] + $otras_deducciones;
            $conceptosF649['rubro3'] = $conceptosF649['rubro1'] - $conceptosF649['rubro2'];
            
            
            echo '<br>-------------------<br>';
            echo 'Rubro 1: ' . $conceptosF649['rubro1'];
            echo '<br>-------------------<br>';
            echo 'Rubro 2: ' . $conceptosF649['rubro2'];
            echo '<br>-------------------<br>';
            echo 'Rubro 3: ' . $conceptosF649['rubro3'];
            
            
            /*
            $tipoLiquidacionHabitual = $em->getRepository('ADIFRecursosHumanosBundle:TipoLiquidacion')->find(1);
            
            $gananciaEmpleado = $em
                                ->getRepository('ADIFRecursosHumanosBundle:GananciaEmpleado')
                                ->getGananciaEmpleadoByEmpleadoYAnioYMesYTipoLiquidacion($empleado, $anio, 12, $tipoLiquidacionHabitual);
            echo '<br>-------------------<br>';
            echo 'Haber neto acumulado ganancia empleado: ' . $gananciaEmpleado->getHaberNetoAcumulado();
            */
            
            //$conceptosF649['rubro3'] = $gananciaEmpleado->getHaberNetoAcumulado();

            $formulario572['donaciones'] = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_DONACIONES, $rangoRemuneracion, $mes, $netoDonaciones, $anioTope);
			
			//var_dump("Donaciones = " . $formulario572['donaciones']);
			//exit;

            $conceptosF649['rubro4'] = $formulario572['donaciones'];
            $conceptosF649['rubro5'] = $conceptosF649['rubro3'] - $conceptosF649['rubro4'];

//            // deduccion especial y no imponible
//            if (!$rangoRemuneracion->getAplicaGanancias()) {
//                echo "1111111111111";
//                $deducciones_obligatorias['deduccion_especial'] = $conceptosF649['rubro5'] + $formulario572['conyuge'] + $formulario572['hijos'] + $formulario572['otras_cargas'];
//            } else {
//                echo "2222222222222222";
//                $deducciones_obligatorias['deduccion_especial'] = $this->getTopeConceptoGanancia(ConceptoGanancia::__CODIGO_DEDUCCION_ESPECIAL, $mesDeduccionesPersonales, $rangoRemuneracion, $anioTope);
//            }
            
            $deducciones_obligatorias['deduccion_especial'] = $this->getTopeConceptoGanancia(ConceptoGanancia::__CODIGO_DEDUCCION_ESPECIAL, $mesDeduccionesPersonales, $rangoRemuneracion, $anioTope);
            
			if ($anio == 2016) {
				// Me tengo que fijar por el decreto 1253/2016 SAC 2016 la deduccion especial 
				$deduccionEspecial2016 = $this->getTopeConceptoGananciaDeduccionEspecialAnio2016($empleado);
				if ($deduccionEspecial2016 != null) {
					$deducciones_obligatorias['deduccion_especial'] = $deduccionEspecial2016;
				}
			}
			
            $deducciones_obligatorias['minimo_no_imponible'] = $this->getTopeConceptoGanancia(ConceptoGanancia::__CODIGO_MINIMO_NO_IMPONIBLE, $mesDeduccionesPersonales, $rangoRemuneracion, $anioTope);

            echo '<br>---------------------------------<br>';
            echo 'Conyugue: ' . $formulario572['conyuge'] . '<br>';
            echo '<br>---------------------------------<br>';
            echo 'Hijos: ' . $formulario572['hijos'] . '<br>';
            echo '<br>---------------------------------<br>';
            echo 'Otras Cargas: ' . $formulario572['otras_cargas'] . '<br>';
            echo '<br>---------------------------------<br>';
            
            $conceptosF649['rubro6'] = $deducciones_obligatorias['deduccion_especial'] + $deducciones_obligatorias['minimo_no_imponible'] + $formulario572['conyuge'] + $formulario572['hijos'] + $formulario572['otras_cargas'];
            $conceptosF649['rubro7'] = $conceptosF649['rubro5'] - $conceptosF649['rubro6'] - $sac_2;

            $conceptosF649['rubro7'] = $conceptosF649['rubro7'] < 0 ? 0 : $conceptosF649['rubro7'];
			$escalaImpuesto = null;
//			if ($anio >= 2017) {
//				$escalaImpuesto = $em->getRepository('ADIFRecursosHumanosBundle:EscalaImpuesto')
//                    ->getEscalaImpuestoByMesYMontoYVigencia(12, $conceptosF649['rubro7']);
//			} else {
            
//            echo '<br>---------------------------------<br>';
//            echo 'Rubro 7 remuneracion sujeta impuesto: ' . $conceptosF649['rubro7'] . '<br>';
//            echo '<br>---------------------------------<br>';
            
				$fecha = $anio . '-12-01';
				$dtFecha = \DateTime::createFromFormat('Y-m-d', $fecha);
				$escalaImpuesto = $em->getRepository('ADIFRecursosHumanosBundle:EscalaImpuesto')
                    ->getEscalaImpuestoByMesYMontoYFecha(12, $conceptosF649['rubro7'], $dtFecha);
//			}
            

            $conceptosLiquidacion['retenidoAnual'] += $concepto_998_2014 + $concepto_994_diciembre;
            $conceptosF649['rubro8'] = $escalaImpuesto->getMontoFijo() + (($conceptosF649['rubro7'] - $escalaImpuesto->getMontoDesde()) * $escalaImpuesto->getPorcentajeASumar());
            $conceptosF649['rubro9'] = $conceptosLiquidacion['retenidoAnual'] + $conceptosLiquidacion['promocion'] + $formulario572['percepciones'];

			
            // calculo el 997
            $concepto_997 = 0;
            if ($conceptosF649['rubro8'] > $conceptosF649['rubro9']) {
                //falta ver el caso de que no supere el 35% del sueldo                
                $concepto_997 = $conceptosF649['rubro9'] - $conceptosF649['rubro8'];
            } else {
                if ($conceptosF649['rubro9'] > $conceptosF649['rubro8']) {
                    $concepto_997 = (($conceptosF649['rubro9'] - $conceptosF649['rubro8']) <= $conceptosLiquidacion['retenidoAnual']) ? $conceptosF649['rubro9'] - $conceptosF649['rubro8'] : $conceptosLiquidacion['retenidoAnual'];
                }
            }

            if ($concepto_997 < 0) {
                $conceptosLiquidacion['retenidoAnual'] += $formulario572['percepciones'];
                $formulario572['percepciones'] = 0;
            }

//            echo $conceptosLiquidacion['retenidoAnual'].'<br/>';
//            echo $conceptosF649['rubro8'].'<br/>';
//            echo $conceptosF649['rubro9'];
//            echo $concepto_997;
//            die;
            //recalculo después de aplicar el 997
            $conceptosLiquidacion['retenidoAnual'] -= $concepto_997;
            $conceptosF649['rubro9'] = $conceptosLiquidacion['retenidoAnual'] + $conceptosLiquidacion['promocion'] + $formulario572['percepciones'];

			$formulario572['percepciones'] = ($f572 != null) ? $f572->getPercepciones() : 0;
			
			/**
			* Al retenido anual (Rubro 9 inciso a "Retenciones efectuadas en el perÃ­odo fiscal que se liquida")
			* se le resta las percepciones que es el mismo rubro inciso c
			* Tomo el rubro 8 (rubro 8 == rubro 9) para la resta y fuerzo a que el retenido anual sea la diferencia
			* gluis - 30/05/2017
			*/
			$retenidoAnual = $conceptosF649['rubro8'] - $formulario572['percepciones'];
			if ($conceptosLiquidacion['retenidoAnual'] != $retenidoAnual) {
				$conceptosLiquidacion['retenidoAnual'] = $retenidoAnual;
			}
			
			
			if ($returnConceptosF649) {
				return $conceptosF649;
			}
			
			if ($returnFormulario572) {
				return $formulario572;
			}
			
			if ($returnConceptosLiquidacion) {
				return $conceptosLiquidacion;
			}
			
            //detalles de conceptos del f572
            $formulario572Detalle = array(
                'medica_asistencial' => $this->getDetalleF572($f572, ConceptoGanancia::__CODIGO_CUOTA_MEDICA_ASISTENCIAL, $rangoRemuneracion, $mes, $netoAcumulado),
                'primas' => $this->getDetalleF572($f572, ConceptoGanancia::__CODIGO_PRIMAS_DE_SEGURO, $rangoRemuneracion, $mes),
                'sepelio' => $this->getDetalleF572($f572, ConceptoGanancia::__CODIGO_SEPELIO, $rangoRemuneracion, $mes),
                'donaciones' => $this->getDetalleF572($f572, ConceptoGanancia::__CODIGO_DONACIONES, $rangoRemuneracion, $mes, $conceptosF649['rubro3']),
            );
            
            $rangoHistorico2015 = 'Sin datos';
            if ($anio < 2016) {
                $montoDesde = $rangoRemuneracion->getMontoDesde();
                $montoHasta = $rangoRemuneracion->getMontoHasta();
                if ($montoDesde == 0) {
                    $rangoHistorico2015 = 'Menor que $' . $montoHasta;
                } else if ($montoHasta > 9999998) {
                    $rangoHistorico2015 = 'Mayor que $' . $montoDesde;
                } else {
                    $rangoHistorico2015 = 'Entre $' . $montoDesde . ' y $' . $montoHasta;
                }
            }
			
            $html .= $this->renderView(
                    'ADIFRecursosHumanosBundle:Empleado:exportacion649.html.twig', array(
                    'empleado' => $empleado,
                    'adif' => $datosADIF,
                    'motivo' => $motivo,
                    'fechaInicio' => $fechaInicio,
                    'fechaFin' => $fechaFin,
                    'conceptosLiquidacion' => $conceptosLiquidacion,
                    'formulario572' => $formulario572,
                    'formulario572Detalle' => $formulario572Detalle,
                    'conceptosF649' => $conceptosF649,
                    'sac2' => $sac_2,
                    'deduccionesObligatorias' => $deducciones_obligatorias,
                    'anio' => $anio,
                    'rangoHistorico2015' => $rangoHistorico2015
                )
            );
        } elseif ($redirect) {
            return null;
        }
		//var_dump($conceptosLiquidacion['netoAcumulado'] );
		//die($html);

        return $html;
    }
	
	/**
     * 
     * @param Empleado $empleado
     * @param type $anio
     * @param type $redirect
     * @return string
     */
    private function getDatos649TotalRubro1(Empleado $empleado, $anio) {
        $html = '';

        //datos adif
        $datosADIF = array(
            'nombre' => 'ADIF S.E',
            'cuil' => '30-71069599-3',
            'direccion' => 'AV. RAMOS MEJIA 1302 - Capital Federal'
        );

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id = $empleado->getId();

        //fecha y motivo de f649
        $fechaInicio = ($empleado->getFechaIngreso() > (new DateTime(date($anio . '-01-01')))) ? $empleado->getFechaIngreso()->format('d.m.Y') : '01.01.' . $anio;
        $motivo = 'Anual';
        $fechaFin = '31.12.' . $anio;
        $mes = 12;
        $mesDeduccionesPersonales = 12;
        if (($empleado->getFechaEgreso() !== null) and ( $empleado->getFechaEgreso()->format('Y') === $anio)) {
            $motivo = 'Baja';
            $fechaFin = $empleado->getFechaEgreso()->format('d.m.Y');
            $mes = $empleado->getFechaEgreso()->format('n');
            $mesDeduccionesPersonales = ($empleado->getAplicaEscalaDiciembre()) ? 12 : $empleado->getFechaEgreso()->format('n');
        }

        $liqEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
                ->createQueryBuilder('le')
                ->select('le, e, l')
                ->innerJoin('le.empleado', 'e')
                ->innerJoin('le.liquidacion', 'l')
                ->where('YEAR(l.fechaCierreNovedades) = :anio')
                ->andWhere('e.id = :idEmpleado')
                ->setParameter('idEmpleado', $id)
                ->setParameter('anio', $anio)
                ->getQuery()
                ->getResult();

        if ($liqEmpleado) {

            $netoAcumulado = 0;
            $jubilacion = 0;
            $obra_social = 0;
            $ley_19032 = 0;
            $cuota_sindical = 0;
            $apdfa_cuota_sindical = 0;
            $apoc_cuota_sindical = 0;
            $promocion = 0;
            $retenidoAnual = 0;
            $monto_SAC = 0;

            $concepto_998_2014 = 0;
            $concepto_994_diciembre = 0;
            $sac_2 = 0;
            $otro_empleador_649 = 0;

            $f649 = $empleado->getFormulario649();
            //if ($f649 != null) {
			if ($f649 != null && $anio == $f649->getFechaFormulario()->format('Y')) {
                $otro_empleador_649 = $f649->getGananciaAcumulada();
                $netoAcumulado += $otro_empleador_649;
                $retenidoAnual += $f649->getTotalImpuestoDeterminado();
            }

            foreach ($liqEmpleado as $liquidacionEmpleado) {
                /* @var $liquidacionEmpleado LiquidacionEmpleado */
                //conceptos de liquidacion
                $conceptosLiq = $liquidacionEmpleado->getLiquidacionEmpleadoConceptos();
                $netoAcumulado += $liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2() + $liquidacionEmpleado->getNoRemunerativo();
                $jubilacion += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_JUBILACION) + $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_1011);
                $obra_social += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_OBRA_SOCIAL_3) + $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_1012);
                $ley_19032 += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_LEY_19032);
                $cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_CUOTA_SINDICAL_UF);
                $apdfa_cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_APDFA_CUOTA_SINDICAL);
                $apoc_cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_CUOTA_SINDICAL_APOC);
                $retenidoAnual += ($liquidacionEmpleado->getGananciaEmpleado() !== null) ? $liquidacionEmpleado->getGananciaEmpleado()->getSaldoImpuestoMes() : 0;
                if ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC) {
                    $monto_SAC += $liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2() + $liquidacionEmpleado->getNoRemunerativo();
                }
                if ($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Y') == 2014) {
                    if (($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('n') <= 8) && ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__HABITUAL)) {
                        $concepto_998_2014 += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_998);
                    }
                }
                if (($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('n') == 12) && ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC)) {
                    $concepto_994_diciembre = $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_AJUSTE_GANANCIAS_SAC);
                    $sac_2 = ($concepto_994_diciembre != 0) ? $liquidacionEmpleado->getNeto() : 0;
                }
                //devolucion
                $retenidoAnual += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_DEVOLUCION_649);
            }

            $f572 = $empleado->getFormulario572($anio);

            if ($anio != (new DateTime())->format("Y")) {
                $rangoRemuneracion = $this->get('adif.empleado_historico_rango_remuneracion_service')->getRangoRemuneracionByEmpleadoAndAnio($empleado, $anio);
            } else {
                $rangoRemuneracion = $empleado->getRangoRemuneracion();
            }
            
            if ($rangoRemuneracion == null) {
                die($empleado->getId() . ' - ' . $empleado->getPersona()->getNombreCompleto() . ' no tienen rango de remuneracion para el año ' . $anio);
            }

            //$monto_SAC = $this->getMontoConceptoF572($f572, ConceptoGanancia::__CODIGO_SAC, $rangoRemuneracion);
            //$netoAcumulado += $monto_SAC;
            //conceptos de liquidacion
			
            $conceptosLiquidacion = array(
                'netoAcumulado' => $netoAcumulado,
                'jubilacion' => $jubilacion,
                'obra_social' => $obra_social,
                'ley19032' => $ley_19032,
                'cuota_sindical' => $cuota_sindical,
                'apdfa_cuota_sindical' => $apdfa_cuota_sindical,
                'apoc_cuota_sindical' => $apoc_cuota_sindical,
                'retenidoAnual' => $retenidoAnual,
                'promocion' => $promocion,
                'monto_SAC' => $monto_SAC,
                'otro_empleador_649' => $otro_empleador_649
            );

            $otro_empleador = $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR);
            $otro_empleador += $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_AJUSTE_REINTEGRO);
            $otro_empleador += $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_AJUSTE_RETROACTIVO);
            $netoAcumulado += $otro_empleador;

            //datos del f572
            $formulario572 = array(
                //orden 0
                //'SAC_primer_semestre' => $monto_SAC,
                'otro_empleador' => $otro_empleador,
                'percepciones' => ($f572 != null) ? $f572->getPercepciones() : 0,
                //orden 1
                'primas' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_PRIMAS_DE_SEGURO, $rangoRemuneracion, $mes),
                'sepelio' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_SEPELIO, $rangoRemuneracion, $mes),
                'retiro' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_RETIRO, $rangoRemuneracion, $mes),
                'domestico' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_SERVICIO_DOMESTICO, $rangoRemuneracion, $mes),
                'jubilatorios' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_JUBILATORIO, $rangoRemuneracion, $mes),
                'obra_social' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_OBRA_SOCIAL, $rangoRemuneracion, $mes),
                'hipotecarios' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_HIPOTECARIO, $rangoRemuneracion, $mes),
                //orden 3
                'conyuge' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_CONYUGE, $rangoRemuneracion, $mesDeduccionesPersonales),
                'hijos' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_HIJOS, $rangoRemuneracion, $mesDeduccionesPersonales),
                'otras_cargas' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_OTRAS_CARGAS, $rangoRemuneracion, $mesDeduccionesPersonales),
            );
			
			// Base de impuestos para el 2016
			if ($anio == 2016) {
				$liquidacionDic2016 = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
										->createQueryBuilder('le')
										->select('le, e, l, ge')
										->innerJoin('le.empleado', 'e')
										->innerJoin('le.liquidacion', 'l')
										->innerJoin('le.gananciaEmpleado', 'ge')
										->where('YEAR(l.fechaCierreNovedades) = :anio')
										->andWhere('e.id = :idEmpleado')
										->setParameter('idEmpleado', $id)
										->setParameter('anio', $anio)
										->orderBy('ge.id', 'DESC')
										->setMaxResults(1)
										->getQuery()
										->getOneOrNullResult();
											
				if ($liquidacionDic2016 != null && $liquidacionDic2016->getGananciaEmpleado() != null) {
					$netoAcumulado = $liquidacionDic2016->getGananciaEmpleado()->getHaberNetoAcumulado();
				}		
			}
			
			$conceptosF649['rubro1'] = $netoAcumulado;
			return $conceptosF649['rubro1'];
		}
		
		return '---';
    }
	

    /**
     * Genera el formulario 649
     *
     * @Route("/formulario649diferencias/{anio}", name="exportacionF649_diferencias")
     * @Method("GET")
     * @Security("has_role('ROLE_RRHH_ALTA_F649')")
     */
    public function exportarDiferenciasF649(Request $request, $anio) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleados = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->findAll();


        if (!$empleados) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $html = '<table><thead>'
                . '<tr>'
//                . '<th>Id</th>'
                . '<th>Legajo</th>'
                . '<th>CUIL</th>'
                . '<th>Empleado</th>'
                . '<th>Saldo</th>'
//                . '<th>Honorarios de servicios de asistencia sanitaria, médica y paramédica</th>'
//                . '<th>Impuestos sobre Créditos y Débitos en cuenta Bancaria</th>'
//                . '<th>Retenciones y Percepciones Aduaneras</th>'
//                . '<th>Pago a Cuenta - Compras en el Exterior</th>'
//                . '<th>Impuesto sobre los Movimientos de Fondos Propios o de Terceros</th>'
//                . '<th>Pago a Cuenta - Compra de Paquetes TurÃ­sticos</th>'
//                . '<th>Pago a Cuenta - Compra de Pasajes</th>'
//                . '<th>Pago a Cuenta - Compra de Moneda Extranjera para Turismo / Transf. al Exterior</th>'
//                . '<th>Pago a Cuenta - Adquisición de moneda extranjera para tenencia de billetes extranjeros en el paÃ­s</th>'
                . '</tr>'
                . '</thead><tbody>';

        foreach ($empleados as $empleado) {

            $html .=$this->getDatos649DiferenciaV2($empleado, $anio);
        }
        $html .= '</tbody></table>';

        return new Response($html);
    }
	
	/**
     * 
     * @param Empleado $empleado
     * @param type $anio
     * @param type $redirect
     * @return string
     */
    private function getDatos649DiferenciaV2(Empleado $empleado, $anio) 
	{
		$html = '';
		
		$conceptosF649 = $this->getDatos649($empleado, $anio, $redirect = false, $esDiferencia = true);
		//var_dump( $empleado->getId() );
		//var_dump($conceptosF649);
		//echo "<br>-----------<br>";
		
		$rubro8 = isset($conceptosF649['rubro8']) ? $conceptosF649['rubro8'] : 0;
		$rubro9 = isset($conceptosF649['rubro9']) ? $conceptosF649['rubro9'] : 0;
		
		echo "<br>----------<br>";
		echo "Empleado ID " . $empleado->getId();
		echo "Rubro 8 " . $rubro8;
		echo "Rubro 9 " . $rubro9;
		echo "<br>----------<br>";
		
		$html .= '<tr>';
//            $html .= '<td>' . $empleado->getId() . '</td>';
		$html .= '<td>' . $empleado->getNroLegajo() . '</td>';
		$html .= '<td>' . $empleado->getPersona()->getCuil() . '</td>';
		$html .= '<td>' . $empleado->getPersona()->__toString() . '</td>';
		$html .= '<td>' . number_format( ($rubro8 - $rubro9), 2) . '</td>';
		//anuales
//            $html .= '<td>' . number_format($formulario572['asistencia_sanitaria_medica_paramedica'], 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_IMPUESTOS_SOBRE_CREDITOS_Y_DEBITOS), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_PERCEPCIONES_Y_RETENCIONES_ADUANERAS), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_COMPRAS_EN_EL_EXTERIOR), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_IMPUESTOS_SOBRE_LOS_MOVIMIENTOS), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_COMPRA_DE_PAQUETES_TURISTICOS), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_COMPRA_DE_PASAJES), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_COMPRA_DE_MONEDA), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_ADQUISICION_DE_MONEDA), 2) . '</td>';

		$html .= '</tr>';
        

        return $html;
    }

    /**
     * 
     * @param Empleado $empleado
     * @param type $anio
     * @param type $redirect
     * @return string
     */
    private function getDatos649Diferencia(Empleado $empleado, $anio) {
        $html = '';

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id = $empleado->getId();

        //fecha y motivo de f649
        $fechaInicio = ($empleado->getFechaIngreso() > (new DateTime(date($anio . '-01-01')))) ? $empleado->getFechaIngreso()->format('d.m.Y') : '01.01.' . $anio;
        $motivo = 'Anual';
        $fechaFin = '31.12.' . $anio;
        $mes = 12;
        $mesDeduccionesPersonales = 12;
        if (($empleado->getFechaEgreso() !== null) and ( $empleado->getFechaEgreso()->format('Y') === $anio)) {
            $motivo = 'Baja';
            $fechaFin = $empleado->getFechaEgreso()->format('d.m.Y');
            $mes = $empleado->getFechaEgreso()->format('n');
            $mesDeduccionesPersonales = ($empleado->getAplicaEscalaDiciembre()) ? 12 : $empleado->getFechaEgreso()->format('n');
        }

        $liqEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
                ->createQueryBuilder('le')
                ->select('le, e, l')
                ->innerJoin('le.empleado', 'e')
                ->innerJoin('le.liquidacion', 'l')
                ->where('YEAR(l.fechaCierreNovedades) = :anio')
                ->andWhere('e.id = :idEmpleado')
                ->setParameter('idEmpleado', $id)
                ->setParameter('anio', $anio)
                ->getQuery()
                ->getResult();

        if ($liqEmpleado) {

            $netoAcumulado = 0;
            $jubilacion = 0;
            $obra_social = 0;
            $ley_19032 = 0;
            $cuota_sindical = 0;
            $apdfa_cuota_sindical = 0;
            $apoc_cuota_sindical = 0;
            $promocion = 0;
            $retenidoAnual = 0;
            $monto_SAC = 0;

            $concepto_998_2014 = 0;
            $concepto_994_diciembre = 0;
            $sac_2 = 0;

            $f649 = $empleado->getFormulario649();
            //if ($f649 != null) {
			if ($f649 != null && $anio == $f649->getFechaFormulario()->format('Y')) {
                $netoAcumulado += $f649->getGananciaAcumulada();
                $retenidoAnual += $f649->getTotalImpuestoDeterminado();
            }

            foreach ($liqEmpleado as $liquidacionEmpleado) {
                /* @var $liquidacionEmpleado LiquidacionEmpleado */
                //conceptos de liquidacion
                $conceptosLiq = $liquidacionEmpleado->getLiquidacionEmpleadoConceptos();
                $netoAcumulado += $liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2() + $liquidacionEmpleado->getNoRemunerativo();
                $jubilacion += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_JUBILACION) + $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_1011);
                $obra_social += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_OBRA_SOCIAL_3) + $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_1012);
                $ley_19032 += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_LEY_19032);
                $cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_CUOTA_SINDICAL_UF);
                $apdfa_cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_APDFA_CUOTA_SINDICAL);
                $apoc_cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_CUOTA_SINDICAL_APOC);
                $retenidoAnual += ($liquidacionEmpleado->getGananciaEmpleado() !== null) ? $liquidacionEmpleado->getGananciaEmpleado()->getSaldoImpuestoMes() : 0;
                if ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC) {
                    $monto_SAC += $liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2() + $liquidacionEmpleado->getNoRemunerativo();
                }
                if ($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Y') == 2014) {
                    if (($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('n') <= 8) && ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__HABITUAL)) {
                        $concepto_998_2014 += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_998);
                    }
                }
                if (($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('n') == 12) && ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC)) {
                    $concepto_994_diciembre = $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_AJUSTE_GANANCIAS_SAC);
                    $sac_2 = ($concepto_994_diciembre != 0) ? $liquidacionEmpleado->getNeto() : 0;
                }
            }

            $f572 = $empleado->getFormulario572($anio);
            $rangoRemuneracion = $empleado->getRangoRemuneracion();
            //$monto_SAC = $this->getMontoConceptoF572($f572, ConceptoGanancia::__CODIGO_SAC, $rangoRemuneracion);
            //$netoAcumulado += $monto_SAC;
            //conceptos de liquidacion
            $conceptosLiquidacion = array(
                'netoAcumulado' => $netoAcumulado,
                'jubilacion' => $jubilacion,
                'obra_social' => $obra_social,
                'ley19032' => $ley_19032,
                'cuota_sindical' => $cuota_sindical,
                'apdfa_cuota_sindical' => $apdfa_cuota_sindical,
                'apoc_cuota_sindical' => $apoc_cuota_sindical,
                'retenidoAnual' => $retenidoAnual,
                'promocion' => $promocion,
                'monto_SAC' => $monto_SAC
            );

            $otro_empleador = $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR);
            $otro_empleador += $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_AJUSTE_REINTEGRO);
            $otro_empleador += $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_AJUSTE_RETROACTIVO);
            $netoAcumulado += $otro_empleador;

            //datos del f572
            $formulario572 = array(
                //orden 0
                //'SAC_primer_semestre' => $monto_SAC,
                'otro_empleador' => $otro_empleador,
                'percepciones' => ($f572 != null) ? $f572->getPercepciones() : 0,
                //orden 1
                'primas' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_PRIMAS_DE_SEGURO, $rangoRemuneracion, $mes),
                'sepelio' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_SEPELIO, $rangoRemuneracion, $mes),
                'retiro' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_RETIRO, $rangoRemuneracion, $mes),
                'domestico' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_SERVICIO_DOMESTICO, $rangoRemuneracion, $mes),
                'jubilatorios' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_JUBILATORIO, $rangoRemuneracion, $mes),
                'obra_social' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_OBRA_SOCIAL, $rangoRemuneracion, $mes),
                'hipotecarios' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_HIPOTECARIO, $rangoRemuneracion, $mes),
                //orden 3
                'conyuge' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_CONYUGE, $rangoRemuneracion, $mesDeduccionesPersonales),
                'hijos' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_HIJOS, $rangoRemuneracion, $mesDeduccionesPersonales),
                'otras_cargas' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_OTRAS_CARGAS, $rangoRemuneracion, $mesDeduccionesPersonales),
            );

            $conceptosF649['rubro1'] = $netoAcumulado;

            $deducciones = $jubilacion + $ley_19032 + $obra_social + $formulario572['obra_social'] + $formulario572['primas'] + $formulario572['sepelio'];
            $otras_deducciones = $formulario572['retiro'] + $formulario572['domestico'] + $formulario572['jubilatorios'] + $formulario572['hipotecarios'] + $cuota_sindical + $apdfa_cuota_sindical + $apoc_cuota_sindical;
            $netoAcumulado -= ($deducciones + $otras_deducciones);

            $formulario572['medica_asistencial'] = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_CUOTA_MEDICA_ASISTENCIAL, $rangoRemuneracion, $mes, $netoAcumulado);
            $formulario572['asistencia_sanitaria_medica_paramedica'] = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_ASISTENCIA_SANITARIA, $rangoRemuneracion, $mes, $netoAcumulado);

            $otras_deducciones += $formulario572['asistencia_sanitaria_medica_paramedica'];

            $conceptosF649['otras_deducciones'] = $otras_deducciones;
            $conceptosF649['rubro2'] = $deducciones + $formulario572['medica_asistencial'] + $otras_deducciones;
            $conceptosF649['rubro3'] = $conceptosF649['rubro1'] - $conceptosF649['rubro2'];

            $formulario572['donaciones'] = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_DONACIONES, $rangoRemuneracion, $mes, $conceptosF649['rubro3']);

            $conceptosF649['rubro4'] = $formulario572['donaciones'];
            $conceptosF649['rubro5'] = $conceptosF649['rubro3'] - $conceptosF649['rubro4'];

            // deduccion especial y no imponible
            if (!$rangoRemuneracion->getAplicaGanancias()) {
                $deducciones_obligatorias['deduccion_especial'] = $conceptosF649['rubro5'] + $formulario572['conyuge'] + $formulario572['hijos'] + $formulario572['otras_cargas'];
            } else {
                $deducciones_obligatorias['deduccion_especial'] = $this->getTopeConceptoGanancia(ConceptoGanancia::__CODIGO_DEDUCCION_ESPECIAL, $mesDeduccionesPersonales, $rangoRemuneracion);
            }
            $deducciones_obligatorias['minimo_no_imponible'] = $this->getTopeConceptoGanancia(ConceptoGanancia::__CODIGO_MINIMO_NO_IMPONIBLE, $mesDeduccionesPersonales, $rangoRemuneracion);

            $conceptosF649['rubro6'] = $deducciones_obligatorias['deduccion_especial'] + $deducciones_obligatorias['minimo_no_imponible'] + $formulario572['conyuge'] + $formulario572['hijos'] + $formulario572['otras_cargas'];
            $conceptosF649['rubro7'] = $conceptosF649['rubro5'] - $conceptosF649['rubro6'] - $sac_2;

            $conceptosF649['rubro7'] = $conceptosF649['rubro7'] < 0 ? 0 : $conceptosF649['rubro7'];
            $escalaImpuesto = $em->getRepository('ADIFRecursosHumanosBundle:EscalaImpuesto')
                    ->getEscalaImpuestoByMesYMontoYVigencia(12, $conceptosF649['rubro7']);

            $conceptosLiquidacion['retenidoAnual'] += $concepto_998_2014 + $concepto_994_diciembre;
            $conceptosF649['rubro8'] = $escalaImpuesto->getMontoFijo() + (($conceptosF649['rubro7'] - $escalaImpuesto->getMontoDesde()) * $escalaImpuesto->getPorcentajeASumar());
            $conceptosF649['rubro9'] = $conceptosLiquidacion['retenidoAnual'] + $conceptosLiquidacion['promocion'] + $formulario572['percepciones'];

            $html .= '<tr>';
//            $html .= '<td>' . $empleado->getId() . '</td>';
            $html .= '<td>' . $empleado->getNroLegajo() . '</td>';
            $html .= '<td>' . $empleado->getPersona()->getCuil() . '</td>';
            $html .= '<td>' . $empleado->getPersona()->__toString() . '</td>';
            $html .= '<td>' . number_format(($conceptosF649['rubro8'] - $conceptosF649['rubro9']), 2) . '</td>';
            //anuales
//            $html .= '<td>' . number_format($formulario572['asistencia_sanitaria_medica_paramedica'], 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_IMPUESTOS_SOBRE_CREDITOS_Y_DEBITOS), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_PERCEPCIONES_Y_RETENCIONES_ADUANERAS), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_COMPRAS_EN_EL_EXTERIOR), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_IMPUESTOS_SOBRE_LOS_MOVIMIENTOS), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_COMPRA_DE_PAQUETES_TURISTICOS), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_COMPRA_DE_PASAJES), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_COMPRA_DE_MONEDA), 2) . '</td>';
//            $html .= '<td>' . number_format($this->getMontoPercepcionConceptoF572($f572, ConceptoGanancia::__CODIGO_ADQUISICION_DE_MONEDA), 2) . '</td>';

            $html .= '</tr>';
        }

        return $html;
    }

    private function getMontoPercepcionConceptoF572($f572, $codigo) {
        $total = 0;
        if ($f572 != null) {
            $conceptosF572 = $this->getConceptosFormulario572ByCodigo($f572, $codigo);
            if ($conceptosF572->count() > 0) {
                foreach ($conceptosF572 as $conceptoFormulario572) {
                    $total += $conceptoFormulario572->getMonto() * ($conceptoFormulario572->getMesHasta() - $conceptoFormulario572->getMesDesde() + 1);
                }
            }
        }
        return $total;
    }

    /**
     *
     * @Route("/devolucionGanancia/{anio}", name="devolucion_ganancia")
     * @Method("GET")
     * @Security("has_role('ROLE_RRHH_ALTA_F649')")
     */
    public function exportarDevolucionGanancia(Request $request, $anio) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleados = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')->findByActivo(1);


        if (!$empleados) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }

        $html = '<table><thead>'
                . '<tr>'
                . '<th>Legajo</th>'
                . '<th>CUIL</th>'
                . '<th>Empleado</th>'
                . '<th>Saldo</th>'
                . '</tr>'
                . '</thead><tbody>';

        foreach ($empleados as $empleado) {
            //$html .= $this->getDatos649Diferencia($empleado, $anio);
			$html .= $this->getDatos649DiferenciaV2($empleado, $anio);
        }
        $html .= '</tbody></table>';

        return new Response($html);
    }

    /**
     * 
     * @param Empleado $empleado
     * @param type $anio
     * @param type $redirect
     * @return type
     */
    private function getDevolucionGanancia(Empleado $empleado, $anio, $redirect = false) {
        $html = '';

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id = $empleado->getId();

        //fecha y motivo de f649
        $fechaInicio = ($empleado->getFechaIngreso() > (new DateTime(date($anio . '-01-01')))) ? $empleado->getFechaIngreso()->format('d.m.Y') : '01.01.' . $anio;
        $motivo = 'Anual';
        $fechaFin = '31.12.' . $anio;
        $mes = 12;
        $mesDeduccionesPersonales = 12;
        if (($empleado->getFechaEgreso() !== null) and ( $empleado->getFechaEgreso()->format('Y') === $anio)) {
            $motivo = 'Baja';
            $fechaFin = $empleado->getFechaEgreso()->format('d.m.Y');
            $mes = $empleado->getFechaEgreso()->format('n');
            $mesDeduccionesPersonales = ($empleado->getAplicaEscalaDiciembre()) ? 12 : $empleado->getFechaEgreso()->format('n');
        }

        $liqEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
                ->createQueryBuilder('le')
                ->select('le, e, l')
                ->innerJoin('le.empleado', 'e')
                ->innerJoin('le.liquidacion', 'l')
                ->where('YEAR(l.fechaCierreNovedades) = :anio')
                ->andWhere('e.id = :idEmpleado')
                ->setParameter('idEmpleado', $id)
                ->setParameter('anio', $anio)
                ->getQuery()
                ->getResult();

        if ($liqEmpleado) {
            $netoAcumulado = 0;
            $jubilacion = 0;
            $obra_social = 0;
            $ley_19032 = 0;
            $cuota_sindical = 0;
            $apdfa_cuota_sindical = 0;
            $apoc_cuota_sindical = 0;
            $promocion = 0;
            $retenidoAnual = 0;
            $monto_SAC = 0;

            $concepto_998_2014 = 0;
            $concepto_994_diciembre = 0;
            $sac_2 = 0;
            $otro_empleador_649 = 0;

            $f649 = $empleado->getFormulario649();
            //if ($f649 != null) {
			if ($f649 != null && $anio == $f649->getFechaFormulario()->format('Y')) {
                $otro_empleador_649 = $f649->getGananciaAcumulada();
                $netoAcumulado += $otro_empleador_649;
                $retenidoAnual += $f649->getTotalImpuestoDeterminado();
            }

            foreach ($liqEmpleado as $liquidacionEmpleado) {
                /* @var $liquidacionEmpleado LiquidacionEmpleado */
                //conceptos de liquidacion
                $conceptosLiq = $liquidacionEmpleado->getLiquidacionEmpleadoConceptos();
                $netoAcumulado += $liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2() + $liquidacionEmpleado->getNoRemunerativo();
                $jubilacion += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_JUBILACION) + $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_1011);
                $obra_social += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_OBRA_SOCIAL_3) + $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_1012);
                $ley_19032 += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_LEY_19032);
                $cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_CUOTA_SINDICAL_UF);
                $apdfa_cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_APDFA_CUOTA_SINDICAL);
                $apoc_cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_CUOTA_SINDICAL_APOC);
                $retenidoAnual += ($liquidacionEmpleado->getGananciaEmpleado() !== null) ? $liquidacionEmpleado->getGananciaEmpleado()->getSaldoImpuestoMes() : 0;
                if ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC) {
                    $monto_SAC += $liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2() + $liquidacionEmpleado->getNoRemunerativo();
                }
                if ($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Y') == 2014) {
                    if (($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('n') <= 8) && ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__HABITUAL)) {
                        $concepto_998_2014 += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_998);
                    }
                }
                if (($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('n') == 12) && ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC)) {
                    $concepto_994_diciembre = $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_AJUSTE_GANANCIAS_SAC);
                    $sac_2 = ($concepto_994_diciembre != 0) ? $liquidacionEmpleado->getNeto() : 0;
                }
                //devolucion
                $retenidoAnual += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_DEVOLUCION_649);
            }

            $f572 = $empleado->getFormulario572($anio);
            $rangoRemuneracion = $empleado->getRangoRemuneracion();
            //$monto_SAC = $this->getMontoConceptoF572($f572, ConceptoGanancia::__CODIGO_SAC, $rangoRemuneracion);
            //$netoAcumulado += $monto_SAC;
            //conceptos de liquidacion
            $conceptosLiquidacion = array(
                'netoAcumulado' => $netoAcumulado,
                'jubilacion' => $jubilacion,
                'obra_social' => $obra_social,
                'ley19032' => $ley_19032,
                'cuota_sindical' => $cuota_sindical,
                'apdfa_cuota_sindical' => $apdfa_cuota_sindical,
                'apoc_cuota_sindical' => $apoc_cuota_sindical,
                'retenidoAnual' => $retenidoAnual,
                'promocion' => $promocion,
                'monto_SAC' => $monto_SAC,
                'otro_empleador_649' => $otro_empleador_649
            );

            $otro_empleador = $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR);
            $otro_empleador += $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_AJUSTE_REINTEGRO);
            $otro_empleador += $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_AJUSTE_RETROACTIVO);
            $netoAcumulado += $otro_empleador;

            //datos del f572
            $formulario572 = array(
                //orden 0
                //'SAC_primer_semestre' => $monto_SAC,
                'otro_empleador' => $otro_empleador,
                'percepciones' => ($f572 != null) ? $f572->getPercepciones() : 0,
                //orden 1
                'primas' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_PRIMAS_DE_SEGURO, $rangoRemuneracion, $mes),
                'sepelio' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_SEPELIO, $rangoRemuneracion, $mes),
                'retiro' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_RETIRO, $rangoRemuneracion, $mes),
                'domestico' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_SERVICIO_DOMESTICO, $rangoRemuneracion, $mes),
                'jubilatorios' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_JUBILATORIO, $rangoRemuneracion, $mes),
                'obra_social' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_OBRA_SOCIAL, $rangoRemuneracion, $mes),
                'hipotecarios' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_HIPOTECARIO, $rangoRemuneracion, $mes),
                //orden 3
                'conyuge' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_CONYUGE, $rangoRemuneracion, $mesDeduccionesPersonales),
                'hijos' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_HIJOS, $rangoRemuneracion, $mesDeduccionesPersonales),
                'otras_cargas' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_OTRAS_CARGAS, $rangoRemuneracion, $mesDeduccionesPersonales),
            );

            $conceptosF649['rubro1'] = $netoAcumulado;

            $deducciones = $jubilacion + $ley_19032 + $obra_social + $formulario572['obra_social'] + $formulario572['primas'] + $formulario572['sepelio'];
            $otras_deducciones = $formulario572['retiro'] + $formulario572['domestico'] + $formulario572['jubilatorios'] + $formulario572['hipotecarios'] + $cuota_sindical + $apdfa_cuota_sindical + $apoc_cuota_sindical;
            $netoAcumulado -= ($deducciones + $otras_deducciones);

            $formulario572['medica_asistencial'] = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_CUOTA_MEDICA_ASISTENCIAL, $rangoRemuneracion, $mes, $netoAcumulado);
            $formulario572['asistencia_sanitaria_medica_paramedica'] = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_ASISTENCIA_SANITARIA, $rangoRemuneracion, $mes, $netoAcumulado);

            $otras_deducciones += $formulario572['asistencia_sanitaria_medica_paramedica'];

            $conceptosF649['otras_deducciones'] = $otras_deducciones;
            $conceptosF649['rubro2'] = $deducciones + $formulario572['medica_asistencial'] + $otras_deducciones;
            $conceptosF649['rubro3'] = $conceptosF649['rubro1'] - $conceptosF649['rubro2'];

            $formulario572['donaciones'] = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_DONACIONES, $rangoRemuneracion, $mes, $conceptosF649['rubro3']);

            $conceptosF649['rubro4'] = $formulario572['donaciones'];
            $conceptosF649['rubro5'] = $conceptosF649['rubro3'] - $conceptosF649['rubro4'];

            // deduccion especial y no imponible
            if (!$rangoRemuneracion->getAplicaGanancias()) {
                $deducciones_obligatorias['deduccion_especial'] = $conceptosF649['rubro5'] + $formulario572['conyuge'] + $formulario572['hijos'] + $formulario572['otras_cargas'];
            } else {
                $deducciones_obligatorias['deduccion_especial'] = $this->getTopeConceptoGanancia(ConceptoGanancia::__CODIGO_DEDUCCION_ESPECIAL, $mesDeduccionesPersonales, $rangoRemuneracion);
            }
            $deducciones_obligatorias['minimo_no_imponible'] = $this->getTopeConceptoGanancia(ConceptoGanancia::__CODIGO_MINIMO_NO_IMPONIBLE, $mesDeduccionesPersonales, $rangoRemuneracion);

            $conceptosF649['rubro6'] = $deducciones_obligatorias['deduccion_especial'] + $deducciones_obligatorias['minimo_no_imponible'] + $formulario572['conyuge'] + $formulario572['hijos'] + $formulario572['otras_cargas'];
            $conceptosF649['rubro7'] = $conceptosF649['rubro5'] - $conceptosF649['rubro6'] - $sac_2;

            $conceptosF649['rubro7'] = $conceptosF649['rubro7'] < 0 ? 0 : $conceptosF649['rubro7'];
            $escalaImpuesto = $em->getRepository('ADIFRecursosHumanosBundle:EscalaImpuesto')
                    ->getEscalaImpuestoByMesYMontoYVigencia(12, $conceptosF649['rubro7']);

            $conceptosLiquidacion['retenidoAnual'] += $concepto_998_2014 + $concepto_994_diciembre;
            $conceptosF649['rubro8'] = $escalaImpuesto->getMontoFijo() + (($conceptosF649['rubro7'] - $escalaImpuesto->getMontoDesde()) * $escalaImpuesto->getPorcentajeASumar());
            $conceptosF649['rubro9'] = $conceptosLiquidacion['retenidoAnual'] + $conceptosLiquidacion['promocion'] + $formulario572['percepciones'];

            // calculo el 997
            $concepto_997 = 0;
            if ($conceptosF649['rubro8'] > $conceptosF649['rubro9']) {
                //falta ver el caso de que no supere el 35% del sueldo                
                $concepto_997 = $conceptosF649['rubro9'] - $conceptosF649['rubro8'];
            } else {
                if ($conceptosF649['rubro9'] > $conceptosF649['rubro8']) {
                    $concepto_997 = (($conceptosF649['rubro9'] - $conceptosF649['rubro8']) <= $conceptosLiquidacion['retenidoAnual']) ? $conceptosF649['rubro9'] - $conceptosF649['rubro8'] : $conceptosLiquidacion['retenidoAnual'];
                }
            }

            $html .= '<tr>';
            $html .= '<td>' . $empleado->getNroLegajo() . '</td>';
            $html .= '<td>' . $empleado->getPersona()->getCuil() . '</td>';
            $html .= '<td>' . $empleado->getPersona()->__toString() . '</td>';
            $html .= '<td>' . number_format($concepto_997, 2) . '</td>';
            $html .= '</tr>';
        }
        return $html;
    }

    /**
     * 
     * @param Empleado $empleado
     * @param type $anio
     * @return string
     */
    public function getDevolucionGananciaAnual(Empleado $empleado, $anio) {

        $devolucionGananciaAnual = 0;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $id = $empleado->getId();

        $mes = 12;
        $mesDeduccionesPersonales = 12;
        if (($empleado->getFechaEgreso() !== null) and ( $empleado->getFechaEgreso()->format('Y') === $anio)) {
            $mes = $empleado->getFechaEgreso()->format('n');
            $mesDeduccionesPersonales = ($empleado->getAplicaEscalaDiciembre()) ? 12 : $empleado->getFechaEgreso()->format('n');
        }

        $liqEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
                ->createQueryBuilder('le')
                ->select('le, e, l')
                ->innerJoin('le.empleado', 'e')
                ->innerJoin('le.liquidacion', 'l')
                ->where('YEAR(l.fechaCierreNovedades) = :anio')
                ->andWhere('e.id = :idEmpleado')
                ->setParameter('idEmpleado', $id)
                ->setParameter('anio', $anio)
                ->getQuery()
                ->getResult();

        if ($liqEmpleado) {

            $netoAcumulado = 0;
            $jubilacion = 0;
            $obra_social = 0;
            $ley_19032 = 0;
            $cuota_sindical = 0;
            $apdfa_cuota_sindical = 0;
            $apoc_cuota_sindical = 0;
            $retenidoAnual = 0;
            $monto_SAC = 0;

            $concepto_998_2014 = 0;
            $concepto_994_diciembre = 0;
            $sac_2 = 0;
            $otro_empleador_649 = 0;

            $f649 = $empleado->getFormulario649();
            //if ($f649 != null) {
			if ($f649 != null && $anio == $f649->getFechaFormulario()->format('Y')) {
                $otro_empleador_649 = $f649->getGananciaAcumulada();
                $netoAcumulado += $otro_empleador_649;
                $retenidoAnual += $f649->getTotalImpuestoDeterminado();
            }

            foreach ($liqEmpleado as $liquidacionEmpleado) {
                /* @var $liquidacionEmpleado LiquidacionEmpleado */
                //conceptos de liquidacion
                $conceptosLiq = $liquidacionEmpleado->getLiquidacionEmpleadoConceptos();
                $netoAcumulado += $liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2() + $liquidacionEmpleado->getNoRemunerativo();
                $jubilacion += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_JUBILACION) + $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_1011);
                $obra_social += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_OBRA_SOCIAL_3) + $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_1012);
                $ley_19032 += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_LEY_19032);
                $cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_CUOTA_SINDICAL_UF);
                $apdfa_cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_APDFA_CUOTA_SINDICAL);
                $apoc_cuota_sindical += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_CUOTA_SINDICAL_APOC);
                $retenidoAnual += ($liquidacionEmpleado->getGananciaEmpleado() !== null) ? $liquidacionEmpleado->getGananciaEmpleado()->getSaldoImpuestoMes() : 0;
                if ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC) {
                    $monto_SAC += $liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2() + $liquidacionEmpleado->getNoRemunerativo();
                }
                if ($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('Y') == 2014) {
                    if (($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('n') <= 8) && ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__HABITUAL)) {
                        $concepto_998_2014 += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_998);
                    }
                }
                if (($liquidacionEmpleado->getLiquidacion()->getFechaCierreNovedades()->format('n') == 12) && ($liquidacionEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC)) {
                    $concepto_994_diciembre = $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_AJUSTE_GANANCIAS_SAC);
                    $sac_2 = ($concepto_994_diciembre != 0) ? $liquidacionEmpleado->getNeto() : 0;
                }
            }

            $f572 = $empleado->getFormulario572($anio);
            $rangoRemuneracion = $empleado->getRangoRemuneracion();
            //conceptos de liquidacion
            $conceptosLiquidacion = array(
                'netoAcumulado' => $netoAcumulado,
                'jubilacion' => $jubilacion,
                'obra_social' => $obra_social,
                'ley19032' => $ley_19032,
                'cuota_sindical' => $cuota_sindical,
                'apdfa_cuota_sindical' => $apdfa_cuota_sindical,
                'apoc_cuota_sindical' => $apoc_cuota_sindical,
                'retenidoAnual' => $retenidoAnual,
                'monto_SAC' => $monto_SAC,
                'otro_empleador_649' => $otro_empleador_649
            );

            $otro_empleador = $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR);
            $otro_empleador += $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_AJUSTE_REINTEGRO);
            $otro_empleador += $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_AJUSTE_RETROACTIVO);
            $netoAcumulado += $otro_empleador;

            //datos del f572
            $formulario572 = array(
                //orden 0
                //'SAC_primer_semestre' => $monto_SAC,
                'otro_empleador' => $otro_empleador,
                'percepciones' => ($f572 != null) ? $f572->getPercepciones() : 0,
                //orden 1
                'primas' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_PRIMAS_DE_SEGURO, $rangoRemuneracion, $mes),
                'sepelio' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_SEPELIO, $rangoRemuneracion, $mes),
                'retiro' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_RETIRO, $rangoRemuneracion, $mes),
                'domestico' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_SERVICIO_DOMESTICO, $rangoRemuneracion, $mes),
                'jubilatorios' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_JUBILATORIO, $rangoRemuneracion, $mes),
                'obra_social' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_OBRA_SOCIAL, $rangoRemuneracion, $mes),
                'hipotecarios' => $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_HIPOTECARIO, $rangoRemuneracion, $mes),
                //orden 3
                'conyuge' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_CONYUGE, $rangoRemuneracion, $mesDeduccionesPersonales),
                'hijos' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_HIJOS, $rangoRemuneracion, $mesDeduccionesPersonales),
                'otras_cargas' => $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_OTRAS_CARGAS, $rangoRemuneracion, $mesDeduccionesPersonales),
            );

            $conceptosF649['rubro1'] = $netoAcumulado;

            $deducciones = $jubilacion + $ley_19032 + $obra_social + $formulario572['obra_social'] + $formulario572['primas'] + $formulario572['sepelio'];
            $otras_deducciones = $formulario572['retiro'] + $formulario572['domestico'] + $formulario572['jubilatorios'] + $formulario572['hipotecarios'] + $cuota_sindical + $apdfa_cuota_sindical + $apoc_cuota_sindical;
            $netoAcumulado -= ($deducciones + $otras_deducciones);

            $formulario572['medica_asistencial'] = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_CUOTA_MEDICA_ASISTENCIAL, $rangoRemuneracion, $mes, $netoAcumulado);
            $formulario572['asistencia_sanitaria_medica_paramedica'] = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_ASISTENCIA_SANITARIA, $rangoRemuneracion, $mes, $netoAcumulado);

            $otras_deducciones += $formulario572['asistencia_sanitaria_medica_paramedica'];

            $conceptosF649['otras_deducciones'] = $otras_deducciones;
            $conceptosF649['rubro2'] = $deducciones + $formulario572['medica_asistencial'] + $otras_deducciones;
            $conceptosF649['rubro3'] = $conceptosF649['rubro1'] - $conceptosF649['rubro2'];

            $formulario572['donaciones'] = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_DONACIONES, $rangoRemuneracion, $mes, $conceptosF649['rubro3']);

            $conceptosF649['rubro4'] = $formulario572['donaciones'];
            $conceptosF649['rubro5'] = $conceptosF649['rubro3'] - $conceptosF649['rubro4'];

            // deduccion especial y no imponible
            if (!$rangoRemuneracion->getAplicaGanancias()) {
                $deducciones_obligatorias['deduccion_especial'] = $conceptosF649['rubro5'] + $formulario572['conyuge'] + $formulario572['hijos'] + $formulario572['otras_cargas'];
            } else {
                $deducciones_obligatorias['deduccion_especial'] = $this->getTopeConceptoGanancia(ConceptoGanancia::__CODIGO_DEDUCCION_ESPECIAL, $mesDeduccionesPersonales, $rangoRemuneracion);
            }
            $deducciones_obligatorias['minimo_no_imponible'] = $this->getTopeConceptoGanancia(ConceptoGanancia::__CODIGO_MINIMO_NO_IMPONIBLE, $mesDeduccionesPersonales, $rangoRemuneracion);

            $conceptosF649['rubro6'] = $deducciones_obligatorias['deduccion_especial'] + $deducciones_obligatorias['minimo_no_imponible'] + $formulario572['conyuge'] + $formulario572['hijos'] + $formulario572['otras_cargas'];
            $conceptosF649['rubro7'] = $conceptosF649['rubro5'] - $conceptosF649['rubro6'] - $sac_2;

            $conceptosF649['rubro7'] = $conceptosF649['rubro7'] < 0 ? 0 : $conceptosF649['rubro7'];
            $escalaImpuesto = $em->getRepository('ADIFRecursosHumanosBundle:EscalaImpuesto')
                    ->getEscalaImpuestoByMesYMontoYVigencia(12, $conceptosF649['rubro7']);

            $conceptosLiquidacion['retenidoAnual'] += $concepto_998_2014 + $concepto_994_diciembre;
            $conceptosF649['rubro8'] = $escalaImpuesto->getMontoFijo() + (($conceptosF649['rubro7'] - $escalaImpuesto->getMontoDesde()) * $escalaImpuesto->getPorcentajeASumar());
            $conceptosF649['rubro9'] = $conceptosLiquidacion['retenidoAnual'] + $formulario572['percepciones'];

            // calculo el 998.1
            $concepto_998_1 = 0;
            if ($conceptosF649['rubro8'] > $conceptosF649['rubro9']) {
                //falta ver el caso de que no supere el 35% del sueldo                
                $concepto_998_1 = $conceptosF649['rubro9'] - $conceptosF649['rubro8'];
            } else {
                if ($conceptosF649['rubro9'] > $conceptosF649['rubro8']) {
                    $concepto_998_1 = (($conceptosF649['rubro9'] - $conceptosF649['rubro8']) <= $conceptosLiquidacion['retenidoAnual']) ? $conceptosF649['rubro9'] - $conceptosF649['rubro8'] : $conceptosLiquidacion['retenidoAnual'];
                }
            }

//            echo $conceptosLiquidacion['retenidoAnual'].'<br/>';
//            echo $conceptosF649['rubro8'].'<br/>';
//            echo $conceptosF649['rubro9'];die;

            $devolucionGananciaAnual = $concepto_998_1 * (-1);
        }
        return round($devolucionGananciaAnual, 2);
    }
	
	/**
     * Exporta para todos los empleados, el rubro 8 "Total del Impuesto Determinado"
     * @Route("/exportarTotalImpuestoDeterminado/{anio}", name="impuesto_determinado")
     * @Method("GET")
     * @Security("has_role('ROLE_RRHH_ALTA_F649')")
     */
	public function exportarTotalImpuestoDeterminado($anio)
	{
		$em = $this->getDoctrine()->getManager($this->getEntityManager());

//        $legajos = array(1731);
		
        $empleados = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')
                ->createQueryBuilder('e')
				->innerJoin('e.liquidacionEmpleados', 'le')
				->innerJoin('le.liquidacion', 'li')
                ->innerJoin('e.persona', 'p')
                ->where('e.fechaEgreso IS NULL')
//				->andWhere('e.nroLegajo IN (:legajos)')
				->andWhere('YEAR(li.fechaCierreNovedades) = :anio')
                //->orWhere('e.fechaEgreso IS NOT NULL AND YEAR(e.fechaEgreso) > :anio')
                ->orderBy('p.apellido', 'ASC')
                ->setParameter('anio', $anio)
//				->setParameter('legajos', $legajos)
                ->getQuery()
                ->getResult();

        if (!$empleados) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }
		
		$html = '<table>
				<tr>
					<th>Legajo</th>
					<th>CUIL</th>
					<th>Apellido</th>
					<th>Nombre</th>
					<th>Total impuesto determinado</th>
                    <th>Total impuesto retenido</th>
                    <th>Pago a cuenta</th>
                    <th>Saldo</th>
				</tr>';
				
		foreach ($empleados as $i => $empleado) {
			//echo $i . "<br>------<br>";
			$html .= '<tr>';
			$html .= '<td>' . $empleado->getNroLegajo() . '</td>';
			$html .= '<td>' . $empleado->getPersona()->getCuil() . '</td>';	
			$html .= '<td>' . $empleado->getPersona()->getApellido() . '</td>';	
			$html .= '<td>' . $empleado->getPersona()->getNombre() . '</td>';
            if ($anio >= 2017) {
                $datos649 = $this->getDatos649Anio2018($empleado, $anio, $return = true);
                $html .= '<td>' . number_format($datos649['impuestoDeterminado'], 2, ',', '.') . '</td>';	
                $html .= '<td>' . number_format($datos649['retenidoAnual'], 2, ',', '.') . '</td>';	
                $html .= '<td>' . number_format($datos649['percepciones'], 2, ',', '.') . '</td>';	
                $html .= '<td>' . number_format($datos649['saldo'], 2, ',', '.') . '</td>';	
            } else {
                $datos649 = $this->getDatos649($empleado, $anio, $redirect = false, $returnConceptosF649 = true);
                $rubro8 = isset($datos649['rubro8']) ? $datos649['rubro8'] : 0;	
                $rubro8 = number_format($rubro8, 2, ',', '.');
                $html .= '<td>' . $rubro8 . '</td>';	
                $html .= '<td>0</td>';	// Total impuesto retenido: es para el nuevo formato del Form 649
                $html .= '<td>0</td>';	// Pago a cuenta: es para el nuevo formato del Form 649
                $html .= '<td>0</td>';	// Saldo: es para el nuevo formato del Form 649
            }
            
            $html .= '</tr>';
		}
			
		$html .= '</table>';
		return new Response($html);
	}
	
	/**
     * Exporta para todos los empleados, el rubro 9 inciso c "RegÃ­menes de percepción"
     * @Route("/exportarPercepciones649/{anio}", name="percepciones_649")
     * @Method("GET")
     * @Security("has_role('ROLE_RRHH_ALTA_F649')")
     */
	public function exportarPercepciones649($anio)
	{
		$em = $this->getDoctrine()->getManager($this->getEntityManager());

        $empleados = $em->getRepository('ADIFRecursosHumanosBundle:Empleado')
                ->createQueryBuilder('e')
                ->innerJoin('e.persona', 'p')
                ->where('e.fechaEgreso IS NULL')
                ->orWhere('e.fechaEgreso IS NOT NULL AND YEAR(e.fechaEgreso) > :anio')
                ->orderBy('p.apellido', 'ASC')
                ->setParameter('anio', $anio)
                ->getQuery()
                ->getResult();

        if (!$empleados) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Empleado.');
        }
		
		$html = '<table>
				<tr>
					<th>Legajo</th>
					<th>CUIL</th>
					<th>Apellido</th>
					<th>Nombre</th>
					<th>RegÃ­menes de percepción - Rubro 9 inciso c</th>
				</tr>';
				
		foreach ($empleados as $i => $empleado) {
			//echo $i . "<br>------<br>";
			$html .= '<tr>';
			$html .= '<td>' . $empleado->getNroLegajo() . '</td>';
			$html .= '<td>' . $empleado->getPersona()->getCuil() . '</td>';	
			$html .= '<td>' . $empleado->getPersona()->getApellido() . '</td>';	
			$html .= '<td>' . $empleado->getPersona()->getNombre() . '</td>';
			
			$datos572 = $this
				->getDatos649($empleado, $anio, $redirect = false, $returnConceptosF649 = false, 
					$returnFormulario572 = true);
			
			$percepciones = isset($datos572['percepciones']) ? $datos572['percepciones'] : 0;	
			$percepciones = number_format($percepciones, 2, ',', '.');
			$html .= '<td>' . $percepciones . '</td>';	
			$html .= '</tr>';
		}
			
		$html .= '</table>';
		return new Response($html);
	}
    
    /**
     * Nuevo formato del Formulario 649 a partir de 2017 para adelante
     * Si el @var $return es false, devuelve el template del form, si es true, devuelve el array con los valores calculados.
     * @param Empleado $empleado
     * @param int $anio
     * @param boolean $return
     * @return string
     */
    private function getDatos649Anio2018(Empleado $empleado, $anio, $return = false) {
        $html = '';

        //datos adif
        $datosADIF = array(
            'nombre' => 'ADIF S.E',
            'cuil' => '30-71069599-3',
            'direccion' => 'AV. RAMOS MEJIA 1302 - Capital Federal'
        );

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        
        $this->logger = new Logger('f649');

        $monologFormat = "%message%\n";
        $dateFormat = "Y/m/d H:i:s";
        $monologLineFormat = new LineFormatter($monologFormat, $dateFormat);

        $streamHandler = new StreamHandler($this->get('kernel')->getRootDir() . '/logs/f649_legajo_' . $empleado->getNroLegajo() . '_' . date('d_m_Y__H_i_s') . '.log', Logger::INFO);
        $streamHandler->setFormatter($monologLineFormat);

        $this->logger->pushHandler($streamHandler);
        
        $this->logger->info("Cálculo para Form 649 - año $anio");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info("------------------------------------------------------------------------------");
        $this->logger->info('Empleado ID: ' . $empleado->getId());
        $this->logger->info('Empleado legajo: ' . $empleado->getNroLegajo());
        $this->logger->info('Empleado apellido y nombre: ' . $empleado->__toString());
        $this->logger->info("------------------------------------------------------------------------------");
        
        
        $fechaHoy = new \DateTime();
 
        $liqEmpleado = $em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
            ->getLiquidacionEmpleadoByEmpleadoAndAnio($empleado, $anio);
        
        if ($liqEmpleado) {

            $remuneracionBruta = 0;
            $sacPrimeraCuota = 0;
            $sacSegundaCuota = 0;
            $remuneracionExenta = 0;
            $remuneracionExentaHsExtras = 0;
            $otroEmpleador649 = 0;
             
            $jubilacion = 0;
            $ley19032 = 0;
            $aportesObraSocial = 0;
            $cuotaSindical = 0;
            
            $retenidoAnual = 0;
            $saldo = 0;
            $retencionAnualGanancias = 0;
           
            $remuneraciones = array();
            $deducciones = array();
            $deduccionesArt23 = array();
            
            $f649 = $empleado->getFormulario649();
            $f572 = $empleado->getFormulario572($anio);
            
            $mes = 12;
            
            $rangoRemuneracion = $empleado->getRangoRemuneracion();
            
            if ($rangoRemuneracion == null) {
                die($empleado->getId() . ' - ' . $empleado->getPersona()->getNombreCompleto() . ' no tienen rango de remuneracion para el año ' . $anio);
            }
            
            foreach ($liqEmpleado as $liquidacionEmpleado) {
                /* @var $liquidacionEmpleado LiquidacionEmpleado */
                
                //conceptos de liquidacion
                $conceptosLiq = $liquidacionEmpleado->getLiquidacionEmpleadoConceptos();
                $liquidacion = $liquidacionEmpleado->getLiquidacion();
                
                $redondeoSac2 = 0;
                
                $this->logger->info("------------------------------------------------------------------------------");
                $this->logger->info('Liquidacion nro : ' . $liquidacion->getNumero() . ' mes ' . $liquidacion->getFechaCierreNovedades()->format('m') . ' año ' . $liquidacion->getFechaCierreNovedades()->format('Y'));
                $this->logger->info("Tipo: " . $liquidacion->getTipoLiquidacion()->getNombre());
                $this->logger->info("Bruto 1: " . $liquidacionEmpleado->getBruto1());
                $this->logger->info("Bruto 2: " . $liquidacionEmpleado->getBruto2());
                $this->logger->info("No remunerativos: " . $liquidacionEmpleado->getNoRemunerativo());
                
                $remuneracionBruta += $liquidacionEmpleado->getBruto1() + $liquidacionEmpleado->getBruto2() + $liquidacionEmpleado->getNoRemunerativo();
                
                if ($liquidacion->getFechaCierreNovedades()->format('Y') == 2017) {
                    if ($liquidacion->getFechaCierreNovedades()->format('n') == 12 && $liquidacion->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC) {
                        $redondeoSac2 = $liquidacionEmpleado->getRedondeo();
                    }
                }
                
                $this->logger->info("Redondeo SAC Diciembre 2017: " . $redondeoSac2);
                
                $remuneracionBruta += $redondeoSac2;
                
                $sacPrimeraCuota += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_SAC_1_SEMESTRE);
                $sacSegundaCuota += $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_SAC_2_SEMESTRE);
                
                $this->logger->info("SAC primera cuota: " . $sacPrimeraCuota);
                $this->logger->info("SAC segunda cuota: " . $sacSegundaCuota);
                
                $remuneracionExentaMes = $this->getMontoConceptoLiquidacionArray($conceptosLiq, array(
                    '94',   // Indemnización Antigüedad
                    '94.2', // Ind. Fallecimiento
                    '99.1', // Gratificación Especial Extraordinaria
                    '66.2'  // Ajuste SNR Acta 16/2/2018
                ));
                
                // Remuneración exenta (sin incluir horas extras)
                $remuneracionExenta += $remuneracionExentaMes;
                
                $this->logger->info("Remuneración exenta (sin incluir horas extras): " . $remuneracionExentaMes);
                
                // Remuneración exenta horas extras
                $remuneracionExentaHsExtras += $this->getMontoConceptoLiquidacionArray($conceptosLiq, array(
                    '30.3', // Plus 50% fin de sem
                    '30.6', // Plus Noct 50% fin de sem
                    '31.3', // Ajuste Plus 50% fin de sem
                    '32.2', // Plus 100% Inhábil/fin de sem/feriado
                    '32.4', // Plus Noct 100% Inhábil/fin de sem/feriado
                    '33.2', // Ajuste Plus 100% Inhábil/fin de sem/feriado
                    '32.6', // Ajuste Plus Hs extras Noct.100 %
                ));
                
                $jubilacionMes = $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_JUBILACION) + $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_1011);
                $ley19032Mes = $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_LEY_19032);
                $aportesObraSocialMes = $this->getMontoConceptoLiquidacionArray($conceptosLiq, array(
                    Concepto::__CODIGO_OBRA_SOCIAL_3,
                    Concepto::__CODIGO_1012, // Anssal
                    Concepto::__CODIGO_1011 // Familiar Adicional
                ));
                $cuotaSindicalMes = $this->getMontoConceptoLiquidacionArray($conceptosLiq, array(
                    Concepto::__CODIGO_CUOTA_SINDICAL_UF,
                    Concepto::__CODIGO_APDFA_CUOTA_SINDICAL,
                    Concepto::__CODIGO_CUOTA_SINDICAL_APOC
                ));
                
                $jubilacion += $jubilacionMes; 
                $ley19032 += $ley19032Mes;
                $aportesObraSocial += $aportesObraSocialMes;
                $cuotaSindical += $cuotaSindicalMes;
                
                $this->logger->info("Jubilacion: " . $jubilacionMes);
                $this->logger->info("Ley 19032: " . $ley19032Mes);
                $this->logger->info("Obra social: " . $aportesObraSocialMes);
                $this->logger->info("Cuota sindical: " . $cuotaSindicalMes);
                
                // @var float $saldoImpuestoMes es el concepto 999 de la liquidacion
                $saldoImpuestoMes = ($liquidacionEmpleado->getGananciaEmpleado() != null)
                        ? $liquidacionEmpleado->getGananciaEmpleado()->getSaldoImpuestoMes() 
                        : 0;
                
                $this->logger->info("Saldo impuesto mes (suma al IMPUESTO RETENIDO): " . $saldoImpuestoMes);
                
                $retenidoAnual += $saldoImpuestoMes;
            }
            
            $anioSiguiente = $anio + 1;
            $liqEmpleadoAnioSiguiente = $em
                ->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
                ->getLiquidacionEmpleadoByEmpleadoAndAnio($empleado, $anioSiguiente);
            
            
            if ($liqEmpleadoAnioSiguiente != null) {
                
                foreach ($liqEmpleadoAnioSiguiente as $liquidacionEmpleadoAnioSiguiente) {
                    // Sumo los conceptos 998.1 de las liquidaciones del año siguiente
                    
                    $conceptosLiq = $liquidacionEmpleadoAnioSiguiente->getLiquidacionEmpleadoConceptos();
                    $liquidacion = $liquidacionEmpleadoAnioSiguiente->getLiquidacion();
                    
                    $this->logger->info("------------------------------------------------------------------------------");
                    $this->logger->info('Liquidacion nro : ' . $liquidacion->getNumero() . ' mes ' . $liquidacion->getFechaCierreNovedades()->format('m') . ' año ' . $liquidacion->getFechaCierreNovedades()->format('Y'));
                    $retencionAnualGanancias = $this->getMontoConceptoLiquidacion($conceptosLiq, Concepto::__CODIGO_DEVOLUCION_649);
                    $this->logger->info("Retencion anual de ganancias para el año $anio - concepto " . Concepto::__CODIGO_DEVOLUCION_649 . " (suma al IMPUESTO RETENIDO): " . $retencionAnualGanancias);
                    $retenidoAnual += $retencionAnualGanancias;
                }
            }

            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("SUBTOTALES");
            $this->logger->info("REMUNERACION BRUTA: " . $remuneracionBruta);
            $this->logger->info("IMPUESTO RETENIDO: " . $retenidoAnual);
            
            if ($f649 != null && $anio == $f649->getFechaFormulario()->format('Y')) {
                $otroEmpleador649 = $f649->getGananciaAcumulada();
                $this->logger->info("Form 649 Inicial - Otros empleadores 649: " . $otroEmpleador649);
                $this->logger->info("Form 649 Inicial - Total impuesto determinado (suma al IMPUESTO RETENIDO): " . $f649->getTotalImpuestoDeterminado());
                $retenidoAnual += $f649->getTotalImpuestoDeterminado();
            }
            
            $retenidoPorOtrosEmpleadores = $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_RETENCION_OTROS_EMPLEADOR);
            $retenidoAnual += $retenidoPorOtrosEmpleadores;
            
            $this->logger->info("Retenido de IG de otros empleadores (suma al IMPUESTO RETENIDO): " . $retenidoPorOtrosEmpleadores);
            
            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("TOTALES");
            $this->logger->info("IMPUESTO RETENIDO TOTAL: " . $retenidoAnual);
            
            // Conceptos del F572
            $otroEmpleador572 = $this->getMontoRemuneracionesOtrosEmpleador($f572, ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR);
            $jubilacion572 = $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_JUBILACION_OTRO_EMPLEADOR);
            $obraSocial572 = $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_OBRA_SOCIAL_OTRO_EMPLEADOR);
            $sindical572 = $this->getMontoOtrosIngresosConceptoF572($f572, ConceptoGanancia::__CODIGO_SINDICAL_OTRO_EMPLEADOR);
            //$otroEmpleador572 -= $jubilacion572 + $obraSocial572 + $sindical572;
                        
            // Conceptos del F572 con topes
            $primas572 = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_PRIMAS_DE_SEGURO, $rangoRemuneracion, $mes, $valorReferenciaPorcentaje = null, $anio);
            $sepelio572 = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_SEPELIO, $rangoRemuneracion, $mes, $valorReferenciaPorcentaje = null, $anio);
            // y Retiro?
            $servicioDomestico572 = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_SERVICIO_DOMESTICO, $rangoRemuneracion, $mes, $valorReferenciaPorcentaje = null, $anio);
            
            $interesCreditosHipotecarios572 = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_HIPOTECARIO, $rangoRemuneracion, $mes, $valorReferenciaPorcentaje = null, $anio);
            $alquiler572 =  $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_ALQUILER, $rangoRemuneracion, $mes, $valorReferenciaPorcentaje = null, $anio);
            $aportesCajaPrevision = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_JUBILATORIO, $rangoRemuneracion, $mes, $valorReferenciaPorcentaje = null, $anio);
            
            //determinarTopeCargaFamiliarConceptoF572($f572, $codigo, $rangoRemuneracion, $mes, $anio = null)
            $conyuge572 = $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_CONYUGE, $rangoRemuneracion, $mes, $anio);
            $hijos572 = $this->determinarTopeCargaFamiliarConceptoF572($f572, ConceptoGanancia::__CODIGO_HIJOS, $rangoRemuneracion, $mes, $anio);
            // Conceptos de ganancias con topes
            $gananciaNoImponible = $this->getTopeConceptoGanancia(ConceptoGanancia::__CODIGO_MINIMO_NO_IMPONIBLE, $mes, $rangoRemuneracion, $anio);
            $deduccionEspecial = $this->getTopeConceptoGanancia(ConceptoGanancia::__CODIGO_DEDUCCION_ESPECIAL, $mes, $rangoRemuneracion, $anio);
            
            $remuneracionOtrosEmpleos = $otroEmpleador649 + $otroEmpleador572;
            
            // Seccion de Remuneraciones
            $remuneraciones['remuneracionBruta'] = $remuneracionBruta;
            $remuneraciones['retribucionesNoHabituales'] = 0; // No aplica
            $remuneraciones['sacPrimeraCuota'] = $sacPrimeraCuota;
            $remuneraciones['sacSegundaCuota'] = $sacSegundaCuota;
            $remuneraciones['remuneracionNoAlcanzada'] = 0; // No aplica
            $remuneraciones['remuneracionExenta'] = $remuneracionExenta;
            $remuneraciones['remuneracionExentaHsExtras'] = $remuneracionExentaHsExtras;
            $remuneraciones['remuneracionOtroEmpleos'] = $remuneracionOtrosEmpleos;
            $remuneraciones['remumeracionComputable'] = $remuneracionBruta - $remuneracionExenta  - $remuneracionExentaHsExtras + $remuneracionOtrosEmpleos;
            
            // Conceptos del F572 con topes porcentuales (remuneracion computable - deducciones grales)
            $deduccionesGenerales = $jubilacion + $ley19032 + $aportesObraSocial + $cuotaSindical;
            $deduccionesGenerales += $jubilacion572 + $obraSocial572 + $sindical572; // otros empleadores
            $deduccionesGenerales += $alquiler572 + $sepelio572 + $primas572 + $servicioDomestico572;
            
            $netoTopePorcentuales = $remuneraciones['remumeracionComputable'] - $deduccionesGenerales;
            
            $cuotasMedicas572 = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_CUOTA_MEDICA_ASISTENCIAL, $rangoRemuneracion, $mes, $netoTopePorcentuales, $anio);
            $donaciones572 = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_DONACIONES, $rangoRemuneracion, $mes, $netoTopePorcentuales, $anio);
            $asistenciaSanitaria572 = $this->determinarTopeConceptoF572($f572, ConceptoGanancia::__CODIGO_ASISTENCIA_SANITARIA, $rangoRemuneracion, $mes, $netoTopePorcentuales, $anio);
            // Fin de Conceptos del F572 con topes porcentuales
            
            // Seccion de Deducciones
            $deducciones['jubilacionYley19032'] = $jubilacion + $ley19032;
            $deducciones['aportesObraSocial'] = $aportesObraSocial;
            $deducciones['cuotaSindical'] = $cuotaSindical;
            $deducciones['aporteJubilatoriosOtrosEmpleos'] = $jubilacion572;
            $deducciones['aporteObraSocialOtrosEmpleos'] = $obraSocial572;
            $deducciones['cuotaSindicalOtrosEmpleos'] = $sindical572;
            $deducciones['cuotasMedicoAsistenciales'] = $cuotasMedicas572;
            $deducciones['primasSeguroCasosMuerte'] = $primas572;
            $deducciones['gastosSepelios'] = $sepelio572;
            $deducciones['gastosEstimativosCorredoresViajantes'] = 0; // No aplica
            $deducciones['donaciones'] = $donaciones572;
            $deducciones['descuentosLeyNacionalProvincialMunicipal'] = 0; // @TODO: preguntar de donde sacar este dato
            $deducciones['honorariosAsistenciaSanitaria'] = $asistenciaSanitaria572;
            $deducciones['interesesCreditosHipotecarios'] = $interesCreditosHipotecarios572;
            $deducciones['aportesCapitalSocialFondoRiesgo'] = 0; // @TODO: preguntar de donde sacar este dato
            $deducciones['empleadosServicioDomestico'] = $servicioDomestico572;
            $deducciones['aportesCajaPrevision'] = $aportesCajaPrevision;
            $deducciones['alquiler'] = $alquiler572;
            $deducciones['gastosMovilidad'] = 0; // No aplica
            $deducciones['otrasDeducciones'] = 0; // No aplica
            $deducciones['totalDeducciones'] = $jubilacion + $ley19032 + $aportesObraSocial + $cuotaSindical + $jubilacion572 + $obraSocial572; 
            $deducciones['totalDeducciones'] += $sindical572 + $cuotasMedicas572 + $primas572 + $sepelio572 + $donaciones572 + $asistenciaSanitaria572;
            $deducciones['totalDeducciones'] += $interesCreditosHipotecarios572 + $servicioDomestico572 + $alquiler572 + $aportesCajaPrevision;
            
            // Seccion de deducciones Art 23
            $deduccionesArt23['gananciaNoImponible'] = $gananciaNoImponible; // Ganancia no imponible o minimo no imponible
            $deduccionesArt23['deduccionEspecial'] = $deduccionEspecial;
            $deduccionesArt23['cargasFamilia'] = 0; // No aplica
            $deduccionesArt23['conyuge'] = $conyuge572;
            $deduccionesArt23['hijos'] = $hijos572;
            $deduccionesArt23['totalDeduccionesArt23'] = $gananciaNoImponible + $deduccionEspecial + $conyuge572 + $hijos572;
            
            // Me fijo el porcentaje de la categoria impuesto mes, del ultimo mes liquidado
            $remuneracionSujetaImpuesto = $remuneraciones['remumeracionComputable'] - $deducciones['totalDeducciones'] - $deduccionesArt23['totalDeduccionesArt23'];

            /** Calculo para porcentaje **/
           
            // Hay casos que puede dar negativa la rem. sujeta impuesto, entonces tiene que ser cero.
            $remuneracionSujetaImpuesto = ($remuneracionSujetaImpuesto < 0)
                    ? 0
                    : $remuneracionSujetaImpuesto;
            $fecha = $anio . '-12-01';
            $dtFecha = \DateTime::createFromFormat('Y-m-d', $fecha);
            $escalaImpuesto = $em->getRepository('ADIFRecursosHumanosBundle:EscalaImpuesto')
                    ->getEscalaImpuestoByMesYMontoYFecha(12, $remuneracionSujetaImpuesto, $dtFecha);
            
            $porcentaje = $escalaImpuesto->getPorcentajeASumar();
            $tramoAlicuotaAplicabale = 0; // @TODO: preguntar como sacar este caso
            $impuestoDeterminado = $escalaImpuesto->getMontoFijo() + (($remuneracionSujetaImpuesto - $escalaImpuesto->getMontoDesde()) * $escalaImpuesto->getPorcentajeASumar());
            $impuestoDeterminado = round($impuestoDeterminado, 2);
            
            // Si la diferencia entre el imp. determinado y el imp. retenido es menor o igual a $1,
            // igualo el retenido al determinado
            if (abs($impuestoDeterminado - $retenidoAnual) <= 1) {
                $retenidoAnual = $impuestoDeterminado;
            }
            
            $percepciones = ($f572 != null) ? $f572->getPercepciones() : 0;
            
            if ($impuestoDeterminado > 0) {
                $epsilon = 0.01;                
                $saldo = $impuestoDeterminado - ($retenidoAnual + $percepciones);                
                if (abs($saldo) < $epsilon) {
                    $saldo = 0;
                }
                
                if ($impuestoDeterminado > ($retenidoAnual + $percepciones) ) {
                    // Le sumo la diferencia al retenido si es menor o igual que 1
                    if ($saldo <= 1) {
                        $retenidoAnual += $saldo;
                        $saldo = 0;
                    }
                }
            }
            
            $this->logger->info("IMPUESTO DETERMINADO: " . $impuestoDeterminado);
            $this->logger->info("IMPUESTO RETENIDO: " . $retenidoAnual);
            $this->logger->info("PAGOS A CUENTA: " . $percepciones);
            $this->logger->info("SALDO: " . $saldo);
            
            $returnArray = array(
                    'empleado' => $empleado,
                    'anio' => $anio,
                    'adif' => $datosADIF,
                    'remuneraciones' => $remuneraciones,
                    'deducciones' => $deducciones,
                    'deduccionesArt23' => $deduccionesArt23,
                    'remuneracionSujetaImpuesto' => $remuneracionSujetaImpuesto, //Remuneracion sujeta impuesto o ganancia sujeto impuesto
                    'porcentaje' => $porcentaje,
                    'tramoAlicuotaAplicabale' => $tramoAlicuotaAplicabale,
                    'impuestoDeterminado' => $impuestoDeterminado,
                    'retenidoAnual' => $retenidoAnual,
                    'percepciones' => $percepciones,
                    'saldo' => $saldo
            );
            
            $html .= $this->renderView('ADIFRecursosHumanosBundle:Empleado:exportacion649_2018.html.twig', $returnArray);
        }
		
//		die($html);

        return (!$return) ? $html : $returnArray;
    }
    
    /**
     * Calcula los concepto ganancia "Retencion otros empleos" y lo deja aplicado para que
     * no se vuelva a liquidar y calculado para que aparezca en el excel de IG. 
     * Se usa para la liquidacion mensual
     * @param GananciaEmpleado $gananciaEmpleado
     * @param int $mes
     * @return float
     */
    private function calcularConceptoRetencionOtrosEmpleos($gananciaEmpleado, $mes) 
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $formulario572 = $gananciaEmpleado->getLiquidacionEmpleado()->getEmpleado()->getFormulario572();

        $conceptosFormulario572 = $em->getRepository('ADIFRecursosHumanosBundle:ConceptoFormulario572')
                ->getConceptosFormulario572ByOrdenAplicacionTipoConcepto($formulario572, 5);

        $total = 0;
        
        foreach ($conceptosFormulario572 as $conceptoFormulario572) {

            $montoASumar = $conceptoFormulario572->getMonto();
            
            $conceptoGanancia = $conceptoFormulario572->getConceptoGanancia();

            if ($conceptoFormulario572->getDetalleConceptoFormulario572Aplicado() != null) {
                if ($conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->getAplicado()) {
                    $montoASumar = 0;
                } else {
                    $montoASumar = $conceptoFormulario572->getMonto() - $conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->getMontoAplicado();
                    $conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->setMontoAplicado($montoASumar);
                    $conceptoFormulario572->getDetalleConceptoFormulario572Aplicado()->setAplicado(true);
                }
            }

            $total += $montoASumar;

            $this->logger->info("------------------------------------------------------------------------------");
            $this->logger->info("calcularConceptoRetencionOtrosEmpleos"); 
            $this->logger->info("Concepto ID: " . $conceptoGanancia->getId()); 
            $this->logger->info("Concepto Form 572 ID: " . $conceptoFormulario572->getId()); // 
            $this->logger->info("Concepto: " . $conceptoGanancia);
            $this->logger->info("Monto a sumar: $" . number_format($montoASumar, 2));
            $this->logger->info("------------------------------------------------------------------------------");

            $conceptoGananciaCalculado = new ConceptoGananciaCalculado();
            $conceptoGananciaCalculado
                    ->setConceptoGanancia($conceptoGanancia)
                    ->setMonto($montoASumar);

            $gananciaEmpleado->addConcepto($conceptoGananciaCalculado);
								
			
        }

        return $total;
    }
}
