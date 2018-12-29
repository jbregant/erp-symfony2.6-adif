<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EjercicioContableType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('denominacionEjercicio', null, array(
                    'required' => true,
                    'label' => 'Denominación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control integerPositive'))
                )
                ->add('fechaInicio', 'date', array(
                    'required' => true,
                    'label' => 'Fecha inicio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('fechaFin', 'date', array(
                    'required' => true,
                    'label' => 'Fecha fin',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('estaCerrado', null, array(
                    'required' => false,
                    'label' => 'Cerrado',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control estado-ejercicio'))
                )
                ->add('periodoEneroHabilitado', null, array(
                    'required' => false,
                    'label' => 'Enero',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'data-mes' => '01')
                        )
                )
                ->add('periodoFebreroHabilitado', null, array(
                    'required' => false,
                    'label' => 'Febrero',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'data-mes' => '02')
                        )
                )
                ->add('periodoMarzoHabilitado', null, array(
                    'required' => false,
                    'label' => 'Marzo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'data-mes' => '03')
                        )
                )
                ->add('periodoAbrilHabilitado', null, array(
                    'required' => false,
                    'label' => 'Abril',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'data-mes' => '04')
                        )
                )
                ->add('periodoMayoHabilitado', null, array(
                    'required' => false,
                    'label' => 'Mayo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'data-mes' => '05')
                        )
                )
                ->add('periodoJunioHabilitado', null, array(
                    'required' => false,
                    'label' => 'Junio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'data-mes' => '06')
                        )
                )
                ->add('periodoJulioHabilitado', null, array(
                    'required' => false,
                    'label' => 'Julio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'data-mes' => '07')
                        )
                )
                ->add('periodoAgostoHabilitado', null, array(
                    'required' => false,
                    'label' => 'Agosto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'data-mes' => '08')
                        )
                )
                ->add('periodoSeptiembreHabilitado', null, array(
                    'required' => false,
                    'label' => 'Septiembre',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'data-mes' => '09')
                        )
                )
                ->add('periodoOctubreHabilitado', null, array(
                    'required' => false,
                    'label' => 'Octubre',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'data-mes' => '10')
                        )
                )
                ->add('periodoNoviembreHabilitado', null, array(
                    'required' => false,
                    'label' => 'Noviembre',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'data-mes' => '11')
                        )
                )
                ->add('periodoDiciembreHabilitado', null, array(
                    'required' => false,
                    'label' => 'Diciembre',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ',
                        'data-mes' => '12')
                        )
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\EjercicioContable'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_ejerciciocontable';
    }

}
