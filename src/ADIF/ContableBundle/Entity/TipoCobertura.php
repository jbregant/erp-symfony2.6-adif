<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoCobertura
 *
 * 
 * @ORM\Table(name="tipo_cobertura")
 * @ORM\Entity
 */
class TipoCobertura
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
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     *
     * @var PolizaSeguro
     * 
     * @ORM\OneToMany(targetEntity="PolizaSeguro", mappedBy="tipoCobertura")
     */
    
    protected $polizas;


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
     * @return TipoCobertura
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

    public function __toString()
    {
        return $this->nombre;
    }

    /**
     * Set poliza
     *
     * @param string $nombre
     * @return TipoCobertura
     */
    public function setPoliza($poliza)
    {
        $this->poliza = $poliza;

        return $this;
    }

    /**
     * Get poliza
     *
     * @return string 
     */
    public function getPoliza()
    {
        return $this->poliza;
    }

}