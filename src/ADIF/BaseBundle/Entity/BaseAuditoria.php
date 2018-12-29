<?php

namespace ADIF\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BaseAuditoria
 *
 * @ORM\MappedSuperclass
 */
class BaseAuditoria extends BaseEliminadoLogico {

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="fecha_creacion", type="datetime", nullable=false)
     */
    protected $fechaCreacion;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="fecha_ultima_actualizacion", type="datetime", nullable=false)
     */
    protected $fechaUltimaActualizacion;

    /**
     * @ORM\Column(name="id_usuario_creacion", type="integer", nullable=true)
     */
    protected $idUsuarioCreacion;

    /**
     * @var ADIF\AutenticacionBundle\Entity\Usuario
     */
    protected $usuarioCreacion;

    /**
     * @ORM\Column(name="id_usuario_ultima_modificacion", type="integer", nullable=true)
     */
    protected $idUsuarioUltimaModificacion;

    /**
     * @var ADIF\AutenticacionBundle\Entity\Usuario
     */
    protected $usuarioUltimaModificacion;
    
    /**
     * @var ADIF\AutenticacionBundle\Entity\Usuario
     */
    protected $username;

    /**
     * Get fechaCreacion
     *
     * @return \DateTime 
     */
    public function getFechaCreacion() {
        return $this->fechaCreacion;
    }

    /**
     * Set eliminado
     *
     * @param \DateTime $fechaCreacion
     * @return BaseAuditoria
     */
    public function setFechaCreacion($fechaCreacion) {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaUltimaActualizacion
     *
     * @return \DateTime 
     */
    public function getUltimaActualizacion() {
        return $this->fechaUltimaActualizacion;
    }

    /**
     * Set eliminado
     *
     * @param \DateTime $fechaUltimaActualizacion
     * @return BaseAuditoria
     */
    public function setFechaUltimaActualizacion($fechaUltimaActualizacion) {
        $this->fechaUltimaActualizacion = $fechaUltimaActualizacion;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdUsuarioCreacion() {
        return $this->idUsuarioCreacion;
    }

    /**
     * 
     * @param \ADIF\AutenticacionBundle\Entity\Usuario $usuarioCreacion
     */
    public function setUsuarioCreacion($usuarioCreacion) {

        if (null != $usuarioCreacion) {
            $this->idUsuarioCreacion = $usuarioCreacion->getId();
        } //.
        else {
            $this->idUsuarioCreacion = null;
        }

        $this->usuarioCreacion = $usuarioCreacion;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getUsuarioCreacion() {
        return $this->usuarioCreacion;
    }

    /**
     * 
     * @return type
     */
    public function getIdUsuarioUltimaModificacion() {
        return $this->idUsuarioUltimaModificacion;
    }

    /**
     * 
     * @param \ADIF\AutenticacionBundle\Entity\Usuario $usuarioUltimaModificacion
     */
    public function setUsuarioUltimaModificacion($usuarioUltimaModificacion) {

        if (null != $usuarioUltimaModificacion) {
            $this->idUsuarioUltimaModificacion = $usuarioUltimaModificacion->getId();
        } //.
        else {
            $this->idUsuarioUltimaModificacion = null;
        }

        $this->usuarioUltimaModificacion = $usuarioUltimaModificacion;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getUsuarioUltimaModificacion() {
        return $this->usuarioUltimaModificacion;
    }
    
    /**
     * 
     * @return type
     */
    public function getUsername() {
        return $this->username;
    }

}
