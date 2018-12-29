<?php

namespace ADIF\AutenticacionBundle\EventListener;

use ADIF\AutenticacionBundle\Entity\Usuario;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * PostLoadEventSubscriber
 *
 * @author Manuel Becerra
 * created 26/07/2014
 * 
 * 
 */
class PostLoadEventSubscriber {

    /**
     * CLASE_USUARIO
     */
    const CLASE_USUARIO = 'ADIF\AutenticacionBundle\Entity\Usuario';

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

        // Si la entidad es un Usuario
        if ($entity instanceof Usuario && null != $entity->getIdArea()) {

            $this->setEntityValue(
                    $eventArgs, //
                    PostLoadEventSubscriber::CLASE_USUARIO, //
                    'area', //
                    'ADIF\RecursosHumanosBundle\Entity\Area', //
                    $entity->getIdArea())
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

        $reflProp->setValue(
                $entity, $this->registry->getManagerForClass($referenceEntityClass)
                        ->getReference($referenceEntityClass, $idEntity)
        );
    }

}
