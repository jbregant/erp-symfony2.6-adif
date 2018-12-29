<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {

    public function registerBundles() {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle(),
            new ADIF\BaseBundle\ADIFBaseBundle(),
            new ADIF\CustomGeneratorBundle\ADIFCustomGeneratorBundle(),
            new ADIF\AutenticacionBundle\ADIFAutenticacionBundle(),
            new ADIF\RecursosHumanosBundle\ADIFRecursosHumanosBundle(),
            new ADIF\ContableBundle\ADIFContableBundle(),
            new ADIF\ComprasBundle\ADIFComprasBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new TFox\MpdfPortBundle\TFoxMpdfPortBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new BG\BarcodeBundle\BarcodeBundle(),
            new ADIF\WarehouseBundle\ADIFWarehouseBundle(),
            new ADIF\InventarioBundle\ADIFInventarioBundle(),
            new ADIF\ApiBundle\ADIFApiBundle(),
            new ADIF\PortalProveedoresBundle\ADIFPortalProveedoresBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader) {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

}
