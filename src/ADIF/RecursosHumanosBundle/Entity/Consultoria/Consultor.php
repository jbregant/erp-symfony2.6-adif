<?php

namespace ADIF\RecursosHumanosBundle\Entity\Consultoria;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ComprasBundle\Entity\ConsultorProveedor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Consultor
 *
 * @author Manuel Becerra
 * created 04/03/2015
 * 
 * @ORM\Table(name="consultor")
 * @ORM\Entity
 */
class Consultor extends ConsultorProveedor implements BaseAuditable {

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=50, nullable=false)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El CUIT no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $CUIT;

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
     * @ORM\Column(name="legajo", type="string", length=50, nullable=false)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El legajo no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $legajo;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="El email no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="La tel&eacute;fono no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $telefono;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\CuentaBancariaPersona
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\CuentaBancariaPersona", cascade={"persist"})
     * @ORM\JoinColumn(name="id_cuenta", referencedColumnName="id")
     */
    protected $cuenta;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Domicilio
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Domicilio", cascade={"persist"})
     * @ORM\JoinColumn(name="id_domicilio_comercial", referencedColumnName="id")
     */
    protected $domicilioComercial;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Domicilio
     *
     * @ORM\ManyToOne(targetEntity="ADIF\RecursosHumanosBundle\Entity\Domicilio", cascade={"persist"})
     * @ORM\JoinColumn(name="id_domicilio_fiscal", referencedColumnName="id")
     */
    protected $domicilioFiscal;

    /**
     * @ORM\Column(name="id_datos_impositivos", type="integer", nullable=false)
     */
    protected $idDatosImpositivos;

    /**
     * @var ADIF\ComprasBundle\Entity\DatosImpositivos
     */
    protected $datosImpositivos;

    /**
     * @ORM\Column(name="id_certificado_exencion_iva", type="integer", nullable=true)
     */
    protected $idCertificadoExencionIVA;

    /**
     * @var ADIF\ComprasBundle\Entity\CertificadoExencion
     */
    protected $certificadoExencionIVA;

    /**
     * @ORM\Column(name="id_certificado_exencion_ganancias", type="integer", nullable=true)
     */
    protected $idCertificadoExencionGanancias;

    /**
     * @var ADIF\ComprasBundle\Entity\CertificadoExencion
     */
    protected $certificadoExencionGanancias;

    /**
     * @ORM\Column(name="id_certificado_ingresos_brutos", type="integer", nullable=true)
     */
    protected $idCertificadoExencionIngresosBrutos;

    /**
     * @var ADIF\ComprasBundle\Entity\CertificadoExencion
     */
    protected $certificadoExencionIngresosBrutos;

    /**
     * @ORM\Column(name="id_certificado_suss", type="integer", nullable=true)
     */
    protected $idCertificadoExencionSUSS;

    /**
     * @var ADIF\ComprasBundle\Entity\CertificadoExencion
     */
    protected $certificadoExencionSUSS;

    /**
     * @ORM\OneToMany(targetEntity="CodigoAutorizacionImpresionConsultor", mappedBy="consultor", cascade={"persist", "remove"})
     */
    protected $cais;

    /**
     * @ORM\OneToMany(targetEntity="ConsultorArchivo", mappedBy="consultor", cascade={"persist","remove"})
     */
    protected $archivos;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=false, options={"default" = 1})
     */
    private $activo = true;

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->cais = new \Doctrine\Common\Collections\ArrayCollection();
        $this->archivos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set CUIT
     *
     * @param string $cUIT
     * @return Consultor
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
     * 
     * Me devuelve el DNI del consultor
     */
    public function getDNI() {
        $arrCuit = explode('-', $this->CUIT);
        return $arrCuit[1];
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     * @return Consultor
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
     * Set legajo
     *
     * @param string $legajo
     * @return Consultor
     */
    public function setLegajo($legajo) {
        $this->legajo = $legajo;

        return $this;
    }

    /**
     * Get legajo
     *
     * @return string 
     */
    public function getLegajo() {
        return str_pad($this->legajo, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Consultor
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
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
     * Set telefono
     *
     * @param string $telefono
     * @return Consultor
     */
    public function setTelefono($telefono) {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono() {
        return $this->telefono;
    }

    /**
     * Set cuenta
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\CuentaBancariaPersona $cuenta
     * @return Consultor
     */
    public function setCuenta(\ADIF\RecursosHumanosBundle\Entity\CuentaBancariaPersona $cuenta = null) {
        $this->cuenta = $cuenta;

        return $this;
    }

    /**
     * Get cuenta
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\CuentaBancariaPersona 
     */
    public function getCuenta() {
        return $this->cuenta;
    }

    /**
     * Set domicilioComercial
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Domicilio $domicilioComercial
     * @return Consultor
     */
    public function setDomicilioComercial(\ADIF\RecursosHumanosBundle\Entity\Domicilio $domicilioComercial = null) {
        $this->domicilioComercial = $domicilioComercial;

        return $this;
    }

    /**
     * Get domicilioComercial
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Domicilio 
     */
    public function getDomicilioComercial() {
        return $this->domicilioComercial;
    }

    /**
     * Set domicilioFiscal
     *
     * @param \ADIF\RecursosHumanosBundle\Entity\Domicilio $domicilioFiscal
     * @return Consultor
     */
    public function setDomicilioFiscal(\ADIF\RecursosHumanosBundle\Entity\Domicilio $domicilioFiscal = null) {
        $this->domicilioFiscal = $domicilioFiscal;

        return $this;
    }

    /**
     * Get domicilioFiscal
     *
     * @return \ADIF\RecursosHumanosBundle\Entity\Domicilio 
     */
    public function getDomicilioFiscal() {
        return $this->domicilioFiscal;
    }

    /**
     * 
     * @param type $idDatosImpositivos
     * @return Consultor
     */
    public function setIdDatosImpositivos($idDatosImpositivos) {
        $this->idDatosImpositivos = $idDatosImpositivos;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdDatosImpositivos() {
        return $this->idDatosImpositivos;
    }

    /**
     * Set datosImpositivos
     *
     * @param DatosImpositivos $datosImpositivos
     * @return Consultor
     */
    public function setDatosImpositivos(\ADIF\ComprasBundle\Entity\DatosImpositivos $datosImpositivos = null) {

        if (null != $datosImpositivos) {
            $this->idDatosImpositivos = $datosImpositivos->getId();
        } //.
        else {
            $this->idDatosImpositivos = null;
        }

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
     * Set idCertificadoExencionIVA
     *
     * @param integer $idCertificadoExencionIVA
     * @return Consultor
     */
    public function setIdCertificadoExencionIVA($idCertificadoExencionIVA) {
        $this->idCertificadoExencionIVA = $idCertificadoExencionIVA;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdCertificadoExencionIVA() {
        return $this->idCertificadoExencionIVA;
    }

    /**
     * Set certificadoExencionIVA
     *
     * @param \ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionIVA
     * @return Consultor
     */
    public function setCertificadoExencionIVA(\ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionIVA = null) {

        if (null != $certificadoExencionIVA) {
            $this->idCertificadoExencionIVA = $certificadoExencionIVA->getId();
        } //.
        else {
            $this->idCertificadoExencionIVA = null;
        }

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
     * Set idCertificadoExencionGanancias
     *
     * @param integer $idCertificadoExencionGanancias
     * @return Consultor
     */
    public function setIdCertificadoExencionGanancias($idCertificadoExencionGanancias) {
        $this->idCertificadoExencionGanancias = $idCertificadoExencionGanancias;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdCertificadoExencionGanancias() {
        return $this->idCertificadoExencionGanancias;
    }

    /**
     * Set certificadoExencionGanancias
     *
     * @param \ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionGanancias
     * @return Consultor
     */
    public function setCertificadoExencionGanancias(\ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionGanancias = null) {

        if (null != $certificadoExencionGanancias) {
            $this->idCertificadoExencionGanancias = $certificadoExencionGanancias->getId();
        } //.
        else {
            $this->idCertificadoExencionGanancias = null;
        }

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
     * Set idCertificadoExencionIngresosBrutos
     *
     * @param integer $idCertificadoExencionIngresosBrutos
     * @return Consultor
     */
    public function setIdCertificadoExencionIngresosBrutos($idCertificadoExencionIngresosBrutos) {
        $this->idCertificadoExencionIngresosBrutos = $idCertificadoExencionIngresosBrutos;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdCertificadoExencionIngresosBrutos() {
        return $this->idCertificadoExencionIngresosBrutos;
    }

    /**
     * Set certificadoExencionIngresosBrutos
     *
     * @param \ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionIngresosBrutos
     * @return Consultor
     */
    public function setCertificadoExencionIngresosBrutos(\ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionIngresosBrutos = null) {

        if (null != $certificadoExencionIngresosBrutos) {
            $this->idCertificadoExencionIngresosBrutos = $certificadoExencionIngresosBrutos->getId();
        } //.
        else {
            $this->idCertificadoExencionIngresosBrutos = null;
        }

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
     * Set idCertificadoExencionSUSS
     *
     * @param integer $idCertificadoExencionSUSS
     * @return Consultor
     */
    public function setIdCertificadoExencionSUSS($idCertificadoExencionSUSS) {
        $this->idCertificadoExencionSUSS = $idCertificadoExencionSUSS;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdCertificadoExencionSUSS() {
        return $this->idCertificadoExencionSUSS;
    }

    /**
     * Set certificadoExencionSUSS
     *
     * @param \ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionSUSS
     * @return Proveedor
     */
    public function setCertificadoExencionSUSS(\ADIF\ComprasBundle\Entity\CertificadoExencion $certificadoExencionSUSS = null) {

        if (null != $certificadoExencionSUSS) {
            $this->idCertificadoExencionSUSS = $certificadoExencionSUSS->getId();
        } //.
        else {
            $this->idCertificadoExencionSUSS = null;
        }

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
     * Add cais
     *
     * @param CodigoAutorizacionImpresionConsultor $cais
     * @return Consultor
     */
    public function addCai(CodigoAutorizacionImpresionConsultor $cais) {
        $this->cais[] = $cais;

        return $this;
    }

    /**
     * Remove cais
     *
     * @param CodigoAutorizacionImpresionConsultor $cais
     */
    public function removeCai(CodigoAutorizacionImpresionConsultor $cais) {
        $this->cais->removeElement($cais);
    }

    /**
     * Get cais
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCais() {
        return $this->cais;
    }

    /**
     * Add archivos
     *
     * @param ConsultorArchivo $archivos
     * @return Consultor
     */
    public function addArchivo(ConsultorArchivo $archivos) {
        $this->archivos[] = $archivos;

        return $this;
    }

    /**
     * Remove archivos
     *
     * @param ConsultorArchivo $archivos
     */
    public function removeArchivo(ConsultorArchivo $archivos) {
        $this->archivos->removeElement($archivos);
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
    public function getCuitAndRazonSocial() {
        return $this->CUIT . ' — ' . $this->razonSocial;
    }

    /**
     * 
     * @return type
     */
    public function getTipoDocumento() {
        return 'CUIT';
    }

    /**
     * 
     * @return type
     */
    public function getNroDocumento() {
        return $this->CUIT;
    }

    /**
     * 
     */
    public function getDomicilio() {
        return $this->getDomicilioComercial();
    }

    /**
     * 
     */
    public function getLocalidad() {
        return $this->getDomicilioComercial()->getLocalidad();
    }

    /**
     * 
     * @return string
     */
    public function getControllerPath() {
        return 'consultor';
    }

    /**
     * 
     * @return type
     */
    public function getLetrasComprobante() {

        $letras = [];

        $tipoResponsable = $this->getDatosImpositivos()->getCondicionIVA()
                ->getDenominacionTipoResponsable();

        if ($tipoResponsable == ConstanteTipoResponsable::INSCRIPTO) {
            $letras[] = ConstanteLetraComprobante::A;
            $letras[] = ConstanteLetraComprobante::A_CON_LEYENDA;
            $letras[] = ConstanteLetraComprobante::E;
            $letras[] = ConstanteLetraComprobante::M;
            $letras[] = ConstanteLetraComprobante::Y;
        }

        if ($tipoResponsable == ConstanteTipoResponsable::IVA_EXENTO || $tipoResponsable == ConstanteTipoResponsable::RESPONSABLE_MONOTRIBUTO) {
            $letras[] = ConstanteLetraComprobante::C;
            $letras[] = ConstanteLetraComprobante::Y;
        }

        if ($tipoResponsable == ConstanteTipoResponsable::SUJETO_NO_CATEGORIZADO) {

            $letras[] = ConstanteLetraComprobante::A;
            $letras[] = ConstanteLetraComprobante::A_CON_LEYENDA;
            $letras[] = ConstanteLetraComprobante::B;
            $letras[] = ConstanteLetraComprobante::C;
            $letras[] = ConstanteLetraComprobante::E;
            $letras[] = ConstanteLetraComprobante::M;
            $letras[] = ConstanteLetraComprobante::Y;
        }

        return $letras;
    }

    /**
     * 
     * @return type
     */
    public function getCondicionIVA() {
        return $this->getDatosImpositivos()->getCondicionIVA();
    }

    /**
     * 
     * @return type
     */
    public function getCondicionGanancias() {
        return $this->getDatosImpositivos()->getCondicionGanancias();
    }

    /**
     * 
     * @return type
     */
    public function getCondicionIngresosBrutos() {
        return $this->getDatosImpositivos()->getCondicionIngresosBrutos();
    }

    /**
     * 
     * @return type
     */
    public function getCondicionSUSS() {
        return $this->getDatosImpositivos()->getCondicionSUSS();
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
     * 
     * @return type
     */
    public function getCaisPorPuntoVenta() {

        $cais = [];

        foreach ($this->cais as $cai) {

            /* @var $cai CodigoAutorizacionImpresionConsultor */
            if (!isset($cais[$cai->getPuntoVenta()])) {

                $cais[$cai->getPuntoVenta()] = $cai->getFechaVencimiento()->format('d/m/Y');
            } else {

                if ($cais[$cai->getPuntoVenta()] < $cai->getFechaVencimiento()->format('d/m/Y')) {

                    $cais[$cai->getPuntoVenta()] = $cai->getFechaVencimiento()->format('d/m/Y');
                }
            }
        }

        return $cais;
    }
    
    /**
     * Set activo
     *
     * @param boolean $activo
     * @return Consultor
     */
    public function setActivo($activo) {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo() {
        return $this->activo;
    }

}
