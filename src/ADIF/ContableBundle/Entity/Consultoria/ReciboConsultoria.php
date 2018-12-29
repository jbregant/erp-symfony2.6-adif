<?php

namespace ADIF\ContableBundle\Entity\Consultoria;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * ReciboConsultoria
 *
 * @author Manuel Becerra
 * created 05/03/2015
 * 
 * @ORM\Table(name="recibo_consultoria")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteConsultoriaRepository")
 */
class ReciboConsultoria extends ComprobanteConsultoria implements BaseAuditable {
    
}
