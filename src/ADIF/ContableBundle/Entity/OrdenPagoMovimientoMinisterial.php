<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\ContableBundle\Entity\OrdenPago;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrdenPagoMovimientoMinisterial
 * 
 * @ORM\Table(name="orden_pago_movimiento_ministerial")
 * @ORM\Entity
 */
class OrdenPagoMovimientoMinisterial extends OrdenPago {

    /**
     * @var MovimientoMinisterial
     *
     * @ORM\ManyToOne(targetEntity="MovimientoMinisterial", inversedBy="ordenesPago")
     * @ORM\JoinColumn(name="id_movimiento_ministerial", referencedColumnName="id", nullable=false)
     */
    protected $movimientoMinisterial;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=15, scale=2, nullable=true)
     * 
     */
    protected $importe;

    /**
     * Set movimientoMinisterial
     *
     * @param MovimientoMinisterial $movimientoMinisterial
     * @return OrdenPagoMovimientoMinisterial
     */
    public function setMovimientoMinisterial(MovimientoMinisterial $movimientoMinisterial) {
        $this->movimientoMinisterial = $movimientoMinisterial;

        return $this;
    }

    /**
     * Get movimientoMinisterial
     *
     * @return MovimientoMinisterial 
     */
    public function getMovimientoMinisterial() {
        return $this->movimientoMinisterial;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return OrdenPagoMovimientoMinisterial
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
        return 'ordenpagomovimientoministerial';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontablemovimientoministerial';
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
        return $this->getMovimientoMinisterial()->getMonto();
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return new AdifDatos();
    }

    /**
     * 
     * @return boolean
     */
    public function getRequiereVisado() {
        return false;
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\OrdenPagoMovimientoMinisterialController();
    }
}
