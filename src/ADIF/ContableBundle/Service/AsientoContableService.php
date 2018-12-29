<?php

namespace ADIF\ContableBundle\Service;

use ADIF\AutenticacionBundle\Entity\Usuario;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Entity\Cliente;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteCodigoInternoBienEconomico;
use ADIF\ComprasBundle\Entity\Proveedor;
use ADIF\ComprasBundle\Entity\RenglonOrdenCompra;
use ADIF\ContableBundle\Entity\AdicionalComprobanteCompra;
use ADIF\ContableBundle\Entity\AdifDatos;
use ADIF\ContableBundle\Entity\AsientoContable;
use ADIF\ContableBundle\Entity\Cheque;
use ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza;
use ADIF\ContableBundle\Entity\Cobranza\CobroRetencionCliente;
use ADIF\ContableBundle\Entity\Cobranza\RenglonCobranza;
use ADIF\ContableBundle\Entity\ComprobanteCompra;
use ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoCompras;
use ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion;
use ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteAlicuotaIva;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoInternoCuentaContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteConceptoAsientoContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoAsientoContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoReconocimientoEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteNaturalezaCuenta;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoAsientoContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteVenta;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoDeclaracionJurada;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoOperacionContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;
use ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria;
use ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria;
use ADIF\ContableBundle\Entity\CuentaContable;
use ADIF\ContableBundle\Entity\DeclaracionJuradaIvaContribuyente;
use ADIF\ContableBundle\Entity\EgresoValor\EgresoValorGerencia;
use ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoReconocimientoEgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\ReconocimientoEgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\RenglonComprobanteEgresoValor;
use ADIF\ContableBundle\Entity\EjercicioContable;
use ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta;
use ADIF\ContableBundle\Entity\Facturacion\OrdenPagoDevolucionGarantia;
use ADIF\ContableBundle\Entity\Facturacion\RenglonComprobanteVentaGeneral;
use ADIF\ContableBundle\Entity\MovimientoBancario;
use ADIF\ContableBundle\Entity\MovimientoMinisterial;
use ADIF\ContableBundle\Entity\Obras\ComprobanteObra;
use ADIF\ContableBundle\Entity\Obras\DocumentoFinanciero;
use ADIF\ContableBundle\Entity\Obras\RenglonComprobanteObra;
use ADIF\ContableBundle\Entity\Obras\Tramo;
use ADIF\ContableBundle\Entity\OrdenPago;
use ADIF\ContableBundle\Entity\OrdenPagoAnticipoSueldo;
use ADIF\ContableBundle\Entity\OrdenPagoCargasSociales;
use ADIF\ContableBundle\Entity\OrdenPagoDeclaracionJurada;
use ADIF\ContableBundle\Entity\OrdenPagoDeclaracionJuradaIvaContribuyente;
use ADIF\ContableBundle\Entity\OrdenPagoDevolucionRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\OrdenPagoGeneral;
use ADIF\ContableBundle\Entity\OrdenPagoPagoACuenta;
use ADIF\ContableBundle\Entity\OrdenPagoPagoParcial;
use ADIF\ContableBundle\Entity\OrdenPagoRenglonRetencionLiquidacion;
use ADIF\ContableBundle\Entity\OrdenPagoSueldo;
use ADIF\ContableBundle\Entity\PagoOrdenPago;
use ADIF\ContableBundle\Entity\RenglonAsientoContable;
use ADIF\ContableBundle\Entity\RenglonComprobante;
use ADIF\ContableBundle\Entity\RenglonComprobanteCompra;
use ADIF\ContableBundle\Entity\RenglonImpuesto;
use ADIF\ContableBundle\Entity\RenglonPercepcion;
use ADIF\ContableBundle\Entity\TransferenciaBancaria;
use ADIF\RecursosHumanosBundle\Entity\Concepto;
use ADIF\RecursosHumanosBundle\Entity\ConfiguracionCuentaContableSueldos;
use ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor;
use ADIF\RecursosHumanosBundle\Entity\Liquidacion;
use ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado;
use ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleadoConcepto;
use ADIF\RecursosHumanosBundle\Entity\TipoConcepto;
use ADIF\RecursosHumanosBundle\Entity\TipoLiquidacion;
use DateInterval;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\NoResultException;
use PDO;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteObra;
use ADIF\ContableBundle\Entity\Facturacion\ComprobanteRendicionLiquidoProducto;

/**
 * Description of AsientoContableService
 *
 * @author Darío Rapetti
 * created 10/11/2014
 */
class AsientoContableService {

    /**
     *
     * @var type 
     */
    protected $doctrine;

    /**
     *
     * @var type 
     */
    protected $container;

    /**
     *
     * @var type 
     */
    protected $cuentasContablesInactivas;
	
	/**
     *
     * @var type 
     */
	protected $cuentasContablesNoImputables;
	
	/**
	* Flag que indica si quiero de debugear los asientos mal balanceados
	* (solo con app_dev.php)
	*/
	private $_debugAsientosMalBalanceados = false;

    /**
     * 
     * @param Container $container
     */
    public function __construct(Container $container) {

        $this->container = $container;
        $this->doctrine = $container->get("doctrine");
        $this->cuentasContablesInactivas = array();
    }

    /**
     * 
     * @param ComprobanteConsultoria $comprobante
     * @param Usuario $usuario
     */
    public function generarAsientoComprobanteConsultoria(ComprobanteConsultoria $comprobante, Usuario $usuario, $esContraAsiento = false, $fecha_contable = null) {

        $asientoArray = array();

        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $total_asiento = 0;
        $erroresArray = array();

        $detalleRenglon = 'Honorarios contrato n&deg; ' . $comprobante->getContrato()->getNumeroContrato();

        /* @var $consultor Consultor */
        $consultor = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($comprobante->getContrato()->getIdConsultor());

        $esHonorarioProfesional = $comprobante->getContrato()->getEsHonorarioProfesional();
        if ($esHonorarioProfesional) {
            $bienEconomico = $emCompras->getRepository('ADIFComprasBundle:BienEconomico')
                    ->findOneByCodigoInterno(ConstanteCodigoInternoBienEconomico::HONORARIO_PROFESIONAL);
        } else {
            $bienEconomico = $emCompras->getRepository('ADIFComprasBundle:BienEconomico')
                    ->findOneByCodigoInterno(ConstanteCodigoInternoBienEconomico::HONORARIO_NO_PROFESIONAL);
        }

        if ($bienEconomico) {
            /* @var $cuenta_contable CuentaContable */
            $cuenta_contable = $bienEconomico->getCuentaContable();

            if (!$cuenta_contable) {
                $erroresArray[$bienEconomico->getId()] = 'El bien econ&oacute;mico ' . $bienEconomico . ' no posee una cuenta contable asociada.';
            }

            $codigo_cuenta = $cuenta_contable->getCodigoCuentaContable();

            $naturaleza_cuenta = $cuenta_contable->getSegmentoOrden(1);

            if ($naturaleza_cuenta == ConstanteNaturalezaCuenta::GASTO) {
                // Si la naturaleza es un gasto, busco el centro de costos del consultor         
                $area_contrato = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Area')->find($comprobante->getContrato()->getIdArea());
                $centro_costo = $emContable->getRepository('ADIFContableBundle:CentroCosto')->find($area_contrato->getIdCentrocosto());

                $codigo_cuenta = $this->getCuentaContableConCentroCosto($codigo_cuenta, $centro_costo);
            }

            //Cuenta asociada al consultor
            $asientoArray[$codigo_cuenta] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $comprobante->getImporteTotalNeto(),
                'detalle' => $detalleRenglon
            );

            $total_asiento += $comprobante->getImporteTotalNeto();
        }

