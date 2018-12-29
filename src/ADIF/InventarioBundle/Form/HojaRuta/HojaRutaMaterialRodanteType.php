<?php
namespace ADIF\InventarioBundle\Form\HojaRuta;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;


class HojaRutaMaterialRodanteType extends HojaRutaType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        parent::buildForm($builder, $options);

        $builder
                ->add('linea','entity', array(
                    'label' => 'Línea',
                    'mapped' => false,
                    'empty_value' => '-- Línea --',
                    'class' => 'ADIF\InventarioBundle\Entity\Linea',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('operador','entity', array(
                    'label' => 'Operador',
                    'mapped' => false,
                    'empty_value' => '-- Operador --',
                    'class' => 'ADIF\InventarioBundle\Entity\Operador',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('estacion','entity', array(
                    'label' => 'Estacion',
                    'mapped' => false,
                    'empty_value' => '-- Estacion --',
                    'class' => 'ADIF\InventarioBundle\Entity\Estacion',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('grupoRodante','entity', array(
                    'label' => 'Grupo Rodante',
                    'mapped' => false,
                    'empty_value' => '-- Grupo Rodante --',
                    'class' => 'ADIF\InventarioBundle\Entity\GrupoRodante',
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('tipoRodante','entity', array(
                    'label' => 'Tipo Rodante',
                    'mapped' => false,
                    'empty_value' => '-- Tipo Rodante --',
                    'class' => 'ADIF\InventarioBundle\Entity\TipoRodante',
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('numeroVehiculo','entity', array(
                    'label' => 'Número de Vehículo',
                    'mapped' => false,
                    'empty_value' => '-- Número de Vehículo --',
                    'property' => 'numeroVehiculo',
                    'class' => 'ADIF\InventarioBundle\Entity\CatalogoMaterialesRodantes',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('itemsHojaRutaMaterialRodante', 'collection', array(
                    'type' => new ItemHojaRutaMaterialRodanteCollectionType($this->entityManager),
                    'label' => 'Materiales Rodantes',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'by_reference' => false,
                    'prototype_name' => '__item_hoja_ruta__',
                ));
    }
    // /**
    // * @param OptionsResolverInterface $resolver
    // */
    // public function setDefaultOptions(OptionsResolverInterface $resolver) {
    //     $resolver->setDefaults(array(
    //     'data_class' => 'ADIF\InventarioBundle\Entity\ItemHojaRutaMaterialRodante'
    //     ));
    // }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_hojarutamaterialrodante';
    }
}
