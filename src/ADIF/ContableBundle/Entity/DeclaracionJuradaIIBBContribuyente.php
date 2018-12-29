<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeclaracionJuradaIIBBContribuyente
 *
 * @author Darío Rapetti
 * created 09/06/2015
 * 
 * @ORM\Table(name="declaracion_jurada_iibb_contribuyente")
 * @ORM\Entity
 */
class DeclaracionJuradaIIBBContribuyente extends DeclaracionJuradaContribuyente {

    /**
     * @ORM\OneToOne(targetEntity="OrdenPagoDeclaracionJuradaIIBBContribuyente", mappedBy="declaracionJuradaIIBBContribuyente")
     * */
    protected $ordenPago;

    /**
     * @var string
     * @ORM\Column(name="monto_ingresos_gravados", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoIngresosGravados;

    /**
     * @var string
     * @ORM\Column(name="monto_ingresos_no_gravados", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoIngresosNoGravados;

    /**
     * @var string
     * @ORM\Column(name="monto_ingresos_exentos", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoIngresosExentos;

    /**
     * @var string
     * @ORM\Column(name="monto_impuesto_determinado", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoImpuestoDeterminado;

    /**
     * @var string
     * @ORM\Column(name="monto_retenciones_iibb", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoRetencionesIIBB;

    /**
     * @var string
     * @ORM\Column(name="monto_percepciones_iibb", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoPercepcionesIIBB;

    /**
     * @var string
     * @ORM\Column(name="monto_pagos_a_cuenta", type="decimal", precision=10, scale=2, nullable=false)
     * 
     */
    private $montoPagosACuenta;

    /**
     * Set montoIngresosGravados
     *
     * @param string $montoIngresosGravados
     * @return DeclaracionJuradaIIBBContribuyente
     */
    public function setMontoIngresosGravados($montoIngresosGravados) {
        $this->montoIngresosGravados = $montoIngresosGravados;

        return $this;
    }

    /**
     * Get montoIngresosGravados
     *
     * @return string 
     */
    public function getMontoIngresosGravados() {
        return $this->montoIngresosGravados;
    }

    /**
     * Set montoIngresosNoGravados
     *
     * @param string $montoIngresosNoGravados
     * @return DeclaracionJuradaIIBBContribuyente
     */
    public function setMontoIngresosNoGravados($montoIngresosNoGravados) {
        $this->montoIngresosNoGravados = $montoIngresosNoGravados;

        return $this;
    }

    /**
     * Get montoIngresosNoGravados
     *
     * @return string 
     */
    public function getMontoIngresosNoGravados() {
        return $this->montoIngresosNoGravados;
    }

    /**
     * Set montoIngresosExentos
     *
     * @param string $montoIngresosExentos
     * @return DeclaracionJuradaIIBBContribuyente
     */
    public function setMontoIngresosExentos($montoIngresosExentos) {
        $this->montoIngresosExentos = $montoIngresosExentos;

        return $this;
    }

    /**
     * Get montoIngresosExentos
     *
     * @return string 
     */
    public function getMontoIngresosExentos() {
        return $this->montoIngresosExentos;
    }

    /**
     * Set montoImpuestoDeterminado
     *
     * @param string $montoImpuestoDeterminado
     * @return DeclaracionJuradaIIBBContribuyente
     */
    public function setMontoImpuestoDeterminado($montoImpuestoDeterminado) {
        $this->montoImpuestoDeterminado = $montoImpuestoDeterminado;

        return $this;
    }

    /**
     * Get montoImpuestoDeterminado
     *
     * @return string 
     */
    public function getMontoImpuestoDeterminado() {
        return $this->montoImpuestoDeterminado;
    }

    /**
     * Set montoRetencionesIIBB
     *
     * @param string $montoRetencionesIIBB
     * @return DeclaracionJuradaIIBBContribuyente
     */
    public function setMontoRetencionesIIBB($montoRetencionesIIBB) {
        $this->montoRetencionesIIBB = $montoRetencionesIIBB;

        return $this;
    }

    /**
     * Get montoRetencionesIIBB
     *
     * @return string 
     */
    public function getMontoRetencionesIIBB() {
        return $this->montoRetencionesIIBB;
    }

    /**
     * Set montoPercepcionesIIBB
     *
     * @param string $montoPercepcionesIIBB
     * @return DeclaracionJuradaIIBBContribuyente
     */
    public function setMontoPercepcionesIIBB($montoPercepcionesIIBB) {
        $this->montoPercepcionesIIBB = $montoPercepcionesIIBB;

        return $this;
    }

    /**
     * Get montoPercepcionesIIBB
     *
     * @return string 
     */
    public function getMontoPercepcionesIIBB() {
        return $this->montoPercepcionesIIBB;
    }

    /**
     * Set montoPagosACuenta
     *
     * @param string $montoPagosACuenta
     * @return DeclaracionJuradaIIBBContribuyente
     */
    public function setMontoPagosACuenta($montoPagosACuenta) {
        $this->montoPagosACuenta = $montoPagosACuenta;

        return $this;
    }

    /**
     * Get montoPagosACuenta
     *
     * @return string 
     */
    public function getMontoPagosACuenta() {
        return $this->montoPagosACuenta;
    }

    /**
     * Set ordenPago
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPagoDeclaracionJuradaIIBBContribuyente $ordenPago
     * @return DeclaracionJuradaIIBBContribuyente
     */
    public function setOrdenPago(\ADIF\ContableBundle\Entity\OrdenPagoDeclaracionJuradaIIBBContribuyente $ordenPago = null) {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return \ADIF\ContableBundle\Entity\OrdenPagoDeclaracionJuradaIIBBContribuyente 
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }

    public function getTotalIngresos() {
        return $this->getMontoIngresosGravados() + $this->getMontoIngresosNoGravados() + $this->getMontoIngresosExentos();
    }

    public function getTotalImpuesto() {
        return $this->getMontoImpuestoDeterminado() - $this->getMontoRetencionesIIBB() - $this->getMontoPercepcionesIIBB() - $this->getMontoPagosACuenta();
    }

}