        foreach ($comprobante->getRenglonesComprobante() as $renglon_comprobante) {
            if ($renglon_comprobante->getAlicuotaIva()->getValor() != '0.00') {
                $codigo_cuenta_iva = $renglon_comprobante->getAlicuotaIva()->getCuentaContableCredito()->getCodigoCuentaContable();
                $acumulado_iva = isset($asientoArray[$codigo_cuenta_iva]['monto']) ? $asientoArray[$codigo_cuenta_iva]['monto'] : 0;
                //Cuenta asociada al iva
                $asientoArray[$codigo_cuenta_iva] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $acumulado_iva + $renglon_comprobante->getMontoIva(),
                    'detalle' => $detalleRenglon
                );

                $total_asiento += $renglon_comprobante->getMontoIva();
            }
        }

        //Cuentas de percepciones
        foreach ($comprobante->getRenglonesPercepcion() as $renglon_percepcion) {
            /* @var $renglon_percepcion RenglonPercepcion */
            if ($renglon_percepcion->getJurisdiccion()) {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneBy(array(
                    'conceptoPercepcion' => $renglon_percepcion->getConceptoPercepcion(), //
                    'jurisdiccion' => $renglon_percepcion->getJurisdiccion())
                );
            } else {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneByConceptoPercepcion($renglon_percepcion->getConceptoPercepcion());
            }

            if ($cuenta_percepcion) {
                $codigo_cuenta_percepcion = $cuenta_percepcion->getCuentaContableCredito()->getCodigoCuentaContable();
                $acumulado_percepcion = isset($asientoArray[$codigo_cuenta_percepcion]['monto']) ? $asientoArray[$codigo_cuenta_percepcion]['monto'] : 0;

                //Cuenta asociada a la percepcion
                $asientoArray[$codigo_cuenta_percepcion] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $acumulado_percepcion + $renglon_percepcion->getMonto(),
                    'detalle' => $detalleRenglon
                );
                $total_asiento += $renglon_percepcion->getMonto();
            } else {
                $erroresArray[$renglon_percepcion->getConceptoPercepcion()->getId()] = 'El concepto de percepci&oacute;n ' . $renglon_percepcion->getConceptoPercepcion() . ' no posee una cuenta contable asociada.';
            }
        }

        //Cuentas de impuestos
        foreach ($comprobante->getRenglonesImpuesto() as $renglon_impuesto) {
            /* @var $renglon_impuesto RenglonImpuesto */
            $codigo_cuenta_impuesto = $renglon_impuesto->getConceptoImpuesto()->getCuentaContable()->getCodigoCuentaContable();
            $acumulado_impuesto = isset($asientoArray[$codigo_cuenta_impuesto]['monto']) ? $asientoArray[$codigo_cuenta_impuesto]['monto'] : 0;

            //Cuenta asociada a la percepcion
            $asientoArray[$codigo_cuenta_impuesto] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumulado_impuesto + $renglon_impuesto->getMonto(),
                'detalle' => $detalleRenglon
            );
            $total_asiento += $renglon_impuesto->getMonto();
        }

        $cuenta_consultor = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($consultor->getIdCuentaContable());

        if ($cuenta_consultor) {
            $codigo_cuenta_consultor = $cuenta_consultor->getCodigoCuentaContable();
            $asientoArray[$codigo_cuenta_consultor] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                'monto' => $total_asiento,
                'detalle' => $detalleRenglon
            );
        } else {
            $erroresArray[$consultor->getId()] = 'El consultor ' . $consultor->getId() . ' no posee una cuenta contable asociada.';
        }

        $renglones_asiento = array();

        foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {
            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);
            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => $datos_movimiento['monto'],
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::DEVENGAMIENTO_CONTRATO_LOCACION_SERVICIO);

        $denominacionAsientoContable = ($esContraAsiento ? 'Anulaci&oacute;n - ' : '')
                . 'Contrato n&deg; '
                . $comprobante->getContrato()->getNumeroContrato()
                . ' - ' . $consultor->getCuitAndRazonSocial();

        $datosAsiento = array(
            'denominacion' => $denominacionAsientoContable,
            'concepto' => $concepto_asiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario,
            'razonSocial' => $consultor->getRazonSocial(),
            'numeroDocumento' => $consultor->getCUIT(),
            'comprobante' => $comprobante
        );

        if ($fecha_contable) {
            $datosAsiento['fecha_contable'] = $fecha_contable;
        }

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $mensajeErrorAsientoContable);
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearAsientoFromComprobanteConsultoria($comprobante, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $this->container->get('request')->getSession()->getFlashBag()
                        ->add('error', $mensajeErrorAsientoPresupuestarioAsiento);
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {

            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param ComprobanteCompra $comprobante
     * @param type $usuario
     * @return type
     */
    public function generarAsientoCompras(ComprobanteCompra $comprobante, Usuario $usuario, $esContraAsiento = false, $fecha_contable = null) 
	{
		// @hot-fix fecha de implementacion compras x CeCo -pushear
		$fechaImplementacionCeCo = \DateTime::createFromFormat('Y-m-d H:i:s', '2017-09-01 00:00:00');
		$implementacionComprasCeCo = false;
	
        $asientoArray = array();

        $emAutenticacion = $this->doctrine->getManager(EntityManagers::getEmAutenticacion());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $total_asiento = 0;

        $erroresArray = array();

        $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')->find($comprobante->getIdProveedor());

        $detalleRenglon = 'Comprobante ' . $comprobante->getTipoComprobante() . ' ' . $comprobante->getNumeroCompleto() . ' - ' . $proveedor->getCUITAndRazonSocial();

        // Cuentas de bienes e IVA
        foreach ($comprobante->getRenglonesComprobante() as $renglon_comprobante) {

            /* @var $renglon_comprobante RenglonComprobanteCompra */

            /* @var $renglon_oc RenglonOrdenCompra */
            $renglon_oc = $emCompras->getRepository('ADIFComprasBundle:RenglonOrdenCompra')
                    ->find($renglon_comprobante->getIdRenglonOrdenCompra());

            $bienEconomico = $renglon_oc->getBienEconomico();

            /* @var $cuenta_contable CuentaContable */
            $cuenta_contable = $bienEconomico->getCuentaContable();

            if (!$cuenta_contable) {
                $erroresArray[$bienEconomico->getId()] = 'El bien econ&oacute;mico ' . $bienEconomico . ' no posee una cuenta contable asociada.';
            }

            $codigo_cuenta = $cuenta_contable->getCodigoCuentaContable();

            $naturaleza_cuenta = $cuenta_contable->getSegmentoOrden(1);
			
			if ($naturaleza_cuenta == ConstanteNaturalezaCuenta::GASTO) {

                if ($renglon_oc->getIdCentroCosto() != null) {

                    $centro_costo = $renglon_oc->getCentroCosto();
                } //.
                else {

                    // Si la naturaleza es un gasto, busco el centro de costos del bien
                    if ($renglon_oc->getRenglonPedidoInterno()) {

						$pedidoInterno = $renglon_oc->getRenglonPedidoInterno()->getPedidoInterno();
						$fechaCreacionPI = $pedidoInterno->getFechaCreacion();
						if ($fechaCreacionPI >= $fechaImplementacionCeCo) {
							$implementacionComprasCeCo = true;
							$centro_costo = $renglon_oc->getRenglonPedidoInterno()
                                    ->getPedidoInterno()->getCentroCosto();
							
						} else {
							$implementacionComprasCeCo = false;
							$id_area = $renglon_oc->getRenglonPedidoInterno()
                                        ->getPedidoInterno()->getIdArea();
						}
					
                        
                    } else {

                        $id_usuario = $renglon_oc->getRenglonCotizacion()
                                        ->getRenglonRequerimiento()->getRenglonSolicitudCompra()
                                        ->getSolicitudCompra()->getIdUsuario();

                        $usuario = $emAutenticacion->getRepository('ADIFAutenticacionBundle:Usuario')
                                ->find($id_usuario);

                        $id_area = $usuario->getIdArea();
                    }
					
					if (!$implementacionComprasCeCo) {
						$area_usuario = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Area')
                            ->find($id_area);

						$centro_costo = $emContable->getRepository('ADIFContableBundle:CentroCosto')
                            ->find($area_usuario->getIdCentrocosto());
					}
                }

                $codigo_cuenta = $this
                        ->getCuentaContableConCentroCosto($codigo_cuenta, $centro_costo);
            }

            $acumulado = isset($asientoArray[$codigo_cuenta]['monto']) //
                    ? $asientoArray[$codigo_cuenta]['monto'] //
                    : 0;

            $brutoProrrateado = $renglon_comprobante->getMontoAdicionalProrrateadoDiscriminado();

            // Cuenta asociada al bien
            $asientoArray[$codigo_cuenta] = array(
                'imputacion' => !$esContraAsiento //
                        ? ConstanteTipoOperacionContable::DEBE //
                        : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumulado + $brutoProrrateado['neto'],
                'detalle' => $detalleRenglon
            );

            $total_asiento += $brutoProrrateado['neto'];

            if ($renglon_comprobante->getAlicuotaIva()->getValor() != '0.00') {

                $codigo_cuenta_iva = $renglon_comprobante->getAlicuotaIva()
                                ->getCuentaContableCredito()->getCodigoCuentaContable();

                $acumulado_iva = isset($asientoArray[$codigo_cuenta_iva]['monto']) //
                        ? $asientoArray[$codigo_cuenta_iva]['monto'] //
                        : 0;

                //Cuenta asociada al iva
                $asientoArray[$codigo_cuenta_iva] = array(
                    'imputacion' => !$esContraAsiento //
                            ? ConstanteTipoOperacionContable::DEBE //
                            : ConstanteTipoOperacionContable::HABER,
                    'monto' => $acumulado_iva + $brutoProrrateado['iva'],
                    'detalle' => $detalleRenglon
                );

                $total_asiento += $brutoProrrateado['iva'];
            }
        }
		
        // Cuentas de IVA de los adicionales
        foreach ($comprobante->getAdicionales() as $adicional) {
            /* @var $adicional AdicionalComprobanteCompra */
            if ($adicional->getAlicuotaIva()->getValor() != '0.00') {
                $codigo_cuenta_iva = $adicional->getAlicuotaIva()->getCuentaContableCredito()->getCodigoCuentaContable();
                $acumulado_iva = isset($asientoArray[$codigo_cuenta_iva]['monto']) ? $asientoArray[$codigo_cuenta_iva]['monto'] : 0;
                //Cuenta asociada al iva
                $asientoArray[$codigo_cuenta_iva] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $acumulado_iva + $adicional->getMontoIva(),
                    'detalle' => $detalleRenglon
                );

                $total_asiento += $adicional->getMontoIva();
            }
        }

        //Cuentas de percepciones
        foreach ($comprobante->getRenglonesPercepcion() as $renglon_percepcion) {

            /* @var $renglon_percepcion RenglonPercepcion */
            if ($renglon_percepcion->getJurisdiccion()) {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneBy(array(
                    'conceptoPercepcion' => $renglon_percepcion->getConceptoPercepcion(), //
                    'jurisdiccion' => $renglon_percepcion->getJurisdiccion())
                );
            } else {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneByConceptoPercepcion($renglon_percepcion->getConceptoPercepcion());
            }

            if ($cuenta_percepcion) {
                $codigo_cuenta_percepcion = $cuenta_percepcion->getCuentaContableCredito()->getCodigoCuentaContable();
            } else {
                $erroresArray[$renglon_percepcion->getConceptoPercepcion()->getId()] = 'El concepto de percepci&oacute;n ' . $renglon_percepcion->getConceptoPercepcion() . ' no posee una cuenta contable asociada.';
            }

            $acumulado_percepcion = isset($asientoArray[$codigo_cuenta_percepcion]['monto']) ? $asientoArray[$codigo_cuenta_percepcion]['monto'] : 0;

            //Cuenta asociada a la percepcion
            $asientoArray[$codigo_cuenta_percepcion] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumulado_percepcion + $renglon_percepcion->getMonto(),
                'detalle' => $detalleRenglon
            );
            $total_asiento += $renglon_percepcion->getMonto();
        }

        //Cuentas de impuestos
        foreach ($comprobante->getRenglonesImpuesto() as $renglon_impuesto) {
            /* @var $renglon_impuesto RenglonImpuesto */

            $codigo_cuenta_impuesto = $renglon_impuesto->getConceptoImpuesto()->getCuentaContable()->getCodigoCuentaContable();
            $acumulado_impuesto = isset($asientoArray[$codigo_cuenta_impuesto]['monto']) ? $asientoArray[$codigo_cuenta_impuesto]['monto'] : 0;

            //Cuenta asociada a la percepcion
            $asientoArray[$codigo_cuenta_impuesto] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumulado_impuesto + $renglon_impuesto->getMonto(),
                'detalle' => $detalleRenglon
            );
            $total_asiento += $renglon_impuesto->getMonto();
        }

        if ($proveedor->getCuentaContable()) {
            $codigo_cuenta_proveedor = $proveedor->getCuentaContable()->getCodigoCuentaContable();
        } else {
            $erroresArray[$proveedor->getId()] = 'El proveedor ' . $proveedor->cuitAndRazonSocial() . ' no posee una cuenta contable asociada.';
        }

        $asientoArray[$codigo_cuenta_proveedor] = array(
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
            'monto' => $total_asiento,
            'detalle' => $detalleRenglon
        );

        $renglones_asiento = array();

        foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {
            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);
            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => $datos_movimiento['monto'],
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::COMPRAS);

        /* @var $ordenCompra OrdenCompra */
        $ordenCompra = $emCompras->getRepository('ADIFComprasBundle:OrdenCompra')
                ->find($comprobante->getIdOrdenCompra());

        $denominacion = ($esContraAsiento ? 'Anulaci&oacute;n - ' : '')
                . 'Orden de compra n&deg; '
                . $ordenCompra . ' - ' . $proveedor->cuitAndRazonSocial();

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $concepto_asiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario,
            'razonSocial' => $proveedor->getRazonSocial(),
            'numeroDocumento' => $proveedor->getCUIT(),
            'comprobante' => $comprobante
        );

        if ($fecha_contable) {
            $datosAsiento['fecha_contable'] = $fecha_contable;
        }

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $mensajeErrorAsientoContable);
        } //.
        else {

            // Persisto los asientos presupuestarios
//            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
//                    ->crearAsientoFromComprobanteCompra($comprobante, $esContraAsiento, $asiento);
//
//            // Si el asiento presupuestario falló
//            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
//                $this->container->get('request')->getSession()->getFlashBag()
//                        ->add('error', $mensajeErrorAsientoPresupuestarioAsiento);
//            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param ComprobanteCompra $comprobante
     * @param Usuario $usuario
     * @return type
     */
    public function generarAsientoServicio(ComprobanteCompra $comprobante, Usuario $usuario, $esContraAsiento = false, $fecha_contable = null) {

        $asientoArray = array();

        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresArray = array();

        $total_asiento = 0;

        $detalleRenglon = '';

        $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')->find($comprobante->getIdProveedor());

        $detalleRenglon = 'Comprobante ' . $comprobante->getTipoComprobante() . ' ' . $comprobante->getNumeroCompleto() . ' - ' . $proveedor->getCUITAndRazonSocial();

        // Cuentas de bienes e IVA
        foreach ($comprobante->getRenglonesComprobante() as $renglon_comprobante) {
            /* @var $renglon_oc RenglonOrdenCompra */
            $bienEconomico = $emCompras->getRepository('ADIFComprasBundle:BienEconomico')->find($renglon_comprobante->getIdBienEconomico());
            $cuenta_contable = $bienEconomico->getCuentaContable();

            /* @var $cuenta_contable CuentaContable */
            $codigo_cuenta = $cuenta_contable->getCodigoCuentaContable();

            foreach ($renglon_comprobante->getRenglonComprobanteCompraCentrosDeCosto() as $renglonComprobanteCompraCentrosDeCosto) {

                /* @var $renglonComprobanteCompraCentrosDeCosto RenglonComprobanteCompraCentrosDeCosto */
                $centro_costo = $renglonComprobanteCompraCentrosDeCosto->getCentroDeCosto();

                // Indico el centro de costos
                if ($centro_costo != null) {

                    $naturalezaCuentaContable = $cuenta_contable->getSegmentoOrden(1);

                    // Si la naturaleza es un gasto
                    if ($naturalezaCuentaContable == ConstanteNaturalezaCuenta::GASTO) {
                        $codigo_cuenta = $this
                                ->getCuentaContableConCentroCosto($codigo_cuenta, $centro_costo);
                    }
                }

                $acumulado = isset($asientoArray[$codigo_cuenta]['monto']) ? $asientoArray[$codigo_cuenta]['monto'] : 0;

                //Cuenta asociada al bien
                $asientoArray[$codigo_cuenta] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $acumulado + $renglonComprobanteCompraCentrosDeCosto->getPorcentaje() * $renglon_comprobante->getMontoNeto() / 100,
                    'detalle' => $detalleRenglon
                );
            }

            $total_asiento += $renglon_comprobante->getMontoNeto();

            if ($renglon_comprobante->getAlicuotaIva()->getValor() != '0.00') {

                $codigo_cuenta_iva = $renglon_comprobante->getAlicuotaIva()->getCuentaContableCredito()->getCodigoCuentaContable();
                $acumulado_iva = isset($asientoArray[$codigo_cuenta_iva]['monto']) ? $asientoArray[$codigo_cuenta_iva]['monto'] : 0;

                //Cuenta asociada al iva
                $asientoArray[$codigo_cuenta_iva] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $acumulado_iva + $renglon_comprobante->getMontoIva(),
                    'detalle' => $detalleRenglon
                );

                $total_asiento += $renglon_comprobante->getMontoIva();
            }
        }

        //Cuentas de percepciones
        foreach ($comprobante->getRenglonesPercepcion() as $renglon_percepcion) {

            /* @var $renglon_percepcion RenglonPercepcion */
            if ($renglon_percepcion->getJurisdiccion()) {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneBy(array(
                    'conceptoPercepcion' => $renglon_percepcion->getConceptoPercepcion(), //
                    'jurisdiccion' => $renglon_percepcion->getJurisdiccion())
                );
            } else {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneByConceptoPercepcion($renglon_percepcion->getConceptoPercepcion());
            }

            if ($cuenta_percepcion) {
                $codigo_cuenta_percepcion = $cuenta_percepcion->getCuentaContableCredito()->getCodigoCuentaContable();
                $acumulado_percepcion = isset($asientoArray[$codigo_cuenta_percepcion]['monto']) ? $asientoArray[$codigo_cuenta_percepcion]['monto'] : 0;

                //Cuenta asociada a la percepcion
                $asientoArray[$codigo_cuenta_percepcion] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $acumulado_percepcion + $renglon_percepcion->getMonto(),
                    'detalle' => $detalleRenglon
                );

                $total_asiento += $renglon_percepcion->getMonto();
            } else {
                $erroresArray[$renglon_percepcion->getConceptoPercepcion()->getId()] = 'El concepto de percepci&oacute;n ' . $renglon_percepcion->getConceptoPercepcion() . ' no posee una cuenta contable asociada.';
            }
        }

        //Cuentas de impuestos
        foreach ($comprobante->getRenglonesImpuesto() as $renglon_impuesto) {
            /* @var $renglon_impuesto RenglonImpuesto */

            $codigo_cuenta_impuesto = $renglon_impuesto->getConceptoImpuesto()
                            ->getCuentaContable()->getCodigoCuentaContable();

            $acumulado_impuesto = isset($asientoArray[$codigo_cuenta_impuesto]['monto']) ? $asientoArray[$codigo_cuenta_impuesto]['monto'] : 0;

            //Cuenta asociada a la percepcion
            $asientoArray[$codigo_cuenta_impuesto] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumulado_impuesto + $renglon_impuesto->getMonto(),
                'detalle' => $detalleRenglon
            );

            $total_asiento += $renglon_impuesto->getMonto();
        }

        if ($proveedor->getCuentaContable()) {
            $codigo_cuenta_proveedor = $proveedor->getCuentaContable()->getCodigoCuentaContable();
        } else {
            $erroresArray[$proveedor->getId()] = 'El proveedor ' . $proveedor->cuitAndRazonSocial() . ' no posee una cuenta contable asociada.';
        }

        $asientoArray[$codigo_cuenta_proveedor] = array(
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
            'monto' => $total_asiento,
            'detalle' => $detalleRenglon
        );

        $renglones_asiento = array();

        foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {

            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);

            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => $datos_movimiento['monto'],
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::COMPRAS);

        $denominacion = ($esContraAsiento ? 'Anulaci&oacute;n - ' : '') . 'Asiento de servicio';

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $concepto_asiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario,
            'razonSocial' => $proveedor->getRazonSocial(),
            'numeroDocumento' => $proveedor->getCUIT(),
            'comprobante' => $comprobante
        );

        if ($fecha_contable) {
            $datosAsiento['fecha_contable'] = $fecha_contable;
        }

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $mensajeErrorAsientoContable);
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container
                    ->get('adif.contabilidad_presupuestaria_service')
                    ->crearAsientoFromComprobanteCompra($comprobante, false, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $this->container->get('request')->getSession()->getFlashBag()
                        ->add('error', $mensajeErrorAsientoPresupuestarioAsiento);
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param Tramo $tramo
     * @param Usuario $usuario
     * @return type
     */
    public function generarAsientoTramoFinalizado(Tramo $tramo, Usuario $usuario) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $asientoArray = array();
        $erroresArray = array();
        $renglonesAsiento = array();

        $detalleRenglon = $tramo->__toString();

        /* OBRAS FINALIZADAS */
        $cuentaContableObrasFinalizadas = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_FINALIZADAS);

        if ($cuentaContableObrasFinalizadas != null) {

            $renglonesAsiento[] = array(
                'cuenta' => $cuentaContableObrasFinalizadas,
                'imputacion' => ConstanteTipoOperacionContable::DEBE,
                'monto' => $tramo->getTotalContrato(true),
                'detalle' => $detalleRenglon
            );
        } else {
            $erroresArray[ConstanteCodigoInternoCuentaContable::OBRAS_FINALIZADAS] = 'No se encontr&oacute; una cuenta contable con c&oacute;digo interno: '
                    . ConstanteCodigoInternoCuentaContable::OBRAS_FINALIZADAS;
        }


        /* DOCUMENTOS FINANCIEROS */

        $totalObrasEjecucion = 0;

        $totalAnticipoFinanciero = 0;

        foreach ($tramo->getDocumentosFinancieros() as $documentoFinanciero) {

            /* @var $documentoFinanciero DocumentoFinanciero */

            if (!$documentoFinanciero->getEsAnticipoFinanciero()) {
                $totalObrasEjecucion += $documentoFinanciero->getMontoSinIVA();
            } else {
                $totalAnticipoFinanciero += $documentoFinanciero->getMontoSinIVA();
            }
        }

        if ($totalObrasEjecucion > 0) {
            if ($tramo->getTieneFuenteCAF()) {
                // Va todo a la cuenta CAF
                $cuentaContableFuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                        ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION_CAF);

                if ($cuentaContableFuenteFinanciamiento != null) {
                    $renglonesAsiento[] = array(
                        'cuenta' => $cuentaContableFuenteFinanciamiento,
                        'imputacion' => ConstanteTipoOperacionContable::HABER,
                        'monto' => $totalObrasEjecucion,
                        'detalle' => $detalleRenglon
                    );
                } else {
                    $erroresArray[ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION_CAF] = 'No se encontr&oacute; una cuenta contable con c&oacute;digo interno: ' . ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION_CAF;
                }
            } else {
                foreach ($tramo->getFuentesFinanciamiento() as $fuenteFinanciamientoTramo) {
                    $fuenteFinanciamiento = $fuenteFinanciamientoTramo->getFuenteFinanciamiento();

                    // Si la FuenteFinanciamiento modifica las cuentas contables
                    if ($fuenteFinanciamiento->getModificaCuentaContable()) {
                        $cuentaContableObrasEjecucion = $fuenteFinanciamiento->getCuentaContable();
                    } else {
                        $cuentaContableObrasEjecucion = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION);
                    }

                    if ($cuentaContableObrasEjecucion != null) {
                        $totalObrasEjecucionProrrateado = $totalObrasEjecucion * $fuenteFinanciamientoTramo->getPorcentaje() / 100;

                        $renglonesAsiento[] = array(
                            'cuenta' => $cuentaContableObrasEjecucion,
                            'imputacion' => ConstanteTipoOperacionContable::HABER,
                            'monto' => $totalObrasEjecucionProrrateado,
                            'detalle' => $detalleRenglon
                        );
                    } else {
                        $erroresArray[ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION] = 'No se encontr&oacute; una cuenta contable con c&oacute;digo interno: ' . ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION;
                    }
                }
            }
        }

        if ($totalAnticipoFinanciero > 0) {
            $cuentaContableAnticipoFinancieroObras = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::ANTICIPO_FINANCIERO_OBRAS);

            if ($cuentaContableAnticipoFinancieroObras != null) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContableAnticipoFinancieroObras,
                    'imputacion' => ConstanteTipoOperacionContable::HABER,
                    'monto' => $totalAnticipoFinanciero,
                    'detalle' => $detalleRenglon
                );
            } else {
                $erroresArray[ConstanteCodigoInternoCuentaContable::ANTICIPO_FINANCIERO_OBRAS] = 'No se encontr&oacute; una cuenta contable con c&oacute;digo interno: ' . ConstanteCodigoInternoCuentaContable::ANTICIPO_FINANCIERO_OBRAS;
            }
        }

        foreach ($asientoArray as $codigoCuentaContable => $datosMovimiento) {

            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigoCuentaContable);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuentaContable] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuentaContable;
            }
        }

        $conceptoAsiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::COMPRAS);

        $proveedor = $tramo->getProveedor();

        $datosAsiento = array(
            'denominacion' => 'Reimputaci&oacute;n por obra finalizada - ' . $tramo->__toString(),
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $proveedor->getRazonSocial(),
            'numeroDocumento' => $proveedor->getCUIT()
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $this->container->get('request')->getSession()->getFlashBag()->add('error', $mensajeErrorAsientoContable);
            $this->container->get('request')->attributes->set('form-error', true);
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->generarAsientoPresupuestarioTramoFinalizado($tramo, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $this->container->get('request')->getSession()->getFlashBag()
                        ->add('error', $mensajeErrorAsientoPresupuestarioAsiento);
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param ComprobanteObra $comprobante
     * @param Usuario $usuario
     * @param type $esContraAsiento
     * @return type
     */
    public function generarAsientoComprobanteObra(ComprobanteObra $comprobante, Usuario $usuario, $esContraAsiento = false, $fecha_contable = null) {

        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $asientoArray = array();

        $erroresArray = array();

        $detalleRenglon = $comprobante->getTramo() . ' - ' . $comprobante->getDocumentoFinanciero()->getTipoDocumentoFinanciero();

        /* @var $documentoFinanciero DocumentoFinanciero */
        $documentoFinanciero = $comprobante->getDocumentoFinanciero();

        // Si el documento financiero es un AnticipoFinanciero
        if ($documentoFinanciero->getEsAnticipoFinanciero() || $documentoFinanciero->getTipoDocumentoFinanciero()->getImputaCuentaTipoDocumento()) {

            $cuentaContable = $documentoFinanciero->getTipoDocumentoFinanciero()->getCuentaContable();

            /* @var $cuentaContable CuentaContable */
            $codigoCuentaContable = $cuentaContable->getCodigoCuentaContable();

            $asientoArray[$codigoCuentaContable] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $comprobante->getTotalNeto(),
                'detalle' => $detalleRenglon
            );
        } else {

            $tramo = $comprobante->getTramo();
            if ($tramo->getTieneFuenteCAF()) {
                // Va todo a CAF                
                $cuentaContableFuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION_CAF);
                $codigoCuentaContable = $cuentaContableFuenteFinanciamiento->getCodigoCuentaContable();

                $asientoArray[$codigoCuentaContable] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $comprobante->getTotalNeto(),
                    'detalle' => $detalleRenglon
                );
            } else {
                foreach ($tramo->getFuentesFinanciamiento() as $fuenteFinanciamientoTramo) {
                    $fuenteFinanciamiento = $fuenteFinanciamientoTramo->getFuenteFinanciamiento();
                    // Si la FuenteFinanciamiento modifica las cuentas contables
                    if ($fuenteFinanciamiento->getModificaCuentaContable()) {
                        $cuentaContableFuenteFinanciamiento = $fuenteFinanciamiento->getCuentaContable();
                    } else {
                        $cuentaContableFuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION);
                    }

                    if ($cuentaContableFuenteFinanciamiento != null) {
                        $totalComprobanteProrrateado = $comprobante->getTotalNeto() * $fuenteFinanciamientoTramo->getPorcentaje() / 100;

                        /* @var $cuentaContable CuentaContable */
                        $codigoCuentaContable = $cuentaContableFuenteFinanciamiento->getCodigoCuentaContable();

                        $acumuladoFuenteFinanciamiento = isset($asientoArray[$codigoCuentaContable]['monto']) //
                                ? $asientoArray[$codigoCuentaContable]['monto'] + $totalComprobanteProrrateado //
                                : $totalComprobanteProrrateado;

                        $asientoArray[$codigoCuentaContable] = array(
                            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                            'monto' => $acumuladoFuenteFinanciamiento,
                            'detalle' => $detalleRenglon
                        );
                    } else {
                        $erroresArray[ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION] = 'No se encontr&oacute; una cuenta contable con c&oacute;digo interno: ' . ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION;
                    }
                }
            }
        }

        // Por cada renglon del comprobante de obra
        foreach ($comprobante->getRenglonesComprobante() as $renglonComprobanteObra) {
            /* @var $renglonComprobanteObra RenglonComprobanteObra  */
            if ($renglonComprobanteObra->getAlicuotaIva()->getValor() != '0.00') {
                $codigo_cuenta_iva = $renglonComprobanteObra->getAlicuotaIva()->getCuentaContableCredito()->getCodigoCuentaContable();

                $acumulado_iva = isset($asientoArray[$codigo_cuenta_iva]['monto']) //
                        ? $asientoArray[$codigo_cuenta_iva]['monto'] //
                        : 0;

                // Cuenta asociada al iva
                $asientoArray[$codigo_cuenta_iva] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $acumulado_iva + $renglonComprobanteObra->getMontoIva(),
                    'detalle' => $detalleRenglon
                );
            }
        }

        //Cuentas de percepciones
        foreach ($comprobante->getRenglonesPercepcion() as $renglon_percepcion) {
            /* @var $renglon_percepcion RenglonPercepcion */
            if ($renglon_percepcion->getJurisdiccion()) {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')->findOneBy(array(
                    'conceptoPercepcion' => $renglon_percepcion->getConceptoPercepcion(), //
                    'jurisdiccion' => $renglon_percepcion->getJurisdiccion())
                );
            } else {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')->findOneByConceptoPercepcion($renglon_percepcion->getConceptoPercepcion());
            }

            if ($cuenta_percepcion) {
                $codigo_cuenta_percepcion = $cuenta_percepcion->getCuentaContableCredito()->getCodigoCuentaContable();
            } else {
                $erroresArray[$renglon_percepcion->getConceptoPercepcion()->getId()] = 'El concepto de percepci&oacute;n ' . $renglon_percepcion->getConceptoPercepcion() . ' no posee una cuenta contable asociada.';
            }

            $acumulado_percepcion = isset($asientoArray[$codigo_cuenta_percepcion]['monto']) ? $asientoArray[$codigo_cuenta_percepcion]['monto'] : 0;

            //Cuenta asociada a la percepcion
            $asientoArray[$codigo_cuenta_percepcion] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumulado_percepcion + $renglon_percepcion->getMonto(),
                'detalle' => $detalleRenglon
            );
        }

        // Cuentas de impuestos
        foreach ($comprobante->getRenglonesImpuesto() as $renglonImpuesto) {

            /* @var $renglonImpuesto RenglonImpuesto */

            $codigoCuentaContableImpuesto = $renglonImpuesto->getConceptoImpuesto()
                            ->getCuentaContable()->getCodigoCuentaContable();

            $acumuladoImpuesto = isset($asientoArray[$codigoCuentaContableImpuesto]['monto']) //
                    ? $asientoArray[$codigoCuentaContableImpuesto]['monto'] //
                    : 0;

            //Cuenta asociada a la percepcion
            $asientoArray[$codigoCuentaContableImpuesto] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumuladoImpuesto + $renglonImpuesto->getMonto(),
                'detalle' => $detalleRenglon
            );
        }

        /* @var $proveedor Proveedor */
        $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')->find($comprobante->getIdProveedor());

        if ($proveedor->getCuentaContable()) {
            $codigoCuentaContableProveedor = $proveedor->getCuentaContable()->getCodigoCuentaContable();
        } else {
            $erroresArray[$proveedor->getId()] = 'El proveedor ' . $proveedor->cuitAndRazonSocial() . ' no posee una cuenta contable asociada.';
        }

        $asientoArray[$codigoCuentaContableProveedor] = array(
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
            'monto' => $comprobante->getTotal(),
            'detalle' => $detalleRenglon
        );

        $renglonesAsiento = array();

        foreach ($asientoArray as $codigoCuentaContable => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigoCuentaContable);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuentaContable] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuentaContable;
            }
        }

        $conceptoAsiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::COMPRAS);

        $denominacion = ($esContraAsiento ? 'Anulaci&oacute;n - ' : '')
                . 'Tramo ' . $comprobante->getTramo()
                . ' - ' . $proveedor->cuitAndRazonSocial();

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $proveedor->getRazonSocial(),
            'numeroDocumento' => $proveedor->getCUIT(),
            'comprobante' => $comprobante
        );

        if ($fecha_contable) {
            $datosAsiento['fecha_contable'] = $fecha_contable;
        }

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $this->container->get('request')->getSession()->getFlashBag()->add('error', $mensajeErrorAsientoContable);
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')->crearAsientoFromComprobanteObra($comprobante, $esContraAsiento, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $this->container->get('request')->getSession()->getFlashBag()->add('error', $mensajeErrorAsientoPresupuestarioAsiento);
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param RendicionEgresoValor $rendicionEgresoValor
     * @param Usuario $usuario
     * @return type
     */
    public function generarAsientoFromRendicionEgresoValor(RendicionEgresoValor $rendicionEgresoValor, Usuario $usuario) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresArray = array();

        $asientoArray = array();

        $egresoValor = $rendicionEgresoValor->getEgresoValor();

        $detalleRenglon = 'Rendici&oacute;n de ' . strtolower($egresoValor->getTipoEgresoValor());

        // Obtengo el Centro de costo asociado a la Gerencia
        $centroCosto = $egresoValor->getGerencia() ? $egresoValor->getGerencia()->getCentroCosto() : null;

        foreach ($rendicionEgresoValor->getComprobantes() as $comprobante) {

            // Por cada renglon del comprobante
            foreach ($comprobante->getRenglonesComprobante() as $renglonComprobanteEgresoValor) {

                /* @var $renglonComprobanteEgresoValor RenglonComprobanteEgresoValor */
                $cuentaContable = $renglonComprobanteEgresoValor->getConceptoEgresoValor()
                        ->getCuentaContable();

                /* @var $cuentaContable CuentaContable */
                $codigoCuentaContable = $cuentaContable->getCodigoCuentaContable();


                // Indico el centro de costos
                if ($centroCosto != null) {

                    $naturalezaCuentaContable = $cuentaContable->getSegmentoOrden(1);

                    // Si la naturaleza es un gasto
                    if ($naturalezaCuentaContable == ConstanteNaturalezaCuenta::GASTO) {
                        $codigoCuentaContable = $this
                                ->getCuentaContableConCentroCosto($codigoCuentaContable, $centroCosto);
                    }
                }

                $acumuladoRenglon = isset($asientoArray[$codigoCuentaContable]['monto']) //
                        ? $asientoArray[$codigoCuentaContable]['monto'] + $renglonComprobanteEgresoValor->getMontoNeto()//
                        : $renglonComprobanteEgresoValor->getMontoNeto();

                $asientoArray[$codigoCuentaContable] = array(
                    'imputacion' => ConstanteTipoOperacionContable::DEBE,
                    'monto' => $acumuladoRenglon,
                    'detalle' => $detalleRenglon
                );

                if ($renglonComprobanteEgresoValor->getAlicuotaIva()->getValor() != '0.00') {

                    $codigoCuentaContableIVA = $renglonComprobanteEgresoValor->getAlicuotaIva()->getCuentaContableCredito()->getCodigoCuentaContable();

                    $acumuladoIVA = isset($asientoArray[$codigoCuentaContableIVA]['monto']) //
                            ? $asientoArray[$codigoCuentaContableIVA]['monto'] //
                            : 0;

                    // Cuenta asociada al iva
                    $asientoArray[$codigoCuentaContableIVA] = array(
                        'imputacion' => ConstanteTipoOperacionContable::DEBE,
                        'monto' => $acumuladoIVA + $renglonComprobanteEgresoValor->getMontoIva(),
                        'detalle' => $detalleRenglon
                    );
                }
            }

            // Cuentas de percepciones
            foreach ($comprobante->getRenglonesPercepcion() as $renglonPercepcion) {

                /* @var $renglonPercepcion RenglonPercepcion */
                if ($renglonPercepcion->getJurisdiccion()) {
                    $cuentaContablePercepcion = $emContable
                            ->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                            ->findOneBy(array(
                        'conceptoPercepcion' => $renglonPercepcion->getConceptoPercepcion(), //
                        'jurisdiccion' => $renglonPercepcion->getJurisdiccion())
                    );
                } else {
                    $cuentaContablePercepcion = $emContable
                            ->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                            ->findOneByConceptoPercepcion($renglonPercepcion->getConceptoPercepcion());
                }

                if ($cuentaContablePercepcion) {
                    $codigoCuentaContablePercepcion = $cuentaContablePercepcion->getCuentaContableCredito()
                            ->getCodigoCuentaContable();
                } else {
                    $erroresArray[$renglonPercepcion->getConceptoPercepcion()->getId()] = 'El concepto de percepci&oacute;n '
                            . $renglonPercepcion->getConceptoPercepcion() . ' no posee una cuenta contable asociada.';
                }

                $acumuladoPercepcion = isset($asientoArray[$codigoCuentaContablePercepcion]['monto']) //
                        ? $asientoArray[$codigoCuentaContablePercepcion]['monto'] //
                        : 0
                ;

                // Cuenta asociada a la percepcion
                $asientoArray[$codigoCuentaContablePercepcion] = array(
                    'imputacion' => ConstanteTipoOperacionContable::DEBE,
                    'monto' => $acumuladoPercepcion + $renglonPercepcion->getMonto(),
                    'detalle' => $detalleRenglon
                );
            }

            // Cuentas de impuestos
            foreach ($comprobante->getRenglonesImpuesto() as $renglonImpuesto) {

                /* @var $renglonImpuesto RenglonImpuesto */

                $codigoCuentaContableImpuesto = $renglonImpuesto->getConceptoImpuesto()
                                ->getCuentaContable()->getCodigoCuentaContable();

                $acumuladoImpuesto = isset($asientoArray[$codigoCuentaContableImpuesto]['monto']) //
                        ? $asientoArray[$codigoCuentaContableImpuesto]['monto'] //
                        : 0;

                //Cuenta asociada a la percepcion
                $asientoArray[$codigoCuentaContableImpuesto] = array(
                    'imputacion' => ConstanteTipoOperacionContable::DEBE,
                    'monto' => $acumuladoImpuesto + $renglonImpuesto->getMonto(),
                    'detalle' => $detalleRenglon
                );
            }
        }

        // Por cada Devolucion de la RendicionEgresoValor
        foreach ($rendicionEgresoValor->getDevoluciones() as $devolucion) {

            $codigoCuentaContableDevolucion = $devolucion->getCuenta()
                            ->getCuentaContable()->getCodigoCuentaContable();

            $acumuladoDevolucion = isset($asientoArray[$codigoCuentaContableDevolucion]['monto']) //
                    ? $asientoArray[$codigoCuentaContableDevolucion]['monto'] //
                    : 0;

            // Cuenta asociada a la devoluion
            $asientoArray[$codigoCuentaContableDevolucion] = array(
                'imputacion' => ConstanteTipoOperacionContable::DEBE,
                'monto' => $acumuladoDevolucion + $devolucion->getMontoDevolucion(),
                'detalle' => $detalleRenglon
            );
        }


        $renglonesAsiento = array();

        foreach ($asientoArray as $codigoCuentaContable => $datosMovimiento) {

            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigoCuentaContable);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuentaContable] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: '
                        . $codigoCuentaContable;
            }
        }

        // Renglones relacionados al EgresoValor        
        if ($egresoValor->getTipoEgresoValor()->getId() == ConstanteTipoEgresoValor::CAJA_CHICA) {
            // Busco la cc asociada a la gerencia de la caja chica
            $egresoValorGerencia = $emContable->getRepository('ADIFContableBundle:EgresoValor\EgresoValorGerencia')->findOneByIdGerencia($egresoValor->getIdGerencia());
            /* @var $egresoValorGerencia EgresoValorGerencia */

            $cuentaContableTipoEgresoValor = $egresoValorGerencia->getCuentaContable();
        } else {
            $cuentaContableTipoEgresoValor = $egresoValor->getTipoEgresoValor()->getCuentaContable();
        }

        // Renglones relacionados al Egreso de Valor
        if ($cuentaContableTipoEgresoValor) {

            //verifico si hay excedente y lo derivo a acreedores varios
            $totalRendido = $rendicionEgresoValor->getImporteRendido();
            $saldoEgresoValor = $egresoValor->getSaldo();

            $epsilon = 0.00001;

            $diferencia = $saldoEgresoValor - $totalRendido;
            
            // Si el saldo NO es igual al total rendido Y el totalRendido es mayor
            if (!(abs($diferencia) < $epsilon) && $totalRendido > $saldoEgresoValor) {

                $reconocimiento = new ReconocimientoEgresoValor();

                $reconocimiento->setEgresoValor($egresoValor);

                $reconocimiento->setResponsableEgresoValor($egresoValor->getResponsableEgresoValor());

                $reconocimiento->setEstadoReconocimientoEgresoValor($emContable->getRepository('ADIFContableBundle:EgresoValor\EstadoReconocimientoEgresoValor')
                                ->findOneByCodigo(ConstanteEstadoReconocimientoEgresoValor::ESTADO_GENERADO));

                $reconocimiento->setMonto($totalRendido - $saldoEgresoValor);

                $egresoValor->addReconocimiento($reconocimiento);

                $emContable->persist($reconocimiento);

                //se debe obtener la cuenta de la configuracion
                $cuentaExcedente = $rendicionEgresoValor->getEgresoValor()->getTipoEgresoValor()->getCuentaContablReconocimiento();
				
				if ($cuentaExcedente == null) {
					$erroresArray[$egresoValor->getTipoEgresoValor()->getId()] = 'No se encontr&oacute; una cuenta contable para el reconocimiento de gasto, para el tipo: '
                    . $egresoValor->getTipoEgresoValor();
				}
				
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaExcedente,
                    'imputacion' => ConstanteTipoOperacionContable::HABER,
                    'monto' => $reconocimiento->getMonto(),
                    'detalle' => $detalleRenglon
                );

                $totalRendido -= $reconocimiento->getMonto();
            }

            $renglonesAsiento[] = array(
                'cuenta' => $cuentaContableTipoEgresoValor,
                'imputacion' => ConstanteTipoOperacionContable::HABER,
                'monto' => $totalRendido,
                'detalle' => $detalleRenglon
            );
        }
        // Si el tipo de egreso de valor NO tiene CuentaContable asociada: 
        else {
            $erroresArray[$egresoValor->getTipoEgresoValor()->getId()] = 'No se encontr&oacute; una cuenta contable asociada al tipo: '
                    . $egresoValor->getTipoEgresoValor();
        }

        $conceptoAsiento = $emContable->getRepository(
                        'ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        /* @var $responsableEgresoValor ResponsableEgresoValor */
        $responsableEgresoValor = $egresoValor->getResponsableEgresoValor();

        $denominacion = 'Rendici&oacute;n de ' . $egresoValor->getTipoEgresoValor()
                . ' - ' . $responsableEgresoValor->getNombre()
                . ($egresoValor->getGerencia() ? (' - ' . $egresoValor->getGerencia()) : '');

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $responsableEgresoValor->getRazonSocial(),
            'numeroDocumento' => $responsableEgresoValor->getNroDocumento()
        );
		
		$asiento = $this->generarAsientoContable($datosAsiento);
		
		$numeroAsiento = $asiento->getNumeroAsiento();
        
        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $mensajeErrorAsientoContable);
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromRendicionEgresoValor($rendicionEgresoValor, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $this->container->get('request')->getSession()->getFlashBag()
                        ->add('error', $mensajeErrorAsientoPresupuestarioAsiento);
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }
		
        return $numeroAsiento;
    }

    /**
     * 
     * @param OrdenPago $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     * @param type $esOrdenPagoObra
     * @return type
     */
    public function generarAsientoPagoProveedor(OrdenPago $ordenPago, Usuario $usuario, $esContraAsiento = false, $esOrdenPagoObra = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        // Renglones relacionados al Proveedor
        $cuentaContableProveedor = $ordenPago->getProveedor()->getCuentaContable();
        $codigoCuentaContable = $cuentaContableProveedor->getCodigoCuentaContable();

        $asientoArray[$codigoCuentaContable] = array(
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
            'monto' => $ordenPago->getTotalBruto(),
            'detalle' => $ordenPago->getPagoOrdenPago()->getDetalle()
        );


        // Renglones relacionados al anticipo
        if ($ordenPago->getMontoAnticipos() > 0) {
            $configuracion_cuenta_anticipo_proveedor = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')
                    ->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_PROVEEDORES);
            $cuentaContableAnticipoProveedor = $configuracion_cuenta_anticipo_proveedor->getCuentaContable();

            $codigoCuentaContableAnticipoProveedor = $cuentaContableAnticipoProveedor->getCodigoCuentaContable();

            $asientoArray[$codigoCuentaContableAnticipoProveedor] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                'monto' => $ordenPago->getMontoAnticipos(),
                'detalle' => $ordenPago->getPagoOrdenPago()->getDetalle()
            );
        }

        // Renglones relacionados a los ComprobanteRetencion
        foreach ($ordenPago->getRetenciones() as $comprobanteRetencion) {

            /* @var $comprobanteRetencion ComprobanteRetencionImpuestoCompras */
            $cuentaContableComprobanteRetencion = $comprobanteRetencion->getRegimenRetencion()
                    ->getCuentaContable();

            $codigoCuentaComprobanteRetencion = $cuentaContableComprobanteRetencion
                    ->getCodigoCuentaContable();

            // Cuenta asociada a la percepcion
            if (!isset($asientoArray[$codigoCuentaComprobanteRetencion])) {
                $asientoArray[$codigoCuentaComprobanteRetencion] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                    'monto' => 0,
                    'detalle' => $ordenPago->getPagoOrdenPago()->getDetalle()
                );
            }
            $asientoArray[$codigoCuentaComprobanteRetencion]['monto'] += $comprobanteRetencion->getMonto();
        }

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        $renglonesAsiento = array();

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::PAGO_PROVEEDORES);

        $proveedor = $ordenPago->getProveedor();

        $denominacion = !$esContraAsiento //
                ? ('Orden de pago n&ordm; ' . $ordenPago->getNumeroOrdenPago() . ' - ' . $proveedor->cuitAndRazonSocial()) //
                : ('Anulaci&oacute;n de la orden de pago n&ordm;' . $ordenPago->getNumeroOrdenPago());

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $proveedor->getRazonSocial(),
            'numeroDocumento' => $proveedor->getCUIT(),
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {
            // Persisto los asientos presupuestarios
            if ($esOrdenPagoObra) {

                $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                        ->crearEjecutadoFromOrdenPagoObra($ordenPago, $asiento, $esContraAsiento);
            } else {
                $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                        ->crearEjecutadoFromOrdenPagoComprobante($ordenPago, $asiento, $esContraAsiento);
            }

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param type $ordenPago
     * @param Cheque $chequeAnterior
     * @param Cheque $chequeNuevo
     * @param TransferenciaBancaria $transferenciaAnterior
     * @param TransferenciaBancaria $transferenciaNueva
     * @param Usuario $usuario
     * @param type $conceptoAsiento
     * @return type
     */
    public function generarAsientoReemplazoPago($ordenPago, Cheque $chequeAnterior = null, Cheque $chequeNuevo = null, TransferenciaBancaria $transferenciaAnterior = null, TransferenciaBancaria $transferenciaNueva = null, Usuario $usuario, $conceptoAsiento) {
        $renglonesAsiento = array();
        $erroresArray = array();

        if ($chequeAnterior) {
            $cuentaBancariaPagoAnterior = $chequeAnterior->getChequera()->getCuenta();
            $detalleAnterior = 'Cheque N&ordm; ' . $chequeAnterior->getNumeroCheque();
            $monto = $chequeAnterior->getMonto();
        } else {
            $cuentaBancariaPagoAnterior = $transferenciaAnterior->getCuenta();
            $detalleAnterior = 'Transferencia N&ordm; ' . $transferenciaAnterior->getNumeroTransferencia();
            $monto = $transferenciaAnterior->getMonto();
        }

        // Renglones relacionados al Pago Anterior        
        $cuentaContableCuentaBancariaPagoAnterior = $cuentaBancariaPagoAnterior->getCuentaContable();

        if ($cuentaContableCuentaBancariaPagoAnterior) {
            $renglonesAsiento[] = array(
                'cuenta' => $cuentaContableCuentaBancariaPagoAnterior,
                'imputacion' => ConstanteTipoOperacionContable::DEBE,
                'monto' => $monto,
                'detalle' => $detalleAnterior
            );
        } else {
            $erroresArray[$cuentaBancariaPagoAnterior->getId()] = 'La cuenta con CBU ' . $cuentaBancariaPagoAnterior->getCbu() . ' no posee una cuenta contable asociada.';
        }

        if ($chequeNuevo) {
            $cuentaBancariaPagoNuevo = $chequeNuevo->getChequera()->getCuenta();
            $detalleNuevo = 'Cheque N&ordm; ' . $chequeNuevo->getNumeroCheque();
        } else {
            $cuentaBancariaPagoNuevo = $transferenciaNueva->getCuenta();
            $detalleNuevo = 'Transferencia N&ordm; ' . $transferenciaNueva->getNumeroTransferencia();
        }

        // Renglones relacionados al Pago Nuevo        
        $cuentaContableCuentaBancariaPagoNuevo = $cuentaBancariaPagoNuevo->getCuentaContable();

        if ($cuentaContableCuentaBancariaPagoNuevo) {
            $renglonesAsiento[] = array(
                'cuenta' => $cuentaContableCuentaBancariaPagoNuevo,
                'imputacion' => ConstanteTipoOperacionContable::HABER,
                'monto' => $monto,
                'detalle' => $detalleNuevo
            );
        } else {
            $erroresArray[$cuentaBancariaPagoNuevo->getId()] = 'La cuenta con CBU ' . $cuentaBancariaPagoNuevo->getCbu() . ' no posee una cuenta contable asociada.';
        }

        $beneficiario = $ordenPago->getBeneficiario();

        $razonSocial = $beneficiario != null //
                ? is_string($beneficiario) //
                        ? $beneficiario //
                        : $beneficiario->getRazonSocial() //
                : null;

        $numeroDocumento = ($beneficiario != null && !is_string($beneficiario)) //
                ? $beneficiario->getNroDocumento() //
                : null;

        $detalleAsiento = 'Reemplazo de pago: ' . $detalleAnterior . ' por ' . $detalleNuevo;

        $datosAsiento = array(
            'denominacion' => $detalleAsiento,
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $razonSocial,
            'numeroDocumento' => $numeroDocumento,
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);

            $this->container->get('request')->getSession()->getFlashBag()->add('error', $mensajeErrorAsientoContable);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param OrdenPagoEgresoValor $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     * @return type
     */
    public function generarAsientoPagoEgresoValor(OrdenPagoEgresoValor $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $renglonesAsiento = array();
        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        $egresoValor = $ordenPago->getEgresoValor();

        $cuentaContableTipoEgresoValor = $egresoValor->getTipoEgresoValor()->getCuentaContable();

        // Renglones relacionados al Egreso de Valor
        if ($cuentaContableTipoEgresoValor) {
            if ($egresoValor->getTipoEgresoValor()->getId() == ConstanteTipoEgresoValor::CAJA_CHICA) {
                // Busco la cc asociada a la gerencia de la caja chica
                $egresoValorGerencia = $emContable->getRepository('ADIFContableBundle:EgresoValor\EgresoValorGerencia')->findOneByIdGerencia($egresoValor->getIdGerencia());
                /* @var $egresoValorGerencia EgresoValorGerencia */

                $cuentaContableTipoEgresoValor = $egresoValorGerencia->getCuentaContable();
            }

            $renglonesAsiento[] = array(
                'cuenta' => $cuentaContableTipoEgresoValor,
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $ordenPago->getTotalBruto(),
                'detalle' => 'Pago de ' . strtolower($egresoValor->getTipoEgresoValor())
            );
        }
        // Si el tipo de egreso de valor NO tiene CuentaContable asociada:
        else {
            $erroresArray[$egresoValor->getTipoEgresoValor()->getId()] = 'No se encontr&oacute; una cuenta contable asociada al tipo: '
                    . $egresoValor->getTipoEgresoValor();
        }

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        $responsableEgresoValor = $egresoValor->getResponsableEgresoValor();

        $denominacion = (!$esContraAsiento ? '' : 'Anulaci&oacute;n de pago de ')
                . $egresoValor->getTipoEgresoValor()
                . ' - ' . $responsableEgresoValor->getNombre()
                . ($egresoValor->getGerencia() ? (' - ' . $egresoValor->getGerencia()) : '');

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $responsableEgresoValor->getRazonSocial(),
            'numeroDocumento' => $responsableEgresoValor->getNroDocumento(),
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromOrdenPagoEgresoValor($ordenPago, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }
        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    public function generarAsientoPagoDevolucionGarantia(OrdenPagoDevolucionGarantia $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $renglonesAsiento = array();
        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        $contrato = $ordenPago->getDevolucionGarantia()
                        ->getCuponGarantia()->getContrato();

        if ($contrato->getEsContratoAlquiler()) {

            $codigoInterno = ConstanteCodigoInternoCuentaContable::DEVOLUCION_GARANTIA_ALQUILER;
        } else {

            $codigoInterno = ConstanteCodigoInternoCuentaContable::DEVOLUCION_GARANTIA;
        }

        // Obtengo la CuentaContable por codigo interno
        $cuentaContableDevolucionGarantia = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                ->findOneByCodigoInterno($codigoInterno);

        // Renglones relacionados a la DevolucionGarantia
        if ($cuentaContableDevolucionGarantia) {

            $renglonesAsiento[] = array(
                'cuenta' => $cuentaContableDevolucionGarantia,
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $ordenPago->getImporte(),
                'detalle' => 'Pago de devoluci&oacute;n de garant&iacute;a'
            );
        }
        // Si NO se encontró ninguna CuentaContable:
        else {
            $erroresArray[$codigoInterno] = 'No se encontr&oacute; una cuenta contable con c&oacute;digo interno: '
                    . $codigoInterno;
        }

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::FINANCIERO);

        /* @var $cliente Cliente */
        $cliente = $contrato->getCliente();

        $denominacion = (!$esContraAsiento ? '' : 'Anulaci&oacute;n de pago - ')
                . 'Devoluci&oacute;n de garant&iacute;a - Contrato ' . $contrato->getNumeroContrato();

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $cliente->getRazonSocial(),
            'numeroDocumento' => $cliente->getNroDocumento(),
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromOrdenPagoDevolucionGarantia($ordenPago, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param MovimientoBancario $movimientoBancario
     * @param Usuario $usuario
     * @return type
     */
    public function generarAsientoMovimientoBancario(MovimientoBancario $movimientoBancario, Usuario $usuario, $esContraAsiento = false) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $renglonesAsiento = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        $cuentaContableOrigen = $movimientoBancario->getCuentaOrigen()->getCuentaContable();
        $cuentaContableDestino = $movimientoBancario->getCuentaDestino()->getCuentaContable();

        if ($cuentaContableOrigen && $cuentaContableDestino) {

            //DEBE
            $renglonesAsiento[] = array(
                'cuenta' => $cuentaContableDestino,
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $movimientoBancario->getMonto(),
                'detalle' => 'Transferencia de ' . $movimientoBancario->getCuentaOrigen() . ' a ' . $movimientoBancario->getCuentaDestino()
            );

            //HABER
            $renglonesAsiento[] = array(
                'cuenta' => $cuentaContableOrigen,
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                'monto' => $movimientoBancario->getMonto(),
                'detalle' => 'Transferencia de ' . $movimientoBancario->getCuentaOrigen() . ' a ' . $movimientoBancario->getCuentaDestino()
            );
        } else {
            if (!$cuentaContableOrigen) {
                $erroresArray[$movimientoBancario->getCuentaOrigen()->getId()] = 'No se encontr&oacute; una cuenta contable asociada a la cuenta bancaria de origen';
            } else {
                if (!$cuentaContableDestino) {
                    $erroresArray[$movimientoBancario->getCuentaDestino()->getId()] = 'No se encontr&oacute; una cuenta contable asociada a la cuenta bancaria de destino';
                }
            }
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        $datosAsiento = array(
            'denominacion' => (!$esContraAsiento ? 'T' : 'Anulaci&oacute;n de t' ) . 'ransacci&oacute;n bancaria ' . $movimientoBancario->getReferencia(),
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => null,
            'numeroDocumento' => null
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromMovimientoBancario($movimientoBancario, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param MovimientoMinisterial $movimientoMinisterial
     * @param Usuario $usuario
     * @return type
     */
    public function generarAsientoMovimientoMinisterial(MovimientoMinisterial $movimientoMinisterial, Usuario $usuario, $esContraAsiento = false) {


        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $asientoArray = array();
        $erroresArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $cuentaContableOrigen = $movimientoMinisterial->getEsIngreso() ? $movimientoMinisterial->getConceptoTransaccionMinisterial()->getCuentaContable() : $movimientoMinisterial->getCuentaBancariaADIF()->getCuentaContable();
        $cuentaContableDestino = $movimientoMinisterial->getEsIngreso() ? $movimientoMinisterial->getCuentaBancariaADIF()->getCuentaContable() : $movimientoMinisterial->getConceptoTransaccionMinisterial()->getCuentaContable();

        if ($cuentaContableOrigen && $cuentaContableDestino) {

            $detalle_renglon = 'Movimiento ministerial de ' . ($movimientoMinisterial->getEsIngreso() ? $movimientoMinisterial->getConceptoTransaccionMinisterial() : $movimientoMinisterial->getCuentaBancariaADIF()) . ' a ' . ($movimientoMinisterial->getEsIngreso() ? $movimientoMinisterial->getCuentaBancariaADIF() : $movimientoMinisterial->getConceptoTransaccionMinisterial());

            // DEBE
            $codigo_cuenta_contable_destino = $cuentaContableDestino->getCodigoCuentaContable();
            $asientoArray[$codigo_cuenta_contable_destino] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $movimientoMinisterial->getMonto(),
                'detalle' => $detalle_renglon
            ); // 
//            $renglonesAsiento[] = array(
//                'cuenta' => $cuentaContableDestino,
//                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
//                'monto' => $movimientoMinisterial->getMonto(),
//                'detalle' => $detalle_renglon
//            );
            // HABER
            $codigo_cuenta_contable_origen = $cuentaContableOrigen->getCodigoCuentaContable();
            $asientoArray[$codigo_cuenta_contable_origen] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                'monto' => $movimientoMinisterial->getMonto(),
                'detalle' => $detalle_renglon
            ); // 
//            $renglonesAsiento[] = array(
//                'cuenta' => $cuentaContableOrigen,
//                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
//                'monto' => $movimientoMinisterial->getMonto(),
//                'detalle' => $detalle_renglon
//            );
        } else {

            if (!$cuentaContableOrigen) {

                $codigoCuentaOrigen = $movimientoMinisterial->getEsIngreso() ? $movimientoMinisterial->getConceptoTransaccionMinisterial()->getId() : $movimientoMinisterial->getCuentaBancariaADIF()->getId();

                $erroresArray[$codigoCuentaOrigen] = 'No se encontr&oacute; una cuenta contable asociada a la cuenta bancaria de origen';
            } else {
                if (!$cuentaContableDestino) {

                    $codigoCuentaDestino = $movimientoMinisterial->getEsIngreso() ? $movimientoMinisterial->getCuentaBancariaADIF()->getId() : $movimientoMinisterial->getConceptoTransaccionMinisterial()->getId();

                    $erroresArray[$codigoCuentaDestino] = 'No se encontr&oacute; una cuenta contable asociada a la cuenta bancaria de destino';
                }
            }
        }

        $renglones_asiento = array();

        foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {

            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);

            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => $datos_movimiento['monto'],
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }


        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        $datosAsiento = array(
            'denominacion' => (!$esContraAsiento ? 'T' : 'Anulaci&oacute;n de t' ) . 'ransacci&oacute;n ministerial ' . $movimientoMinisterial->getReferencia(),
            'razonSocial' => null,
            'numeroDocumento' => null,
            'concepto' => $conceptoAsiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario,
            'fecha_contable' => new DateTime()
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromMovimientoMinisterial($movimientoMinisterial, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param type $codigoCuentaContable
     * @param type $centroCosto
     * @return type
     */
    private function getCuentaContableConCentroCosto($codigoCuentaContable, $centroCosto) {

        $codigoCentroCosto = str_pad($centroCosto->getCodigo(), 2, '0', STR_PAD_LEFT);

        $segmentoArray = explode('.', $codigoCuentaContable);

        if ($segmentoArray[2] != '08') {
            // Sólo hay que modificar el centro de costo si es distinto de 08
            $segmentoArray[2] = $codigoCentroCosto;
        }

        return implode('.', $segmentoArray);
    }

    /**
     * 
     * @param OrdenPagoReconocimientoEgresoValor $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     * @return type
     */
    public function generarAsientoPagoReconocimientoEgresoValor(OrdenPagoReconocimientoEgresoValor $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $renglonesAsiento = array();
        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        $cuentaExcedente = $ordenPago->getReconocimientoEgresoValor()->getEgresoValor()->getTipoEgresoValor()->getCuentaContablReconocimiento();
        // Renglones relacionados al Reconocimiento de Egreso de Valor

        $renglonesAsiento[] = array(
            'cuenta' => $cuentaExcedente,
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
            'monto' => $ordenPago->getImporte(),
            'detalle' => 'Pago de ' . strtolower($ordenPago->getReconocimientoEgresoValor()->getEgresoValor()->getTipoEgresoValor())
        );

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        $egresoValor = $ordenPago->getReconocimientoEgresoValor()
                ->getEgresoValor();

        $responsableEgresoValor = $egresoValor->getResponsableEgresoValor();

        $denominacion = (!$esContraAsiento ? '' :
                        'Anulaci&oacute;n de pago de ') . $egresoValor->getTipoEgresoValor()
                . ' - ' . $responsableEgresoValor->getNombre()
                . ($egresoValor->getGerencia() ? (' - ' . $egresoValor->getGerencia()) : '');

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $responsableEgresoValor->getRazonSocial(),
            'numeroDocumento' => $responsableEgresoValor->getNroDocumento(),
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container
                    ->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromOrdenPagoReconocimientoEgresoValor($ordenPago, $esContraAsiento, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {

            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param ReconocimientoEgresoValor $reconocimientoEgresoValor
     * @param Usuario $usuario
     * @param type $esContraAsiento
     * @return type
     */
    public function generarAsientoCierreReconocimientoEgresoValor(ReconocimientoEgresoValor $reconocimientoEgresoValor, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $renglonesAsiento = array();

        $cuentaExcedente = $reconocimientoEgresoValor->getEgresoValor()
                        ->getTipoEgresoValor()->getCuentaContablReconocimiento();

        $cuentaGanancia = $reconocimientoEgresoValor->getEgresoValor()
                        ->getTipoEgresoValor()->getCuentaContablGanancia();

        $renglonesAsiento[] = array(
            'cuenta' => $cuentaExcedente,
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
            'monto' => $reconocimientoEgresoValor->getMonto(),
            'detalle' => 'Cierre de gasto de ' . strtolower($reconocimientoEgresoValor->getEgresoValor()->getTipoEgresoValor())
        );

        $renglonesAsiento[] = array(
            'cuenta' => $cuentaGanancia,
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
            'monto' => $reconocimientoEgresoValor->getMonto(),
            'detalle' => 'Cierre de gasto de ' . strtolower($reconocimientoEgresoValor->getEgresoValor()->getTipoEgresoValor())
        );

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        $egresoValor = $reconocimientoEgresoValor->getEgresoValor();

        $responsableEgresoValor = $egresoValor->getResponsableEgresoValor();

        $denominacion = (!$esContraAsiento ? '' :
                        'Anulaci&oacute;n de pago de ') .
                $egresoValor->getTipoEgresoValor() . ' - ' . $responsableEgresoValor->getNombre()
                . ($egresoValor->getGerencia() ? (' - ' . $egresoValor->getGerencia()) : '');

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $responsableEgresoValor->getRazonSocial(),
            'numeroDocumento' => $responsableEgresoValor->getNroDocumento()
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        // Persisto los asientos presupuestarios
        $mensajeErrorAsientoPresupuestarioAsiento = $this->container
                ->get('adif.contabilidad_presupuestaria_service')
                ->crearEjecutadoFromCierreReconocimientoEgresoValor($reconocimientoEgresoValor, $esContraAsiento, $asiento);

        // Si el asiento presupuestario falló
        if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
            $this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $mensajeErrorAsientoPresupuestarioAsiento);
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '') {

            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param type $ids_renglones
     * @param type $usuario
     * @param type $esConciliacion
     * @param Conciliacion $conciliacion
     * @param type $fecha_contable
     * @return type
     */
    public function generarAsientosConciliacion($ids_renglones, $usuario, $esConciliacion, Conciliacion $conciliacion, $fecha_contable = null) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $renglones_asiento = array();

        $erroresArray = array();

        $cuentaBancaria = $conciliacion->getCuenta();
        $cuentaContableCuentaBancaria = $cuentaBancaria->getCuentaContable();

        $total = 0;

        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        //renglones del comprobante  
        

        foreach ($ids_renglones as $renglon) {

            /* @var $renglon RenglonConciliacion */
            $total += $renglon->getMonto();
            $acumulado = isset($asientoArray[$renglon->getConceptoConciliacion()->getCuentaContable()->getCodigoCuentaContable()]['monto']) ? $asientoArray[$renglon->getConceptoConciliacion()->getCuentaContable()->getCodigoCuentaContable()]['monto'] : 0;
            $acumulado += $renglon->getMonto();
            $asientoArray[$renglon->getConceptoConciliacion()->getCuentaContable()->getCodigoCuentaContable()] = array(
                'imputacion' => (($acumulado < 0 && $esConciliacion) || ($acumulado >= 0 && !$esConciliacion)) ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumulado,
                'detalle' => ($esConciliacion ? 'Conciliaci&oacute;n' : 'Desconciliaci&oacute;n') . ' de cuenta ' . $conciliacion->getCuenta()
            );
        }


        foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {

            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);

            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => abs($datos_movimiento['monto']),
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }

        //renglones del banco        
        $renglones_asiento[] = array(
            'cuenta' => $cuentaContableCuentaBancaria,
            'imputacion' => (($total < 0 && $esConciliacion) || ($total >= 0 && !$esConciliacion)) ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
            'monto' => abs($total),
            'detalle' => ($esConciliacion ? 'Conciliaci&oacute;n' : 'Desconciliaci&oacute;n') . ' de cuenta ' . $conciliacion->getCuenta()
        );

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        $datosAsiento = array(
            'denominacion' => ($esConciliacion ? 'Conciliaci&oacute;n' :
                    'Desconciliaci&oacute;n') .
            ' de cuenta ' . $conciliacion->getCuenta(),
            'concepto' => $conceptoAsiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario,
            'razonSocial' => null,
            'numeroDocumento' => null
        );

        if ($fecha_contable) {
            $datosAsiento['fecha_contable'] = $fecha_contable;
        }

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container
                    ->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromGastoBancario($ids_renglones, $esConciliacion, $conciliacion, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {

                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param ComprobanteVenta $comprobante
     * @param type $usuario
     * @return type
     */
    public function generarAsientosComprobantesVenta($comprobante, $usuario, $offsetNumeroAsiento = 0, $esContraAsiento = false) {

        $asientoArray = array();
        $erroresArray = array();

        $revertirImputacion = ($esContraAsiento && ($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteVenta::NOTA_CREDITO)) || (!$esContraAsiento && ($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_CREDITO));

        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $total_asiento = 0;

        $detalleRenglon = 'Asiento ventas';

        $codigoClaseContato = $comprobante->getCodigoClaseContrato();

        $claseContato = $emContable->getRepository('ADIFContableBundle:Facturacion\ClaseContrato')
                ->findOneByCodigo($codigoClaseContato);

        /* @var $comprobante ComprobanteVenta */
        if ($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES) {

            // Si el comprobante es una nota de débito de intereses va a una cuenta particular
            $configuracion_cuenta_nota_debito_intereses = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_NOTA_DEBITO_INTERESES);

            #$cuenta_contable = $configuracion_cuenta_nota_debito_intereses->getCuentaContable();
            $cuenta_contable = $claseContato->getCuentaContable();
            $cuenta_ingreso = $claseContato->getCuentaIngreso();
        } elseif ($comprobante->getEsCupon() && $comprobante->getEsCuponGarantia()) {

            // Obtengo la CuentaContable con codigo interno DEVOLUCION_GARANTIA_ALQUILER
            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::DEVOLUCION_GARANTIA_ALQUILER);
        } else {
            $cuenta_contable = $claseContato->getCuentaContable();
            $cuenta_ingreso = $claseContato->getCuentaIngreso();
        }

        if (!$cuenta_contable || !$cuenta_ingreso) {
            if ($cuenta_contable == null) {
                $erroresArray[$renglonComprobante->getConceptoVentaGeneral()->getId()] = 'No se encontr&oacute; una cuenta cr&eacute;dito asociada al tipo: '. $renglonComprobante->getConceptoVentaGeneral();
            } else {
                $erroresArray[$renglonComprobante->getConceptoVentaGeneral()->getId()] = 'No se encontr&oacute; una cuenta ingreso asociada al tipo: '. $renglonComprobante->getConceptoVentaGeneral();
            }
        }

        $codigo_cuenta_credito = $cuenta_contable->getCodigoCuentaContable();
        $codigo_cuenta_ingreso = $cuenta_ingreso->getCodigoCuentaContable();

        $codigo_cuenta = $codigo_cuenta_credito;

        foreach ($comprobante->getRenglonesComprobante() as $renglonComprobante) {

            /* @var $renglonComprobante RenglonComprobante */

            $acumulado = isset($asientoArray[$codigo_cuenta]['monto']) ? $asientoArray[$codigo_cuenta]['monto'] : 0;

            //Cuenta asociada al comprobante
            $asientoArray[$codigo_cuenta] = array(
                'imputacion' => $revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumulado + $renglonComprobante->getMontoNeto(),
                'detalle' => $detalleRenglon
            );

            $total_asiento += $renglonComprobante->getMontoNeto();

            if ($renglonComprobante->getAlicuotaIva()->getValor() != ConstanteAlicuotaIva::ALICUOTA_0) {
                $cuenta_iva = $renglonComprobante->getAlicuotaIva()->getCuentaContableDebito();

                if ($cuenta_iva) {
                    $codigo_cuenta_iva = $cuenta_iva->getCodigoCuentaContable();
                } else {
                    $erroresArray[$cuenta_iva->getId()] = 'El concepto de IVA no posee una cuenta contable asociada.';
                }

                $acumulado_iva = isset($asientoArray[$codigo_cuenta_iva]['monto']) ? $asientoArray[$codigo_cuenta_iva]['monto'] : 0;
                //Cuenta asociada al iva
                $asientoArray[$codigo_cuenta_iva] = array(
                    'imputacion' => $revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $acumulado_iva + $renglonComprobante->getMontoIva(),
                    'detalle' => $detalleRenglon
                );

                $total_asiento += $renglonComprobante->getMontoIva();
            }
        }

        //Cuentas de percepciones
        foreach ($comprobante->getRenglonesPercepcion() as $renglon_percepcion) {

            /* @var $renglon_percepcion RenglonPercepcion */
            if ($renglon_percepcion->getJurisdiccion()) {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneBy(array(
                    'conceptoPercepcion' => $renglon_percepcion->getConceptoPercepcion(), //
                    'jurisdiccion' => $renglon_percepcion->getJurisdiccion())
                );
            } else {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneByConceptoPercepcion($renglon_percepcion->getConceptoPercepcion());
            }

            if ($cuenta_percepcion) {
                $codigo_cuenta_percepcion = $cuenta_percepcion->getCuentaContableDebito()->getCodigoCuentaContable();
            } else {
                $erroresArray[$renglon_percepcion->getConceptoPercepcion()->getId()] = 'El concepto de percepci&oacute;n ' . $renglon_percepcion->getConceptoPercepcion() . ' no posee una cuenta contable asociada.';
            }

            $acumulado_percepcion = isset($asientoArray[$codigo_cuenta_percepcion]['monto']) ? $asientoArray[$codigo_cuenta_percepcion]['monto'] : 0;

            //Cuenta asociada a la percepcion
            $asientoArray[$codigo_cuenta_percepcion] = array(
                'imputacion' => $revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumulado_percepcion + $renglon_percepcion->getMonto(),
                'detalle' => $detalleRenglon
            );

            $total_asiento += $renglon_percepcion->getMonto();
        }

        /* @var $cliente Cliente */
        $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')
                ->find($comprobante->getIdCliente());
        if ($cliente->getCuentaContable()) {
            $codigo_cuenta_cliente = $cliente->getCuentaContable()->getCodigoCuentaContable();
        } else {
            $erroresArray[$cliente->getId()] = 'El cliente ' . $cliente->getClienteProveedor()->getRazonSocial() . ' no posee una cuenta contable asociada.';
        }

        $asientoArray[$codigo_cuenta_ingreso] = array(
            'imputacion' => $revertirImputacion ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
            'monto' => $total_asiento,
            'detalle' => $detalleRenglon
        );

        $renglones_asiento = array();

        foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {

            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);

            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => $datos_movimiento['monto'],
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::VENTAS);
        /* @var $comprobante ComprobanteVenta */

        $letra = ($comprobante->getLetraComprobante() != null ) ? ' ' . $comprobante->getLetraComprobante()->__toString() : '';

        $denominacion = ($esContraAsiento ? 'Anulaci&oacute;n - ' : '')
                . $comprobante->getTipoComprobante()->getNombre()
                . $letra
                . ' N&ordm; ' . $comprobante->getNumeroCompleto();

        if ($comprobante->getContrato() != null) {
            $denominacion .= ' - Contrato N&ordm; ' . $comprobante->getContrato()->getNumeroContrato();
        }

        $denominacion .= ' - ' . $comprobante->getCliente();

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $concepto_asiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario,
            'razonSocial' => $cliente->getRazonSocial(),
            'numeroDocumento' => $cliente->getNroDocumento(),
            'comprobante' => $comprobante
        );


        $asiento = $this->generarAsientoContable($datosAsiento, $offsetNumeroAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorDevengado = '';
        $mensajeErrorEjecutado = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $mensajeErrorAsientoContable);
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorDevengado = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearDevengadoFromComprobanteVenta($comprobante, $asiento, $esContraAsiento);

            $mensajeErrorEjecutado = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromComprobanteVenta($comprobante, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorDevengado != '' && $mensajeErrorEjecutado != '') {

                // Si hubo un error en el asiento presupuestario - Devengado
                if ($mensajeErrorDevengado != '') {

                    $this->container->get('request')->getSession()->getFlashBag()
                            ->add('error', $mensajeErrorDevengado);
                }

                // Si hubo un error en el asiento presupuestario - Ejecutado
                if ($mensajeErrorEjecutado != '') {

                    $this->container->get('request')->getSession()->getFlashBag()
                            ->add('error', $mensajeErrorEjecutado);
                }
            }
        }

        // Si hubo algun error
        if ($mensajeErrorDevengado != '' && $mensajeErrorEjecutado != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param ComprobanteVenta $comprobante
     * @param type $usuario
     * @return type
     */
    public function generarAsientosComprobanteVentaGeneral($comprobante, $usuario, $offsetNumeroAsiento = 0, $esContraAsiento = false) {

        $asientoArray = array();
        $erroresArray = array();

        $revertirImputacion = ($esContraAsiento && ($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteVenta::NOTA_CREDITO)) || (!$esContraAsiento && ($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_CREDITO));

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $total_asiento = 0;

        $detalleRenglon = 'Asiento ventas';
        $codigo_cuenta_credito = '';
        $codigo_cuenta_ingreso = '';

        foreach ($comprobante->getRenglonesComprobante() as $renglonComprobante) {

            /* @var $renglonComprobante RenglonComprobanteVentaGeneral */

            $cuenta_contable = $renglonComprobante->getConceptoVentaGeneral()->getCuentaContable();
            $cuenta_contable_ingreso = $renglonComprobante->getConceptoVentaGeneral()->getCuentaIngreso();

            if ($cuenta_contable && $cuenta_contable_ingreso) {

                $codigo_cuenta_credito = $cuenta_contable->getCodigoCuentaContable();
                $codigo_cuenta_ingreso = $cuenta_contable_ingreso->getCodigoCuentaContable();
                $codigo_cuenta = $codigo_cuenta_credito;

                $acumulado = isset($asientoArray[$codigo_cuenta]['monto']) ? $asientoArray[$codigo_cuenta]['monto'] : 0;

                // Cuenta asociada al comprobante
                $asientoArray[$codigo_cuenta] = array(
                    'imputacion' => $revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $acumulado + $renglonComprobante->getMontoNeto(),
                    'detalle' => $detalleRenglon
                );

                $total_asiento += $renglonComprobante->getMontoNeto();

                if ($renglonComprobante->getAlicuotaIva()->getValor() != ConstanteAlicuotaIva::ALICUOTA_0) {

                    $cuenta_iva = $renglonComprobante->getAlicuotaIva()->getCuentaContableDebito();

                    if ($cuenta_iva) {
                        $codigo_cuenta_iva = $cuenta_iva->getCodigoCuentaContable();
                    } else {
                        $erroresArray[$cuenta_iva->getId()] = 'El concepto de IVA no posee una cuenta contable asociada.';
                    }

                    $acumulado_iva = isset($asientoArray[$codigo_cuenta_iva]['monto']) ? $asientoArray[$codigo_cuenta_iva]['monto'] : 0;

                    // Cuenta asociada al iva
                    $asientoArray[$codigo_cuenta_iva] = array(
                        'imputacion' => $revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                        'monto' => $acumulado_iva + $renglonComprobante->getMontoIva(),
                        'detalle' => $detalleRenglon
                    );

                    $total_asiento += $renglonComprobante->getMontoIva();
                }


            } else {
                if ($cuenta_contable == null) {
                    $erroresArray[$renglonComprobante->getConceptoVentaGeneral()->getId()] = 'No se encontr&oacute; una cuenta cr&eacute;dito asociada al tipo: '. $renglonComprobante->getConceptoVentaGeneral();
                } else {
                    $erroresArray[$renglonComprobante->getConceptoVentaGeneral()->getId()] = 'No se encontr&oacute; una cuenta ingreso asociada al tipo: '. $renglonComprobante->getConceptoVentaGeneral();
                }
            }
        }

        //Cuentas de percepciones
        foreach ($comprobante->getRenglonesPercepcion() as $renglon_percepcion) {

            /* @var $renglon_percepcion RenglonPercepcion */
            if ($renglon_percepcion->getJurisdiccion()) {

                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneBy(array(
                    'conceptoPercepcion' => $renglon_percepcion->getConceptoPercepcion(), //
                    'jurisdiccion' => $renglon_percepcion->getJurisdiccion())
                );
            } else {

                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneByConceptoPercepcion($renglon_percepcion->getConceptoPercepcion());
            }

            if ($cuenta_percepcion) {

                $codigo_cuenta_percepcion = $cuenta_percepcion->getCuentaContableDebito()->getCodigoCuentaContable();
            } else {

                $erroresArray[$renglon_percepcion->getConceptoPercepcion()->getId()] = 'El concepto de percepci&oacute;n ' . $renglon_percepcion->getConceptoPercepcion() . ' no posee una cuenta contable asociada.';
            }

            $acumulado_percepcion = isset($asientoArray[$codigo_cuenta_percepcion]['monto']) ? $asientoArray[$codigo_cuenta_percepcion]['monto'] : 0;

            // Cuenta asociada a la percepcion
            $asientoArray[$codigo_cuenta_percepcion] = array(
                'imputacion' => $revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumulado_percepcion + $renglon_percepcion->getMonto(),
                'detalle' => $detalleRenglon
            );

            $total_asiento += $renglon_percepcion->getMonto();
        }

        $asientoArray[$codigo_cuenta_ingreso] = array(
            'imputacion' => $revertirImputacion ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
            'monto' => $total_asiento,
            'detalle' => $detalleRenglon
        );

        $renglones_asiento = array();

        foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {

            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);

            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => $datos_movimiento['monto'],
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::VENTAS);
        /* @var $comprobante ComprobanteVenta */

        $letra = ($comprobante->getLetraComprobante() != null ) ? ' ' . $comprobante->getLetraComprobante()->__toString() : '';

        $denominacion = ($esContraAsiento ? 'Anulaci&oacute;n - ' : '')
                . $comprobante->getTipoComprobante()->getNombre()
                . $letra
                . ' N&ordm; ' . $comprobante->getNumeroCompleto();

        if ($comprobante->getContrato() != null) {
            $denominacion .= ' - Contrato N&ordm; ' . $comprobante->getContrato()->getNumeroContrato();
        }

        $cliente = $comprobante->getCliente();

        $denominacion .= ' - ' . $cliente;

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $concepto_asiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario,
            'razonSocial' => $cliente->getRazonSocial(),
            'numeroDocumento' => $cliente->getNroDocumento(),
            'comprobante' => $comprobante
        );


        $asiento = $this->generarAsientoContable($datosAsiento, $offsetNumeroAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorDevengado = '';
        $mensajeErrorEjecutado = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $mensajeErrorAsientoContable);
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorDevengado = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearDevengadoFromComprobanteVentaGeneral($comprobante, $asiento, $esContraAsiento);

            $mensajeErrorEjecutado = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromComprobanteVenta($comprobante, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorDevengado != '' && $mensajeErrorEjecutado != '') {

                // Si hubo un error en el asiento presupuestario - Devengado
                if ($mensajeErrorDevengado != '') {

                    $this->container->get('request')->getSession()->getFlashBag()
                            ->add('error', $mensajeErrorDevengado);
                }

                // Si hubo un error en el asiento presupuestario - Ejecutado
                if ($mensajeErrorEjecutado != '') {

                    $this->container->get('request')->getSession()->getFlashBag()
                            ->add('error', $mensajeErrorEjecutado);
                }
            }
        }

        // Si hubo algun error
        if ($mensajeErrorDevengado != '' && $mensajeErrorEjecutado != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param Liquidacion $liquidacion
     * @param type $usuario
     * @param type $totalAsiento
     * @return type
     */
    public function generarAsientoSueldos(Liquidacion $liquidacion, $usuario, &$totalAsiento, &$numerosAsientos) {

        $asiento_sueldos = array();
        $asiento_contribuciones = array();
		$numerosAsientos = array(); 

        $erroresArray = array();
		$mensajesErroresAsientos = '';

        $total_asiento_sueldos = 0;
        $total_asiento_contribuciones['contribuciones'] = 0;
        $total_asiento_contribuciones['art'] = 0;

        $tipos_concepto_asiento_sueldos = array(
            TipoConcepto::__REMUNERATIVO,
            TipoConcepto::__NO_REMUNERATIVO,
            TipoConcepto::__DESCUENTO,
            TipoConcepto::__APORTE,
            TipoConcepto::__CUOTA_SINDICAL_APORTES,
            TipoConcepto::__CALCULO_GANANCIAS
        );

        $tipos_concepto_asiento_contribuciones = array(
            TipoConcepto::__CONTRIBUCIONES,
            TipoConcepto::__CUOTA_SINDICAL_CONTRIBUCIONES
        );

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        setlocale(LC_ALL, "es_AR.UTF-8");
        $nombre_liquidacion = ucfirst(strftime("%B %Y", $liquidacion->getFechaCierreNovedades()->getTimestamp()));

        $detalle_renglon_sueldos = 'Asiento sueldos ' . $nombre_liquidacion;
        $detalle_renglon_contribuciones = 'Asiento cargas sociales ' . $nombre_liquidacion;

        foreach ($liquidacion->getLiquidacionEmpleados() as $liquidacionEmpleado) {

            /* @var $liquidacionEmpleado LiquidacionEmpleado */
            $centro_costo = $liquidacionEmpleado->getEmpleado()->getGerencia()->getCentroCosto();

            // Renglon asiento del básico
            $configuracion_cuenta_basico = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')
                    ->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_BASICO);
            $cuenta_contable = $configuracion_cuenta_basico->getCuentaContable();

            $codigo_cuenta = $this->getCuentaContableConCentroCosto($cuenta_contable->getCodigoCuentaContable(), $centro_costo);
            $acumulado = isset($asiento_sueldos[$codigo_cuenta]['monto']) ? $asiento_sueldos[$codigo_cuenta]['monto'] : 0;

            //Cuenta asociada al comprobante
            $asiento_sueldos[$codigo_cuenta] = array(
                'imputacion' => ConstanteTipoOperacionContable::DEBE,
                'monto' => $acumulado + $liquidacionEmpleado->getBasico() + $liquidacionEmpleado->getRedondeo(),
                'detalle' => $detalle_renglon_sueldos
            );

            $total_asiento_sueldos += $liquidacionEmpleado->getBasico() + $liquidacionEmpleado->getRedondeo();

            foreach ($liquidacionEmpleado->getLiquidacionEmpleadoConceptos() as $liquidacionEmpleadoConcepto) {
                /* @var $liquidacionEmpleadoConcepto LiquidacionEmpleadoConcepto */

                $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto()->getIdCuentaContable());

                $codigo_cuenta = $cuenta_contable->getCodigoCuentaContable();

                $naturaleza_cuenta = $cuenta_contable->getSegmentoOrden(1);

                $imputacion = ConstanteTipoOperacionContable::HABER;

                if ($naturaleza_cuenta == ConstanteNaturalezaCuenta::GASTO) {
                    // Si la naturaleza es un gasto, busco el centro de costos del empleado segun su gerencia
                    $codigo_cuenta = $this->getCuentaContableConCentroCosto($codigo_cuenta, $centro_costo);
                    
                    if ( $liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto()->getEsNegativo() ) {
                        //Es negativo (para Sport Club, en lugar de asignar nro negativo al DEBE, asigna Positivo al Haber
                        $imputacion = ConstanteTipoOperacionContable::HABER;
                    }
                    else {
                        $imputacion = ConstanteTipoOperacionContable::DEBE;
                    }

                }

                if (in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto()->getIdTipoConcepto()->getId(), $tipos_concepto_asiento_sueldos)) {
                    //Es uno de los conceptos del asiento de sueldos
                    $acumulado = isset($asiento_sueldos[$codigo_cuenta]['monto']) ? $asiento_sueldos[$codigo_cuenta]['monto'] : 0;
                    //Cuenta asociada al comprobante
                    $asiento_sueldos[$codigo_cuenta] = array(
                        'imputacion' => $imputacion,
                        'monto' => $acumulado + $liquidacionEmpleadoConcepto->getMonto(),
                        'detalle' => $detalle_renglon_sueldos
                    );

                    $total_asiento_sueldos += $liquidacionEmpleadoConcepto->getMonto() * ($imputacion == ConstanteTipoOperacionContable::DEBE ? 1 : -1);
                } else {
                    if (in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto()->getIdTipoConcepto()->getId(), $tipos_concepto_asiento_contribuciones)) {
                        //Es uno de los conceptos del asiento de contribuciones y no es la liq de SAC
                        $acumulado = isset($asiento_contribuciones[$codigo_cuenta]['monto']) ? $asiento_contribuciones[$codigo_cuenta]['monto'] : 0;
                        //Cuenta asociada al comprobante
                        $asiento_contribuciones[$codigo_cuenta] = array(
                            'imputacion' => $imputacion,
                            'monto' => $acumulado + $liquidacionEmpleadoConcepto->getMonto(),
                            'detalle' => $detalle_renglon_contribuciones
                        );

                        if ($liquidacionEmpleadoConcepto->getConceptoVersion()->getCodigo() == Concepto::__CODIGO_ART_FIJA || $liquidacionEmpleadoConcepto->getConceptoVersion()->getCodigo() == Concepto::__CODIGO_ART_VARIABLE) {
                            //Los conceptos de art van a la cuenta ART a pagar
                            $total_asiento_contribuciones['art'] += $liquidacionEmpleadoConcepto->getMonto();
                        } else {
                            $total_asiento_contribuciones['contribuciones'] += $liquidacionEmpleadoConcepto->getMonto();
                        }
                    }
                }
            }
        }
		
		//var_dump($total_asiento_contribuciones);

        // HABER de sueldos a pagar        
        $configuracion_cuenta_sueldos_a_pagar = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')
                ->findOneByCodigo($liquidacion->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC ? ConfiguracionCuentaContableSueldos::__CODIGO_PASIVO_SUELDOS_SAC : ConfiguracionCuentaContableSueldos::__CODIGO_PASIVO_SUELDOS);

        $asiento_sueldos[$configuracion_cuenta_sueldos_a_pagar->getCuentaContable()->getCodigoCuentaContable()] = array(
            'imputacion' => ConstanteTipoOperacionContable::HABER,
            'monto' => round($total_asiento_sueldos, 2),
            'detalle' => $detalle_renglon_sueldos
        );


        $renglones_asiento = array();

        foreach ($asiento_sueldos as $codigo_cuenta => $datos_movimiento) {
            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);

            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => $datos_movimiento['monto'],
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::DEVENGAMIENTO_SUELDOS);

        $datosAsiento = array(
            'denominacion' => 'Asiento devengamiento sueldos ' . $nombre_liquidacion,
            'concepto' => $concepto_asiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario,
            'razonSocial' => AdifDatos::RAZON_SOCIAL,
			'numeroDocumento' => AdifDatos::CUIT
        );

        $asientoDevengamientoSueldo = $this->generarAsientoContable($datosAsiento);
		
		$numerosAsientos[] = $asientoDevengamientoSueldo->getNumeroAsiento();
		//$ids[] = $asientoDevengamientoSueldo->getId();
		
		$mensajesErroresAsientos = $this->getMensajeError($asientoDevengamientoSueldo, $erroresArray);
		if ($mensajesErroresAsientos != '') {
			$mensajesErroresAsientos = '<span>Error en asiento contable de devengamiento de sueldos.</span>&nbsp;' . $mensajesErroresAsientos;
		}
		
        // if ($liquidacion->getTipoLiquidacion()->getId() != TipoLiquidacion::__SAC) {

			$mensajesErroresCargaSociales = '';
            //Si no es liquidacion sac, genero el asiento de contribuciones
            if ($total_asiento_contribuciones['contribuciones']) {
                //HABER de cargas sociales
                $configuracion_cuenta_cargas_sociales_a_pagar = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')
                        ->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_PASIVO_CARGAS);

				// 2.1.00.02.03.00.00.00 - CONTRIBUCIONES SEG SOCIAL
                $asiento_contribuciones[$configuracion_cuenta_cargas_sociales_a_pagar->getCuentaContable()->getCodigoCuentaContable()] = array(
                    'imputacion' => ConstanteTipoOperacionContable::HABER,
                    'monto' => round($total_asiento_contribuciones['contribuciones'], 2),
                    'detalle' => $detalle_renglon_contribuciones
                );
            }
		
            if ($total_asiento_contribuciones['art']) {
                //HABER de ART
                $configuracion_cuenta_art_a_pagar = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')
                        ->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_PASIVO_ART);

				// 2.1.00.02.15.00.00.00 - ART a Pagar 
                $asiento_contribuciones[$configuracion_cuenta_art_a_pagar->getCuentaContable()->getCodigoCuentaContable()] = array(
                    'imputacion' => ConstanteTipoOperacionContable::HABER,
                    'monto' => round($total_asiento_contribuciones['art'], 2),
                    'detalle' => $detalle_renglon_contribuciones
                );
            }

            $renglones_asiento = array();
	
            foreach ($asiento_contribuciones as $codigo_cuenta => $datos_movimiento) {
                $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                        ->findOneByCodigoCuentaContable($codigo_cuenta);
                if ($cuenta_contable) {
                    $renglones_asiento[] = array(
                        'cuenta' => $cuenta_contable,
                        'imputacion' => $datos_movimiento['imputacion'],
                        'monto' => $datos_movimiento['monto'],
                        'detalle' => $datos_movimiento['detalle']
                    );
                } else {
                    $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
                }
            }

            $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                    ->findOneByCodigo(ConstanteConceptoAsientoContable::DEVENGAMIENTO_SUELDOS);

            $datosAsiento = array(
                'denominacion' => 'Asiento cargas sociales ' . $nombre_liquidacion,
                'razonSocial' => AdifDatos::RAZON_SOCIAL,
                'numeroDocumento' => AdifDatos::CUIT,
                'concepto' => $concepto_asiento,
                'renglones' => $renglones_asiento,
                'usuario' => $usuario
            );

            $totalAsiento = round($total_asiento_contribuciones['contribuciones'], 2) + round($total_asiento_contribuciones['art'], 2);

            $asientoCargasSociales = $this->generarAsientoContable($datosAsiento, 1);
			
			$numerosAsientos[] = $asientoCargasSociales->getNumeroAsiento();
			//$ids[] = $asientoCargasSociales->getId();

            $mensajesErroresCargaSociales = $this->getMensajeError($asientoCargasSociales, $erroresArray);
			if ($mensajesErroresCargaSociales != '') {
				$mensajesErroresCargaSociales = '<span>Error en asiento contable de cargas sociales.</span>&nbsp;' . $mensajesErroresCargaSociales;
			}
			
			$mensajesErroresAsientos .= $mensajesErroresCargaSociales;
        //} //
		
		return $mensajesErroresAsientos;
    }

    /**
     * 
     * @param OrdenPagoSueldo $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     * @return type
     */
    public function generarAsientoPagoSueldos(OrdenPagoSueldo $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $renglonesAsiento = array();
        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        $liquidacion = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find($ordenPago->getIdLiquidacion());

        //liquidacion
        setlocale(LC_ALL, "es_AR.UTF-8");
        $nombre_liquidacion = 'Liquidaci&oacute;n ' . ucfirst(strftime("%B %Y", $liquidacion->getFechaCierreNovedades()->getTimestamp()));

        $configuracion_cuenta_sueldos_a_pagar = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')
                ->findOneByCodigo($liquidacion->getTipoLiquidacion()->getId() == TipoLiquidacion::__SAC ? ConfiguracionCuentaContableSueldos::__CODIGO_PASIVO_SUELDOS_SAC : ConfiguracionCuentaContableSueldos::__CODIGO_PASIVO_SUELDOS);

        $renglonesAsiento[] = array(
            'cuenta' => $configuracion_cuenta_sueldos_a_pagar->getCuentaContable(),
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
            'monto' => $ordenPago->getTotalBruto(),
            'detalle' => 'Pago de ' . $nombre_liquidacion
        );

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::PAGO_SUELDOS);

        $datosAsiento = array(
            'denominacion' => (!$esContraAsiento ? '' :
                    'Anulaci&oacute;n de pago de ') .
            $nombre_liquidacion,
            'concepto' => $concepto_asiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => null,
            'numeroDocumento' => null,
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } //.
        else {

           /* // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromOrdenPagoSueldos($ordenPago, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
            * 
            */
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param OrdenPagoAnticipoSueldo $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     * @return type
     */
    public function generarAsientoPagoAnticipoSueldo(OrdenPagoAnticipoSueldo $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $renglonesAsiento = array();
        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        $configuracion_cuenta_anticipo_sueldo = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')
                ->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_SUELDOS);

        $renglonesAsiento[] = array(
            'cuenta' => $configuracion_cuenta_anticipo_sueldo->getCuentaContable(),
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
            'monto' => $ordenPago->getTotalBruto(),
            'detalle' => 'Anticipo de sueldo'
        );

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::PAGO_ANTICIPO_SUELDO);

        $datosAsiento = array(
            'denominacion' => (!$esContraAsiento ? '' :
                    'Anulaci&oacute;n de pago de ') .
            'Anticipo de sueldo',
            'concepto' => $concepto_asiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => null,
            'numeroDocumento' => null,
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } //.
        else {
            /*
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromOrdenPagoAnticipoSueldo($ordenPago, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
             * 
             */
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     * @return type
     */
    public function generarAsientoPagoAnticipoProveedor($ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $renglonesAsiento = array();
        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        $configuracion_cuenta_anticipo_proveedores = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')
                ->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_PROVEEDORES);

        /* @var $proveedor Proveedor */

        $renglonesAsiento[] = array(
            'cuenta' => $configuracion_cuenta_anticipo_proveedores->getCuentaContable(),
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
            'monto' => $ordenPago->getTotalBruto(),
            'detalle' => 'Anticipo de proveedor'
        );

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::PAGO_ANTICIPO_PROVEEDOR);

        $proveedor = $ordenPago->getProveedor();

        $datosAsiento = array(
            'denominacion' => (!$esContraAsiento ? '' :
                    'Anulaci&oacute;n de pago de ') .
            'Anticipo de proveedor',
            'concepto' => $concepto_asiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $proveedor->getRazonSocial(),
            'numeroDocumento' => $proveedor->getCUIT(),
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } //.
        else {
            /*
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromOrdenPagoAnticipoProveedor($ordenPago, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
             * 
             */
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param OrdenPagoCargasSociales $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     * @return type
     */
    public function generarAsientoPagoCargasSociales(OrdenPagoCargasSociales $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $renglonesAsiento = array();
        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        $liquidacion = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find($ordenPago->getIdLiquidacion());

        // Liquidacion
        setlocale(LC_ALL, "es_AR.UTF-8");
        $nombre_liquidacion = 'Liquidaci&oacute;n ' . ucfirst(strftime("%B %Y", $liquidacion->getFechaCierreNovedades()->getTimestamp()));

        $montoART = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->getMontoARTByLiquidacion($ordenPago->getIdLiquidacion());

        // Contribuciones
        $configuracion_cuenta_sueldos_a_pagar = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')
                ->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_PASIVO_CARGAS);

        $renglonesAsiento[] = array(
            'cuenta' => $configuracion_cuenta_sueldos_a_pagar->getCuentaContable(),
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
            'monto' => $ordenPago->getTotalBruto() - $montoART,
            'detalle' => 'Pago de ' . $nombre_liquidacion
        );

        // ART
        if ($montoART > 0) {
            $configuracion_cuenta_sueldos_a_pagar = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')
                    ->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_PASIVO_ART);

            $renglonesAsiento[] = array(
                'cuenta' => $configuracion_cuenta_sueldos_a_pagar->getCuentaContable(),
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $montoART,
                'detalle' => 'Pago de ' . $nombre_liquidacion
            );
        }

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::PAGO_SUELDOS);

        $datosAsiento = array(
            'denominacion' => (!$esContraAsiento ? '' :
                    'Anulaci&oacute;n de pago de ') .
            $nombre_liquidacion,
            'concepto' => $concepto_asiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => null,
            'numeroDocumento' => null,
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromOrdenPagoCargasSociales($ordenPago, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param OrdenPagoConsultoria $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     * @return type
     */
    public function generarAsientoPagoConsultor(OrdenPagoConsultoria $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        // Renglones relacionados al Consultor
        $cuentaContableConsultor = $ordenPago->getContrato()->getConsultor()->getCuentaContable();
        $codigoCuentaContable = $cuentaContableConsultor->getCodigoCuentaContable();

        $asientoArray[$codigoCuentaContable] = array(
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
            'monto' => $ordenPago->getTotalBruto(),
            'detalle' => $ordenPago->getPagoOrdenPago()->getDetalle()
        );

        // Renglones relacionados al anticipo
        if ($ordenPago->getMontoAnticipos() > 0) {
            $configuracion_cuenta_anticipo_consultor = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_PROVEEDORES);
            $cuentaContableAnticipoConsultor = $configuracion_cuenta_anticipo_consultor->getCuentaContable();

            $codigoCuentaContableAnticipoConsultor = $cuentaContableAnticipoConsultor->getCodigoCuentaContable();

            $asientoArray[$codigoCuentaContableAnticipoConsultor] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                'monto' => $ordenPago->getMontoAnticipos(),
                'detalle' => $ordenPago->getPagoOrdenPago()->getDetalle()
            );
        }


        // Renglones relacionados a los ComprobanteRetencion
        foreach ($ordenPago->getRetenciones() as $comprobanteRetencion) {

            /* @var $comprobanteRetencion ComprobanteRetencionImpuestoCompras */
            $cuentaContableComprobanteRetencion = $comprobanteRetencion->getRegimenRetencion()
                    ->getCuentaContable();

            $codigoCuentaComprobanteRetencion = $cuentaContableComprobanteRetencion
                    ->getCodigoCuentaContable();

            // Cuenta asociada a la percepcion
            $asientoArray[$codigoCuentaComprobanteRetencion] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                'monto' => $comprobanteRetencion->getMonto(),
                'detalle' => $ordenPago->getPagoOrdenPago()->getDetalle()
            );
        }

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        $renglonesAsiento = array();

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::PAGO_CONTRATO_LOCACION_SERVICIO);

        $consultor = $ordenPago->getContrato()->getConsultor();

        $denominacion = !$esContraAsiento //
                ? ('Orden de pago n&ordm; ' . $ordenPago->getNumeroOrdenPago() . ' - ' . $consultor->getCuitAndRazonSocial()) //
                : ('Anulaci&oacute;n de la orden de pago n&ordm; ' . $ordenPago->getNumeroOrdenPago());

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $consultor->getRazonSocial(),
            'numeroDocumento' => $consultor->getNroDocumento(),
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')->crearEjecutadoFromOrdenPagoConsultoria($ordenPago, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param OrdenPagoPagoACuenta $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     */
    public function generarAsientoPagoPagoACuenta(OrdenPagoPagoACuenta $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $renglonesAsiento = array();
        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        // Obtengo la CuentaContable con codigo interno CREDITOS_IMPOSITIVOS
        $cuentaContableCreditosImpositivos = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::CREDITOS_IMPOSITIVOS);

        if ($cuentaContableCreditosImpositivos != null) {
            $renglonesAsiento[] = array(
                'cuenta' => $cuentaContableCreditosImpositivos,
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $ordenPago->getTotalBruto(),
                'detalle' => $ordenPago->getConcepto()
            );
        } else {
            $erroresArray[ConstanteCodigoInternoCuentaContable::CREDITOS_IMPOSITIVOS] = 'No se encontr&oacute; una cuenta contable con codigo interno: '
                    . ConstanteCodigoInternoCuentaContable::CREDITOS_IMPOSITIVOS;
        }

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        $datosAsiento = array(
            'denominacion' => (!$esContraAsiento ? '' :
                    'Anulaci&oacute;n de pago de ') . $ordenPago->getConcepto(),
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => null,
            'numeroDocumento' => null,
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')->crearEjecutadoFromOrdenPagoPagoACuenta($ordenPago, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;
            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param OrdenPagoPagoACuenta $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     */
    public function generarAsientoPagoDevolucionRenglonDeclaracionJurada(OrdenPagoDevolucionRenglonDeclaracionJurada $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $renglonesAsiento = array();
        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        switch ($ordenPago->getDevolucionRenglonDeclaracionJurada()->getRenglonDeclaracionJurada()->getTipoImpuesto()->getDenominacion()) {
            case ConstanteTipoImpuesto::IVA:
                $cuentaContableRetencion = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::RETENCIONES_IVA_TERCEROS);
                break;
            case ConstanteTipoImpuesto::Ganancias:
                $cuentaContableRetencion = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::RETENCIONES_GANANCIAS_TERCEROS);
                break;
            case ConstanteTipoImpuesto::IIBB:
                $cuentaContableRetencion = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::RETENCIONES_IIBB_A_DEPOSITAR);
                break;
            case ConstanteTipoImpuesto::SUSS:
                $cuentaContableRetencion = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::RETENCIONES_SIJP_TERCEROS);
                break;
            default:
                break;
        }

        if ($cuentaContableRetencion != null) {
            $renglonesAsiento[] = array(
                'cuenta' => $cuentaContableRetencion,
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $ordenPago->getTotalBruto(),
                'detalle' => $ordenPago->getConcepto()
            );
        } else {
            $erroresArray[$ordenPago->getDevolucionRenglonDeclaracionJurada()->getRenglonDeclaracionJurada()->getTipoImpuesto()->getId()] = 'No se encontr&oacute; una cuenta contable para el tipo de impuesto: ' . $ordenPago->getDevolucionRenglonDeclaracionJurada()->getRenglonDeclaracionJurada()->getTipoImpuesto()->getDenominacion();
        }

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        $datosAsiento = array(
            'denominacion' => (!$esContraAsiento ? '' :
                    'Anulaci&oacute;n de pago de ') . $ordenPago->getConcepto(),
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => null,
            'numeroDocumento' => null,
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')->crearEjecutadoFromOrdenPagoDevolucionRenglonDeclaracionJurada($ordenPago, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param OrdenPagoPagoParcial $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     * @return type
     */
    public function generarAsientoPagoPagoParcial(OrdenPagoPagoParcial $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $asientoArray = array();
        $renglonesAsiento = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();


        // Renglones relacionados al Proveedor
        $cuentaContableProveedor = $ordenPago->getProveedor()->getCuentaContable();
        $codigoCuentaContableProveedor = $cuentaContableProveedor->getCodigoCuentaContable();

        $asientoArray[$codigoCuentaContableProveedor] = array(
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
            'monto' => $ordenPago->getTotalBruto(),
            'detalle' => $ordenPago->getPagoOrdenPago()->getDetalle()
        );
		
		// Renglones relacionados a los ComprobanteRetencion
        foreach ($ordenPago->getRetenciones() as $comprobanteRetencion) {

            /* @var $comprobanteRetencion ComprobanteRetencionImpuestoCompras */
            $cuentaContableComprobanteRetencion = $comprobanteRetencion->getRegimenRetencion()
                    ->getCuentaContable();

            $codigoCuentaComprobanteRetencion = $cuentaContableComprobanteRetencion
                    ->getCodigoCuentaContable();

            // Cuenta asociada a la percepcion
            if (!isset($asientoArray[$codigoCuentaComprobanteRetencion])) {
                $asientoArray[$codigoCuentaComprobanteRetencion] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                    'monto' => 0,
                    'detalle' => $ordenPago->getPagoOrdenPago()->getDetalle()
                );
            }
            $asientoArray[$codigoCuentaComprobanteRetencion]['monto'] += $comprobanteRetencion->getMonto();
        }

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::PAGO_PROVEEDORES);

        $proveedor = $ordenPago->getProveedor();

        $datosAsiento = array(
            'denominacion' => (!$esContraAsiento ? '' :
                    'Anulaci&oacute;n de pago de ') . $ordenPago->getConcepto(),
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $proveedor->getRazonSocial(),
            'numeroDocumento' => $proveedor->getNroDocumento(),
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')->crearEjecutadoFromOrdenPagoPagoParcial($ordenPago, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param OrdenPagoDeclaracionJurada $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     */
    public function generarAsientoPagoDeclaracionJurada(OrdenPagoDeclaracionJurada $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $renglonesAsiento = array();
        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        $declaracionJurada = $ordenPago->getDeclaracionJurada();

        switch ($declaracionJurada->getTipoDeclaracionJurada()) {
            case ConstanteTipoDeclaracionJurada::SICORE:

                $importeGanancias = $declaracionJurada->getImporteTotalRenglonesDeclaracionJuradaPagosACuentaByTipoImpuesto(ConstanteTipoImpuesto::Ganancias);

                if ($importeGanancias > 0) {

                    // Obtengo la CuentaContable con codigo interno RETENCIONES_GANANCIAS_TERCEROS
                    $cuentaContableRetencionesGananciasTerceros = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                            ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::RETENCIONES_GANANCIAS_TERCEROS);

                    if ($cuentaContableRetencionesGananciasTerceros) {
                        $renglonesAsiento[] = array(
                            'cuenta' => $cuentaContableRetencionesGananciasTerceros,
                            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                            'monto' => $importeGanancias,
                            'detalle' => $ordenPago->getConcepto()
                        );
                    } else {
                        $erroresArray[ConstanteCodigoInternoCuentaContable::RETENCIONES_GANANCIAS_TERCEROS] = 'No se encontr&oacute; una cuenta contable con c&oacute;digo interno: '
                                . ConstanteCodigoInternoCuentaContable::RETENCIONES_GANANCIAS_TERCEROS;
                    }
                }

                $importeIVA = $declaracionJurada->getImporteTotalRenglonesDeclaracionJuradaPagosACuentaByTipoImpuesto(ConstanteTipoImpuesto::IVA);

                if ($importeIVA > 0) {

                    // Obtengo la CuentaContable con codigo interno RETENCIONES_IVA_TERCEROS
                    $cuentaContableRetencionesIVATerceros = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                            ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::RETENCIONES_IVA_TERCEROS);

                    if ($cuentaContableRetencionesIVATerceros) {
                        $renglonesAsiento[] = array(
                            'cuenta' => $cuentaContableRetencionesIVATerceros,
                            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                            'monto' => $importeIVA,
                            'detalle' => $ordenPago->getConcepto()
                        );
                    } else {
                        $erroresArray[ConstanteCodigoInternoCuentaContable::RETENCIONES_IVA_TERCEROS] = 'No se encontr&oacute; una cuenta contable con c&oacute;digo interno: '
                                . ConstanteCodigoInternoCuentaContable::RETENCIONES_IVA_TERCEROS;
                    }
                }

                break;
            case ConstanteTipoDeclaracionJurada::SIJP:

//                $importeSUSS = $declaracionJurada->getImporteTotalRenglonesDeclaracionJurada();
                $importeSUSS = $declaracionJurada->getImporteTotalRenglonesDeclaracionJuradaPagosACuentaByTipoImpuesto(ConstanteTipoImpuesto::SUSS);

                if ($importeSUSS > 0) {

                    // Obtengo la CuentaContable con codigo interno RETENCIONES_SIJP_TERCEROS
                    $cuentaContableRetencionesSIJPTerceros = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                            ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::RETENCIONES_SIJP_TERCEROS);

                    if ($cuentaContableRetencionesSIJPTerceros) {
                        $renglonesAsiento[] = array(
                            'cuenta' => $cuentaContableRetencionesSIJPTerceros,
                            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                            'monto' => $importeSUSS,
                            'detalle' => $ordenPago->getConcepto()
                        );
                    } else {
                        $erroresArray[ConstanteCodigoInternoCuentaContable::RETENCIONES_SIJP_TERCEROS] = 'No se encontr&oacute; una cuenta contable con c&oacute;digo interno: '
                                . ConstanteCodigoInternoCuentaContable::RETENCIONES_SIJP_TERCEROS;
                    }
                }

                break;
            case ConstanteTipoDeclaracionJurada::IIBB:

                $importeRetencionesIIBB = $declaracionJurada->getImporteTotalRenglonesDeclaracionJuradaByTipoRenglon(ConstanteTipoRenglonDeclaracionJurada::COMPROBANTE_RETENCION_IMPUESTO_COMPRA);

                if ($importeRetencionesIIBB > 0) {

                    // Obtengo la CuentaContable con codigo interno RETENCIONES_IIBB_A_DEPOSITAR
                    $cuentaContableRetencionesIIBB = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                            ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::RETENCIONES_IIBB_A_DEPOSITAR);

                    if ($cuentaContableRetencionesIIBB) {
                        $renglonesAsiento[] = array(
                            'cuenta' => $cuentaContableRetencionesIIBB,
                            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                            'monto' => $importeRetencionesIIBB,
                            'detalle' => $ordenPago->getConcepto()
                        );
                    } else {
                        $erroresArray[ConstanteCodigoInternoCuentaContable::RETENCIONES_IIBB_A_DEPOSITAR] = 'No se encontr&oacute; una cuenta contable con c&oacute;digo interno: '
                                . ConstanteCodigoInternoCuentaContable::RETENCIONES_IIBB_A_DEPOSITAR;
                    }
                }

                $importePercepcionesIIBB = $declaracionJurada->getImporteTotalRenglonesDeclaracionJuradaByTipoRenglon(ConstanteTipoRenglonDeclaracionJurada::RENGLON_PERCEPCION);

                if ($importePercepcionesIIBB > 0) {

                    // Obtengo la CuentaContable con codigo interno PERCEPCIONES_IIBB_A_DEPOSITAR
                    $cuentaContablePercepcionesIIBB = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                            ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::PERCEPCIONES_IIBB_A_DEPOSITAR);

                    if ($cuentaContablePercepcionesIIBB) {
                        $renglonesAsiento[] = array(
                            'cuenta' => $cuentaContablePercepcionesIIBB,
                            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                            'monto' => $importePercepcionesIIBB,
                            'detalle' => $ordenPago->getConcepto()
                        );
                    } else {
                        $erroresArray[ConstanteCodigoInternoCuentaContable::PERCEPCIONES_IIBB_A_DEPOSITAR] = 'No se encontr&oacute; una cuenta contable con c&oacute;digo interno: '
                                . ConstanteCodigoInternoCuentaContable::PERCEPCIONES_IIBB_A_DEPOSITAR;
                    }
                }

                break;
            default:
                break;
        }

        // Si existe al menos un PagoACuenta
        if ($declaracionJurada->getImporteTotalPagosACuenta() > 0) {

            // Obtengo la CuentaContable con codigo interno CREDITOS_IMPOSITIVOS
            $cuentaContableCreditosImpositivos = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::CREDITOS_IMPOSITIVOS);

            if ($cuentaContableCreditosImpositivos) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContableCreditosImpositivos,
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                    'monto' => $declaracionJurada->getImporteTotalPagosACuenta(),
                    'detalle' => $ordenPago->getConcepto()
                );
            } else {
                $erroresArray[ConstanteCodigoInternoCuentaContable::CREDITOS_IMPOSITIVOS] = 'No se encontr&oacute; una cuenta contable con c&oacute;digo interno: '
                        . ConstanteCodigoInternoCuentaContable::CREDITOS_IMPOSITIVOS;
            }
        }

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        $datosAsiento = array(
            'denominacion' => (!$esContraAsiento ? '' :
                    'Anulaci&oacute;n de pago de ') . $ordenPago->getConcepto(),
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => null,
            'numeroDocumento' => null,
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromOrdenPagoDeclaracionJurada($ordenPago, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param OrdenPago $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     * @return type
     */
    public function generarAsientoPagoOrdenPagoGeneral(OrdenPago $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        $montoRenglonAsientoContable = $ordenPago->getTotalBruto() - $ordenPago->getMontoRetenciones() - $ordenPago->getMontoAnticipos();

        // Renglones relacionados al Concepto OP
        $detalleRenglon = $ordenPago->getBeneficiario()->getCuitAndRazonSocial()
                . ' - OP n&deg; ' . $ordenPago->getNumeroOrdenPago();

        $cuentaContableConcepto = $ordenPago
                        ->getConceptoOrdenPago()->getCuentaContable();

        /* @var $cuentaContable CuentaContable */
        $codigoCuentaContable = $cuentaContableConcepto->getCodigoCuentaContable();

        $asientoArray[$codigoCuentaContable] = array(
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
            'monto' => $montoRenglonAsientoContable,
            'detalle' => $detalleRenglon
        );


        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        $renglonesAsiento = array();

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        $observaciones = $ordenPago->getObservaciones() != null //
                ? ' - ' . $ordenPago->getObservaciones() //
                : '';

        $proveedor = $ordenPago->getProveedor();

        $denominacionAsiento = !$esContraAsiento //
                ? ('Orden de pago general n&ordm; ' . $ordenPago->getNumeroOrdenPago()
                . ' - Pago de ' . $ordenPago->getConceptoOrdenPago()
                . $observaciones . ' - ' . $proveedor->cuitAndRazonSocial()) //
                : ('Anulaci&oacute;n de la orden de pago general n&ordm;'
                . $ordenPago->getNumeroOrdenPago());

        $datosAsiento = array(
            'denominacion' => $denominacionAsiento,
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $proveedor->getRazonSocial(),
            'numeroDocumento' => $proveedor->getNroDocumento(),
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromOrdenPagoGeneral($ordenPago, $esContraAsiento, $asiento);


            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @return type
     */
    public function getSiguienteNumeroAsiento() {

        $repository = $this->doctrine
                ->getRepository('ADIFContableBundle:AsientoContable', EntityManagers::getEmContable());

        $query = $repository->createQueryBuilder('a')
                ->select('a.numeroAsiento')
                ->orderBy('a.numeroAsiento', 'DESC')
                ->setMaxResults(1)
                ->getQuery();

        try {
            $siguienteNumero = $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            $siguienteNumero = 0;
        }

        return $siguienteNumero + 1;
    }

    /**
     * 
     * @param AsientoContable $asientoContable
     * @return type
     */
    public function getSiguienteNumeroOficialByFechaContable(AsientoContable $asientoContable) {

        $repository = $this->doctrine
                ->getRepository('ADIFContableBundle:AsientoContable', EntityManagers::getEmContable());

        $qb = $repository->createQueryBuilder('a');

        $query = $qb
                ->select('a.numeroAsiento')
                ->innerJoin('a.estadoAsientoContable', 'e')
                ->where('a.fechaContable <= :fechaContable')
                ->andWhere('e.denominacionEstado = :denominacionEstado')
                ->setParameter('denominacionEstado', ConstanteEstadoAsientoContable::ESTADO_ASIENTO_GENERADO)
                ->setParameter('fechaContable', $asientoContable->getFechaContable())
                ->orderBy('a.numeroAsiento', 'DESC')
                ->setMaxResults(1);

        if ($asientoContable->getId() != null) {

            $query
                    ->andWhere('a.id != :id')
                    ->setParameter('id', $asientoContable->getId());
        }

        try {
            $numeroOficialInicial = $query->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            $numeroOficialInicial = 0;
        }

        return $numeroOficialInicial + 1;
    }

    /**
     * 
     * @param type $datos_asiento
     * @param type $offsetNumeroAsiento
     * @return AsientoContable
     */
    public function generarAsientoContable($datos_asiento, $offsetNumeroAsiento = 0) {

        $asiento = new AsientoContable($this->_debugAsientosMalBalanceados);

        if ($this->container->get('security.context')->isGranted('ROLE_DETACH_ASIENTO') === true) {
            // Si tiene el rol de detach, devuelvo el asiento vacío
            $asiento->setNumeroAsiento(-1);
        } else {
            $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

            $fecha_contable = $this->getFechaContable(
                    isset($datos_asiento['fecha_contable']) //
                            ? $datos_asiento['fecha_contable'] //
                            : null
            );

            if ($this->getFechaContableValida($fecha_contable)) {

                $asiento
                        ->setConceptoAsientoContable($datos_asiento['concepto'])
                        ->setDenominacionAsientoContable($datos_asiento['denominacion'])
                        ->setEstadoAsientoContable($emContable->getRepository('ADIFContableBundle:EstadoAsientoContable')->findOneByDenominacionEstado(ConstanteEstadoAsientoContable::ESTADO_ASIENTO_GENERADO))
                        ->setFechaContable($fecha_contable)
                        ->setTipoAsientoContable($emContable->getRepository('ADIFContableBundle:TipoAsientoContable')->findOneByDenominacion(ConstanteTipoAsientoContable::TIPO_ASIENTO_AUTOMATICO))
                        ->setUsuario($datos_asiento['usuario']);

                if (isset($datos_asiento['comprobante'])) {
                    /* @var $comprobante \ADIF\ContableBundle\Entity\Comprobante */
                    $comprobante = $datos_asiento['comprobante'];
                    $comprobante->setFechaContable($fecha_contable);
                    if ($comprobante->getAsientoContable() == null) {
                        $comprobante->setAsientoContable($asiento);
                    } else {
                        $comprobante->setAsientoContableAnulacion($asiento);
                    }
                }

                if (isset($datos_asiento['ordenPago'])) {
                    /* @var $ordenPago  OrdenPago */
                    $ordenPago = $datos_asiento['ordenPago'];
                    $ordenPago->setFechaContable($fecha_contable);
                    if ($ordenPago->getAsientoContable() == null) {
                        $ordenPago->setAsientoContable($asiento);
                    } else {
                        $ordenPago->setAsientoContableAnulacion($asiento);
                    }
                }

                $asiento->setRazonSocial($datos_asiento['razonSocial']);
                $asiento->setNumeroDocumento($datos_asiento['numeroDocumento']);
                
               // \Doctrine\Common\Util\Debug::dump( $datos_asiento['renglones'] ); exit;

                foreach ($datos_asiento['renglones'] as $renglon) {

					if (!$renglon['cuenta']) {
						
						$asiento->setNumeroAsiento(-1);
						
					} else if (!$renglon['cuenta']->getActiva()) {
						
                        $asiento->setNumeroAsiento(-1);
                        $this->cuentasContablesInactivas[] = $renglon['cuenta'];
					
                    } elseif (!$renglon['cuenta']->getEsImputable()) {
						
						$asiento->setNumeroAsiento(-1);
						$this->cuentasContablesNoImputables[] = $renglon['cuenta'];
						
					} else {
						
                        if ($renglon['monto'] != 0) {
                            $renglon_asiento = new RenglonAsientoContable();

                            $tipoMonedaMCL = $emContable->getRepository('ADIFContableBundle:TipoMoneda')
                                    ->findOneByEsMCL(true);

                            $renglon_asiento
                                    ->setCuentaContable($renglon['cuenta'])
                                    ->setTipoOperacionContable($emContable->getRepository('ADIFContableBundle:TipoOperacionContable')->findOneByDenominacion($renglon['imputacion']))
                                    ->setDetalle(isset($renglon['detalle']) ? $renglon['detalle'] : null)
                                    ->setTipoMoneda($tipoMonedaMCL)
                                    ->setImporteMO($renglon['monto'])
                                    ->setImporteMCL($renglon['monto']);

                            $asiento->addRenglonesAsientoContable($renglon_asiento);
                        }
                    }
                }

                // Si el asiento NO balancea correctamente
                if (!$asiento->getAsientoBalanceado()) {
                    $asiento->setNumeroAsiento(-1);
                } else {
                    $numeroAsiento = $this->getSiguienteNumeroAsiento() + $offsetNumeroAsiento;

                    $asiento->setNumeroOriginal($numeroAsiento)
                            ->setNumeroAsiento($numeroAsiento);

                    $emContable->persist($asiento);
                }
            } else {
                $asiento->setNumeroAsiento(-1);
                $asiento->setFechaContable($fecha_contable);
            }
        }

        return $asiento;
    }

    /**
     * 
     * @param type $asiento
     * @param type $erroresArray
     * @return string
     */
    private function getMensajeError($asiento = null, $erroresArray = array()) {

        $errorMsg = '';

        if (!empty($erroresArray) || ($asiento != null && (!$asiento->getAsientoBalanceado() || !$this->getFechaContableValida($asiento->getFechaContable())))) {

            $errorMsg .= '<span>El asiento contable no se pudo generar correctamente:</span>';

            $errorMsg .= '<div class="error-asiento-contable" style="padding-left: 3em; margin-top: .5em">';

            $errorMsg .= '<ul>';

            if ($asiento != null && !empty($this->cuentasContablesInactivas)) {
                foreach ($this->cuentasContablesInactivas as $cuentaInactiva) {
                    $errorMsg .= '<li>La cuenta contable: ' . $cuentaInactiva . ' se encuentra inactiva</li>';
                }
			} elseif($asiento != null && !empty($this->cuentasContablesNoImputables)) {
				foreach ($this->cuentasContablesNoImputables as $cuentaNoImputable) {
                    $errorMsg .= '<li>La cuenta contable: ' . $cuentaNoImputable . ' es una cuenta no imputable o t&iacute;tulo/rubro</li>';
                }
            } else {
                if ($asiento != null && !$asiento->getAsientoBalanceado()) {
                    $mensajeErrorBalanceo = "El asiento no balancea correctamente.";
                    $errorMsg .= '<li>' . $mensajeErrorBalanceo . ' </li>';
                }

                if ($asiento != null && !$this->getFechaContableValida($asiento->getFechaContable())) {
                    $mensajeErrorFechaContable = "La fecha indicada pertenece a un período contable cerrado.";
                    $errorMsg .= '<li>' . $mensajeErrorFechaContable . ' </li>';
                }
            }

            foreach ($erroresArray as $error) {
                $errorMsg .= '<li>' . $error . ' </li>';
            }

            $errorMsg .= '</ul>';

            $errorMsg .= '</div>';
        }

        return $errorMsg;
    }

    /**
     * 
     * @param type $fecha
     * @param CobroRenglonCobranza $cobro
     * @param type $usuario
     * @param type $esImputacionCompleta
     * @param type $esContraAsiento
     * @param type $offsetNumeroAsiento
     * @return type
     */
    public function generarAsientosContablesParaCobranzaImputada($fecha, CobroRenglonCobranza $cobro, $usuario, $esImputacionCompleta, $esContraAsiento = false, $offsetNumeroAsiento = 0, $esOnabe = false) {

//        return $resultArray = [
//            'numeroAsiento' => 0,
//            'mensajeErrorPresupuestario' => null,
//            'mensajeErrorContable' => null
//        ];
		
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $asientoArray = array();
        $erroresArray = array();

        $asiento = null;

        $numeroAsiento = -1;

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $detalleRenglon = !$esContraAsiento ? 'Asiento cobranzas' : 'Desimputaci&oacute;n de cobranzas';

        $comprobantes = $cobro->getComprobantes();

        if (!empty($comprobantes)) {

            /* @var $comprobante ComprobanteVenta */
            $comprobante = $comprobantes[0]; // Debería ser F, ND y C (xq las NC no generan asientos contables, sólo modifican la CC del cliente actualizando su deduda)

            $cancelado = $cobro->getMonto();
            $anticipo = ($cobro->getAnticipoCliente() != null ? $cobro->getAnticipoCliente()->getMonto() : 0);
            $total_asiento = $cancelado + $anticipo;

            //var_dump($cobro->getMontoCheques());var_dump($cobro->getMonto());die();
            $hayBanco = (abs($cobro->getMontoCheques() - $cobro->getMonto()) > 0.00000001);

            //var_dump($hayBanco);die();
            $hayCheque = $cobro->getMontoCheques() != 0;
            if ($esImputacionCompleta) {
                //if ($cobro->getMontoCheques() > 1) {var_dump($cobro->getMontoCheques());var_dump($cobro->getMonto());die();}
                if ($hayBanco) {
                    /* @var $renglonCobro RenglonCobranza */
                    $renglonCobro = $cobro->getRenglonesCobranza()->first();
                    $cuentaBancaria = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($renglonCobro->getIdCuentaBancaria());
                    $cuentaContableCuentaBancaria = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cuentaBancaria->getIdCuentaContable());

                    if (!$cuentaContableCuentaBancaria) {
                        $erroresArray[$cuentaBancaria->getId()] = 'La cuenta bancaria ' . $cuentaBancaria . ' no posee una cuenta contable asociada.';
                    }
                    $codigo_cuenta = $cuentaContableCuentaBancaria->getCodigoCuentaContable();
                    $monto_para_el_asiento = $total_asiento - $cobro->getMontoCheques();
                }
                if ($hayCheque) {

                    $cuentaValoresDepositar = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::VALORES_A_DEPOSITAR);
                    $codigo_cuenta = $cuentaValoresDepositar->getCodigoCuentaContable();
                    $monto_para_el_asiento = $hayBanco ? $cobro->getMontoCheques() : $cobro->getMontoCheques() + $anticipo;
                }
            } else {
                $cuenta_cobranzas = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::COBRANZAS_A_IMPUTAR);
                $codigo_cuenta = $cuenta_cobranzas->getCodigoCuentaContable();
                $monto_para_el_asiento = $total_asiento;
            }

            $asientoArray[$codigo_cuenta] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $monto_para_el_asiento,
                'detalle' => $detalleRenglon
            );

            //$cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->find($comprobante->getContrato()->getIdCliente());
            $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->find($comprobante->getCliente()->getId());
			$cuenta_deudores = null;
			if ($esOnabe) {
				$cuenta_deudores = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::DEUDORES_POR_VENTA_ONABE);
			} else {
                $cuenta_deudores = $this->getCuentaContableComprobante($comprobante);
				#$cuenta_deudores = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cliente->getIdCuentaContable());
			}

            if($comprobante->esComprobanteVentaGeneral()) {
                foreach ($cuenta_deudores as $codigo_cuenta_deudores) {
                    $codigo_cuenta = $codigo_cuenta_deudores->getCodigoCuentaContable();
                    $asientoArray[$codigo_cuenta] = array(
                        'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                        'monto' => $total_asiento, //$cancelado,
                        'detalle' => $detalleRenglon
                    );
                }
            } else {
                $codigo_cuenta_deudores = $cuenta_deudores->getCodigoCuentaContable();
                $asientoArray[$codigo_cuenta_deudores] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                    'monto' => $total_asiento, //$cancelado,
                    'detalle' => $detalleRenglon
                );
             }
            
 
