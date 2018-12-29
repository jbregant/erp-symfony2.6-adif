<?php

namespace ADIF\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\InventarioBundle\Entity\HojaRuta;

/**
 * Relevamiento
 *
 * @ORM\Table(name="relevamiento")
 * @ORM\Entity
 */
class Relevamiento extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="HojaRuta")
     * @ORM\JoinColumn(name="id_hoja_ruta", referencedColumnName="id", nullable=false)
     */
    private $hojaRuta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=100)
     */
    private $observaciones;

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
     * Set Id Hoja de Ruta
     *
     * @param \ADIF\InventarioBundle\Entity\HojaRuta $hojaRuta
     * @return ItemRelevamiento
     */
    public function setHojaRuta(\ADIF\InventarioBundle\Entity\HojaRuta $id)
    {
        $this->hojaRuta = $id;
        return $this;
    }

    /**
     * Get Id Hoja de Ruta
     *
     * @return \ADIF\InventarioBundle\Entity\HojaRuta
     */
    public function getRelevamiento()
    {
        return $this->hojaRuta;
    }


    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Relevamiento
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return Relevamiento
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set idEmpresa
     *
     * @param integer $idEmpresa
     * @return Relevamiento
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
