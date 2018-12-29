<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;

/**
 * Economia
 * 
 * @ORM\Table(name="economia")
 * @ORM\Entity
 */
class Economia extends DocumentoFinanciero {

    /**
     * 
     * @return boolean
     */
    public function getEsEconomia() {
        return true;
    }

}
