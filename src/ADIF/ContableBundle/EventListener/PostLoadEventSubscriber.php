<?php

namespace ADIF\ContableBundle\EventListener;

use ADIF\ContableBundle\Entity\AdicionalComprobanteCompra;
use ADIF\ContableBundle\Entity\AnticipoOrdenCompra;
use ADIF\ContableBundle\Entity\AnticipoSueldo;
use ADIF\ContableBundle\Entity\AsientoContable;
use ADIF\ContableBundle\Entity\BeneficiarioLiquidacion;
use ADIF\ContableBundle\Entity\Chequera;
use ADIF\ContableBundle\Entity\Cobranza\AnticipoCliente;
use ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza;
use ADIF\ContableBundle\Entity\Cobranza\RenglonCobranza;
use ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque;
use ADIF\ContableBundle\Entity\ComprobanteCompra;
use ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoCompras;
use ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoObras;
use ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion;
use ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria;
use ADIF\ContableBundle\Entity\EgresoValor\DevolucionDinero;
use ADIF\ContableBundle\Entity\EgresoValor\EgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\EgresoValorGerencia;
use ADIF\ContableBundle\Entity\EgresoValor\EstadoEgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\ResponsableEgresoValor;
use ADIF\ContableBundle\Entity\EstadoOrdenPago;
use ADIF\ContableBundle\Entity\EstadoPago;
use ADIF\ContableBundle\Entity\EstadoPagoHistorico;
use ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta;
use ADIF\ContableBundle\Entity\Facturacion\ContratoVenta;
use ADIF\ContableBundle\Entity\Facturacion\EstadoContrato;
use ADIF\ContableBundle\Entity\Licitacion;
use ADIF\ContableBundle\Entity\MovimientoBancario;
use ADIF\ContableBundle\Entity\MovimientoMinisterial;
use ADIF\ContableBundle\Entity\MovimientoPresupuestario;
use ADIF\ContableBundle\Entity\NetCash;
use ADIF\ContableBundle\Entity\Obras\ComprobanteObra;
use ADIF\ContableBundle\Entity\Obras\EstadoTramo;
use ADIF\ContableBundle\Entity\Obras\OrdenPagoObra;
use ADIF\ContableBundle\Entity\Obras\Tramo;
use ADIF\ContableBundle\Entity\OrdenPagoComprobante;
use ADIF\ContableBundle\Entity\OrdenPagoGeneral;
use ADIF\ContableBundle\Entity\OrdenPagoPagoParcial;
use ADIF\ContableBundle\Entity\PagoParcial;
use ADIF\ContableBundle\Entity\ProvisorioSueldoHistorico;
use ADIF\ContableBundle\Entity\RegimenRetencionBienEconomico;
use ADIF\ContableBundle\Entity\RenglonComprobanteCompra;
use ADIF\ContableBundle\Entity\RenglonDeclaracionJuradaLiquidacion;
use ADIF\ContableBundle\Entity\RenglonRetencionLiquidacion;
use ADIF\ContableBundle\Entity\TransferenciaBancaria;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ADIF\ContableBundle\Entity\ComprobanteAjuste;

/**
 * PostLoadEventSubscriber
 *
 * @author Manuel Becerra
 * created 07/10/2014
 * 
 */
class PostLoadEventSubscriber {

    /**
     * CLASE_RECIBO_COBRANZA
     */
    const CLASE_RECIBO_COBRANZA = 'ADIF\ContableBundle\Entity\Cobranza\ReciboCobranza';

    /**
     * CLASE_RETENCION_CLIENTE
     */
    const CLASE_RETENCION_CLIENTE = 'ADIF\ContableBundle\Entity\Cobranza\RetencionCliente';

    /**
     * CLASE_RENGLON_COBRANZA_CHEQUE
     */
    const CLASE_RENGLON_COBRANZA_CHEQUE = 'ADIF\ContableBundle\Entity\Cobranza\RenglonCobranzaCheque';

    /**
     * CLASE_RENGLON_COBRANZA
     */
    const CLASE_RENGLON_COBRANZA = 'ADIF\ContableBundle\Entity\Cobranza\RenglonCobranza';

    /**
     * CLASE_ANTICIPO_CLIENTE
     */
    const CLASE_ANTICIPO_CLIENTE = 'ADIF\ContableBundle\Entity\Cobranza\AnticipoCliente';

