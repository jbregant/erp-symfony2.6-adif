<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\ContableBundle\Entity\RenglonComprobanteCompra;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ComprobanteCompra
 *
 * @author Darío Rapetti
 * created 21/10/2014
 * 
 * @ORM\Table(name="comprobante_compra")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteCompraRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "factura" = "Factura",
 *      "ticket_factura" = "TicketFactura",
 *      "recibo" = "Recibo",
 *      "nota_debito" = "NotaDebito",
 *      "nota_credito" = "NotaCredito",
 *      "anticipo_proveedor" = "AnticipoProveedor"
 * })
 * @UniqueEntity(
 *      fields={"id", "fechaComprobante", "letraComprobante", "puntoVenta", "numero", "idProveedor", "tipoComprobante"},
 *      message="El número de comprobante ya se encuentra en uso.",
 *      repositoryMethod="validarNumeroComprobanteUnico"
 * )
 * @UniqueEntity(
 *      fields={"letraComprobante", "puntoVenta", "numero", "idProveedor", "tipoComprobante"},
 *      message="El comprobante ya se encuentra cargado en Calipso.",
 *      repositoryMethod="validarComprobanteCalipso",
 *      groups={"create"}
 * )
 * @UniqueEntity(
 *      fields={"fechaComprobante", "letraComprobante", "puntoVenta", "numero", "idProveedor", "tipoComprobante"},
 *      message="El número de comprobante ya se encuentra en uso.",
 *      repositoryMethod="validarNumeroComprobanteUnico",
 *      groups={"create"}
 * )
 */
class ComprobanteCompra extends Comprobante {

    /**
     * @var string
     *
     * @ORM\Column(name="punto_venta", type="string", length=4, nullable=true)
     */
    protected $puntoVenta;

    /**
     * @ORM\Column(name="id_orden_compra", type="integer", nullable=true)
     */
    protected $idOrdenCompra;

    /**
     * @var ADIF\ComprasBundle\Entity\OrdenCompra
     */
    protected $ordenCompra;

    /**
     * @ORM\ManyToOne(targetEntity="OrdenPagoComprobante", inversedBy="comprobantes")
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id", nullable=true)
     * */
    protected $ordenPago;

    /**
     *
     * @var AdicionalComprobanteCompra
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\AdicionalComprobanteCompra", mappedBy="comprobanteCompra", cascade={"all"})
     */
    protected $adicionales;

    /**
     * @ORM\Column(name="id_proveedor", type="integer", nullable=false)
     */
    protected $idProveedor;

    /**
     * @var ADIF\ComprasBundle\Entity\Proveedor
     */
    protected $proveedor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_ingreso_adif", type="datetime", nullable=false)
     */
    protected $fechaIngresoADIF;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_anulacion", type="datetime", nullable=true)
     */
    protected $fechaAnulacion;
	
	/**
     * @var double
     * @ORM\Column(name="total_oc_moneda_extranjera", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $totalOcMonedaExtranjera;
	
	/**
     * @var double
     * @ORM\Column(name="total_moneda_extranjera", type="decimal", precision=15, scale=2, nullable=true)
     */
    protected $totalMonedaExtranjera;

    /**
     * 
     */
    public function __construct() {

        parent::__construct();

        $this->adicionales = new ArrayCollection();
    }

    /**
     * Set puntoVenta
     *
     * @param string $puntoVenta
     * @return ComprobanteCompra
     */
    public function setPuntoVenta($puntoVenta) {
        $this->puntoVenta = $puntoVenta;

        return $this;
    }

    /**
     * Get puntoVenta
     *
     * @return string 
     */
    public function getPuntoVenta() {
        return $this->puntoVenta;
    }

    /**
     * Set ordenPago
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPagoComprobante $ordenPago
     * @return ComprobanteCompra
     */
    public function setOrdenPago(\ADIF\ContableBundle\Entity\OrdenPagoComprobante $ordenPago = null) {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return \ADIF\ContableBundle\Entity\OrdenPagoComprobante 
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }

    /**
     * Set renglonesComprobante
     *
     * @param \Doctrine\Common\Collections\ArrayCollection 
     * @return ComprobanteCompra
     */
    public function setRenglonesComprobante(ArrayCollection $renglonesComprobante) {
        $this->renglonesComprobante = $renglonesComprobante;

        return $this;
    }

    /**
     * Set idOrdenCompra
     *
     * @param integer $idOrdenCompra
     * @return ComprobanteCompra
     */
    public function setIdOrdenCompra($idOrdenCompra) {
        $this->idOrdenCompra = $idOrdenCompra;

        return $this;
    }

