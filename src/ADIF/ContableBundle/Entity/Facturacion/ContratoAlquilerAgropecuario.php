<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * ContratoAlquilerAgropecuario
 *
 * @author Manuel Becerra
 * created 09/02/2015
 * 
 * @ORM\Table(name="contrato_alquiler_agropecuario")
 * @ORM\Entity
 */
class ContratoAlquilerAgropecuario extends ContratoAlquiler implements BaseAuditable {

    /**
     * 
     * @return type
     */
    public function getImpOpEx($comprobante) {
        return $comprobante->getImporteTotalExento();
    }  
    
}
