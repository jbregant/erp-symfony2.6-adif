<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;

/**
 * FacturaObra
 *
 * @ORM\Table(name="factura_obra")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteObraRepository")
 */
class FacturaObra extends ComprobanteObra {
    
}
