<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of OrdenPagoPagoACuenta
 *
 * @author Darío Rapetti
 * created 17/04/2015
 * 
 * @ORM\Table(name="orden_pago_pago_a_cuenta")
 * @ORM\Entity
 */
class OrdenPagoPagoACuenta extends OrdenPago {

    /**
     * @var \ADIF\ContableBundle\Entity\PagoACuenta
     *
     * @ORM\OneToOne(targetEntity="PagoACuenta", cascade={"all"}, inversedBy="ordenPago")
     * @ORM\JoinColumn(name="id_pago_a_cuenta", referencedColumnName="id", nullable=false)
     * 
     */
    protected $pagoACuenta;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=15, scale=2, nullable=true)
     * 
     */
    protected $importe;

    /**
     * Set pagoACuenta
     *
     * @param \ADIF\ContableBundle\Entity\PagoACuenta $pagoACuenta
     * @return OrdenPagoPagoACuenta
     */
    public function setPagoACuenta(\ADIF\ContableBundle\Entity\PagoACuenta $pagoACuenta = null) {
        $this->pagoACuenta = $pagoACuenta;

        return $this;
    }

    /**
     * Get pagoACuenta
     *
     * @return \ADIF\ContableBundle\Entity\PagoACuenta
     */
    public function getPagoACuenta() {
        return $this->pagoACuenta;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return OrdenPagoPagoACuenta
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
     * 
     * @return string
     */
    public function getPath() {
        return 'ordenpagopagoacuenta';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontablepagoacuenta';
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return "AFIP";
    }
    
    /**
     * 
     * @return type
     */
    public function getProveedorCUIT() {
        return "33-69345023-9";
    }    

    /**
     * Get comprobantes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComprobantes() {
        return new ArrayCollection();
    }

    /**
     * Get totalBruto
     *
     * @return double
     */
    public function getTotalBruto() {
        return $this->importe;
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return "AFIP";
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\OrdenPagoPagoACuentaController();
    }
}
