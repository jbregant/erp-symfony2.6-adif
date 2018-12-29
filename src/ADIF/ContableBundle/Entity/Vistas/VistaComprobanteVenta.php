<?php

namespace ADIF\ContableBundle\Entity\Vistas;

use Doctrine\ORM\Mapping as ORM;

/**
 * VistaComprobanteVenta 
 * 
 * @ORM\Table(name="vistacomprobanteventa")
 * @ORM\Entity
 */
class VistaComprobanteVenta {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_comprobante", type="datetime", nullable=true)
     */
    protected $fechaComprobante;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_contable", type="datetime", nullable=true)
     */
    protected $fechaContable;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", nullable=true)
     */
    protected $tipoComprobante;

    /**
     * @var string
     *
     * @ORM\Column(name="letra", type="string", nullable=true)
     */
    protected $letra;

    /**
     * @var string
     *
     * @ORM\Column(name="puntoVenta", type="string", nullable=true)
     */
    protected $puntoVenta;

    /**
     * @var string
     *
     * @ORM\Column(name="licitacion", type="string", nullable=true)
     */
    protected $licitacion;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroComprobante", type="string", nullable=true)
     */
    protected $numeroComprobante;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroCupon", type="string", nullable=true)
     */
    protected $numeroCupon;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroContrato", type="string", nullable=true)
     */
    protected $numeroContrato;

    /**
     * @var string
     *
     * @ORM\Column(name="cliente", type="string", nullable=true)
     */
    protected $cliente;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", nullable=true)
     */
    protected $observaciones;

    /**
     * @var double
     * @ORM\Column(name="importeTotalNeto", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $importeTotalNeto;

    /**
     * @var double
     * @ORM\Column(name="importeTotalIVA", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $importeTotalIVA;

    /**
     * @var double
     * @ORM\Column(name="percepcionIIBB", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $percepcionIIBB;

    /**
     * @var double
     * @ORM\Column(name="percepcionIVA", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $percepcionIVA;

    /**
     * @var double
     * @ORM\Column(name="totalMCL", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $totalMCL;

    /**
     * @var string
     *
     * @ORM\Column(name="estadoComprobante", type="string", nullable=true)
     */
    protected $estadoComprobante;

    /**
     * @var integer
     * 
     * @ORM\Column(name="idEstadoComprobante", type="integer", nullable=true)
     */
    protected $idEstadoComprobante;


