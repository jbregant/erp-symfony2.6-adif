<?php

namespace ADIF\ContableBundle\Form\Facturacion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\ContableBundle\Form\Facturacion\PuntoVentaClaseContratoType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class PuntoVentaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('numero', null, array(
                    'required' => true,
                    'label' => 'Numero',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('puntosVentaClaseContrato', 'collection', array(
                    'type' => new PuntoVentaClaseContratoType($options['entity_manager']),
                    'label' => 'Tipo de contrato',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__punto_venta_clase_contrato__')
                )
                ->add('generaComprobanteElectronico', null, array(
                    'required' => false,
                    'label' => 'Electrónico',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Facturacion\PuntoVenta'
        ));
        
       $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_facturacion_puntoventa';
    }

}
