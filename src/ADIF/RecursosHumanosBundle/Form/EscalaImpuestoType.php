<?php
namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EscalaImpuestoType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder            

                ->add('mes', null, array(
                    'required' => true,
                    'label' => 'Mes',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))
                            

                ->add('montoDesde', null, array(
                    'required' => true,
                    'label' => 'Monto desde',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency', 'data-digits' => '2'), 
				))
                            
                ->add('montoHasta', null, array(
                    'required' => true,
                    'label' => 'Monto hasta',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency', 'data-digits' => '2'), 
				))

                ->add('montoFijo', null, array(
                    'required' => true,
                    'label' => 'Monto fijo',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency', 'data-digits' => '2'), 
                ))            

                ->add('porcentajeASumar', null, array(
                    'required' => true,
                    'label' => 'Porcentaje a sumar (el valor debe estar dividido por 100)',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency', 'data-digits' => '2'), 
				))
                            

                ->add('vigenciaDesde', 'date', array(
                    'required' => true,
                    'label' => 'Vigencia desde',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),                        'widget' => 'single_text',
                        'format' => 'dd/MM/yyyy',                ))
                            

                ->add('vigenciaHasta', 'date', array(
                    'required' => true,
                    'label' => 'Vigencia hasta',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),                        'widget' => 'single_text',
                        'format' => 'dd/MM/yyyy',                ))
                        ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\RecursosHumanosBundle\Entity\EscalaImpuesto'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_recursoshumanosbundle_escalaimpuesto';
    }
}
