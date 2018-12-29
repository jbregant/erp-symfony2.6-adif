<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class CotizacionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaInvitacion', 'date', array(
                    'required' => true,
                    'label' => 'Fecha Invitación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaCotizacion', 'date', array(
                    'required' => true,
                    'label' => 'Fecha Cotización',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('requerimiento', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\Requerimiento',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                ->add('proveedor', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\Proveedor',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'])
                )
                // Archivos adjuntos
                ->add('archivos', 'collection', array(
                    'type' => new CotizacionArchivoType(),
                    'allow_delete' => true,
                    'allow_add' => true,
                    'label' => 'Adjuntos',
                    'prototype_name' => '__adjunto__',
                    'label_attr' => array(
                        'class' => 'hidden'),
                    'attr' => array(
                        'class' => 'hidden'))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\Cotizacion'
        ));
        
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_cotizacion';
    }

}
