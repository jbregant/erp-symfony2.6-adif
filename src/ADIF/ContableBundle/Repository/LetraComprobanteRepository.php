<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class LetraComprobanteRepository extends EntityRepository {

    /**
     * 
     * @param type $letrasComprobante
     * @return type
     */
    public function getLetrasComprobanteByDenominacion($letrasComprobante) {

        $query = $this->createQueryBuilder('l')
                ->select('l.id', 'l.letra')
                ->where('l.letra IN (:letras)')
                ->setParameter('letras', $letrasComprobante, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->getQuery();

        return $query->getResult();
    }

}
