<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of OrdenPagoGeneral
 *
 * @author Manuel Becerra
 * created 31/08/2015
 * 
 * @ORM\Table(name="orden_pago_general")
 * @ORM\Entity
 */
class OrdenPagoGeneral extends OrdenPago {

    /**
     * @ORM\Column(name="id_proveedor", type="integer", nullable=false)
     */
    protected $idProveedor;

    /**
     * @var ADIF\ComprasBundle\Entity\Proveedor
     */
    protected $proveedor;

    /**
     * @var ConceptoOrdenPago
     *
     * @ORM\ManyToOne(targetEntity="ConceptoOrdenPago", cascade={"persist"})
     * @ORM\JoinColumn(name="id_concepto_orden_pago", referencedColumnName="id")
     * 
     */
    protected $conceptoOrdenPago;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=15, scale=2, nullable=true)
     * 
     */
    protected $importe;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=1000, nullable=true)
     */
    protected $observaciones;

    /**
     * Set idProveedor
     *
     * @param integer $idProveedor
     * @return OrdenPagoObra
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
        } //.
        else {
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
     * 
     * @return type
     */
    public function getProveedorCUIT() {
        return $this->proveedor->getCUIT();
    }       

    /**
     * Set conceptoOrdenPago
     *
     * @param \ADIF\ContableBundle\Entity\ConceptoOrdenPago $conceptoOrdenPago
     *
     * @return OrdenPagoGeneral
     */
    public function setConceptoOrdenPago(\ADIF\ContableBundle\Entity\ConceptoOrdenPago $conceptoOrdenPago = null) {
        $this->conceptoOrdenPago = $conceptoOrdenPago;


        return $this;
    }

    /**
     * Get conceptoOrdenPago
     *
     * @return \ADIF\ContableBundle\Entity\ConceptoOrdenPago
     */
    public function getConceptoOrdenPago() {
        return $this->conceptoOrdenPago;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return OrdenPagoGeneral
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
     * Set observaciones
     *
     * @param string $observaciones
     * @return OrdenPagoGeneral
     */
    public function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones() {
        return $this->observaciones;
    }

    /**
     * 
     * @return string
     */
    public function getPath() {
        return 'ordenpagogeneral';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {
        return 'autorizacioncontablegeneral';
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
        return $this->importe;
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return $this->proveedor;
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\OrdenPagoGeneralController();
    }
}
