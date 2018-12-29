<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class AsientoContableType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaContable', 'date', array(
                    'required' => true,
                    'label' => 'Fecha contable',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('denominacionAsientoContable', null, array(
                    'required' => true,
                    'label' => 'Título',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('conceptoAsientoContable', EntityType::clase, array(
                    'label' => 'Concepto',
                    'empty_value' => '-- Elija un concepto --',
                    'class' => 'ADIF\ContableBundle\Entity\ConceptoAsientoContable',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                ->add('modeloAsientoContable', EntityType::clase, array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Modelo de asiento',
                    'empty_value' => '-- Elija un modelo --',
                    'class' => 'ADIF\ContableBundle\Entity\ModeloAsientoContable',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                ->add('razonSocial', null, array(
                    'required' => false,
                    'label' => 'Raz&oacute;n social',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('numeroDocumento', null, array(
                    'required' => false,
                    'label' => 'N&deg; documento',
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
            'data_class' => 'ADIF\ContableBundle\Entity\AsientoContable'
        ));
        
        $resolver->setRequired('entity_manager');
        
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_asientocontable';
    }

}
