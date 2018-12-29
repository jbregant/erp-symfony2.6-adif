<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of NaturalezaCuentaContable:
 *      1 - Activo 
 *      2 - Pasivo
 *      3 - Patrimonio neto
 *      4 - Ingreso
 *      5 - Gasto
 *      
 * @author Manuel Becerra
 * created 06/10/2014
 * 
 * @ORM\Table(name="naturaleza_cuenta_contable")
 * @ORM\Entity
 * @UniqueEntity("denominacion", message="La denominación ingresada ya se encuentra en uso.")
 */
class NaturalezaCuentaContable {

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

}
