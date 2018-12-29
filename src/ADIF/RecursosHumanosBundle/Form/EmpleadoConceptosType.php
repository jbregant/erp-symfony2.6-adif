<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\RecursosHumanosBundle\Repository\ConceptosRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class EmpleadoConceptosType extends AbstractType {

    /**
     * 
     * @param \UEP\InformesBundle\Entity\Obra $obra
     */
    function __construct(\ADIF\RecursosHumanosBundle\Entity\Empleado $empleado) {
        $this->convenio = $empleado->getConvenio();
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('conceptos', EntityType::clase, array(
            'class' => 'ADIF\RecursosHumanosBundle\Entity\Concepto',
            'required' => true,
            'expanded' => true,
            'multiple' => true,
            'label' => 'Conceptos',
            'em' => $options['entity_manager'],
            'attr' => array('class' => 'not-checkbox-transform'),
            'property' => 'nombreCodigo',
            'query_builder' => function(ConceptosRepository $er) {
                return $er->findAllByConvenio($this->convenio);
            }
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Empleado',
            'validation_groups' => array('EmpleadoConcepto'),
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_empleado_conceptos';
    }

}
