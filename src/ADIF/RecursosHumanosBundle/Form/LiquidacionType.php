<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LiquidacionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('numero', null, array(
                'required' => true,
                'label' => 'Numero',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control  number '),
            ))
            ->add('fechaCierreNovedades', 'date', array(
                'required' => true,
                'label' => 'Fecha cierre novedades',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control  datepicker '), 
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            ))
            ->add('idUsuario', null, array(
                'required' => true,
                'label' => 'Idusuario',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control  number '),
            ))
            ->add('fechaAlta', 'datetime', array(
                'required' => true,
                'label' => 'Fechaalta',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control  datepicker '), 
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy'
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Liquidacion'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_liquidacion';
    }

}
