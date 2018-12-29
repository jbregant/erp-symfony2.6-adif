<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class ContratoConsultoriaRepository extends EntityRepository {

    /**
     * 
     * @param type $codigos
     * @return type
     */
    public function getContratosByNotEstados($codigos) {

        $query = $this->createQueryBuilder('c')
                ->innerJoin('c.estadoContrato', 'ec')
                ->where('ec.codigo NOT IN (:codigos)')
                ->setParameter('codigos', $codigos, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->orderBy('c.id', 'DESC');

        return $query->getQuery()->getResult();
    }

    /**
     * 
     * @param type $codigos
     * @param type $idConsultor
     * @return type
     */
    public function getContratosByNotEstadosAndIdConsultor($codigos, $idConsultor) {

        $query = $this->createQueryBuilder('c')
                ->innerJoin('c.estadoContrato', 'ec')
                ->where('c.idConsultor = :id')
                ->andWhere('ec.codigo NOT IN (:codigos)')
                ->setParameter('id', $idConsultor)
                ->setParameter('codigos', $codigos, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->orderBy('c.numeroContrato', 'ASC');

        return $query->getQuery()->getResult();
    }

}
