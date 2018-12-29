<?php

namespace ADIF\ContableBundle\Form\Obras;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class DocumentoFinancieroType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * 
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('proveedor', null, array(
                    'required' => false,
                    'label' => 'B&uacute;squeda de proveedor',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
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
                ->add('numeroReferencia', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de referencia otros sistemas',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('tipoDocumentoFinanciero', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Obras\TipoDocumentoFinanciero',
                    'label' => 'Tipo de documento',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('t')
                        ->orderBy('t.nombre', 'ASC');
            })
                )
                ->add('tramo', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Obras\Tramo',
                    'empty_value' => '-- Tramo --',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => 'hidden'))
                )
                ->add('fechaDocumentoFinancieroInicio', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha inicio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaDocumentoFinancieroFin', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha fin',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaIngresoADIF', 'datetime', array(
                    'required' => true,
                    'label' => 'Ingreso ADIF',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaRemisionGerenciaAdministracion', 'datetime', array(
                    'required' => false,
                    'label' => 'Remisión Gerencia Admin.',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaIngresoGerenciaAdministracion', 'datetime', array(
                    'required' => false,
                    'label' => 'Ingreso Gerencia Admin.',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('correspondePago', 'checkbox', array(
                    'required' => false,
                    'label' => 'Corresponde pago',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('montoTotalDocumentoFinanciero', null, array(
                    'required' => true,
                    'mapped' => false,
                    'label' => 'Total documento financiero',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'))
                )
                ->add('montoSinIVA', null, array(
                    'required' => true,
                    'label' => 'Monto sin IVA',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'))
                )
                ->add('montoIVA', null, array(
                    'required' => true,
                    'label' => 'Monto IVA',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'))
                )
                ->add('montoPercepciones', null, array(
                    'required' => true,
                    'label' => 'Monto Percepciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'))
                )
                ->add('fechaAprobacionTecnica', 'datetime', array(
                    'required' => false,
                    'label' => 'Aprobación técnica',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('porcentajeCertificacion', null, array(
                    'required' => false,
                    'label' => 'Pct. de certificación',
                    'label_attr' => array('class' => 'control-label'),
                    'data' => 0,
                    'attr' => array('class' => 'form-control percentage'))
                )
                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )

                // AnticipoFinanciero
                ->add('porcentajeAnticipo', null, array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Pct. de anticipo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control percentage'))
                )

                // CertificadoObra - RedeterminacionObra
                ->add('numero', null, array(
                    'required' => false,
                    'label' => 'Número de certificado',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => 'form-control numero-documento-financiero'))
                )

                // CertificadoObra - RedeterminacionObra - AnticipoFinanciero
                ->add('montoFondoReparo', null, array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Monto fondo de reparo',
                    'label_attr' => array('class' => 'control-label currency'),
                    'attr' => array('class' => ' form-control currency'))
                )

                // FondoReparo
                ->add('porcentajeAbonar', null, array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Pct. a abonar',
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => 'form-control percentage'))
                )
                // Polizas
                ->add('polizasSeguro', 'collection', array(
                    'type' => new PolizaSeguroDocumentoFinancieroType($options['entity_manager']),
                    'label' => 'P&oacute;lizas de seguro',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'by_reference' => false,
                    'prototype_name' => '__poliza__')
                )

                // Archivos adjuntos
                ->add('archivos', 'collection', array(
                    'type' => new DocumentoFinancieroArchivoType(),
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
            'data_class' => 'ADIF\ContableBundle\Entity\Obras\DocumentoFinanciero'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_obras_documentofinanciero';
    }

}
