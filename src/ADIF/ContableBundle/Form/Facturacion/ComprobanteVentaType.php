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

class ComprobanteVentaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
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
                ->add('esCuponGarantia', 'checkbox', array(
                    'required' => false,
                    'label' => 'Es de garant&iacute;a',
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
                    'attr' => array('class' => 'form-control'))
                )
                ->add('total', null, array(
                    'required' => true,
                    'label' => 'Total',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control currency', 'readonly' => true))
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
                ->add('montoValidacion', null, array(
                    'required' => true,
                    'label' => 'Total comprobante',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control currency'))
                )
                ->add('letraComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\LetraComprobante',
                    'required' => false,
                    'empty_value' => '-- Letra --',
                    'label' => 'Letra',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'],/* ,
                          'query_builder' => function(EntityRepository $er) {

                          return $er->createQueryBuilder('l')
                          ->join('ADIFContableBundle:Facturacion\Talonario', 't', Join::WITH, 't.letraComprobante = l')
                          ->distinct()
                          ->orderBy('l.letra', 'ASC');
                          } */)
                )
                ->add('tipoComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoComprobante',
                    'property' => 'nombreReal',
                    'label' => 'Tipo de comprobante',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'],
                    'query_builder' => function(EntityRepository $er) {
                $comprobantesValidos = [
                    ConstanteTipoComprobanteVenta::FACTURA,
                    ConstanteTipoComprobanteVenta::NOTA_CREDITO,
                    ConstanteTipoComprobanteVenta::NOTA_DEBITO,
                    ConstanteTipoComprobanteVenta::NOTA_DEBITO_INTERESES,
                    ConstanteTipoComprobanteVenta::CUPON
                ];

                return $er->createQueryBuilder('t')
                        ->where('t.id IN (:nombre)')
                        ->setParameter('nombre', $comprobantesValidos, Connection::PARAM_STR_ARRAY)
                        ->orderBy('t.nombre', 'ASC');
            }
                    )
                )
                ->add('contrato', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Facturacion\ContratoVenta',
                    'attr' => array('class' => 'hidden'),
                    'em' => $options['entity_manager'])
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
                ->add('cancelaCuota', 'checkbox', array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Cancela cuota',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control'))
                )
                ->add('comprobanteCancelado', null, array(
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
            'data_class' => 'ADIF\ContableBundle\Entity\Facturacion\ComprobanteVenta',
            'validation_groups' => array('create')
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
