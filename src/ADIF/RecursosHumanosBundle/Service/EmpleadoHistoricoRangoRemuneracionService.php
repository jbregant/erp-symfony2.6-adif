<?php

namespace ADIF\RecursosHumanosBundle\Service;

use ADIF\RecursosHumanosBundle\Entity\Empleado;
use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\RecursosHumanosBundle\Entity\EmpleadoHistoricoRangoRemuneracion;

/**
 * Description of EmpleadoHistoricoRangoRemuneracionService
 */
class EmpleadoHistoricoRangoRemuneracionService {

    protected $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * 
     * @param Empleado $empleado
     * @param type $anio
     * @return type
     */
    public function getRangoRemuneracionByEmpleadoAndAnio(Empleado $empleado, $anio) {

        $repository = $this->doctrine
                ->getRepository('ADIFRecursosHumanosBundle:EmpleadoHistoricoRangoRemuneracion', EntityManagers::getEmRrhh());

        $qb = $repository->createQueryBuilder('ehrr');

        $query = $qb
                ->where('YEAR(ehrr.fechaHasta) IS NULL')
                //->andWhere($qb->expr()->between(':anio', 'YEAR(ehrr.fechaDesde)', 'YEAR(ehrr.fechaHasta)'))
                //->andWhere('YEAR(ehrr.fechaDesde) <= :anio')
                ->andWhere('ehrr.empleado = :empleado')
                //->setParameter('anio', $anio)
                ->setParameter('empleado', $empleado)
                ->setMaxResults(1)
                ->getQuery();
                //->getQuery()->getSQL();
                
        //die($query);
        $result = $query->getOneOrNullResult();

        if ($result != null) {
            return $result->getRangoRemuneracion();
        } else {
            return null;
        }
    }

}
