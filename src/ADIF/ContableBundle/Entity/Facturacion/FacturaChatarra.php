<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * FacturaChatarra
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="factura_chatarra")
 * @ORM\Entity(repositoryClass="ADIF\ContableBundle\Repository\ComprobanteVentaRepository")
 */
class FacturaChatarra extends FacturaIngreso implements BaseAuditable {
    
}
