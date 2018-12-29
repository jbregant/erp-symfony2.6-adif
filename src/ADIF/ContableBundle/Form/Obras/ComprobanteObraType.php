<?php

namespace ADIF\ContableBundle\Form\Obras;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteObra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteVenta;
use ADIF\ContableBundle\Form\Obras\RenglonComprobanteObraType;
use ADIF\ContableBundle\Form\RenglonImpuestoType;
use ADIF\ContableBundle\Form\RenglonPercepcionType;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ComprobanteObraType extends AbstractType {

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
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('proveedor_razonSocial', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'Proveedor',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('proveedor_cuit', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'CUIT',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('idProveedor', 'hidden', array(
                    'required' => true)
                )
                ->add('fechaComprobante', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de comprobante',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control  datepicker no-editable'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaIngresoADIF', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha ingreso ADIF',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('puntoVenta', null, array(
                    'required' => true,
                    'label' => 'P. de venta',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control input-xsmall no-editable'))
                )
                ->add('numero', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de comprobante',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control input-small no-editable'))
                )
                ->add('numeroReferencia', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de referencia otros sistemas',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control '))
                )
                ->add('observaciones', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control '))
                )
                ->add('total', null, array(
                    'required' => true,
                    'label' => 'Total',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control currency no-editable',
                        'data-digits' => '4',
                        'readonly' => true)
                        )
                )
                ->add('montoValidacion', null, array(
                    'required' => true,
                    'label' => 'Total comprobante',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control currency',
                        'data-digits' => '4')
                        )
                )
                ->add('letraComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\LetraComprobante',
                    'label' => 'Letra',
                    'attr' => array('class' => 'form-control choice no-editable'),
                    'em' => $options['entity_manager'],
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('l')
                        ->orderBy('l.letra', 'ASC');
            })
                )
                ->add('tipoComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoComprobante',
                    'label' => 'Tipo de comprobante',
                    'attr' => array('class' => 'form-control choice no-editable'),
                    'em' => $options['entity_manager'],
                    'query_builder' => function(EntityRepository $er) {

                $comprobantesNoValidos = [
                    ConstanteTipoComprobanteObra::NOTA_DEBITO_INTERESES,
                    ConstanteTipoComprobanteObra::CUPON,
                    ConstanteTipoComprobanteVenta::RENDICION_LIQUIDO_PRODUCTO
                ];

                return $er->createQueryBuilder('t')
                        ->where('t.id NOT IN (:nombre)')
                        ->setParameter('nombre', $comprobantesNoValidos, Connection::PARAM_STR_ARRAY)
                        ->orderBy('t.nombre', 'ASC');
            })
                )
                ->add('documentoFinanciero', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Obras\DocumentoFinanciero',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => 'hidden'))
                )
                ->add('renglonesComprobante', 'collection', array(
                    'type' => new RenglonComprobanteObraType($options['entity_manager'], $options['entity_manager_compras']),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false
                ))
                ->add('fechaVencimiento', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de vencimiento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
        ;

        $builder->add('renglonesPercepcion', 'collection', array(
            'type' => new RenglonPercepcionType($options['entity_manager']),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ));

        $builder->add('renglonesImpuesto', 'collection', array(
            'type' => new RenglonImpuestoType($options['entity_manager']),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Obras\ComprobanteObra'
        ));
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager_compras');

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_comprobanteobra';
    }

}
