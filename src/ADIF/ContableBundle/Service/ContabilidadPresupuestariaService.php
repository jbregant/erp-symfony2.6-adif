<?php

namespace ADIF\ContableBundle\Service;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Entity\BienEconomico;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteCodigoInternoBienEconomico;
use ADIF\ComprasBundle\Entity\OrdenCompra;
use ADIF\ComprasBundle\Entity\Proveedor;
use ADIF\ComprasBundle\Entity\RenglonOrdenCompra;
use ADIF\ComprasBundle\Entity\Requerimiento;
use ADIF\ContableBundle\Entity\AsientoContable;
use ADIF\ContableBundle\Entity\CentroCosto;
use ADIF\ContableBundle\Entity\Cobranza\CobroRenglonCobranza;
use ADIF\ContableBundle\Entity\Cobranza\CobroRetencionCliente;
use ADIF\ContableBundle\Entity\Cobranza\RenglonCobranza;
use ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque;
use ADIF\ContableBundle\Entity\ComprobanteCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteAlicuotaIva;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoInternoCuentaContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoInternoCuentaPresupuestariaEconomica;
use ADIF\ContableBundle\Entity\Constantes\ConstanteNaturalezaCuenta;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteVenta;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoDeclaracionJurada;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoOperacionContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria;
use ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria;
use ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria;
use ADIF\ContableBundle\Entity\CuentaContable;
use ADIF\ContableBundle\Entity\CuentaPresupuestaria;
use ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica;
use ADIF\ContableBundle\Entity\DeclaracionJuradaIvaContribuyente;
use ADIF\ContableBundle\Entity\DefinitivoCompra;
use ADIF\ContableBundle\Entity\DefinitivoConsultoria;
use ADIF\ContableBundle\Entity\DefinitivoContratoVenta;
use ADIF\ContableBundle\Entity\DefinitivoObra;
use ADIF\ContableBundle\Entity\DevengadoCargas;
use ADIF\ContableBundle\Entity\DevengadoCompra;
use ADIF\ContableBundle\Entity\DevengadoConsultoria;
use ADIF\ContableBundle\Entity\DevengadoOrdenPagoGeneral;
use ADIF\ContableBundle\Entity\DevengadoSueldo;
use ADIF\ContableBundle\Entity\DevengadoVenta;
use ADIF\ContableBundle\Entity\EgresoValor\EjecutadoEgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoReconocimientoEgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\ReconocimientoEgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\RenglonComprobanteEgresoValor;
use ADIF\ContableBundle\Entity\Ejecutado;
use ADIF\ContableBundle\Entity\EjecutadoAnticipoProveedor;
use ADIF\ContableBundle\Entity\EjecutadoAnticipoSueldo;
use ADIF\ContableBundle\Entity\EjecutadoCargas;
use ADIF\ContableBundle\Entity\EjecutadoCompra;
use ADIF\ContableBundle\Entity\EjecutadoConsultoria;
use ADIF\ContableBundle\Entity\EjecutadoSueldo;
use ADIF\ContableBundle\Entity\EjercicioContable;
use ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta;
use ADIF\ContableBundle\Entity\Facturacion\ContratoVenta;
use ADIF\ContableBundle\Entity\Facturacion\OrdenPagoDevolucionGarantia;
use ADIF\ContableBundle\Entity\Facturacion\RenglonComprobanteVenta;
use ADIF\ContableBundle\Entity\MovimientoBancario;
use ADIF\ContableBundle\Entity\MovimientoMinisterial;
use ADIF\ContableBundle\Entity\Obras\ComprobanteObra;
use ADIF\ContableBundle\Entity\Obras\DevengadoObra;
use ADIF\ContableBundle\Entity\Obras\DocumentoFinanciero;
use ADIF\ContableBundle\Entity\Obras\EjecutadoObra;
use ADIF\ContableBundle\Entity\Obras\OrdenPagoObra;
use ADIF\ContableBundle\Entity\Obras\Tramo;
use ADIF\ContableBundle\Entity\OrdenPagoAnticipoSueldo;
use ADIF\ContableBundle\Entity\OrdenPagoCargasSociales;
use ADIF\ContableBundle\Entity\OrdenPagoComprobante;
use ADIF\ContableBundle\Entity\OrdenPagoDeclaracionJurada;
use ADIF\ContableBundle\Entity\OrdenPagoDeclaracionJuradaIvaContribuyente;
use ADIF\ContableBundle\Entity\OrdenPagoGeneral;
use ADIF\ContableBundle\Entity\OrdenPagoPagoACuenta;
use ADIF\ContableBundle\Entity\OrdenPagoDevolucionRenglonDeclaracionJurada;
use ADIF\ContableBundle\Entity\OrdenPagoPagoParcial;
use ADIF\ContableBundle\Entity\OrdenPagoRenglonRetencionLiquidacion;
use ADIF\ContableBundle\Entity\OrdenPagoSueldo;
use ADIF\ContableBundle\Entity\ProvisorioCompra;
use ADIF\ContableBundle\Entity\RenglonComprobanteCompra;
use ADIF\RecursosHumanosBundle\Entity\ConfiguracionCuentaContableSueldos;
use ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor;
use ADIF\RecursosHumanosBundle\Entity\Liquidacion;
use ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado;
use ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleadoConcepto;
use ADIF\RecursosHumanosBundle\Entity\TipoConcepto;
use ADIF\RecursosHumanosBundle\Entity\TipoLiquidacion;
use Exception;
use SoapClient;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteObra;
use ADIF\ContableBundle\Entity\OrdenPago;
use ADIF\ContableBundle\Entity\Facturacion\ComprobanteRendicionLiquidoProducto;

/**
 * Description of ContabilidadPresupuestariaService
 *
 * @author Manuel Becerra
 * created 14/10/2014
 */
class ContabilidadPresupuestariaService {

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
     * @param Container $container
     */
    public function __construct(Container $container) {

        $this->container = $container;

        $this->doctrine = $container->get("doctrine");
    }

    /**
     * 
     * Crea un provisorio por cada renglon del Requerimiento recibido como parámetro, 
     * retornando un booleando que indica si hubo algún error o no.
     * 
     * @param Requerimiento $requerimiento
     * @return string
     */
    public function crearProvisorioFromRequerimiento(Requerimiento $requerimiento) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();

        $erroresPresupuestoArray = array();

        $erroresCuentaPresupuestariaEconomicaArray = array();

        $erroresCuentaPresupuestariaEconomicaSinSaldoArray = array();

        foreach ($requerimiento->getRenglonesRequerimiento() as $renglonRequerimiento) {

            $provisorio = new ProvisorioCompra();

            $provisorio->setRenglonRequerimiento($renglonRequerimiento);

            /* @var $cuentaContable \ADIF\ContableBundle\Entity\CuentaContable */
            $cuentaContable = $renglonRequerimiento->getRenglonSolicitudCompra()
                            ->getBienEconomico()->getCuentaContable();

            if ($cuentaContable) {

                $provisorio->setCuentaContable($cuentaContable);

                $provisorio->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                $provisorio->setCuentaPresupuestariaEconomica(
                        $cuentaContable->getCuentaPresupuestariaEconomica()
                );

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $provisorio->getFechaProvisorio(), //
                        $cuentaContable->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $provisorio->setCuentaPresupuestaria($cuentaPresupuestaria);

					/** 
					* Dejo comentado la validacion $cuentaPresupuestaria->getTieneSaldo(), ya que el metodo es muy lento, ya que por cada
					* renglon del requerimiento (estamos en un foreach) tiene que ir a buscar todos sus provisorios, definitivos, 
					* devengados y ejectutados y va acumulando la sumatoria de los saldos de cada uno y el costo de performance es muy alto, 
					* ya que se come toda la memoria.
					* Resultado de aprobacion de requerimiento con el metodo y sin el metodo (ambiente de desarrollo local - vagrant)
					* Con la validacion: Total Execution Time: 2.0670193354289 Mins
					* Sin la validacion: Total Execution Time: 0.03583041826884 Mins
					* @TODO: buscar la manera de con un stored procedure calcular el saldo o directamente tener en un campo el dato ya calculado.
					* @gluis - 24/10/2016
					*/
					
//					// Si la CuentaPresupuestaria NO presenta saldo
//					if (!$cuentaPresupuestaria->getTieneSaldo()) {
//
//                       $erroresCuentaPresupuestariaEconomicaSinSaldoArray[$cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getId()] = 'La cuenta econ&oacute;mica '
//                                . $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()
//                                . ' no presenta saldo para el ejercicio '
//                                . $cuentaPresupuestaria->getEjercicioContable()
//                                . '.';
//                    }
                } else {
                    if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                    }
                }
            } else {
                $erroresCuentaContableArray[$renglonRequerimiento->getRenglonSolicitudCompra()
                                ->getBienEconomico()->getId()] = $renglonRequerimiento->getRenglonSolicitudCompra()
                        ->getBienEconomico();
            }

            $provisorio->setMonto($renglonRequerimiento->getJustiprecioTotal());

            $provisorio->setDetalle('Requerimiento nº ' . $requerimiento->getNumero());

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {

                $this->guardarAsientoPresupuestario($provisorio);
            }
        }


        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray) || !empty($erroresCuentaContableArray)) {

            $errorMsg .= '<span>El requerimiento no se pudo aprobar contablemente:</span>';
        } else {

            // Si existen CuentasPresupuestariasEconomicas SIN saldo
            if (!empty($erroresCuentaPresupuestariaEconomicaSinSaldoArray)) {

                $this->mostrarMensajeErrorCuentaPresupuestariaEconomicaSinSaldo($erroresCuentaPresupuestariaEconomicaSinSaldoArray);
            }
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenCompra $ordenCompra
     * @return type
     */
    public function crearDefinitivoFromOrdenCompra(OrdenCompra $ordenCompra) 
    {
        $fechaImplementacionCeCo = \DateTime::createFromFormat('Y-m-d H:i:s', '2017-09-01 00:00:00');
		$implementacionComprasCeCo = false;
        $centro_costo = null;
        $id_area = 0;
        
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emAutenticacion = $this->doctrine->getManager(EntityManagers::getEmAutenticacion());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());
        
        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();
        $erroresCuentaPresupuestariaEconomicaSinSaldoArray = array();

        foreach ($ordenCompra->getRenglones() as $renglonOrdenCompra) {
            /* @var $renglonOrdenCompra RenglonOrdenCompra */

            $definitivo = new DefinitivoCompra();

            $definitivo->setRenglonOrdenCompra($renglonOrdenCompra);

            // RenglonRequerimiento
            $renglonRequerimiento = $renglonOrdenCompra->getRenglonCotizacion()->getRenglonRequerimiento();

            // Seteo el Provisorio si es que existe
            $provisorio = $emContable->getRepository('ADIFContableBundle:ProvisorioCompra')->findOneByIdRenglonRequerimiento($renglonRequerimiento->getId());

            $definitivo->setProvisorio($provisorio);

            // CuentaContable
            $cuentaContable = $renglonRequerimiento->getRenglonSolicitudCompra()->getBienEconomico()->getCuentaContable();

            if ($cuentaContable) {
                $naturaleza_cuenta = $cuentaContable->getSegmentoOrden(1);

                if ($naturaleza_cuenta == ConstanteNaturalezaCuenta::GASTO) {

                    // Si la naturaleza es un gasto, busco el centro de costos del bien
                    if ($renglonOrdenCompra->getRenglonPedidoInterno()) {

                        $pedidoInterno = $renglonOrdenCompra
                                            ->getRenglonPedidoInterno()
                                            ->getPedidoInterno();
                        
                        $fechaCreacionPI = $pedidoInterno->getFechaCreacion();
                        if ($fechaCreacionPI >= $fechaImplementacionCeCo) {
                            $implementacionComprasCeCo = true;
							$centro_costo = $pedidoInterno->getCentroCosto();
                        } else {
                            $implementacionComprasCeCo = false;
                            $id_area = $pedidoInterno->getIdArea();
                        }
                        
                    } else {

                        $id_usuario = $renglonOrdenCompra->getRenglonCotizacion()
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
                    
                    $codigo_cuenta = $this->getCuentaContableConCentroCosto($cuentaContable->getCodigoCuentaContable(), $centro_costo);
                    
                    $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable($codigo_cuenta);
                }

                if ($cuentaContable) {
                    $definitivo->setCuentaContable($cuentaContable);

                    $definitivo->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    $definitivo->setCuentaPresupuestariaEconomica(
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $definitivo->getFechaDefinitivo(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $definitivo->setCuentaPresupuestaria($cuentaPresupuestaria);

//                        // Si la CuentaPresupuestaria NO presenta saldo
//                        if (!$cuentaPresupuestaria->getTieneSaldo()) {
//
//                            $erroresCuentaPresupuestariaEconomicaSinSaldoArray[$cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getId()] = 'La cuenta econ&oacute;mica '
//                                    . $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()
//                                    . ' no presenta saldo para el ejercicio '
//                                    . $cuentaPresupuestaria->getEjercicioContable()
//                                    . '.';
//                        }
                    } else {
                        if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                            $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                        }
                    }
                } else {
                    $erroresCuentaContableArray[$renglonRequerimiento->getRenglonSolicitudCompra()
                                    ->getBienEconomico()->getId()] = $renglonRequerimiento
                                    ->getRenglonSolicitudCompra()->getBienEconomico();
                }
            } else {
                $erroresCuentaContableArray[$renglonRequerimiento->getRenglonSolicitudCompra()
                                ->getBienEconomico()->getId()] = $renglonRequerimiento
                                ->getRenglonSolicitudCompra()->getBienEconomico();
            }

            $definitivo->setMonto($renglonOrdenCompra->getPrecioTotalProrrateado());

            // Genero el detalle del definitivo
            $detalle = 'Orden de compra nº ' . $ordenCompra->getNumero()
                    . ' — ' . $ordenCompra->getProveedor()->getRazonSocial()
                    . ' - ' . $ordenCompra->getProveedor()->getCUIT();

            $definitivo->setDetalle($detalle);

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($definitivo);
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray) || !empty($erroresCuentaContableArray)) {

            $errorMsg .= '<span>El requerimiento no se pudo aprobar contablemente:</span>';
        } else {

            // Si existen CuentasPresupuestariasEconomicas SIN saldo
            if (!empty($erroresCuentaPresupuestariaEconomicaSinSaldoArray)) {

                $this->mostrarMensajeErrorCuentaPresupuestariaEconomicaSinSaldo($erroresCuentaPresupuestariaEconomicaSinSaldoArray);
            }
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param RenglonOrdenCompra $renglonOrdenCompra
     * @param BienEconomico $bienEconomico
     * @param CentroCosto $centroCosto
     * @return type
     */
    public function crearDefinitivoFromRenglonOrdenCompra(RenglonOrdenCompra $renglonOrdenCompra, BienEconomico $bienEconomico, CentroCosto $centroCosto) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();
        $erroresCuentaPresupuestariaEconomicaSinSaldoArray = array();

        $ordenCompra = $renglonOrdenCompra->getOrdenCompra();

        $definitivo = new DefinitivoCompra();

        $definitivo->setRenglonOrdenCompra($renglonOrdenCompra);


        // CuentaContable
        $cuentaContable = $bienEconomico->getCuentaContable();

        if ($cuentaContable) {

            $naturalezaCuenta = $cuentaContable->getSegmentoOrden(1);

            if ($naturalezaCuenta == ConstanteNaturalezaCuenta::GASTO) {

                $codigoCuenta = $this->getCuentaContableConCentroCosto($cuentaContable->getCodigoCuentaContable(), $centroCosto);

                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                        ->findOneByCodigoCuentaContable($codigoCuenta);
            }

            if ($cuentaContable) {

                $definitivo->setCuentaContable($cuentaContable);

                $definitivo->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                $definitivo->setCuentaPresupuestariaEconomica(
                        $cuentaContable->getCuentaPresupuestariaEconomica()
                );

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $definitivo->getFechaDefinitivo(), //
                        $cuentaContable->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $definitivo->setCuentaPresupuestaria($cuentaPresupuestaria);

                    // Si la CuentaPresupuestaria NO presenta saldo
                    if (!$cuentaPresupuestaria->getTieneSaldo()) {

                        $erroresCuentaPresupuestariaEconomicaSinSaldoArray[$cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getId()] = 'La cuenta econ&oacute;mica '
                                . $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()
                                . ' no presenta saldo para el ejercicio '
                                . $cuentaPresupuestaria->getEjercicioContable()
                                . '.';
                    }
                } else {
                    if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                    }
                }
            } else {
                $erroresCuentaContableArray[$bienEconomico->getId()] = $bienEconomico;
            }
        } else {
            $erroresCuentaContableArray[$bienEconomico->getId()] = $bienEconomico;
        }

        $definitivo->setMonto($renglonOrdenCompra->getMontoNeto());

        // Genero el detalle del definitivo
        $detalle = 'Orden de compra nº ' . $ordenCompra->getNumeroOrdenCompra()
                . ' — ' . $ordenCompra->getProveedor()->getRazonSocial()
                . ' - ' . $ordenCompra->getProveedor()->getCUIT();

        $definitivo->setDetalle($detalle);

        if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
            $this->guardarAsientoPresupuestario($definitivo);
        }


        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray) || !empty($erroresCuentaContableArray)) {

            $errorMsg .= '<span>El requerimiento no se pudo aprobar contablemente:</span>';
        } else {

            // Si existen CuentasPresupuestariasEconomicas SIN saldo
            if (!empty($erroresCuentaPresupuestariaEconomicaSinSaldoArray)) {

                $this->mostrarMensajeErrorCuentaPresupuestariaEconomicaSinSaldo($erroresCuentaPresupuestariaEconomicaSinSaldoArray);
            }
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param ContratoConsultoria $contratoConsultoria
     * @return type
     */
    public function crearDefinitivoFromContratoConsultoria(ContratoConsultoria $contratoConsultoria) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresCuentaContableArray = array();

        $erroresPresupuestoArray = array();

        $erroresCuentaPresupuestariaEconomicaArray = array();

        $erroresCuentaPresupuestariaEconomicaSinSaldoArray = array();


        // Por cada ejercicio de los ciclos de facturacion del contrato
        foreach ($contratoConsultoria->getEjerciciosCiclosFacturacion() as $ejercicioCicloFacturacion) {


            $definitivo = new DefinitivoConsultoria();

            $definitivo->setContrato($contratoConsultoria);

            // Obtengo la cuenta economica correspondiente
            $cuentaPresupuestariaEconomica = $emContable
                    ->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')
                    ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaPresupuestariaEconomica::DEFINITIVO_LOCACION_SERVICIOS);

            $definitivo->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);


            // Si el ejercicio del ciclo es distinto al actual
            if ($definitivo->getFechaDefinitivo()->format('Y') != $ejercicioCicloFacturacion) {

                $definitivo->setFechaDefinitivo(new \DateTime($ejercicioCicloFacturacion . '-01-01'));
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $definitivo->getFechaDefinitivo(), //
                    $cuentaPresupuestariaEconomica
            );

            if ($cuentaPresupuestaria != null) {

                $definitivo->setCuentaPresupuestaria($cuentaPresupuestaria);

                // Si la CuentaPresupuestaria NO presenta saldo
                if (!$cuentaPresupuestaria->getTieneSaldo()) {

                    $erroresCuentaPresupuestariaEconomicaSinSaldoArray[$cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getId()] = 'La cuenta econ&oacute;mica '
                            . $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()
                            . ' no presenta saldo para el ejercicio '
                            . $cuentaPresupuestaria->getEjercicioContable()
                            . '.';
                }
            }
//            else {
//                if ($cuentaPresupuestariaEconomica != null) {
//                    $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
//                }
//            }

            $cosultor = $emRRHH
                    ->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')
                    ->find($contratoConsultoria->getIdConsultor());

            // Genero el detalle del definitivo
            $detalle = 'Contrato de ' . $contratoConsultoria->getClaseContrato()
                    . ' nº ' . $contratoConsultoria->getNumeroContrato()
                    . ' — '
                    . $cosultor->getRazonSocial() . ' - ' . $cosultor->getCUIT();


            // Calculo el monto del definitivo segun el ejercicio
            $monto = $contratoConsultoria->getImporteCiclosFacturacionByEjercicio($ejercicioCicloFacturacion);

            $contratoOrigen = $contratoConsultoria->getContratoOrigen();

            if ($contratoOrigen != null) {

                $montoDefinitivoOrigen = $contratoOrigen->getImporteCiclosFacturacionByEjercicio($ejercicioCicloFacturacion) - $contratoOrigen->getSaldoPendienteFacturacion();

                $monto -= $montoDefinitivoOrigen;

                // Actualizo el saldo del definitivo anterior
                $definitivoOrigen = $emContable->getRepository('ADIFContableBundle:DefinitivoConsultoria')
                        ->findOneByContrato($contratoOrigen);

                if ($definitivoOrigen) {

                    $definitivoOrigen->setMonto($montoDefinitivoOrigen);
                }
            }

            $definitivo->setMonto($monto);
            $definitivo->setDetalle($detalle);

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($definitivo);
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray) || !empty($erroresCuentaContableArray)) {

            $errorMsg .= '<span>Hubo un error al generar el asiento presupuestario:</span>';
        } else {

            // Si existen CuentasPresupuestariasEconomicas SIN saldo
            if (!empty($erroresCuentaPresupuestariaEconomicaSinSaldoArray)) {

                $this->mostrarMensajeErrorCuentaPresupuestariaEconomicaSinSaldo($erroresCuentaPresupuestariaEconomicaSinSaldoArray);
            }
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param ContratoConsultoria $contratoConsultoria
     */
    public function actualizarDefinitivoFromContratoConsultoria(ContratoConsultoria $contratoConsultoria) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        // Obtengo los definitivos asociado al contrato 
        $definitivos = $emContable->getRepository('ADIFContableBundle:DefinitivoConsultoria')
                ->findByContrato($contratoConsultoria);

        foreach ($definitivos as $definitivo) {

            // Actualizo el monto del difinitivo al nuevo importe del contrato
            $definitivo->setMonto(
                    $contratoConsultoria->getImporteCiclosFacturacionByEjercicio(
                            $definitivo->getFechaDefinitivo()->format('Y')
                    )
            );
        }
    }

    /**
     * 
     * @param Requerimiento $requerimiento
     */
    public function eliminarProvisorioFromRequerimiento(Requerimiento $requerimiento) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        foreach ($requerimiento->getRenglonesRequerimiento() as $renglonRequerimiento) {

            // Get el Provisorio asociado
            $provisorio = $emContable->getRepository('ADIFContableBundle:ProvisorioCompra')
                    ->findOneByIdRenglonRequerimiento($renglonRequerimiento->getId());

            if (null != $provisorio) {
                $emContable->remove($provisorio);
            }
        }
    }

    /**
     * 
     * @param OrdenCompra $ordenCompra
     */
    public function eliminarDefinitivoFromOrdenCompra(OrdenCompra $ordenCompra) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        foreach ($ordenCompra->getRenglones() as $renglonOrdenCompra) {

            // Get el Definitivo asociado
            $definitivo = $emContable->getRepository('ADIFContableBundle:DefinitivoCompra')
                    ->findOneByIdRenglonOrdenCompra($renglonOrdenCompra->getId());

            if (null != $definitivo) {
                $emContable->remove($definitivo);
            }
        }
    }

    /**
     * 
     * @param Tramo $tramo
     * @return type
     */
    public function crearDefinitivoFromTramo(Tramo $tramo) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());

        $erroresCuentaContableArray = array();

        $erroresPresupuestoArray = array();

        $erroresCuentaPresupuestariaEconomicaArray = array();

        $erroresCuentaPresupuestariaEconomicaSinSaldoArray = array();


        // Por cada ejercicio involucrado en el plazo del tramo
        foreach ($tramo->getEjercicios() as $ejercicioPlazoTramo) {

            $definitivo = new DefinitivoObra();

            $definitivo->setTramo($tramo);

            // Obtengo la cuenta economica correspondiente
            $cuentaPresupuestariaEconomica = $emContable
                    ->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')
                    ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaPresupuestariaEconomica::DEFINITIVO_OBRAS);

            $definitivo->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);

            // Si el ejercicio analizado es distinto al actual
            if ($definitivo->getFechaDefinitivo()->format('Y') != $ejercicioPlazoTramo) {

                $definitivo->setFechaDefinitivo(new \DateTime($ejercicioPlazoTramo . '-01-01'));
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $definitivo->getFechaDefinitivo(), //
                    $cuentaPresupuestariaEconomica
            );

            if ($cuentaPresupuestaria != null) {

                $definitivo->setCuentaPresupuestaria($cuentaPresupuestaria);

                // Si la CuentaPresupuestaria NO presenta saldo
                if (!$cuentaPresupuestaria->getTieneSaldo()) {

                    $erroresCuentaPresupuestariaEconomicaSinSaldoArray[$cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getId()] = 'La cuenta econ&oacute;mica '
                            . $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()
                            . ' no presenta saldo para el ejercicio '
                            . $cuentaPresupuestaria->getEjercicioContable()
                            . '.';
                }
            }
