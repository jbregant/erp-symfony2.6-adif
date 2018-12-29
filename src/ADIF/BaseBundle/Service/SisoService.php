<?php

namespace ADIF\BaseBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Servicio de comunicacion con la base de datos de Sistema SISO
 *
 * @author Gustavo Luis
 */
class SisoService 
{
    private $em;
    
    public function __construct(Container $container)
    {
        $this->em = $container->get('doctrine')->getManager('siso');
    }
    
    public function getIdContratoByContrato($contrato)
    {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id_contrato', 'id_contrato');
        
        $sql = '
            SELECT c.id AS id_contrato
            FROM soa_contratos c
            INNER JOIN soa_gerencias g ON c.gerencia_id = g.id
            WHERE 1 = 1
            AND CONCAT(g.acronimo, \'-\', c.contrato) = :contrato
            AND c.activo IS TRUE
            ORDER BY c.id
        ';
        
        $query = $this->em->createNativeQuery($sql, $rsm);
        
        $query->setParameter('contrato', $contrato);
        
        $resultado = $query->getOneOrNullResult();
        
        return ($resultado != null && isset($resultado['id_contrato'])) 
            ? $resultado['id_contrato']
            : null;
    }
}
