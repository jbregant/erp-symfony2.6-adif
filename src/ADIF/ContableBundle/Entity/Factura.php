<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Factura
 *
 * @ORM\Table(name="factura")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteCompraRepository")
 */
class Factura extends ComprobanteCompra {
    
}
