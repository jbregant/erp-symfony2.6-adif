<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\RecursosHumanosBundle\Repository\ConceptoGananciaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ConceptoFormulario572Type extends AbstractType {

    private $emRRHH;
    
    public function __construct($emRRHH = null) {

        $this->emRRHH = $emRRHH;
        //parent::__construct();
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('conceptoGanancia', EntityType::clase, array(
                    'required' => true,
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia',
                    'label' => 'Concepto',
                    'em' => $this->emRRHH,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice conceptoConcepto'),
                    'empty_value' => '-- Concepto --',
                    'query_builder' => function(ConceptoGananciaRepository $er){
                        return $er->findAllConceptosF572();
                    })
                    )
                ->add('monto', null, array(
                    'required' => true,
                    'label' => 'Monto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency montoConcepto inputEditable'),))
                ->add('mesDesde', null, array(
                    'required' => true,
                    'label' => 'Desde',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control mesDesde inputEditable'),))
                ->add('mesHasta', null, array(
                    'required' => true,
                    'label' => 'Hasta',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control mesHasta inputEditable'),))
                ->add('detalleConceptoFormulario572', new DetalleConceptoFormulario572Type())
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\ConceptoFormulario572'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_conceptoformulario572';
    }

}
