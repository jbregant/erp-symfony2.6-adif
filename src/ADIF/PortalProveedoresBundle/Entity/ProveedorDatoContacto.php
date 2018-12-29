<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ProveedorDatoContacto
 *
 * @ORM\Table("proveedor_dato_contacto")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\ProveedorDatoContactoRepository")
 */
class ProveedorDatoContacto extends BaseAuditoria implements BaseAuditable {
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
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="proveedorDatoContacto")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    protected $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=64, nullable=false)
     * @Assert\NotBlank()
     * 
     * 
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=64, nullable=false)
     * @Assert\NotBlank()
     * 
     * 
     */
    private $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="area", type="string", length=64, nullable=false)
     * @Assert\NotBlank()
     * 
     * 
     */
    private $area;

    /**
     * @var string
     *
     * @ORM\Column(name="posicion", type="string", length=64, nullable=false)
     * @Assert\NotBlank()
     * 
     * 
     */
    private $posicion;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string",nullable=false)
     */
    private $telefono;

    /**
     * @var ProveedorDatoPersonal
     *
     * @ORM\ManyToOne(targetEntity="ProveedorDatoPersonal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_proveedor_dato_personal", referencedColumnName="id", nullable=false)
     * })
     */
    private $idDatoPersonal;

    /**
     * Set idDatoPersonal
     *
     * @param ProveedorDatoPersonal $idDatoPersonal
     *
     * @return ProveedorDatoContacto
     */
    public function setIdDatoPersonal($idDatoPersonal)
    {
        $this->idDatoPersonal = $idDatoPersonal;

        return $this;
    }

    /**
     * Get idDatoPersonal
     *
     * @return string
     */
    public function getIdDatoPersonal()
    {
        return $this->idDatoPersonal;
    }

    public function __construct() {}

    public function toArray() {
        return get_object_vars($this);
    }
    
    public function __clone() {
        $this->id = null;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set usuario
     *
     * @param integer $usuario
     * @return ProveedorDatoContacto
     */
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
        return $this;
    }

    /**
     * Get usuario
     *
     * @return integer
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * Set nombre
     *
     * @param integer $nombre
     * @return ProveedorDatoContacto
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;
        return $this;
    }

    /**
     * Get nombre
     *
     * @return integer
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param integer $apellido
     * @return ProveedorDatoContacto
     */
    public function setApellido($apellido) {
        $this->apellido = $apellido;
        return $this;
    }

    /**
     * Get apellido
     *
     * @return integer
     */
    public function getApellido() {
        return $this->apellido;
    }

    /**
     * Set area
     *
     * @param integer $area
     * @return ProveedorDatoContacto
     */
    public function setArea($area) {
        $this->area = $area;
        return $this;
    }

    /**
     * Get area
     *
     * @return integer
     */
    public function getArea() {
        return $this->area;
    }

    /**
     * Set posicion
     *
     * @param integer $posicion
     * @return ProveedorDatoContacto
     */
    public function setPosicion($posicion) {
        $this->posicion = $posicion;
        return $this;
    }

    /**
     * Get posicion
     *
     * @return integer
     */
    public function getPosicion() {
        return $this->posicion;
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
     * Set email
     *
     * @param string $email
     * 
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    /**
     * Set telefono
     *
     * @param \ADIF\ComprasBundle\Entity\ProveedorDatoContacto $telefono
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
}
