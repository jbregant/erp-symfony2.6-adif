<?php

namespace ADIF\ComprasBundle\Service;

use ADIF\BaseBundle\Entity\EntityManagers;
use Doctrine\ORM\NoResultException;

/**
 * Description of ClienteProveedorService
 *
 * @author Manuel Becerra
 * created 16/12/2014
 */
class ClienteProveedorService {

    /**
     *
     * @var type 
     */
    protected $doctrine;

    /**
     * 
     * @param type $doctrine
     */
    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * 
     * @return type
     */
    public function getSiguienteCodigoClienteProveedor() {

        $repository = $this->doctrine
                ->getRepository('ADIFComprasBundle:ClienteProveedor', EntityManagers::getEmCompras());

        $query = $repository->createQueryBuilder('cp')
                ->select('cp.codigo')
                ->orderBy('cp.codigo', 'DESC')
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
