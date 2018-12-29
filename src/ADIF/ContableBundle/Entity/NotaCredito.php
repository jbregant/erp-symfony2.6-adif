<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotaCredito
 *
 * @ORM\Table(name="nota_credito")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteCompraRepository")
 */
class NotaCredito extends ComprobanteCompra {

    public function getEsNotaCredito() {
        return true;
    }

}
