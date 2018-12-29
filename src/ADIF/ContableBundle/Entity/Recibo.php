<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recibo
 *
 * @ORM\Table(name="recibo")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteCompraRepository")
 */
class Recibo extends ComprobanteCompra {
    
}
