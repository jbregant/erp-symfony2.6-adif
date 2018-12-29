<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * TiempoRespuesta
 *
 * @ORM\Table(name="tiempo_respuesta")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\TiempoRespuestaRepository")
 */

class TiempoRespuesta
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;
   
    /**
     * @var string
     *
     * @ORM\Column(name="fecha_tiempo", type="string", length=30, nullable=false)
     */
    private $fechaTiempo;

    /**
     * @var integer
     *
     * @ORM\Column(name="tiempo", type="integer", nullable=true)
     */
    private $tiempo;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoAccion", inversedBy="tiempoRespuesta"))
     * @ORM\JoinColumn(name="id_tipo_accion", referencedColumnName="id")
     */
    private $accion;

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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return TiempoRespuesta
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
    
    
    function getFechaTiempo() {
        return $this->fechaTiempo;
    }

    function setFechaTiempo($fechaTiempo) {
        $this->fechaTiempo = $fechaTiempo;
    }

    /**
     * Set tiempo
     *
     * @param integer $tiempo
     * @return TiempoRespuesta
     */
    public function setTiempo($tiempo)
    {
        $this->tiempo = $tiempo;
    
        return $this;
    }

    /**
     * Get tiempo
     *
     * @return integer 
     */
    public function getTiempo()
    {
        return $this->tiempo;
    }

    /**
     * Set accion
     *
     * @param integer $accion
     * @return TiempoRespuesta
     */
    public function setAccion($accion)
    {
        $this->accion = $accion;
    
        return $this;
    }

    /**
     * Get accion
     *
     * @return integer 
     */
    public function getAccion()
    {
        return $this->accion;
    }
}
