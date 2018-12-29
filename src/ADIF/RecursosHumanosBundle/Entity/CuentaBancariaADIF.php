<?php

namespace ADIF\RecursosHumanosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of CuentaBancariaADIF
 *
 * @author Manuel Becerra
 * created 05/11/2014
 * 
 * @ORM\Entity(repositoryClass="ADIF\RecursosHumanosBundle\Repository\CuentaBancariaADIFRepository")
 */
class CuentaBancariaADIF extends CuentaBancaria {

    /**
     * @ORM\Column(name="id_cuenta_contable", type="integer", nullable=true)
     */
    protected $idCuentaContable;

    /**
     * @var ADIF\ContableBundle\Entity\CuentaContable
     */
    protected $cuentaContable;

    /**
     * @ORM\Column(name="numero_sucursal", type="string", nullable=false)
     */
    protected $numeroSucursal;

    /**
     * @ORM\Column(name="numero_cuenta", type="string", nullable=false)
     */
    protected $numeroCuenta;

    /**
     * @var boolean
     *
     * @ORM\Column(name="esta_activa", type="boolean", nullable=false)
     */
    protected $estaActiva;

    /**
     * @var double
     *
     * @ORM\Column(name="monto_ingresos_pendientes", type="decimal", precision=15, scale=2, nullable=true, options={"default": 0})
     */
    protected $montoIngresosPendientes;

    /**
     * @var double
     *
     * @ORM\Column(name="monto_cheques_pendientes", type="decimal", precision=15, scale=2, nullable=true, options={"default": 0})
     */
    protected $montoChequesPendientes;
    
    /**
     * @ORM\Column(name="id_tipo_moneda", type="integer", nullable=false)
     */
    protected $idTipoMoneda;

    /**
     * @var ADIF\ContableBundle\Entity\TipoMoneda
     */
    protected $tipoMoneda;    

    public function __construct() {
        $this->estaActiva = true;
    }

    /**
     * toString 
     * 
     * @return type
     */
    public function __toString() {
        return (string) $this->idBanco . ' — ' . (string)$this->cuentaContable;
    }

    /**
     * Set estaActiva
     *
     * @param boolean $estaActiva
     * @return Concepto
     */
    public function setEstaActiva($estaActiva) {
        $this->estaActiva = $estaActiva;

        return $this;
    }

    /**
     * Get estaActiva
     *
     * @return boolean 
     */
    public function getEstaActiva() {
        return $this->estaActiva;
    }

    /**
     * Set idCuentaContable
     *
     * @param integer $idCuentaContable
     * @return CuentaBancaria
     */
    public function setIdCuentaContable($idCuentaContable) {
        $this->idCuentaContable = $idCuentaContable;

        return $this;
    }

    /**
     * Get idCuentaContable
     *
     * @return integer 
     */
    public function getIdCuentaContable() {
        return $this->idCuentaContable;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\CuentaContable $cuentaContable
     */
    public function setCuentaContable($cuentaContable) {

        if (null != $cuentaContable) {
            $this->idCuentaContable = $cuentaContable->getId();
        } //.
        else {
            $this->idCuentaContable = null;
        }

        $this->cuentaContable = $cuentaContable;
    }

    /**
     * 
     * @return type
     */
    public function getCuentaContable() {
        return $this->cuentaContable;
    }

    /**
     * Set numeroSucursal
     *
     * @param string $numeroSucursal
     * @return CuentaBancariaADIF
     */
    public function setNumeroSucursal($numeroSucursal) {
        $this->numeroSucursal = $numeroSucursal;

        return $this;
    }

    /**
     * Get numeroSucursal
     *
     * @return string 
     */
    public function getNumeroSucursal() {
        return $this->numeroSucursal;
    }

    /**
     * Set numeroCuenta
     *
     * @param string $numeroCuenta
     * @return CuentaBancariaADIF
     */
    public function setNumeroCuenta($numeroCuenta) {
        $this->numeroCuenta = $numeroCuenta;

        return $this;
    }

    /**
     * Get numeroCuenta
     *
     * @return string 
     */
    public function getNumeroCuenta() {
        return $this->numeroCuenta;
    }

    /**
     * Get numeroSucursalYCuenta
     *
     * @return string 
     */
    public function getNumeroSucursalYCuenta() {
        return $this->numeroSucursal . ' ' . $this->numeroCuenta;
    }

    /**
     * Set montoIngresosPendientes
     *
     * @param string $montoIngresosPendientes
     * @return CuentaBancariaADIF
     */
    public function setMontoIngresosPendientes($montoIngresosPendientes) {
        $this->montoIngresosPendientes = $montoIngresosPendientes;

        return $this;
    }

    /**
     * Get montoIngresosPendientes
     *
     * @return string 
     */
    public function getMontoIngresosPendientes() {
        return $this->montoIngresosPendientes;
    }

    /**
     * Set montoChequesPendientes
     *
     * @param string $montoChequesPendientes
     * @return CuentaBancariaADIF
     */
    public function setMontoChequesPendientes($montoChequesPendientes) {
        $this->montoChequesPendientes = $montoChequesPendientes;

        return $this;
    }

    /**
     * Get montoChequesPendientes
     *
     * @return string 
     */
    public function getMontoChequesPendientes() {
        return $this->montoChequesPendientes;
    }
    
    /**
     * Set idTipoMoneda
     *
     * @param integer $idTipoMoneda
     * @return RenglonCobranza
     */
    public function setIdTipoMoneda($idTipoMoneda) {
        $this->idTipoMoneda = $idTipoMoneda;

        return $this;
    }

    /**
     * Get idTipoMoneda
     *
     * @return integer 
     */
    public function getIdTipoMoneda() {
        return $this->idTipoMoneda;
    }

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\TipoMoneda $tipoMoneda
     */
    public function setTipoMoneda($tipoMoneda) {

        if (null != $tipoMoneda) {
            $this->idTipoMoneda = $tipoMoneda->getId();
        } //.
        else {
            $this->idTipoMoneda = null;
        }

        $this->tipoMoneda = $tipoMoneda;
    }

    /**
     * 
     * @return type
     */
    public function getTipoMoneda() {
        return $this->tipoMoneda;
    }    

}
