<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AdicionalComprobanteCompraType extends AbstractType {
    
    private $emContable;
    
    public function __construct($emContable = null) {

        $this->emContable = $emContable;
        //parent::__construct();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('idAdicionalCotizacion', 'hidden', array(
                    'required' => true))
                ->add('tipoAdicional', 'entity', array(
                    'class' => 'ADIF\ComprasBundle\Entity\TipoAdicional',
                    'required' => true,
                    'label' => 'Tipo',
                    'empty_value' => '-- Tipo --',
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('signo', 'choice', array(
                    'choices' => array('+' => 'Suma (+)', '-' => 'Resta (-)',),
                    'required' => true,
                    'label' => 'Signo',
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('tipoValor', 'choice', array(
                    'choices' => array('$' => 'Monto', '%' => 'Porcentaje'),
                    'required' => true,
                    'label' => 'Tipo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice'))
                )
                ->add('valor', null, array(
                    'required' => true,
                    'label' => 'Valor',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control money-format '))
                )
                ->add('montoNeto', null, array(
                    'label' => 'Neto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'),)
                )
                ->add('alicuotaIva', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\AlicuotaIva',
                    'required' => true,
                    'label' => 'Alicuota IVA',
                    'em' => $this->emContable,
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('montoIva', null, array(
                    'label' => 'Monto IVA',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'),)
                )
                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observación',
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
            'data_class' => 'ADIF\ContableBundle\Entity\AdicionalComprobanteCompra'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_adicionalcomprobantecompra';
    }

}
