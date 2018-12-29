<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * ContratoTenenciaPrecaria
 *
 * @author Manuel Becerra
 * created 09/02/2015
 * 
 * @ORM\Table(name="contrato_tenencia_precaria")
 * @ORM\Entity
 */
class ContratoTenenciaPrecaria extends ContratoAlquiler implements BaseAuditable {

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
    public function getImpTotConc($comprobante) {
        return $comprobante->getImporteTotalExento();
    }  
       

}
