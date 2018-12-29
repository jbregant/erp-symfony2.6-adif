<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TituloUniversitarioType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder            

                ->add('nombre', null, array(
                    'required' => true,
                    'label' => 'Nombre',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                        ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\RecursosHumanosBundle\Entity\TituloUniversitario'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_recursoshumanosbundle_titulouniversitario';
    }
}
