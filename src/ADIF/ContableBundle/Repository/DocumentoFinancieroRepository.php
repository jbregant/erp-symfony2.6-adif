<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Description of DocumentoFinancieroRepository
 *
 * @author Gustavo Luis
 * created 12/10/2017
 */
class DocumentoFinancieroRepository extends EntityRepository
{
    public function getAll()
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('fechaCreacion', 'fechaCreacion');
        $rsm->addScalarResult('tipoContratacion', 'tipoContratacion');
        $rsm->addScalarResult('numeroLicitacion', 'numeroLicitacion');
        $rsm->addScalarResult('anio', 'anio');
        $rsm->addScalarResult('descripcion', 'descripcion');
        $rsm->addScalarResult('proveedor', 'proveedor');
        $rsm->addScalarResult('tipoDocumentoFinanciero', 'tipoDocumentoFinanciero');
        $rsm->addScalarResult('fechaDocumentoFinancieroInicio', 'fechaDocumentoFinancieroInicio');
        $rsm->addScalarResult('fechaDocumentoFinancieroFin', 'fechaDocumentoFinancieroFin');
        $rsm->addScalarResult('fechaIngresoADIF', 'fechaIngresoADIF');
        $rsm->addScalarResult('montoSinIVA', 'montoSinIVA');
        $rsm->addScalarResult('montoIVA', 'montoIVA');
        $rsm->addScalarResult('montoFondoReparo', 'montoFondoReparo');
        $rsm->addScalarResult('montoTotalDocumentoFinanciero', 'montoTotalDocumentoFinanciero');
        $rsm->addScalarResult('fechaAnulacion', 'fechaAnulacion');
        $rsm->addScalarResult('correspondePago', 'correspondePago');
        $rsm->addScalarResult('esEditable', 'esEditable');
        $rsm->addScalarResult('esAnulable', 'esAnulable');

        $sql = 'SELECT * FROM vistadocumentosfinancieros';
        
        $native_query = $this->_em->createNativeQuery($sql, $rsm);

        return $native_query->getResult();
    }
    
    public function getReporteGeneral()
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('id_licitacion', 'id_licitacion');
        $rsm->addScalarResult('id_tramo', 'id_tramo');
        $rsm->addScalarResult('fechaCreacion', 'fechaCreacion');
        $rsm->addScalarResult('tipoContratacionAlias', 'tipoContratacionAlias');
        $rsm->addScalarResult('numero', 'numero');
        $rsm->addScalarResult('anio', 'anio');
        $rsm->addScalarResult('descripcion', 'descripcion');
        $rsm->addScalarResult('cuit', 'cuit');
        $rsm->addScalarResult('razonSocial', 'razonSocial');
        $rsm->addScalarResult('tipoDocumentoFinanciero', 'tipoDocumentoFinanciero');
        $rsm->addScalarResult('numeroDocumentoFinanciero', 'numeroDocumentoFinanciero');
        $rsm->addScalarResult('fechaAnulacion', 'fechaAnulacion');
        $rsm->addScalarResult('montoSinIVA', 'montoSinIVA');
        $rsm->addScalarResult('correspondePago', 'correspondePago');
        $rsm->addScalarResult('comprobante', 'comprobante');
        $rsm->addScalarResult('totalComprobante', 'totalComprobante');
        $rsm->addScalarResult('fechaIngresoADIF', 'fechaIngresoADIF');
        $rsm->addScalarResult('fechaIngresoGerenciaAdministracion', 'fechaIngresoGerenciaAdministracion');
        $rsm->addScalarResult('numeroReferencia', 'numeroReferencia');
        $rsm->addScalarResult('ordenPago', 'ordenPago');
        $rsm->addScalarResult('pago', 'pago');
        $rsm->addScalarResult('fechaPago', 'fechaPago');
        $rsm->addScalarResult('estadoPago', 'estadoPago');
        $rsm->addScalarResult('montoNeto', 'montoNeto');
        $rsm->addScalarResult('observaciones', 'observaciones');
        $rsm->addScalarResult('fechaAprobacionTecnica', 'fechaAprobacionTecnica');
        $rsm->addScalarResult('fechaVencimiento', 'fechaVencimiento');
		
		$rsm->addScalarResult('porcentajeCertificacion', 'porcentajeCertificacion');
		$rsm->addScalarResult('fechaInicio', 'fechaInicio');
		$rsm->addScalarResult('fechaFin', 'fechaFin');

        $sql = '
            SELECT *
            FROM vistareportedocumentosfinancieros
            ORDER BY id_licitacion, id_tramo ASC
        ';
        
        $native_query = $this->_em->createNativeQuery($sql, $rsm);

        $documentosFinancieros = $native_query->getResult();
        
        /**
         * Work around para que aparezca el nro de renglon del tramo 
         * Hago corte control por licitacion y cuando sea el tramo diferente que incremente en 1 al renglon
         */
        $idLicitacion = null;
        $idLicitacionAnterior = null;
        $idTramo = null;
        $idTramoAnterior = null;
        $renglon = 0;
        for($i = 0; $i < count($documentosFinancieros); $i++) {
            
            $doc = $documentosFinancieros[$i];
            
            $idLicitacion = $doc['id_licitacion'];
            $idTramo = $doc['id_tramo'];
            
            if ($idLicitacion == $idLicitacionAnterior) {
                if ($idTramo != $idTramoAnterior) {
                    $renglon++;
                }
            } else {
                $renglon = 1;
            }
            
            $documentosFinancieros[$i]['renglon_nro'] = $renglon;
            $idLicitacionAnterior = $doc['id_licitacion'];
            $idTramoAnterior = $doc['id_tramo'];
        }
        
        return $documentosFinancieros;
    }
    
}
