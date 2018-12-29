<?php

namespace ADIF\ContableBundle\Entity\Obras;

use Doctrine\ORM\Mapping as ORM;
use ADIF\ContableBundle\Entity\Provisorio;

/**
 * ProvisorioObra
 *
 * @author Manuel Becerra
 * created 20/10/2014
 * 
 * @ORM\Table(name="provisorio_obra")
 * @ORM\Entity
 */
class ProvisorioObra extends Provisorio {

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->esManual = true;
    }

}
