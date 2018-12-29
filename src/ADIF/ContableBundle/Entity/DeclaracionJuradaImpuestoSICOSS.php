<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoDeclaracionJurada;
use Doctrine\ORM\Mapping as ORM;

/**
 * DeclaracionJuradaImpuestoSICOSS
 *
 * @ORM\Table(name="declaracion_jurada_impuesto_sicoss")
 * @ORM\Entity
 */
class DeclaracionJuradaImpuestoSICOSS extends DeclaracionJuradaImpuesto {

    /**
     * 
     * @return type
     */
    public function getTipoDeclaracionJurada() {
        return ConstanteTipoDeclaracionJurada::SICOSS;
    }

    /**
     * 
     * @return string
     */
    public function getImpuestoLabel() {
        return 'sicoss';
    }
    
    /**
     * 
     * @return string
     */
    public function getFilePath() {
        return 'generar_sicoss';
    }

}
