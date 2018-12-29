<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DevengadoCompra
 *
 * @author Manuel Becerra
 * created 20/10/2014
 * 
 * @ORM\Table(name="devengado_compra")
 * @ORM\Entity
 */
class DevengadoCompra extends Devengado {

    /**
     * @ORM\OneToOne(targetEntity="RenglonComprobanteCompra")
     * @ORM\JoinColumn(name="id_renglon_comprobante_compra", referencedColumnName="id")
     * */
    protected $renglonComprobanteCompra;

    /**
     * Set renglonComprobanteCompra
     *
     * @param \ADIF\ContableBundle\Entity\RenglonComprobanteCompra $renglonComprobanteCompra
     * @return DevengadoCompra
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