//            else {
//                if ($cuentaPresupuestariaEconomica != null) {
//                    $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
//                }
//            }

            /* @var $proveedor Proveedor */
            $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')
                    ->find($tramo->getIdProveedor());

            // Genero el detalle del definitivo
            $detalle = 'Licitación ' . $tramo->__toString() . ' — '
                    . $proveedor->getRazonSocial() . ' - ' . $proveedor->getCUIT();

            $definitivo->setMonto($tramo->getImporteByEjercicio($ejercicioPlazoTramo));

            $definitivo->setDetalle($detalle);

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($definitivo);
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray) || !empty($erroresCuentaContableArray)) {

            $errorMsg .= '<span>Hubo un error al generar el asiento presupuestario:</span>';
        } else {

            // Si existen CuentasPresupuestariasEconomicas SIN saldo
            if (!empty($erroresCuentaPresupuestariaEconomicaSinSaldoArray)) {

                $this->mostrarMensajeErrorCuentaPresupuestariaEconomicaSinSaldo($erroresCuentaPresupuestariaEconomicaSinSaldoArray);
            }
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param Tramo $tramo
     */
    public function actualizarDefinitivoFromTramo(Tramo $tramo) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        // Obtengo los definitivos asociados al tramo 
        $definitivos = $emContable->getRepository('ADIFContableBundle:DefinitivoObra')
                ->findByTramo($tramo);

        foreach ($definitivos as $definitivo) {

            // Actualizo el monto del difinitivo al nuevo importe del tramo segun el plazo
            $definitivo->setMonto(
                    $tramo->getImporteByEjercicio(
                            $definitivo->getFechaDefinitivo()->format('Y')
                    )
            );
        }
    }

    /**
     * 
     * @param DocumentoFinanciero $documentoFinanciero
     * @param type $suma
     */
    public function actualizarDefinitivoFromDocumentoFinanciero(DocumentoFinanciero $documentoFinanciero, $suma = true) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        // Obtengo los definitivos asociados al tramo 
        $definitivos = $emContable->getRepository('ADIFContableBundle:DefinitivoObra')
                ->findByTramo($documentoFinanciero->getTramo());

        foreach ($definitivos as $definitivo) {

            if ($definitivo->getFechaDefinitivo()->format('Y') == $documentoFinanciero->getFechaDocumentoFinancieroFin()->format('Y')) {

                $montoTotalDocumentoFinanciero = $documentoFinanciero->getMontoTotalDocumentoFinanciero() * ($suma ? 1 : -1);

                // Actualizo el monto del difinitivo, con el monto del DocumentoFinanciero
                $definitivo->setMonto($definitivo->getMonto() + $montoTotalDocumentoFinanciero);
            }
        }
    }

    /**
     * 
     * @param Tramo $tramo
     */
    public function eliminarDefinitivoFromTramo(Tramo $tramo) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        // Get el Definitivo asociado
        $definitivo = $emContable->getRepository('ADIFContableBundle:DefinitivoObra')
                ->findOneByTramo($tramo);

        if (null != $definitivo) {
            $emContable->remove($definitivo);
        }
    }

    /**
     * 
     * @param ContratoVenta $contrato
     * @param \ADIF\ContableBundle\Service\type $esNegativo
     * @return \ADIF\ContableBundle\Service\type
     * @param ContratoVenta $contrato
     * @param type $esNegativo
     * @return type
     */
    public function crearDefinitivoFromContratoVenta(ContratoVenta $contrato, $esNegativo = false) {

        $erroresCuentaContableArray = array();

        $erroresPresupuestoArray = array();

        $erroresCuentaPresupuestariaEconomicaArray = array();

        $erroresCuentaPresupuestariaEconomicaSinSaldoArray = array();


        // Por cada ejercicio de los ciclos de facturacion del contrato
        foreach ($contrato->getEjerciciosCiclosFacturacion() as $ejercicioCicloFacturacion) {

            $definitivo = new DefinitivoContratoVenta();

            $definitivo->setContrato($contrato);

            $cuentaContable = $contrato->getClaseContrato()->getCuentaContable();

            $cuentaPresupuestariaEconomica = $cuentaContable->getCuentaPresupuestariaEconomica();

            $definitivo->setCuentaContable($cuentaContable);

            $definitivo->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);

            // Si el ejercicio del ciclo es distinto al actual
            if ($definitivo->getFechaDefinitivo()->format('Y') != $ejercicioCicloFacturacion) {

                $definitivo->setFechaDefinitivo(new \DateTime($ejercicioCicloFacturacion . '-01-01'));
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $definitivo->getFechaDefinitivo(), //
                    $cuentaPresupuestariaEconomica
            );

            if ($cuentaPresupuestaria != null) {

                $definitivo->setCuentaPresupuestaria($cuentaPresupuestaria);

                // Si la CuentaPresupuestaria NO presenta saldo
                if (!$cuentaPresupuestaria->getTieneSaldo()) {

                    $erroresCuentaPresupuestariaEconomicaSinSaldoArray[$cuentaPresupuestaria->getCuentaPresupuestariaEconomica()->getId()] = 'La cuenta econ&oacute;mica '
                            . $cuentaPresupuestaria->getCuentaPresupuestariaEconomica()
                            . ' no presenta saldo para el ejercicio '
                            . $cuentaPresupuestaria->getEjercicioContable()
                            . '.';
                }
            }
//            else {
//                if ($cuentaPresupuestariaEconomica != null) {
//                    $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
//                }
//            }
            // Genero el detalle del definitivo
            $detalle = 'Contrato ' . $contrato->getClaseContrato() . ' n&deg; ' . $contrato->getNumeroContrato();

            // Seteo el monto total segun el ejercicio
            $monto = $contrato->getImporteCiclosFacturacionByEjercicio($ejercicioCicloFacturacion);

            $definitivo->setMonto($monto * ($esNegativo ? -1 : 1));

            $definitivo->setDetalle($detalle);

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($definitivo);
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray) || !empty($erroresCuentaContableArray)) {

            $errorMsg .= '<span>Hubo un error al generar el asiento presupuestario:</span>';
        } else {

            // Si existen CuentasPresupuestariasEconomicas SIN saldo
            if (!empty($erroresCuentaPresupuestariaEconomicaSinSaldoArray)) {

                $this->mostrarMensajeErrorCuentaPresupuestariaEconomicaSinSaldo($erroresCuentaPresupuestariaEconomicaSinSaldoArray);
            }
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param ContratoVenta $contrato
     */
    public function actualizarDefinitivoFromContratoVenta(ContratoVenta $contrato) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        // Obtengo los definitivos asociado al contrato 
        $definitivos = $emContable->getRepository('ADIFContableBundle:DefinitivoContratoVenta')
                ->findByContrato($contrato);

        foreach ($definitivos as $definitivo) {

            // Actualizo el monto del difinitivo al nuevo importe del contrato
            $definitivo->setMonto(
                    $contrato->getImporteCiclosFacturacionByEjercicio(
                            $definitivo->getFechaDefinitivo()->format('Y')
                    )
            );
        }
    }

    /**
     * 
     * @param ContratoVenta $contrato
     */
    public function saldarDefinitivoFromContratoVenta(ContratoVenta $contrato) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        // Obtengo el definitivo asociado al contrato 
        $definitivo = $emContable->getRepository('ADIFContableBundle:DefinitivoContratoVenta')
                ->findOneByContrato($contrato);

        if (null != $definitivo) {

            // Actualizo el monto del difinitivo a cero
            $definitivo->setMonto(0);
        }
    }

    /**
     * 
     * @param ComprobanteCompra $comprobanteCompra
     * @param type $esContraAsiento
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearAsientoFromComprobanteCompra(ComprobanteCompra $comprobanteCompra, $esContraAsiento = false, AsientoContable $asiento = null) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emAutenticacion = $this->doctrine->getManager(EntityManagers::getEmAutenticacion());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresCuentaContableArray = array();

        $erroresPresupuestoArray = array();

        $erroresCuentaPresupuestariaEconomicaArray = array();

        /** GENERO LOS DEVENGADOS CORRESPONDIENTES * */
        foreach ($comprobanteCompra->getRenglonesComprobante() as $renglonComprobanteCompra) {

            $devengado = new DevengadoCompra();

            $devengado->setAsientoContable($asiento);

            $renglonOrdenCompra = $emCompras->getRepository('ADIFComprasBundle:RenglonOrdenCompra')
                    ->find($renglonComprobanteCompra->getIdRenglonOrdenCompra());

            if ($renglonComprobanteCompra->getIdBienEconomico() != null) {
                $bienEconomico = $emCompras->getRepository('ADIFComprasBundle:BienEconomico')
                        ->find($renglonComprobanteCompra->getIdBienEconomico());

                // CuentaContable
//                $cuentaContable = $bienEconomico->getCuentaContable();
                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($bienEconomico->getIdCuentaContable());
            } else {
                // RenglonRequerimiento 
                $renglonRequerimiento = $renglonOrdenCompra->getRenglonCotizacion()
                        ->getRenglonRequerimiento();
                // CuentaContable
//                $cuentaContable = $renglonRequerimiento->getRenglonSolicitudCompra()->getBienEconomico()->getCuentaContable();
                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($renglonRequerimiento->getRenglonSolicitudCompra()->getBienEconomico()->getIdCuentaContable());
            }

            // Seteo el Definitivo si es que existe
            $definitivo = $emContable->getRepository('ADIFContableBundle:DefinitivoCompra')
                    ->findOneByIdRenglonOrdenCompra($renglonOrdenCompra->getId());

            $devengado->setDefinitivo($definitivo);

            if ($cuentaContable) {

                // Si no es de servicio
                if ($renglonOrdenCompra->getRenglonCotizacion() != null) {

                    $naturaleza_cuenta = $cuentaContable->getSegmentoOrden(1);

                    if ($naturaleza_cuenta == ConstanteNaturalezaCuenta::GASTO) {

                        if ($renglonOrdenCompra->getIdCentroCosto() != null) {

                            $centro_costo = $renglonOrdenCompra->getCentroCosto();
                        } //.
                        else {
                            if ($renglonOrdenCompra->getRenglonPedidoInterno()) {

                                $id_area = $renglonOrdenCompra->getRenglonPedidoInterno()
                                                ->getPedidoInterno()->getIdArea();
                            } else {

                                $id_usuario = $renglonOrdenCompra->getRenglonCotizacion()
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
                                ->getCuentaContableConCentroCosto($cuentaContable->getCodigoCuentaContable(), $centro_costo);

                        $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                                ->findOneByCodigoCuentaContable($codigo_cuenta);
                    }
                } else {

                    if ($renglonOrdenCompra->getIdCentroCosto() != null) {

                        $centro_costo = $renglonOrdenCompra->getCentroCosto();
                    } //.
                    else {

                        $renglonCentroDeCosto = $renglonComprobanteCompra
                                        ->getRenglonComprobanteCompraCentrosDeCosto()->first();

                        $centro_costo = $renglonCentroDeCosto->getCentroDeCosto();
                    }

                    $codigo_cuenta = $cuentaContable->getCodigoCuentaContable();

                    // Indico el centro de costos
                    if ($centro_costo != null) {

                        $naturalezaCuentaContable = $cuentaContable->getSegmentoOrden(1);

                        // Si la naturaleza es un gasto
                        if ($naturalezaCuentaContable == ConstanteNaturalezaCuenta::GASTO) {
                            $codigo_cuenta = $this
                                    ->getCuentaContableConCentroCosto($codigo_cuenta, $centro_costo);
                        }
                    }

                    $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                            ->findOneByCodigoCuentaContable($codigo_cuenta);
                }

                $devengado->setCuentaContable($cuentaContable);

                $devengado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                $devengado->setCuentaPresupuestariaEconomica($cuentaContable->getCuentaPresupuestariaEconomica());

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $devengado->getFechaDevengado(), //
                        $cuentaContable->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $devengado->setCuentaPresupuestaria($cuentaPresupuestaria);
                } else {
                    if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                    }
                }
            } else {
                $erroresCuentaContableArray[$bienEconomico->getId()] = $bienEconomico;
            }

            $devengado->setRenglonComprobanteCompra($renglonComprobanteCompra);
            $devengado->setMonto($renglonComprobanteCompra->getPrecioNetoTotalProrrateado(true) * ($esContraAsiento ? -1 : 1));

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($devengado);
            }

            // Ejecutado IVA del renglon
            if ($renglonComprobanteCompra->getAlicuotaIva()->getValor() != ConstanteAlicuotaIva::ALICUOTA_0) {
                // CuentaContable                
                $cuentaContable = $renglonComprobanteCompra->getAlicuotaIva()->getCuentaContableCredito();

                if ($cuentaContable) {
                    $montoRenglonProrrateado = $renglonComprobanteCompra->getPrecioNetoTotalProrrateado(true);
                    $monto = $montoRenglonProrrateado * $renglonComprobanteCompra->getAlicuotaIva()->getValor() / 100;

                    $ejecutado = new Ejecutado();
                    $ejecutado->setAsientoContable($asiento);
                    $ejecutado->setDefinitivo($definitivo);
                    $ejecutado->setCuentaContable($cuentaContable);

                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    if ($cuentaContable->getCuentaPresupuestariaEconomica()) {
                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                (!$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE), //
                                $cuentaContable
                        );

                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                    }

                    if ($cuentaContable->getCuentaPresupuestariaEconomica()) {
                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                ConstanteTipoOperacionContable::DEBE, //
                                $cuentaContable
                        );

                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                    }

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $ejecutado->getFechaEjecutado(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                    } else {
                        if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                            $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                        }
                    }

                    $ejecutado->setMonto($monto * ($esContraAsiento ? -1 : 1));
                } else {
                    $erroresCuentaContableArray[$renglonComprobanteCompra->getAlicuotaIva()->getId()] = $renglonComprobanteCompra->getAlicuotaIva();
                }

                if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                    $this->guardarAsientoPresupuestario($ejecutado);
                }
            }
        }

        /*         * **** Ejecutado - Proveedor **** *         */

        /* @var $proveedor Proveedor */
        $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')
                ->find($comprobanteCompra->getIdProveedor());

//        $cuentaContable = $proveedor->getCuentaContable();
        $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($proveedor->getIdCuentaContable());

        if ($cuentaContable) {

            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            $ejecutado->setCuentaContable($cuentaContable);
            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

            $ejecutado->setCuentaPresupuestariaEconomica($cuentaContable->getCuentaPresupuestariaEconomica());

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuentaContable->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }

            $ejecutado->setMonto($comprobanteCompra->getTotal() * ($esContraAsiento ? -1 : 1));

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        } else {
            $erroresCuentaContableArray[$proveedor->getId()] = $proveedor;
        }


        /*         * **** Ejecutado - Percepciones e Impuestos **** *         */

        $montoEjecutadoPercepcionesEImpuestos = 0;
        $cuentaContablePercepcionesEImpuestos = null;

//        // Chequeo si el comprobante tiene IVA 
//        foreach ($comprobanteCompra->getRenglonesComprobante() as $renglonComprobanteCompra) {
//
//            /* @var $renglonComprobanteCompra RenglonComprobanteCompra */
//
//            if ($renglonComprobanteCompra->getAlicuotaIva()->getValor() != ConstanteAlicuotaIva::ALICUOTA_0) {
//                // CuentaContable                
//                $cuentaContablePercepcionesEImpuestos = $renglonComprobanteCompra->getAlicuotaIva()->getCuentaContableCredito();
//
//                if (!$cuentaContablePercepcionesEImpuestos) {
//                    $erroresCuentaContableArray[$renglonComprobanteCompra->getAlicuotaIva()->getId()] = $renglonComprobanteCompra->getAlicuotaIva();
//                }
//
//                $montoRenglonProrrateado = $renglonComprobanteCompra->getPrecioNetoTotalProrrateado(true);
//
//                $montoIva = $montoRenglonProrrateado * $renglonComprobanteCompra->getAlicuotaIva()->getValor() / 100;
//
//                $montoEjecutadoPercepcionesEImpuestos += $montoIva;
//            }
//        }
        // Ejecutado de las percepciones
        foreach ($comprobanteCompra->getRenglonesPercepcion() as $renglonPercepcion) {

            /* @var $renglonPercepcion \ADIF\ContableBundle\Entity\RenglonPercepcion */

            // ConceptoPercepcionParametrizacion
            $conceptoPercepcionParametrizacion = $emContable
                    ->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                    ->findOneBy(array(
                'conceptoPercepcion' => $renglonPercepcion->getConceptoPercepcion(), //
                'jurisdiccion' => $renglonPercepcion->getJurisdiccion())
            );

            if ($conceptoPercepcionParametrizacion) {
                // CuentaContable
                $cuentaContablePercepcionesEImpuestos = $conceptoPercepcionParametrizacion->getCuentaContableCredito();
            }

            $montoEjecutadoPercepcionesEImpuestos += $renglonPercepcion->getMonto();
        }


        // Ejecutado de los impuestos
        foreach ($comprobanteCompra->getRenglonesImpuesto() as $renglonImpuesto) {

            /* @var $renglonImpuesto \ADIF\ContableBundle\Entity\RenglonImpuesto */

            // CuentaContable
            $cuentaContablePercepcionesEImpuestos = $renglonImpuesto->getConceptoImpuesto()->getCuentaContable();

            $montoEjecutadoPercepcionesEImpuestos += $renglonImpuesto->getMonto();
        }

        // Creo el ejecutado
        if ($cuentaContablePercepcionesEImpuestos && $montoEjecutadoPercepcionesEImpuestos > 0) {

            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            $ejecutado->setCuentaContable($cuentaContablePercepcionesEImpuestos);

            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaObjetoGasto());

            if ($cuentaContable->getCuentaPresupuestariaEconomica()) {

                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                        (!$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE), //
                        $cuentaContablePercepcionesEImpuestos
                );

                $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
            }

            if ($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()) {

                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                        ConstanteTipoOperacionContable::DEBE, //
                        $cuentaContablePercepcionesEImpuestos
                );

                $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContablePercepcionesEImpuestos->getId()] = $cuentaContablePercepcionesEImpuestos;
                }
            }

            $ejecutado->setMonto($montoEjecutadoPercepcionesEImpuestos * ($esContraAsiento ? -1 : 1));

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        }
        /*         *  Fin Ejecutado - Percepciones e Impuestos  */


        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El comprobante no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param RendicionEgresoValor $rendicionEgresoValor
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromRendicionEgresoValor(RendicionEgresoValor $rendicionEgresoValor, AsientoContable $asiento = null) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        foreach ($rendicionEgresoValor->getComprobantes() as $comprobanteEgresoValor) {
            foreach ($comprobanteEgresoValor->getRenglonesComprobante() as $renglonComprobanteEgresoValor) {
                /* @var $renglonComprobanteEgresoValor RenglonComprobanteEgresoValor */

                $ejecutado = new EjecutadoEgresoValor();
                $ejecutado->setAsientoContable($asiento);

                $cuentaContable = $renglonComprobanteEgresoValor->getConceptoEgresoValor()->getCuentaContable();

                if ($cuentaContable) {
                    $ejecutado->setCuentaContable($cuentaContable);

                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    $ejecutado->setCuentaPresupuestariaEconomica(
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $ejecutado->getFechaEjecutado(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                    } else {
                        if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                            $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                        }
                    }
                } else {
                    $erroresCuentaContableArray[$renglonComprobanteEgresoValor->getConceptoEgresoValor()->getId()] = $renglonComprobanteEgresoValor->getConceptoEgresoValor();
                }

                $ejecutado->setRenglonComprobanteEgresoValor($renglonComprobanteEgresoValor);
                $ejecutado->setMonto($renglonComprobanteEgresoValor->getPrecioTotalProrrateado());

                if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                    $this->guardarAsientoPresupuestario($ejecutado);
                }
            }

            /*             * **** Ejecutado - Percepciones e Impuestos **** */

            $montoEjecutadoPercepcionesEImpuestos = 0;
            $cuentaContablePercepcionesEImpuestos = null;

            // Chequeo si el comprobante tiene IVA 
            foreach ($comprobanteEgresoValor->getRenglonesComprobante() as $renglonComprobanteCompra) {
                /* @var $renglonComprobanteCompra RenglonComprobanteCompra */
                if ($renglonComprobanteCompra->getAlicuotaIva()->getValor() != ConstanteAlicuotaIva::ALICUOTA_0) {
                    // CuentaContable                
                    $cuentaContablePercepcionesEImpuestos = $renglonComprobanteCompra->getAlicuotaIva()->getCuentaContableCredito();

                    if (!$cuentaContablePercepcionesEImpuestos) {
                        $erroresCuentaContableArray[$renglonComprobanteCompra->getAlicuotaIva()->getId()] = $renglonComprobanteCompra->getAlicuotaIva();
                    }

                    $montoRenglonProrrateado = $renglonComprobanteCompra->getPrecioNetoTotalProrrateado(true);
                    $montoIva = $montoRenglonProrrateado * $renglonComprobanteCompra->getAlicuotaIva()->getValor() / 100;
                    $montoEjecutadoPercepcionesEImpuestos += $montoIva;
                }
            }

            // Ejecutado de las percepciones
            foreach ($comprobanteEgresoValor->getRenglonesPercepcion() as $renglonPercepcion) {
                /* @var $renglonPercepcion \ADIF\ContableBundle\Entity\RenglonPercepcion */

                // ConceptoPercepcionParametrizacion
                $conceptoPercepcionParametrizacion = $emContable
                        ->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneBy(array(
                    'conceptoPercepcion' => $renglonPercepcion->getConceptoPercepcion(), //
                    'jurisdiccion' => $renglonPercepcion->getJurisdiccion())
                );

                if ($conceptoPercepcionParametrizacion) {
                    // CuentaContable
                    $cuentaContablePercepcionesEImpuestos = $conceptoPercepcionParametrizacion->getCuentaContableCredito();
                }

                $montoEjecutadoPercepcionesEImpuestos += $renglonPercepcion->getMonto();
            }


            // Ejecutado de los impuestos
            foreach ($comprobanteEgresoValor->getRenglonesImpuesto() as $renglonImpuesto) {
                /* @var $renglonImpuesto \ADIF\ContableBundle\Entity\RenglonImpuesto */
                // CuentaContable
                $cuentaContablePercepcionesEImpuestos = $renglonImpuesto->getConceptoImpuesto()->getCuentaContable();

                $montoEjecutadoPercepcionesEImpuestos += $renglonImpuesto->getMonto();
            }

            // Creo el ejecutado
            if ($cuentaContablePercepcionesEImpuestos && $montoEjecutadoPercepcionesEImpuestos > 0) {
                $ejecutado = new EjecutadoEgresoValor();
                $ejecutado->setAsientoContable($asiento);

                $ejecutado->setCuentaContable($cuentaContablePercepcionesEImpuestos);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaObjetoGasto());

                if ($cuentaContable->getCuentaPresupuestariaEconomica()) {
                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            ConstanteTipoOperacionContable::HABER, //
                            $cuentaContablePercepcionesEImpuestos
                    );

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                if ($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()) {
                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            ConstanteTipoOperacionContable::DEBE, //
                            $cuentaContablePercepcionesEImpuestos
                    );

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                } else {
                    if ($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContablePercepcionesEImpuestos->getId()] = $cuentaContablePercepcionesEImpuestos;
                    }
                }

                $ejecutado->setMonto($montoEjecutadoPercepcionesEImpuestos);

                if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                    $this->guardarAsientoPresupuestario($ejecutado);
                }
            }
        }

        // Por cada Devolucion de la RendicionEgresoValor
        foreach ($rendicionEgresoValor->getDevoluciones() as $devolucion) {
            $cuentaContableDevolucion = $devolucion->getCuenta()
                    ->getCuentaContable();

            if ($cuentaContableDevolucion) {

                $ejecutado = new Ejecutado();
                $ejecutado->setAsientoContable($asiento);

                $ejecutado->setCuentaContable($cuentaContableDevolucion);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContableDevolucion->getCuentaPresupuestariaObjetoGasto());

                $ejecutado->setCuentaPresupuestariaEconomica(
                        $cuentaContableDevolucion->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaContableDevolucion->getCuentaPresupuestariaEconomica()) {
                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            ConstanteTipoOperacionContable::DEBE, //
                            $cuentaContableDevolucion
                    );

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $cuentaContableDevolucion->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                } else {
                    if ($cuentaContableDevolucion->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$cuentaContableDevolucion->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContableDevolucion->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDevolucion->getId()] = $cuentaContableDevolucion;
                    }
                }

                $ejecutado->setMonto($devolucion->getMontoDevolucion());

                if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                    $this->guardarAsientoPresupuestario($ejecutado);
                }
            }
        }

        $egresoValor = $rendicionEgresoValor->getEgresoValor();

        $cuentaExcedente = $rendicionEgresoValor->getEgresoValor()
                        ->getTipoEgresoValor()->getCuentaContablReconocimiento();

        foreach ($egresoValor->getReconocimientos() as $reconocimiento) {
            /* @var $reconocimiento ReconocimientoEgresoValor */

            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            $ejecutado->setCuentaContable($cuentaExcedente);

            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaExcedente->getCuentaPresupuestariaObjetoGasto());

            $ejecutado->setCuentaPresupuestariaEconomica(
                    $cuentaExcedente->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaExcedente->getCuentaPresupuestariaEconomica()) {
                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                        ConstanteTipoOperacionContable::HABER, //
                        $cuentaExcedente
                );

                $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuentaExcedente->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaExcedente->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuentaExcedente->getCuentaPresupuestariaEconomica()->getId()] = $cuentaExcedente->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaExcedente->getId()] = $cuentaExcedente;
                }
            }

            $ejecutado->setMonto($reconocimiento->getMonto());

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        }

        /** EJECUTADO por el total de la rendicion */
        $ejecutado = new Ejecutado();

        $ejecutado->setAsientoContable($asiento);

        // Obtengo el Centro de costo asociado a la Gerencia        
        $centroCosto = $egresoValor->getGerencia() ? $egresoValor->getGerencia()->getCentroCosto() : null;

        // Renglones relacionados al EgresoValor        
        if ($egresoValor->getTipoEgresoValor()->getId() == ConstanteTipoEgresoValor::CAJA_CHICA) {

            // Busco la cc asociada a la gerencia de la caja chica
            $egresoValorGerencia = $emContable->getRepository('ADIFContableBundle:EgresoValor\EgresoValorGerencia')->findOneByIdGerencia($egresoValor->getIdGerencia());
            /* @var $egresoValorGerencia \ADIF\ContableBundle\Entity\EgresoValor\EgresoValorGerencia */

            $cuentaContableTipoEgresoValor = $egresoValorGerencia->getCuentaContable();
        } else {
            $cuentaContableTipoEgresoValor = $egresoValor->getTipoEgresoValor()->getCuentaContable();
        }

        if ($cuentaContableTipoEgresoValor) {

            // Indico el centro de costos
            if ($centroCosto != null) {

                $naturalezaCuentaContable = $cuentaContableTipoEgresoValor->getSegmentoOrden(1);

                // Si la naturaleza es un gasto
                if ($naturalezaCuentaContable == ConstanteNaturalezaCuenta::GASTO) {

                    $codigoCuentaContable = $this
                            ->getCuentaContableConCentroCosto($cuentaContableTipoEgresoValor->getCodigoCuentaContable(), $centroCosto);

                    $cuentaContableTipoEgresoValor = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                            ->findOneByCodigoCuentaContable($codigoCuentaContable);
                }
            }

            $ejecutado->setCuentaContable($cuentaContableTipoEgresoValor);

            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContableTipoEgresoValor->getCuentaPresupuestariaObjetoGasto());

            $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                    $cuentaContableTipoEgresoValor->getId() == 630 ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, //
                    $cuentaContableTipoEgresoValor
            );

            $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuentaPresupuestariaEconomica
            );

            if ($cuentaPresupuestaria != null) {

                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                // Cálculo del monto                
                $totalRendido = $rendicionEgresoValor->getImporteRendido();

                foreach ($egresoValor->getReconocimientos() as $reconocimiento) {
                    $totalRendido -= $reconocimiento->getMonto();
                }

                $ejecutado->setMonto($totalRendido);
            } else {

                if ($cuentaContableTipoEgresoValor->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableTipoEgresoValor->getId()] = $cuentaContableTipoEgresoValor;
                }
            }
        } else {
            $erroresCuentaContableArray[$egresoValor->getTipoEgresoValor()->getId()] = $egresoValor->getTipoEgresoValor();
        }


        if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
            $this->guardarAsientoPresupuestario($ejecutado);
        }




        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>La rendición no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param ComprobanteObra $comprobanteObra
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearAsientoFromComprobanteObra(ComprobanteObra $comprobanteObra, $esContraAsiento = false, AsientoContable $asiento = null) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        /*         * **** Devengado - Conceptos de Obra ***** */


        $tramo = $comprobanteObra->getDocumentoFinanciero()->getTramo();

        // Seteo el Definitivo si es que existe
        $definitivo = $emContable->getRepository('ADIFContableBundle:DefinitivoObra')->findOneByTramo($tramo);

        if ($tramo->getTieneFuenteCAF()) {
            $devengadoConceptoObra = new DevengadoObra();
            $devengadoConceptoObra->setAsientoContable($asiento);

            $devengadoConceptoObra->setDefinitivo($definitivo);

            $cuentaContableFuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION_CAF);

            if ($cuentaContableFuenteFinanciamiento != null) {
                $devengadoConceptoObra->setCuentaContable($cuentaContableFuenteFinanciamiento);

                $devengadoConceptoObra->setCuentaPresupuestariaObjetoGasto($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaObjetoGasto());

                $cuentaPresupuestariaEconomica = $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica();

                // Si es un AnticipoFinanciero
                if ($comprobanteObra->getDocumentoFinanciero()->getEsAnticipoFinanciero()) {
                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            ConstanteTipoOperacionContable::DEBE, //
                            $cuentaContableFuenteFinanciamiento
                    );
                }

                $devengadoConceptoObra->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $devengadoConceptoObra->getFechaDevengado(), //
                        $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $devengadoConceptoObra->setCuentaPresupuestaria($cuentaPresupuestaria);
                } else {
                    if ($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableFuenteFinanciamiento->getId()] = $cuentaContableFuenteFinanciamiento;
                    }
                }
            } else {
                $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION_CAF] = 'C&oacute;digo interno OBRAS_EJECUCION_CAF';
            }

            $fuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:Obras\FuenteFinanciamiento')->findOneByCodigo(\ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoFuenteFinanciamiento::CODIGO_CAF);

            $devengadoConceptoObra->setComprobanteObra($comprobanteObra);
            $devengadoConceptoObra->setFuenteFinanciamiento($fuenteFinanciamiento);

            $devengadoConceptoObra->setMonto($comprobanteObra->getTotalNeto());

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($devengadoConceptoObra);
            }
        } else {
            // Por cada FuenteFinanciamiento asociado al Tramo
            foreach ($tramo->getFuentesFinanciamiento() as $fuenteFinanciamientoTramo) {
                $fuenteFinanciamiento = $fuenteFinanciamientoTramo->getFuenteFinanciamiento();

                $devengadoConceptoObra = new DevengadoObra();
                $devengadoConceptoObra->setAsientoContable($asiento);

                $devengadoConceptoObra->setDefinitivo($definitivo);

                // Si la FuenteFinanciamiento modifica las cuentas contables
                if ($fuenteFinanciamiento->getModificaCuentaContable()) {

                    $cuentaContableFuenteFinanciamiento = $fuenteFinanciamiento->getCuentaContable();
                } else {

                    $cuentaContableFuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                            ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION);
                }

                if ($cuentaContableFuenteFinanciamiento != null) {

                    $devengadoConceptoObra->setCuentaContable($cuentaContableFuenteFinanciamiento);

                    $devengadoConceptoObra->setCuentaPresupuestariaObjetoGasto($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaObjetoGasto());

                    $cuentaPresupuestariaEconomica = $cuentaContableFuenteFinanciamiento
                            ->getCuentaPresupuestariaEconomica();

                    // Si es un AnticipoFinanciero
                    if ($comprobanteObra->getDocumentoFinanciero()->getEsAnticipoFinanciero()) {

                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                ConstanteTipoOperacionContable::DEBE, //
                                $cuentaContableFuenteFinanciamiento
                        );
                    }

                    $devengadoConceptoObra->setCuentaPresupuestariaEconomica(
                            $cuentaPresupuestariaEconomica
                    );

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $devengadoConceptoObra->getFechaDevengado(), //
                            $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $devengadoConceptoObra->setCuentaPresupuestaria($cuentaPresupuestaria);
                    } else {
                        if ($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica() != null) {
                            $erroresPresupuestoArray[$cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica();
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableFuenteFinanciamiento->getId()] = $cuentaContableFuenteFinanciamiento;
                        }
                    }
                } else {
                    $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION] = 'C&oacute;digo interno OBRAS_EJECUCION';
                }

                $totalComprobanteProrrateado = $comprobanteObra->getTotalNeto() * $fuenteFinanciamientoTramo->getPorcentaje() / 100;

                $devengadoConceptoObra->setComprobanteObra($comprobanteObra);
                $devengadoConceptoObra->setFuenteFinanciamiento($fuenteFinanciamiento);

                $devengadoConceptoObra->setMonto($totalComprobanteProrrateado);

                if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                    $this->guardarAsientoPresupuestario($devengadoConceptoObra);
                }
            }
        }



        // Por cada RenglonComprobanteObra del comprobante
        foreach ($comprobanteObra->getRenglonesComprobante() as $renglonComprobanteObra) {
            /* @var $renglonComprobanteObra \ADIF\ContableBundle\Entity\Obras\RenglonComprobanteObra */

            // Ejecutado IVA del renglon
            if ($renglonComprobanteObra->getAlicuotaIva()->getValor() != ConstanteAlicuotaIva::ALICUOTA_0) {
                // CuentaContable                
                $cuentaContable = $renglonComprobanteObra->getAlicuotaIva()->getCuentaContableCredito();

                if ($cuentaContable) {
                    $monto = $renglonComprobanteObra->getMontoIva();

                    $ejecutado = new Ejecutado();
                    $ejecutado->setAsientoContable($asiento);

                    $ejecutado->setCuentaContable($cuentaContable);

                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    if ($cuentaContable->getCuentaPresupuestariaEconomica()) {
                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                (!$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE), //
                                $cuentaContable
                        );

                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                    }

                    if ($cuentaContable->getCuentaPresupuestariaEconomica()) {
                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                ConstanteTipoOperacionContable::DEBE, //
                                $cuentaContable
                        );

                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                    }

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $ejecutado->getFechaEjecutado(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                    } else {
                        if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                            $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                        }
                    }

                    $ejecutado->setMonto($monto * ($esContraAsiento ? -1 : 1));
                } else {
                    $erroresCuentaContableArray[$renglonComprobanteObra->getAlicuotaIva()->getId()] = $renglonComprobanteObra->getAlicuotaIva();
                }

                if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                    $this->guardarAsientoPresupuestario($ejecutado);
                }
            }
        }

        /*         * **** FIN Ejecutado - Conceptos de Obra ***** */


        /*         * **** Ejecutado - Percepciones e Impuestos ***** */

        $montoEjecutadoPercepcionesEImpuestos = 0;
        $cuentaContablePercepcionesEImpuestos = null;

        // Ejecutado de las percepciones
        foreach ($comprobanteObra->getRenglonesPercepcion() as $renglonPercepcion) {

            /* @var $renglonPercepcion \ADIF\ContableBundle\Entity\RenglonPercepcion */

            // ConceptoPercepcionParametrizacion
            $conceptoPercepcionParametrizacion = $emContable
                    ->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                    ->findOneBy(array(
                'conceptoPercepcion' => $renglonPercepcion->getConceptoPercepcion(), //
                'jurisdiccion' => $renglonPercepcion->getJurisdiccion())
            );

            if ($conceptoPercepcionParametrizacion) {
                // CuentaContable
                $cuentaContablePercepcionesEImpuestos = $conceptoPercepcionParametrizacion->getCuentaContableCredito();
            }

            $montoEjecutadoPercepcionesEImpuestos += $renglonPercepcion->getMonto();
        }


        // Ejecutado de los impuestos
        foreach ($comprobanteObra->getRenglonesImpuesto() as $renglonImpuesto) {

            /* @var $renglonImpuesto \ADIF\ContableBundle\Entity\RenglonImpuesto */

            // CuentaContable
            $cuentaContablePercepcionesEImpuestos = $renglonImpuesto->getConceptoImpuesto()->getCuentaContable();

            $montoEjecutadoPercepcionesEImpuestos += $renglonImpuesto->getMonto();
        }

        // Creo el ejecutado
        if ($cuentaContablePercepcionesEImpuestos && $montoEjecutadoPercepcionesEImpuestos > 0) {

            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            $ejecutado->setCuentaContable($cuentaContablePercepcionesEImpuestos);

            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaObjetoGasto());

            $ejecutado->setCuentaPresupuestariaEconomica(
                    $cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()) {

                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                        ConstanteTipoOperacionContable::DEBE, //
                        $cuentaContablePercepcionesEImpuestos
                );

                $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContablePercepcionesEImpuestos->getId()] = $cuentaContablePercepcionesEImpuestos;
                }
            }

            $ejecutado->setMonto($montoEjecutadoPercepcionesEImpuestos);

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        }
        /*         *  FIN Ejecutado - Percepciones e Impuestos  */


        /*         * **** Ejecutado - Proveedor **** *         */

        /* @var $proveedor Proveedor */
        $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')
                ->find($comprobanteObra->getIdProveedor());

        $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($proveedor->getIdCuentaContable());

        if ($cuentaContable) {

            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            $ejecutado->setCuentaContable($cuentaContable);
            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

            $ejecutado->setCuentaPresupuestariaEconomica(
                    $cuentaContable->getCuentaPresupuestariaEconomica()
            );

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuentaContable->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }

            $ejecutado->setMonto($comprobanteObra->getTotal());

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        } else {
            $erroresCuentaContableArray[$proveedor->getId()] = $proveedor;
        }

        /*         * **** FIN Ejecutado - Proveedor **** *         */


        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El comprobante no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param Tramo $tramo
     * @param AsientoContable $asiento
     */
    public function generarAsientoPresupuestarioTramoFinalizado(Tramo $tramo, AsientoContable $asiento = null) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        /* OBRAS FINALIZADAS */
        $cuentaContableObrasFinalizadas = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_FINALIZADAS);

        if ($cuentaContableObrasFinalizadas != null) {

            $ejecutadoObrasFinalizadas = new Ejecutado();
            $ejecutadoObrasFinalizadas->setAsientoContable($asiento);

            $ejecutadoObrasFinalizadas->setCuentaContable($cuentaContableObrasFinalizadas);

            $ejecutadoObrasFinalizadas->setCuentaPresupuestariaObjetoGasto($cuentaContableObrasFinalizadas
                            ->getCuentaPresupuestariaObjetoGasto()
            );

            $ejecutadoObrasFinalizadas->setCuentaPresupuestariaEconomica(
                    $cuentaContableObrasFinalizadas->getCuentaPresupuestariaEconomica()
            );

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoObrasFinalizadas->getFechaEjecutado(), //
                    $cuentaContableObrasFinalizadas->getCuentaPresupuestariaEconomica()
            );

            $ejecutadoObrasFinalizadas->setMonto($tramo->getTotalContrato(true));

            if ($cuentaPresupuestaria != null) {
                $ejecutadoObrasFinalizadas->setCuentaPresupuestaria($cuentaPresupuestaria);

                $this->guardarAsientoPresupuestario($ejecutadoObrasFinalizadas);
            } else {

                if ($cuentaContableObrasFinalizadas->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuentaContableObrasFinalizadas->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContableObrasFinalizadas->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableObrasFinalizadas->getId()] = $cuentaContableObrasFinalizadas;
                }
            }
        } else {
            $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::OBRAS_FINALIZADAS] = 'C&oacute;digo interno OBRAS_FINALIZADAS';
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
                $ejecutado = new Ejecutado();
                $ejecutado->setAsientoContable($asiento);

                $cuentaContableFuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION_CAF);

                if ($cuentaContableFuenteFinanciamiento != null) {
                    $ejecutado->setCuentaContable($cuentaContableFuenteFinanciamiento);

                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaObjetoGasto());

                    $ejecutado->setCuentaPresupuestariaEconomica(
                            $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()
                    );

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $ejecutado->getFechaEjecutado(), //
                            $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                    } else {
                        if ($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica() != null) {
                            $erroresPresupuestoArray[$cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica();
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableFuenteFinanciamiento->getId()] = $cuentaContableFuenteFinanciamiento;
                        }
                    }
                } else {
                    $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION_CAF] = 'C&oacute;digo interno OBRAS_EJECUCION_CAF';
                }

                $ejecutado->setMonto($totalObrasEjecucion * -1);

                $this->guardarAsientoPresupuestario($ejecutado);
            } else {
                // Por cada FuenteFinanciamiento asociado al Tramo
                foreach ($tramo->getFuentesFinanciamiento() as $fuenteFinanciamientoTramo) {
                    $fuenteFinanciamiento = $fuenteFinanciamientoTramo->getFuenteFinanciamiento();

                    $ejecutado = new Ejecutado();
                    $ejecutado->setAsientoContable($asiento);

                    // Si la FuenteFinanciamiento modifica las cuentas contables
                    if ($fuenteFinanciamiento->getModificaCuentaContable()) {
                        $cuentaContableFuenteFinanciamiento = $fuenteFinanciamiento->getCuentaContable();
                    } else {
                        $cuentaContableFuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION);
                    }

                    if ($cuentaContableFuenteFinanciamiento != null) {
                        $ejecutado->setCuentaContable($cuentaContableFuenteFinanciamiento);

                        $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaObjetoGasto());

                        $ejecutado->setCuentaPresupuestariaEconomica(
                                $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()
                        );

                        /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                        $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                                $ejecutado->getFechaEjecutado(), //
                                $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()
                        );

                        if ($cuentaPresupuestaria != null) {
                            $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                        } else {
                            if ($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica() != null) {
                                $erroresPresupuestoArray[$cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica();
                            } else {
                                $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableFuenteFinanciamiento->getId()] = $cuentaContableFuenteFinanciamiento;
                            }
                        }
                    } else {
                        $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION] = 'C&oacute;digo interno OBRAS_EJECUCION';
                    }

                    $totalObrasEjecucionProrrateado = $totalObrasEjecucion * $fuenteFinanciamientoTramo->getPorcentaje() / 100;

                    $ejecutado->setMonto($totalObrasEjecucionProrrateado * -1);

                    $this->guardarAsientoPresupuestario($ejecutado);
                }
            }
        }


        if ($totalAnticipoFinanciero > 0) {

            $cuentaContableAnticipoFinancieroObras = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::ANTICIPO_FINANCIERO_OBRAS);

            if ($cuentaContableAnticipoFinancieroObras != null) {

                $ejecutadoAnticipoFinancieroObras = new Ejecutado();
                $ejecutadoAnticipoFinancieroObras->setAsientoContable($asiento);

                $ejecutadoAnticipoFinancieroObras->setCuentaContable($cuentaContableAnticipoFinancieroObras);

                $ejecutadoAnticipoFinancieroObras->setCuentaPresupuestariaObjetoGasto($cuentaContableAnticipoFinancieroObras
                                ->getCuentaPresupuestariaObjetoGasto()
                );

                $ejecutadoAnticipoFinancieroObras->setCuentaPresupuestariaEconomica(
                        $cuentaContableAnticipoFinancieroObras->getCuentaPresupuestariaEconomica()
                );

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutadoAnticipoFinancieroObras->getFechaEjecutado(), //
                        $cuentaContableAnticipoFinancieroObras->getCuentaPresupuestariaEconomica()
                );

                $ejecutadoAnticipoFinancieroObras->setMonto($totalAnticipoFinanciero);

                if ($cuentaPresupuestaria != null) {
                    $ejecutadoAnticipoFinancieroObras->setCuentaPresupuestaria($cuentaPresupuestaria);

                    $this->guardarAsientoPresupuestario($ejecutadoAnticipoFinancieroObras);
                } else {

                    if ($cuentaContableAnticipoFinancieroObras->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$cuentaContableAnticipoFinancieroObras->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContableAnticipoFinancieroObras->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableAnticipoFinancieroObras->getId()] = $cuentaContableAnticipoFinancieroObras;
                    }
                }
            } else {
                $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::ANTICIPO_FINANCIERO_OBRAS] = 'C&oacute;digo interno ANTICIPO_FINANCIERO_OBRAS';
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenPagoComprobante $ordenPagoComprobante
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromOrdenPagoComprobante(OrdenPagoComprobante $ordenPagoComprobante, AsientoContable $asiento = null, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        // Por cada ComprobanteCompra relacionado a la OrdenPagoComprobante
        foreach ($ordenPagoComprobante->getComprobantes() as $comprobante) {

            // Por cada RenglonComprobanteCompra del ComprobanteCompra
            
            // Me fijo que los netos no den 0, para que cueando prorratee los renglones no pase
            // el error de "division by cero" - hot-fix @gluis - 03/03/2016
            if ($comprobante->getTotalNeto() != 0) {
                
                foreach ($comprobante->getRenglonesComprobante() as $renglonComprobanteCompra) {

                    $ejecutado = new EjecutadoCompra();
                    $ejecutado->setAsientoContable($asiento);

                    $bienEconomico = $emCompras->getRepository('ADIFComprasBundle:BienEconomico')->find($renglonComprobanteCompra->getIdBienEconomico());

                    // Seteo el DevengadoCompra si es que existe
                    $devengado = $emContable->getRepository('ADIFContableBundle:DevengadoCompra')->findOneByRenglonComprobanteCompra($renglonComprobanteCompra);

                    $ejecutado->setDevengado($devengado);

					$cuentaContable = null;
					if ($bienEconomico->getIdCuentaContable() == null) {
						 $erroresCuentaContableArray[$bienEconomico->getId()] = $bienEconomico;
					} else {
						// CuentaContable
						$cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($bienEconomico->getIdCuentaContable());
					}
					
                    if ($cuentaContable) {
                        $ejecutado->setCuentaContable($cuentaContable);

                        $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                        $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutado, $renglonComprobanteCompra->getPrecioTotalProrrateado(), $cuentaContable, !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

                        /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                        $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                                $ejecutado->getFechaEjecutado(), //
                                $ejecutado->getCuentaPresupuestariaEconomica()
                        );

                        if ($cuentaPresupuestaria != null) {
                            $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                            if ($ejecutado->getMonto() != 0) {
                                $ejecutado->setOrdenPagoComprobante($ordenPagoComprobante);
                                $ejecutado->setRenglonComprobanteCompra($renglonComprobanteCompra);
                                $this->guardarAsientoPresupuestario($ejecutado);
                            }
                        } else {
                            if ($ejecutado->getCuentaPresupuestariaEconomica() != null) {
                                $erroresPresupuestoArray[$ejecutado->getCuentaPresupuestariaEconomica()->getId()] = $ejecutado->getCuentaPresupuestariaEconomica();
                            } else {
                                $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                            }
                        }
                    } else {
                        $erroresCuentaContableArray[$bienEconomico->getId()] = $bienEconomico;
                    }
                }
            }
        }

        //  * ******************************************** */
        //     Ejecutado relacionado al Proveedor          */
        //  * ******************************************** */
        $ejecutadoProveedor = new Ejecutado();

        $ejecutadoProveedor->setAsientoContable($asiento);

        $prov = $emCompras->getRepository('ADIFComprasBundle:Proveedor')->find($ordenPagoComprobante->getIdProveedor());
        $cuentaContableProveedor = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($prov->getIdCuentaContable());

        if ($cuentaContableProveedor) {

            $ejecutadoProveedor->setCuentaContable($cuentaContableProveedor);

            $ejecutadoProveedor->setCuentaPresupuestariaObjetoGasto($cuentaContableProveedor->getCuentaPresupuestariaObjetoGasto());

            $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoProveedor, $ordenPagoComprobante->getTotalBruto(), $cuentaContableProveedor, $esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoProveedor->getFechaEjecutado(), //
                    $ejecutadoProveedor->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoProveedor->setCuentaPresupuestaria($cuentaPresupuestaria);
                if ($ejecutadoProveedor->getMonto() != 0) {

                    $this->guardarAsientoPresupuestario($ejecutadoProveedor);
                }
            } else {
                if ($ejecutadoProveedor->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$ejecutadoProveedor->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoProveedor->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableProveedor->getId()] = $cuentaContableProveedor;
                }
            }
        } else {
            $erroresCuentaContableArray[$ordenPagoComprobante->getContrato()->getConsultor()->getId()] = $ordenPagoComprobante->getContrato()->getConsultor();
        }


        //  * ******************************************** */
        //     Ejecutado relacionado al Pago               */
        //  * ******************************************** */

        $this->generarEjecutadoPago($asiento, $ordenPagoComprobante, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);

        //  * ******************************************** */
        //     Ejecutado relacionado a las Retenciones     */
        //  * ******************************************** */

        foreach ($ordenPagoComprobante->getRetenciones() as $retencion) {

            $ejecutadoRetenciones = new Ejecutado();

            $ejecutadoRetenciones->setAsientoContable($asiento);

            $cuentaContableRegimenRetencion = $retencion->getRegimenRetencion()->getCuentaContable();

            if ($cuentaContableRegimenRetencion) {

                $ejecutadoRetenciones->setCuentaContable($cuentaContableRegimenRetencion);

                $ejecutadoRetenciones->setCuentaPresupuestariaObjetoGasto($cuentaContableRegimenRetencion->getCuentaPresupuestariaObjetoGasto());

                $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoRetenciones, $retencion->getMonto(), $cuentaContableRegimenRetencion, $esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutadoRetenciones->getFechaEjecutado(), //
                        $ejecutadoRetenciones->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutadoRetenciones->setCuentaPresupuestaria($cuentaPresupuestaria);
                    if ($ejecutadoRetenciones->getMonto() > 0) {

                        $this->guardarAsientoPresupuestario($ejecutadoRetenciones);
                    }
                } else {
                    if ($ejecutadoRetenciones->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$ejecutadoRetenciones->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoRetenciones->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableRegimenRetencion->getId()] = $cuentaContableRegimenRetencion;
                    }
                }
            } else {
                $erroresCuentaContableArray[$retencion->getRegimenRetencion()->getId()] = $retencion->getRegimenRetencion();
            }
        }


        //  * ******************************************** */
        //     Ejecutado relacionado a los Anticipos     */
        //  * ******************************************** */

        foreach ($ordenPagoComprobante->getAnticipos() as $anticipo) {

            /* @var $anticipo \ADIF\ContableBundle\Entity\AnticipoProveedor */
            $ejecutadoAnticipos = new Ejecutado();

            $ejecutadoAnticipos->setAsientoContable($asiento);

            $configuracion_cuenta_anticipo_proveedor = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_PROVEEDORES);
            $cuentaContableAnticipoProveedor = $configuracion_cuenta_anticipo_proveedor->getCuentaContable();

            if ($cuentaContableAnticipoProveedor) {

                $ejecutadoAnticipos->setCuentaContable($cuentaContableAnticipoProveedor);

                $ejecutadoAnticipos->setCuentaPresupuestariaObjetoGasto($cuentaContableAnticipoProveedor->getCuentaPresupuestariaObjetoGasto());

                $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoAnticipos, $anticipo->getMonto(), $cuentaContableAnticipoProveedor, $esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutadoAnticipos->getFechaEjecutado(), //
                        $ejecutadoAnticipos->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutadoAnticipos->setCuentaPresupuestaria($cuentaPresupuestaria);
                    if ($ejecutadoAnticipos->getMonto() != 0) {

                        $this->guardarAsientoPresupuestario($ejecutadoAnticipos);
                    }
                } else {
                    if ($ejecutadoRetenciones->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$ejecutadoAnticipos->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoAnticipos->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableAnticipoProveedor->getId()] = $cuentaContableAnticipoProveedor;
                    }
                }
            } else {
                $erroresCuentaContableArray[$anticipo->getId()] = $anticipo->getProveedor();
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenPagoObra $ordenPagoObra
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromOrdenPagoObra(OrdenPagoObra $ordenPagoObra, AsientoContable $asiento = null, $esContraAsiento = false) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        // Por cada Comprobante relacionado a la OrdenPagoObra
        foreach ($ordenPagoObra->getComprobantes() as $comprobante) {

            $tramo = $comprobante->getDocumentoFinanciero()->getTramo();

            // Por cada FuenteFinanciamiento asociado al Tramo
            foreach ($tramo->getFuentesFinanciamiento() as $fuenteFinanciamientoTramo) {

                $fuenteFinanciamiento = $fuenteFinanciamientoTramo->getFuenteFinanciamiento();

                $ejecutado = new EjecutadoObra();
                $ejecutado->setAsientoContable($asiento);

                // Seteo el DevengadoObra si es que existe
                $devengado = $emContable->getRepository('ADIFContableBundle:Obras\DevengadoObra')->findOneBy(array('comprobanteObra' => $comprobante, 'fuenteFinanciamiento' => $fuenteFinanciamiento));

                $ejecutado->setDevengado($devengado);

                // Si la FuenteFinanciamiento modifica las cuentas contables
                if ($fuenteFinanciamiento->getModificaCuentaContable()) {
                    $cuentaContableFuenteFinanciamiento = $fuenteFinanciamiento->getCuentaContable();
                } else {
                    $cuentaContableFuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION);
                }

                if ($cuentaContableFuenteFinanciamiento != null) {
                    $ejecutado->setCuentaContable($cuentaContableFuenteFinanciamiento);

                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaObjetoGasto());

                    $totalComprobanteProrrateado = $comprobante->getTotalNeto() * $fuenteFinanciamientoTramo->getPorcentaje() / 100;

                    $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutado, $totalComprobanteProrrateado, $cuentaContableFuenteFinanciamiento, !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $ejecutado->getFechaEjecutado(), //
                            $ejecutado->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                        if ($ejecutado->getMonto() != 0) {
                            $ejecutado->setOrdenPagoObra($ordenPagoObra);
                            $ejecutado->setComprobanteObra($comprobante);
                            $ejecutado->setFuenteFinanciamiento($fuenteFinanciamiento);
                            $this->guardarAsientoPresupuestario($ejecutado);
                        }
                    } else {
                        if ($ejecutado->getCuentaPresupuestariaEconomica() != null) {
                            $erroresPresupuestoArray[$ejecutado->getCuentaPresupuestariaEconomica()->getId()] = $ejecutado->getCuentaPresupuestariaEconomica();
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableFuenteFinanciamiento->getId()] = $cuentaContableFuenteFinanciamiento;
                        }
                    }
