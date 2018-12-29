<?php

namespace ADIF\ContableBundle\Repository;

use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonDeclaracionJurada;
use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class RenglonDeclaracionJuradaRepository extends EntityRepository {

    /**
     * 
     * @param type $tiposImpuesto
     * @param type $mes
     * @return type
     */
    public function findByTiposImpuestoAndPeriodo($tiposImpuesto, $mes) {

        $query = $this->createQueryBuilder('rdj');
        
        $query->innerJoin('rdj.tipoImpuesto', 'ti')
                ->innerJoin('rdj.estadoRenglonDeclaracionJurada', 'edj')
                ->where('ti.denominacion IN (:tipoImpuesto)')
                //se cambio para que muestre el mes anterior
                ->andWhere('MONTH(rdj.fecha) >= :mes')
                ->andWhere('edj.denominacion NOT IN (:estados)')
                ->setParameters(array(
                    'tipoImpuesto' => $tiposImpuesto,
                    'mes' => $mes,
                    'estados' => array(ConstanteEstadoRenglonDeclaracionJurada::CON_DDJJ, ConstanteEstadoRenglonDeclaracionJurada::CON_DEVOLUCION))
                    );

        return $query->getQuery()->getResult();
    }
    
    public function findByTiposImpuestoAndFechaDesdeAndFechaHasta($tiposImpuesto, $fechaDesde, $fechaHasta) {

        $query = $this->createQueryBuilder('rdj');
        
        $query->innerJoin('rdj.tipoImpuesto', 'ti')
                ->innerJoin('rdj.estadoRenglonDeclaracionJurada', 'edj')
                ->where('ti.denominacion IN (:tipoImpuesto)')
                ->andWhere('rdj.fecha BETWEEN :fechaDesde AND :fechaHasta')
                ->andWhere('edj.denominacion NOT IN (:estados)')
                ->setParameters(array(
                    'tipoImpuesto' => $tiposImpuesto,
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta,
                    'estados' => array(ConstanteEstadoRenglonDeclaracionJurada::CON_DDJJ, ConstanteEstadoRenglonDeclaracionJurada::CON_DEVOLUCION))
                    );

        return $query->getQuery()->getResult();
    }

}
