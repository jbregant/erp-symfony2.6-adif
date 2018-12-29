<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * EmpleadoTipoContrato
 *
 * @ORM\Table(name="empleado_tipo_contrato", indexes={@ORM\Index(name="fk_empleado_tipo_contrato_empleado_1", columns={"id_empleado"}), @ORM\Index(name="fk_empleado_tipo_contrato_tipo_contrato_1", columns={"id_tipo_contrato"})})
 * @ORM\Entity
 */
class EmpleadoTipoContrato extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Empleado", inversedBy="tiposContrato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleado", referencedColumnName="id")
     * })
     */
    private $empleado;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\TipoContrato
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\TipoContrato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_contrato", referencedColumnName="id")
     * })
     */
    private $tipoContrato;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    private $descripcion;

    public function __toString() {
        return $this->getTipoContrato()->__toString();
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
     * @return EmpleadoTipoContrato
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
     * @return EmpleadoTipoContrato
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
     * @return EmpleadoTipoContrato
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
     * Set tipoContrato
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\TipoContrato $tipoContrato
     * @return EmpleadoTipoContrato
     */
    public function setTipoContrato(\ADIF\RecursosHumanosBundle\Entity\TipoContrato $tipoContrato = null) {
        $this->tipoContrato = $tipoContrato;

        return $this;
    }

    /**
     * Get tipoContrato
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\TipoContrato 
     */
    public function getTipoContrato() {
        return $this->tipoContrato;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return EmpleadoTipoContrato
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * @return integer Días de diferencia entre fechaDesde y fechaHasta
     */
    public function getDiferenciaEnDias() {

//        $hoy = date("Y-m-d", strtotime(date('y-m-d')));
//        return $hoy; 
        return date_diff(
                        $this->getFechaHasta() ? $this->getFechaHasta() : date_create(), $this->getFechaDesde()
                )->format('%a');
    }

}
