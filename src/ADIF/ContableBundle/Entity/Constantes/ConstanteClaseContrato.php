<?php

namespace ADIF\ContableBundle\Entity\Constantes;

use ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria;
use ADIF\ContableBundle\Entity\Facturacion\ContratoAlquiler;
use ADIF\ContableBundle\Entity\Facturacion\ContratoAlquilerAgropecuario;
use ADIF\ContableBundle\Entity\Facturacion\ContratoAlquilerComercial;
use ADIF\ContableBundle\Entity\Facturacion\ContratoAlquilerVivienda;
use ADIF\ContableBundle\Entity\Facturacion\ContratoAsuntoOficialMunicipalidad;
use ADIF\ContableBundle\Entity\Facturacion\ContratoChatarra;
use ADIF\ContableBundle\Entity\Facturacion\ContratoServidumbreDePaso;
use ADIF\ContableBundle\Entity\Facturacion\ContratoTenenciaPrecaria;
use ADIF\ContableBundle\Entity\Facturacion\ContratoVentaPlazo;

/**
 * ConstanteClaseContrato
 *
 * @author Manuel Becerra
 * created 26/01/2015
 */
class ConstanteClaseContrato {

    /**
     * ALQUILER_VIVIENDA
     */
    const ALQUILER_VIVIENDA = 1;

    /**
     * ALQUILER_COMERCIAL
     */
    const ALQUILER_COMERCIAL = 2;

    /**
     * ALQUILER_AGROPECUARIO
     */
    const ALQUILER_AGROPECUARIO = 3;

    /**
     * TENENCIA_PRECARIA
     */
    const TENENCIA_PRECARIA = 4;

    /**
     * CHATARRA
     */
    const CHATARRA = 5;

    /**
     * VENTA_A_PLAZO
     */
    const VENTA_A_PLAZO = 6;

    /**
     * SERVIDUMBRES_DE_PASO
     */
    const SERVIDUMBRE_DE_PASO = 7;

    /**
     * ASUNTO_OFICIAL_MUNICIPALIDAD
     */
    const ASUNTO_OFICIAL_MUNICIPALIDAD = 8;

    /**
     * PLIEGO
     */
    const PLIEGO = 9;

    /**
     * LOCACION_SERVICIOS
     */
    const LOCACION_SERVICIOS = 10;

    /**
     * VENTA_GENERAL
     */
    const VENTA_GENERAL = 11;

    /**
     * 
     * @param type $idClaseContrato
     * @return ContratoAlquilerVivienda|ContratoAlquilerComercial|
     *  ContratoTenenciaPrecaria|ContratoServidumbreDePaso|
     *  ContratoAlquiler|ContratoAlquilerAgropecuario|
     *  ContratoVentaPlazo|ContratoChatarra|ContratoAsuntoOficialMunicipalidad
     */
    public static function getSubclass($idClaseContrato = self::ALQUILER) {
        switch ($idClaseContrato) {
            case self::ALQUILER_VIVIENDA:
                return new ContratoAlquilerVivienda();
            case self::ALQUILER_COMERCIAL:
                return new ContratoAlquilerComercial();
            case self::ALQUILER_AGROPECUARIO:
                return new ContratoAlquilerAgropecuario();
            case self::TENENCIA_PRECARIA:
                return new ContratoTenenciaPrecaria();
            case self::CHATARRA:
                return new ContratoChatarra();
            case self::VENTA_A_PLAZO:
                return new ContratoVentaPlazo();
            case self::SERVIDUMBRE_DE_PASO:
                return new ContratoServidumbreDePaso();
            case self::ASUNTO_OFICIAL_MUNICIPALIDAD:
                return new ContratoAsuntoOficialMunicipalidad();
            case self::LOCACION_SERVICIOS:
                return new ContratoConsultoria();
            default:
                return new ContratoAlquiler();
        }
    }

}