    /**
     * CLASE_CONCILIACION
     */
    const CLASE_CONCILIACION = 'ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion';

    /**
     * CLASE_ASIENTO_CONTABLE
     */
    const CLASE_ASIENTO_CONTABLE = 'ADIF\ContableBundle\Entity\AsientoContable';

    /**
     * CLASE_ESTADO_PAGO_HISTORICO
     */
    const CLASE_ESTADO_PAGO_HISTORICO = 'ADIF\ContableBundle\Entity\EstadoPagoHistorico';

    /**
     * CLASE_PROVISORIO_SUELDO_HISTORICO
     */
    const CLASE_PROVISORIO_SUELDO_HISTORICO = 'ADIF\ContableBundle\Entity\ProvisorioSueldoHistorico';

    /**
     * CLASE_CHEQUERA
     */
    const CLASE_CHEQUERA = 'ADIF\ContableBundle\Entity\Chequera';

    /**
     * CLASE_COMPROBANTE_COMPRA
     */
    const CLASE_COMPROBANTE_COMPRA = 'ADIF\ContableBundle\Entity\ComprobanteCompra';

    /**
     * CLASE_COMPROBANTE_OBRA
     */
    const CLASE_COMPROBANTE_OBRA = 'ADIF\ContableBundle\Entity\Obras\ComprobanteObra';

    /**
     * CLASE_ORDEN_PAGO_GENERAL
     */
    const CLASE_ORDEN_PAGO_GENERAL = 'ADIF\ContableBundle\Entity\OrdenPagoGeneral';

    /**
     * CLASE_ORDEN_PAGO_PAGO_PARCIAL
     */
    const CLASE_ORDEN_PAGO_PAGO_PARCIAL = 'ADIF\ContableBundle\Entity\OrdenPagoPagoParcial';

    /**
     * CLASE_ESTADO_ORDEN_PAGO
     */
    const CLASE_ESTADO_ORDEN_PAGO = 'ADIF\ContableBundle\Entity\EstadoOrdenPago';

    /**
     * CLASE_ESTADO_PAGO
     */
    const CLASE_ESTADO_PAGO = 'ADIF\ContableBundle\Entity\EstadoPago';

    /**
     * CLASE_ESTADO_TRAMO
     */
    const CLASE_ESTADO_TRAMO = 'ADIF\ContableBundle\Entity\Obras\EstadoTramo';

    /**
     * CLASE_RENGLON_COMPROBANTE_COMPRA
     */
    const CLASE_RENGLON_COMPROBANTE_COMPRA = 'ADIF\ContableBundle\Entity\RenglonComprobanteCompra';

    /**
     * CLASE_ORDEN_PAGO_COMPROBANTE
     */
    const CLASE_ORDEN_PAGO_COMPROBANTE = 'ADIF\ContableBundle\Entity\OrdenPagoComprobante';

    /**
     * CLASE_ORDEN_PAGO_OBRA
     */
    const CLASE_ORDEN_PAGO_OBRA = 'ADIF\ContableBundle\Entity\Obras\OrdenPagoObra';

    /**
     * CLASE_MOVIMIENTO_PRESUPUESTARIO
     */
    const CLASE_MOVIMIENTO_PRESUPUESTARIO = 'ADIF\ContableBundle\Entity\MovimientoPresupuestario';

    /**
     * CLASE_REGIMEN_RETENCION_BIEN_ECONOMICO
     */
    const CLASE_REGIMEN_RETENCION_BIEN_ECONOMICO = 'ADIF\ContableBundle\Entity\RegimenRetencionBienEconomico';

    /**
     * CLASE_TRANSFERENCIA_BANCARIA
     */
    const CLASE_TRANSFERENCIA_BANCARIA = 'ADIF\ContableBundle\Entity\TransferenciaBancaria';

    /**
     * CLASE_TRAMO
     */
    const CLASE_TRAMO = 'ADIF\ContableBundle\Entity\Obras\Tramo';

    /**
     * CLASE_EGRESO_VALOR
     */
    const CLASE_EGRESO_VALOR = 'ADIF\ContableBundle\Entity\EgresoValor\EgresoValor';

    /**
     * CLASE_EGRESO_VALOR_GERENCIA
     */
    const CLASE_EGRESO_VALOR_GERENCIA = 'ADIF\ContableBundle\Entity\EgresoValor\EgresoValorGerencia';

