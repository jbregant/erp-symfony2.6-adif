<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ChequeraType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('cuenta', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF',
                    'empty_value' => '-- Cuenta bancaria --',
                    'required' => true,
                    'label' => 'Cuenta bancaria',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $options['entity_manager_rrhh'],
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('tipoChequera', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoChequera',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager_conta'])
                )
                ->add('responsable', null, array(
                    'required' => true,
                    'label' => 'Responsable',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('numeroSerie', null, array(
                    'required' => true,
                    'label' => 'N&ordm; serie',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('numeroInicial', null, array(
                    'required' => true,
                    'label' => 'N&ordm; inicial',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control  number'))
                )
                ->add('numeroFinal', null, array(
                    'required' => true,
                    'label' => 'N&ordm; final',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Chequera'
        ));
        $resolver->setRequired('entity_manager_conta');
        $resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_chequera';
    }

}
