<?php

namespace ADIF\ContableBundle\Form\EgresoValor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class DevolucionDineroType extends AbstractType {
    
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
                ->add('montoDevolucion', null, array(
                    'required' => true,
                    'label' => 'Devoluci&oacute;n de dinero',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control text-right money-format changeable'))
                )
                ->add('cuenta', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF',
                    'empty_value' => '-- Cuenta bancaria --',
                    'required' => true,
                    'label' => 'Cuenta bancaria',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $this->emRRHH,
                    'attr' => array('class' => 'form-control choice'))
                )
                ->add('numero', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('fechaIngresoADIF', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha ingreso ADIF',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('numeroReferencia', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de referencia otros sistemas',
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
            'data_class' => 'ADIF\ContableBundle\Entity\EgresoValor\DevolucionDinero'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_egresovalor_devoluciondinero';
    }

}