    /**
     * CLASE_ESTADO_EGRESO_VALOR
     */
    const CLASE_ESTADO_EGRESO_VALOR = 'ADIF\ContableBundle\Entity\EgresoValor\EstadoEgresoValor';

    /**
     * CLASE_RESPONSABLE_EGRESO_VALOR
     */
    const CLASE_RESPONSABLE_EGRESO_VALOR = 'ADIF\ContableBundle\Entity\EgresoValor\ResponsableEgresoValor';

    /**
     * CLASE_DEVOLUCION_DINERO
     */
    const CLASE_DEVOLUCION_DINERO = 'ADIF\ContableBundle\Entity\EgresoValor\DevolucionDinero';

    /**
     * CLASE_MOVIMIENTO_BANCARIO
     */
    const CLASE_MOVIMIENTO_BANCARIO = 'ADIF\ContableBundle\Entity\MovimientoBancario';

    /**
     * CLASE_MOVIMIENTO_MINISTERIAL
     */
    const CLASE_MOVIMIENTO_MINISTERIAL = 'ADIF\ContableBundle\Entity\MovimientoMinisterial';

    /**
     * CLASE_CONTRATO_VENTA
     */
    const CLASE_CONTRATO_VENTA = 'ADIF\ContableBundle\Entity\Facturacion\ContratoVenta';

    /**
     * CLASE_CONTRATO_CONSULTORIA
     */
    const CLASE_CONTRATO_CONSULTORIA = 'ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria';

    /**
     * CLASE_ESTADO_CONTRATO
     */
    const CLASE_ESTADO_CONTRATO = 'ADIF\ContableBundle\Entity\Facturacion\EstadoContrato';

    /**
     * CLASE_ANTICIPO_SUELDO
     */
    const CLASE_ANTICIPO_SUELDO = 'ADIF\ContableBundle\Entity\AnticipoSueldo';

    /**
     * CLASE_ANTICIPO_PROVEEDOR
     */
    const CLASE_ANTICIPO_PROVEEDOR = 'ADIF\ContableBundle\Entity\AnticipoProveedor';

    /**
     * CLASE_ANTICIPO_ORDEN_COMPRA
     */
    const CLASE_ANTICIPO_ORDEN_COMPRA = 'ADIF\ContableBundle\Entity\AnticipoOrdenCompra';

    /**
     * CLASE_RENGLON_DECLARACION_JURADA_LIQUIDACION
     */
    const CLASE_RENGLON_DECLARACION_JURADA_LIQUIDACION = 'ADIF\ContableBundle\Entity\RenglonDeclaracionJuradaLiquidacion';

    /**
     * CLASE_RENGLON_RETENCION_LIQUIDACION
     */
    const CLASE_RENGLON_RETENCION_LIQUIDACION = 'ADIF\ContableBundle\Entity\RenglonRetencionLiquidacion';

    /**
     * CLASE_ADICIONAL_COMPROBANTE_COMPRA
     */
    const CLASE_ADICIONAL_COMPROBANTE_COMPRA = 'ADIF\ContableBundle\Entity\AdicionalComprobanteCompra';

    /**
     * CLASE_BENEFICIARIO_LIQUIDACION
     */
    const CLASE_BENEFICIARIO_LIQUIDACION = 'ADIF\ContableBundle\Entity\BeneficiarioLiquidacion';

    /**
     * CLASE_LICITACION
     */
    const CLASE_LICITACION = 'ADIF\ContableBundle\Entity\Licitacion';

    /**
     * CLASE_COMPROBANTE_VENTA
     */
    const CLASE_COMPROBANTE_VENTA = 'ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta';

    /**
     * CLASE_COMPROBANTE_RETENCION_IMPUESTO_COMPRAS
     */
    const CLASE_COMPROBANTE_RETENCION_IMPUESTO_COMPRAS = 'ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoCompras';

    /**
     * CLASE_COMPROBANTE_RETENCION_IMPUESTO_OBRAS
     */
    const CLASE_COMPROBANTE_RETENCION_IMPUESTO_OBRAS = 'ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoObras';

    /**
     * CLASE_PAGO_PARCIAL
     */
    const CLASE_PAGO_PARCIAL = 'ADIF\ContableBundle\Entity\PagoParcial';
	
    /**
     * CLASE_NETCASH
     */
    const CLASE_NETCASH = 'ADIF\ContableBundle\Entity\NetCash';
    
