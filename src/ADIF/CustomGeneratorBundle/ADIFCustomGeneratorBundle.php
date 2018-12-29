<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ADIF\CustomGeneratorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Application;
//use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\Filesystem\Filesystem;
use ADIF\CustomGeneratorBundle\Generator\DoctrineCrudGenerator;

/**
 * Description of ADIFCustomGeneratorBundle
 *
 * @author eprimost
 */
class ADIFCustomGeneratorBundle extends Bundle {

    public function getParent() {
        return 'SensioGeneratorBundle';
    }
    
//    public function registerCommands(Application $application) {
//        
//        $crudCommand = $application->get('generate:doctrine:crud');
//        $generator = new DoctrineCrudGenerator(new Filesystem(), __DIR__.'/Resources/skeleton/crud');
//        $crudCommand->setGenerator($generator);
//
//        parent::registerCommands($application);
//    }

}