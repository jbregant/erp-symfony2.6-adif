<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ADIF\CustomGeneratorBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator as Generator;

/**
 * Generates a CRUD controller.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CustomDoctrineCrudGenerator extends Generator {

    /**
     * Generates the new.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateNewView($dir) {
        $this->renderFile('crud/views/new.html.twig.twig', $dir . '/new.html.twig', array(
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'actions' => $this->actions,
            'fields' => $this->getFieldsFromMetadata($this->metadata),
        ));
    }

    private function getFieldsFromMetadata(ClassMetadataInfo $metadata) {
        $fields = (array) $metadata->fieldNames;

        // Remove the primary key field if it's not managed manually
        if (!$metadata->isIdentifierNatural()) {
            $fields = array_diff($fields, $metadata->identifier);
        }

        foreach ($metadata->associationMappings as $fieldName => $relation) {
            if ($relation['type'] !== ClassMetadataInfo::ONE_TO_MANY) {
                $fields[] = $fieldName;
            }
        }

        return $fields;
    }

    /**
     * Generate the CRUD controller.
     *
     * @param BundleInterface   $bundle           A bundle object
     * @param string            $entity           The entity relative class name
     * @param ClassMetadataInfo $metadata         The entity class metadata
     * @param string            $format           The configuration format (xml, yaml, annotation)
     * @param string            $routePrefix      The route name prefix
     * @param array             $needWriteActions Wether or not to generate write actions
     *
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite) {

        parent::generate($bundle, $entity, $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite);

        $dir = sprintf('%s/Resources/views/%s', $this->bundle->getPath(), str_replace('\\', '/', $this->entity));
        $tempPath = strtolower(substr($this->bundle->getNamespace(), strpos($this->bundle->getNamespace(), '\\') + 1));
        $webDir = sprintf('%sweb/js/custom/%s/%s', substr($dir, 0, strpos($dir, 'src')), str_replace('bundle', '', $tempPath), strtolower(str_replace('\\', '/', $this->entity)));

        $this->generateIndexTableView($dir);
        $this->generateIndexTableJavascript($webDir);
    }

    /**
     * Generates the edit.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateEditView($dir) {
        
    }

    /**
     * Generates the index.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateIndexView($dir) {
        $tempPath = strtolower(substr($this->bundle->getNamespace(), strpos($this->bundle->getNamespace(), '\\') + 1));

        $this->renderFile('crud/views/index.html.twig.twig', $dir . '/index.html.twig', array(
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'entityPath' => str_replace('\\', '/', $this->entity),
            'entityTable' => str_replace('\\', '_', $this->entity),
            'identifier' => $this->metadata->identifier[0],
            'fields' => $this->metadata->fieldMappings,
            'actions' => $this->actions,
            'bundleName' => str_replace('bundle', '', $tempPath),
            'record_actions' => $this->getRecordActions(),
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
        ));
    }

    /**
     * Generates the index_table.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateIndexTableView($dir) {
        $this->renderFile('crud/views/index_table.html.twig.twig', $dir . '/index_table.html.twig', array(
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'identifier' => $this->metadata->identifier[0],
            'entity' => $this->entity,
            'fields' => $this->metadata->fieldMappings,
            'bundle' => $this->bundle->getName(),
            'actions' => $this->actions,
        ));
    }

    /**
     * Generates the index.js file in the final bundle.
     *
     * @param string $webDir
     */
    protected function generateIndexTableJavascript($webDir) {
        $this->renderFile('crud/js/index.js.twig', $webDir . '/index.js', array(
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'identifier' => $this->metadata->identifier[0],
            'entity' => str_replace('\\', '_', $this->entity),
            'fields' => $this->metadata->fieldMappings,
            'bundle' => $this->bundle->getName(),
            'actions' => $this->actions,
        ));
    }

    /**
     * Generates the functional test class only.
     *
     */
    protected function generateTestClass() {
        return;
    }

}
