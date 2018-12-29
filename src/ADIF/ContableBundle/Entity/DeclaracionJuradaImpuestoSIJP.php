<?php

namespace ADIF\ContableBundle\Entity;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoDeclaracionJurada;
use Doctrine\ORM\Mapping as ORM;

/**
 * DeclaracionJuradaImpuestoSijp
 *
 * @ORM\Table(name="declaracion_jurada_impuesto_sijp")
 * @ORM\Entity
 */
class DeclaracionJuradaImpuestoSIJP extends DeclaracionJuradaImpuesto {

    /**
     * 
     * @return type
     */
    public function getTipoDeclaracionJurada() {
        return ConstanteTipoDeclaracionJurada::SIJP;
    }

    /**
     * 
     * @return string
     */
    public function getImpuestoLabel() {
        return 'suss';
    }
    
    
    /**
     * 
     * @return string
     */
    public function getFilePath() {
        return 'generar_sijp';
    }

}