//            if ($anticipo != 0) {
//                // Renglon asiento del básico
//                $configuracion_cuenta_anticipo_clientes = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_CLIENTES);
//                $cuenta_anticipos = $configuracion_cuenta_anticipo_clientes->getCuentaContable();
//                $codigo_cuenta_anticipos = $cuenta_anticipos->getCodigoCuentaContable();
//
//                $asientoArray[$codigo_cuenta_anticipos] = array(
//                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
//                    'monto' => $anticipo,
//                    'detalle' => $detalleRenglon
//                );
//            }

            $renglones_asiento = array();

            foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {

                $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                        ->findOneByCodigoCuentaContable($codigo_cuenta);

                if ($cuenta_contable) {
                    $renglones_asiento[] = array(
                        'cuenta' => $cuenta_contable,
                        'imputacion' => $datos_movimiento['imputacion'],
                        'monto' => $datos_movimiento['monto'],
                        'detalle' => $datos_movimiento['detalle']
                    );
                } else {
                    $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
                }
            }

            $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                    ->findOneByCodigo(ConstanteConceptoAsientoContable::COBRANZAS);

            $datosAsiento = array(
                'denominacion' => 'Cobranza relacionada con el comprobante ' . $comprobante->getTextoParaAsiento(),
                'razonSocial' => $cliente->getClienteProveedor()->getRazonSocial(),
                'numeroDocumento' => $cliente->getClienteProveedor()->getCuit(),
                'concepto' => $concepto_asiento,
                'renglones' => $renglones_asiento,
                'usuario' => $usuario,
                'fecha_contable' => $fecha
            );

            $asiento = $this->generarAsientoContable($datosAsiento, $offsetNumeroAsiento);

            $numeroAsiento = $asiento->getNumeroAsiento();
        } else {
            $erroresArray[$cobro->getId()] = 'El cobro producto de la imputación no tiene comprobante asociado.';
        }

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoParaCobranzaImputada($cobro, $esImputacionCompleta, $esContraAsiento, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param type $fecha
     * @param type $renglones
     * @param type $usuario
     * @param type $esContraAsiento
     * @param type $tipo
     * @param type $offsetNumeroAsiento
     * @return type
     */
    public function generarAsientosContablesParaCobranzaPreImputada($fecha, $renglones, $usuario, $esContraAsiento = false, $tipo, $offsetNumeroAsiento = 0) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $asientoArray = array();
        $erroresArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $total_asiento = 0;

        foreach ($renglones as $renglon) {
            $total_asiento += $renglon->getMonto();
        }

        $detalleRenglon = !$esContraAsiento ? 'Asiento cobranzas' : 'Desimputaci&oacute;n de cobranzas';

        if ($tipo == 'banco') {
            $cuentaBancaria = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($renglon->getIdCuentaBancaria());
            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cuentaBancaria->getIdCuentaContable());

            if (!$cuenta_contable) {
                $erroresArray[$renglon->getIdCuentaBancaria()] = 'La cuenta bancaria ' . $renglon->getIdCuentaBancaria() . ' no tiene cuenta contable asociada.';
            }

            $codigo_cuenta_contable = $cuenta_contable->getCodigoCuentaContable();
        } else { //es cheque
            $cuentaValoresDepositar = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::VALORES_A_DEPOSITAR);
            $codigo_cuenta_contable = $cuentaValoresDepositar->getCodigoCuentaContable();
        }
        $asientoArray[$codigo_cuenta_contable] = array(
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
            'monto' => $total_asiento,
            'detalle' => $detalleRenglon
        );

        $cuenta_cobranzas = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::COBRANZAS_A_IMPUTAR);
        $codigo_cuenta_cobranzas = $cuenta_cobranzas->getCodigoCuentaContable();

        $asientoArray[$codigo_cuenta_cobranzas] = array(
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
            'monto' => $total_asiento,
            'detalle' => $detalleRenglon
        );

        $renglones_asiento = array();

        foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {

            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);

            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => $datos_movimiento['monto'],
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::COBRANZAS);

        $datosAsiento = array(
            'denominacion' => 'Cobranza a imputar',
            'razonSocial' => null,
            'numeroDocumento' => null,
            'concepto' => $concepto_asiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario,
            'fecha_contable' => $fecha
        );

        $asiento = $this->generarAsientoContable($datosAsiento, $offsetNumeroAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoParaCobranzaPreImputada($renglones, $esContraAsiento, $tipo, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param type $usuario
     * @return type
     */
    public function generarAsientosContablesParaCobranzaImputadaConAnticipo($cobro, $usuario, $esContraAsiento = false, $offsetNumeroAsiento = 0) {

//        return $resultArray = [
//            'numeroAsiento' => 0,
//            'mensajeErrorPresupuestario' => null,
//            'mensajeErrorContable' => null
//        ];
        //$cobro es un CobroAnticipoCliente pudiendo ser asiento y contrasiento ó,
        //$cobro es un CorboRenglonCobranza pero solo debería ser contraasiento -> usado en el caso raro de desimputar cobranza (1 renglon a 2 0 + comprobantes)

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $asientoArray = array();
        $erroresArray = array();

        $asiento = null;

        $numeroAsiento = -1;

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $detalleRenglon = !$esContraAsiento ? 'Asiento cobranzas' : 'Desimputaci&oacute; de cobranzas';
        $total_asiento = $cobro->getMonto();

        $comprobantes = $cobro->getComprobantes();

        if (!empty($comprobantes)) {

            $comprobante = $comprobantes[0];

            $configuracion_cuenta_anticipo_clientes = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_CLIENTES);
            $cuenta_anticipos = $configuracion_cuenta_anticipo_clientes->getCuentaContable();
            $codigo_cuenta_anticipos = $cuenta_anticipos->getCodigoCuentaContable();

            $asientoArray[$codigo_cuenta_anticipos] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                'monto' => $total_asiento,
                'detalle' => $detalleRenglon
            );

            //$cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->find($comprobante->getContrato()->getIdCliente());
            $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->find($comprobante->getCliente()->getId());

            $cuenta_deudores = $this->getCuentaContableComprobante($comprobante);

            if($comprobante->esComprobanteVentaGeneral()) {
                foreach ($cuenta_deudores as $codigo_cuenta_deudores) {
                    $codigo_cuenta = $codigo_cuenta_deudores->getCodigoCuentaContable();
                    $asientoArray[$codigo_cuenta] = array(
                        'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                        'monto' => $total_asiento, //$cancelado,
                        'detalle' => $detalleRenglon
                    );
                }
            } else {
                $codigo_cuenta_deudores = $cuenta_deudores->getCodigoCuentaContable();
                $asientoArray[$codigo_cuenta_deudores] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                    'monto' => $total_asiento, //$cancelado,
                    'detalle' => $detalleRenglon
                );
            }

            $renglones_asiento = array();

            foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {

                $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                        ->findOneByCodigoCuentaContable($codigo_cuenta);

                if ($cuenta_contable) {
                    $renglones_asiento[] = array(
                        'cuenta' => $cuenta_contable,
                        'imputacion' => $datos_movimiento['imputacion'],
                        'monto' => $datos_movimiento['monto'],
                        'detalle' => $datos_movimiento['detalle']
                    );
                } else {
                    $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
                }
            }

            $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                    ->findOneByCodigo(ConstanteConceptoAsientoContable::COBRANZAS);

            $datosAsiento = array(
                'denominacion' => 'Cobranza relacionada con el comprobante ' . $comprobante->getTextoParaAsiento(),
                'razonSocial' => null,
                'numeroDocumento' => null,
                'concepto' => $concepto_asiento,
                'renglones' => $renglones_asiento,
                'usuario' => $usuario
            );

            $asiento = $this->generarAsientoContable($datosAsiento, $offsetNumeroAsiento);

            $numeroAsiento = $asiento->getNumeroAsiento();
        } else {
            $erroresArray[$cobro->getId()] = 'El cobro producto de la imputación no tiene comprobante asociado.';
        }

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoParaCobranzaImputadaConAnticipo($cobro, $esContraAsiento, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param CobroRenglonCobranza $cobro
     * @param type $usuario
     * @return type
     */
    public function generarAsientosContablesParaAnticipoCreado($fecha, CobroRenglonCobranza $cobro, $usuario, $esImputacionCompleta, $esContraAsiento = false, $offsetNumeroAsiento = 0) {

//        return $resultArray = [
//            'numeroAsiento' => 0,
//            'mensajeErrorPresupuestario' => null,
//            'mensajeErrorContable' => null
//        ];

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        //$emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $asientoArray = array();
        $erroresArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $anticipo = $cobro->getAnticipoCliente()->getMonto();
        $cliente = $cobro->getAnticipoCliente()->getCliente();
        $detalleRenglon = (!$esContraAsiento ? 'Asiento cobranzas por anticipo de cliente creado' : 'Asiento cobranzas por deshacer de anticipo de clientes');
        if (!$esContraAsiento && $esImputacionCompleta) {
            //$hayBanco = $cobro->getMontoCheques() != $cobro->getAnticipoCliente()->getMonto();//$cobro->getMonto();
            $hayBanco = (abs($cobro->getMontoCheques() - $anticipo) > 0.00000001);
            //var_dump($cobro->getMontoCheques()); var_dump($cobro->getMonto());die();
            $hayCheque = $cobro->getMontoCheques() != 0;
            //var_dump(sizeOf($cobro->getRenglonesCobranza()));die();
            if ($hayBanco) {
                $renglonCobro = $cobro->getRenglonesCobranza()->first();
                //$detalleRenglon = 'Asiento cobranzas';
                //var_dump('holaa');die();
                $cuentaBancaria = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($renglonCobro->getIdCuentaBancaria());
                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cuentaBancaria->getIdCuentaContable());


                if (!$cuentaContable) {
                    $erroresArray[$cuentaBancaria->getId()] = 'La cuenta bancaria ' . $cuentaBancaria . ' no posee una cuenta contable asociada.';
                }
                $codigo_cuenta = $cuentaContable->getCodigoCuentaContable();

                $asientoArray[$codigo_cuenta] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $anticipo - $cobro->getMontoCheques(),
                    'detalle' => $detalleRenglon
                );
            }

            if ($hayCheque) {
                $cuentaValoresDepositar = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::VALORES_A_DEPOSITAR);
                $codigoCuentaValoresDepositar = $cuentaValoresDepositar->getCodigoCuentaContable();
                $asientoArray[$codigoCuentaValoresDepositar] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $cobro->getMontoCheques(),
                    'detalle' => $detalleRenglon
                );
            }

