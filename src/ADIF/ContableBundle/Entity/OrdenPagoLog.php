<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Gustavo Luis
 * created 06/04/2017
 *
 * @ORM\Table(name="orden_pago_log")
 * @ORM\Entity
 */
class OrdenPagoLog extends BaseAuditoria implements BaseAuditable
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
     * @var \ADIF\ContableBundle\Entity\OrdenPago
     *
     * @ORM\ManyToOne(targetEntity="OrdenPago")
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id", nullable=false)
     * 
     */
    private $ordenPago;
	
	/**
     * @var \ADIF\ContableBundle\Entity\EstadoOrdenPago
     *
     * @ORM\ManyToOne(targetEntity="EstadoOrdenPago")
     * @ORM\JoinColumn(name="id_estado_orden_pago", referencedColumnName="id", nullable=false)
     * 
     */
	private $estadoOrdenPago;

	/**
     * @ORM\ManyToMany(targetEntity="Comprobante", inversedBy="ordenPagoLog")
     * @ORM\JoinTable(name="orden_pago_log_comprobante",
     *      joinColumns={@ORM\JoinColumn(name="id_orden_pago_log", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_comprobante", referencedColumnName="id")}
     *      )
     */
    private $comprobantes;
	
	 /**
     * @var double
     * @ORM\Column(name="total_bruto", type="decimal", precision=15, scale=2, nullable=false)
     */
    private $totalBruto;
	
	/**
     * @var double
     * @ORM\Column(name="total_neto", type="decimal", precision=15, scale=2, nullable=false)
     */
    private $totalNeto;
	
	/**
     * @var double
     * @ORM\Column(name="total_retenciones", type="decimal", precision=15, scale=2, nullable=false)
     */
	private $totalRetenciones; 
	
	/**
     * @var string
     * @ORM\Column(name="descripcion", type="string", nullable=true)
     */
	private $descripcion;
	
	
	/**
	* var ADIF\ContableBundle\Entity\Anticipo
	* 
    * @ORM\ManyToMany(targetEntity="Anticipo", inversedBy="ordenPagoLog")
    * @ORM\JoinTable(name="orden_pago_log_anticipo",
    *      joinColumns={@ORM\JoinColumn(name="id_orden_pago_log", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="id_anticipo", referencedColumnName="id")}
    *      )
	*/
	protected $anticipos;
	
	
	/**
	* var ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto
	* 
    * @ORM\ManyToMany(targetEntity="ComprobanteRetencionImpuesto", inversedBy="ordenPagoLog")
    * @ORM\JoinTable(name="orden_pago_log_comprobante_retencion_impuesto",
    *      joinColumns={@ORM\JoinColumn(name="id_orden_pago_log", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="id_comprobante_retencion_impuesto", referencedColumnName="id")}
    *      )
	*/
	protected $retenciones;
	

	public function __construct()
	{
		$this->comprobantes = new ArrayCollection();
		$this->anticipos = new ArrayCollection();
		$this->retenciones = new ArrayCollection();
	}
	
	public function __toString()
	{
		return "Log historico de OP/AC: " . $this->ordenPago->getId();
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
     * Set ordenPago
     *
     * @param \stdClass $ordenPago
     * @return OrdenPagoLog
     */
    public function setOrdenPago($ordenPago)
    {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return \stdClass 
     */
    public function getOrdenPago()
    {
        return $this->ordenPago;
    }
	
	public function setEstadoOrdenPago($estadoOrdenPago)
	{
		$this->estadoOrdenPago = $estadoOrdenPago;
		
		return $this;
	}
	
	public function getEstadoOrdenPago()
	{
		return $this->estadoOrdenPago;
	}

    /**
     * Set comprobante
     *
     * @param \stdClass $comprobante
     * @return OrdenPagoLog
     */
    public function setComprobantes($comprobantes)
    {
        $this->comprobantes = $comprobantes;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return \stdClass 
     */
    public function getComprobantes()
    {
        return $this->comprobantes;
    }
	
	public function addComprobante($comprobante)
	{
		$this->comprobantes->add($comprobante);
		
		return $this;
	}
	
	public function removeComprobante($comprobante)
	{
		$this->comprobantes->removeElement($chequeHistorico);
		
		return $this;
	}
	
	public function setTotalBruto($totalBruto)
	{
		$this->totalBruto = $totalBruto;
		
		return $this;
	}
	
	public function getTotalBruto()
	{
		return $this->totalBruto;
	}
	
	public function setTotalNeto($totalNeto)
	{
		$this->totalNeto = $totalNeto;
		
		return $this;
	}
	
	public function getTotalNeto()
	{
		return $this->totalNeto;
	}
	
	public function setTotalRetenciones($totalRetenciones)
	{
		$this->totalRetenciones = $totalRetenciones;
		
		return $this;
	}
	
	public function getTotalRetenciones()
	{
		return $this->totalRetenciones;
	}
	
	public function setDescripcion($descripcion)
	{
		$this->descripcion = $descripcion;
		
		return $this;
	}
	
	public function getDescripcion()
	{
		return $this->descripcion;
	}
	
	/**
     * Add anticipos
     *
     * @param \ADIF\ContableBundle\Entity\Anticipo $anticipos
     * @return OrdenPagoComprobante
     */
    public function addAnticipo(\ADIF\ContableBundle\Entity\Anticipo $anticipo) 
	{
        $this->anticipos[] = $anticipo;

        return $this;
    }

    /**
     * Remove anticipos
     *
     * @param \ADIF\ContableBundle\Entity\AnticipoProveedor $anticipos
     */
    public function removeAnticipo(\ADIF\ContableBundle\Entity\Anticipo $anticipo) 
	{
        $this->anticipos->removeElement($anticipo);
    }

    /**
     * Get anticipos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnticipos() 
	{
        return $this->anticipos;
    }
	
	/**
     * Add retenciones
     *
     * @param \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto $retenciones
     * @return OrdenPagoComprobante
     */
    public function addRetencion(\ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto $retencion) 
	{
        $this->retenciones[] = $retencion;

        return $this;
    }

    /**
     * Remove retenciones
     *
     * @param \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto $retenciones
     */
    public function removeRetencion(\ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto $retencion) 
	{
        $this->retenciones->removeElement($retencion);
    }

    /**
     * Get retenciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRetenciones() 
	{
        return $this->retenciones;
    }
	
	/**
     * Get montoRetencionesPorTipoImpuesto
     *
     * @return double
     */
    public function getMontoRetencionesPorTipoImpuesto($denominacionImpuesto) 
	{
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
     * @param  string $denominacionImpuesto
     * @return array
     */
    public function getRetencionesPorTipoImpuesto($denominacionImpuesto) 
	{
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
    public function getMontoAnticipos() 
	{
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
    public function getBeneficiario() 
	{
		return $this->ordenPago->getBeneficiario();
    }
}
