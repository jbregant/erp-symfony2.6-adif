<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteAlicuotaIva;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ClienteProveedor
 *
 * @ORM\Table(name="cliente_proveedor")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\ClienteProveedorRepository")
 */
class ClienteProveedor extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="codigo", type="string", length=50, nullable=false)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El código no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La razón social no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El CUIT no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $CUIT;

    /**
     * @var string
     *
     * @ORM\Column(name="dni", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El DNI no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $DNI;

    /**
     * @ORM\ManyToMany(targetEntity="TipoActividad", inversedBy="clienteProveedor")
     * @ORM\JoinTable(name="cliente_proveedor_tipo_actividad",
     *      joinColumns={@ORM\JoinColumn(name="id_cliente_proveedor", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_tipo_actividad", referencedColumnName="id")}
     *      )
     */
    protected $actividades;

    /**
     * @ORM\ManyToMany(targetEntity="DatoContacto", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="cliente_proveedor_dato_contacto",
     *      joinColumns={@ORM\JoinColumn(name="id_cliente_proveedor", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_dato_contacto", referencedColumnName="id")}
     *      )
     */
    protected $datosContacto;

    /**
     * @ORM\Column(name="id_domicilio_comercial", type="integer", nullable=true)
     */
    protected $idDomicilioComercial;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Domicilio
     */
    protected $domicilioComercial;

    /**
     * @ORM\Column(name="id_domicilio_legal", type="integer", nullable=true)
     */
    protected $idDomicilioLegal;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Domicilio
     */
    protected $domicilioLegal;

    /**
     * @var DatosImpositivos
     *
     * @ORM\ManyToOne(targetEntity="DatosImpositivos", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_datos_impositivos", referencedColumnName="id", nullable=false)
     * })
     */
    protected $datosImpositivos;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_extranjero", type="boolean", nullable=false)
     */
    protected $esExtranjero;

    /**
     * Se utiliza en el caso de que el proveedor sea extranjero y no tenga CUIT.
     * 
     * @var string
     *
     * @ORM\Column(name="codigo_identificacion", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El código no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $codigoIdentificacion;

    /**
     * @ORM\OneToMany(targetEntity="ClienteProveedorArchivo", mappedBy="clienteProveedor", cascade={"persist","remove"})
     */
    protected $archivos;
	
	/**
     * @ORM\OneToMany(targetEntity="Proveedor", mappedBy="clienteProveedor")
     */
    protected $proveedores;
	
	
	const SITUACION_CLIENTE_PROVEEDOR_CODIGO_APLICO_RG_NORMALMENTE = 1;

    /**
     * Constructor
     */
    public function __construct() {
        $this->datosContacto = new ArrayCollection();
        $this->esExtranjero = FALSE;
        $this->archivos = new ArrayCollection();
        $this->datosImpositivos = new DatosImpositivos();
		$this->proveedores = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->razonSocial;
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
     * Set codigo
     *
     * @param string $codigo
     * @return ClienteProveedor
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo() {
        return str_pad($this->codigo, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     * @return ClienteProveedor
     */
    public function setRazonSocial($razonSocial) {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return string 
     */
    public function getRazonSocial() {
        return $this->razonSocial;
    }

    /**
     * Set CUIT
     *
     * @param string $cUIT
     * @return ClienteProveedor
     */
    public function setCUIT($cUIT) {
        $this->CUIT = $cUIT;

        return $this;
    }

    /**
     * Get CUIT
     *
     * @return string 
     */
    public function getCUIT() {
        return $this->CUIT;
    }

    /**
     * Set DNI
     *
     * @param string $dNI
     * @return ClienteProveedor
     */
    public function setDNI($dNI) {
        $this->DNI = $dNI;

        return $this;
    }

    /**
     * Get DNI
     *
     * @return string 
     */
    public function getDNI() {
        return $this->DNI;
    }

    /**
     * Set datosImpositivos
     *
     * @param DatosImpositivos $datosImpositivos
     * @return ClienteProveedor
     */
    public function setDatosImpositivos(DatosImpositivos $datosImpositivos = null) {
        $this->datosImpositivos = $datosImpositivos;

        return $this;
    }

    /**
     * Get datosImpositivos
     *
     * @return DatosImpositivos 
     */
    public function getDatosImpositivos() {
        return $this->datosImpositivos;
    }

    /**
     * Add actividades
     *
     * @param \ADIF\ComprasBundle\Entity\TipoActividad $actividades
     * @return ClienteProveedor
     */
    public function addActividad(\ADIF\ComprasBundle\Entity\TipoActividad $actividades) {
        $this->actividades[] = $actividades;

        return $this;
    }

    /**
     * Remove actividades
     *
     * @param \ADIF\ComprasBundle\Entity\TipoActividad $actividades
     */
    public function removeActividad(\ADIF\ComprasBundle\Entity\TipoActividad $actividades) {
        $this->actividades->removeElement($actividades);
    }

    /**
     * Get actividades
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActividades() {
        return $this->actividades;
    }

    /**
     * Add datosContacto
     *
     * @param \ADIF\ComprasBundle\Entity\DatoContacto $datosContacto
     * @return ClienteProveedor
     */
    public function addDatosContacto(\ADIF\ComprasBundle\Entity\DatoContacto $datosContacto) {
        $this->datosContacto[] = $datosContacto;

        return $this;
    }

    /**
     * Remove datosContacto
     *
     * @param \ADIF\ComprasBundle\Entity\DatoContacto $datosContacto
     */
    public function removeDatosContacto(\ADIF\ComprasBundle\Entity\DatoContacto $datosContacto) {
        $this->datosContacto->removeElement($datosContacto);
    }

    /**
     * Get datosContacto
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDatosContacto() {
        return $this->datosContacto;
    }

// DOMICILIO COMERCIAL    

    /**
     * 
     * @param type $idDomicilioComercial
     * @return \ADIF\ComprasBundle\Entity\ClienteProveedor
     */
    public function setIdDomicilioComercial($idDomicilioComercial) {
        $this->idDomicilioComercial = $idDomicilioComercial;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdDomicilioComercial() {
        return $this->idDomicilioComercial;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Domicilio $domicilioComercial
     */
    public function setDomicilioComercial($domicilioComercial) {
        if (null != $domicilioComercial) {
            $this->idDomicilioComercial = $domicilioComercial->getId();
        } else {
            $this->idDomicilioComercial = null;
        }

        $this->domicilioComercial = $domicilioComercial;
    }

    /**
     * 
     * @return type
     */
    public function getDomicilioComercial() {
        return $this->domicilioComercial;
    }

// DOMICILIO LEGAL  

    /**
     * 
     * @param type $idDomicilioLegal
     * @return \ADIF\ComprasBundle\Entity\ClienteProveedor
     */
    public function setIdDomicilioLegal($idDomicilioLegal) {
        $this->idDomicilioLegal = $idDomicilioLegal;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdDomicilioLegal() {
        return $this->idDomicilioLegal;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Domicilio $domicilioLegal
     */
    public function setDomicilioLegal($domicilioLegal) {
        if (null != $domicilioLegal) {
            $this->idDomicilioLegal = $domicilioLegal->getId();
        } else {
            $this->idDomicilioLegal = null;
        }

        $this->domicilioLegal = $domicilioLegal;
    }

    /**
     * 
     * @return type
     */
    public function getDomicilioLegal() {
        return $this->domicilioLegal;
    }

    /**
     * Set esExtranjero
     *
     * @param boolean $esExtranjero
     * @return ClienteProveedor
     */
    public function setEsExtranjero($esExtranjero) {
        $this->esExtranjero = $esExtranjero;

        return $this;
    }

    /**
     * Get esExtranjero
     *
     * @return boolean 
     */
    public function getEsExtranjero() {
        return $this->esExtranjero;
    }

    /**
     * Set codigoIdentificacion
     *
     * @param string $codigoIdentificacion
     * @return ClienteProveedor
     */
    public function setCodigoIdentificacion($codigoIdentificacion) {
        $this->codigoIdentificacion = $codigoIdentificacion;

        return $this;
    }

    /**
     * Get codigoIdentificacion
     *
     * @return string 
     */
    public function getCodigoIdentificacion() {
        return $this->codigoIdentificacion;
    }

    /**
     * Add archivo
     *
     * @param ClienteProveedorArchivo $archivo
     * @return ClienteProveedor
     */
    public function addArchivo(ClienteProveedorArchivo $archivo) {
        $this->archivos[] = $archivo;

        return $this;
    }

    /**
     * Remove archivo
     *
     * @param ClienteProveedorArchivo $archivo
     */
    public function removeArchivo(ClienteProveedorArchivo $archivo) {
        $this->archivos->removeElement($archivo);
    }

    /**
     * Get archivos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArchivos() {
        return $this->archivos;
    }

    /**
     * 
     * @return type
     */
    public function getAlicuotaIva() {
        $alicuota = ConstanteAlicuotaIva::ALICUOTA_21;
        // Si el cliente tiene problemas en AFIP
        if ($this->getTieneProblemasAFIP()) {
            // Le cargo el 10.5% + el 21%
            $alicuota = ConstanteAlicuotaIva::ALICUOTA_31_5;
        }
        return $alicuota;
    }

    // Redefinicion de getters y setters de datos impositivos

    /**
     * Set idCondicionIVA
     *
     * @param integer $idCondicionIVA
     * @return ClienteProveedor
     */
    public function setIdCondicionIVA($idCondicionIVA) {
        $this->getDatosImpositivos()->setIdCondicionIVA($idCondicionIVA);

        return $this;
    }

    /**
     * Set exentoIVA
     *
     * @param boolean $exentoIVA
     * @return ClienteProveedor
     */
    public function setExentoIVA($exentoIVA) {
        $this->getDatosImpositivos()->setExentoIVA($exentoIVA);

        return $this;
    }

    /**
     * Get exentoIVA
     *
     * @return boolean 
     */
    public function getExentoIVA() {
        return $this->getDatosImpositivos()->getExentoIVA();
    }

    /**
     * Set idCondicionGanancias
     *
     * @param integer $idCondicionGanancias
     * @return ClienteProveedor
     */
    public function setIdCondicionGanancias($idCondicionGanancias) {
        $this->getDatosImpositivos()->setIdCondicionGanancias($idCondicionGanancias);

        return $this;
    }

    /**
     * Set exentoGanancias
     *
     * @param boolean $exentoGanancias
     * @return ClienteProveedor
     */
    public function setExentoGanancias($exentoGanancias) {
        $this->getDatosImpositivos()->setExentoGanancias($exentoGanancias);

        return $this;
    }

    /**
     * Get exentoGanancias
     *
     * @return boolean 
     */
    public function getExentoGanancias() {
        return $this->getDatosImpositivos()->getExentoGanancias();
    }

    /**
     * Set numeroIngresosBrutos
     *
     * @param string $numeroIngresosBrutos
     * @return ClienteProveedor
     */
    public function setNumeroIngresosBrutos($numeroIngresosBrutos) {
        $this->getDatosImpositivos()->setNumeroIngresosBrutos($numeroIngresosBrutos);

        return $this;
    }

    /**
     * Get numeroIngresosBrutos
     *
     * @return string 
     */
    public function getNumeroIngresosBrutos() {
        return $this->getDatosImpositivos()->getNumeroIngresosBrutos();
    }

    /**
     * Set idCondicionIngresosBrutos
     *
     * @param integer $idCondicionIngresosBrutos
     * @return ClienteProveedor
     */
    public function setIdCondicionIngresosBrutos($idCondicionIngresosBrutos) {
        $this->getDatosImpositivos()->setIdCondicionIngresosBrutos($idCondicionIngresosBrutos);

        return $this;
    }

    /**
     * Set exentoIngresosBrutos
     *
     * @param boolean $exentoIngresosBrutos
     * @return ClienteProveedor
     */
    public function setExentoIngresosBrutos($exentoIngresosBrutos) {
        $this->getDatosImpositivos()->setExentoIngresosBrutos($exentoIngresosBrutos);

        return $this;
    }

    /**
     * Get exentoIngresosBrutos
     *
     * @return boolean 
     */
    public function getExentoIngresosBrutos() {
        return $this->getDatosImpositivos()->getExentoIngresosBrutos();
    }

    /**
     * Set idCondicionSUSS
     *
     * @param integer $idCondicionSUSS
     * @return ClienteProveedor
     */
    public function setIdCondicionSUSS($idCondicionSUSS) {
        $this->getDatosImpositivos()->setIdCondicionSUSS($idCondicionSUSS);

        return $this;
    }

    /**
     * Set exentoSUSS
     *
     * @param boolean $exentoSUSS
     * @return ClienteProveedor
     */
    public function setExentoSUSS($exentoSUSS) {
        $this->getDatosImpositivos()->setExentoSUSS($exentoSUSS);

        return $this;
    }

    /**
     * Get exentoSUSS
     *
     * @return boolean 
     */
    public function getExentoSUSS() {
        return $this->getDatosImpositivos()->getExentoSUSS();
    }

    /**
     * Set convenioMultilateralIngresosBrutos
     *
     * @param \ADIF\ComprasBundle\Entity\ConvenioMultilateral $convenioMultilateralIngresosBrutos
     * @return ClienteProveedor
     */
    public function setConvenioMultilateralIngresosBrutos(\ADIF\ComprasBundle\Entity\ConvenioMultilateral $convenioMultilateralIngresosBrutos = null) {
        $this->getDatosImpositivos()->setConvenioMultilateralIngresosBrutos($convenioMultilateralIngresosBrutos);

        return $this;
    }

    /**
     * Get convenioMultilateralIngresosBrutos
     *
     * @return \ADIF\ComprasBundle\Entity\ConvenioMultilateral 
     */
    public function getConvenioMultilateralIngresosBrutos() {
        return $this->getDatosImpositivos()->getConvenioMultilateralIngresosBrutos();
    }

// CONDICION IVA

    /**
     * 
     * @return type
     */
    public function getIdCondicionIVA() {
        return $this->getDatosImpositivos()->getIdCondicionIVA();
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoResponsable $tipoResponsable
     */
    public function setCondicionIVA($tipoResponsable) {
        if (null != $tipoResponsable) {
            $this->getDatosImpositivos()->setIdCondicionIVA($tipoResponsable->getId());
        } else {
            $this->getDatosImpositivos()->setIdCondicionIVA(null);
        }

        $this->getDatosImpositivos()->setCondicionIVA($tipoResponsable);
    }

    /**
     * 
     * @return type
     */
    public function getCondicionIVA() {
        return $this->getDatosImpositivos()->getCondicionIVA();
    }

// CONDICION GANANCIAS

    /**
     * 
     * @return type
     */
    public function getIdCondicionGanancias() {
        return $this->getDatosImpositivos()->setIdCondicionGanancias();
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoResponsable $tipoResponsable
     */
    public function setCondicionGanancias($tipoResponsable) {
        if (null != $tipoResponsable) {
            $this->getDatosImpositivos()->setIdCondicionGanancias($tipoResponsable->getId());
        } else {
            $this->getDatosImpositivos()->setIdCondicionGanancias(null);
        }

        $this->getDatosImpositivos()->setCondicionGanancias($tipoResponsable);
    }

    /**
     * 
     * @return type
     */
    public function getCondicionGanancias() {
        return $this->getDatosImpositivos()->getCondicionGanancias();
    }

// CONDICION INGRESOS BRUTOS

    /**
     * 
     * @return type
     */
    public function getIdCondicionIngresosBrutos() {
        return $this->getDatosImpositivos()->getIdCondicionIngresosBrutos();
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoResponsable $tipoResponsable
     */
    public function setCondicionIngresosBrutos($tipoResponsable) {
        if (null != $tipoResponsable) {
            $this->getDatosImpositivos()->setIdCondicionIngresosBrutos($tipoResponsable->getId());
        } else {
            $this->getDatosImpositivos()->setIdCondicionIngresosBrutos(null);
        }

        $this->getDatosImpositivos()->setCondicionIngresosBrutos($tipoResponsable);
    }

    /**
     * 
     * @return type
     */
    public function getCondicionIngresosBrutos() {
        return $this->getDatosImpositivos()->getCondicionIngresosBrutos();
    }

// CONDICION SUSS

    /**
     * Get idCondicionSUSS
     *
     * @return integer 
     */
    public function getIdCondicionSUSS() {
        return $this->getDatosImpositivos()->getIdCondicionSUSS();
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoResponsable $tipoResponsable
     */
    public function setCondicionSUSS($tipoResponsable) {
        if (null != $tipoResponsable) {
            $this->getDatosImpositivos()->setIdCondicionSUSS($tipoResponsable->getId());
        } else {
            $this->getDatosImpositivos()->setIdCondicionSUSS(null);
        }

        $this->getDatosImpositivos()->setCondicionSUSS($tipoResponsable);
    }

    /**
     * 
     * @return type
     */
    public function getCondicionSUSS() {
        return $this->getDatosImpositivos()->getCondicionSUSS();
    }

    /**
     * Set situacionClienteProveedor
     *
     * @param integer $situacionClienteProveedor
     * @return ClienteProveedor
     */
    public function setSituacionClienteProveedor($situacionClienteProveedor) {
        $this->getDatosImpositivos()->setSituacionClienteProveedor($situacionClienteProveedor);

        $this->getDatosImpositivos()->setFechaUltimaActualizacionCodigoSituacion(new \DateTime());

        return $this;
    }

    /**
     * Get situacionClienteProveedor
     *
     * @return integer 
     */
    public function getSituacionClienteProveedor() {
        return $this->getDatosImpositivos()->getSituacionClienteProveedor();
    }

    /**
     * Set fechaUltimaActualizacionCodigoSituacion
     *
     * @param \DateTime $fechaUltimaActualizacionCodigoSituacion
     * @return ClienteProveedor
     */
    public function setFechaUltimaActualizacionCodigoSituacion($fechaUltimaActualizacionCodigoSituacion) {
        $this->getDatosImpositivos()->setFechaUltimaActualizacionCodigoSituacion($fechaUltimaActualizacionCodigoSituacion);

        return $this;
    }

    /**
     * Get fechaUltimaActualizacionCodigoSituacion
     *
     * @return \DateTime 
     */
    public function getFechaUltimaActualizacionCodigoSituacion() {
        return $this->getDatosImpositivos()->getFechaUltimaActualizacionCodigoSituacion();
    }

    /**
     * Set tieneRiesgoFiscal
     *
     * @param boolean $tieneRiesgoFiscal
     * @return ClienteProveedor
     */
    public function setTieneRiesgoFiscal($tieneRiesgoFiscal) {
        $this->getDatosImpositivos()->setTieneRiesgoFiscal($tieneRiesgoFiscal);

        $this->getDatosImpositivos()->setFechaUltimaActualizacionRiesgoFiscal(new \DateTime());

        return $this;
    }

    /**
     * Get tieneRiesgoFiscal
     *
     * @return boolean 
     */
    public function getTieneRiesgoFiscal() {
        return $this->getDatosImpositivos()->getTieneRiesgoFiscal();
    }

    /**
     * Set fechaUltimaActualizacionRiesgoFiscal
     *
     * @param \DateTime $fechaUltimaActualizacionRiesgoFiscal
     * @return ClienteProveedor
     */
    public function setFechaUltimaActualizacionRiesgoFiscal($fechaUltimaActualizacionRiesgoFiscal) {
        $this->getDatosImpositivos()->setFechaUltimaActualizacionRiesgoFiscal($fechaUltimaActualizacionRiesgoFiscal);

        return $this;
    }

    /**
     * Get fechaUltimaActualizacionRiesgoFiscal
     *
     * @return \DateTime 
     */
    public function getFechaUltimaActualizacionRiesgoFiscal() {
        return $this->getDatosImpositivos()->getFechaUltimaActualizacionRiesgoFiscal();
    }

    /**
     * Set incluyeMagnitudesSuperadas
     *
     * @param boolean $incluyeMagnitudesSuperadas
     * @return ClienteProveedor
     */
    public function setIncluyeMagnitudesSuperadas($incluyeMagnitudesSuperadas) {
        $this->getDatosImpositivos()->setIncluyeMagnitudesSuperadas($incluyeMagnitudesSuperadas);

        $this->getDatosImpositivos()->setFechaUltimaActualizacionIncluyeMagnitudesSuperadas(new \DateTime());

        return $this;
    }

    /**
     * Get incluyeMagnitudesSuperadas
     *
     * @return boolean 
     */
    public function getIncluyeMagnitudesSuperadas() {
        return $this->getDatosImpositivos()->getIncluyeMagnitudesSuperadas();
    }

    /**
     * Set fechaUltimaActualizacionIncluyeMagnitudesSuperadas
     *
     * @param \DateTime $fechaUltimaActualizacionIncluyeMagnitudesSuperadas
     * @return ClienteProveedor
     */
    public function setFechaUltimaActualizacionIncluyeMagnitudesSuperadas($fechaUltimaActualizacionIncluyeMagnitudesSuperadas) {
        $this->getDatosImpositivos()->setFechaUltimaActualizacionIncluyeMagnitudesSuperadas($fechaUltimaActualizacionIncluyeMagnitudesSuperadas);

        return $this;
    }

    /**
     * Get fechaUltimaActualizacionIncluyeMagnitudesSuperadas
     *
     * @return \DateTime 
     */
    public function getFechaUltimaActualizacionIncluyeMagnitudesSuperadas() {
        return $this->getDatosImpositivos()->getFechaUltimaActualizacionIncluyeMagnitudesSuperadas();
    }

    /**
     * Set tieneProblemasAFIP
     *
     * @param boolean $tieneProblemasAFIP
     * @return ClienteProveedor
     */
    public function setTieneProblemasAFIP($tieneProblemasAFIP) {
        $this->getDatosImpositivos()->setTieneProblemasAFIP($tieneProblemasAFIP);

        $this->getDatosImpositivos()->setFechaUltimaActualizacionTieneProblemasAFIP(new \DateTime());

        return $this;
    }

    /**
     * Get tieneProblemasAFIP
     *
     * @return boolean 
     */
    public function getTieneProblemasAFIP() {
        return $this->getDatosImpositivos()->getTieneProblemasAFIP();
    }

    /**
     * Set fechaUltimaActualizacionTieneProblemasAFIP
     *
     * @param \DateTime $fechaUltimaActualizacionTieneProblemasAFIP
     * @return ClienteProveedor
     */
    public function setFechaUltimaActualizacionTieneProblemasAFIP($fechaUltimaActualizacionTieneProblemasAFIP) {
        $this->getDatosImpositivos()->setFechaUltimaActualizacionTieneProblemasAFIP($fechaUltimaActualizacionTieneProblemasAFIP);

        return $this;
    }

    /**
     * Get fechaUltimaActualizacionTieneProblemasAFIP
     *
     * @return \DateTime 
     */
    public function getFechaUltimaActualizacionTieneProblemasAFIP() {
        return $this->getDatosImpositivos()->getFechaUltimaActualizacionTieneProblemasAFIP();
    }

    /**
     * Get CUIT o DNI
     * 
     * @return \DateTime 
     */
    public function getCuitDni() {

        if ($this->CUIT != null) {
            return $this->CUIT;
        } else {
            return $this->DNI;
        }
    }
	
	/**
	* Me devuelve si aplico el regimen normalmente (situacion del cliente/proveedor)
	* @return boolean
	*/
	public function getAplicaRgNormalmente()
	{
		$datosImpositivos = $this->getDatosImpositivos();
		if ($datosImpositivos->getSituacionClienteProveedor() != null) {
			if ($datosImpositivos->getSituacionClienteProveedor()->getCodigo() == self::SITUACION_CLIENTE_PROVEEDOR_CODIGO_APLICO_RG_NORMALMENTE) {
				return true;
			}
		}
		
		return false;
	}
	
	public function getProveedores()
	{
		return $this->proveedores;
	}

}
