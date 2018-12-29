<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EstadoCuentaContable 
 * 
 * Indica el estado de la Cuenta Contable. 
 * 
 * Por ejemplo:
 *      Activa.
 *      Inactiva.
 * 
 *
 * @author Manuel Becerra
 * created 24/06/2014
 * 
 * @ORM\Table(name="estado_cuenta_contable")
 * @ORM\Entity
 * @UniqueEntity("denominacionEstado", message="La denominación ingresada ya se encuentra en uso.")
 */
class EstadoCuentaContable extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="denominacion", type="string", length=255, unique=true, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacionEstado;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512", 
     *      maxMessage="La descripción del estado no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $descripcionEstado;

    /**
     * @ORM\OneToMany(targetEntity="CuentaContable", mappedBy="estadoCuentaContable")
     */
    protected $cuentasContables;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cuentasContables = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacionEstado;
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
     * Set denominacionEstado
     *
     * @param string $denominacionEstado
     * @return EstadoCuentaContable
     */
    public function setDenominacionEstado($denominacionEstado) {
        $this->denominacionEstado = $denominacionEstado;

        return $this;
    }

    /**
     * Get denominacionEstado
     *
     * @return string 
     */
    public function getDenominacionEstado() {
        return $this->denominacionEstado;
    }

    /**
     * Set descripcionEstado
     *
     * @param string $descripcionEstado
     * @return EstadoCuentaContable
     */
    public function setDescripcionEstado($descripcionEstado) {
        $this->descripcionEstado = $descripcionEstado;

        return $this;
    }

    /**
     * Get descripcionEstado
     *
     * @return string 
     */
    public function getDescripcionEstado() {
        return $this->descripcionEstado;
    }

    /**
     * Add cuentasContables
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     * @return EstadoCuentaContable
     */
    public function addCuentasContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContable) {
        $this->cuentasContables[] = $cuentaContable;

        return $this;
    }

    /**
     * Remove cuentasContables
     *
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     */
    public function removeCuentasContable(\ADIF\ContableBundle\Entity\CuentaContable $cuentaContable) {
        $this->cuentasContables->removeElement($cuentaContable);
    }

    /**
     * Get cuentasContables
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCuentasContables() {
        return $this->cuentasContables;
    }

}
