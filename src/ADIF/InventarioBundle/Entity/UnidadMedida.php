<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UnidadMedida
 *
 * @ORM\Table(name="unidad_medida")
 * @ORM\Entity
 * @UniqueEntity("denominacionCorta", message="La denominación corta ingresada
 *                ya se encuentra en uso.")
 */
class UnidadMedida extends BaseAuditoria implements BaseAuditable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=100, nullable=true)
     */
    private $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion_corta", type="string", length=20, nullable=false)
     * @Assert\NotBlank
     */
    private $denominacionCorta;

    public function __toString() {
        return $this->getDenominacionCorta(). " - " .$this->getDenominacion();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return UnidadMedida
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }

    /**
     * Set denominacionCorta
     *
     * @param string $denominacionCorta
     * @return UnidadMedida
     */
    public function setDenominacionCorta($denominacionCorta)
    {
        $this->denominacionCorta = $denominacionCorta;

        return $this;
    }

    /**
     * Get denominacionCorta
     *
     * @return string
     */
    public function getDenominacionCorta()
    {
        return $this->denominacionCorta;
    }
}
