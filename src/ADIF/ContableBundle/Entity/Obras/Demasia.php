<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;

/**
 * Demasia
 * 
 * @ORM\Table(name="demasia")
 * @ORM\Entity
 */
class Demasia extends DocumentoFinanciero {

    /**
     * 
     * @return boolean
     */
    public function getEsDemasia() {
        return true;
    }

}
