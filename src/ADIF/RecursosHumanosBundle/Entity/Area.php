<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Area
 *
 * @ORM\Table(name="area")
 * @ORM\Entity
 */
class Area extends BaseAuditoria {

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
     * @ORM\OneToMany(targetEntity="Empleado", mappedBy="idArea")
     * */
    private $empleados;
    
    /**
    * @ORM\Column(name="id_centro_costo", type="integer", nullable=false)
    */
    protected $idCentroCosto;

    /**
     * @var ADIF\ContableBundle\Entity\CentroCosto
     */
    protected $centroCosto;

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
     * @return Area
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
     * @return Area
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

}
