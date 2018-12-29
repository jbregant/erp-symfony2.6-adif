<?php

namespace ADIF\InventarioBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ADIF\InventarioBundle\Entity\Almacen;
use ADIF\InventarioBundle\Entity\CatalogoMaterialesRodantes;
use ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos;
use ADIF\InventarioBundle\Entity\ActivoLineal;
use ADIF\InventarioBundle\Entity\HojaRuta;
use ADIF\InventarioBundle\Entity\ItemHojaRutaNuevoProducido;

/**
 * PostLoadEventSubscriber
 *
 */
class PostLoadEventSubscriber {

    /**
     * CLASE_ALMACEN
     */
    const CLASE_ALMACEN = 'ADIF\InventarioBundle\Entity\Almacen';

    /**
     * CLASE_ACTIVO_LINEAL
     */
    const CLASE_ACTIVO_LINEAL = 'ADIF\InventarioBundle\Entity\ActivoLineal';

    /**
     * CLASE_HOJA_RUTA
     */
    const CLASE_HOJA_RUTA = 'ADIF\InventarioBundle\Entity\HojaRuta';

    /**
     * CLASE_MATERIAL_RODANTE
     */
    const CLASE_MATERIAL_RODANTE = 'ADIF\InventarioBundle\Entity\CatalogoMaterialesRodantes';

    /**
     * CLASE_MATERIAL_NUEVO
     */
    const CLASE_MATERIAL_NUEVO = 'ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos';

     /**
     * CLASE_HOJARUTA_MATERIAL_NUEVO_PRODUCIDO_ITEM
     */
    const CLASE_HOJARUTA_MATERIAL_NUEVO_PRODUCIDO_ITEM = 'ADIF\InventarioBundle\Entity\ItemHojaRutaNuevoProducido';

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
    public function prePersist(LifecycleEventArgs $eventArgs) {

        $this->updateEntities($eventArgs);
    }

    /**
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs) {

        $this->updateEntities($eventArgs);
    }

    /**
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs) {

        $entity = $eventArgs->getEntity();

        // Si la entidad es un Almacen:
        if ($entity instanceof Almacen && null != $entity->getIdProvincia()) {

            $this->setEntityValue(
                $eventArgs, //
                PostLoadEventSubscriber::CLASE_ALMACEN, //
                'provincia', //
                'ADIF\RecursosHumanosBundle\Entity\Provincia', //
                $entity->getIdProvincia())
            ;
        }

        // Si la entidad es un Material Rodante:
        if ($entity instanceof CatalogoMaterialesRodantes && null != $entity->getIdProvincia()) {

            $this->setEntityValue(
                $eventArgs, //
                PostLoadEventSubscriber::CLASE_MATERIAL_RODANTE, //
                'provincia', //
                'ADIF\RecursosHumanosBundle\Entity\Provincia', //
                $entity->getIdProvincia())
            ;
        }

         // Si la entidad es un Material Nuevo:
        if ($entity instanceof CatalogoMaterialesNuevos && null != $entity->getIdTipoImpuesto() ) {

            $this->setEntityValue(
                $eventArgs, //
                PostLoadEventSubscriber::CLASE_MATERIAL_NUEVO, //
                'tipoImpuesto', //
                'ADIF\ContableBundle\Entity\TipoImpuesto', //
                $entity->getIdTipoImpuesto())
            ;
        }

        // Si la entidad es un ActivoLineal:
        if ($entity instanceof ActivoLineal && null != $entity->getIdLocalidad()) {

            $this->setEntityValue(
                $eventArgs, //
                PostLoadEventSubscriber::CLASE_ACTIVO_LINEAL, //
                'localidad', //
                'ADIF\RecursosHumanosBundle\Entity\Localidad', //
                $entity->getIdLocalidad())
            ;
        }

        // Si la entidad es HojaRuta:
        if ($entity instanceof HojaRuta && null != $entity->getIdUsuarioAsignado()) {

            $this->setEntityValue(
                $eventArgs, //
                PostLoadEventSubscriber::CLASE_HOJA_RUTA, //
                'usuarioAsignado', //
                'ADIF\AutenticacionBundle\Entity\Usuario', //
                $entity->getIdUsuarioAsignado())
            ;
        }

        // Si la entidad es un ItemHojaRutaNuevoProducido:
        if ($entity instanceof ItemHojaRutaNuevoProducido && null != $entity->getIdProvincia()) {

            $this->setEntityValue(
                $eventArgs, //
                PostLoadEventSubscriber::CLASE_HOJARUTA_MATERIAL_NUEVO_PRODUCIDO_ITEM, //
                'provincia', //
                'ADIF\RecursosHumanosBundle\Entity\Provincia', //
                $entity->getIdProvincia())
            ;
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

        $value = $this->registry->getManagerForClass($referenceEntityClass)
                    ->getReference($referenceEntityClass, $idEntity);

        $reflProp->setValue( $entity, $value );
    }

    /**
     *
     * @param type $entity
     * @return type
     */
    private function updateEntityId($entity) {

        $entityManager = $this->registry->getManagerForClass(get_class($entity));

        $entityManager->persist($entity);
        $entityManager->flush();

        return $entity->getId();
    }

    /**
     *
     * @param type $eventArgs
     */
    private function updateEntities($eventArgs) {

        $entity = $eventArgs->getEntity();

        // Si la entidad es Almacen
        if ($entity instanceof Almacen && null != $entity->getProvincia()) {

            $entityId = $this->updateEntityId($entity->getProvincia());

            $entity->setIdProvincia($entityId);
        }

        // Si la entidad es Material Rodante
        if ($entity instanceof CatalogoMaterialesRodantes && null != $entity->getProvincia()) {

            $entityId = $this->updateEntityId($entity->getProvincia());

            $entity->setIdProvincia($entityId);
        }

        // Si la entidad es HojaRuta
        if ($entity instanceof HojaRuta && null != $entity->getUsuarioAsignado()) {

            $entityId = $this->updateEntityId($entity->getUsuarioAsignado());

            $entity->setIdUsuarioAsignado($entityId);
        }

        // Si la entidad es Material Nuevo
        if ($entity instanceof CatalogoMaterialesNuevos && null != $entity->getTipoImpuesto()) {

            $entityId = $this->updateEntityId($entity->getTipoImpuesto());

            $entity->setIdTipoImpuesto($entityId);
        }

         // Si la entidad es ItemHojaRutaNuevoProducido
        if ($entity instanceof ItemHojaRutaNuevoProducido && null != $entity->getProvincia()) {

            $entityId = $this->updateEntityId($entity->getProvincia());

            $entity->setIdProvincia($entityId);
        }

        // Si la entidad es Material Producido de Obra
        if ($entity instanceof CatalogoMaterialesProducidosDeObra && null != $entity->getTipoImpuesto()) {

            $entityId = $this->updateEntityId($entity->getTipoImpuesto());

            $entity->setIdTipoImpuesto($entityId);
        }

    }

}
