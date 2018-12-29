<?php

namespace ADIF\ContableBundle\Entity\Constantes;

use ADIF\ContableBundle\Entity\Obras\ComprobanteObra;
use ADIF\ContableBundle\Entity\Obras\FacturaObra;
use ADIF\ContableBundle\Entity\Obras\NotaCreditoObra;
use ADIF\ContableBundle\Entity\Obras\NotaDebitoInteresObra;
use ADIF\ContableBundle\Entity\Obras\NotaDebitoObra;
use ADIF\ContableBundle\Entity\Obras\ReciboObra;
use ADIF\ContableBundle\Entity\Obras\TicketFacturaObra;

/**
 * Description of ConstanteTipoComprobanteObra
 *
 * @author Manuel Becerra
 * created 07/06/2015
 */
class ConstanteTipoComprobanteObra {

    /**
     * FACTURA
     */
    const FACTURA = 1;

    /**
     * NOTA_DEBITO
     */
    const NOTA_DEBITO = 2;

    /**
     * NOTA_CREDITO
     */
    const NOTA_CREDITO = 3;

    /**
     * RECIBO
     */
    const RECIBO = 4;

    /**
     * TICKET_FACTURA
     */
    const TICKET_FACTURA = 5;

    /**
     * CUPON
     */
    const CUPON = 7;

    /**
     * NOTA_DEBITO_INTERESES
     */
    const NOTA_DEBITO_INTERESES = 8;

    /**
     * 
     * @param type $idTipoComprobante
     * @return ReciboObra|TicketFacturaObra|FacturaObra|ComprobanteObra|NotaDebitoInteresObra|NotaCreditoObra|NotaDebitoObra
     */
    public static function getSubclass($idTipoComprobante = ConstanteTipoComprobanteObra::FACTURA) {
        switch ($idTipoComprobante) {
            case ConstanteTipoComprobanteObra::FACTURA:
                return new FacturaObra();
            case ConstanteTipoComprobanteObra::RECIBO:
                return new ReciboObra();
            case ConstanteTipoComprobanteObra::NOTA_DEBITO:
                return new NotaDebitoObra();
            case ConstanteTipoComprobanteObra::NOTA_CREDITO:
                return new NotaCreditoObra();
            case ConstanteTipoComprobanteObra::NOTA_DEBITO_INTERESES:
                return new NotaDebitoInteresObra();
            case ConstanteTipoComprobanteObra::TICKET_FACTURA:
                return new TicketFacturaObra();
            default:
                return new ComprobanteObra();
        }
    }

}
