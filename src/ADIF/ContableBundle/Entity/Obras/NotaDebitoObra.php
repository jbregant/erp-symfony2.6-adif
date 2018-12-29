<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotaDebitoObra
 *
 * @ORM\Table(name="nota_debito_obra")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteObraRepository")
 */
class NotaDebitoObra extends ComprobanteObra {
    
}
