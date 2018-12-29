<?php
namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ObraSocialType extends AbstractType
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
                    'attr' => array('class' => ' form-control '),
                    ))


                ->add('codigo', null, array(
                    'required' => true,
                    'label' => 'Codigo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),
                    ));
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\RecursosHumanosBundle\Entity\ObraSocial'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_recursoshumanosbundle_obrasocial';
    }
}
