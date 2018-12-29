<?php

namespace ADIF\ComprasBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @author Gustavo Luis
 * created 11/10/2017
 */
class ClienteProveedorRepository extends EntityRepository
{
    public function getClienteDatosImpositivosByIdCliente($idCliente)
    {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id_cliente', 'id_cliente');
        
        $rsm->addScalarResult('condicion_iva', 'condicion_iva');
        $rsm->addScalarResult('exento_iva', 'exento_iva');
        
        $rsm->addScalarResult('condicion_ganancias', 'condicion_ganancias');
        $rsm->addScalarResult('exento_ganancias', 'exento_ganancias');
        
        $rsm->addScalarResult('condicion_suss', 'condicion_suss');
        $rsm->addScalarResult('exento_suss', 'exento_suss');
        
        $rsm->addScalarResult('condicion_iibb', 'condicion_iibb');
        $rsm->addScalarResult('exento_iibb', 'exento_iibb');
        
        $sql = ' 
            SELECT *
            FROM vw_cliente_datos_impositivos di
            WHERE di.`id_cliente` = :idCliente
        ';
                
        $query = $this->_em->createNativeQuery($sql, $rsm);
        
        $query->setParameter('idCliente', $idCliente);
        
        return $query->getOneOrNullResult();
    }
}
