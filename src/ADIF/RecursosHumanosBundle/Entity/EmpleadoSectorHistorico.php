<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * EmpleadoSectorHistorico
 *
 * @ORM\Table(name="empleado_sector_historico", indexes={@ORM\Index(name="gerencia_1", columns={"id_gerencia"}), @ORM\Index(name="subgerencia", columns={"id_subgerencia"}), @ORM\Index(name="area", columns={"id_area"}), @ORM\Index(name="sector_1", columns={"id_sector"}), @ORM\Index(name="id_empleado", columns={"id_empleado"})})
 * @ORM\Entity
 */
class EmpleadoSectorHistorico extends BaseAuditoria implements BaseAuditable {

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
     * @var \ADIF\RecursosHumanosBundle\Entity\Area
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Area")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_area", referencedColumnName="id")
     * })
     */
    private $area;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Gerencia
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Gerencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_gerencia", referencedColumnName="id")
     * })
     */
    private $gerencia;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Empleado
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Empleado", inversedBy="puestosHistoricos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleado", referencedColumnName="id")
     * })
     */
    private $empleado;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Sector
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Sector")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_sector", referencedColumnName="id")
     * })
     */
    private $sector;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Subgerencia
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Subgerencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_subgerencia", referencedColumnName="id")
     * })
     */
    private $subgerencia;

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
     * @return EmpleadoSectorHistorico
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
     * @return EmpleadoSectorHistorico
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
     * Set area
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Area $area
     * @return EmpleadoSectorHistorico
     */
    public function setArea(\ADIF\RecursosHumanosBundle\Entity\Area $area = null) {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Area 
     */
    public function getArea() {
        return $this->area;
    }

    /**
     * Set gerencia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Gerencia $gerencia
     * @return EmpleadoSectorHistorico
     */
    public function setGerencia(\ADIF\RecursosHumanosBundle\Entity\Gerencia $gerencia = null) {
        $this->gerencia = $gerencia;

        return $this;
    }

    /**
     * Get gerencia
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Gerencia 
     */
    public function getGerencia() {
        return $this->gerencia;
    }

    /**
     * Set empleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $empleado
     * @return EmpleadoSectorHistorico
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
     * Set sector
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Sector $sector
     * @return EmpleadoSectorHistorico
     */
    public function setSector(\ADIF\RecursosHumanosBundle\Entity\Sector $sector = null) {
        $this->sector = $sector;

        return $this;
    }

    /**
     * Get sector
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Sector 
     */
    public function getSector() {
        return $this->sector;
    }

    /**
     * Set subgerencia
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Subgerencia $subgerencia
     * @return EmpleadoSectorHistorico
     */
    public function setSubgerencia(\ADIF\RecursosHumanosBundle\Entity\Subgerencia $subgerencia = null) {
        $this->subgerencia = $subgerencia;

        return $this;
    }

    /**
     * Get subgerencia
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Subgerencia 
     */
    public function getSubgerencia() {
        return $this->subgerencia;
    }

}
