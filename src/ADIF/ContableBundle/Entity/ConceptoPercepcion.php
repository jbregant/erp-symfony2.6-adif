<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ConceptoPercepcion
 * 
 * @author Darío Rapetti
 * created 22/10/2014
 * 
 * @ORM\Table(name="concepto_percepcion")
 * @ORM\Entity 
 */
class ConceptoPercepcion extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La denominación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $denominacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="esta_activo", type="boolean", nullable=false)
     */
    protected $estaActivo;

    /**
     * Constructor
     */
    public function __construct() {
        $this->estaActivo = true;
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->denominacion;
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
     * Set denominacion
     *
     * @param string $denominacion
     * @return ConceptoPercepcion
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
     * Set estaActivo
     *
     * @param boolean $estaActivo
     * @return ConceptoPercepcion
     */
    public function setEstaActivo($estaActivo) {
        $this->estaActivo = $estaActivo;

        return $this;
    }

    /**
     * Get estaActivo
     *
     * @return boolean 
     */
    public function getEstaActivo() {
        return $this->estaActivo;
    }

}
