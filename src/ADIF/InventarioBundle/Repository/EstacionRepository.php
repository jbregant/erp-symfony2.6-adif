<?php

namespace ADIF\InventarioBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 */
class EstacionRepository extends EntityRepository {

    public function findAllEstaciones()
    {

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('denominacion', 'denominacion');
        $rsm->addScalarResult('numero', 'numero');
        $rsm->addScalarResult('linea', 'linea');
        $rsm->addScalarResult('ramal', 'ramal');

        $native_query = $this->getEntityManager()->createNativeQuery('SELECT * FROM vista_estaciones', $rsm);
        $result = $native_query->getResult();

        return $result;
    }


}
