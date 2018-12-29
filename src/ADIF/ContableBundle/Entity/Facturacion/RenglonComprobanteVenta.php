<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\RenglonComprobante;
use ADIF\ContableBundle\Entity\Facturacion\IRenglonComprobanteVenta;

/**
 * RenglonComprobanteVenta
 *
 * @author Manuel Becerra
 * created 23/02/2015
 * 
 * @ORM\Table(name="renglon_comprobante_venta")
 * @ORM\Entity 
 */
class RenglonComprobanteVenta extends RenglonComprobante implements IRenglonComprobanteVenta 
{
    
}
