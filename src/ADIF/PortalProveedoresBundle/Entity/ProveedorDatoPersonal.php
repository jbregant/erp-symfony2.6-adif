<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\BaseBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * ProveedorDatoPersonal
 *
 * @ORM\Table("proveedor_dato_personal")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\ProveedorDatoPersonalRepository")
 *
 */
class ProveedorDatoPersonal extends BaseAuditoria implements BaseAuditable
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
     * @var Usuario
     *
     * @ORM\ManyToMany(targetEntity="Usuario", inversedBy="proveedorDatoPersonal")
     * @JoinTable(name="usuario_dato_personal")
     * @Assert\NotBlank()
     */
    protected $usuario;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoPersona", inversedBy="proveedorDatoPersonal"))
     * @ORM\JoinColumn(name="id_tipo_persona", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    protected $tipoPersona;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoPersonaJuridica", inversedBy="proveedorDatoPersonal"))
     * @ORM\JoinColumn(name="id_tipo_persona_juridica", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    protected $tipoPersonaJuridica;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoProveedor", inversedBy="proveedorDatoPersonal"))
     * @ORM\JoinColumn(name="id_tipo_proveedor", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    protected $tipoProveedor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="proveedor", type="boolean", nullable=false)
     */
    protected $proveedor;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="extranjero", type="boolean", nullable=true)
     */
    protected $extranjero;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     *
     */
    protected $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     *
     */
    protected $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_documento", type="string", length=255, nullable=true)
     */
    protected $idTipoDocumento;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\TipoDocumento
     */
    protected $tipoDocumento;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_documento", type="string", nullable=true)
     * @Assert\Regex("/^[0-9]{2,2}\.[0-9]{3,3}\.[0-9]{3,3}$$/")
     */
    protected $numeroDocumento;

    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=255, nullable=true)
     */
    protected $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=13, nullable=true)
     * @Assert\Regex(
     *   pattern="/^[0-9]{2}-[0-9]{8}-[0-9]{1}$/",
     *   match=false,
     *   message="Formato de CUIT inválido")
     */
    protected $cuit;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_id_tributaria", type="string", length=255, nullable=true)
     */
    protected $numeroIdTributaria;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_pais_radicacion", type="integer", nullable=true)
     */
    protected $idPaisRadicacion;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Nacionalidad
     */
    protected $paisRadicacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio_actividades", type="date", nullable=true)
     */
    protected $fechaInicioActividades;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    protected $email;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorPersonaJuridicaMiembros", mappedBy="proveedorDatoPersonal")
     */
    protected $proveedorPersonaJuridicaMiembros;

    /**
     * @var integer
     * @ORM\Column(name="proveedor_id", type="integer", nullable=true)
     */
    protected $idProveedorAsoc;

    /**
     * @var ADIF\ComprasBundle\Entity\Proveedor
     */
    protected $proveedorAsoc;

    //------------------------------------------------------------//
    /**
     * @ORM\OneToMany(targetEntity="ProveedorDatoContacto", mappedBy="idDatoPersonal")
     */
    private $proveedorDatoContacto;
