<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class SolicitudCompraType extends AbstractType {

    /**
     *
     * @var type 
     */
    private $securityContext;

    /**
     * 
     * @param \ADIF\ComprasBundle\Form\SecurityContext $securityContext
     */
    public function __construct(SecurityContext $securityContext) {
        $this->securityContext = $securityContext;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaSolicitud', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de solicitud',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('numeroReferencia', null, array(
                    'required' => false,
                    'label' => 'N&uacute;mero de referencia otros sistemas',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('descripcion', 'text', array(
                    'required' => true,
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')
                ))
                ->add('tipoSolicitudCompra', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\TipoSolicitudCompra',
                    'label' => 'Tipo solicitud',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $options['entity_manager'],
                    //'empty_value' => '-- Tipo Solicitud --',
                    'attr' => array('class' => ' form-control choice ')
                ))
                ->add('renglonesSolicitudCompra', 'collection', array(
                    'type' => new RenglonSolicitudCompraType($this->securityContext, $options['entity_manager']),
                    'label' => 'Renglones de la Solicitud',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__renglon_solicitud__')
                )
                ->add('justiprecio', null, array(
                    'required' => false,
                    'mapped' => false,
                    'read_only' => true,
                    'data' => '0',
                    'label' => 'Justiprecio total',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control money-format',
                        'data-digits' => '4'
                    ),
                ))
                ->add('justificacion', new JustificacionSolicitudCompraType(), array(
                    'required' => false)
                )
                ->add('observacion', 'text', array(
                    'required' => false,
                    'read_only' => true,
                    'label' => 'Observación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\SolicitudCompra'
        ));
        
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_solicitudcompra';
    }

}
