<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DeclaracionJuradaIvaContribuyenteType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaInicio', 'date', array(
                    'required' => true,
                    'attr' => array('class' => ' form-control hidden '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy'))
                ->add('fechaInicioDatepicker', null, array(
                    'required' => true,
                    'mapped' => false,
                    'label' => 'Per&iacute;odo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control nomask novalidate'),))
                ->add('montoDebitoFiscal', null, array(
                    'required' => true,
                    'label' => 'D&eacute;bito fiscal',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'),))
                ->add('montoCreditoFiscal', null, array(
                    'required' => true,
                    'label' => 'Cr&eacute;dito fiscal',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'),))
                ->add('montoRetencionesIva', null, array(
                    'required' => true,
                    'label' => 'Retenciones iva',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'),))
                ->add('montoPercepcionesIva', null, array(
                    'required' => true,
                    'label' => 'Percepciones Iva',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'),))
                ->add('montoTotalFacturado', null, array(
                    'required' => true,
                    'label' => 'Total facturado',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'),))
                ->add('montoGravadoFacturado', null, array(
                    'required' => true,
                    'label' => 'Total gravado facturado',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'),))
                ->add('montoIva105', null, array(
                    'required' => true,
                    'label' => 'Iva %10,5',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'),))
                ->add('montoIva21', null, array(
                    'required' => true,
                    'label' => 'Iva %21',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'),))
                ->add('montoIva27', null, array(
                    'required' => true,
                    'label' => 'Iva %27',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency '),))
                ->add('saldo', null, array(
                    'required' => true,
                    'label' => 'Saldo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency hidden', 'readonly' => true),))
                ->add('saldoMostrar', null, array(
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency', 'readonly' => true),));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\DeclaracionJuradaIvaContribuyente'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_declaracionjuradaivacontribuyente';
    }

}