//                    $ejecutado->setCuentaPresupuestariaEconomica(
//                            $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()
//                    );
//
//                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
//                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
//                            $ejecutado->getFechaEjecutado(), //
//                            $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()
//                    );
//
//                    if ($cuentaPresupuestaria != null) {
//                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
//                    } else {
//                        if ($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica() != null) {
//                            $erroresPresupuestoArray[$cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica();
//                        } else {
//                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableFuenteFinanciamiento->getId()] = $cuentaContableFuenteFinanciamiento;
//                        }
//                    }
                } else {
                    $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION] = 'C&oacute;digo interno OBRAS_EJECUCION';
                }

//                $totalComprobanteProrrateado = $comprobante->getTotalNeto() * $fuenteFinanciamientoTramo->getPorcentaje() / 100;
//
//                $ejecutado->setOrdenPagoObra($ordenPagoObra);
//                $ejecutado->setComprobanteObra($comprobante);
//                $ejecutado->setFuenteFinanciamiento($fuenteFinanciamiento);
//
//                $ejecutado->setMonto($totalComprobanteProrrateado);
//
//                $this->guardarAsientoPresupuestario($ejecutado);
            }
        }

        //  * ******************************************** */
        //     Ejecutado relacionado al Proveedor          */
        //  * ******************************************** */
        $ejecutadoProveedor = new Ejecutado();

        $ejecutadoProveedor->setAsientoContable($asiento);

        $cuentaContableProveedor = $ordenPagoObra->getProveedor()->getCuentaContable();

        if ($cuentaContableProveedor) {

            $ejecutadoProveedor->setCuentaContable($cuentaContableProveedor);

            $ejecutadoProveedor->setCuentaPresupuestariaObjetoGasto($cuentaContableProveedor->getCuentaPresupuestariaObjetoGasto());
            // COPIAR ESTO 
            $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoProveedor, $ordenPagoObra->getTotalBruto(), $cuentaContableProveedor, $esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoProveedor->getFechaEjecutado(), //
                    $ejecutadoProveedor->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoProveedor->setCuentaPresupuestaria($cuentaPresupuestaria);
                if ($ejecutadoProveedor->getMonto() != 0) {
                    $this->guardarAsientoPresupuestario($ejecutadoProveedor);
                }
            } else {
                if ($ejecutadoProveedor->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$ejecutadoProveedor->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoProveedor->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableProveedor->getId()] = $cuentaContableProveedor;
                }
            }
            // COPIAR ESTO 
        } else {
            $erroresCuentaContableArray[$ordenPagoObra->getProveedor()->getId()] = $ordenPagoObra->getProveedor();
        }

        //  * ******************************************** */
        //     Ejecutado relacionado al Pago               */
        //  * ******************************************** */

        $this->generarEjecutadoPago($asiento, $ordenPagoObra, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);

        //  * ******************************************** */
        //     Ejecutado relacionado a las Retenciones     */
        //  * ******************************************** */

        foreach ($ordenPagoObra->getRetenciones() as $retencion) {

            $ejecutadoRetenciones = new Ejecutado();

            $ejecutadoRetenciones->setAsientoContable($asiento);

            $cuentaContableRegimenRetencion = $retencion->getRegimenRetencion()->getCuentaContable();

            if ($cuentaContableRegimenRetencion) {

                $ejecutadoRetenciones->setCuentaContable($cuentaContableRegimenRetencion);

                $ejecutadoRetenciones->setCuentaPresupuestariaObjetoGasto($cuentaContableRegimenRetencion->getCuentaPresupuestariaObjetoGasto());

                $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoRetenciones, $retencion->getMonto(), $cuentaContableRegimenRetencion, $esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutadoRetenciones->getFechaEjecutado(), //
                        $ejecutadoRetenciones->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutadoRetenciones->setCuentaPresupuestaria($cuentaPresupuestaria);
                    if ($ejecutadoRetenciones->getMonto() != 0) {
                        $this->guardarAsientoPresupuestario($ejecutadoRetenciones);
                    }
                } else {
                    if ($ejecutadoRetenciones->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$ejecutadoRetenciones->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoRetenciones->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableRegimenRetencion->getId()] = $cuentaContableRegimenRetencion;
                    }
                }
            } else {
                $erroresCuentaContableArray[$retencion->getRegimenRetencion()->getId()] = $retencion->getRegimenRetencion();
            }
        }

        //  * ******************************************** */
        //     Ejecutado relacionado a los Anticipos     */
        //  * ******************************************** */

        foreach ($ordenPagoObra->getAnticipos() as $anticipo) {
            /* @var $anticipo \ADIF\ContableBundle\Entity\AnticipoProveedor */
            $ejecutadoAnticipos = new Ejecutado();

            $ejecutadoAnticipos->setAsientoContable($asiento);

            $configuracion_cuenta_anticipo_proveedor = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_PROVEEDORES);
            $cuentaContableAnticipoProveedor = $configuracion_cuenta_anticipo_proveedor->getCuentaContable();

            if ($cuentaContableAnticipoProveedor) {

                $ejecutadoAnticipos->setCuentaContable($cuentaContableAnticipoProveedor);

                $ejecutadoAnticipos->setCuentaPresupuestariaObjetoGasto($cuentaContableAnticipoProveedor->getCuentaPresupuestariaObjetoGasto());

                if ($cuentaContableAnticipoProveedor->getCuentaPresupuestariaEconomica()) {

                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            ConstanteTipoOperacionContable::HABER, //
                            $cuentaContableAnticipoProveedor
                    );

                    $ejecutadoAnticipos->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutadoAnticipos->getFechaEjecutado(), //
                        $cuentaContableAnticipoProveedor->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutadoAnticipos->setCuentaPresupuestaria($cuentaPresupuestaria);
                    $ejecutadoAnticipos->setMonto($anticipo->getMonto());
                } else {
                    if ($cuentaPresupuestariaEconomica != null) {
                        $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableAnticipoProveedor->getId()] = $cuentaContableAnticipoProveedor;
                    }
                }
            } else {
                $erroresCuentaContableArray[$anticipo->getId()] = $anticipo->getProveedor();
            }

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutadoAnticipos);
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenPagoEgresoValor $ordenPagoEgresoValor
     */
    public function crearEjecutadoFromOrdenPagoEgresoValor(OrdenPagoEgresoValor $ordenPagoEgresoValor, AsientoContable $asiento = null, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $egresoValor = $ordenPagoEgresoValor->getEgresoValor();

        //  * ******************************************** */
        //     Ejecutado relacionado al Egreso de Valor    */
        //  * ******************************************** */
        $ejecutadoEgresoValor = new EjecutadoEgresoValor();
        $ejecutadoEgresoValor->setAsientoContable($asiento);

        $cuentaContableEgresoValor = $egresoValor->getTipoEgresoValor()->getCuentaContable();

        if ($cuentaContableEgresoValor) {

            if ($egresoValor->getTipoEgresoValor()->getId() == ConstanteTipoEgresoValor::CAJA_CHICA) {
                // Busco la cc asociada a la gerencia de la caja chica
                $egresoValorGerencia = $emContable->getRepository('ADIFContableBundle:EgresoValor\EgresoValorGerencia')->findOneByIdGerencia($egresoValor->getIdGerencia());
                /* @var $egresoValorGerencia \ADIF\ContableBundle\Entity\EgresoValor\EgresoValorGerencia */

                $cuentaContableEgresoValor = $egresoValorGerencia->getCuentaContable();
            }

            $ejecutadoEgresoValor->setCuentaContable($cuentaContableEgresoValor);

            $ejecutadoEgresoValor->setCuentaPresupuestariaObjetoGasto($cuentaContableEgresoValor->getCuentaPresupuestariaObjetoGasto());

            $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoEgresoValor, $ordenPagoEgresoValor->getImporte(), $cuentaContableEgresoValor, $esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoEgresoValor->getFechaEjecutado(), //
                    $ejecutadoEgresoValor->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoEgresoValor->setCuentaPresupuestaria($cuentaPresupuestaria);
                if ($ejecutadoEgresoValor->getMonto() != 0) {
                    $ejecutadoEgresoValor->setOrdenPagoEgresoValor($ordenPagoEgresoValor);
                    $this->guardarAsientoPresupuestario($ejecutadoEgresoValor);
                }
            } else {
                if ($ejecutadoEgresoValor->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$ejecutadoEgresoValor->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoEgresoValor->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableEgresoValor->getId()] = $cuentaContableEgresoValor;
                }
            }
        } else {
            $erroresCuentaContableArray[$egresoValor->getTipoEgresoValor()->getId()] = $egresoValor->getTipoEgresoValor();
        }

        //  * ******************************************** */
        //     Ejecutado relacionado al Pago               */
        //  * ******************************************** */

        $this->generarEjecutadoPago($asiento, $ordenPagoEgresoValor, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenPagoDevolucionGarantia $ordenPagoDevolucionGarantia
     */
    public function crearEjecutadoFromOrdenPagoDevolucionGarantia(OrdenPagoDevolucionGarantia $ordenPagoDevolucionGarantia, AsientoContable $asiento = null, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $contrato = $ordenPagoDevolucionGarantia->getDevolucionGarantia()->getCuponGarantia()->getContrato();

        if ($contrato->getEsContratoAlquiler()) {
            $codigoInterno = ConstanteCodigoInternoCuentaContable::DEVOLUCION_GARANTIA_ALQUILER;
        } else {
            $codigoInterno = ConstanteCodigoInternoCuentaContable::DEVOLUCION_GARANTIA;
        }

        //  * ******************************************** */
        //     Ejecutado relacionado a la Devolucion de Garantia */
        //  * ******************************************** */
        $ejecutadoDevolucionGarantia = new Ejecutado();
        $ejecutadoDevolucionGarantia->setAsientoContable($asiento);

        // Obtengo la CuentaContable por codigo interno
        $cuentaContableDevolucionGarantia = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                ->findOneByCodigoInterno($codigoInterno);

        // Renglones relacionados a la DevolucionGarantia
        if ($cuentaContableDevolucionGarantia) {
            $ejecutadoDevolucionGarantia->setCuentaContable($cuentaContableDevolucionGarantia);

            $ejecutadoDevolucionGarantia->setCuentaPresupuestariaObjetoGasto($cuentaContableDevolucionGarantia->getCuentaPresupuestariaObjetoGasto());

            $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoDevolucionGarantia, $ordenPagoDevolucionGarantia->getImporte(), $cuentaContableDevolucionGarantia, !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoDevolucionGarantia->getFechaEjecutado(), //
                    $ejecutadoDevolucionGarantia->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoDevolucionGarantia->setCuentaPresupuestaria($cuentaPresupuestaria);
                if ($ejecutadoDevolucionGarantia->getMonto() != 0) {
                    $this->guardarAsientoPresupuestario($ejecutadoDevolucionGarantia);
                }
            } else {
                if ($ejecutadoDevolucionGarantia->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$ejecutadoDevolucionGarantia->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoDevolucionGarantia->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDevolucionGarantia->getId()] = $cuentaContableDevolucionGarantia;
                }
            }
        } else {
            $erroresCuentaContableArray[$codigoInterno] = 'C&oacute;digo interno ' . $codigoInterno;
        }


        //  * ******************************************** */
        //     Ejecutado relacionado al Pago               */
        //  * ******************************************** */
        $this->generarEjecutadoPago($asiento, $ordenPagoDevolucionGarantia, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenPagoReconocimientoEgresoValor $ordenPago
     * @param type $esContraAsiento
     * @param AsientoContable $asiento
     */
    public function crearEjecutadoFromOrdenPagoReconocimientoEgresoValor(OrdenPagoReconocimientoEgresoValor $ordenPago, $esContraAsiento, AsientoContable $asiento) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $egresoValor = $ordenPago->getEgresoValor();

        //  * ******************************************** */
        //     Ejecutado relacionado al Egreso de Valor    */
        //  * ******************************************** */
        $ejecutadoEgresoValor = new Ejecutado();
        $ejecutadoEgresoValor->setAsientoContable($asiento);

        $cuentaContableEgresoValor = $egresoValor->getTipoEgresoValor()->getCuentaContablReconocimiento();

        if ($cuentaContableEgresoValor) {
            $ejecutadoEgresoValor->setCuentaContable($cuentaContableEgresoValor);

            $ejecutadoEgresoValor->setCuentaPresupuestariaObjetoGasto($cuentaContableEgresoValor->getCuentaPresupuestariaObjetoGasto());

            $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoEgresoValor, $ordenPago->getImporte(), $cuentaContableEgresoValor, !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoEgresoValor->getFechaEjecutado(), //
                    $ejecutadoEgresoValor->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoEgresoValor->setCuentaPresupuestaria($cuentaPresupuestaria);
                if ($ejecutadoEgresoValor->getMonto() != 0) {
                    $this->guardarAsientoPresupuestario($ejecutadoEgresoValor);
                }
            } else {
                if ($ejecutadoEgresoValor->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$ejecutadoEgresoValor->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoEgresoValor->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableEgresoValor->getId()] = $cuentaContableEgresoValor;
                }
            }
        } else {
            $erroresCuentaContableArray[$egresoValor->getTipoEgresoValor()->getId()] = $egresoValor->getTipoEgresoValor();
        }


        //  * ******************************************** */
        //     Ejecutado relacionado al Pago               */
        //  * ******************************************** */
        $this->generarEjecutadoPago($asiento, $ordenPago, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param ReconocimientoEgresoValor $reconocimientoEgresoValor
     * @param type $esContraAsiento
     * @param AsientoContable $asiento
     */
    public function crearEjecutadoFromCierreReconocimientoEgresoValor(ReconocimientoEgresoValor $reconocimientoEgresoValor, $esContraAsiento, AsientoContable $asiento) {

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $egresoValor = $reconocimientoEgresoValor->getEgresoValor();

        //  * ******************************************** */
        //     Ejecutado relacionado al Egreso de Valor    */
        //  * ******************************************** */
        $ejecutadoEgresoValor = new Ejecutado();
        $ejecutadoEgresoValor->setAsientoContable($asiento);

        $cuentaContableEgresoValor = $egresoValor->getTipoEgresoValor()->getCuentaContablReconocimiento();

        if ($cuentaContableEgresoValor) {

            $ejecutadoEgresoValor->setCuentaContable($cuentaContableEgresoValor);

            $ejecutadoEgresoValor->setCuentaPresupuestariaObjetoGasto($cuentaContableEgresoValor->getCuentaPresupuestariaObjetoGasto());

            if ($cuentaContableEgresoValor->getCuentaPresupuestariaEconomica()) {

                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                        !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, //
                        $cuentaContableEgresoValor
                );

                $ejecutadoEgresoValor->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoEgresoValor->getFechaEjecutado(), //
                    $cuentaContableEgresoValor->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoEgresoValor->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaPresupuestariaEconomica != null) {
                    $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableEgresoValor->getId()] = $cuentaContableEgresoValor;
                }
            }
        } else {
            $erroresCuentaContableArray[$egresoValor->getTipoEgresoValor()->getId()] = $egresoValor->getTipoEgresoValor();
        }

        $ejecutadoEgresoValor->setMonto($reconocimientoEgresoValor->getMonto());



        //  * ******************************************** */
        //     Ejecutado relacionado a la Ganancia */
        //  * ******************************************** */
        $ejecutadoGanancia = new Ejecutado();
        $ejecutadoGanancia->setAsientoContable($asiento);

        $cuentaGanancia = $egresoValor->getTipoEgresoValor()->getCuentaContablGanancia();

        if ($cuentaGanancia) {

            $ejecutadoGanancia->setCuentaContable($cuentaGanancia);

            $ejecutadoGanancia->setCuentaPresupuestariaObjetoGasto($cuentaGanancia->getCuentaPresupuestariaObjetoGasto());

            if ($cuentaGanancia->getCuentaPresupuestariaEconomica()) {

                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                        !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, //
                        $cuentaGanancia
                );

                $ejecutadoGanancia->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoGanancia->getFechaEjecutado(), //
                    $cuentaGanancia->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoGanancia->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaPresupuestariaEconomica != null) {
                    $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaGanancia->getId()] = $cuentaGanancia;
                }
            }
        } else {
            $erroresCuentaContableArray[$egresoValor->getTipoEgresoValor()->getId()] = $egresoValor->getTipoEgresoValor();
        }

        $ejecutadoGanancia->setMonto($reconocimientoEgresoValor->getMonto());


        if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
            $this->guardarAsientoPresupuestario($ejecutadoEgresoValor);
            $this->guardarAsientoPresupuestario($ejecutadoGanancia);
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param MovimientoBancario $movimientoBancario
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromMovimientoBancario(MovimientoBancario $movimientoBancario, AsientoContable $asiento = null, $esContraAsiento = false) {

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $cuentaContableOrigen = $movimientoBancario->getCuentaOrigen()->getCuentaContable();
        $cuentaContableDestino = $movimientoBancario->getCuentaDestino()->getCuentaContable();

        if ($cuentaContableOrigen) {
            //Renglon del ejecutado de la cuenta Origen
            $ejecutadoOrigen = new Ejecutado();
            $ejecutadoOrigen->setAsientoContable($asiento);

            $ejecutadoOrigen->setCuentaContable($cuentaContableOrigen);

            $cuenta_presupuestaria_economica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                    !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, //
                    $cuentaContableOrigen
            );
            $ejecutadoOrigen->setCuentaPresupuestariaEconomica($cuenta_presupuestaria_economica);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoOrigen->getFechaEjecutado(), //
                    $cuenta_presupuestaria_economica
            );

            if ($cuentaPresupuestaria != null) {

                $ejecutadoOrigen->setCuentaPresupuestaria($cuentaPresupuestaria);
                $ejecutadoOrigen->setMonto($movimientoBancario->getMonto());
            } else {
                if ($cuenta_presupuestaria_economica != null) {
                    $erroresPresupuestoArray[$cuenta_presupuestaria_economica->getId()] = $cuenta_presupuestaria_economica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableOrigen->getId()] = $cuentaContableOrigen;
                }
            }
        } else {
            $erroresCuentaContableArray[$renglonComprobanteEgresoValor->getConceptoEgresoValor()->getId()] = $movimientoBancario->getCuentaOrigen();
        }

        if ($cuentaContableDestino) {
            //Renglon del ejecutado de la cuenta Origen
            $ejecutadoDestino = new Ejecutado();
            $ejecutadoDestino->setAsientoContable($asiento);

            $ejecutadoDestino->setCuentaContable($cuentaContableDestino);

            $cuenta_presupuestaria_economica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                    !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, //
                    $cuentaContableDestino
            );
            $ejecutadoDestino->setCuentaPresupuestariaEconomica($cuenta_presupuestaria_economica);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoDestino->getFechaEjecutado(), //
                    $cuenta_presupuestaria_economica
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoDestino->setCuentaPresupuestaria($cuentaPresupuestaria);
                $ejecutadoDestino->setMonto($movimientoBancario->getMonto());
            } else {
                if ($cuenta_presupuestaria_economica != null) {
                    $erroresPresupuestoArray[$cuenta_presupuestaria_economica->getId()] = $cuenta_presupuestaria_economica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDestino->getId()] = $cuentaContableDestino;
                }
            }
        } else {
            $erroresCuentaContableArray[$renglonComprobanteEgresoValor->getConceptoEgresoValor()->getId()] = $movimientoBancario->getCuentaDestino();
        }

        if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
            $this->guardarAsientoPresupuestario($ejecutadoOrigen);
            $this->guardarAsientoPresupuestario($ejecutadoDestino);
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El movimiento no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param MovimientoMinisterial $movimientoMinisterial
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromMovimientoMinisterial(MovimientoMinisterial $movimientoMinisterial, AsientoContable $asiento = null, $esContraAsiento = false) {

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $cuentaContableOrigen = $movimientoMinisterial->getEsIngreso() ? $movimientoMinisterial->getConceptoTransaccionMinisterial()->getCuentaContable() : $movimientoMinisterial->getCuentaBancariaADIF()->getCuentaContable();
        $cuentaContableDestino = $movimientoMinisterial->getEsIngreso() ? $movimientoMinisterial->getCuentaBancariaADIF()->getCuentaContable() : $movimientoMinisterial->getConceptoTransaccionMinisterial()->getCuentaContable();

        if ($cuentaContableOrigen) {
            //Renglon del ejecutado de la cuenta Origen
            $ejecutadoOrigen = new Ejecutado();
            $ejecutadoOrigen->setAsientoContable($asiento);

            $ejecutadoOrigen->setCuentaContable($cuentaContableOrigen);

            $cuenta_presupuestaria_economica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                    !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, //
                    $cuentaContableOrigen
            );
            $ejecutadoOrigen->setCuentaPresupuestariaEconomica($cuenta_presupuestaria_economica);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoOrigen->getFechaEjecutado(), //
                    $cuenta_presupuestaria_economica
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoOrigen->setCuentaPresupuestaria($cuentaPresupuestaria);
                $ejecutadoOrigen->setMonto($movimientoMinisterial->getMonto());
            } else {
                if ($cuenta_presupuestaria_economica != null) {
                    $erroresPresupuestoArray[$cuenta_presupuestaria_economica->getId()] = $cuenta_presupuestaria_economica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableOrigen->getId()] = $cuentaContableOrigen;
                }
            }
        } else {

            if ($movimientoMinisterial->getEsIngreso()) {

                $erroresCuentaContableArray[$movimientoMinisterial->getConceptoTransaccionMinisterial()->getId()] = $movimientoMinisterial->getConceptoTransaccionMinisterial();
            } else {

                $erroresCuentaContableArray[$movimientoMinisterial->getCuentaBancariaADIF()->getId()] = $movimientoMinisterial->getCuentaBancariaADIF();
            }
        }

        if ($cuentaContableDestino) {
            //Renglon del ejecutado de la cuenta Origen
            $ejecutadoDestino = new Ejecutado();
            $ejecutadoDestino->setAsientoContable($asiento);

            $ejecutadoDestino->setCuentaContable($cuentaContableDestino);

            $cuenta_presupuestaria_economica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                    !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, //
                    $cuentaContableDestino
            );
            $ejecutadoDestino->setCuentaPresupuestariaEconomica($cuenta_presupuestaria_economica);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoDestino->getFechaEjecutado(), //
                    $cuenta_presupuestaria_economica
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoDestino->setCuentaPresupuestaria($cuentaPresupuestaria);
                $ejecutadoDestino->setMonto($movimientoMinisterial->getMonto());
            } else {
                if ($cuenta_presupuestaria_economica != null) {
                    $erroresPresupuestoArray[$cuenta_presupuestaria_economica->getId()] = $cuenta_presupuestaria_economica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDestino->getId()] = $cuentaContableDestino;
                }
            }
        } else {

            if ($movimientoMinisterial->getEsIngreso()) {
                $erroresCuentaContableArray[$movimientoMinisterial->getCuentaBancariaADIF()->getId()] = $movimientoMinisterial->getCuentaBancariaADIF();
            } else {
                $erroresCuentaContableArray[$movimientoMinisterial->getConceptoTransaccionMinisterial()->getId()] = $movimientoMinisterial->getConceptoTransaccionMinisterial();
            }
        }

        if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
            $this->guardarAsientoPresupuestario($ejecutadoOrigen);
            $this->guardarAsientoPresupuestario($ejecutadoDestino);
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El movimiento no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * @param type $fecha
     * @param CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica
     * @return type
     */
    public function getCuentaPresupuestaria($fecha, CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica = null) {

        $em = $this->doctrine->getManager(EntityManagers::getEmContable());

        $ejercicioContable = $em->getRepository('ADIFContableBundle:EjercicioContable')
                ->getEjercicioContableByFecha($fecha);

        $cuentaPresupuestaria = $em->getRepository('ADIFContableBundle:CuentaPresupuestaria')
                ->getCuentaPresupuestariaByEjercicioYCuentaEconomica($ejercicioContable, $cuentaPresupuestariaEconomica);

        return $cuentaPresupuestaria;
    }

    /**
     * 
     * @param type $fromCurrency
     * @param type $toCurrency
     * @return type
     */
    private function getTipoCambio($fromCurrency, $toCurrency) {

        $parametros = array(
            "FromCurrency" => $fromCurrency,
            "ToCurrency" => $toCurrency);

        $client = new SoapClient("http://www.webservicex.net/CurrencyConvertor.asmx?WSDL");

        $res = $client->ConversionRate($parametros);

        return $res->ConversionRateResult;
    }

    /**
     * 
     * @param type $renglonesMovimiento
     * @param type $esConciliacion
     * @param type $conciliacion
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromGastoBancario($renglonesMovimiento, $esConciliacion, $conciliacion, AsientoContable $asiento = null) {

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $total_asiento = 0;

        foreach ($renglonesMovimiento as $renglon) {
            /* @var $renglon \ADIF\ContableBundle\Entity\ConciliacionBancaria\RenglonConciliacion */
            //Renglon del ejecutado del gasto bancario

            $cuentaContable = $renglon->getConceptoConciliacion()->getCuentaContable();

            $ejecutadoConceptoConciliacion = new Ejecutado();
            $ejecutadoConceptoConciliacion->setAsientoContable($asiento);

            $ejecutadoConceptoConciliacion->setCuentaContable($cuentaContable);

            $naturaleza_cuenta = $cuentaContable->getSegmentoOrden(1);

            if ($naturaleza_cuenta != ConstanteNaturalezaCuenta::GASTO) {
                // Si es una cuenta resultado cambia la economica según sume o reste
                $cuenta_presupuestaria_economica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                        (($renglon->getMonto() < 0 && $esConciliacion) || ($renglon->getMonto() >= 0 && !$esConciliacion)) ? ConstanteTipoOperacionContable::DEBE :
                                ConstanteTipoOperacionContable::HABER, //
                        $cuentaContable
                );

                $monto = abs($renglon->getMonto());
            } else {
                // Es una cuenta de activo, misma economica pero cambia el signo del monto
                $cuenta_presupuestaria_economica = $cuentaContable->getCuentaPresupuestariaEconomica();
                $monto = (($renglon->getMonto() < 0 && $esConciliacion) || ($renglon->getMonto() >= 0 && !$esConciliacion)) ? abs($renglon->getMonto()) : $renglon->getMonto();
            }

            $total_asiento += $monto;

            $ejecutadoConceptoConciliacion->setCuentaPresupuestariaEconomica($cuenta_presupuestaria_economica);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoConceptoConciliacion->getFechaEjecutado(), //
                    $cuenta_presupuestaria_economica
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoConceptoConciliacion->setCuentaPresupuestaria($cuentaPresupuestaria);
                $ejecutadoConceptoConciliacion->setMonto($monto);
            } else {
                if ($cuenta_presupuestaria_economica != null) {
                    $erroresPresupuestoArray[$cuenta_presupuestaria_economica->getId()] = $cuenta_presupuestaria_economica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }
        }

        // Cuenta bancaria
        $cuentaContable = $conciliacion->getCuenta()->getCuentaContable();

        $ejecutadoCuentaBancaria = new Ejecutado();
        $ejecutadoCuentaBancaria->setAsientoContable($asiento);

        $ejecutadoCuentaBancaria->setCuentaContable($cuentaContable);

        $cuenta_presupuestaria_economica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                (($total_asiento < 0 && $esConciliacion) || ($total_asiento >= 0 && !$esConciliacion)) ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, $cuentaContable
        );

        //$monto = abs($renglon->getMonto());

        $ejecutadoCuentaBancaria->setCuentaPresupuestariaEconomica($cuenta_presupuestaria_economica);

        /* @var $cuentaPresupuestaria CuentaPresupuestaria */
        $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                $ejecutadoCuentaBancaria->getFechaEjecutado(), //
                $cuenta_presupuestaria_economica
        );

        if ($cuentaPresupuestaria != null) {
            $ejecutadoCuentaBancaria->setCuentaPresupuestaria($cuentaPresupuestaria);
            $ejecutadoCuentaBancaria->setMonto(abs($total_asiento));
        } else {
            if ($cuenta_presupuestaria_economica != null) {
                $erroresPresupuestoArray[$cuenta_presupuestaria_economica->getId()] = $cuenta_presupuestaria_economica;
            } else {
                $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>El movimiento no se pudo generar correctamente:</span>';
        } //.
        else {

            $this->guardarAsientoPresupuestario($ejecutadoConceptoConciliacion);
            $this->guardarAsientoPresupuestario($ejecutadoCuentaBancaria);
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param ComprobanteVenta $comprobanteVenta
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromComprobanteVenta(ComprobanteVenta $comprobanteVenta, AsientoContable $asiento = null, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());

        $revertirImputacion = ($esContraAsiento && ($comprobanteVenta->getTipoComprobante()->getId() != ConstanteTipoComprobanteVenta::NOTA_CREDITO)) || (!$esContraAsiento && ($comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_CREDITO));

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        //Ejecutado Cliente

        /* @var $cliente Cliente */
        $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')
                ->find($comprobanteVenta->getIdCliente());

        $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                ->find($cliente->getIdCuentaContable());

        if ($cuentaContable) {
            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            $ejecutado->setCuentaContable($cuentaContable);
            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

            $cuenta_presupuestaria_economica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(!$revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, $cuentaContable);

            $ejecutado->setCuentaPresupuestariaEconomica(
                    $cuenta_presupuestaria_economica
            );

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuenta_presupuestaria_economica
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuenta_presupuestaria_economica->getId()] = $cuenta_presupuestaria_economica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }

            $ejecutado->setMonto(abs($comprobanteVenta->getTotal()));

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        } else {
            $erroresCuentaContableArray[$cliente->getId()] = $cliente;
        }


        $monto_ejecutado_impuestos_percepciones = 0;

        // Chequeo si el comprobante tiene IVA
        foreach ($comprobanteVenta->getRenglonesComprobante() as $renglonComprobanteVenta) {
            /* @var $renglonComprobanteVenta RenglonComprobanteVenta */

            if ($renglonComprobanteVenta->getAlicuotaIva()->getValor() != ConstanteAlicuotaIva::ALICUOTA_0) {
                // CuentaContable                
                $cuentaContable = $renglonComprobanteVenta->getAlicuotaIva()->getCuentaContableDebito();

                if (!$cuentaContable) {
                    $erroresCuentaContableArray[$renglonComprobanteVenta->getAlicuotaIva()->getId()] = $renglonComprobanteVenta->getAlicuotaIva();
                }

                $monto_ejecutado_impuestos_percepciones += $renglonComprobanteVenta->getMontoIva();
            }
        }

        // Ejecutado de las percepciones
        foreach ($comprobanteVenta->getRenglonesPercepcion() as $renglonPercepcion) {
            /* @var $renglonPercepcion \ADIF\ContableBundle\Entity\RenglonPercepcion */

            /* @var $renglon_percepcion RenglonPercepcion */
            if ($renglonPercepcion->getJurisdiccion()) {
                $conceptoPercepcionParametrizacion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneBy(array(
                    'conceptoPercepcion' => $renglonPercepcion->getConceptoPercepcion(), //
                    'jurisdiccion' => $renglonPercepcion->getJurisdiccion())
                );
            } else {
                $conceptoPercepcionParametrizacion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                        ->findOneByConceptoPercepcion($renglonPercepcion->getConceptoPercepcion());
            }

//            $conceptoPercepcionParametrizacion = $emContable->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')->findOneBy(array(
//                'conceptoPercepcion' => $renglonPercepcion->getConceptoPercepcion(), //
//                'jurisdiccion' => $renglonPercepcion->getJurisdiccion())
//            );

            if ($conceptoPercepcionParametrizacion) {
                // CuentaContable
                $cuentaContable = $conceptoPercepcionParametrizacion->getCuentaContableDebito();
            }

            $monto_ejecutado_impuestos_percepciones += $renglonPercepcion->getMonto();
        }

        if ($cuentaContable && $monto_ejecutado_impuestos_percepciones) {
            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            $ejecutado->setCuentaContable($cuentaContable);

            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

            $cuenta_presupuestaria_economica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(!$revertirImputacion ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, $cuentaContable);

            $ejecutado->setCuentaPresupuestariaEconomica(
                    $cuenta_presupuestaria_economica
            );

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuenta_presupuestaria_economica
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuenta_presupuestaria_economica != null) {
                    $erroresPresupuestoArray[$cuenta_presupuestaria_economica->getId()] = $cuenta_presupuestaria_economica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }

            $ejecutado->setMonto(abs($monto_ejecutado_impuestos_percepciones));

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param ComprobanteVenta $comprobanteVenta
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearDevengadoFromComprobanteVenta(ComprobanteVenta $comprobanteVenta, AsientoContable $asiento = null, $esContraAsiento = false) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $codigoClaseContato = $comprobanteVenta->getCodigoClaseContrato();

        $claseContato = $emContable->getRepository('ADIFContableBundle:Facturacion\ClaseContrato')
                ->findOneByCodigo($codigoClaseContato);

        /* @var $comprobante ComprobanteVenta */
        if ($comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES) {
            // Si el comprobante es una nota de débito de intereses va a una cuenta particular
            $configuracion_cuenta_nota_debito_intereses = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_NOTA_DEBITO_INTERESES);
            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($configuracion_cuenta_nota_debito_intereses->getIdCuentaContable());
        } else {
            $cuentaContable = $claseContato->getCuentaContable();
        }

        foreach ($comprobanteVenta->getRenglonesComprobante() as $renglonComprobanteVenta) {

            /* @var $renglonComprobanteVenta RenglonComprobanteVenta */
            $devengado = new DevengadoVenta();
            $devengado->setAsientoContable($asiento);

            if ($cuentaContable) {
                $devengado->setCuentaContable($cuentaContable);

                $devengado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                $devengado->setCuentaPresupuestariaEconomica(
                        $cuentaContable->getCuentaPresupuestariaEconomica()
                );

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $devengado->getFechaDevengado(), //
                        $cuentaContable->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $devengado->setCuentaPresupuestaria($cuentaPresupuestaria);
                } else {
                    if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                    }
                }
            } else {
                $erroresCuentaContableArray[$claseContato->getId()] = $claseContato;
            }


            $definitivo = $emContable->getRepository('ADIFContableBundle:DefinitivoContratoVenta')
                    ->getDefinitivoByContratoYEjercicio($comprobanteVenta->getContrato(), $comprobanteVenta->getFechaComprobante()->format('Y'));

            $devengado->setDefinitivo($definitivo);

            $devengado->setRenglonComprobanteVenta($renglonComprobanteVenta);

            $monto = $renglonComprobanteVenta->getMontoNeto();

            if (($esContraAsiento && ($comprobanteVenta->getTipoComprobante()->getId() != ConstanteTipoComprobanteVenta::NOTA_CREDITO)) ||
                    (!$esContraAsiento && ($comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_CREDITO))) {
                $monto *= -1;
            }

            $devengado->setMonto($monto);

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($devengado);
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param ComprobanteVenta $comprobanteVenta
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearDevengadoFromComprobanteVentaGeneral(ComprobanteVenta $comprobanteVenta, AsientoContable $asiento = null, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        foreach ($comprobanteVenta->getRenglonesComprobante() as $renglonComprobanteVenta) {

            /* @var $renglonComprobanteVenta \ADIF\ContableBundle\Entity\Facturacion\RenglonComprobanteVentaGeneral */
            $devengado = new DevengadoVenta();
            $devengado->setAsientoContable($asiento);

            $cuentaContable = $renglonComprobanteVenta->getConceptoVentaGeneral()->getCuentaContable();

            $devengado->setCuentaContable($cuentaContable);

            $devengado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

            $devengado->setCuentaPresupuestariaEconomica(
                    $cuentaContable->getCuentaPresupuestariaEconomica()
            );

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $devengado->getFechaDevengado(), //
                    $cuentaContable->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $devengado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }

            $definitivo = $emContable->getRepository('ADIFContableBundle:DefinitivoContratoVenta')
                    ->getDefinitivoByContratoYEjercicio($comprobanteVenta->getContrato(), $comprobanteVenta->getFechaComprobante()->format('Y'));

            $devengado->setDefinitivo($definitivo);

            $devengado->setRenglonComprobanteVenta($renglonComprobanteVenta);

            $monto = $renglonComprobanteVenta->getMontoNeto();

            if (($esContraAsiento && ($comprobanteVenta->getTipoComprobante()->getId() != ConstanteTipoComprobanteVenta::NOTA_CREDITO)) ||
                    (!$esContraAsiento && ($comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_CREDITO))) {
                $monto *= -1;
            }

            $devengado->setMonto($monto);

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($devengado);
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param Liquidacion $liquidacion
     * @return boolean
     */
    public function crearDevengadoSueldosFromLiquidacion(Liquidacion $liquidacion) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $hayError = false;

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

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

        $movimientos_asiento = array();

        foreach ($liquidacion->getLiquidacionEmpleados() as $liquidacionEmpleado) {
            /* @var $liquidacionEmpleado LiquidacionEmpleado */
            $centro_costo = $liquidacionEmpleado->getEmpleado()->getGerencia()->getCentroCosto();

            // Renglon asiento del básico
            $configuracion_cuenta_basico = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_BASICO);
            $cuenta_contable = $configuracion_cuenta_basico->getCuentaContable();

            $codigo_cuenta = $this->getCuentaContableConCentroCosto($cuenta_contable->getCodigoCuentaContable(), $centro_costo);
            if (!isset($movimientos_asiento[$codigo_cuenta])) {
                $movimientos_asiento[$codigo_cuenta] = array('sueldo' => 0);
            }
            $movimientos_asiento[$codigo_cuenta]['sueldo'] += $liquidacionEmpleado->getBasico() + $liquidacionEmpleado->getRedondeo();

            foreach ($liquidacionEmpleado->getLiquidacionEmpleadoConceptos() as $liquidacionEmpleadoConcepto) {
                /* @var $liquidacionEmpleadoConcepto LiquidacionEmpleadoConcepto */

                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto()->getIdCuentaContable());

                if ($cuentaContable) {
                    $naturaleza_cuenta = $cuentaContable->getSegmentoOrden(1);

                    if ($naturaleza_cuenta == ConstanteNaturalezaCuenta::GASTO) {
                        // Si la naturaleza es un gasto, busco el centro de costos del empleado segun el area
                        $codigo_cuenta = $this->getCuentaContableConCentroCosto($cuentaContable->getCodigoCuentaContable(), $centro_costo);

                        if (in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto()->getIdTipoConcepto()->getId(), $tipos_concepto_asiento_sueldos)) {
                            if (!isset($movimientos_asiento[$codigo_cuenta])) {
                                $movimientos_asiento[$codigo_cuenta] = array('sueldo' => 0);
                            }
                            $movimientos_asiento[$codigo_cuenta]['sueldo'] += $liquidacionEmpleadoConcepto->getMonto();
                        } else {
                            if (in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto()->getIdTipoConcepto()->getId(), $tipos_concepto_asiento_contribuciones) && $liquidacion->getTipoLiquidacion()->getId() != TipoLiquidacion::__SAC) {
                                if (!isset($movimientos_asiento[$codigo_cuenta])) {
                                    $movimientos_asiento[$codigo_cuenta] = array('cargas' => 0);
                                }
                                $movimientos_asiento[$codigo_cuenta]['cargas'] += $liquidacionEmpleadoConcepto->getMonto();
                            }
                        }
                    }
                } else {
                    $erroresCuentaContableArray[$liquidacionEmpleadoConcepto->getId()] = $liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto()->getCodigo();
                }
            }
        }

        if (!$hayError) {
            foreach ($movimientos_asiento as $codigo_cuenta => $datos_movimiento) {
                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable($codigo_cuenta);
                foreach ($datos_movimiento as $tipo_devengado => $monto) {
                    if ($tipo_devengado == 'sueldo') {
                        $devengado = new DevengadoSueldo();
                    } else {
                        $devengado = new DevengadoCargas();
                    }
					
					// @TODO: en donde mierda guardas el id_asiento de la liquidacion?????

                    $devengado->setIdLiquidacion($liquidacion->getId());

                    $devengado->setCuentaContable($cuentaContable);

                    $devengado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    $devengado->setCuentaPresupuestariaEconomica($cuentaContable->getCuentaPresupuestariaEconomica());

                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $devengado->getFechaDevengado(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $devengado->setCuentaPresupuestaria($cuentaPresupuestaria);
                    } else {
                        if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                            $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                        }
                    }

                    $devengado->setMonto($monto);

                    if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                        $this->guardarAsientoPresupuestario($devengado);
                    }
                }
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param Liquidacion $liquidacion
     * @return boolean
     */
    public function crearEjecutadoSueldosFromLiquidacion(Liquidacion $liquidacion) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $hayError = false;

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $tipos_concepto_asiento_sueldos = array(
            TipoConcepto::__REMUNERATIVO,
            TipoConcepto::__NO_REMUNERATIVO,
            TipoConcepto::__DESCUENTO,
            TipoConcepto::__APORTE,
            TipoConcepto::__CUOTA_SINDICAL_APORTES,
            TipoConcepto::__CALCULO_GANANCIAS
        );

        $movimientos_asiento = array();

        foreach ($liquidacion->getLiquidacionEmpleados() as $liquidacionEmpleado) {
            /* @var $liquidacionEmpleado LiquidacionEmpleado */
            $centro_costo = $liquidacionEmpleado->getEmpleado()->getGerencia()->getCentroCosto();

            foreach ($liquidacionEmpleado->getLiquidacionEmpleadoConceptos() as $liquidacionEmpleadoConcepto) {
                /* @var $liquidacionEmpleadoConcepto LiquidacionEmpleadoConcepto */

                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto()->getIdCuentaContable());
                $naturaleza_cuenta = $cuentaContable->getSegmentoOrden(1);

                if ($naturaleza_cuenta != ConstanteNaturalezaCuenta::GASTO) {
                    // Si la naturaleza es un gasto, busco el centro de costos del empleado segun el area
                    $codigo_cuenta = $cuentaContable->getCodigoCuentaContable();

                    if ($cuentaContable) {
                        if (in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto()->getIdTipoConcepto()->getId(), $tipos_concepto_asiento_sueldos)) {
                            if (!isset($movimientos_asiento[$codigo_cuenta])) {
                                $movimientos_asiento[$codigo_cuenta] = 0;
                            }
                            $movimientos_asiento[$codigo_cuenta] += $liquidacionEmpleadoConcepto->getMonto();
                        }
                    } else {
                        $erroresCuentaContableArray[$liquidacionEmpleadoConcepto->getId()] = $liquidacionEmpleadoConcepto;
                    }
                }
            }
        }

        if (!$hayError && empty($erroresCuentaContableArray)) {
            foreach ($movimientos_asiento as $codigo_cuenta => $monto) {
                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable($codigo_cuenta);

                if ($cuentaContable) {

                    $ejecutado = new EjecutadoSueldo();

                    $ejecutado->setIdLiquidacion($liquidacion->getId());

                    $ejecutado->setCuentaContable($cuentaContable);

                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $naturaleza_cuenta = $cuentaContable->getSegmentoOrden(1);

                    if ($naturaleza_cuenta == ConstanteNaturalezaCuenta::GASTO) {
                        $cuentaPresupuestariaEconomica = $cuentaContable->getCuentaPresupuestariaEconomica();
                    } else {
                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                ConstanteTipoOperacionContable::HABER, //
                                $cuentaContable
                        );
                    }

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);

                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $ejecutado->getFechaEjecutado(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                    } else {
                        if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                            $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                        }
                    }

                    $ejecutado->setMonto($monto);

                    if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                        $this->guardarAsientoPresupuestario($ejecutado);
                    }
                } else {
                    $erroresCuentaContableArray[$liquidacionEmpleadoConcepto->getId()] = $liquidacionEmpleadoConcepto;
                }
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenPagoSueldo $ordenPago
     * @param AsientoContable $asiento
     * @return type
     * @throws Exception
     */
    public function crearEjecutadoFromOrdenPagoSueldos(OrdenPagoSueldo $ordenPago, AsientoContable $asiento = null) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $liquidacionesEmpleado = $emRRHH->getRepository('ADIFRecursosHumanosBundle:LiquidacionEmpleado')->getLiquidacionesEmpleadoByIdsLiquidacionesEmpleado(explode(',', $ordenPago->getLiquidacionesEmpleado()));

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();
        $errorCustom = '';

        $tipos_concepto_asiento_sueldos = array(
            TipoConcepto::__REMUNERATIVO,
            TipoConcepto::__NO_REMUNERATIVO,
            TipoConcepto::__DESCUENTO,
            TipoConcepto::__APORTE,
            TipoConcepto::__CUOTA_SINDICAL_APORTES,
            TipoConcepto::__CALCULO_GANANCIAS
        );

        $movimientos_asiento = array();

        foreach ($liquidacionesEmpleado as $liquidacionEmpleado) {

            /* @var $liquidacionEmpleado LiquidacionEmpleado */
            $centro_costo = $liquidacionEmpleado->getEmpleado()->getGerencia()->getCentroCosto();

            // Renglon asiento del básico
            $configuracion_cuenta_basico = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_BASICO);
            $cuenta_contable = $configuracion_cuenta_basico->getCuentaContable();

            $codigo_cuenta = $this->getCuentaContableConCentroCosto($cuenta_contable->getCodigoCuentaContable(), $centro_costo);
            if (!isset($movimientos_asiento[$codigo_cuenta]['devengado'])) {
                $movimientos_asiento[$codigo_cuenta]['devengado'] = 0;
            }
            $movimientos_asiento[$codigo_cuenta]['devengado'] += $liquidacionEmpleado->getBasico() + $liquidacionEmpleado->getRedondeo();

            foreach ($liquidacionEmpleado->getLiquidacionEmpleadoConceptos() as $liquidacionEmpleadoConcepto) {
                /* @var $liquidacionEmpleadoConcepto LiquidacionEmpleadoConcepto */

                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto()->getIdCuentaContable());
                $naturaleza_cuenta = $cuentaContable->getSegmentoOrden(1);

                //ejecucion devengado
                if ($naturaleza_cuenta == ConstanteNaturalezaCuenta::GASTO) {
                    // Si la naturaleza es un gasto, busco el centro de costos del empleado segun el area
                    $codigo_cuenta = $this->getCuentaContableConCentroCosto($cuentaContable->getCodigoCuentaContable(), $centro_costo);

                    if ($cuentaContable) {
                        if (in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto()->getIdTipoConcepto()->getId(), $tipos_concepto_asiento_sueldos)) {
                            if (!isset($movimientos_asiento[$codigo_cuenta]['devengado'])) {
                                $movimientos_asiento[$codigo_cuenta]['devengado'] = 0;
                            }
                            $movimientos_asiento[$codigo_cuenta]['devengado'] += $liquidacionEmpleadoConcepto->getMonto();
                        }
                    } else {
                        $erroresCuentaContableArray[$liquidacionEmpleadoConcepto->getId()] = $liquidacionEmpleadoConcepto;
                    }
                    //ejecutado
                } else {
                    // Si la naturaleza es un gasto, busco el centro de costos del empleado segun el area
                    $codigo_cuenta = $cuentaContable->getCodigoCuentaContable();

                    if ($cuentaContable) {
                        if (in_array($liquidacionEmpleadoConcepto->getConceptoVersion()->getConcepto()->getIdTipoConcepto()->getId(), $tipos_concepto_asiento_sueldos)) {
                            if (!isset($movimientos_asiento[$codigo_cuenta]['ejecutado'])) {
                                $movimientos_asiento[$codigo_cuenta]['ejecutado'] = 0;
                            }
                            $movimientos_asiento[$codigo_cuenta]['ejecutado'] += $liquidacionEmpleadoConcepto->getMonto();
                        }
                    } else {
                        $erroresCuentaContableArray[$liquidacionEmpleadoConcepto->getId()] = $liquidacionEmpleadoConcepto;
                    }
                }
            }
        }

        if (empty($erroresCuentaContableArray)) {

            foreach ($movimientos_asiento as $codigo_cuenta => $datos_movimiento) {

                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoCuentaContable($codigo_cuenta);

                foreach ($datos_movimiento as $tipo => $monto) {
                    if ($cuentaContable) {
                        $ejecutado = new EjecutadoSueldo();

                        $ejecutado->setIdLiquidacion($ordenPago->getIdLiquidacion())
                                ->setAsientoContable($asiento)
                                ->setCuentaContable($cuentaContable)
                                ->setMonto($monto)
                                ->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto())
                        ;

                        if ($tipo == 'devengado') {
                            $devengadoSueldo = $emContable->getRepository('ADIFContableBundle:DevengadoSueldo')->findOneBy(
                                    array(
                                        'idLiquidacion' => $ordenPago->getIdLiquidacion(),
                                        'cuentaContable' => $cuentaContable
                                    )
                            );

                            if ($devengadoSueldo) {
                                $ejecutado->setDevengado($devengadoSueldo)
                                        ->setCuentaPresupuestaria($devengadoSueldo->getCuentaPresupuestaria())
                                        ->setCuentaPresupuestariaEconomica($devengadoSueldo->getCuentaPresupuestariaEconomica());
                            } else {
                                $errorCustom .= '<div class="error-presupuestario" style="padding-left: 3em; margin-top: .5em">'
                                        . '<span class="error-title">No se encontró el devengado de la cuenta contable ' . $cuentaContable->__toString() . '</span>'
                                        . '</div>';
                            }
                        } else {
                            $naturaleza_cuenta = $cuentaContable->getSegmentoOrden(1);
//
                            if ($naturaleza_cuenta != ConstanteNaturalezaCuenta::ACTIVO) {

                                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                        ConstanteTipoOperacionContable::DEBE, //
                                        $cuentaContable
                                );

                                $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);

                                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                                        $ejecutado->getFechaEjecutado(), //
                                        $cuentaContable->getCuentaPresupuestariaEconomica()
                                );

                                if ($cuentaPresupuestaria != null) {
                                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                                } else {
                                    if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                                        $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                                    } else {
                                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                                    }
                                }
                            }
                        }

                        if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                            $this->guardarAsientoPresupuestario($ejecutado);
                        }
                    } else {
                        $erroresCuentaContableArray[$liquidacionEmpleadoConcepto->getId()] = $liquidacionEmpleadoConcepto->getConceptoVersion()->getCodigo();
                    }
                }
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg) . $errorCustom;
    }

    /**
     * 
     * @param OrdenPagoCargasSociales $ordenPago
     * @param AsientoContable $asiento
     * @return string
     */
    public function crearEjecutadoFromOrdenPagoCargasSociales(OrdenPagoCargasSociales $ordenPago, AsientoContable $asiento = null) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        //creo ejecutados por cada devengado que tengo
        $devengadosLiquidacion = $emContable->getRepository('ADIFContableBundle:DevengadoCargas')
                ->findByIdLiquidacion($ordenPago->getIdLiquidacion());

        foreach ($devengadosLiquidacion as $devengadoCargas) {

            /* @var $devengadoCargas DevengadoCargas */
            $ejecutado = new EjecutadoCargas();

            $ejecutado->setDevengado($devengadoCargas)
                    ->setAsientoContable($asiento)
                    ->setCuentaContable($devengadoCargas->getCuentaContable())
                    ->setCuentaPresupuestaria($devengadoCargas->getCuentaPresupuestaria())
                    ->setCuentaPresupuestariaEconomica($devengadoCargas->getCuentaPresupuestariaEconomica())
                    ->setCuentaPresupuestariaObjetoGasto($devengadoCargas->getCuentaPresupuestariaObjetoGasto())
                    ->setMonto($devengadoCargas->getMonto());

            $this->guardarAsientoPresupuestario($ejecutado);
        }

        return '';
    }

    /**
     * 
     * @param OrdenPagoAnticipoSueldo $anticipoSueldo
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromOrdenPagoAnticipoSueldo(OrdenPagoAnticipoSueldo $anticipoSueldo, AsientoContable $asiento = null, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $configuracion_cuenta_anticipo_sueldo = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_SUELDOS);

        $cuentaContable = $configuracion_cuenta_anticipo_sueldo->getCuentaContable();

        //$cuentaPresupuestariaEconomicaAnticipo = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(ConstanteTipoOperacionContable::DEBE, $cuentaContable);       

        $ejecutado = new EjecutadoAnticipoSueldo();
        $ejecutado->setAsientoContable($asiento);

        //$ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomicaAnticipo);
        $ejecutado->setCuentaContable($cuentaContable);
        $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

        $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutado, $anticipoSueldo->getTotalBruto(), $cuentaContable, !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

        /* @var $cuentaPresupuestaria CuentaPresupuestaria */
        $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                $ejecutado->getFechaEjecutado(), //
                $ejecutado->getCuentaPresupuestariaEconomica()
        );

        if ($cuentaPresupuestaria != null) {
            $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            if ($ejecutado->getMonto() != 0) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        } else {
            if ($ejecutado->getCuentaPresupuestariaEconomica() != null) {
                $erroresPresupuestoArray[$ejecutado->getCuentaPresupuestariaEconomica()->getId()] = $ejecutado->getCuentaPresupuestariaEconomica();
            } else {
                $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
            }
        }
        /*
          $cuentaPresupuestariaAnticipo = $this->getCuentaPresupuestaria(
          $ejecutado->getFechaEjecutado(), //
          $cuentaContable->getCuentaPresupuestariaEconomica()
          );

          if ($cuentaPresupuestariaAnticipo != null) {
          $ejecutado->setCuentaPresupuestaria($cuentaPresupuestariaAnticipo);
          $ejecutado->setMonto($anticipoSueldo->getTotalBruto());

          $this->guardarAsientoPresupuestario($ejecutado);
          } else {
          if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
          $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
          } else {
          $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
          }
          }
         */
        //$ejecutado_banco = new EjecutadoAnticipoSueldo();
        //$ejecutado_banco->setAsientoContable($asiento);
        // Renglones relacionados al Pago
        $this->generarEjecutadoPago($asiento, $anticipoSueldo, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);
        /*
          $cuentaBancariaPago = $anticipoSueldo->getPagoOrdenPago()->getCuentaBancariaADIF();
          $cuentaContableCuentaBancariaPago = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cuentaBancariaPago->getIdCuentaContable());

          $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
          ConstanteTipoOperacionContable::HABER, //
          $cuentaContableCuentaBancariaPago
          );

          $ejecutado_banco->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
          $ejecutado_banco->setCuentaContable($cuentaContableCuentaBancariaPago);
          $ejecutado_banco->setCuentaPresupuestariaObjetoGasto($cuentaContableCuentaBancariaPago->getCuentaPresupuestariaObjetoGasto());

          $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
          $ejecutado_banco->getFechaEjecutado(), //
          $cuentaContable->getCuentaPresupuestariaEconomica()
          );

          if ($cuentaPresupuestaria != null) {
          $ejecutado_banco->setCuentaPresupuestaria($cuentaPresupuestaria);
          $ejecutado_banco->setMonto($anticipoSueldo->getTotalBruto());

          $this->guardarAsientoPresupuestario($ejecutado_banco);
          } else {
          if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
          $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
          } else {
          $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
          }
          }

          //FIN REEMPLAZO
         */
        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param type $anticipoProveedor
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromOrdenPagoAnticipoProveedor($anticipoProveedor, AsientoContable $asiento = null, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $configuracion_cuenta_anticipo_proveedores = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_PROVEEDORES);

        $cuentaContable = $configuracion_cuenta_anticipo_proveedores->getCuentaContable();

        // $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(ConstanteTipoOperacionContable::DEBE, $cuentaContable);

        $ejecutado = new EjecutadoAnticipoProveedor();
        $ejecutado->setAsientoContable($asiento);

        // $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
        $ejecutado->setCuentaContable($cuentaContable);
        $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

        $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutado, $anticipoProveedor->getTotalBruto(), $cuentaContable, !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

        /* @var $cuentaPresupuestaria CuentaPresupuestaria */
        $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                $ejecutado->getFechaEjecutado(), //
                $ejecutado->getCuentaPresupuestariaEconomica()
        );

    
        if ($cuentaPresupuestaria != null) {
            $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            if ($ejecutado->getMonto() != 0) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        } else {
            if ($ejecutado->getCuentaPresupuestariaEconomica() != null) {
                $erroresPresupuestoArray[$ejecutado->getCuentaPresupuestariaEconomica()->getId()] = $ejecutado->getCuentaPresupuestariaEconomica();
            } else {
                $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
            }
        }

        // Renglones relacionados al Pago
        $this->generarEjecutadoPago($asiento, $anticipoProveedor, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);


        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param ComprobanteConsultoria $comprobanteConsultoria
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearAsientoFromComprobanteConsultoria(ComprobanteConsultoria $comprobanteConsultoria, AsientoContable $asiento = null) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        /*         * **** Devengado - Honorarios **** *         */
        $esHonorarioProfesional = $comprobanteConsultoria->getContrato()->getEsHonorarioProfesional();

        if ($esHonorarioProfesional) {
            $bienEconomico = $emCompras->getRepository('ADIFComprasBundle:BienEconomico')
                    ->findOneByCodigoInterno(ConstanteCodigoInternoBienEconomico::HONORARIO_PROFESIONAL);
        } else {
            $bienEconomico = $emCompras->getRepository('ADIFComprasBundle:BienEconomico')
                    ->findOneByCodigoInterno(ConstanteCodigoInternoBienEconomico::HONORARIO_NO_PROFESIONAL);
        }

        $definitivo = $emContable->getRepository('ADIFContableBundle:DefinitivoConsultoria')->findOneByContrato($comprobanteConsultoria->getContrato());

        if ($bienEconomico) {

            if ($bienEconomico->getCuentaContable() != null) {

                $cuentaContable = $bienEconomico->getCuentaContable();
                $naturaleza_cuenta = $cuentaContable->getSegmentoOrden(1);
                if ($naturaleza_cuenta == ConstanteNaturalezaCuenta::GASTO) {
                    // Si la naturaleza es un gasto, busco el centro de costos del consultor
                    $area_contrato = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Area')->find($comprobanteConsultoria->getContrato()->getIdArea());
                    $centro_costo = $emContable->getRepository('ADIFContableBundle:CentroCosto')->find($area_contrato->getIdCentrocosto());

                    $codigo_cuenta = $this->getCuentaContableConCentroCosto($cuentaContable->getCodigoCuentaContable(), $centro_costo);

                    $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                            ->findOneByCodigoCuentaContable($codigo_cuenta);
                }

                if ($cuentaContable) {

                    $devengado = new DevengadoConsultoria();

                    $devengado->setAsientoContable($asiento);

                    $devengado->setCuentaContable($cuentaContable);

                    $devengado->setContrato($comprobanteConsultoria->getContrato());

                    // Seteo el DefinitivoConsultoria si es que existe
                    $devengado->setDefinitivo($definitivo);

                    $devengado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    $devengado->setCuentaPresupuestariaEconomica(
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $devengado->getFechaDevengado(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $devengado->setCuentaPresupuestaria($cuentaPresupuestaria);
                        $devengado->setMonto($comprobanteConsultoria->getImporteTotalNeto());
                    } else {
                        if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                            $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                        }
                    }
                    if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                        $this->guardarAsientoPresupuestario($devengado);
                    }
                    // Fin Devengado - Honorarios
                } else {
                    $erroresCuentaContableArray[$bienEconomico->getId()] = $bienEconomico;
                }
            } else {
                $erroresCuentaContableArray[$bienEconomico->getId()] = $bienEconomico;
            }
        }

        /*         * **** Ejecutado - Percepciones e Impuestos **** *         */

        $montoEjecutadoPercepcionesEImpuestos = 0;
        $cuentaContablePercepcionesEImpuestos = null;

        // Chequeo si el comprobante tiene IVA
        foreach ($comprobanteConsultoria->getRenglonesComprobante() as $renglonComprobanteVenta) {
            /* @var $renglonComprobanteVenta RenglonComprobanteVenta */
            if ($renglonComprobanteVenta->getAlicuotaIva()->getValor() != ConstanteAlicuotaIva::ALICUOTA_0) {
                // CuentaContable                
                $cuentaContablePercepcionesEImpuestos = $renglonComprobanteVenta->getAlicuotaIva()->getCuentaContableCredito();

                if (!$cuentaContablePercepcionesEImpuestos) {
                    $erroresCuentaContableArray[$renglonComprobanteVenta->getAlicuotaIva()->getId()] = $renglonComprobanteVenta->getAlicuotaIva();
                }

                $montoEjecutadoPercepcionesEImpuestos += $renglonComprobanteVenta->getMontoIva();
            }
        }

        // Ejecutado de las percepciones
        foreach ($comprobanteConsultoria->getRenglonesPercepcion() as $renglonPercepcion) {

            /* @var $renglonPercepcion \ADIF\ContableBundle\Entity\RenglonPercepcion */

            // ConceptoPercepcionParametrizacion
            $conceptoPercepcionParametrizacion = $emContable
                    ->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                    ->findOneBy(array(
                'conceptoPercepcion' => $renglonPercepcion->getConceptoPercepcion(), //
                'jurisdiccion' => $renglonPercepcion->getJurisdiccion())
            );

            if ($conceptoPercepcionParametrizacion) {
                // CuentaContable
                $cuentaContablePercepcionesEImpuestos = $conceptoPercepcionParametrizacion->getCuentaContableCredito();
            }

            $montoEjecutadoPercepcionesEImpuestos += $renglonPercepcion->getMonto();
        }


        // Ejecutado de los impuestos
        foreach ($comprobanteConsultoria->getRenglonesImpuesto() as $renglonImpuesto) {

            /* @var $renglonImpuesto \ADIF\ContableBundle\Entity\RenglonImpuesto */

            // CuentaContable
            $cuentaContablePercepcionesEImpuestos = $renglonImpuesto->getConceptoImpuesto()->getCuentaContable();

            $montoEjecutadoPercepcionesEImpuestos += $renglonImpuesto->getMonto();
        }

        // Creo el ejecutado
        if ($cuentaContablePercepcionesEImpuestos && $montoEjecutadoPercepcionesEImpuestos > 0) {

            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            $ejecutado->setCuentaContable($cuentaContablePercepcionesEImpuestos);

            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaObjetoGasto());

            $ejecutado->setCuentaPresupuestariaEconomica(
                    $cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()
            );

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContablePercepcionesEImpuestos->getId()] = $cuentaContablePercepcionesEImpuestos;
                }
            }

            $ejecutado->setMonto($montoEjecutadoPercepcionesEImpuestos);

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        }
        // Fin Ejecutado - Percepciones e Impuestos

        /*         * **** Ejecutado - Acreedores Varios **** *         */

        /* @var $consultor Consultor */
        $consultor = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor')->find($comprobanteConsultoria->getContrato()->getIdConsultor());
        $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($consultor->getIdCuentaContable());

        if ($cuentaContable) {

            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            $ejecutado->setCuentaContable($cuentaContable);
            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

            $ejecutado->setCuentaPresupuestariaEconomica(
                    $cuentaContable->getCuentaPresupuestariaEconomica()
            );

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuentaContable->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }

            $ejecutado->setMonto($comprobanteConsultoria->getTotal());

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        } else {
            $erroresCuentaContableArray[$consultor->getId()] = $consultor;
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenPagoConsultoria $ordenPagoConsultoria
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromOrdenPagoConsultoria(OrdenPagoConsultoria $ordenPagoConsultoria, AsientoContable $asiento = null, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $idsDevengadosEjecutados = array();

        // Por cada ComprobanteConsultoria relacionado a la OrdenPago
        foreach ($ordenPagoConsultoria->getComprobantes() as $comprobante) {

            $monto = $comprobante->getImporteTotalNeto();

            $devengadosAEjecutar = $emContable->getRepository('ADIFContableBundle:DevengadoConsultoria')
                    ->findBy(
                    array(
                        'contrato' => $ordenPagoConsultoria->getContrato(),
                        'monto' => $monto
                    )
            );

            /* @var $devengado DevengadoConsultoria */
            $devengado = null;

            foreach ($devengadosAEjecutar as $devengadoAEjecutar) {
                if (!in_array($devengadoAEjecutar->getId(), $idsDevengadosEjecutados)) {
                    $idsDevengadosEjecutados[] = $devengadoAEjecutar->getId();
                    $devengado = $devengadoAEjecutar;
                    break;
                }
            }

            $ejecutado = new EjecutadoConsultoria();

            $ejecutado->setAsientoContable($asiento);
            $ejecutado->setDevengado($devengado);
            $ejecutado->setContrato($devengado->getContrato());
            $ejecutado->setCuentaContable($devengado->getCuentaContable());
            $ejecutado->setCuentaPresupuestariaObjetoGasto($devengado->getCuentaPresupuestariaObjetoGasto());
            $ejecutado->setCuentaPresupuestariaEconomica($devengado->getCuentaPresupuestariaEconomica());
            $ejecutado->setCuentaPresupuestaria($devengado->getCuentaPresupuestaria());
            $ejecutado->setMonto($devengado->getMonto());
            
            $this->guardarAsientoPresupuestario($ejecutado);
            
        }

        //  * ******************************************** */
        //     Ejecutado relacionado al Consultor          */
        //  * ******************************************** */
        $ejecutadoConsultor = new Ejecutado();

        $ejecutadoConsultor->setAsientoContable($asiento);

        $cuentaContableConsultor = $ordenPagoConsultoria->getContrato()->getConsultor()->getCuentaContable();

        if ($cuentaContableConsultor) {
            $ejecutadoConsultor->setCuentaContable($cuentaContableConsultor);

            $ejecutadoConsultor->setCuentaPresupuestariaObjetoGasto($cuentaContableConsultor->getCuentaPresupuestariaObjetoGasto());

            $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoConsultor, $ordenPagoConsultoria->getTotalBruto(), $cuentaContableConsultor, !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoConsultor->getFechaEjecutado(), //
                    $ejecutadoConsultor->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoConsultor->setCuentaPresupuestaria($cuentaPresupuestaria);
                if ($ejecutadoConsultor->getMonto() != 0) {
                    $this->guardarAsientoPresupuestario($ejecutadoConsultor);
                }
            } else {
                if ($ejecutadoConsultor->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$ejecutadoConsultor->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoConsultor->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableConsultor->getId()] = $cuentaContableConsultor;
                }
            }
        } else {
            $erroresCuentaContableArray[$ordenPagoConsultoria->getContrato()->getConsultor()->getId()] = $ordenPagoConsultoria->getContrato()->getConsultor();
        }


        //  * ******************************************** */
        //     Ejecutado relacionado al Pago               */
        //  * ******************************************** */

        $this->generarEjecutadoPago($asiento, $ordenPagoConsultoria, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);

        //  * ******************************************** */
        //     Ejecutado relacionado a las Retenciones     */
        //  * ******************************************** */

        foreach ($ordenPagoConsultoria->getRetenciones() as $retencion) {

            $ejecutadoRetenciones = new Ejecutado();

            $ejecutadoRetenciones->setAsientoContable($asiento);

            $cuentaContableRegimenRetencion = $retencion->getRegimenRetencion()->getCuentaContable();

            if ($cuentaContableRegimenRetencion) {

                $ejecutadoRetenciones->setCuentaContable($cuentaContableRegimenRetencion);

                $ejecutadoRetenciones->setCuentaPresupuestariaObjetoGasto($cuentaContableRegimenRetencion->getCuentaPresupuestariaObjetoGasto());

                $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoRetenciones, $retencion->getMonto(), $cuentaContableRegimenRetencion, !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE);

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutadoRetenciones->getFechaEjecutado(), //
                        $ejecutadoRetenciones->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutadoRetenciones->setCuentaPresupuestaria($cuentaPresupuestaria);
                    if ($ejecutadoRetenciones->getMonto() != 0) {
                        $this->guardarAsientoPresupuestario($ejecutadoRetenciones);
                    }
                } else {
                    if ($ejecutadoRetenciones->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$ejecutadoRetenciones->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoRetenciones->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableRegimenRetencion->getId()] = $cuentaContableRegimenRetencion;
                    }
                }
            } else {
                $erroresCuentaContableArray[$retencion->getRegimenRetencion()->getId()] = $retencion->getRegimenRetencion();
            }
        }

        //  * ******************************************** */
        //     Ejecutado relacionado a los Anticipos     */
        //  * ******************************************** */

        foreach ($ordenPagoConsultoria->getAnticipos() as $anticipo) {
            /* @var $anticipo \ADIF\ContableBundle\Entity\AnticipoContratoConsultoria */
            $ejecutadoAnticipos = new Ejecutado();

            $ejecutadoAnticipos->setAsientoContable($asiento);

            $configuracion_cuenta_anticipo_consultor = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_PROVEEDORES);
            $cuentaContableAnticipoConsultor = $configuracion_cuenta_anticipo_consultor->getCuentaContable();

            if ($cuentaContableAnticipoConsultor) {

                $ejecutadoAnticipos->setCuentaContable($cuentaContableAnticipoConsultor);

                $ejecutadoAnticipos->setCuentaPresupuestariaObjetoGasto($cuentaContableAnticipoConsultor->getCuentaPresupuestariaObjetoGasto());

                $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoAnticipos, $anticipo->getMonto(), $cuentaContableAnticipoConsultor, !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE);

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutadoAnticipos->getFechaEjecutado(), //
                        $ejecutadoAnticipos->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutadoAnticipos->setCuentaPresupuestaria($cuentaPresupuestaria);
                    if ($ejecutadoAnticipos->getMonto() != 0) {
                        $this->guardarAsientoPresupuestario($ejecutadoAnticipos);
                    }
                } else {
                    if ($ejecutadoAnticipos->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$ejecutadoAnticipos->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoAnticipos->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableAnticipoConsultor->getId()] = $cuentaContableRegimenRetencion;
                    }
                }
            } else {
                $erroresCuentaContableArray[$anticipo->getId()] = $anticipo->getConsultor();
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenPagoPagoACuenta $ordenPago
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromOrdenPagoPagoACuenta(OrdenPagoPagoACuenta $ordenPago, AsientoContable $asiento = null, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        //  **********************************************/
        //  Ejecutado relacionado a CREDITOS_IMPOSITIVOS */
        //  **********************************************/

        $ejecutadoCreditosImpositivos = new Ejecutado();

        $ejecutadoCreditosImpositivos->setAsientoContable($asiento);

        // Obtengo la CuentaContable con codigo interno CREDITOS_IMPOSITIVOS
        $cuentaContableCreditosImpositivos = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::CREDITOS_IMPOSITIVOS);

        if ($cuentaContableCreditosImpositivos) {
            $ejecutadoCreditosImpositivos->setCuentaContable($cuentaContableCreditosImpositivos);

            $ejecutadoCreditosImpositivos->setCuentaPresupuestariaObjetoGasto($cuentaContableCreditosImpositivos->getCuentaPresupuestariaObjetoGasto());

            $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoCreditosImpositivos, $ordenPago->getTotalBruto(), $cuentaContableCreditosImpositivos, !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoCreditosImpositivos->getFechaEjecutado(), //
                    $ejecutadoCreditosImpositivos->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoCreditosImpositivos->setCuentaPresupuestaria($cuentaPresupuestaria);
                if ($ejecutadoCreditosImpositivos->getMonto() != 0) {
                    $this->guardarAsientoPresupuestario($ejecutadoCreditosImpositivos);
                }
            } else {
                if ($ejecutadoCreditosImpositivos->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$ejecutadoCreditosImpositivos->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoCreditosImpositivos->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableCreditosImpositivos->getId()] = $cuentaContableCreditosImpositivos;
                }
            }
        } else {
            $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::CREDITOS_IMPOSITIVOS] = 'C&oacute;digo interno CREDITOS_IMPOSITIVOS';
        }


        //  * ******************************************** */
        //     Ejecutado relacionado al Pago               */
        //  * ******************************************** */
        $this->generarEjecutadoPago($asiento, $ordenPago, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenPagoDevolucionRenglonDeclaracionJurada $ordenPago
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromOrdenPagoDevolucionRenglonDeclaracionJurada(OrdenPagoDevolucionRenglonDeclaracionJurada $ordenPago, AsientoContable $asiento = null, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        //  **********************************************/
        //  Ejecutado relacionado a CREDITOS_IMPOSITIVOS */
        //  **********************************************/

        $ejecutadoRetenciones = new Ejecutado();
        $ejecutadoRetenciones->setAsientoContable($asiento);

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

        if ($cuentaContableRetencion) {
            $ejecutadoRetenciones->setCuentaContable($cuentaContableRetencion);

            $ejecutadoRetenciones->setCuentaPresupuestariaObjetoGasto($cuentaContableRetencion->getCuentaPresupuestariaObjetoGasto());

            $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoRetenciones, $ordenPago->getTotalBruto(), $cuentaContableRetencion, !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoRetenciones->getFechaEjecutado(), //
                    $ejecutadoRetenciones->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoRetenciones->setCuentaPresupuestaria($cuentaPresupuestaria);
                if ($ejecutadoRetenciones->getMonto() != 0) {
                    $this->guardarAsientoPresupuestario($ejecutadoRetenciones);
                }
            } else {
                if ($ejecutadoRetenciones->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$ejecutadoRetenciones->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoRetenciones->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableRetencion->getId()] = $cuentaContableRetencion;
                }
            }
        } else {
            $erroresCuentaContableArray[$ordenPago->getDevolucionRenglonDeclaracionJurada()->getRenglonDeclaracionJurada()->getTipoImpuesto()->getId()] = 'Tipo impuesto ' . $ordenPago->getDevolucionRenglonDeclaracionJurada()->getRenglonDeclaracionJurada()->getTipoImpuesto()->getDenominacion();
        }

        //  * ******************************************** */
        //     Ejecutado relacionado al Pago               */
        //  * ******************************************** */
        $this->generarEjecutadoPago($asiento, $ordenPago, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenPagoPagoParcial $ordenPago
     * @param AsientoContable $asientoContable
     * @return type
     */
    public function crearEjecutadoFromOrdenPagoPagoParcial(OrdenPagoPagoParcial $ordenPago, AsientoContable $asientoContable = null, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        //  * ******************************************** */
        //     Ejecutado relacionado al Proveedor          */
        //  * ******************************************** */

        $ejecutadoProveedor = new Ejecutado();
        $ejecutadoProveedor->setAsientoContable($asientoContable);

        $cuentaContableProveedor = $ordenPago->getProveedor()->getCuentaContable();

        if ($cuentaContableProveedor) {
            $ejecutadoProveedor->setCuentaContable($cuentaContableProveedor);

            $ejecutadoProveedor->setCuentaPresupuestariaObjetoGasto($cuentaContableProveedor->getCuentaPresupuestariaObjetoGasto());

            $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoProveedor, $ordenPago->getImporte(), $cuentaContableProveedor, !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoProveedor->getFechaEjecutado(), //
                    $ejecutadoProveedor->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoProveedor->setCuentaPresupuestaria($cuentaPresupuestaria);
                if ($ejecutadoProveedor->getMonto() != 0) {
                    $this->guardarAsientoPresupuestario($ejecutadoProveedor);
                }
            } else {
                if ($ejecutadoProveedor->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$ejecutadoProveedor->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoProveedor->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableProveedor->getId()] = $cuentaContableProveedor;
                }
            }
        } else {
            $erroresCuentaContableArray[$ordenPago->getProveedor()->getId()] = $ordenPago->getProveedor();
        }


        //  * ******************************************** */
        //     Ejecutado relacionado al Pago               */
        //  * ******************************************** */
        $this->generarEjecutadoPago($asientoContable, $ordenPago, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenPagoDeclaracionJurada $ordenPago
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromOrdenPagoDeclaracionJurada(OrdenPagoDeclaracionJurada $ordenPago, AsientoContable $asiento = null, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $declaracionJurada = $ordenPago->getDeclaracionJurada();

        switch ($declaracionJurada->getTipoDeclaracionJurada()) {
            case ConstanteTipoDeclaracionJurada::SICORE:

                $importeGanancias = $declaracionJurada->getImporteTotalRenglonesDeclaracionJuradaByTipoImpuesto(ConstanteTipoImpuesto::Ganancias);

                if ($importeGanancias > 0) {

                    $ejecutadoRetencionesGananciasTerceros = new Ejecutado();

                    $ejecutadoRetencionesGananciasTerceros->setAsientoContable($asiento);

                    // Obtengo la CuentaContable con codigo interno RETENCIONES_GANANCIAS_TERCEROS
                    $cuentaContableRetencionesGananciasTerceros = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::RETENCIONES_GANANCIAS_TERCEROS);

                    if ($cuentaContableRetencionesGananciasTerceros) {
                        $ejecutadoRetencionesGananciasTerceros->setCuentaContable($cuentaContableRetencionesGananciasTerceros);

                        $ejecutadoRetencionesGananciasTerceros->setCuentaPresupuestariaObjetoGasto($cuentaContableRetencionesGananciasTerceros->getCuentaPresupuestariaObjetoGasto());

                        $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoRetencionesGananciasTerceros, $importeGanancias, $cuentaContableRetencionesGananciasTerceros, !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE);

                        /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                        $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                                $ejecutadoRetencionesGananciasTerceros->getFechaEjecutado(), //
                                $ejecutadoRetencionesGananciasTerceros->getCuentaPresupuestariaEconomica()
                        );

                        if ($cuentaPresupuestaria != null) {
                            $ejecutadoRetencionesGananciasTerceros->setCuentaPresupuestaria($cuentaPresupuestaria);
                            if ($ejecutadoRetencionesGananciasTerceros->getMonto() != 0) {
                                $this->guardarAsientoPresupuestario($ejecutadoRetencionesGananciasTerceros);
                            }
                        } else {
                            if ($ejecutadoRetencionesGananciasTerceros->getCuentaPresupuestariaEconomica() != null) {
                                $erroresPresupuestoArray[$ejecutadoRetencionesGananciasTerceros->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoRetencionesGananciasTerceros->getCuentaPresupuestariaEconomica();
                            } else {
                                $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableRetencionesGananciasTerceros->getId()] = $cuentaContableRetencionesGananciasTerceros;
                            }
                        }
                    } else {
                        $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::RETENCIONES_GANANCIAS_TERCEROS] = 'C&oacute;digo interno RETENCIONES_GANANCIAS_TERCEROS';
                    }
                }

                $importeIVA = $declaracionJurada->getImporteTotalRenglonesDeclaracionJuradaByTipoImpuesto(ConstanteTipoImpuesto::IVA);

                if ($importeIVA > 0) {
                    $ejecutadoRetencionesIVATerceros = new Ejecutado();
                    $ejecutadoRetencionesIVATerceros->setAsientoContable($asiento);

                    // Obtengo la CuentaContable con codigo interno RETENCIONES_IVA_TERCEROS
                    $cuentaContableRetencionesIVATerceros = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::RETENCIONES_IVA_TERCEROS);

                    if ($cuentaContableRetencionesIVATerceros) {
                        $ejecutadoRetencionesIVATerceros->setCuentaContable($cuentaContableRetencionesIVATerceros);

                        $ejecutadoRetencionesIVATerceros->setCuentaPresupuestariaObjetoGasto($cuentaContableRetencionesIVATerceros->getCuentaPresupuestariaObjetoGasto());

                        $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoRetencionesIVATerceros, $importeIVA, $cuentaContableRetencionesIVATerceros, !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE);

                        /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                        $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                                $ejecutadoRetencionesIVATerceros->getFechaEjecutado(), //
                                $ejecutadoRetencionesIVATerceros->getCuentaPresupuestariaEconomica()
                        );

                        if ($cuentaPresupuestaria != null) {
                            $ejecutadoRetencionesIVATerceros->setCuentaPresupuestaria($cuentaPresupuestaria);
                            if ($ejecutadoRetencionesIVATerceros->getMonto() != 0) {
                                $this->guardarAsientoPresupuestario($ejecutadoRetencionesIVATerceros);
                            }
                        } else {
                            if ($ejecutadoRetencionesIVATerceros->getCuentaPresupuestariaEconomica() != null) {
                                $erroresPresupuestoArray[$ejecutadoRetencionesIVATerceros->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoRetencionesIVATerceros->getCuentaPresupuestariaEconomica();
                            } else {
                                $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableRetencionesIVATerceros->getId()] = $cuentaContableRetencionesIVATerceros;
                            }
                        }
                    } else {
                        $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::RETENCIONES_IVA_TERCEROS] = 'C&oacute;digo interno RETENCIONES_IVA_TERCEROS';
                    }
                }

                break;
            case ConstanteTipoDeclaracionJurada::SIJP:

                $importeSUSS = $declaracionJurada->getImporteTotalRenglonesDeclaracionJurada();

                if ($importeSUSS > 0) {

                    $ejecutadoRetencionesSIJPTerceros = new Ejecutado();
                    $ejecutadoRetencionesSIJPTerceros->setAsientoContable($asiento);

                    // Obtengo la CuentaContable con codigo interno RETENCIONES_SIJP_TERCEROS
                    $cuentaContableRetencionesSIJPTerceros = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::RETENCIONES_SIJP_TERCEROS);

                    if ($cuentaContableRetencionesSIJPTerceros) {
                        $ejecutadoRetencionesSIJPTerceros->setCuentaContable($cuentaContableRetencionesSIJPTerceros);

                        $ejecutadoRetencionesSIJPTerceros->setCuentaPresupuestariaObjetoGasto($cuentaContableRetencionesSIJPTerceros->getCuentaPresupuestariaObjetoGasto());

                        $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoRetencionesSIJPTerceros, $importeSUSS, $cuentaContableRetencionesSIJPTerceros, !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE);

                        /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                        $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                                $ejecutadoRetencionesSIJPTerceros->getFechaEjecutado(), //
                                $ejecutadoRetencionesSIJPTerceros->getCuentaPresupuestariaEconomica()
                        );

                        if ($cuentaPresupuestaria != null) {
                            $ejecutadoRetencionesSIJPTerceros->setCuentaPresupuestaria($cuentaPresupuestaria);
                            if ($ejecutadoRetencionesSIJPTerceros->getMonto() != 0) {
                                $this->guardarAsientoPresupuestario($ejecutadoRetencionesSIJPTerceros);
                            }
                        } else {
                            if ($ejecutadoRetencionesSIJPTerceros->getCuentaPresupuestariaEconomica() != null) {
                                $erroresPresupuestoArray[$ejecutadoRetencionesSIJPTerceros->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoRetencionesSIJPTerceros->getCuentaPresupuestariaEconomica();
                            } else {
                                $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableRetencionesSIJPTerceros->getId()] = $cuentaContableRetencionesSIJPTerceros;
                            }
                        }
                    } else {
                        $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::RETENCIONES_SIJP_TERCEROS] = 'C&oacute;digo interno RETENCIONES_SIJP_TERCEROS';
                    }
                }

                break;
            case ConstanteTipoDeclaracionJurada::IIBB:

                $importeRetencionesIIBB = $declaracionJurada->getImporteTotalRenglonesDeclaracionJuradaByTipoRenglon(ConstanteTipoRenglonDeclaracionJurada::COMPROBANTE_RETENCION_IMPUESTO_COMPRA);

                if ($importeRetencionesIIBB > 0) {
                    $ejecutadoRetencionesIIBBADepositar = new Ejecutado();
                    $ejecutadoRetencionesIIBBADepositar->setAsientoContable($asiento);

                    // Obtengo la CuentaContable con codigo interno RETENCIONES_IIBB_A_DEPOSITAR
                    $cuentaContableRetencionesIIBB = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::RETENCIONES_IIBB_A_DEPOSITAR);

                    if ($cuentaContableRetencionesIIBB) {
                        $ejecutadoRetencionesIIBBADepositar->setCuentaContable($cuentaContableRetencionesIIBB);
                        $ejecutadoRetencionesIIBBADepositar->setCuentaPresupuestariaObjetoGasto($cuentaContableRetencionesIIBB->getCuentaPresupuestariaObjetoGasto());

                        $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoRetencionesIIBBADepositar, $importeRetencionesIIBB, $cuentaContableRetencionesIIBB, !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE);

                        /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                        $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                                $ejecutadoRetencionesIIBBADepositar->getFechaEjecutado(), //
                                $ejecutadoRetencionesIIBBADepositar->getCuentaPresupuestariaEconomica()
                        );

                        if ($cuentaPresupuestaria != null) {
                            $ejecutadoRetencionesIIBBADepositar->setCuentaPresupuestaria($cuentaPresupuestaria);
                            if ($ejecutadoRetencionesIIBBADepositar->getMonto() != 0) {
                                $this->guardarAsientoPresupuestario($ejecutadoRetencionesIIBBADepositar);
                            }
                        } else {
                            if ($ejecutadoRetencionesIIBBADepositar->getCuentaPresupuestariaEconomica() != null) {
                                $erroresPresupuestoArray[$ejecutadoRetencionesIIBBADepositar->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoRetencionesIIBBADepositar->getCuentaPresupuestariaEconomica();
                            } else {
                                $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableRetencionesIIBB->getId()] = $cuentaContableRetencionesIIBB;
                            }
                        }
                    } else {
                        $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::RETENCIONES_IIBB_A_DEPOSITAR] = 'C&oacute;digo interno RETENCIONES_IIBB_A_DEPOSITAR';
                    }
                }

                break;
            default:
                break;
        }

        //  * ******************************************** */
        //     Ejecutado relacionado a CREDITOS_IMPOSITIVOS */
        //  * ******************************************** */

        $ejecutadoCreditosImpositivos = new Ejecutado();

        $ejecutadoCreditosImpositivos->setAsientoContable($asiento);

        // Obtengo la CuentaContable con codigo interno CREDITOS_IMPOSITIVOS
        $cuentaContableCreditosImpositivos = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::CREDITOS_IMPOSITIVOS);

        if ($cuentaContableCreditosImpositivos) {
            $ejecutadoCreditosImpositivos->setCuentaContable($cuentaContableCreditosImpositivos);

            $ejecutadoCreditosImpositivos->setCuentaPresupuestariaObjetoGasto($cuentaContableCreditosImpositivos->getCuentaPresupuestariaObjetoGasto());

            $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoCreditosImpositivos, $ordenPago->getTotalBruto(), $cuentaContableCreditosImpositivos, !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoCreditosImpositivos->getFechaEjecutado(), //
                    $ejecutadoCreditosImpositivos->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoCreditosImpositivos->setCuentaPresupuestaria($cuentaPresupuestaria);
                if ($ejecutadoCreditosImpositivos->getMonto() != 0) {
                    $this->guardarAsientoPresupuestario($ejecutadoCreditosImpositivos);
                }
            } else {
                if ($ejecutadoCreditosImpositivos->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$ejecutadoCreditosImpositivos->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoCreditosImpositivos->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableCreditosImpositivos->getId()] = $cuentaContableCreditosImpositivos;
                }
            }
        } else {
            $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::CREDITOS_IMPOSITIVOS] = 'C&oacute;digo interno CREDITOS_IMPOSITIVOS';
        }


        //  * ******************************************** */
        //     Ejecutado relacionado al Pago               */
        //  * ******************************************** */
        $this->generarEjecutadoPago($asiento, $ordenPago, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param DeclaracionJuradaIvaContribuyente $declaracionJurada
     * @param AsientoContable $asiento
     * @param boolean $esContraAsiento
     * @return type
     */
    public function crearEjecutadoFromDeclaracionJuradaIvaContribuyente(DeclaracionJuradaIvaContribuyente $declaracionJurada, AsientoContable $asiento = null, $esContraAsiento = false) {
        
    }

    /**
     * 
     * @param OrdenPagoDeclaracionJuradaIvaContribuyente $ordenPago
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromOrdenPagoDeclaracionJuradaIvaContribuyente(OrdenPagoDeclaracionJuradaIvaContribuyente $ordenPago, AsientoContable $asiento = null, $esContraAsiento = false) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        //  * ******************************************** */
        //     Ejecutado IVA a pagar                       */
        //  * ******************************************** */

        $ejecutadoIva = new Ejecutado();

        $ejecutadoIva->setAsientoContable($asiento);

        //IVA a pagar
        $configuracion_cuenta_iva_a_pagar = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_IVA_SALDO_A_PAGAR);
        $cuentaContableIvaAPagar = $configuracion_cuenta_iva_a_pagar->getCuentaContable();

        if ($cuentaContableIvaAPagar) {
            $ejecutadoIva->setCuentaContable($cuentaContableIvaAPagar);

            $ejecutadoIva->setCuentaPresupuestariaObjetoGasto($cuentaContableIvaAPagar->getCuentaPresupuestariaObjetoGasto());

            $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoIva, $ordenPago->getImporte(), $cuentaContableIvaAPagar, !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoIva->getFechaEjecutado(), //
                    $ejecutadoIva->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoIva->setCuentaPresupuestaria($cuentaPresupuestaria);
                if ($ejecutadoIva->getMonto() != 0) {
                    $this->guardarAsientoPresupuestario($ejecutadoIva);
                }
            } else {
                if ($ejecutadoIva->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$ejecutadoIva->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoIva->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableIvaAPagar->getId()] = $cuentaContableIvaAPagar;
                }
            }
        } else {
            $erroresCuentaContableArray[$cuentaContableIvaAPagar->getId()] = $cuentaContableIvaAPagar;
        }


        //  * ******************************************** */
        //     Ejecutado relacionado al Pago               */
        //  * ******************************************** */
        $this->generarEjecutadoPago($asiento, $ordenPago, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenPagoRenglonRetencionLiquidacion $ordenPago
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoFromOrdenPagoRenglonRetencionLiquidacion(OrdenPagoRenglonRetencionLiquidacion $ordenPago, AsientoContable $asiento = null, $esContraAsiento = false) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        foreach ($ordenPago->getRenglonesRetencionLiquidacion() as $renglonRetencionLiquidacion) {
            /* @var $renglonRetencionLiquidacion RenglonRetencionLiquidacion */

            $conceptoVersion = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConceptoVersion')->find($renglonRetencionLiquidacion->getIdConceptoVersion());
            $liquidacion = $emRRHH->getRepository('ADIFRecursosHumanosBundle:Liquidacion')->find($renglonRetencionLiquidacion->getIdLiquidacion());
            $concepto = $conceptoVersion->getConcepto();

            // Obtengo la CuentaContable con codigo interno CREDITOS_IMPOSITIVOS
            $cuentaContableConcepto = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->find($concepto->getIdCuentaContable());

            if ($cuentaContableConcepto) {
                $ejecutado = new Ejecutado();
                $ejecutado->setAsientoContable($asiento);

                $ejecutado->setCuentaContable($cuentaContableConcepto);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContableConcepto->getCuentaPresupuestariaObjetoGasto());

                $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutado, $renglonRetencionLiquidacion->getMonto(), $cuentaContableConcepto, !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $ejecutado->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                    if ($ejecutado->getMonto() != 0) {
                        $this->guardarAsientoPresupuestario($ejecutado);
                    }
                } else {
                    if ($ejecutado->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$ejecutado->getCuentaPresupuestariaEconomica()->getId()] = $ejecutado->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableConcepto->getId()] = $cuentaContableConcepto;
                    }
                }
            } else {
                $erroresCuentaContableArray[$concepto->getId()] = $concepto;
            }
        }

        //  * ******************************************** */
        //     Ejecutado relacionado al Pago               */
        //  * ******************************************** */
        $this->generarEjecutadoPago($asiento, $ordenPago, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param CobroRenglonCobranza $cobro
     * @param type $esImputacionCompleta
     * @param type $esContraasiento
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoParaCobranzaImputada(CobroRenglonCobranza $cobro, $esImputacionCompleta, $esContraasiento, AsientoContable $asiento = null) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresArray = array();
        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $comprobantes = $cobro->getComprobantes();

        if (!empty($comprobantes)) {
            /* @var $comprobante ComprobanteVenta */
            $comprobante = $comprobantes[0]; // Debería ser F, ND y C (xq las NC no generan asientos contables, sólo modifican la CC del cliente actualizando su deduda)
            $cancelado = $cobro->getMonto();
            $anticipo = ($cobro->getAnticipoCliente() != null ? $cobro->getAnticipoCliente()->getMonto() : 0);
            $total_asiento = $cancelado + $anticipo;

            //$hayBanco = $cobro->getMontoCheques() != $cobro->getMonto();
            $hayBanco = (abs($cobro->getMontoCheques() - $cobro->getMonto()) > 0.00000001);
            $hayCheque = $cobro->getMontoCheques() != 0;

            if ($esImputacionCompleta) {
                if ($hayBanco) {
                    /* @var $renglonCobro RenglonCobranza */
                    $renglonCobro = $cobro->getRenglonesCobranza()->first();
                    $cuentaBancaria = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($renglonCobro->getIdCuentaBancaria());
                    $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cuentaBancaria->getIdCuentaContable());

                    if (!$cuentaContable) {
                        $erroresArray[$cuentaBancaria->getId()] = 'La cuenta bancaria ' . $cuentaBancaria . ' no posee una cuenta contable asociada.';
                    }
                    $monto_para_el_asiento = $total_asiento - $cobro->getMontoCheques();
                }
                if ($hayCheque) {

                    $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::VALORES_A_DEPOSITAR);
                    $codigo_cuenta = $cuentaContable->getCodigoCuentaContable();
                    $monto_para_el_asiento = $hayBanco ? $cobro->getMontoCheques() : $cobro->getMontoCheques() + $anticipo;
                }
            } else {
                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::COBRANZAS_A_IMPUTAR);
                $monto_para_el_asiento = $total_asiento;
            }

            // Cuenta banco o cobranzas a imputar                        
            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            if ($cuentaContable) {
                $ejecutado->setCuentaContable($cuentaContable);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                if ($cuentaContable->getCuentaPresupuestariaEconomica()) {

                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            !$esContraasiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, //
                            $cuentaContable
                    );

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $cuentaContable->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                    $ejecutado->setMonto(abs($monto_para_el_asiento)); //$total_asiento));

                    $this->guardarAsientoPresupuestario($ejecutado);
                } else {
                    if ($cuentaPresupuestariaEconomica != null) {
                        $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDestino->getId()] = $cuentaContable;
                    }
                }
            } else {
                $erroresCuentaContableArray[$cuentaContable->getId()] = $cuentaContable;
            }

            $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->find($comprobante->getCliente()->getId());

            $cuenta_deudores = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cliente->getIdCuentaContable());


            // Ejecutado deudores por venta
            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            if ($cuenta_deudores) {
                $ejecutado->setCuentaContable($cuenta_deudores);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_deudores->getCuentaPresupuestariaObjetoGasto());

                if ($cuenta_deudores->getCuentaPresupuestariaEconomica()) {

                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            !$esContraasiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, //
                            $cuenta_deudores
                    );

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $cuenta_deudores->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                    $ejecutado->setMonto(abs($total_asiento)); //$cancelado));

                    $this->guardarAsientoPresupuestario($ejecutado);
                } else {
                    if ($cuentaPresupuestariaEconomica != null) {
                        $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuenta_deudores->getId()] = $cuenta_deudores;
                    }
                }

                // Tengo que ejecutar lo devengado en ventas brutas cuando se genero el comprobante
                $devengado = $emContable->getRepository('ADIFContableBundle:DevengadoVenta')->findOneByRenglonComprobanteVenta($comprobante->getRenglonesComprobante()->first());
                if ($devengado) {
                    /* @var $devengado DevengadoVenta */
                    $ejecutado = new Ejecutado();
                    $ejecutado->setDevengado($devengado);
                    $ejecutado->setAsientoContable($asiento);
                    $ejecutado->setCuentaContable($devengado->getCuentaContable());
                    $ejecutado->setCuentaPresupuestaria($devengado->getCuentaPresupuestaria());
                    $ejecutado->setCuentaPresupuestariaEconomica($devengado->getCuentaPresupuestariaEconomica());
                    $ejecutado->setCuentaPresupuestariaObjetoGasto($devengado->getCuentaPresupuestariaObjetoGasto());
                    $ejecutado->setMonto(abs($total_asiento)); //$cancelado));

                    $this->guardarAsientoPresupuestario($ejecutado);
                }
            } else {
                $erroresCuentaContableArray[$cuenta_deudores->getId()] = $cuenta_deudores;
            }

