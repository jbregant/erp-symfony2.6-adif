<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use ADIF\RecursosHumanosBundle\Entity\Concepto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\ResultSetMapping;
use ADIF\RecursosHumanosBundle\Entity\TipoConcepto;

class LiquidacionRepository extends EntityRepository {

	/**
	* Calcula la mejor remuneracion para el primero o segundo semestre por empleado
	* Este metodo se usa para la liquidacion de la nomina
	*/
	public function mejorBrutoByEmpleadoAndPeriodo($idEmpleado, $mesInicio, $mesFin, $anio) 
	{   
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('mejor_remuneracion', 'mejor_remuneracion');

        $native_query = $this->_em->createNativeQuery('
            SELECT 
                le.id_empleado, 
                MAX(bruto_1 + bruto_2 + IFNULL(le.adicional_remunerativo_retroactivo, 0) + IFNULL(en_suma.ajustes, 0) - IFNULL(en_resta.ajustes, 0) - IFNULL(integra_sac.monto, 0) ) AS mejor_remuneracion
			FROM liquidacion_empleado le
			INNER JOIN empleado e ON le.id_empleado = e.id
			INNER JOIN liquidacion l ON le.id_liquidacion = l.id
			LEFT JOIN (
				SELECT SUM(en.valor) AS ajustes, en.id_liquidacion_ajuste, en.id_empleado 
				FROM empleado_novedad en
                INNER JOIN liquidacion_empleado_concepto lec ON en.id = lec.id_empleado_novedad
                INNER JOIN concepto c ON en.id_concepto = c.id
                WHERE 1 = 1
                AND c.id_tipo_concepto <> 2 -- Que no sea no Remunerativo
                AND c.integra_sac = TRUE
				GROUP BY en.id_liquidacion_ajuste, en.id_empleado
			) en_suma ON en_suma.id_liquidacion_ajuste = l.id AND le.id_empleado = en_suma.id_empleado

			LEFT JOIN (
				SELECT 
				IF (c.es_ajuste IS TRUE, ABS(SUM(lec.monto)), ABS(SUM(en.valor)) ) AS ajustes,
				le.id_liquidacion, 
				en.id_empleado
				FROM empleado_novedad en
				INNER JOIN liquidacion_empleado_concepto lec ON en.id = lec.id_empleado_novedad
				INNER JOIN liquidacion_empleado le ON lec.id_liquidacion_empleado = le.id
                INNER JOIN concepto c ON en.id_concepto = c.id
				WHERE id_liquidacion_ajuste IS NOT NULL
                AND c.integra_sac = TRUE
                AND c.id_tipo_concepto <> 2 -- Que no sea no Remunerativo
				GROUP BY le.id_liquidacion, en.id_empleado
			) en_resta ON en_resta.id_liquidacion = l.id AND le.id_empleado = en_resta.id_empleado

			-- integra sac: no, no tiene que sumar para mejor remunerativo
			LEFT JOIN (
				SELECT 
					SUM(lec.monto) AS monto,
					en.id_empleado,
					le.id_liquidacion
				FROM empleado_novedad en
				INNER JOIN liquidacion_empleado_concepto lec ON en.id = lec.id_empleado_novedad
				INNER JOIN liquidacion_empleado le ON lec.id_liquidacion_empleado = le.id
				INNER JOIN concepto_version cv ON lec.id_concepto_version = cv.id AND lec.id_empleado_novedad = en.id
                INNER JOIN concepto c ON cv.id_concepto = c.id
				WHERE c.integra_sac = FALSE
				AND c.id_tipo_concepto = 1 -- Remunerativo
				GROUP BY le.id_liquidacion, en.id_empleado
			) integra_sac ON le.id_empleado = integra_sac.id_empleado AND l.id = integra_sac.id_liquidacion
			
			WHERE le.id_empleado = ?
			AND MONTH(l.fecha_cierre_novedades) BETWEEN ? AND ?
			AND YEAR(l.fecha_cierre_novedades) = ?
			AND e.fecha_inicio_antiguedad < l.fecha_cierre_novedades
			GROUP BY le.id_empleado
        ', $rsm);

        $native_query->setParameter(1, $idEmpleado);
        $native_query->setParameter(2, $mesInicio);
        $native_query->setParameter(3, $mesFin);
        $native_query->setParameter(4, $anio);

        //$result = $native_query->getResult();
		$resultado = $native_query->getResult();
		
        return isset($resultado[0]) ? $resultado[0]['mejor_remuneracion'] : null;

        
        // return (count($result) == 0) ? 0 : $result[0]['mejor_remuneracion'];
    }
    
	/**
	* Calcula la mejor remuneracion para el primero o segundo semestre 
	* Este metodo se usa para el reporte de mejor de mejor remunerativo
	*/
    public function mejorBrutoByPeriodo($mesInicio, $mesFin, $anio) {
       
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id_empleado', 'id_empleado');
        $rsm->addScalarResult('mejor_remuneracion', 'mejor_remuneracion');
        $rsm->addScalarResult('nro_legajo', 'nro_legajo');
        $rsm->addScalarResult('cuil', 'cuil');
        $rsm->addScalarResult('apellido', 'apellido');
        $rsm->addScalarResult('nombre', 'nombre');
        $rsm->addScalarResult('monto_basico', 'monto_basico');

        $native_query = $this->_em->createNativeQuery('
            SELECT  
                    le.id_empleado, 
                    MAX(bruto_1 + bruto_2 + IFNULL(le.adicional_remunerativo_retroactivo, 0) + IFNULL(en_suma.ajustes, 0) - IFNULL(en_resta.ajustes, 0) - IFNULL(integra_sac.monto, 0)) as mejor_remuneracion,
                    e.nro_legajo,
                    p.cuil,
                    p.apellido,
                    p.nombre,    
                    MAX(le.basico) AS monto_basico
            FROM liquidacion_empleado le
                INNER JOIN empleado e ON le.id_empleado = e.id
                INNER JOIN persona p ON e.id_persona = p.id
                INNER JOIN subcategoria	sub ON e.id_subcategoria = sub.id	
                INNER JOIN liquidacion l ON le.id_liquidacion = l.id
                LEFT JOIN (
                    SELECT SUM(en.valor) AS ajustes, en.id_liquidacion_ajuste, en.id_empleado 
                    FROM empleado_novedad en
                    INNER JOIN liquidacion_empleado_concepto lec ON en.id = lec.id_empleado_novedad
                    INNER JOIN concepto c ON en.id_concepto = c.id
                    WHERE 1 = 1
                    AND c.id_tipo_concepto <> 2 -- Que no sea no Remunerativo
                    AND c.integra_sac = TRUE
                    GROUP BY en.id_liquidacion_ajuste, en.id_empleado
                ) en_suma ON en_suma.id_liquidacion_ajuste = l.id AND le.id_empleado = en_suma.id_empleado
             
                LEFT JOIN (
                    SELECT 
                        IF (c.es_ajuste IS TRUE, ABS(SUM(lec.monto)), ABS(SUM(en.valor)) ) AS ajustes,
                        le.id_liquidacion, 
                        en.id_empleado
                    FROM empleado_novedad en
                        INNER JOIN liquidacion_empleado_concepto lec ON en.id = lec.id_empleado_novedad
                        INNER JOIN liquidacion_empleado le ON lec.id_liquidacion_empleado = le.id
                        INNER JOIN concepto c ON en.id_concepto = c.id
                    WHERE id_liquidacion_ajuste IS NOT NULL
                    AND c.integra_sac = TRUE
                    AND c.id_tipo_concepto <> 2 -- Que no sea no Remunerativo
                    GROUP BY le.id_liquidacion, en.id_empleado
                ) en_resta ON en_resta.id_liquidacion = l.id AND le.id_empleado = en_resta.id_empleado
				
				-- integra sac: no, no tiene que sumar para mejor remunerativo
				LEFT JOIN (
					SELECT 
						SUM(lec.monto) AS monto,
						en.id_empleado,
						le.id_liquidacion
					FROM empleado_novedad en
					INNER JOIN liquidacion_empleado_concepto lec ON en.id = lec.id_empleado_novedad
					INNER JOIN liquidacion_empleado le ON lec.id_liquidacion_empleado = le.id
					INNER JOIN concepto_version cv ON lec.id_concepto_version = cv.id AND lec.id_empleado_novedad = en.id
                    INNER JOIN concepto c ON cv.id_concepto = c.id
					WHERE c.integra_sac = FALSE
					AND c.id_tipo_concepto = 1 -- Remunerativo
					GROUP BY le.id_liquidacion, en.id_empleado
				) integra_sac ON le.id_empleado = integra_sac.id_empleado AND l.id = integra_sac.id_liquidacion
				
            WHERE 1 = 1
                AND MONTH(l.fecha_cierre_novedades) BETWEEN ? AND ?
                AND YEAR(l.fecha_cierre_novedades) = ?
                AND e.fecha_inicio_antiguedad < l.fecha_cierre_novedades
				AND e.activo = 1
                GROUP BY le.id_empleado
                ORDER BY p.apellido, p.nombre
        ', $rsm);

        $native_query->setParameter(1, $mesInicio);
        $native_query->setParameter(2, $mesFin);
        $native_query->setParameter(3, $anio);

        $resultado = $native_query->getResult();
		
		return $resultado;
        
        // return (count($result) == 0) ? 0 : $result[0]['mejor_remuneracion'];
    }
    
	/**
	* Calcula la mejor remuneracion para el primero o segundo semestre por id empleado
	* Este metodo se usa para el detalle del reporte de mejor de mejor remunerativo
	* desgloasdo por los meses
	*/
    public function mejorBrutoByPeriodoAndIdEmpleado($mesInicio, $mesFin, $anio, $idEmpleado) {
       
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id_empleado', 'id_empleado');
        $rsm->addScalarResult('empleado', 'empleado');
        $rsm->addScalarResult('mes_liquidacion', 'mes_liquidacion');
        $rsm->addScalarResult('anio_liquidacion', 'anio_liquidacion');
        $rsm->addScalarResult('tipo_liquidacion', 'tipo_liquidacion');
        $rsm->addScalarResult('basico', 'basico');
        $rsm->addScalarResult('bruto_1', 'bruto_1');
        $rsm->addScalarResult('bruto_2', 'bruto_2');
        $rsm->addScalarResult('bruto_sumado', 'bruto_sumado');
        $rsm->addScalarResult('ajuste_suma', 'ajuste_suma');
        $rsm->addScalarResult('ajuste_resta', 'ajuste_resta');
		$rsm->addScalarResult('monto_integra_sac', 'monto_integra_sac');
        $rsm->addScalarResult('adicional_remunerativo_retroactivo', 'adicional_remunerativo_retroactivo');
        $rsm->addScalarResult('mejor_remuneracion', 'mejor_remuneracion');
        $rsm->addScalarResult('fecha_cierre_novedades', 'fecha_cierre_novedades');
		$rsm->addScalarResult('id_liquidacion_empleado', 'id_liquidacion_empleado');
		
        $native_query = $this->_em->createNativeQuery('
            SELECT
                e.id as id_empleado,
                CONCAT(p.apellido, ", ", p.nombre, " (", e.nro_legajo, ")") AS empleado,
                MONTH(l.fecha_cierre_novedades) AS mes_liquidacion,
                YEAR(l.fecha_cierre_novedades) AS anio_liquidacion,  
                tl.nombre AS tipo_liquidacion,
                le.basico,
                le.bruto_1,
                le.bruto_2,
                (le.bruto_1 + le.bruto_2) AS bruto_sumado,
                en_suma.ajustes AS ajuste_suma,
                en_resta.ajustes AS ajuste_resta,
				integra_sac.monto AS monto_integra_sac,
                IFNULL(le.adicional_remunerativo_retroactivo, 0) AS adicional_remunerativo_retroactivo,
                (bruto_1 + bruto_2 + IFNULL(le.adicional_remunerativo_retroactivo, 0) + IFNULL(en_suma.ajustes, 0) - IFNULL(en_resta.ajustes, 0) - IFNULL(integra_sac.monto, 0)) AS mejor_remuneracion,
                l.fecha_cierre_novedades,
				le.id AS id_liquidacion_empleado
            FROM liquidacion_empleado le
                INNER JOIN empleado e ON le.id_empleado = e.id
                INNER JOIN persona p ON e.id_persona = p.id
                INNER JOIN subcategoria	sub ON e.id_subcategoria = sub.id	
                INNER JOIN liquidacion l ON le.id_liquidacion = l.id
                INNER JOIN tipo_liquidacion tl ON l.id_tipo_liquidacion	= tl.id
                LEFT JOIN (
                    SELECT
                         SUM(en.valor) AS ajustes,
                         en.id_liquidacion_ajuste,
                         en.id_empleado
                       FROM empleado_novedad en
                       INNER JOIN liquidacion_empleado_concepto lec ON en.id = lec.id_empleado_novedad
                       INNER JOIN concepto c ON en.id_concepto = c.id
                       WHERE 1 = 1 
                       AND c.id_tipo_concepto <> 2 -- Que no sea no Remunerativo
                       AND c.integra_sac = TRUE
                       GROUP BY en.id_liquidacion_ajuste, en.id_empleado
                ) en_suma ON en_suma.id_liquidacion_ajuste = l.id AND le.id_empleado = en_suma.id_empleado
                LEFT JOIN (
                    SELECT
                        IF (c.es_ajuste IS TRUE, ABS(SUM(lec.monto)), ABS(SUM(en.valor)) ) AS ajustes,
                        le.id_liquidacion,
                        en.id_empleado
                    FROM empleado_novedad en
                    INNER JOIN liquidacion_empleado_concepto lec ON en.id = lec.id_empleado_novedad
                    INNER JOIN liquidacion_empleado le ON lec.id_liquidacion_empleado = le.id
                    INNER JOIN concepto c ON en.id_concepto = c.id
                    WHERE id_liquidacion_ajuste IS NOT NULL
                    AND c.integra_sac = TRUE
                    AND c.id_tipo_concepto <> 2 -- Que no sea no Remunerativo
                    GROUP BY le.id_liquidacion, en.id_empleado
                ) en_resta ON en_resta.id_liquidacion = l.id AND le.id_empleado = en_resta.id_empleado
			  
			  -- integra sac: no, no tiene que sumar para mejor remunerativo
				LEFT JOIN (
					SELECT 
						SUM(lec.monto) AS monto,
						en.id_empleado,
						le.id_liquidacion
					FROM empleado_novedad en
					INNER JOIN liquidacion_empleado_concepto lec ON en.id = lec.id_empleado_novedad
					INNER JOIN liquidacion_empleado le ON lec.id_liquidacion_empleado = le.id
					INNER JOIN concepto_version cv ON lec.id_concepto_version = cv.id AND lec.id_empleado_novedad = en.id
                    INNER JOIN concepto c ON cv.id_concepto = c.id
					WHERE c.integra_sac = FALSE
					AND c.id_tipo_concepto = 1 -- Remunerativo
					GROUP BY le.id_liquidacion, en.id_empleado
				) integra_sac ON le.id_empleado = integra_sac.id_empleado AND l.id = integra_sac.id_liquidacion
			  
			  
          WHERE MONTH(l.fecha_cierre_novedades) BETWEEN ? AND ?
              AND YEAR(l.fecha_cierre_novedades) = ?
              AND e.activo = 1
              AND e.id = ?
              AND e.fecha_inicio_antiguedad < l.fecha_cierre_novedades
          ORDER BY mes_liquidacion ASC
        ', $rsm);

        $native_query->setParameter(1, $mesInicio);
        $native_query->setParameter(2, $mesFin);
        $native_query->setParameter(3, $anio);
        $native_query->setParameter(4, $idEmpleado);

        $resultado = $native_query->getResult();
		
		return $resultado;
    }
    
    /**
     * 
     * Cantidad de meses liquidados del período especificado para el empleado correspondiente.
     * 
     * @param int $idEmpleado
     * @param int $mesInicio
     * @param int $mesFin
     * @param int $anio
     * @return double
     */
    public function mesesLiquidadosByEmpleadoAndPeriodo($idEmpleado, $mesInicio, $mesFin, $anio){
        $result = $this->createQueryBuilder("l")
                ->select('l, le, COUNT(e.id) AS meses_liquidados')
                    ->innerJoin('l.liquidacionEmpleados', 'le')
                    ->innerJoin('le.empleado', 'e')
                ->where('e.id = :idEmpleado')                    
                    ->andWhere('MONTH(l.fechaCierreNovedades) BETWEEN :mesInicio AND :mesFin')    
                    ->andWhere('YEAR(l.fechaCierreNovedades) = :anio')
                ->setParameters(new ArrayCollection(array( 
                    new Parameter('idEmpleado', $idEmpleado),
                    new Parameter('mesInicio', $mesInicio),
                    new Parameter('mesFin', $mesFin),
                    new Parameter('anio', $anio))))
                ->groupBy('e.id')
                ->getQuery()
                ->setMaxResults(1)
                ->getResult();
        
        return (count($result) == 0) ? 0 : $result[0]['meses_liquidados'];
    }
    
    /**
     * 
     * Mejor BRUTO1 de los ultimos 12 meses mas el promedio del BRUTO2 de los ultimos 12 meses
     * 
     * @param int $idEmpleado
     * @return double
     */
    public function mejorRemuneracionHabitualByEmpleado($idEmpleado){
        $result = $this->createQueryBuilder("l")
                ->select('l, le, MAX(le.bruto1) + AVG(le.bruto2) AS mejor_remunerativo')
                    ->innerJoin('l.liquidacionEmpleados', 'le')
                    ->innerJoin('le.empleado', 'e')
                ->where('e.id = :idEmpleado')
                    ->setParameter('idEmpleado', $idEmpleado)
                    ->andWhere('l.fechaCierreNovedades BETWEEN DATE_SUB(DATE_SUB(CURRENT_TIMESTAMP(), 12, \'MONTH\'), (DAY(CURRENT_TIMESTAMP()) - 1), \'DAY\') AND CURRENT_TIMESTAMP()')
                ->groupBy('e.id')
                ->getQuery()
                ->setMaxResults(1)
                ->getResult();
        
        return (count($result) == 0) ? 0 : $result[0]['mejor_remunerativo'];
    }
    
    public function getConceptoByCodigoAndMes($idEmpleado, $codigo, $fechaCierreNovedades){
        $result = $this->createQueryBuilder("l")
                ->select('SUM(IFNULL(lec.monto, 0)) AS montoConcepto')
                    ->innerJoin('l.liquidacionEmpleados', 'le')
                    ->innerJoin('le.liquidacionEmpleadoConceptos', 'lec')
                    ->innerJoin('lec.conceptoVersion', 'cv')
                    ->innerJoin('le.empleado', 'e')
                ->where('e.id = :idEmpleado')
                    ->andWhere('cv.codigo = :codigoConcepto')
                    ->andWhere('YEAR(l.fechaCierreNovedades) = YEAR(:fechaCierre)')
                    ->andWhere('MONTH(l.fechaCierreNovedades) = MONTH(:fechaCierre)')
                    ->setParameters(array(
                        'idEmpleado' => $idEmpleado,
                        'codigoConcepto' => $codigo,
                        'fechaCierre' => $fechaCierreNovedades))
                ->getQuery()
                ->setMaxResults(1)
                ->getResult();
        
        return (count($result) == 0) ? 0 : $result[0]['montoConcepto'];
    }
    
    public function getGananciaByEmpleado2($idEmpleado, $fechaCierreNovedades){
        $result = $this->createQueryBuilder("l")
            ->select('SUM(IFNULL(ge.saldoImpuestoMes, 0)) - (IFNULL(lec.monto, 0)) AS retencion')
                ->innerJoin('l.liquidacionEmpleados', 'le')
                ->innerJoin('le.gananciaEmpleado', 'ge')
                ->innerJoin('le.empleado', 'e')
                ->leftJoin('le.liquidacionEmpleadoConceptos', 'lec')
                ->leftJoin('lec.conceptoVersion', 'cv', Join::WITH, 'cv.codigo = 994')
            ->where('e.id = :idEmpleado')
                ->andWhere('YEAR(l.fechaCierreNovedades) = YEAR(:fechaCierre)')
                ->andWhere('MONTH(l.fechaCierreNovedades) = MONTH(:fechaCierre)')
                ->setParameters(array(
                    'idEmpleado' => $idEmpleado,
                    'fechaCierre' => $fechaCierreNovedades))
            ->getQuery();
        echo $result->getSQL();die;
            //->setMaxResults(1)
            //->getResult();
        
        return (count($result) == 0) ? 0 : $result[0]['retencion'];
    }
    
    public function getGananciaByEmpleado($idEmpleado, $fechaCierreNovedades){
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('retenciones', 'retenciones');

/*
        $native_query = $this->_em->createNativeQuery('
            SELECT SUM(IFNULL(ge.saldo_impuesto_mes, 0)) + IFNULL(lec.bonificacion, 0) AS retenciones
            FROM liquidacion l
                INNER JOIN liquidacion_empleado le ON le.id_liquidacion = l.id
                INNER JOIN g_ganancia_empleado ge ON ge.id = le.id_ganancia_empleado                
                LEFT JOIN (
                    SELECT SUM(monto) AS bonificacion, id_liquidacion_empleado
                    FROM liquidacion_empleado_concepto lec
                        INNER JOIN concepto_version cv ON cv.id = lec.id_concepto_version
                    WHERE cv.codigo IN (?, ?, ?, ?)
                    GROUP BY id_liquidacion_empleado
                ) lec ON lec.id_liquidacion_empleado = le.id
            WHERE le.id_empleado = ?
                AND YEAR(l.fecha_cierre_novedades) = YEAR(?)
                AND MONTH(l.fecha_cierre_novedades) = MONTH (?)
            ', $rsm);
*/
			
		$native_query = $this->_em->createNativeQuery('
            SELECT 
			IFNULL(lec.bonificacion, 0) AS retenciones
            FROM liquidacion l
                INNER JOIN liquidacion_empleado le ON le.id_liquidacion = l.id
                INNER JOIN (
                    SELECT lec.id_liquidacion_empleado, SUM(lec.monto) AS bonificacion
                    FROM liquidacion_empleado_concepto lec
                    INNER JOIN liquidacion_empleado le ON lec.id_liquidacion_empleado = le.id
                    INNER JOIN liquidacion l ON le.id_liquidacion = l.id
                    INNER JOIN concepto_version cv ON cv.id = lec.id_concepto_version
                    WHERE 1 = 1 
                    AND cv.codigo IN (?, ?, ?, ?)
					AND le.id_empleado = ?
                    AND YEAR(l.fecha_cierre_novedades) = YEAR(?)
					AND MONTH(l.fecha_cierre_novedades) = MONTH(?)
                ) lec ON lec.id_liquidacion_empleado = le.id 
            ', $rsm);
        
        $native_query->setParameter(1, Concepto::__CODIGO_GANANCIAS); // codigo 999
        $native_query->setParameter(2, Concepto::__CODIGO_AJUSTE_GANANCIAS_SAC); // codigo 994
		$native_query->setParameter(3, Concepto::__CODIGO_DEVOLUCION_649); // codigo 998.1
        $native_query->setParameter(4, Concepto::__CODIGO_AJUSTE_LIQUIDACION_IMPUESTO_GANANCIAS); // codigo 998.4
        $native_query->setParameter(5, $idEmpleado);
        $native_query->setParameter(6, $fechaCierreNovedades);
        $native_query->setParameter(7, $fechaCierreNovedades);
        
        $result = $native_query->getResult();
        
        return isset($result[0]) ? $result[0]['retenciones'] : null;
    }
    
    public function getRemunerativoSinTopeByEmpleado($idEmpleado, $fechaCierreNovedades){
        $result = $this->createQueryBuilder("l")
            ->select('SUM(IFNULL(le.bruto1, 0)) + SUM(IFNULL(le.bruto2, 0)) AS remunerativo')
                ->innerJoin('l.liquidacionEmpleados', 'le')
                ->innerJoin('le.empleado', 'e')
            ->where('e.id = :idEmpleado')
                ->andWhere('YEAR(l.fechaCierreNovedades) = YEAR(:fechaCierre)')
                ->andWhere('MONTH(l.fechaCierreNovedades) = MONTH(:fechaCierre)')
                ->setParameters(array(
                    'idEmpleado' => $idEmpleado,
                    'fechaCierre' => $fechaCierreNovedades))
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();
        
        return (count($result) == 0) ? 0 : $result[0]['remunerativo'];
    }
    
    public function getRemunerativoConTopeByEmpleado($idEmpleado, $fechaCierreNovedades){
        $result = $this->createQueryBuilder("l")
            ->select('SUM(IFNULL(le.montoRemunerativoConTope, 0)) AS remunerativo_con_tope')
                ->innerJoin('l.liquidacionEmpleados', 'le')
                ->innerJoin('le.empleado', 'e')
            ->where('e.id = :idEmpleado')
                ->andWhere('YEAR(l.fechaCierreNovedades) = YEAR(:fechaCierre)')
                ->andWhere('MONTH(l.fechaCierreNovedades) = MONTH(:fechaCierre)')
                ->setParameters(array(
                    'idEmpleado' => $idEmpleado,
                    'fechaCierre' => $fechaCierreNovedades))
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();
        
        return (count($result) == 0) ? 0 : $result[0]['remunerativo_con_tope'];
    }
    
    public function getNoRemunerativoByEmpleado($idEmpleado, $fechaCierreNovedades){
        $result = $this->createQueryBuilder("l")
            ->select('SUM(IFNULL(le.noRemunerativo, 0)) AS no_remunerativo')
                ->innerJoin('l.liquidacionEmpleados', 'le')
                ->innerJoin('le.empleado', 'e')
            ->where('e.id = :idEmpleado')
                ->andWhere('YEAR(l.fechaCierreNovedades) = YEAR(:fechaCierre)')
                ->andWhere('MONTH(l.fechaCierreNovedades) = MONTH(:fechaCierre)')
                ->setParameters(array(
                    'idEmpleado' => $idEmpleado,
                    'fechaCierre' => $fechaCierreNovedades))
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();
        
        return (count($result) == 0) ? 0 : $result[0]['no_remunerativo'];
    }
    
    public function getMontoARTByLiquidacion($idLiquidacion){
        $result = $this->createQueryBuilder("l")
            ->select('SUM(IFNULL(lec.monto, 0)) AS monto_art')
                ->innerJoin('l.liquidacionEmpleados', 'le')
                ->innerJoin('le.liquidacionEmpleadoConceptos', 'lec')
                ->innerJoin('lec.conceptoVersion', 'cv')
            ->where('l.id = :idLiquidacion')
                ->andWhere('cv.codigo IN (:conceptosArt)')
                ->setParameters(array(
                    'idLiquidacion' => $idLiquidacion,
                    'conceptosArt' => array(Concepto::__CODIGO_ART_VARIABLE, Concepto::__CODIGO_ART_FIJA)))
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();
        
        return (count($result) == 0) ? 0 : $result[0]['monto_art'];
    }
    
    public function getGananciaByLiquidacion($idLiquidacion){
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('retenciones', 'retenciones');

        $native_query = $this->_em->createNativeQuery('
            SELECT IFNULL(SUM(IFNULL(ge.saldo_impuesto_mes, 0)) + IFNULL(lec.bonificacion, 0),0) AS retenciones
            FROM liquidacion l
                INNER JOIN liquidacion_empleado le ON le.id_liquidacion = l.id
                INNER JOIN g_ganancia_empleado ge ON ge.id = le.id_ganancia_empleado                
                LEFT JOIN (
                    SELECT SUM(monto) AS bonificacion, id_liquidacion_empleado
                    FROM liquidacion_empleado_concepto lec
                        INNER JOIN concepto_version cv ON cv.id = lec.id_concepto_version
                    WHERE cv.codigo IN (?, ?, ?)
                    GROUP BY id_liquidacion_empleado
                ) lec ON lec.id_liquidacion_empleado = le.id
            WHERE l.id = ?
            ', $rsm);
        
        $native_query->setParameter(1, Concepto::__CODIGO_AJUSTE_GANANCIAS_SAC);
        $native_query->setParameter(2, Concepto::__CODIGO_DEVOLUCION_649);
        $native_query->setParameter(3, Concepto::__CODIGO_DEVOLUCION_RESOLUCION_3770);
        $native_query->setParameter(4, $idLiquidacion);
        
        $result = $native_query->getResult();
        
        return isset($result[0]) ? $result[0]['retenciones'] : null;
    }
    
    public function getSicossByLiquidacion($idLiquidacion){
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('contribuciones', 'contribuciones');

        $native_query = $this->_em->createNativeQuery('
            SELECT SUM(IFNULL(lec.contribuciones, 0)) AS contribuciones
            FROM liquidacion l
                INNER JOIN liquidacion_empleado le ON le.id_liquidacion = l.id
                LEFT JOIN (
                    SELECT SUM(monto) AS contribuciones, id_liquidacion_empleado
                    FROM liquidacion_empleado_concepto lec
                        INNER JOIN concepto_version cv ON cv.id = lec.id_concepto_version
                        INNER JOIN tipo_concepto tc ON tc.id = cv.id_tipo_concepto
                    WHERE cv.id_tipo_concepto IN (:idsTiposConcepto)
                        OR cv.codigo IN (:conceptosSicoss)
                    GROUP BY id_liquidacion_empleado
                ) lec ON lec.id_liquidacion_empleado = le.id
            WHERE l.id = :idLiquidacion
            ', $rsm);
        
        $native_query->setParameter('idsTiposConcepto', array(TipoConcepto::__CONTRIBUCIONES, TipoConcepto::__CUOTA_SINDICAL_CONTRIBUCIONES));
        $native_query->setParameter('conceptosSicoss', array(100, 101, '101.1', '101.2', 102));
        $native_query->setParameter('idLiquidacion', $idLiquidacion);
        
        $result = $native_query->getResult();
        
        return isset($result[0]) ? $result[0]['contribuciones'] : null;
    }
	
	/**
	* Devuelve el id de la ultima liquidacion
	* @author gluis
	*/
	public function getMaxLiquidacionId(){
        $result = $this->createQueryBuilder("l")
                ->select('MAX(l.id) AS max_liquidacion_id')
                ->getQuery()
                ->setMaxResults(1)
                ->getResult();
        
        return (count($result) == 0) ? 0 : $result[0]['max_liquidacion_id'];
    }
	
	public function getEsBeneficiarioDecretoSACDiciembre2016($empleado)
	{
		// Busco en el ultimo semestre del año, si el empleado cobro mas de $55000
		$rsm = new ResultSetMapping();

		$rsm->addScalarResult('es_menor_55000', 'es_menor_55000');

		$sql = 'SELECT 
					IF ( (le.bruto_1 + le.bruto_2) <= 55000, TRUE, FALSE) AS es_menor_55000
				FROM liquidacion l
				INNER JOIN liquidacion_empleado le ON l.id = le.id_liquidacion
				INNER JOIN empleado e ON le.id_empleado = e.id
				WHERE l.numero IN (32,33,34,35,36) 
				AND e.id = ' . $empleado->getId();

		$query = $this->_em->createNativeQuery($sql, $rsm);
		$ultimoSemestre = $query->getResult();

		$esBeneficiarioDecreto = true;
		foreach($ultimoSemestre as $item) {
			if ($item['es_menor_55000'] == 0) {
				$esBeneficiarioDecreto = false;
				break 1;
			}
		}
		
		return $esBeneficiarioDecreto;
	}
	
	public function getNoRemunerativoIndemnizatorioByLiquidacionYEmpleado($liquidacion, $empleado)
	{
		$rsm = new ResultSetMapping();

		$rsm->addScalarResult('no_remunerativo_indemnizatorio', 'no_remunerativo_indemnizatorio');
		
		$sql = '
			SELECT IFNULL(SUM(lec.monto),0) AS no_remunerativo_indemnizatorio
			FROM liquidacion_empleado le
			INNER JOIN liquidacion_empleado_concepto lec ON le.id = lec.id_liquidacion_empleado
			INNER JOIN concepto_version cv ON cv.id = lec.id_concepto_version
			WHERE 1 = 1 
			AND le.id_empleado = ' . $empleado->getId() . '
			AND le.id_liquidacion = ' . $liquidacion->getId() . '
			AND cv.id_tipo_concepto = 2 -- No remunerativo
			AND cv.es_indemnizatorio = 1
		';
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		
		$result = $query->getResult();
        
        return isset($result[0]) ? $result[0]['no_remunerativo_indemnizatorio'] : null;
	}
    

}
