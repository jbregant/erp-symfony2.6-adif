<?php

namespace ADIF\RecursosHumanosBundle\Form\Consultoria;

use ADIF\RecursosHumanosBundle\Form\CuentaType;
use ADIF\RecursosHumanosBundle\Form\DomicilioType;
use ADIF\ComprasBundle\Form\CertificadoExencionType;
use ADIF\ComprasBundle\Form\DatosImpositivosType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ConsultorType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('CUIT', null, array(
                    'required' => true,
                    'label' => 'CUIT',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('razonSocial', null, array(
                    'required' => true,
                    'label' => 'Raz&oacute;n social',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('legajo', null, array(
                    'required' => true,
                    'read_only' => true,
                    'label' => 'Legajo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('email', 'email', array(
                    'required' => false,
                    'label' => 'Email',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('telefono', null, array(
                    'required' => false,
                    'label' => 'Tel&eacute;fono',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('domicilioComercial', new DomicilioType($options['entity_manager_rrhh']), array(
                    'required' => true,
                    'label' => 'Domicilio comercial')
                )
                ->add('domicilioFiscal', new DomicilioType($options['entity_manager_rrhh']), array(
                    'required' => true,
                    'label' => 'Domicilio fiscal')
                )
                ->add('datosImpositivos', new DatosImpositivosType($options['entity_manager_compras'], $options['entity_manager_contable']))
                ->add('certificadoExencionIVA', new CertificadoExencionType(), array(
                    'required' => true)
                )
                ->add('certificadoExencionGanancias', new CertificadoExencionType(), array(
                    'required' => true)
                )
                ->add('certificadoExencionIngresosBrutos', new CertificadoExencionType(), array(
                    'required' => true)
                )
                ->add('certificadoExencionSUSS', new CertificadoExencionType(), array(
                    'required' => true)
                )
                ->add('tipoPago', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoPago',
                    'required' => false,
                    'label' => 'Tipo de pago',
                    'empty_value' => '-- Tipo pago --',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => ' form-control choice'))
                )
                ->add('tipoMoneda', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoMoneda',
                    'required' => true,
                    'label' => 'Tipo de moneda',
                    'empty_value' => '-- Tipo moneda --',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => ' form-control choice'))
                )
                ->add('cuenta', new CuentaType($options['entity_manager_rrhh']), array(
                    'required' => false)
                )
                ->add('cuentaContable', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'required' => true,
                    'label' => 'Cuenta contable',
                    'empty_value' => '-- Cuenta contable --',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => ' form-control choice'))
                )
                ->add('pasibleRetencionIVA', null, array(
                    'required' => false,
                    'label' => 'Pasible retención',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('pasibleRetencionGanancias', null, array(
                    'required' => false,
                    'label' => 'Pasible retención',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('pasibleRetencionIngresosBrutos', null, array(
                    'required' => false,
                    'label' => 'Pasible retención',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('pasibleRetencionSUSS', null, array(
                    'required' => false,
                    'label' => 'Pasible retenci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('archivos', 'collection', array(
                    'type' => new ConsultorArchivoType(),
                    'allow_delete' => true,
                    'allow_add' => true,
                    'label' => 'Adjuntos',
                    'prototype_name' => '__adjunto__',
                    'label_attr' => array(
                        'class' => 'hidden'),
                    'attr' => array(
                        'class' => 'hidden'))
                )
                ->add('cais', 'collection', array(
                    'type' => new CodigoAutorizacionImpresionConsultorType(),
                    'label' => 'CAI',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__cai_consultor__')
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Consultoria\Consultor'
        ));
        $resolver->setRequired('entity_manager_compras');
        $resolver->setRequired('entity_manager_contable');
        $resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_consultoria_consultor';
    }

}
