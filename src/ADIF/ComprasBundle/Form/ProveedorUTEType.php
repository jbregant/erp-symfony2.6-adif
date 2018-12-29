<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProveedorUTEType extends AbstractType {
        
    private $em;
    
    public function __construct($em = null) {
        $this->em = $em;
        //parent::__construct();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('proveedor', EntityType::clase, array(
                    'label' => 'Proveedor',
                    'class' => 'ADIF\ComprasBundle\Entity\Proveedor',
                    'property' => 'cuitAndRazonSocial',
                    'empty_value' => '-- Proveedor --',
                    'em' => $this->em,
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('porcentajeRemuneracion', null, array(
                    'required' => true,
                    'label' => 'Pct. incidencia en remuneraciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control percentage porcentaje-remuneracion'))
                )
                ->add('porcentajeGanancia', null, array(
                    'required' => true,
                    'label' => 'Pct. incidencia en la ganancia',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control percentage porcentaje-ganancia') )
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\ProveedorUTE'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_proveedorute';
    }

}
