<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProvisorioServicio
 *
 * @author Manuel Becerra
 * created 20/10/2014
 * 
 * @ORM\Table(name="provisorio_servicio")
 * @ORM\Entity
 */
class ProvisorioServicio extends Provisorio {

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->esManual = true;
    }

}
