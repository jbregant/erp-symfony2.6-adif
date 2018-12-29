<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\ContableBundle\Entity\OrdenPago;
use ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoObras;

/**
 * Description of OrdenPagoObra
 * 
 * @ORM\Table(name="orden_pago_obra")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\OrdenPagoObraRepository")
 */
class OrdenPagoObra extends OrdenPago {

    /**
     *
     * @var ComprobanteObra
     * 
     * @ORM\OneToMany(targetEntity="ADIF\ContableBundle\Entity\Obras\ComprobanteObra", mappedBy="ordenPago")
     */
    protected $comprobantes;

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
    }

    /**
     * Add comprobantes
     *
     * @param \ADIF\ContableBundle\Entity\Obras\ComprobanteObra $comprobantes
     * @return OrdenPagoObra
     */
    public function addComprobante(\ADIF\ContableBundle\Entity\Obras\ComprobanteObra $comprobantes) {
        $this->comprobantes[] = $comprobantes;

        return $this;
    }

    /**
     * Remove comprobantes
     *
     * @param \ADIF\ContableBundle\Entity\Obras\ComprobanteObra $comprobantes
     */
    public function removeComprobante(\ADIF\ContableBundle\Entity\Obras\ComprobanteObra $comprobantes) {
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
     * 
     */
    public function getTramo() {

        $tramo = null;

        if (!$this->getComprobantes()->isEmpty()) {
            $tramo = $this->getComprobantes()->first()->getTramo();
        }

        return $tramo;
    }

    /**
     * 
     */
    public function getOrigen() {

        return $this->getTramo();
    }

    /**
     * 
     * @return string
     */
    public function getPath() {

        return 'ordenpagoobra';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {

        return 'autorizacioncontableobra';
    }

    /**
     * 
     * @return \ADIF\ContableBundle\Entity\Obras\ComprobanteRetencionImpuestoObras
     */
    public function getComprobanteRetencion($idProveedorUTE = null) {
        $comprobanteRetencion = new ComprobanteRetencionImpuestoObras();
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
     * @return EjecutadoObra
     */
    public function getEjecutadoEntity() {
        $ejecutado = new EjecutadoObra();
        return $ejecutado->setOrdenPagoObra($this);
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\Obras\OrdenPagoObraController();
    }
}
