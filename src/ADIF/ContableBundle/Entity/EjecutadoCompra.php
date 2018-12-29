<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EjecutadoCompra
 *
 * @author Manuel Becerra
 * created 16/12/2014
 * 
 * @ORM\Table(name="ejecutado_compra")
 * @ORM\Entity
 */
class EjecutadoCompra extends Ejecutado {

    /**
     * @ORM\OneToOne(targetEntity="OrdenPagoComprobante")
     * @ORM\JoinColumn(name="id_orden_pago", referencedColumnName="id")
     */
    protected $ordenPagoComprobante;

    /**
     * @ORM\OneToOne(targetEntity="RenglonComprobanteCompra")
     * @ORM\JoinColumn(name="id_renglon_comprobante_compra", referencedColumnName="id")
     * */
    protected $renglonComprobanteCompra;

    /**
     * Set ordenPagoComprobante
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPagoComprobante $ordenPagoComprobante
     * @return EjecutadoCompra
     */
    public function setOrdenPagoComprobante(\ADIF\ContableBundle\Entity\OrdenPagoComprobante $ordenPagoComprobante = null) {
        $this->ordenPagoComprobante = $ordenPagoComprobante;

        return $this;
    }

    /**
     * Get ordenPagoComprobante
     *
     * @return \ADIF\ContableBundle\Entity\OrdenPagoComprobante 
     */
    public function getOrdenPagoComprobante() {
        return $this->ordenPagoComprobante;
    }

    /**
     * Set renglonComprobanteCompra
     *
     * @param \ADIF\ContableBundle\Entity\RenglonComprobanteCompra $renglonComprobanteCompra
     * @return EjecutadoCompra
     */
    public function setRenglonComprobanteCompra(\ADIF\ContableBundle\Entity\RenglonComprobanteCompra $renglonComprobanteCompra = null) {
        $this->renglonComprobanteCompra = $renglonComprobanteCompra;

        return $this;
    }

    /**
     * Get renglonComprobanteCompra
     *
     * @return \ADIF\ContableBundle\Entity\RenglonComprobanteCompra 
     */
    public function getRenglonComprobanteCompra() {
        return $this->renglonComprobanteCompra;
    }

}
