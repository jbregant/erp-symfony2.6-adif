<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\RecursosHumanosBundle\Entity\BaseEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Persona
 *
 * @ORM\Table(name="persona", indexes={@ORM\Index(name="domicilio_1", columns={"id_domicilio"}), @ORM\Index(name="estado_civil_1", columns={"id_estado_civil"}), @ORM\Index(name="tipo_documento", columns={"id_tipo_documento"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"cuil"}, message="Ya existe una persona con ese cuil")
 */
class Persona extends BaseEntity {

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
     * @ORM\Column(name="sexo", type="string", length=255, nullable=false)
     */
    private $sexo;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_documento", type="string", length=255, nullable=false)
     */
    private $nroDocumento;

    /**
     * @var string
     *
     * @ORM\Column(name="cuil", type="string", length=255, nullable=true)
     */
    private $cuil;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_nacimiento", type="date", nullable=true)
     */
    private $fechaNacimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="celular", type="string", length=255, nullable=true)
     */
    private $celular;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Domicilio
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Domicilio", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_domicilio", referencedColumnName="id")
     * })
     */
    private $idDomicilio;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\EstadoCivil
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\EstadoCivil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_estado_civil", referencedColumnName="id")
     * })
     */
    private $idEstadoCivil;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\TipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\TipoDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tipo_documento", referencedColumnName="id", nullable=false)
     * })
     */
    private $idTipoDocumento;

    /**
     * @var \ADIF\RecursosHumanosBundle\Entity\Nacionalidad
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Nacionalidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_nacionalidad", referencedColumnName="id")
     * })
     */
    private $idNacionalidad;

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
     * Set sexo
     *
     * @param string $sexo
     * @return Persona
     */
    public function setSexo($sexo) {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return string 
     */
    public function getSexo() {
        return $this->sexo;
    }

    /**
     * Set nroDocumento
     *
     * @param string $nroDocumento
     * @return Persona
     */
    public function setNroDocumento($nroDocumento) {
        $this->nroDocumento = $nroDocumento;

        return $this;
    }

    /**
     * Get nroDocumento
     *
     * @return string 
     */
    public function getNroDocumento() {
        return $this->nroDocumento;
    }

    /**
     * Set cuil
     *
     * @param string $cuil
     * @return Persona
     */
    public function setCuil($cuil) {
        $this->cuil = $cuil;

        return $this;
    }

    /**
     * Get cuil
     *
     * @return string 
     */
    public function getCuil() {
        return $this->cuil;
    }

    /**
     * Set fechaNacimiento
     *
     * @param \DateTime $fechaNacimiento
     * @return Persona
     */
    public function setFechaNacimiento($fechaNacimiento) {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    /**
     * Get fechaNacimiento
     *
     * @return \DateTime 
     */
    public function getFechaNacimiento() {
        return $this->fechaNacimiento;
    }

    /**
     * Set idNacionalidad
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Nacionalidad $idNacionalidad
     * @return Persona
     */
    public function setIdNacionalidad(\ADIF\RecursosHumanosBundle\Entity\Nacionalidad $idNacionalidad = null) {
        $this->idNacionalidad = $idNacionalidad;

        return $this;
    }

    /**
     * Get idNacionalidad
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Nacionalidad 
     */
    public function getIdNacionalidad() {
        return $this->idNacionalidad;
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
     * Set celular
     *
     * @param string $celular
     * @return Persona
     */
    public function setCelular($celular) {
        $this->celular = $celular;

        return $this;
    }

    /**
     * Get celular
     *
     * @return string 
     */
    public function getCelular() {
        return $this->celular;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Persona
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set idDomicilio
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Domicilio $idDomicilio
     * @return Persona
     */
    public function setIdDomicilio(\ADIF\RecursosHumanosBundle\Entity\Domicilio $idDomicilio = null) {
        $this->idDomicilio = $idDomicilio;

        return $this;
    }

    /**
     * Get idDomicilio
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Domicilio 
     */
    public function getIdDomicilio() {
        return $this->idDomicilio;
    }

    /**
     * Set idEstadoCivil
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\EstadoCivil $idEstadoCivil
     * @return Persona
     */
    public function setIdEstadoCivil(\ADIF\RecursosHumanosBundle\Entity\EstadoCivil $idEstadoCivil = null) {
        $this->idEstadoCivil = $idEstadoCivil;

        return $this;
    }

    /**
     * Get idEstadoCivil
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\EstadoCivil 
     */
    public function getIdEstadoCivil() {
        return $this->idEstadoCivil;
    }

    /**
     * Set idTipoDocumento
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\TipoDocumento $idTipoDocumento
     * @return Persona
     */
    public function setIdTipoDocumento(\ADIF\RecursosHumanosBundle\Entity\TipoDocumento $idTipoDocumento = null) {
        $this->idTipoDocumento = $idTipoDocumento;

        return $this;
    }

    /**
     * Get idTipoDocumento
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\TipoDocumento 
     */
    public function getIdTipoDocumento() {
        return $this->idTipoDocumento;
    }

    /**
     * 
     * @return Domicilio
     */
    public function getDomicilio() {
        return $this->getIdDomicilio();
    }

    /**
     * Set domicilio
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Domicilio $domicilio
     * @return \ADIF\RecursosHumanosBundle\Entity\Persona
     */
    public function setDomicilio(Domicilio $domicilio) {
        $this->setIdDomicilio($domicilio);
        return $this;
    }

    /**
     * Set estadoCivil
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\EstadoCivil $estadoCivil
     * @return Persona
     */
    public function setEstadoCivil(\ADIF\RecursosHumanosBundle\Entity\EstadoCivil $estadoCivil = null) {
        return $this->setIdEstadoCivil($estadoCivil);
    }

    /**
     * Get estadoCivil
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\EstadoCivil 
     */
    public function getEstadoCivil() {
        return $this->getIdEstadoCivil();
    }

    /**
     * 
     * @return type
     */
    public function __toString() {
        return $this->getNombreCompleto();
    }

    /**
     * 
     * @return type
     */
    public function getNombreCompleto() {
        return $this->getApellido() . ', ' . $this->getNombre();
    }

}
