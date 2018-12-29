<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ParametrosLiquidacionType extends AbstractType {

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
                'attr' => array('class' => ' form-control ', "readonly" => "readonly")))
            ->add('esPorcentaje', null, array(
                'required' => true,
                'label' => 'Es porcentaje',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control ignore '),))
            ->add('valor', null, array(
                'required' => true,
                'label' => 'Valor',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control currency '),))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\ParametrosLiquidacion'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_parametrosliquidacion';
    }

}
