<?php

namespace ADIF\ContableBundle\Entity\Constantes;

/**
 * Description of ConstanteNaturalezaCuenta
 *
 * @author Darío Rapetti
 * created 10/11/2014
 */
class ConstanteNaturalezaCuenta {

    /**
     * ACTIVO
     */
    const ACTIVO = 1;

    /**
     * PASIVO
     */
    const PASIVO = 2;

    /**
     * PATRIMONIO NETO
     */
    const PATRIMONIO_NETO = 3;

    /**
     * INGRESO
     */
    const INGRESO = 4;

    /**
     * GASTO
     */
    const GASTO = 5;

    /**
     * 
     * @return array
     */
    public static function findAll() {

        $naturalezaArray = [];

        $naturalezaArray[] = self::ACTIVO;

        $naturalezaArray[] = self::PASIVO;

        $naturalezaArray[] = self::PATRIMONIO_NETO;

        $naturalezaArray[] = self::INGRESO;

        $naturalezaArray[] = self::GASTO;

        return $naturalezaArray;
    }

}
