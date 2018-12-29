<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * AnticipoProveedor
 * 
 * @author Darío Rapetti
 * created 21/10/2014
 *
 * @ORM\Table(name="anticipo_proveedor")
 * @ORM\Entity
 */
class AnticipoProveedor extends Anticipo {

    /**
     * @ORM\Column(name="id_proveedor", type="integer", nullable=true)
     */
    protected $idProveedor;

    /**
     * @var ADIF\ComprasBundle\Entity\Proveedor
     */
    protected $proveedor;

    /**
     * @ORM\OneToOne(targetEntity="OrdenPagoAnticipoProveedor", mappedBy="anticipoProveedor")
     * */
    protected $ordenPago;

    /**
     * @ORM\ManyToOne(targetEntity="ADIF\ContableBundle\Entity\OrdenPagoComprobante", inversedBy="anticipos")
     * @ORM\JoinColumn(name="id_orden_pago_cancelada", referencedColumnName="id")
     */
    protected $ordenPagoCancelada;

    /**
     * Set idProveedor
     *
     * @param integer $idProveedor
     * @return AnticipoProveedor
     */
    public function setIdProveedor($idProveedor) {
        $this->idProveedor = $idProveedor;

        return $this;
    }

    /**
     * Get idProveedor
     *
     * @return integer 
     */
    public function getIdProveedor() {
        return $this->idProveedor;
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Entity\Proveedor $proveedor
     */
    public function setProveedor($proveedor) {

        if (null != $proveedor) {
            $this->idProveedor = $proveedor->getId();
        } else {
            $this->idProveedor = null;
        }

        $this->proveedor = $proveedor;
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        
        return $this->proveedor;
    }

    /**
     * Set ordenPago
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPagoAnticipoProveedor $ordenPago
     * @return AnticipoProveedor
     */
    public function setOrdenPago(\ADIF\ContableBundle\Entity\OrdenPagoAnticipoProveedor $ordenPago = null) {
        $this->ordenPago = $ordenPago;

        return $this;
    }

    /**
     * Get ordenPago
     *
     * @return \ADIF\ContableBundle\Entity\OrdenPagoAnticipoProveedor 
     */
    public function getOrdenPago() {
        return $this->ordenPago;
    }

    /**
     * Set ordenPagoCancelada
     *
     * @param \ADIF\ContableBundle\Entity\OrdenPagoComprobante $ordenPagoCancelada
     * @return AnticipoOrdenCompra
     */
    public function setOrdenPagoCancelada(\ADIF\ContableBundle\Entity\OrdenPagoComprobante $ordenPagoCancelada = null) {
        $this->ordenPagoCancelada = $ordenPagoCancelada;

        return $this;
    }

    /**
     * Get ordenPagoCancelada
     *
     * @return \ADIF\ContableBundle\Entity\OrdenPagoComprobante 
     */
    public function getOrdenPagoCancelada() {
        return $this->ordenPagoCancelada;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\AnticipoProveedor $anticipoProveedor
     * @return \ADIF\ContableBundle\Entity\AnticipoProveedor
     */
    public function inicializar(AnticipoProveedor $anticipoProveedor) {

        $this->setFechaCreacion(new DateTime());
        $this->setFechaUltimaActualizacion(new DateTime());
        $this->setProveedor($anticipoProveedor->getProveedor());
        $this->setFecha($anticipoProveedor->getFecha());
        $this->setMonto($anticipoProveedor->getMonto());
        $this->setObservacion($anticipoProveedor->getObservacion());

        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getDetalle() {

        return null;
    }

    /**
     * 
     * @return string
     */
    public function getTipo() {

        return self::TIPO_ANTICIPO_PROVEEDOR;
    }

}
