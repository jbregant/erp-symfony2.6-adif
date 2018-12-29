<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\RecursosHumanosBundle\Form\ConceptoFormulario572Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class Formulario572Type extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaFormulario', 'date', array(
                    'required' => true,
                    'label' => 'Fecha Última Presentación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('conceptos', 'collection', array(
                    'type' => new ConceptoFormulario572Type($options['entity_manager']),
                    'label' => 'Conceptos del Formulario 572',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__concepto__')
                )
                ->add('anio', 'text', array(
                    'required' => true,
                    'label' => 'Año',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control nomask novalidate'),
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Formulario572'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_formulario572';
    }

}
