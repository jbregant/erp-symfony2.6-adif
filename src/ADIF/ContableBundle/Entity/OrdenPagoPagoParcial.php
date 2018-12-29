<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoObras;
use ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoCompras;

/**
 * Description of OrdenPagoPagoParcial
 * 
 * @ORM\Table(name="orden_pago_pago_parcial")
 * @ORM\Entity
 */
class OrdenPagoPagoParcial extends OrdenPago {

    /**
     * @ORM\Column(name="id_proveedor", type="integer", nullable=false)
     */
    protected $idProveedor;

    /**
     * @var ADIF\ComprasBundle\Entity\Proveedor
     */
    protected $proveedor;

    /**
     * @var PagoParcial
     *
     * @ORM\ManyToOne(targetEntity="PagoParcial", cascade={"all"})
     * @ORM\JoinColumn(name="id_pago_parcial", referencedColumnName="id")
     * 
     */
    protected $pagoParcial;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=15, scale=2, nullable=true)
     * 
     */
    protected $importe;
	
	
    /**
     * Set idProveedor
     *
     * @param integer $idProveedor
     * @return OrdenPagoPagoParcial
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
     * Set pagoParcial
     *
     * @param \ADIF\ContableBundle\Entity\PagoParcial $pagoParcial
     *
     * @return OrdenPagoPagoParcial
     */
    public function setPagoParcial(\ADIF\ContableBundle\Entity\PagoParcial $pagoParcial = null) {
        $this->pagoParcial = $pagoParcial;

        return $this;
    }

    /**
     * Get pagoParcial
     *
     * @return \ADIF\ContableBundle\Entity\PagoParcial
     */
    public function getPagoParcial() {
        return $this->pagoParcial;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return OrdenPagoPagoParcial
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
     * Get totalBruto
     *
     * @return double
     */
    public function getTotalBruto() {
        return $this->importe;
    }

    /**
     * 
     * @return string
     */
    public function getPath() {

        return 'ordenpagopagoparcial';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {

        return 'autorizacioncontablepagoparcial';
    }

    /**
     * Devuelvo un array de un comprobante, para mantener integridad
     * 
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComprobantes() {
        //return new ArrayCollection();
		$comprobantes = new ArrayCollection();
		$comprobantes->add( $this->pagoParcial->getComprobante() );
		return $comprobantes;
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return $this->getProveedor();
    }

    /**
     * 
     * @return boolean
     */
    public function getEsOrdenPagoParcial() {
        return true;
    }

    /**
     * 
     * @return \ADIF\ContableBundle\Entity\Ejecutado
     */
    public function getEjecutadoEntity() {

        return new Ejecutado();
    }
	
	/**
     * Puede devolver: 
     * \ADIF\ContableBundle\Entity\Obras\ComprobanteRetencionImpuestoObras 
	 * \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuestoCompras
     */
    public function getComprobanteRetencion($idProveedorUTE = null, $esComprobanteObra = true) 
	{
		if ($esComprobanteObra) {
			$comprobanteRetencion = new ComprobanteRetencionImpuestoObras();
		} else {
			$comprobanteRetencion = new ComprobanteRetencionImpuestoCompras();
		}
        
        $comprobanteRetencion->setIdProveedor($idProveedorUTE);
        return $comprobanteRetencion;
    }
	
	 /**
     * Get montoneto
     * Override de OrdenPago
     * @return double
     */
    public function getMontoNeto() 
	{
        $montoNeto = $this->getTotalBruto() - $this->getMontoRetenciones();
		return $montoNeto < 0 ? 0 : $montoNeto;
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\OrdenPagoPagoParcialController();
    }
}
