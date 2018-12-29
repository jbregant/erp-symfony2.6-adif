<?php

namespace ADIF\ComprasBundle\Form;

use ADIF\RecursosHumanosBundle\Form\DomicilioType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class OrdenCompraType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaOrdenCompra', 'date', array(
                    'required' => true,
                    'label' => 'Fecha',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control datepicker'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaEntrega', 'date', array(
                    'required' => false,
                    'label' => 'Fecha de entrega',
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy'))
                ->add('numeroCarpeta', null, array(
                    'required' => false,
                    'label' => 'N&ordm; de carpeta',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('proveedor', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\Proveedor',
                    'property' => 'cuitAndRazonSocial',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('tipoPago', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoPago',
                    'required' => false,
                    'label' => 'Tipo de pago',
                    'empty_value' => '-- Tipo pago --',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $options['entity_manager_conta'],
                    'attr' => array('class' => ' form-control choice'))
                )
                ->add('condicionPago', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CondicionPago',
                    'required' => false,
                    'label' => 'Condición de pago',
                    'empty_value' => '-- Condición de pago --',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $options['entity_manager_conta'],
                    'attr' => array('class' => 'form-control choice'))
                )
                ->add('domicilioEntrega', new DomicilioType($options['entity_manager_rrhh']), array(
                    'required' => true,
                    'label' => 'Domicilio comercial')
                )
                ->add('tipoContratacion', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\TipoContratacion',
                    'label' => 'Tipo de contratación',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('observacion', 'textarea', array(
                    'required' => false,
                    'label' => 'Observación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'rows' => '5')
                        )
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\OrdenCompra'
        ));
        
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager_rrhh');
        $resolver->setRequired('entity_manager_conta');
        
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_ordencompra';
    }

}
