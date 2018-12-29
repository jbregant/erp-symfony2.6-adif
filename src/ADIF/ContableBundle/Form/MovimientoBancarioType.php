<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class MovimientoBancarioType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder
                ->add('cuentaOrigen', EntityType::clase, array(
                    'required' => true,
                    'empty_value' => '-- Cuenta bancaria origen --',
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF',
                    //'em' => $options['entity_manager_rrhh'],
                    'attr' => array('class' => ' form-control choice '),
                )) 
                ->add('cuentaDestino', EntityType::clase, array(
                    'required' => true,
                    'empty_value' => '-- Cuenta bancaria destino --',
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF',
                    //'em' => $options['entity_manager_rrhh'],
                    'attr' => array('class' => ' form-control choice '),
                ))                
                ->add('monto', null, array(
                    'required' => true,
                    'label' => 'Monto',
                    'label_attr' => array('class' => 'control-label currency'),
                    'attr' => array('class' => ' form-control '),))
                ->add('detalle', null, array(
                    'required' => false,
                    'label' => 'Detalle',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('numeroReferencia', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de referencia',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('fecha', 'date', array(
                    'required' => true,
                    'label' => 'Fecha',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\MovimientoBancario'
        ));
        
        $resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_movimientobancario';
    }

}
