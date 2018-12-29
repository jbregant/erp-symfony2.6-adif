<?php

namespace ADIF\ContableBundle\Form\EgresoValor;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteVenta;
use ADIF\ContableBundle\Form\EgresoValor\DevolucionDineroType;
use ADIF\ContableBundle\Form\RenglonImpuestoType;
use ADIF\ContableBundle\Form\RenglonPercepcionType;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ComprobanteEgresoValorCreateType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('devolucionDinero', new DevolucionDineroType($options['entity_manager-rrhh']), array(
                    'required' => true,
                    'mapped' => false)
                )
                ->add('busquedaProveedor', null, array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'B&uacute;squeda de proveedor',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('razonSocial', null, array(
                    'required' => true,
                    'label' => 'Raz&oacute;n social',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('CUIT', null, array(
                    'required' => true,
                    'label' => 'CUIT',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('fechaComprobante', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de comprobante',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
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
                    'attr' => array('class' => ' form-control input-xsmall '))
                )
                ->add('numero', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de comprobante',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control input-small '))
                )
                ->add('numeroCupon', null, array(
                    'required' => false,
                    'label' => 'N&uacute;mero de cup&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('numeroReferencia', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de referencia otros sistemas',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('observaciones', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('total', null, array(
                    'required' => true,
                    'label' => 'Total',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency ', 'readonly' => true))
                )
                ->add('montoValidacion', null, array(
                    'required' => true,
                    'label' => 'Total comprobante',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency '))
                )
                ->add('letraComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\LetraComprobante',
                    'required' => false,
                    'em' => $options['entity_manager'],
                    'empty_value' => '-- Letra --',
                    'label' => 'Letra',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('l')
                                ->orderBy('l.letra', 'ASC');
                    })
                )
                ->add('tipoComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoComprobante',
                    'label' => 'Tipo de comprobante',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {

                        $comprobantesNoValidos = [
                            ConstanteTipoComprobanteCompra::NOTA_DEBITO_INTERESES,
                            ConstanteTipoComprobanteVenta::RENDICION_LIQUIDO_PRODUCTO
                        ];

                        return $er->createQueryBuilder('t')
                                ->where('t.id NOT IN (:nombre)')
                                ->setParameter('nombre', $comprobantesNoValidos, Connection::PARAM_STR_ARRAY)
                                ->orderBy('t.nombre', 'ASC');
                    })
                )
                ->add('renglonesComprobante', 'collection', array(
                    'type' => new RenglonComprobanteEgresoValorType($options['entity_manager']),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false)
                )
                ->add('renglonesPercepcion', 'collection', array(
                    'type' => new RenglonPercepcionType($options['entity_manager']),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false)
                )
                ->add('renglonesImpuesto', 'collection', array(
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
            'data_class' => 'ADIF\ContableBundle\Entity\EgresoValor\ComprobanteEgresoValor',
			'validation_groups' => array('create')
        ));
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager-rrhh');
        
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_comprobanteegresovalor';
    }

}
