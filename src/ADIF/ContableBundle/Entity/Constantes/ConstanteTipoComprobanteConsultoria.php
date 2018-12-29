<?php

namespace ADIF\ContableBundle\Entity\Constantes;

use ADIF\ContableBundle\Entity\Consultoria\FacturaConsultoria;
use ADIF\ContableBundle\Entity\Consultoria\ReciboConsultoria;
use ADIF\ContableBundle\Entity\Consultoria\NotaCreditoConsultoria;
use ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria;

/**
 * Description of ConstanteTipoComprobanteConsultoria
 */
class ConstanteTipoComprobanteConsultoria {

    /**
     * Factura
     */
    const FACTURA = 1;

    /**
     * Nota de crédito
     */
    const NOTA_CREDITO = 3;

    /**
     * Recibo
     */
    const RECIBO = 4;

    /**
     * 
     * @param type $idTipoComprobante
     * @return NotaCredito|Recibo|Factura
     */
    public static function getSubclass($idTipoComprobante = ConstanteTipoComprobanteConsultoria::FACTURA) {
        switch ($idTipoComprobante) {
            case ConstanteTipoComprobanteConsultoria::FACTURA:
                return new FacturaConsultoria();
            case ConstanteTipoComprobanteConsultoria::NOTA_CREDITO:
                return new NotaCreditoConsultoria();
            case ConstanteTipoComprobanteConsultoria::RECIBO:
                return new ReciboConsultoria();
            default:
                return new ComprobanteConsultoria();
        }
    }

}
