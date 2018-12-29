<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoDeclaracionJurada;
use Doctrine\ORM\Mapping as ORM;

/**
 * DeclaracionJuradaImpuestoSICORE
 *
 * @ORM\Table(name="declaracion_jurada_impuesto_sicore")
 * @ORM\Entity
 */
class DeclaracionJuradaImpuestoSICORE extends DeclaracionJuradaImpuesto {

    /**
     * 
     * @return type
     */
    public function getTipoDeclaracionJurada() {
        return ConstanteTipoDeclaracionJurada::SICORE;
    }

    /**
     * 
     * @return string
     */
    public function getImpuestoLabel() {
        return 'iva-y-ganancias';
    }
    
    /**
     * 
     * @return string
     */
    public function getFilePath() {
        return 'generar_sicore';
    }

}
