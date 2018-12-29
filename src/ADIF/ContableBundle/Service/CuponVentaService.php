<?php

namespace ADIF\ContableBundle\Service;

use ADIF\BaseBundle\Entity\EntityManagers;
use Doctrine\ORM\NoResultException;

/**
 * Description of CuponVentaService
 */
class CuponVentaService {

    protected $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * 
     * @return type
     */
    public function getSiguienteNumeroCupon() {

        $repository = $this->doctrine
                ->getRepository('ADIFContableBundle:Facturacion\CuponVenta', EntityManagers::getEmContable());

        $query = $repository->createQueryBuilder('c')
                ->select('c.numeroCupon')
                ->orderBy('c.numeroCupon', 'DESC')
                ->setMaxResults(1)
                ->getQuery();

        try {
            $siguienteNumero = $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            $siguienteNumero = 0;
        }

        return str_pad($siguienteNumero + 1, 8, '0', STR_PAD_LEFT);
    }

}