    /**
     * Set id
     *
     * @param integer $id
     * @return VistaComprobanteVenta
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * Set fechaComprobante
     *
     * @param \DateTime $fechaComprobante
     * @return VistaComprobanteVenta
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
     * Set nombre
     *
     * @param string $tipoComprobante
     * @return VistaComprobanteVenta
     */
    public function setTipoComprobante($tipoComprobante)
    {
        $this->tipoComprobante = $tipoComprobante;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getTipoComprobante()
    {
        return $this->tipoComprobante;
    }

    /**
     * Set letra
     *
     * @param string $letra
     * @return VistaComprobanteVenta
     */
    public function setLetra($letra)
    {
        $this->letra = $letra;

        return $this;
    }

    /**
     * Get letra
     *
     * @return string 
     */
    public function getLetra()
    {
        return $this->letra;
    }

    /**
     * Set puntoVenta
     *
     * @param string $puntoVenta
     * @return VistaComprobanteVenta
     */
    public function setPuntoVenta($puntoVenta)
    {
        $this->puntoVenta = $puntoVenta;

        return $this;
    }

    /**
     * Get puntoVenta
     *
     * @return string 
     */
    public function getPuntoVenta()
    {
        return $this->puntoVenta;
    }

    /**
     * Set numeroComprobante
     *
     * @param string $numeroComprobante
     * @return VistaComprobanteVenta
     */
    public function setNumeroComprobante($numeroComprobante)
    {
        $this->numeroComprobante = $numeroComprobante;

        return $this;
    }

    /**
     * Get numeroComprobante
     *
     * @return string 
     */
    public function getNumeroComprobante()
    {
        return $this->numeroComprobante;
    }

    /**
     * Set numeroCupon
     *
     * @param string $numeroCupon
     * @return VistaComprobanteVenta
     */
    public function setNumeroCupon($numeroCupon)
    {
        $this->numeroCupon = $numeroCupon;

        return $this;
    }

    /**
     * Get numeroCupon
     *
     * @return string 
     */
    public function getNumeroCupon()
    {
        return $this->numeroCupon;
    }

    /**
     * Set cliente
     *
     * @param string $cliente
     * @return VistaComprobanteVenta
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return string 
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return VistaComprobanteVenta
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
     * Set importeTotalNeto
     *
     * @param string $importeTotalNeto
     * @return VistaComprobanteVenta
     */
    public function setImporteTotalNeto($importeTotalNeto)
    {
        $this->importeTotalNeto = $importeTotalNeto;

        return $this;
    }

    /**
     * Get importeTotalNeto
     *
     * @return string 
     */
    public function getImporteTotalNeto()
    {
        return $this->importeTotalNeto;
    }

    /**
     * Set importeTotalIVA
     *
     * @param string $importeTotalIVA
     * @return VistaComprobanteVenta
     */
    public function setImporteTotalIVA($importeTotalIVA)
    {
        $this->importeTotalIVA = $importeTotalIVA;

        return $this;
    }

    /**
     * Get importeTotalIVA
     *
     * @return string 
     */
    public function getImporteTotalIVA()
    {
        return $this->importeTotalIVA;
    }

    /**
     * Set percepcionIIBB
     *
     * @param string $percepcionIIBB
     * @return VistaComprobanteVenta
     */
    public function setPercepcionIIBB($percepcionIIBB)
    {
        $this->percepcionIIBB = $percepcionIIBB;

        return $this;
    }

    /**
     * Get percepcionIIBB
     *
     * @return string 
     */
    public function getPercepcionIIBB()
    {
        return $this->percepcionIIBB;
    }

    /**
     * Set percepcionIVA
     *
     * @param string $percepcionIVA
     * @return VistaComprobanteVenta
     */
    public function setPercepcionIVA($percepcionIVA)
    {
        $this->percepcionIVA = $percepcionIVA;

        return $this;
    }

    /**
     * Get percepcionIVA
     *
     * @return string 
     */
    public function getPercepcionIVA()
    {
        return $this->percepcionIVA;
    }

    /**
     * Set totalMCL
     *
     * @param string $totalMCL
     * @return VistaComprobanteVenta
     */
    public function setTotalMCL($totalMCL)
    {
        $this->totalMCL = $totalMCL;

        return $this;
    }

    /**
     * Get totalMCL
     *
     * @return string 
     */
    public function getTotalMCL()
    {
        return $this->totalMCL;
    }

    /**
     * Set estadoComprobante
     *
     * @param string $estadoComprobante
     * @return VistaComprobanteVenta
     */
    public function setEstadoComprobante($estadoComprobante)
    {
        $this->estadoComprobante = $estadoComprobante;

        return $this;
    }

    /**
     * Get estadoComprobante
     *
     * @return string 
     */
    public function getEstadoComprobante()
    {
        return $this->estadoComprobante;
    }

    /**
     * Set idEstadoComprobante
     *
     * @param integer $idEstadoComprobante
     * @return VistaComprobanteVenta
     */
    public function setIdEstadoComprobante($idEstadoComprobante)
    {
        $this->idEstadoComprobante = $idEstadoComprobante;

        return $this;
    }

    /**
     * Get idEstadoComprobante
     *
     * @return integer 
     */
    public function getIdEstadoComprobante()
    {
        return $this->idEstadoComprobante;
    }

    /**
     * Set numeroContrato
     *
     * @param string $numeroContrato
     * @return VistaComprobanteVenta
     */
    public function setNumeroContrato($numeroContrato)
    {
        $this->numeroContrato = $numeroContrato;

        return $this;
    }

    /**
     * Get numeroContrato
     *
     * @return string 
     */
    public function getNumeroContrato()
    {
        return $this->numeroContrato;
    }

    /**
     * Set fechaContable
     *
     * @param \DateTime $fechaContable
     * @return VistaComprobanteVenta
     */
    public function setFechaContable($fechaContable)
    {
        $this->fechaContable = $fechaContable;

        return $this;
    }

    /**
     * Get fechaContable
     *
     * @return \DateTime 
     */
    public function getFechaContable()
    {
        return $this->fechaContable;
    }
    
    /**
     * Set licitacion
     *
     * @param string $licitacion
     * @return VistaComprobanteVenta
     */
    public function setLicitacion($licitacion)
    {
        $this->licitacion = $licitacion;

        return $this;
    }

    /**
     * Get licitacion
     *
     * @return string 
     */
    public function getLicitacion()
    {
        return $this->licitacion;
    }
}
