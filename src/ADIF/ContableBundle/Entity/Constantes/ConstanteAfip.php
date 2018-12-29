<?php

namespace ADIF\ContableBundle\Entity\Constantes;

use Symfony\Component\DependencyInjection\ContainerInterface;
use ADIF\ContableBundle\Entity\Constantes\ConstanteClaseContrato;

/**
 * Description of ConstanteAfip
 *
 * @author Augusto Villa Monte
 */
class ConstanteAfip 
{    
    const CUIT_PROD = 30710695993;
    #const CUIT_DEV = 20256905648;
	#const CUIT_DEV  = 30710695993;
	const CUIT_DEV  = 20294823140;
    const WSAA_PROD = "https://wsaa.afip.gov.ar/ws/services/LoginCms";
    const WSAA_DEV = "https://wsaahomo.afip.gov.ar/ws/services/LoginCms";
    // WSFE URLs
    const WSFE_PROD = "https://servicios1.afip.gov.ar/wsfev1/service.asmx";
    const WSFE_DEV = "https://wswhomo.afip.gov.ar/wsfev1/service.asmx";
    // WSDL addresses
    const WSFE_WSDL_PROD = "https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL";
    const WSFE_WSDL_DEV = "https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL";
    const WSAA_WSDL_PROD = "https://wsaa.afip.gov.ar/ws/services/LoginCms?WSDL";
    const WSAA_WSDL_DEV = "https://wsaahomo.afip.gov.ar/ws/services/LoginCms?WSDL";
    const WSPN3_WSDL_PROD = "https://aws.afip.gov.ar/padron-puc-ws/services/select.ContribuyenteNivel3SelectServiceImpl?wsdl";
    const WSPN3_WSDL_DEV = "https://awshomo.afip.gov.ar/padron-puc-ws/services/select.ContribuyenteNivel3SelectServiceImpl?wsdl";
    // Taken from WSDL
    const SOAP_ACTION = "http://ar.gov.afip.dif.facturaelectronica/";
    // Identificadores AFIP de los tipos de comprobante (se consultan a través del método getTiposCbte del WSFEService)
    const FACTURA_A = 1;
    const NOTA_DEBITO_A = 2;
    const NOTA_CREDITO_A = 3;
    const FACTURA_B = 6;
    const NOTA_DEBITO_B = 7;
    const NOTA_CREDITO_B = 8;
    const RECIBO_A = 4;
    //const NOTAS_VENTA_CONTADO_A = 5;
    const RECIBOS_B = 9;
    //const NOTAS_VENTA_CONTADO_B = 10;
    //const LIQUIDACION_A = 63;
    //const LIQUIDACION_B = 64;
    /*
      const Cbtes. A del Anexo I, Apartado A,inc.f),R.G.Nro. 1415 = 34;
      const Cbtes. B del Anexo I,Apartado A,inc. f),R.G. Nro. 1415 = 35;
      const Otros comprobantes A que cumplan con R.G.Nro. 1415 = 39;
      const Otros comprobantes B que cumplan con R.G.Nro. 1415 = 40;
      const Cta de Vta y Liquido prod. A = 60;
      const Cta de Vta y Liquido prod. B = 61;
     */
    const FACTURA_C = 11;
    const NOTA_DEBITO_C = 12;
    const NOTA_CREDITO_C = 13;
    const RECIBO_C = 15;
    //const Comprobante de Compra de Bienes Usados a Consumidor Final = 49;
    // Identificadores AFIP de los tipos de documento (se consultan a través del método getTiposDoc del WSFEService)
    const CUIT = 80;
    const DNI = 96;
    const PASAPORTE = 94;
    const OTRO_DOCUMENTO = 99;
    // FALTAN PASAR VARIOS TIPOS DE DOCUMENTO !!!
    // Identificadores AFIP de los tipos de iva (se consultan a través del método getTiposIva del WSFEService) 
    const CERO = 3;
    const DIEZ_COMA_CINCO = 4;
    const VEINTIUNO = 5;
    const VEINTISIETE = 6;
    //const CINCO = 8; //no está usada en el sistema
    //const DOS_COMA_CINCO = 9; //no está usada en el sistema
    // Identificadores AFIP de los tipos de impuestos (se consultan a través del método getTiposIva del WSFEService) 
    const NACIONALES = 1;
    const PROVINCIALES = 2;
    const MUNICIPALES = 3;
    const INTERNOS = 4;
    const OTRO = 99;
    // Identificadores AFIP de los tipos de moneda del comprobante
    const PESOS = 'PES';
    // FALTAN LO DEMÁS
    // Identificadores AFIP de los tipos de conceptos de un comprobante
    const PRODUCTO = 1;
    const SERVICIO = 2;
    const MIXTO = 3; // No hay en ADIF conceptos de producto y servicio en un mismo comprobante

    /**
     * 
     * @param type $letraComprobante
     * @param type $idTipoComprobante
     * @return type
     */

    public static function getTipoComprobante($letraComprobante, $idTipoComprobante) 
	{
        switch ($idTipoComprobante) {
            case ConstanteTipoComprobanteVenta::FACTURA:
                switch ($letraComprobante) {
                    case ConstanteLetraComprobante::A : return ConstanteAfip::FACTURA_A;
                    case ConstanteLetraComprobante::B : return ConstanteAfip::FACTURA_B;
                    //case ConstanteLetraComprobante::C : return ConstanteAfip::FACTURA_C;                        
                }
                break;
            case ConstanteTipoComprobanteVenta::NOTA_DEBITO:
            case ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES:
                switch ($letraComprobante) {
                    case ConstanteLetraComprobante::A : return ConstanteAfip::NOTA_DEBITO_A;
                    case ConstanteLetraComprobante::B : return ConstanteAfip::NOTA_DEBITO_B;
                    //case ConstanteLetraComprobante::C : return ConstanteAfip::NOTA_DEBITO_C;
                }
                break;
            case ConstanteTipoComprobanteVenta::NOTA_CREDITO:
                switch ($letraComprobante) {
                    case ConstanteLetraComprobante::A : return ConstanteAfip::NOTA_CREDITO_A;
                    case ConstanteLetraComprobante::B : return ConstanteAfip::NOTA_CREDITO_B;
                    //case ConstanteLetraComprobante::C : return ConstanteAfip::NOTA_CREDITO_C;
                }
                break;
        }
        return -1;
    }

    public static function getTipoIva($alicuota) 
	{
        switch ($alicuota) {
            case ConstanteAlicuotaIva::ALICUOTA_0: return ConstanteAfip::CERO;
            case ConstanteAlicuotaIva::ALICUOTA_10_5: return ConstanteAfip::DIEZ_COMA_CINCO;
            case ConstanteAlicuotaIva::ALICUOTA_21: return ConstanteAfip::VEINTIUNO;
            case ConstanteAlicuotaIva::ALICUOTA_27: return ConstanteAfip::VEINTISIETE;
        }
        return -1;
    }

    public static function getTipoConcepto($comprobante) 
	{
        $codigo = $comprobante->getCodigoClaseContrato();

        if ($codigo == ConstanteClaseContrato::CHATARRA || $codigo == ConstanteClaseContrato::PLIEGO) { // Todo es servicio menos chatarra y pliego, no hay mixto
            return ConstanteAfip::PRODUCTO;
        } else {
            return ConstanteAfip::SERVICIO;
        }
    }
}
