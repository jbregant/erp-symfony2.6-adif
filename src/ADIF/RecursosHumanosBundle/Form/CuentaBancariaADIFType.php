<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class CuentaBancariaADIFType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('cbu', null, array(
                    'required' => true,
                    'label' => 'CBU',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('numeroSucursal', null, array(
                    'required' => true,
                    'label' => 'N&ordm; sucursal',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control '))
                )
                ->add('numeroCuenta', null, array(
                    'required' => true,
                    'label' => 'N&ordm; cuenta',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('idTipoCuenta', EntityType::clase, array(
                    'required' => true,
                    'label' => 'Tipo de cuenta',
                    'label_attr' => array('class' => 'control-label'),
                    'empty_value' => '-- Tipo cuenta --',
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\TipoCuenta',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager_rrhh'],
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('t')
                        ->orderBy('t.nombre', 'ASC');
            })
                )
                ->add('idBanco', EntityType::clase, array(
                    'required' => true,
                    'label' => 'Banco',
                    'label_attr' => array('class' => 'control-label'),
                    'empty_value' => '-- Banco --',
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Banco',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager_rrhh'],
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('b')
                        ->orderBy('b.nombre', 'ASC');
            })
                )
                ->add('cuentaContable', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'label' => 'Cuenta contable',
                    'empty_value' => '-- Cuenta contable --',
                    'required' => true,
                    //'em' => $options['entity_manager_conta'],
                    'attr' => array(
                        'class' => ' form-control choice ',
                        'placeholder' => '-- Cuenta contable --'
                    ))
                )
                ->add('tipoMoneda', EntityType::clase, array(
                    'required' => true,
                    'label' => 'Tipo de moneda',                    
                    'empty_value' => '-- Tipo de moneda --',
                    'class' => 'ADIF\ContableBundle\Entity\TipoMoneda',
                    //'em' => $options['entity_manager_conta'],
                    'attr' => array('class' => ' form-control choice '
                    ))
                )                     
                ->add('estaActiva', null, array(
                    'required' => false,
                    'label' => 'Activa',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF'
        ));
        $resolver->setRequired('entity_manager_conta');
        $resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_cuentabancariaadif';
    }

}
