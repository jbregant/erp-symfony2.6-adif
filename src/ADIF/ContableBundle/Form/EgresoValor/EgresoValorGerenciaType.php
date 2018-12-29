<?php

namespace ADIF\ContableBundle\Form\EgresoValor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class EgresoValorGerenciaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('monto', null, array(
                    'required' => true,
                    'label' => 'Monto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('gerencia', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Gerencia',
                    'empty_value' => '-- Gerencia --',
                    'required' => true,
                    'label' => 'Gerencia',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control '),
                    'em' => $options['entity_manager_rrhh']
                    )
                )
                ->add('tipoEgresoValor', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\EgresoValor\TipoEgresoValor',
                    'attr' => array('class' => 'form-control choice '),
                    'em' => $options['entity_manager'])
                )
                ->add('cuentaContable', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])                
                );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\EgresoValor\EgresoValorGerencia'
        ));
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_egresovalor_egresovalorgerencia';
    }

}
