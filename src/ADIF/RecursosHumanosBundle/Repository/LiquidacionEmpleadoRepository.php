<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * 
 */
class LiquidacionEmpleadoRepository extends EntityRepository {

    /**
     * 
     * @param type $empleado
     * @param type $anio
     * @param type $mes
     * @return type
     */
    public function getLiquidacionEmpleadoByFecha($empleado, $anio, $mes) {

        $query = $this->createQueryBuilder('le')
                ->select('le')
                ->leftJoin('le.liquidacion', 'li')
                ->where('le.empleado = :empleado')
                ->andWhere('YEAR(li.fechaCierreNovedades) = :anio')
                ->andWhere('MONTH(li.fechaCierreNovedades) = :mes')
                ->orderBy('le.id', 'DESC')
                ->setParameter('empleado', $empleado)
                ->setParameter('anio', $anio)
                ->setParameter('mes', $mes)
                ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * 
     * @param type $idsLiquidacionesEmpleado
     * @return type
     */
    public function getLiquidacionesEmpleadoByIdsLiquidacionesEmpleado($idsLiquidacionesEmpleado) {
        $query = $this->createQueryBuilder('le')
                ->where('le.id IN(:ids)')
                ->setParameter('ids', $idsLiquidacionesEmpleado)
                ->getQuery();
        return $query->getResult();
    }
	
	public function getSumaProrrateoSac($empleado, $anio, $semestre) 
	{
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('suma_prorrateo_sac', 'suma_prorrateo_sac');

		$sql = "
			SELECT SUM(le.prorrateo_sac) AS suma_prorrateo_sac
			FROM liquidacion_empleado le 
			INNER JOIN liquidacion l ON le.id_liquidacion = l.id
			INNER JOIN empleado e ON le.id_empleado = e.id
			WHERE e.id = " . $empleado->getId() . " 
			AND YEAR(l.fecha_cierre_novedades) = $anio
			";
		
		if ($semestre == 1) {
			$sql .= " AND MONTH(l.fecha_cierre_novedades) BETWEEN 1 AND 6 ";
		}
		
		if ($semestre == 2) {
			$sql .= " AND MONTH(l.fecha_cierre_novedades) BETWEEN 7 AND 12 ";
		}
		
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		
		$result = $query->getOneOrNullResult();
		return (!is_null($result) && isset($result['suma_prorrateo_sac'])) ? $result['suma_prorrateo_sac'] : 0; 
	}
    
    public function getLiquidacionEmpleadoByEmpleadoAndAnio($empleado, $anio)
    {
        return $this
                ->createQueryBuilder('le')
                ->select('le, l')
                ->innerJoin('le.liquidacion', 'l')
                ->where('YEAR(l.fechaCierreNovedades) = :anio')
                ->andWhere('le.empleado = :empleado')
                ->setParameter('empleado', $empleado)
                ->setParameter('anio', $anio)
                ->getQuery()
                ->getResult();
    }
}
