<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ComprasBundle\Entity\OrdenCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteLetraComprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Proveedor
 *
 * @ORM\Table(name="proveedor")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\ProveedorRepository")
 */
class Proveedor extends ConsultorProveedor implements BaseAuditable {

    /**
     * @var ClienteProveedor
     *
     * @ORM\ManyToOne(targetEntity="ClienteProveedor", inversedBy="proveedores", cascade={"persist"})
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
     * @var EstadoProveedor
     *
     * @ORM\ManyToOne(targetEntity="EstadoProveedor", inversedBy="proveedores")
     * @ORM\JoinColumn(name="id_estado_proveedor", referencedColumnName="id")
     * 
     */
    protected $estadoProveedor;

    /**
     * @ORM\Column(name="id_cuenta", type="integer", nullable=true)
     */
    protected $idCuenta;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\CuentaBancariaPersona
     */
    protected $cuenta;

    /**
     * @ORM\Column(name="id_nacionalidad", type="integer", nullable=true)
     */
    protected $idNacionalidad;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Nacionalidad
     */
    protected $nacionalidad;

    /**
     * @var CertificadoExencion
     * 
     * @ORM\OneToOne(targetEntity="CertificadoExencion", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_certificado_exencion_iva", referencedColumnName="id")
     */
    protected $certificadoExencionIVA;

    /**
     * @var CertificadoExencion
     * 
     * @ORM\OneToOne(targetEntity="CertificadoExencion", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_certificado_exencion_ganancias", referencedColumnName="id")
     */
    protected $certificadoExencionGanancias;

    /**
     * @var CertificadoExencion
     * 
     * @ORM\OneToOne(targetEntity="CertificadoExencion", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_certificado_ingresos_brutos", referencedColumnName="id")
     */
    protected $certificadoExencionIngresosBrutos;

    /**
     * @var CertificadoExencion
     * 
     * @ORM\OneToOne(targetEntity="CertificadoExencion", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_certificado_suss", referencedColumnName="id")
     */
    protected $certificadoExencionSUSS;

    /**
     * @ORM\ManyToMany(targetEntity="Rubro", inversedBy="proveedores")
     * @ORM\JoinTable(name="proveedor_rubro",
     *      joinColumns={@ORM\JoinColumn(name="id_proveedor", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_rubro", referencedColumnName="id")}
     *      )
     */
    protected $rubros;

    /**
     * @ORM\OneToMany(targetEntity="ContactoProveedor", mappedBy="proveedor", cascade={"persist", "remove"})
     */
    protected $contactosProveedor;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion_calificacion", type="text", nullable=true)
     */
    protected $observacionCalificacion;

    /**
     * @ORM\OneToOne(targetEntity="EvaluacionProveedor", mappedBy="proveedor", cascade={"persist", "remove"})
     */
    protected $evaluacionProveedor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_ute", type="boolean", nullable=false)
     */
    protected $esUTE;

    /**
     * @var \ADIF\ContableBundle\Entity\ProveedorUTE
     * 
     * @ORM\OneToMany(targetEntity="ProveedorUTE", mappedBy="proveedorUTE", cascade={"persist", "remove"})
     */
    protected $proveedoresUTE;

    /**
     * @ORM\OneToMany(targetEntity="Cotizacion", mappedBy="proveedor")
     */
    protected $cotizacionesSolicitadas;

    /**
     * @ORM\OneToMany(targetEntity="OrdenCompra", mappedBy="proveedor")
     */
    protected $ordenesCompra;

    /**
     * @ORM\OneToMany(targetEntity="CodigoAutorizacionImpresionProveedor", mappedBy="proveedor", cascade={"persist", "remove"})
     */
    protected $cais;

    /**
     * @var double
     * @ORM\Column(name="monto_facturado_acumulado", type="decimal", precision=15, scale=2, nullable=true)
     * 
     */
    protected $montoFacturadoAcumulado;

    /**
     * @var double
     * @ORM\Column(name="monto_suss", type="decimal", precision=15, scale=2, nullable=true)
     * 
     */
    protected $montoSUSS;
	
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

        parent::__construct();

        $this->esUTE = FALSE;
        $this->cantidadCotizacionesPresentadas = 0;
        $this->cantidadCotizacionesSolicitadas = 0;
        $this->cantidadSolicitudesGanadas = 0;
        $this->evaluacionProveedor = new EvaluacionProveedor();
        $this->proveedoresUTE = new ArrayCollection();
        $this->contactosProveedor = new ArrayCollection();
        $this->rubros = new ArrayCollection();
        $this->cotizacionesSolicitadas = new ArrayCollection();
        $this->ordenesCompra = new ArrayCollection();
        $this->cais = new ArrayCollection();
        $this->montoFacturadoAcumulado = 0;
        $this->montoSUSS = 0;
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
     * Set clienteProveedor
     *
     * @param ClienteProveedor $clienteProveedor
     * @return Proveedor
     */
    public function setClienteProveedor(ClienteProveedor $clienteProveedor = null) {
        $this->clienteProveedor = $clienteProveedor;

        return $this;
    }

