<?php

namespace ADIF\ContableBundle\Form;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteCompra;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteVenta;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ComprobanteCompraCreateType extends AbstractType {
    private $emCompras;
    private $emContable;
    
    public function __construct($emContable = null, $emCompras = null) {

        $this->emCompras = $emCompras;
        $this->emContable = $emContable;
        //parent::__construct();
    }
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
                ->add('idProveedor', 'hidden', array(
                    'required' => true)
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
                    'attr' => array(
                        'class' => 'form-control currency',
                        'data-digits' => '2',
                        'readonly' => true))
                )
                ->add('montoValidacion', null, array(
                    'required' => true,
                    'label' => 'Total comprobante',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control currency',
                        'data-digits' => '2')
                        )
                )
                ->add('letraComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\LetraComprobante',
                    'label' => 'Letra',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $this->emContable,
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('l')
                        ->orderBy('l.letra', 'ASC');
            })
                )
                ->add('tipoComprobante', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoComprobante',
                    'label' => 'Tipo de comprobante',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $this->emContable,
                    'query_builder' => function(EntityRepository $er) {
                        $comprobantesNoValidos = [
                            ConstanteTipoComprobanteCompra::NOTA_DEBITO_INTERESES,
                            ConstanteTipoComprobanteCompra::CUPON,
                            ConstanteTipoComprobanteVenta::RENDICION_LIQUIDO_PRODUCTO
                        ];

                        return $er->createQueryBuilder('t')
                                ->where('t.id NOT in (:tipoComprobante)')
                                ->setParameter('tipoComprobante', $comprobantesNoValidos)
                                ->orderBy('t.nombre', 'ASC');
                        })
                )
                ->add('fechaVencimiento', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de vencimiento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
            ;

        $builder->add('renglonesComprobante', 'collection', array(
            'type' => new RenglonComprobanteCompraType($this->emCompras, $this->emContable),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ));

        $builder->add('renglonesPercepcion', 'collection', array(
            'type' => new RenglonPercepcionType($this->emContable),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ));

        $builder->add('renglonesImpuesto', 'collection', array(
            'type' => new RenglonImpuestoType($this->emContable),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ));

        $builder->add('adicionales', 'collection', array(
            'type' => new AdicionalComprobanteCompraType($this->emContable),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ));

        $builder->add('idOrdenCompra', null, array(
            'attr' => array('class' => ' hidden '),
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\ComprobanteCompra',
            'validation_groups' => array('create')
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_comprobantecompra';
    }

}
