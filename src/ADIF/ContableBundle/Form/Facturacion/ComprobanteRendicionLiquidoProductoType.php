<?php

namespace ADIF\ContableBundle\Form\Facturacion;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteVenta;
use ADIF\ContableBundle\Form\RenglonPercepcionType;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ComprobanteRendicionLiquidoProductoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->add('cliente', null, array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'B&uacute;squeda de cliente',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('cliente_razonSocial', null, array(
                    'required' => false,
                    'disabled' => true,
                    'mapped' => false,
                    'label' => 'Cliente',
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
                ->add('strPuntoVenta', null, array(
                    'required' => true,
                    'label' => 'P. de venta',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control '))
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
                    'attr' => array('class' => 'form-control'))
                )
                ->add('total', null, array(
                    'required' => true,
                    'label' => 'Total',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control currency', 'readonly' => true))
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
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('tipoComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoComprobante',
                    'property' => 'nombreReal',
                    'label' => 'Tipo de comprobante',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
						
						$comprobantesValidos = [
							ConstanteTipoComprobanteVenta::RENDICION_LIQUIDO_PRODUCTO
						];

						return $er->createQueryBuilder('t')
								->where('t.id IN (:nombre)')
								->setParameter('nombre', $comprobantesValidos, Connection::PARAM_STR_ARRAY)
								->orderBy('t.nombre', 'ASC');
					})
                )
               
                ->add('renglonesComprobante', 'collection', array(
                    'type' => new RenglonComprobanteRendicionLiquidoProductoType($options['entity_manager']),
                    'allow_add' => true,
                    'allow_delete' => false,
                    'by_reference' => false)
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Facturacion\ComprobanteRendicionLiquidoProducto'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_comprobanterendicionliquidoproducto';
    }

}