//            if ($anticipo != 0) {
//                // Renglon asiento del básico
//                $configuracion_cuenta_anticipo_clientes = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_CLIENTES);
//                $cuenta_anticipos = $configuracion_cuenta_anticipo_clientes->getCuentaContable();
//
//                // Ejecutado anticipo
//                $ejecutado = new Ejecutado();
//                $ejecutado->setAsientoContable($asiento);
//
//                if ($cuenta_anticipos) {
//                    $ejecutado->setCuentaContable($cuenta_anticipos);
//
//                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_anticipos->getCuentaPresupuestariaObjetoGasto());
//
//                    if ($cuenta_anticipos->getCuentaPresupuestariaEconomica()) {
//
//                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
//                                !$esContraasiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, //
//                                $cuenta_anticipos
//                        );
//
//                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
//                    }
//
//                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
//                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
//                            $ejecutado->getFechaEjecutado(), //
//                            $cuenta_anticipos->getCuentaPresupuestariaEconomica()
//                    );
//
//                    if ($cuentaPresupuestaria != null) {
//                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
//
//                        $ejecutado->setMonto(abs($anticipo));
//
//                        $this->guardarAsientoPresupuestario($ejecutado);
//                    } else {
//                        if ($cuentaPresupuestariaEconomica != null) {
//                            $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
//                        } else {
//                            $erroresCuentaPresupuestariaEconomicaArray[$cuenta_anticipos->getId()] = $cuenta_anticipos;
//                        }
//                    }
//                } else {
//                    $erroresCuentaContableArray[$cuenta_anticipos->getId()] = $cuenta_anticipos;
//                }
//            }
        } else {
            $erroresArray[$cobro->getId()] = 'El cobro producto de la imputación no tiene comprobante asociado.';
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresArray) || !empty($erroresCuentaContableArray) || !empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param type $esContraAsiento
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoParaCobranzaPreImputada($renglones, $esContraAsiento, $tipo, AsientoContable $asiento = null) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $total_asiento = 0;
        foreach ($renglones as $renglon) {
            $total_asiento += $renglon->getMonto();
        }

        if ($tipo == 'banco') {
            $cuentaBancaria = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($renglon->getIdCuentaBancaria());
            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cuentaBancaria->getIdCuentaContable());
        } else {
            $cuenta_contable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::VALORES_A_DEPOSITAR);
        }

        // Cuenta banco
        $ejecutado = new Ejecutado();
        $ejecutado->setAsientoContable($asiento);

        if ($cuenta_contable) {
            $ejecutado->setCuentaContable($cuenta_contable);

            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_contable->getCuentaPresupuestariaObjetoGasto());

            if ($cuenta_contable->getCuentaPresupuestariaEconomica()) {
                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                        //ConstanteTipoOperacionContable::DEBE, //
                        (!$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER), //
                        $cuenta_contable
                );

                $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuenta_contable->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                //$ejecutado->setMonto($esContraAsiento ? ($total_asiento * -1) : $total_asiento);
                $ejecutado->setMonto(abs($total_asiento));

                $this->guardarAsientoPresupuestario($ejecutado);
            } else {
                if ($cuentaPresupuestariaEconomica != null) {
                    $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuenta_contable->getId()] = $cuenta_contable;
                }
            }
        } else {
            $erroresCuentaContableArray[$cuenta_contable->getId()] = $cuenta_contable;
        }

        $cuenta_cobranzas = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::COBRANZAS_A_IMPUTAR);

        // Cuenta cobranzas a imputar
        $ejecutado = new Ejecutado();
        $ejecutado->setAsientoContable($asiento);

        if ($cuenta_cobranzas) {
            $ejecutado->setCuentaContable($cuenta_cobranzas);

            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_cobranzas->getCuentaPresupuestariaObjetoGasto());

            if ($cuenta_contable->getCuentaPresupuestariaEconomica()) {
                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                        //ConstanteTipoOperacionContable::HABER, //
                        (!$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE), //
                        $cuenta_cobranzas
                );

                $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuenta_cobranzas->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                //$ejecutado->setMonto($esContraAsiento ? ($total_asiento * -1) : $total_asiento);
                $ejecutado->setMonto(abs($total_asiento));

                $this->guardarAsientoPresupuestario($ejecutado);
            } else {
                if ($cuentaPresupuestariaEconomica != null) {
                    $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuenta_cobranzas->getId()] = $cuenta_cobranzas;
                }
            }
        } else {
            $erroresCuentaContableArray[$cuenta_cobranzas->getId()] = $cuenta_cobranzas;
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresCuentaContableArray) || !empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param type $cobro
     * @param type $esContraAsiento
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoParaCobranzaImputadaConAnticipo($cobro, $esContraAsiento = false, AsientoContable $asiento = null) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresArray = array();
        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $total_asiento = $cobro->getMonto();

        $comprobantes = $cobro->getComprobantes();

        if (!empty($comprobantes)) {
            $comprobante = $comprobantes[0];

            $configuracion_cuenta_anticipo_clientes = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_CLIENTES);
            $cuenta_anticipos = $configuracion_cuenta_anticipo_clientes->getCuentaContable();

            // Ejecutado anticipo
            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            if ($cuenta_anticipos) {
                $ejecutado->setCuentaContable($cuenta_anticipos);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_anticipos->getCuentaPresupuestariaObjetoGasto());

                if ($cuenta_anticipos->getCuentaPresupuestariaEconomica()) {

                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            !$esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, //
                            $cuenta_anticipos
                    );

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $cuenta_anticipos->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                    $ejecutado->setMonto(abs($total_asiento));


                    $this->guardarAsientoPresupuestario($ejecutado);
                } else {
                    if ($cuentaPresupuestariaEconomica != null) {
                        $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuenta_anticipos->getId()] = $cuenta_anticipos;
                    }
                }
            } else {
                $erroresCuentaContableArray[$cuenta_anticipos->getId()] = $cuenta_anticipos;
            }

            $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->find($comprobante->getCliente()->getId());

            $cuenta_deudores = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cliente->getIdCuentaContable());

            // Ejecutado deudores por venta
            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            if ($cuenta_deudores) {
                $ejecutado->setCuentaContable($cuenta_deudores);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_deudores->getCuentaPresupuestariaObjetoGasto());

                if ($cuenta_deudores->getCuentaPresupuestariaEconomica()) {

                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            !$esContraAsiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, //
                            $cuenta_deudores
                    );

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $cuenta_deudores->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                    $ejecutado->setMonto(abs($total_asiento));

                    $this->guardarAsientoPresupuestario($ejecutado);
                } else {
                    if ($cuentaPresupuestariaEconomica != null) {
                        $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuenta_deudores->getId()] = $cuenta_deudores;
                    }
                }

                // Tengo que ejecutar lo devengado en ventas brutas cuando se genero el comprobante
                $devengado = $emContable->getRepository('ADIFContableBundle:DevengadoVenta')->findOneByRenglonComprobanteVenta($comprobante->getRenglonesComprobante()->first());
                if ($devengado) {
                    /* @var $devengado DevengadoVenta */
                    $ejecutado = new Ejecutado();
                    $ejecutado->setDevengado($devengado);
                    $ejecutado->setAsientoContable($asiento);
                    $ejecutado->setCuentaContable($devengado->getCuentaContable());
                    $ejecutado->setCuentaPresupuestaria($devengado->getCuentaPresupuestaria());
                    $ejecutado->setCuentaPresupuestariaEconomica($devengado->getCuentaPresupuestariaEconomica());
                    $ejecutado->setCuentaPresupuestariaObjetoGasto($devengado->getCuentaPresupuestariaObjetoGasto());
                    $ejecutado->setMonto(abs($total_asiento));
                    $this->guardarAsientoPresupuestario($ejecutado);
                }
            } else {
                $erroresCuentaContableArray[$cuenta_deudores->getId()] = $cuenta_deudores;
            }
        } else {
            $erroresArray[$anticipo->getId()] = 'El cobro producto de la imputación no tiene comprobante asociado.';
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresArray) || !empty($erroresCuentaContableArray) || !empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param OrdenPagoGeneral $ordenPagoGeneral
     * @param type $esContraAsiento
     * @param AsientoContable $asientoContable
     * @return type
     */
    public function crearEjecutadoFromOrdenPagoGeneral(OrdenPagoGeneral $ordenPagoGeneral, $esContraAsiento, AsientoContable $asientoContable) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $monto = $ordenPagoGeneral->getImporte();

        if ($esContraAsiento) {
            $monto *= -1;
        }

        //  * ******************************************** */
        //     Ejecutado relacionado al Concepto          */
        //  * ******************************************** */

        if (!$esContraAsiento) {
            $ejecutado = new \ADIF\ContableBundle\Entity\EjecutadoOrdenPagoGeneral();
            $ejecutado->setAsientoContable($asientoContable);

            $cuentaContableConcepto = $ordenPagoGeneral->getConceptoOrdenPago()->getCuentaContable();

            if ($cuentaContableConcepto != null) {
                $ejecutado->setCuentaContable($cuentaContableConcepto);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContableConcepto->getCuentaPresupuestariaObjetoGasto());

                $ejecutado->setCuentaPresupuestariaEconomica(
                        $cuentaContableConcepto->getCuentaPresupuestariaEconomica()
                );

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $cuentaContableConcepto->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                } else {
                    if ($cuentaContableConcepto->getCuentaPresupuestariaEconomica() != null) {
                        $erroresPresupuestoArray[$cuentaContableConcepto->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContableConcepto->getCuentaPresupuestariaEconomica();
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableConcepto->getId()] = $cuentaContableConcepto;
                    }
                }
            } else {
                $erroresCuentaContableArray[$ordenPagoGeneral->getConceptoOrdenPago()->getId()] = $ordenPagoGeneral->getConceptoOrdenPago();
            }

            $ejecutado->setOrdenPagoGeneral($ordenPagoGeneral);

            $ejecutado->setMonto($monto);

            $this->guardarAsientoPresupuestario($ejecutado);
        } else {
            // Obtengo el EjecutadoOrdenPagoGeneral si es que existe
            $ejecutado = $emContable->getRepository('ADIFContableBundle:EjecutadoOrdenPagoGeneral')
                    ->findOneBy(array('ordenPagoGeneral' => $ordenPagoGeneral));

            $emContable->remove($ejecutado);
        }


        //  * ******************************************** */
        //     Ejecutado relacionado al Pago               */
        //  * ******************************************** */
        $this->generarEjecutadoPago($asientoContable, $ordenPagoGeneral, $emContable, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param AsientoContable $asientoContable
     * @return type
     */
    public function crearEjecutadoFromAsientoManual(AsientoContable $asientoContable) {
        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        // Por cada renglon del asiento contable
        foreach ($asientoContable->getRenglonesAsientoContable() as $renglonAsientoContable) {

            //  * ************* */
            //     Ejecutado    */
            //  * ************* */

            $ejecutado = new Ejecutado();

            $cuentaContable = $renglonAsientoContable->getCuentaContable();

            if ($cuentaContable) {

                $ejecutado->setCuentaContable($cuentaContable);

                $ejecutado->setAsientoContable($asientoContable);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                if ($cuentaContable->getCuentaPresupuestariaEconomica()) {
                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            $renglonAsientoContable->getTipoOperacionContable()->getDenominacion(), //
                            $cuentaContable
                    );

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $cuentaContable->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                    $ejecutado->setMonto($renglonAsientoContable->getImporteMCL());

                    $this->guardarAsientoPresupuestario($ejecutado);
                } else {
                    if ($cuentaPresupuestariaEconomica != null) {
                        $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                    }
                }
            } else {
                $erroresCuentaContableArray[$renglonAsientoContable->getId()] = $renglonAsientoContable;
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param type $erroresPresupuestoArray
     * @param type $erroresCuentaPresupuestariaEconomicaArray
     * @param type $erroresCuentaContableArray
     * @param type $errorMsg
     * @return string
     */
    private function getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg = '') {

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<div class="error-presupuestario" style="padding-left: 3em; margin-top: .5em">';
        }

        if (!empty($erroresPresupuestoArray)) {
            $errorMsg .= '<span class="error-title">'
                    . 'No se encontr&oacute; un presupuesto relacionado a las siguientes '
                    . 'cuentas presupuestarias econ&oacute;micas: </span>';

            $errorMsg .= '<ul>';

            foreach ($erroresPresupuestoArray as $codigo) {
                $errorMsg .= '<li>' . $codigo . ' </li>';
            }

            $errorMsg .= '</ul>';
        }

        if (!empty($erroresCuentaContableArray)) {

            $errorMsg .= '<span class="error-title">'
                    . 'No se encontr&oacute; la cuenta contable relacionada a los '
                    . 'siguientes bienes econ&oacute;micos: </span>';

            $errorMsg .= '<ul>';

            foreach ($erroresCuentaContableArray as $codigo) {
                $errorMsg .= '<li>' . $codigo . ' </li>';
            }

            $errorMsg .= '</ul>';
        }

        if (!empty($erroresCuentaPresupuestariaEconomicaArray)) {

            $errorMsg .= '<span class="error-title">'
                    . 'Las siguientes cuentas contables no tienen una '
                    . 'cuenta presupuestaria econ&oacute;mica asociada: </span>';

            $errorMsg .= '<ul>';

            foreach ($erroresCuentaPresupuestariaEconomicaArray as $codigo) {
                $errorMsg .= '<li>' . $codigo . ' </li>';
            }

            $errorMsg .= '</ul>';
        }

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '</div>';
        }

        return $errorMsg;
    }

    /**
     * 
     * @param type $codigoFinanciamiento
     * @param CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica
     * @return type
     */
    private function getCuentaPresupuestariaEconomicaByCodigoFinanciamiento($codigoFinanciamiento, CuentaPresupuestariaEconomica $cuentaPresupuestariaEconomica) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $codigoCuentaPresupuestaria = $cuentaPresupuestariaEconomica->getCodigo();

        $codigoCuentaPresupuestariaNuevo = substr_replace($codigoCuentaPresupuestaria, $codigoFinanciamiento, 0, 1);

        $cuentaPresupuestariaEconomicaResultado = $emContable->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')
                ->findOneByCodigo($codigoCuentaPresupuestariaNuevo);

        return $cuentaPresupuestariaEconomicaResultado;
    }

    /**
     * 
     * @param type $imputacionCuentaContable
     * @param CuentaContable $cuentaContable
     * @return type
     */
    private function getCuentaPresupuestariaEconomicaByImputacionYCuentaContable($imputacionCuentaContable, CuentaContable $cuentaContable) {
		
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

		if (!$cuentaContable->getEsImputable()) {
			//$error = 5 / 0; exit;
			throw new \Exception("La cuenta contable \"$cuentaContable\" no es imputable.<br>Por favor, revise las configuraciones del plan de cuentas.");
		}
		
		if ($cuentaContable->getCuentaPresupuestariaEconomica() == null) {
			//$error = 5 / 0; exit;
			throw new \Exception("La cuenta contable \"$cuentaContable\" no tiene asociada una cuenta presupuestaria económica.<br>Por favor, revise las configuraciones del plan de cuentas.");
		}
		
        $codigoCuentaPresupuestaria = $cuentaContable->getCuentaPresupuestariaEconomica()->getCodigo();

        if ($imputacionCuentaContable == ConstanteTipoOperacionContable::DEBE) {
            $codigoFinanciamiento = 2;
        } else {
            $codigoFinanciamiento = 1;
        }

        $codigoCuentaPresupuestariaNuevo = substr_replace($codigoCuentaPresupuestaria, $codigoFinanciamiento, 0, 1);

        $cuentaPresupuestariaEconomicaResultado = $emContable->getRepository('ADIFContableBundle:CuentaPresupuestariaEconomica')->findOneByCodigo($codigoCuentaPresupuestariaNuevo);

        return $cuentaPresupuestariaEconomicaResultado;
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
     * @param array $erroresCuentaPresupuestariaEconomicaSinSaldoArray
     */
    public function mostrarMensajeErrorCuentaPresupuestariaEconomicaSinSaldo($erroresCuentaPresupuestariaEconomicaSinSaldoArray) {

        $errorMsg = '<span>Existen cuentas presupuestarias sin saldo:</span>';

        $errorMsg .= '<div class="error-presupuestario" style="padding-left: 3em; margin-top: .5em">';

        $errorMsg .= '<ul>';

        foreach ($erroresCuentaPresupuestariaEconomicaSinSaldoArray as $error) {
            $errorMsg .= '<li>' . $error . ' </li>';
        }

        $errorMsg .= '</ul>';

        $errorMsg .= '</div>';

        $this->container->get('request')->getSession()->getFlashBag()->add('warning', $errorMsg);
    }

    /**
     * 
     * @param CobroRenglonCobranza $cobro
     * @param type $esContraasiento
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoParaAnticipoCreado(CobroRenglonCobranza $cobro, $esImputacionCompleta, $esContraasiento, AsientoContable $asiento = null) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresArray = array();
        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $cliente = $cobro->getAnticipoCliente()->getCliente();
        $anticipo = $cobro->getAnticipoCliente();
        $total_asiento = $anticipo->getMonto();

        /* @var $renglonCobro RenglonCobranza */

        //$hayBanco = $cobro->getMontoCheques() != $cobro->getAnticipoCliente()->getMonto();//$cobro->getMonto();
        $hayBanco = (abs($cobro->getMontoCheques() - $total_asiento) > 0.00000001);
        $hayCheque = $cobro->getMontoCheques() != 0;
        if (!$esContraasiento && $esImputacionCompleta) {
            if ($hayBanco) {

                $renglonCobro = $cobro->getRenglonesCobranza()->first();

                $cuentaBancaria = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($renglonCobro->getIdCuentaBancaria());
                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cuentaBancaria->getIdCuentaContable());


//                else {
//                    $cuentaBancaria = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($renglonCobro->getIdCuentaBancaria());
//                    $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cuentaBancaria->getIdCuentaContable());
//                }

                if (!$cuentaContable) {
                    $erroresArray[$cuentaBancaria->getId()] = 'La cuenta bancaria ' . $cuentaBancaria . ' no posee una cuenta contable asociada.';
                }

                // Cuenta banco o cobranzas a imputar                        
                $ejecutado = new Ejecutado();
                $ejecutado->setAsientoContable($asiento);

                if ($cuentaContable) {
                    $ejecutado->setCuentaContable($cuentaContable);

                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    if ($cuentaContable->getCuentaPresupuestariaEconomica()) {

                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                (!$esContraasiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER), //
                                $cuentaContable
                        );

                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                    }

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $ejecutado->getFechaEjecutado(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                        //$ejecutado->setMonto($esContraasiento ? ($total_asiento * -1) : $total_asiento);
                        $ejecutado->setMonto(abs($total_asiento - $cobro->getMontoCheques()));

                        $this->guardarAsientoPresupuestario($ejecutado);
                    } else {
                        if ($cuentaPresupuestariaEconomica != null) {
                            $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDestino->getId()] = $cuentaContable;
                        }
                    }
                } else {
                    $erroresCuentaContableArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }

            if ($hayCheque) {
                $cuentaBancaria = ConstanteCodigoInternoCuentaContable::VALORES_A_DEPOSITAR;
                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cuentaBancaria);


                if (!$cuentaContable) {
                    $erroresArray[$cuentaBancaria->getId()] = 'La cuenta bancaria ' . $cuentaBancaria . ' no posee una cuenta contable asociada.';
                }

                // Cuenta banco o cobranzas a imputar                        
                $ejecutado = new Ejecutado();
                $ejecutado->setAsientoContable($asiento);

                if ($cuentaContable) {
                    $ejecutado->setCuentaContable($cuentaContable);

                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    if ($cuentaContable->getCuentaPresupuestariaEconomica()) {

                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                (!$esContraasiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER), //
                                $cuentaContable
                        );

                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                    }

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $ejecutado->getFechaEjecutado(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                        //$ejecutado->setMonto($esContraasiento ? ($total_asiento * -1) : $total_asiento);
                        $ejecutado->setMonto(abs($cobro->getMontoCheques()));

                        $this->guardarAsientoPresupuestario($ejecutado);
                    } else {
                        if ($cuentaPresupuestariaEconomica != null) {
                            $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDestino->getId()] = $cuentaContable;
                        }
                    }
                } else {
                    $erroresCuentaContableArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }
        } else {


            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::COBRANZAS_A_IMPUTAR);

            if (!$cuentaContable) {
                $erroresArray[$cuentaBancaria->getId()] = 'La cuenta bancaria ' . $cuentaBancaria . ' no posee una cuenta contable asociada.';
            }

            // Cuenta banco o cobranzas a imputar                        
            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            if ($cuentaContable) {
                $ejecutado->setCuentaContable($cuentaContable);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                if ($cuentaContable->getCuentaPresupuestariaEconomica()) {

                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            (!$esContraasiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER), //
                            $cuentaContable
                    );

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $cuentaContable->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                    //$ejecutado->setMonto($esContraasiento ? ($total_asiento * -1) : $total_asiento);
                    $ejecutado->setMonto(abs($total_asiento));

                    $this->guardarAsientoPresupuestario($ejecutado);
                } else {
                    if ($cuentaPresupuestariaEconomica != null) {
                        $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDestino->getId()] = $cuentaContable;
                    }
                }
            } else {
                $erroresCuentaContableArray[$cuentaContable->getId()] = $cuentaContable;
            }
        }

