<?php
namespace ADIF\InventarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AtributoType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder            

                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Denominacion',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                // ->add('idUsuarioCreacion', null, array(
                //     'required' => false,
                //     'label' => 'Idusuariocreacion',                    
                //     'label_attr' => array('class' => 'control-label'),
                //     'attr' => array('class' => ' form-control  number '),                ))
                            

                // ->add('idUsuarioUltimaModificacion', null, array(
                //     'required' => false,
                //     'label' => 'Idusuarioultimamodificacion',                    
                //     'label_attr' => array('class' => 'control-label'),
                //     'attr' => array('class' => ' form-control  number '),                ))
                        ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\InventarioBundle\Entity\Atributo'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_atributo';
    }
}
