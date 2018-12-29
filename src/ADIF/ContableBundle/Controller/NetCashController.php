<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\AdifDatos;
use ADIF\ContableBundle\Form\NetCashType;
use ADIF\ContableBundle\Entity\NetCash;
use ADIF\ContableBundle\Entity\PagoOrdenPago;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoNetCash;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;

/**
 * NetCash controller.
 *
 * @Route("/netcash")
 */
class NetCashController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Net Cash' => ''
        );
    }

    /**
     * Creates a new NetCash entity.
     *
     * @Route("/exportar/{id}", name="netcash_exportar")
     * @Method("GET|POST")
     */
    public function exportarAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $netCash \ADIF\ContableBundle\Entity\NetCash */
        $netCash = $em->getRepository('ADIFContableBundle:NetCash')->find($id);

        $char_pad_string = ' ';
        $char_pad_int = '0';
        $type_pad_string = STR_PAD_RIGHT;
        $type_pad_int = STR_PAD_LEFT;
        $salto = chr(13) . chr(10);

        //Configurar constantes
        $adif_cuit = str_replace('-', '', AdifDatos::CUIT);
        
        //Cuenta Net Cash
        $cuentaNetCash = $netCash->getCuenta()->getNumeroCuenta();
        $adif_suc_cuenta_debito = substr($cuentaNetCash, 0, 4);
        $adif_num_cuenta_debito = substr($cuentaNetCash, 4, 7);
        $adif_suc_cuenta_debito_verificador = substr($cuentaNetCash, 10);
                
        $adif_tipo_cuenta_debito = '01';
        $adif_moneda_cuenta_debito = '0';

        //Conversor IDs Provincias a provincias NETCASH
        $vec_provincias = array(
            '1' => '2', '2' => '2', '3' => '1', '4' => '3', '5' => '6',
            '6' => '7', '7' => '4', '8' => '5', '9' => '8', '10' => '9',
            '11' => '10', '12' => '11', '13' => '12', '14' => '13', '15' => '14',
            '16' => '15', '17' => '16', '18' => '17', '19' => '18', '20' => '19',
            '21' => '20', '22' => '21', '23' => '22', '24' => '23', '24' => '40',
        );

        //Contadores
        $str_secuencia = 0; //Numero de secuencia 
        $str_cantidad20 = 0; //Cantidad de registros 20        
        //Variables
        // Nombre del archivo, id de netcash
        $str_archivo = 'NC_' . str_pad($netCash->getNumero(), 8, $char_pad_int, $type_pad_int) . '.txt';

        //Fechas pasadas en rl request
        //$fecha_emision = date('Ymd'); //Formato YYYYMMDD
		$fecha_emision = $netCash->getFechaEntrega()->format('Ymd'); //Formato YYYYMMDD
        $fecha_entrega = $netCash->getFechaEntrega()->format('Ymd'); //Formato YYYYMMDD
        $fecha_pago = $netCash->getFechaPago()->format('Ymd'); //Formato YYYYMMDD

        $str10 = '';
        $str20 = '';
        $str40 = '';
        $str90 = '';
        $str95 = '';
        $strreg = '';

        foreach ($netCash->getPagosOrdenPago() as $pagoOrdenPago) {  // ITERAR POR CADA ORDEN DE PAGO
            $str20 = '';
            $str90 = '';
            $str_cantidad20++;

            /* @var $pagoOrdenPago \ADIF\ContableBundle\Entity\PagoOrdenPago */

            //***Variables****
            //Importe de la OP a pagar 2 decimales
            $str_importe = explode(",", number_format($pagoOrdenPago->getOrdenPagoPagada()->getMontoNeto(), 2, ',', ''));
            //CUIT proveedor

            /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPago */
            $ordenPago = $pagoOrdenPago->getOrdenPagoPagada();
            
            /* @var $proveedor \ADIF\ComprasBundle\Entity\Proveedor */
            $proveedor = $ordenPago->getProveedor();
            
            if(!$proveedor->getCuenta()){
                throw $this->createNotFoundException('El proveedor '.$proveedor->getCUITAndRazonSocial().' no tiene configurada una cuenta bancaria');
            }

            //---------------------
            $str_cuit_proveedor = str_replace('-', '', $proveedor->getCUIT());
            //Razon Social del proveedor
            $str_razonsocial_proveedor = $proveedor->getRazonSocial();
            //CBU del proveedor
            $str_cbu_proveedor = $proveedor->getCuenta()->getCbu();
            //Calle del proveedor
            $str_calle_proveedor = $proveedor->getDomicilio()->getCalle();
            //Numero de calle del proveedor
            $str_calle_nro_proveedor = $proveedor->getDomicilio()->getNumero();
            //Interfasear con la tabla de provincias del instructivo
            $str_codprovincia_proveedor = $vec_provincias[2];
            //Numero de orden de pago
            $str_nro_op = $ordenPago->getNumeroOrdenPago();

            $str_secuencia++;
            /* 1 */ $str20.='0306'; //ident-registro 
            /* 2 */ $str20.='020'; //tipo-registro 
            /* 3 */ $str20.='CUIT'; //tipo-doc-empre 
            /* 4 */ $str20.= str_pad($adif_cuit, 13, $char_pad_int, $type_pad_int); //nro-cuit-empre 
            /* 5 */ $str20.= str_pad($str_secuencia, 6, $char_pad_int, $type_pad_int); //secuencia 
            /* 6 */ $str20.= str_pad($str_cuit_proveedor, 15, $char_pad_int, $type_pad_int); //pro-nro-beneficiario 
            /* 7 */ $str20.= str_pad($str_nro_op, 8, $char_pad_int, $type_pad_int); //nro-minuta 
            /* 8 */ $str20.= str_pad($str_importe[0] . $str_importe[1], 13, $char_pad_int, $type_pad_int); //importe 
            /* 9 */ $str20.= str_pad('', 14, $char_pad_string, $type_pad_int); //nro-cert-ret-ganancias
            /* 10 */ $str20.= str_pad('', 30, $char_pad_string, $type_pad_int); //regimen-ganancias
            /* 11 */ $str20.= str_pad('', 13, $char_pad_int, $type_pad_int); //imp-ret-ganancias
            /* 12 */ $str20.= str_pad('', 14, $char_pad_string, $type_pad_int); //nro-cert-ret-iva
            /* 13 */ $str20.= str_pad('', 30, $char_pad_string, $type_pad_int); //regimen-iva
            /* 14 */ $str20.= str_pad($str_archivo, 15, $char_pad_string, $type_pad_int); //pro-nro-ord
            /* 15 */ $str20.= str_pad('', 8, $char_pad_string, $type_pad_int); //filler
            /* 16 */ $str20.= str_pad('', 1, $char_pad_string, $type_pad_int); //acred-a-susp
            /* 17 */ $str20.= str_pad('', 1, $char_pad_string, $type_pad_int); //ipermfin
            /* 18 */ $str20.= 'S'; //CLI-AJE
            /* 19 */ $str20.= str_pad('', 13, $char_pad_int, $type_pad_int); //ncuit-pago
            /* 20 */ $str20.= str_pad('', 40, $char_pad_string, $type_pad_int); //nome-pago
            /* 21 */ $str20.= 'CUI'; //tipo-documento
            /* 22 */ $str20.= $str_cuit_proveedor; //nro-documento
            /* 23 */ $str20.= $adif_suc_cuenta_debito; //suc-entrega
            /* 24 */ $str20.= '00000000'; //fecha-entrega
            /* 25 */ $str20.= '00000000'; //fecha-pago
            /* 26 */ $str20.= str_pad('', 2, $char_pad_string, $type_pad_int); //forma-pago
            /* 27 */ $str20.= '0'; //forma-cobro
            /* 28 */ $str20.= '0'; //dispon-p
            /* 29 */ $str20.= '0'; //deposito
            /* 30 */ $str20.= str_pad('', 13, $char_pad_int, $type_pad_int); //nro-instruccion
            /* 31 */ $str20.= str_pad('', 6, $char_pad_string, $type_pad_int); //cod-devolucion
            /* 32 */ $str20.= str_pad('', 40, $char_pad_string, $type_pad_int); //desc-devolucion
            /* 33 */ $str20.= str_pad('', 506, $char_pad_string, $type_pad_int); //filler

            $str40 = '';
            foreach ($ordenPago->getComprobantes() as $comprobante) {
                /* @var $comprobante \ADIF\ContableBundle\Entity\Comprobante */
                //\Doctrine\Common\Util\Debug::dump($comprobante);
                //Fecha del comprobante
                $fecha_comprobante = $comprobante->getFechaComprobante()->format('Ymd');

                //Tipo de Comprobante
                $desc_comprobante = $comprobante->getTipoComprobante();

                //Factura DB  o Nota de Credito CR
                $tipo_comprobante = $comprobante->getEsNotaCredito() ? 'DB' : 'CR';

                //Letra del comprobante
                $letra_comprobante = $comprobante->getLetraComprobante();

                $numero_comprobante = $comprobante->getPuntoVenta() . $comprobante->getNumero();

                //Importe bruto comprobante a pagar 2 decimales
                $importe_comprobante = explode(",", number_format($comprobante->getTotal(), 2, ',', ''));

                $str_secuencia++;
                /* 1 */ $str40.='0306'; //ident-registro 
                /* 2 */ $str40.='040'; //tipo-registro 
                /* 3 */ $str40.='CUIT'; //tipo-doc-empre 
                /* 4 */ $str40.= str_pad($adif_cuit, 13, $char_pad_int, $type_pad_int); //nro-cuit-empre 
                /* 5 */ $str40.= str_pad($str_secuencia, 6, $char_pad_int, $type_pad_int); //secuencia 
                /* 6 */ $str40.= str_pad('', 2, $char_pad_string, $type_pad_int); //filler
                /* 7 */ $str40.= $fecha_comprobante; //fecha-comp-minuta
                /* 8 */ $str40.= str_pad($desc_comprobante, 25, $char_pad_string, $type_pad_string); //desc-comp-minuta
                /* 9 */ $str40.= $tipo_comprobante; //comp-db-cr
                /* 10 */ $str40.= $letra_comprobante; //tipo-comp-minuta 
                /* 11 */ $str40.= str_pad($numero_comprobante, 12, $char_pad_string, $type_pad_int); //nro-comp-minuta
                /* 12 */ $str40.= str_pad($importe_comprobante[0] . $importe_comprobante[1], 13, $char_pad_int, $type_pad_int); //importe-comp-minuta
                /* 13 */ $str40.= str_pad('', 2, $char_pad_string, $type_pad_int); //cod-impuesto
                /* 14 */ $str40.= str_pad(0, 5, $char_pad_int, $type_pad_int); //alicuota-1-minuta
                /* 15 */ $str40.= str_pad(0, 13, $char_pad_int, $type_pad_int); //importe-1-minuta
                /* 16 */ $str40.= str_pad(0, 5, $char_pad_int, $type_pad_int); //alicuota-2-minuta
                /* 17 */ $str40.= str_pad(0, 13, $char_pad_int, $type_pad_int); //importe-2-minuta
                /* 18 */ $str40.= str_pad('', 719, $char_pad_string, $type_pad_int); //filler 

                $str40.= $salto;
            } //FIN PAGO
            //Un registro por Orden de Pago
            $str_secuencia++;
            /* 1 */ $str90.= '0306'; //ident-registro 
            /* 2 */ $str90.= '090'; //tipo-registro 
            /* 3 */ $str90.= 'CUIT'; //tipo-doc-empre 
            /* 4 */ $str90.= str_pad($adif_cuit, 13, $char_pad_int, $type_pad_int); //nro-cuit-empre 
            /* 5 */ $str90.= str_pad($str_secuencia, 6, $char_pad_int, $type_pad_int); //secuencia 
            /* 6 */ $str90.= str_pad($str_archivo, 15, $char_pad_string, $type_pad_int); //pro-nro-ord
            /* 7 */ $str90.= str_pad($str_cuit_proveedor, 15, $char_pad_int, $type_pad_int); //pro-nro-beneficiario 
            /* 8 */ $str90.= '1'; //pro-est-benef
            /* 9 */ $str90.= 'CUI'; //pro-docto-tip
            /* 10 */ $str90.= $str_cuit_proveedor; //pro-docto-nro
            /* 11 */ $str90.= str_pad($str_razonsocial_proveedor, 40, $char_pad_string, $type_pad_string); //pro-denomina
            /* 12 */ $str90.= str_pad('', 2, $char_pad_string, $type_pad_int); //pro-catego
            /* 13 */ $str90.= str_pad('', 1, $char_pad_string, $type_pad_int); //pro-permit-finan
            /* 14 */ $str90.= str_pad(0, 2, $char_pad_int, $type_pad_int); //pro-cus-tip
            /* 15 */ $str90.= str_pad(0, 3, $char_pad_int, $type_pad_int); //pro-cus-suc
            /* 16 */ $str90.= str_pad(0, 10, $char_pad_int, $type_pad_int); //pro-cus-nro
            /* 17 */ $str90.= $str_cbu_proveedor; //pro-cbu-nro
            /* 18 */ $str90.= str_pad(0, 11, $char_pad_int, $type_pad_int); //pro-ingbrts
            /* 19 */ $str90.= str_pad($str_calle_proveedor, 24, $char_pad_string, $type_pad_string); //pro-calle
            /* 20 */ $str90.= str_pad($str_calle_nro_proveedor, 5, $char_pad_string, $type_pad_string); //pro-numero
            /* 21 */ $str90.= str_pad('', 3, $char_pad_string, $type_pad_int); //pro-depto 
            /* 22 */ $str90.= str_pad('', 2, $char_pad_string, $type_pad_int); //pro-piso
            /* 23 */ $str90.= str_pad('', 28, $char_pad_string, $type_pad_int); //pro-localid 
            /* 24 */ $str90.= str_pad('', 5, $char_pad_string, $type_pad_int); //pro-cpostal 
            /* 25 */ $str90.= str_pad($str_codprovincia_proveedor, 2, $char_pad_int, $type_pad_int); //pro-codprov
            /* 26 */ $str90.= '080'; //pro-codpais
            /* 27 */ $str90.= str_pad('', 40, $char_pad_string, $type_pad_int); //pro-email
            /* 28 */ $str90.= str_pad('', 24, $char_pad_string, $type_pad_int); //pro-calle-entrega
            /* 29 */ $str90.= str_pad('', 5, $char_pad_string, $type_pad_int); //pro-numero-entrega
            /* 30 */ $str90.= str_pad('', 3, $char_pad_string, $type_pad_int); //pro-depto-entrega
            /* 31 */ $str90.= str_pad('', 2, $char_pad_string, $type_pad_int); //pro-piso-entrega
            /* 32 */ $str90.= str_pad('', 28, $char_pad_string, $type_pad_int); //pro-localid-entrega
            /* 33 */ $str90.= str_pad('', 5, $char_pad_string, $type_pad_int); //pro-cpostal-entrega
            /* 34 */ $str90.= str_pad('', 2, $char_pad_string, $type_pad_int); //pro-codprov-entrega
            /* 35 */ $str90.= str_pad('', 3, $char_pad_string, $type_pad_int); //pro-codpais-entrega
            /* 36 */ $str90.= str_pad('', 2, $char_pad_string, $type_pad_int); //pro-telef-tipo
            /* 37 */ $str90.= str_pad('', 4, $char_pad_string, $type_pad_int); //pro-telef-pre
            /* 38 */ $str90.= str_pad('', 4, $char_pad_string, $type_pad_int); //pro-telef-car
            /* 39 */ $str90.= str_pad('', 5, $char_pad_string, $type_pad_int); //pro-telef-nro
            /* 40 */ $str90.= str_pad('', 18, $char_pad_string, $type_pad_int); //pro-telef-int
            /* 41 */ $str90.= str_pad('', 2, $char_pad_string, $type_pad_int); //pro-telef-alter-tip
            /* 42 */ $str90.= str_pad('', 4, $char_pad_string, $type_pad_int); //pro-telef-alter-pre
            /* 43 */ $str90.= str_pad('', 4, $char_pad_string, $type_pad_int); //pro-telef-alter-car
            /* 44 */ $str90.= str_pad('', 5, $char_pad_string, $type_pad_int); //pro-telef-alter-nro
            /* 45 */ $str90.= str_pad('', 18, $char_pad_string, $type_pad_int); //pro-telef-alter-int
            /* 46 */ $str90.= str_pad('', 25, $char_pad_string, $type_pad_int); //pro-autoriza-nom1
            /* 47 */ $str90.= str_pad('', 3, $char_pad_string, $type_pad_int); //pro-autoriza-tip1
            /* 48 */ $str90.= str_pad('', 8, $char_pad_int, $type_pad_int); //pro-autoriza-doc1
            /* 49 */ $str90.= str_pad('', 25, $char_pad_string, $type_pad_int); //pro-autoriza-nom2
            /* 50 */ $str90.= str_pad('', 3, $char_pad_string, $type_pad_int); //pro-autoriza-tip2
            /* 51 */ $str90.= str_pad('', 8, $char_pad_int, $type_pad_int); //pro-autoriza-doc2
            /* 52 */ $str90.= str_pad('', 25, $char_pad_string, $type_pad_int); //pro-autoriza-nom3
            /* 53 */ $str90.= str_pad('', 3, $char_pad_string, $type_pad_int); //pro-autoriza-tip3
            /* 54 */ $str90.= str_pad('', 8, $char_pad_int, $type_pad_int); //pro-autoriza-doc3
            /* 55 */ $str90.= str_pad('', 100, $char_pad_string, $type_pad_int); //pro-datos
            /* 56 */ $str90.= str_pad($str_nro_op, 8, $char_pad_int, $type_pad_int); //nro-minuta 
            /* 57 */ $str90.= str_pad('', 218, $char_pad_string, $type_pad_int); //filler
            //$suma_importe = $suma_importe + 88888;
            $strreg.= $str20 . $salto . $str40 . $str90 . $salto;
        } //FIN OP 
        //
        //Importe total de pagos lote 2 decimales
        $str_suma_importe = explode(",", number_format($netCash->getMonto(), 2, ',', ''));

        //cantidad de registros con la cabecera y pie
        $str_secuencia++;
        $str_total_reg = $str_secuencia + 1;

        //***** REGISTRO PIE *****
        /* 1 */ $str95.= '0306'; //ident-registro 
        /* 2 */ $str95.= '095'; //tipo-registro 
        /* 3 */ $str95.= 'CUIT'; //tipo-doc-empre 
        /* 4 */ $str95.= str_pad($adif_cuit, 13, $char_pad_int, $type_pad_int); //nro-cuit-empre 
        /* 5 */ $str95.= str_pad($str_secuencia, 6, $char_pad_int, $type_pad_int); //secuencia 
        /* 6 */ $str95.= str_pad($str_suma_importe[0] . $str_suma_importe[1], 13, $char_pad_int, $type_pad_int); //sumaimporte
        /* 7 */ $str95.= str_pad($str_cantidad20, 7, $char_pad_int, $type_pad_int); //cant-pagos 
        /* 8 */ $str95.= str_pad($str_total_reg, 10, $char_pad_int, $type_pad_int); //tot-reg
        /* 9 */ $str95.= str_pad('', 790, $char_pad_string, $type_pad_int); //filler
        //***** REGISTRO CABECERA *****

        /* 1 */ $str10.='0306'; //ident-registro 
        /* 2 */ $str10.='010'; //tipo-registro 
        /* 3 */ $str10.='CUIT'; //tipo-doc-empre 
        /* 4 */ $str10.= str_pad($adif_cuit, 13, $char_pad_int, $type_pad_int); //nro-cuit-empre 
        /* 5 */ $str10.= str_pad(0, 6, $char_pad_int, $type_pad_int); //secuencia 
        /* 6 */ $str10.= '0'; //moneda 
        /* 7 */ $str10.= str_pad($str_suma_importe[0] . $str_suma_importe[1], 13, $char_pad_int, $type_pad_int); //importe 
        /* 8 */ $str10.= 'AB'; //forma-pago 
        /* 9 */ $str10.= '0'; //forma-cobro 
        /* 10 */ $str10.= '0'; //dispon-pago 
        /* 11 */ $str10.= '0'; //deposito 
        /* 12 */ $str10.= $fecha_emision; //fecha-emision 
        /* 13 */ $str10.= $fecha_entrega; //fecha-entrega 
        /* 14 */ $str10.= $fecha_pago; //fecha-pago 
        /* 15 */ $str10.= '0017'; //entidad 
        /* 16 */ $str10.= $adif_suc_cuenta_debito; //suc-cta-debito 
        /* 17 */ $str10.= str_pad($adif_suc_cuenta_debito_verificador, 2, $char_pad_int, $type_pad_int); //dv-cta-debito 
        /* 18 */ $str10.= $adif_tipo_cuenta_debito; //tipo-cta-debito 
        /* 19 */ $str10.= $adif_moneda_cuenta_debito; //moneda-cta-debito 
        /* 20 */ $str10.= $adif_num_cuenta_debito; //numero-cta-debito 
        /* 21 */ $str10.= str_pad($str_cantidad20, 7, $char_pad_int, $type_pad_int); //cantidad-inst
        /* 22 */ $str10.= str_pad('', 1, $char_pad_string, $type_pad_int); //entrega-lote
        /* 23 */ $str10.= str_pad('', 4, $char_pad_string, $type_pad_int); //suc-entrega-lote 
        /* 24 */ $str10.= str_pad('', 6, $char_pad_string, $type_pad_int); //filler 
        /* 25 */ $str10.= str_pad('', 1, $char_pad_string, $type_pad_int); //libre-impresion 
        /* 26 */ $str10.= str_pad('', 12, $char_pad_string, $type_pad_int); //nombre-fichero 
        /* 27 */ $str10.= $fecha_emision; //fecha-proceso
        /* 28 */ $str10.= str_pad('', 20, $char_pad_string, $type_pad_int); //contrato-prov
        /* 29 */ $str10.= str_pad('', 698, $char_pad_string, $type_pad_int); //filler 
        //Concateno para el armado del registros
        $str = $str10 . $salto . $strreg . $str95;

        // GENERO EL ARCHIVO
        
        $filePath = $this->get('kernel')->getRootDir() . '/../web/uploads/netcash/proveedores/' . $str_archivo;
        
        $f = fopen($filePath, "w");
        fwrite($f, $str);
        fclose($f);       
        
        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'text/plaine');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filePath) . '"');
        $response->headers->set('Content-length', filesize($filePath));

        $response->setContent(file_get_contents($filePath));

        return $response;
        
    }

    /**
     * Displays a form to edit an existing NetCash entity.
     *
     * @Route("/editar/{id}", name="netcash_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:NetCash:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:NetCash')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad NetCash.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Net Cash'
        );
    }

    /**
     * Creates a form to edit a NetCash entity.
     *
     * @param NetCash $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(NetCash $entity) {
        $form = $this->createForm(new NetCashType(), $entity, array(
            'action' => $this->generateUrl('netcash_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing NetCash entity.
     *
     * @Route("/actualizar/{id}", name="netcash_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:NetCash:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:NetCash')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad NetCash.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $em->flush();

            return $this->redirect($this->generateUrl('pagos_reporte_pagos'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Net Cash'
        );
    }
    
    /**
     *
     * @Route("/datos-autorizaciones/", name="netcash_datos-autorizaciones")
     * @Method("POST")
     */
    public function datosAutorizacionesAction(Request $request) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $form = $this->get('form.factory')
                ->createNamedBuilder('generar_netcash', 'form', array(), array())
                ->getForm();
        $form->handleRequest($request);

        $idsRequest = $request->request->get('ids');

        if ($idsRequest == null) {
            $response = new Response();
            $response->setContent(json_encode(array(
                'result' => 'ERROR',
                'msg' => 'Debe seleccionar al menos una autorizacion contable',
            )));

            return $response;
        }
        
        $cuentasBancoAdif = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->findBy(array('estaActiva' => true,
        'idBanco' => 2)
                );

        // IDS DE ACs
        $ids = json_decode($idsRequest, '[]');

        $autorizaciones = $em->getRepository('ADIFContableBundle:OrdenPago')
                ->createQueryBuilder('o')
                ->where('o.id IN (:ids)')->setParameter('ids', $ids)
                ->getQuery()
                ->getResult();

