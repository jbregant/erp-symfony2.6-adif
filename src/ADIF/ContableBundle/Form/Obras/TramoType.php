<?php

namespace ADIF\ContableBundle\Form\Obras;

use ADIF\ContableBundle\Form\Obras\PolizaSeguroObraType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class TramoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('proveedor', null, array(
                    'required' => false,
                    'label' => 'B&uacute;squeda de proveedor',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control no-editable'))
                )
                ->add('proveedor_razonSocial', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'Proveedor',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('proveedor_cuit', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'CUIT',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('idProveedor', 'hidden', array(
                    'required' => true)
                )
                ->add('categoriaObra', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Obras\CategoriaObra',
                    'label' => 'Categoría de obra',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice no-editable'))
                )
                ->add('estadoTramo', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Obras\EstadoTramo',
                    'label' => 'Estado',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice'))
                )
                ->add('tipoObra', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Obras\TipoObra',
                    'label' => 'Tipo de obra',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice no-editable'))
                )
                ->add('totalContrato', null, array(
                    'required' => true,
                    'label' => 'Total contrato',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency no-editable'))
                )
                ->add('saldo', null, array(
                    'required' => true,
                    'read_only' => true,
                    'mapped' => false,
                    'label' => 'Saldo tramo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency no-editable'))
                )
                ->add('plazoDias', null, array(
                    'required' => true,
                    'label' => 'Plazo en días',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control integerPositive no-editable'))
                )
                ->add('porcentajeFondoReparo', null, array(
                    'required' => true,
                    'label' => 'Pct. fondo de reparo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control no-editable'))
                )
                ->add('porcentajeAvanceInicial', null, array(
                    'required' => true,
                    'label' => 'Pct. avance inicial',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control percentage no-editable'))
                )
                ->add('porcentajeAnticipoInicial', null, array(
                    'required' => true,
                    'label' => 'Pct. anticipo inicial',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control percentage no-editable'))
                )
                ->add('fechaFirmaContrato', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha firma contrato',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaRecepcionProvisoria', 'datetime', array(
                    'required' => false,
                    'label' => 'Fecha recepción provisoria',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaRecepcionDefinitiva', 'datetime', array(
                    'required' => false,
                    'label' => 'Fecha recepción definitiva',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('descripcion', null, array(
                    'required' => true,
                    'label' => 'Descripci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('fuentesFinanciamiento', 'collection', array(
                    'type' => new FuenteFinanciamientoTramoType($options['entity_manager']),
                    'label' => 'Fuentes de financiamiento',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__fuente_financiamiento__')
                )
                ->add('polizasSeguro', 'collection', array(
                    'type' => new PolizaSeguroObraType($options['entity_manager']),
                    'label' => 'P&oacute;lizas de seguro',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__poliza__')
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Obras\Tramo'
        ));
        
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_obras_tramo';
    }

}
