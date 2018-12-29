<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AusenciaEmpleado
 *
 * @ORM\Table(name="ausencia_empleado", indexes={@ORM\Index(name="fk_ausencia_empleado_empleado_1", columns={"id_empleado"}), @ORM\Index(name="fk_ausencia_empleado_justificacion_1", columns={"id_justificacion"})})
 * @ORM\Entity
 */
class AusenciaEmpleado
{
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
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Empleado
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Empleado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleado", referencedColumnName="id")
     * })
     */
    private $idEmpleado;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Justificacion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Justificacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_justificacion", referencedColumnName="id")
     * })
     */
    private $idJustificacion;



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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return AusenciaEmpleado
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set idEmpleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $idEmpleado
     * @return AusenciaEmpleado
     */
    public function setIdEmpleado(\ADIF\RecursosHumanosBundle\Entity\Empleado $idEmpleado = null)
    {
        $this->idEmpleado = $idEmpleado;

        return $this;
    }

    /**
     * Get idEmpleado
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Empleado 
     */
    public function getIdEmpleado()
    {
        return $this->idEmpleado;
    }

    /**
     * Set idJustificacion
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Justificacion $idJustificacion
     * @return AusenciaEmpleado
     */
    public function setIdJustificacion(\ADIF\RecursosHumanosBundle\Entity\Justificacion $idJustificacion = null)
    {
        $this->idJustificacion = $idJustificacion;

        return $this;
    }

    /**
     * Get idJustificacion
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Justificacion 
     */
    public function getIdJustificacion()
    {
        return $this->idJustificacion;
    }
}
