<?php

namespace ADIF\ContableBundle\Form;

use ADIF\ContableBundle\Form\Obras\TramoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class LicitacionObraType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('numero', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control integerPositive'))
                )
                ->add('anio', 'text', array(
                    'required' => true,
                    'label' => 'A&ntilde;o',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control nomask novalidate'),
                ))
                ->add('tipoContratacion', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\TipoContratacion',
                    'required' => true,
                    'property' => 'aliasYMonto',
                    'label' => 'Tipo contrataci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $options['entity_manager_compras'],
                    'attr' => array('class' => 'form-control choice')
                ))
                ->add('fechaApertura', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha apertura',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control  datepicker'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('importePliego', null, array(
                    'required' => false,
                    'label' => 'Importe pliego',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control currency'))
                )
                ->add('importeLicitacion', null, array(
                    'required' => true,
                    'label' => 'Importe licitaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control currency'))
                )

                // Archivos adjuntos
                ->add('archivos', 'collection', array(
                    'type' => new LicitacionArchivoType(),
                    'allow_delete' => true,
                    'allow_add' => true,
                    'label' => 'Adjuntos',
                    'prototype_name' => '__adjunto__',
                    'label_attr' => array(
                        'class' => 'hidden'),
                    'attr' => array(
                        'class' => 'hidden')))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\LicitacionObra'
        ));
        $resolver->setRequired('entity_manager_compras');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_licitacionobra';
    }

}
