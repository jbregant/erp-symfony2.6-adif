<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmpleadoRequisito
 *
 * @ORM\Table(name="empleado_requisito", indexes={@ORM\Index(name="requisito", columns={"id_requisito"}), @ORM\Index(name="empleado_2", columns={"id_empleado"})})
 * @ORM\Entity
 */
class EmpleadoRequisito
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
     * @var \ADIF\RecursosHumanosBundle\Entity\Empleado
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Empleado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleado", referencedColumnName="id")
     * })
     */
    private $idEmpleado;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Requisito
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Requisito")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_requisito", referencedColumnName="id")
     * })
     */
    private $idRequisito;



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
     * Set idEmpleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $idEmpleado
     * @return EmpleadoRequisito
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
     * Set idRequisito
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Requisito $idRequisito
     * @return EmpleadoRequisito
     */
    public function setIdRequisito(\ADIF\RecursosHumanosBundle\Entity\Requisito $idRequisito = null)
    {
        $this->idRequisito = $idRequisito;

        return $this;
    }

    /**
     * Get idRequisito
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Requisito 
     */
    public function getIdRequisito()
    {
        return $this->idRequisito;
    }
}
