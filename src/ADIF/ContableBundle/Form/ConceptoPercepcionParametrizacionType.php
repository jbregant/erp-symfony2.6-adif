<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ConceptoPercepcionParametrizacionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('conceptoPercepcion', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\ConceptoPercepcion',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                ->add('jurisdiccion', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Jurisdiccion',
                    'required' => false,
                    'empty_value' => '-- Jurisdicción --',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                ->add('cuentaContableCredito', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                ->add('cuentaContableDebito', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager']
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\ConceptoPercepcionParametrizacion'
        ));
        
        $resolver->setRequired('entity_manager');

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_conceptopercepcionparametrizacion';
    }

}
