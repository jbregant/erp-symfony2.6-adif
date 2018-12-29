<?php

namespace ADIF\ContableBundle\Form\Consultoria;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Form\RenglonPercepcionType;
use ADIF\ContableBundle\Form\RenglonImpuestoType;
use Doctrine\DBAL\Portability\Connection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ComprobanteConsultoriaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('idContrato', null, array(
                    'mapped' => false,
                    'attr' => array('class' => 'hidden'))
                )
                ->add('consultor', null, array(
                    'required' => false,
                    'label' => 'B&uacute;squeda de consultor',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('consultor_razonSocial', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'Consultor',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('consultor_cuit', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'CUIT',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label '),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('consultor_id', 'hidden', array(
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
                    'attr' => array('class' => 'form-control'))
                )
                ->add('numero', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de comprobante',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
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
                    'attr' => array('class' => ' form-control '))
                )
                ->add('numeroReferencia', null, array(
                    'required' => true,
                    'label' => 'N&uacute;mero de referencia otros sistemas',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('letraComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\LetraComprobante',
                    'label' => 'Letra',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'],
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

                $comprobantesValidos = [
                    ConstanteTipoComprobanteCompra::FACTURA,
                    ConstanteTipoComprobanteCompra::RECIBO,
					ConstanteTipoComprobanteCompra::NOTA_CREDITO,
					ConstanteTipoComprobanteCompra::NOTA_DEBITO
                ];

                return $er->createQueryBuilder('t')
                        ->where('t.id IN (:nombre)')
                        ->setParameter('nombre', $comprobantesValidos, Connection::PARAM_STR_ARRAY)
                        ->orderBy('t.nombre', 'ASC');
            })
                )
                ->add('montoValidacion', null, array(
                    'required' => true,
                    'label' => 'Total comprobante',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency '))
                )
                ->add('renglonesComprobante', 'collection', array(
                    'type' => new RenglonComprobanteConsultoriaType($options['entity_manager']),
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
                        )
                )
                ->add('fechaVencimiento', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de vencimiento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Consultoria\ComprobanteConsultoria'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_comprobanteconsultoria';
    }

}
