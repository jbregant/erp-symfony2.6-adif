<?php

namespace ADIF\ContableBundle\Form\Facturacion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class DevolucionGarantiaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('cliente', null, array(
                    'required' => false,
                    'label' => 'B&uacute;squeda de cliente',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control no-editable'))
                )
                ->add('cliente_razonSocial', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'Cliente',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('cliente_cuit', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'CUIT',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('idCliente', 'hidden', array(
                    'mapped' => false,
                    'required' => true)
                )
                ->add('fechaDevolucion', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de devolución',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('cuponGarantia', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Facturacion\CuponVenta',
                    'property' => 'numeroCupon',
                    'empty_value' => '-- Cupon --',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice hidden'))
                )
                ->add('importe', null, array(
                    'required' => true,
                    'label' => 'Importe',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Facturacion\DevolucionGarantia'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_facturacion_devoluciongarantia';
    }

}
