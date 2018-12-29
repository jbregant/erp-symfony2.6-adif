<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\RecursosHumanosBundle\Entity\BaseEntity;

/**
 * Convenio
 *
 * @ORM\Table(name="convenio")
 * @ORM\Entity
 */
class Convenio extends BaseEntity {
    
    const __FUERA_DE_CONVENIO = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_alta", type="datetime", nullable=false)
     */
    private $fechaAlta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_baja", type="datetime", nullable=true)
     */
    private $fechaBaja;

    /**
     * @ORM\ManyToMany(targetEntity="ADIF\RecursosHumanosBundle\Entity\Concepto", mappedBy="convenios")
     * */
    private $conceptos;

    public function __construct() {
        $this->conceptos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Convenio
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
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     * @return Convenio
     */
    public function setFechaAlta($fechaAlta) {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime 
     */
    public function getFechaAlta() {
        return $this->fechaAlta;
    }

    /**
     * Set fechaBaja
     *
     * @param \DateTime $fechaBaja
     * @return Convenio
     */
    public function setFechaBaja($fechaBaja) {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }

    /**
     * Get fechaBaja
     *
     * @return \DateTime 
     */
    public function getFechaBaja() {
        return $this->fechaBaja;
    }

    /**
     * To string
     *
     * @return string 
     */
    public function __toString() {
        return $this->nombre;
    }

    /**
     * Add conceptos
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Concepto $conceptos
     * @return Convenio
     */
    public function addConcepto(\ADIF\RecursosHumanosBundle\Entity\Concepto $conceptos) {
        $this->conceptos[] = $conceptos;

        return $this;
    }

    /**
     * Remove conceptos
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Concepto $conceptos
     */
    public function removeConcepto(\ADIF\RecursosHumanosBundle\Entity\Concepto $conceptos) {
        $this->conceptos->removeElement($conceptos);
    }

    /**
     * Get conceptos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConceptos() {
        return $this->conceptos;
    }

}
