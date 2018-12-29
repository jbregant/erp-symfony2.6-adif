<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class CuentaContableType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                /* Cuenta Padre */
                ->add('cuentaContablePadre', EntityType::clase, array(
                    'required' => false,
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'label' => 'Cuenta contable padre',
                    'empty_value' => '-- Cuenta contable --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                /* Cuenta Ecónomica */
                ->add('cuentaPresupuestariaEconomica', EntityType::clase, array(
                    'required' => true,
                    'class' => 'ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica',
                    'label' => 'Cuenta presupuestaria económica',
                    'empty_value' => '-- Cuenta presupuestaria económica --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                /* Cuenta Presupuestaria Objeto del Gasto */
                ->add('cuentaPresupuestariaObjetoGasto', EntityType::clase, array(
                    'required' => false,
                    'class' => 'ADIF\ContableBundle\Entity\CuentaPresupuestariaObjetoGasto',
                    'label' => 'Cuenta presupuestaria',
                    'empty_value' => '-- Cuenta presupuestaria del gasto --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                /* Código */
                ->add('codigoCuentaContable', null, array(
                    'required' => true,
                    'label' => 'Código',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                /* Denominación */
                ->add('denominacionCuentaContable', null, array(
                    'required' => true,
                    'label' => 'Denominación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                /* Es Imputable */
                ->add('esImputable', null, array(
                    'required' => false,
                    'label' => 'Es imputable',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                /* Tipo Moneda */
                ->add('tipoMoneda', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoMoneda',
                    'label' => 'Moneda',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                /* Estado */
                ->add('estadoCuentaContable', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\EstadoCuentaContable',
                    'label' => 'Estado',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                /* Descripción */
                ->add('descripcionCuentaContable', null, array(
                    'required' => false,
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('activa', null, array(
                    'required' => false,
                    'label' => 'Activa',
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
            'data_class' => 'ADIF\ContableBundle\Entity\CuentaContable'
        ));
        
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_cuentacontable';
    }

}
