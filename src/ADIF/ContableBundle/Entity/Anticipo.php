<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Anticipo
 * 
 * @author Darío Rapetti
 * created 21/10/2014
 *
 * @ORM\Table(name="anticipo")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "proveedor" = "AnticipoProveedor",
 *      "sueldo" = "AnticipoSueldo",
 *      "ordencompra" = "AnticipoOrdenCompra",
 *      "contratoconsultoria" = "AnticipoContratoConsultoria"
 * })
 */
abstract class Anticipo extends BaseAuditoria implements BaseAuditable {

    /**
     * TIPO_ANTICIPO_ORDEN_COMPRA
     */
    const TIPO_ANTICIPO_ORDEN_COMPRA = 'oc';

    /**
     * TIPO_ANTICIPO_PROVEEDOR
     */
    const TIPO_ANTICIPO_PROVEEDOR = 'ap';

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
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    protected $fecha;

    /**
     * @var double
     * @ORM\Column(name="monto", type="decimal", precision=15, scale=2, nullable=false)
     */
    protected $monto;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=1000, nullable=true)
     */
    protected $observacion;
	
	/**
     * @ORM\ManyToMany(targetEntity="OrdenPagoLog", mappedBy="comprobantes", cascade={"all"})
     */
    protected $ordenPagoLog;
	
	public function __construct()
	{
		$this->ordenPagoLog = new ArrayCollection();
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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Anticipo
     */
    public function setFecha($fecha) {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha() {
        return $this->fecha;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return Anticipo
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto() {
        return $this->monto;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return Anticipo
     */
    public function setObservacion($observacion) {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion() {
        return $this->observacion;
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

}
