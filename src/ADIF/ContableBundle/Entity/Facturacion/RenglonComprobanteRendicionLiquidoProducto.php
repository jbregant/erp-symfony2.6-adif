<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\Facturacion\IRenglonComprobanteVenta;

/**
 * RenglonComprobanteRendicionLiquidoProducto
 *
 * @author Gustavo Luis
 * created 23/06/2017
 * 
 * @ORM\Table(name="renglon_comprobante_rendicion_liquido_producto")
 * @ORM\Entity 
 */
class RenglonComprobanteRendicionLiquidoProducto extends RenglonComprobanteVenta implements IRenglonComprobanteVenta
{
	
}
