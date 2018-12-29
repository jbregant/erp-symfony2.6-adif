<?php

namespace ADIF\ComprasBundle\Repository;

use ADIF\BaseBundle\Repository\BaseRepository;

/**
 * 
 */
class RenglonPadronRepository extends BaseRepository {

    /**
     * 
     * @param type $id
     * @return type
     */
    public function getRenglonesPadron($id) {

        $qb = $this->createQueryBuilder('r');

        $query = $qb
                ->select('r.id, r.id_padron, r.numero_certificado, r.tipo_regimen, r.fecha_desde, r.fecha_hasta, r.porcentaje_exencion, r.actualiza')
                ->where('r.id_padron = :id')->setParameter('id', $id)
				->andWhere('r.fecha_baja = NULL')
				;

        return $query->getQuery()->getResult();
    }

}
