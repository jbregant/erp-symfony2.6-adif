<?php
namespace ADIF\InventarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CorredorType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder            

                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Denominacion',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            
                ->add('linea','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Linea',
                    'label' => 'Línea',                    
                    'attr' => array('class' => ' form-control choice '),
                ))               
                ->add('operador','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Operador',
                    'label' => 'Operador',                    
                    'attr' => array('class' => ' form-control choice '),
                ))                
                ->add('division','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Divisiones',
                    'label' => 'División',
                    'required' => false,
                    'attr' => array('class' => ' form-control choice '),
                ))        ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\InventarioBundle\Entity\Corredor'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_corredor';
    }
}
