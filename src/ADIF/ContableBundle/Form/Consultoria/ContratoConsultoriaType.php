<?php

namespace ADIF\ContableBundle\Form\Consultoria;

use ADIF\ContableBundle\Form\Facturacion\CicloFacturacionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ContratoConsultoriaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('consultor', null, array(
                    'required' => false,
                    'label' => 'B&uacute;squeda de consultor',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('consultor_razonSocial', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'Consultor',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('consultor_cuit', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'CUIT',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('idConsultor', 'hidden', array(
                    'required' => true)
                )
                ->add('area', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Area',
                    'empty_value' => '-- &Aacute;rea --',
                    'required' => true,
                    'label' => '&Aacute;rea',
                    'em' => $options['entity_manager_rrhh'],
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice no-editable'))
                )
                ->add('gerencia', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Gerencia',
                    'empty_value' => '-- Gerencia --',
                    'required' => true,
                    'label' => 'Gerencia',
                    'em' => $options['entity_manager_rrhh'],
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice no-editable'))
                )
                ->add('subgerencia', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Subgerencia',
                    'empty_value' => '-- Subgerencia --',
                    'required' => true,
                    'label' => 'Subgerencia',
                    'em' => $options['entity_manager_rrhh'],
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control choice no-editable'))
                )
                ->add('fechaInicio', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de inicio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control datepicker no-editable'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaFin', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de fin',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control datepicker no-editable'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('numeroContrato', null, array(
                    'required' => true,
                    'label' => 'N&ordm; contrato',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('numeroCarpeta', null, array(
                    'required' => false,
                    'label' => 'N&ordm; carpeta',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('importeTotal', null, array(
                    'required' => true,
                    'label' => 'Importe total',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control money-format changeable text-right no-editable'))
                )
                ->add('esHonorarioProfesional', null, array(
                    'required' => false,
                    'label' => 'Honorario profesional',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observacion',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('claseContrato', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Facturacion\ClaseContrato',
                    'label' => 'Clase contrato',
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => 'form-control hidden no-editable'))
                )
                ->add('categoriaContrato', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Facturacion\CategoriaContrato',
                    'label' => 'Tipo contrato',
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => 'form-control hidden no-editable'))
                )
                ->add('estadoContrato', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Facturacion\EstadoContrato',
                    'label' => 'Estado',
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => 'form-control choice'))
                )
                ->add('tipoMoneda', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoMoneda',
                    'label' => 'Tipo moneda',
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => 'form-control choice no-editable'))
                )
                ->add('contratoOrigen', EntityType::clase, array(
                    'required' => false,
                    'class' => 'ADIF\ContableBundle\Entity\Facturacion\Contrato',
                    'empty_value' => '-- Contrato origen --',
                    'label' => 'Contrato',
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => 'form-control hidden no-editable'))
                )
                ->add('ciclosFacturacion', 'collection', array(
                    'type' => new CicloFacturacionType($options['entity_manager_contable']),
                    'label' => 'Ciclos de facturaci&oacute;n',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__ciclo_facturacion__')
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Consultoria\ContratoConsultoria'
        ));
        $resolver->setRequired('entity_manager_contable');
        $resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_consultoria_contratoconsultoria';
    }

}
