<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Domicilio
 *
 * @ORM\Table(name="domicilio", indexes={@ORM\Index(name="localidad", columns={"id_localidad"})})
 * @ORM\Entity
 */
class Domicilio {

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
     * @ORM\Column(name="calle", type="string", length=255, nullable=false)
     */
    private $calle;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=255, nullable=false)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="piso", type="string", length=255, nullable=true)
     */
    private $piso;

    /**
     * @var string
     *
     * @ORM\Column(name="depto", type="string", length=255, nullable=true)
     */
    private $depto;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_postal", type="string", length=8, nullable=true)
     */
    private $codPostal;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Localidad
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Localidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_localidad", referencedColumnName="id")
     * })
     */
    private $localidad;

    /**
     * To string
     * 
     * @return string
     */
    public function __toString() {
        return $this->getCalle() . ' ' . $this->getNumero() . ' ' .
                ($this->getPiso() ? ' ' . $this->getPiso() : '&nbsp;') .
                ($this->getDepto() ? ' ' . $this->getDepto() : '&nbsp;');
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
     * Set calle
     *
     * @param string $calle
     * @return Domicilio
     */
    public function setCalle($calle) {
        $this->calle = $calle;

        return $this;
    }

    /**
     * Get calle
     *
     * @return string 
     */
    public function getCalle() {
        return $this->calle;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return Domicilio
     */
    public function setNumero($numero) {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero() {
        return $this->numero;
    }

    /**
     * Set piso
     *
     * @param string $piso
     * @return Domicilio
     */
    public function setPiso($piso) {
        $this->piso = $piso;

        return $this;
    }

    /**
     * Get piso
     *
     * @return string 
     */
    public function getPiso() {
        return $this->piso;
    }

    /**
     * Set depto
     *
     * @param string $depto
     * @return Domicilio
     */
    public function setDepto($depto) {
        $this->depto = $depto;

        return $this;
    }

    /**
     * Get depto
     *
     * @return string 
     */
    public function getDepto() {
        return $this->depto;
    }

    /**
     * Set codPostal
     *
     * @param string $codPostal
     * @return Domicilio
     */
    public function setCodPostal($codPostal) {
        $this->codPostal = $codPostal;

        return $this;
    }

    /**
     * Get codPostal
     *
     * @return string 
     */
    public function getCodPostal() {
        return $this->codPostal;
    }

    /**
     * Set localidad
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Localidad $localidad
     * @return Domicilio
     */
    public function setLocalidad(\ADIF\RecursosHumanosBundle\Entity\Localidad $localidad = null) {
        $this->localidad = $localidad;

        return $this;
    }

    /**
     * Get localidad
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Localidad 
     */
    public function getLocalidad() {
        return $this->localidad;
    }

}
