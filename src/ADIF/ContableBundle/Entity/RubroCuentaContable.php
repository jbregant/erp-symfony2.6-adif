<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of RubroCuentaContable
 *      
 * @author Manuel Becerra
 * created 06/10/2014
 * 
 * @ORM\Table(name="rubro_cuenta_contable")
 * @ORM\Entity
 * @UniqueEntity("denominacion", message="La denominación ingresada ya se encuentra en uso.")
 */
class RubroCuentaContable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ContableBundle\Entity\NaturalezaCuentaContable
     *
     * @ORM\ManyToOne(targetEntity="NaturalezaCuentaContable")
     * @ORM\JoinColumn(name="id_naturaleza_cuenta_contable", referencedColumnName="id")
     * 
     */
    protected $naturalezaCuentaContable;

    /**
     * @var \ADIF\ContableBundle\Entity\LiquidezCuentaContable
     *
     * @ORM\ManyToOne(targetEntity="LiquidezCuentaContable")
     * @ORM\JoinColumn(name="id_liquidez_cuenta_contable", referencedColumnName="id")
     * 
     */
    protected $liquidezCuentaContable;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, unique=true, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     */
    protected $codigo;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return NaturalezaCuentaContable
     */
    public function setDenominacion($denominacion) {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string 
     */
    public function getDenominacion() {
        return $this->denominacion;
    }

    /**
     * Set codigo
     *
     * @param integer $codigo
     * @return NaturalezaCuentaContable
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo() {
        return $this->codigo;
    }

    /**
     * Set naturalezaCuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\NaturalezaCuentaContable $naturalezaCuentaContable
     * @return RubroCuentaContable
     */
    public function setNaturalezaCuentaContable(\ADIF\ContableBundle\Entity\NaturalezaCuentaContable $naturalezaCuentaContable = null) {
        $this->naturalezaCuentaContable = $naturalezaCuentaContable;

        return $this;
    }

    /**
     * Get naturalezaCuentaContable
     *
     * @return \ADIF\ContableBundle\Entity\NaturalezaCuentaContable 
     */
    public function getNaturalezaCuentaContable() {
        return $this->naturalezaCuentaContable;
    }

    /**
     * Set liquidezCuentaContable
     *
     * @param \ADIF\ContableBundle\Entity\LiquidezCuentaContable $liquidezCuentaContable
     * @return RubroCuentaContable
     */
    public function setLiquidezCuentaContable(\ADIF\ContableBundle\Entity\LiquidezCuentaContable $liquidezCuentaContable = null) {
        $this->liquidezCuentaContable = $liquidezCuentaContable;

        return $this;
    }

    /**
     * Get liquidezCuentaContable
     *
     * @return \ADIF\ContableBundle\Entity\LiquidezCuentaContable 
     */
    public function getLiquidezCuentaContable() {
        return $this->liquidezCuentaContable;
    }

}
