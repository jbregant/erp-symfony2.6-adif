<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\ContableBundle\Entity\OrdenPago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrdenPagoDevolucionGarantia
 *
 * @author Manuel Becerra
 * created 11/04/2015
 * 
 * @ORM\Table(name="orden_pago_devolucion_garantia")
 * @ORM\Entity
 */
class OrdenPagoDevolucionGarantia extends OrdenPago {

    /**
     * @var DevolucionGarantia
     *
     * @ORM\ManyToOne(targetEntity="DevolucionGarantia", inversedBy="ordenesPago")
     * @ORM\JoinColumn(name="id_devolucion_garantia", referencedColumnName="id", nullable=false)
     */
    protected $devolucionGarantia;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=10, scale=2, nullable=true)
     * 
     */
    protected $importe;

    /**
     * Set devolucionGarantia
     *
     * @param DevolucionGarantia $devolucionGarantia
     * @return OrdenPagoDevolucionGarantia
     */
    public function setDevolucionGarantia(DevolucionGarantia $devolucionGarantia) {
        $this->devolucionGarantia = $devolucionGarantia;

        return $this;
    }

    /**
     * Get devolucionGarantia
     *
     * @return DevolucionGarantia 
     */
    public function getDevolucionGarantia() {
        return $this->devolucionGarantia;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return OrdenPagoDevolucionGarantia
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
        return 'ordenpagodevoluciongarantia';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontabledevoluciongarantia';
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return "ADIF";
    }
    
    /**
     * 
     * @return type
     */
    public function getProveedorCUIT() {
        return "30-71069599-3";
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
        return $this->getDevolucionGarantia()->getImporte();
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return $this->getDevolucionGarantia()->getCuponGarantia()->getCliente();
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\Facturacion\OrdenPagoDevolucionGarantiaController();
    }
}
