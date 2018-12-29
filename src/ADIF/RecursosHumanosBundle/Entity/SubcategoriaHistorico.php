<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubcategoriaHistorico
 *
 * @ORM\Table(name="subcategoria_historico")
 * @ORM\Entity
 */
class SubcategoriaHistorico
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_subcategoria", type="integer", nullable=false)
     */
    private $idSubcategoria;

    /**
     * @var string
     *
     * @ORM\Column(name="monto_basico", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $montoBasico;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_desde", type="date", nullable=false)
     */
    private $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hasta", type="date", nullable=false)
     */
    private $fechaHasta;



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
     * Set idSubcategoria
     *
     * @param integer $idSubcategoria
     * @return SubcategoriaHistorico
     */
    public function setIdSubcategoria($idSubcategoria)
    {
        $this->idSubcategoria = $idSubcategoria;

        return $this;
    }

    /**
     * Get idSubcategoria
     *
     * @return integer 
     */
    public function getIdSubcategoria()
    {
        return $this->idSubcategoria;
    }

    /**
     * Set montoBasico
     *
     * @param string $montoBasico
     * @return SubcategoriaHistorico
     */
    public function setMontoBasico($montoBasico)
    {
        $this->montoBasico = $montoBasico;

        return $this;
    }

    /**
     * Get montoBasico
     *
     * @return string 
     */
    public function getMontoBasico()
    {
        return $this->montoBasico;
    }

    /**
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return SubcategoriaHistorico
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
     * @return SubcategoriaHistorico
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
}
