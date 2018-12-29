<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ConfiguracionCuentaContableSueldosType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('codigo', 'hidden', array(
                    'required' => true,                    
                    'label' => 'Codigo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('descripcion', null, array(
                    'required' => true,
                    'label' => 'Descripci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ', 'readonly' => true),))
                ->add('cuentaContable', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'label' => 'Cuenta contable',
                    'required' => false,
                    'multiple' => false,
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => ' form-control choice ',
                        'placeholder' => '-- Seleccione la cuenta contable --'),
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\ConfiguracionCuentaContableSueldos'
        ));
        $resolver->setRequired('entity_manager_contable');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_configuracioncuentacontablesueldos';
    }

}
