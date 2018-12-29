<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * EmpleadoObraSocial
 *
 * @ORM\Table(name="empleado_obra_social", indexes={@ORM\Index(name="fk_empleado_obra_social_empleado_1", columns={"id_empleado"}), @ORM\Index(name="fk_empleado_obra_social_obra_social_1", columns={"id_obra_social"})})
 * @ORM\Entity
 */
class EmpleadoObraSocial extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_desde", type="date", nullable=false)
     */
    private $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hasta", type="date", nullable=true)
     */
    private $fechaHasta;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Empleado
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Empleado", inversedBy="obrasSociales")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleado", referencedColumnName="id")
     * })
     */
    private $empleado;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\ObraSocial
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\ObraSocial")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_obra_social", referencedColumnName="id")
     * })
     */
    private $obraSocial;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return EmpleadoObraSocial
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
     * @return EmpleadoObraSocial
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

    /**
     * Set empleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $empleado
     * @return EmpleadoObraSocial
     */
    public function setEmpleado(\ADIF\RecursosHumanosBundle\Entity\Empleado $empleado = null) {
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

    /**
     * Set obraSocial
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\ObraSocial $obraSocial
     * @return EmpleadoObraSocial
     */
    public function setObraSocial(\ADIF\RecursosHumanosBundle\Entity\ObraSocial $obraSocial = null) {
        $this->obraSocial = $obraSocial;

        return $this;
    }

    /**
     * Get obraSocial
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\ObraSocial 
     */
    public function getObraSocial() {
        return $this->obraSocial;
    }

}
