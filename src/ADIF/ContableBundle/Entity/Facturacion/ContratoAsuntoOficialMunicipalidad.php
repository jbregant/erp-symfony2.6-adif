<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * ContratoAsuntoOficialMunicipalidad
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="contrato_asunto_oficial_municipalidad")
 * @ORM\Entity
 */
class ContratoAsuntoOficialMunicipalidad extends ContratoAlquiler implements BaseAuditable {

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

        $this->calculaIVA = FALSE;
    }
    
    /**
     * 
     * @return type
     */
    public function getImpOpEx($comprobante) {
        return $comprobante->getImporteTotalExento();
    }      

}
