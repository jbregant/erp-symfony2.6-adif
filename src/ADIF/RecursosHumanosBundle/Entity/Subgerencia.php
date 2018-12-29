<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Subgerencia
 *
 * @ORM\Table(name="subgerencia")
 * @ORM\Entity
 */
class Subgerencia extends BaseAuditoria {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="Empleado", mappedBy="idSubgerencia")
     * */
    private $empleados;

    /**
     * Constructor
     */
    public function __construct() {
        $this->empleados = new ArrayCollection();
    }

    /**
     * To String
     * 
     * @return string
     */
    public function __toString() {
        return $this->getNombre();
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
     * Set nombre
     *
     * @param string $nombre
     * @return Subgerencia
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Add empleados
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $empleados
     * @return Subgerencia
     */
    public function addEmpleado(\ADIF\RecursosHumanosBundle\Entity\Empleado $empleados) {
        $this->empleados[] = $empleados;

        return $this;
    }

    /**
     * Remove empleados
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $empleados
     */
    public function removeEmpleado(\ADIF\RecursosHumanosBundle\Entity\Empleado $empleados) {
        $this->empleados->removeElement($empleados);
    }

    /**
     * Get empleados
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmpleados() {
        return $this->empleados;
    }

}
