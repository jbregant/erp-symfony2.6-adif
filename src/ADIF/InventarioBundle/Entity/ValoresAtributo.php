<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use ADIF\InventarioBundle\Entity\Atributo;

/**
 * ValoresAtributo
 *
 * @ORM\Table("valores_atributo")
 * @ORM\Entity
 * @UniqueEntity(fields = {"denominacion", "atributo"}, message="La denominación ingresada ya se encuentra en uso para este atributo.")
 */
class ValoresAtributo extends BaseAuditoria implements BaseAuditable
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Atributo")
     * @ORM\JoinColumn(name="id_atributo", referencedColumnName="id", nullable=false)
     */
    private $atributo;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="denominacion", type="string", length=100)
     */
    private $denominacion;

    /**
     * @return string
     */
    public function __toString() {
        return (string) $this->getDenominacion();
    }
    
    public function __construct() {
        $this->atributo = new ArrayCollection();
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
     * Set atributo
     *
     * @param integer $atributo
     * @return ValoresAtributo
     */
    public function setAtributo(Atributo $atributo = null)
    {
        $this->atributo = $atributo;

        return $this;
    }

    /**
     * Get atributo
     *
     * @return integer
     */
    public function getAtributo()
    {
        return $this->atributo;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     * @return ValoresAtributo
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
}
