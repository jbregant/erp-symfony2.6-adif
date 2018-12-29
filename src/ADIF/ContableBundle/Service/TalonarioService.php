<?php

namespace ADIF\ContableBundle\Service;

use ADIF\ContableBundle\Entity\Facturacion\Talonario;

/**
 * Description of TalonarioService
 */
class TalonarioService {

    protected $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * 
     * @param Talonario $talonario
     * @return type
     */
    public function getSiguienteNumeroComprobante(Talonario $talonario) {

        $siguienteNumero = $talonario->getNumeroSiguiente();

        if ($siguienteNumero >= $talonario->getNumeroHasta()) {

            $talonario->setEstaAgotado(true);
        }

        $talonario->setNumeroSiguiente($siguienteNumero + 1);

        return str_pad($siguienteNumero, 8, '0', STR_PAD_LEFT);
    }

}