//
//    /**
//     * @ORM\OneToOne(targetEntity="ProveedorDatoPersonal", mappedBy="usuario")
//     */
//    private $proveedorDatoPersonal;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorActividad", mappedBy="idDatoPersonal")
     */
    private $proveedorActividad;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorDomicilio", mappedBy="idDatoPersonal")
     */
    private $proveedorDomicilio;

    /**
     * @ORM\OneToOne(targetEntity="ProveedorDatoImpositivo", mappedBy="idDatoPersonal")
     */
    private $proveedorDatoImpositivo;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorRubro", mappedBy="idDatoPersonal")
     */
    private $proveedorRubro;

    /**
     * @ORM\OneToOne(targetEntity="ProveedorEvaluacion", mappedBy="idDatoPersonal")
     */
    private $proveedorEvaluacion;

    /**
     * @ORM\OneToOne(targetEntity="ProveedorUte", mappedBy="idDatoPersonal")
     */
    private $proveedorUte;

    /**
     * @ORM\OneToOne(targetEntity="ProveedorDatoBancario", mappedBy="idDatoPersonal")
     */
    private $proveedorDatoBancario;

    /**
     * @ORM\OneToOne(targetEntity="ProveedorDatoGcshm", mappedBy="idDatoPersonal")
     */
    private $proveedorDatoGcshm;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorRepresentanteApoderado", mappedBy="idDatoPersonal")
     */
    private $proveedorRepresentanteApoderado;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorDocumentacion", mappedBy="idDatoPersonal")
     */
    private $proveedorDocumentacion;

    public function getProveedorActividad() {
        return $this->proveedorActividad;
    }

    public function setProveedorActividad($proveedorActividad) {
        $this->proveedorActividad = $proveedorActividad;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProveedorEvaluacion()
    {
        return $this->proveedorEvaluacion;
    }

    /**
     * @param mixed $proveedorEvaluacion
     *
     * @return self
     */
    public function setProveedorEvaluacion($proveedorEvaluacion)
    {
        $this->proveedorEvaluacion = $proveedorEvaluacion;

        return $this;
    }

    public function getProveedorDomicilio() {
        return $this->proveedorDomicilio;
    }

    public function setProveedorDomicilio($proveedorDomicilio) {
        $this->proveedorDomicilio = $proveedorDomicilio;
        return $this;
    }

    public function getProveedorRubro()
    {
        return $this->proveedorRubro;
    }

    public function setProveedorRubro($proveedorRubro)
    {
        $this->proveedorRubro = $proveedorRubro;
    }

    public function getProveedorDatoContacto() {
        return $this->proveedorDatoContacto;
    }

    public function getProveedorDatoImpositivo()
    {
        return $this->proveedorDatoImpositivo;
    }

    public function setProveedorDatoImpositivo($proveedorDatoImpositivo)
    {
        $this->proveedorDatoImpositivo = $proveedorDatoImpositivo;
    }

    public function getProveedorDatoBancario()
    {
        return $this->proveedorDatoBancario;
    }

    public function setProveedorDatoBancario($proveedorDatoBancario)
    {
        $this->proveedorDatoBancario = $proveedorDatoBancario;
    }

    public function getProveedorUte()
    {
        return $this->proveedorUte;
    }

    public function setProveedorUte($proveedorUte)
    {
        $this->proveedorUte = $proveedorUte;
    }

    public function getProveedorRepresentanteApoderado()
    {
        return $this->proveedorRepresentanteApoderado;
    }

    public function setProveedorRepresentanteApoderado($proveedorRepresentanteApoderado)
    {
        $this->proveedorRepresentanteApoderado = $proveedorRepresentanteApoderado;
    }
    public function getProveedorDocumentacion()
    {
        return $this->proveedorDocumentacion;
    }

    public function setProveedorDocumentacion($proveedorDocumentacion)
    {
        $this->proveedorDocumentacion = $proveedorDocumentacion;
    }

    public function getProveedorDatoGcshm()
    {
        return $this->proveedorDatoGcshm;
    }

    public function setProveedorDatoGcshm($proveedorDatoGcshm)
    {
        $this->proveedorDatoGcshm = $proveedorDatoGcshm;
    }
    //------------------------------------------------------------//

    public function __construct()
    {
        $this->usuario = new ArrayCollection();
    }

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
     * Set usuario
     *
     * @param integer $usuario
     * @return ProveedorDatoPersonal
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
     * Set tipoPersona
     *
     * @param integer $tipoPersona
     * @return ProveedorDatoPersonal
     */
    public function setTipoPersona($tipoPersona)
    {
        $this->tipoPersona = $tipoPersona;
        return $this;
    }

    /**
     * Get tipoPersona
     *
     * @return integer
     */
    public function getTipoPersona()
    {
        return $this->tipoPersona;
    }

    /**
     * Set tipoPersonaJuridica
     *
     * @param integer $tipoPersonaJuridica
     * @return ProveedorDatoPersonal
     */
    public function setTipoPersonaJuridica($tipoPersonaJuridica)
    {
        $this->tipoPersonaJuridica = $tipoPersonaJuridica;
        return $this;
    }

    /**
     * Get tipoPersonaJuridica
     *
     * @return integer
     */
    public function getTipoPersonaJuridica()
    {
        return $this->tipoPersonaJuridica;
    }

    /**
     * Set nombre
     *
     * @param integer $nombre
     * @return ProveedorDatoPersonal
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
     * @return ProveedorDatoPersonal
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

    //TipoDocumento de RecursosHumanoBundle:
    public function getIdTipoDocumento()
    {
        return $this->idTipoDocumento;
    }

    public function setIdTipoDocumento($idTipoDocumento)
    {
        $this->idTipoDocumento = $idTipoDocumento;
        return $this;
    }

    /**
     * Set tipoDocumento
     *
     * @param \ADIF\RecursosHumanoBundle\Entity\TipoDocumento $tipoDocumento
     */
    public function setTipoDocumento($tipoDocumento)
    {
        if (null != $tipoDocumento) {
            $this->idTipoDocumento = $tipoDocumento->getId();
        } else {
            $this->idTipoDocumento = null;
        }

        $this->tipoDocumento = $tipoDocumento;
    }

    /**
     * Get tipoDocumento
     *
     * @return integer
     */
    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    /**
     * Set numeroDocumento
     *
     * @param string $numeroDocumento
     * @return ProveedorDatoPersonal
     */
    public function setNumeroDocumento($numeroDocumento)
    {
        $this->numeroDocumento = $numeroDocumento;
        return $this;
    }

    /**
     * Get numeroDocumento
     *
     * @return string
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    //Nacionalidad de RecursosHumanoBundle:
    public function getIdPaisRadicacion()
    {
        return $this->idPaisRadicacion;
    }

    public function setIdPaisRadicacion($idPaisRadicacion)
    {
        $this->idPaisRadicacion = $idPaisRadicacion;
        return $this;
    }

    /**
     * Set paisRadicacion
     *
     * @param \ADIF\RecursosHumanoBundle\Entity\Nacionalidad $paisRadicacion
     */
    public function setPaisRadicacion($paisRadicacion)
    {
        if (null != $paisRadicacion) {
            $this->idPaisRadicacion = $paisRadicacion->getId();
        } else {
            $this->idPaisRadicacion = null;
        }

        $this->paisRadicacion = $paisRadicacion;
    }

    /**
     * Get paisRadicacion
     *
     * @return integer
     */
    public function getPaisRadicacion()
    {
        return $this->paisRadicacion;
    }

    /**
     * Set fechaInicioActividades
     *
     * @param \DateTime $fechaInicioActividades
     * @return ProveedorDatoPersonal
     */
    public function setFechaInicioActividades($fechaInicioActividades)
    {
        $this->fechaInicioActividades = $fechaInicioActividades;
        return $this;
    }

    /**
     * Get fechaInicioActividades
     *
     * @return \DateTime
     */
    public function getFechaInicioActividades()
    {
        return $this->fechaInicioActividades;
    }

    /**
     * Set razRnSociarazonSocial
     *
     * @param string $razonSocial
     * @return ProveedorDatoPersonal
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
     * @return ProveedorDatoPersonal
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    
        /**
     * @return string
     */
    public function getDireccionWeb()
    {
        return $this->direccionWeb;
    }

    public function getCuit()
    {
        return $this->cuit;
    }

    public function setCuit($cuit)
    {
        $this->cuit = $cuit;
    }

    public function getProveedorPersonaJuridicaMiembros()
    {
        return $this->proveedorPersonaJuridicaMiembros;
    }

    public function setMiembrosProveedorDatoPersonal($miembrosProveedorDatoPersonal)
    {
        $this->miembrosProveedorDatoPersonal = $miembrosProveedorDatoPersonal;
        return $this;
    }

    /**
     * Get the value of tipoProveedor
     *
     * @return  integer
     */
    public function getTipoProveedor()
    {
        return $this->tipoProveedor;
    }

    /**
     * Set the value of tipoProveedor
     *
     * @param  integer  $tipoProveedor
     *
     * @return  self
     */
    public function setTipoProveedor($tipoProveedor)
    {
        $this->tipoProveedor = $tipoProveedor;

        return $this;
    }

    /**
     * Get the value of proveedor
     *
     * @return  boolean
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }

    /**
     * Set the value of proveedor
     *
     * @param  boolean  $proveedor
     *
     * @return  self
     */
    public function setProveedor($proveedor)
    {
        $this->proveedor = $proveedor;

        return $this;
    }
    
    /**
     * @return bool
     */
    public function esProveedor()
    {
        return $this->proveedor;
    }

    /**
     * @return bool
     */
    public function getExtranjero()
    {
        return $this->extranjero;
    }

    /**
     * @param bool $extranjero
     * @return ProveedorDatoPersonal
     */
    public function setExtranjero($extranjero)
    {
        $this->extranjero = $extranjero;
        return $this;
    }

    /**
     * @return bool
     */
    public function esExtranjero()
    {
        return $this->extranjero;
    }    

    /**
     * Get the value of numeroIdTributaria
     *
     * @return  string
     */
    public function getNumeroIdTributaria()
    {
        return $this->numeroIdTributaria;
    }

    /**
     * Set the value of numeroIdTributaria
     *
     * @param  string  $numeroIdTributaria
     *
     * @return  self
     */
    public function setNumeroIdTributaria($numeroIdTributaria)
    {
        $this->numeroIdTributaria = $numeroIdTributaria;

        return $this;
    }

    public function getIdProveedorAsoc()
    {
        return $this->idProveedorAsoc;
    }

    public function setIdProveedorAsoc($idProveedor)
    {
        $this->idProveedorAsoc = $idProveedor;
        return $this;
    }

    /**
     * Set proveedor
     *
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     */
    public function setProveedorAsoc($Proveedor)
    {
        if (null != $Proveedor) {
            $this->proveedorAsoc = $Proveedor;
        } else {
            $this->proveedorAsoc = null;
        }

        $this->proveedorAsoc = $Proveedor;
    }

    /**
     * Get proveedor
     *
     * @return type
     */
    public function getProveedorAsoc()
    {
        return $this->proveedorAsoc;
    }

    /**
     * Get IdUsuario
     *
     * @return GYL\UsuarioBundle\Entity\Usuario
     */
    public function getId_Usuario()
    {
        return $this->idUsuario;
    }

    public function addIdUsuario($idUsuario){
        $this->idUsuario[] = $idUsuario;
    }
}
