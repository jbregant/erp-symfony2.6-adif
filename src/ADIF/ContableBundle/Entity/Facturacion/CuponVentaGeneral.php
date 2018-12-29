<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteClaseContrato;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CuponVentaGeneral
 *
 * @author Manuel Becerra
 * created 17/07/2015
 * 
 * @ORM\Table(name="cupon_venta_general")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\CuponVentaRepository")
 */
class CuponVentaGeneral extends CuponVenta implements BaseAuditable {

    /**
     * @var string
     *
     * @ORM\Column(name="numero_onabe", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número Onabe no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroOnabe;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_contrato", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número de contrato no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroContrato;
    
    /**
     * Set numeroContrato
     *
     * @param string $numeroContrato
     * @return Contrato
     */
    public function setNumeroContrato($numeroContrato) {
        $this->numeroContrato = $numeroContrato;

        return $this;
    }

    /**
     * Get numeroContrato
     *
     * @return string 
     */
    public function getNumeroContrato() {
        return $this->numeroContrato;
    }
        
    /**
     * Set numeroOnabe
     *
     * @param string $numeroOnabe
     * @return Contrato
     */
    public function setNumeroOnabe($numeroOnabe) {
        $this->numeroOnabe = $numeroOnabe;

        return $this;
    }

    /**
     * Get numeroOnabe
     *
     * @return string 
     */
    public function getNumeroOnabe() {
        return $this->numeroOnabe;
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

        return ConstanteClaseContrato::VENTA_GENERAL;
    }

    /**
     * 
     * @return boolean
     */
    public function esComprobanteVentaGeneral() {
        return true;
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

        $vencimiento_contrato = $this->getFechaVencimiento()->format('mY');

        return $idClienteAdif
                . ord($primera_letra)
                . ord($segunda_letra)
                . $numero
                . '00'
                . $vencimiento_contrato;        
    }

}
