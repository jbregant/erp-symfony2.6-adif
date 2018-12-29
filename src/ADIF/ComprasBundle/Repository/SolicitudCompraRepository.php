<?php

namespace ADIF\ComprasBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * 
 */
class SolicitudCompraRepository extends EntityRepository {

    /**
     * 
     * @param type $entidadesAutorizantesIds
     * @param type $estadoSolicitudCompra
     * @return type
     */
    public function getSolicitudCompraByEntidadAutorizanteId($entidadesAutorizantesIds) {

        $query = $this->createQueryBuilder('s')
                ->select('s')
                ->join('s.entidadAutorizante', 'ea')
                ->where('ea.id IN (:string)')
                ->setParameter('string', $entidadesAutorizantesIds, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->getQuery();

        return $query->getResult();
    }

    /**
     * 
     * @return type
     */
    public function getSolicitudCompraByEstadoEqual($denominacionEstadoSolicitudCompra) {
        $query = $this->createQueryBuilder('s')
                ->select('s')
                ->innerJoin('s.estadoSolicitudCompra', 'es')
                ->where('es.denominacionEstadoSolicitudCompra = (:denominacionEstadoSolicitudCompra)')
                ->setParameter('denominacionEstadoSolicitudCompra', $denominacionEstadoSolicitudCompra)
                ->getQuery();

        return $query->getResult();
    }
    
    /**
     * 
     * @return type
     */
    public function getSolicitudCompraByEstadoNotEqual($denominacionEstadoSolicitudCompra) {
        $query = $this->createQueryBuilder('s')
                ->select('s')
                ->innerJoin('s.estadoSolicitudCompra', 'es')
                ->where('es.denominacionEstadoSolicitudCompra != (:denominacionEstadoSolicitudCompra)')
                ->setParameter('denominacionEstadoSolicitudCompra', $denominacionEstadoSolicitudCompra)
                ->getQuery();

        return $query->getResult();
    }

    /**
     * 
     * @param type $renglonRequerimientoIds
     * @return type
     */
    public function getSolicitudCompraMayorFechaByRenglonId($renglonRequerimientoIds) {

        $query = $this->createQueryBuilder('s')
                ->select('partial s.{id, fechaSolicitud}')
                ->innerJoin('s.renglonesSolicitudCompra', 'rs')
                ->where('rs.id IN (:ids)')
                ->setParameter('ids', $renglonRequerimientoIds, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->orderBy('s.fechaSolicitud', 'DESC')
                ->setMaxResults(1)
                ->getQuery();

        return $query->getOneOrNullResult();
    }
    
    public function getSolicitudesCompra($idUsuario)
    {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('numero', 'numero');
        $rsm->addScalarResult('fecha_solicitud', 'fecha_solicitud');
        $rsm->addScalarResult('tipo_solicitud_compra', 'tipo_solicitud_compra');
        $rsm->addScalarResult('origen', 'origen');
        $rsm->addScalarResult('descripcion', 'descripcion');
        $rsm->addScalarResult('justiprecio', 'justiprecio');
        $rsm->addScalarResult('estado', 'estado');
        $rsm->addScalarResult('alias_tipo_importancia', 'alias_tipo_importancia');
        $rsm->addScalarResult('es_editable', 'es_editable');
        $rsm->addScalarResult('es_anulable', 'es_anulable');

        $query = $this->_em->createNativeQuery('CALL sp_get_solicitudes_compra(:idUsuario)', $rsm);
        
        $query->setParameter('idUsuario', $idUsuario);
        
        return $query->getResult();
    }
    
    public function getSolicitudesCompraAprobadas()
    {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('numero', 'numero');
        $rsm->addScalarResult('fecha_solicitud', 'fecha_solicitud');
        $rsm->addScalarResult('tipo_solicitud_compra', 'tipo_solicitud_compra');
        $rsm->addScalarResult('origen', 'origen');
        $rsm->addScalarResult('descripcion', 'descripcion');
        $rsm->addScalarResult('justiprecio', 'justiprecio');
        $rsm->addScalarResult('estado', 'estado');
        $rsm->addScalarResult('alias_tipo_importancia', 'alias_tipo_importancia');
        $rsm->addScalarResult('es_editable', 'es_editable');
        $rsm->addScalarResult('es_anulable', 'es_anulable');

        $sql = 'SELECT sc.*
                FROM vw_solicitudes_compra sc
                WHERE sc.id_estado_solicitud_compra = 6
                ORDER BY sc.id DESC
                ';
        
        $query = $this->_em->createNativeQuery($sql, $rsm);
        
        return $query->getResult();
    }
    
    public function getSolicitudesCompraNoBorrador()
    {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('numero', 'numero');
        $rsm->addScalarResult('fecha_solicitud', 'fecha_solicitud');
        $rsm->addScalarResult('tipo_solicitud_compra', 'tipo_solicitud_compra');
        $rsm->addScalarResult('origen', 'origen');
        $rsm->addScalarResult('descripcion', 'descripcion');
        $rsm->addScalarResult('justiprecio', 'justiprecio');
        $rsm->addScalarResult('estado', 'estado');
        $rsm->addScalarResult('alias_tipo_importancia', 'alias_tipo_importancia');
        $rsm->addScalarResult('es_editable', 'es_editable');
        $rsm->addScalarResult('es_anulable', 'es_anulable');

        $sql = 'SELECT sc.*
                FROM vw_solicitudes_compra sc
                WHERE sc.id_estado_solicitud_compra <> 2
                ORDER BY sc.id DESC
                ';
        
        $query = $this->_em->createNativeQuery($sql, $rsm);
        
        return $query->getResult();
    }
    
    public function getSolicitudesCompraByEntidadAutorizante($idUsuario, $pendienteAutorizacion = true)
    {
         $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('numero', 'numero');
        $rsm->addScalarResult('fecha_solicitud', 'fecha_solicitud');
        $rsm->addScalarResult('tipo_solicitud_compra', 'tipo_solicitud_compra');
        $rsm->addScalarResult('origen', 'origen');
        $rsm->addScalarResult('descripcion', 'descripcion');
        $rsm->addScalarResult('justiprecio', 'justiprecio');
        $rsm->addScalarResult('estado', 'estado');
        $rsm->addScalarResult('alias_tipo_importancia', 'alias_tipo_importancia');
        $rsm->addScalarResult('es_editable', 'es_editable');
        $rsm->addScalarResult('es_anulable', 'es_anulable');

        $sql = '
                SELECT sc.*
                FROM vw_solicitudes_compra sc
                INNER JOIN `vw_entidad_autorizante_usuario_grupo` ea ON sc.id = ea.id_solicitud_compra
                WHERE ea.id_usuario = :idUsuario
                AND `sc`.`id_estado_solicitud_compra` <> 2'; /* Sin las borrador */
        
        if ($pendienteAutorizacion) {
            $sql .= ' AND `sc`.`id_estado_solicitud_compra` = 5 ';
        }
                
        $sql .= ' ORDER BY sc.id DESC ';
                
        $query = $this->_em->createNativeQuery($sql, $rsm);
        
        $query->setParameter('idUsuario', $idUsuario);
        
        return $query->getResult();
    }

}