//        // Renglon asiento del básico
//        $configuracion_cuenta_anticipo_clientes = $emRRHH
//                ->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')
//                ->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_CLIENTES);
//
//        $cuenta_anticipos = $configuracion_cuenta_anticipo_clientes->getCuentaContable();
//
//        // Ejecutado anticipo
//        $ejecutado = new Ejecutado();
//        $ejecutado->setAsientoContable($asiento);
//
//        if ($cuenta_anticipos) {
//            $ejecutado->setCuentaContable($cuenta_anticipos);
//
//            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_anticipos->getCuentaPresupuestariaObjetoGasto());
//
//            if ($cuenta_anticipos->getCuentaPresupuestariaEconomica()) {
//
//                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
//                        (!$esContraasiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE), //
//                        $cuenta_anticipos
//                );
//
//                $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
//            }
//
//            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
//            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
//                    $ejecutado->getFechaEjecutado(), //
//                    $cuenta_anticipos->getCuentaPresupuestariaEconomica()
//            );
//
//            if ($cuentaPresupuestaria != null) {
//                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
//
//                $ejecutado->setMonto(abs($total_asiento));
//
//                $this->guardarAsientoPresupuestario($ejecutado);
//            } else {
//                if ($cuentaPresupuestariaEconomica != null) {
//                    $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
//                } else {
//                    $erroresCuentaPresupuestariaEconomicaArray[$cuenta_anticipos->getId()] = $cuenta_anticipos;
//                }
//            }
//        } else {
//            $erroresCuentaContableArray[$cuenta_anticipos->getId()] = $cuenta_anticipos;
//        }

        $cuenta_deudores = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cliente->getIdCuentaContable());


        // Ejecutado deudores por venta
        $ejecutado = new Ejecutado();
        $ejecutado->setAsientoContable($asiento);

        if ($cuenta_deudores) {
            $ejecutado->setCuentaContable($cuenta_deudores);

            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_deudores->getCuentaPresupuestariaObjetoGasto());

            if ($cuenta_deudores->getCuentaPresupuestariaEconomica()) {

                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                        !$esContraasiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, //
                        $cuenta_deudores
                );

                $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuenta_deudores->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                $ejecutado->setMonto(abs($total_asiento));

                $this->guardarAsientoPresupuestario($ejecutado);
            } else {
                if ($cuentaPresupuestariaEconomica != null) {
                    $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuenta_deudores->getId()] = $cuenta_deudores;
                }
            }

            // Tengo que ejecutar lo devengado en ventas brutas cuando se genero el comprobante
