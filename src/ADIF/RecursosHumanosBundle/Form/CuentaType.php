<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class CuentaType extends AbstractType {

    private $emRRHH;
    
    public function __construct($emRRHH = null) {

        $this->emRRHH = $emRRHH;
        //parent::__construct();
    }
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
                    'attr' => array('class' => ' form-control '))
                )
                ->add('idTipoCuenta', EntityType::clase, array(
                    'required' => true,
                    'label' => 'Tipo de cuenta',
                    'label_attr' => array('class' => 'control-label'),
                    'empty_value' => '-- Tipo cuenta --',
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\TipoCuenta',
                    'em' => $this->emRRHH,
                    'attr' => array('class' => ' form-control choice '),
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
                    'em' => $this->emRRHH,
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('b')
                        ->orderBy('b.nombre', 'ASC');
            })
                )
                ->add('cargar', 'checkbox', array(
                    'required' => false,
                    'label' => '¿Cargar datos bancarios?',
                    'label_attr' => array('class' => 'control-label'),
                    'data' => false,
                    'mapped' => false)
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaPersona'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_cuenta';
    }

}
