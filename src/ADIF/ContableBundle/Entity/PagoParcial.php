<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * PagoParcial
 * 
 * @author Manuel Becerra
 * created 14/09/2015
 *
 * @ORM\Table(name="pago_parcial")
 * @ORM\Entity
 */
class PagoParcial extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="id_proveedor", type="integer", nullable=false)
     */
    protected $idProveedor;

    /**
     * @var ADIF\ComprasBundle\Entity\Proveedor
     */
    protected $proveedor;

    /**
     * @var Comprobante
     *
     * @ORM\ManyToOne(targetEntity="Comprobante", inversedBy="pagosParciales")
     * @ORM\JoinColumn(name="id_comprobante", referencedColumnName="id", nullable=false)
     */
    protected $comprobante;

    /**
     * @var OrdenPagoPagoParcial
     *
     * @ORM\ManyToOne(targetEntity="OrdenPagoPagoParcial")
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id", nullable=false)
     */
    protected $ordenPago;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_pago", type="datetime", nullable=false)
     */
    protected $fechaPago;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=15, scale=2, nullable=false)
     */
    protected $importe;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=1000, nullable=true)
     */
    protected $observaciones;

    /**
     * @var boolean
     *
     * @ORM\Column(name="anulado", type="boolean", nullable=true, options={"default": 0})
     */
    protected $anulado;
	
	/***********************/
	
	/**
     * @var double
     * @ORM\Column(name="retencion_suss", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $retencionSuss;
	
	/**
     * @var double
     * @ORM\Column(name="retencion_iibb", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $retencionIibb;
	
	/**
     * @var double
     * @ORM\Column(name="retencion_ganancias", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $retencionGanancias;
	
	/**
     * @var double
     * @ORM\Column(name="retencion_iva", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $retencionIva;
	
	
	
	/***********************/
	
	/**
     * @var double
     * @ORM\Column(name="monto_total_retenciones", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $montoTotalRetenciones;
	
	/**
     * @var double
     * @ORM\Column(name="diferencia_importe_retenciones", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $diferenciaImporteRetenciones;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set idProveedor
     *
     * @param integer $idProveedor
     *
     * @return PagoParcial
     */
    public function setIdProveedor($idProveedor) {
        $this->idProveedor = $idProveedor;

        return $this;
    }

    /**
     * Get idProveedor
     *
     * @return integer
     */
    public function getIdProveedor() {
        return $this->idProveedor;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     */
    public function setProveedor($proveedor) {

        if (null != $proveedor) {
            $this->idProveedor = $proveedor->getId();
        } //.
        else {
            $this->idProveedor = null;
        }

        $this->proveedor = $proveedor;
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return $this->proveedor;
    }

    /**
     * Set comprobante
     *
     * @param \ADIF\ContableBundle\Entity\Comprobante $comprobante
     *
     * @return PagoParcial
     */
    public function setComprobante(\ADIF\ContableBundle\Entity\Comprobante $comprobante) {
        $this->comprobante = $comprobante;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return \ADIF\ContableBundle\Entity\Comprobante
     */
    public function getComprobante() {
        return $this->comprobante;
    }

    /**
     * Set ordenPago
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPagoPagoParcial $ordenPago
     *
     * @return PagoParcial
     */
    public function setOrdenPago(\ADIF\ContableBundle\Entity\OrdenPagoPagoParcial $ordenPago) {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return \ADIF\ContableBundle\Entity\OrdenPagoPagoParcial
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }

    /**
     * Set fechaPago
     *
     * @param \DateTime $fechaPago
     *
     * @return PagoParcial
     */
    public function setFechaPago($fechaPago) {
        $this->fechaPago = $fechaPago;

        return $this;
    }

    /**
     * Get fechaPago
     *
     * @return \DateTime
     */
    public function getFechaPago() {
        return $this->fechaPago;
    }

    /**
     * Set importe
     *
     * @param string $importe
     *
     * @return PagoParcial
     */
    public function setImporte($importe) {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return string
     */
    public function getImporte() {
        return $this->importe;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return PagoParcial
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
     * Set anulado
     *
     * @param boolean $anulado
     * @return PagoParcial
     */
    public function setAnulado($anulado) {
        $this->anulado = $anulado;

        return $this;
    }

    /**
     * Get anulado
     *
     * @return boolean 
     */
    public function getAnulado() {
        return $this->anulado;
    }
	
	public function setMontoTotalRetenciones($montoTotalRetenciones)
	{
		$this->montoTotalRetenciones = $montoTotalRetenciones;
		
		return $this;
	}
	
	public function getMontoTotalRetenciones()
	{
		return ($this->montoTotalRetenciones == null) ? 0 : $this->montoTotalRetenciones;
	}
	
	public function setDiferenciaImporteRetenciones($diferenciaImporteRetenciones)
	{
		$this->diferenciaImporteRetenciones = $diferenciaImporteRetenciones;
		
		return $this;
	}
	
	public function getDiferenciaImporteRetenciones()
	{
		return $this->diferenciaImporteRetenciones;
	}
	
	public function getTotalNeto()
	{
		return $this->getImporte();
	}
	
	public function setRetencionSuss($retencionSuss)
	{
		$this->retencionSuss = $retencionSuss;
		
		return $this;
	}
	
	public function getRetencionSuss()
	{
		return ($this->retencionSuss == null) ? 0 : $this->retencionSuss;
	}
	
	public function setRetencionIibb($retencionIibb)
	{
		$this->retencionIibb = $retencionIibb;
		
		return $this;
	}
	
	public function getRetencionIibb()
	{
		return ($this->retencionIibb == null) ? 0 : $this->retencionIibb;
	}
	
	public function setRetencionGanancias($retencionGanancias)
	{
		$this->retencionGanancias = $retencionGanancias;
		
		return $this;
	}
	
	public function getRetencionGanancias()
	{
		return ($this->retencionGanancias == null) ? 0 : $this->retencionGanancias;
	}
	
	public function setRetencionIva($retencionIva)
	{
		$this->retencionIva = $retencionIva;
		
		return $this;
	}
	
	public function getRetencionIva()
	{
		return ($this->retencionIva == null) ? 0 : $this->retencionIva;
	}
	
}