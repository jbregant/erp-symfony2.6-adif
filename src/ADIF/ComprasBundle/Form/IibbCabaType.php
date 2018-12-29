<?php
namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IibbCabaType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder            

                ->add('grupo', null, array(
                    'required' => true,
                    'label' => 'Grupo',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))
                            

                ->add('alicuota', null, array(
                    'required' => true,
                    'label' => 'Alicuota',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('esProveedor', null, array(
                    'required' => true,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                        ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\ComprasBundle\Entity\IibbCaba'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_comprasbundle_iibbcaba';
    }
}
