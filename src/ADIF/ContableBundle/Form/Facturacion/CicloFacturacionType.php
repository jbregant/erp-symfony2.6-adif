<?php

namespace ADIF\ContableBundle\Form\Facturacion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class CicloFacturacionType extends AbstractType {
    
    private $emContable;
    
    public function __construct($emContable = null, $emCompras = null) {       
        $this->emContable = $emContable;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaInicio', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha inicio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker no-editable'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaFin', 'datetime', array(
                    'required' => true,
                    'label' => 'Fecha fin',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control datepicker no-editable'),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('cantidadUnidadTiempo', null, array(
                    'required' => true,
                    'label' => 'Cantidad',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control number no-editable'))
                )
                ->add('importe', null, array(
                    'required' => true,
                    'label' => 'Importe',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control money-format changeable text-right no-editable'))
                )
                ->add('cantidadFacturas', null, array(
                    'required' => true,
                    'label' => 'Cantidad de facturas',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control number no-editable'))
                )
                ->add('cantidadFacturasPendientes', null, array(
                    'required' => false,
                    'label' => 'Cantidad de facturas pendientes',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control number hidden no-editable'))
                )
                ->add('unidadTiempo', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Facturacion\UnidadTiempo',
                    'required' => true,
                    'label' => 'Unidad de tiempo',
                    'em' => $this->emContable,
                    'attr' => array('class' => 'form-control choice no-editable'))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Facturacion\CicloFacturacion'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_facturacion_ciclofacturacion';
    }

}
