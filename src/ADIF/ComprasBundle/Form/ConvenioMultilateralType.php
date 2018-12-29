<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ConvenioMultilateralType extends AbstractType {
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
//                ->add('clienteProveedor', 'entity', array(
//                    'class' => 'ADIF\ComprasBundle\Entity\ClienteProveedor',
//                    'attr' => array('class' => ' form-control choice '))
//                )
                ->add('datosImpositivos', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\DatosImpositivos',
                    'em' => $this->em,
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('jurisdiccion', null, array(
                    'required' => true,
                    'read_only' => true,
                    'data' => 'CABA',
                    'label' => 'Jurisdicción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('porcentajeAplicacionCABA', null, array(
                    'required' => true,
                    'label' => 'Pct. aplicación CABA',
                    'precision' => 5,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'percentage form-control'))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\ConvenioMultilateral'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_conveniomultilateral';
    }

}