//        \Doctrine\Common\Util\Debug::dump($autorizaciones);die;
        
        return $this->render('ADIFContableBundle:NetCash:datos_autorizaciones.html.twig', array(
            'autorizaciones' => $autorizaciones,
            'cuentasBancoAdif' => $cuentasBancoAdif,
            'form' => $form->createView(),
        ));
    }
    
    /**
     *
     * @Route("/generar/", name="netcash_generar")
     */
    public function generarAction(Request $request) {
        $idsRequest = $request->request->get('ids');
        $id_cuenta_bancaria = $request->request->get('cuenta_bancaria');
        $monto_total = $request->request->get('monto_total');
        $fecha_entrega = $request->request->get('fecha_entrega');
        $fecha_pago = $request->request->get('fecha_pago');
        
        $ids = json_decode($idsRequest, '[]');
        
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());
        $emContable = $this->getDoctrine()->getManager($this->getEntityManager());
        
        $estadoPagada = $emContable->getRepository('ADIFContableBundle:EstadoOrdenPago')->findOneByDenominacionEstado(ConstanteEstadoOrdenPago::ESTADO_PAGADA);
                
        $numero_op = null;
        $numero_comprobante_retencion = array();
        $error_asiento = false;
        $offsetNumeroAsiento = 0;
        
        /* Genero el NetCash */
        $netCashEntity = new NetCash();
        // Obtengo el EstadoNetCash igual a "Net Cash corrida pendiente"
        $estadoNetCash = $emContable->getRepository('ADIFContableBundle:EstadoNetCash')->findOneByDenominacion(\ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoNetCash::ESTADO_GENERADO);
        $netCashEntity->setEstadoNetCash($estadoNetCash);
        $cuentaBancoAdif = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')->find($id_cuenta_bancaria);
        $netCashEntity->setCuenta($cuentaBancoAdif);
        $netCashService = $this->get('adif.netcash_service');

        $netCashEntity->setNumero($netCashService->getSiguienteNumero());        
        $netCashEntity->setMonto($monto_total);
        $netCashEntity->setFechaEntrega(\DateTime::createFromFormat('d/m/Y', $fecha_entrega));
        $netCashEntity->setFechaPago(\DateTime::createFromFormat('d/m/Y', $fecha_pago));
        
        foreach($ids as $id_orden_pago){
            $ordenPagoPendientePago = $emContable->getRepository('ADIFContableBundle:OrdenPago')->find($id_orden_pago);

            /* @var $ordenPagoPendientePago \ADIF\ContableBundle\Entity\OrdenPago */

            if ($ordenPagoPendientePago->getPagoOrdenPago() != null) {
                $resultPago = array(
                    'result' => 'ERROR',
                    'msg' => 'La autorizaci&oacute;n contable N&ordm; '. $ordenPagoPendientePago->getNumeroAutorizacionContable() .' ya fue pagada con anterioridad'
                );
                return new JsonResponse($resultPago);
            }

            //estado pagada
            $ordenPagoPendientePago->setEstadoOrdenPago($estadoPagada);

            $ordenPagoPendientePago->setFechaOrdenPago(new \DateTime());
            $ordenPagoPendientePago->setFechaContable(new \DateTime());

            $pagoOrdenPago = new PagoOrdenPago();
            $pagoOrdenPago->setMonto($ordenPagoPendientePago->getTotalBruto());
            $pagoOrdenPago->addOrdenesPago($ordenPagoPendientePago);

            if(!$numero_op){
                try {
                    $numero_op = $this->get('adif.orden_pago_service')->getSiguienteNumeroOrdenPago();
                } catch (Exception $e) {
                    $resultPago = array(
                        'result' => 'ERROR',
                        'msg' => 'Hubo un error al asignar el número de orden de pago'
                    );

                    return new JsonResponse($resultPago);
                }
            } else {
                $numero_op++;
            }
            $ordenPagoPendientePago->setNumeroOrdenPago($numero_op);
            $ordenPagoPendientePago->setPagoOrdenPago($pagoOrdenPago);
            
            $netCashEntity->addPagosOrdenPago($pagoOrdenPago);
            $pagoOrdenPago->setNetCash($netCashEntity);

            foreach ($ordenPagoPendientePago->getRetenciones() as $comprobanteRetencionImpuestoCompras) {
                /* @var $comprobanteRetencionImpuestoCompras \ADIF\ContableBundle\Entity\ComprobanteRetencionImpuesto */
                
                if(!isset($numero_comprobante_retencion[$comprobanteRetencionImpuestoCompras->getRegimenRetencion()->getTipoImpuesto()->getId()])){
                    $numero_comprobante_retencion[$comprobanteRetencionImpuestoCompras->getRegimenRetencion()->getTipoImpuesto()->getId()] = $this->get('adif.comprobante_retencion_service')->getSiguienteNumeroComprobanteRetencionPorImpuesto($comprobanteRetencionImpuestoCompras->getRegimenRetencion()->getTipoImpuesto());
                } else {
                    $numero_comprobante_retencion[$comprobanteRetencionImpuestoCompras->getRegimenRetencion()->getTipoImpuesto()->getId()]++;
                }

                $comprobanteRetencionImpuestoCompras->setNumeroComprobanteRetencion($numero_comprobante_retencion[$comprobanteRetencionImpuestoCompras->getRegimenRetencion()->getTipoImpuesto()->getId()]);

                $comprobanteRetencionImpuestoCompras->setFechaComprobanteRetencion(new \DateTime());
                $comprobanteRetencionImpuestoCompras->getRenglonDeclaracionJurada()->setFecha(new \DateTime());
            }

            // Persisto la entidad
            $emContable->persist($pagoOrdenPago);
            
            $controller = $ordenPagoPendientePago->getController();
            $controller->setContainer($this->container);
            
            $controller->pagarActionCustom($ordenPagoPendientePago, $emContable);

            /* Genero el asiento contable y presupuestario */
            $resultArray = $controller->generarAsientoContablePagar($ordenPagoPendientePago, $this->getUser(), $offsetNumeroAsiento++);

            // Si el asiento presupuestario falló
            if ($resultArray['mensajeErrorPresupuestario'] != null) {
                return new JsonResponse(array('result' => 'ERROR', 'msg' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorPresupuestario']));
            }

            // Si el asiento contable falló
            if ($resultArray['mensajeErrorContable'] != null) {
                return new JsonResponse(array('result' => 'ERROR', 'msg' => 'Fall&oacute; el registro de los asientos <br/>' . $resultArray['mensajeErrorContable']));
            }
            
            $error_asiento = $error_asiento && ($resultArray['numeroAsiento'] == -1);
        }
                
        $emContable->persist($netCashEntity);

        // Si no hubo errores en los asientos
        if (!$error_asiento) {
            // Comienzo la transaccion
            $emContable->getConnection()->beginTransaction();

            try {
                $emContable->flush();

                $emContable->getConnection()->commit();
                /*
                $dataArray = [
                    'data-id-orden-pago' => $id
                ];
                
                $mensajeFlash = $this->get('adif.asiento_service')
                        ->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], $dataArray, true);

                $mensajeImprimir = 'Para imprimir la orden de pago haga click <a href="' . $this->generateUrl($ordenPagoPendientePago->getPath() . '_print', ['id' => $ordenPagoPendientePago->getId()]) . '" class="link-imprimir-op">aquí</a>';
                
                return new JsonResponse(array('result' => 'OK', 'msg' => $mensajeFlash, 'imprimir' => $mensajeImprimir));
                */
                $resultPago = array(
                    'result' => 'OK',
                    'id' => $netCashEntity->getId()
                );
            } catch (\Exception $e) {
                $emContable->getConnection()->rollback();
                $emContable->close();

                throw $e;
            }
        } else {
            return new JsonResponse(array('result' => 'ERROR', 'msg' => 'Fall&oacute; el registro de los asientos'));
        }

        return new JsonResponse($resultPago);
    }
    
    /**
     *
     * @Route("/anular/{id}", name="netcash_anular")
     */
    public function anularAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        
        $error_asiento = false;
        $offsetNumeroAsiento = 0;
        
        /* @var $netCash NetCash */
        $netCash = $em->getRepository('ADIFContableBundle:NetCash')->find($id);
        /* Seteo el pago del NetCash a "Anulado" */        
        $netCash->setEstadoNetCash(
                $em->getRepository('ADIFContableBundle:EstadoNetCash')
                        ->findOneByDenominacion(ConstanteEstadoNetCash::ESTADO_ANULADO)
        );
        
        if (!$netCash) {
            throw $this->createNotFoundException('No se puede encontrar la entidad NetCash');
        }
        
        //$ordenPago = $emContable->getRepository($this->getClassName())->find($id);
        foreach($netCash->getPagosOrdenPago() as $pagoOrdenPago){
            /* @var $ordenPago \ADIF\ContableBundle\Entity\OrdenPago */
            $ordenPago = $pagoOrdenPago->getOrdenPagoPagada();
            
            $fecha_hoy = new \DateTime();

            /* Seteo el estado de la OP a "Anulada" */
            $ordenPago->setEstadoOrdenPago($em->getRepository('ADIFContableBundle:EstadoOrdenPago')->findOneByDenominacionEstado(ConstanteEstadoOrdenPago::ESTADO_ANULADA));
            /* Seteo la fecha de anulacion de la op anulada */
            $ordenPago->setFechaAnulacion($fecha_hoy);

            /* @var $autorizacionContable \ADIF\ContableBundle\Entity\Obras\OrdenPagoObra */
            $autorizacionContable = clone $ordenPago;

            /*  Seteo el nº y fecha de OP de la Autorización Contable a NULL */
            $autorizacionContable->setNumeroOrdenPago(null);
            $autorizacionContable->setFechaOrdenPago(null);

            /* Seteo el asiento contable relacionado a NULL */
            $autorizacionContable->setAsientoContable(null);

            /* Datos auditoria */
            $autorizacionContable->setFechaUltimaActualizacion($fecha_hoy);
            $autorizacionContable->setUsuarioUltimaModificacion($this->getUser());

            /* Seteo el Pago de la Autorización Contable a NULL */
            $autorizacionContable->setPagoOrdenPago(null);

            /* Seteo el estado de la Autorización Contable a "Pendiente pago" */
            $autorizacionContable->setEstadoOrdenPago(
                $em->getRepository('ADIFContableBundle:EstadoOrdenPago')
                    ->findOneByDenominacionEstado(ConstanteEstadoOrdenPago::ESTADO_PENDIENTE_PAGO)
            );
            
            $controller = $ordenPago->getController();
            $controller->setContainer($this->container);

            /* Genero el asiento contable y presupuestario */
            \Doctrine\Common\Util\Debug::dump($controller->generarAsientoContableAnular($ordenPago, $this->getUser(), true, $offsetNumeroAsiento++)); die;

            $controller->anularActionCustom($ordenPago, $em, $autorizacionContable);

            // Persisto la entidad
            $em->persist($autorizacionContable);

            // Si el asiento presupuestario falló
            if (!empty($resultArray['mensajeErrorPresupuestario'])) {
                $this->get('session')->getFlashBag()->add('error', $resultArray['mensajeErrorPresupuestario']);
                return $this->redirect($this->generateUrl('pagos_reporte_pagos'));
            }

            // Si el asiento contable falló
            if (!empty($resultArray['mensajeErrorContable'])) {
                $this->get('session')->getFlashBag()->add('error', $resultArray['mensajeErrorContable']);
                return $this->redirect($this->generateUrl('pagos_reporte_pagos'));
            }
            
            $error_asiento = $error_asiento && ($resultArray['numeroAsiento'] == -1);
        }
        
        // Si no hubo errores en los asientos
        if (!$error_asiento) {            
            // Comienzo la transaccion
            $em->getConnection()->beginTransaction();

            try {
                $em->flush();

                $em->getConnection()->commit();
                /*                
                $dataArray = [
                    'data-id-orden-pago' => $id,
                    'data-fecha-asiento' => $ordenPago->getFechaAnulacion()->format('d/m/Y'),
                    'data-es-anulacion' => 1
                ];

                $this->get('adif.asiento_service')->showMensajeFlashAsientoContable($resultArray['numeroAsiento'], $dataArray);
                */
                $this->get('session')->getFlashBag()->add('success', 'La anulación del Net Cash se realizó con éxito.');
            } catch (\Exception $e) {
                $em->getConnection()->rollback();
                $em->close();

                throw $e;
            }
        }        

        return $this->redirect($this->generateUrl('pagos_reporte_pagos'));
    }
 
}
