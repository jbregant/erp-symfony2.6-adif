<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DatosImpositivos
 *
 * @ORM\Table(name="datos_impositivos")
 * @ORM\Entity 
 */
class DatosImpositivos extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="id_condicion_iva", type="integer", nullable=true)
     */
    protected $idCondicionIVA;

    /**
     * @var ADIF\ContableBundle\Entity\TipoResponsable
     */
    protected $condicionIVA;

    /**
     * @var boolean
     *
     * @ORM\Column(name="exento_iva", type="boolean", nullable=false)
     */
    protected $exentoIVA;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion_exento_iva", type="string", length=4000, nullable=true)
     * @Assert\Length(
     *      max="4000", 
     *      maxMessage="La observación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $observacionExentoIVA;

    /**
     * @ORM\Column(name="id_condicion_ganancias", type="integer", nullable=true)
     */
    protected $idCondicionGanancias;

    /**
     * @var ADIF\ContableBundle\Entity\TipoResponsable
     */
    protected $condicionGanancias;

    /**
     * @var boolean
     *
     * @ORM\Column(name="exento_ganancias", type="boolean", nullable=false)
     */
    protected $exentoGanancias;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_ingresos_brutos", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número de Ingresos Brutos no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroIngresosBrutos;

    /**
     * @ORM\Column(name="id_condicion_ingresos_brutos", type="integer", nullable=true)
     */
    protected $idCondicionIngresosBrutos;

    /**
     * @var boolean
     *
     * @ORM\Column(name="exento_ingresos_brutos", type="boolean", nullable=false)
     */
    protected $exentoIngresosBrutos;

    /**
     * @var ADIF\ContableBundle\Entity\TipoResponsable
     */
    protected $condicionIngresosBrutos;

    /**
     * @ORM\OneToOne(targetEntity="ConvenioMultilateral", mappedBy="datosImpositivos", cascade={"all"})
     * */
    protected $convenioMultilateralIngresosBrutos;

    /**
     * @ORM\Column(name="id_condicion_suss", type="integer", nullable=true)
     */
    protected $idCondicionSUSS;

    /**
     * @var ADIF\ContableBundle\Entity\TipoResponsable
     */
    protected $condicionSUSS;

    /**
     * @var boolean
     *
     * @ORM\Column(name="exento_suss", type="boolean", nullable=false)
     */
    protected $exentoSUSS;

    /**
     * @var SituacionClienteProveedor
     *
     * @ORM\ManyToOne(targetEntity="SituacionClienteProveedor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_situacion_cliente_proveedor", referencedColumnName="id")
     * })
     */
    protected $situacionClienteProveedor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_ultima_actualizacion_codigo_situacion", type="datetime", nullable=false)
     */
    protected $fechaUltimaActualizacionCodigoSituacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tiene_riesgo_fiscal", type="boolean", nullable=false)
     */
    protected $tieneRiesgoFiscal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_ultima_actualizacion_riesgo_fiscal", type="datetime", nullable=false)
     */
    protected $fechaUltimaActualizacionRiesgoFiscal;

    /**
     * @var boolean
     *
     * @ORM\Column(name="incluye_magnitudes_superadas", type="boolean", nullable=false)
     */
    protected $incluyeMagnitudesSuperadas;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_ultima_actualizacion_magnitudes_superadas", type="datetime", nullable=false)
     */
    protected $fechaUltimaActualizacionIncluyeMagnitudesSuperadas;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tiene_problemas_afip", type="boolean", nullable=false)
     */
    protected $tieneProblemasAFIP;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_ultima_actualizacion_tiene_problemas_afip", type="datetime", nullable=true)
     */
    protected $fechaUltimaActualizacionTieneProblemasAFIP;

    /**
     * Constructor
     */
    public function __construct() {
        $this->exentoIVA = FALSE;
        $this->exentoGanancias = FALSE;
        $this->exentoIngresosBrutos = FALSE;
        $this->exentoSUSS = FALSE;
        $this->fechaUltimaActualizacionCodigoSituacion = new \DateTime();
        $this->tieneRiesgoFiscal = FALSE;
        $this->fechaUltimaActualizacionRiesgoFiscal = new \DateTime();
        $this->incluyeMagnitudesSuperadas = FALSE;
        $this->fechaUltimaActualizacionIncluyeMagnitudesSuperadas = new \DateTime();
        $this->tieneProblemasAFIP = FALSE;
        $this->fechaUltimaActualizacionTieneProblemasAFIP = new \DateTime();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return "";
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
     * Set idCondicionIVA
     *
     * @param integer $idCondicionIVA
     * @return ClienteProveedor
     */
    public function setIdCondicionIVA($idCondicionIVA) {
        $this->idCondicionIVA = $idCondicionIVA;

        return $this;
    }

    /**
     * Set exentoIVA
     *
     * @param boolean $exentoIVA
     * @return ClienteProveedor
     */
    public function setExentoIVA($exentoIVA) {
        $this->exentoIVA = $exentoIVA;

        return $this;
    }

    /**
     * Get exentoIVA
     *
     * @return boolean 
     */
    public function getExentoIVA() {
        return $this->exentoIVA;
    }

    /**
     * Set observacionExentoIVA
     *
     * @param string $observacionExentoIVA
     * @return DatosImpositivos
     */
    public function setObservacionExentoIVA($observacionExentoIVA) {
        $this->observacionExentoIVA = $observacionExentoIVA;

        return $this;
    }

    /**
     * Get observacionExentoIVA
     *
     * @return string 
     */
    public function getObservacionExentoIVA() {
        return $this->observacionExentoIVA;
    }

    /**
     * Set idCondicionGanancias
     *
     * @param integer $idCondicionGanancias
     * @return ClienteProveedor
     */
    public function setIdCondicionGanancias($idCondicionGanancias) {
        $this->idCondicionGanancias = $idCondicionGanancias;

        return $this;
    }

    /**
     * Set exentoGanancias
     *
     * @param boolean $exentoGanancias
     * @return ClienteProveedor
     */
    public function setExentoGanancias($exentoGanancias) {
        $this->exentoGanancias = $exentoGanancias;

        return $this;
    }

    /**
     * Get exentoGanancias
     *
     * @return boolean 
     */
    public function getExentoGanancias() {
        return $this->exentoGanancias;
    }

    /**
     * Set numeroIngresosBrutos
     *
     * @param string $numeroIngresosBrutos
     * @return ClienteProveedor
     */
    public function setNumeroIngresosBrutos($numeroIngresosBrutos) {
        $this->numeroIngresosBrutos = $numeroIngresosBrutos;

        return $this;
    }

    /**
     * Get numeroIngresosBrutos
     *
     * @return string 
     */
    public function getNumeroIngresosBrutos() {
        return $this->numeroIngresosBrutos;
    }

    /**
     * Set idCondicionIngresosBrutos
     *
     * @param integer $idCondicionIngresosBrutos
     * @return ClienteProveedor
     */
    public function setIdCondicionIngresosBrutos($idCondicionIngresosBrutos) {
        $this->idCondicionIngresosBrutos = $idCondicionIngresosBrutos;

        return $this;
    }

    /**
     * Set exentoIngresosBrutos
     *
     * @param boolean $exentoIngresosBrutos
     * @return ClienteProveedor
     */
    public function setExentoIngresosBrutos($exentoIngresosBrutos) {
        $this->exentoIngresosBrutos = $exentoIngresosBrutos;

        return $this;
    }

    /**
     * Get exentoIngresosBrutos
     *
     * @return boolean 
     */
    public function getExentoIngresosBrutos() {
        return $this->exentoIngresosBrutos;
    }

    /**
     * Set idCondicionSUSS
     *
     * @param integer $idCondicionSUSS
     * @return ClienteProveedor
     */
    public function setIdCondicionSUSS($idCondicionSUSS) {
        $this->idCondicionSUSS = $idCondicionSUSS;

        return $this;
    }

    /**
     * Set exentoSUSS
     *
     * @param boolean $exentoSUSS
     * @return ClienteProveedor
     */
    public function setExentoSUSS($exentoSUSS) {
        $this->exentoSUSS = $exentoSUSS;

        return $this;
    }

    /**
     * Get exentoSUSS
     *
     * @return boolean 
     */
    public function getExentoSUSS() {
        return $this->exentoSUSS;
    }

    /**
     * Set convenioMultilateralIngresosBrutos
     *
     * @param \ADIF\ComprasBundle\Entity\ConvenioMultilateral $convenioMultilateralIngresosBrutos
     * @return ClienteProveedor
     */
    public function setConvenioMultilateralIngresosBrutos(\ADIF\ComprasBundle\Entity\ConvenioMultilateral $convenioMultilateralIngresosBrutos = null) {
        $this->convenioMultilateralIngresosBrutos = $convenioMultilateralIngresosBrutos;

        return $this;
    }

    /**
     * Get convenioMultilateralIngresosBrutos
     *
     * @return \ADIF\ComprasBundle\Entity\ConvenioMultilateral 
     */
    public function getConvenioMultilateralIngresosBrutos() {
        return $this->convenioMultilateralIngresosBrutos;
    }

// CONDICION IVA

    /**
     * 
     * @return type
     */
    public function getIdCondicionIVA() {
        return $this->idCondicionIVA;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoResponsable $tipoResponsable
     */
    public function setCondicionIVA($tipoResponsable) {
        if (null != $tipoResponsable) {
            $this->idCondicionIVA = $tipoResponsable->getId();
        } else {
            $this->idCondicionIVA = null;
        }

        $this->condicionIVA = $tipoResponsable;
    }

    /**
     * 
     * @return type
     */
    public function getCondicionIVA() {
        return $this->condicionIVA;
    }

// CONDICION GANANCIAS

    /**
     * 
     * @return type
     */
    public function getIdCondicionGanancias() {
        return $this->idCondicionGanancias;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoResponsable $tipoResponsable
     */
    public function setCondicionGanancias($tipoResponsable) {
        if (null != $tipoResponsable) {
            $this->idCondicionGanancias = $tipoResponsable->getId();
        } else {
            $this->idCondicionGanancias = null;
        }

        $this->condicionGanancias = $tipoResponsable;
    }

    /**
     * 
     * @return type
     */
    public function getCondicionGanancias() {
        return $this->condicionGanancias;
    }

// CONDICION INGRESOS BRUTOS

    /**
     * 
     * @return type
     */
    public function getIdCondicionIngresosBrutos() {
        return $this->idCondicionIngresosBrutos;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoResponsable $tipoResponsable
     */
    public function setCondicionIngresosBrutos($tipoResponsable) {
        if (null != $tipoResponsable) {
            $this->idCondicionIngresosBrutos = $tipoResponsable->getId();
        } else {
            $this->idCondicionIngresosBrutos = null;
        }

        $this->condicionIngresosBrutos = $tipoResponsable;
    }

    /**
     * 
     * @return type
     */
    public function getCondicionIngresosBrutos() {
        return $this->condicionIngresosBrutos;
    }

// CONDICION SUSS

    /**
     * Get idCondicionSUSS
     *
     * @return integer 
     */
    public function getIdCondicionSUSS() {
        return $this->idCondicionSUSS;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoResponsable $tipoResponsable
     */
    public function setCondicionSUSS($tipoResponsable) {
        if (null != $tipoResponsable) {
            $this->idCondicionSUSS = $tipoResponsable->getId();
        } else {
            $this->idCondicionSUSS = null;
        }

        $this->condicionSUSS = $tipoResponsable;
    }

    /**
     * 
     * @return type
     */
    public function getCondicionSUSS() {
        return $this->condicionSUSS;
    }

    /**
     * Set situacionClienteProveedor
     *
     * @param integer $situacionClienteProveedor
     * @return ClienteProveedor
     */
    public function setSituacionClienteProveedor($situacionClienteProveedor) {
        $this->situacionClienteProveedor = $situacionClienteProveedor;

        $this->fechaUltimaActualizacionCodigoSituacion = new \DateTime();

        return $this;
    }

    /**
     * Get situacionClienteProveedor
     *
     * @return integer 
     */
    public function getSituacionClienteProveedor() {
        return $this->situacionClienteProveedor;
    }

    /**
     * Set fechaUltimaActualizacionCodigoSituacion
     *
     * @param \DateTime $fechaUltimaActualizacionCodigoSituacion
     * @return ClienteProveedor
     */
    public function setFechaUltimaActualizacionCodigoSituacion($fechaUltimaActualizacionCodigoSituacion) {
        $this->fechaUltimaActualizacionCodigoSituacion = $fechaUltimaActualizacionCodigoSituacion;

        return $this;
    }

    /**
     * Get fechaUltimaActualizacionCodigoSituacion
     *
     * @return \DateTime 
     */
    public function getFechaUltimaActualizacionCodigoSituacion() {
        return $this->fechaUltimaActualizacionCodigoSituacion;
    }

    /**
     * Set tieneRiesgoFiscal
     *
     * @param boolean $tieneRiesgoFiscal
     * @return ClienteProveedor
     */
    public function setTieneRiesgoFiscal($tieneRiesgoFiscal) {
        $this->tieneRiesgoFiscal = $tieneRiesgoFiscal;

        $this->fechaUltimaActualizacionRiesgoFiscal = new \DateTime();

        return $this;
    }

    /**
     * Get tieneRiesgoFiscal
     *
     * @return boolean 
     */
    public function getTieneRiesgoFiscal() {
        return $this->tieneRiesgoFiscal;
    }

    /**
     * Set fechaUltimaActualizacionRiesgoFiscal
     *
     * @param \DateTime $fechaUltimaActualizacionRiesgoFiscal
     * @return ClienteProveedor
     */
    public function setFechaUltimaActualizacionRiesgoFiscal($fechaUltimaActualizacionRiesgoFiscal) {
        $this->fechaUltimaActualizacionRiesgoFiscal = $fechaUltimaActualizacionRiesgoFiscal;

        return $this;
    }

    /**
     * Get fechaUltimaActualizacionRiesgoFiscal
     *
     * @return \DateTime 
     */
    public function getFechaUltimaActualizacionRiesgoFiscal() {
        return $this->fechaUltimaActualizacionRiesgoFiscal;
    }

    /**
     * Set incluyeMagnitudesSuperadas
     *
     * @param boolean $incluyeMagnitudesSuperadas
     * @return ClienteProveedor
     */
    public function setIncluyeMagnitudesSuperadas($incluyeMagnitudesSuperadas) {
        $this->incluyeMagnitudesSuperadas = $incluyeMagnitudesSuperadas;

        $this->fechaUltimaActualizacionIncluyeMagnitudesSuperadas = new \DateTime();

        return $this;
    }

    /**
     * Get incluyeMagnitudesSuperadas
     *
     * @return boolean 
     */
    public function getIncluyeMagnitudesSuperadas() {
        return $this->incluyeMagnitudesSuperadas;
    }

    /**
     * Set fechaUltimaActualizacionIncluyeMagnitudesSuperadas
     *
     * @param \DateTime $fechaUltimaActualizacionIncluyeMagnitudesSuperadas
     * @return ClienteProveedor
     */
    public function setFechaUltimaActualizacionIncluyeMagnitudesSuperadas($fechaUltimaActualizacionIncluyeMagnitudesSuperadas) {
        $this->fechaUltimaActualizacionIncluyeMagnitudesSuperadas = $fechaUltimaActualizacionIncluyeMagnitudesSuperadas;

        return $this;
    }

    /**
     * Get fechaUltimaActualizacionIncluyeMagnitudesSuperadas
     *
     * @return \DateTime 
     */
    public function getFechaUltimaActualizacionIncluyeMagnitudesSuperadas() {
        return $this->fechaUltimaActualizacionIncluyeMagnitudesSuperadas;
    }

    /**
     * Set tieneProblemasAFIP
     *
     * @param boolean $tieneProblemasAFIP
     * @return ClienteProveedor
     */
    public function setTieneProblemasAFIP($tieneProblemasAFIP) {
        $this->tieneProblemasAFIP = $tieneProblemasAFIP;

        $this->fechaUltimaActualizacionTieneProblemasAFIP = new \DateTime();

        return $this;
    }

    /**
     * Get tieneProblemasAFIP
     *
     * @return boolean 
     */
    public function getTieneProblemasAFIP() {
        return $this->tieneProblemasAFIP;
    }

    /**
     * Set fechaUltimaActualizacionTieneProblemasAFIP
     *
     * @param \DateTime $fechaUltimaActualizacionTieneProblemasAFIP
     * @return ClienteProveedor
     */
    public function setFechaUltimaActualizacionTieneProblemasAFIP($fechaUltimaActualizacionTieneProblemasAFIP) {
        $this->fechaUltimaActualizacionTieneProblemasAFIP = $fechaUltimaActualizacionTieneProblemasAFIP;

        return $this;
    }

    /**
     * Get fechaUltimaActualizacionTieneProblemasAFIP
     *
     * @return \DateTime 
     */
    public function getFechaUltimaActualizacionTieneProblemasAFIP() {
        return $this->fechaUltimaActualizacionTieneProblemasAFIP;
    }

}
