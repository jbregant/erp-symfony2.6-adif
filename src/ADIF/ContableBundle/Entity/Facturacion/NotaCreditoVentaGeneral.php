<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteClaseContrato;
use Doctrine\ORM\Mapping as ORM;

/**
 * NotaCreditoVentaGeneral
 *
 * @author Manuel Becerra
 * created 17/07/2015
 * 
 * @ORM\Table(name="nota_credito_venta_general")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteVentaRepository")
 */
class NotaCreditoVentaGeneral extends NotaCreditoVenta implements BaseAuditable {

    /**
     * Get fechaInicioServicio
     *
     * @return \DateTime 
     */
    public function getFechaInicioServicio() {
        return $this->getFechaComprobante();
    }

    /**
     * Get fechaFinServicio
     *
     * @return \DateTime 
     */
    public function getFechaFinServicio() {
        return $this->getFechaComprobante();
    }

    /**
     * 
     * @return type
     */
    public function getImpTotConc() { // NETO NO GRAVADO
        return $this->getImporteNetoNoGravado();
    }

    /**
     * 
     * @return type
     */
    public function getImpOpEx() { //IMPORTE EXENTO
        return 0;
    }

    /**
     * 
     * @return type
     */
    public function getCodigoClaseContrato() {

        return ConstanteClaseContrato::VENTA_GENERAL;
    }

    /**
     * 
     * @return boolean
     */
    public function esComprobanteVentaGeneral() {
        return true;
    }

}
