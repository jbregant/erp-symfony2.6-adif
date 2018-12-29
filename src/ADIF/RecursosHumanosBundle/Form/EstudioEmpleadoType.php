<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class EstudioEmpleadoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('titulo', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\TituloUniversitario',
                    'em' => $options['entity_manager_rrhh'],
                    'attr' => array('class' => ' form-control choice ')))
                ->add('fechaDesde', 'date', array(
                    'required' => false,
                    'label' => 'Fecha de inicio',
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('fechaHasta', 'date', array(
                    'required' => false,
                    'label' => 'Fecha de finalizaci&oacute;n',
                    'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',))
                ->add('establecimiento', null, array(
                    'required' => true,
                    'label' => 'Establecimiento',
                    'attr' => array('class' => ' form-control '),))
                ->add('idEmpleado', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Empleado',
                    'em' => $options['entity_manager_rrhh'],
                    'attr' => array('class' => ' form-control choice '),)
                )
                ->add('idNivelEstudio', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\NivelEstudio',
                    'em' => $options['entity_manager_rrhh'],
                    'attr' => array('class' => ' form-control choice '),
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\EstudioEmpleado'
        ));
        $resolver->setRequired('entity_manager_rrhh');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_estudioempleado';
    }

}
