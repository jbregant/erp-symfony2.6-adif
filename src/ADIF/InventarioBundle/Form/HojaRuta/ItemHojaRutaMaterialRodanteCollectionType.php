<?php
namespace ADIF\InventarioBundle\Form\HojaRuta;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\InventarioBundle\Form\HojaRuta\DataTransformer\EntityToNumberTransformer;
use Doctrine\ORM\EntityManager;

class ItemHojaRutaMaterialRodanteCollectionType extends AbstractType
{

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('itemRelevado', null, array(
                    'label' => 'Item Relevado',
                ))
                ->add('idEmpresa', null, array(
                    'data' => 1,
                ))
                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observación',
                ))
                ->add('linea','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Linea',
                ))
                ->add('operador','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Operador',
                ))
                ->add('estacion','text', array(
                    'required' => false,
                ))
                ->add('grupoRodante','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\GrupoRodante',
                ))
                ->add('tipoRodante','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\TipoRodante',
                ))
                ->add('tipoActivo','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\TipoActivo',
                    'mapped' => false,
                ))
                ->add('materialRodante','text', array(
                    'required' => false,
                ))
                ->add('numeroVehiculo','text', array(
                    //'class' => 'ADIF\InventarioBundle\Entity\CatalogoMaterialesRodantes',
                    //'property' => 'numeroVehiculo',
                    'mapped' => false,
                ));

                $builder
                    ->get('materialRodante')
                        ->addModelTransformer(new EntityToNumberTransformer($this->entityManager,'CatalogoMaterialesRodantes'));
                $builder
                    ->get('estacion')
                        ->addModelTransformer(new EntityToNumberTransformer($this->entityManager,'Estacion'));

    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\InventarioBundle\Entity\ItemHojaRutaMaterialRodante'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_itemhojaruta_activolineal';
    }
}
