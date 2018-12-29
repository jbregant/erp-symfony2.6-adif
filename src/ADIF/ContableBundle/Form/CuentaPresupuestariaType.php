<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class CuentaPresupuestariaType extends AbstractType {
    
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
                ->add('montoInicial', null, array(
                    'required' => true,
                    'attr' => array('class' => ' form-control numberPositive'),
                ))
                ->add('cuentaPresupuestariaEconomica', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaPresupuestariaEconomica',
                    'em' => $this->emContable,
                    'attr' => array('class' => ' hidden '),
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\CuentaPresupuestaria'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_cuentapresupuestaria';
    }

}
