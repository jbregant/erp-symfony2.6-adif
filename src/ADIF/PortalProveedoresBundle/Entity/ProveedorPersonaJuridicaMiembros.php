<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ProveedorPersonaJuridicaMiembros
 *
 * @ORM\Table("proveedor_persona_juridica_miembros")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\ProveedorPersonaJuridicaMiembrosRepository")
 */
class ProveedorPersonaJuridicaMiembros extends BaseAuditoria implements BaseAuditable
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="ProveedorDatoPersonal", inversedBy="proveedorPersonaJuridicaMiembros")
     * @ORM\JoinColumn(name="id_proveedor_dato_personal", referencedColumnName="id", nullable=false)
     */
    private $proveedorDatoPersonal;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=13, nullable=false)
     * @Assert\Regex(
     *   pattern="/^[0-9]{2}-[0-9]{8}-[0-9]{1}$/",
     *   match=false,
     *   message="Formato de CUIT inválido")
     */
    protected $cuit;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     *
     */
    protected $apellido;

    /**
     * @var decimal
     *
     * @ORM\Column(name="participacion", type="decimal", precision=10, scale=0, nullable=false)
     *
     */
    protected $participacion;

    public function __construct()
    {}

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function __clone()
    {
        $this->id = null;
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
     * Set proveedorDatoPersonal
     *
     * @param integer $proveedorDatoPersonal
     * @return ProveedorPersonaJuridicaMiembros
     */
    public function setProveedorDatoPersonal($proveedorDatoPersonal)
    {
        $this->proveedorDatoPersonal = $proveedorDatoPersonal;
        return $this;
    }

    /**
     * Get proveedorDatoPersonal
     *
     * @return integer
     */
    public function getProveedorDatoPersonal()
    {
        return $this->proveedorDatoPersonal;
    }

    /**
     * Set cuit
     *
     * @param string $cuit
     * @return ProveedorPersonaJuridicaMiembros
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;
        return $this;
    }

    /**
     * Get cuit
     *
     * @return string
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * Set nombre
     *id_tipo_documento
     * @param integer $nombre
     * @return ProveedorPersonaJuridicaMiembros
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        return $this;
    }

    /**
     * Get nombre
     *
     * @return integer
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param integer $apellido
     * @return ProveedorPersonaJuridicaMiembros
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
        return $this;
    }

    /**
     * Get apellido
     *
     * @return integer
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set participacion
     *
     * @param decimal $participacion
     * @return ProveedorPersonaJuridicaMiembros
     */
    public function setParticipacion($participacion)
    {
        $this->participacion = $participacion;
        return $this;
    }

    /**
     * Get participacion
     *
     * @return decimal
     */
    public function getParticipacion()
    {
        return $this->participacion;
    }
}
