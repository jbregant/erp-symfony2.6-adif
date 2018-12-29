<?php
namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AseguradoraType extends AbstractType
{
    /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder

                ->add('nombre', null, array(
                    'required' => true,
                    'label' => 'Nombre (min. 5 caracteres)',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))


                ->add('detalle', null, array(
                    'required' => true,
                    'label' => 'Detalle (min. 5 caracteres)',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))


                ->add('activo', null, array(
                    'required' => false,
                    'label' => 'Activo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                        ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\ContableBundle\Entity\Aseguradora'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_contablebundle_aseguradora';
    }
}