	 /**
     * CLASE_COMPROBANTE_AJUSTE
     */
    const CLASE_COMPROBANTE_AJUSTE = 'ADIF\ContableBundle\Entity\ComprobanteAjuste';

    /**
     *
     * @var type \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $registry;

    /**
     * 
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $registry
     */
    public function __construct(Registry $registry) {

        $this->registry = $registry;
    }

    /**
     * 
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs) {

        $this->updateEntities($eventArgs);
    }

    /**
     * 
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs) {

        $this->updateEntities($eventArgs);
    }

    /**
     * 
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs) {

        $entity = $eventArgs->getEntity();

        // Si la entidad es un RenglonCobranzaCheque
        if ($entity instanceof ReciboCobranza && null != $entity->getIdCliente()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_RECIBO_COBRANZA, //
                    'cliente', //
                    'ADIF\ComprasBundle\Entity\Cliente', //
                    $entity->getIdCliente())
            ;
        }

        // Si la entidad es un RenglonCobranzaCheque
        if ($entity instanceof RenglonCobranzaCheque && null != $entity->getIdCuenta()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_RENGLON_COBRANZA_CHEQUE, //
                    'cuenta', //
                    'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF', //
                    $entity->getIdCuenta())
            ;
        }

        // Si la entidad es un RenglonCobranzaCheque
        if ($entity instanceof RenglonCobranzaCheque && null != $entity->getIdBanco()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_RENGLON_COBRANZA_CHEQUE, //
                    'banco', //
                    'ADIF\RecursosHumanosBundle\Entity\Banco', //
                    $entity->getIdBanco())
            ;
        }

        // Si la entidad es un AnticipoCliente
        if ($entity instanceof AnticipoCliente && null != $entity->getIdCliente()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_ANTICIPO_CLIENTE, //
                    'cliente', //
                    'ADIF\ComprasBundle\Entity\Cliente', //
                    $entity->getIdCliente())
            ;
        }

        // Si la entidad es un RenglonCobranza
        if ($entity instanceof RenglonCobranza && null != $entity->getIdCuentaBancaria()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_RENGLON_COBRANZA, //
                    'cuentaBancaria', //
                    'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF', //
                    $entity->getIdCuentaBancaria())
            ;
        }

        // Si la entidad es una Conciliacion
        if ($entity instanceof Conciliacion && null != $entity->getIdCuenta()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_CONCILIACION, //
                    'cuenta', //
                    'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF', //
                    $entity->getIdCuenta())
            ;
        }

        // Si la entidad es un AsientoContable
        if ($entity instanceof AsientoContable && null != $entity->getIdUsuario()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_ASIENTO_CONTABLE, //
                    'usuario', //
                    'ADIF\AutenticacionBundle\Entity\Usuario', //
                    $entity->getIdUsuario())
            ;
        }

        // Si la entidad es un AsientoContable
        if ($entity instanceof AsientoContable && null != $entity->getIdUsuario()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_ASIENTO_CONTABLE, //
                    'usuario', //
                    'ADIF\AutenticacionBundle\Entity\Usuario', //
                    $entity->getIdUsuario())
            ;
        }

        // Si la entidad es un EstadoPagoHistorico
        if ($entity instanceof EstadoPagoHistorico && null != $entity->getIdUsuario()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_ESTADO_PAGO_HISTORICO, //
                    'usuario', //
                    'ADIF\AutenticacionBundle\Entity\Usuario', //
                    $entity->getIdUsuario())
            ;
        }

        // Si la entidad es una Chequera
        if ($entity instanceof Chequera && null != $entity->getIdCuenta()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_CHEQUERA, //
                    'cuenta', //
                    'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF', //
                    $entity->getIdCuenta())
            ;
        }

        // Si la entidad es una TransferenciaBancaria
        if ($entity instanceof TransferenciaBancaria && null != $entity->getIdCuenta()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_TRANSFERENCIA_BANCARIA, //
                    'cuenta', //
                    'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF', //
                    $entity->getIdCuenta())
            ;
        }

        // Si la entidad es una ComprobanteObra
        if ($entity instanceof ComprobanteObra) {
            //Proveedor
            if (null != $entity->getIdProveedor()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_COMPROBANTE_OBRA, //
                        'proveedor', //
                        'ADIF\ComprasBundle\Entity\Proveedor', //
                        $entity->getIdProveedor())
                ;
            }
        }
		
        // Si la entidad es una OrdenPagoGeneral
        if ($entity instanceof OrdenPagoGeneral) {

            //Proveedor
            if (null != $entity->getIdProveedor()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ORDEN_PAGO_GENERAL, //
                        'proveedor', //
                        'ADIF\ComprasBundle\Entity\Proveedor', //
                        $entity->getIdProveedor())
                ;
            }
        }

        // Si la entidad es una PagoParcial
        if ($entity instanceof PagoParcial) {

            //Proveedor
            if (null != $entity->getIdProveedor()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_PAGO_PARCIAL, //
                        'proveedor', //
                        'ADIF\ComprasBundle\Entity\Proveedor', //
                        $entity->getIdProveedor())
                ;
            }
        }

        // Si la entidad es una OrdenPagoPagoParcial
        if ($entity instanceof OrdenPagoPagoParcial) {

            //Proveedor
            if (null != $entity->getIdProveedor()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ORDEN_PAGO_PAGO_PARCIAL, //
                        'proveedor', //
                        'ADIF\ComprasBundle\Entity\Proveedor', //
                        $entity->getIdProveedor())
                ;
            }
        }

        // Si la entidad es una ComprobanteCompra
        if ($entity instanceof ComprobanteCompra) {
            //Proveedor
            if (null != $entity->getIdProveedor()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_COMPROBANTE_COMPRA, //
                        'proveedor', //
                        'ADIF\ComprasBundle\Entity\Proveedor', //
                        $entity->getIdProveedor())
                ;
            }

            //OrdenCompra
            if (null != $entity->getIdOrdenCompra()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_COMPROBANTE_COMPRA, //
                        'ordenCompra', //
                        'ADIF\ComprasBundle\Entity\OrdenCompra', //
                        $entity->getIdOrdenCompra())
                ;
            }
        }

        // Si la entidad es una Tramo
        if ($entity instanceof Tramo && null != $entity->getIdProveedor()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_TRAMO, //
                    'proveedor', //
                    'ADIF\ComprasBundle\Entity\Proveedor', //
                    $entity->getIdProveedor())
            ;
        }

        // Si la entidad es una RenglonComprobanteCompra
        if ($entity instanceof RenglonComprobanteCompra) {
            if (null != $entity->getIdRenglonOrdenCompra()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_RENGLON_COMPROBANTE_COMPRA, //
                        'renglonOrdenCompra', //
                        'ADIF\ComprasBundle\Entity\RenglonOrdenCompra', //
                        $entity->getIdRenglonOrdenCompra())
                ;
            }
            if (null != $entity->getIdBienEconomico()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_RENGLON_COMPROBANTE_COMPRA, //
                        'bienEconomico', //
                        'ADIF\ComprasBundle\Entity\BienEconomico', //
                        $entity->getIdBienEconomico())
                ;
            }
        }

        // Si la entidad es una MovimientoPresupuestario
        if ($entity instanceof MovimientoPresupuestario && null != $entity->getIdUsuario()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_MOVIMIENTO_PRESUPUESTARIO, //
                    'usuario', //
                    'ADIF\AutenticacionBundle\Entity\Usuario', //
                    $entity->getIdUsuario())
            ;
        }

        // Si la entidad es una OrdenPagoComprobante
        if ($entity instanceof OrdenPagoComprobante && null != $entity->getIdProveedor()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_ORDEN_PAGO_COMPROBANTE, //
                    'proveedor', //
                    'ADIF\ComprasBundle\Entity\Proveedor', //
                    $entity->getIdProveedor())
            ;
        }

        // Si la entidad es una OrdenPagoObra
        if ($entity instanceof OrdenPagoObra && null != $entity->getIdProveedor()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_ORDEN_PAGO_OBRA, //
                    'proveedor', //
                    'ADIF\ComprasBundle\Entity\Proveedor', //
                    $entity->getIdProveedor())
            ;
        }

        // Si la entidad es una ComprobanteRetencionImpuestoCompras
        if ($entity instanceof ComprobanteRetencionImpuestoCompras && null != $entity->getIdProveedor()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_COMPROBANTE_RETENCION_IMPUESTO_COMPRAS, //
                    'proveedor', //
                    'ADIF\ComprasBundle\Entity\Proveedor', //
                    $entity->getIdProveedor())
            ;
        }

        // Si la entidad es una ComprobanteRetencionImpuestoObras
        if ($entity instanceof ComprobanteRetencionImpuestoObras && null != $entity->getIdProveedor()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_COMPROBANTE_RETENCION_IMPUESTO_OBRAS, //
                    'proveedor', //
                    'ADIF\ComprasBundle\Entity\Proveedor', //
                    $entity->getIdProveedor())
            ;
        }

        // Si la entidad es una RegimenRetencionBienEconomico
        if ($entity instanceof RegimenRetencionBienEconomico && null != $entity->getIdBienEconomico()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_REGIMEN_RETENCION_BIEN_ECONOMICO, //
                    'bienEconomico', //
                    'ADIF\ComprasBundle\Entity\BienEconomico', //
                    $entity->getIdBienEconomico())
            ;
        }

        // Si la entidad es un EstadoOrdenPago
        if ($entity instanceof EstadoOrdenPago && null != $entity->getIdTipoImportancia()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_ESTADO_ORDEN_PAGO, //
                    'tipoImportancia', //
                    'ADIF\ComprasBundle\Entity\TipoImportancia', //
                    $entity->getIdTipoImportancia())
            ;
        }

        // Si la entidad es un EstadoPago
        if ($entity instanceof EstadoPago && null != $entity->getIdTipoImportancia()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_ESTADO_PAGO, //
                    'tipoImportancia', //
                    'ADIF\ComprasBundle\Entity\TipoImportancia', //
                    $entity->getIdTipoImportancia())
            ;
        }

        // Si la entidad es un EstadoTramo
        if ($entity instanceof EstadoTramo && null != $entity->getIdTipoImportancia()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_ESTADO_TRAMO, //
                    'tipoImportancia', //
                    'ADIF\ComprasBundle\Entity\TipoImportancia', //
                    $entity->getIdTipoImportancia())
            ;
        }

        // Si la entidad es un AdicionalComprobanteCompra
        if ($entity instanceof AdicionalComprobanteCompra) {

            if (null != $entity->getIdTipoAdicional()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ADICIONAL_COMPROBANTE_COMPRA, //
                        'tipoAdicional', //
                        'ADIF\ComprasBundle\Entity\TipoAdicional', //
                        $entity->getIdTipoAdicional())
                ;
            }

            if (null != $entity->getIdAdicionalCotizacion()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ADICIONAL_COMPROBANTE_COMPRA, //
                        'adicionalCotizacion', //
                        'ADIF\ComprasBundle\Entity\AdicionalCotizacion', //
                        $entity->getIdAdicionalCotizacion())
                ;
            }
        }

        // Si la entidad es una EgresoValor
        if ($entity instanceof EgresoValor && null != $entity->getIdGerencia()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_EGRESO_VALOR, //
                    'gerencia', //
                    'ADIF\RecursosHumanosBundle\Entity\Gerencia', //
                    $entity->getIdGerencia())
            ;
        }

        // Si la entidad es una EgresoValorGerencia
        if ($entity instanceof EgresoValorGerencia && null != $entity->getIdGerencia()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_EGRESO_VALOR_GERENCIA, //
                    'gerencia', //
                    'ADIF\RecursosHumanosBundle\Entity\Gerencia', //
                    $entity->getIdGerencia())
            ;
        }

        // Si la entidad es un EstadoEgresoValor
        if ($entity instanceof EstadoEgresoValor && null != $entity->getIdTipoImportancia()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_ESTADO_EGRESO_VALOR, //
                    'tipoImportancia', //
                    'ADIF\ComprasBundle\Entity\TipoImportancia', //
                    $entity->getIdTipoImportancia())
            ;
        }

        // Si la entidad es un ResponsableEgresoValor
        if ($entity instanceof ResponsableEgresoValor && null != $entity->getIdTipoDocumento()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_RESPONSABLE_EGRESO_VALOR, //
                    'tipoDocumento', //
                    'ADIF\RecursosHumanosBundle\Entity\TipoDocumento', //
                    $entity->getIdTipoDocumento())
            ;
        }

        // Si la entidad es un DevolucionDinero
        if ($entity instanceof DevolucionDinero && null != $entity->getIdCuenta()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_DEVOLUCION_DINERO, //
                    'cuenta', //
                    'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF', //
                    $entity->getIdCuenta())
            ;
        }

        // Si la entidad es un MovimientoBancario
        if ($entity instanceof MovimientoBancario) {
            if (null != $entity->getIdCuentaOrigen()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_MOVIMIENTO_BANCARIO, //
                        'cuentaOrigen', //
                        'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF', //
                        $entity->getIdCuentaOrigen())
                ;
            }

            if (null != $entity->getIdCuentaDestino()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_MOVIMIENTO_BANCARIO, //
                        'cuentaDestino', //
                        'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF', //
                        $entity->getIdCuentaDestino())
                ;
            }
        }

        // Si la entidad es un MovimientoMinisterial
        if ($entity instanceof MovimientoMinisterial && null != $entity->getIdCuentaBancariaADIF()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_MOVIMIENTO_MINISTERIAL, //
                    'cuentaBancariaADIF', //
                    'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF', //
                    $entity->getIdCuentaBancariaADIF())
            ;
        }

        // Si la entidad es un EstadoContrato
        if ($entity instanceof EstadoContrato) {
            if (null != $entity->getIdTipoImportancia()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ESTADO_CONTRATO, //
                        'tipoImportancia', //
                        'ADIF\ComprasBundle\Entity\TipoImportancia', //
                        $entity->getIdTipoImportancia())
                ;
            }
        }

        // Si la entidad es un ContratoVenta
        if ($entity instanceof ContratoVenta) {
            if (null != $entity->getIdCliente()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONTRATO_VENTA, //
                        'cliente', //
                        'ADIF\ComprasBundle\Entity\Cliente', //
                        $entity->getIdCliente())
                ;
            }
        }

        // Si la entidad es un ContratoConsultoria
        if ($entity instanceof ContratoConsultoria) {

            if (null != $entity->getIdConsultor()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONTRATO_CONSULTORIA, //
                        'consultor', //
                        'ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor', //
                        $entity->getIdConsultor())
                ;
            }

            if (null != $entity->getIdArea()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONTRATO_CONSULTORIA, //
                        'area', //
                        'ADIF\RecursosHumanosBundle\Entity\Area', //
                        $entity->getIdArea())
                ;
            }

            if (null != $entity->getIdGerencia()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONTRATO_CONSULTORIA, //
                        'gerencia', //
                        'ADIF\RecursosHumanosBundle\Entity\Gerencia', //
                        $entity->getIdGerencia())
                ;
            }

            if (null != $entity->getIdSubgerencia()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONTRATO_CONSULTORIA, //
                        'subgerencia', //
                        'ADIF\RecursosHumanosBundle\Entity\Subgerencia', //
                        $entity->getIdSubgerencia())
                ;
            }
        }

        // Si la entidad es un AnticipoSueldo
        if ($entity instanceof AnticipoSueldo) {
            if (null != $entity->getIdEmpleado()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ANTICIPO_SUELDO, //
                        'empleado', //
                        'ADIF\RecursosHumanosBundle\Entity\Empleado', //
                        $entity->getIdEmpleado())
                ;
            }
        }

        // Si la entidad es un AnticipoOrdenCompra
        if ($entity instanceof AnticipoOrdenCompra) {
            if (null != $entity->getIdOrdenCompra()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ANTICIPO_ORDEN_COMPRA, //
                        'ordenCompra', //
                        'ADIF\ComprasBundle\Entity\OrdenCompra', //
                        $entity->getIdOrdenCompra())
                ;
            }
        }

        // Si la entidad es un AnticipoProveedor
        if ($entity instanceof \ADIF\ContableBundle\Entity\AnticipoProveedor) {
            if (null != $entity->getIdProveedor()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_ANTICIPO_PROVEEDOR, //
                        'proveedor', //
                        'ADIF\ComprasBundle\Entity\Proveedor', //
                        $entity->getIdProveedor())
                ;
            }
        }

        // Si la entidad es un Renglon
        if ($entity instanceof RenglonDeclaracionJuradaLiquidacion) {
            if (null != $entity->getIdLiquidacion()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_RENGLON_DECLARACION_JURADA_LIQUIDACION, //
                        'liquidacion', //
                        'ADIF\RecursosHumanosBundle\Entity\Liquidacion', //
                        $entity->getIdLiquidacion())
                ;
            }
        }

        // Si la entidad es un Renglon
        if ($entity instanceof RenglonRetencionLiquidacion) {
            if (null != $entity->getIdLiquidacion()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_RENGLON_RETENCION_LIQUIDACION, //
                        'liquidacion', //
                        'ADIF\RecursosHumanosBundle\Entity\Liquidacion', //
                        $entity->getIdLiquidacion())
                ;
            }

            if (null != $entity->getIdConceptoVersion()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_RENGLON_RETENCION_LIQUIDACION, //
                        'conceptoVersion', //
                        'ADIF\RecursosHumanosBundle\Entity\ConceptoVersion', //
                        $entity->getIdConceptoVersion())
                ;
            }
        }

        // Si la entidad es un BeneficiarioLiquidacion
        if ($entity instanceof BeneficiarioLiquidacion) {
            if (null != $entity->getIdDomicilio()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_BENEFICIARIO_LIQUIDACION, //
                        'domicilio', //
                        'ADIF\RecursosHumanosBundle\Entity\Domicilio', //
                        $entity->getIdDomicilio())
                ;
            }
        }

        // Si la entidad es una Licitacion
        if ($entity instanceof Licitacion && null != $entity->getIdTipoContratacion()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_LICITACION, //
                    'tipoContratacion', //
                    'ADIF\ComprasBundle\Entity\TipoContratacion', //
                    $entity->getIdTipoContratacion())
            ;
        }

        // Si la entidad es un ComprobanteVenta 
        if ($entity instanceof ComprobanteVenta && null != $entity->getIdCliente()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_COMPROBANTE_VENTA, //
                    'cliente', //
                    'ADIF\ComprasBundle\Entity\Cliente', //
                    $entity->getIdCliente())
            ;
        }
		
        // Si la entidad es un NetCash
        if ($entity instanceof NetCash && null != $entity->getIdCuenta()) {
            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_NETCASH, //
                    'cuenta', //
                    'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF', //
                    $entity->getIdCuenta())
            ;
        }
        
		// Si la entidad es un ComprobanteAjuste
        if ($entity instanceof ComprobanteAjuste) {
            //Proveedor
            if (null != $entity->getIdProveedor()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_COMPROBANTE_AJUSTE, //
                        'proveedor', //
                        'ADIF\ComprasBundle\Entity\Proveedor', //
                        $entity->getIdProveedor())
                ;
            }
			
			// Cliente
			if (null != $entity->getIdCliente()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_COMPROBANTE_AJUSTE, //
                        'cliente', //
                        'ADIF\ComprasBundle\Entity\Cliente', //
                        $entity->getIdCliente())
                ;
            }
        }
    }

    /**
     * 
     * @param type $eventArgs
     * @param type $entityClass
     * @param type $property
     * @param type $referenceEntityClass
     * @param type $idEntity
     */
    private function setEntityValue($eventArgs, $entityClass, $property, $referenceEntityClass, $idEntity) {

        $em = $eventArgs->getEntityManager();

        $entity = $eventArgs->getEntity();

        $reflProp = $em->getClassMetadata($entityClass)
                ->reflClass->getProperty($property);

        $reflProp->setAccessible(true);

        $reflProp->setValue(
                $entity, $this->registry->getManagerForClass($referenceEntityClass)
                        ->getReference($referenceEntityClass, $idEntity)
        );
    }

