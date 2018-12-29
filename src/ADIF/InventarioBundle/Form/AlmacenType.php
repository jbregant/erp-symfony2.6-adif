<?php
namespace ADIF\InventarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AlmacenType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Denominación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('tipo', 'choice', array(
                    'required'  => true,
                    'label' => 'Tipo',
                    'label_attr' => array('class' => 'control-label'),
                    'choices'   => array('A' => 'Almacén',
                                         'B' => 'Buque',
                                         'CA' => 'Centro de Acopio',
                                         'O' => 'Obrador',
                                         'P' => 'Puerto'),

                    'attr' => array('class' => ' form-control ')                 ))

                ->add('numeroDeposito', null, array(
                    'required' => false,
                    'label' => 'Número Depósito',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),        ))

                ->add('provincia', 'entity', array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Provincia',
                    'required' => false,
                    'label' => 'Provincia',
                    'empty_value' => '-- Provincia --',
                    'property' => 'nombre',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice')           ))

                ->add('latitud', null, array(
                    'required' => false,
                    'label' => 'Latitud',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('longitud', null, array(
                    'required' => false,
                    'label' => 'Longitud',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('linea','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Linea',
                    'empty_value' => '-- Linea --',
                    'required' => false,
                    'attr' => array('class' => ' form-control choice '),         ))

                ->add('estacion','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Estacion',
                    'empty_value' => '-- Estacion --',
                    'required' => false,
                    'attr' => array('class' => ' form-control choice '),         ))

                ->add('zonaVia', null, array(
                    'required' => false,
                    'label' => 'Zona Vía',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
            ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\InventarioBundle\Entity\Almacen'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_almacen';
    }
}
