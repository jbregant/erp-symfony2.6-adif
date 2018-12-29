<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use Doctrine\ORM\Mapping as ORM;
use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use ADIF\ContableBundle\Entity\Comprobante;

/**
 * ComprobanteRendicionLiquidoProducto
 *
 * @ORM\Table(name="comprobante_rendicion_liquido_producto")
 * @ORM\Entity
 */
class ComprobanteRendicionLiquidoProducto extends ComprobanteVenta implements BaseAuditable
{
	
	// ID 310 = 4.0.00.03.02.00.00.00 - Ingresos por Venta de Rezagos
	const ID_CUENTA_CREDITO = 310;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="str_punto_venta", type="string", length=11)
     */
    protected $strPuntoVenta;

    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set puntoVenta
     *
     * @param string $puntoVenta
     * @return ComprobanteRendicionLiquidoProducto
     */
    public function setStrPuntoVenta($puntoVenta)
    {
        $this->strPuntoVenta = $puntoVenta;

        return $this;
    }

    /**
     * Get puntoVenta
     *
     * @return string 
     */
    public function getStrPuntoVenta()
    {
        return $this->strPuntoVenta;
    }

    /**
     * Get esRendicionLiquidoProducto
     *
     * @return boolean 
     */

    public function getEsRendicionLiquidoProducto() 
	{
        return true;
    }
	
	/**
     * 
     * @return type
     */
    public function getNumeroCompleto() 
	{
        return ($this->strPuntoVenta ? $this->strPuntoVenta . '-' : '' ) . $this->numero;
    }
	
	public function esComprobanteRendicionLiquidoProducto()
	{
		return true;
	}
	
	public function getTextoParaAsiento()
	{
        $letra = $this->getLetraComprobante() != null
                ? $this->getLetraComprobante()->getLetra()
                : null;
		$texto = 'rendici&oacute;n l&iacute;quido producto ';
		$texto .= $this->getTipoComprobante()->getNombre() . ' '; 
        if ($letra != null) {
            $texto .= '"' . $letra . '" ';
        }
		$texto .= $this->getNumeroCompleto();
		
		return $texto;
	}
    
    public function esComprobanteVentaGeneral()
    {
        return false;
    }

}
