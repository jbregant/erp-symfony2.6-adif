<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstudioEmpleado
 *
 * @ORM\Table(name="estudio_empleado", indexes={@ORM\Index(name="empleado_3", columns={"id_empleado"}), @ORM\Index(name="nivel_estudio", columns={"id_nivel_estudio"})})
 * @ORM\Entity
 */
class EstudioEmpleado {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\TituloUniversitario
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\TituloUniversitario", inversedBy="estudios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_titulo_universitario", referencedColumnName="id", nullable=false)
     * })
     */
    private $titulo;

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
     * @var string
     *
     * @ORM\Column(name="establecimiento", type="string", length=255, nullable=false)
     */
    private $establecimiento;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Empleado
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Empleado", inversedBy="estudios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleado", referencedColumnName="id", nullable=false)
     * })
     */
    private $idEmpleado;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\NivelEstudio
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\NivelEstudio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_nivel_estudio", referencedColumnName="id")
     * })
     */
    private $idNivelEstudio;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set titulo
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\TituloUniversitario $titulo
     * @return EstudioEmpleado
     */
    public function setTitulo($titulo) {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\TituloUniversitario 
     */
    public function getTitulo() {
        return $this->titulo;
    }

    /**
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return EstudioEmpleado
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
     * @return EstudioEmpleado
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
     * Set establecimiento
     *
     * @param string $establecimiento
     * @return EstudioEmpleado
     */
    public function setEstablecimiento($establecimiento) {
        $this->establecimiento = $establecimiento;

        return $this;
    }

    /**
     * Get establecimiento
     *
     * @return string 
     */
    public function getEstablecimiento() {
        return $this->establecimiento;
    }

    /**
     * Set idEmpleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $idEmpleado
     * @return EstudioEmpleado
     */
    public function setIdEmpleado(\ADIF\RecursosHumanosBundle\Entity\Empleado $idEmpleado = null) {
        $this->idEmpleado = $idEmpleado;

        return $this;
    }

    /**
     * Get idEmpleado
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Empleado 
     */
    public function getIdEmpleado() {
        return $this->idEmpleado;
    }

    /**
     * Set idNivelEstudio
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\NivelEstudio $idNivelEstudio
     * @return EstudioEmpleado
     */
    public function setIdNivelEstudio(\ADIF\RecursosHumanosBundle\Entity\NivelEstudio $idNivelEstudio = null) {
        $this->idNivelEstudio = $idNivelEstudio;

        return $this;
    }

    /**
     * Get idNivelEstudio
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\NivelEstudio 
     */
    public function getIdNivelEstudio() {
        return $this->idNivelEstudio;
    }

}
