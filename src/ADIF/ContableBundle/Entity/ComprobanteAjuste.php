<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * ComprobanteAjuste
 *
 * @ORM\Table(name="comprobante_ajuste")
 * @ORM\Entity
 */
class ComprobanteAjuste extends BaseAuditoria implements BaseAuditable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_comprobante", type="integer")
     */
    private $idComprobante;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_proveedor", type="integer")
     */
    private $idProveedor;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cliente", type="integer")
     */
    private $idCliente;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="decimal", precision=15, scale=2)
     */
    private $total;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_comprobante", type="date")
     */
    private $fechaComprobante;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_nota_credito", type="boolean")
     */
    private $esNotaCredito;
	
	/**
     * @var string
     *
     * @ORM\Column(name="detalle", type="string", length=50)
     */
    protected $detalle;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="text")
     */
    private $observaciones;
	
	/**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_anulacion", type="datetime")
     */
    private $fechaAnulacion;
	
	/**
     * @var comprobante
     *
     * @ORM\ManyToOne(targetEntity="Comprobante", inversedBy="comprobantesAjustes")
     * @ORM\JoinColumn(name="id_comprobante", referencedColumnName="id", nullable=true)
     * 
     */
    protected $comprobante;
	
	/**
     * @var ADIF\ComprasBundle\Entity\Proveedor
     */
    protected $proveedor;
	
	/**
     * @var ADIF\ComprasBundle\Entity\Cliente
     */
    protected $cliente;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idComprobante
     *
     * @param integer $idComprobante
     * @return ComprobanteAjuste
     */
    public function setIdComprobante($idComprobante)
    {
        $this->idComprobante = $idComprobante;

        return $this;
    }

    /**
     * Get idComprobante
     *
     * @return integer 
     */
    public function getIdComprobante()
    {
        return $this->idComprobante;
    }

    /**
     * Set idProveedor
     *
     * @param integer $idProveedor
     * @return ComprobanteAjuste
     */
    public function setIdProveedor($idProveedor)
    {
        $this->idProveedor = $idProveedor;

        return $this;
    }

    /**
     * Get idProveedor
     *
     * @return integer 
     */
    public function getIdProveedor()
    {
        return $this->idProveedor;
    }

    /**
     * Set idCliente
     *
     * @param integer $idCliente
     * @return ComprobanteAjuste
     */
    public function setIdCliente($idCliente)
    {
        $this->idCliente = $idCliente;

        return $this;
    }

    /**
     * Get idCliente
     *
     * @return integer 
     */
    public function getIdCliente()
    {
        return $this->idCliente;
    }

    /**
     * Set total
     *
     * @param float $total
     * @return ComprobanteAjuste
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float 
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set fechaComprobante
     *
     * @param \DateTime $fechaComprobante
     * @return ComprobanteAjuste
     */
    public function setFechaComprobante($fechaComprobante)
    {
        $this->fechaComprobante = $fechaComprobante;

        return $this;
    }

    /**
     * Get fechaComprobante
     *
     * @return \DateTime 
     */
    public function getFechaComprobante()
    {
        return $this->fechaComprobante;
    }

    /**
     * Set esNotaCredito
     *
     * @param boolean $esNotaCredito
     * @return ComprobanteAjuste
     */
    public function setEsNotaCredito($esNotaCredito)
    {
        $this->esNotaCredito = $esNotaCredito;

        return $this;
    }

    /**
     * Get esNotaCredito
     *
     * @return boolean 
     */
    public function getEsNotaCredito()
    {
        return $this->esNotaCredito;
    }
	
	/**
     * Set detalle
     *
     * @param string $detalle
     * @return ComprobanteAjuste
     */
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * Get detalle
     *
     * @return string 
     */
    public function getDetalle()
    {
        return $this->detalle;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return ComprobanteAjuste
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }
	
	 /**
     * Set fechaAnulacion
     *
     * @param \DateTime $fechaAnulacion
     * @return AnulacionAjuste
     */
    public function setFechaAnulacion($fechaAnulacion)
    {
        $this->fechaAnulacion = $fechaAnulacion;

        return $this;
    }

    /**
     * Get fechaAnulacion
     *
     * @return \DateTime 
     */
    public function getFechaAnulacion()
    {
        return $this->fechaAnulacion;
    }
	
	
	
	public function getComprobante()
	{
		return $this->comprobante;
	}
	
	public function setComprobante(\ADIF\ContableBundle\Entity\Comprobante $comprobante)
	{
		$this->comprobante = $comprobante;
		return $this;
	}
	
	public function getProveedor() 
	{
		return $this->proveedor;
	}
	
	public function setProveedor($proveedor) 
	{
		$this->proveedor = $proveedor;
		return $this;
	}
	
	public function getCliente() 
	{
		return $this->cliente;
	}
	
	public function setCliente($cliente) 
	{
		$this->cliente = $cliente;
		return $this;
	}
	
	public function __toString()
	{
		return $this->detalle;
	}
}