//            else {
//                $cuentaCobranzasAimputar = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::COBRANZAS_A_IMPUTAR);
//                $codigoCuentaCobranzasAimputar = $cuentaCobranzasAimputar->getCodigoCuentaContable();
//                $asientoArray[$codigoCuentaCobranzasAimputar] = array(
//                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
//                    'monto' => $anticipo,
//                    'detalle' => $detalleRenglon
//                );                
//            }   
        } else {

            //$detalleRenglon = 'Desimputaci&oacute;n anticipo de clientes';
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::COBRANZAS_A_IMPUTAR);
            $codigo_cuenta = $cuentaContable->getCodigoCuentaContable();

            $asientoArray[$codigo_cuenta] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $anticipo,
                'detalle' => $detalleRenglon
            );
        }
        // Renglon asiento del básico
//        $configuracion_cuenta_anticipo_clientes = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_CLIENTES);
//        $cuenta_anticipos = $configuracion_cuenta_anticipo_clientes->getCuentaContable();
//        $codigo_cuenta_anticipos = $cuenta_anticipos->getCodigoCuentaContable();
//
//        $asientoArray[$codigo_cuenta_anticipos] = array(
        //lo anterior se comentó al modificar los asientos para que no toquen mas la cuenta de anticipos
        $cuenta_deudores = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cliente->getIdCuentaContable());

        $codigo_cuenta_deudores = $cuenta_deudores->getCodigoCuentaContable();
        $asientoArray[$codigo_cuenta_deudores] = array(
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
            'monto' => $anticipo,
            'detalle' => $detalleRenglon
        );


        $renglones_asiento = array();

        foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {
            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);

            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => $datos_movimiento['monto'],
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::COBRANZAS);

        $datosAsiento = array(
            'denominacion' => 'Anticipo de cliente ' . $cliente->getClienteProveedor()->getRazonSocial() . ' ' . $cliente->getClienteProveedor()->getCuit(),
            'razonSocial' => $cliente->getClienteProveedor()->getRazonSocial(),
            'numeroDocumento' => $cliente->getClienteProveedor()->getCuit(),
            'concepto' => $concepto_asiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario,
            'fecha_contable' => $fecha
        );

        $asiento = $this->generarAsientoContable($datosAsiento, $offsetNumeroAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoParaAnticipoCreado($cobro, $esImputacionCompleta, $esContraAsiento, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param AsientoContable $asientoContable
     * @param type $fechaContable
     * @param type $usuario
     * @return AsientoContable
     */
    public function revertirAsientoContable(AsientoContable $asientoContable, $fechaContable, $usuario) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $asientoContableRevertido = clone $asientoContable;

        foreach ($asientoContable->getRenglonesAsientoContable() as $renglonAsientoContable) {

            /* @var $renglonAsientoContableRevertido RenglonAsientoContable */

            $renglonAsientoContableRevertido = clone $renglonAsientoContable;

            $denominacionTipoOperacionContable = $renglonAsientoContable
                            ->getTipoOperacionContable()->getDenominacion();

            if ($denominacionTipoOperacionContable == ConstanteTipoOperacionContable::DEBE) {

                $tipoOperacionContable = $emContable
                        ->getRepository('ADIFContableBundle:TipoOperacionContable')
                        ->findOneByDenominacion(ConstanteTipoOperacionContable::HABER);
            } else {

                $tipoOperacionContable = $emContable
                        ->getRepository('ADIFContableBundle:TipoOperacionContable')
                        ->findOneByDenominacion(ConstanteTipoOperacionContable::DEBE);
            }

            $renglonAsientoContableRevertido->setTipoOperacionContable($tipoOperacionContable);

            $renglonAsientoContableRevertido->setAsientoContable($asientoContableRevertido);

            $asientoContableRevertido->addRenglonesAsientoContable($renglonAsientoContableRevertido);
        }

        $asientoContableRevertido->setFechaContable($fechaContable);

        $asientoContableRevertido->setUsuario($usuario);

        $numeroAsiento = $this->getSiguienteNumeroAsiento();

        $asientoContableRevertido->setNumeroOriginal($numeroAsiento);
        $asientoContableRevertido->setNumeroAsiento($numeroAsiento);

        $emContable->persist($asientoContableRevertido);

        //$this->actualizarNumeroOficialAsiento($asientoContableRevertido);

        return $asientoContableRevertido;
    }

    /**
     * 
     * @param OrdenPagoRenglonRetencionLiquidacion $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     */
    public function generarAsientoPagoRenglonRetencionLiquidacion(OrdenPagoRenglonRetencionLiquidacion $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $renglonesAsiento = array();
        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        foreach ($ordenPago->getRenglonesRetencionLiquidacion() as $renglonRetencionLiquidacion) {
            /* @var $renglonRetencionLiquidacion RenglonRetencionLiquidacion */

            $conceptoVersion = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConceptoVersion')->find($renglonRetencionLiquidacion->getIdConceptoVersion());
            $liquidacion = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find($renglonRetencionLiquidacion->getIdLiquidacion());
            $concepto = $conceptoVersion->getConcepto();

            // Obtengo la CuentaContable con codigo interno CREDITOS_IMPOSITIVOS
            $cuentaContableConcepto = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->find($concepto->getIdCuentaContable());

            if ($cuentaContableConcepto) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContableConcepto,
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $renglonRetencionLiquidacion->getMonto(),
                    'detalle' => $liquidacion . ' - ' . $concepto
                );
            } else {
                $erroresArray[$concepto->getId()] = 'No se encontr&oacute; una cuenta contable asociada al concepto: ' . $concepto;
            }
        }

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        $datosAsiento = array(
            'denominacion' => (!$esContraAsiento ? '' :
                    'Anulaci&oacute;n de pago de ') . $ordenPago->getConcepto(),
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $ordenPago->getBeneficiario()->getRazonSocial(),
            'numeroDocumento' => $ordenPago->getBeneficiario()->getNroDocumento(),
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromOrdenPagoRenglonRetencionLiquidacion($ordenPago, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param DeclaracionJuradIvaContribuyente $declaracionJurada
     * @param Usuario $usuario
     * @param type $esContraAsiento
     */
    public function generarAsientoFromDeclaracionJuradaIvaContribuyente(DeclaracionJuradaIvaContribuyente $declaracionJurada, Usuario $usuario, $esContraAsiento = false) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $renglonesAsiento = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        //Débito fiscal
        if ($declaracionJurada->getMontoDebitoFiscal() > 0) {
            $alicuotaIva = $emContable->getRepository('ADIFContableBundle:AlicuotaIva')->findOneByValor(ConstanteAlicuotaIva::ALICUOTA_21);

            // Obtengo la CuentaContable con codigo interno CREDITOS_IMPOSITIVOS
            $cuentaContableDebitoFiscal = $alicuotaIva->getCuentaContableDebito();

            if ($cuentaContableDebitoFiscal) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContableDebitoFiscal,
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $declaracionJurada->getMontoDebitoFiscal(),
                    'detalle' => ''
                );
            } else {
                $erroresArray[$declaracionJurada->getId()] = 'No se encontr&oacute; una cuenta contable asociada al IVA d&eacute;bito fiscal';
            }
        }

        //Crédito fiscal 10,5
        if ($declaracionJurada->getMontoIva105() > 0) {
            $alicuotaIva = $emContable->getRepository('ADIFContableBundle:AlicuotaIva')->findOneByValor(ConstanteAlicuotaIva::ALICUOTA_10_5);

            $cuentaContableCreditoFiscal105 = $alicuotaIva->getCuentaContableCredito();

            if ($cuentaContableCreditoFiscal105) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContableCreditoFiscal105,
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                    'monto' => $declaracionJurada->getMontoIva105(),
                    'detalle' => ''
                );
            } else {
                $erroresArray[$declaracionJurada->getId()] = 'No se encontr&oacute; una cuenta contable asociada al IVA cre&eacute;dito fiscal %10,5';
            }
        }

        //Crédito fiscal 21
        if ($declaracionJurada->getMontoIva21() > 0) {
            $alicuotaIva = $emContable->getRepository('ADIFContableBundle:AlicuotaIva')->findOneByValor(ConstanteAlicuotaIva::ALICUOTA_21);

            $cuentaContableCreditoFiscal21 = $alicuotaIva->getCuentaContableCredito();

            if ($cuentaContableCreditoFiscal21) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContableCreditoFiscal21,
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                    'monto' => $declaracionJurada->getMontoIva21(),
                    'detalle' => ''
                );
            } else {
                $erroresArray[$declaracionJurada->getId()] = 'No se encontr&oacute; una cuenta contable asociada al IVA cre&eacute;dito fiscal %21';
            }
        }

        //Crédito fiscal 27
        if ($declaracionJurada->getMontoIva27() > 0) {
            $alicuotaIva = $emContable->getRepository('ADIFContableBundle:AlicuotaIva')->findOneByValor(ConstanteAlicuotaIva::ALICUOTA_27);

            $cuentaContableCreditoFiscal27 = $alicuotaIva->getCuentaContableCredito();

            if ($cuentaContableCreditoFiscal27) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContableCreditoFiscal27,
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                    'monto' => $declaracionJurada->getMontoIva27(),
                    'detalle' => ''
                );
            } else {
                $erroresArray[$declaracionJurada->getId()] = 'No se encontr&oacute; una cuenta contable asociada al IVA cre&eacute;dito fiscal %27';
            }
        }

        //IVA Retenciones
        if ($declaracionJurada->getMontoRetencionesIva() > 0) {
            $configuracion_cuenta_iva_retenciones = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_IVA_RETENCIONES);
            $cuentaContableIvaRetenciones = $configuracion_cuenta_iva_retenciones->getCuentaContable();

            if ($cuentaContableIvaRetenciones) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContableIvaRetenciones,
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                    'monto' => $declaracionJurada->getMontoRetencionesIva(),
                    'detalle' => ''
                );
            } else {
                $erroresArray[$declaracionJurada->getId()] = 'No se encontr&oacute; una cuenta contable asociada a IVA Retenciones';
            }
        }

        //IVA Percepciones
        if ($declaracionJurada->getMontoPercepcionesIva() > 0) {
            $configuracion_cuenta_iva_percepciones = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_IVA_PERCEPCIONES);
            $cuentaContableIvaPercepciones = $configuracion_cuenta_iva_percepciones->getCuentaContable();

            if ($cuentaContableIvaPercepciones) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContableIvaPercepciones,
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                    'monto' => $declaracionJurada->getMontoPercepcionesIva(),
                    'detalle' => ''
                );
            } else {
                $erroresArray[$declaracionJurada->getId()] = 'No se encontr&oacute; una cuenta contable asociada a IVA Percepciones';
            }
        }

        //IVA No computable
        if ($declaracionJurada->getIvaCFNoComputable() > 0) {
            $configuracion_cuenta_iva_no_computable = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_IVA_NO_COMPUTABLE);
            $cuentaContableIvaNoComputable = $configuracion_cuenta_iva_no_computable->getCuentaContable();

            if ($cuentaContableIvaNoComputable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContableIvaNoComputable,
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $declaracionJurada->getIvaCFNoComputable(),
                    'detalle' => ''
                );
            } else {
                $erroresArray[$declaracionJurada->getId()] = 'No se encontr&oacute; una cuenta contable asociada a IVA Percepciones';
            }
        }

        //Saldo
        if ($declaracionJurada->getSaldo() < 0) {
            $configuracion_cuenta_saldo = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_IVA_SALDO_LIBRE_DISPONIBLE);
            $imputacionSaldo = !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER;
        } else {
            $configuracion_cuenta_saldo = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_IVA_SALDO_A_PAGAR);
            $imputacionSaldo = !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE;
        }
        $cuentaContableSaldo = $configuracion_cuenta_saldo->getCuentaContable();

        if ($cuentaContableSaldo) {
            $renglonesAsiento[] = array(
                'cuenta' => $cuentaContableSaldo,
                'imputacion' => $imputacionSaldo,
                'monto' => abs($declaracionJurada->getSaldo()),
                'detalle' => ''
            );
        } else {
            $erroresArray[$declaracionJurada->getId()] = 'No se encontr&oacute; una cuenta contable asociada al saldo';
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        setlocale(LC_ALL, "es_AR.UTF-8");
        $periodo = ucfirst(strftime("%B %Y", $declaracionJurada->getFechaInicio()->getTimestamp()));

        $datosAsiento = array(
            'denominacion' => 'Declaraci&oacute;n jurada IVA contribuyente per&iacute;odo ' . $periodo,
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $this->container->get('request')->getSession()->getFlashBag()->add('error', $mensajeErrorAsientoContable);
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')->crearEjecutadoFromDeclaracionJuradaIvaContribuyente($declaracionJurada, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $this->container->get('request')->getSession()->getFlashBag()
                        ->add('error', $mensajeErrorAsientoPresupuestarioAsiento);
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param OrdenPagoDeclaracionJuradaIvaContribuyente $ordenPago
     * @param Usuario $usuario
     * @param type $esContraAsiento
     */
    public function generarAsientoPagoDeclaracionJuradaIvaContribuyente(OrdenPagoDeclaracionJuradaIvaContribuyente $ordenPago, Usuario $usuario, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $renglonesAsiento = array();
        $asientoArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $erroresArray = array();

        //IVA a pagar
        $configuracion_cuenta_iva_a_pagar = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_IVA_SALDO_A_PAGAR);
        $cuentaContableIvaAPagar = $configuracion_cuenta_iva_a_pagar->getCuentaContable();

        if ($cuentaContableIvaAPagar) {
            $renglonesAsiento[] = array(
                'cuenta' => $cuentaContableIvaAPagar,
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $ordenPago->getImporte(),
                'detalle' => ''
            );
        } else {
            $erroresArray[$declaracionJurada->getId()] = 'No se encontr&oacute; una cuenta contable asociada a IVA Percepciones';
        }

        // Renglones relacionados al Pago
        $this->generarRenglonesPago($ordenPago, $asientoArray, $erroresArray, $esContraAsiento);

        foreach ($asientoArray as $codigoCuenta => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable(explode('_', $codigoCuenta)[0]);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuenta;
            }
        }

        $conceptoAsiento = $emContable
                ->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::TESORERIA);

        $datosAsiento = array(
            'denominacion' => (!$esContraAsiento ? '' :
                    'Anulaci&oacute;n de pago de ') . $ordenPago->getConcepto(),
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'ordenPago' => $ordenPago
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')->crearEjecutadoFromOrdenPagoDeclaracionJuradaIvaContribuyente($ordenPago, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param type $numeroAsiento
     * @param type $dataArray
     * @param type $ajaxCall
     * @return string
     */
    public function showMensajeFlashAsientoContable($numeroAsiento, $dataArray = array(), $ajaxCall = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        /* @var $ejercicioContable EjercicioContable */
        $ejercicioContable = $emContable->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByFecha(new \DateTime());

        /*  @var $asientoContable AsientoContable */
        $asientoContable = $emContable->getRepository('ADIFContableBundle:AsientoContable')
                ->findOneByNumeroAsiento($numeroAsiento);

        $fechaMesCerradoSuperior = $this->getFechaMesCerradoSuperiorByEjercicio($ejercicioContable);

        $fechaAsiento = (new \DateTime())->format('d/m/Y');

        if ($asientoContable) {
            $fechaAsiento = $asientoContable->getFechaContable()->format('d/m/Y');
        }

        $mensajeFlash = "<div class='mensaje-asiento-contable inline' "
                . "data-fecha-mes-cerrado-superior='" . $fechaMesCerradoSuperior . "' ";

        foreach ($dataArray as $key => $value) {

            $mensajeFlash .= $key . "='" . $value . "' ";
        }

        $mensajeFlash .= '>';

        if ($asientoContable && !$asientoContable->getAsientoBalanceado()) {

            $mensajeFlash .= "<span class='color-red'>El asiento n&deg; <span>"
                    . $numeroAsiento
                    . "</span> se ha generado con fecha <span class='fecha-asiento-contable'> "
                    . $fechaAsiento . "</span>, pero no balancea correctamente.</span>";
        } //
        else {

            $mensajeFlash .= "El asiento n&deg; <span>"
                    . $numeroAsiento
                    . "</span> se ha generado correctamente con fecha <span class='fecha-asiento-contable'> "
                    . $fechaAsiento . "</span>.";
        }

        if ($asientoContable) {

            $router = $this->container->get('router');

            $showPath = $router->generate('asientocontable_show', array('id' => $asientoContable->getId()));

            $mensajeFlash .= '<span style="margin-left: .5em">Para ver el detalle haga click '
                    . '<a href = "' . $showPath . '" class="detalle-asiento-link" target="_blank">aqu&iacute;</a>'
                    . '</span>';
        }

        if (true === $this->container->get('security.context')->isGranted('ROLE_EDITAR_FECHA_ASIENTO_CONTABLE')) {

            $mensajeFlash .= '<span id="numero-asiento" data-numero-asiento = "'
                    . $numeroAsiento
                    . '" style="margin-left: 1em"> Para modificar la fecha haga click '
                    . '<a href = "#" class = "link-editar-fecha-asiento">aqu&iacute;</a>'
                    . '</span>';
        }

        $mensajeFlash .= "</div>";

        // Si no es una llamada Ajax
        if (!$ajaxCall) {

            $this->container->get('request')->getSession()->getFlashBag()->add('info', $mensajeFlash);
        }

        return $mensajeFlash;
    }

    /**
     * 
     * @param type $numerosAsientosArray
     * @param type $dataArray
     * @param type $ajaxCall
     */
    public function showMensajeFlashColeccionAsientosContables($numerosAsientosArray, $dataArray = array(), $ajaxCall = false) {

        $numeroAsientoInicial = reset($numerosAsientosArray);

        $numeroAsientoFinal = end($numerosAsientosArray);

        $numerosAsiento = implode(',', $numerosAsientosArray);

        // Si se realizaron múltiples asientos contables
        if ($numeroAsientoInicial != $numeroAsientoFinal) {

            $today = (new \DateTime())->format('d/m/Y');

            $mensajeFlash = "<div class='mensaje-asiento-contable inline' ";

            foreach ($dataArray as $key => $value) {

                $mensajeFlash .= $key . "='" . $value . "' ";
            }

            $mensajeFlash .= '>';

            $mensajeFlash .= "Los asientos del n&deg; "
                    . "<span id='numero-asiento-inicial'>" . $numeroAsientoInicial . "</span> al n&deg; "
                    . "<span id='numero-asiento-final'>" . $numeroAsientoFinal . "</span> "
                    . "se han generado correctamente con fecha "
                    . "<span class='fecha-asiento-contable'> " . $today . "</span>.";

            $router = $this->container->get('router');

            $indexPath = $router->generate('asientocontable');

			if (true === $this->container->get('security.context')->isGranted('ROLE_MENU_CONTABILIDAD_ASIENTO_MANUAL')) { 
			
				$mensajeFlash .= '<span style="margin-left: .5em">Para ver el listado de asientos haga click '
                    . '<a href = "' . $indexPath . '" class="detalle-asiento-link" target="_blank">aqu&iacute;</a>'
                    . '</span>';
			}

            if (true === $this->container->get('security.context')->isGranted('ROLE_EDITAR_FECHA_ASIENTO_CONTABLE')) {

                $mensajeFlash .= '<span id="numero-asiento" data-numero-asiento = "'
                        . $numerosAsiento
                        . '" style="margin-left: 1em"> Para modificar la fecha haga click '
                        . '<a href = "#" class = "link-editar-fecha-asiento">aqu&iacute;</a>'
                        . '</span>';
            }

            $mensajeFlash .= "</div>";

            // Si no es una llamada Ajax
            if (!$ajaxCall) {
                $this->container->get('request')->getSession()->getFlashBag()->add('info', $mensajeFlash);
            }

            return $mensajeFlash;
        }

        // Sino, si se realizó un único asiento contable
        else {

            return $this->showMensajeFlashAsientoContable($numeroAsientoInicial, $dataArray, $ajaxCall);
        }
    }

    /**
     * Actualiza el nº oficial de todos los asientos, según corresponda.
     * 
     * @param AsientoContable $asientoContable
	 * Nota: no va mas esta funcionalidad - gluis - 23/02/2017
     */
    public function actualizarNumeroOficialAsiento() {}
	/*
        $em = $this->doctrine->getManager(EntityManagers::getEmContable());

        $siguienteNumeroOficial = $this
                ->getSiguienteNumeroOficialByFechaContable($asientoContable);

        $asientoContable->setNumeroAsiento($siguienteNumeroOficial);

        $repository = $this->doctrine
                ->getRepository('ADIFContableBundle:AsientoContable', EntityManagers::getEmContable());

        $qb = $repository->createQueryBuilder('a');

        $query = $qb
                ->innerJoin('a.estadoAsientoContable', 'e')
                ->where('a.fechaContable > :fechaContable')
                ->andWhere('e.denominacionEstado = :denominacionEstado')
                ->setParameter('denominacionEstado', ConstanteEstadoAsientoContable::ESTADO_ASIENTO_GENERADO)
                ->setParameter('fechaContable', $asientoContable->getFechaContable())
                ->orderBy('a.fechaContable', 'ASC');

        if ($asientoContable->getId() != null) {

            $query
                    ->andWhere('a.id != :id')
                    ->setParameter('id', $asientoContable->getId());
        }

        $asientosSinActualizar = $query->getQuery()->getResult();

        foreach ($asientosSinActualizar as $asiento) {

            $asiento->setNumeroAsiento( ++$siguienteNumeroOficial);

            $em->persist($asiento);
        }
    }
	*/

    /**
     * 
     * @param EjercicioContable $ejercicioContable
     * @return type
     */
    public function getFechaMesCerradoSuperiorByEjercicio(EjercicioContable $ejercicioContable) {

        $mesCerradoSuperior = $ejercicioContable->getMesCerradoSuperior();

        $ejercicio = $ejercicioContable->getDenominacionEjercicio();

        if ($mesCerradoSuperior > 0) {

            $fecha = new \DateTime(
                    date('Y-m-d', strtotime(
                                    (new \DateTime($ejercicio . '-' . $mesCerradoSuperior . '-01'))
                                            ->format("Y-m-t"))
                    )
            );

            $fecha->add(new DateInterval('P1D'));
        } //.
        else {

            $fecha = new \DateTime(
                    date('Y-m-d', strtotime(
                                    (new \DateTime($ejercicio . '-01-01'))
                                            ->format("Y-m-d"))
                    )
            );
        }

        return $fecha->format('d/m/Y');
    }

    /**
     * 
     * @param CobroRetencionCliente $cobro
     * @param type $usuario
     * @return type
     */
    public function generarAsientosContablesParaRetencion($fecha, CobroRetencionCliente $cobro, $usuario, $esContraAsiento = false, $offsetNumeroAsiento = 0) {
//        return $resultArray = [
//            'numeroAsiento' => 0,
//            'mensajeErrorPresupuestario' => null,
//            'mensajeErrorContable' => null
//        ];
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $asientoArray = array();
        $erroresArray = array();

        $asiento = null;

        $numeroAsiento = -1;

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $detalleRenglon = !$esContraAsiento ? 'Asiento cobranzas' : 'Desimputaci&oacute;n de cobranzas';

        $comprobantes = $cobro->getComprobantes();

        if (!empty($comprobantes)) {

            /* @var $comprobante ComprobanteVenta */
            $comprobante = $comprobantes[0]; // Debería ser F, ND y C (xq las NC no generan asientos contables, sólo modifican la CC del cliente actualizando su deduda)
            $cancelado = $cobro->getMonto();
            $cuenta = $cobro->getRetencionesCliente()[0]->getTipoImpuesto()->getCuentaContable();




            $asientoArray[$cuenta->getCodigoCuentaContable()] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $cancelado,
                'detalle' => $detalleRenglon
            );

            //$cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->find($comprobante->getContrato()->getIdCliente());
            $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->find($comprobante->getCliente()->getId());
            $cuenta_deudores = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cliente->getIdCuentaContable());

            $codigo_cuenta_deudores = $cuenta_deudores->getCodigoCuentaContable();
            $asientoArray[$codigo_cuenta_deudores] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                'monto' => $cancelado,
                'detalle' => $detalleRenglon
            );


            $renglones_asiento = array();

            foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {

                $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                        ->findOneByCodigoCuentaContable($codigo_cuenta);

                if ($cuenta_contable) {
                    $renglones_asiento[] = array(
                        'cuenta' => $cuenta_contable,
                        'imputacion' => $datos_movimiento['imputacion'],
                        'monto' => $datos_movimiento['monto'],
                        'detalle' => $datos_movimiento['detalle']
                    );
                } else {
                    $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
                }
            }

            $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                    ->findOneByCodigo(ConstanteConceptoAsientoContable::COBRANZAS);

            $datosAsiento = array(
                'denominacion' => 'Cobranza relacionada con el comprobante ' . $comprobante->getTextoParaAsiento(),
                'razonSocial' => $cliente->getClienteProveedor()->getRazonSocial(),
                'numeroDocumento' => $cliente->getClienteProveedor()->getCuit(),
                'concepto' => $concepto_asiento,
                'renglones' => $renglones_asiento,
                'usuario' => $usuario,
                'fecha_contable' => $fecha
            );

            $asiento = $this->generarAsientoContable($datosAsiento, $offsetNumeroAsiento);

            $numeroAsiento = $asiento->getNumeroAsiento();
        } else {
            $erroresArray[$cobro->getId()] = 'El cobro producto de la imputación no tiene comprobante asociado.';
        }

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoParaRetencion($cobro, $esContraAsiento, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param type $cheques
     * @param type $usuario
     * @param type $fechaContable
     * @param type $esContraAsiento
     * @param type $offsetNumeroAsiento
     * @return type
     */
    public function generarAsientosContablesParaChequeDepositado($cheques, $usuario, $fechaContable, $esContraAsiento = false, $offsetNumeroAsiento = 0) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $monto_total = 0;
        $numeros = '';

        foreach ($cheques as $cheque) {
            $monto_total += $cheque->getMonto();
            $cuentaBancaria = $cheque->getCuenta();
            $numeros .= $cheque->getNumero() . ' ';
        }

        $asientoArray = array();
        $erroresArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $detalleRenglon = !$esContraAsiento ? 'Dep&oacute;sito de cheque de cliente' : 'Anulac&oacute;n de dep&oacute;sito de cheque de cliente';
        //var_dump($cheque->getIdCuenta());die();

        $cuentaContableCuentaBancaria = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                ->find($cuentaBancaria->getIdCuentaContable());

        if (!$cuentaContableCuentaBancaria) {
            $erroresArray[$cuentaBancaria->getId()] = 'La cuenta bancaria ' . $cuentaBancaria . ' no posee una cuenta contable asociada.';
        }

        $codigo_cuenta = $cuentaContableCuentaBancaria->getCodigoCuentaContable();

        $asientoArray[$codigo_cuenta] = array(
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
            'monto' => $monto_total,
            'detalle' => $detalleRenglon
        );

        $cuentaValoresDepositar = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::VALORES_A_DEPOSITAR);
        $codigo_cuenta = $cuentaValoresDepositar->getCodigoCuentaContable();

        $asientoArray[$codigo_cuenta] = array(
            'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
            'monto' => $monto_total,
            'detalle' => $detalleRenglon
        );


        $renglones_asiento = array();

        foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {

            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);

            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => $datos_movimiento['monto'],
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::COBRANZAS);

        $datosAsiento = array(
            'fecha_contable' => $fechaContable != null ? $fechaContable : new DateTime(),
            'denominacion' => 'Cobranza relacionada con cheques: ' . $numeros,
            'razonSocial' => null,
            'numeroDocumento' => null,
            'concepto' => $concepto_asiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario
        );

        $asiento = $this->generarAsientoContable($datosAsiento, $offsetNumeroAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoParaChequeDepositado($cheques, $esContraAsiento, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param CobroRenglonCobranza $cobro
     * @param type $usuario
     * @return type
     */
    public function generarAsientosContablesParaDesimputacionRenglonCobranza(CobroRenglonCobranza $cobro, $usuario, $offsetNumeroAsiento = 0) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $asientoArray = array();
        $erroresArray = array();

        $asiento = null;

        $numeroAsiento = -1;

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $detalleRenglon = 'Desimputaci&oacute;n de cobranzas';

        $comprobantes = $cobro->getComprobantes();

        if (!empty($comprobantes)) {

            /* @var $comprobante ComprobanteVenta */
            $comprobante = $comprobantes[0]; // Debería ser F, ND y C (xq las NC no generan asientos contables, sólo modifican la CC del cliente actualizando su deduda)
            $cancelado = $cobro->getMonto();

            $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->find($comprobante->getCliente()->getId());

            $cuenta_deudores = $this->getCuentaContableComprobante($comprobante);
            #$cuenta_deudores = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cliente->getIdCuentaContable());

            if($comprobante->esComprobanteVentaGeneral()) {
                foreach ($cuenta_deudores as $codigo_cuenta_deudores) {
                    $codigo_cuenta = $codigo_cuenta_deudores->getCodigoCuentaContable();
                    $asientoArray[$codigo_cuenta] = array(
                        'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                        'monto' => $total_asiento, //$cancelado,
                        'detalle' => $detalleRenglon
                    );
                }
            } else {
                $codigo_cuenta_deudores = $cuenta_deudores->getCodigoCuentaContable();
                $asientoArray[$codigo_cuenta_deudores] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $total_asiento, //$cancelado,
                    'detalle' => $detalleRenglon
                );
            }

            // Renglon asiento del básico
            $configuracion_cuenta_anticipo_clientes = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_CLIENTES);
            $cuenta_anticipos = $configuracion_cuenta_anticipo_clientes->getCuentaContable();
            $codigo_cuenta_anticipos = $cuenta_anticipos->getCodigoCuentaContable();

            $asientoArray[$codigo_cuenta_anticipos] = array(
                'imputacion' => ConstanteTipoOperacionContable::HABER,
                'monto' => $cancelado,
                'detalle' => $detalleRenglon
            );


            $renglones_asiento = array();

            foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {

                $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                        ->findOneByCodigoCuentaContable($codigo_cuenta);

                if ($cuenta_contable) {
                    $renglones_asiento[] = array(
                        'cuenta' => $cuenta_contable,
                        'imputacion' => $datos_movimiento['imputacion'],
                        'monto' => $datos_movimiento['monto'],
                        'detalle' => $datos_movimiento['detalle']
                    );
                } else {
                    $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
                }
            }

            $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                    ->findOneByCodigo(ConstanteConceptoAsientoContable::COBRANZAS);

            $datosAsiento = array(
                'denominacion' => 'Cobranza relacionada con el comprobante ' . $comprobante->getTextoParaAsiento(),
                'concepto' => $concepto_asiento,
                'renglones' => $renglones_asiento,
                'usuario' => $usuario
            );

            $asiento = $this->generarAsientoContable($datosAsiento, $offsetNumeroAsiento);

            $numeroAsiento = $asiento->getNumeroAsiento();
        } else {
            $erroresArray[$cobro->getId()] = 'El cobro producto de la imputación no tiene comprobante asociado.';
        }

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoParaDesimputacionRenglonCobranza($cobro, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param type $usuario
     * @return type
     */
    public function generarAsientosContablesParaImputaciones($fecha, $cobrosRetencion, $cobrosAnticipo, $cobrosRenglonCobranza, $usuario, $esImputacionCompleta, $esContraAsiento = false, $offsetNumeroAsiento = 0, $esOnabe = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $asientoArray = array();
        $erroresArray = array();

        $resultArray = [
            'numeroAsiento' => null,
            'mensajeErrorPresupuestario' => null,
            'mensajeErrorContable' => null
        ];

        $detalleRenglon = !$esContraAsiento ? 'Asiento cobranzas' : 'Desimputaci&oacute;n de cobranzas';
        $total_cuenta_deudores = 0;

        $comprobantes = array();

        if (sizeOf($cobrosRetencion) > 0) {
            $totales_cuentas_retencion = [];
            $codigos_cuentas_retencion = [];
            foreach ($cobrosRetencion as $cobro) {
                $comprobante = $cobro->getComprobantes()[0];
                $comprobantes[$comprobante->getId()] = $comprobante->getTextoParaAsiento();
                $codigo_cliente = $comprobante->getCliente()->getId();
                $cuenta_contable = $cobro->getRetencionesCliente()[0]->getTipoImpuesto()->getCuentaContable();
                $codigo_cuenta_contable = $cuenta_contable->getCodigoCuentaContable();

                (!isset($totales_cuentas_retencion[$cuenta_contable->getId()]) ? $totales_cuentas_retencion[$cuenta_contable->getId()] = $cobro->getMonto() : $totales_cuentas_retencion[$cuenta_contable->getId()] += $cobro->getMonto());
                $codigos_cuentas_retencion[$cuenta_contable->getId()] = $codigo_cuenta_contable;
            }
            foreach ($totales_cuentas_retencion as $id_cuenta_contable => $total_cuenta_retencion) {
                $codigo_cuenta_contable = $codigos_cuentas_retencion[$id_cuenta_contable];
                $total_cuenta_deudores += $total_cuenta_retencion;
                $asientoArray[$codigo_cuenta_contable] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $total_cuenta_retencion,
                    'detalle' => $detalleRenglon
                );
            }
        }

//        if (sizeOf($cobrosAnticipo) > 0) {
//            $total_cuenta_anticipos = 0;
//            foreach ($cobrosAnticipo as $cobro) {
//                $comprobante = $cobro->getComprobantes()[0];
//                $comprobantes[$comprobante->getId()] = $comprobante->getTextoParaAsiento();
//
//                $codigo_cliente = $comprobante->getCliente()->getId();
//                $total_cuenta_anticipos += $cobro->getMonto();
//            }
//            $cuenta_contable = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_CLIENTES)->getCuentaContable();
//            $codigo_cuenta_contable = $cuenta_contable->getCodigoCuentaContable();
//            $asientoArray[$codigo_cuenta_contable] = array(
//                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
//                'monto' => $total_cuenta_anticipos,
//                'detalle' => $detalleRenglon
//            );
//            $total_cuenta_deudores += $total_cuenta_anticipos;
//        }

        $monto_anticipo = 0;
        if (sizeOf($cobrosRenglonCobranza)) { //pueden venir cobrosRenglonCobranza con renglonesBanco o con renglonesCheque
            $total_cuenta_banco = 0;
            $total_cuenta_cheques = 0;
            $total_cuenta_a_imputar = 0;
            foreach ($cobrosRenglonCobranza as $cobro) { //$cobro puede tener renglones banco o renglones cheque pero esos $cobro vienen en la misma colección
                $comprobante = $cobro->getComprobantes()[0];
                $comprobantes[$comprobante->getId()] = $comprobante->getTextoParaAsiento();
                $codigo_cliente = $comprobante->getCliente()->getId();

                $monto_cancelado = $cobro->getMonto();
                $monto_anticipo = ($cobro->getAnticipoCliente() != null ? $cobro->getAnticipoCliente()->getMonto() : 0);
                $total_asiento = $monto_cancelado + $monto_anticipo;
                $monto_cheques = $cobro->getMontoCheques();
                $monto_banco = $total_asiento - $monto_cheques;

                $hayBancoEnCobro = (abs($monto_cheques - $monto_cancelado) > 0.00000001);
                $hayChequeEnCobro = $monto_cheques != 0;

                $total_cuenta_deudores += $monto_cancelado;

                if ($esImputacionCompleta) {

                    if ($hayBancoEnCobro) { //o es banco o es cheque
                        $renglonCobro = $cobro->getRenglonesCobranza()->first(); //puede tener varios renglones
                        $id_cuenta_bancaria = $renglonCobro->getIdCuentaBancaria();

                        $total_cuenta_banco += $monto_banco;
                    }
                    if ($hayChequeEnCobro)
                        $total_cuenta_cheques += $hayBancoEnCobro ? $monto_cheques : $monto_cheques + $monto_anticipo;
                } else
                    $total_cuenta_a_imputar += $total_asiento;
            }
            if ($total_cuenta_banco > 0) {

                $cuenta_bancaria = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($id_cuenta_bancaria);
                $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cuenta_bancaria->getIdCuentaContable());

                if (!$cuenta_contable) {
                    $erroresArray[$cuenta_bancaria->getId()] = 'La cuenta bancaria ' . $cuenta_bancaria . ' no posee una cuenta contable asociada.';
                }
                $codigo_cuenta_contable = $cuenta_contable->getCodigoCuentaContable();
                $asientoArray[$codigo_cuenta_contable] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $total_cuenta_banco,
                    'detalle' => $detalleRenglon
                );
            }
            if ($total_cuenta_cheques > 0) {
                $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::VALORES_A_DEPOSITAR);
                $codigo_cuenta_contable = $cuenta_contable->getCodigoCuentaContable();
                $asientoArray[$codigo_cuenta_contable] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $total_cuenta_cheques,
                    'detalle' => $detalleRenglon
                );
            }
