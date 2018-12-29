<?php

namespace ADIF\ContableBundle\Repository;

use Doctrine\ORM\EntityRepository;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoContrato;

/**
 * 
 */
class ContratoVentaRepository extends EntityRepository {

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
     * @return type
     */
    public function getContratosPendientesByCliente($codigos) {

        $query = $this->createQueryBuilder('c')
                ->innerJoin('c.estadoContrato', 'ec')
                ->where('ec.codigo NOT IN (:codigos)')
                ->setParameter('codigos', $codigos, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->orderBy('c.id', 'DESC');

        return $query->getQuery()->getResult();
    }
    
    public function getContratosConComprobantesByIdCliente($idCliente) 
    {
        $contratos = $this->getContratosByIdCliente($idCliente);
        $contratosConComprobantes = array();
        foreach ($contratos as $contrato) {
            $comprobantesContrato = $contrato->getComprobantesEnCuentaCorriente();
            if (!empty($comprobantesContrato)) {
                $contratosConComprobantes [] = $contrato;
            }
        }
        return $contratosConComprobantes;
    }
    
    public function getContratosByIdCliente($idCliente) 
    {
        $query = $this->createQueryBuilder('con')
                ->innerJoin('con.estadoContrato', 'estado')
                ->where('con.idCliente = ' . $idCliente)
                ->andWhere('estado.codigo <> ' . ConstanteEstadoContrato::ADENDADO)
                ->andWhere('estado.codigo <> ' . ConstanteEstadoContrato::PRORROGADO)
                ->orderBy('con.fechaInicio', 'ASC')
                //->orderBy('c.numeroContrato', 'ASC')                
                ->getQuery();

        return $query->getResult();
    }

}
