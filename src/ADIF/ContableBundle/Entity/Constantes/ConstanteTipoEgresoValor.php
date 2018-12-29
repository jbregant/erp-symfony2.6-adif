<?php

namespace ADIF\ContableBundle\Entity\Constantes;

use ADIF\ContableBundle\Entity\EgresoValor\EgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\CajaChica;
use ADIF\ContableBundle\Entity\EgresoValor\CargoARendir;
use ADIF\ContableBundle\Entity\EgresoValor\Viaticos;
use ADIF\ContableBundle\Entity\EgresoValor\Combustible;

/**
 * Description of ConstanteTipoEgresoValor
 *
 * @author Manuel Becerra
 * created 15/01/2015
 */
class ConstanteTipoEgresoValor {

    /**
     * CAJA_CHICA
     */
    const CAJA_CHICA = 1;

    /**
     * CARGO_A_RENDIR
     */
    const CARGO_A_RENDIR = 2;

    /**
     * VIATICOS
     */
    const VIATICOS = 3;

    /**
     * COMBUSTIBLE
     */
    const COMBUSTIBLE = 4;
    
    /**
     * FONDO_FIJO_SERVICIOS
     */
    const FONDO_FIJO_SERVICIOS = 5;

    /**
     * 
     * @param type $idTipoEgresoValor
     * @return Combustible|CargoARendir|CajaChica|Viaticos|EgresoValor
     */
    public static function getSubclass($idTipoEgresoValor = self::CAJA_CHICA) {
        switch ($idTipoEgresoValor) {
            case self::CAJA_CHICA:
                return new CajaChica();
            case self::CARGO_A_RENDIR:
                return new CargoARendir();
            case self::VIATICOS:
                return new Viaticos();
            case self::COMBUSTIBLE:
                return new Combustible();
            default:
                return new EgresoValor();
        }
    }

}
