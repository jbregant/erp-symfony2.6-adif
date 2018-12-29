<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParametrosLiquidacion
 *
 * @ORM\Table(name="parametros_liquidacion")
 * @ORM\Entity
 */
class ParametrosLiquidacion
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_porcentaje", type="boolean", nullable=false)
     */
    private $esPorcentaje;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $valor;



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
     * Set nombre
     *
     * @param string $nombre
     * @return ParametrosLiquidacion
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
     * Set esPorcentaje
     *
     * @param boolean $esPorcentaje
     * @return ParametrosLiquidacion
     */
    public function setEsPorcentaje($esPorcentaje)
    {
        $this->esPorcentaje = $esPorcentaje;

        return $this;
    }

    /**
     * Get esPorcentaje
     *
     * @return boolean 
     */
    public function getEsPorcentaje()
    {
        return $this->esPorcentaje;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return ParametrosLiquidacion
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
