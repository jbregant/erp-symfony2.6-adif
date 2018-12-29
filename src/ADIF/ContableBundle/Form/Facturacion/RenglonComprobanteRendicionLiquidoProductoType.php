<?php

namespace ADIF\ContableBundle\Form\Facturacion;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RenglonComprobanteRendicionLiquidoProductoType extends AbstractType {
    
    private $emContable;
    
    public function __construct($emContable = null) {

        $this->emContable = $emContable;

    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
               
                ->add('descripcion', null, array(
                    'required' => true,
                    'label' => 'Detalle',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('cantidad', null, array(
                    'required' => true,
                    'label' => 'Cantidad',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control numberPositive'))
                )
                ->add('precioUnitario', null, array(
                    'required' => true,
                    'label' => 'Precio unitario',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'))
                )
                ->add('montoNeto', null, array(
                    'label' => 'Neto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control currency', 'readonly' => 'readonly'))
                )
                ->add('montoIva', null, array(
                    'label' => 'Monto IVA',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control numberPositive', 'readonly' => 'readonly'))
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
                    'em' => $this->emContable,
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('ai')->orderBy('ai.valor', 'ASC');
                    })
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Facturacion\RenglonComprobanteRendicionLiquidoProducto'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_facturacion_rengloncomprobanterendicionliquidoproducto';
    }

}
