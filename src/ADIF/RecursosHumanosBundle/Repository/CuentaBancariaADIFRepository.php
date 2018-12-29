<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * 
 */
class CuentaBancariaADIFRepository extends EntityRepository {

    /**
     * 
     * @param type $boolEstaActiva
     * @return type
     */
    public function findByEstaActiva($boolEstaActiva) {

        $estaActiva = $boolEstaActiva ? 1 : 0;

        $rsm = new ResultSetMapping();

        $rsm->addEntityResult('ADIFRecursosHumanosBundle:CuentaBancariaADIF', 'c');
        $rsm->addFieldResult('c', 'id', 'id');
        $rsm->addMetaResult('c', 'discriminador', 'discriminador');
        $rsm->setDiscriminatorColumn('c', 'discriminador');
        $rsm->addFieldResult('c', 'id_cuenta_contable', 'idCuentaContable');
        $rsm->addFieldResult('c', 'numero_sucursal', 'numeroSucursal');
        $rsm->addFieldResult('c', 'numero_cuenta', 'numeroCuenta');
        $rsm->addFieldResult('c', 'cbu', 'cbu');

        $rsm->addJoinedEntityResult('ADIFRecursosHumanosBundle:TipoCuenta', 'tc', 'c', 'idTipoCuenta');
        $rsm->addFieldResult('tc', 'id_tipo_cuenta', 'id');
        $rsm->addFieldResult('tc', 'nombre_tipo_cuenta', 'nombre');

        $rsm->addJoinedEntityResult('ADIFRecursosHumanosBundle:Banco', 'b', 'c', 'idBanco');
        $rsm->addFieldResult('b', 'id_banco', 'id');
        $rsm->addFieldResult('b', 'nombre_banco', 'nombre');

        $querySTR = '
            SELECT 
                c.id, c.discriminador, c.id_cuenta_contable, c.numero_sucursal, c.numero_cuenta, 
                c.cbu, tc.id AS id_tipo_cuenta, tc.nombre AS nombre_tipo_cuenta, b.id AS id_banco, b.nombre AS nombre_banco
            FROM cuenta c
            INNER JOIN tipo_cuenta tc ON c.id_tipo_cuenta = tc.id
            INNER JOIN banco b ON c.id_banco = b.id
            WHERE c.fecha_baja IS NULL
            AND c.discriminador = "adif"
            AND c.esta_activa = ?';

        $query = $this->getEntityManager()->createNativeQuery($querySTR, $rsm);

        $query->setParameter(1, $estaActiva);

        $result = $query->getResult();

        return $result;
    }

}
