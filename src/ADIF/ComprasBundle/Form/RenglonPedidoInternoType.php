<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RenglonPedidoInternoType extends AbstractType {
    
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
                ->add('rubro', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\Rubro',
                    'required' => true,
                    'label' => 'Rubro',
                    'empty_value' => '-- Rubro --',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $this->em,
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('bienEconomico', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\BienEconomico',
                    'required' => true,
                    'label' => 'Bien económico',
                    'empty_value' => '-- Bien económico --',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $this->em,
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('descripcion', null, array(
                    'required' => false,
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('unidadMedida', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\UnidadMedida',
                    'required' => true,
                    'label' => 'Unidad',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $this->em,
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('cantidadSolicitada', null, array(
                    'required' => true,
                    'label' => 'Cantidad',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control changeable currency-format'),
                ))
                ->add('prioridad', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\Prioridad',
                    'required' => true,
                    'label' => 'Prioridad',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $this->em,
                    'attr' => array('class' => ' form-control choice '))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\RenglonPedidoInterno'
        ));
        
        //$resolver->setRequired('entity_manager');

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_renglonpedidointerno';
    }

}
