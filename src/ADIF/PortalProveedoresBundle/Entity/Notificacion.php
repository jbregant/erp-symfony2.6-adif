<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * Notificacion
 *
 * @ORM\Table("notificacion")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\NotificacionRepository")
 */
class Notificacion extends BaseAuditoria implements BaseAuditable
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
     * @ORM\Column(name="titulo", type="string", length=45)
     * @Assert\NotBlank()
     */
    private $titulo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_desde", type="date", nullable=false)
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual("today")(groups={"create"})
     */
    private $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hasta", type="date", nullable=false)
     * @Assert\NotBlank()
     */
    private $fechaHasta;

    /**
     * @var string
     *
     * @ORM\Column(name="autor", type="string", length=45)
     */
    private $autor;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $mensaje;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoNotificacion")
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $estadoId;

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
     * Set titulo
     *
     * @param string $titulo
     * @return Notificacion
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string 
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return Notificacion
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
     * @return Notificacion
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
     * Set autor
     *
     * @param string $autor
     * @return Notificacion
     */
    public function setAutor($autor)
    {
        $this->autor = $autor;

        return $this;
    }

    /**
     * Get autor
     *
     * @return string 
     */
    public function getAutor()
    {
        return $this->autor;
    }

    /**
     * Set mensaje
     *
     * @param string $mensaje
     * @return Notificacion
     */
    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    /**
     * Get mensaje
     *
     * @return string 
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * Set estadoId
     *
     * @param integer $estadoId
     * @return Notificacion
     */
    public function setEstadoId($estadoId)
    {
        $this->estadoId = $estadoId;

        return $this;
    }

    /**
     * Get estadoId
     *
     * @return integer 
     */
    public function getEstadoId()
    {
        return $this->estadoId;
    }

    /**
     * @return integer
     */
    public function getEstadoNotificacion()
    {
        return $this->estadoId;
    }

    /**
     * @param integer $estadoNotificacion
     *
     * @return self
     */
    public function setEstadoNotificacion($estadoId)
    {
        $this->estadoId = $estadoId;

        return $this;
    }
}
