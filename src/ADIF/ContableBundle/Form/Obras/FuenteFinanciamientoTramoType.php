<?php

namespace ADIF\ContableBundle\Form\Obras;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class FuenteFinanciamientoTramoType extends AbstractType {
    
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
                ->add('fuenteFinanciamiento', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\Obras\FuenteFinanciamiento',
                    'em' => $this->emContable,
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('porcentaje', null, array(
                    'required' => true,
                    'label' => 'Porcentaje',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control percentage porcentaje-fuente-financiamiento'))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Obras\FuenteFinanciamientoTramo'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_obras_fuentefinanciamientotramo';
    }

}
