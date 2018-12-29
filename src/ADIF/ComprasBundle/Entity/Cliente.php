<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Cliente
 *
 * @ORM\Table(name="cliente")
 * @ORM\Entity
 */
class Cliente extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ADIF\ComprasBundle\Entity\ClienteProveedor
     *
     * @ORM\ManyToOne(targetEntity="ClienteProveedor", cascade={"persist"})
     * @ORM\JoinColumn(name="id_cliente_proveedor", referencedColumnName="id")
     * 
     */
    protected $clienteProveedor;

    /**
     * @var string
     *
     * @ORM\Column(name="representante_legal", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="El representante legal no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $representanteLegal;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    protected $observacion;

    /**
     * @var \ADIF\ComprasBundle\Entity\EstadoCliente
     *
     * @ORM\ManyToOne(targetEntity="EstadoCliente", inversedBy="clientes")
     * @ORM\JoinColumn(name="id_estado_cliente", referencedColumnName="id")
     * 
     */
    protected $estadoCliente;

    /**
     * @ORM\Column(name="id_tipo_moneda", type="integer", nullable=false)
     */
    protected $idTipoMoneda;

    /**
     * @var ADIF\ContableBundle\Entity\TipoMoneda
     */
    protected $tipoMoneda;

    /**
     * @ORM\Column(name="id_cuenta_contable", type="integer", nullable=true)
     */
    protected $idCuentaContable;

    /**
     * @var ADIF\ContableBundle\Entity\CuentaContable
     */
    protected $cuentaContable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="pasible_percepcion_ingresos_brutos", type="boolean", nullable=false)
     */
    protected $pasiblePercepcionIngresosBrutos;

    /**
     * @var CertificadoExencion
     * 
     * @ORM\OneToOne(targetEntity="ADIF\ComprasBundle\Entity\CertificadoExencion", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_certificado_exencion_iva", referencedColumnName="id")
     */
    protected $certificadoExencionIVA;

    /**
     * @var CertificadoExencion
     * 
     * @ORM\OneToOne(targetEntity="ADIF\ComprasBundle\Entity\CertificadoExencion", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_certificado_exencion_ganancias", referencedColumnName="id")
     */
    protected $certificadoExencionGanancias;

    /**
     * @var CertificadoExencion
     * 
     * @ORM\OneToOne(targetEntity="ADIF\ComprasBundle\Entity\CertificadoExencion", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_certificado_ingresos_brutos", referencedColumnName="id")
     */
    protected $certificadoExencionIngresosBrutos;

    /**
     * @var CertificadoExencion
     * 
     * @ORM\OneToOne(targetEntity="ADIF\ComprasBundle\Entity\CertificadoExencion", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_certificado_suss", referencedColumnName="id")
     */
    protected $certificadoExencionSUSS;
	
	 /**
     * @var IibbCaba
     *
     * @ORM\ManyToOne(targetEntity="IibbCaba", inversedBy="clientes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_iibb_caba", referencedColumnName="id", nullable=true)
     * })
     */
	protected $iibbCaba;

    /**
     * Constructor
     */
    public function __construct() {
        $this->pasiblePercepcionIngresosBrutos = false;
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->getClienteProveedor()->getRazonSocial();
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
     * Set clienteProveedor
     *
     * @param \ADIF\ComprasBundle\Entity\ClienteProveedor $clienteProveedor
     * @return Cliente
     */
    public function setClienteProveedor(\ADIF\ComprasBundle\Entity\ClienteProveedor $clienteProveedor = null) {
        $this->clienteProveedor = $clienteProveedor;

        return $this;
    }

    /**
     * Get clienteProveedor
     *
     * @return \ADIF\ComprasBundle\Entity\ClienteProveedor 
     */
    public function getClienteProveedor() {
        return $this->clienteProveedor;
    }

    /**
     * Set representanteLegal
     *
     * @param string $representanteLegal
     * @return Cliente
     */
    public function setRepresentanteLegal($representanteLegal) {
        $this->representanteLegal = $representanteLegal;

        return $this;
    }

    /**
     * Get representanteLegal
     *
     * @return string 
     */
    public function getRepresentanteLegal() {
        return $this->representanteLegal;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return Cliente
     */
    public function setObservacion($observacion) {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion() {
        return $this->observacion;
    }

    /**
     * Set pasiblePercepcionIngresosBrutos
     *
     * @param boolean $pasiblePercepcionIngresosBrutos
     * @return Cliente
     */
    public function setPasiblePercepcionIngresosBrutos($pasiblePercepcionIngresosBrutos) {
        $this->pasiblePercepcionIngresosBrutos = $pasiblePercepcionIngresosBrutos;

        return $this;
    }

    /**
     * Get pasiblePercepcionIngresosBrutos
     *
     * @return boolean 
     */
    public function getPasiblePercepcionIngresosBrutos() {
        return $this->pasiblePercepcionIngresosBrutos;
    }

    /**
     * Set estadoCliente
     *
     * @param \ADIF\ComprasBundle\Entity\EstadoCliente $estadoCliente
     * @return Cliente
     */
    public function setEstadoCliente(\ADIF\ComprasBundle\Entity\EstadoCliente $estadoCliente = null) {
        $this->estadoCliente = $estadoCliente;

        return $this;
    }

    /**
     * Get estadoCliente
     *
     * @return \ADIF\ComprasBundle\Entity\EstadoCliente 
     */
    public function getEstadoCliente() {
        return $this->estadoCliente;
    }

// CUENTA CONTABLE

    /**
     * 
     * @return type
     */
    public function getIdCuentaContable() {
        return $this->idCuentaContable;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     */
    public function setCuentaContable($cuentaContable) {

        if (null != $cuentaContable) {
            $this->idCuentaContable = $cuentaContable->getId();
        } else {
            $this->idCuentaContable = null;
        }

        $this->cuentaContable = $cuentaContable;
    }

    /**
     * 
     * @return type
     */
    public function getCuentaContable() {
        return $this->cuentaContable;
    }

// TIPO MONEDA  

    /**
     * 
     * @return type
     */
    public function getIdTipoMoneda() {
        return $this->idTipoMoneda;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoMoneda $tipoMoneda
     */
    public function setTipoMoneda($tipoMoneda) {
        if (null != $tipoMoneda) {
            $this->idTipoMoneda = $tipoMoneda->getId();
        } else {
            $this->idTipoMoneda = null;
        }

        $this->tipoMoneda = $tipoMoneda;
    }

    /**
     * 
     * @return type
     */
    public function getTipoMoneda() {
        return $this->tipoMoneda;
    }

    /**
     * 
     * @return type
     */
    public function getRazonSocial() {
        return $this->clienteProveedor->getRazonSocial();
    }

    /**
     * 
     * @return type
     */
    public function getCUIT() {
        return $this->clienteProveedor->getCUIT();
    }

    /**
     * 
     * @return type
     */
    public function getCuitAndRazonSocial() {
        return $this->getNroDocumento() . ' — ' . $this->clienteProveedor->getRazonSocial();
    }

    /**
     * Get pasibleRetencionIngresosBrutos
     *
     * @return float 
     */
    public function getAlicuotaIngresosBrutos() {
        $alicuota = null;

        // Si el cliente es Convenio Multilateral
        if ($this->getClienteProveedor()->getCondicionIngresosBrutos()->getDenominacionTipoResponsable() == ConstanteTipoResponsable::CONVENIO_MULTILATERAL) {
            $convenioMultilateral = $this->getClienteProveedor()->getConvenioMultilateralIngresosBrutos();
            if ($convenioMultilateral) {
                // Aplico porcentaje CABA
                $alicuota = $convenioMultilateral->getPorcentajeAplicacionCABA();
            }
        }

        // Si tiene riesgo fiscal
        if ($this->getClienteProveedor()->getTieneRiesgoFiscal()) {
            // Aplico Regimen correspondiente al Riesgo Fiscal
            $alicuota = 6;
        } else {
            // Si el Proveedor es Monotributista
            if ($this->getClienteProveedor()->getCondicionIVA()->getDenominacionTipoResponsable() == ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO) {
                // Me fijo si está en la base de Magnitudes Superadas
                if ($this->getClienteProveedor()->getIncluyeMagnitudesSuperadas()) {
                    
                }
            }
        }
    }

    /**
     * Getter letraComprobanteVenta
     * 
     * @return type
     */
    public function getLetraComprobanteVenta() {

        $tipoResponsable = $this->getClienteProveedor()
                ->getDatosImpositivos()->getCondicionIVA()
                ->getDenominacionTipoResponsable();

        return /*(*/($tipoResponsable == ConstanteTipoResponsable::INSCRIPTO) /*&& (!($this->getClienteProveedor()->getDatosImpositivos()->getExentoIVA()))) //*/
                ? ConstanteLetraComprobante::A //
                : ConstanteLetraComprobante::B;
    }

    /**
     * 
     * @return type
     */
    public function getTipoDocumento() {

        if ($this->getCUIT() != null) {
            return 'CUIT';
        }

        if ($this->getDNI() != null) {
            return 'DNI';
        }
    }

    /**
     * 
     * @return type
     */
    public function getNroDocumento() {

        if ($this->getCUIT() != null) {
            return $this->getCUIT();
        }

        if ($this->getDNI() != null) {
            return $this->getDNI();
        }
    }

    public function getDatosImpositivos() {
        return $this->clienteProveedor->getDatosImpositivos();
    }

    /**
     * Set certificadoExencionIVA
     *
     * @param \ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionIVA
     * @return Cliente
     */
    public function setCertificadoExencionIVA(\ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionIVA = null) {
        $this->certificadoExencionIVA = $certificadoExencionIVA;

        return $this;
    }

    /**
     * Get certificadoExencionIVA
     *
     * @return \ADIF\ComprasBundle\Entity\CertificadoExencion 
     */
    public function getCertificadoExencionIVA() {
        return $this->certificadoExencionIVA;
    }

    /**
     * Set certificadoExencionGanancias
     *
     * @param \ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionGanancias
     * @return Cliente
     */
    public function setCertificadoExencionGanancias(\ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionGanancias = null) {
        $this->certificadoExencionGanancias = $certificadoExencionGanancias;

        return $this;
    }

    /**
     * Get certificadoExencionGanancias
     *
     * @return \ADIF\ComprasBundle\Entity\CertificadoExencion 
     */
    public function getCertificadoExencionGanancias() {
        return $this->certificadoExencionGanancias;
    }

    /**
     * Set certificadoExencionIngresosBrutos
     *
     * @param \ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionIngresosBrutos
     * @return Cliente
     */
    public function setCertificadoExencionIngresosBrutos(\ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionIngresosBrutos = null) {
        $this->certificadoExencionIngresosBrutos = $certificadoExencionIngresosBrutos;

        return $this;
    }

    /**
     * Get certificadoExencionIngresosBrutos
     *
     * @return \ADIF\ComprasBundle\Entity\CertificadoExencion 
     */
    public function getCertificadoExencionIngresosBrutos() {
        return $this->certificadoExencionIngresosBrutos;
    }

    /**
     * Set certificadoExencionSUSS
     *
     * @param \ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionSUSS
     * @return Cliente
     */
    public function setCertificadoExencionSUSS(\ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionSUSS = null) {
        $this->certificadoExencionSUSS = $certificadoExencionSUSS;

        return $this;
    }

    /**
     * Get certificadoExencionSUSS
     *
     * @return \ADIF\ComprasBundle\Entity\CertificadoExencion 
     */
    public function getCertificadoExencionSUSS() {
        return $this->certificadoExencionSUSS;
    }

    /**
     * Get DNI
     *
     * @return string 
     */
    public function getDNI() {
        return $this->clienteProveedor->getDNI();
    }

    /**
     * 
     * @return type
     */
    public function getDomicilio() {
        return $this->clienteProveedor->getDomicilioComercial();
    }

    /**
     * 
     * @return type
     */
    public function getLocalidad() {
        return $this->clienteProveedor->getDomicilioComercial()->getLocalidad();
    }
	
	public function setIibbCaba($iibbCaba) 
	{
		$this->iibbCaba = $iibbCaba;
		return $this;
	}
	
	public function getIibbCaba()
	{
		return $this->iibbCaba;
	}

}
