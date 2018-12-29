<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmpleadoHistoricoRangoRemuneracion
 *
 * @ORM\Table(name="empleado_historico_rango_remuneracion")
 * @ORM\Entity
 */
class EmpleadoHistoricoRangoRemuneracion extends BaseAuditoria {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Empleado")
     * @ORM\JoinColumn(name="empleado_id", referencedColumnName="id")
     */
    private $empleado;

    /**
     * @ORM\ManyToOne(targetEntity="RangoRemuneracion")
     * @ORM\JoinColumn(name="rango_remuneracion_id", referencedColumnName="id")
     */
    private $rangoRemuneracion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_desde", type="datetime", nullable=false)
     */
    protected $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hasta", type="datetime", nullable=true)
     */
    protected $fechaHasta;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return EmpleadoHistoricoRangoRemuneracion
     */
    public function setFechaDesde($fechaDesde)
    {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    /**
     * Get fechaDesde
     *
     * @return \DateTime 
     */
    public function getFechaDesde()
    {
        return $this->fechaDesde;
    }

    /**
     * Set fechaHasta
     *
     * @param \DateTime $fechaHasta
     * @return EmpleadoHistoricoRangoRemuneracion
     */
    public function setFechaHasta($fechaHasta)
    {
        $this->fechaHasta = $fechaHasta;

        return $this;
    }

    /**
     * Get fechaHasta
     *
     * @return \DateTime 
     */
    public function getFechaHasta()
    {
        return $this->fechaHasta;
    }

    /**
     * Set empleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $empleado
     * @return EmpleadoHistoricoRangoRemuneracion
     */
    public function setEmpleado(\ADIF\RecursosHumanosBundle\Entity\Empleado $empleado = null)
    {
        $this->empleado = $empleado;

        return $this;
    }

    /**
     * Get empleado
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Empleado 
     */
    public function getEmpleado()
    {
        return $this->empleado;
    }

    /**
     * Set rangoRemuneracion
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\RangoRemuneracion $rangoRemuneracion
     * @return EmpleadoHistoricoRangoRemuneracion
     */
    public function setRangoRemuneracion(\ADIF\RecursosHumanosBundle\Entity\RangoRemuneracion $rangoRemuneracion = null)
    {
        $this->rangoRemuneracion = $rangoRemuneracion;

        return $this;
    }

    /**
     * Get rangoRemuneracion
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\RangoRemuneracion 
     */
    public function getRangoRemuneracion()
    {
        return $this->rangoRemuneracion;
    }
}
