<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\RecursosHumanosBundle\Form\DomicilioType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class BeneficiarioLiquidacionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('razonSocial', null, array(
                    'required' => true,
                    'label' => 'Raz&oacute;n social',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('CUIT', null, array(
                    'required' => true,
                    'label' => 'CUIT',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')))
                ->add('domicilio', new DomicilioType($options['entity_manager_rrhh']), array(
                    'required' => true,
                    'label' => 'Domicilio',
                    'label_attr' => array('class' => 'control-label')))
                ->add('cuentasContables', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'required' => true,
                    'label' => 'Cuentas contables',
                    'multiple' => true,
                    'error_bubbling' => true,
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice ',
                        'placeholder' => 'Seleccione las cuentas contables.'),
                ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\BeneficiarioLiquidacion'
        ));
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_beneficiarioliquidacion';
    }

}
