<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteAlicuotaIva;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Comprobante
 *
 * @author Darío Rapetti
 * created 21/10/2014
 * 
 * @ORM\Table(name="comprobante")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "comprobante_general" = "Comprobante",
 *      "comprobante_compra" = "ComprobanteCompra",
 *      "comprobante_obra" = "ADIF\ContableBundle\Entity\Obras\ComprobanteObra",
 *      "nota_credito_obra" = "ADIF\ContableBundle\Entity\Obras\NotaCreditoObra",
 *      "nota_debito_obra" = "ADIF\ContableBundle\Entity\Obras\NotaDebitoObra",
 *      "nota_credito_consultoria" = "ADIF\ContableBundle\Entity\Consultoria\NotaCreditoConsultoria",
 *      "nota_debito_interes_obra" = "ADIF\ContableBundle\Entity\Obras\NotaDebitoInteresObra",
 *      "ticket_factura_obra" = "ADIF\ContableBundle\Entity\Obras\TicketFacturaObra",
 *      "recibo_obra" = "ADIF\ContableBundle\Entity\Obras\ReciboObra",
 *      "factura_obra" = "ADIF\ContableBundle\Entity\Obras\FacturaObra",
 *      "comprobante_consultoria" = "ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria",
 *      "factura_consultoria" = "ADIF\ContableBundle\Entity\Consultoria\FacturaConsultoria",
 *      "recibo_consultoria" = "ADIF\ContableBundle\Entity\Consultoria\ReciboConsultoria",
 *      "comprobante_egreso_valor" = "ADIF\ContableBundle\Entity\EgresoValor\ComprobanteEgresoValor",
 *      "factura" = "Factura",
 *      "ticket_factura" = "TicketFactura",
 *      "recibo" = "Recibo",
 *      "nota_debito" = "NotaDebito",
 *      "nota_credito" = "NotaCredito",
 *      "anticipo_proveedor" = "AnticipoProveedor",
 *      "comprobante_venta" = "ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta",
 *      "factura_venta" = "ADIF\ContableBundle\Entity\Facturacion\FacturaVenta",
 *      "factura_venta_general" = "ADIF\ContableBundle\Entity\Facturacion\FacturaVentaGeneral",
 *      "factura_pliego" = "ADIF\ContableBundle\Entity\Facturacion\FacturaPliego",
 *      "factura_ingreso" = "ADIF\ContableBundle\Entity\Facturacion\FacturaIngreso",
 *      "factura_alquiler" = "ADIF\ContableBundle\Entity\Facturacion\FacturaAlquiler",
 *      "factura_chatarra" = "ADIF\ContableBundle\Entity\Facturacion\FacturaChatarra",
 *      "nota_debito_venta" = "ADIF\ContableBundle\Entity\Facturacion\NotaDebitoVenta",
 *      "nota_debito_venta_general" = "ADIF\ContableBundle\Entity\Facturacion\NotaDebitoVentaGeneral",
 *      "nota_debito_pliego" = "ADIF\ContableBundle\Entity\Facturacion\NotaDebitoPliego",
 *      "nota_debito_interes" = "ADIF\ContableBundle\Entity\Facturacion\NotaDebitoInteres",
 *      "nota_credito_venta" = "ADIF\ContableBundle\Entity\Facturacion\NotaCreditoVenta",
 *      "nota_credito_venta_general" = "ADIF\ContableBundle\Entity\Facturacion\NotaCreditoVentaGeneral",
 *      "nota_credito_pliego" = "ADIF\ContableBundle\Entity\Facturacion\NotaCreditoPliego",
 *      "cupon_venta" = "ADIF\ContableBundle\Entity\Facturacion\CuponVenta",
 *      "cupon_venta_general" = "ADIF\ContableBundle\Entity\Facturacion\CuponVentaGeneral",
 *      "cupon_venta_plazo" = "ADIF\ContableBundle\Entity\Facturacion\CuponVentaPlazo",
 *      "cupon_pliego" = "ADIF\ContableBundle\Entity\Facturacion\CuponPliego",
 *      "comprobante_rendicion_liquido_producto" = "ADIF\ContableBundle\Entity\Facturacion\ComprobanteRendicionLiquidoProducto"
 * })
 */
