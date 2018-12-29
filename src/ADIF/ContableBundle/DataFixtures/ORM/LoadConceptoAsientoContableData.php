<?php

namespace ADIF\ContableBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ADIF\ContableBundle\Entity\ConceptoAsientoContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteSubdiarioAsientoContable;

/**
 * LoadConceptoAsientoContableData
 * 
 * @author Manuel Becerra
 * created 23/10/2014
 *
 */
class LoadConceptoAsientoContableData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * 
     * @param type $manager
     * @param type $subdiarioAsientoContable
     * @param type $alias
     * @param type $codigo
     * @param type $denominacion
     * @param type $descripcion
     */
    private function setConceptoAsientoContable($manager, $subdiarioAsientoContable, $alias, $codigo, $denominacion, $descripcion) {

        $conceptoAsientoContable = new ConceptoAsientoContable();

        $conceptoAsientoContable->setSubdiarioAsientoContable($subdiarioAsientoContable);
        $conceptoAsientoContable->setAlias($alias);
        $conceptoAsientoContable->setCodigo($codigo);
        $conceptoAsientoContable->setDenominacion($denominacion);
        $conceptoAsientoContable->setDescripcion($descripcion);

        $manager->persist($conceptoAsientoContable);
    }

    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        // Subdiario compras
        $subdiarioCompras = $this->getReference(ConstanteSubdiarioAsientoContable::SUBDIARIO_COMPRAS);

        $this->setConceptoAsientoContable($manager, $subdiarioCompras, 'CP', 'COMPRAS', 'Asiento de compras', '');
        $this->setConceptoAsientoContable($manager, $subdiarioCompras, 'DCLS', 'DEVENGAMIENTO_CONTRATO_LOCACION_SERVICIO', 'Devengamiento contratos de locación de servicios', '');
        $this->setConceptoAsientoContable($manager, $subdiarioCompras, 'PCLS', 'PAGO_CONTRATO_LOCACION_SERVICIO', 'Pago contratos de locación de servicios', '');


        // Subdiario ventas
        $subdiarioVentas = $this->getReference(ConstanteSubdiarioAsientoContable::SUBDIARIO_VENTAS);

        $this->setConceptoAsientoContable($manager, $subdiarioVentas, 'ING', 'DEVENGAMIENTO_INGRESOS', 'Asiento de devengamiento de ingresos', '');
        $this->setConceptoAsientoContable($manager, $subdiarioVentas, 'AV', 'VENTAS', 'Asiento de ventas', '');


        // Subdiario caja y bancos
        $subdiarioCajaYBancos = $this->getReference(ConstanteSubdiarioAsientoContable::SUBDIARIO_CAJA_BANCOS);

        $this->setConceptoAsientoContable($manager, $subdiarioCajaYBancos, 'EGR', 'PAGO_PROVEEDORES', 'Asiento de pago a proveedores', '');
        $this->setConceptoAsientoContable($manager, $subdiarioCajaYBancos, 'TS', 'TESORERIA', 'Asiento de tesoreria', '');
        $this->setConceptoAsientoContable($manager, $subdiarioCajaYBancos, 'CC', 'COBRANZAS', 'Asiento de cobranzas', '');

        // Contabilidad general
        $contabilidadGeneral = $this->getReference(ConstanteSubdiarioAsientoContable::SUBDIARIO_CONTABILIDAD_GENERAL);

        $this->setConceptoAsientoContable($manager, $contabilidadGeneral, 'SU', 'DEVENGAMIENTO_SUELDOS', 'Asiento de devengamiento de sueldos', '');
        $this->setConceptoAsientoContable($manager, $contabilidadGeneral, 'CRED', 'DEVENGAMIENTO_CREDITOS_MINISTERIALES', 'Asiento de devengamiento de créditos ministeriales', '');
        $this->setConceptoAsientoContable($manager, $contabilidadGeneral, 'PSU', 'PAGO_SUELDOS', 'Asiento de pago de sueldos', '');
        $this->setConceptoAsientoContable($manager, $contabilidadGeneral, 'ASU', 'ANTICIPO_SUELDO', 'Asiento de anticipo de sueldo', '');
        $this->setConceptoAsientoContable($manager, $contabilidadGeneral, 'PASU', 'PAGO_ANTICIPO_SUELDO', 'Asiento de pago de anticipo de sueldo', '');

        // Subdiario obras
        $subdiarioObras = $this->getReference(ConstanteSubdiarioAsientoContable::SUBDIARIO_OBRAS);

        $this->setConceptoAsientoContable($manager, $subdiarioObras, 'OB', 'OBRAS', 'Asiento de obras', '');


        $manager->flush();
    }

    /**
     * 
     * @return int
     */
    public function getOrder() {
        return 3;
    }

}
