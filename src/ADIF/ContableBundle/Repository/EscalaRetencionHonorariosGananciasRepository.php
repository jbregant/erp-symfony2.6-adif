<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

/**
 * 
 */
class EscalaRetencionHonorariosGananciasRepository extends EntityRepository {

    /**
     * 
     * @param type $monto
     * @return type
     */
    public function getEscalaRetencionHonorariosGananciasByMonto($monto) {

        $query = $this->createQueryBuilder('erhg')
                ->where(':monto >=  erhg.montoDesde')
                ->andWhere(':monto < erhg.montoHasta')
                ->setParameter('monto', $monto)
                ->getQuery();

        return $query->getOneOrNullResult();
    }

}
