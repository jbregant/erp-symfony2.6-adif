<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * 
 */
class GananciaEmpleadoRepository extends EntityRepository {

    /**
     * 
     * @param type $empleado
     * @param type $anio
     * @return type
     */
    public function getGananciaEmpleadoByEmpleado($empleado, $anio) {

        $query = $this
                ->createQueryBuilder('ge')
                ->join('ge.liquidacionEmpleado', 'le')
                ->join('le.liquidacion', 'li')
                ->where('le.empleado = :empleado')
                ->andWhere('YEAR(li.fechaCierreNovedades) = :anio')
                ->setParameter('empleado', $empleado)
                ->setParameter('anio', $anio)
                ->orderBy('li.fechaCierreNovedades', 'asc')
                ->getQuery();

        return $query->getResult();
    }
    
    public function getUltimoHaberNetoAcumulado($idEmpleado)
    {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('haber_neto_acumulado', 'haber_neto_acumulado');
        
        $sql = '
            SELECT ge.`haber_neto_acumulado`
            FROM `empleado` e
            INNER JOIN `liquidacion_empleado` le ON e.id = le.`id_empleado`
            INNER JOIN `liquidacion` l ON le.`id_liquidacion` = l.id
            INNER JOIN `g_ganancia_empleado` ge ON le.`id_ganancia_empleado` = ge.id
            WHERE e.id = :idEmpleado
            ORDER BY l.id DESC
            LIMIT 1
        ';
        
        $query = $this->_em->createNativeQuery($sql, $rsm);
        
        $query->setParameter('idEmpleado', $idEmpleado);
        
        return $query->getOneOrNullResult();
    }
    
    /**
     * 
     * @param type $empleado
     * @param type $anio
     * @return type
     */
    public function getGananciaEmpleadoByEmpleadoYAnioYMes($empleado, $anio, $mes) {

        $query = $this
                ->createQueryBuilder('ge')
                ->join('ge.liquidacionEmpleado', 'le')
                ->join('le.liquidacion', 'li')
                ->where('le.empleado = :empleado')
                ->andWhere('YEAR(li.fechaCierreNovedades) = :anio')
                ->andWhere('MONTH(li.fechaCierreNovedades) = :mes')
                ->setParameter('empleado', $empleado)
                ->setParameter('anio', $anio)
                ->setParameter('mes', $mes)
                ->orderBy('li.fechaCierreNovedades', 'asc')
                ->getQuery();

        return $query->getOneOrNullResult();
    }
    
    /**
     * 
     * @param type $empleado
     * @param type $anio
     * @return type
     */
    public function getGananciaEmpleadoByEmpleadoYAnioYMesYTipoLiquidacion($empleado, $anio, $mes, $tipoLiquidacion) {

        $query = $this
                ->createQueryBuilder('ge')
                ->join('ge.liquidacionEmpleado', 'le')
                ->join('le.liquidacion', 'li')
                ->where('le.empleado = :empleado')
                ->andWhere('YEAR(li.fechaCierreNovedades) = :anio')
                ->andWhere('MONTH(li.fechaCierreNovedades) = :mes')
                ->andWhere('li.tipoLiquidacion = :tipoLiquidacion')
                ->setParameter('empleado', $empleado)
                ->setParameter('anio', $anio)
                ->setParameter('mes', $mes)
                ->setParameter('tipoLiquidacion', $tipoLiquidacion)
                ->orderBy('li.fechaCierreNovedades', 'asc')
                ->getQuery();

        return $query->getOneOrNullResult();
    }

}
