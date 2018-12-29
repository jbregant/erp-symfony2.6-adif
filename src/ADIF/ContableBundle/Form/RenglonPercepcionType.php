<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RenglonPercepcionType extends AbstractType {
    

    private $emContable;
    
    public function __construct($emContable = null) {

        $this->emContable = $emContable;
        //parent::__construct();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('monto', null, array(
                    'required' => true,
                    'label' => 'Monto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control no-editable'))
                )
                ->add('conceptoPercepcion', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\ConceptoPercepcion',
                    'label' => 'Concepto',
                    'em' => $this->emContable,
                    'attr' => array('class' => 'form-control choice no-editable'),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                                ->orderBy('c.denominacion', 'ASC');
                    })
                )
                ->add('jurisdiccion', EntityType::clase, array(
                    'required' => false,
                    'empty_value' => '-- Jurisdiccion --',
                    'class' => 'ADIF\ContableBundle\Entity\Jurisdiccion',
                    'label' => 'Jurisdicci&oacute;n',
                    'em' => $this->emContable,
                    'attr' => array('class' => 'form-control choice no-editable'),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('j')
                                ->orderBy('j.denominacion', 'ASC');
                    })
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\RenglonPercepcion'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_renglonpercepcion';
    }

}
