<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ConceptoGananciaCalculado
 *
 * @author Manuel Becerra
 * created 21/07/2014
 * 
 * @ORM\Table(name="g_concepto_ganancia_calculado")
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\ConceptoGananciaCalculadoRepository")
 */
class ConceptoGananciaCalculado {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\GananciaEmpleado
     *
     * @ORM\ManyToOne(targetEntity="GananciaEmpleado", inversedBy="conceptos")
     * @ORM\JoinColumn(name="id_ganancia_empleado", referencedColumnName="id", nullable=false)
     * 
     */
    protected $gananciaEmpleado;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia
     *
     * @ORM\ManyToOne(targetEntity="ConceptoGanancia")
     * @ORM\JoinColumn(name="id_concepto_ganancia", referencedColumnName="id", nullable=false)
     * 
     */
    protected $conceptoGanancia;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $monto;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\TopeConceptoGanancia
     *
     * @ORM\ManyToOne(targetEntity="TopeConceptoGanancia")
     * @ORM\JoinColumn(name="id_tope_concepto_ganancia", referencedColumnName="id", nullable=true)
     * 
     */
    protected $topeConceptoGanancia;

    /**
     * Constructor
     */
    public function __construct() {
        $this->monto = 0;
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
     * Set monto
     *
     * @param float $monto
     * @return ConceptoGananciaCalculado
     */
    public function setMonto($monto) {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return float 
     */
    public function getMonto() {
        return $this->monto;
    }

    /**
     * Set conceptoGanancia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia $conceptoGanancia
     * @return ConceptoGananciaCalculado
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
     * Set gananciaEmpleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\GananciaEmpleado $gananciaEmpleado
     * @return ConceptoGananciaCalculado
     */
    public function setGananciaEmpleado(\ADIF\RecursosHumanosBundle\Entity\GananciaEmpleado $gananciaEmpleado) {
        $this->gananciaEmpleado = $gananciaEmpleado;

        return $this;
    }

    /**
     * Get gananciaEmpleado
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\GananciaEmpleado 
     */
    public function getGananciaEmpleado() {
        return $this->gananciaEmpleado;
    }


    /**
     * Set topeConceptoGanancia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\TopeConceptoGanancia $topeConceptoGanancia
     * @return ConceptoGananciaCalculado
     */
    public function setTopeConceptoGanancia(\ADIF\RecursosHumanosBundle\Entity\TopeConceptoGanancia $topeConceptoGanancia)
    {
        $this->topeConceptoGanancia = $topeConceptoGanancia;

        return $this;
    }

    /**
     * Get topeConceptoGanancia
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\TopeConceptoGanancia 
     */
    public function getTopeConceptoGanancia()
    {
        return $this->topeConceptoGanancia;
    }
}
