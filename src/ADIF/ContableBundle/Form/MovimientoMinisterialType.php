<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class MovimientoMinisterialType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('cuentaBancariaADIF', EntityType::clase, array(
                    'required' => true,
                    'label' => 'Cuenta bancaria ADIF',
                    'empty_value' => '-- Cuenta bancaria ADIF --',
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF',
                    //'em' => $options['entity_manager_rrhh'],
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('conceptoTransaccionMinisterial', EntityType::clase, array(
                    'required' => true,
                    'empty_value' => '-- Cuenta bancaria ADIF --',
                    'class' => 'ADIF\ContableBundle\Entity\ConceptoTransaccionMinisterial',
                    'em' => $options['entity_manager_conta'],
                    'attr' => array('class' => ' form-control choice '),
                ))
//                ->add('esIngreso', 'choice', array(
//                    'required'  => true,
//                    'label' => 'Ingreso/egreso',
//                    'choices'   => array('1' => 'Ingreso', '0' => 'Egreso'),
//                    'attr' => array('class' => ' form-control choice ')
//                ))
                ->add('esIngreso', null, array(
                    'required' => false,
                    'label' => 'Es ingreso',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))                
                ->add('monto', null, array(
                    'required' => true,
                    'label' => 'Monto',
                    'label_attr' => array('class' => 'control-label currency'),
                    'attr' => array('class' => ' form-control currency'),
                ))
                ->add('detalle', null, array(
                    'required' => false,
                    'label' => 'Detalle',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('numeroReferencia', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de referencia',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control number'),))
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
            'data_class' => 'ADIF\ContableBundle\Entity\MovimientoMinisterial'
        ));
        $resolver->setRequired('entity_manager_conta');
        $resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_movimientoministerial';
    }

}
