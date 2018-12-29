<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RegimenPercepcionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Denominaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('descripcion', null, array(
                    'required' => true,
                    'label' => 'Descripci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('alicuota', null, array(
                    'required' => true,
                    'label' => 'Alicuota',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control numberPositive'),))
               ->add('conceptoPercepcion', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\ConceptoPercepcion',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager']));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\RegimenPercepcion'
        ));
        
        $resolver->setRequired('entity_manager');

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_regimenpercepcion';
    }

}
