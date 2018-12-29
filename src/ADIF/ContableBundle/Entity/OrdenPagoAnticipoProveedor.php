<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of OrdenPagoAnticipoProveedor
 *
 * @author Darío Rapetti
 * created 11/04/2015
 * 
 * @ORM\Table(name="orden_pago_anticipo_proveedor")
 * @ORM\Entity
 */
class OrdenPagoAnticipoProveedor extends OrdenPago {

    /**
     * @var \ADIF\ContableBundle\Entity\AnticipoOrdenCompra
     *
     * @ORM\OneToOne(targetEntity="AnticipoProveedor", cascade={"all"}, inversedBy="ordenPago")
     * @ORM\JoinColumn(name="id_anticipo", referencedColumnName="id", nullable=false)
     * 
     */
    protected $anticipoProveedor;

    /**
     * Set anticipoProveedor
     *
     * @param \ADIF\ContableBundle\Entity\AnticipoProveedor $anticipoProveedor
     * @return OrdenPagoAnticipoProveedor
     */
    public function setAnticipoProveedor(\ADIF\ContableBundle\Entity\AnticipoProveedor $anticipoProveedor = null) {
        $this->anticipoProveedor = $anticipoProveedor;

        return $this;
    }

    /**
     * Get anticipoProveedor
     *
     * @return \ADIF\ContableBundle\Entity\AnticipoProveedor
     */
    public function getAnticipoProveedor() {
        return $this->anticipoProveedor;
    }

    /**
     * 
     * @return string
     */
    public function getPath() {
        return 'ordenpagoanticipoproveedor';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontableanticipoproveedor';
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return $this->getAnticipoProveedor()->getProveedor();
    }

    /**
     * 
     * @return type
     */
    public function getProveedorCUIT() {
        return $this->getAnticipoProveedor()->getProveedor()->getCUIT();
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
        return $this->getAnticipoProveedor()->getMonto();
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return $this->getAnticipoProveedor()->getProveedor();
    }

    /**
     * 
     * @return type
     */
    public function getOrdenCompra() {

        $anticipoProveedor = $this->getAnticipoProveedor();

        if ($anticipoProveedor->getTipo() == Anticipo::TIPO_ANTICIPO_ORDEN_COMPRA) {

            return $this->getAnticipoProveedor()->getOrdenCompra();
        }

        return null;
    }
    
    /**
     * 
     * @return EjecutadoEgresoValor
     */
    public function getEjecutadoEntity() {
        return new EjecutadoAnticipoProveedor();
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\OrdenPagoAnticipoProveedorController();
    }
}
