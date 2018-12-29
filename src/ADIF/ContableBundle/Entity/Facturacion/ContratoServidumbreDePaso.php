<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * ContratoServidumbreDePaso
 *
 * @author Manuel Becerra
 * created 27/01/2015
 * 
 * @ORM\Table(name="contrato_servidumbre_de_paso")
 * @ORM\Entity
 */
class ContratoServidumbreDePaso extends ContratoVenta implements BaseAuditable {

    /**
     * 
     * @return type
     */
    public function getImpTotConc($comprobante) {
        return $comprobante->getImporteTotalExento();
    }

    /**
     * 
     * @return boolean
     */
    public function getEsContratoServidumbreDePaso() {
        return true;
    }

}
