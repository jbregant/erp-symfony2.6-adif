<?php

namespace ADIF\ComprasBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * 
 */
class RenglonPedidoInternoRepository extends EntityRepository {

    /**
     * 
     * @param type $idArea
     * @return type
     */
    public function getRenglonPedidoInternoByArea($idArea) 
    {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('fecha_pedido', 'fecha_pedido');
        $rsm->addScalarResult('origen', 'origen');
        $rsm->addScalarResult('usuario', 'usuario');
        $rsm->addScalarResult('rubro', 'rubro');
        $rsm->addScalarResult('bien_economico', 'bien_economico');
        $rsm->addScalarResult('descripcion', 'descripcion');
        $rsm->addScalarResult('cantidad_solicitada', 'cantidad_solicitada');
        $rsm->addScalarResult('cantidad_pendiente', 'cantidad_pendiente');
        $rsm->addScalarResult('unidad_medida', 'unidad_medida');
        $rsm->addScalarResult('prioridad', 'prioridad');

        $query = $this->_em->createNativeQuery('CALL sp_get_renglones_pedido_interno(:idArea)', $rsm);
        
        $query->setParameter('idArea', $idArea);
        
        return $query->getResult();
    }

}