    /**
     * Get idOrdenCompra
     *
     * @return integer 
     */
    public function getIdOrdenCompra() {
        return $this->idOrdenCompra;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\OrdenCompra $ordenCompra
     */
    public function setOrdenCompra($ordenCompra) {

        if (null != $ordenCompra) {
            $this->idOrdenCompra = $ordenCompra->getId();
        } //.
        else {
            $this->idOrdenCompra = null;
        }

        $this->ordenCompra = $ordenCompra;
    }

    /**
     * 
     * @return type
     */
    public function getOrdenCompra() {
        return $this->ordenCompra;
    }

    /**
     * 
     * @return type
     */
    public function __toString() {
        return '' . $this->id . '';
    }

    /**
     * Get renglonesComprobante
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRenglonesComprobante() {
        return $this->renglonesComprobante;
    }

    /**
     * 
     * @return boolean
     */
    public function getEstaCanceladoParcialmente() {
        return $this->ordenPago !== null;
    }

    /**
     * Add adicionales
     *
     * @param \ADIF\ContableBundle\Entity\AdicionalComprobanteCompra $adicionales
     * @return ComprobanteCompra
     */
    public function addAdicionale(\ADIF\ContableBundle\Entity\AdicionalComprobanteCompra $adicionales) {
        $this->adicionales[] = $adicionales;
        $adicionales->setComprobanteCompra($this);
        return $this;
    }

    /**
     * Remove adicionales
     *
     * @param \ADIF\ContableBundle\Entity\AdicionalComprobanteCompra $adicionales
     */
    public function removeAdicionale(\ADIF\ContableBundle\Entity\AdicionalComprobanteCompra $adicionales) {
        $this->adicionales->removeElement($adicionales);
        $adicionales->setComprobanteCompra(null);
    }

    /**
     * Get adicionales
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdicionales() {
        return $this->adicionales;
    }

    /**
     * 
     * @return type
     */
    public function getTotalIva() {
        $total = 0;

        foreach ($this->renglonesComprobante as $renglon) {
            $total += $renglon->getMontoIva();
        }

        return $total;
    }

    /**
     * 
     * @return type
     */
    public function getTotalNetoMasIva() {
        return $this->getTotalNeto() + $this->getTotalIva();
    }

    /**
     * 
     * @param RenglonComprobanteCompra $renglonComprobanteCompra
     * @param type $incluirSoloAdicionales
     * @return type
     */
    public function getMontoAdicionalProrrateado(RenglonComprobanteCompra $renglonComprobanteCompra, $incluirSoloAdicionales = false) {

        $totalAdicionalProrrateado = 0;

        $totalPercepcionesProrrateado = 0;
        $totalImpuestosProrrateado = 0;

        foreach ($this->getAdicionales() as $adicional) {

            /* @var $adicional AdicionalComprobanteCompra */

            $porcentajeAlicuotaIva = $adicional->getPorcentajeIvaReal();

            // Si el valor es un monto
            if ($adicional->getTipoValor() == "$") {
                // Si el adicional NO tiene IVA
                if ($porcentajeAlicuotaIva == 0) {
                    $porcentajeAdicional = $adicional->getValor() / $this->getTotalNeto();
                    $netoAdicional = $porcentajeAdicional * $renglonComprobanteCompra->getMontoNeto();
                    $montoIvaAdicional = 0;
                    $montoASumar = $netoAdicional + $montoIvaAdicional;
                } else {
                    $porcentajeAdicional = $adicional->getMontoNetoMasIva() / $this->getTotalNetoMasIva();
                    $montoASumar = $porcentajeAdicional * $renglonComprobanteCompra->getMontoBruto();
                }
            } else {
                // Si el adicional NO tiene IVA
                if ($porcentajeAlicuotaIva == 0) {

                    $montoIvaAdicional = 0;

                    $netoAdicional = $adicional->getValor() * $renglonComprobanteCompra->getMontoNeto() / 100;

                    if (!$incluirSoloAdicionales) {
                        $montoIvaAdicional = $renglonComprobanteCompra->getPorcentajeIvaReal() * $netoAdicional / 100;
                    }

                    $montoASumar = $netoAdicional + $montoIvaAdicional;
                } else {
                    $montoASumar = 0;
                }
            }

            $totalAdicionalProrrateado += $adicional->getSigno() == "-" ? $montoASumar * -1 : $montoASumar;
        }

        if (!$incluirSoloAdicionales) {

            //Prorateo las percepciones e impuestos
            $porcentajePercepciones = $this->getImporteTotalPercepcion() / $this->getTotalNeto();
            $totalPercepcionesProrrateado = $porcentajePercepciones * $renglonComprobanteCompra->getMontoNeto();

            $porcentajeImpuestos = $this->getImporteTotalImpuesto() / $this->getTotalNeto();
            $totalImpuestosProrrateado = $porcentajeImpuestos * $renglonComprobanteCompra->getMontoNeto();
        }

        return $totalAdicionalProrrateado + $totalPercepcionesProrrateado + $totalImpuestosProrrateado;
    }

    /**
     * Set idProveedor
     *
     * @param integer $idProveedor
     * @return ComprobanteCompra
     */
    public function setIdProveedor($idProveedor) {
        $this->idProveedor = $idProveedor;

        return $this;
    }

    /**
     * Get idProveedor
     *
     * @return integer 
     */
    public function getIdProveedor() {
        return $this->idProveedor;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     */
    public function setProveedor($proveedor) {

        if (null != $proveedor) {
            $this->idProveedor = $proveedor->getId();
        } else {
            $this->idProveedor = null;
        }

        $this->proveedor = $proveedor;
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return $this->proveedor;
    }

    /**
     * Set fechaIngresoADIF
     *
     * @param \DateTime $fechaIngresoADIF
     * @return ComprobanteCompra
     */
    public function setFechaIngresoADIF($fechaIngresoADIF) {
        $this->fechaIngresoADIF = $fechaIngresoADIF;

        return $this;
    }

    /**
     * Get fechaIngresoADIF
     *
     * @return \DateTime 
     */
    public function getFechaIngresoADIF() {
        return $this->fechaIngresoADIF;
    }

    /**
     * 
     * @return type
     */
    public function getNumeroCompleto() {

        return $this->puntoVenta . '-' . $this->numero;
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiarioIVACompras() {
        return $this->getProveedor()->getClienteProveedor();
    }

    /**
     * 
     * @return string
     */
    public function getPath() {

        return 'comprobantes_compra';
    }

    /**
     * Set fechaAnulacion
     *
     * @param \DateTime $fechaAnulacion
     * @return ComprobanteCompra
     */
    public function setFechaAnulacion($fechaAnulacion) {
        $this->fechaAnulacion = $fechaAnulacion;

        return $this;
    }

    /**
     * Get fechaVencimiento
     *
     * @return \DateTime 
     */
    public function getFechaAnulacion() {
        return $this->fechaAnulacion;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsComprobanteCompra() {
        return true;
    }

    /**
     * 
     * @return boolean
     */
    public function getEsComprobanteServicio() {

        $esComprobanteServicio = false;

        if ($this->idOrdenCompra != null) {

            /* @var $ordenCompra \ADIF\ComprasBundle\Entity\OrdenCompra */
            $ordenCompra = $this->getOrdenCompra();

            foreach ($ordenCompra->getRenglones() as $renglonOrdenCompra) {

                if ($renglonOrdenCompra->getRenglonCotizacion() == null) {

                    $esComprobanteServicio = true;

                    break;
                }
            }
        }

        return $esComprobanteServicio;
    }

    /**
     * 
     * @return type
     */
    public function getCondicionImpositivaIVACompras() {
        return $this->getBeneficiarioIVACompras()->getDatosImpositivos()->getCondicionIVA()->getDenominacionTipoResponsable();
    }

    /**
     * 
     * @return boolean
     */
    public function isEqualTo(ComprobanteCompra $comprobante) {
        return $this->getNumero() == $comprobante->getNumero() //
                && $this->getPuntoVenta() == $comprobante->getPuntoVenta() //
                && $this->getLetraComprobante() == $comprobante->getLetraComprobante() //
                && $this->getTipoComprobante() == $comprobante->getTipoComprobante() //
                && $this->getProveedor() == $comprobante->getProveedor();
    }
	
	/**
     * Set totalOcMonedaExtranjera
     *
     * @param string $totalOcMonedaExtranjera
     * @return ComprobanteVenta
     */
    public function setTotalOcMonedaExtranjera($totalOcMonedaExtranjera) {
        $this->totalOcMonedaExtranjera = $totalOcMonedaExtranjera;

        return $this;
    }

    /**
     * Get totalOcMonedaExtranjera
     *
     * @return string 
     */
    public function getTotalOcMonedaExtranjera() {
        return $this->totalOcMonedaExtranjera;
    }
	
	/**
     * Set totalOcMonedaExtranjera
     *
     * @param string $totalOcMonedaExtranjera
     * @return ComprobanteVenta
     */
    public function setTotalMonedaExtranjera($totalMonedaExtranjera) {
        $this->totalMonedaExtranjera = $totalMonedaExtranjera;

        return $this;
    }

    /**
     * Get totalOcMonedaExtranjera
     *
     * @return string 
     */
    public function getTotalMonedaExtranjera() {
        return $this->totalMonedaExtranjera;
    }

}
