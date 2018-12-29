<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdjuntoExencionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('archivo', 'file', array(
                    'required' => false,
                    'label' => 'Adjunto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control filestyle')
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\AdjuntoExencion'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_adjuntoexencion';
    }

}