class Comprobante extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="fecha_comprobante", type="datetime", nullable=false)
     */
    protected $fechaComprobante;

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
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=8, nullable=true)
     */
    protected $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=1000, nullable=true)
     */
    protected $observaciones;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="LetraComprobante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_letra_comprobante", referencedColumnName="id", nullable=true)
     * })
     */
    protected $letraComprobante;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoComprobante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_comprobante", referencedColumnName="id", nullable=false)
     * })
     */
    protected $tipoComprobante;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoComprobante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_estado_comprobante", referencedColumnName="id", nullable=false)
     * })
     */
    protected $estadoComprobante;

    /**
     * @var ComprobanteImpresion
     * 
     * @ORM\ManyToOne(targetEntity="ComprobanteImpresion", cascade={"all"})
     * @ORM\JoinColumn(name="id_comprobante_impresion", referencedColumnName="id", nullable=true)
     */
    protected $comprobanteImpresion;

    /**
     * @var TipoMoneda
     *
     * @ORM\ManyToOne(targetEntity="TipoMoneda")
     * @ORM\JoinColumn(name="id_tipo_moneda", referencedColumnName="id", nullable=true)
     * 
     */
    protected $tipoMoneda;

    /**
     * @var double
     * @ORM\Column(name="total", type="decimal", precision=15, scale=2, nullable=false)
     */
    protected $total;

    /**
     * @var double
     * @ORM\Column(name="tipo_cambio", type="decimal", precision=10, scale=4, nullable=false)
     */
    protected $tipoCambio;

    /**
     * @var integer
     * 
     * @ORM\Column(name="numero_cuota", type="integer", nullable=true)
     */
    protected $numeroCuota;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_referencia", type="string", length=50, nullable=true)
     */
    protected $numeroReferencia;

    /**
     *
     * @var RenglonComprobante
     * 
     * @ORM\OneToMany(targetEntity="RenglonComprobante", mappedBy="comprobante", cascade={"all"})
     */
    protected $renglonesComprobante;

    /**
     *
     * @var RenglonPercepcion
     * 
     * @ORM\OneToMany(targetEntity="RenglonPercepcion", mappedBy="comprobante", cascade={"all"})
     */
    protected $renglonesPercepcion;

    /**
     *
     * @var RenglonImpuesto
     * 
     * @ORM\OneToMany(targetEntity="RenglonImpuesto", mappedBy="comprobante", cascade={"all"})
     */
    protected $renglonesImpuesto;

    /**
     *
     * @var PagoParcial
     * 
     * @ORM\OneToMany(targetEntity="PagoParcial", mappedBy="comprobante", cascade={"all"})
     */
    protected $pagosParciales;

    /**
     * @var double
     * @ORM\Column(name="saldo", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $saldo;

    /**
     * @var NotaCreditoComprobante
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\NotaCreditoComprobante", mappedBy="notaCredito")
     *
     */
    protected $notasCredito;

    /**
     * @var NotaCreditoComprobante
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\NotaCreditoComprobante", mappedBy="comprobante")
     *
     */
    protected $comprobantesAcreditados;
    
    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_vencimiento", type="date", nullable=true)
     */
    protected $fechaVencimiento;
	
	 /**
     *
     * @var comprobanteAjuste
     * 
     * @ORM\OneToMany(targetEntity="ComprobanteAjuste", mappedBy="comprobante")
     */
    protected $comprobantesAjustes;
	
	/**
     * @ORM\ManyToMany(targetEntity="OrdenPagoLog", mappedBy="comprobantes")
     */
    protected $ordenPagoLog;

    /**
     * 
     */
    public function __construct() {

        $this->tipoCambio = 1;

        $this->fechaContable = new \DateTime();

        $this->renglonesPercepcion = new ArrayCollection();
        $this->renglonesImpuesto = new ArrayCollection();
        $this->renglonesComprobante = new ArrayCollection();
        $this->pagosParciales = new ArrayCollection();
        $this->notasCredito = new ArrayCollection();
        $this->comprobantesAcreditados = new ArrayCollection();
		$this->comprobantesAjustes = new ArrayCollection();
		$this->ordenPagoLog = new ArrayCollection();
    }

    /**
     * 
     */
    public function __clone() {
        $this->id = null;
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
     * Set fechaComprobante
     *
     * @param \DateTime $fechaComprobante
     * @return Comprobante
     */
    public function setFechaComprobante($fechaComprobante) {
        $this->fechaComprobante = $fechaComprobante;

        return $this;
    }

    /**
     * Get fechaComprobante
     *
     * @return \DateTime 
     */
    public function getFechaComprobante() {
        return $this->fechaComprobante;
    }

    /**
     * Set asientoContable
     *
     * @param \ADIF\ContableBundle\Entity\AsientoContable $asientoContable
     * @return Comprobante
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
     * @return Comprobante
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
     * @return Comprobante
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
     * Set numero
     *
     * @param string $numero
     * @return Comprobante
     */
    public function setNumero($numero) {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero() {
        return $this->numero;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return Comprobante
     */
    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones() {
        return $this->observaciones;
    }

    /**
     * Set comprobanteImpresion
     *
     * @param ComprobanteImpresion $comprobanteImpresion
     *
     * @return Comprobante
     */
    public function setComprobanteImpresion(ComprobanteImpresion $comprobanteImpresion = null) {
        $this->comprobanteImpresion = $comprobanteImpresion;

        return $this;
    }

    /**
     * Get comprobanteImpresion
     *
     * @return ComprobanteImpresion
     */
    public function getComprobanteImpresion() {
        return $this->comprobanteImpresion;
    }

    /**
     * Set tipoMoneda
     *
     * @param TipoMoneda $tipoMoneda
     * @return Comprobante
     */
    public function setTipoMoneda(TipoMoneda $tipoMoneda = null) {
        $this->tipoMoneda = $tipoMoneda;

        return $this;
    }

    /**
     * Get tipoMoneda
     *
     * @return TipoMoneda 
     */
    public function getTipoMoneda() {
        return $this->tipoMoneda;
    }

    /**
     * Set total
     *
     * @param string $total
     * @return Comprobante
     */
    public function setTotal($total) {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string 
     */
    public function getTotal() {
        return $this->total;
    }

    /**
     * Set tipoCambio
     *
     * @param string $tipoCambio
     * @return Comprobante
     */
    public function setTipoCambio($tipoCambio) {
        $this->tipoCambio = $tipoCambio;

        return $this;
    }

    /**
     * Get tipoCambio
     *
     * @return string 
     */
    public function getTipoCambio() {
        return $this->tipoCambio;
    }

    /**
     * Set letraComprobante
     *
     * @param LetraComprobante $letraComprobante
     * @return Comprobante
     */
    public function setLetraComprobante(LetraComprobante $letraComprobante) {
        $this->letraComprobante = $letraComprobante;

        return $this;
    }

    /**
     * Get letraComprobante
     *
     * @return LetraComprobante 
     */
    public function getLetraComprobante() {
        return $this->letraComprobante;
    }

    /**
     * Set tipoComprobante
     *
     * @param TipoComprobante $tipoComprobante
     * @return Comprobante
     */
    public function setTipoComprobante(TipoComprobante $tipoComprobante) {
        $this->tipoComprobante = $tipoComprobante;

        return $this;
    }

    /**
     * Get tipoComprobante
     *
     * @return TipoComprobante 
     */
    public function getTipoComprobante() {
        return $this->tipoComprobante;
    }

    /**
     * Set estadoComprobante
     *
     * @param EstadoComprobante $estadoComprobante
     * @return Comprobante
     */
    public function setEstadoComprobante(EstadoComprobante $estadoComprobante) {
        $this->estadoComprobante = $estadoComprobante;

        return $this;
    }

    /**
     * Get estadoComprobante
     *
     * @return EstadoComprobante 
     */
    public function getEstadoComprobante() {
        return $this->estadoComprobante;
    }

    /**
     * Get renglonesComprobante
     *
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getRenglonesComprobante() {
        return $this->renglonesComprobante;
    }

    /**
     * Add renglonComprobante
     *
     * @param RenglonComprobante $renglonComprobante
     * @return ComprobanteCompra
     */
    public function addRenglonesComprobante(RenglonComprobante $renglonComprobante) {
        $this->renglonesComprobante[] = $renglonComprobante;
        $renglonComprobante->setComprobante($this);
        return $this;
    }

    /**
     * Remove renglonComprobante
     *
     * @param RenglonComprobante $renglonComprobante
     */
    public function removeRenglonesComprobante(RenglonComprobante $renglonComprobante) {
        $this->renglonesComprobante->removeElement($renglonComprobante);
        $renglonComprobante->setComprobante(null);
    }

    /**
     * Set numeroCuota
     *
     * @param integer $numeroCuota
     * @return Comprobante
     */
    public function setNumeroCuota($numeroCuota) {
        $this->numeroCuota = $numeroCuota;

        return $this;
    }

    /**
     * Get numeroCuota
     *
     * @return integer 
     */
    public function getNumeroCuota() {
        return $this->numeroCuota;
    }

    /**
     * Get numeroReferencia
     *
     * @return string 
     */
    public function getNumeroReferencia() {
        return $this->numeroReferencia;
    }

    /**
     * Set numeroReferencia
     *
     * @param string $numeroReferencia
     * @return Comprobante
     */
    public function setNumeroReferencia($numeroReferencia) {
        $this->numeroReferencia = $numeroReferencia;

        return $this;
    }

    /**
     * Retorna el neto de cada renglon del comprobante (es decir, 
     * ya aplicadas las bonificaciones) y lo totaliza según la 
     * alicuota recibida como parámetro.
     * 
     * @param type $valorAlicuotaIVA
     * @return type
     */
    public function getImporteTotalNetoByAlicuota($valorAlicuotaIVA) {
        $totalNeto = 0;
        foreach ($this->getRenglonesComprobante() as $renglonComprobante) {
            $alicuotaIVA = $renglonComprobante->getAlicuotaIva();
            if (null != $alicuotaIVA && $alicuotaIVA->getValor() == $valorAlicuotaIVA) {
                $totalNeto += $renglonComprobante->getMontoNetoBonificado();
            }
        }

        return $totalNeto;
    }

    /**
     * 
     * @return type
     */
    public function getImporteTotalNeto() {

        $totalNeto = 0;

        foreach ($this->getRenglonesComprobante() as $renglonComprobante) {
            $totalNeto += $renglonComprobante->getMontoNeto();
        }

        return $totalNeto;
    }

    /**
     * 
     * @return type
     */
    public function getImporteTotalExento() {
        $totalExento = 0;
        foreach ($this->getRenglonesComprobante() as $renglonComprobante) {
            $alicuotaIVA = $renglonComprobante->getAlicuotaIva();
            if (null != $alicuotaIVA && $alicuotaIVA->getValor() == ConstanteAlicuotaIva::ALICUOTA_0) {
                $totalExento += $renglonComprobante->getMontoNeto();
            }
        }

        return $totalExento;
    }

    /**
     * 
     * @param type $valorAlicuotaIVA
     * @return type
     */
    public function getImporteTotalIVAByAlicuota($valorAlicuotaIVA) {
        $totalIVA = 0;
        foreach ($this->getRenglonesComprobante() as $renglonComprobante) {
            $alicuotaIVA = $renglonComprobante->getAlicuotaIva();
            if (null != $alicuotaIVA && $alicuotaIVA->getValor() == $valorAlicuotaIVA) {
                $totalIVA += $renglonComprobante->getMontoAdicionalProrrateadoDiscriminado()['iva'];
            }
        }

        return $totalIVA;
    }

    /**
     * 
     * @return type
     */
    public function getImporteTotalIVA() {
        $totalIVA = 0;
        foreach ($this->getRenglonesComprobante() as $renglonComprobante) {
            $alicuotaIVA = $renglonComprobante->getAlicuotaIva();
            if (null != $alicuotaIVA && $alicuotaIVA->getValor() != ConstanteAlicuotaIva::ALICUOTA_0) {
                $totalIVA += $renglonComprobante->getMontoIva();
            }
        }

        return $totalIVA;
    }

    /**
     * Set renglonesPercepcion
     *
     * @param \Doctrine\Common\Collections\ArrayCollection 
     * @return Comprobante
     */
    public function setRenglonesPercepcion(ArrayCollection $renglonesPercepcion) {
        $this->renglonesPercepcion = $renglonesPercepcion;

        return $this;
    }

    /**
     * Get renglonesPercepcion
     *
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getRenglonesPercepcion() {
        return $this->renglonesPercepcion;
    }

    /**
     * Add renglonPercepcion
     *
     * @param RenglonPercepcion $renglonPercepcion
     * @return Comprobante
     */
    public function addRenglonesPercepcion(RenglonPercepcion $renglonPercepcion) {
        $this->renglonesPercepcion[] = $renglonPercepcion;
        $renglonPercepcion->setComprobante($this);
        return $this;
    }

    /**
     * Remove renglonPercepcion
     *
     * @param RenglonPercepcion $renglonPercepcion
     */
    public function removeRenglonesPercepcion(RenglonPercepcion $renglonPercepcion) {
        $this->renglonesPercepcion->removeElement($renglonPercepcion);
        $renglonPercepcion->setComprobante(null);
    }

    /**
     * Set renglonesImpuesto
     *
     * @param \Doctrine\Common\Collections\ArrayCollection 
     * @return Comprobante
     */
    public function setRenglonesImpuesto(ArrayCollection $renglonesImpuesto) {
        $this->renglonesImpuesto = $renglonesImpuesto;

        return $this;
    }

    /**
     * Get renglonesImpuesto
     *
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getRenglonesImpuesto() {
        return $this->renglonesImpuesto;
    }

    /**
     * Add renglonImpuesto
     *
     * @param RenglonImpuesto $renglonImpuesto
     * @return Comprobante
     */
    public function addRenglonesImpuesto(RenglonImpuesto $renglonImpuesto) {
        $this->renglonesImpuesto[] = $renglonImpuesto;
        $renglonImpuesto->setComprobante($this);
        return $this;
    }

    /**
     * Remove renglonImpuesto
     *
     * @param RenglonImpuesto $renglonImpuesto
     */
    public function removeRenglonesImpuesto(RenglonImpuesto $renglonImpuesto) {
        $this->renglonesImpuesto->removeElement($renglonImpuesto);
        $renglonImpuesto->setComprobante(null);
    }

    /**
     * Add pagosParciale
     *
     * @param PagoParcial $pagosParciale
     *
     * @return Comprobante
     */
    public function addPagosParciale(PagoParcial $pagosParciale) {
        $this->pagosParciales[] = $pagosParciale;

        return $this;
    }

    /**
     * Remove pagosParciale
     *
     * @param PagoParcial $pagosParciale
     */
    public function removePagosParciale(PagoParcial $pagosParciale) {
        $this->pagosParciales->removeElement($pagosParciale);
    }

    /**
     * Get pagosParciales
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPagosParciales() {
        return $this->pagosParciales;
    }

    /**
     * 
     * @return type
     */
    public function getImporteTotalPercepcion() {

        $totalPercepcion = 0;

        foreach ($this->getRenglonesPercepcion() as $renglonPercepcion) {
            $totalPercepcion += $renglonPercepcion->getMonto();
        }

        return $totalPercepcion;
    }

    /**
     * 
     * @param type $conceptoImpuesto
     * @return type
     */
    public function getImporteTotalImpuesto() {

        $totalImpuesto = 0;

        foreach ($this->getRenglonesImpuesto() as $renglonImpuesto) {

            $totalImpuesto += $renglonImpuesto->getMonto();
        }

        return $totalImpuesto;
    }

    /**
     * 
     * @return type
     */
    public function getTotalNeto() {

        $total = 0;

        foreach ($this->renglonesComprobante as $renglon) {
            $total += $renglon->getMontoNeto();
        }

        return $total;
    }

    /**
     * 
     * @return type
     */
    public function getImporteNetoGravado() {

        $totalNetoGravado = 0;

        foreach ($this->getRenglonesComprobante() as $renglonComprobante) {
            $alicuotaIVA = $renglonComprobante->getAlicuotaIva();

            if (null != $alicuotaIVA && $alicuotaIVA->getValor() != ConstanteAlicuotaIva::ALICUOTA_0) {
                $totalNetoGravado += $renglonComprobante->getMontoNeto();
            }
        }

        return $totalNetoGravado;
    }

    /**
     * 
     * @return type
     */
    public function getImporteNetoNoGravado() {

        $totalNetoNoGravado = 0;

        foreach ($this->getRenglonesComprobante() as $renglonComprobante) {
            $alicuotaIVA = $renglonComprobante->getAlicuotaIva();

            if (null != $alicuotaIVA && $alicuotaIVA->getValor() == ConstanteAlicuotaIva::ALICUOTA_0) {
                $totalNetoNoGravado += $renglonComprobante->getMontoNeto();
            }
        }

        return $totalNetoNoGravado;
    }

    /**
     * 
     * @return type
     */
    public function getTotalMO() {

        return $this->total / $this->tipoCambio;
    }

    /**
     * 
     * @return type
     */
    public function getTotalMCL() {

        return $this->total;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsNotaCredito() {
        return false;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsComprobanteCompra() {
        return false;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsComprobanteServicio() {
        return false;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsComprobanteObra() {
        return false;
    }

    /**
     * 
     * @param type $conceptoPercepcion
     * @return type
     */
    public function getImporteTotalPercepcionByConcepto($conceptoPercepcion) {
        $totalPercepcion = 0;
        foreach ($this->getRenglonesPercepcion() as $renglonPercepcion) {
            if ($renglonPercepcion->getConceptoPercepcion() == $conceptoPercepcion) {
                $totalPercepcion += $renglonPercepcion->getMonto();
            }
        }

        return $totalPercepcion;
    }

    /**
     * 
     * @param type $conceptoImpuesto
     * @return type
     */
    public function getImporteTotalImpuestoByConcepto($conceptoImpuesto) {
        $totalImpuesto = 0;
        foreach ($this->getRenglonesImpuesto() as $renglonImpuesto) {
            if ($renglonImpuesto->getConceptoImpuesto() == $conceptoImpuesto) {
                $totalImpuesto += $renglonImpuesto->getMonto();
            }
        }

        return $totalImpuesto;
    }

    /**
     * 
     * @return float
     */
    public function getImporteTotalFactura() {

        return $this->getImporteTotalNeto() //
                + $this->getImporteTotalIVA() //
                + $this->getImporteTotalPercepcion() //
                + $this->getImporteTotalImpuesto();
    }

    /**
     * Get fechaIngresoADIF
     * 
     * @return type
     */
    public function getFechaIngresoADIF() {

        return $this->fechaCreacion;
    }

    /**
     * Get adicionales
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdicionales() {
        return new ArrayCollection();
    }

    /**
     * 
     * @return int
     */
    public function getTotalIva() {
        return 0;
    }

    /**
     * 
     * @return double
     */
    public function getImportePendientePago() {
        $importePendientePago = $this->getTotal();
        foreach ($this->pagosParciales as $pagoParcial) {
            /* @var $pagoParcial PagoParcial */

            if (!$pagoParcial->getAnulado()) {
                $ordenPago = $pagoParcial->getOrdenPago();
                if ($ordenPago != null && $ordenPago->getEstadoOrdenPago() != ConstanteEstadoOrdenPago::ESTADO_ANULADA) {
                    $importePendientePago -= $pagoParcial->getTotalNeto();
                }
            }
        }

        return $importePendientePago;
    }

    /**
     * 
     * @return boolean
     */
    public function esComprobanteVentaGeneral() {
        return false;
    }

    /**
     * 
     * @return boolean
     */
    public function getTienePagoParcialPendientePago() {
        $tienePagoParcialPendientePago = false;

        foreach ($this->pagosParciales as $pagoParcial) {
            if (!$pagoParcial->getAnulado()) {
                $denominacionEstado = $pagoParcial->getOrdenPago()->getEstadoOrdenPago()->getDenominacionEstado();

                /* @var $pagoParcial PagoParcial */
                $tienePagoParcialPendientePago = (
                        $denominacionEstado == ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_PAGO //
                        || $denominacionEstado == ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_AUTORIZACION
                        );

                if ($tienePagoParcialPendientePago) {
                    break;
                }
            }
        }

        return $tienePagoParcialPendientePago;
    }

    /**
     * 
     * @return type
     */
    public function getOrdenPagoSinAnular() {

        return null;
    }

    /**
     * Set saldo
     *
     * @param string $saldo
     * @return ComprobanteVenta
     */
    public function setSaldo($saldo) {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Get saldo
     *
     * @return string 
     */
    public function getSaldo() {
        return $this->saldo;
    }

    /**
     * Add notasCredito
     *
     * @param \ADIF\ContableBundle\Entity\NotaCreditoComprobante $notasCredito
     * @return Comprobante
     */
    public function addNotasCredito(\ADIF\ContableBundle\Entity\NotaCreditoComprobante $notasCredito) {
        $this->notasCredito[] = $notasCredito;

        return $this;
    }

    /**
     * Remove notasCredito
     *
     * @param \ADIF\ContableBundle\Entity\NotaCreditoComprobante $notasCredito
     */
    public function removeNotasCredito(\ADIF\ContableBundle\Entity\NotaCreditoComprobante $notasCredito) {
        $this->notasCredito->removeElement($notasCredito);
    }

    /**
     * Get notasCredito
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotasCredito() {
        return $this->notasCredito;
    }

    /**
     * Add comprobantesAcreditados
     *
     * @param \ADIF\ContableBundle\Entity\NotaCreditoComprobante $comprobantesAcreditados
     * @return Comprobante
     */
    public function addComprobantesAcreditado(\ADIF\ContableBundle\Entity\NotaCreditoComprobante $comprobantesAcreditados) {
        $this->comprobantesAcreditados[] = $comprobantesAcreditados;

        return $this;
    }

    /**
     * Remove comprobantesAcreditados
     *
     * @param \ADIF\ContableBundle\Entity\NotaCreditoComprobante $comprobantesAcreditados
     */
    public function removeComprobantesAcreditado(\ADIF\ContableBundle\Entity\NotaCreditoComprobante $comprobantesAcreditados) {
        $this->comprobantesAcreditados->removeElement($comprobantesAcreditados);
    }

    /**
     * Get comprobantesAcreditados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComprobantesAcreditados() {
        return $this->comprobantesAcreditados;
    }

    public function getEstaAnulado() {
        return $this->getEstadoComprobante()->getId() == EstadoComprobante::__ESTADO_ANULADO;
    }

    public function getSaldoALaFecha($fecha) {
        $saldo = $this->getTotal();
        /* @var $ordenPago OrdenPago */
        $ordenPago = $this->getOrdenPago();
        if ($ordenPago != null) {
            if ($ordenPago->getNumeroOrdenPago() != null && ($ordenPago->getFechaContable() <= $fecha && ($ordenPago->getFechaAnulacion() == null || $ordenPago->getFechaAnulacion() >= $fecha))) {
                //A la fecha tiene una OP no anulada
                return 0;
            }
        }
        foreach ($this->getPagosParciales() as $pagoParcial) {
            /* @var $pagoParcial PagoParcial */
            $ordenPago = $pagoParcial->getOrdenPago();
            if ($ordenPago->getNumeroOrdenPago() != null && ($ordenPago->getFechaContable() <= $fecha && ($ordenPago->getFechaAnulacion() == null || $ordenPago->getFechaAnulacion() >= $fecha))) {
                //A la fecha tiene un pago parcial
                $saldo -= $pagoParcial->getImporte();
            }
        }
        /* @var $comprobante Comprobante */
        foreach ($this->getComprobantesAcreditadosNoAnuladosALaFecha($fecha) as $comprobante) {
            $notaCredito = $comprobante->getNotaCredito();
            foreach ($notaCredito->getRenglonesComprobante() as $renglonComprobante) {

                if ($renglonComprobante->getRenglonAcreditado()->getComprobante()->getId() == $comprobante->getId()) {

                    $saldo -= $renglonComprobante->getMontoBruto();
                }
            }
        }
		
		foreach($this->comprobantesAjustes as $comprobanteAjuste) {
			
			if ($this->getEsNotaCredito()) {
				$saldo *= -1;
			}
			
			$sumaResta = $comprobanteAjuste->getEsNotaCredito() ? -1 : 1;
			$saldo += $comprobanteAjuste->getTotal() * $sumaResta;
			
			$epsilon = 0.00001;
		
			if ($saldo <= $epsilon) {
				$saldo = 0;
			}
		}

        return $saldo;
    }

    /**
     * Get ordenPago
     *     
     */
    public function getOrdenPago() {
        return null;
    }

    /**
     * Get fechaAnulacion
     *     
     */
    public function getFechaAnulacion() {
        return null;
    }

    /**
     * Get estaAnuladoALaFecha
     *     
     */
    public function getEstaAnuladoALaFecha($fecha) {
        return $this->getFechaAnulacion() != null && $this->getFechaAnulacion() <= $fecha;
    }

    public function getPagosParcialesPagosALaFecha($fecha) {
        $pagosParciales = array();

        foreach ($this->getPagosParciales() as $pagoParcial) {
            /* @var $pagoParcial PagoParcial */
            $ordenPago = $pagoParcial->getOrdenPago();
            if ($ordenPago->getNumero() != null && ($ordenPago->getFechaContable() <= $fecha && ($ordenPago->getFechaAnulacion() == null || $ordenPago->getFechaAnulacion() >= $fecha))) {
                //A la fecha tiene un pago parcial
                $pagosParciales[] = $pagoParcial;
            }
        }

        return $pagosParciales;
    }

    public function getComprobantesAcreditadosNoAnuladosALaFecha($fecha) {
        $comprobantesAcreditados = array();

        /* @var $comprobanteAcreditado Comprobante */
        foreach ($this->comprobantesAcreditados as $comprobanteAcreditado) {
            if ($comprobanteAcreditado->fechaContable <= $fecha && !(getEstaAnuladoALaFecha($fecha))) {
                $comprobantesAcreditados[] = $comprobanteAcreditado;
            }
        }

        return $comprobantesAcreditados;
    }
    
     /**
     * Set fechaVencimiento
     *
     * @param \Date $fechaVencimiento
     * @return Vencimiento
     */
    public function setFechaVencimiento($fechaVencimiento) {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    /**
     * Get fechaVencimiento
     *
     * @return \Date
     */
    public function getFechaVencimiento() {
        return $this->fechaVencimiento;    
    }
	
	
	public function getComprobantesAjustes()
	{
		return $this->comprobantesAjustes;
	}
	
	public function setComprobantesAjustes(\ADIF\ContableBundle\Entity\ComprobanteAjuste $comprobantesAjustes)
	{
		$this->comprobantesAjustes = $comprobantesAjustes;
	}
	
    public function addComprobantesAjustes(\ADIF\ContableBundle\Entity\ComprobanteAjuste $comprobanteAjuste) 
	{
        $this->comprobantesAjustes[] = $comprobanteAjuste;

        return $this;
    }
   
    public function removeComprobantesAjustes(\ADIF\ContableBundle\Entity\ComprobanteAjuste $comprobanteAjuste) 
	{
        $this->comprobantesAjustes->removeElement($comprobanteAjuste);
		return $this;
    }
	
	public function getAlicuotaIva($valorAbsoluto = true) 
	{
		$alicuotaIVA = ($valorAbsoluto) ? '0,00' : '0,00 %';
		if ($this->getRenglonesComprobante() != null) {
			foreach ($this->getRenglonesComprobante() as $renglonComprobante) {
				if ($valorAbsoluto) {
					$alicuotaIVA = $renglonComprobante->getAlicuotaIva()->getValor();
				} else {
					$alicuotaIVA = $renglonComprobante->getAlicuotaIva();
				}
			}
		}
		
		return $alicuotaIVA;
	}
	
	public function setOrdenPagoLog($ordenPagoLog)
	{
		$this->ordenPagoLog = $ordenPagoLog;
		
		return $this;
	}
	
	public function addOrdenPagoLog($ordenPagoLog)
	{
		$this->ordenPagoLog->add($ordenPagoLog);
		
		return $this;
	}
	
	public function removeOrdenPagoLog($ordenPagoLog)
	{
		$this->ordenPagoLog->removeElement($ordenPagoLog);
		
		return $this;
	}
	
	public function getOrdenPagoLog()
	{
		return $this->ordenPagoLog;
	}
	
	public function getPrimeraDescripcionRenglon()
	{
		if ($this->renglonesComprobante != null && !$this->renglonesComprobante->isEmpty()) {
			if ($this->renglonesComprobante->first() != null) {
				return $this->renglonesComprobante->first()->getDescripcion();
			}
		}
		return null;
	}
	
	public function esComprobanteRendicionLiquidoProducto()
	{
		return false;
	}
}
