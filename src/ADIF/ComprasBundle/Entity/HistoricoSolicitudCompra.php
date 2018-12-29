<?php

namespace ADIF\ComprasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistoricoSolicitudCompra
 * 
 * @author Manuel Becerra
 * created 23/07/2014
 * 
 * @ORM\Table(name="historico_solicitud_compra")
 * @ORM\Entity
 */
class HistoricoSolicitudCompra extends BaseAuditoria
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
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @var \ADIF\ComprasBundle\Entity\SolicitudCompra
     *
     * @ORM\ManyToOne(targetEntity="SolicitudCompra", cascade={"persist"})
     * @ORM\JoinColumn(name="id_solicitud_compra", referencedColumnName="id", nullable=false)
     * 
     */
    protected $solicitudCompra;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_cambio_estado", type="datetime", nullable=false)
     */
    protected $fechaCambioEstado;

    /**
     * @var \ADIF\ComprasBundle\Entity\EstadoSolicitudCompra
     *
     * @ORM\ManyToOne(targetEntity="EstadoSolicitudCompra")
     * @ORM\JoinColumn(name="id_estado_solicitud_compra", referencedColumnName="id", nullable=false)
     * 
     */
    protected $estadoSolicitudCompra;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    protected $descripcion;
    
    /**
     * @ORM\Column(name="id_usuario", type="integer", nullable=false)
     */
    protected $idUsuario;
    
     /**
     * @var ADIF\AutenticacionBundle\Entity\Usuario
     */
    protected $usuario;

    /**
     * Set fechaCambioEstado
     *
     * @param \DateTime $fechaCambioEstado
     * @return HistoricoSolicitudCompra
     */
    public function setFechaCambioEstado($fechaCambioEstado) {
        $this->fechaCambioEstado = $fechaCambioEstado;

        return $this;
    }

    /**
     * Get fechaCambioEstado
     *
     * @return \DateTime 
     */
    public function getFechaCambioEstado() {
        return $this->fechaCambioEstado;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return HistoricoSolicitudCompra
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Set solicitudCompra
     *
     * @param \ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudCompra
     * @return HistoricoSolicitudCompra
     */
    public function setSolicitudCompra(\ADIF\ComprasBundle\Entity\SolicitudCompra $solicitudCompra) {
        $this->solicitudCompra = $solicitudCompra;

        return $this;
    }

    /**
     * Get solicitudCompra
     *
     * @return \ADIF\ComprasBundle\Entity\SolicitudCompra 
     */
    public function getSolicitudCompra() {
        return $this->solicitudCompra;
    }

    /**
     * Set estadoSolicitudCompra
     *
     * @param \ADIF\ComprasBundle\Entity\EstadoSolicitudCompra $estadoSolicitudCompra
     * @return HistoricoSolicitudCompra
     */
    public function setEstadoSolicitudCompra(\ADIF\ComprasBundle\Entity\EstadoSolicitudCompra $estadoSolicitudCompra) {
        $this->estadoSolicitudCompra = $estadoSolicitudCompra;

        return $this;
    }

    /**
     * Get estadoSolicitudCompra
     *
     * @return \ADIF\ComprasBundle\Entity\EstadoSolicitudCompra 
     */
    public function getEstadoSolicitudCompra() {
        return $this->estadoSolicitudCompra;
    }
    
     /**
     * 
     * @return type
     */
    public function getIdUsuario() {
        return $this->idUsuario;
    }

    /**
     * 
     * @param \ADIF\AutenticacionBundle\Entity\Usuario $usuario
     */
    public function setUsuario($usuario) {

        if (null != $usuario) {
            $this->idUsuario = $usuario->getId();
        } //.
        else {
            $this->idUsuario = null;
        }

        $this->usuario = $usuario;
    }

    /**
     * 
     * @return type
     */
    public function getUsuario() {
        return $this->usuario;
    }

}
