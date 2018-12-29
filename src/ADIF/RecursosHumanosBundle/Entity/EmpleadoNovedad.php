<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * EmpleadoNovedad
 *
 * @ORM\Table(name="empleado_novedad", indexes={@ORM\Index(name="concepto_1", columns={"id_concepto"}), @ORM\Index(name="empleado_4", columns={"id_empleado"})})
 * @ORM\Entity
 */
class EmpleadoNovedad {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $valor;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="fecha_alta", type="datetime", nullable=false)
     */
    private $fechaAlta;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="fecha_baja", type="datetime", nullable=true)
     */
    private $fechaBaja;

    /**
     * @var Empleado
     *
     * @ORM\ManyToOne(targetEntity="Empleado", inversedBy="novedades")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleado", referencedColumnName="id")
     * })
     */
    private $idEmpleado;

    /**
     * @var Concepto
     *
     * @ORM\ManyToOne(targetEntity="Concepto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_concepto", referencedColumnName="id")
     * })
     */
    private $idConcepto;

    /**
     * @var integer
     *
     * @ORM\Column(name="dias", type="integer", nullable=true)
     */
    private $dias;

    /**
     * @var Liquidacion
     *
     * @ORM\ManyToOne(targetEntity="Liquidacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_liquidacion_ajuste", referencedColumnName="id", nullable=true)
     * })
     */
    private $liquidacionAjuste;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=true, options={"default" = 1})
     */
    private $activo = true;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return EmpleadoNovedad
     */
    public function setValor($valor) {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor() {
        return $this->valor;
    }

    /**
     * Set fechaAlta
     *
     * @param DateTime $fechaAlta
     * @return EmpleadoNovedad
     */
    public function setFechaAlta($fechaAlta) {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return DateTime 
     */
    public function getFechaAlta() {
        return $this->fechaAlta;
    }

    /**
     * Set fechaBaja
     *
     * @param DateTime $fechaBaja
     * @return EmpleadoNovedad
     */
    public function setFechaBaja($fechaBaja) {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }

    /**
     * Get fechaBaja
     *
     * @return DateTime 
     */
    public function getFechaBaja() {
        return $this->fechaBaja;
    }

    /**
     * Set idEmpleado
     *
     * @param Empleado $idEmpleado
     * @return EmpleadoNovedad
     */
    public function setIdEmpleado(Empleado $idEmpleado = null) {
        $this->idEmpleado = $idEmpleado;

        return $this;
    }

    /**
     * Get idEmpleado
     *
     * @return Empleado 
     */
    public function getIdEmpleado() {
        return $this->idEmpleado;
    }

    /**
     * Set idConcepto
     *
     * @param Concepto $idConcepto
     * @return EmpleadoNovedad
     */
    public function setIdConcepto(Concepto $idConcepto = null) {
        $this->idConcepto = $idConcepto;

        return $this;
    }

    /**
     * Get idConcepto
     *
     * @return Concepto 
     */
    public function getIdConcepto() {
        return $this->idConcepto;
    }

    /**
     * Get Concepto
     *
     * @return Concepto 
     */
    public function getConcepto() {
        return $this->getIdConcepto();
    }

    /**

     * Set $empleado
     * 
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $empleado
     * @return EmpleadoNovedad
     */
    public function setEmpleado(Empleado $empleado) {
        return $this->setIdEmpleado($empleado);
    }

    /**
     * Get empleado
     * 
     * @return Empleado
     */
    public function getEmpleado() {
        return $this->getIdEmpleado();
    }

    /**
     * Set concepto
     *
     * @param Concepto $concepto
     * @return EmpleadoNovedad
     */
    public function setConcepto(Concepto $concepto = null) {
        return $this->setIdConcepto($concepto);
    }
    
    /**
     * Set dias
     *
     * @param integer $dias
     * @return EmpleadoNovedad
     */
    public function setDias($dias) {
        $this->dias = $dias;

        return $this;
    }

    /**
     * Get dias
     *
     * @return integer 
     */
    public function getDias() {
        return $this->dias;
    }
    
    /**
     * Set liquidacionAjuste
     *
     * @param Concepto $liquidacionAjuste
     * @return EmpleadoNovedad
     */
    public function setLiquidacionAjuste(Liquidacion $liquidacionAjuste = null) {
        $this->liquidacionAjuste = $liquidacionAjuste;

        return $this;
    }

    /**
     * Get liquidacionAjuste
     *
     * @return Liquidacion 
     */
    public function getLiquidacionAjuste() {
        return $this->liquidacionAjuste;
    }
    
    /**
     * Set activo
     *
     * @param boolean $activo
     * @return Empleado
     */
    public function setActivo($activo) {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo() {
        return $this->activo;
    }

}
