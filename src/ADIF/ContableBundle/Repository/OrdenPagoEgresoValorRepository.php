<?php

namespace ADIF\ContableBundle\Repository;

use ADIF\BaseBundle\Repository\BaseRepository;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;

/**
 * Description of OrdenPagoEgresoValorRepository
 * 
 */
class OrdenPagoEgresoValorRepository extends BaseRepository {

    /**
     * 
     * @param \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor $egresoValor
     * @return type
     */
    public function getUltimaReposicionPaga(\ADIF\ContableBundle\Entity\EgresoValor\EgresoValor $egresoValor) {

        $qb = $this->createQueryBuilder('opev');

        $query = $qb
                ->innerJoin('opev.estadoOrdenPago', 'eop')
                ->where('eop.denominacionEstado = :denominacion')
                ->andWhere('opev.egresoValor = :egresoValor')
                ->orderBy('opev.fechaCreacion', 'desc')
                ->setMaxResults(1)
                ->setParameter('denominacion', ConstanteEstadoOrdenPago::ESTADO_PAGADA)
                ->setParameter('egresoValor', $egresoValor)
        ;
        return $query->getQuery()->getResult();
    }

}
