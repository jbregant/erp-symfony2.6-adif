<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of OrdenPago
 *
 * @author Manuel Becerra
 * created 03/11/2014
 * 
 * @ORM\Table(name="orden_pago")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\OrdenPagoRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "orden_pago" = "OrdenPago",
 *      "orden_pago_comprobante" = "OrdenPagoComprobante",
 *      "orden_pago_consultoria" = "ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria",
 *      "orden_pago_obra" = "ADIF\ContableBundle\Entity\Obras\OrdenPagoObra",
 *      "orden_pago_egreso_valor" = "ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor",
 *      "orden_pago_reconocimiento_egreso_valor" = "ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoReconocimientoEgresoValor",
 *      "orden_pago_sueldo" = "OrdenPagoSueldo",
 *      "orden_pago_cargas_sociales" = "OrdenPagoCargasSociales",
 *      "orden_pago_anticipo_sueldo" = "OrdenPagoAnticipoSueldo",
 *      "orden_pago_anticipo_proveedor" = "OrdenPagoAnticipoProveedor",
 *      "orden_pago_anticipo_contratoconsultoria" = "OrdenPagoAnticipoContratoConsultoria",
 *      "orden_pago_pago_a_cuenta" = "OrdenPagoPagoACuenta",
 *      "orden_pago_declaracion_jurada" = "OrdenPagoDeclaracionJurada",
 *      "orden_pago_devolucion_renglon_declaracion_jurada" = "OrdenPagoDevolucionRenglonDeclaracionJurada",
 *      "orden_pago_declaracion_jurada_iva_contribuyente" = "OrdenPagoDeclaracionJuradaIvaContribuyente",
 *      "orden_pago_declaracion_jurada_iibb_contribuyente" = "OrdenPagoDeclaracionJuradaIIBBContribuyente",
 *      "orden_pago_renglon_retencion_liquidacion" = "OrdenPagoRenglonRetencionLiquidacion",
 *      "orden_pago_devolucion_garantia" = "ADIF\ContableBundle\Entity\Facturacion\OrdenPagoDevolucionGarantia",
 *      "orden_pago_movimiento_bancario" = "OrdenPagoMovimientoBancario",
 *      "orden_pago_movimiento_ministerial" = "OrdenPagoMovimientoMinisterial",
 *      "egreso_vario" = "EgresoVario",
 *      "orden_pago_general" = "OrdenPagoGeneral",
 *      "orden_pago_pago_parcial" = "OrdenPagoPagoParcial"
 * })
 */
