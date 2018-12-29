<?php

namespace ADIF\ContableBundle\Entity\Consultoria;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * FacturaConsultoria
 *
 * @author Manuel Becerra
 * created 05/03/2015
 * 
 * @ORM\Table(name="factura_consultoria")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteConsultoriaRepository")
 */
class FacturaConsultoria extends ComprobanteConsultoria implements BaseAuditable {
    
}
