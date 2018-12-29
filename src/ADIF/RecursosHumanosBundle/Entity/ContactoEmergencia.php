<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable; 
/**
 * ContactoEmergencia
 *
 * @ORM\Table(name="contacto_emergencia", indexes={@ORM\Index(name="empleado_1", columns={"id_empleado"}), @ORM\Index(name="tipo_relacion_1", columns={"id_tipo_relacion"})})
 * @ORM\Entity
 */
class ContactoEmergencia extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="apellido", type="string", length=255, nullable=false)
     */
    private $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", nullable=false)
     */
    private $telefono;

    /**
     * @var integer
     *
     * @ORM\Column(name="domicilio", type="string", nullable=true)
     */
    private $domicilio;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Empleado
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Empleado", inversedBy="contactosEmergencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_empleado", referencedColumnName="id")
     * })
     */
    private $idEmpleado;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\TipoRelacion
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\TipoRelacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_relacion", referencedColumnName="id", nullable=false)
     * })
     */
    private $idTipoRelacion;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     * @return Persona
     */
    public function setApellido($apellido) {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido() {
        return $this->apellido;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Persona
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Persona
     */
    public function setTelefono($telefono) {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono() {
        return $this->telefono;
    }

    /**
     * Set domicilio
     *
     * @param string $domicilio
     * @return Persona
     */
    public function setDomicilio($domicilio) {
        $this->domicilio = $domicilio;

        return $this;
    }

    /**
     * Get domicilio
     *
     * @return string
     */
    public function getDomicilio() {
        return $this->domicilio;
    }

    /**
     * Set idEmpleado
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Empleado $idEmpleado
     * @return Familiar
     */
    public function setIdEmpleado(\ADIF\RecursosHumanosBundle\Entity\Empleado $idEmpleado = null) {
        $this->idEmpleado = $idEmpleado;

        return $this;
    }

    /**
     * Get idEmpleado
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Empleado 
     */
    public function getIdEmpleado() {
        return $this->idEmpleado;
    }

    /**
     * Set idTipoRelacion
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\TipoRelacion $idTipoRelacion
     * @return Familiar
     */
    public function setIdTipoRelacion(\ADIF\RecursosHumanosBundle\Entity\TipoRelacion $idTipoRelacion = null) {
        $this->idTipoRelacion = $idTipoRelacion;

        return $this;
    }

    /**
     * Get idTipoRelacion
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\TipoRelacion 
     */
    public function getIdTipoRelacion() {
        return $this->idTipoRelacion;
    }

}
