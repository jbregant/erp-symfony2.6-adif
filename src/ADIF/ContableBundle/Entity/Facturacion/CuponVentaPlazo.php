<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * CuponVentaPlazo
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="cupon_venta_plazo")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\CuponVentaRepository")
 */
class CuponVentaPlazo extends CuponVenta implements BaseAuditable {

    /**
     * @var CicloFacturacion
     *
     * @ORM\ManyToOne(targetEntity="CicloFacturacion")
     * @ORM\JoinColumn(name="id_ciclo_facturacion", referencedColumnName="id", nullable=false)
     */
    protected $cicloFacturacion;

    /**
     * Set cicloFacturacion
     *
     * @param \ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion $cicloFacturacion
     * @return CuponVentaPlazo
     */
    public function setCicloFacturacion(\ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion $cicloFacturacion) {
        $this->cicloFacturacion = $cicloFacturacion;

        return $this;
    }

    /**
     * Get cicloFacturacion
     *
     * @return \ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion 
     */
    public function getCicloFacturacion() {
        return $this->cicloFacturacion;
    }

    /**
     * Get esCupon
     *
     * @return boolean 
     */
    public function getEsCupon() {
        return true;
    }
    
    /**
     * Get esRendicionLiquidoProducto
     *
     * @return boolean 
     */
    public function getEsRendicionLiquidoProducto() {
        return false;
    }

    public function getCodigoBarrasNacion() {
        return ($this->getCodigoBarras() == null ? $this->generarCodigoBarras() : $this->getCodigoBarras());      
    } 
    
    public function getEsCuponVentaPlazo() {
        return true;
    }  
    
    public function generarCodigoBarras() {    
        $idClienteAdif = '4687';
        $numeroContrato = $this->getContrato()->getNumeroContrato();
//        $primera_letra = substr($numeroContrato, 0, 1);
//        $segunda_letra = substr($numeroContrato, 1, 1);
        $numero = substr($numeroContrato, 2, 10);
        
//        $codigoContrato = str_pad(ord($primera_letra) . ord($segunda_letra) . $numero, 12, "0", STR_PAD_LEFT);
        $codigoContrato = str_pad($numero, 10, "0", STR_PAD_LEFT);        
        $fecha = ($this->getFechaVencimiento() != null ? $this->getFechaVencimiento() : $this->getFechaComprobante()); 
        return $idClienteAdif . $codigoContrato. '01'.str_pad($fecha->format('my'), 4, '0', STR_PAD_LEFT) . '000000';    
    }
    
}
