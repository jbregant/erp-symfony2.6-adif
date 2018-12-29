<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RangoRemuneracion
 *
 * @author Manuel Becerra
 * created 21/07/2014
 * 
 * @ORM\Table(name="g_rango_remuneracion")
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\RangoRemuneracionRepository")
 */
class RangoRemuneracion {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_desde", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $montoDesde;

    /**
     * @var float
     * 
     * @ORM\Column(name="monto_hasta", type="float", nullable=false)
     * @Assert\Type(
     *  type="numeric",
     *  message="El monto debe ser de tipo numérico.")
     */
    protected $montoHasta;

    /**
     * @var boolean
     *
     * @ORM\Column(name="aplica_ganancias", type="boolean", nullable=false)
     */
    protected $aplicaGanancias;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vigente", type="boolean")
     */
    protected $vigente;

    /**
     * Constructor
     */
    public function __construct() {
        $this->aplicaGanancias = true;
        $this->vigente = false;
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
     * Set montoDesde
     *
     * @param float $montoDesde
     * @return RangoRemuneracion
     */
    public function setMontoDesde($montoDesde) {
        $this->montoDesde = $montoDesde;

        return $this;
    }

    /**
     * Get montoDesde
     *
     * @return float 
     */
    public function getMontoDesde() {
        return $this->montoDesde;
    }

    /**
     * Set montoHasta
     *
     * @param float $montoHasta
     * @return RangoRemuneracion
     */
    public function setMontoHasta($montoHasta) {
        $this->montoHasta = $montoHasta;

        return $this;
    }

    /**
     * Get montoHasta
     *
     * @return float 
     */
    public function getMontoHasta() {
        return $this->montoHasta;
    }

    /**
     * Set aplicaGanancias
     *
     * @param boolean $aplicaGanancias
     * @return RangoRemuneracion
     */
    public function setAplicaGanancias($aplicaGanancias) {
        $this->aplicaGanancias = $aplicaGanancias;

        return $this;
    }

    /**
     * Get aplicaGanancias
     *
     * @return boolean 
     */
    public function getAplicaGanancias() {
        return $this->aplicaGanancias;
    }

    public function __toString() {
        if ($this->montoDesde == 0) {
            return 'Menor que $' . $this->montoHasta;
        } else if ($this->montoHasta > 9999998) {
            return 'Mayor que $' . $this->montoDesde;
        } else {
            return 'Entre $' . $this->montoDesde . ' y $' . $this->montoHasta;
        }
    }

    /**
     * Set vigente
     *
     * @param boolean $vigente
     * @return RangoRemuneracion
     */
    public function setVigente($vigente) {
        $this->vigente = $vigente;

        return $this;
    }

    /**
     * Get vigente
     *
     * @return boolean 
     */
    public function getVigente() {
        return $this->vigente;
    }

}
