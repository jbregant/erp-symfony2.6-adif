<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ProveedorRepresentanteApoderado
 *
 * @ORM\Table("proveedor_representante_apoderado")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Entity\ProveedorRepresentanteApoderadoRepository")
 */
class ProveedorRepresentanteApoderado extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="proveedorRepresentanteApoderado")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=false)  
     * @Assert\NotBlank()
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=64)
     */
    private $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit_cuil", type="string", length=11)
     */
    private $cuitCuil;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tipo_documento", type="string", nullable=true)
     */
    private $idTipoDocumento;

    /**
     *
     * @var ADIF\RecursosHumanosBundle\Entity\TipoDocumento
     */
    private $tipoDocumento;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_documento", type="integer")
     */
    private $numeroDocumento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_designacion", type="datetime")
     */
    private $fechaDesignacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="representante", type="boolean", nullable=true)
     */
    private $representante;

    /**
     * @var boolean
     *
     * @ORM\Column(name="apoderado", type="boolean", nullable=true)
     */
    private $apoderado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="poder_judicial", type="boolean", nullable=true)
     */
    private $poderJudicial;

    /**
     * @var boolean
     *
     * @ORM\Column(name="bancario", type="boolean", nullable=true)
     */
    private $bancario;

    /**
     * @var boolean
     *
     * @ORM\Column(name="adm_especial", type="boolean", nullable=true)
     */
    private $admEspecial;

    /**
     * @var boolean
     *
     * @ORM\Column(name="adm_general", type="boolean", nullable=true)
     */
    private $admGeneral;

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
     * @return ProveedorRepresentanteApoderado
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
     * Set usuario
     *
     * @param integer $usuario
     * @return ProveedorRepresentanteApoderado
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return integer 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return ProveedorRepresentanteApoderado
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     * @return ProveedorRepresentanteApoderado
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set cuitCuil
     *
     * @param string $cuitCuil
     * @return ProveedorRepresentanteApoderado
     */
    public function setCuitCuil($cuitCuil)
    {
        $this->cuitCuil = $cuitCuil;

        return $this;
    }

    /**
     * Get cuitCuil
     *
     * @return string 
     */
    public function getCuitCuil()
    {
        return $this->cuitCuil;
    }

    /**
     * Set idTipoDocumento
     *
     * @param integer $idTipoDocumento
     * @return ProveedorRepresentanteApoderado
     */
    public function setIdTipoDocumento($idTipoDocumento)
    {
        $this->idTipoDocumento = $idTipoDocumento;

        return $this;
    }

    /**
     * Get idTipoDocumento
     *
     * @return integer 
     */
    public function getIdTipoDocumento()
    {
        return $this->idTipoDocumento;
    }

    /**
     * Set tipoDocumento
     *
     * @param string $tipoDocumento
     * @return ProveedorRepresentanteApoderado
     */
    public function setTipoDocumento($tipoDocumento)
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    /**
     * Get tipoDocumento
     *
     * @return string 
     */
    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    /**
     * Set numeroDocumento
     *
     * @param integer $numeroDocumento
     * @return ProveedorRepresentanteApoderado
     */
    public function setNumeroDocumento($numeroDocumento)
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numeroDocumento
     *
     * @return integer 
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * Set fechaDesignacion
     *
     * @param \DateTime $fechaDesignacion
     * @return ProveedorRepresentanteApoderado
     */
    public function setFechaDesignacion($fechaDesignacion)
    {
        $this->fechaDesignacion = $fechaDesignacion;

        return $this;
    }

    /**
     * Get fechaDesignacion
     *
     * @return \DateTime 
     */
    public function getFechaDesignacion()
    {
        return $this->fechaDesignacion;
    }

    /**
     * Set representante
     *
     * @param boolean $representante
     * @return ProveedorRepresentanteApoderado
     */
    public function setRepresentante($representante)
    {
        $this->representante = $representante;

        return $this;
    }

    /**
     * Get representante
     *
     * @return boolean 
     */
    public function getRepresentante()
    {
        return $this->representante;
    }

    /**
     * Set apoderado
     *
     * @param boolean $apoderado
     * @return ProveedorRepresentanteApoderado
     */
    public function setApoderado($apoderado)
    {
        $this->apoderado = $apoderado;

        return $this;
    }

    /**
     * Get apoderado
     *
     * @return boolean 
     */
    public function getApoderado()
    {
        return $this->apoderado;
    }

    /**
     * Set poderJudicial
     *
     * @param boolean $poderJudicial
     * @return ProveedorRepresentanteApoderado
     */
    public function setPoderJudicial($poderJudicial)
    {
        $this->poderJudicial = $poderJudicial;

        return $this;
    }

    /**
     * Get poderJudicial
     *
     * @return boolean 
     */
    public function getPoderJudicial()
    {
        return $this->poderJudicial;
    }

    /**
     * Set bancario
     *
     * @param boolean $bancario
     * @return ProveedorRepresentanteApoderado
     */
    public function setBancario($bancario)
    {
        $this->bancario = $bancario;

        return $this;
    }

    /**
     * Get bancario
     *
     * @return boolean 
     */
    public function getBancario()
    {
        return $this->bancario;
    }

    /**
     * Set admEspecial
     *
     * @param boolean $admEspecial
     * @return ProveedorRepresentanteApoderado
     */
    public function setAdmEspecial($admEspecial)
    {
        $this->admEspecial = $admEspecial;

        return $this;
    }

    /**
     * Get admEspecial
     *
     * @return boolean 
     */
    public function getAdmEspecial()
    {
        return $this->admEspecial;
    }

    /**
     * Set admGeneral
     *
     * @param boolean $admGeneral
     * @return ProveedorRepresentanteApoderado
     */
    public function setAdmGeneral($admGeneral)
    {
        $this->admGeneral = $admGeneral;

        return $this;
    }

    /**
     * Get admGeneral
     *
     * @return boolean 
     */
    public function getAdmGeneral()
    {
        return $this->admGeneral;
    }
}
