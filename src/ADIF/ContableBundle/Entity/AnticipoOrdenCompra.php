<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnticipoOrdenCompra
 * 
 * @author Darío Rapetti
 * created 05/05/2015
 *
 * @ORM\Table(name="anticipo_orden_compra")
 * @ORM\Entity
 */
class AnticipoOrdenCompra extends AnticipoProveedor {

    /**
     * @ORM\Column(name="id_orden_compra", type="integer")
     */
    protected $idOrdenCompra;

    /**
     * @var ADIF\ComprasBundle\Entity\OrdenCompra
     */
    protected $ordenCompra;

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\AnticipoProveedor $anticipoProveedor
     * @return \ADIF\ContableBundle\Entity\AnticipoOrdenCompra
     */
    public function __construct(AnticipoProveedor $anticipoProveedor) {

        parent::inicializar($anticipoProveedor);

        return $this;
    }

    /**
     * Set idOrdenCompra
     *
     * @param integer $idOrdenCompra
     * @return AnticipoOrdenCompra
     */
    public function setIdOrdenCompra($idOrdenCompra) {
        $this->idOrdenCompra = $idOrdenCompra;

        return $this;
    }

    /**
     * Get idOrdenCompra
     *
     * @return integer 
     */
    public function getIdOrdenCompra() {
        return $this->idOrdenCompra;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\OrdenCompra $ordenCompra
     */
    public function setOrdenCompra($ordenCompra) {

        if (null != $ordenCompra) {
            $this->idOrdenCompra = $ordenCompra->getId();
        } else {
            $this->idOrdenCompra = null;
        }

        $this->ordenCompra = $ordenCompra;
    }

    /**
     * 
     * @return type
     */
    public function getOrdenCompra() {
        return $this->ordenCompra;
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {

        if ($this->getIdProveedor() == null) {
            return $this->getOrdenCompra()->getProveedor();
        }

        return parent::getProveedor();
    }

    /**
     * 
     * @return type
     */
    public function getDetalle() {
        return 'Orden de compra: ' . $this->getOrdenCompra();
    }

    /**
     * 
     * @return string
     */
    public function getTipo() {
        return self::TIPO_ANTICIPO_ORDEN_COMPRA;
    }

}
