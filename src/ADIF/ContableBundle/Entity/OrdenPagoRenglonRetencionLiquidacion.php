<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of OrdenPagoRenglonRetencionLiquidacion
 *
 * @author Darío Rapetti
 * created 08/06/2015
 * 
 * @ORM\Table(name="orden_pago_renglon_retencion_liquidacion")
 * @ORM\Entity
 */
class OrdenPagoRenglonRetencionLiquidacion extends OrdenPago {

    /**
     *
     * @var RenglonRetencionLiquidacion
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\RenglonRetencionLiquidacion", mappedBy="ordenPago", cascade={"persist", "remove"})
     */
    protected $renglonesRetencionLiquidacion;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=10, scale=2, nullable=true)
     * 
     */
    protected $importe;

    /**
     * Set importe
     *
     * @param string $importe
     * @return OrdenPagoDeclaracionJuradaImpuesto
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
        return 'ordenpagorenglonretencionliquidacion';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontablerenglonretencionliquidacion';
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        $renglonRetencionLiquidacion = $this->getRenglonesRetencionLiquidacion()->first();        
        return $renglonRetencionLiquidacion->getBeneficiarioLiquidacion()->getRazonSocial();
    }
    
    /**
     * 
     * @return type
     */
    public function getProveedorCUIT() {
        $renglonRetencionLiquidacion = $this->getRenglonesRetencionLiquidacion()->first();        
        return $renglonRetencionLiquidacion->getBeneficiarioLiquidacion()->getCUIT();
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
        return $this->getImporte();
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        /* @var $renglonRetencionLiquidacion RenglonRetencionLiquidacion */
        $renglonRetencionLiquidacion = $this->getRenglonesRetencionLiquidacion()->first();        
        return $renglonRetencionLiquidacion->getBeneficiarioLiquidacion();
        //return new AdifDatos();
    }

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->renglonesRetencionLiquidacion = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add renglonesRetencionLiquidacion
     *
     * @param \ADIF\ContableBundle\Entity\RenglonRetencionLiquidacion $renglonesRetencionLiquidacion
     * @return OrdenPagoRenglonRetencionLiquidacion
     */
    public function addRenglonesRetencionLiquidacion(\ADIF\ContableBundle\Entity\RenglonRetencionLiquidacion $renglonesRetencionLiquidacion) {
        $this->renglonesRetencionLiquidacion[] = $renglonesRetencionLiquidacion;
        $renglonesRetencionLiquidacion->setOrdenPago($this);

        return $this;
    }

    /**
     * Remove renglonesRetencionLiquidacion
     *
     * @param \ADIF\ContableBundle\Entity\RenglonRetencionLiquidacion $renglonesRetencionLiquidacion
     */
    public function removeRenglonesRetencionLiquidacion(\ADIF\ContableBundle\Entity\RenglonRetencionLiquidacion $renglonesRetencionLiquidacion) {
        $this->renglonesRetencionLiquidacion->removeElement($renglonesRetencionLiquidacion);
        $renglonesRetencionLiquidacion->setOrdenPago(null);
    }

    /**
     * Get renglonesRetencionLiquidacion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRenglonesRetencionLiquidacion() {
        return $this->renglonesRetencionLiquidacion;
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\OrdenPagoRenglonRetencionLiquidacionController();
    }
}
