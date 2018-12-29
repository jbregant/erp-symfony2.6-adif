<?php

namespace ADIF\InventarioBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;


/**
 *
 */
class CatalogoMaterialesProducidosDeObraRepository extends EntityRepository {


    public function findAllMaterialProducido()
    {

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('num', 'num');
        $rsm->addScalarResult('grupoMaterial', 'grupoMaterial');
        $rsm->addScalarResult('denominacion', 'denominacion');
        $rsm->addScalarResult('medida', 'medida');
        $rsm->addScalarResult('peso', 'peso');
        $rsm->addScalarResult('volumen', 'volumen');
        $rsm->addScalarResult('unidadMedida', 'unidadMedida');
        $rsm->addScalarResult('observacion', 'observacion');
        $rsm->addScalarResult('estadoInventario', 'estadoInventario');

        $native_query = $this->getEntityManager()->createNativeQuery('SELECT * FROM vista_materiales_producidos_obra', $rsm);
        $result = $native_query->getResult();

        return $result;
    }
}
