<?php

namespace ADIF\ContableBundle\Form\Obras;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentoFinancieroArchivoType extends AbstractType {

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
            'data_class' => 'ADIF\ContableBundle\Entity\Obras\DocumentoFinancieroArchivo'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_obras_documentofinancieroarchivo';
    }

}
