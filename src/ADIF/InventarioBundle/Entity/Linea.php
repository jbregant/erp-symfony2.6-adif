<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ADIF\InventarioBundle\Entity\Ramal;
use ADIF\InventarioBundle\Entity\Divisiones;
use ADIF\InventarioBundle\Entity\Corredor;

/**
 * Linea
 *
 * @ORM\Table("linea")
 * @ORM\Entity
 * @UniqueEntity("denominacion", message="La denominación ingresada ya se encuentra en uso.")
 *
 */

class Linea extends BaseAuditoria implements BaseAuditable
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
     */
    private $denominacion;


    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Ramal", mappedBy="linea")
     */
    private $ramales;
    
    /**
     * @ORM\OneToMany(targetEntity="Divisiones", mappedBy="linea")
     */
    private $divisiones;
    
    /**
     * @ORM\OneToMany(targetEntity="Corredor", mappedBy="linea")
     */
    private $corredores;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
     */
    private $idEmpresa;

    public function __construct()
    {
        $this->ramales = new ArrayCollection();
        $this->divisiones = new ArrayCollection();
        $this->corredores = new ArrayCollection();
    }

    public function __toString()
    {
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
     * @return Linea
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
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return Linea
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

    public function getRamales()
    {
        return $this->ramales;
    }
    
    public function getDivisiones()
    {
        return $this->divisiones;
    }
    
    public function getCorredores()
    {
        return $this->corredores;
    }
}
