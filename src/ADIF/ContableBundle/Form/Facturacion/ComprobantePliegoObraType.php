<?php

namespace ADIF\ContableBundle\Form\Facturacion;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteVenta;
use ADIF\ContableBundle\Form\Facturacion\RenglonComprobanteVentaType;
use ADIF\ContableBundle\Form\RenglonPercepcionType;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ComprobantePliegoObraType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->add('licitacion', null, array(
                    'required' => false,
                    'label' => 'B&uacute;squeda de licitaci&oacute;n',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('numero_licitacion', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'Licitaci&oacute;n',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('numero_licitacion_busqueda', null, array(
                    'required' => false,
                    'disabled' => false,
                    'label' => 'N&uacute;mero licitaci&oacute;n',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('anio_licitacion_busqueda', null, array(
                    'required' => false,
                    'disabled' => false,
                    'label' => 'A&ntilde;o licitaci&oacute;n',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('idLicitacion', 'hidden', array(
                    'required' => true,
                    'mapped' => false)
                )
                ->add('cliente', null, array(
                    'required' => false,
                    'label' => 'B&uacute;squeda de cliente',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('cliente_razonSocial', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'Cliente',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('cliente_cuit', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'CUIT',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('idCliente', 'hidden', array(
                    'required' => true,
                    'mapped' => false)
                )
                ->add('fechaComprobante', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de comprobante',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaVencimiento', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de vencimiento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('puntoVenta', null, array(
                    'required' => true,
                    'label' => 'P. de venta',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control hidden'))
                )
                ->add('numero', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de comprobante',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('numeroCupon', null, array(
                    'required' => false,
                    'label' => 'N&uacute;mero de cup&oacute;n',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('numeroReferencia', null, array(
                    'required' => false,
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
                    'empty_value' => '-- Letra --',
                    'label' => 'Letra',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice ')/* ,
                          'query_builder' => function(EntityRepository $er) {
                          return $er->createQueryBuilder('l')
                          ->join('ADIFContableBundle:Facturacion\Talonario', 't', Join::WITH, 't.letraComprobante = l')
                          ->distinct()
                          ->orderBy('l.letra', 'ASC');
                          } */)
                )
                ->add('tipoComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoComprobante',
                    'label' => 'Tipo de comprobante',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'],
                    'query_builder' => function(EntityRepository $er) {

                $comprobantesValidos = [
                    ConstanteTipoComprobanteVenta::CUPON,
                    ConstanteTipoComprobanteVenta::FACTURA,
                    ConstanteTipoComprobanteVenta::NOTA_CREDITO,
                    ConstanteTipoComprobanteVenta::NOTA_DEBITO
                ];

                return $er->createQueryBuilder('t')
                        ->where('t.id IN (:nombre)')
                        ->setParameter('nombre', $comprobantesValidos, Connection::PARAM_STR_ARRAY)
                        ->orderBy('t.nombre', 'ASC');
            })
                )
                ->add('renglonesComprobante', 'collection', array(
                    'type' => new RenglonComprobanteVentaType($options['entity_manager']),
                    'allow_add' => true,
                    'allow_delete' => false,
                    'by_reference' => false)
                )
                ->add('renglonesPercepcion', 'collection', array(
                    'type' => new RenglonPercepcionType($options['entity_manager']),
                    'allow_add' => true,
                    'allow_delete' => false,
                    'by_reference' => false)
                )
                ->add('esCuponGarantia', 'checkbox', array(
                    'required' => false,
                    'label' => 'Es de garant&iacute;a',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('tipoCambio', null, array(
                    'required' => true,
                    'label' => 'Tipo cambio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control numberPositive',
                        'data-digits' => '4'
                    ))
                )
                ->add('tipoContratacion', null, array(
                    'required' => false,
                    'mapped' => false,
                    'attr' => array('class' => 'hidden')
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_comprobanteventa';
    }

}
