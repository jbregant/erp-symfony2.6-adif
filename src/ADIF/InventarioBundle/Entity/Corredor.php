<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\InventarioBundle\Entity\Linea;
use ADIF\InventarioBundle\Entity\Operador;
use ADIF\InventarioBundle\Entity\Divisiones;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity; 

/**
 * Corredor
 *
 * @ORM\Table("corredor")
 * @ORM\Entity
 * @UniqueEntity("denominacion", message="La denominación ingresada ya se encuentra en uso.")
 */
class Corredor extends BaseAuditoria implements BaseAuditable
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
     * @ORM\Column(name="denominacion", type="string", length=100, nullable=false)
     * @Assert\NotBlank()
     */
    private $denominacion;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Linea", inversedBy="corredores")
     * @ORM\JoinColumn(name="id_linea", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $linea;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Operador", inversedBy="corredores")
     * @ORM\JoinColumn(name="id_operador", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $operador;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Divisiones", inversedBy="corredores")
     * @ORM\JoinColumn(name="id_division", referencedColumnName="id", nullable=true)
     */
    private $division;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
     */
    private $idEmpresa;
    
    
    /**
     * @return string
     */
    public function __toString() {
        return $this->getDenominacion();
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
     * @return Corredor
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
     * Set linea
     *
     * @param integer $linea
     * @return Corredor
     */
    public function setLinea($linea)
    {
        $this->linea = $linea;
    
        return $this;
    }

    /**
     * Get linea
     *
     * @return integer 
     */
    public function getLinea()
    {
        return $this->linea;
    }

    /**
     * Set operador
     *
     * @param integer $operador
     * @return Corredor
     */
    public function setOperador($operador)
    {
        $this->operador = $operador;
    
        return $this;
    }

    /**
     * Get operador
     *
     * @return integer 
     */
    public function getOperador()
    {
        return $this->operador;
    }

    /**
     * Set division
     *
     * @param integer $division
     * @return Corredor
     */
    public function setDivision($division)
    {
        $this->division = $division;
    
        return $this;
    }

    /**
     * Get division
     *
     * @return integer 
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return Corredor
     */
    public function setIdEmpresa($idEmpresa)
    {
        $this->idEmpresa = $idEmpresa;
    
        return $this;
    }

    /**
     * Get idEmpresa
     *
     * @return integer 
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }

}
