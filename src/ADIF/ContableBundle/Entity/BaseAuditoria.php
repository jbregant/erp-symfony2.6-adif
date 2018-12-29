<?php

namespace ADIF\ContableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use ADIF\BaseBundle\Entity\BaseAuditoria as BaseAuditoriaGeneral;

/**
 * @author Manuel Becerra
 * created 01/09/2014
 * 
 * BaseAuditoria.
 * 
 * @ORM\MappedSuperclass 
 * @Gedmo\SoftDeleteable(fieldName="fechaBaja")
 */
class BaseAuditoria extends BaseAuditoriaGeneral {
    
}
