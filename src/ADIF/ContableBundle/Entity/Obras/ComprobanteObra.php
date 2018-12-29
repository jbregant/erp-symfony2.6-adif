<?php

namespace ADIF\ContableBundle\Entity\Obras;

use ADIF\ContableBundle\Entity\Comprobante;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ComprobanteObra
 *
 * @author Esteban Primost
 * created 13/11/2014
 * 
 * @ORM\Table(name="comprobante_obra")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteObraRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "comprobante_obra" = "ComprobanteObra",
 *      "nota_credito_obra" = "NotaCreditoObra",
 *      "nota_debito_obra" = "NotaDebitoObra",
 *      "nota_debito_interes_obra" = "NotaDebitoInteresObra",
 *      "ticket_factura_obra" = "TicketFacturaObra",
 *      "recibo_obra" = "ReciboObra",
 *      "factura_obra" = "FacturaObra"
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
class ComprobanteObra extends Comprobante {

    /**
     * @var string
     *
     * @ORM\Column(name="punto_venta", type="string", length=4, nullable=true)
     */
    protected $puntoVenta;

    /**
     * @var DocumentoFinanciero
     *
     * @ORM\ManyToOne(targetEntity="DocumentoFinanciero", inversedBy="comprobantes", cascade={"persist"})
     * @ORM\JoinColumn(name="id_documento_financiero", referencedColumnName="id")
     * 
     */
    protected $documentoFinanciero;

    /**
     * @ORM\ManyToOne(targetEntity="OrdenPagoObra", inversedBy="comprobantes")
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id", nullable=true)
     * */
    protected $ordenPago;

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
     * @ORM\Column(name="fecha_anulacion", type="datetime", nullable=true)
     */
    protected $fechaAnulacion;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_ingreso_adif", type="date", nullable=false)
     */
    protected $fechaIngresoADIF;

    /**
     * Set documentoFinanciero
     *
     * @param DocumentoFinanciero $documentoFinanciero
     * @return DocumentoFinancieroArchivo
     */
    public function setDocumentoFinanciero(DocumentoFinanciero $documentoFinanciero = null) {
        $this->documentoFinanciero = $documentoFinanciero;

        return $this;
    }

    /**
     * Get documentoFinanciero
     *
     * @return DocumentoFinanciero 
     */
    public function getDocumentoFinanciero() {
        return $this->documentoFinanciero;
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
     * @param OrdenPagoObra $ordenPago
     * @return ComprobanteObra
     */
    public function setOrdenPago(OrdenPagoObra $ordenPago = null) {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return OrdenPagoObra 
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }

    /**
     * Get ordenPagoSinAnular
     *
     * @return OrdenPagoObra 
     */
    public function getOrdenPagoSinAnular() {

        $ordenPago = null;

        if ($this->ordenPago != null && $this->ordenPago->getEstadoOrdenPago() != ConstanteEstadoOrdenPago::ESTADO_ANULADA) {
            $ordenPago = $this->ordenPago;
        }

        return $ordenPago;
    }

    /**
     * Get ordenesPago
     * 
     * @return type
     */
    public function getOrdenesPagoSinAnular() {

        $ordenesPago = [];

        foreach ($this->pagosParciales as $pagoParcial) {

            if (!$pagoParcial->getAnulado()) {

                $ordenPago = $pagoParcial->getOrdenPago();

                if ($ordenPago != null && !$ordenPago->getEstaAnulada()) {

                    $ordenesPago[] = $ordenPago;
                }
            }
        }

        if ($this->getOrdenPagoSinAnular() != null) {
            $ordenesPago[] = $this->getOrdenPagoSinAnular();
        }

        return $ordenesPago;
    }

    /**
     * Set idProveedor
     *
     * @param integer $idProveedor
     * @return ComprobanteObra
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
     * 
     * @return type
     */
    public function getAdicionales() {
        return new ArrayCollection();
    }

    /**
     * 
     * @return string
     */
    public function getPath() {
        return 'comprobanteobra';
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
        return $this->getProveedor();
    }

    /**
     * 
     * @return boolean
     */
    public function getEsComprobanteObra() {
        return true;
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
     * Get tramo
     * 
     * @return type
     */
    public function getTramo() {
		if ($this->documentoFinanciero != null) {
			return $this->documentoFinanciero->getTramo(); 
		}
    }

    /**
     * Get fechaIngresoADIF
     * 
     * @return type
     */
    public function getFechaIngresoADIFDocumentoFinanciero() {
		if ($this->documentoFinanciero != null) {
			return $this->documentoFinanciero->getFechaIngresoADIF();
		}
    }

    /**
     * Set fechaAnulacion
     *
     * @param \DateTime $fechaAnulacion
     * @return ComprobanteObra
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
     * @return type
     */
    public function getCondicionImpositivaIVACompras() {
        return $this->getBeneficiarioIVACompras()->getDatosImpositivos()->getCondicionIVA()->getDenominacionTipoResponsable();
    }

    /**
     * 
     * @return boolean
     */
    public function isEqualTo(ComprobanteObra $comprobante) {
        return $this->getNumero() == $comprobante->getNumero() //
                && $this->getPuntoVenta() == $comprobante->getPuntoVenta() //
                && $this->getLetraComprobante() == $comprobante->getLetraComprobante() //
                && $this->getTipoComprobante() == $comprobante->getTipoComprobante() //
                && $this->getProveedor() == $comprobante->getProveedor();
    }
    
    /**
     * Set fechaIngresoADIF
     *
     * @param \DateTime $fechaIngresoADIF 
     * @return ComprobanteObra
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

}
