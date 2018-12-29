<?php

namespace ADIF\RecursosHumanosBundle\Helper;

use ADIF\RecursosHumanosBundle\Controller\LiquidacionController;
use ADIF\RecursosHumanosBundle\Entity\TipoConcepto;
use ADIF\RecursosHumanosBundle\Entity\TipoLiquidacion;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Criteria;
use H2P\Converter\PhantomJS;
use H2P\TempFile;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;
use ADIF\ContableBundle\Entity\OrdenPagoSueldo;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use ADIF\RecursosHumanosBundle\Entity\Liquidacion;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Helper de cálculo de liquidación
 *
 * @author eprimost
 */
class DocumentoLiquidacionHelper {
    /* Códigos de resultados */

    const __TIPO_RESULT_OK = 'OK';
    const __TIPO_RESULT_ERROR = 'ERROR';

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
     * @var ContainerAware
     */
    private $container;

    function __construct(LiquidacionController $controller) {
        $this->controller = $controller;
        $this->em = $controller->getDoctrine()->getManager($controller->getEntityManager());
    }

    /**
     * 
     * @param ContainerAware $container
     * @param array $ids LiquidacionEmpleado a imprimir
     */
    public function imprimirRecibos($container, $ids = null, Liquidacion $liquidacionEnSession = null, $parametrosLiquidacionSession = array()) {
        $this->container = $container;

        $liqEmpleados = array();
        $esSession = false;
        if ($liquidacionEnSession == null) {
            $idLiquidacionEmpleados = json_decode($ids);

            $liqEmpleados = $this->em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
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
                    ->where('le.id IN (:idLiquidacionEmpleados)')
                    ->setParameter('idLiquidacionEmpleados', $idLiquidacionEmpleados)
                    ->orderBy('e.nroLegajo', 'ASC')
                    ->addOrderBy('le.id', 'DESC')
                    ->addOrderBy('cv.codigo * 1', 'ASC')
                    ->getQuery()
                    ->getResult();


            if (!$liqEmpleados) {
                throw $this->controller->createNotFoundException('No se puede encontrar la entidad LiquidacionEmpleado.');
            }
        } else {
            $liqEmpleados = $liquidacionEnSession->getLiquidacionEmpleados();
            $esSession = true;
        }

        $datosADIF = array(
            'cuil' => '30-71069599-3',
            'direccion' => 'AV. RAMOS MEJIA 1302 - Capital Federal'
        );

        $datosComunes = array(
            'adif' => $datosADIF,
            'tc_remunerativo' => TipoConcepto::__REMUNERATIVO,
            'tc_no_remunerativo' => TipoConcepto::__NO_REMUNERATIVO,
            'tc_aporte' => TipoConcepto::__APORTE,
            'tc_cuota_sindical_aportes' => TipoConcepto::__CUOTA_SINDICAL_APORTES,
            'tc_descuento' => TipoConcepto::__DESCUENTO,
            'tc_ganancias' => TipoConcepto::__CALCULO_GANANCIAS
        );

        $html = '<html><head><meta charset="utf-8"/><style type="text/css">' . $this->controller->renderView('ADIFRecursosHumanosBundle:Liquidacion:Recibo/recibo.css.twig') . '</style></head><body>';
        $i = 1;
        $dtFechaAutunno = new \DateTime('2016-02-29');
		$dtFechaCarro = new \DateTime('2017-05-29');
        foreach ($liqEmpleados as $i => $liqEmpleado) {
			//Agregando el correspondiente join con la clausula BETWEEN en el QueryBuilder no lograba hidratar correctamente las entidades.
			//Con este enfoque se filtran las subcategorías mediante SQL al cargarlas usando LazyLoading
			$criterioSubcategoriaDelPeriodo = Criteria::create()
							->where(Criteria::expr()->gt("fechaHasta", $liqEmpleado->getLiquidacion()->getFechaAlta()))
							->andWhere(Criteria::expr()->lte("fechaDesde", $liqEmpleado->getLiquidacion()->getFechaAlta()));

            $subcategoriaPeriodo = new ArrayCollection();
            if (!$liqEmpleado->getEmpleado()->getSubcategoriasHistoricas()->isEmpty()) {
                $subcategoriaPeriodo = $liqEmpleado->getEmpleado()->getSubcategoriasHistoricas()->matching($criterioSubcategoriaDelPeriodo);
            }
			//Agregando el correspondiente join con la clausula BETWEEN en el QueryBuilder no lograba hidratar correctamente las entidades.
			//Con este enfoque se filtran los tipos de contrato que no corresponden al período de la liquidacion
			$criterioTipoContratoDelPeriodo = Criteria::create()
							->where(Criteria::expr()->lte("fechaDesde", $liqEmpleado->getLiquidacion()->getFechaAlta()))
							->andWhere(Criteria::expr()->orX(
												Criteria::expr()->gt("fechaHasta", $liqEmpleado->getLiquidacion()->getFechaAlta()),
												//Si fechaHasta es NULL entonces el intervalo contiene el tipo de contrato actual
												Criteria::expr()->isNull("fechaHasta")));

            $tipoContratoPeriodo = new ArrayCollection();
            if (!$liqEmpleado->getEmpleado()->getTiposContrato()->isEmpty()) {
                $tipoContratoPeriodo = $liqEmpleado->getEmpleado()->getTiposContrato()->matching($criterioTipoContratoDelPeriodo);
            }
            $html .= $this->controller->renderView(
                    'ADIFRecursosHumanosBundle:Liquidacion:Recibo/recibo.html.twig', array_merge(
                            array(
				                'le' 					=> $liqEmpleado,
				                'firma_autunno' 		=> (
												$liqEmpleado->getLiquidacion()->getFechaCierreNovedades() > $dtFechaAutunno &&
											 	$liqEmpleado->getLiquidacion()->getFechaCierreNovedades() <= $dtFechaCarro 
										),
								'firma_carro' 			=> ($liqEmpleado->getLiquidacion()->getFechaCierreNovedades() > $dtFechaCarro),
				                'es_sac' 				=> $liqEmpleado->getLiquidacion()->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC,
				                'empleado' 				=> $liqEmpleado->getEmpleado(),
								//El empleado puede haber tenido otra subcategoría para el período de la liquidación solicitada
								'subcategoria_periodo' 	=> !$subcategoriaPeriodo->isEmpty() ? $subcategoriaPeriodo->first() : NULL,
								'tipo_contrato_periodo'	=> !$tipoContratoPeriodo->isEmpty() ? $tipoContratoPeriodo->first() : NULL,
                                'es_session'            => $esSession,
				                'lugarPagoSession'  	=> isset($parametrosLiquidacionSession['lugarPago']) ? $parametrosLiquidacionSession['lugarPago'] : null,
				                'fechaPagoSession'  	=> isset($parametrosLiquidacionSession['fechaPago']) ? $parametrosLiquidacionSession['fechaPago'] : null,
				                'anios_ant' 			=> $liqEmpleado->getEmpleado()->getAniosAntiguedad($liqEmpleado->getLiquidacion()->getFechaCierreNovedades()),
                            ), $datosComunes
                    )
            );

            $html .= (count($liqEmpleados) > $i++) ? '<div style="page-break-after: always;" />' : '';
        }

        $html .= '</body></html>';

        $filename = 'recibos_' . time() . '.pdf';

        $snappy = $this->controller->get('knp_snappy.pdf');
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
     * 
     * @param ContainerAware $container
     * @param array $idLiquidacion Liquidacion a imprimir
     */
    public function imprimirLibroSueldos($container, $idLiquidacion = null) {
        $this->container = $container;

        $liqEmpleados = $this->em->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')
                ->createQueryBuilder('le')
                ->select('le, e, p, s, sh, lec, cv, en, l, g, sg, c, etc, tc, b, con')
                ->innerJoin('le.empleado', 'e')
                ->innerJoin('le.liquidacion', 'l')
                ->innerJoin('le.liquidacionEmpleadoConceptos', 'lec')
                ->innerJoin('e.persona', 'p')
                ->innerJoin('e.idSubcategoria', 's')
				//1 - 1. sh sólo tendrá valores si la fecha de alta de la liquidacion cae entre fecha_desde y fecha_hasta de la subcategoría histórica
				->leftJoin('e.subcategoriasHistoricas', 'sh', \Doctrine\ORM\Query\Expr\JOIN::WITH, 'l.fechaAlta BETWEEN sh.fechaDesde AND sh.fechaHasta') 
                ->leftJoin('e.idGerencia', 'g')
                ->leftJoin('e.idSubgerencia', 'sg')
				//Al filtrar desde la condición del join, la lista de tipos de contrato que tuvo el empleado contendrá sólo un elemento, el que corresponde a la liquidación vinculada.
                ->innerJoin('e.tiposContrato', 'etc', \Doctrine\ORM\Query\Expr\JOIN::WITH, 'etc.fechaDesde <= l.fechaAlta AND (etc.fechaHasta > l.fechaAlta OR etc.fechaHasta IS NULL)')
                ->leftJoin('l.bancoAporte', 'b')
                ->innerJoin('lec.conceptoVersion', 'cv')
                ->leftJoin('lec.empleadoNovedad', 'en')
                ->leftJoin('s.idCategoria', 'c')
                ->innerJoin('etc.tipoContrato', 'tc')
                ->leftJoin('c.idConvenio', 'con')
                ->where('l.id = (:idLiquidacion) AND cv.imprimeRecibo = :imprimeRecibo')
                ->setParameter('idLiquidacion', $idLiquidacion)
                ->setParameter('imprimeRecibo', true)
                ->orderBy('e.nroLegajo', 'ASC')
                ->addOrderBy('cv.codigo * 1', 'ASC')
                ->getQuery()
                ->getResult();

        if (!$liqEmpleados) {
            throw $this->controller->createNotFoundException('No se puede encontrar la entidad LiquidacionEmpleado.');
        }

        $datosADIF = array(
            'cuil' => '30-71069599-3',
            'direccion' => 'AV. RAMOS MEJIA 1302 - Capital Federal'
        );

        $datosComunes = array(
            'adif' => $datosADIF,
            'tc_remunerativo' => TipoConcepto::__REMUNERATIVO,
            'tc_no_remunerativo' => TipoConcepto::__NO_REMUNERATIVO,
            'tc_aporte' => TipoConcepto::__APORTE,
            'tc_cuota_sindical_aportes' => TipoConcepto::__CUOTA_SINDICAL_APORTES,
            'tc_descuento' => TipoConcepto::__DESCUENTO,
            'tc_ganancias' => TipoConcepto::__CALCULO_GANANCIAS,
            'tl_sac' => TipoLiquidacion::__SAC,
            'tl_habitual' => TipoLiquidacion::__HABITUAL,
            'tl_adicional' => TipoLiquidacion::__ADICIONAL
        );

        $html = '<!doctype html><html>
            <head>
                <meta charset="utf-8"/>
                <style type="text/css">' . $this->controller->renderView('ADIFRecursosHumanosBundle:Liquidacion:LibroSueldos/libro_sueldos.css.twig') . '</style>
            </head>
            <body>';

        $fecha_cierre_novedades = $liqEmpleados[0]->getLiquidacion()->getFechaCierreNovedades();
        $tipo_liquidacion = $liqEmpleados[0]->getLiquidacion()->getTipoLiquidacion();

        $periodo_liquidacion = ($tipo_liquidacion->getId() == TipoLiquidacion::__SAC ?
                        ($fecha_cierre_novedades->format('n') > 1 ? '2' : '1') . 'º SAC' :
                        $fecha_cierre_novedades->format('F') )
                . ' ' . $fecha_cierre_novedades->format('Y');

        $i = 1;
        foreach ($liqEmpleados as $liqEmpleado) {
            $html .= $this->controller->renderView(
                    'ADIFRecursosHumanosBundle:Liquidacion:LibroSueldos/libro_sueldos.html.twig', array_merge(
                            array(
                'periodo_liquidacion' => $periodo_liquidacion,
                'le' => $liqEmpleado,
                'empleado' => $liqEmpleado->getEmpleado(),
				//El empleado puede haber tenido otra subcategoría para el período de la liquidación solicitada
				'subcategoria_periodo' => !empty($liqEmpleado->getEmpleado()->getSubcategoriasHistoricas()) ? $liqEmpleado->getEmpleado()->getSubcategoriasHistoricas()[0] : NULL,
                'anios_ant' => $liqEmpleado->getEmpleado()->getAniosAntiguedad($fecha_cierre_novedades),
                            ), $datosComunes
                    )
            );
        }

        $html .= '</body></html>';

        // die ($html);

        $filename = 'libro_sueldo_' . time() . '.pdf';

        $snappy = $this->controller->get('knp_snappy.pdf');
        $snappy->getInternalGenerator()->setTimeout(2 * 3600);
        $snappy->setOption('margin-top', '15mm');
        $snappy->setOption('quiet', true);
        $snappy->setOption('lowquality', false);
        $snappy->setOption('orientation', 'Landscape');

        return new Response(
                $snappy->getOutputFromHtml($html), 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                )
        );
    }

