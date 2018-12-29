<?php

namespace ADIF\ContableBundle\Entity\Consultoria;

use ADIF\ContableBundle\Entity\Comprobante;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ComprobanteConsultoria
 *
 * @author Manuel Becerra
 * created 05/03/2015
 * 
 * @ORM\Table(name="comprobante_consultoria")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteConsultoriaRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "factura_consultoria" = "ADIF\ContableBundle\Entity\Consultoria\FacturaConsultoria",
 *      "nota_credito_consultoria" = "ADIF\ContableBundle\Entity\Consultoria\NotaCreditoConsultoria",
 *      "recibo_consultoria" = "ADIF\ContableBundle\Entity\Consultoria\ReciboConsultoria"
 * })
 * @UniqueEntity(
 *      fields={"contrato", "fechaComprobante", "letraComprobante", "puntoVenta", "numero", "tipoComprobante"},
 *      message="El número de comprobante ya se encuentra en uso.",
 *      repositoryMethod="validarNumeroComprobanteUnico"
 * )
 */
class ComprobanteConsultoria extends Comprobante {

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria", inversedBy="comprobantesConsultoria")
     * @ORM\JoinColumn(name="id_contrato", referencedColumnName="id", nullable=true)
     */
    protected $contrato;

    /**
     * @var string
     *
     * @ORM\Column(name="punto_venta", type="string", length=4, nullable=true)
     */
    protected $puntoVenta;

    /**
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria", inversedBy="comprobantes")
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id", nullable=true)
     * */
    protected $ordenPago;

    /**
     * @var CicloFacturacion
     *
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion")
     * @ORM\JoinColumn(name="id_ciclo_facturacion", referencedColumnName="id", nullable=true)
     */
    protected $cicloFacturacion;

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
     * @var string
     *
     * @ORM\Column(name="periodo", type="string", nullable=true)
     */
    protected $periodo;

    /**
     * Set contrato
     *
     * @param \ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria $contrato
     * @return ComprobanteConsultoria
     */
    public function setContrato(\ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria $contrato) {
        $this->contrato = $contrato;

        return $this;
    }

    /**
     * Get contrato
     *
     * @return \ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria 
     */
    public function getContrato() {
        return $this->contrato;
    }

    /**
     * Set puntoVenta
     *
     * @param string $puntoVenta
     * @return ComprobanteConsultoria
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
     * @param \ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria $ordenPago
     * @return ComprobanteConsultoria
     */
    public function setOrdenPago(\ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria $ordenPago = null) {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return \ADIF\ContableBundle\Entity\Consultoria\OrdenPagoConsultoria 
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }

    /**
     * Set cicloFacturacion
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion $cicloFacturacion
     * @return ComprobanteConsultoria
     */
    public function setCicloFacturacion(\ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion $cicloFacturacion) {
        $this->cicloFacturacion = $cicloFacturacion;

        return $this;
    }

    /**
     * Get cicloFacturacion
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion 
     */
    public function getCicloFacturacion() {
        return $this->cicloFacturacion;
    }

    /**
     * Set fechaIngresoADIF
     *
     * @param \DateTime $fechaIngresoADIF
     * @return ComprobanteConsultoria
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
     * Get consultor
     */
    public function getConsultor() {
        return $this->contrato->getConsultor();
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
     * @return type
     */
    public function getNumeroCompleto() {

        return $this->puntoVenta . '-' . $this->numero;
    }

    public function getBeneficiarioIVACompras() {
        return $this->getConsultor();
    }

    public function getPath() {

        return 'comprobante_consultoria';
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
     * Set fechaAnulacion
     *
     * @param \DateTime $fechaAnulacion
     * @return ComprobanteConsultoria
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
     * Set periodo
     *
     * @param string $periodo
     *
     * @return ComprobanteConsultoria
     */
    public function setPeriodo($periodo) {
        $this->periodo = $periodo;

        return $this;
    }

    /**
     * Get periodo
     *
     * @return string
     */
    public function getPeriodo() {
        return $this->periodo;
    }

    /**
     * 
     * @return type
     */
    public function getCondicionImpositivaIVACompras() {
        return $this->getBeneficiarioIVACompras()->getDatosImpositivos()->getCondicionIVA()->getDenominacionTipoResponsable();
    }

}