    /**
     * 
     * @param type $eventArgs
     */
    private function updateEntities($eventArgs) {

        $entity = $eventArgs->getEntity();

        // Si la entidad es una Chequera
        if ($entity instanceof Chequera) {
            // Cuenta
            if (null != $entity->getCuenta()) {
                $entityId = $this->updateEntityId($entity->getCuenta());

                $entity->setIdCuenta($entityId);
            }
        }

        // Si la entidad es una TransferenciaBancaria
        if ($entity instanceof TransferenciaBancaria) {
            // Cuenta
            if (null != $entity->getCuenta()) {
                $entityId = $this->updateEntityId($entity->getCuenta());

                $entity->setIdCuenta($entityId);
            }
        }

        // Si la entidad es una BeneficiarioLiquidacion
        if ($entity instanceof BeneficiarioLiquidacion) {
            // Domicilio 
            if (null != $entity->getDomicilio()) {
                $entityId = $this->updateEntityId($entity->getDomicilio());
                $entity->setIdDomicilio($entityId);
            }
        }
    }

    /**
     * 
     * @param type $entity
     * @return type
     */
    private function updateEntityId($entity) {
        $entityManager = $this->registry->getManagerForClass(get_class($entity));
        $entityManager->persist($entity);
        $entityManager->flush();

        return $entity->getId();
    }

}
