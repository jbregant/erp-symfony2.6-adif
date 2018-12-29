<?php

namespace ADIF\ComprasBundle\Form;

use ADIF\ComprasBundle\Form\DatosImpositivosType;
use ADIF\RecursosHumanosBundle\Form\DomicilioType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ClienteProveedorType extends AbstractType {
    
    private $em;
    private $emContable;
    private $emRRHH;
    
    public function __construct($em = null, $emContable = null, $emRRHH = null) {
        $this->em = $em;
        $this->emContable = $emContable;
        $this->emRRHH = $emRRHH;
        //parent::__construct();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('codigo', null, array(
                    'required' => true,
                    'read_only' => true,
                    'label' => 'Código',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control')))
                ->add('razonSocial', null, array(
                    'required' => true,
                    'label' => 'Razón social',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control')))
                ->add('CUIT', null, array(
                    'required' => false,
                    'label' => 'CUIT',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')))
                ->add('tipoDocumento', 'choice', array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Tipo documento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice'),
                    'choices' => array('dni' => 'DNI'),
                ))
                ->add('DNI', null, array(
                    'required' => false,
                    'label' => 'DNI',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')))
                ->add('codigoIdentificacion', null, array(
                    'required' => false,
                    'label' => 'CDI',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')))
                ->add('esExtranjero', null, array(
                    'required' => false,
                    'label' => 'Extranjero',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')))
                ->add('actividades', EntityType::clase, array(
                    'required' => false,
                    'multiple' => true,
                    'class' => 'ADIF\ComprasBundle\Entity\TipoActividad',
                    'label' => 'Actividades',
                    'label_attr' => array('class' => 'control-label'),  
                    'attr' => array('class' => ' form-control choice'),
                    'em' => $this->em
                    )
                )
                ->add('datosContacto', 'collection', array(
                    'type' => new DatoContactoType($this->em, $this->emContable),
                    'label' => 'Información de Contacto',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__dato_contacto_cliente_proveedor__')
                )
                ->add('domicilioComercial', new DomicilioType($this->emRRHH), array(
                    'required' => true,
                    'label' => 'Domicilio comercial')
                )
                ->add('domicilioLegal', new DomicilioType($this->emRRHH), array(
                    'required' => true,
                    'label' => 'Domicilio legal')
                )
                ->add('archivos', 'collection', array(
                    'type' => new ClienteProveedorArchivoType(),
                    'allow_delete' => true,
                    'allow_add' => true,
                    'label' => 'Adjuntos',
                    'prototype_name' => '__adjunto__',
                    'label_attr' => array(
                        'class' => 'hidden'),
                    'attr' => array(
                        'class' => 'hidden'))
                )
                ->add('datosImpositivos', new DatosImpositivosType($this->em, $this->emContable),
                        array( 'required' => false)
                        )
                /*
                ->add('datosImpositivos', 'collection', array(
                    'type' => new DatosImpositivosType($this->em, $this->emContable),
                    'label' => 'datos Impositivos',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__DatosImpositivos__',
                    )
                )
                 * 
                 */
                /*
                ->add('datosImpositivos', new DatosImpositivosType($this->em, $this->emContable),
                        array( 'required' => false)
                    )
                 * 
                 */
               
                ;

        //$builder->add('datosImpositivos', new DatosImpositivosType(),);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\ClienteProveedor'
        ));
        /*
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager_contable');      
        
         * 
         */
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_clienteproveedor';
    }

}
