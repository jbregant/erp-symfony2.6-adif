<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ConceptoPresupuestarioNivelVentasType extends AbstractType {
    
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
                ->add('cuentasContables', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'multiple' => true,
                    'em' => $this->emContable,
                    'attr' => array('class' => ' form-control choice '),
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\ConceptoPresupuestarioNivelVentas'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_conceptopresupuestarionivelventas';
    }

}
