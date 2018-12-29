<?php

namespace ADIF\AutenticacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Manuel Becerra
 * created 30/06/2014
 * 
 * BaseAuditoria. 
 * 
 * Aquella clase que implemente BaseAuditable será auditable.
 * 
 * @ORM\MappedSuperclass
 */
interface BaseAuditable {
    
}
