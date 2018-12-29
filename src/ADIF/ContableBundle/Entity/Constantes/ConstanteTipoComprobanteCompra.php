<?php

namespace ADIF\ContableBundle\Entity\Constantes;

use ADIF\ContableBundle\Entity\ComprobanteCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Entity\Factura;
use ADIF\ContableBundle\Entity\NotaCredito;
use ADIF\ContableBundle\Entity\NotaDebito;
use ADIF\ContableBundle\Entity\Recibo;
use ADIF\ContableBundle\Entity\TicketFactura;

/**
 * Description of ConstanteTipoComprobanteCompra
 *
 * @author Esteban Primost
 * created 14/11/2014
 */
class ConstanteTipoComprobanteCompra {

    /**
     * Factura
     */
    const FACTURA = 1;

    /**
     * Nota de débito
     */
    const NOTA_DEBITO = 2;

    /**
     * Nota de crédito
     */
    const NOTA_CREDITO = 3;

    /**
     * Recibo
     */
    const RECIBO = 4;

    /**
     * Ticket factura
     */
    const TICKET_FACTURA = 5;

    /**
     * Cupón
     */
    const CUPON = 7;

    /**
     * NOTA_DEBITO_INTERESES
     */
    const NOTA_DEBITO_INTERESES = 8;

    /**
     * 
     * @param type $idTipoComprobante
     * @return NotaCredito|Recibo|NotaDebito|TicketFactura|ComprobanteCompra|Factura
     */
    public static function getSubclass($idTipoComprobante = ConstanteTipoComprobanteCompra::FACTURA) {
        switch ($idTipoComprobante) {
            case ConstanteTipoComprobanteCompra::FACTURA:
                return new Factura();
            case ConstanteTipoComprobanteCompra::NOTA_DEBITO:
                return new NotaDebito();
            case ConstanteTipoComprobanteCompra::NOTA_CREDITO:
                return new NotaCredito();
            case ConstanteTipoComprobanteCompra::NOTA_DEBITO_INTERESES:
                return new NotaDebitoInteresObra();
            case ConstanteTipoComprobanteCompra::RECIBO:
                return new Recibo();
            case ConstanteTipoComprobanteCompra::TICKET_FACTURA:
                return new TicketFactura();
            default:
                return new ComprobanteCompra();
        }
    }

}
