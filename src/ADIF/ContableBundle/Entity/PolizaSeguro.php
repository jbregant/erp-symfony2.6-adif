<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\BaseAuditoria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PolizaSeguro
 *
 * @author Manuel Becerra
 * created 26/01/2015
 *
 *
 * @ORM\Table(name="poliza_seguro")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "poliza_seguro" = "PolizaSeguro",
 *      "poliza_seguro_contrato" = "ADIF\ContableBundle\Entity\Facturacion\PolizaSeguroContrato",
 *      "poliza_seguro_obra" = "ADIF\ContableBundle\Entity\Obras\PolizaSeguroObra",
 *      "poliza_seguro_documento_financiero" = "ADIF\ContableBundle\Entity\Obras\PolizaSeguroDocumentoFinanciero"
 * })
 * )
 */
class PolizaSeguro extends BaseAuditoria implements BaseAuditable {

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
     * @ORM\Column(name="numero_poliza", type="string", nullable=false)
     */
    protected $numeroPoliza;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio", type="date", nullable=true)
     */
    protected $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_vencimiento", type="date", nullable=false)
     */
    protected $fechaVencimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="aseguradora", type="string", length=512, nullable=false)
     * @Assert\Length(
     *      max="512",
     *      maxMessage="La aseguradora no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $aseguradora;

    /**
     * @var string
     *
     * @ORM\Column(name="id_aseguradora", type="string", length=512, nullable=false)
     *
     */
    protected $idAseguradora;


    /**
     * @var string
     *
     * @ORM\Column(name="riesgo_cubierto", type="string", length=512, nullable=false)
     * @Assert\Length(
     *      max="512",
     *      maxMessage="El riesgo cubierto no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $riesgoCubierto;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=15, scale=2, nullable=true)
     *
     */
    protected $importe;

    /**
     * @var double
     * @ORM\Column(name="monto_asegurado", type="decimal", precision=15, scale=2, nullable=false)
     *
     */
    protected $montoAsegurado;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_tramite_envio", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512",
     *      maxMessage="El número de trámite de evío no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroTramiteEnvio;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_tramite_poliza_garantia", type="string", length=512, nullable=true)
     * @Assert\Length(
     *      max="512",
     *      maxMessage="El número de trámite de poliza en garantia no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroTramitePolizaGarantia;

	/**
     *
     * @var Aseguradora
     *
     * @ORM\ManyToOne(targetEntity="Aseguradora", inversedBy="polizas")
	 * @ORM\JoinColumn(name="id_aseguradora", referencedColumnName="id", nullable=true)
     */
    protected $aseguradora2;

    /**
     *
     * @var TipoCobertura
     *
     * @ORM\ManyToOne(targetEntity="TipoCobertura", inversedBy="polizas")
     * @ORM\JoinColumn(name="id_tipo_cobertura", referencedColumnName="id", nullable=true)
     */
    protected $tipoCobertura;

	/**
     *
     * @var EstadoPoliza
     *
     * @ORM\ManyToOne(targetEntity="EstadoPoliza", inversedBy="polizas")
	 * @ORM\JoinColumn(name="id_estado", referencedColumnName="id", nullable=true)
     */
    protected $estadoPoliza;

	/**
     *
     * @var EstadoRevisionPoliza
     *
     * @ORM\ManyToOne(targetEntity="EstadoRevisionPoliza", inversedBy="polizas")
	 * @ORM\JoinColumn(name="id_estado_revision", referencedColumnName="id", nullable=true)
     */
    protected $estadoRevisionPoliza;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set numeroPoliza
     *
     * @param string $numeroPoliza
     * @return PolizaSeguro
     */
    public function setNumeroPoliza($numeroPoliza) {
        $this->numeroPoliza = $numeroPoliza;

        return $this;
    }

    /**
     * Get numeroPoliza
     *
     * @return string
     */
    public function getNumeroPoliza() {
        return $this->numeroPoliza;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return PolizaSeguro
     */
    public function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime
     */
    public function getFechaInicio() {
        return $this->fechaInicio;
    }

    /**
     * Set fechaVencimiento
     *
     * @param \DateTime $fechaVencimiento
     * @return PolizaSeguro
     */
    public function setFechaVencimiento($fechaVencimiento) {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    /**
     * Get fechaVencimiento
     *
     * @return \DateTime
     */
    public function getFechaVencimiento() {
        return $this->fechaVencimiento;
    }

    /**
     * Set aseguradora
     *
     * @param string $aseguradora
     * @return PolizaSeguro
     */
    public function setAseguradora($aseguradora) {
        $this->aseguradora = $aseguradora;

        return $this;
    }

    /**
     * Get aseguradora
     *
     * @return string
     */
    public function getAseguradora() {
        return $this->aseguradora;
    }

    /**
     * Set riesgoCubierto
     *
     * @param string $riesgoCubierto
     * @return PolizaSeguro
     */
    public function setRiesgoCubierto($riesgoCubierto) {
        $this->riesgoCubierto = $riesgoCubierto;

        return $this;
    }

    /**
     * Get riesgoCubierto
     *
     * @return string
     */
    public function getRiesgoCubierto() {
        return $this->riesgoCubierto;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return PolizaSeguro
     */
    public function setImporte($importe) {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return string
     */
    public function getImporte() {
        return $this->importe;
    }
    /**
     * Set montoAsegurado
     *
     * @param string $montoAsegurado
     * @return PolizaSeguro
     */
    public function setMontoAsegurado($montoAsegurado) {
        $this->montoAsegurado = $montoAsegurado;

        return $this;
    }

    /**
     * Get montoAsegurado
     *
     * @return string
     */
    public function getMontoAsegurado() {
        return $this->montoAsegurado;
    }

    /**
     * Set numeroTramiteEnvio
     *
     * @param string $numeroTramiteEnvio
     * @return PolizaSeguro
     */
    public function setNumeroTramiteEnvio($numeroTramiteEnvio) {
        $this->numeroTramiteEnvio = $numeroTramiteEnvio;

        return $this;
    }

    /**
     * Get numeroTramiteEnvio
     *
     * @return string
     */
    public function getNumeroTramiteEnvio() {
        return $this->numeroTramiteEnvio;
    }

    /**
     * Set numeroTramitePolizaGarantia
     *
     * @param string $numeroTramiteEnvio
     * @return PolizaSeguro
     */
    public function setNumeroTramitePolizaGarantia($numeroTramitePolizaGarantia) {
        $this->numeroTramitePolizaGarantia = $numeroTramitePolizaGarantia;

        return $this;
    }

    /**
     * Get numeroTramitePolizaGarantia
     *
     * @return string
     */
    public function getNumeroTramitePolizaGarantia() {
        return $this->numeroTramitePolizaGarantia;
    }

    public function setAseguradora2($aseguradora2)
    {
        $this->aseguradora2 = $aseguradora2;

        return $this;
    }

    public function getAseguradora2()
    {
        return $this->aseguradora2;
    }

    public function setTipoCobertura($tipoCobertura)
    {
        $this->tipoCobertura = $tipoCobertura;

        return $this;
    }

    public function getTipoCobertura()
    {
        return $this->tipoCobertura;
    }

    public function getDocumentoFinanciero() {
        return null;
    }

	public function setEstadoPoliza($estadoPoliza)
	{
		$this->estadoPoliza = $estadoPoliza;
		return $this;
	}

	public function getEstadoPoliza()
	{
		return $this->estadoPoliza;
	}

	public function setEstadoRevisionPoliza($estadoRevisionPoliza)
	{
		$this->estadoRevisionPoliza = $estadoRevisionPoliza;
		return $this;
	}

	public function getEstadoRevisionPoliza()
	{
		return $this->estadoRevisionPoliza;
	}
}
