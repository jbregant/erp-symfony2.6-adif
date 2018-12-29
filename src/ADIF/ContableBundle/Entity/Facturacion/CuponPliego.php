<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteClaseContrato;
use Doctrine\ORM\Mapping as ORM;

/**
 * CuponPliego
 *
 * @author Manuel Becerra
 * created 05/03/2015
 * 
 * @ORM\Table(name="cupon_pliego")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\CuponVentaRepository")
 */
class CuponPliego extends CuponVenta implements BaseAuditable {

    /**
     * @var \ADIF\ContableBundle\Entity\Licitacion
     *
     * @ORM\ManyToOne(targetEntity="\ADIF\ContableBundle\Entity\Licitacion")
     * @ORM\JoinColumn(name="id_licitacion", referencedColumnName="id", nullable=true)
     */
    protected $licitacion;

    /**
     * Set licitacion
     *
     * @param \ADIF\ContableBundle\Entity\Licitacion $licitacion
     * @return CuponPliego
     */
    public function setLicitacion(\ADIF\ContableBundle\Entity\Licitacion $licitacion) {
        $this->licitacion = $licitacion;

        return $this;
    }

    /**
     * Get licitacion
     *
     * @return \ADIF\ContableBundle\Entity\Licitacion 
     */
    public function getLicitacion() {
        return $this->licitacion;
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
    public function getCodigoClaseContrato() {

        return ConstanteClaseContrato::PLIEGO;
    }

    /**
     * Get codigoBarrasNacion
     *
     * @return string 
     */
    public function getCodigoBarrasNacion() {
        return ($this->getCodigoBarras() == null ? $this->generarCodigoBarras() : $this->getCodigoBarras());
    }
    
    public function generarCodigoBarras() {
        $idClienteAdif = '4687';

        $codigoContrato = str_pad($this->getNumeroCupon(), 12, "0", STR_PAD_LEFT);

        $primera_letra = substr($codigoContrato, 0, 1);

        $segunda_letra = substr($codigoContrato, 1, 1);

        $numero = substr($codigoContrato, 2, 10);

        $vencimiento_contrato = $this->getLicitacion()->getFechaApertura()->format('mY');

        return $idClienteAdif
                . ord($primera_letra)
                . ord($segunda_letra)
                . $numero
                . '00'
                . $vencimiento_contrato;
    }    

}
