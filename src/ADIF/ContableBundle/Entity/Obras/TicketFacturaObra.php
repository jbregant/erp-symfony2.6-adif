<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicketFacturaObra
 *
 * @ORM\Table(name="ticket_factura_obra")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteObraRepository")
 */
class TicketFacturaObra extends ComprobanteObra {
    
}
