<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * EmpleadoTipoLicencia
 *
 * @ORM\Table(name="empleado_tipo_licencia", indexes={@ORM\Index(name="fk_empleado_tipo_licencia_empleado_1", columns={"id_empleado"}), @ORM\Index(name="fk_empleado_tipo_licencia_tipo_licencia_1", columns={"id_tipo_licencia"})})
 * @ORM\Entity
 */
class EmpleadoTipoLicencia extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="fecha_hasta", type="date", nullable=false)
     */
    private $fechaHasta;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Empleado
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Empleado", inversedBy="tiposLicencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleado", referencedColumnName="id")
     * })
     */
    private $empleado;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\TipoLicencia
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\TipoLicencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_licencia", referencedColumnName="id")
     * })
     */
    private $tipoLicencia;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La observación no puede superar los {{ limit }} caracteres.")
     * )
     */
    private $observaciones;

    public function __toString() {
        return $this->getTipoLicencia()->__toString();
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
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return EmpleadoTipoLicencia
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
     * @return EmpleadoTipoLicencia
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
     * @return EmpleadoTipoLicencia
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
     * Set tipoLicencia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\TipoLicencia $tipoLicencia
     * @return EmpleadoTipoLicencia
     */
    public function setTipoLicencia(\ADIF\RecursosHumanosBundle\Entity\TipoLicencia $tipoLicencia = null) {
        $this->tipoLicencia = $tipoLicencia;
        return $this;
    }

    /**
     * Get tipoLicencia
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\TipoLicencia
     */
    public function getTipoLicencia() {
        return $this->tipoLicencia;
    }

    /**
     * @return integer Días de diferencia entre fechaDesde y fechaHasta
     */
    public function getDiferenciaEnDias() {
        return date_diff(
            $this->getFechaHasta() ? $this->getFechaHasta() : date_create(), $this->getFechaDesde()
        )->format('%a');
    }


    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return EmpleadoTipoLicencia
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }
}
