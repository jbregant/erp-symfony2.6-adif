<?php

namespace ADIF\ContableBundle\Form\ConciliacionBancaria;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ConciliacionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaInicio', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha de inicio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('fechaFin', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha de fin',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('fechaCierre', 'datetime', array(
                    'required' => false,
                    'label' => 'Fecha de cierre',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('cuenta', EntityType::clase, array(
                    'required' => true,
                    'empty_value' => '-- Cuenta bancaria --',
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\CuentaBancariaADIF',
                    'em' => $options['entity_manager_rrhh'],
                    'attr' => array('class' => ' form-control choice '),))
                ->add('file', 'file', array(
                    'required' => false,
                    'label' => 'Resumen bancario',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control filestyle'))
                )
                ->add('cargar', 'button', array(
                    'label' => 'Cargar',
                    'attr' => array('class' => 'btn btn-sm blue'),))
                ->add('saldoExtracto', null, array(
                    'required' => true,
                    'label' => 'Saldo',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),)) 
                ->add('fechaExtracto', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha extracto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('tipoCambio', null, array(
                    'required' => true,
                    'label' => 'Tipo de cambio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'),))
                ->add('tipoCambioImportacion', null, array(
                    'required' => false,
                    'label' => 'Tipo de cambio',
                    'mapped' => false,                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'),))                
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\ConciliacionBancaria\Conciliacion'
        ));
        //$resolver->setRequired('entity_manager_conta');
        $resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_conciliacionbancaria_conciliacion';
    }

}
