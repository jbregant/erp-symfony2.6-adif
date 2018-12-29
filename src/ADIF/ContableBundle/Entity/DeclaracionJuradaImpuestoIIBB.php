<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoDeclaracionJurada;
use Doctrine\ORM\Mapping as ORM;

/**
 * DeclaracionJuradaImpuestoIIBB
 *
 * @ORM\Table(name="declaracion_jurada_impuesto_iibb")
 * @ORM\Entity
 */
class DeclaracionJuradaImpuestoIIBB extends DeclaracionJuradaImpuesto {

    /**
     * 
     * @return type
     */
    public function getTipoDeclaracionJurada() {
        return ConstanteTipoDeclaracionJurada::IIBB;
    }

    /**
     * 
     * @return string
     */
    public function getImpuestoLabel() {
        return 'iibb';
    }
    
    /**
     * 
     * @return string
     */
    public function getFilePath() {
        return 'generar_arciba';
    }

}
