<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ProveedorUte
 *
 * @ORM\Table("proveedor_ute")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Entity\ProveedorUteRepository")
 */
class ProveedorUte extends BaseAuditoria implements BaseAuditable
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
     * @ORM\OneToOne(targetEntity="Usuario", inversedBy="proveedorUte")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=false) 
     * @Assert\NotBlank()
     */
    protected $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=50, nullable=false, unique=true)
     */
    private $denominacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_constitucion", type="date", nullable=true)
     */
    private $fechaConstitucion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_finalizacion", type="date", nullable=true)
     */
    private $fechaFinalizacion;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_inscripcion", type="string", length=50, nullable=true, unique=true)
     */
    private $numeroInscripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=50, nullable=true, unique=true)
     */
    private $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=120, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_fantasia", type="string", length=50, nullable=true)
     */
    private $nombreFantasia;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorUteMiembros", mappedBy="proveedorUteMiembros")
     */
    protected $proveedorUteMiembros;

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
     * @return ProveedorUte
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
     * Set denominacion
     *
     * @param string $denominacion
     * @return ProveedorUte
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string 
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }

    /**
     * Set fechaConstitucion
     *
     * @param \DateTime $fechaConstitucion
     * @return ProveedorUte
     */
    public function setFechaConstitucion($fechaConstitucion)
    {
        $this->fechaConstitucion = $fechaConstitucion;

        return $this;
    }

    /**
     * Get fechaConstitucion
     *
     * @return \DateTime 
     */
    public function getFechaConstitucion()
    {
        return $this->fechaConstitucion;
    }

    /**
     * Set fechaFinalizacion
     *
     * @param \DateTime $fechaFinalizacion
     * @return ProveedorUte
     */
    public function setFechaFinalizacion($fechaFinalizacion)
    {
        $this->fechaFinalizacion = $fechaFinalizacion;

        return $this;
    }

    /**
     * Get fechaFinalizacion
     *
     * @return \DateTime 
     */
    public function getFechaFinalizacion()
    {
        return $this->fechaFinalizacion;
    }

    /**
     * Set numeroInscripcion
     *
     * @param string $numeroInscripcion
     * @return ProveedorUte
     */
    public function setNumeroInscripcion($numeroInscripcion)
    {
        $this->numeroInscripcion = $numeroInscripcion;

        return $this;
    }

    /**
     * Get numeroInscripcion
     *
     * @return string 
     */
    public function getNumeroInscripcion()
    {
        return $this->numeroInscripcion;
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     * @return ProveedorUte
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return string 
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return ProveedorUte
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set nombreFantasia
     *
     * @param string $nombreFantasia
     * @return ProveedorUte
     */
    public function setNombreFantasia($nombreFantasia)
    {
        $this->nombreFantasia = $nombreFantasia;

        return $this;
    }

    /**
     * Get nombreFantasia
     *
     * @return string 
     */
    public function getNombreFantasia()
    {
        return $this->nombreFantasia;
    }
    
    public function getUsuario()
    {
        return $this->usuario;
    }

    public function getProveedorUteMiembros()
    {
        return $this->proveedorUteMiembros;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function setProveedorUteMiembros($proveedorUteMiembros)
    {
        $this->proveedorUteMiembros = $proveedorUteMiembros;
    }

}
