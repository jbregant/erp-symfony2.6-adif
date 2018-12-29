<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EscalaImpuesto
 *
 * @author Manuel Becerra
 * created 21/07/2014
 * 
 * @ORM\Table(name="g_escala_impuesto")
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\EscalaImpuestoRepository")
 */
class EscalaImpuesto {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     * 
     * @ORM\Column(name="mes", type="integer", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El mes debe ser de tipo numérico.")
     */
    protected $mes;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_desde", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $montoDesde;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_hasta", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $montoHasta;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_fijo", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $montoFijo;

    /**
     * @var float
     * 
     * @ORM\Column(name="porcentaje_a_sumar", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El porcentaje debe ser de tipo numérico.")
     */
    protected $porcentajeASumar;
	
	/**
     * @var \DateTime
     *
     * @ORM\Column(name="vigencia_desde", type="date", nullable=false)
     */
	protected $vigenciaDesde;
	
	/**
     * @var \DateTime
     *
     * @ORM\Column(name="vigencia_hasta", type="date", nullable=false)
     */
	protected $vigenciaHasta;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set mes
     *
     * @param integer $mes
     * @return EscalaImpuesto
     */
    public function setMes($mes) {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return integer 
     */
    public function getMes() {
        return $this->mes;
    }

    /**
     * Set montoDesde
     *
     * @param float $montoDesde
     * @return RangoEscalaImpuesto
     */
    public function setMontoDesde($montoDesde) {
        $this->montoDesde = $montoDesde;

        return $this;
    }

    /**
     * Get montoDesde
     *
     * @return float 
     */
    public function getMontoDesde() {
        return $this->montoDesde;
    }

    /**
     * Set montoHasta
     *
     * @param float $montoHasta
     * @return RangoEscalaImpuesto
     */
    public function setMontoHasta($montoHasta) {
        $this->montoHasta = $montoHasta;

        return $this;
    }

    /**
     * Get montoHasta
     *
     * @return float 
     */
    public function getMontoHasta() {
        return $this->montoHasta;
    }

    /**
     * Set montoFijo
     *
     * @param float $montoFijo
     * @return EscalaImpuesto
     */
    public function setMontoFijo($montoFijo) {
        $this->montoFijo = $montoFijo;

        return $this;
    }

    /**
     * Get montoFijo
     *
     * @return float 
     */
    public function getMontoFijo() {
        return $this->montoFijo;
    }

    /**
     * Set porcentajeASumar
     *
     * @param float $porcentajeASumar
     * @return EscalaImpuesto
     */
    public function setPorcentajeASumar($porcentajeASumar) {
        $this->porcentajeASumar = $porcentajeASumar;

        return $this;
    }

    /**
     * Get porcentajeASumar
     *
     * @return float 
     */
    public function getPorcentajeASumar() {
        return $this->porcentajeASumar;
    }
	
	public function setVigenciaDesde($vigenciaDesde)
	{
		$this->vigenciaDesde = $vigenciaDesde;
		
		return $this;
	}
	
	public function getVigenciaDesde()
	{
		return $this->vigenciaDesde;
	}
	
	public function setVigenciaHasta($vigenciaHasta)
	{
		$this->vigenciaHasta = $vigenciaHasta;
		
		return $this;
	}
	
	public function getVigenciaHasta()
	{
		return $this->vigenciaHasta;
	}
	
	public function __toString()
	{
		setlocale(LC_ALL, "es_AR.UTF-8");
		$fecha = $this->vigenciaDesde->format('Y') . '-' . $this->mes . '-' . $this->vigenciaDesde->format('d');
        $dt = new \DateTime($fecha);
        $mes = ucfirst(strftime("%B", $dt->getTimestamp()));
		return 'Mes ' . $mes . ' - categor&iacute;a ' . $this->porcentajeASumar * 100 . '%';
	}

}
