<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AlicuotaIvaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('valor', null, array(
                    'required' => true,
                    'label' => 'Porcentaje',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'))
                )
                ->add('cuentaContableCredito', EntityType::clase, array(
                    'label' => 'Cuenta contable cr&eacute;dito',
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                ->add('cuentaContableDebito', EntityType::clase, array(
                    'label' => 'Cuenta contable d&eacute;bito',
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\AlicuotaIva'
        ));
        
        $resolver->setRequired('entity_manager');

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_alicuotaiva';
    }

}
