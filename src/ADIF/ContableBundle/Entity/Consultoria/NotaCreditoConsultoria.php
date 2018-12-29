<?php

namespace ADIF\ContableBundle\Entity\Consultoria;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotaCreditoConsultoria
 *
 * @ORM\Table(name="nota_credito_consultoria")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteConsultoriaRepository")
 */
class NotaCreditoConsultoria extends ComprobanteConsultoria {

    public function getEsNotaCredito() {
        return true;
    }

}
