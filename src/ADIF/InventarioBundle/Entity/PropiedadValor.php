<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use ADIF\InventarioBundle\Entity\Propiedades;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * PropiedadValor
 *
 * @ORM\Table(name="Propiedad_Valor")
 * @ORM\Entity
 * @UniqueEntity(fields = {"valor", "idPropiedad"}, message="El valor ingresado ya ha sido agregado para esta propiedad.")
 */
class PropiedadValor extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="Propiedades", inversedBy="valoresPropiedad")
     * @ORM\JoinColumn(name="id_propiedad", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $idPropiedad;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="string", length=100, nullable=false)
     * @Assert\NotBlank()
     */
    private $valor;
 
    /** 
     * @return string 
     */ 
    public function __toString() { 
        return (string) $this->getValor(); 
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
     * Set idPropiedad
     *
     * @param integer $idPropiedad
     * @return PropiedadValor
     */
    public function setIdPropiedad($idPropiedad)
    {
        $this->idPropiedad = $idPropiedad;

        return $this;
    }

    /**
     * Get idPropiedad
     *
     * @return integer
     */
    public function getIdPropiedad()
    {
        return $this->idPropiedad;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return PropiedadValor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }
}
