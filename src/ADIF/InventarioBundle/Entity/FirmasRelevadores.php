<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\InventarioBundle\Entity\Relevamiento;
use ADIF\InventarioBundle\Entity\InspeccionTecnica;

/**
 * FirmasRelevadores
 *
 * @ORM\Table(name="firmas_relevadores")
 * @ORM\Entity
 */
class FirmasRelevadores extends BaseAuditoria implements BaseAuditable
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
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="InspeccionTecnica")
     * @ORM\JoinColumn(name="id_inspeccion_tecnica", referencedColumnName="id")
     */
    private $inspeccionTecnica;

    /**
     * @var  integer
     *
     * @ORM\ManyToOne(targetEntity="Relevamiento")
     * @ORM\JoinColumn(name="id_relevamiento", referencedColumnName="id")
     */
    private $relevamiento;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=100)
     */
    private $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="cargo", type="string", length=100)
     */
    private $cargo;

    /**
     * @var string
     *
     * @ORM\Column(name="organismo", type="string", length=100)
     */
    private $organismo;

    /**
     * @var string
     *
     * @ORM\Column(name="firma", type="string", length=100)
     */
    private $firma;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_empresa", type="integer")
     */
    private $idEmpresa;


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
     * Set Id Inspeccion Tecnica
     *
     * @param \ADIF\InventarioBundle\Entity\InspeccionTecnica $inspeccionTecnica
     * @return FirmasRelevadores
     */
    public function setInspeccionTecnica(\ADIF\InventarioBundle\Entity\InspeccionTecnica $id)
    {
        $this->inspeccionTecnica = $id;
        return $this;
    }

    /**
     * Get Id Inspeccion Tecnica
     *
     * @return \ADIF\InventarioBundle\Entity\InspeccionTecnica
     */
    public function getInspeccionTecnica()
    {
        return $this->inspeccionTecnica ;
    }

    /**
     * Set Id Relevamiento
     *
     * @param \ADIF\InventarioBundle\Entity\Relevamiento $relevamiento
     * @return FirmasRelevadores
     */
    public function setRelevamiento(\ADIF\InventarioBundle\Entity\Relevamiento $id)
    {
        $this->relevamiento = $id;
        return $this;
    }

    /**
     * Get Id Relevamiento
     *
     * @return \ADIF\InventarioBundle\Entity\Relevamiento
     */
    public function getRelevamiento()
    {
        return $this->relevamiento ;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return FirmasRelevadores
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     * @return FirmasRelevadores
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set cargo
     *
     * @param string $cargo
     * @return FirmasRelevadores
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * Get cargo
     *
     * @return string
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Set organismo
     *
     * @param string $organismo
     * @return FirmasRelevadores
     */
    public function setOrganismo($organismo)
    {
        $this->organismo = $organismo;

        return $this;
    }

    /**
     * Get organismo
     *
     * @return string
     */
    public function getOrganismo()
    {
        return $this->organismo;
    }

    /**
     * Set firma
     *
     * @param string $firma
     * @return FirmasRelevadores
     */
    public function setFirma($firma)
    {
        $this->firma = $firma;

        return $this;
    }

    /**
     * Get firma
     *
     * @return string
     */
    public function getFirma()
    {
        return $this->firma;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return FirmasRelevadores
     */
    public function setIdEmpresa($id)
    {
        $this->idEmpresa = $id;
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
