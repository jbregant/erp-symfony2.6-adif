<?php

namespace ADIF\ContableBundle\Form\Facturacion;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PuntoVentaClaseContratoType extends AbstractType {
    
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
                ->add('montoMinimo', null, array(
                    'required' => true,
                    'label' => 'Monto m&iacute;nimo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control numberPositive montoMinimo'),))
                ->add('montoMaximo', null, array(
                    'required' => true,
                    'label' => 'Monto m&aacute;ximo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control numberPositive montoMaximo'),))                
                ->add('claseContrato', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Facturacion\ClaseContrato',
                    'attr' => array('class' => 'form-control choice claseContrato'),
                    'em' => $this->emContable
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Facturacion\PuntoVentaClaseContrato'
        ));
        
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_facturacion_puntoventaclasecontrato';
    }

}