    /**
     * Genera el archivo de exportacion netcash
     * 
     * @param ContainerAware $container
     * @param integer $idLiquidacion
     * @param string $fecha_bbva
     * @param string $fecha_otros
     * @param string $fecha_venc_frances
     * @param string $fecha_venc_otros
     * @return Response Archivo NETCASH
     */
    public function exportarNetcash($container, $idLiquidacion, $fecha_bbva, $fecha_otros, $fecha_venc_frances, $fecha_venc_otros) {
        $this->container = $container;
        $em = $this->em;
        $emContable = $this->controller->getDoctrine()->getManager(EntityManagers::getEmContable());
        $siguienteNumeroOP = 0;

        $char_pad_string = ' ';
        $char_pad_int = '0';
        $char_pad_importe = ' ';
        $type_pad_string = STR_PAD_RIGHT;
        $type_pad_int = STR_PAD_LEFT;
        $type_pad_importe = STR_PAD_LEFT;

        //Formateo fechas
        $fecha_bbva = str_replace('/', '-', $fecha_bbva);
        $fecha_otros = str_replace('/', '-', $fecha_otros);
        $fecha_venc_frances = str_replace('/', '-', $fecha_venc_frances);
        $fecha_venc_otros = str_replace('/', '-', $fecha_venc_otros);

        $fecha_bbva = date('Ymd', strtotime($fecha_bbva));
        $fecha_otros = date('Ymd', strtotime($fecha_otros));
        $fecha_venc_frances = date('Ymd', strtotime($fecha_venc_frances));
        $fecha_venc_otros = date('Ymd', strtotime($fecha_venc_otros));


        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find($idLiquidacion);

        if (!$entity) {
            throw $this->controller->createNotFoundException('No se puede encontrar la entidad Liquidacion.');
        }

        //Datos reiterarivos
        $cad_idempresa = '36256';
        $cad_concepto = 'LIQUIDACION';
        $cad_cod_concepto = str_pad(strtoupper('HABERES'), 10, $char_pad_string, $type_pad_string); //HABERES/HONORARIOS/PRESTACION/S.A.C./TRANSF-ARS/VACACIONES/VIATICOS   
        //Creo cabecera
        /* 1 */ $cabecera = '2110'; //Código de Registro
        /* 2 */ $cabecera .= $cad_idempresa; // Nº Id empresa
        /* 3 */ $cabecera .= date("Ymd"); // Fecha creación fichero
        /* 4 */ $cabecera .= 'YYYYYYYY'; // Fecha deseada de proceso
        /* 5 */ $cabecera .= '0017'; // Banco emisor
        /* 6 */ $cabecera .= '0178'; // Sucursal cuenta de cargo
        /* 7 */ $cabecera .= '56'; // Dígito de control de la cuenta de cargo
        /* 8 */ $cabecera .= '0100020952'; // Número de cuenta de Cargo
        /* 9 */ $cabecera .= $cad_cod_concepto; // Código de Servicio
        /* 10 */ $cabecera .= 'ARS'; // Divisa de la cuenta
        /* 11 */ $cabecera .= '0'; // Indicador devolución
        /* 12 */ $cabecera .= 'XXXXXXXXXXXX'; // Nombre y Extension del fichero
        /* 13 */ $cabecera .= str_pad('ADIFSE', 36, $char_pad_string, $type_pad_string); // Nombre y apellido del emisor
        /* 14 */ $cabecera .= '20'; // Para poder convertir de CCC a CBU
        /* 15 */ $cabecera .= str_pad('', 141, $char_pad_string, $type_pad_string); // Relleno a blancos
        $cabecera .= chr(13) . chr(10);

        /* 1 */ $pie = '2910'; //Código de Registro
        /* 2 */ $pie .= $cad_idempresa; // Nº Id empresa
        /* 3 */ $pie .= str_pad(strval('XMONTOENTEROS'), 13, $char_pad_int, $type_pad_int); // Importe total fichero parte entera
        /* 4 */ $pie .= str_pad(strval('{}'), 2, $char_pad_int, $type_pad_int); // Importe total fichero parte decimal
        /* 5 */ $pie .= str_pad(strval('CANT2210'), 8, $char_pad_int, $type_pad_int); // Cantidad de registros 2210
        /* 6 */ $pie .= str_pad(strval('TOTREGISTS'), 10, $char_pad_int, $type_pad_int); // Cantidad de registros total (inc cabecera y pie)
        /* 7 */ $pie .= str_pad('', 208, $char_pad_string, $type_pad_string); // Relleno a blancos

        foreach ($entity->getLiquidacionEmpleados() as $liquidacionEmpleado) {
            $empleado = $liquidacionEmpleado->getEmpleado();
            $persona = $empleado->getPersona();

            if (($liquidacionEmpleado->getCbu() <> null) && ($liquidacionEmpleado->getNeto() > 0)) { //Si tiene cuenta asociada
                $cbu = $liquidacionEmpleado->getCbu();
                $cuenta = $liquidacionEmpleado->getBanco()->getId();

                //Contadores y Acumuladores                            
                $cad_idbeneficiario = str_pad(strval(str_replace('-', '', $persona->getCuil())), 18, $char_pad_int, $type_pad_int);
                $cad_idbeneficiario = str_pad($cad_idbeneficiario, 22, $char_pad_string, $type_pad_string);
                $cad_nro_factura = str_pad(strval(str_replace('-', '', $persona->getCuil())), 15, $char_pad_int, $type_pad_int);
                $cad_neto = explode(",", number_format($liquidacionEmpleado->getNeto(), 2, ',', ''));
//                $cad_localidad = $persona->getIdDomicilio()->getLocalidad();
//                $cad_provincia = $persona->getIdDomicilio()->getLocalidad()->getProvincia();
//                $cad_cp = $persona->getIdDomicilio()->getCodPostal();
                $cad_nya = substr($persona->__toString(), 0, 35);


                /* 1 */ $cadena_ind1 = '2210'; //Código de Registro
                /* 2 */ $cadena_ind1 .= $cad_idempresa; // Nº Id empresa
                /* 3 */ $cadena_ind1 .= str_pad('', 2, $char_pad_string, $type_pad_string); // Relleno a blancos
                /* 4 */ $cadena_ind1 .= $cad_idbeneficiario; // Nº id Beneficiario
                /* 5 */ $cadena_ind1 .= '1'; //Forma de pago
                /* 6 */ $cadena_ind1 .= $cbu; // CBU
                /* 7 */ $cadena_ind1 .= str_pad('0', 10, $char_pad_int, $type_pad_int); //Código de Registro
                /* 8 */ $cadena_ind1 .= str_pad(strval($cad_neto[0]), 13, $char_pad_int, $type_pad_int); //Importe Entero
                /* 9 */ $cadena_ind1 .= str_pad(strval($cad_neto[1]), 2, $char_pad_int, $type_pad_int); //Importe decimal
                /* 10 */ $cadena_ind1 .= str_pad('', 6, $char_pad_string, $type_pad_string); // Codigo devolucion


                if ($cuenta == 2) { //Registros del Banco Frances
                    /* 11 */
                    $cadena_ind1 .= $fecha_venc_frances; //Fecha vencimiento frances
                } else { //Registros de otros bancos
                    /* 11 */
                    $cadena_ind1 .= $fecha_venc_otros; //Fecha vencimiento otros bancos
                }


                /* 12 */ $cadena_ind1 .= $cad_nro_factura; //Número de factura
                /* 13 */ $cadena_ind1 .= str_pad('', 23, $char_pad_string, $type_pad_string); //Libre
                /* 14 */ $cadena_ind1 .= str_pad('', 1, $char_pad_string, $type_pad_string); //Cod estado dev domiciliaciones
                /* 15 */ $cadena_ind1 .= str_pad('', 40, $char_pad_string, $type_pad_string); //Descrip. devolucion
                /* 16 */ $cadena_ind1 .= str_pad('', 76, $char_pad_string, $type_pad_string); //Libre
                $cadena_ind1 .= chr(13) . chr(10);

                /* 1 */ $cadena_ind2 = '2220'; //Código de Registro
                /* 2 */ $cadena_ind2 .= $cad_idempresa; // Nº Id empresa 
                /* 3 */ $cadena_ind2 .= str_pad('', 2, $char_pad_string, $type_pad_string); // Relleno a blancos
                /* 4 */ $cadena_ind2 .= $cad_idbeneficiario; // Nº id Beneficiario
                /* 5 */ $cadena_ind2 .= str_pad($cad_nya, 36, $char_pad_string, $type_pad_string); // Beneficiario
                /* 6 */ $cadena_ind2 .= str_pad('', 36, $char_pad_string, $type_pad_string); // Domicilio
                /* 7 */ $cadena_ind2 .= str_pad('', 36, $char_pad_string, $type_pad_string); // Domicilio Continuacion
                /* 8 */ $cadena_ind2 .= str_pad('', 109, $char_pad_string, $type_pad_string); //Libre
                $cadena_ind2 .= chr(13) . chr(10);

                /* 1 */ $cadena_ind3 = '2230'; //Código de Registro
                /* 2 */ $cadena_ind3 .= $cad_idempresa; // Nº Id empresa
                /* 3 */ $cadena_ind3 .= str_pad('', 2, $char_pad_string, $type_pad_string); // Relleno a blancos 
                /* 4 */ $cadena_ind3 .= $cad_idbeneficiario; // Nº id Beneficiario
                /* 5 */ $cadena_ind3 .= str_pad('', 36, $char_pad_string, $type_pad_string); // Localidad
                /* 6 */ $cadena_ind3 .= str_pad('', 36, $char_pad_string, $type_pad_string); // Provincia
                /* 7 */ $cadena_ind3 .= str_pad('', 36, $char_pad_string, $type_pad_string); // CP
                /* 8 */ $cadena_ind3 .= str_pad('', 109, $char_pad_string, $type_pad_string); //Libre
                $cadena_ind3 .= chr(13) . chr(10);

                /* 1 */ $cadena_ind4 = '2240'; //Código de Registro
                /* 2 */ $cadena_ind4 .= $cad_idempresa; // Nº Id empresa
                /* 3 */ $cadena_ind4 .= str_pad('', 2, $char_pad_string, $type_pad_string); // Relleno a blancos 
                /* 4 */ $cadena_ind4 .= $cad_idbeneficiario; // Nº id Beneficiario
                /* 5 */ $cadena_ind4 .= $cad_concepto; // 1er Concepto
                /* 6 */ $cadena_ind4 .= str_pad('', 177, $char_pad_string, $type_pad_string); //Libre
                $cadena_ind4 .= chr(13) . chr(10);

                if ($cuenta == 2) { //Registros del Banco Frances
                    $vec_frances[] = array($cadena_ind1 . $cadena_ind2 . $cadena_ind3 . $cadena_ind4, $liquidacionEmpleado->getNeto(), $liquidacionEmpleado->getId());
                } else { //Registros de otros bancos
                    $vec_otros[] = array($cadena_ind1 . $cadena_ind2 . $cadena_ind3 . $cadena_ind4, $liquidacionEmpleado->getNeto(), $liquidacionEmpleado->getId());
                }
            } else {
                if (($liquidacionEmpleado->getCbu() == null) && ($liquidacionEmpleado->getNeto() > 0)) {
                    $this->generarAutorizacionContableCheque($entity, $liquidacionEmpleado, $emContable, $siguienteNumeroOP);
                }
            }
        }


        //Armo los archivos
        $rootpath = $this->controller->get('kernel')->getRootDir() . '/../web/uploads/netcash/';
        $liq_nro = $entity->getNumero();

        $files_frances = null;
        $files_otros = null;
        
        $this->eliminarOPSueldo($emContable, $entity);

        if (isset($vec_frances) <> 0) {
            //Divido en un maximo de 100 registros por archivo
            $vec_frances = array_chunk($vec_frances, 100);
            $files_frances = $this->generarArchivo($cabecera, $pie, $vec_frances, 'NF', $rootpath, $fecha_bbva, strval($liq_nro), $entity, $emContable, $siguienteNumeroOP);
        }

        if (isset($vec_otros) <> 0) {
            //Divido en un maximo de 100 registros por archivo        
            $vec_otros = array_chunk($vec_otros, 100);
            $files_otros = $this->generarArchivo($cabecera, $pie, $vec_otros, 'NO', $rootpath, $fecha_otros, strval($liq_nro), $entity, $emContable, $siguienteNumeroOP);
        }

        $emContable->flush();

        //Los meto en un zip
        $zippath = $rootpath . strval($liq_nro) . '.zip';

        $zip = new ZipArchive;
        $zip->open($zippath, ZipArchive::CREATE);
        if ($files_frances != null) {
            foreach ($files_frances as $actual) {
                $zip->addFile($actual[0], $actual[1]);
            }
        }

        if ($files_otros != null) {
            foreach ($files_otros as $actual) {
                $zip->addFile($actual[0], $actual[1]);
            }
        }
        $zip->close();

        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($zippath));
        $response->headers->set('Content-Disposition', 'filename="' . basename($zippath) . '"');
        $response->headers->set('Content-length', filesize($zippath));

