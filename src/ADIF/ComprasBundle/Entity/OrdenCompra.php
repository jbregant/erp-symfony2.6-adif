<?php

namespace ADIF\ComprasBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoOrdenCompra;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of OrdenCompra
 *
 * @author Manuel Becerra
 * created 14/10/2014
 * 
 * @ORM\Table(name="orden_compra")
 * @ORM\Entity(repositoryClass="ADIF\ComprasBundle\Repository\OrdenCompraRepository")
 */
class OrdenCompra extends BaseAuditoria implements BaseAuditable {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="id_usuario", type="integer", nullable=true)
     */
    protected $idUsuario;

    /**
     * @var ADIF\AutenticacionBundle\Entity\Usuario
     */
    protected $usuario;

    /**
     * @ORM\OneToOne(targetEntity="\ADIF\ComprasBundle\Entity\OrdenCompra", cascade={"all"})
     * @ORM\JoinColumn(name="id_orden_compra_original", referencedColumnName="id", nullable=true)
     */
    protected $ordenCompraOriginal;

    /**
     * @var \ADIF\ComprasBundle\Entity\EstadoOrdenCompra
     *
     * @ORM\ManyToOne(targetEntity="EstadoOrdenCompra")
     * @ORM\JoinColumn(name="id_estado_orden_compra", referencedColumnName="id", nullable=true)
     * 
     */
    protected $estadoOrdenCompra;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_orden_compra", type="date", nullable=true)
     */
    protected $fechaOrdenCompra;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_orden_compra", type="integer", nullable=true)
     */
    protected $numeroOrdenCompra;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_anulacion", type="date", nullable=true)
     */
    protected $fechaAnulacion;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo_anulacion", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max="255", 
     *      maxMessage="El motivo de anulación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $motivoAnulacion;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_entrega", type="date", nullable=true)
     */
    protected $fechaEntrega;

    /**
     * @var \ADIF\ComprasBundle\Entity\Proveedor
     *
     * @ORM\ManyToOne(targetEntity="Proveedor", inversedBy="ordenesCompra")
     * @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id", nullable=false)
     * 
     */
    protected $proveedor;

    /**
     * @var \ADIF\ComprasBundle\Entity\Cotizacion
     *
     * @ORM\ManyToOne(targetEntity="Cotizacion")
     * @ORM\JoinColumn(name="id_cotizacion", referencedColumnName="id", nullable=true)
     * 
     */
    protected $cotizacion;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_carpeta", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número de carpeta no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroCarpeta;

    /**
     * @var \ADIF\ComprasBundle\Entity\TipoContratacion
     *
     * @ORM\ManyToOne(targetEntity="TipoContratacion")
     * @ORM\JoinColumn(name="id_tipo_contratacion", referencedColumnName="id", nullable=true)
     * 
     */
    protected $tipoContratacion;

    /**
     * @ORM\Column(name="id_tipo_pago", type="integer", nullable=true)
     */
    protected $idTipoPago;

    /**
     * @var ADIF\ContableBundle\Entity\TipoPago
     */
    protected $tipoPago;

    /**
     * @ORM\Column(name="id_condicion_pago", type="integer", nullable=true)
     */
    protected $idCondicionPago;

    /**
     * @var ADIF\ContableBundle\Entity\CondicionPago
     */
    protected $condicionPago;

    /**
     * @ORM\Column(name="id_tipo_moneda", type="integer", nullable=true)
     */
    protected $idTipoMoneda;

    /**
     * @var ADIF\ContableBundle\Entity\TipoMoneda
     */
    protected $tipoMoneda;

    /**
     * @ORM\Column(name="id_domicilio_entrega", type="integer", nullable=true)
     */
    protected $idDomicilioEntrega;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Domicilio
     */
    protected $domicilioEntrega;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=4000, nullable=true)
     * @Assert\Length(
     *      max="9000", 
     *      maxMessage="La observación no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $observacion;

    /**
     * 
     * @ORM\OneToMany(targetEntity="RenglonOrdenCompra", mappedBy="ordenCompra", cascade={"persist", "remove"})
     */
    protected $renglones;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_calipso", type="string", length=255, nullable=true)
     */
    protected $numeroCalipso;
	
	/**
     * @var double
     * @ORM\Column(name="saldo_moneda_extranjera", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $saldoMonedaExtranjera;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="es_oc_abierta", type="boolean", nullable=true)
     */
    protected $esOcAbierta;
	
	/**
     * @var double
     * @ORM\Column(name="total_original", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $totalOriginal;
	
	/**
     * @var double
     * @ORM\Column(name="total_actual", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $totalActual;

    /**
     * Constructor
     */
    public function __construct() {

        $this->fechaOrdenCompra = new \DateTime();

        $this->renglones = new ArrayCollection();
    }

    /**
     * Campo a mostrar
     * 
     * @return string
     */
    public function __toString() {
        return $this->getNumeroOrdenCompra() != null ? $this->getNumeroOrdenCompra() : '';
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
     * 
     * @return type
     */
    public function getIdUsuario() {
        return $this->idUsuario;
    }

    /**
     * 
     * @param \ADIF\AutenticacionBundle\Entity\Usuario $usuario
     */
    public function setUsuario($usuario) {

        if (null != $usuario) {
            $this->idUsuario = $usuario->getId();
        } //.
        else {
            $this->idUsuario = null;
        }

        $this->usuario = $usuario;
    }

    /**
     * 
     * @return type
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * Set ordenCompraOriginal
     *
     * @param \ADIF\ComprasBundle\Entity\OrdenCompra $ordenCompraOriginal
     * @return OrdenCompra
     */
    public function setOrdenCompraOriginal(\ADIF\ComprasBundle\Entity\OrdenCompra $ordenCompraOriginal = null) {
        $this->ordenCompraOriginal = $ordenCompraOriginal;

        return $this;
    }

    /**
     * Get ordenCompraOriginal
     *
     * @return \ADIF\ComprasBundle\Entity\OrdenCompra 
     */
    public function getOrdenCompraOriginal() {
        return $this->ordenCompraOriginal;
    }

    /**
     * Set estadoOrdenCompra
     *
     * @param \ADIF\ComprasBundle\Entity\EstadoOrdenCompra $estadoOrdenCompra
     * @return OrdenCompra
     */
    public function setEstadoOrdenCompra(\ADIF\ComprasBundle\Entity\EstadoOrdenCompra $estadoOrdenCompra) {
        $this->estadoOrdenCompra = $estadoOrdenCompra;

        return $this;
    }

    /**
     * Get estadoOrdenCompra
     *
     * @return \ADIF\ComprasBundle\Entity\EstadoOrdenCompra
     */
    public function getEstadoOrdenCompra() {
        return $this->estadoOrdenCompra;
    }

    /**
     * Set fechaOrdenCompra
     *
     * @param \DateTime $fechaOrdenCompra
     * @return OrdenCompra
     */
    public function setFechaOrdenCompra($fechaOrdenCompra) {
        $this->fechaOrdenCompra = $fechaOrdenCompra;

        return $this;
    }

    /**
     * Get fechaOrdenCompra
     *
     * @return \DateTime 
     */
    public function getFechaOrdenCompra() {
        return $this->fechaOrdenCompra;
    }

    /**
     * Set numeroOrdenCompra
     *
     * @param integer $numeroOrdenCompra
     * @return OrdenCompra
     */
    public function setNumeroOrdenCompra($numeroOrdenCompra) {
        $this->numeroOrdenCompra = $numeroOrdenCompra;

        return $this;
    }

    /**
     * Get numeroOrdenCompra
     *
     * @return integer 
     */
    public function getNumeroOrdenCompra() {

        if (null != $this->numeroOrdenCompra) {
            return str_pad($this->numeroOrdenCompra, 8, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Set fechaAnulacion
     *
     * @param \DateTime $fechaAnulacion
     * @return OrdenCompra
     */
    public function setFechaAnulacion($fechaAnulacion) {
        $this->fechaAnulacion = $fechaAnulacion;

        return $this;
    }

    /**
     * Get fechaAnulacion
     *
     * @return \DateTime 
     */
    public function getFechaAnulacion() {
        return $this->fechaAnulacion;
    }

    /**
     * Set motivoAnulacion
     *
     * @param string $motivoAnulacion
     * @return OrdenCompra
     */
    public function setMotivoAnulacion($motivoAnulacion) {
        $this->motivoAnulacion = $motivoAnulacion;

        return $this;
    }

    /**
     * Get motivoAnulacion
     *
     * @return string
     */
    public function getMotivoAnulacion() {
        return $this->motivoAnulacion;
    }

    /**
     * Set fechaEntrega
     *
     * @param \DateTime $fechaEntrega
     * @return OrdenCompra
     */
    public function setFechaEntrega($fechaEntrega) {
        $this->fechaEntrega = $fechaEntrega;

        return $this;
    }

    /**
     * Get fechaEntrega
     *
     * @return \DateTime 
     */
    public function getFechaEntrega() {
        return $this->fechaEntrega;
    }

    /**
     * Set numeroCarpeta
     *
     * @param string $numeroCarpeta
     * @return OrdenCompra
     */
    public function setNumeroCarpeta($numeroCarpeta) {
        $this->numeroCarpeta = $numeroCarpeta;

        return $this;
    }

    /**
     * Get numeroCarpeta
     *
     * @return string 
     */
    public function getNumeroCarpeta() {
        return $this->numeroCarpeta;
    }

    /**
     * 
     * @return type
     */
    public function getIdTipoPago() {
        return $this->idTipoPago;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoPago $tipoPago
     */
    public function setTipoPago($tipoPago) {

        if (null != $tipoPago) {
            $this->idTipoPago = $tipoPago->getId();
        } //.
        else {
            $this->idTipoPago = null;
        }

        $this->tipoPago = $tipoPago;
    }

    /**
     * 
     * @return type
     */
    public function getTipoPago() {
        return $this->tipoPago;
    }

    /**
     * Set idCondicionPago
     *
     * @param integer $idCondicionPago
     * @return OrdenCompra
     */
    public function setIdCondicionPago($idCondicionPago) {
        $this->idCondicionPago = $idCondicionPago;

        return $this;
    }

    /**
     * Get idCondicionPago
     *
     * @return integer 
     */
    public function getIdCondicionPago() {
        return $this->idCondicionPago;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\CondicionPago $condicionPago
     */
    public function setCondicionPago($condicionPago) {

        if (null != $condicionPago) {
            $this->idCondicionPago = $condicionPago->getId();
        } //.
        else {
            $this->idCondicionPago = null;
        }

        $this->condicionPago = $condicionPago;
    }

    /**
     * 
     * @return type
     */
    public function getCondicionPago() {
        return $this->condicionPago;
    }

    /**
     * Set idTipoMoneda
     *
     * @param integer $idTipoMoneda
     * @return OrdenCompra
     */
    public function setIdTipoMoneda($idTipoMoneda) {
        $this->idTipoMoneda = $idTipoMoneda;

        return $this;
    }

    /**
     * Get idTipoMoneda
     *
     * @return integer 
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
        } //.
        else {
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
     * Set idDomicilioEntrega
     *
     * @param integer $idDomicilioEntrega
     * @return OrdenCompra
     */
    public function setIdDomicilioEntrega($idDomicilioEntrega) {
        $this->idDomicilioEntrega = $idDomicilioEntrega;

        return $this;
    }

    /**
     * Get idDomicilioEntrega
     *
     * @return integer 
     */
    public function getIdDomicilioEntrega() {
        return $this->idDomicilioEntrega;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Domicilio $domicilioEntrega
     */
    public function setDomicilioEntrega($domicilioEntrega) {

        if (null != $domicilioEntrega) {
            $this->idDomicilioEntrega = $domicilioEntrega->getId();
        } //.
        else {
            $this->idDomicilioEntrega = null;
        }

        $this->domicilioEntrega = $domicilioEntrega;
    }

    /**
     * 
     * @return type
     */
    public function getDomicilioEntrega() {
        return $this->domicilioEntrega;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return OrdenCompra
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
     * Set proveedor
     *
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     * @return OrdenCompra
     */
    public function setProveedor(\ADIF\ComprasBundle\Entity\Proveedor $proveedor) {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor
     *
     * @return \ADIF\ComprasBundle\Entity\Proveedor 
     */
    public function getProveedor() {
        return $this->proveedor;
    }

    /**
     * Set cotizacion
     *
     * @param \ADIF\ComprasBundle\Entity\Cotizacion $cotizacion
     * @return OrdenCompra
     */
    public function setCotizacion(\ADIF\ComprasBundle\Entity\Cotizacion $cotizacion = null) {
        $this->cotizacion = $cotizacion;

        return $this;
    }

    /**
     * Get cotizacion
     *
     * @return \ADIF\ComprasBundle\Entity\Cotizacion 
     */
    public function getCotizacion() {
        return $this->cotizacion;
    }

    /**
     * Set tipoContratacion
     *
     * @param \ADIF\ComprasBundle\Entity\TipoContratacion $tipoContratacion
     * @return OrdenCompra
     */
    public function setTipoContratacion(\ADIF\ComprasBundle\Entity\TipoContratacion $tipoContratacion) {
        $this->tipoContratacion = $tipoContratacion;

        return $this;
    }

    /**
     * Get tipoContratacion
     *
     * @return \ADIF\ComprasBundle\Entity\TipoContratacion 
     */
    public function getTipoContratacion() {
        return $this->tipoContratacion;
    }

    /**
     * Add renglones
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonOrdenCompra $renglones
     * @return OrdenCompra
     */
    public function addRenglon(\ADIF\ComprasBundle\Entity\RenglonOrdenCompra $renglones) {
        $this->renglones[] = $renglones;

        return $this;
    }

    /**
     * Remove renglones
     *
     * @param \ADIF\ComprasBundle\Entity\RenglonOrdenCompra $renglones
     */
    public function removeRenglon(\ADIF\ComprasBundle\Entity\RenglonOrdenCompra $renglones) {
        $this->renglones->removeElement($renglones);
    }

    /**
     * Get renglones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRenglones() {
	
		return $this->renglones->filter(
		
			function($renglon) {
				
				if (!$renglon->getEsDesglosado()) {
					return true;
				}
			}
		);	        
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getTotalNeto($enMCL = true) {

        $total = 0;

        foreach ($this->getRenglones() as $renglon) {
			$total += $renglon->getMontoNeto($enMCL);
        }

        return $total;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getTotalNetoMasIva($enMCL = true) {

        $total = 0;

        foreach ($this->getRenglones() as $renglon) {
			$total += $renglon->getMontoNetoMasIva($enMCL);
        }

        return $total;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getMonto($enMCL = true) {

        $totalCotizacionSinIva = $this->getTotalNeto($enMCL);

        $total = 0;

        $subtotalAdicionales = 0;

        $ajusteAdicional = 0;

        // Totalizo los montos brutos de los renglones
        foreach ($this->getRenglones() as $renglon) {
            // $total += $renglon->getRestante() > 0 ? $renglon->getPrecioTotal() : 0;
            $total += $renglon->getPrecioTotal($enMCL);
        }

        // Si la OrdenCompra tiene una Cotizacion asociada
        if ($this->getCotizacion() != null) {

            // Por cada adicional elegido en la Cotizacion
            foreach ($this->getCotizacion()->getAdicionalesCotizacion() as $adicional) {

                /* @var $adicional AdicionalCotizacion */

                if ($adicional->getAdicionalElegido()) {

                    $valorNetoAdicional = $adicional->getValor($enMCL);

                    if ($adicional->getTipoValor() == "%") {
                        $valorNetoAdicional *= $totalCotizacionSinIva / 100;
                    }

                    $valorNetoAdicional *= $adicional->getSigno() == '-' ? -1 : 1;

                    $subtotalAdicionales += $valorNetoAdicional;
                }
            }


            // Totalizo los IVA de los adicionales elegidos
            foreach ($this->getAdicionalesElegidos() as $adicional) {

                if ($adicional->getPorcentajeIva() != null && $adicional->getPorcentajeIva() != 0) {

                    $valorNetoAdicional = $adicional->getValor($enMCL);

                    if ($adicional->getTipoValor() == "%") {
                        $valorNetoAdicional *= $totalCotizacionSinIva / 100;
                    }

                    $valorNetoAdicional *= $adicional->getPorcentajeIva() / 100;

                    $valorNetoAdicional *= $adicional->getSigno() == '-' ? -1 : 1;

                    $ajusteAdicional += $valorNetoAdicional;
                } else {
                    if ($adicional->getTipoValor() == "%") {

                        $valorAdicional = $adicional->getValor($enMCL);

                        $ivaRenglones = 0;

                        foreach ($this->getRenglones() as $renglon) {
                            $ivaRenglones += $renglon->getMontoTotalIva($enMCL);
                        }

                        $ivaAdicional = $ivaRenglones * $valorAdicional / 100;
                        $ivaAdicional *= $adicional->getSigno() == '-' ? -1 : 1;
                        $ajusteAdicional += $ivaAdicional;
                    }
                }
            }
        }

        return $total + $subtotalAdicionales + $ajusteAdicional;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getTotalAdicionalesElegidosSinIVA($enMCL = true) {

        $total = 0;

        foreach ($this->getCotizacion()->getAdicionalesCotizacion() as $adicional) {

            if ($adicional->getAdicionalElegido()) {

                if ($adicional->getAlicuotaIva() != null && $adicional->getPorcentajeIva() == 0) {

                    $valorAdicional = $adicional->getValor($enMCL);

                    if ($adicional->getTipoValor() == "%") {
                        $valorAdicional *= $this->getTotalNeto($enMCL) / 100;
                    }

                    $valorAdicional *= $adicional->getSigno() == '-' ? -1 : 1;

                    $total += $valorAdicional;
                }
            }
        }
        return $total;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getTotalAdicionalesElegidos($enMCL = true) {

        $total = 0;

        foreach ($this->getAdicionalesElegidos() as $adicional) {

            $valorAdicional = $adicional->getValor($enMCL);

            if ($adicional->getAlicuotaIva() != null && $adicional->getAlicuotaIva()->getValor() == 0) {

                if ($adicional->getTipoValor() == "%") {
                    $valorAdicional *= $this->getTotalNeto($enMCL) / 100;
                }
            }

            $valorAdicional *= $adicional->getSigno() == '-' ? -1 : 1;

            $total += $valorAdicional;
        }
        return $total;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\RenglonOrdenCompra $renglonOrdenCompra
     * @param type $enMCL
     * @return type
     */
    public function getMontoAdicionalProrrateado(RenglonOrdenCompra $renglonOrdenCompra, $enMCL = true) {

        $totalAdicionalProrrateado = 0;

        foreach ($this->getCotizacion()->getAdicionalesCotizacion() as $adicionalCotizacion) {

            /* @var $adicionalCotizacion AdicionalCotizacion */
            if ($adicionalCotizacion->getAdicionalElegido()) {

                $porcentajeAlicuotaIva = $adicionalCotizacion->getPorcentajeIva();

                // Si el valor es un monto
                if ($adicionalCotizacion->getTipoValor() == "$") {

                    // Si el adicional NO tiene IVA
                    if ($porcentajeAlicuotaIva == 0) {

                        $porcentajeAdicional = $adicionalCotizacion->getValor($enMCL) / $this->getTotalNeto($enMCL);

                        $netoAdicional = $porcentajeAdicional * $renglonOrdenCompra->getMontoNeto($enMCL);

                        //$montoIvaAdicional = $renglonOrdenCompra->getPorcentajeIva() * $netoAdicional / 100;
                        $montoIvaAdicional = 0;

                        $montoASumar = $netoAdicional + $montoIvaAdicional;
                    } else {

                        $porcentajeAdicional = $adicionalCotizacion->getMontoNetoMasIva($enMCL) / $this->getTotalNetoMasIva($enMCL);

                        $montoASumar = $porcentajeAdicional * $renglonOrdenCompra->getMontoNetoMasIva($enMCL);
                    }
                }
                // Sino, si es un porcentaje
                else {

                    // Si el adicional NO tiene IVA
                    if ($porcentajeAlicuotaIva == 0) {

                        $netoAdicional = $adicionalCotizacion->getValor($enMCL) * $renglonOrdenCompra->getMontoNeto($enMCL) / 100;

                        $montoIvaAdicional = $renglonOrdenCompra->getPorcentajeIva() * $netoAdicional / 100;

                        $montoASumar = $netoAdicional + $montoIvaAdicional;
                    } else {
                        $montoASumar = 0;
                    }
                }

                $totalAdicionalProrrateado += $adicionalCotizacion->getSigno() == "-" ? $montoASumar * -1 : $montoASumar;
            }
        }

        return $totalAdicionalProrrateado;
    }

    /**
     * 
     * @return type
     */
    public function getNumero() {
        return $this->getNumeroOrdenCompra();
    }

    /**
     * Retorna los adicionales elegidos para la OC
     * @return ArrayCollection
     */
    public function getAdicionalesElegidos() {

        $adicionalesElegidos = array();

        if ($this->getCotizacion() != null) {
            $adicionalesElegidos = $this->getCotizacion()->getAdicionalesElegidos();
        }

        return $adicionalesElegidos;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getTotalIvaRenglones($enMCL = true) {

        $total = 0;

        foreach ($this->getRenglones() as $renglon) {
            $total += $renglon->getMontoTotalIva($enMCL);
        }

        return $total;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getAjusteAdicionales($enMCL = true) {

        $total = 0;
        $prorrateoAplicado = false;

        // Totalizo los IVA de los adicionales elegidos
        foreach ($this->getAdicionalesElegidos() as $adicional) {

            $valorAdicional = $adicional->getValor($enMCL);

            if ($adicional->getAlicuotaIva() != null && $adicional->getAlicuotaIva()->getValor() != 0) {

                $valorAdicional *= $adicional->getAlicuotaIva()->getValor() / 100;

                $valorAdicional *= $adicional->getSigno() == '-' ? -1 : 1;

                $total += $valorAdicional;
            } elseif (!$prorrateoAplicado && $adicional->getTipoValor() == "%") {

                $prorrateoAplicado = true;

                $adicionalPorcentaje = $this->getTotalAdicionalesElegidosSinIVA($enMCL) / $this->getTotalNeto($enMCL);

                $totalProrrateado = 0;

                foreach ($this->getRenglones() as $renglon) {

                    /* @var $renglon RenglonOrdenCompra */

                    $totalNetoRenglon = $renglon->getMontoNeto($enMCL);

                    $montoNetoConPorcentajeAplicado = $totalNetoRenglon * $adicionalPorcentaje;

                    if ($renglon->getAlicuotaIva() != null) {
                        $totalProrrateado += $renglon->getAlicuotaIva()->getValor() * $montoNetoConPorcentajeAplicado / 100;
                    }
                }

                $total += $totalProrrateado;
            }
        }

        return $total;
    }

    /**
     * 
     * @param type $enMCL
     * @return type
     */
    public function getTotalIva($enMCL = true) {

        return $this->getTotalIvaRenglones($enMCL) + $this->getAjusteAdicionales($enMCL);
    }

    /**
     * 
     * @return type
     */
    public function getCantidadRestanteTotal() {

        $cantidadRestante = 0;

        foreach ($this->getRenglones() as $renglon) {
            $cantidadRestante += $renglon->getRestante();
        }

        return $cantidadRestante;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsServicio() {

        $servicio = false;

        if ($this->getRenglones() != null) {
            if ($this->renglones[0]->getRenglonCotizacion() == null) {
                $servicio = true;
            }
        }

        return $servicio;
    }

    /**
     * Get requerimiento
     */
    public function getRequerimiento() {

        if ($this->cotizacion != null) {
            return $this->cotizacion->getRequerimiento();
        }

        return null;
    }

    /**
     * Get muestraReporteDesvio
     * 
     * @return boolean
     */
    public function getMuestraReporteDesvio() {

        if ($this->cotizacion != null) {
            return !$this->getEsServicio() //
                    && $this->getRequerimiento() != null //
                    && $this->estadoOrdenCompra->getDenominacionEstado() != ConstanteEstadoOrdenCompra::ESTADO_OC_BORRADOR;
        }

        return false;
    }

    /**
     * Set numeroCalipso
     *
     * @param integer $numeroCalipso
     * @return OrdenCompra
     */
    public function setNumeroCalipso($numeroCalipso) {
        $this->numeroCalipso = $numeroCalipso;

        return $this;
    }

    /**
     * Get numeroCalipso
     *
     * @return integer 
     */
    public function getNumeroCalipso() {
        return $this->numeroCalipso;
    }

    /**
     * 
     * @return type
     */
    public function getTipoCambio() {

        $tipoCambio = 1;

        if ($this->getTipoMoneda() != null) {

            $tipoCambio = $this->getTipoMoneda()->getTipoCambio();
        }

        return $tipoCambio;
    }

    /**
     * Get simboloTipoMoneda
     *
     * @return string 
     */
    public function getSimboloTipoMoneda() {

        $tipoMoneda = "$";

        if ($this->idTipoMoneda != null) {

            $tipoMoneda = $this->getTipoMoneda()->getSimboloTipoMoneda();
        }

        return $tipoMoneda;
    }
    
    /**
     * Get simboloTipoMoneda
     *
     * @return string 
     */
    public function getDenomincacionTipoMoneda() {

        $denomincacion = "Peso Argentino";

        if ($this->idTipoMoneda != null) {

            $denomincacion = $this->getTipoMoneda()->getDenominacionTipoMoneda();
        }

        return $denomincacion;
    }

    /**
     * 
     * @return type
     */
    public function getEsBorrador() {

        return $this->estadoOrdenCompra->getDenominacionEstado() == ConstanteEstadoOrdenCompra::ESTADO_OC_BORRADOR;
    }

    /**
     * 
     * @return boolean
     */
    public function getEstaAnulada() {

        $denominacionEstado = $this->estadoOrdenCompra->getDenominacionEstado();

        return $denominacionEstado == ConstanteEstadoOrdenCompra::ESTADO_OC_ANULADA;
    }
	
	/**
     * Set saldoMonedaExtranjera
     *
     * @param string $saldoMonedaExtranjera
     * @return ComprobanteVenta
     */
    public function setSaldoMonedaExtranjera($saldoMonedaExtranjera) {
        $this->saldoMonedaExtranjera = $saldoMonedaExtranjera;

        return $this;
    }

    /**
     * Get saldoMonedaExtranjera
     *
     * @return string 
     */
    public function getSaldoMonedaExtranjera() {
        return $this->saldoMonedaExtranjera;
    }
	
	public function setEsOcAbierta($esOcAbierta)
	{
		$this->esOcAbierta = $esOcAbierta;
		
		return $this;
	}
	
	public function getEsOcAbierta()
	{
		return $this->esOcAbierta;
	}
	
	public function setTotalOriginal($totalOriginal)
	{
		$this->totalOriginal = $totalOriginal;
		
		return $this;
	}
	
	public function getTotalOriginal()
	{
		if ($this->totalOriginal == null || $this->totalOriginal == 0) {
			return $this->getMonto(true);
		} else {
			return $this->totalOriginal;
		}
	}
	
	public function setTotalActual($totalActual)
	{
		$this->totalActual = $totalActual;
		
		return $this;
	}
	
	public function getTotalActual()
	{
		return $this->totalActual;
	}
}
