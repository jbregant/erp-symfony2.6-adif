<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleConceptoFormulario572Aplicado
 *
 * @ORM\Table(name="g_detalle_concepto_formulario_572_aplicado")
 * @ORM\Entity
 */
class DetalleConceptoFormulario572Aplicado {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572
     *
     * @ORM\OneToOne(targetEntity="ConceptoFormulario572", inversedBy="detalleConceptoFormulario572Aplicado")
     * @ORM\JoinColumn(name="id_concepto_formulario_572", referencedColumnName="id")
     * 
     */
    protected $conceptoFormulario572;

    /**
     * @var boolean
     *
     * @ORM\Column(name="aplicado", type="boolean", nullable=false)
     */
    protected $aplicado;

    /**
     * @var integer
     * @ORM\Column(name="periodo", type="integer", nullable=false)
     */
    protected $periodo;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_aplicado", type="float", nullable=false)
     */
    protected $montoAplicado;

    /**
     * Constructor
     */
    public function __construct() {
        $this->aplicado = false;
        $this->montoAplicado = 0;
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
     * Set conceptoFormulario572
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 $conceptoFormulario572
     * @return DetalleConceptoFormulario572
     */
    public function setConceptoFormulario572(\ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 $conceptoFormulario572 = null) {
        $this->conceptoFormulario572 = $conceptoFormulario572;
        $conceptoFormulario572->setDetalleConceptoFormulario572Aplicado($this);

        return $this;
    }

    /**
     * Get conceptoFormulario572
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572 
     */
    public function getConceptoFormulario572() {
        return $this->conceptoFormulario572;
    }

    /**
     * Set aplicado
     *
     * @param boolean $aplicado
     * @return DetalleConceptoFormulario572Aplicado
     */
    public function setAplicado($aplicado) {
        $this->aplicado = $aplicado;

        return $this;
    }

    /**
     * Get aplicado
     *
     * @return boolean 
     */
    public function getAplicado() {
        return $this->aplicado;
    }

    /**
     * Set periodo
     *
     * @param string $periodo
     * @return DetalleConceptoFormulario572Aplicado
     */
    public function setPeriodo($periodo) {
        $this->periodo = $periodo;

        return $this;
    }

    /**
     * Get periodo
     *
     * @return string 
     */
    public function getPeriodo() {
        return $this->periodo;
    }

    /**
     * Set montoAplicado
     *
     * @param float $montoAplicado
     * @return DetalleConceptoFormulario572Aplicado
     */
    public function setMontoAplicado($montoAplicado) {
        $this->montoAplicado = $montoAplicado;

        return $this;
    }

    /**
     * Get montoAplicado
     *
     * @return float 
     */
    public function getMontoAplicado() {
        return $this->montoAplicado;
    }

}