//            $devengado = $emContable->getRepository('ADIFContableBundle:DevengadoVenta')->findOneByRenglonComprobanteVenta($comprobante->getRenglonesComprobante()->first());
//            if ($devengado) {
//                /* @var $devengado DevengadoVenta */
//                $ejecutado = new Ejecutado();
//                $ejecutado->setDevengado($devengado);
//                $ejecutado->setAsientoContable($asiento);
//                $ejecutado->setCuentaContable($devengado->getCuentaContable());
//                $ejecutado->setCuentaPresupuestaria($devengado->getCuentaPresupuestaria());
//                $ejecutado->setCuentaPresupuestariaEconomica($devengado->getCuentaPresupuestariaEconomica());
//                $ejecutado->setCuentaPresupuestariaObjetoGasto($devengado->getCuentaPresupuestariaObjetoGasto());
//                $ejecutado->setMonto(abs($total_asiento));
//                $this->guardarAsientoPresupuestario($ejecutado);
//            }
        } else {
            $erroresCuentaContableArray[$cuenta_deudores->getId()] = $cuenta_deudores;
        }


        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresArray) || !empty($erroresCuentaContableArray) || !empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param EjercicioContable $ejercicioContableOrigen
     * @param EjercicioContable $ejercicioContableDestino
     * @return type
     */
    public function transferirAsientosPresupuestariosEntreEjerciciosContables(EjercicioContable $ejercicioContableOrigen, EjercicioContable $ejercicioContableDestino) {

        ini_set('max_execution_time', 0);

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $fechaAsientoPresupuestario = $ejercicioContableDestino->getFechaInicio();


        // Si el ejercicio contable origen fue cerrado al menos una vez
        if ($ejercicioContableOrigen->getCantidadCierres() > 0) {

            // Obtengo todos los provisorios del ejercicio origen
            $provisorios = $emContable->getRepository('ADIFContableBundle:Provisorio')
                    ->getProvisoriosByEjercicio($ejercicioContableOrigen->getDenominacionEjercicio());
        } else {

            // Obtengo los provisorios con saldo del ejercicio origen
            $provisorios = $emContable->getRepository('ADIFContableBundle:Provisorio')
                    ->getProvisoriosConSaldoByEjercicio($ejercicioContableOrigen->getDenominacionEjercicio());
        }

        foreach ($provisorios as $provisorio) {

            /* @var $provisorioNuevo \ADIF\ContableBundle\Entity\Provisorio */
            $provisorioNuevo = null;

            // Si el ejercicio contable origen fue cerrado al menos una vez
            if ($ejercicioContableOrigen->getCantidadCierres() > 0) {

                // Busco si existe un provisorio asociado al provisorio origen
                $provisorioNuevo = $emContable->getRepository('ADIFContableBundle:Provisorio')
                        ->getProvisorioByProvisorioOrigen($provisorio);
            }

            // Si no se encontraron resultados
            if (!$provisorioNuevo) {

                // Si el provisorio origen tiene saldo
                if ($provisorio->getSaldo() > 0) {

                    $provisorioNuevo = clone $provisorio;

                    $provisorioNuevo->setProvisorioOrigen($provisorio);
                    $provisorioNuevo->setFechaProvisorio($fechaAsientoPresupuestario);

                    $cuentaPresupuestaria = $emContable->getRepository('ADIFContableBundle:CuentaPresupuestaria')
                            ->getCuentaPresupuestariaByEjercicioYCuentaEconomica($ejercicioContableDestino, $provisorio->getCuentaPresupuestariaEconomica());


                    if ($cuentaPresupuestaria != null) {

                        $provisorioNuevo->setCuentaPresupuestaria($cuentaPresupuestaria);
                    } else {

                        $erroresPresupuestoArray[$provisorio->getCuentaPresupuestariaEconomica()->getId()] = $provisorio->getCuentaPresupuestariaEconomica();
                    }
                }
            }
            // Sino, si se encontraron resultados
            else {

                $provisorioNuevo->setMonto($provisorio->getSaldo());
            }


            if ($provisorioNuevo) {

                $this->guardarAsientoPresupuestario($provisorioNuevo);
            }
        }



        // Obtengo todos los definitivos del ejercicio origen
        $definitivosEjercicioOrigen = $emContable->getRepository('ADIFContableBundle:Definitivo')
                ->getDefinitivosByEjercicio($ejercicioContableOrigen->getDenominacionEjercicio());

        foreach ($definitivosEjercicioOrigen as $definitivo) {

            /* @var $definitivoNuevo \ADIF\ContableBundle\Entity\Definitivo */
            $definitivoNuevo = null;

            // Si el ejercicio contable origen fue cerrado al menos una vez
            if ($ejercicioContableOrigen->getCantidadCierres() > 0) {

                // Busco si existe un definitivo asociado al definitivo origen
                $definitivoNuevo = $emContable->getRepository('ADIFContableBundle:Definitivo')
                        ->getDefinitivoByDefinitivoOrigen($definitivo);
            }

            // Si no se encontraron resultados
            if (!$definitivoNuevo) {

                // Si el definitivo origen tiene saldo
                if ($definitivo->getSaldo() > 0) {

                    $definitivoNuevo = clone $definitivo;

                    $definitivoNuevo->setDefinitivoOrigen($definitivo);
                    $definitivoNuevo->setFechaDefinitivo($fechaAsientoPresupuestario);

                    $cuentaPresupuestaria = $emContable->getRepository('ADIFContableBundle:CuentaPresupuestaria')
                            ->getCuentaPresupuestariaByEjercicioYCuentaEconomica($ejercicioContableDestino, $definitivo->getCuentaPresupuestariaEconomica());

                    if ($cuentaPresupuestaria != null) {

                        $definitivoNuevo->setCuentaPresupuestaria($cuentaPresupuestaria);
                    } else {

                        $erroresPresupuestoArray[$definitivo->getCuentaPresupuestariaEconomica()->getId()] = $definitivo->getCuentaPresupuestariaEconomica();
                    }
                }
            }

            if ($definitivoNuevo) {

                $definitivoNuevo->setMonto($definitivo->getSaldo());

                $this->guardarAsientoPresupuestario($definitivoNuevo);
            }
        }


        // Obtengo todos los definitivos del ejercicio destino
        $definitivosEjercicioDestino = $emContable->getRepository('ADIFContableBundle:Definitivo')
                ->getDefinitivosByEjercicio($ejercicioContableDestino->getDenominacionEjercicio());

        foreach ($definitivosEjercicioDestino as $definitivo) {

            $cuentaPresupuestaria = $emContable->getRepository('ADIFContableBundle:CuentaPresupuestaria')
                    ->getCuentaPresupuestariaByEjercicioYCuentaEconomica($ejercicioContableDestino, $definitivo->getCuentaPresupuestariaEconomica());

            if ($cuentaPresupuestaria != null) {

                $definitivo->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {

                $erroresPresupuestoArray[$definitivo->getCuentaPresupuestariaEconomica()->getId()] = $definitivo->getCuentaPresupuestariaEconomica();
            }
        }


        // Si el ejercicio contable origen fue cerrado al menos una vez
        if ($ejercicioContableOrigen->getCantidadCierres() > 0) {

            // Obtengo todos los devengados del ejercicio origen
            $devengados = $emContable->getRepository('ADIFContableBundle:Devengado')
                    ->getDevengadosByEjercicio($ejercicioContableOrigen->getDenominacionEjercicio());
        } else {

            // Obtengo los devengados con saldo del ejercicio origen
            $devengados = $emContable->getRepository('ADIFContableBundle:Devengado')
                    ->getDevengadosConSaldoByEjercicio($ejercicioContableOrigen->getDenominacionEjercicio());
        }

        foreach ($devengados as $devengado) {

            /* @var $devengadoNuevo \ADIF\ContableBundle\Entity\Devengado */
            $devengadoNuevo = null;

            // Si el ejercicio contable origen fue cerrado al menos una vez
            if ($ejercicioContableOrigen->getCantidadCierres() > 0) {

                // Busco si existe un devengado asociado al devengado origen
                $devengadoNuevo = $emContable->getRepository('ADIFContableBundle:Devengado')
                        ->getDevengadoByDevengadoOrigen($devengado);
            }

            // Si no se encontraron resultados
            if (!$devengadoNuevo) {

                // Si el devengado origen tiene saldo
                if ($devengado->getSaldo() > 0) {

                    $devengadoNuevo = clone $devengado;

                    $devengadoNuevo->setDevengadoOrigen($devengado);

                    $cuentaPresupuestaria = $emContable->getRepository('ADIFContableBundle:CuentaPresupuestaria')
                            ->getCuentaPresupuestariaByEjercicioYCuentaEconomica($ejercicioContableDestino, $devengado->getCuentaPresupuestariaEconomica());

                    if ($cuentaPresupuestaria != null) {

                        $devengadoNuevo->setCuentaPresupuestaria($cuentaPresupuestaria);
                    } else {

                        $erroresPresupuestoArray[$devengado->getCuentaPresupuestariaEconomica()->getId()] = $devengado->getCuentaPresupuestariaEconomica();
                    }
                }
            }

            if ($devengadoNuevo) {

                $devengadoNuevo->setMonto($devengado->getSaldo());

                $this->guardarAsientoPresupuestario($devengadoNuevo);
            }
        }

        $errorMsg = '';

        if (!empty($erroresCuentaContableArray) || !empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>La transferencia de asientos presupuestarios al ejercicio siguiente no se pudo realizar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param CobroRetencionCliente $cobro
     * @param type $esContraasiento
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoParaRetencion(CobroRetencionCliente $cobro, $esContraasiento, AsientoContable $asiento = null) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());

        $erroresArray = array();
        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $comprobantes = $cobro->getComprobantes();

        if (!empty($comprobantes)) {
            /* @var $comprobante ComprobanteVenta */
            $comprobante = $comprobantes[0]; // Debería ser F, ND y C (xq las NC no generan asientos contables, sólo modifican la CC del cliente actualizando su deduda)
            $cancelado = $cobro->getMonto();
            $cuentaContable = $cobro->getRetencionesCliente()[0]->getTipoImpuesto()->getCuentaContable();


            // Cuenta banco o cobranzas a imputar                        
            $ejecutadoCobro = new Ejecutado();
            $ejecutadoCobro->setAsientoContable($asiento);

            if ($cuentaContable) {
                $ejecutadoCobro->setCuentaContable($cuentaContable);

                $ejecutadoCobro->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                if ($cuentaContable->getCuentaPresupuestariaEconomica()) {

                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            !$esContraasiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, //
                            $cuentaContable
                    );

                    $ejecutadoCobro->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutadoCobro->getFechaEjecutado(), //
                        $cuentaContable->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutadoCobro->setCuentaPresupuestaria($cuentaPresupuestaria);

                    $ejecutadoCobro->setMonto(abs($cancelado)); //$total_asiento));

                    $this->guardarAsientoPresupuestario($ejecutadoCobro);
                } else {
                    if ($cuentaPresupuestariaEconomica != null) {
                        $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDestino->getId()] = $cuentaContable;
                    }
                }
            } else {
                $erroresCuentaContableArray[$cuentaContable->getId()] = $cuentaContable;
            }

            $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->find($comprobante->getCliente()->getId());

            $cuenta_deudores = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cliente->getIdCuentaContable());


            // Ejecutado deudores por venta
            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            if ($cuenta_deudores) {
                $ejecutado->setCuentaContable($cuenta_deudores);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_deudores->getCuentaPresupuestariaObjetoGasto());

                if ($cuenta_deudores->getCuentaPresupuestariaEconomica()) {

                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            !$esContraasiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, //
                            $cuenta_deudores
                    );

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $cuenta_deudores->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                    $ejecutado->setMonto(abs($cancelado));

                    $this->guardarAsientoPresupuestario($ejecutado);
                } else {
                    if ($cuentaPresupuestariaEconomica != null) {
                        $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuenta_deudores->getId()] = $cuenta_deudores;
                    }
                }

                // Tengo que ejecutar lo devengado en ventas brutas cuando se genero el comprobante
                $devengado = $emContable->getRepository('ADIFContableBundle:DevengadoVenta')->findOneByRenglonComprobanteVenta($comprobante->getRenglonesComprobante()->first());
                if ($devengado) {
                    /* @var $devengado DevengadoVenta */
                    $ejecutado = new Ejecutado();
                    $ejecutado->setDevengado($devengado);
                    $ejecutado->setAsientoContable($asiento);
                    $ejecutado->setCuentaContable($devengado->getCuentaContable());
                    $ejecutado->setCuentaPresupuestaria($devengado->getCuentaPresupuestaria());
                    $ejecutado->setCuentaPresupuestariaEconomica($devengado->getCuentaPresupuestariaEconomica());
                    $ejecutado->setCuentaPresupuestariaObjetoGasto($devengado->getCuentaPresupuestariaObjetoGasto());
                    $ejecutado->setMonto(abs($cancelado));
                    $this->guardarAsientoPresupuestario($ejecutado);
                }
            } else {
                $erroresCuentaContableArray[$cuenta_deudores->getId()] = $cuenta_deudores;
            }
        } else {
            $erroresArray[$cobro->getId()] = 'El cobro producto de la imputación no tiene comprobante asociado.';
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresArray) || !empty($erroresCuentaContableArray) || !empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param RenglonCobranzaCheque $cheque
     * @param type $esContraasiento
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoParaChequeDepositado($cheques, $esContraasiento, AsientoContable $asiento = null) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $monto_total = 0;

        foreach ($cheques as $cheque) {
            $monto_total += $cheque->getMonto();
            $cuentaBancaria = $cheque->getCuenta();
        }
        $erroresArray = array();
        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $cuentaContableCuentaBancaria = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cuentaBancaria->getIdCuentaContable());

        if (!$cuentaContableCuentaBancaria) {
            $erroresArray[$cuentaBancaria->getId()] = 'La cuenta bancaria ' . $cuentaBancaria . ' no posee una cuenta contable asociada.';
        }


        $ejecutado = new Ejecutado();
        $ejecutado->setAsientoContable($asiento);

        if ($cuentaContableCuentaBancaria) {
            $ejecutado->setCuentaContable($cuentaContableCuentaBancaria);

            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContableCuentaBancaria->getCuentaPresupuestariaObjetoGasto());

            if ($cuentaContableCuentaBancaria->getCuentaPresupuestariaEconomica()) {

                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                        !$esContraasiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, //
                        $cuentaContableCuentaBancaria
                );

                $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuentaContableCuentaBancaria->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                $ejecutado->setMonto(abs($monto_total));

                $this->guardarAsientoPresupuestario($ejecutado);
            } else {
                if ($cuentaPresupuestariaEconomica != null) {
                    $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableCuentaBancaria->getId()] = $cuentaContableCuentaBancaria;
                }
            }
        } else {
            $erroresCuentaContableArray[$cuentaContableCuentaBancaria->getId()] = $cuentaContableCuentaBancaria;
        }


        $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::VALORES_A_DEPOSITAR);
        $codigo_cuenta = $cuentaContable->getCodigoCuentaContable();




        $ejecutado = new Ejecutado();
        $ejecutado->setAsientoContable($asiento);

        if ($cuentaContable) {
            $ejecutado->setCuentaContable($cuentaContable);

            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

            if ($cuentaContable->getCuentaPresupuestariaEconomica()) {

                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                        !$esContraasiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, //
                        $cuentaContable
                );

                $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuentaContable->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                $ejecutado->setMonto(abs($monto_total)); //$total_asiento));

                $this->guardarAsientoPresupuestario($ejecutado);
            } else {
                if ($cuentaPresupuestariaEconomica != null) {
                    $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDestino->getId()] = $cuentaContable;
                }
            }
        } else {
            $erroresCuentaContableArray[$cuentaContable->getId()] = $cuentaContable;
        }


        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresArray) || !empty($erroresCuentaContableArray) || !empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param CobroRenglonCobranza $cobro
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoParaDesimputacionRenglonCobranza(CobroRenglonCobranza $cobro, AsientoContable $asiento = null) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresArray = array();
        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        $comprobantes = $cobro->getComprobantes();

        if (!empty($comprobantes)) {
            /* @var $comprobante ComprobanteVenta */
            $comprobante = $comprobantes[0]; // Debería ser F, ND y C (xq las NC no generan asientos contables, sólo modifican la CC del cliente actualizando su deduda)
            $cancelado = $cobro->getMonto();

            $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->find($comprobante->getCliente()->getId());

            $cuenta_deudores = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cliente->getIdCuentaContable());


            // Ejecutado deudores por venta
            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            if ($cuenta_deudores) {
                $ejecutado->setCuentaContable($cuenta_deudores);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_deudores->getCuentaPresupuestariaObjetoGasto());

                if ($cuenta_deudores->getCuentaPresupuestariaEconomica()) {

                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            ConstanteTipoOperacionContable::DEBE, //
                            $cuenta_deudores
                    );

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $cuenta_deudores->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                    $ejecutado->setMonto(abs($cancelado));

                    $this->guardarAsientoPresupuestario($ejecutado);
                } else {
                    if ($cuentaPresupuestariaEconomica != null) {
                        $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuenta_deudores->getId()] = $cuenta_deudores;
                    }
                }
            } else {
                $erroresCuentaContableArray[$cuenta_deudores->getId()] = $cuenta_deudores;
            }


            // Renglon asiento del básico
            $configuracion_cuenta_anticipo_clientes = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_CLIENTES);
            $cuenta_anticipos = $configuracion_cuenta_anticipo_clientes->getCuentaContable();

            // Ejecutado anticipo
            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            if ($cuenta_anticipos) {
                $ejecutado->setCuentaContable($cuenta_anticipos);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_anticipos->getCuentaPresupuestariaObjetoGasto());

                if ($cuenta_anticipos->getCuentaPresupuestariaEconomica()) {

                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            ConstanteTipoOperacionContable::HABER, //
                            $cuenta_anticipos
                    );

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $cuenta_anticipos->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                    $ejecutado->setMonto(abs($cancelado));

                    $this->guardarAsientoPresupuestario($ejecutado);
                } else {
                    if ($cuentaPresupuestariaEconomica != null) {
                        $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuenta_anticipos->getId()] = $cuenta_anticipos;
                    }
                }
            } else {
                $erroresCuentaContableArray[$cuenta_anticipos->getId()] = $cuenta_anticipos;
            }
        } else {
            $erroresArray[$cobro->getId()] = 'El cobro producto de la imputación no tiene comprobante asociado.';
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresArray) || !empty($erroresCuentaContableArray) || !empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    /**
     * 
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearEjecutadoParaImputaciones($cobrosRetencion, $cobrosAnticipo, $cobrosRenglonCobranza, $esImputacionCompleta, $esContraasiento, AsientoContable $asiento = null) {
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());
        $emRRHH = $this->doctrine->getManager(EntityManagers::getEmRrhh());

        $erroresArray = array();
        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();


        $total_cuenta_deudores = 0;

        $comprobantes = array();

        if (sizeOf($cobrosRetencion) > 0) {
            $totales_cuentas_retencion = array();
            $codigos_cuentas_retencion = array();
            foreach ($cobrosRetencion as $cobro) {
                $comprobante = $cobro->getComprobantes()[0];
                !isset($comprobantes[$comprobante->getId()]) ? $comprobantes[$comprobante->getId()] = $cobro->getMonto() : $comprobantes[$comprobante->getId()] += $cobro->getMonto();
                $codigo_cliente = $comprobante->getCliente()->getId();
                $cuentaContable = $cobro->getRetencionesCliente()[0]->getTipoImpuesto()->getCuentaContable();
                $codigo_cuenta_contable = $cuentaContable->getId(); //->getCodigoCuentaContable()->getId();
                !isset($totales_cuentas_retencion[$codigo_cuenta_contable]) ? $totales_cuentas_retencion[$codigo_cuenta_contable] = $cobro->getMonto() : $totales_cuentas_retencion[$codigo_cuenta_contable] += $cobro->getMonto();
            }
            foreach ($totales_cuentas_retencion as $codigo_cuenta_contable => $total_cuenta_retencion) {
                $total_cuenta_deudores += $total_cuenta_retencion;

                $ejecutado = new Ejecutado();
                $ejecutado->setAsientoContable($asiento);

                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($codigo_cuenta_contable);

                if ($cuentaContable) {
                    $ejecutado->setCuentaContable($cuentaContable);

                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    if ($cuentaContable->getCuentaPresupuestariaEconomica()) {

                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                !$esContraasiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, //
                                $cuentaContable
                        );

                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                    }

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $ejecutado->getFechaEjecutado(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                        $ejecutado->setMonto(abs($total_cuenta_retencion));

                        $this->guardarAsientoPresupuestario($ejecutado);
                    } else {
                        if ($cuentaPresupuestariaEconomica != null) {
                            $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDestino->getId()] = $cuentaContable;
                        }
                    }
                } else {
                    $erroresCuentaContableArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }
        }

//        if (sizeOf($cobrosAnticipo) > 0) {
//            $total_cuenta_anticipos = 0;
//            foreach ($cobrosAnticipo as $cobro) {
//                $comprobante = $cobro->getComprobantes()[0];
//                !isset($comprobantes[$comprobante->getId()]) ? $comprobantes[$comprobante->getId()] = $cobro->getMonto() : $comprobantes[$comprobante->getId()] += $cobro->getMonto();
//
//                $codigo_cliente = $comprobante->getCliente()->getId();
//                $total_cuenta_anticipos += $cobro->getMonto();
//            }
//            $cuenta_anticipos = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_CLIENTES)->getCuentaContable();
//            $total_cuenta_deudores += $total_cuenta_anticipos;
//
//            // Ejecutado anticipo
//            $ejecutado = new Ejecutado();
//            $ejecutado->setAsientoContable($asiento);
//
//            if ($cuenta_anticipos) {
//                $ejecutado->setCuentaContable($cuenta_anticipos);
//
//                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_anticipos->getCuentaPresupuestariaObjetoGasto());
//
//                if ($cuenta_anticipos->getCuentaPresupuestariaEconomica()) {
//
//                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
//                            !$esContraasiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, //
//                            $cuenta_anticipos
//                    );
//
//                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
//                }
//
//                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
//                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
//                        $ejecutado->getFechaEjecutado(), //
//                        $cuenta_anticipos->getCuentaPresupuestariaEconomica()
//                );
//
//                if ($cuentaPresupuestaria != null) {
//                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
//
//                    $ejecutado->setMonto(abs($total_cuenta_anticipos));
//
//                    $this->guardarAsientoPresupuestario($ejecutado);
//                } else {
//                    if ($cuentaPresupuestariaEconomica != null) {
//                        $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
//                    } else {
//                        $erroresCuentaPresupuestariaEconomicaArray[$cuenta_anticipos->getId()] = $cuenta_anticipos;
//                    }
//                }
//            } else {
//                $erroresCuentaContableArray[$cuenta_anticipos->getId()] = $cuenta_anticipos;
//            }
//        }

        $monto_anticipo = 0;
        if (sizeOf($cobrosRenglonCobranza)) { //pueden venir cobrosRenglonCobranza con renglonesBanco o con renglonesCheque
            $total_cuenta_banco = 0;
            $total_cuenta_cheques = 0;
            $total_cuenta_a_imputar = 0;
            foreach ($cobrosRenglonCobranza as $cobro) { //$cobro puede tener renglones banco o renglones cheque pero esos $cobro vienen en la misma colección
                $comprobante = $cobro->getComprobantes()[0];
                !isset($comprobantes[$comprobante->getId()]) ? $comprobantes[$comprobante->getId()] = $cobro->getMonto() : $comprobantes[$comprobante->getId()] += $cobro->getMonto();
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
                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cuenta_bancaria->getIdCuentaContable());

                if (!$cuentaContable) {
                    $erroresArray[$cuenta_bancaria->getId()] = 'La cuenta bancaria ' . $cuenta_bancaria . ' no posee una cuenta contable asociada.';
                }

                // Cuenta banco o cobranzas a imputar                        
                $ejecutado = new Ejecutado();
                $ejecutado->setAsientoContable($asiento);

                if ($cuentaContable) {
                    $ejecutado->setCuentaContable($cuentaContable);

                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    if ($cuentaContable->getCuentaPresupuestariaEconomica()) {

                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                !$esContraasiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, //
                                $cuentaContable
                        );

                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                    }

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $ejecutado->getFechaEjecutado(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                        $ejecutado->setMonto(abs($total_cuenta_banco)); //$total_asiento));

                        $this->guardarAsientoPresupuestario($ejecutado);
                    } else {
                        if ($cuentaPresupuestariaEconomica != null) {
                            $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDestino->getId()] = $cuentaContable;
                        }
                    }
                } else {
                    $erroresCuentaContableArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }
            if ($total_cuenta_cheques > 0) {
                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::VALORES_A_DEPOSITAR);

                // Cuenta banco o cobranzas a imputar                        
                $ejecutado = new Ejecutado();
                $ejecutado->setAsientoContable($asiento);

                if ($cuentaContable) {
                    $ejecutado->setCuentaContable($cuentaContable);

                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    if ($cuentaContable->getCuentaPresupuestariaEconomica()) {

                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                !$esContraasiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, //
                                $cuentaContable
                        );

                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                    }

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $ejecutado->getFechaEjecutado(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                        $ejecutado->setMonto(abs($total_cuenta_cheques)); //$total_asiento));

                        $this->guardarAsientoPresupuestario($ejecutado);
                    } else {
                        if ($cuentaPresupuestariaEconomica != null) {
                            $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDestino->getId()] = $cuentaContable;
                        }
                    }
                } else {
                    $erroresCuentaContableArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }
//            if ($monto_anticipo > 0) {
//                $cuenta_anticipos = $emRRHH->getRepository('ADIFRecursosHumanosBundle:ConfiguracionCuentaContableSueldos')->findOneByCodigo(ConfiguracionCuentaContableSueldos::__CODIGO_ANTICIPO_CLIENTES)->getCuentaContable();
//
//                // Ejecutado anticipo
//                $ejecutado = new Ejecutado();
//                $ejecutado->setAsientoContable($asiento);
//
//                if ($cuenta_anticipos) {
//                    $ejecutado->setCuentaContable($cuenta_anticipos);
//
//                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_anticipos->getCuentaPresupuestariaObjetoGasto());
//
//                    if ($cuenta_anticipos->getCuentaPresupuestariaEconomica()) {
//
//                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
//                                !$esContraasiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, //
//                                $cuenta_anticipos
//                        );
//
//                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
//                    }
//
//                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
//                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
//                            $ejecutado->getFechaEjecutado(), //
//                            $cuenta_anticipos->getCuentaPresupuestariaEconomica()
//                    );
//
//                    if ($cuentaPresupuestaria != null) {
//                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
//
//                        $ejecutado->setMonto(abs($monto_anticipo));
//
//                        $this->guardarAsientoPresupuestario($ejecutado);
//                    } else {
//                        if ($cuentaPresupuestariaEconomica != null) {
//                            $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
//                        } else {
//                            $erroresCuentaPresupuestariaEconomicaArray[$cuenta_anticipos->getId()] = $cuenta_anticipos;
//                        }
//                    }
//                } else {
//                    $erroresCuentaContableArray[$cuenta_anticipos->getId()] = $cuenta_anticipos;
//                }
//            }
            $total_cuenta_deudores += $monto_anticipo;

            if ($total_cuenta_a_imputar > 0) { //o es este o al menos uno de los anteriores
                $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find(ConstanteCodigoInternoCuentaContable::COBRANZAS_A_IMPUTAR);

                // Cuenta banco o cobranzas a imputar                        
                $ejecutado = new Ejecutado();
                $ejecutado->setAsientoContable($asiento);

                if ($cuentaContable) {
                    $ejecutado->setCuentaContable($cuentaContable);

                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    if ($cuentaContable->getCuentaPresupuestariaEconomica()) {

                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                !$esContraasiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, //
                                $cuentaContable
                        );

                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                    }

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $ejecutado->getFechaEjecutado(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                        $ejecutado->setMonto(abs($total_cuenta_a_imputar)); //$total_asiento));

                        $this->guardarAsientoPresupuestario($ejecutado);
                    } else {
                        if ($cuentaPresupuestariaEconomica != null) {
                            $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableDestino->getId()] = $cuentaContable;
                        }
                    }
                } else {
                    $erroresCuentaContableArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }
        }

        if ($total_cuenta_deudores > 0) {

            $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')->find($codigo_cliente);
            $cuenta_deudores = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cliente->getIdCuentaContable());

            // Ejecutado deudores por venta
            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            if ($cuenta_deudores) {
                $ejecutado->setCuentaContable($cuenta_deudores);

                $ejecutado->setCuentaPresupuestariaObjetoGasto($cuenta_deudores->getCuentaPresupuestariaObjetoGasto());

                if ($cuenta_deudores->getCuentaPresupuestariaEconomica()) {

                    $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                            !$esContraasiento ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, //
                            $cuenta_deudores
                    );

                    $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                }

                /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                        $ejecutado->getFechaEjecutado(), //
                        $cuenta_deudores->getCuentaPresupuestariaEconomica()
                );

                if ($cuentaPresupuestaria != null) {
                    $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);

                    $ejecutado->setMonto(abs($total_cuenta_deudores));

                    $this->guardarAsientoPresupuestario($ejecutado);
                } else {
                    if ($cuentaPresupuestariaEconomica != null) {
                        $erroresPresupuestoArray[$cuentaPresupuestariaEconomica->getId()] = $cuentaPresupuestariaEconomica;
                    } else {
                        $erroresCuentaPresupuestariaEconomicaArray[$cuenta_deudores->getId()] = $cuenta_deudores;
                    }
                }
                foreach ($comprobantes as $comprobante_id => $monto_cancelado) {
                    $comprobante = $emContable->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')->find($comprobante_id);
                    $this->crearDevengadoParaComprobanteCanceladoEnCobranzas($emContable, $comprobante, $monto_cancelado, $asiento);
                }
            } else {
                $erroresCuentaContableArray[$cuenta_deudores->getId()] = $cuenta_deudores;
            }
        }


        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresArray) || !empty($erroresCuentaContableArray) || !empty($erroresPresupuestoArray) || !empty($erroresCuentaPresupuestariaEconomicaArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    private function crearDevengadoParaComprobanteCanceladoEnCobranzas($emContable, $comprobante, $cancelado, $asiento) {
        // Tengo que ejecutar lo devengado en ventas brutas cuando se genero el comprobante
        $devengado = $emContable->getRepository('ADIFContableBundle:DevengadoVenta')->findOneByRenglonComprobanteVenta($comprobante->getRenglonesComprobante()->first());
        if ($devengado) {
            /* @var $devengado DevengadoVenta */
            $ejecutado = new Ejecutado();
            $ejecutado->setDevengado($devengado);
            $ejecutado->setAsientoContable($asiento);
            $ejecutado->setCuentaContable($devengado->getCuentaContable());
            $ejecutado->setCuentaPresupuestaria($devengado->getCuentaPresupuestaria());
            $ejecutado->setCuentaPresupuestariaEconomica($devengado->getCuentaPresupuestariaEconomica());
            $ejecutado->setCuentaPresupuestariaObjetoGasto($devengado->getCuentaPresupuestariaObjetoGasto());
            $ejecutado->setMonto(abs($cancelado));

            $this->guardarAsientoPresupuestario($ejecutado);
        }
    }

    /**
     * 
     * @param type $asientoPresupuestario
     */
    private function guardarAsientoPresupuestario($asientoPresupuestario) {

        if (false === $this->container->get('security.context')->isGranted('ROLE_DETACH_ASIENTO')) {

            $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

            $emContable->persist($asientoPresupuestario);
        }
    }

    /**
     * 
     * @param ComprobanteObra $comprobanteObra
     * @param AsientoContable $asiento
     * @return type
     */
    public function crearAsientoFromNotaCreditoObra(ComprobanteObra $comprobanteObra, $esContraAsiento = false, AsientoContable $asiento = null) {

        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());

        $revertirImputacion = ($esContraAsiento && ($comprobanteObra->getTipoComprobante()->getId() != ConstanteTipoComprobanteObra::NOTA_CREDITO)) || (!$esContraAsiento && ($comprobanteObra->getTipoComprobante()->getId() == ConstanteTipoComprobanteObra::NOTA_CREDITO));

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        /*         * **** Devengado - Conceptos de Obra ***** */
        // Por cada RenglonComprobanteObra del comprobante
        foreach ($comprobanteObra->getRenglonesComprobante() as $renglonComprobanteObra) {
            /* @var $renglonComprobanteObra \ADIF\ContableBundle\Entity\Obras\RenglonComprobanteObra */

            // Ejecutado IVA del renglon
            if ($renglonComprobanteObra->getAlicuotaIva()->getValor() != ConstanteAlicuotaIva::ALICUOTA_0) {
                // CuentaContable                
                $cuentaContable = $renglonComprobanteObra->getAlicuotaIva()->getCuentaContableCredito();

                if ($cuentaContable) {
                    $monto = $renglonComprobanteObra->getMontoIva();

                    $ejecutado = new Ejecutado();
                    $ejecutado->setAsientoContable($asiento);

                    $ejecutado->setCuentaContable($cuentaContable);

                    $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

                    if ($cuentaContable->getCuentaPresupuestariaEconomica()) {
                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                (!$revertirImputacion ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE), //
                                $cuentaContable
                        );

                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                    }

                    if ($cuentaContable->getCuentaPresupuestariaEconomica()) {
                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                ConstanteTipoOperacionContable::DEBE, //
                                $cuentaContable
                        );

                        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
                    }

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $ejecutado->getFechaEjecutado(), //
                            $cuentaContable->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
                    } else {
                        if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                            $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                        }
                    }

                    $ejecutado->setMonto($monto * ($revertirImputacion ? -1 : 1));
                } else {
                    $erroresCuentaContableArray[$renglonComprobanteObra->getAlicuotaIva()->getId()] = $renglonComprobanteObra->getAlicuotaIva();
                }

                if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                    $this->guardarAsientoPresupuestario($ejecutado);
                }
            }

            ///////////////////////

            /* @var $comprobanteRenglon ComprobanteObra */
            $comprobanteRenglon = $renglonComprobanteObra->getRenglonAcreditado()->getComprobante();

            $tramo = $comprobanteRenglon->getDocumentoFinanciero()->getTramo();

            // Seteo el Definitivo si es que existe
            $definitivo = $emContable->getRepository('ADIFContableBundle:DefinitivoObra')->findOneByTramo($tramo);

            if ($tramo->getTieneFuenteCAF()) {
                $devengadoConceptoObra = new DevengadoObra();
                $devengadoConceptoObra->setAsientoContable($asiento);

                $devengadoConceptoObra->setDefinitivo($definitivo);

                $cuentaContableFuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:CuentaContable')->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION_CAF);

                if ($cuentaContableFuenteFinanciamiento != null) {
                    $devengadoConceptoObra->setCuentaContable($cuentaContableFuenteFinanciamiento);

                    $devengadoConceptoObra->setCuentaPresupuestariaObjetoGasto($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaObjetoGasto());

                    $cuentaPresupuestariaEconomica = $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica();

                    // Si es un AnticipoFinanciero
                    if ($comprobanteRenglon->getDocumentoFinanciero()->getEsAnticipoFinanciero()) {
                        $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                ConstanteTipoOperacionContable::DEBE, //
                                $cuentaContableFuenteFinanciamiento
                        );
                    }

                    $devengadoConceptoObra->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);

                    /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                    $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                            $devengadoConceptoObra->getFechaDevengado(), //
                            $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()
                    );

                    if ($cuentaPresupuestaria != null) {
                        $devengadoConceptoObra->setCuentaPresupuestaria($cuentaPresupuestaria);
                    } else {
                        if ($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica() != null) {
                            $erroresPresupuestoArray[$cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica();
                        } else {
                            $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableFuenteFinanciamiento->getId()] = $cuentaContableFuenteFinanciamiento;
                        }
                    }
                } else {
                    $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION_CAF] = 'C&oacute;digo interno OBRAS_EJECUCION_CAF';
                }

                $fuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:Obras\FuenteFinanciamiento')->findOneByCodigo(\ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoFuenteFinanciamiento::CODIGO_CAF);

                $devengadoConceptoObra->setComprobanteObra($comprobanteObra);
                $devengadoConceptoObra->setFuenteFinanciamiento($fuenteFinanciamiento);

                $devengadoConceptoObra->setMonto($renglonComprobanteObra->getMontoNeto());

                if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                    $this->guardarAsientoPresupuestario($devengadoConceptoObra);
                }
            } else {
                // Por cada FuenteFinanciamiento asociado al Tramo
                foreach ($tramo->getFuentesFinanciamiento() as $fuenteFinanciamientoTramo) {
                    $fuenteFinanciamiento = $fuenteFinanciamientoTramo->getFuenteFinanciamiento();

                    $devengadoConceptoObra = new DevengadoObra();
                    $devengadoConceptoObra->setAsientoContable($asiento);

                    $devengadoConceptoObra->setDefinitivo($definitivo);

                    // Si la FuenteFinanciamiento modifica las cuentas contables
                    if ($fuenteFinanciamiento->getModificaCuentaContable()) {

                        $cuentaContableFuenteFinanciamiento = $fuenteFinanciamiento->getCuentaContable();
                    } else {

                        $cuentaContableFuenteFinanciamiento = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                                ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION);
                    }

                    if ($cuentaContableFuenteFinanciamiento != null) {

                        $devengadoConceptoObra->setCuentaContable($cuentaContableFuenteFinanciamiento);

                        $devengadoConceptoObra->setCuentaPresupuestariaObjetoGasto($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaObjetoGasto());

                        $cuentaPresupuestariaEconomica = $cuentaContableFuenteFinanciamiento
                                ->getCuentaPresupuestariaEconomica();

                        // Si es un AnticipoFinanciero
                        if ($comprobanteRenglon->getDocumentoFinanciero()->getEsAnticipoFinanciero()) {

                            $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                                    ConstanteTipoOperacionContable::DEBE, //
                                    $cuentaContableFuenteFinanciamiento
                            );
                        }

                        $devengadoConceptoObra->setCuentaPresupuestariaEconomica(
                                $cuentaPresupuestariaEconomica
                        );

                        /* @var $cuentaPresupuestaria CuentaPresupuestaria */
                        $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                                $devengadoConceptoObra->getFechaDevengado(), //
                                $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()
                        );

                        if ($cuentaPresupuestaria != null) {
                            $devengadoConceptoObra->setCuentaPresupuestaria($cuentaPresupuestaria);
                        } else {
                            if ($cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica() != null) {
                                $erroresPresupuestoArray[$cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContableFuenteFinanciamiento->getCuentaPresupuestariaEconomica();
                            } else {
                                $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableFuenteFinanciamiento->getId()] = $cuentaContableFuenteFinanciamiento;
                            }
                        }
                    } else {
                        $erroresCuentaContableArray[ConstanteCodigoInternoCuentaContable::OBRAS_EJECUCION] = 'C&oacute;digo interno OBRAS_EJECUCION';
                    }

                    $totalComprobanteProrrateado = $renglonComprobanteObra->getMontoNeto() * $fuenteFinanciamientoTramo->getPorcentaje() / 100;

                    $devengadoConceptoObra->setComprobanteObra($comprobanteObra);
                    $devengadoConceptoObra->setFuenteFinanciamiento($fuenteFinanciamiento);

                    $devengadoConceptoObra->setMonto($totalComprobanteProrrateado);

                    if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                        $this->guardarAsientoPresupuestario($devengadoConceptoObra);
                    }
                }
            }

            ///////////////////////
        }

        /*         * **** FIN Ejecutado - Conceptos de Obra ***** */


        /*         * **** Ejecutado - Percepciones e Impuestos ***** */

        $montoEjecutadoPercepcionesEImpuestos = 0;
        $cuentaContablePercepcionesEImpuestos = null;

        // Ejecutado de las percepciones
        foreach ($comprobanteObra->getRenglonesPercepcion() as $renglonPercepcion) {

            /* @var $renglonPercepcion \ADIF\ContableBundle\Entity\RenglonPercepcion */

            // ConceptoPercepcionParametrizacion
            $conceptoPercepcionParametrizacion = $emContable
                    ->getRepository('ADIFContableBundle:ConceptoPercepcionParametrizacion')
                    ->findOneBy(array(
                'conceptoPercepcion' => $renglonPercepcion->getConceptoPercepcion(), //
                'jurisdiccion' => $renglonPercepcion->getJurisdiccion())
            );

            if ($conceptoPercepcionParametrizacion) {
                // CuentaContable
                $cuentaContablePercepcionesEImpuestos = $conceptoPercepcionParametrizacion->getCuentaContableCredito();
            }

            $montoEjecutadoPercepcionesEImpuestos += $renglonPercepcion->getMonto();
        }


        // Ejecutado de los impuestos
        foreach ($comprobanteObra->getRenglonesImpuesto() as $renglonImpuesto) {

            /* @var $renglonImpuesto \ADIF\ContableBundle\Entity\RenglonImpuesto */

            // CuentaContable
            $cuentaContablePercepcionesEImpuestos = $renglonImpuesto->getConceptoImpuesto()->getCuentaContable();

            $montoEjecutadoPercepcionesEImpuestos += $renglonImpuesto->getMonto();
        }

        // Creo el ejecutado
        if ($cuentaContablePercepcionesEImpuestos && $montoEjecutadoPercepcionesEImpuestos > 0) {

            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            $ejecutado->setCuentaContable($cuentaContablePercepcionesEImpuestos);

            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaObjetoGasto());

            $ejecutado->setCuentaPresupuestariaEconomica(
                    $cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()) {

                $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                        ConstanteTipoOperacionContable::DEBE, //
                        $cuentaContablePercepcionesEImpuestos
                );

                $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
            }

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContablePercepcionesEImpuestos->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContablePercepcionesEImpuestos->getId()] = $cuentaContablePercepcionesEImpuestos;
                }
            }

            $ejecutado->setMonto($montoEjecutadoPercepcionesEImpuestos);

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        }
        /*         *  FIN Ejecutado - Percepciones e Impuestos  */


        /*         * **** Ejecutado - Proveedor **** *         */

        /* @var $proveedor Proveedor */
        $proveedor = $emCompras->getRepository('ADIFComprasBundle:Proveedor')
                ->find($comprobanteObra->getIdProveedor());

        $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($proveedor->getIdCuentaContable());

        if ($cuentaContable) {

            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            $ejecutado->setCuentaContable($cuentaContable);
            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

            $ejecutado->setCuentaPresupuestariaEconomica(
                    $cuentaContable->getCuentaPresupuestariaEconomica()
            );

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuentaContable->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }

            $ejecutado->setMonto($comprobanteObra->getTotal());

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        } else {
            $erroresCuentaContableArray[$proveedor->getId()] = $proveedor;
        }

        /*         * **** FIN Ejecutado - Proveedor **** *         */


        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El comprobante no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

    private function generarEjecutadoPago(AsientoContable $asiento, OrdenPago $ordenPago, $emContable, &$erroresPresupuestoArray, &$erroresCuentaPresupuestariaEconomicaArray, &$erroresCuentaContableArray, $esContraAsiento) {

        //Cheques
        foreach ($ordenPago->getPagoOrdenPago()->getCheques() as $cheque) {
            /* @var $cheque \ADIF\ContableBundle\Entity\Cheque */
            $cuentaBancaria = $cheque->getChequera()->getCuenta();
            $this->generarAsientoEjecutadoPago($asiento, $ordenPago, $cheque->getMonto(), $emContable, $cuentaBancaria, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);
        }

        //Transferencias
        foreach ($ordenPago->getPagoOrdenPago()->getTransferencias() as $transferencia) {
            /* @var $transferencia \ADIF\ContableBundle\Entity\TransferenciaBancaria */
            $cuentaBancaria = $transferencia->getCuenta();
            $this->generarAsientoEjecutadoPago($asiento, $ordenPago, $transferencia->getMonto(), $emContable, $cuentaBancaria, $erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $esContraAsiento);
        }
    }

    private function generarAsientoEjecutadoPago(AsientoContable $asiento, OrdenPago $ordenPago, $monto, $emContable, $cuentaBancaria, &$erroresPresupuestoArray, &$erroresCuentaPresupuestariaEconomicaArray, &$erroresCuentaContableArray, $esContraAsiento) {

        $cuentaContableCuentaBancaria = $emContable->getRepository('ADIFContableBundle:CuentaContable')->find($cuentaBancaria->getIdCuentaContable());

        if ($cuentaContableCuentaBancaria) {

            $ejecutadoPago = $ordenPago->getEjecutadoEntity();

            $ejecutadoPago->setAsientoContable($asiento);

            $ejecutadoPago->setCuentaContable($cuentaContableCuentaBancaria);

            $ejecutadoPago->setCuentaPresupuestariaObjetoGasto($cuentaContableCuentaBancaria->getCuentaPresupuestariaObjetoGasto());

            $this->setPresupuestariaEconomicaYMontoEjecutado($ejecutadoPago, $monto, $cuentaContableCuentaBancaria, $esContraAsiento ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER);

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutadoPago->getFechaEjecutado(), //
                    $ejecutadoPago->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutadoPago->setCuentaPresupuestaria($cuentaPresupuestaria);

                if ($ejecutadoPago->getMonto() != 0) {

                    $this->guardarAsientoPresupuestario($ejecutadoPago);
                }
            } else {
                if ($ejecutadoPago->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$ejecutadoPago->getCuentaPresupuestariaEconomica()->getId()] = $ejecutadoPago->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContableCuentaBancaria->getId()] = $cuentaContableCuentaBancaria;
                }
            }
        } else {

            $erroresCuentaContableArray[$cuentaBancaria->getId()] = $cuentaBancaria;
        }
    }

    private function setPresupuestariaEconomicaYMontoEjecutado($ejecutado, $monto, $cuentaContableCuenta, $imputacion) {

        $naturaleza_cuenta = $cuentaContableCuenta->getSegmentoOrden(1);

        if ($naturaleza_cuenta != ConstanteNaturalezaCuenta::GASTO) {
            // Si es una cuenta resultado cambia la economica según sume o reste
            $cuentaPresupuestariaEconomica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(
                    $imputacion, //
                    $cuentaContableCuenta
            );
            if ($cuentaPresupuestariaEconomica == null) {
                $cuentaPresupuestariaEconomica = $cuentaContableCuenta->getCuentaPresupuestariaEconomica();
                $monto = $monto * -1;
            }
        } else {
            // Es una cuenta de activo, misma economica pero cambia el signo del monto
            $cuentaPresupuestariaEconomica = $cuentaContableCuenta->getCuentaPresupuestariaEconomica();
            $monto = $monto * -1;
        }

        $ejecutado->setCuentaPresupuestariaEconomica($cuentaPresupuestariaEconomica);
        $ejecutado->setMonto($monto);
    }
	
	public function crearDevengadoFromComprobanteRendicionLiquidoProducto(ComprobanteRendicionLiquidoProducto $comprobanteVenta, AsientoContable $asiento = null, $esContraAsiento = false) 
	{
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

        foreach ($comprobanteVenta->getRenglonesComprobante() as $renglonComprobanteVenta) {

            /* @var $renglonComprobanteVenta \ADIF\ContableBundle\Entity\Facturacion\RenglonComprobanteVentaGeneral */
            $devengado = new DevengadoVenta();
            $devengado->setAsientoContable($asiento);

            //$cuentaContable = $renglonComprobanteVenta->getConceptoVentaGeneral()->getCuentaContable();
			$cuentaContable = $comprobanteVenta->getCliente()->getCuentaContable();

            $devengado->setCuentaContable($cuentaContable);

            $devengado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

            $devengado->setCuentaPresupuestariaEconomica(
                    $cuentaContable->getCuentaPresupuestariaEconomica()
            );

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $devengado->getFechaDevengado(), //
                    $cuentaContable->getCuentaPresupuestariaEconomica()
            );

            if ($cuentaPresupuestaria != null) {
                $devengado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuentaContable->getCuentaPresupuestariaEconomica()->getId()] = $cuentaContable->getCuentaPresupuestariaEconomica();
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }
			
            $devengado->setDefinitivo(null);

            $devengado->setRenglonComprobanteVenta($renglonComprobanteVenta);

            $monto = $renglonComprobanteVenta->getMontoNeto();

            if (($esContraAsiento && ($comprobanteVenta->getTipoComprobante()->getId() != ConstanteTipoComprobanteVenta::NOTA_CREDITO)) ||
                    (!$esContraAsiento && ($comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_CREDITO))) {
                $monto *= -1;
            }

            $devengado->setMonto($monto);

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($devengado);
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }
	
	public function crearEjecutadoFromComprobanteRendicionLiquidoProducto(ComprobanteRendicionLiquidoProducto $comprobanteVenta, AsientoContable $asiento = null, $esContraAsiento = false) 
	{
        $emContable = $this->doctrine->getManager(EntityManagers::getEmContable());
        $emCompras = $this->doctrine->getManager(EntityManagers::getEmCompras());

        $revertirImputacion = ($esContraAsiento && ($comprobanteVenta->getTipoComprobante()->getId() != ConstanteTipoComprobanteVenta::NOTA_CREDITO)) || (!$esContraAsiento && ($comprobanteVenta->getTipoComprobante()->getId() == ConstanteTipoComprobanteVenta::NOTA_CREDITO));

        $erroresCuentaContableArray = array();
        $erroresPresupuestoArray = array();
        $erroresCuentaPresupuestariaEconomicaArray = array();

		$cuentaContable = $comprobanteVenta->getCliente()->getCuentaContable();

        if ($cuentaContable) {
            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            $ejecutado->setCuentaContable($cuentaContable);
            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

            $cuenta_presupuestaria_economica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(!$revertirImputacion ? ConstanteTipoOperacionContable::DEBE : ConstanteTipoOperacionContable::HABER, $cuentaContable);

            $ejecutado->setCuentaPresupuestariaEconomica(
                    $cuenta_presupuestaria_economica
            );

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuenta_presupuestaria_economica
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuentaContable->getCuentaPresupuestariaEconomica() != null) {
                    $erroresPresupuestoArray[$cuenta_presupuestaria_economica->getId()] = $cuenta_presupuestaria_economica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }

            $ejecutado->setMonto(abs($comprobanteVenta->getTotal()));

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        } else {
            $erroresCuentaContableArray[$cliente->getId()] = $cliente;
        }


        $monto_ejecutado_impuestos_percepciones = 0;

        // Chequeo si el comprobante tiene IVA
        foreach ($comprobanteVenta->getRenglonesComprobante() as $renglonComprobanteVenta) {
            /* @var $renglonComprobanteVenta RenglonComprobanteVenta */

            if ($renglonComprobanteVenta->getAlicuotaIva()->getValor() != ConstanteAlicuotaIva::ALICUOTA_0) {
                // CuentaContable                
                $cuentaContable = $renglonComprobanteVenta->getAlicuotaIva()->getCuentaContableDebito();

                if (!$cuentaContable) {
                    $erroresCuentaContableArray[$renglonComprobanteVenta->getAlicuotaIva()->getId()] = $renglonComprobanteVenta->getAlicuotaIva();
                }

                $monto_ejecutado_impuestos_percepciones += $renglonComprobanteVenta->getMontoIva();
            }
        }

        if ($cuentaContable && $monto_ejecutado_impuestos_percepciones) {
            $ejecutado = new Ejecutado();
            $ejecutado->setAsientoContable($asiento);

            $ejecutado->setCuentaContable($cuentaContable);

            $ejecutado->setCuentaPresupuestariaObjetoGasto($cuentaContable->getCuentaPresupuestariaObjetoGasto());

            $cuenta_presupuestaria_economica = $this->getCuentaPresupuestariaEconomicaByImputacionYCuentaContable(!$revertirImputacion ? ConstanteTipoOperacionContable::HABER : ConstanteTipoOperacionContable::DEBE, $cuentaContable);

            $ejecutado->setCuentaPresupuestariaEconomica(
                    $cuenta_presupuestaria_economica
            );

            /* @var $cuentaPresupuestaria CuentaPresupuestaria */
            $cuentaPresupuestaria = $this->getCuentaPresupuestaria(
                    $ejecutado->getFechaEjecutado(), //
                    $cuenta_presupuestaria_economica
            );

            if ($cuentaPresupuestaria != null) {
                $ejecutado->setCuentaPresupuestaria($cuentaPresupuestaria);
            } else {
                if ($cuenta_presupuestaria_economica != null) {
                    $erroresPresupuestoArray[$cuenta_presupuestaria_economica->getId()] = $cuenta_presupuestaria_economica;
                } else {
                    $erroresCuentaPresupuestariaEconomicaArray[$cuentaContable->getId()] = $cuentaContable;
                }
            }

            $ejecutado->setMonto(abs($monto_ejecutado_impuestos_percepciones));

            if (empty($erroresPresupuestoArray) && empty($erroresCuentaPresupuestariaEconomicaArray) && empty($erroresCuentaContableArray)) {
                $this->guardarAsientoPresupuestario($ejecutado);
            }
        }

        // Retorno el mensaje de error correspondiente
        $errorMsg = '';

        if (!empty($erroresPresupuestoArray) || !empty($erroresCuentaContableArray)) {
            $errorMsg .= '<span>El asiento presupuestario no se pudo generar correctamente:</span>';
        }

        return $this->getMensajeError($erroresPresupuestoArray, $erroresCuentaPresupuestariaEconomicaArray, $erroresCuentaContableArray, $errorMsg);
    }

}
