<?php

namespace ADIF\PortalProveedoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\BaseBundle\Entity\BaseAuditoria;

/**
 * ProveedorActividad
 *
 * @ORM\Table("proveedor_actividad")
 * @ORM\Entity(repositoryClass="ADIF\PortalProveedoresBundle\Repository\ProveedorActividadRepository")
 */
class ProveedorActividad extends BaseAuditoria implements BaseAuditable
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
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="proveedorActividad")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $usuario;

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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="TipoActividad", inversedBy="proveedorActividad")
     * @ORM\JoinColumn(name="id_tipo_actividad", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $tipoActividad;

    /**
     * @var boolean
     *
     * @ORM\Column(name="exportacion_bienes", type="boolean", nullable=true)
     */
    private $exportacionBienes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="prestacion_servicios", type="boolean", nullable=true)
     */
    private $prestacionServicios;

    /**
     * @var string
     *
     * @ORM\Column(name="prestacion_servicio_numero", type="string", nullable=true)
     */
    private $prestacionServicioNumero;

    /**
     * @var string
     *
     * @ORM\Column(name="prestacion_servicio_regimen", type="string", nullable=true)
     */
    private $prestacionServicioRegimen;

    /**
     * @var string
     *
     * @ORM\Column(name="prestacion_servicio_porcentaje_excension", type="string", nullable=true)
     */
    private $prestacionServicioPorcentajeExcension;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="prestacion_servicio_fecha_desde", type="datetime", nullable=true)
     */
    private $prestacionServicioFechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="prestacion_servicio_fecha_hasta", type="datetime", nullable=true)
     */
    private $prestacionServicioFechaHasta;

    /**
     * @var bool
     *
     * @ORM\Column(name="convenio_unilateral", type="boolean", nullable=true)
     */
    private $convenioUnilateral;


    /**
     * @var string
     *
     * @ORM\Column(name="convenio_unilateral_aplicacion_caba", type="string", nullable=true)
     */
    private $convenioUnilateralAplicacionCaba;

    /**
     * @var string
     *
     * @ORM\Column(name="convenio_unilateral_aplicacion_grupo", type="string", nullable=true)
     */
    private $convenioUnilateralGrupo;

    /**
     * @var bool
     *
     * @ORM\Column(name="tipo_prestacion_asistencia_tecnica", type="boolean", nullable=true)
     */
    private $tipoPrestacionAsistenciaTecnica;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_prestacion_otros", type="string", nullable=true)
     */
    private $tipoPrestacionOtros;

    /**
     * @var bool
     *
     * @ORM\Column(name="convenio_tributacion_internacional", type="boolean", nullable=true)
     */
    private $convenioTributacionInternacional;

    /**
     * @var bool
     *
     * @ORM\Column(name="establecimiento_argentina", type="boolean", nullable=true)
     */
    private $establecimientoArgentina;


    /**
     * @var string
     *
     * @ORM\Column(name="articulo_de_convenio_aplicable", type="string", nullable=true)
     */
    private $articuloDeConvenioAplicable;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_baja", type="datetime", nullable=true)
     */
    protected $fechaBaja;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idUsuario
     *
     * @param GYL\UsuarioBundle\Entity\Usuario $idUsuario
     *
     * @return ProveedorActividad
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return GYL\UsuarioBundle\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Set idDatoPersonal
     *
     * @param ProveedorDatoPersonal $idDatoPersonal
     *
     * @return ProveedorActividad
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
     * Set exportacionBienes
     *
     * @param bool $exportacionBienes
     *
     * @return ProveedorActividad
     */
    public function setExportacionBienes($exportacionBienes)
    {
        $this->exportacionBienes = $exportacionBienes;

        return $this;
    }

    /**
     * Get exportacionBienes
     *
     * @return bool
     */
    public function getExportacionBienes()
    {
        return $this->exportacionBienes;
    }

    /**
     * Set PrestacionServicios
     *
     * @param bool $prestacionServicios
     *
     * @return ProveedorActividad
     */
    public function setPrestacionServicios($prestacionServicios)
    {
        $this->prestacionServicios = $prestacionServicios;

        return $this;
    }

    /**
     * Get PrestacionServicios
     *
     * @return bool
     */
    public function getPrestacionServicios()
    {
        return $this->prestacionServicios;
    }

    /**
     * @return string
     */
    public function getPrestacionServicioNumero()
    {
        return $this->prestacionServicioNumero;
    }

    /**
     * @param string $prestacionServicioNumero
     * @return ProveedorActividad
     */
    public function setPrestacionServicioNumero($prestacionServicioNumero)
    {
        $this->prestacionServicioNumero = $prestacionServicioNumero;
        return $this;
    }

    /**
     * @return string
     * @return ProveedorActividad
     */
    public function getPrestacionServicioRegimen()
    {
        return $this->prestacionServicioRegimen;
    }

    /**
     * @param string $prestacionServicioRegimen
     * @return ProveedorActividad
     */
    public function setPrestacionServicioRegimen($prestacionServicioRegimen)
    {
        $this->prestacionServicioRegimen = $prestacionServicioRegimen;
        return $this;
    }

    /**
     * @param string $prestacionServicioPorcentajeExcension
     * @return ProveedorActividad
     */
    public function setPrestacionServicioPorcentajeExcension($prestacionServicioPorcentajeExcension)
    {
        $this->prestacionServicioPorcentajeExcension = $prestacionServicioPorcentajeExcension;
        return $this;
    }

    /**
     * @param \DateTime $prestacionServicioFechaDesde
     * @return ProveedorActividad
     */
    public function setPrestacionServicioFechaDesde($prestacionServicioFechaDesde)
    {
        $this->prestacionServicioFechaDesde = $prestacionServicioFechaDesde;
        return $this;
    }

    /**
     * @param \DateTime $prestacionServicioFechaHasta
     * @return ProveedorActividad
     */
    public function setPrestacionServicioFechaHasta($prestacionServicioFechaHasta)
    {
        $this->prestacionServicioFechaHasta = $prestacionServicioFechaHasta;
        return $this;
    }

    /**
     * @param string $convenioUnilateralAplicacionCaba
     * @return ProveedorActividad
     */
    public function setConvenioUnilateralAplicacionCaba($convenioUnilateralAplicacionCaba)
    {
        $this->convenioUnilateralAplicacionCaba = $convenioUnilateralAplicacionCaba;
        return $this;
    }

    /**
     * @return string
     */
    public function getConvenioUnilateralGrupo()
    {
        return $this->convenioUnilateralGrupo;
    }

    /**
     * @param string $convenioUnilateralGrupo
     * @return ProveedorActividad
     */
    public function setConvenioUnilateralGrupo($convenioUnilateralGrupo)
    {
        $this->convenioUnilateralGrupo = $convenioUnilateralGrupo;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTipoPrestacionAsistenciaTecnica()
    {
        return $this->tipoPrestacionAsistenciaTecnica;
    }

    /**
     * @param bool $tipoPrestacionAsistenciaTecnica
     * @return ProveedorActividad
     */
    public function setTipoPrestacionAsistenciaTecnica($tipoPrestacionAsistenciaTecnica)
    {
        $this->tipoPrestacionAsistenciaTecnica = $tipoPrestacionAsistenciaTecnica;
        return $this;
    }

    /**
     * @return string
     */
    public function getTipoPrestacionOtros()
    {
        return $this->tipoPrestacionOtros;
    }

    /**
     * @param string $tipoPrestacionOtros
     * @return ProveedorActividad
     */
    public function setTipoPrestacionOtros($tipoPrestacionOtros)
    {
        $this->tipoPrestacionOtros = $tipoPrestacionOtros;
        return $this;
    }

    /**
     * @return bool
     */
    public function isConvenioTributacionInternacional()
    {
        return $this->convenioTributacionInternacional;
    }

    /**
     * @param bool $convenioTributacionInternacional
     * @return ProveedorActividad
     */
    public function setConvenioTributacionInternacional($convenioTributacionInternacional)
    {
        $this->convenioTributacionInternacional = $convenioTributacionInternacional;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEstablecimientoArgentina()
    {
        return $this->establecimientoArgentina;
    }

    /**
     * @param bool $establecimientoArgentina
     * @return ProveedorActividad
     */
    public function setEstablecimientoArgentina($establecimientoArgentina)
    {
        $this->establecimientoArgentina = $establecimientoArgentina;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPrestacionServicioFechaDesde()
    {
        return $this->prestacionServicioFechaDesde;
    }

    /**
     * @return \DateTime
     */
    public function getPrestacionServicioFechaHasta()
    {
        return $this->prestacionServicioFechaHasta;
    }

    /**
     * @return bool
     */
    public function isConvenioUnilateral()
    {
        return $this->convenioUnilateral;
    }

    /**
     * @param bool $convenioUnilateral
     * @return ProveedorActividad
     */
    public function setConvenioUnilateral($convenioUnilateral)
    {
        $this->convenioUnilateral = $convenioUnilateral;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrestacionServicioPorcentajeExcension()
    {
        return $this->prestacionServicioPorcentajeExcension;
    }

    /**
     * @return string
     */
    public function getConvenioUnilateralAplicacionCaba()
    {
        return $this->convenioUnilateralAplicacionCaba;
    }

    /**
     * @return string
     */
    public function getArticuloDeConvenioAplicable()
    {
        return $this->articuloDeConvenioAplicable;
    }

    /**
     * @param string $articuloDeConvenioAplicable
     * @return ProveedorActividad
     */
    public function setArticuloDeConvenioAplicable($articuloDeConvenioAplicable)
    {
        $this->articuloDeConvenioAplicable = $articuloDeConvenioAplicable;
        return $this;
    }

    /**
     * @return int
     */
    public function getTipoActividad()
    {
        return $this->tipoActividad;
    }

    /**
     * @param int $tipoActividad
     */
    public function setTipoActividad($tipoActividad)
    {
        $this->tipoActividad = $tipoActividad;
    }
}

