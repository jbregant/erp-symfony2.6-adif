<?php

namespace ADIF\InventarioBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;


/**
 *
 */
class CatalogoMaterialesNuevosRepository extends EntityRepository {


    public function findAllMaterialNuevo()
    {

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('num', 'num');
        $rsm->addScalarResult('grupoMaterial', 'grupoMaterial');
        $rsm->addScalarResult('descripcion', 'descripcion');
        $rsm->addScalarResult('medida', 'medida');
        $rsm->addScalarResult('peso', 'peso');
        $rsm->addScalarResult('volumen', 'volumen');
        $rsm->addScalarResult('unidadMedida', 'unidadMedida');
        $rsm->addScalarResult('valor', 'valor');
        $rsm->addScalarResult('tipoValor', 'tipoValor');
        $rsm->addScalarResult('fabricante', 'fabricante');
        $rsm->addScalarResult('observacion', 'observacion');
        $rsm->addScalarResult('estadoInventario', 'estadoInventario');

        $native_query = $this->getEntityManager()->createNativeQuery('SELECT * FROM vista_materiales_nuevos', $rsm);
        $result = $native_query->getResult();

        return $result;
    }
}