        $response->setContent(file_get_contents($zippath));

        return $response;
    }

    private function generarArchivo($cabecera_tpl, $pie_tpl, $vec_registros, $prefijo_archivo, $rootdir, $fecha_cobro, $nro_liq, $liquidacion, $emContable, &$siguienteNumeroOP) {
        $file_nro = 0;
        foreach ($vec_registros as $actual) {
            $cabecera = $cabecera_tpl;
            $pie = $pie_tpl;
            $cadena = '';
            $monto_total = 0;
            $cant2210 = 0;

            $liquidacionesEmpleado = array();

            foreach ($actual as $campo) {
                $liquidacionesEmpleado[] = $campo[2];
                $cadena .= $campo[0];
                $monto_total = $monto_total + $campo[1]; //Totalizo montos
                $cant2210 = $cant2210 + 1;
            }

            $this->generarAutorizacionContableLote($liquidacion, $liquidacionesEmpleado, $monto_total, $emContable, $siguienteNumeroOP);

            //Genero nombre archivo NFX_0000.txt

            $filename = $prefijo_archivo . $file_nro . '_' . str_pad(strval($nro_liq), 4, 0, STR_PAD_LEFT);
            $fileext = '.txt';
            $path = $rootdir . $filename . $fileext;
            $vec_path[] = array($path, $filename . $fileext);
            //$vec_path[] = $path;
            //Reemplazo valores en cabecera template
            $file_nro = $file_nro + 1;

            $cabecera = str_replace('YYYYYYYY', $fecha_cobro, $cabecera);
            $cabecera = str_replace('XXXXXXXXXXXX', $filename . $fileext, $cabecera);

            $archivo_str = $cabecera;
            $archivo_str .= $cadena;

            $tot_registros = preg_split('/\n/', $archivo_str);
            $tot_registros = count($tot_registros);

            $monto_total = explode(".", number_format($monto_total, 2, '.', '.'));

            //Reemplazo valores de pie de pagina
            $pie = str_replace('XMONTOENTEROS', str_pad(strval($monto_total[0]), 13, 0, STR_PAD_LEFT), $pie);
            $pie = str_replace('{}', str_pad(strval($monto_total[1]), 2, 0, STR_PAD_LEFT), $pie);
            $pie = str_replace('CANT2210', str_pad(strval($cant2210), 8, 0, STR_PAD_LEFT), $pie);
            $pie = str_replace('TOTREGISTS', str_pad(strval($tot_registros), 10, 0, STR_PAD_LEFT), $pie);

            $archivo_str.= $pie;

            $f = fopen($path, "w");
            fwrite($f, $archivo_str);
            fclose($f);
        }

        return $vec_path;
    }

    private function generarAutorizacionContableLote(Liquidacion $liquidacion, $liquidacionesEmpleado, $montoLote, $emContable, &$siguienteNumeroOP) {
        $ordenPago = new OrdenPagoSueldo();
        $ordenPago->setIdLiquidacion($liquidacion->getId())
                ->setImporte($montoLote);
        setlocale(LC_ALL,"es_AR.UTF-8");
        $nombre_liquidacion = ucfirst(strftime("%B %Y", $liquidacion->getFechaCierreNovedades()->getTimestamp()));

        $this->controller->get('adif.orden_pago_service')->initAutorizacionContable($ordenPago, 'Sueldos liquidación ' . $nombre_liquidacion);
        $siguienteNumeroOP = $siguienteNumeroOP == 0 ? $ordenPago->getNumeroAutorizacionContable() : $siguienteNumeroOP + 1;
        $ordenPago->setNumeroAutorizacionContable($siguienteNumeroOP);
        $stringLiquidacionesEmpleados = implode(',', $liquidacionesEmpleado);       
        $ordenPago->setLiquidacionesEmpleado($stringLiquidacionesEmpleados);
        $emContable->persist($ordenPago);
    }
    
    private function generarAutorizacionContableCheque(Liquidacion $liquidacion, \ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado $liquidacionEmpleado, $emContable, &$siguienteNumeroOP) {
        $ordenPago = new OrdenPagoSueldo();
        $ordenPago->setIdLiquidacion($liquidacion->getId())
                ->setImporte($liquidacionEmpleado->getNeto());
        setlocale(LC_ALL,"es_AR.UTF-8");
        $nombre_liquidacion = ucfirst(strftime("%B %Y", $liquidacion->getFechaCierreNovedades()->getTimestamp()));

        $this->controller->get('adif.orden_pago_service')->initAutorizacionContable($ordenPago, 'Sueldos liquidación ' . $nombre_liquidacion);
        $siguienteNumeroOP = $siguienteNumeroOP == 0 ? $ordenPago->getNumeroAutorizacionContable() : $siguienteNumeroOP + 1;
        $ordenPago->setNumeroAutorizacionContable($siguienteNumeroOP);        
        $ordenPago->setLiquidacionesEmpleado($liquidacionEmpleado->getId());
        $emContable->persist($ordenPago);
    }

    private function eliminarOPSueldo($emContable, Liquidacion $liquidacion) {
        $ops = $emContable->getRepository('ADIFContableBundle:OrdenPagoSueldo')->findByIdLiquidacion($liquidacion->getId());
        foreach ($ops as $op) {
            $emContable->remove($op);
        }
        $emContable->flush();
    }

}
