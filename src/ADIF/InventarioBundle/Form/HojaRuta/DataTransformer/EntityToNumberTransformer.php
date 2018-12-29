<?php
namespace ADIF\InventarioBundle\Form\HojaRuta\DataTransformer;

/**
 * Clase para transformar una entidad a un numero y viceversa
 *
 * @author gyl
 */
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;
    private $entityName;

    public function __construct(EntityManager $entityManager,$entityName)
    {
        $this->entityManager = $entityManager;
        $this->entityName = $entityName;
    }

    /**
     * Transforms an object (entity) to a string (number).
     *
     * @param  Entity|null $entity
     * @return string
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return '';
        }

        return $entity->getId();
    }

    /**
     * Transforms a string (number) to an object (entity).
     *
     * @param  string $entityId
     * @return Entity|null
     * @throws TransformationFailedException if object (entity) is not found.
     */
    public function reverseTransform($entityId)
    {
        // no entity id number? It's optional, so that's ok
        if (!$entityId) {
            return;
        }

        $entity = $this->entityManager
            ->getRepository('ADIFInventarioBundle:'.$this->entityName)
            // query for the issue with this id
            ->find($entityId)
        ;

        if (null === $entity) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $entityId
            ));
        }

        return $entity;
    }
}