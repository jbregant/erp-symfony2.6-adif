<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Banco
 *
 * @ORM\Table(name="banco")
 * @ORM\Entity
 */
class Banco extends BaseAuditoria {

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
     * @ORM\OneToMany(targetEntity="CuentaBancaria", mappedBy="idBanco")
     * */
    private $cuentas;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cuentas = new ArrayCollection();
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
     * @return Banco
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
     * Add cuentas
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\CuentaBancaria $cuentas
     * @return Banco
     */
    public function addCuenta(\ADIF\RecursosHumanosBundle\Entity\CuentaBancaria $cuentas) {
        $this->cuentas[] = $cuentas;

        return $this;
    }

    /**
     * Remove cuentas
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\CuentaBancaria $cuentas
     */
    public function removeCuenta(\ADIF\RecursosHumanosBundle\Entity\CuentaBancaria $cuentas) {
        $this->cuentas->removeElement($cuentas);
    }

    /**
     * Get cuentas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCuentas() {
        return $this->cuentas;
    }

}
