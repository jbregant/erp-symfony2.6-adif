<?php

namespace ADIF\ContableBundle\Form\Obras;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class OrdenPagoObraType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaAutorizacionContable', 'datetime', array(
                    'required' => true,
                    'label' => 'Fechaautorizacioncontable',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('numeroAutorizacionContable', null, array(
                    'required' => true,
                    'label' => 'Numeroautorizacioncontable',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('idProveedor', null, array(
                    'required' => true,
                    'label' => 'Idproveedor',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),))
                ->add('facturaConformada', null, array(
                    'required' => true,
                    'label' => 'Facturaconformada',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('calculosVerificados', null, array(
                    'required' => true,
                    'label' => 'Calculosverificados',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('impuestosVerificados', null, array(
                    'required' => true,
                    'label' => 'Impuestosverificados',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('fechaOrdenPago', 'datetime', array(
                    'required' => true,
                    'label' => 'Fechaordenpago',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('numeroOrdenPago', null, array(
                    'required' => true,
                    'label' => 'Numeroordenpago',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('concepto', null, array(
                    'required' => true,
                    'label' => 'Concepto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('estadoOrdenPago', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\EstadoOrdenPago',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '),
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Obras\OrdenPagoObra'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_ordenpagoobra';
    }

}
