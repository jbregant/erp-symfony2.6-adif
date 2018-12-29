<?php

namespace ADIF\ContableBundle\Entity\Constantes;

use ADIF\ContableBundle\Entity\Constantes\ConstanteClaseContrato;
use ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta;
use ADIF\ContableBundle\Entity\Facturacion\CuponPliego;
use ADIF\ContableBundle\Entity\Facturacion\CuponVenta;
use ADIF\ContableBundle\Entity\Facturacion\CuponVentaGeneral;
use ADIF\ContableBundle\Entity\Facturacion\FacturaAlquiler;
use ADIF\ContableBundle\Entity\Facturacion\FacturaChatarra;
use ADIF\ContableBundle\Entity\Facturacion\FacturaIngreso;
use ADIF\ContableBundle\Entity\Facturacion\FacturaPliego;
use ADIF\ContableBundle\Entity\Facturacion\FacturaVenta;
use ADIF\ContableBundle\Entity\Facturacion\FacturaVentaGeneral;
use ADIF\ContableBundle\Entity\Facturacion\NotaCreditoPliego;
use ADIF\ContableBundle\Entity\Facturacion\NotaCreditoVenta;
use ADIF\ContableBundle\Entity\Facturacion\NotaCreditoVentaGeneral;
use ADIF\ContableBundle\Entity\Facturacion\NotaDebitoInteres;
use ADIF\ContableBundle\Entity\Facturacion\NotaDebitoPliego;
use ADIF\ContableBundle\Entity\Facturacion\NotaDebitoVenta;
use ADIF\ContableBundle\Entity\Facturacion\NotaDebitoVentaGeneral;
use ADIF\ContableBundle\Entity\Facturacion\CuponVentaPlazo;
use ADIF\ContableBundle\Entity\Facturacion\ComprobanteRendicionLiquidoProducto;

/**
 * Description of ConstanteTipoComprobanteVenta
 *
 * @author Manuel Becerra
 * created 23/02/2015
 */
class ConstanteTipoComprobanteVenta {

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
     * CUPON
     */
    const CUPON = 7;

    /**
     * NOTA_DEBITO_INTERESES
     */
    const NOTA_DEBITO_INTERESES = 8;

    /**
     * RENDICION_LIQUIDO_PRODUCTO
     */
    const RENDICION_LIQUIDO_PRODUCTO = 9;

    /**
     * 
     * @param type $idClaseContrato
     * @param type $idTipoComprobante
     * @return FacturaIngreso|NotaCreditoPliego|NotaDebitoPliego|NotaDebitoVenta|NotaCreditoVentaGeneral|CuponVenta|NotaCreditoVenta|FacturaChatarra|FacturaPliego|FacturaAlquiler|ComprobanteVenta|CuponPliego|NotaDebitoVentaGeneral|FacturaVentaGeneral|FacturaVenta|NotaDebitoInteres|ComprobanteRendicionLiquidoProducto
     */
    public static function getSubclass($idClaseContrato, $idTipoComprobante) {
        switch ($idTipoComprobante) {
            case ConstanteTipoComprobanteVenta::FACTURA:
                switch ($idClaseContrato) {
                    case ConstanteClaseContrato::ALQUILER_AGROPECUARIO:
                    case ConstanteClaseContrato::ALQUILER_COMERCIAL:
                    case ConstanteClaseContrato::ALQUILER_VIVIENDA:
                    case ConstanteClaseContrato::TENENCIA_PRECARIA:
                    case ConstanteClaseContrato::ASUNTO_OFICIAL_MUNICIPALIDAD:
                        return new FacturaAlquiler();
                    case ConstanteClaseContrato::SERVIDUMBRE_DE_PASO:
                        return new FacturaIngreso();
                    case ConstanteClaseContrato::CHATARRA:
                        return new FacturaChatarra();
                    case ConstanteClaseContrato::PLIEGO:
                        return new FacturaPliego();
                    case ConstanteClaseContrato::VENTA_GENERAL:
                        return new FacturaVentaGeneral();
                    default:
                        return new FacturaVenta();
                }
            case ConstanteTipoComprobanteVenta::NOTA_DEBITO:
                switch ($idClaseContrato) {
                    case ConstanteClaseContrato::PLIEGO:
                        return new NotaDebitoPliego();
                    case ConstanteClaseContrato::VENTA_GENERAL:
                        return new NotaDebitoVentaGeneral();
                    default:
                        return new NotaDebitoVenta();
                }
            case ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES:
                return new NotaDebitoInteres();
            case ConstanteTipoComprobanteVenta::NOTA_CREDITO:
                switch ($idClaseContrato) {
                    case ConstanteClaseContrato::PLIEGO:
                        return new NotaCreditoPliego();
                    case ConstanteClaseContrato::VENTA_GENERAL:
                        return new NotaCreditoVentaGeneral();
                    default:
                        return new NotaCreditoVenta();
                }
            case ConstanteTipoComprobanteVenta::CUPON:
                switch ($idClaseContrato) {
                    case ConstanteClaseContrato::PLIEGO:
                        return new CuponPliego();
                    case ConstanteClaseContrato::VENTA_GENERAL:
                        return new CuponVentaGeneral();
                    case ConstanteClaseContrato::VENTA_A_PLAZO:
                        return new CuponVentaPlazo();
                    default:
                        return new CuponVenta();
                }
            case ConstanteTipoComprobanteVenta::RENDICION_LIQUIDO_PRODUCTO:
                return new ComprobanteRendicionLiquidoProducto();
            default:
                return new ComprobanteVenta();
        }
    }

    /**
     * 
     * @param type $idClaseContrato
     * @return type
     */
    public static function getSubclassFromContrato($idClaseContrato) {
        switch ($idClaseContrato) {
            case ConstanteClaseContrato::ALQUILER_VIVIENDA:
            case ConstanteClaseContrato::ALQUILER_COMERCIAL:
            case ConstanteClaseContrato::ALQUILER_AGROPECUARIO:
            case ConstanteClaseContrato::SERVIDUMBRE_DE_PASO:
            case ConstanteClaseContrato::ASUNTO_OFICIAL_MUNICIPALIDAD:
            case ConstanteClaseContrato::TENENCIA_PRECARIA:
                return ConstanteTipoComprobanteVenta::getSubclass($idClaseContrato, ConstanteTipoComprobanteVenta::FACTURA);
            case ConstanteClaseContrato::VENTA_A_PLAZO:
                return ConstanteTipoComprobanteVenta::getSubclass($idClaseContrato, ConstanteTipoComprobanteVenta::CUPON);
        }
    }

}
