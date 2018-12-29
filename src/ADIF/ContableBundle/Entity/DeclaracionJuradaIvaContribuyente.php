<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * DeclaracionJuradaIvaContribuyente
 *
 * @author Darío Rapetti
 * created 09/06/2015
 * 
 * @ORM\Table(name="declaracion_jurada_iva_contribuyente")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\DeclaracionJuradaIvaContribuyenteRepository")
 */
class DeclaracionJuradaIvaContribuyente extends DeclaracionJuradaContribuyente {
    
    /**
     * @ORM\OneToOne(targetEntity="OrdenPagoDeclaracionJuradaIvaContribuyente", mappedBy="declaracionJuradaIvaContribuyente")
     * */
    protected $ordenPago;
    
    /**
     * @var string
     * @ORM\Column(name="monto_debito_fiscal", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoDebitoFiscal;

    /**
     * @var string
     * @ORM\Column(name="monto_credito_fiscal", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoCreditoFiscal;

    /**
     * @var string
     * @ORM\Column(name="monto_retenciones_iva", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoRetencionesIva;

    /**
     * @var string
     * @ORM\Column(name="monto_percepciones_iva", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoPercepcionesIva;

    /**
     * @var string
     * @ORM\Column(name="monto_total_facturado", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoTotalFacturado;

    /**
     * @var string
     * @ORM\Column(name="monto_gravado_facturado", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoGravadoFacturado;

    /**
     * @var string
     * @ORM\Column(name="monto_iva_105", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoIva105;

    /**
     * @var string
     * @ORM\Column(name="monto_iva_21", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoIva21;

    /**
     * @var string
     * @ORM\Column(name="monto_iva_27", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoIva27;

    /**
     * Set montoDebitoFiscal
     *
     * @param string $montoDebitoFiscal
     * @return DeclaracionJuradaIvaContribuyente
     */
    public function setMontoDebitoFiscal($montoDebitoFiscal) {
        $this->montoDebitoFiscal = $montoDebitoFiscal;

        return $this;
    }

    /**
     * Get montoDebitoFiscal
     *
     * @return string 
     */
    public function getMontoDebitoFiscal() {
        return $this->montoDebitoFiscal;
    }

    /**
     * Set montoCreditoFiscal
     *
     * @param string $montoCreditoFiscal
     * @return DeclaracionJuradaIvaContribuyente
     */
    public function setMontoCreditoFiscal($montoCreditoFiscal) {
        $this->montoCreditoFiscal = $montoCreditoFiscal;

        return $this;
    }

    /**
     * Get montoCreditoFiscal
     *
     * @return string 
     */
    public function getMontoCreditoFiscal() {
        return $this->montoCreditoFiscal;
    }

    /**
     * Set montoRetencionesIva
     *
     * @param string $montoRetencionesIva
     * @return DeclaracionJuradaIvaContribuyente
     */
    public function setMontoRetencionesIva($montoRetencionesIva) {
        $this->montoRetencionesIva = $montoRetencionesIva;

        return $this;
    }

    /**
     * Get montoRetencionesIva
     *
     * @return string 
     */
    public function getMontoRetencionesIva() {
        return $this->montoRetencionesIva;
    }

    /**
     * Set montoPercepcionesIva
     *
     * @param string $montoPercepcionesIva
     * @return DeclaracionJuradaIvaContribuyente
     */
    public function setMontoPercepcionesIva($montoPercepcionesIva) {
        $this->montoPercepcionesIva = $montoPercepcionesIva;

        return $this;
    }

    /**
     * Get montoPercepcionesIva
     *
     * @return string 
     */
    public function getMontoPercepcionesIva() {
        return $this->montoPercepcionesIva;
    }

    /**
     * Set montoTotalFacturado
     *
     * @param string $montoTotalFacturado
     * @return DeclaracionJuradaIvaContribuyente
     */
    public function setMontoTotalFacturado($montoTotalFacturado) {
        $this->montoTotalFacturado = $montoTotalFacturado;

        return $this;
    }

    /**
     * Get montoTotalFacturado
     *
     * @return string 
     */
    public function getMontoTotalFacturado() {
        return $this->montoTotalFacturado;
    }

    /**
     * Set montoGravadoFacturado
     *
     * @param string $montoGravadoFacturado
     * @return DeclaracionJuradaIvaContribuyente
     */
    public function setMontoGravadoFacturado($montoGravadoFacturado) {
        $this->montoGravadoFacturado = $montoGravadoFacturado;

        return $this;
    }

    /**
     * Get montoGravadoFacturado
     *
     * @return string 
     */
    public function getMontoGravadoFacturado() {
        return $this->montoGravadoFacturado;
    }

    /**
     * Set montoIva105
     *
     * @param string $montoIva105
     * @return DeclaracionJuradaIvaContribuyente
     */
    public function setMontoIva105($montoIva105) {
        $this->montoIva105 = $montoIva105;

        return $this;
    }

    /**
     * Get montoIva105
     *
     * @return string 
     */
    public function getMontoIva105() {
        return $this->montoIva105;
    }

    /**
     * Set montoIva21
     *
     * @param string $montoIva21
     * @return DeclaracionJuradaIvaContribuyente
     */
    public function setMontoIva21($montoIva21) {
        $this->montoIva21 = $montoIva21;

        return $this;
    }

    /**
     * Get montoIva21
     *
     * @return string 
     */
    public function getMontoIva21() {
        return $this->montoIva21;
    }

    /**
     * Set montoIva27
     *
     * @param string $montoIva27
     * @return DeclaracionJuradaIvaContribuyente
     */
    public function setMontoIva27($montoIva27) {
        $this->montoIva27 = $montoIva27;

        return $this;
    }

    /**
     * Get montoIva27
     *
     * @return string 
     */
    public function getMontoIva27() {
        return $this->montoIva27;
    } 
    
    public function getTotalRetencionesYPercepciones(){
        return $this->getMontoRetencionesIva() + $this->getMontoPercepcionesIva();
    }
    
    public function getCoeficienteProrrateo(){
        return round($this->getMontoGravadoFacturado() / ($this->getMontoTotalFacturado() == 0 ? 1 : $this->getMontoTotalFacturado()), 2);
    }
    
    public function getTotalIva(){
        return round($this->getMontoIva105() + $this->getMontoIva21() + $this->getMontoIva27(), 2);
    }
    
    public function getIvaCFComputable(){
        return round($this->getTotalIva() * $this->getCoeficienteProrrateo(), 2);
    }
    
    public function getIvaCFNoComputable(){
        return round($this->getTotalIva() - $this->getIvaCFComputable(), 2);
    }

}
