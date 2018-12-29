<?php

namespace ADIF\ComprasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of CodigoAutorizacionImpresionProveedor
 *
 * @author Darío Rapetti
 * created 29/01/2015
 * 
 * @ORM\Table(name="codigo_autorizacion_impresion_proveedor")
 * @ORM\Entity
 */
class CodigoAutorizacionImpresionProveedor extends CodigoAutorizacionImpresion {

    /**
     * @var \ADIF\ComprasBundle\Entity\Proveedor
     *
     * @ORM\ManyToOne(targetEntity="Proveedor", inversedBy="cais")
     * @ORM\JoinColumn(name="id_proveedor", referencedColumnName="id", nullable=false)
     * 
     */
    protected $proveedor;

    /**
     * @var \integer
     *
     * @ORM\Column(name="punto_venta", type="string", length=4, nullable=false)
     */
    protected $puntoVenta;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set proveedor
     *
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     * @return CodigoAutorizacionImpresion
     */
    public function setProveedor(\ADIF\ComprasBundle\Entity\Proveedor $proveedor) {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor
     *
     * @return \ADIF\ComprasBundle\Entity\Proveedor 
     */
    public function getProveedor() {
        return $this->proveedor;
    }

    /**
     * Set puntoVenta
     *
     * @param string $puntoVenta
     * @return CodigoAutorizacionImpresion
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

}
