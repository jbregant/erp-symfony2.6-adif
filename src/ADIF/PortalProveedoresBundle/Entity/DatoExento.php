<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatoExento
 *
 * @ORM\Table(name="dato_exento")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\DatoExentoRepository")
 */
class DatoExento
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="ImpuestoIva")
     * @ORM\JoinColumn(name="id_imp_iva", referencedColumnName="id", nullable=true)
     */
    private $impuestoIva;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaDesde", type="datetime")
     */
    private $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaHasta", type="datetime")
     */
    private $fechaHasta;

    /**
     * @var int
     *
     * @ORM\Column(name="porcentajeExencion", type="integer", nullable=true)
     */
    private $porcentajeExencion;

    /**
     * @var int
     *
     * @ORM\Column(name="regimen", type="integer")
     */
    private $regimen;

    /**
     * @var string
     *
     * @ORM\Column(name="otros", type="string", length=255, nullable=true)
     */
    private $otros;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     *
     * @return DatoExento
     */
    public function setFechaDesde($fechaDesde)
    {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    /**
     * Get fechaDesde
     *
     * @return \DateTime
     */
    public function getFechaDesde()
    {
        return $this->fechaDesde;
    }

    /**
     * Set fechaHasta
     *
     * @param \DateTime $fechaHasta
     *
     * @return DatoExento
     */
    public function setFechaHasta($fechaHasta)
    {
        $this->fechaHasta = $fechaHasta;

        return $this;
    }

    /**
     * Get fechaHasta
     *
     * @return \DateTime
     */
    public function getFechaHasta()
    {
        return $this->fechaHasta;
    }

    /**
     * Set porcentajeExencion
     *
     * @param integer $porcentajeExencion
     *
     * @return DatoExento
     */
    public function setPorcentajeExencion($porcentajeExencion)
    {
        $this->porcentajeExencion = $porcentajeExencion;

        return $this;
    }

    /**
     * Get porcentajeExencion
     *
     * @return int
     */
    public function getPorcentajeExencion()
    {
        return $this->porcentajeExencion;
    }

    /**
     * Set regimen
     *
     * @param integer $regimen
     *
     * @return DatoExento
     */
    public function setRegimen($regimen)
    {
        $this->regimen = $regimen;

        return $this;
    }

    /**
     * Get regimen
     *
     * @return int
     */
    public function getRegimen()
    {
        return $this->regimen;
    }

    /**
     * Set otros
     *
     * @param string $otros
     *
     * @return DatoExento
     */
    public function setOtros($otros)
    {
        $this->otros = $otros;

        return $this;
    }

    /**
     * Get otros
     *
     * @return string
     */
    public function getOtros()
    {
        return $this->otros;
    }
}

