<?php

namespace ADIF\PortalProveedoresBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ADIF\PortalProveedoresBundle\Entity\ProveedorDatoPersonal;
use ADIF\PortalProveedoresBundle\Entity\ProveedorDomicilio;
use ADIF\PortalProveedoresBundle\Entity\ImpuestoIibb;
use ADIF\PortalProveedoresBundle\Entity\ProveedorDatoBancario;
use ADIF\PortalProveedoresBundle\Entity\ProveedorRepresentanteApoderado;

/**
 * PostLoadEventSubscriber
 *
 */
class PostLoadEventSubscriber {

    /**
     * CLASE_DATO_CONTACTO_ADICIONAL
     */
    const CLASE_DATO_CONTACTO_ADICIONAL = 'ADIF\PortalProveedoresBundle\Entity\DatoContacto';
    
    /**
     * CLASE_PROVEEDOR_DATO_PERSONAL
     */
    const CLASE_PROVEEDOR_DATO_PERSONAL = 'ADIF\PortalProveedoresBundle\Entity\ProveedorDatoPersonal';
    
    /**
     * CLASE_PROVEEDOR_DOMICILIO
     */
    const CLASE_PROVEEDOR_DOMICILIO = 'ADIF\PortalProveedoresBundle\Entity\ProveedorDomicilio';
    
    /**
     * CLASE_IMPUESTO_IIBB
     */
    const CLASE_IMPUESTO_IIBB = 'ADIF\PortalProveedoresBundle\Entity\ImpuestoIibb';    

    /**
     * CLASE_PROVEEDOR_DATO_BANCARIO
     */
    const CLASE_PROVEEDOR_DATO_BANCARIO = 'ADIF\PortalProveedoresBundle\Entity\ProveedorDatoBancario';    

    /**
     * CLASE_PROVEEDOR_REPRESENTANTE_APODERADO
     */
    const CLASE_PROVEEDOR_REPRESENTANTE_APODERADO = 'ADIF\PortalProveedoresBundle\Entity\ProveedorRepresentanteApoderado';    

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
        
       if ($entity instanceof ProveedorDatoPersonal) {         

            if (null != $entity->getIdTipoDocumento()) {
                
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_PROVEEDOR_DATO_PERSONAL, 
                        'tipoDocumento', 
                        'ADIF\RecursosHumanosBundle\Entity\TipoDocumento', 
                        $entity->getIdTipoDocumento());
            }

            if (null != $entity->getIdProveedorAsoc()) {
                
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_PROVEEDOR_DATO_PERSONAL, 
                        'proveedorAsoc', 
                        'ADIF\ComprasBundle\Entity\Proveedor', 
                        $entity->getIdProveedorAsoc());
            }
            
            if (null != $entity->getIdPaisRadicacion()) {
                
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_PROVEEDOR_DATO_PERSONAL, 
                        'paisRadicacion', 
                        'ADIF\RecursosHumanosBundle\Entity\Nacionalidad', 
                        $entity->getIdPaisRadicacion());
            }
        }

        if ($entity instanceof ProveedorDomicilio) {
            if (null != $entity->getIdPais()) {
                $this->setEntityValue(
                        $eventArgs,
                        PostLoadEventSubscriber::CLASE_PROVEEDOR_DOMICILIO,
                        'pais',
                        'ADIF\RecursosHumanosBundle\Entity\Nacionalidad',
                        $entity->getIdPais());
            }
            
            if (null != $entity->getIdProvincia()) {
                $this->setEntityValue(
                        $eventArgs,
                        PostLoadEventSubscriber::CLASE_PROVEEDOR_DOMICILIO,
                        'provincia',
                        'ADIF\RecursosHumanosBundle\Entity\Provincia',
                        $entity->getIdProvincia());
            }
            
            if (null != $entity->getIdLocalidad()) {
                $this->setEntityValue(
                        $eventArgs,
                        PostLoadEventSubscriber::CLASE_PROVEEDOR_DOMICILIO,
                        'localidad',
                        'ADIF\RecursosHumanosBundle\Entity\Localidad',
                        $entity->getIdLocalidad());
            }
        }
        
        if ($entity instanceof ImpuestoIibb) {
            if (null != $entity->getIdJurisdiccion()) {
                $this->setEntityValue(
                        $eventArgs,
                        PostLoadEventSubscriber::CLASE_IMPUESTO_IIBB,
                        'jurisdiccion',
                        'ADIF\ContableBundle\Entity\Jurisdiccion',
                        $entity->getIdJurisdiccion());
            }            
        }

        if ($entity instanceof ProveedorDatoBancario) {
            if (null != $entity->getIdEntidadBancaria()) {
                $this->setEntityValue(
                        $eventArgs,
                        PostLoadEventSubscriber::CLASE_PROVEEDOR_DATO_BANCARIO,
                        'entidadBancaria',
                        'ADIF\RecursosHumanosBundle\Entity\Banco',
                        $entity->getIdEntidadBancaria());
            }            

            if (null != $entity->getTipoMoneda()) {
                $this->setEntityValue(
                        $eventArgs,
                        PostLoadEventSubscriber::CLASE_PROVEEDOR_DATO_BANCARIO,
                        'moneda',
                        'ADIF\ContableBundle\Entity\TipoMoneda',
                        $entity->getTipoMoneda());
            }            
        }
        
        if ($entity instanceof ProveedorRepresentanteApoderado) {
            if (null != $entity->getIdTipoDocumento()) {
                
                $this->setEntityValue(
                        $eventArgs, //
                        PostLoadEventSubscriber::CLASE_PROVEEDOR_REPRESENTANTE_APODERADO, 
                        'tipoDocumento', 
                        'ADIF\RecursosHumanosBundle\Entity\TipoDocumento', 
                        $entity->getIdTipoDocumento());
            }            
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

        // Si la entidad es DatoContacto
        if ($entity instanceof DatoContacto) {

            if (null != $entity->getIdDatoContactoEmail()) {

                $entityId = $this->updateEntityId($entity->getEmail());
                $entity->setIdDatoContactoEmail($entityId);
            }

            if (null != $entity->getIdDatoContactoTelefono()) {

                $entityId = $this->updateEntityId($entity->getTelefono());
                $entity->setIdDatoContactoTelefono($entityId);
            }
        }

        // Si la entidad es ProveedorDatoPersonal
        if ($entity instanceof ProveedorDatoPersonal) {

            if (null != $entity->getIdTipoDocumento()) {

                $entityId = $this->updateEntityId($entity->getTipoDocumento());
                $entity->setIdTipoDocumento($entityId);
            }

            if (null != $entity->getIdProveedorAsoc()) {

                $entityId = $this->updateEntityId($entity->getIdProveedorAsoc());
                $entity->setIdProveedorAsoc($entityId);
            }

            if (null != $entity->getIdPaisRadicacion()) {

                $entityId = $this->updateEntityId($entity->getPaisRadicacion());
                $entity->setIdPaisRadicacion($entityId);
            }
        }
    }

}
