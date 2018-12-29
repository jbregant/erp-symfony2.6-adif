<?php
namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RenglonRetencionLiquidacionType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder            

                ->add('idLiquidacion', null, array(
                    'required' => true,
                    'label' => 'Idliquidacion',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))
                            

                ->add('idConceptoVersion', null, array(
                    'required' => true,
                    'label' => 'Idconceptoversion',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))
                            

                ->add('monto', null, array(
                    'required' => true,
                    'label' => 'Monto',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                                ->add('beneficiarioLiquidacion','entity', array(
                    'class' => 'ADIF\ContableBundle\Entity\BeneficiarioLiquidacion',
                    'attr' => array('class' => ' form-control choice '),
                ))                ->add('cuentaContable','entity', array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'attr' => array('class' => ' form-control choice '),
                ))        ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\ContableBundle\Entity\RenglonRetencionLiquidacion'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_contablebundle_renglonretencionliquidacion';
    }
}
