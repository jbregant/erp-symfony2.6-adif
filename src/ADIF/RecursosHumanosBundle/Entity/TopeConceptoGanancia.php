<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TopeConceptoGanancia
 *
 * @author Manuel Becerra
 * created 21/07/2014
 * 
 * @ORM\Table(name="g_tope_concepto_ganancia")
 * @ORM\Entity
 */
class TopeConceptoGanancia {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\RangoRemuneracion
     *
     * @ORM\ManyToOne(targetEntity="RangoRemuneracion")
     * @ORM\JoinColumn(name="id_rango_remuneracion", referencedColumnName="id", nullable=false)
     * 
     */
    protected $rangoRemuneracion;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia
     *
     * @ORM\ManyToOne(targetEntity="ConceptoGanancia")
     * @ORM\JoinColumn(name="id_concepto_ganancia", referencedColumnName="id", nullable=false)
     * 
     */
    protected $conceptoGanancia;

    /**
     * @var integer
     * 
     * @ORM\Column(name="mes", type="integer", nullable=true)
     * @Assert\Type(
     *  type="numeric",
     *  message="El mes debe ser de tipo numérico.")
     */
    protected $mes;

    /**
     * @var float
     * 
     * @ORM\Column(name="valor_tope", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El tope debe ser de tipo numérico.")
     */
    protected $valorTope;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_porcentaje", type="boolean", nullable=false)
     */
    protected $esPorcentaje;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_valor_anual", type="boolean", nullable=false)
     */
    protected $esValorAnual;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vigente", type="boolean", nullable=false)
     */
    private $vigente;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_desde", type="date", nullable=true)
     */
    private $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hasta", type="date", nullable=true)
     */
    private $fechaHasta;

    /**
     * Constructor
     */
    public function __construct() {
        $this->esPorcentaje = false;
        $this->esValorAnual = true;
        $this->vigente = true;
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
     * Set rangoRemuneracion
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\RangoRemuneracion $rangoRemuneracion
     * @return TopeConceptoGanancia
     */
    public function setRangoRemuneracion(\ADIF\RecursosHumanosBundle\Entity\RangoRemuneracion $rangoRemuneracion) {
        $this->rangoRemuneracion = $rangoRemuneracion;

        return $this;
    }

    /**
     * Get rangoRemuneracion
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\RangoRemuneracion 
     */
    public function getRangoRemuneracion() {
        return $this->rangoRemuneracion;
    }

    /**
     * Set conceptoGanancia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia $conceptoGanancia
     * @return TopeConceptoGanancia
     */
    public function setConceptoGanancia(\ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia $conceptoGanancia) {
        $this->conceptoGanancia = $conceptoGanancia;

        return $this;
    }

    /**
     * Get conceptoGanancia
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia 
     */
    public function getConceptoGanancia() {
        return $this->conceptoGanancia;
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
     * Set valorTope
     *
     * @param float $valorTope
     * @return TopeConceptoGanancia
     */
    public function setValorTope($valorTope) {
        $this->valorTope = $valorTope;

        return $this;
    }

    /**
     * Get valorTope
     *
     * @return float 
     */
    public function getValorTope() {
        return $this->valorTope;
    }

    /**
     * Set esPorcentaje
     *
     * @param boolean $esPorcentaje
     * @return TopeConceptoGanancia
     */
    public function setEsPorcentaje($esPorcentaje) {
        $this->esPorcentaje = $esPorcentaje;

        return $this;
    }

    /**
     * Get esPorcentaje
     *
     * @return boolean 
     */
    public function getEsPorcentaje() {
        return $this->esPorcentaje;
    }

    /**
     * Set esValorAnual
     *
     * @param boolean $esValorAnual
     * @return TopeConceptoGanancia
     */
    public function setEsValorAnual($esValorAnual) {
        $this->esValorAnual = $esValorAnual;

        return $this;
    }

    /**
     * Get esValorAnual
     *
     * @return boolean 
     */
    public function getEsValorAnual() {
        return $this->esValorAnual;
    }

    /**
     * Set vigente
     *
     * @param boolean $vigente
     * @return TopeConceptoGanancia
     */
    public function setVigente($vigente) {
        $this->vigente = $vigente;

        return $this;
    }

    /**
     * Get vigente
     *
     * @return boolean 
     */
    public function getVigente() {
        return $this->vigente;
    }

    /**
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return TopeConceptoGanancia
     */
    public function setFechaDesde($fechaDesde) {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    /**
     * Get fechaDesde
     *
     * @return \DateTime 
     */
    public function getFechaDesde() {
        return $this->fechaDesde;
    }

    /**
     * Set fechaHasta
     *
     * @param \DateTime $fechaHasta
     * @return TopeConceptoGanancia
     */
    public function setFechaHasta($fechaHasta) {
        $this->fechaHasta = $fechaHasta;

        return $this;
    }

    /**
     * Get fechaHasta
     *
     * @return \DateTime 
     */
    public function getFechaHasta() {
        return $this->fechaHasta;
    }
	
	public function __toString()
	{
		return $this->conceptoGanancia->getDenominacion() . ' - valor $' .  $this->valorTope;
	}

}
