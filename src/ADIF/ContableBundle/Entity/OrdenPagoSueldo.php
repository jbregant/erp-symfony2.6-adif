<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of OrdenPagoSueldo
 * 
 * @ORM\Table(name="orden_pago_sueldo")
 * @ORM\Entity
 */
class OrdenPagoSueldo extends OrdenPago {

    /**
     * @ORM\Column(name="id_liquidacion", type="integer", nullable=false)
     */
    protected $idLiquidacion;

    /**
     * @var ADIF\RecursosHumanosBundle\Entity\Liquidacion
     */
    protected $liquidacion;

    /**
     * @var double
     * @ORM\Column(name="importe", type="decimal", precision=10, scale=2, nullable=true)
     * 
     */
    protected $importe;

    /**
     * @var string
     *
     * @ORM\Column(name="liquidaciones_empleado", type="text", nullable=true)
     */
    private $liquidacionesEmpleado;

    /**
     * Set idLiquidacion
     *
     * @param integer $idLiquidacion
     * @return OrdenPagoSueldo
     */
    public function setIdLiquidacion($idLiquidacion) {
        $this->idLiquidacion = $idLiquidacion;

        return $this;
    }

    /**
     * Get idLiquidacion
     *
     * @return integer 
     */
    public function getIdLiquidacion() {
        return $this->idLiquidacion;
    }

    /**
     * 
     * @param \ADIF\RecursosHumanosBundle\Entity\Liquidacion $liquidacion
     */
    public function setLiquidacion($liquidacion) {

        if (null != $liquidacion) {
            $this->idLiquidacion = $liquidacion->getId();
        } //.
        else {
            $this->idLiquidacion = null;
        }

        $this->liquidacion = $liquidacion;
    }

    /**
     * 
     * @return type
     */
    public function getLiquidacion() {
        return $this->liquidacion;
    }

    /**
     * Set importe
     *
     * @param string $importe
     * @return OrdenPagoSueldo
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

        return 'ordenpagosueldo';
    }

    /**
     * 
     * @return string
     */
    public function getPathAC() {

        return 'autorizacioncontablesueldo';
    }

    /**
     * 
     * @return type
     */
    public function getProveedor() {
        return "ADIF";
    }

    /**
     * 
     * @return type
     */
    public function getProveedorCUIT() {
        return "30-71069599-3";
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
     * Set liquidacionesEmpleado
     *
     * @param string $liquidacionesEmpleado
     * @return OrdenPagoSueldo
     */
    public function setLiquidacionesEmpleado($liquidacionesEmpleado) {
        $this->liquidacionesEmpleado = $liquidacionesEmpleado;

        return $this;
    }

    /**
     * Get liquidacionesEmpleado
     *
     * @return string 
     */
    public function getLiquidacionesEmpleado() {
        return $this->liquidacionesEmpleado;
    }

    /**
     * 
     * @return type
     */
    public function getBeneficiario() {
        return new AdifDatos();
    }

    public function getController(){
        return new \ADIF\ContableBundle\Controller\OrdenPagoSueldoController();
    }
}
