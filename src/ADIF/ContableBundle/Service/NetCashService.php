<?php

namespace ADIF\ContableBundle\Service;

use ADIF\BaseBundle\Entity\EntityManagers;
//use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoOrdenPago;
use Doctrine\ORM\NoResultException;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Description of NetCashService
 *
 * @author Darío Rapetti
 * created 11/05/2016
 */
class NetCashService {

    /**
     *
     * @var type 
     */
    protected $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * 
     * @return type
     */
    public function getSiguienteNumero() {
        $repository = $this->doctrine->getRepository('ADIFContableBundle:NetCash', EntityManagers::getEmContable());

        $query = $repository->createQueryBuilder('nc')
                ->select('nc.numero')
                ->orderBy('nc.numero', 'DESC')
                ->setMaxResults(1)
                ->getQuery();

        try {
            $siguienteNumero = $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            $siguienteNumero = 0;
        }

        return $siguienteNumero + 1;
    }
}
