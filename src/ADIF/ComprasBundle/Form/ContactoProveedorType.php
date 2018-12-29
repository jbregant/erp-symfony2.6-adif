<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ContactoProveedorType extends AbstractType {

    private $em;
    private $emContable;
    
    public function __construct($em = null, $emContable = null) {
        $this->em = $em;
        $this->emContable = $emContable;
        //parent::__construct();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('proveedor', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\Proveedor',
                    'em' => $this->em,
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('nombre', null, array(
                    'required' => true,
                    'label' => 'Nombre',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('cargo', null, array(
                    'required' => false,
                    'label' => 'Cargo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('datosContacto', 'collection', array(
                    'type' => new DatoContactoType($this->em, $this->emContable),
                    'label' => 'Información de contacto',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__dato_contacto__')
                )
                ->add('observacion', 'text', array(
                    'required' => false,
                    'label' => 'Observación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\ContactoProveedor'
        ));
        //$resolver->setRequired('entity_manager');

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_contactoproveedor';
    }

}
