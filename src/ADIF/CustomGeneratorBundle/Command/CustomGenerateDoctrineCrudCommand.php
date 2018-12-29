<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ADIF\CustomGeneratorBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineFormGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
//use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use ADIF\CustomGeneratorBundle\Generator\CustomDoctrineCrudGenerator;
use ADIF\CustomGeneratorBundle\Generator\CustomDoctrineFormGenerator;

/**
 * Generates a CRUD for a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CustomGenerateDoctrineCrudCommand extends GenerateDoctrineCrudCommand {

    private $formGenerator;

    /**
     * @see Command
     */
    protected function configure() {
        parent::configure();
        $this->setName('custom:doctrine:generate:crud');
    }

    protected function getGenerator(BundleInterface $bundle = null) {
        $generator = new CustomDoctrineCrudGenerator($this->getContainer()->get('filesystem'));
        $this->setGenerator($generator);
        $generator->setSkeletonDirs($this->getSkeletonDirs($bundle));
        return $generator;
    }

    protected function getFormGenerator($bundle = null) {
        if (null === $this->formGenerator) {
            $this->formGenerator = new CustomDoctrineFormGenerator($this->getContainer()->get('filesystem'));
            $this->formGenerator->setSkeletonDirs($this->getSkeletonDirs($bundle));
        }

        return $this->formGenerator;
    }

    public function setFormGenerator(DoctrineFormGenerator $formGenerator) {
        $this->formGenerator = $formGenerator;
    }

}
