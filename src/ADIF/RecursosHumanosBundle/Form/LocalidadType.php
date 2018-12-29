<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class LocalidadType extends AbstractType {

    private $emRRHH;
    
    public function __construct($emRRHH = null) {
        $this->emRRHH = $emRRHH;
        //parent::__construct();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        
        $builder
                ->add('nombre', null, array(
                    'required' => true,
                    'label' => 'Nombre',
                    'attr' => array('class' => ' form-control '),)
                )
                ->add('provincia', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Provincia',
                    'label' => 'Provincia',                    
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $this->emRRHH,
                ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Localidad'
        ));

        //$resolver->setRequired('entity_manager');
 
        
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_localidad';
    }

}
