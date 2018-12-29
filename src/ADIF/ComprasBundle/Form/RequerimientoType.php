<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RequerimientoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaRequerimiento', 'date', array(
                    'required' => true,
                    'label' => 'Fecha',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('numeroReferencia', null, array(
                    'required' => false,
                    'label' => 'N&uacute;mero de referencia otros sistemas',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('descripcion', null, array(
                    'required' => true,
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')
                ))
                ->add('tipoContratacion', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\TipoContratacion',
                    'required' => false,
                    'label' => 'Tipo Contratación',
                    'label_attr' => array('class' => 'control-label'),
                    'property' => 'aliasYMonto',
                    'empty_value' => '-- Sin tipo de contratación --',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => 'form-control')
                ))
                ->add('justiprecio', null, array(
                    'required' => false,
                    'mapped' => false,
                    'read_only' => true,
                    'data' => '0',
                    'label' => 'Justiprecio Total',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control money-format',
                        'data-digits' => '4'
                    ),
                ))
                ->add('renglonesRequerimiento', 'collection', array(
                    'type' => new RenglonRequerimientoType($options['entity_manager']),
                    'label' => 'Renglones del Requerimiento',
                    'allow_delete' => true,
                    'allow_add' => true,
                    //'em' => $options['entity_manager'],
                    'prototype_name' => '__renglon_requerimiento__')
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\Requerimiento'
        ));
        
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_requerimiento';
    }

}
