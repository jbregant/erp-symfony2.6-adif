<?php
namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class TopeConceptoGananciaType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder            

                ->add('mes', null, array(
                    'required' => false,
                    'label' => 'Mes',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                
				))
                ->add('valorTope', null, array(
                    'required' => true,
                    'label' => 'Valor tope',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency', 'data-digits' => '2'),                
				))
                ->add('esPorcentaje', null, array(
                    'required' => false,
                    'label' => 'Es porcentaje',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                
				))
                ->add('esValorAnual', null, array(
                    'required' => false,
                    'label' => 'Es valor anual',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                
				))
                ->add('vigente', null, array(
                    'required' => false,
                    'label' => 'Vigente',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                
				))
                ->add('fechaDesde', 'date', array(
                    'required' => false,
                    'label' => 'Fecha desde',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),                        
					'widget' => 'single_text',
					'format' => 'dd/MM/yyyy',                
				))
                ->add('fechaHasta', 'date', array(
                    'required' => false,
                    'label' => 'Fecha hasta',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),                        
					'widget' => 'single_text',
					'format' => 'dd/MM/yyyy',                
				))
				->add('rangoRemuneracion','entity', array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\RangoRemuneracion',
                    'attr' => array('class' => ' form-control choice '), 
					'empty_value' => '-- Indefinido --',
					'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('r')
								->where('r.vigente = TRUE');
					}
				))                
				->add('conceptoGanancia','entity', array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia',
                    'attr' => array('class' => ' form-control choice '),
					'empty_value' => '-- Indefinido --',
					'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('c')
								->orderBy('c.denominacion', 'ASC');
					}
                ));
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\RecursosHumanosBundle\Entity\TopeConceptoGanancia'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_recursoshumanosbundle_topeconceptoganancia';
    }
}
