<?php

namespace ADIF\ContableBundle\Repository;

use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoEgresoValor;
use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class EgresoValorRepository extends EntityRepository {

    /**
     * 
     * @param type $tipoEgresoValor
     * @param type $nroDocumento
     * @param type $idTipoDocumento
     * @return type
     */
    public function findByTipoEgresoValorYPersona($tipoEgresoValor, $nroDocumento, $idTipoDocumento) {

        $query = $this->createQueryBuilder('ev')
                ->innerJoin('ev.responsableEgresoValor', 'r')
                ->innerJoin('ev.estadoEgresoValor', 'e')
                ->where('ev.tipoEgresoValor = :tipoEgresoValor')
                ->andWhere('r.nroDocumento = :nroDocumento')
                ->andWhere('r.idTipoDocumento = :idTipoDocumento')
                ->andWhere('e.codigo != :codigo')
                ->setParameter('tipoEgresoValor', $tipoEgresoValor)
                ->setParameter('nroDocumento', $nroDocumento)
                ->setParameter('idTipoDocumento', $idTipoDocumento)
                ->setParameter('codigo', ConstanteEstadoEgresoValor::ESTADO_CERRADO)
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param type $tipoEgresoValor
     * @param type $idGerencia
     * @return type
     */
    public function findByTipoEgresoValorYGerencia($tipoEgresoValor, $idGerencia) {

        $query = $this->createQueryBuilder('ev')
                ->innerJoin('ev.estadoEgresoValor', 'e')
                ->where('ev.tipoEgresoValor = :tipoEgresoValor')
                ->andWhere('ev.idGerencia = :idGerencia')
                ->andWhere('e.codigo != :codigo')
                ->setParameter('tipoEgresoValor', $tipoEgresoValor)
                ->setParameter('idGerencia', $idGerencia)
                ->setParameter('codigo', ConstanteEstadoEgresoValor::ESTADO_CERRADO)
        ;

        return $query->getQuery()->getResult();
    }

}
