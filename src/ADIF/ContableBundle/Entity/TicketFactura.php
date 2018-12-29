<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicketFactura
 *
 * @ORM\Table(name="ticket_factura")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteCompraRepository")
 */
class TicketFactura extends ComprobanteCompra {
    
}
