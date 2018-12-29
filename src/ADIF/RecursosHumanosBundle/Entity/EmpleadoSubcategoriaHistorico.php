<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;

/**
 * EmpleadoSubcategoriaHistorico
 *
 * @ORM\Table(name="empleado_subcategoria_historico", indexes={@ORM\Index(name="subcategoria_2", columns={"id_subcategoria"}), @ORM\Index(name="id_empleado", columns={"id_empleado"})})
 * @ORM\Entity
 */
class EmpleadoSubcategoriaHistorico extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="fecha_desde", type="date", nullable=false)
     */
    private $fechaDesde;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="fecha_hasta", type="date", nullable=false)
     */
    private $fechaHasta;

    /**
     * @var Empleado
     *
     * @ORM\ManyToOne(targetEntity="Empleado", inversedBy="subcategoriasHistoricas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleado", referencedColumnName="id")
     * })
     */
    private $empleado;

    /**
     * @var Subcategoria
     *
     * @ORM\ManyToOne(targetEntity="Subcategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_subcategoria", referencedColumnName="id")
     * })
     */
    private $subcategoria;

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
     * @param DateTime $fechaDesde
     * @return EmpleadoSubcategoriaHistorico
     */
    public function setFechaDesde($fechaDesde) {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    /**
     * Get fechaDesde
     *
     * @return DateTime 
     */
    public function getFechaDesde() {
        return $this->fechaDesde;
    }

    /**
     * Set fechaHasta
     *
     * @param DateTime $fechaHasta
     * @return EmpleadoSubcategoriaHistorico
     */
    public function setFechaHasta($fechaHasta) {
        $this->fechaHasta = $fechaHasta;

        return $this;
    }

    /**
     * Get fechaHasta
     *
     * @return DateTime 
     */
    public function getFechaHasta() {
        return $this->fechaHasta;
    }

    /**
     * Set empleado
     *
     * @param Empleado $empleado
     * @return EmpleadoSubcategoriaHistorico
     */
    public function setEmpleado(Empleado $empleado = null) {
        $this->empleado = $empleado;

        return $this;
    }

    /**
     * Get empleado
     *
     * @return Empleado 
     */
    public function getEmpleado() {
        return $this->empleado;
    }

    /**
     * Set subcategoria
     *
     * @param Subcategoria $subcategoria
     * @return EmpleadoSubcategoriaHistorico
     */
    public function setSubcategoria(Subcategoria $subcategoria = null) {
        $this->subcategoria = $subcategoria;

        return $this;
    }

    /**
     * Get subcategoria
     *
     * @return Subcategoria 
     */
    public function getSubcategoria() {
        return $this->subcategoria;
    }

}
