<?php

namespace ADIF\ContableBundle\Form\EgresoValor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ResponsableEgresoValorType extends AbstractType {
    
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
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('tipoDocumento', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\TipoDocumento',
                    'required' => true,
                    'label' => 'Tipo documento',
                    'label_attr' => array('class' => 'control-label'),
                    'em' => $this->emRRHH,
                    'attr' => array('class' => ' form-control  choice '))
                )
                ->add('nroDocumento', null, array(
                    'required' => true,
                    'label' => 'Nro documento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\EgresoValor\ResponsableEgresoValor'
        ));

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_egresovalor_responsableegresovalor';
    }

}