class OrdenPago extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_orden_pago", type="datetime", nullable=true)
     */
    protected $fechaOrdenPago;

    /**
     * @var \ADIF\ContableBundle\Entity\AsientoContable
     *
     * @ORM\OneToOne(targetEntity="AsientoContable")
     * @ORM\JoinColumn(name="id_asiento_contable", referencedColumnName="id", nullable=true)
     * 
     */
    protected $asientoContable;

    /**
     * @var \ADIF\ContableBundle\Entity\AsientoContable
     *
     * @ORM\OneToOne(targetEntity="AsientoContable")
     * @ORM\JoinColumn(name="id_asiento_contable_anulacion", referencedColumnName="id", nullable=true)
     * 
     */
    protected $asientoContableAnulacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_contable", type="datetime", nullable=true)
     */
    protected $fechaContable;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_orden_pago", type="integer", length=8, nullable=true)
     */
    protected $numeroOrdenPago;

    /**
     * @var string
     *
     * @ORM\Column(name="concepto", type="string", length=512, nullable=false)
     */
    protected $concepto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_anulacion", type="datetime", nullable=true)
     */
    protected $fechaAnulacion;

    /**
     * @ORM\ManyToOne(targetEntity="PagoOrdenPago", inversedBy="ordenesPago", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_pago", referencedColumnName="id")
     */
    protected $pagoOrdenPago;

    /**
     * @var \ADIF\ComprasBundle\Entity\EstadoOrdenPago
     *
     * @ORM\ManyToOne(targetEntity="EstadoOrdenPago", inversedBy="ordenesPago")
     * @ORM\JoinColumn(name="id_estado_orden_pago", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoOrdenPago;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_autorizacion_contable", type="datetime", nullable=false)
     */
    protected $fechaAutorizacionContable;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_autorizacion_contable", type="integer", length=8, nullable=false)
     */
    protected $numeroAutorizacionContable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="factura_conformada", type="boolean", nullable=false)
     */
    protected $facturaConformada;

    /**
     * @var boolean
     *
     * @ORM\Column(name="calculos_verificados", type="boolean", nullable=false)
     */
    protected $calculosVerificados;

    /**
     * @var boolean
     *
     * @ORM\Column(name="impuestos_verificados", type="boolean", nullable=false)
     */
    protected $impuestosVerificados;

    /**
     *
     * @var RetencionImpuesto
     * 
     * @ORM\OneToMany(targetEntity="ComprobanteRetencionImpuesto", mappedBy="ordenPago", cascade={"all"})
     */
    protected $retenciones;

    /**
     * @var boolean
     *
     * @ORM\Column(name="fue_vista", type="boolean", nullable=true)
     */
    protected $fueVista;

    /**
     * Constructor
     */
    public function __construct() {

        $this->concepto = '';

        $this->facturaConformada = false;
        $this->calculosVerificados = false;
        $this->impuestosVerificados = false;
        $this->fechaContable = new \DateTime();

        $this->fechaAutorizacionContable = new \DateTime();

        $this->retenciones = new ArrayCollection();

        $this->fueVista = false;
    }

    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->getNumero();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set fechaOrdenPago
     *
     * @param \DateTime $fechaOrdenPago
     * @return OrdenPago
     */
    public function setFechaOrdenPago($fechaOrdenPago) {
        $this->fechaOrdenPago = $fechaOrdenPago;

        return $this;
    }

    /**
     * Get fechaOrdenPago
     *
     * @return \DateTime 
     */
    public function getFechaOrdenPago() {
        return $this->fechaOrdenPago;
    }

    /**
     * Set asientoContable
     *
     * @param \ADIF\ContableBundle\Entity\AsientoContable $asientoContable
     * @return OrdenPago
     */
    public function setAsientoContable(\ADIF\ContableBundle\Entity\AsientoContable $asientoContable = null) {
        $this->asientoContable = $asientoContable;

        return $this;
    }

    /**
     * Get asientoContable
     *
     * @return \ADIF\ContableBundle\Entity\AsientoContable 
     */
    public function getAsientoContable() {
        return $this->asientoContable;
    }

    /**
     * Set asientoContableAnulacion
     *
     * @param \ADIF\ContableBundle\Entity\AsientoContable $asientoContable
     * @return OrdenPago
     */
    public function setAsientoContableAnulacion(\ADIF\ContableBundle\Entity\AsientoContable $asientoContable = null) {
        $this->asientoContableAnulacion = $asientoContable;

        return $this;
    }

    /**
     * Get asientoContableAnulacion
     *
     * @return \ADIF\ContableBundle\Entity\AsientoContable 
     */
    public function getAsientoContableAnulacion() {
        return $this->asientoContableAnulacion;
    }

    /**
     * Set fechaContable
     *
     * @param \DateTime $fechaContable
     * @return OrdenPago
     */
    public function setFechaContable($fechaContable) {
        $this->fechaContable = $fechaContable;

        return $this;
    }

    /**
     * Get fechaContable
     *
     * @return \DateTime 
     */
    public function getFechaContable() {
        return $this->fechaContable;
    }

    /**
     * Set numeroOrdenPago
     *
     * @param \intenger $numeroOrdenPago
     * @return OrdenPago
     */
    public function setNumeroOrdenPago($numeroOrdenPago) {
        $this->numeroOrdenPago = $numeroOrdenPago;

        return $this;
    }

    /**
     * Get numeroOrdenPago
     *
     * @return \intenger 
     */
    public function getNumeroOrdenPago() {
        if (null != $this->numeroOrdenPago) {
            return str_pad($this->numeroOrdenPago, 8, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Set fechaAnulacion
     *
     * @param \DateTime $fechaAnulacion
     * @return OrdenPago
     */
    public function setFechaAnulacion($fechaAnulacion) {
        $this->fechaAnulacion = $fechaAnulacion;

        return $this;
    }

    /**
     * Get fechaAnulacion
     *
     * @return \DateTime 
     */
    public function getFechaAnulacion() {
        return $this->fechaAnulacion;
    }

    /**
     * Set concepto
     *
     * @param string $concepto
     * @return OrdenPago
     */
    public function setConcepto($concepto) {
        $this->concepto = $concepto;

        return $this;
    }

    /**
     * Get concepto
     *
     * @return string 
     */
    public function getConcepto() {
        return $this->concepto;
    }

    /**
     * Set pagoOrdenPago
     *
     * @param PagoOrdenPago $pagoOrdenPago
     * @return OrdenPago
     */
    public function setPagoOrdenPago(PagoOrdenPago $pagoOrdenPago = null) {
        $this->pagoOrdenPago = $pagoOrdenPago;

        return $this;
    }

    /**
     * Get pagoOrdenPago
     *
     * @return PagoOrdenPago 
     */
    public function getPagoOrdenPago() {
        return $this->pagoOrdenPago;
    }

    /**
     * Set estadoOrdenPago
     *
     * @param EstadoOrdenPago $estadoOrdenPago
     * @return OrdenPago
     */
    public function setEstadoOrdenPago(EstadoOrdenPago $estadoOrdenPago) {
        $this->estadoOrdenPago = $estadoOrdenPago;

        return $this;
    }

    /**
     * Get estadoOrdenPago
     *
     * @return EstadoOrdenPago 
     */
    public function getEstadoOrdenPago() {
        return $this->estadoOrdenPago;
    }

    /**
     * Set fechaAutorizacionContable
     *
     * @param \DateTime $fechaAutorizacionContable
     * @return OrdenPago
     */
    public function setFechaAutorizacionContable($fechaAutorizacionContable) {
        $this->fechaAutorizacionContable = $fechaAutorizacionContable;

        return $this;
    }

    /**
     * Get fechaAutorizacionContable
     *
     * @return \DateTime 
     */
    public function getFechaAutorizacionContable() {
        return $this->fechaAutorizacionContable;
    }

    /**
     * Set numeroAutorizacionContable
     *
     * @param \intenger $numeroAutorizacionContable
     * @return OrdenPago
     */
    public function setNumeroAutorizacionContable($numeroAutorizacionContable) {
        $this->numeroAutorizacionContable = $numeroAutorizacionContable;

        return $this;
    }

    /**
     * Get numeroAutorizacionContable
     *
     * @return \intenger 
     */
    public function getNumeroAutorizacionContable() {
        if (null != $this->numeroAutorizacionContable) {
            return str_pad($this->numeroAutorizacionContable, 8, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Set facturaConformada
     *
     * @param boolean $facturaConformada
     * @return OrdenPago
     */
    public function setFacturaConformada($facturaConformada) {
        $this->facturaConformada = $facturaConformada;

        return $this;
    }

    /**
     * Get facturaConformada
     *
     * @return boolean 
     */
    public function getFacturaConformada() {
        return $this->facturaConformada;
    }

    /**
     * Set calculosVerificados
     *
     * @param boolean $calculosVerificados
     * @return OrdenPago
     */
    public function setCalculosVerificados($calculosVerificados) {
        $this->calculosVerificados = $calculosVerificados;

        return $this;
    }

    /**
     * Get calculosVerificados
     *
     * @return boolean 
     */
    public function getCalculosVerificados() {
        return $this->calculosVerificados;
    }

    /**
     * Set impuestosVerificados
     *
     * @param boolean $impuestosVerificados
     * @return OrdenPago
     */
    public function setImpuestosVerificados($impuestosVerificados) {
        $this->impuestosVerificados = $impuestosVerificados;

        return $this;
    }

    /**
     * Get impuestosVerificados
     *
     * @return boolean 
     */
    public function getImpuestosVerificados() {
        return $this->impuestosVerificados;
    }

    /**
     * Get totalBruto
     *
     * @return double
     */
    public function getTotalBruto() {

        $total = 0;
        foreach ($this->getComprobantes() as $comprobante) {
            /* @var $comprobante ComprobanteCompra */
            $importePendientePago = (float)$comprobante->getImportePendientePago();
            $sumaResta = (float)($comprobante->getEsNotaCredito() ? -1 : 1);
            $total += $importePendientePago * $sumaResta;
        }
        
        if ($total < 0) {
            // Si da negativo, me fijo si es por la presicion del punto flotante
            // http://php.net/manual/es/language.types.float.php
            $epsilon = 0.00001;
            if (abs($total) < $epsilon) {
                $total = 0;
            }
        }
        
        return $total;
    }

    /**
     * Get montoRetencionesPorTipoImpuesto
     *
     * @return double
     */
    public function getMontoRetencionesPorTipoImpuesto($denominacionImpuesto) {

        $totalImpuesto = 0;

        foreach ($this->getRetenciones() as $retencion) {

            /* @var $retencion ComprobanteRetencionImpuesto */
            if ($retencion->getRegimenRetencion()->getTipoImpuesto()->getDenominacion() == $denominacionImpuesto) {
                $totalImpuesto += $retencion->getMonto();
            }
        }

        return $totalImpuesto;
    }

    /**
     * Devuelve las retenciones de la OP por tipo de impuesto
     * @param  [type] $denominacionImpuesto [description]
     * @return [type]                       [description]
     */
    public function getRetencionesPorTipoImpuesto($denominacionImpuesto) {
        $retenciones = [];
        foreach ($this->getRetenciones() as $retencion) {
            /* @var $retencion ComprobanteRetencionImpuesto */
            if ($retencion->getRegimenRetencion()->getTipoImpuesto()->getDenominacion() == $denominacionImpuesto) {
                $retenciones[] = $retencion;
            }
        }

        return $retenciones;
    }

    /**
     * Get montoRetenciones
     *
     * @return double
     */
    public function getMontoRetenciones() {

        $totalImpuesto = 0;

        foreach ($this->getRetenciones() as $retencion) {
            /* @var $retencion ComprobanteRetencionImpuesto */
            $totalImpuesto += $retencion->getMonto();
        }

        return $totalImpuesto;
    }

    /**
     * Get montoneto
     *
     * @return double
     */
    public function getMontoNeto() 
	{    
		$montoNeto = $this->getTotalBruto() - $this->getMontoRetenciones() - $this->getMontoAnticipos();
		$montoNetoFormateado = number_format($montoNeto, 2);
        if (abs($montoNetoFormateado) <= '0.01') {
            // Para casos excepcionales que dan a pagar +-0.01
            $montoNeto = 0;
        }
        
        return $montoNeto;
    }

    /**
     * Get anticipos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnticipos() {
        return new ArrayCollection();
    }

    /**
     * Get montoRetenciones
     *
     * @return double
     */
    public function getMontoAnticipos() {

        $totalAnticipo = 0;

        foreach ($this->getAnticipos() as $anticipo) {
            $totalAnticipo += $anticipo->getMonto();
        }

        return $totalAnticipo;
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return null;
    }

    /**
     * 
     * @return boolean
     */
    public function getRequiereVisado() {
        return true;
    }

    /**
     * 
     * @return type
     */
    public function getNumero() {

        return $this->numeroOrdenPago != null //
                ? $this->numeroOrdenPago //
                : $this->numeroAutorizacionContable;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsOrdenPagoParcial() {
        return false;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsAutorizacionContable() {

        return $this->numeroOrdenPago == null;
    }

    /**
     * 
     * @return boolean
     */
    public function getEstaAnulada() {

        return $this->estadoOrdenPago->getDenominacionEstado() == ConstanteEstadoOrdenPago::ESTADO_ANULADA;
    }

    /**
     * Add retenciones
     *
     * @param \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto $retenciones
     * @return OrdenPagoComprobante
     */
    public function addRetencion(\ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto $retenciones) {
        $this->retenciones[] = $retenciones;

        return $this;
    }

    /**
     * Remove retenciones
     *
     * @param \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto $retenciones
     */
    public function removeRetencion(\ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto $retenciones) {
        $this->retenciones->removeElement($retenciones);
    }

    /**
     * Get retenciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRetenciones() {
        return $this->retenciones;
    }

    /**
     * Set fueVista
     *
     * @param boolean $fueVista
     * @return OrdenPago
     */
    public function setFueVista($fueVista) {
        $this->fueVista = $fueVista;

        return $this;
    }

    /**
     * Get fueVista
     *
     * @return boolean 
     */
    public function getFueVista() {
        return $this->fueVista;
    }

    /**
     * 
     * @return \ADIF\ContableBundle\Entity\Ejecutado
     */
    public function getEjecutadoEntity() {

        return new Ejecutado();
    }

    public function getEstaAnuladoALaFecha($fecha) {
        return $this->getFechaAnulacion() != null && $this->getFechaAnulacion() <= $fecha;
    }

    public function getEstaPagadaALaFecha($fecha) {
        return $this->getNumeroOrdenPago() && !$this->getEstaAnuladoALaFecha($fecha) && $this->getFechaContable() <= $fecha;
    }

}