    /**
     * Get clienteProveedor
     *
     * @return ClienteProveedor 
     */
    public function getClienteProveedor() {
        return $this->clienteProveedor;
    }

    /**
     * Set representanteLegal
     *
     * @param string $representanteLegal
     * @return Proveedor
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
     * Set estadoProveedor
     *
     * @param EstadoProveedor $estadoProveedor
     * @return Proveedor
     */
    public function setEstadoProveedor(EstadoProveedor $estadoProveedor = null) {
        $this->estadoProveedor = $estadoProveedor;

        return $this;
    }

    /**
     * Get estadoProveedor
     *
     * @return EstadoProveedor 
     */
    public function getEstadoProveedor() {
        return $this->estadoProveedor;
    }

// CUENTA BANCARIA

    /**
     * 
     * @param int $idCuenta
     * @return ConsultorProveedor
     */
    public function setIdCuenta($idCuenta) {
        $this->idCuenta = $idCuenta;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdCuenta() {
        return $this->idCuenta;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\CuentaBancariaPersona $cuenta
     */
    public function setCuenta($cuenta) {

        if (null != $cuenta) {
            $this->idCuenta = $cuenta->getId();
        } //.
        else {
            $this->idCuenta = null;
        }

        $this->cuenta = $cuenta;
    }

    /**
     * 
     * @return type
     */
    public function getCuenta() {
        return $this->cuenta;
    }

    /**
     * 
     * @param type $idNacionalidad
     * @return Proveedor
     */
    public function setIdNacionalidad($idNacionalidad) {
        $this->idNacionalidad = $idNacionalidad;

        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getIdNacionalidad() {
        return $this->idNacionalidad;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Nacionalidad $nacionalidad
     */
    public function setNacionalidad($nacionalidad) {

        if (null != $nacionalidad) {
            $this->idNacionalidad = $nacionalidad->getId();
        } //.
        else {
            $this->idNacionalidad = null;
        }

        $this->nacionalidad = $nacionalidad;
    }

    /**
     * 
     * @return \ADIF\RecursosHumanosBundle\Entity\Nacionalidad
     */
    public function getNacionalidad() {
        return $this->nacionalidad;
    }

    /**
     * Set certificadoExencionIVA
     *
     * @param CertificadoExencion $certificadoExencionIVA
     * @return Proveedor
     */
    public function setCertificadoExencionIVA(CertificadoExencion $certificadoExencionIVA = null) {
        $this->certificadoExencionIVA = $certificadoExencionIVA;

        return $this;
    }

    /**
     * Get certificadoExencionIVA
     *
     * @return CertificadoExencion 
     */
    public function getCertificadoExencionIVA() {
        return $this->certificadoExencionIVA;
    }

    /**
     * Set certificadoExencionGanancias
     *
     * @param CertificadoExencion $certificadoExencionGanancias
     * @return Proveedor
     */
    public function setCertificadoExencionGanancias(CertificadoExencion $certificadoExencionGanancias = null) {
        $this->certificadoExencionGanancias = $certificadoExencionGanancias;

        return $this;
    }

    /**
     * Get certificadoExencionGanancias
     *
     * @return CertificadoExencion 
     */
    public function getCertificadoExencionGanancias() {
        return $this->certificadoExencionGanancias;
    }

    /**
     * Set certificadoExencionIngresosBrutos
     *
     * @param CertificadoExencion $certificadoExencionIngresosBrutos
     * @return Proveedor
     */
    public function setCertificadoExencionIngresosBrutos(CertificadoExencion $certificadoExencionIngresosBrutos = null) {
        $this->certificadoExencionIngresosBrutos = $certificadoExencionIngresosBrutos;

        return $this;
    }

    /**
     * Get certificadoExencionIngresosBrutos
     *
     * @return CertificadoExencion 
     */
    public function getCertificadoExencionIngresosBrutos() {
        return $this->certificadoExencionIngresosBrutos;
    }

    /**
     * Set certificadoExencionSUSS
     *
     * @param CertificadoExencion $certificadoExencionSUSS
     * @return Proveedor
     */
    public function setCertificadoExencionSUSS(CertificadoExencion $certificadoExencionSUSS = null) {
        $this->certificadoExencionSUSS = $certificadoExencionSUSS;

        return $this;
    }

    /**
     * Get certificadoExencionSUSS
     *
     * @return CertificadoExencion 
     */
    public function getCertificadoExencionSUSS() {
        return $this->certificadoExencionSUSS;
    }

    /**
     * Add rubros
     *
     * @param Rubro $rubros
     * @return Proveedor
     */
    public function addRubro(Rubro $rubros) {
        $this->rubros[] = $rubros;

        return $this;
    }

    /**
     * Remove rubros
     *
     * @param Rubro $rubros
     */
    public function removeRubro(Rubro $rubros) {
        $this->rubros->removeElement($rubros);
    }

    /**
     * Get rubros
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRubros() {
        return $this->rubros;
    }

    /**
     * Add datosContacto
     *
     * @param DatoContacto $datosContacto
     * @return Proveedor
     */
    public function addDatosContacto(DatoContacto $datosContacto) {
        $this->datosContacto[] = $datosContacto;

        return $this;
    }

    /**
     * Add contactosProveedor
     *
     * @param ContactoProveedor $contactoProveedor
     * @return Proveedor
     */
    public function addContactosProveedor(ContactoProveedor $contactoProveedor) {
        $this->contactosProveedor[] = $contactoProveedor;

        $contactoProveedor->setProveedor($this);

        return $this;
    }

    /**
     * Remove contactosProveedor
     *
     * @param ContactoProveedor $contactoProveedor
     */
    public function removeContactosProveedor(ContactoProveedor $contactoProveedor) {
        $this->contactosProveedor->removeElement($contactoProveedor);
    }

    /**
     * Get contactosProveedor
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContactosProveedor() {
        return $this->contactosProveedor;
    }

    /**
     * Set evaluacionProveedor
     *
     * @param EvaluacionProveedor $evaluacionProveedor
     * @return Proveedor
     */
    public function setEvaluacionProveedor(EvaluacionProveedor $evaluacionProveedor = null) {
        $this->evaluacionProveedor = $evaluacionProveedor;

        return $this;
    }

    /**
     * Get evaluacionProveedor
     *
     * @return EvaluacionProveedor 
     */
    public function getEvaluacionProveedor() {
        return $this->evaluacionProveedor;
    }

    /**
     * Set observacionCalificacion
     *
     * @param string $observacionCalificacion
     * @return Proveedor
     */
    public function setObservacionCalificacion($observacionCalificacion) {
        $this->observacionCalificacion = $observacionCalificacion;

        return $this;
    }

    /**
     * Get observacionCalificacion
     *
     * @return string 
     */
    public function getObservacionCalificacion() {
        return $this->observacionCalificacion;
    }

    /**
     * Add cotizacionesSolicitadas
     *
     * @param Cotizacion $cotizacionSolicitada
     * @return Proveedor
     */
    public function addCotizacionSolicitada(Cotizacion $cotizacionSolicitada) {
        $this->cotizacionesSolicitadas[] = $cotizacionSolicitada;

        return $this;
    }

    /**
     * Remove cotizacionesSolicitadas
     *
     * @param Cotizacion $cotizacionSolicitada
     */
    public function removeCotizacionSolicitada(Cotizacion $cotizacionSolicitada) {
        $this->cotizacionesSolicitadas->removeElement($cotizacionSolicitada);
    }

    /**
     * Get cotizacionesSolicitadas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCotizacionesSolicitadas() {
        return $this->cotizacionesSolicitadas;
    }

    /**
     * Get cotizacionesPresentadas
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCotizacionesPresentadas() {

        $cotizacionesPresentadas = $this->cotizacionesSolicitadas;

        foreach ($cotizacionesPresentadas as $cotizacionPresentada) {

            if (null == $cotizacionPresentada->getFechaCotizacion()) {
                $cotizacionesPresentadas->removeElement($cotizacionPresentada);
            }
        }

        return $cotizacionesPresentadas;
    }

    /**
     * Add cais
     *
     * @param CodigoAutorizacionImpresion $cais
     * @return Proveedor
     */
    public function addCai(CodigoAutorizacionImpresion $cais) {
        $this->cais[] = $cais;

        return $this;
    }

    /**
     * Remove cais
     *
     * @param CodigoAutorizacionImpresion $cais
     */
    public function removeCai(CodigoAutorizacionImpresion $cais) {
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
     * Add ordenesCompra
     *
     * @param OrdenCompra $ordenesCompra
     * @return Proveedor
     */
    public function addOrdenesCompra(OrdenCompra $ordenesCompra) {
        $this->ordenesCompra[] = $ordenesCompra;

        return $this;
    }

    /**
     * Remove ordenesCompra
     *
     * @param OrdenCompra $ordenesCompra
     */
    public function removeOrdenesCompra(OrdenCompra $ordenesCompra) {
        $this->ordenesCompra->removeElement($ordenesCompra);
    }

    /**
     * Get ordenesCompra
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrdenesCompra() {
        return $this->ordenesCompra;
    }

    /**
     * Set esUTE
     *
     * @param boolean $esUTE
     * @return Proveedor
     */
    public function setEsUTE($esUTE) {
        $this->esUTE = $esUTE;

        return $this;
    }

    /**
     * Get esUTE
     *
     * @return boolean 
     */
    public function getEsUTE() {
        return $this->esUTE;
    }

    /**
     * Add proveedoresUTE
     *
     * @param ProveedorUTE $proveedoresUTE
     * @return Proveedor
     */
    public function addProveedoresUTE(ProveedorUTE $proveedoresUTE) {
        $this->proveedoresUTE[] = $proveedoresUTE;

        return $this;
    }

    /**
     * Remove proveedoresUTE
     *
     * @param ProveedorUTE $proveedoresUTE
     */
    public function removeProveedoresUTE(ProveedorUTE $proveedoresUTE) {
        $this->proveedoresUTE->removeElement($proveedoresUTE);
    }

    /**
     * Get proveedoresUTE
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProveedoresUTE() {
        return $this->proveedoresUTE;
    }

    /**
     * 
     * @return type
     */
    public function cuitAndRazonSocial() {

        return $this->clienteProveedor->getCUIT() //
                . ' — ' //
                . $this->clienteProveedor->getRazonSocial();
    }

    /**
     * 
     * @return type
     */
    public function getCUITAndRazonSocial() {

        return $this->cuitAndRazonSocial();
    }

    /**
     * 
     * @param OrdenCompra $ordenCompra
     */
    public function getSaldoOC(OrdenCompra $ordenCompra) {

        // Los comprobantes: Notas de Credito, Anticipos, Ordenes de Pago (que tendran cheques y retenciones asociadas) 
        //      Restar deuda al Proveedor
        // Los comprobantes Facturas, Tickets Factura, Recibos y Notas de Debito 
        //      Suman deuda al Proveedor

        $ordenCompra->getComprobantes();
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
    public function getRazonSocial() {
        return $this->clienteProveedor->getRazonSocial();
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
        return $this->getCUIT();
    }

    /**
     * 
     */
    public function getDomicilio() {
        return $this->clienteProveedor->getDomicilioComercial();
    }

    /**
     * 
     */
    public function getLocalidad() {
        return $this->clienteProveedor->getDomicilioComercial()->getLocalidad();
    }

    public function getDatosImpositivos() {
        return $this->clienteProveedor->getDatosImpositivos();
    }

    /**
     * @return Proveedor
     */
    public function getCaisPorPuntoVenta() {

        $cais = [];

        foreach ($this->cais as $cai) {

            /* @var $cai CodigoAutorizacionImpresionProveedor */
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
     * 
     * @return string
     */
    public function getControllerPath() {
        return 'proveedor';
    }

    /**
     * 
     * @return type
     */
    public function getLetrasComprobante() {

        $letras = [];

        $tipoResponsable = $this->getClienteProveedor()
                ->getDatosImpositivos()->getCondicionIVA()
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
     * Set montoSUSS
     *
     * @param string $montoSUSS
     * @return PagoOrdenPago
     */
    public function setMontoSUSS($montoSUSS) {
        $this->montoSUSS = $montoSUSS;

        return $this;
    }

    /**
     * Get montoSUSS
     *
     * @return string 
     */
    public function getMontoSUSS() {
        return $this->montoSUSS;
    }

    /**
     * Set montoFacturadoAcumulado
     *
     * @param string $montoFacturadoAcumulado
     * @return PagoOrdenPago
     */
    public function setMontoFacturadoAcumulado($montoFacturadoAcumulado) {
        $this->montoFacturadoAcumulado = $montoFacturadoAcumulado;

        return $this;
    }

    /**
     * Get montoFacturadoAcumulado
     *
     * @return string 
     */
    public function getMontoFacturadoAcumulado() {
        return $this->montoFacturadoAcumulado;
    }

    public function getOrdenesCompraFinal() {
        $ordenesCompra = array();
        foreach ($this->ordenesCompra as $oc) {
            if ($oc->getOrdenCompraOriginal() != null || $oc->getEsServicio()) {
				if ($oc->getFechaAnulacion() == null) {
					$ordenesCompra[] = $oc;
				}
            }
        }
        return $ordenesCompra;
    }
    
    public function getOrdenesCompraFinalALaFecha($fecha) {
        $ordenesCompra = array();
        foreach ($this->ordenesCompra as $oc) {
            if ($oc->getOrdenCompraOriginal() != null || $oc->getEsServicio() && $oc->getFechaOrdenCompra() <= $fecha) {
				if ($oc->getFechaAnulacion() == null) {
					$ordenesCompra[] = $oc;
				}
            }
        }
        return $ordenesCompra;
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
