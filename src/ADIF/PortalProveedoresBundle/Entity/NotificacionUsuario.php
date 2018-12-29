<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * NotificacionUsuario
 *
 * @ORM\Table("notificacion_usuario")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\NotificacionUsuarioRepository")
 */
class NotificacionUsuario extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="Notificacion")
     * @ORM\JoinColumn(name="notificacion_idnotificacion", referencedColumnName="id", nullable=false)
     */
    private $notificacionIdnotificacion;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="notificacionUsuario")
     * @ORM\JoinColumn(name="usuario_idusuario", referencedColumnName="id", nullable=false)
     */
    private $usuarioIdusuario;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_proveedor", type="integer", nullable=false)
     */
    private $idProveedor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="leido", type="boolean", options={"default" : 0})
     */
    private $leido;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hora", type="datetime")
     */
    private $fechaHora;

    public function __construct() {
        $this->fechaHora = new \DateTime();
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
     * Get idProveedor
     *
     * @return integer 
     */
    public function getIdProveedor()
    {
        return $this->idProveedor;
    }

    /**
     * Set idProveedor
     *
     * @param integer $idProveedor
     * @return NotificacionUsuario
     */
    public function setIdProveedor($idProveedor)
    {
        $this->idProveedor = $idProveedor;

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
