<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

$loader->add('NumerosALetras_', __DIR__.'/../lib');

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
