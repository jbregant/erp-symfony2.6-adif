<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotaDebito
 *
 * @ORM\Table(name="nota_debito")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteCompraRepository")
 */
class NotaDebito extends ComprobanteCompra {
    
}
