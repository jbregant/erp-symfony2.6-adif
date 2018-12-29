<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ModeloAsientoContableType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('conceptoAsientoContable', EntityType::clase, array(
                    'label' => 'Concepto',
                    'empty_value' => '-- Elija un concepto --',
                    'class' => 'ADIF\ContableBundle\Entity\ConceptoAsientoContable',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                ->add('denominacionModeloAsientoContable', null, array(
                    'required' => true,
                    'label' => 'Denominación',
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
            'data_class' => 'ADIF\ContableBundle\Entity\ModeloAsientoContable'
        ));
        
        $resolver->setRequired('entity_manager');

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_modeloasientocontable';
    }

}
