<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\InventarioBundle\Entity\Linea;
use ADIF\InventarioBundle\Entity\Operador;
use ADIF\InventarioBundle\Entity\Ramal;
use ADIF\InventarioBundle\Entity\Corredor;

/**
 * Divisiones
 *
 * @ORM\Table(name="division")
 * @ORM\Entity
 */
class Divisiones extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="Linea", inversedBy="divisiones")
     * @ORM\JoinColumn(name="id_linea", referencedColumnName="id", nullable=false)
     */
    private $linea;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Operador", inversedBy="divisiones")
     * @ORM\JoinColumn(name="id_operador", referencedColumnName="id", nullable=false)
     */
    private $operador;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Ramal", inversedBy="divisiones")
     * @ORM\JoinColumn(name="id_ramal", referencedColumnName="id")
     */
    private $ramal;
    
    /**
     * @ORM\OneToMany(targetEntity="Corredor", mappedBy="division")
     */
    private $corredores;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
     */
    private $idEmpresa;

    public function __construct() {
        //Tengo entendido que esto no es necesario:
        $this->linea = new ArrayCollection();
        $this->operador = new ArrayCollection();
        $this->ramal = new ArrayCollection();
        ///
        $this->corredores = new ArrayCollection();
    }
    
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
     * @return Divisiones
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
     * @return Divisiones
     */
    public function setLinea(Linea $linea = null)
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
     * @return Divisiones
     */
    public function setOperador(Operador $operador = null)
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
     * Set ramal
     *
     * @param integer $ramal
     * @return Divisiones
     */
    public function setRamal(Ramal $ramal = null)
    {
        $this->ramal = $ramal;
    
        return $this;
    }

    /**
     * Get ramal
     *
     * @return integer 
     */
    public function getRamal()
    {
        return $this->ramal;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return Divisiones
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
    
    public function getCorredores()
    {
        return $this->corredores;
    }
}
