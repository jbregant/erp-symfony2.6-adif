<?php

namespace ADIF\RecursosHumanosBundle\EventListener;

use ADIF\RecursosHumanosBundle\Entity\Area;
use ADIF\RecursosHumanosBundle\Entity\Concepto;
use ADIF\RecursosHumanosBundle\Entity\ConfiguracionCuentaContableSueldos;
use ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor;
use ADIF\RecursosHumanosBundle\Entity\CuentaBancaria;
use ADIF\RecursosHumanosBundle\Entity\Gerencia;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * PostLoadEventSubscriber
 *
 * @author Darío Rapetti
 * created 28/10/2014
 * 
 * 
 */
class PostLoadEventSubscriber {

    /**
     * CLASE_AREA
     */
    const CLASE_AREA = 'ADIF\RecursosHumanosBundle\Entity\Area';

    /**
     * CLASE_CUENTA_BANCARIA_ADIF
     */
    const CLASE_CUENTA_BANCARIA_ADIF = 'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF';

    /**
     * CLASE_CONCEPTO
     */
    const CLASE_CONCEPTO = 'ADIF\RecursosHumanosBundle\Entity\Concepto';

    /**
     * CLASE_CONFIGURACION_CUENTA_CONTABLE_SUELDOS
     */
    const CLASE_CONFIGURACION_CUENTA_CONTABLE_SUELDOS = 'ADIF\RecursosHumanosBundle\Entity\ConfiguracionCuentaContableSueldos';

    /**
     * CLASE_CONSULTOR
     */
    const CLASE_CONSULTOR = 'ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor';

    /**
     * CLASE_GERENCIA
     */
    const CLASE_GERENCIA = 'ADIF\RecursosHumanosBundle\Entity\Gerencia';

    /**
     *
     * @var type \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $registry;

    /**
     * 
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $registry
     */
    public function __construct(Registry $registry) {

        $this->registry = $registry;
    }

    /**
     * 
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs) {

        $entity = $eventArgs->getEntity();

        // Si la entidad es un Concepto
        if ($entity instanceof Concepto) {

            // CuentaContable
            if (null != $entity->getIdCuentaContable()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONCEPTO, //
                        'cuentaContable', //
                        'ADIF\ContableBundle\Entity\CuentaContable', //
                        $entity->getIdCuentaContable())
                ;
            }
        }

        // Si la entidad es una ConfiguracionCuentaContableSueldos
        if ($entity instanceof ConfiguracionCuentaContableSueldos) {

            // CuentaContable
            if (null != $entity->getIdCuentaContable()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONFIGURACION_CUENTA_CONTABLE_SUELDOS, //
                        'cuentaContable', //
                        'ADIF\ContableBundle\Entity\CuentaContable', //
                        $entity->getIdCuentaContable())
                ;
            }
        }

        // Si la entidad es un Area
        if ($entity instanceof Area) {
            // CentroCosto
            if (null != $entity->getIdCentroCosto()) {
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_AREA, //
                        'centroCosto', //
                        'ADIF\ContableBundle\Entity\CentroCosto', //
                        $entity->getIdCentroCosto())
                ;
            }
        }

        // Si la entidad es un CuentaBancaria
        if ($entity instanceof CuentaBancaria) {

            // CuentaContable
            if (method_exists($entity, 'getIdCuentaContable') && null != $entity->getIdCuentaContable()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CUENTA_BANCARIA_ADIF, //
                        'cuentaContable', //
                        'ADIF\ContableBundle\Entity\CuentaContable', //
                        $entity->getIdCuentaContable())
                ;
            }
            
            // TipoMoneda
            if (method_exists($entity, 'getIdTipoMoneda') && null != $entity->getIdTipoMoneda()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CUENTA_BANCARIA_ADIF, //
                        'tipoMoneda', //
                        'ADIF\ContableBundle\Entity\TipoMoneda', //
                        $entity->getIdTipoMoneda())
                ;
            }            
        }

        // Si la entidad es una Gerencia
        if ($entity instanceof Gerencia) {

            // CuentaContable
            if (null != $entity->getIdCentroCosto()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_GERENCIA, //
                        'centroCosto', //
                        'ADIF\ContableBundle\Entity\CentroCosto', //
                        $entity->getIdCentroCosto())
                ;
            }
        }

        // Si la entidad es un Consultor
        if ($entity instanceof Consultor) {

            // DatosImpositivos
            if (null != $entity->getIdDatosImpositivos()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONSULTOR, //
                        'datosImpositivos', //
                        'ADIF\ComprasBundle\Entity\DatosImpositivos', //
                        $entity->getIdDatosImpositivos())
                ;
            }

            // CuentaContable
            if (null != $entity->getIdCuentaContable()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONSULTOR, //
                        'cuentaContable', //
                        'ADIF\ContableBundle\Entity\CuentaContable', //
                        $entity->getIdCuentaContable())
                ;
            }

            // TipoPago
            if (null != $entity->getIdTipoPago()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONSULTOR, //
                        'tipoPago', //
                        'ADIF\ContableBundle\Entity\TipoPago', //
                        $entity->getIdTipoPago())
                ;
            }

            // TipoMoneda
            if (null != $entity->getIdTipoMoneda()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONSULTOR, //
                        'tipoMoneda', //
                        'ADIF\ContableBundle\Entity\TipoMoneda', //
                        $entity->getIdTipoMoneda())
                ;
            }

            // CertificadoExencionIVA
            if (null != $entity->getIdCertificadoExencionIVA()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONSULTOR, //
                        'certificadoExencionIVA', //
                        'ADIF\ComprasBundle\Entity\CertificadoExencion', //
                        $entity->getIdCertificadoExencionIVA())
                ;
            }

            // CertificadoExencionGanancias
            if (null != $entity->getIdCertificadoExencionGanancias()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONSULTOR, //
                        'certificadoExencionGanancias', //
                        'ADIF\ComprasBundle\Entity\CertificadoExencion', //
                        $entity->getIdCertificadoExencionGanancias())
                ;
            }

            // CertificadoExencionIngresosBrutos
            if (null != $entity->getIdCertificadoExencionIngresosBrutos()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONSULTOR, //
                        'certificadoExencionIngresosBrutos', //
                        'ADIF\ComprasBundle\Entity\CertificadoExencion', //
                        $entity->getIdCertificadoExencionIngresosBrutos())
                ;
            }

            // CertificadoExencionSUSS
            if (null != $entity->getIdCertificadoExencionSUSS()) {

                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_CONSULTOR, //
                        'certificadoExencionSUSS', //
                        'ADIF\ComprasBundle\Entity\CertificadoExencion', //
                        $entity->getIdCertificadoExencionSUSS())
                ;
            }
        }

        // Si la entidad es un ConsultorProveedor
        if ($entity instanceof \ADIF\ComprasBundle\Entity\ConsultorProveedor) {
            
        }
    }

    /**
     * 
     * @param type $eventArgs
     * @param type $entityClass
     * @param type $property
     * @param type $referenceEntityClass
     * @param type $idEntity
     */
    private function setEntityValue($eventArgs, $entityClass, $property, $referenceEntityClass, $idEntity) {

        $em = $eventArgs->getEntityManager();

        $entity = $eventArgs->getEntity();

        $reflProp = $em->getClassMetadata($entityClass)
                ->reflClass->getProperty($property);

        $reflProp->setAccessible(true);

        $reflProp->setValue($entity, $this->registry->getManagerForClass($referenceEntityClass)
                        ->getReference($referenceEntityClass, $idEntity)
        );
    }

}
