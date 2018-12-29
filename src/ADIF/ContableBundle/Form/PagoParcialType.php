<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PagoParcialType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('proveedor', null, array(
                    'required' => false,
                    'label' => 'B&uacute;squeda de proveedor',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('proveedor_razonSocial', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'Razón social',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('proveedor_cuit', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'CUIT',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('idProveedor', 'hidden', array(
                    'required' => true)
                )
                ->add('fechaPago', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha pago',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('importe', null, array(
                    'required' => true,
                    'label' => 'Importe',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('observaciones', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\PagoParcial'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_pagoparcial';
    }

}