//            if ($monto_anticipo > 0) {
//                $cuenta_contable = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_CLIENTES)->getCuentaContable();
//                $codigo_cuenta_contable = $cuenta_contable->getCodigoCuentaContable();
//                $asientoArray[$codigo_cuenta_contable] = array(
//                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
//                    'monto' => $monto_anticipo,
//                    'detalle' => $detalleRenglon
//                );
//            }
            $total_cuenta_deudores += $monto_anticipo;

            if ($total_cuenta_a_imputar > 0) { //o es este o al menos uno de los anteriores
                $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::COBRANZAS_A_IMPUTAR);
                $codigo_cuenta_contable = $cuenta_contable->getCodigoCuentaContable();
                $asientoArray[$codigo_cuenta_contable] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $total_cuenta_a_imputar,
                    'detalle' => $detalleRenglon
                );
            }
        }

        if ($total_cuenta_deudores > 0) {

            $cuenta_deudores = null;
            if ($esOnabe) {
                $cuenta_deudores = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::DEUDORES_POR_VENTA_ONABE);
            } else {
                $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->find($codigo_cliente);
                $cuenta_deudores = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cliente->getIdCuentaContable());
            }
            
            $codigo_cuenta_deudores = $cuenta_deudores->getCodigoCuentaContable();
            $asientoArray[$codigo_cuenta_deudores] = array(
                'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                'monto' => $total_cuenta_deudores,
                'detalle' => $detalleRenglon
            );
        }

        $renglones_asiento = array();

        foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {

            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);

            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => $datos_movimiento['monto'],
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::COBRANZAS);

        $denominacion_detalle = '';
        foreach ($comprobantes as $cbte) {
            $denominacion_detalle .= ($cbte . ', ');
        }
        $denominacion_detalle = substr($denominacion_detalle, 0, -2);

        $datosAsiento = array(
            'denominacion' => (sizeOf($comprobantes) > 1 ? 'Cobranza relacionada con los comprobantes: ' . $denominacion_detalle : 'Cobranza relacionada con el comprobante ' . $denominacion_detalle),
            'razonSocial' => $cliente ? $cliente->getClienteProveedor()->getRazonSocial() : null,
            'numeroDocumento' => $cliente ? $cliente->getClienteProveedor()->getCuit() : null,
            'concepto' => $concepto_asiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario,
            'fecha_contable' => $fecha
        );

        $asiento = $this->generarAsientoContable($datosAsiento, $offsetNumeroAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $resultArray['mensajeErrorContable'] = $mensajeErrorAsientoContable;
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoParaImputaciones($cobrosRetencion, $cobrosAnticipo, $cobrosRenglonCobranza, $esImputacionCompleta, $esContraAsiento, $asiento);
            //->crearEjecutadoParaRetencion($cobro, $esContraAsiento, $asiento);
            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $resultArray['mensajeErrorPresupuestario'] = $mensajeErrorAsientoPresupuestarioAsiento;
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        $resultArray['numeroAsiento'] = $numeroAsiento;

        return $resultArray;
    }

    /**
     * 
     * @param ComprobanteCompra $comprobante
     * @param type $usuario
     * @return type
     */
    public function generarAsientoNotaCreditoCompras(ComprobanteCompra $comprobante, Usuario $usuario, $esContraAsiento = false) {

        $asientoArray = array();

        $emAutenticacion = $this->doctrine->getManager(EntityManagers::getEmAutenticacion());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $total_asiento = 0;

        $revertirImputacion = ($esContraAsiento && ($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteCompra::NOTA_CREDITO)) || (!$esContraAsiento && ($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteCompra::NOTA_CREDITO));

        $erroresArray = array();

        $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')->find($comprobante->getIdProveedor());

        $detalleRenglon = 'Comprobante ' . $comprobante->getTipoComprobante() . ' ' . $comprobante->getNumeroCompleto() . ' - ' . $proveedor->getCUITAndRazonSocial();

        // Cuentas de bienes e IVA
        /* @var $renglon_comprobante RenglonComprobanteCompra */
        foreach ($comprobante->getRenglonesComprobante() as $renglon_comprobante) {

            /* @var $renglon_oc RenglonOrdenCompra */
            $renglon_oc = $emCompras->getRepository('ADIFComprasBundle:RenglonOrdenCompra')
                    ->find($renglon_comprobante->getIdRenglonOrdenCompra());

            // es renglon de servicio
            if ($renglon_oc->getRenglonCotizacion() == null) {

                /* @var $renglon_oc RenglonOrdenCompra */
                $bienEconomico = $emCompras->getRepository('ADIFComprasBundle:BienEconomico')->find($renglon_comprobante->getIdBienEconomico());
                $cuenta_contable = $bienEconomico->getCuentaContable();

                /* @var $cuenta_contable CuentaContable */
                $codigo_cuenta = $cuenta_contable->getCodigoCuentaContable();

                foreach ($renglon_comprobante->getRenglonComprobanteCompraCentrosDeCosto() as $renglonComprobanteCompraCentrosDeCosto) {

                    /* @var $renglonComprobanteCompraCentrosDeCosto RenglonComprobanteCompraCentrosDeCosto */
                    $centro_costo = $renglonComprobanteCompraCentrosDeCosto->getCentroDeCosto();

                    // Indico el centro de costos
                    if ($centro_costo != null) {

                        $naturalezaCuentaContable = $cuenta_contable->getSegmentoOrden(1);

                        // Si la naturaleza es un gasto
                        if ($naturalezaCuentaContable == ConstanteNaturalezaCuenta::GASTO) {
                            $codigo_cuenta = $this
                                    ->getCuentaContableConCentroCosto($codigo_cuenta, $centro_costo);
                        }
                    }

                    $acumulado = isset($asientoArray[$codigo_cuenta]['monto']) ? $asientoArray[$codigo_cuenta]['monto'] : 0;

                    //Cuenta asociada al bien
                    $asientoArray[$codigo_cuenta] = array(
                        'imputacion' => !$revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                        'monto' => $acumulado + $renglonComprobanteCompraCentrosDeCosto->getPorcentaje() * $renglon_comprobante->getMontoNeto() / 100,
                        'detalle' => $detalleRenglon
                    );
                }

                $total_asiento += $renglon_comprobante->getMontoNeto();

                if ($renglon_comprobante->getAlicuotaIva()->getValor() != '0.00') {

                    $codigo_cuenta_iva = $renglon_comprobante->getAlicuotaIva()->getCuentaContableCredito()->getCodigoCuentaContable();
                    $acumulado_iva = isset($asientoArray[$codigo_cuenta_iva]['monto']) ? $asientoArray[$codigo_cuenta_iva]['monto'] : 0;

                    //Cuenta asociada al iva
                    $asientoArray[$codigo_cuenta_iva] = array(
                        'imputacion' => !$revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                        'monto' => $acumulado_iva + $renglon_comprobante->getMontoIva(),
                        'detalle' => $detalleRenglon
                    );

                    $total_asiento += $renglon_comprobante->getMontoIva();
                }
                // es renglon de OC
            } else {

                $bienEconomico = $renglon_oc->getBienEconomico();

                /* @var $cuenta_contable CuentaContable */
                $cuenta_contable = $bienEconomico->getCuentaContable();

                if (!$cuenta_contable) {
                    $erroresArray[$bienEconomico->getId()] = 'El bien econ&oacute;mico ' . $bienEconomico . ' no posee una cuenta contable asociada.';
                }

                $codigo_cuenta = $cuenta_contable->getCodigoCuentaContable();

                $naturaleza_cuenta = $cuenta_contable->getSegmentoOrden(1);

                if ($naturaleza_cuenta == ConstanteNaturalezaCuenta::GASTO) {

                    if ($renglon_oc->getIdCentroCosto() != null) {

                        $centro_costo = $renglon_oc->getCentroCosto();
                    } //.
                    else {

                        // Si la naturaleza es un gasto, busco el centro de costos del bien
                        if ($renglon_oc->getRenglonPedidoInterno()) {

                            $id_area = $renglon_oc->getRenglonPedidoInterno()
                                            ->getPedidoInterno()->getIdArea();
                        } else {

                            $id_usuario = $renglon_oc->getRenglonCotizacion()
                                            ->getRenglonRequerimiento()->getRenglonSolicitudCompra()
                                            ->getSolicitudCompra()->getIdUsuario();

                            $usuario = $emAutenticacion->getRepository('ADIFAutenticacionBundle:Usuario')
                                    ->find($id_usuario);

                            $id_area = $usuario->getIdArea();
                        }

                        $area_usuario = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Area')
                                ->find($id_area);

                        $centro_costo = $emContable->getRepository('ADIFContableBundle:CentroCosto')
                                ->find($area_usuario->getIdCentrocosto());
                    }

                    $codigo_cuenta = $this
                            ->getCuentaContableConCentroCosto($codigo_cuenta, $centro_costo);
                }

                $acumulado = isset($asientoArray[$codigo_cuenta]['monto']) //
                        ? $asientoArray[$codigo_cuenta]['monto'] //
                        : 0;

                $brutoProrrateado = $renglon_comprobante->getMontoAdicionalProrrateadoDiscriminado();

                // Cuenta asociada al bien
                $asientoArray[$codigo_cuenta] = array(
                    'imputacion' => !$revertirImputacion //
                            ? ConstanteTipoOperacionContable::DEBE //
                            : ConstanteTipoOperacionContable::HABER,
                    'monto' => $acumulado + $brutoProrrateado['neto'],
                    'detalle' => $detalleRenglon
                );

                $total_asiento += $brutoProrrateado['neto'];

                if ($renglon_comprobante->getAlicuotaIva()->getValor() != '0.00') {

                    $codigo_cuenta_iva = $renglon_comprobante->getAlicuotaIva()
                                    ->getCuentaContableCredito()->getCodigoCuentaContable();

                    $acumulado_iva = isset($asientoArray[$codigo_cuenta_iva]['monto']) //
                            ? $asientoArray[$codigo_cuenta_iva]['monto'] //
                            : 0;

                    //Cuenta asociada al iva
                    $asientoArray[$codigo_cuenta_iva] = array(
                        'imputacion' => !$revertirImputacion //
                                ? ConstanteTipoOperacionContable::DEBE //
                                : ConstanteTipoOperacionContable::HABER,
                        'monto' => $acumulado_iva + $brutoProrrateado['iva'],
                        'detalle' => $detalleRenglon
                    );

                    $total_asiento += $brutoProrrateado['iva'];
                }
            }
        }

        // Cuentas de IVA de los adicionales
        foreach ($comprobante->getAdicionales() as $adicional) {
            /* @var $adicional AdicionalComprobanteCompra */
            if ($adicional->getAlicuotaIva()->getValor() != '0.00') {
                $codigo_cuenta_iva = $adicional->getAlicuotaIva()->getCuentaContableCredito()->getCodigoCuentaContable();
                $acumulado_iva = isset($asientoArray[$codigo_cuenta_iva]['monto']) ? $asientoArray[$codigo_cuenta_iva]['monto'] : 0;
                //Cuenta asociada al iva
                $asientoArray[$codigo_cuenta_iva] = array(
                    'imputacion' => !$revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $acumulado_iva + $adicional->getMontoIva(),
                    'detalle' => $detalleRenglon
                );

                $total_asiento += $adicional->getMontoIva();
            }
        }

        //Cuentas de percepciones
        foreach ($comprobante->getRenglonesPercepcion() as $renglon_percepcion) {

            /* @var $renglon_percepcion RenglonPercepcion */
            if ($renglon_percepcion->getJurisdiccion()) {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneBy(array(
                    'conceptoPercepcion' => $renglon_percepcion->getConceptoPercepcion(), //
                    'jurisdiccion' => $renglon_percepcion->getJurisdiccion())
                );
            } else {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneByConceptoPercepcion($renglon_percepcion->getConceptoPercepcion());
            }

            if ($cuenta_percepcion) {
                $codigo_cuenta_percepcion = $cuenta_percepcion->getCuentaContableCredito()->getCodigoCuentaContable();
            } else {
                $erroresArray[$renglon_percepcion->getConceptoPercepcion()->getId()] = 'El concepto de percepci&oacute;n ' . $renglon_percepcion->getConceptoPercepcion() . ' no posee una cuenta contable asociada.';
            }

            $acumulado_percepcion = isset($asientoArray[$codigo_cuenta_percepcion]['monto']) ? $asientoArray[$codigo_cuenta_percepcion]['monto'] : 0;

            //Cuenta asociada a la percepcion
            $asientoArray[$codigo_cuenta_percepcion] = array(
                'imputacion' => !$revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumulado_percepcion + $renglon_percepcion->getMonto(),
                'detalle' => $detalleRenglon
            );
            $total_asiento += $renglon_percepcion->getMonto();
        }

        //Cuentas de impuestos
        foreach ($comprobante->getRenglonesImpuesto() as $renglon_impuesto) {
            /* @var $renglon_impuesto RenglonImpuesto */

            $codigo_cuenta_impuesto = $renglon_impuesto->getConceptoImpuesto()->getCuentaContable()->getCodigoCuentaContable();
            $acumulado_impuesto = isset($asientoArray[$codigo_cuenta_impuesto]['monto']) ? $asientoArray[$codigo_cuenta_impuesto]['monto'] : 0;

            //Cuenta asociada a la percepcion
            $asientoArray[$codigo_cuenta_impuesto] = array(
                'imputacion' => !$revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumulado_impuesto + $renglon_impuesto->getMonto(),
                'detalle' => $detalleRenglon
            );
            $total_asiento += $renglon_impuesto->getMonto();
        }

        if ($proveedor->getCuentaContable()) {
            $codigo_cuenta_proveedor = $proveedor->getCuentaContable()->getCodigoCuentaContable();
        } else {
            $erroresArray[$proveedor->getId()] = 'El proveedor ' . $proveedor->cuitAndRazonSocial() . ' no posee una cuenta contable asociada.';
        }

        $asientoArray[$codigo_cuenta_proveedor] = array(
            'imputacion' => !$revertirImputacion ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
            'monto' => $total_asiento,
            'detalle' => $detalleRenglon
        );

        $renglones_asiento = array();

        foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {
            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);
            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => $datos_movimiento['monto'],
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::COMPRAS);

        $denominacion = ($esContraAsiento ? 'Anulaci&oacute;n - ' : '') . 'Asiento de cancelación con nota de crédito';

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $concepto_asiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario,
            'razonSocial' => $proveedor->getRazonSocial(),
            'numeroDocumento' => $proveedor->getCUIT(),
            'comprobante' => $comprobante
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $mensajeErrorAsientoContable);
        } //.
        else {

            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearAsientoFromComprobanteCompra($comprobante, $esContraAsiento, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $this->container->get('request')->getSession()->getFlashBag()
                        ->add('error', $mensajeErrorAsientoPresupuestarioAsiento);
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param ComprobanteObra $comprobante
     * @param Usuario $usuario
     * @param type $esContraAsiento
     * @return type
     */
    public function generarAsientoNotaCreditoObras(ComprobanteObra $comprobante, Usuario $usuario, $esContraAsiento = false) {

        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $revertirImputacion = ($esContraAsiento && ($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteObra::NOTA_CREDITO)) || (!$esContraAsiento && ($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteObra::NOTA_CREDITO));

        $asientoArray = array();

        $erroresArray = array();
        /* @var $proveedor Proveedor */
        $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')->find($comprobante->getIdProveedor());

        $detalleRenglon = 'Comprobante ' . $comprobante->getTipoComprobante() . ' ' . $comprobante->getNumeroCompleto() . ' - ' . $proveedor->getCUITAndRazonSocial();

        // Por cada renglon del comprobante de obra
        foreach ($comprobante->getRenglonesComprobante() as $renglonComprobanteObra) {
            /* @var $renglonComprobanteObra RenglonComprobanteObra  */

            ///////////////////////

            /* @var $comprobanteRenglon ComprobanteObra */
            $comprobanteRenglon = $renglonComprobanteObra->getRenglonAcreditado()->getComprobante();

            /* @var $documentoFinanciero DocumentoFinanciero */
            $documentoFinanciero = $comprobanteRenglon->getDocumentoFinanciero();

            // Si el documento financiero es un AnticipoFinanciero
            if ($documentoFinanciero->getEsAnticipoFinanciero()) {

                $cuentaContable = $documentoFinanciero->getTipoDocumentoFinanciero()->getCuentaContable();

                /* @var $cuentaContable CuentaContable */
                $codigoCuentaContable = $cuentaContable->getCodigoCuentaContable();

                $asientoArray[$codigoCuentaContable] = array(
                    'imputacion' => !$revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $renglonComprobanteObra->getMontoNeto(),
                    'detalle' => $detalleRenglon
                );
            } else {

                $tramo = $comprobanteRenglon->getTramo();

                if ($tramo->getTieneFuenteCAF()) {
                    // Va todo a CAF                
                    $cuentaContableFuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION_CAF);
                    $codigoCuentaContable = $cuentaContableFuenteFinanciamiento->getCodigoCuentaContable();

                    $asientoArray[$codigoCuentaContable] = array(
                        'imputacion' => !$revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                        'monto' => $renglonComprobanteObra->getMontoNeto(),
                        'detalle' => $detalleRenglon
                    );
                } else {
                    foreach ($tramo->getFuentesFinanciamiento() as $fuenteFinanciamientoTramo) {
                        $fuenteFinanciamiento = $fuenteFinanciamientoTramo->getFuenteFinanciamiento();

                        // Si la FuenteFinanciamiento modifica las cuentas contables
                        if ($fuenteFinanciamiento->getModificaCuentaContable()) {
                            $cuentaContableFuenteFinanciamiento = $fuenteFinanciamiento->getCuentaContable();
                        } else {
                            $cuentaContableFuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION);
                        }

                        if ($cuentaContableFuenteFinanciamiento != null) {
                            $totalComprobanteProrrateado = $renglonComprobanteObra->getMontoNeto() * $fuenteFinanciamientoTramo->getPorcentaje() / 100;

                            /* @var $cuentaContable CuentaContable */
                            $codigoCuentaContable = $cuentaContableFuenteFinanciamiento->getCodigoCuentaContable();

                            $acumuladoFuenteFinanciamiento = isset($asientoArray[$codigoCuentaContable]['monto']) //
                                    ? $asientoArray[$codigoCuentaContable]['monto'] + $totalComprobanteProrrateado //
                                    : $totalComprobanteProrrateado;

                            $asientoArray[$codigoCuentaContable] = array(
                                'imputacion' => !$revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                                'monto' => $acumuladoFuenteFinanciamiento,
                                'detalle' => $detalleRenglon
                            );
                        } else {
                            $erroresArray[ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION] = 'No se encontr&oacute; una cuenta contable con c&oacute;digo interno: ' . ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION;
                        }
                    }
                }
            }

            ///////////////////////

            if ($renglonComprobanteObra->getAlicuotaIva()->getValor() != '0.00') {
                $codigo_cuenta_iva = $renglonComprobanteObra->getAlicuotaIva()->getCuentaContableCredito()->getCodigoCuentaContable();

                $acumulado_iva = isset($asientoArray[$codigo_cuenta_iva]['monto']) //
                        ? $asientoArray[$codigo_cuenta_iva]['monto'] //
                        : 0;

                // Cuenta asociada al iva
                $asientoArray[$codigo_cuenta_iva] = array(
                    'imputacion' => !$revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                    'monto' => $acumulado_iva + $renglonComprobanteObra->getMontoIva(),
                    'detalle' => $detalleRenglon
                );
            }
        }

        //Cuentas de percepciones
        foreach ($comprobante->getRenglonesPercepcion() as $renglon_percepcion) {
            /* @var $renglon_percepcion RenglonPercepcion */
            if ($renglon_percepcion->getJurisdiccion()) {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')->findOneBy(array(
                    'conceptoPercepcion' => $renglon_percepcion->getConceptoPercepcion(), //
                    'jurisdiccion' => $renglon_percepcion->getJurisdiccion())
                );
            } else {
                $cuenta_percepcion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')->findOneByConceptoPercepcion($renglon_percepcion->getConceptoPercepcion());
            }

            if ($cuenta_percepcion) {
                $codigo_cuenta_percepcion = $cuenta_percepcion->getCuentaContableCredito()->getCodigoCuentaContable();
            } else {
                $erroresArray[$renglon_percepcion->getConceptoPercepcion()->getId()] = 'El concepto de percepci&oacute;n ' . $renglon_percepcion->getConceptoPercepcion() . ' no posee una cuenta contable asociada.';
            }

            $acumulado_percepcion = isset($asientoArray[$codigo_cuenta_percepcion]['monto']) ? $asientoArray[$codigo_cuenta_percepcion]['monto'] : 0;

            //Cuenta asociada a la percepcion
            $asientoArray[$codigo_cuenta_percepcion] = array(
                'imputacion' => !$revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumulado_percepcion + $renglon_percepcion->getMonto(),
                'detalle' => $detalleRenglon
            );
        }

        // Cuentas de impuestos
        foreach ($comprobante->getRenglonesImpuesto() as $renglonImpuesto) {

            /* @var $renglonImpuesto RenglonImpuesto */

            $codigoCuentaContableImpuesto = $renglonImpuesto->getConceptoImpuesto()
                            ->getCuentaContable()->getCodigoCuentaContable();

            $acumuladoImpuesto = isset($asientoArray[$codigoCuentaContableImpuesto]['monto']) //
                    ? $asientoArray[$codigoCuentaContableImpuesto]['monto'] //
                    : 0;

            //Cuenta asociada a la percepcion
            $asientoArray[$codigoCuentaContableImpuesto] = array(
                'imputacion' => !$revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
                'monto' => $acumuladoImpuesto + $renglonImpuesto->getMonto(),
                'detalle' => $detalleRenglon
            );
        }

        if ($proveedor->getCuentaContable()) {
            $codigoCuentaContableProveedor = $proveedor->getCuentaContable()->getCodigoCuentaContable();
        } else {
            $erroresArray[$proveedor->getId()] = 'El proveedor ' . $proveedor->cuitAndRazonSocial() . ' no posee una cuenta contable asociada.';
        }

        $asientoArray[$codigoCuentaContableProveedor] = array(
            'imputacion' => !$revertirImputacion ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
            'monto' => $comprobante->getTotal(),
            'detalle' => $detalleRenglon
        );

        $renglonesAsiento = array();

        foreach ($asientoArray as $codigoCuentaContable => $datosMovimiento) {
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigoCuentaContable);

            if ($cuentaContable) {
                $renglonesAsiento[] = array(
                    'cuenta' => $cuentaContable,
                    'imputacion' => $datosMovimiento['imputacion'],
                    'monto' => $datosMovimiento['monto'],
                    'detalle' => $datosMovimiento['detalle']
                );
            } else {
                $erroresArray[$codigoCuentaContable] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigoCuentaContable;
            }
        }

        $conceptoAsiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::COMPRAS);

        $denominacion = ($esContraAsiento ? 'Anulaci&oacute;n - ' : '') . 'Asiento de cancelación con nota de crédito';

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $conceptoAsiento,
            'renglones' => $renglonesAsiento,
            'usuario' => $usuario,
            'razonSocial' => $proveedor->getRazonSocial(),
            'numeroDocumento' => $proveedor->getCUIT(),
            'comprobante' => $comprobante
        );

        $asiento = $this->generarAsientoContable($datosAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorAsientoPresupuestarioAsiento = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {
            $this->container->get('request')->getSession()->getFlashBag()->add('error', $mensajeErrorAsientoContable);
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorAsientoPresupuestarioAsiento = $this->container->get('adif.contabilidad_presupuestaria_service')->crearAsientoFromNotaCreditoObra($comprobante, $esContraAsiento, $asiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorAsientoPresupuestarioAsiento != '') {
                $this->container->get('request')->getSession()->getFlashBag()->add('error', $mensajeErrorAsientoPresupuestarioAsiento);
            }
        }

        // Si hubo algun error
        if ($mensajeErrorAsientoPresupuestarioAsiento != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param EjercicioContable $ejercicioContable
     * @param type $fechaContable
     * @param type $usuario
     * @return type
     */
    public function generarAsientoRefundicionResultados(EjercicioContable $ejercicioContable, $fechaContable, $usuario) {

        $em = $this->doctrine->getManager(EntityManagers::getEmContable());

        $renglonesAsientoContable = array();

        $totalDebe = $totalHaber = 0;

        $numeroAsiento = -1;

        $naturalezaArray = array(ConstanteNaturalezaCuenta::INGRESO, ConstanteNaturalezaCuenta::GASTO);

        $cuentasContables = $this->getCuentasContablesByNaturaleza($ejercicioContable, $naturalezaArray);

        if (!empty($cuentasContables)) {

            foreach ($cuentasContables as $cuentaContableArray) {

                $naturalezaCuentaContable = $cuentaContableArray['naturalezaCuentaContable'];

                if ($naturalezaCuentaContable == ConstanteNaturalezaCuenta::INGRESO) {

                    $saldo = $cuentaContableArray['totalHaber'] - $cuentaContableArray['totalDebe'];

                    if ($saldo > 0) {

                        $tipoOperacionContable = ConstanteTipoOperacionContable::DEBE;
                        $totalDebe += abs($saldo);
                    } //. 
                    else {

                        $tipoOperacionContable = ConstanteTipoOperacionContable::HABER;
                        $totalHaber += abs($saldo);
                    }
                }


                if ($naturalezaCuentaContable == ConstanteNaturalezaCuenta::GASTO) {

                    $saldo = $cuentaContableArray['totalDebe'] - $cuentaContableArray['totalHaber'];

                    if ($saldo > 0) {

                        $tipoOperacionContable = ConstanteTipoOperacionContable::HABER;
                        $totalHaber += abs($saldo);
                    } //.
                    else {

                        $tipoOperacionContable = ConstanteTipoOperacionContable::DEBE;
                        $totalDebe += abs($saldo);
                    }
                }


                $cuentaContable = $em->getRepository('ADIFContableBundle:CuentaContable')
                        ->find($cuentaContableArray['id']);

                if ($cuentaContable) {

                    $renglonesAsientoContable[] = array(
                        'cuenta' => $cuentaContable,
                        'imputacion' => $tipoOperacionContable,
                        'monto' => abs($saldo),
                        'detalle' => null
                    );
                }
            }

            if ($totalDebe != $totalHaber) {

                $cuentaContableResultadoEjercicio = $em
                        ->getRepository('ADIFContableBundle:CuentaContable')
                        ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::RESULTADO_EJERCICIO);

                if ($totalDebe > $totalHaber) {

                    $tipoOperacionContable = ConstanteTipoOperacionContable::HABER;
                    $montoResultadoEjercicio = $totalDebe - $totalHaber;
                } //.
                elseif ($totalHaber > $totalDebe) {

                    $tipoOperacionContable = ConstanteTipoOperacionContable::DEBE;
                    $montoResultadoEjercicio = $totalHaber - $totalDebe;
                }

                $renglonesAsientoContable[] = array(
                    'cuenta' => $cuentaContableResultadoEjercicio,
                    'imputacion' => $tipoOperacionContable,
                    'monto' => $montoResultadoEjercicio,
                    'detalle' => null
                );
            }

            $conceptoAsiento = $em->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                    ->findOneByCodigo(ConstanteConceptoAsientoContable::FORMAL_REFUNDICION);

            $datosAsiento = array(
                'denominacion' => 'Refundici&oacute;n de resultados ' . $ejercicioContable->getDenominacionEjercicio(),
                'razonSocial' => null,
                'numeroDocumento' => null,
                'concepto' => $conceptoAsiento,
                'renglones' => $renglonesAsientoContable,
                'usuario' => $usuario,
                'fecha_contable' => $fechaContable
            );

            $asiento = $this->generarAsientoContable($datosAsiento);

            $numeroAsiento = $asiento->getNumeroAsiento();

            $mensajeErrorAsientoContable = $this->getMensajeError($asiento, array());
        } else {

            $mensajeErrorAsientoContable = 'Asiento formal ya realizado para el ejercicio contable ' .
                    $ejercicioContable->getDenominacionEjercicio() . '.';
        }

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $mensajeErrorAsientoContable);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param EjercicioContable $ejercicioContable
     * @param type $fechaContable
     * @param type $usuario
     * @return type
     */
    public function generarAsientoCierreEjercicio(EjercicioContable $ejercicioContable, $fechaContable, $usuario) {

        $numeroAsiento = -1;

        if ($this->validarEjecucionAsientoCierreEjercicio($ejercicioContable)) {

            $em = $this->doctrine->getManager(EntityManagers::getEmContable());

            $renglonesAsientoContable = array();

            $naturalezaArray = array(ConstanteNaturalezaCuenta::ACTIVO, ConstanteNaturalezaCuenta::PASIVO, ConstanteNaturalezaCuenta::PATRIMONIO_NETO);

            $cuentasContables = $this->getCuentasContablesByNaturaleza($ejercicioContable, $naturalezaArray);

            if (!empty($cuentasContables)) {

                foreach ($cuentasContables as $cuentaContableArray) {

                    $naturalezaCuentaContable = $cuentaContableArray['naturalezaCuentaContable'];

                    if ($naturalezaCuentaContable == ConstanteNaturalezaCuenta::ACTIVO) {

                        $saldo = $cuentaContableArray['totalDebe'] - $cuentaContableArray['totalHaber'];

                        if ($saldo > 0) {
                            $tipoOperacionContable = ConstanteTipoOperacionContable::HABER;
                        } //. 
                        else {
                            $tipoOperacionContable = ConstanteTipoOperacionContable::DEBE;
                        }
                    }

                    if ($naturalezaCuentaContable == ConstanteNaturalezaCuenta::PASIVO || $naturalezaCuentaContable == ConstanteNaturalezaCuenta::PATRIMONIO_NETO) {

                        $saldo = $cuentaContableArray['totalHaber'] - $cuentaContableArray['totalDebe'];

                        if ($saldo > 0) {
                            $tipoOperacionContable = ConstanteTipoOperacionContable::DEBE;
                        } //.
                        else {
                            $tipoOperacionContable = ConstanteTipoOperacionContable::HABER;
                        }
                    }

                    $cuentaContable = $em->getRepository('ADIFContableBundle:CuentaContable')
                            ->find($cuentaContableArray['id']);

                    if ($cuentaContable) {

                        $renglonesAsientoContable[] = array(
                            'cuenta' => $cuentaContable,
                            'imputacion' => $tipoOperacionContable,
                            'monto' => abs($saldo),
                            'detalle' => null
                        );
                    }
                }

                $conceptoAsiento = $em->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                        ->findOneByCodigo(ConstanteConceptoAsientoContable::FORMAL_CIERRE);

                $datosAsiento = array(
                    'denominacion' => 'Cierre ejercicio contable ' . $ejercicioContable->getDenominacionEjercicio(),
                    'razonSocial' => null,
                    'numeroDocumento' => null,
                    'concepto' => $conceptoAsiento,
                    'renglones' => $renglonesAsientoContable,
                    'usuario' => $usuario,
                    'fecha_contable' => $fechaContable
                );

                $asiento = $this->generarAsientoContable($datosAsiento);

                $numeroAsiento = $asiento->getNumeroAsiento();

                $mensajeErrorAsientoContable = $this->getMensajeError($asiento, array());
            } else {

                $mensajeErrorAsientoContable = 'Asiento formal ya realizado para el ejercicio contable ' .
                        $ejercicioContable->getDenominacionEjercicio() . '.';
            }
        } else {

            $mensajeErrorAsientoContable = 'El asiento formal no se puede realizar para el ejercicio contable ' .
                    $ejercicioContable->getDenominacionEjercicio() . '.';
        }

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $mensajeErrorAsientoContable);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param EjercicioContable $ejercicioContable
     * @param type $fechaContable
     * @param type $usuario
     * @return type
     */
    public function generarAsientoAperturaEjercicio(EjercicioContable $ejercicioContable, $fechaContable, $usuario) {

        $em = $this->doctrine->getManager(EntityManagers::getEmContable());

        $renglonesAsientoContable = array();

        $numeroAsiento = -1;

        $denominacionEjercicioContableAnterior = ((int) $ejercicioContable->getDenominacionEjercicio()) - 1;

        $ejercicioContableAnterior = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByDenominacion($denominacionEjercicioContableAnterior);

        if ($ejercicioContableAnterior) {

            $cuentasContables = $this->getCuentasContablesCierreByEjercicio($ejercicioContableAnterior);

            if (!empty($cuentasContables)) {

                foreach ($cuentasContables as $cuentaContableArray) {

                    $naturalezaCuentaContable = $cuentaContableArray['naturalezaCuentaContable'];

                    if ($naturalezaCuentaContable == ConstanteNaturalezaCuenta::ACTIVO) {

                        $saldo = $cuentaContableArray['totalDebe'] - $cuentaContableArray['totalHaber'];

                        if ($saldo < 0) {
                            $tipoOperacionContable = ConstanteTipoOperacionContable::DEBE;
                        } //. 
                        else {
                            $tipoOperacionContable = ConstanteTipoOperacionContable::HABER;
                        }
                    }

                    if ($naturalezaCuentaContable == ConstanteNaturalezaCuenta::PASIVO || $naturalezaCuentaContable == ConstanteNaturalezaCuenta::PATRIMONIO_NETO) {

                        $saldo = $cuentaContableArray['totalHaber'] - $cuentaContableArray['totalDebe'];

                        if ($saldo < 0) {
                            $tipoOperacionContable = ConstanteTipoOperacionContable::HABER;
                        } //.
                        else {
                            $tipoOperacionContable = ConstanteTipoOperacionContable::DEBE;
                        }
                    }

                    $cuentaContable = $em->getRepository('ADIFContableBundle:CuentaContable')
                            ->find($cuentaContableArray['id']);

                    if ($cuentaContable) {

                        $renglonesAsientoContable[] = array(
                            'cuenta' => $cuentaContable,
                            'imputacion' => $tipoOperacionContable,
                            'monto' => abs($saldo),
                            'detalle' => null
                        );
                    }
                }

                $conceptoAsiento = $em->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                        ->findOneByCodigo(ConstanteConceptoAsientoContable::FORMAL_APERTURA);

                $datosAsiento = array(
                    'denominacion' => 'Apertura ejercicio contable ' . $ejercicioContable->getDenominacionEjercicio(),
                    'razonSocial' => null,
                    'numeroDocumento' => null,
                    'concepto' => $conceptoAsiento,
                    'renglones' => $renglonesAsientoContable,
                    'usuario' => $usuario,
                    'fecha_contable' => $fechaContable
                );

                $asiento = $this->generarAsientoContable($datosAsiento);

                $numeroAsiento = $asiento->getNumeroAsiento();

                $mensajeErrorAsientoContable = $this->getMensajeError($asiento, array());
            } else {

                $mensajeErrorAsientoContable = 'Asiento formal ya realizado para el ejercicio contable ' .
                        $ejercicioContable->getDenominacionEjercicio() . '.';
            }
        } else {

            $mensajeErrorAsientoContable = 'Debe cargar el ejercicio contable ' .
                    $denominacionEjercicioContableAnterior . '.';
        }

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $mensajeErrorAsientoContable);
        }

        return $numeroAsiento;
    }

    /**
     * 
     * @param type $ejercicioContable
     * @return type
     */
    private function validarEjecucionAsientoCierreEjercicio($ejercicioContable) {

        $esValido = true;

        $naturalezaArray = array(ConstanteNaturalezaCuenta::INGRESO, ConstanteNaturalezaCuenta::GASTO);

        $cuentasContables = $this->getCuentasContablesByNaturaleza($ejercicioContable, $naturalezaArray);

        foreach ($cuentasContables as $cuentaContableArray) {

            $esValido = round($cuentaContableArray['totalHaber'], 2) == round($cuentaContableArray['totalDebe'], 2);

            if (!$esValido) {
                break;
            }
        }

        return $esValido;
    }

    /**
     * 
     * @param type $ejercicioContable
     * @param type $naturalezaArray
     * @return type
     */
    private function getCuentasContablesByNaturaleza($ejercicioContable, $naturalezaArray = array()) {

        $em = $this->doctrine->getManager(EntityManagers::getEmContable());

        $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $ejercicioContable->getFechaInicio()->format('d/m/Y') . ' 00:00:00');
        $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $ejercicioContable->getFechaFin()->format('d/m/Y') . ' 23:59:59');

        $connection = $em->getConnection();

        $query = '
            SELECT
                acumulado.id_cuenta_contable as id,
                LEFT(acumulado.codigo_cuenta_contable, 1) as naturalezaCuentaContable,
                acumulado.denominacion_cuenta_contable as denominacionCuentaContable,
                acumulado.codigo_cuenta_contable as codigoCuentaContable,
                SUM(acumulado.debe) as totalDebe,
                SUM(acumulado.haber) as totalHaber,
                SUM(acumulado.debe) - SUM(acumulado.haber) as saldo
            FROM
                (
                    SELECT
                        cc.id AS id_cuenta_contable,
                        cc.codigo as codigo_cuenta_contable,
                        cc.denominacion AS denominacion_cuenta_contable,
                        CASE toc.denominacion
                            WHEN :denominacionDebe THEN
                                    sum(r.importe_mcl)
                            ELSE 0
                        END AS debe,
                        CASE toc.denominacion
                            WHEN :denominacionHaber THEN
                                    sum(r.importe_mcl)
                            ELSE 0
                        END AS haber
                    FROM
                        cuenta_contable cc
                    LEFT JOIN
                        renglon_asiento_contable r ON r.id_cuenta_contable = cc.id
                    LEFT JOIN
                        tipo_operacion_contable toc ON r.id_tipo_operacion_contable = toc.id
                    LEFT JOIN asiento_contable a ON r.id_asiento_contable = a.id                    
                    WHERE LEFT(cc.codigo, 1) IN (:naturalezaArray)
                        AND (a.fecha_contable >= :fechaInicio AND a.fecha_contable <= :fechaFin)
                        AND (a.fecha_baja IS NULL)
                        AND (r.fecha_baja IS NULL)
                    GROUP BY
                        cc.id,
                        r.id_tipo_operacion_contable
                ) AS acumulado
            GROUP BY
                acumulado.id_cuenta_contable
            HAVING 
                ROUND(totalDebe, 2) <> ROUND(totalHaber, 2)';

        return $connection
                        ->executeQuery(
                                $query, //
                                array(
                            'denominacionDebe' => ConstanteTipoOperacionContable::DEBE,
                            'denominacionHaber' => ConstanteTipoOperacionContable::HABER,
                            'naturalezaArray' => $naturalezaArray,
                            'fechaInicio' => $fechaInicio,
                            'fechaFin' => $fechaFin
                                ), //
                                array(
                            'denominacionDebe' => PDO::PARAM_STR,
                            'denominacionHaber' => PDO::PARAM_STR,
                            'naturalezaArray' => Connection::PARAM_STR_ARRAY,
                            'fechaInicio' => Type::DATETIME,
                            'fechaFin' => Type::DATETIME
                                )
                        )
                        ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 
     * @param type $ejercicioContable
     * @return type
     */
    private function getCuentasContablesCierreByEjercicio($ejercicioContable) {

        $naturalezaArray = array(ConstanteNaturalezaCuenta::ACTIVO, ConstanteNaturalezaCuenta::PASIVO, ConstanteNaturalezaCuenta::PATRIMONIO_NETO);

        $codigoConceptoAsientoContable = ConstanteConceptoAsientoContable::FORMAL_CIERRE;

        $em = $this->doctrine->getManager(EntityManagers::getEmContable());

        $fechaInicio = $ejercicioContable->getFechaInicio();

        $fechaFin = $ejercicioContable->getFechaFin();

        $connection = $em->getConnection();

        $query = '
            SELECT
                acumulado.id_cuenta_contable as id,
                LEFT(acumulado.codigo_cuenta_contable, 1) as naturalezaCuentaContable,
                acumulado.denominacion_cuenta_contable as denominacionCuentaContable,
                acumulado.codigo_cuenta_contable as codigoCuentaContable,
                sum(acumulado.debe) as totalDebe,
                sum(acumulado.haber) as totalHaber,
                sum(acumulado.debe) - sum(acumulado.haber) as saldo
            FROM
                (
                    SELECT
                        cc.id AS id_cuenta_contable,
                        cc.codigo as codigo_cuenta_contable,
                        cc.denominacion AS denominacion_cuenta_contable,
                        CASE toc.denominacion
                            WHEN :denominacionDebe THEN
                                    sum(r.importe_mcl)
                            ELSE 0
                        END AS debe,
                        CASE toc.denominacion
                            WHEN :denominacionHaber THEN
                                    sum(r.importe_mcl)
                            ELSE 0
                        END AS haber
                    FROM
                        cuenta_contable cc
                    LEFT JOIN
                        renglon_asiento_contable r ON r.id_cuenta_contable = cc.id
                    LEFT JOIN
                        tipo_operacion_contable toc ON r.id_tipo_operacion_contable = toc.id
                    LEFT JOIN 
                        asiento_contable a ON r.id_asiento_contable = a.id
                    LEFT JOIN 
                        concepto_asiento_contable cac ON a.id_concepto_asiento_contable = cac.id                       
                    WHERE LEFT(cc.codigo, 1) IN (:naturalezaArray)
                        AND (a.fecha_contable >= :fechaInicio AND a.fecha_contable <= :fechaFin)
                        AND (cac.codigo = :codigoConceptoAsientoContable)
                        AND (a.fecha_baja IS NULL)
                        AND (r.fecha_baja IS NULL)
                    GROUP BY
                        cc.id,
                        r.id_tipo_operacion_contable
                ) AS acumulado
            GROUP BY
                acumulado.id_cuenta_contable
            HAVING 
                ROUND(totalDebe, 2) <> ROUND(totalHaber, 2)';

        return $connection
                        ->executeQuery(
                                $query, //
                                array(
                            'denominacionDebe' => ConstanteTipoOperacionContable::DEBE,
                            'denominacionHaber' => ConstanteTipoOperacionContable::HABER,
                            'naturalezaArray' => $naturalezaArray,
                            'codigoConceptoAsientoContable' => $codigoConceptoAsientoContable,
                            'fechaInicio' => $fechaInicio,
                            'fechaFin' => $fechaFin
                                ), //
                                array(
                            'denominacionDebe' => PDO::PARAM_STR,
                            'denominacionHaber' => PDO::PARAM_STR,
                            'naturalezaArray' => Connection::PARAM_STR_ARRAY,
                            'codigoConceptoAsientoContable' => PDO::PARAM_STR,
                            'fechaInicio' => Type::DATETIME,
                            'fechaFin' => Type::DATETIME
                                )
                        )
                        ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 
     * @param type $fechaContableParam
     * @return type
     */
    private function getFechaContable($fechaContableParam = null) {

        $fechaContable = null;

        if (isset($fechaContableParam)) {

            $fechaContable = $fechaContableParam;
        } else {

            $ejercicioContableEnCurso = (new \DateTime())->format('Y');

            $ejercicioContableSesion = $this->container->get('request')->getSession()
                    ->get('ejercicio_contable');

            if ($ejercicioContableEnCurso == $ejercicioContableSesion) {

                $fechaContable = new DateTime();
            } else {

                $em = $this->doctrine->getManager(EntityManagers::getEmContable());

                /* @var $ejercicioContable EjercicioContable */
                $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                        ->getEjercicioContableByDenominacion($ejercicioContableSesion);

                $fechaContable = $ejercicioContable->getFechaFin();
            }
        }

        return $fechaContable;
    }

    private function generarRenglonesPago($ordenPago, &$asientoArray, &$erroresArray, $esContraAsiento) {

        //Cheques
        foreach ($ordenPago->getPagoOrdenPago()->getCheques() as $cheque) {
            /* @var $cheque \ADIF\ContableBundle\Entity\Cheque */
            $cuentaBancaria = $cheque->getChequera()->getCuenta();
            $cuentaContableCuentaBancaria = $cuentaBancaria->getCuentaContable();

            if ($cuentaContableCuentaBancaria) {

                $codigoCuentaContableCuentaBancaria = $cuentaContableCuentaBancaria->getCodigoCuentaContable();

                $asientoArray[$codigoCuentaContableCuentaBancaria . '_' . $cheque->getNumeroCheque()] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                    'monto' => $cheque->getMonto(),
                    'detalle' => $cheque->getConcepto()
                );
            } else {
                $erroresArray[$cuentaBancariaPago->getId()] = 'La cuenta con CBU ' . $cuentaBancaria->getCbu() . ' no posee una cuenta contable asociada.';
            }
        }

        //Transferencias
        foreach ($ordenPago->getPagoOrdenPago()->getTransferencias() as $transferencia) {
            /* @var $transferencia \ADIF\ContableBundle\Entity\TransferenciaBancaria */
            $cuentaBancaria = $transferencia->getCuenta();
            $cuentaContableCuentaBancaria = $cuentaBancaria->getCuentaContable();

            if ($cuentaContableCuentaBancaria) {

                $codigoCuentaContableCuentaBancaria = $cuentaContableCuentaBancaria->getCodigoCuentaContable();

                $asientoArray[$codigoCuentaContableCuentaBancaria . '_' . $transferencia->getNumeroTransferencia()] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                    'monto' => $transferencia->getMonto(),
                    'detalle' => $transferencia->getConcepto()
                );
            } else {
                $erroresArray[$cuentaBancariaPago->getId()] = 'La cuenta con CBU ' . $cuentaBancaria->getCbu() . ' no posee una cuenta contable asociada.';
            }
        }
        
        //Net Cash
        if ($ordenPago->getPagoOrdenPago()->getNetCash()) {
            $netCash = $ordenPago->getPagoOrdenPago()->getNetCash();
            /* @var $netCash \ADIF\ContableBundle\Entity\NetCash */
            $cuentaBancaria = $netCash->getCuenta();
            $cuentaContableCuentaBancaria = $cuentaBancaria->getCuentaContable();

            if ($cuentaContableCuentaBancaria) {
                $codigoCuentaContableCuentaBancaria = $cuentaContableCuentaBancaria->getCodigoCuentaContable();

                $asientoArray[$codigoCuentaContableCuentaBancaria . '_' . $netCash->getNumero()] = array(
                    'imputacion' => !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
                    'monto' => $ordenPago->getMontoNeto(),
                    'detalle' => 'Net Cash N&ordm; '.$netCash->getNumero()
                );
            } else {
                $erroresArray[$cuentaBancariaPago->getId()] = 'La cuenta con CBU ' . $cuentaBancaria->getCbu() . ' no posee una cuenta contable asociada.';
            }
        }
    }

    /**
     * 
     * @param type $fechaContable
     * @return type
     */
    public function getFechaContableValida($fechaContable) {

        if ($fechaContable != null) {

            $em = $this->doctrine->getManager(EntityManagers::getEmContable());

            $denominacionEjercicioContableSesion = $this->container->get('request')
                            ->getSession()->get('ejercicio_contable');

            /* @var $ejercicioContable EjercicioContable */
            $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                    ->getEjercicioContableByDenominacion($denominacionEjercicioContableSesion);

            $fechaMesCerradoSuperior = $this->getFechaMesCerradoSuperiorByEjercicio($ejercicioContable);

            $fechaMesCerradoSuperiorFormatted = \DateTime::createFromFormat('d/m/Y', $fechaMesCerradoSuperior);

            return !$ejercicioContable->getEstaCerrado() //
                    && $fechaContable->format('d/m/Y') >= $fechaMesCerradoSuperiorFormatted->format('d/m/Y');
        
		}
		
        return false;
    }

    public function getCuentaContableComprobante($comprobante) {

        if($comprobante) {
            $cuenta_contable = null;
            $idTipoComprobante = $comprobante->getTipoComprobante()->getId();
            $em = $this->doctrine->getManager(EntityManagers::getEmContable());
            $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());

            //venta general
            if ($comprobante->esComprobanteVentaGeneral()) {
                foreach ($comprobante->getRenglonesComprobante() as $renglonComprobante) {
                    $cuenta_contable[] = $renglonComprobante->getConceptoVentaGeneral()->getCuentaIngreso();
                }
                return $cuenta_contable;
			
            } else if ($idTipoComprobante == ConstanteTipoComprobanteVenta::RENDICION_LIQUIDO_PRODUCTO) {
                if ($comprobante->getCliente() != null) {
                    $cuenta_contable = $comprobante->getCliente()->getCuentaContable();
                }
            } else {
				//venta
				$codigoClaseContrato = $comprobante->getCodigoClaseContrato();
				$claseContrato = $em->getRepository('ADIFContableBundle:Facturacion\ClaseContrato')
					->findOneByCodigo($codigoClaseContrato);
				$cuenta_contable = $claseContrato->getCuentaIngreso();
            }
            return $cuenta_contable;
        }
    }
	
	/**
     * 
     * @param ComprobanteVenta $comprobante
     * @param type $usuario
     * @return type
     */
    public function generarAsientosComprobanteRendicionLiquidoProducto($comprobante, $usuario, $offsetNumeroAsiento = 0, $esContraAsiento = false) 
	{
        $asientoArray = array();
        $erroresArray = array();

        $revertirImputacion = ($esContraAsiento && ($comprobante->getTipoComprobante()->getId() != ConstanteTipoComprobanteVenta::NOTA_CREDITO)) || (!$esContraAsiento && ($comprobante->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_CREDITO));

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $total_asiento = 0;

        $detalleRenglon = 'Asiento de rendici&oacute;n l&iacute;quido producto';
        
		// DEBE
        $codigoCuentaIngreso = $comprobante->getCliente()->getCuentaContable()->getCodigoCuentaContable();
		
		// HABER
		$codigoCuentaCredito = $emContable->getRepository('ADIFContableBundle:CuentaContable')
								->find(ComprobanteRendicionLiquidoProducto::ID_CUENTA_CREDITO);
								
		if ($codigoCuentaCredito) {
			 
			foreach ($comprobante->getRenglonesComprobante() as $renglonComprobante) {

				/* @var $renglonComprobante RenglonComprobanteRendicionLiquidoProducto */

				$codigo_cuenta = $codigoCuentaCredito->getCodigoCuentaContable();

				$acumulado = isset($asientoArray[$codigo_cuenta]['monto']) ? $asientoArray[$codigo_cuenta]['monto'] : 0;

				// Cuenta asociada al comprobante

				$total_asiento += abs($renglonComprobante->getMontoNeto());

				if ($renglonComprobante->getAlicuotaIva()->getValor() != ConstanteAlicuotaIva::ALICUOTA_0) {

					$cuenta_iva = $renglonComprobante->getAlicuotaIva()->getCuentaContableDebito();

					if ($cuenta_iva) {
						$codigo_cuenta_iva = $cuenta_iva->getCodigoCuentaContable();
					} else {
						$erroresArray[$cuenta_iva->getId()] = 'El concepto de IVA no posee una cuenta contable asociada.';
					}

					$acumulado_iva = isset($asientoArray[$codigo_cuenta_iva]['monto']) ? $asientoArray[$codigo_cuenta_iva]['monto'] : 0;

					// Cuenta asociada al iva

					if($comprobante->getLetraComprobante() != ConstanteLetraComprobante::B) {
						$asientoArray[$codigo_cuenta_iva] = array(
							'imputacion' => $revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
							'monto' => $acumulado_iva + $renglonComprobante->getMontoIva(),
							'detalle' => $detalleRenglon
						);
					} else {
						$acumulado = $acumulado + $renglonComprobante->getMontoIva();
					}

					$total_asiento += $renglonComprobante->getMontoIva();
				}

				$asientoArray[$codigo_cuenta] = array(
					'imputacion' => $revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER,
					'monto' => $acumulado + abs($renglonComprobante->getMontoNeto()),
					'detalle' => $detalleRenglon
				);
			}
		} else {
			if ($codigoCuentaIngreso == null) {
				$erroresArray[$comprobante->getId()] = 'No se encontr&oacute; una cuenta ingreso relacionada al cliente.';
			} else {
				$erroresArray[$comprobante->getId()] = 'No se encontr&oacute; una cuenta cr&eacute;dito.';
			}
		}

        $asientoArray[$codigoCuentaIngreso] = array(
            'imputacion' => $revertirImputacion ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE,
            'monto' => $total_asiento,
            'detalle' => $detalleRenglon
        );

        $renglones_asiento = array();
		
		//var_dump( $asientoArray );exit;

        foreach ($asientoArray as $codigo_cuenta => $datos_movimiento) {

            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoCuentaContable($codigo_cuenta);

            if ($cuenta_contable) {
                $renglones_asiento[] = array(
                    'cuenta' => $cuenta_contable,
                    'imputacion' => $datos_movimiento['imputacion'],
                    'monto' => $datos_movimiento['monto'],
                    'detalle' => $datos_movimiento['detalle']
                );
            } else {
                $erroresArray[$codigo_cuenta] = 'No se encontr&oacute; la cuenta contable de c&oacute;digo: ' . $codigo_cuenta;
            }
        }

        $concepto_asiento = $emContable->getRepository('ADIFContableBundle:ConceptoAsientoContable')
                ->findOneByCodigo(ConstanteConceptoAsientoContable::RENDICION_LIQUIDO_PRODUCTO);
        /* @var $comprobante ComprobanteVenta */

        $letra = ($comprobante->getLetraComprobante() != null ) ? ' ' . $comprobante->getLetraComprobante()->__toString() : '';

        $denominacion = ($esContraAsiento ? 'Anulaci&oacute;n - ' : '')
                . $comprobante->getTipoComprobante()->getNombre()
                . $letra
                . ' N&ordm; ' . $comprobante->getNumeroCompleto();

        $cliente = $comprobante->getCliente();

        $denominacion .= ' - ' . $cliente;

        $datosAsiento = array(
            'denominacion' => $denominacion,
            'concepto' => $concepto_asiento,
            'renglones' => $renglones_asiento,
            'usuario' => $usuario,
            'razonSocial' => $cliente->getRazonSocial(),
            'numeroDocumento' => $cliente->getNroDocumento(),
            'comprobante' => $comprobante
        );


        $asiento = $this->generarAsientoContable($datosAsiento, $offsetNumeroAsiento);

        $numeroAsiento = $asiento->getNumeroAsiento();

        $mensajeErrorAsientoContable = $this->getMensajeError($asiento, $erroresArray);

        $mensajeErrorDevengado = '';
        $mensajeErrorEjecutado = '';

        // Si el asiento contable falló
        if ($mensajeErrorAsientoContable != '') {

            $this->container->get('request')->getSession()->getFlashBag()
                    ->add('error', $mensajeErrorAsientoContable);
        } else {
            // Persisto los asientos presupuestarios
            $mensajeErrorDevengado = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearDevengadoFromComprobanteRendicionLiquidoProducto($comprobante, $asiento, $esContraAsiento);

            $mensajeErrorEjecutado = $this->container->get('adif.contabilidad_presupuestaria_service')
                    ->crearEjecutadoFromComprobanteRendicionLiquidoProducto($comprobante, $asiento, $esContraAsiento);

            // Si el asiento presupuestario falló
            if ($mensajeErrorDevengado != '' && $mensajeErrorEjecutado != '') {

                // Si hubo un error en el asiento presupuestario - Devengado
                if ($mensajeErrorDevengado != '') {

                    $this->container->get('request')->getSession()->getFlashBag()
                            ->add('error', $mensajeErrorDevengado);
                }

                // Si hubo un error en el asiento presupuestario - Ejecutado
                if ($mensajeErrorEjecutado != '') {

                    $this->container->get('request')->getSession()->getFlashBag()
                            ->add('error', $mensajeErrorEjecutado);
                }
            }
        }

        // Si hubo algun error
        if ($mensajeErrorDevengado != '' && $mensajeErrorEjecutado != '' || $mensajeErrorAsientoContable != '') {
            $numeroAsiento = -1;

            $this->container->get('request')->attributes->set('form-error', true);
        }

        return $numeroAsiento;
    }

}
