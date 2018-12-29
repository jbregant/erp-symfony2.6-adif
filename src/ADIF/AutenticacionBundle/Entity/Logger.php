<?php

namespace ADIF\AutenticacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @author Manuel Becerra
 * created 30/06/2014
 * 
 * @ORM\Entity
 * @ORM\Table(name="logger")
 */
class Logger {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="id_entidad", type="integer", length=11)
     */
    protected $idEntidad;

    /**
     * @ORM\Column(name="clase_entidad", type="text", length=255)
     */
    protected $claseEntidad;

    /**
     * @ORM\Column(name="query", type="text", length=2048, nullable=true)
     */
    protected $query;

    /**
     * @ORM\Column(name="observacion", type="text", length=1024, nullable=true)
     */
    protected $observacion;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * 
     */
    protected $usuario;

    /**
     * @ORM\Column(name="accion", type="text", length=255)
     */
    protected $accion;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="fecha", type="datetime")
     */
    protected $fecha;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set id_entidad
     *
     * @param integer $idEntidad
     * @return Logger
     */
    public function setIdEntidad($idEntidad) {
        $this->idEntidad = $idEntidad;

        return $this;
    }

    /**
     * Get id_entidad
     *
     * @return integer 
     */
    public function getIdEntidad() {
        return $this->idEntidad;
    }

    /**
     * Set entidad_clase
     *
     * @param string $claseEntidad
     * @return Logger
     */
    public function setClaseEntidad($claseEntidad) {
        $this->claseEntidad = $claseEntidad;

        return $this;
    }

    /**
     * Get entidad_clase
     *
     * @return string 
     */
    public function getClaseEntidad() {
        return $this->claseEntidad;
    }

    /**
     * Set actualizacion
     *
     * @param \DateTime $fecha
     * @return Logger
     */
    public function setFecha($fecha) {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get actualizacion
     *
     * @return \DateTime 
     */
    public function getFecha() {
        return $this->fecha;
    }

    /**
     * Set query
     *
     * @param string $query
     * @return Logger
     */
    public function setQuery($query) {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return string 
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * Set $observacion
     *
     * @param string $observacion
     * @return Logger
     */
    public function setObservacion($observacion) {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get $observacion
     *
     * @return string 
     */
    public function getObservacion() {
        return $this->observacion;
    }

    /**
     * Set usuario
     *
     * @param \ADIF\AutenticacionBundle\Entity\Usuario $usuario
     * @return Logger
     */
    public function setUsuario(\ADIF\AutenticacionBundle\Entity\Usuario $usuario = null) {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \ADIF\AutenticacionBundle\Entity\Usuario 
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * Set accion
     *
     * @param string $accion
     * @return Logger
     */
    public function setAccion($accion) {
        $this->accion = $accion;

        return $this;
    }

    /**
     * Get accion
     *
     * @return string 
     */
    public function getAccion() {
        return $this->accion;
    }

}
