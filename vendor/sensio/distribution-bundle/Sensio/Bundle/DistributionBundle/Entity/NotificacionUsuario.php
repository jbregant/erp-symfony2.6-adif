<?php

namespace Sensio\Bundle\DistributionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificacionUsuario
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class NotificacionUsuario
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
     * @var integer
     *
     * @ORM\Column(name="notificacion_idnotificacion", type="integer")
     */
    private $notificacionIdnotificacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_idusuario", type="integer")
     */
    private $usuarioIdusuario;

    /**
     * @var boolean
     *
     * @ORM\Column(name="leido", type="boolean")
     */
    private $leido;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hora", type="datetime")
     */
    private $fechaHora;


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
     * Set notificacionIdnotificacion
     *
     * @param integer $notificacionIdnotificacion
     * @return NotificacionUsuario
     */
    public function setNotificacionIdnotificacion($notificacionIdnotificacion)
    {
        $this->notificacionIdnotificacion = $notificacionIdnotificacion;

        return $this;
    }

    /**
     * Get notificacionIdnotificacion
     *
     * @return integer 
     */
    public function getNotificacionIdnotificacion()
    {
        return $this->notificacionIdnotificacion;
    }

    /**
     * Set usuarioIdusuario
     *
     * @param integer $usuarioIdusuario
     * @return NotificacionUsuario
     */
    public function setUsuarioIdusuario($usuarioIdusuario)
    {
        $this->usuarioIdusuario = $usuarioIdusuario;

        return $this;
    }

    /**
     * Get usuarioIdusuario
     *
     * @return integer 
     */
    public function getUsuarioIdusuario()
    {
        return $this->usuarioIdusuario;
    }

    /**
     * Set leido
     *
     * @param boolean $leido
     * @return NotificacionUsuario
     */
    public function setLeido($leido)
    {
        $this->leido = $leido;

        return $this;
    }

    /**
     * Get leido
     *
     * @return boolean 
     */
    public function getLeido()
    {
        return $this->leido;
    }

    /**
     * Set fechaHora
     *
     * @param \DateTime $fechaHora
     * @return NotificacionUsuario
     */
    public function setFechaHora($fechaHora)
    {
        $this->fechaHora = $fechaHora;

        return $this;
    }

    /**
     * Get fechaHora
     *
     * @return \DateTime 
     */
    public function getFechaHora()
    {
        return $this->fechaHora;
    }
}
