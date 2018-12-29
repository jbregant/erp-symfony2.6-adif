<?php

namespace ADIF\InventarioBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\InventarioBundle\Entity\Linea;
use ADIF\InventarioBundle\Entity\Ramal;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Estacion
 *
 * @ORM\Table("estacion")
 * @ORM\Entity(repositoryClass="ADIF\InventarioBundle\Repository\EstacionRepository")
 * @UniqueEntity(
 *     fields={"denominacion", "linea"},
 *     message="La Estacion que se intenta crear ya se encuentra agregar."
 * )
 */
class Estacion extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="Linea")
     * @ORM\JoinColumn(name="id_linea", referencedColumnName="id", nullable=false)
     */
    private $linea;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Ramal")
     * @ORM\JoinColumn(name="id_ramal", referencedColumnName="id")
     */
    private $ramal;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="integer", nullable=true)
     */
    private $numero;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer", nullable=false)
     */
    private $idEmpresa;

    public function __construct() {
        $this->linea = new ArrayCollection();
        $this->ramal = new ArrayCollection();
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
     * @return Estacion
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
     * @return Estacion
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
     * Set ramal
     *
     * @param integer $ramal
     * @return Estacion
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
     * Set numero
     *
     * @param integer $numero
     * @return Estacion
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return Estacion
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
