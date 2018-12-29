<?php

namespace ADIF\ContableBundle\Form\EgresoValor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RenglonComprobanteEgresoValorType extends AbstractType {
    
    private $emContable;
    
    public function __construct($emContable = null) {

        $this->emContable = $emContable;
        //parent::__construct();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('conceptoEgresoValor', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\EgresoValor\ConceptoEgresoValor',
                    'required' => true,
                    'mapped' => true,
                    'label' => 'Concepto',
                    'empty_value' => '-- Concepto --',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $this->emContable,
					'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('c')->orderBy('c.denominacion', 'ASC');
					}
				))
                ->add('descripcion', null, array(
                    'required' => false,
                    'label' => 'Detalle',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('cantidad', null, array(
                    'required' => true,
                    'label' => 'Cantidad',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control numberPositive')))
                ->add('precioUnitario', null, array(
                    'required' => true,
                    'label' => 'Precio unitario',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control numberPositive')))
                ->add('bonificacionTipo', null, array(
                    'required' => true,
                    'label' => 'Tipo bonificaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('bonificacionValor', null, array(
                    'required' => false,
                    'label' => 'Valor bonificaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('montoNeto', null, array(
                    'label' => 'Neto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control numberPositive'),))
                ->add('montoIva', null, array(
                    'label' => 'Monto IVA',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control numberPositive'),))
                ->add('observaciones', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('alicuotaIva', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\AlicuotaIva',
                    'label' => 'IVA',
                    'em' => $this->emContable,
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('ai')->orderBy('ai.valor', 'ASC');
            }
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\EgresoValor\RenglonComprobanteEgresoValor'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_rengloncomprobanteegresovalor';
    }

}
