<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Gerencia
 *
 * @ORM\Table(name="gerencia")
 * @ORM\Entity
 */
class Gerencia extends BaseAuditoria {

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
     * @ORM\Column(name="id_centro_costo", type="integer", nullable=true)
     */
    protected $idCentroCosto;

    /**
     * @var ADIF\ContableBundle\Entity\CentroCosto
     */
    protected $centroCosto;

    /**
     * @ORM\OneToMany(targetEntity="Empleado", mappedBy="idGerencia")
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
     * @return Gerencia
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
     * 
     * @return type
     */
    public function getIdCentroCosto() {
        return $this->idCentroCosto;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\CentroCosto $centroCosto
     */
    public function setCentroCosto($centroCosto) {

        if (null != $centroCosto) {
            $this->idCentroCosto = $centroCosto->getId();
        } //.
        else {
            $this->idCentroCosto = null;
        }

        $this->centroCosto = $centroCosto;
    }

    /**
     * 
     * @return type
     */
    public function getCentroCosto() {
        return $this->centroCosto;
    }

    /**
     * Add empleados
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $empleados
     * @return Gerencia
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
