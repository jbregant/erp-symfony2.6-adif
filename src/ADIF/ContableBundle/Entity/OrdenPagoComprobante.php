<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoCompras;

/**
 * Description of OrdenPagoComprobante
 *
 * @author Manuel Becerra
 * created 03/11/2014
 * 
 * @ORM\Table(name="orden_pago_comprobante")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\OrdenPagoComprobanteRepository")
 */
class OrdenPagoComprobante extends OrdenPago {

    /**
     *
     * @var ComprobanteCompra
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\ComprobanteCompra", mappedBy="ordenPago")
     */
    protected $comprobantes;

    /**
     * @var AnticipoProveedor Anticipos que descuentan a esta OP
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\AnticipoProveedor", mappedBy="ordenPagoCancelada")
     * 
     */
    protected $anticipos;

    /**
     * @ORM\Column(name="id_proveedor", type="integer", nullable=false)
     */
    protected $idProveedor;

    /**
     * @var ADIF\ComprasBundle\Entity\Proveedor
     */
    protected $proveedor;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->comprobantes = new ArrayCollection();
        $this->anticipos = new ArrayCollection();
    }

    /**
     * Add comprobantes
     *
     * @param \ADIF\ContableBundle\Entity\ComprobanteCompra $comprobantes
     * @return OrdenPagoComprobante
     */
    public function addComprobante(\ADIF\ContableBundle\Entity\ComprobanteCompra $comprobantes) {
        $this->comprobantes[] = $comprobantes;

        return $this;
    }

    /**
     * Remove comprobantes
     *
     * @param \ADIF\ContableBundle\Entity\ComprobanteCompra $comprobantes
     */
    public function removeComprobante(\ADIF\ContableBundle\Entity\ComprobanteCompra $comprobantes) {
        $this->comprobantes->removeElement($comprobantes);
    }

    /**
     * Get comprobantes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComprobantes() {
        return $this->comprobantes;
    }

    /**
     * Add anticipos
     *
     * @param \ADIF\ContableBundle\Entity\AnticipoProveedor $anticipos
     * @return OrdenPagoComprobante
     */
    public function addAnticipo(\ADIF\ContableBundle\Entity\AnticipoProveedor $anticipos) {
        $this->anticipos[] = $anticipos;

        return $this;
    }

    /**
     * Remove anticipos
     *
     * @param \ADIF\ContableBundle\Entity\AnticipoProveedor $anticipos
     */
    public function removeAnticipo(\ADIF\ContableBundle\Entity\AnticipoProveedor $anticipos) {
        $this->anticipos->removeElement($anticipos);
    }

    /**
     * Get anticipos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnticipos() {
        return $this->anticipos;
    }

    /**
     * Set idProveedor
     *
     * @param integer $idProveedor
     * @return OrdenPagoComprobante
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
     * 
     */
    public function getOrdenCompra() {

        $ordenCompra = null;

        if (!$this->getComprobantes()->isEmpty()) {
            $ordenCompra = $this->getComprobantes()->first()->getOrdenCompra();
        }

        return $ordenCompra;
    }

    /**
     * 
     */
    public function getOrigen() {

        return $this->getOrdenCompra();
    }

    /**
     * 
     * @return string
     */
    public function getPath() {

        return 'ordenpagocomprobante';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {

        return 'autorizacioncontablecompra';
    }

    /**
     * 
     * @return \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoCompras
     */
    public function getComprobanteRetencion($idProveedorUTE = null) {
        $comprobanteRetencion = new ComprobanteRetencionImpuestoCompras();
        $comprobanteRetencion->setIdProveedor($idProveedorUTE);
        return $comprobanteRetencion;
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return $this->proveedor;
    }

    /**
     * 
     * @return EjecutadoCompra
     */
    public function getEjecutadoEntity() {
        $ejecutado = new EjecutadoCompra();
        return $ejecutado->setOrdenPagoComprobante($this);
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\OrdenPagoComprobanteController();
    }
}
