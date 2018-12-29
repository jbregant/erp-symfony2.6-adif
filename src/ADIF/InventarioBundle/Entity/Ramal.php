<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\InventarioBundle\Entity\Linea;
use ADIF\InventarioBundle\Entity\Divisiones;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Ramal
 *
 * @ORM\Table("ramal")
 * @ORM\Entity
 * @UniqueEntity("denominacion", message="La denominación ingresada ya se encuentra en uso.")
 *
 */

class Ramal extends BaseAuditoria implements BaseAuditable
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
     * @var string
     *
     * @ORM\Column(name="denominacion_corta", type="string", length=20, nullable=true)
     */
    private $denominacionCorta;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Linea", inversedBy="ramales")
     * @ORM\JoinColumn(name="id_linea", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $linea;

    /**
     * @ORM\OneToMany(targetEntity="Divisiones", mappedBy="ramal")
     */
    private $divisiones;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
     */
    private $idEmpresa;

    public function __construct()
    {
        $this->divisiones = new ArrayCollection();
    }

    public function __toString() {
        return $this->getDenominacionCorta().' - '.$this->getDenominacion();
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
     * @return Ramal
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
     * @return Ramal
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

    /**
     * Set linea
     *
     * @param integer $linea
     * @return Ramal
     */
    public function setLinea(Linea $linea)
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
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return Ramal
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

    public function getDivisiones()
    {
        return $this->divisiones;
    }
}
