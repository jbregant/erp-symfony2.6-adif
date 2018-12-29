<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ProveedorEvaluacion
 *
 * @ORM\Table("proveedor_evaluacion")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\ProveedorEvaluacionRepository")
 */
class ProveedorEvaluacion extends BaseAuditoria implements BaseAuditable
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
     * @ORM\OneToOne(targetEntity="Usuario", inversedBy="proveedorEvaluacion")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    protected $usuario;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Gerencia", inversedBy="proveedorEvaluacionGalo")
     * @ORM\JoinColumn(name="id_gerencia_galo", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    protected $gerenciaGalo;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoEvaluacionGerencia", inversedBy="proveedorEvaluacionGalo")
     * @ORM\JoinColumn(name="id_estado_evaluacion_galo", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    protected $estadoEvaluacionGalo;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Gerencia", inversedBy="proveedorEvaluacionGcshm")
     * @ORM\JoinColumn(name="id_gerencia_gcshm", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    protected $gerenciaGcshm;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoEvaluacionGerencia", inversedBy="proveedorEvaluacionGcshm")
     * @ORM\JoinColumn(name="id_estado_evaluacion_gcshm", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    protected $estadoEvaluacionGcshm;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Gerencia", inversedBy="proveedorEvaluacionGafFinanzas")
     * @ORM\JoinColumn(name="id_gerencia_gaf_finanzas", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    protected $gerenciaGafFinanzas;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoEvaluacionGerencia", inversedBy="proveedorEvaluacionGafFinanzas")
     * @ORM\JoinColumn(name="id_estado_evaluacion_gaf_finanzas", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    protected $estadoEvaluacionGafFinanzas;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Gerencia", inversedBy="proveedorEvaluacionGafImpuestos")
     * @ORM\JoinColumn(name="id_gerencia_gaf_impuestos", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    protected $gerenciaGafImpuestos;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoEvaluacionGerencia", inversedBy="proveedorEvaluacionGafImpuestos")
     * @ORM\JoinColumn(name="id_estado_evaluacion_gaf_impuestos", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    protected $estadoEvaluacionGafImpuestos;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="EstadoEvaluacion", inversedBy="proveedorEvaluacion")
     * @ORM\JoinColumn(name="id_estado_evaluacion", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    protected $estadoEvaluacion;

    /**
     * @ORM\OneToMany(targetEntity="ObservacionEvaluacion", mappedBy="proveedorEvaluacion")
     */
    protected $observacionEvaluacion;

    /**
     * @ORM\OneToMany(targetEntity="ProveedorEvaluacionLog", mappedBy="proveedorEvaluacion")
     */
    protected $proveedorEvaluacionLog;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo_rechazo", type="text", nullable=true)
     */
    protected $MotivoRechazo;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo_rechazo_interno", type="text", nullable=true)
     */
    protected $MotivoRechazoInterno;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo_rechazo_galo", type="text", nullable=true)
     */
    protected $MotivoRechazoGalo;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo_rechazo_interno_galo", type="text", nullable=true)
     */
    protected $MotivoRechazoInternoGalo;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo_rechazo_gaf_finanzas", type="text", nullable=true)
     */
    protected $MotivoRechazoGafFinanzas;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo_rechazo_interno_gaf_finanzas", type="text", nullable=true)
     */
    protected $MotivoRechazoInternoGafFinanzas;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo_rechazo_gaf_impuestos", type="text", nullable=true)
     */
    protected $MotivoRechazoGafImpuestos;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo_rechazo_interno_gaf_impuestos", type="text", nullable=true)
     */
    protected $MotivoRechazoInternoGafImpuestos;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo_rechazo_ggcshm", type="text", nullable=true)
     */
    protected $MotivoRechazoGcshm;
    /**
     * @var string
     *
     * @ORM\Column(name="motivo_rechazo_interno_gcshm", type="text", nullable=true)
     */
    protected $MotivoRechazoInternoGcshm;

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
     * @return ProveedorEvaluacion
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

    public function __construct()
    {
        $this->observacionEvaluacion = new ArrayCollection();
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
     * @return ProveedorEvaluacion
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
     * Set estadoEvaluacionGerencia
     *
     * @param integer $estadoEvaluacionGerencia
     * @return ProveedorEvaluacionGerencia
     */
    public function setEstadoEvaluacionGerencia($estadoEvaluacionGerencia)
    {
        $this->estadoEvaluacionGerencia = $estadoEvaluacionGerencia;

        return $this;
    }

    /**
     * @param mixed $proveedorEvaluacionRevision
     *
     * @return self
     */
    public function setObseervacionEvaluacion($observacionEvaluacion)
    {
        $this->observacionEvaluacion = $observacionEvaluacion;

        return $this;
    }

    /**
     * @return integer
     */
    public function getEstadoEvaluacionGal()
    {
        return $this->estadoEvaluacionGal;
    }

    /**
     * @param integer $estadoEvaluacionGal
     *
     * @return self
     */
    public function setEstadoEvaluacionGal($estadoEvaluacionGal)
    {
        $this->estadoEvaluacionGal = $estadoEvaluacionGal;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getObservacionEvaluacion()
    {
        return $this->observacionEvaluacion;
    }

    /**
     * @return integer
     */
    public function getEstadoEvaluacionGalo()
    {
        return $this->estadoEvaluacionGalo;
    }

    /**
     * @return integer
     */
    public function getEstadoEvaluacionGcshm()
    {
        return $this->estadoEvaluacionGcshm;
    }

    /**
     * @return integer
     */
    public function getEstadoEvaluacionGafFinanzas()
    {
        return $this->estadoEvaluacionGafFinanzas;
    }

    /**
     * @return integer
     */
    public function getEstadoEvaluacionGafImpuestos()
    {
        return $this->estadoEvaluacionGafImpuestos;
    }

    /**
     * @param integer $estadoEvaluacionGalo
     *
     * @return self
     */
    public function setEstadoEvaluacionGalo($estadoEvaluacionGalo)
    {
        $this->estadoEvaluacionGalo = $estadoEvaluacionGalo;

        return $this;
    }

    /**
     * @param integer $estadoEvaluacionGcshm
     *
     * @return self
     */
    public function setEstadoEvaluacionGcshm($estadoEvaluacionGcshm)
    {
        $this->estadoEvaluacionGcshm = $estadoEvaluacionGcshm;

        return $this;
    }

    /**
     * @param integer $estadoEvaluacionGafFinanzas
     *
     * @return self
     */
    public function setEstadoEvaluacionGafFinanzas($estadoEvaluacionGafFinanzas)
    {
        $this->estadoEvaluacionGafFinanzas = $estadoEvaluacionGafFinanzas;

        return $this;
    }

    /**
     * @param integer $estadoEvaluacionGafImpuestos
     *
     * @return self
     */
    public function setEstadoEvaluacionGafImpuestos($estadoEvaluacionGafImpuestos)
    {
        $this->estadoEvaluacionGafImpuestos = $estadoEvaluacionGafImpuestos;

        return $this;
    }

    /**
     * @return integer
     */
    public function getGerenciaGal()
    {
        return $this->gerenciaGal;
    }

    /**
     * @param integer $gerenciaGal
     *
     * @return self
     */
    public function setGerenciaGal($gerenciaGal)
    {
        $this->gerenciaGal = $gerenciaGal;

        return $this;
    }

    /**
     * @return integer
     */
    public function getGerenciaGalo()
    {
        return $this->gerenciaGalo;
    }

    /**
     * @param integer $gerenciaGalo
     *
     * @return self
     */
    public function setGerenciaGalo($gerenciaGalo)
    {
        $this->gerenciaGalo = $gerenciaGalo;

        return $this;
    }

    /**
     * @return integer
     */
    public function getGerenciaGcshm()
    {
        return $this->gerenciaGcshm;
    }

    /**
     * @param integer $gerenciaGcshm
     *
     * @return self
     */
    public function setGerenciaGcshm($gerenciaGcshm)
    {
        $this->gerenciaGcshm = $gerenciaGcshm;

        return $this;
    }

    /**
     * @return integer
     */
    public function getGerenciaGafFinanzas()
    {
        return $this->gerenciaGafFinanzas;
    }

    /**
     * @param integer $gerenciaGafFinanzas
     *
     * @return self
     */
    public function setGerenciaGafFinanzas($gerenciaGafFinanzas)
    {
        $this->gerenciaGafFinanzas = $gerenciaGafFinanzas;

        return $this;
    }

    /**
     * @return integer
     */
    public function getGerenciaGafImpuestos()
    {
        return $this->gerenciaGafImpuestos;
    }

    /**
     * @param integer $gerenciaGafImpuestos
     *
     * @return self
     */
    public function setGerenciaGafImpuestos($gerenciaGafImpuestos)
    {
        $this->gerenciaGafImpuestos = $gerenciaGafImpuestos;

        return $this;
    }
    public function getEstadoEvaluacion()
    {
        return $this->estadoEvaluacion;
    }

    public function getMotivoRechazo()
    {
        return $this->MotivoRechazo;
    }

    public function setEstadoEvaluacion($estadoEvaluacion)
    {
        $this->estadoEvaluacion = $estadoEvaluacion;
    }

    public function setMotivoRechazo($MotivoRechazo)
    {
        $this->MotivoRechazo = $MotivoRechazo;
    }

    public function getMotivoRechazoInterno()
    {
        return $this->MotivoRechazoInterno;
    }

    public function setMotivoRechazoInterno($MotivoRechazoInterno)
    {
        $this->MotivoRechazoInterno = $MotivoRechazoInterno;
    }

    /**
     * @return mixed
     */
    public function getProveedorEvaluacionLog()
    {
        return $this->proveedorEvaluacionLog;
    }

    /**
     * @param mixed $proveedorEvaluacionLog
     *
     * @return self
     */
    public function setProveedorEvaluacionLog($proveedorEvaluacionLog)
    {
        $this->proveedorEvaluacionLog = $proveedorEvaluacionLog;

        return $this;
    }

    /**
     * @return string
     */
    public function getMotivoRechazoGalo()
    {
        return $this->MotivoRechazoGalo;
    }

    /**
     * @param string $MotivoRechazoGalo
     */
    public function setMotivoRechazoGalo($MotivoRechazoGalo)
    {
        $this->MotivoRechazoGalo = $MotivoRechazoGalo;
    }

    /**
     * @return string
     */
    public function getMotivoRechazoInternoGalo()
    {
        return $this->MotivoRechazoInternoGalo;
    }

    /**
     * @param string $MotivoRechazoInternoGalo
     */
    public function setMotivoRechazoInternoGalo($MotivoRechazoInternoGalo)
    {
        $this->MotivoRechazoInternoGalo = $MotivoRechazoInternoGalo;
    }

    /**
     * @return string
     */
    public function getMotivoRechazoGafFinanzas()
    {
        return $this->MotivoRechazoGafFinanzas;
    }

    /**
     * @param string $MotivoRechazoGafFinanzas
     */
    public function setMotivoRechazoGafFinanzas($MotivoRechazoGafFinanzas)
    {
        $this->MotivoRechazoGafFinanzas = $MotivoRechazoGafFinanzas;
    }

    /**
     * @return string
     */
    public function getMotivoRechazoInternoGafFinanzas()
    {
        return $this->MotivoRechazoInternoGafFinanzas;
    }

    /**
     * @param string $MotivoRechazoInternoGafFinanzas
     */
    public function setMotivoRechazoInternoGafFinanzas($MotivoRechazoInternoGafFinanzas)
    {
        $this->MotivoRechazoInternoGafFinanzas = $MotivoRechazoInternoGafFinanzas;
    }

    /**
     * @return string
     */
    public function getMotivoRechazoGafImpuestos()
    {
        return $this->MotivoRechazoGafImpuestos;
    }

    /**
     * @param string $MotivoRechazoGafImpuestos
     */
    public function setMotivoRechazoGafImpuestos($MotivoRechazoGafImpuestos)
    {
        $this->MotivoRechazoGafImpuestos = $MotivoRechazoGafImpuestos;
    }

    /**
     * @return string
     */
    public function getMotivoRechazoInternoGafImpuestos()
    {
        return $this->MotivoRechazoInternoGafImpuestos;
    }

    /**
     * @param string $MotivoRechazoInternoGafImpuestos
     */
    public function setMotivoRechazoInternoGafImpuestos($MotivoRechazoInternoGafImpuestos)
    {
        $this->MotivoRechazoInternoGafImpuestos = $MotivoRechazoInternoGafImpuestos;
    }

    /**
     * @return string
     */
    public function getMotivoRechazoGcshm()
    {
        return $this->MotivoRechazoGcshm;
    }

    /**
     * @param string $MotivoRechazoGcshm
     */
    public function setMotivoRechazoGcshm($MotivoRechazoGcshm)
    {
        $this->MotivoRechazoGcshm = $MotivoRechazoGcshm;
    }

    /**
     * @return string
     */
    public function getMotivoRechazoInternoGcshm()
    {
        return $this->MotivoRechazoInternoGcshm;
    }

    /**
     * @param string $MotivoRechazoInternoGcshm
     */
    public function setMotivoRechazoInternoGcshm($MotivoRechazoInternoGcshm)
    {
        $this->MotivoRechazoInternoGcshm = $MotivoRechazoInternoGcshm;
    }


}
