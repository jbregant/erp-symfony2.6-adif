<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\Facturacion\IRenglonComprobanteVenta;

/**
 * DevengadoVenta
 *
 * @author Darío Rapetti
 * created 06/03/2015
 * 
 * @ORM\Table(name="devengado_venta")
 * @ORM\Entity
 */
class DevengadoVenta extends Devengado 
{

    /**
     * @ORM\OneToOne(targetEntity="\ADIF\ContableBundle\Entity\Facturacion\RenglonComprobanteVenta", cascade={"persist"})
     * @ORM\JoinColumn(name="id_renglon_comprobante_venta", referencedColumnName="id")
     * */
    protected $renglonComprobanteVenta;

    /**
     * Set renglonComprobanteVenta
     *
     * @param ADIF\ContableBundle\Entity\Facturacion\IRenglonComprobanteVenta $renglonComprobanteVenta
     * @return DevengadoVenta
     */
    public function setRenglonComprobanteVenta(IRenglonComprobanteVenta $renglonComprobanteVenta = null) {
        $this->renglonComprobanteVenta = $renglonComprobanteVenta;

        return $this;
    }

    /**
     * Get renglonComprobanteVenta
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\RenglonComprobanteVenta
     */
    public function getRenglonComprobanteVenta() {
        return $this->renglonComprobanteVenta;
    }

}
