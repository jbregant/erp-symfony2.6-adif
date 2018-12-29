<?php

namespace ADIF\RecursosHumanosBundle\Service;

use ADIF\BaseBundle\Entity\EntityManagers;
use Doctrine\ORM\NoResultException;

/**
 * Description of ConsultorService
 *
 * @author Manuel Becerra
 * created 06/04/2015
 */
class ConsultorService {

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
    public function getSiguienteLegajoConsultor() {

        $repository = $this->doctrine
                ->getRepository('ADIFRecursosHumanosBundle:Consultoria\Consultor', EntityManagers::getEmRrhh());

        $query = $repository->createQueryBuilder('c')
                ->select('c.legajo')
                ->orderBy('c.legajo', 'DESC')
                ->setMaxResults(1)
                ->getQuery();

        try {
            $siguienteLegajo = $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            $siguienteLegajo = 0;
        }

        return $siguienteLegajo + 1;
    }

}
