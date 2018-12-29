<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotaCreditoObra
 *
 * @ORM\Table(name="nota_credito_obra")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteObraRepository")
 */
class NotaCreditoObra extends ComprobanteObra {

    public function getEsNotaCredito() {
        return true;
    }

}
