<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class CuentaPresupuestariaEconomicaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('codigo', null, array(
                    'required' => true,
                    'label' => 'Código',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Denominación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('descripcion', null, array(
                    'required' => false,
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('esImputable', null, array(
                    'required' => false,
                    'label' => 'Es imputable',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('cuentaPresupuestariaEconomicaPadre', EntityType::clase, array(
                    'required' => false,
                    'class' => 'ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica',
                    'empty_value' => '-- Cuenta presupuestaria económica  --',
                    'label' => 'Cuenta presupuestaria económica padre',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('categoriaCuentaPresupuestariaEconomica', EntityType::clase, array(
                    'required' => true,
                    'class' => 'ADIF\ContableBundle\Entity\CategoriaCuentaPresupuestariaEconomica',
                    'empty_value' => '-- Categoría --',
                    'label' => 'Categoría',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('suma', null, array(
                    'required' => false,
                    'label' => 'Suma al presupuesto',
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
            'data_class' => 'ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica'
        ));
        $resolver->setRequired('entity_manager_contable');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_cuentapresupuestariaeconomica';
    }

}
