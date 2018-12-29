<?php

namespace ADIF\ContableBundle\Repository;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoOperacionContable;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * 
 */
class CuentaContableRepository extends EntityRepository {

    /**
     * 
     * @return type
     */
    public function getCuentasContablesRaiz() {

        $query = $this->createQueryBuilder('c')
                ->select('partial c.{id, codigoCuentaContable, denominacionCuentaContable}')
                ->where('c.cuentaContablePadre IS NULL')
                ->getQuery()
                ->useResultCache(true, 7200, 'cuentas_contables')
                ->setHydrationMode(Query::HYDRATE_ARRAY);

        return $query->getResult();
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function getCuentasContablesHijas($id) {

        $query = $this->createQueryBuilder('c')
                ->select('partial c.{id, codigoCuentaContable, denominacionCuentaContable, esImputable}')
                ->join('c.cuentaContablePadre', 'cp')
                ->where('cp.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->setHydrationMode(Query::HYDRATE_ARRAY);

        return $query->getResult();
    }

    /**
     * 
     * @param type $string
     * @return type
     */
    public function getCuentasContablesByString($string) {

        $qb = $this->createQueryBuilder('c');

        $query = $qb->select('partial c.{id, codigoCuentaContable, denominacionCuentaContable, esImputable}')
                ->where($qb->expr()->like('c.codigoCuentaContable', ':string'))
                ->orWhere($qb->expr()->like('c.denominacionCuentaContable', ':string'))
                ->setParameter('string', '%' . $string . '%')
                ->getQuery()
                ->setHydrationMode(Query::HYDRATE_ARRAY);

        return $query->getResult();
    }

    /**
     * 
     * @param type $id
     * @param type $fechaLimite
     * @param type $fechaInicio
     * @return type
     */
    public function getSaldoALaFecha($id, $fechaLimite, $fechaInicio = null) {

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('saldo', 'saldo');

        $querySTR = "
            SELECT IFNULL(sum(acumulado.debe) - sum(acumulado.haber), 0) AS saldo
            FROM (SELECT cc.id AS id_cuenta_contable,
                    cc.codigo AS codigo_cuenta_contable,
                    cc.denominacion AS denominacion_cuenta_contable,
                    CASE toc.denominacion WHEN ? THEN sum(r.importe_mcl)
                        ELSE 0
                    END AS debe,
                    CASE toc.denominacion WHEN ? THEN sum(r.importe_mcl)
                        ELSE 0
                    END AS haber
                FROM cuenta_contable cc
                    LEFT JOIN (SELECT r.id_cuenta_contable, r.id_tipo_operacion_contable, r.importe_mcl
                                FROM renglon_asiento_contable r 
                                    INNER JOIN asiento_contable a ON r.id_asiento_contable = a.id
                                WHERE a.fecha_baja IS NULL
                                    AND r.fecha_baja IS NULL
                                    AND DATE(a.fecha_contable) < ?
                                    ";
        
        if ($fechaInicio != null) {
            $querySTR .= "AND DATE(a.fecha_contable) >= ?";
        }
        
        $querySTR .= "                              ) r ON r.id_cuenta_contable = cc.id
                    LEFT JOIN tipo_operacion_contable toc ON r.id_tipo_operacion_contable = toc.id
                GROUP BY cc.id, r.id_tipo_operacion_contable
		) AS acumulado
            WHERE acumulado.id_cuenta_contable = ?";


        $query = $this->getEntityManager()->createNativeQuery($querySTR, $rsm);

        $query->setParameter(1, ConstanteTipoOperacionContable::DEBE);
        $query->setParameter(2, ConstanteTipoOperacionContable::HABER);
        $query->setParameter(3, $fechaLimite);

        if ($fechaInicio != null) {
            $query->setParameter(4, $fechaInicio);
            $query->setParameter(5, $id);
        } else {
            $query->setParameter(4, $id);
        }

        $result = $query->getResult();

        return round($result[0]['saldo'], 2);
    }

    public function getSaldoRangoEjercicio($id, $mesInicio, $mesFin, $ejercicio) {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('saldo', 'saldo');

        $querySTR = "
            SELECT IFNULL(sum(acumulado.debe) - sum(acumulado.haber), 0) AS saldo
            FROM (SELECT cc.id AS id_cuenta_contable,
                    cc.codigo AS codigo_cuenta_contable,
                    cc.denominacion AS denominacion_cuenta_contable,
                    CASE toc.denominacion WHEN ? THEN sum(r.importe_mcl)
                        ELSE 0
                    END AS debe,
                    CASE toc.denominacion WHEN ? THEN sum(r.importe_mcl)
                        ELSE 0
                    END AS haber
                FROM cuenta_contable cc
                    LEFT JOIN renglon_asiento_contable r ON r.id_cuenta_contable = cc.id
                    LEFT JOIN tipo_operacion_contable toc ON r.id_tipo_operacion_contable = toc.id
                    LEFT JOIN asiento_contable a ON r.id_asiento_contable = a.id
                WHERE(
                    (
                        YEAR(a.fecha_contable) = ? 
                        AND (MONTH(a.fecha_contable) BETWEEN ? AND ?) 
                        AND a.fecha_baja IS NULL
                        AND r.fecha_baja IS NULL
                    )
                        OR (a.id IS NULL)
                    )	
                GROUP BY cc.id,
                    r.id_tipo_operacion_contable
		) AS acumulado
            WHERE acumulado.id_cuenta_contable = ?";


        $query = $this->getEntityManager()->createNativeQuery($querySTR, $rsm);

        $query->setParameter(1, ConstanteTipoOperacionContable::DEBE);
        $query->setParameter(2, ConstanteTipoOperacionContable::HABER);
        $query->setParameter(3, $ejercicio);
        $query->setParameter(4, $mesInicio);
        $query->setParameter(5, $mesFin);
        $query->setParameter(6, $id);

        $result = $query->getResult();

        return round($result[0]['saldo'], 2);
    }

    public function getHaberRangoEjercicio($id, $mesInicio, $mesFin, $ejercicio, $constanteConcepto = null) {
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('saldo', 'saldo');

        $querySTR = "
            SELECT IFNULL(sum(acumulado.haber), 0) AS saldo
            FROM (SELECT cc.id AS id_cuenta_contable,
                    cc.codigo AS codigo_cuenta_contable,
                    cc.denominacion AS denominacion_cuenta_contable,
                    CASE toc.denominacion WHEN ? THEN sum(r.importe_mcl)
                        ELSE 0
                    END AS debe,
                    CASE toc.denominacion WHEN ? THEN sum(r.importe_mcl)
                        ELSE 0
                    END AS haber
                FROM cuenta_contable cc
                    LEFT JOIN renglon_asiento_contable r ON r.id_cuenta_contable = cc.id
                    LEFT JOIN tipo_operacion_contable toc ON r.id_tipo_operacion_contable = toc.id
                    LEFT JOIN asiento_contable a ON r.id_asiento_contable = a.id";
        if ($constanteConcepto != null) {
            $querySTR .= ' INNER JOIN concepto_asiento_contable cac ON (cac.id = a.id_concepto_asiento_contable AND cac.codigo = "' . $constanteConcepto . '")';
        }
        $querySTR .=" WHERE(
                    (
                        YEAR(a.fecha_contable) = ? 
                        AND (MONTH(a.fecha_contable) BETWEEN ? AND ?) 
                        AND a.fecha_baja IS NULL
                        AND r.fecha_baja IS NULL
                    )
                        OR (a.id IS NULL)
                    )	
                GROUP BY cc.id,
                    r.id_tipo_operacion_contable
		) AS acumulado
            WHERE acumulado.id_cuenta_contable = ?";


        $query = $this->getEntityManager()->createNativeQuery($querySTR, $rsm);

        $query->setParameter(1, ConstanteTipoOperacionContable::DEBE);
        $query->setParameter(2, ConstanteTipoOperacionContable::HABER);
        $query->setParameter(3, $ejercicio);
        $query->setParameter(4, $mesInicio);
        $query->setParameter(5, $mesFin);
        $query->setParameter(6, $id);

        $result = $query->getResult();

        return round($result[0]['saldo'], 2);
    }

}
