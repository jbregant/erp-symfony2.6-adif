<?php
namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ConceptoGananciaType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder            

                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Denominacion',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('ordenAplicacion', null, array(
                    'required' => false,
                    'label' => 'Orden aplicacion',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))
                            

                ->add('aplicaEnFormulario572', null, array(
                    'required' => false,
                    'label' => 'Aplica en formulario 572',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('esCargaFamiliar', null, array(
                    'required' => false,
                    'label' => 'Es carga familiar',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('codigo572', null, array(
                    'required' => false,
                    'label' => 'Codigo 572',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('indicaSAC', null, array(
                    'required' => false,
                    'label' => 'Indica SAC',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('aplicaGananciaAnual', null, array(
                    'required' => false,
                    'label' => 'Aplica ganancia anual',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('f572Sobreescribe', null, array(
                    'required' => false,
                    'label' => 'F572 sobreescribe',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
                            

                ->add('tieneDetalle', null, array(
                    'required' => false,
                    'label' => 'Tiene detalle',                    
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))
					
				->add('tipoConceptoGanancia','entity', array(
					'required' => true,
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\TipoConceptoGanancia',
                    'attr' => array('class' => ' form-control choice '),
					'empty_value' => '-- Indefinido --',
					'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('t')
								->orderBy('t.denominacion', 'ASC');
					}
                ));
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_recursoshumanosbundle_conceptoganancia';
    }
}
