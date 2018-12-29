<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formulario649
 *
 * @author Manuel Becerra
 * @author Sangiacomo Nahuel
 * created 21/08/2014
 * 
 * @ORM\Table(name="g_formulario_649")
 * @ORM\Entity
 */
class Formulario649 {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Empleado
     *
     * @ORM\OneToOne(targetEntity="Empleado", inversedBy="formulario649")
     * @ORM\JoinColumn(name="id_empleado", referencedColumnName="id", nullable=false)
     * 
     */
    protected $empleado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_formulario", type="date", nullable=false)
     */
    private $fechaFormulario;

    /**
     * @var float
     * 
     * @ORM\Column(name="ganancia_acumulada", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $gananciaAcumulada;

    /**
     * @var float
     * 
     * @ORM\Column(name="total_impuesto_determinado", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $totalImpuestoDeterminado;

    /**
     * Constructor
     */
    public function __construct() {
        $this->fechaFormulario = new \DateTime;
        $this->gananciaAcumulada = 0;
        $this->totalImpuestoDeterminado = 0;
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
     * Set fechaFormulario
     *
     * @param \DateTime $fechaFormulario
     * @return Formulario649
     */
    public function setFechaFormulario($fechaFormulario) {
        $this->fechaFormulario = $fechaFormulario;

        return $this;
    }

    /**
     * Get fechaFormulario
     *
     * @return \DateTime 
     */
    public function getFechaFormulario() {
        return $this->fechaFormulario;
    }

    /**
     * Set gananciaAcumulada
     *
     * @param float $gananciaAcumulada
     * @return Formulario649
     */
    public function setGananciaAcumulada($gananciaAcumulada) {
        $this->gananciaAcumulada = $gananciaAcumulada;

        return $this;
    }

    /**
     * Get gananciaAcumulada
     *
     * @return float 
     */
    public function getGananciaAcumulada() {
        return $this->gananciaAcumulada;
    }

    /**
     * Set totalImpuestoDeterminado
     *
     * @param float $totalImpuestoDeterminado
     * @return Formulario649
     */
    public function setTotalImpuestoDeterminado($totalImpuestoDeterminado) {
        $this->totalImpuestoDeterminado = $totalImpuestoDeterminado;

        return $this;
    }

    /**
     * Get totalImpuestoDeterminado
     *
     * @return float 
     */
    public function getTotalImpuestoDeterminado() {
        return $this->totalImpuestoDeterminado;
    }

    /**
     * Set empleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $empleado
     * @return Formulario649
     */
    public function setEmpleado(\ADIF\RecursosHumanosBundle\Entity\Empleado $empleado) {
        $this->empleado = $empleado;

        return $this;
    }

    /**
     * Get empleado
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Empleado 
     */
    public function getEmpleado() {
        return $this->empleado;
    }

}
