<?php

namespace ADIF\ContableBundle\Entity\Constantes;

use ADIF\ContableBundle\Entity\Obras\AnticipoFinanciero;
use ADIF\ContableBundle\Entity\Obras\CertificadoObra;
use ADIF\ContableBundle\Entity\Obras\Demasia;
use ADIF\ContableBundle\Entity\Obras\DocumentoFinanciero;
use ADIF\ContableBundle\Entity\Obras\Economia;
use ADIF\ContableBundle\Entity\Obras\FondoReparo;
use ADIF\ContableBundle\Entity\Obras\RedeterminacionObra;

/**
 * Description of ConstanteTipoDocumentoFinanciero
 *
 */
class ConstanteTipoDocumentoFinanciero {

    /**
     * CERTIFICADO_OBRA
     */
    const CERTIFICADO_OBRA = 1;

    /**
     * REDETERMINACION_OBRA
     */
    const REDETERMINACION_OBRA = 2;

    /**
     * ANTICIPO_FINANCIERO
     */
    const ANTICIPO_FINANCIERO = 3;

    /**
     * FONDO_REPARO
     */
    const FONDO_REPARO = 4;

    /**
     * ECONOMIA
     */
    const ECONOMIA = 5;

    /**
     * DEMASIA
     */
    const DEMASIA = 6;

    /**
     * 
     * @param type $idTipoDocumentoFinanciero
     * @return Economia|Demasia|DocumentoFinanciero|CertificadoObra|FondoReparo|AnticipoFinanciero|RedeterminacionObra
     */
    public static function getSubclass($idTipoDocumentoFinanciero = ConstanteTipoDocumentoFinanciero::CERTIFICADO_OBRA) {
        switch ($idTipoDocumentoFinanciero) {

            case ConstanteTipoDocumentoFinanciero::CERTIFICADO_OBRA:
                return new CertificadoObra();

            case ConstanteTipoDocumentoFinanciero::REDETERMINACION_OBRA:
                return new RedeterminacionObra();

            case ConstanteTipoDocumentoFinanciero::ANTICIPO_FINANCIERO:
                return new AnticipoFinanciero();

            case ConstanteTipoDocumentoFinanciero::FONDO_REPARO:
                return new FondoReparo();

            case ConstanteTipoDocumentoFinanciero::ECONOMIA:
                return new Economia();

            case ConstanteTipoDocumentoFinanciero::DEMASIA:
                return new Demasia();

            default:
                return new DocumentoFinanciero();
        }
    }

}
