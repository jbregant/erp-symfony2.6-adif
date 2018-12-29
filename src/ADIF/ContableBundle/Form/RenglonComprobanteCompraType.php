<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RenglonComprobanteCompraType extends AbstractType {

    private $em;
    private $emContable;
    
    public function __construct($em = null, $emContable = null) {
        $this->em = $em;
        $this->emContable = $emContable;
        //parent::__construct();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('idRenglonOrdenCompra', 'hidden', array(
                    'required' => true)
                )
                ->add('descripcion', null, array(
                    'required' => true,
                    'label' => 'Detalle',
                    'label_attr' => array('class' => 'control-label descripcion'),
                    'attr' => array('class' => ' form-control descripcion'))
                )
                ->add('cantidad', null, array(
                    'required' => true,
                    'label' => 'Cantidad',
                    'label_attr' => array('class' => 'control-label'),
                     'attr' => array(
                        'class' => 'form-control numberPositive',
                        'data-digits' => '2'
                    ))
                )
                ->add('precioUnitario', null, array(
                    'required' => true,
                    'label' => 'Precio unitario',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control numberPositive',
                        'data-digits' => '2'
                    ))
                )
				->add('precioUnitarioOriginal', 'hidden', array(
					'mapped' => false,
                    'required' => false,
                    'attr' => array(
                        'class' => 'form-control numberPositive',
                        'data-digits' => '2'
                    ))
                )
				->add('tipoCambio', 'hidden', array(
					'mapped' => false,
                    'required' => false,
                    'attr' => array(
                        'class' => 'form-control numberPositive',
                        'data-digits' => '2'
                    ))
                )
                ->add('bonificacionTipo', null, array(
                    'required' => true,
                    'label' => 'Tipo bonificaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('bonificacionValor', null, array(
                    'required' => false,
                    'label' => 'Valor bonificaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'data-digits' => '2'
                    ))
                )
                ->add('montoNeto', null, array(
                    'label' => 'Neto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control numberPositive',
                        'data-digits' => '2'
                    ))
                )
                ->add('montoIva', null, array(
                    'label' => 'Monto IVA',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control numberPositive',
                        'data-digits' => '2'
                    ))
                )
                ->add('observaciones', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('alicuotaIva', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\AlicuotaIva',
                    'label' => 'IVA',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $this->emContable,
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('ai')->orderBy('ai.valor', 'ASC');
            })
                )
                ->add('bienEconomico', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\BienEconomico',
                    'required' => true,
                    'mapped' => true,
                    'label' => 'Bien económico',
                    'em' => $this->em,
                    'empty_value' => '-- Bien económico --',
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('esDevolucion', null, array(
                    'required' => false,
                    'label' => 'Devoluci&oacute;n',
                    'label_attr' => array('class' => 'control-label devolucion'),
                    'attr' => array('class' => 'form-control not-checkbox-transform devolucion'))
                )
                ->add('modificaCantidad', null, array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Mod. cant.',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control not-checkbox-transform'))
                )
                ->add('renglonAcreditado', 'hidden', array(
                    'required' => false)
                )
                ->add('comprobanteCompra', null, array(
                    'required' => false,
                    'mapped' => false)
                )
                ->add('idComprobante', 'hidden', array(
                    'required' => false,
                    'mapped' => false)
                )
                ->add('idRenglonComprobante', 'hidden', array(
                    'required' => false,
                    'mapped' => false)
                )
        ;

        $builder->add('renglonComprobanteCompraCentrosDeCosto', 'collection', array(
            'type' => new RenglonComprobanteCompraCentrosDeCostoType( $this->em, $this->emContable),
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
            'data_class' => 'ADIF\ContableBundle\Entity\RenglonComprobanteCompra'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_rengloncomprobantecompra';
    }

}
