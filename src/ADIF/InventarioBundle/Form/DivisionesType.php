<?php
namespace ADIF\InventarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DivisionesType extends AbstractType
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
                            

                // ->add('idEmpresa', null, array(
                //     'required' => true,
                //     'label' => 'Idempresa',                    
                //     'label_attr' => array('class' => 'control-label'),
                //     'attr' => array('class' => ' form-control  number '),                ))
                            

                // ->add('idUsuarioCreacion', null, array(
                //     'required' => false,
                //     'label' => 'Idusuariocreacion',                    
                //     'label_attr' => array('class' => 'control-label'),
                //     'attr' => array('class' => ' form-control  number '),                ))
                            

                // ->add('idUsuarioUltimaModificacion', null, array(
                //     'required' => false,
                //     'label' => 'Idusuarioultimamodificacion',                    
                //     'label_attr' => array('class' => 'control-label'),
                //     'attr' => array('class' => ' form-control  number '),                ))

                ->add('linea','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Linea',
                    'attr' => array('class' => ' form-control choice '), ))
                ->add('operador','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Operador',
                    'attr' => array('class' => ' form-control choice '), ))

                 /* Ramal que se carga por ajax al cambiar el combo de linea */
                ->add('ramal', 'entity', array(
                    'required' => false,
                    'class' => 'ADIF\InventarioBundle\Entity\Ramal',
                    'empty_value' => '-- Ramal --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '), ))
                ;

    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\InventarioBundle\Entity\Divisiones'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_divisiones';
    }
}
