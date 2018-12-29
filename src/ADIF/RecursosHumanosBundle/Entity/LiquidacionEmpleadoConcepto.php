<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\RecursosHumanosBundle\Entity\BaseEntity;
use ADIF\RecursosHumanosBundle\Entity\Concepto;
use ADIF\RecursosHumanosBundle\Entity\Liquidacion;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * LiquidacionEmpleadoConcepto
 * 
 * @ORM\Table(name="liquidacion_empleado_concepto", indexes={@ORM\Index(name="liquidacion_empleado", columns={"id_liquidacion_empleado"}), @ORM\Index(name="concepto_version", columns={"id_concepto_version"})})
 * @ORM\Entity
 */
class LiquidacionEmpleadoConcepto extends BaseEntity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var LiquidacionEmpleado
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\LiquidacionEmpleado", inversedBy="liquidacionEmpleadoConceptos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_liquidacion_empleado", referencedColumnName="id", nullable=false)
     * })
     */
    private $liquidacionEmpleado;

    /**
     * @var ConceptoVersion
     * 
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\ConceptoVersion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_concepto_version", referencedColumnName="id", nullable=false)
     * })
     */
    private $conceptoVersion;

    /**
     * @var string
     * @ORM\Column(name="monto", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $monto;
    
    /**
     *
     * @var \ADIF\RecursosHumanosBundle\Entity\EmpleadoNovedad
     * 
     * @ORM\OneToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\EmpleadoNovedad")
     * @ORM\JoinColumn(name="id_empleado_novedad", referencedColumnName="id")
     */
    private $empleadoNovedad;
       
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set liquidacionEmpleado
     *
     * @param Liquidacion $liquidacionEmpleado
     * @return LiquidacionEmpleadoConcepto
     */
    public function setLiquidacionEmpleado($liquidacionEmpleado) {
        $this->liquidacionEmpleado = $liquidacionEmpleado;

        return $this;
    }

    /**
     * Get liquidacionEmpleado
     *
     * @return LiquidacionEmpleado
     */
    public function getLiquidacionEmpleado() {
        return $this->liquidacionEmpleado;
    }

    /**
     * Set conceptoVersion
     *
     * @param Concepto $conceptoVersion
     * @return LiquidacionEmpleadoConcepto
     */
    public function setConceptoVersion($conceptoVersion) {
        $this->conceptoVersion = $conceptoVersion;

        return $this;
    }

    /**
     * Get conceptoVersion
     *
     * @return ConceptoVersion
     */
    public function getConceptoVersion() {
        return $this->conceptoVersion;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return LiquidacionEmpleadoConcepto
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
     * Set empleadoNovedad
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\EmpleadoNovedad $empleadoNovedad
     * @return LiquidacionEmpleadoConcepto
     */
    public function setEmpleadoNovedad(\ADIF\RecursosHumanosBundle\Entity\EmpleadoNovedad $empleadoNovedad = null)
    {
        $this->empleadoNovedad = $empleadoNovedad;

        return $this;
    }

    /**
     * Get empleadoNovedad
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\EmpleadoNovedad 
     */
    public function getEmpleadoNovedad()
    {
        return $this->empleadoNovedad;
    }
}
