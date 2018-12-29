<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Description of SubcategoriaRepository
 *
 * @author Gustavo Luis
 */
class SubcategoriaRepository extends EntityRepository
{
    public function findAsArray($id)
    {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('id_categoria', 'id_categoria');
        $rsm->addScalarResult('nombre', 'nombre');
        $rsm->addScalarResult('monto_basico', 'monto_basico');
        $rsm->addScalarResult('categoria_recibo', 'categoria_recibo');
        $rsm->addScalarResult('es_categoria_02', 'es_categoria_02');
        $rsm->addScalarResult('es_tiempo_completo', 'es_tiempo_completo');
        $rsm->addScalarResult('sirhu_grado', 'sirhu_grado');
        $rsm->addScalarResult('sirhu_escalafon', 'sirhu_escalafon');
        $rsm->addScalarResult('fecha_creacion', 'fecha_creacion');
        $rsm->addScalarResult('fecha_ultima_actualizacion', 'fecha_ultima_actualizacion');
        $rsm->addScalarResult('fecha_baja', 'fecha_baja');
        $rsm->addScalarResult('id_usuario_creacion', 'id_usuario_creacion');
        $rsm->addScalarResult('id_usuario_ultima_modificacion', 'id_usuario_ultima_modificacion');
        
        $query = $this->_em->createNativeQuery('SELECT * FROM subcategoria WHERE id = :id', $rsm);
        
        $query->setParameter('id', $id);
        
        return $query->getOneOrNullResult();
    }
}
