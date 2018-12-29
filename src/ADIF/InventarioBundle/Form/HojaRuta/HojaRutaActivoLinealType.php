<?php
namespace ADIF\InventarioBundle\Form\HojaRuta;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;


class HojaRutaActivoLinealType extends HojaRutaType
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
                ->add('division','entity', array(
                    'label' => 'División',
                    'mapped' => false,
                    'empty_value' => '-- División --',
                    'class' => 'ADIF\InventarioBundle\Entity\Divisiones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('tipoActivo','entity', array(
                    'label' => 'Tipo de Activo',
                    'mapped' => false,
                    'empty_value' => '-- Tipo de Activo --',
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                    'class' => 'ADIF\InventarioBundle\Entity\TipoActivo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('progresivaInicioTramo', 'choice', array(
                    'mapped' => false,
                    'property_path' => null,
                    'required' => false,
                    'label' => 'Progresiva Inicio Tramo',
                    'empty_value' => '-- Progresiva Inicio Tramo --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('progresivaFinalTramo', 'choice', array(
                    'mapped' => false,
                    'property_path' => null,
                    'required' => false,
                    'label' => 'Progresiva Final Tramo',
                    'empty_value' => '-- Progresiva Final Tramo --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('itemsHojaRutaActivoLineal', 'collection', array(
                    'type' => new ItemHojaRutaActivoLinealCollectionType($this->entityManager),
                    'label' => 'Activos Lineales',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'by_reference' => false,
                    'prototype_name' => '__item_hoja_ruta__',
                ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_hojaruta_activolineal';
    }
}
