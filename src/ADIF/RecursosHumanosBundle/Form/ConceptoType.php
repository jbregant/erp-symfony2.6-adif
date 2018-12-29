<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ConceptoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('codigo', null, array(
                'required' => true,
                'label' => 'C&oacute;digo',
                'attr' => array('class' => ' form-control ')
            ))
            ->add('idTipoConcepto', EntityType::clase, array(
                'label' => 'Tipo concepto',
                'class' => 'ADIF\RecursosHumanosBundle\Entity\TipoConcepto',
                'em' => $options['entity_manager'],
                'attr' => array('class' => ' form-control choice ')
            ))
            ->add('descripcion', null, array(
                'required' => true,
                'label' => 'Descripci&oacute;n',
                'attr' => array('class' => ' form-control ')
            ))
            ->add('leyenda', null, array(
                'required' => true,
                'label' => 'Leyenda',
                'attr' => array('class' => ' form-control ')
            ))
            ->add('activo', null, array(
                'data' => true,
                'required' => false,
                'label' => 'Activo',
                'attr' => array('class' => ' form-control ignore')
            ))
            ->add('aplicaTope', null, array(
                'required' => false,
                'label' => 'Aplica tope',
                'attr' => array('class' => ' form-control ignore')
            ))
            ->add('integraSac', null, array(
                'required' => false,
                'label' => 'Integra SAC',
                'attr' => array('class' => ' form-control ignore')
            ))
            ->add('integraIg', null, array(
                'required' => false,
                'label' => 'Integra IG',
                'attr' => array('class' => ' form-control ignore')
            ))
            ->add('esNovedad', null, array(
                'required' => false,
                'label' => 'Es novedad',
                'attr' => array('class' => ' form-control ignore')
            ))
            ->add('imprimeRecibo', null, array(
                'required' => false,                
                'label' => 'Imprime recibo',
                'attr' => array('class' => ' form-control ignore')))
            ->add('imprimeLey', null, array(
                'required' => false,
                'label' => 'Imprime ley',
                'attr' => array('class' => ' form-control ignore')))
            ->add('esPorcentaje', null, array(
                'required' => false,
                'label' => 'Es porcentaje',
                'attr' => array('class' => ' form-control ignore')))
            ->add('valor', null, array(
                'required' => false,
                'label' => 'Valor',
                'attr' => array('class' => ' form-control currency')))
            ->add('formula', null, array(
                'required' => true,
                'label' => 'F&oacute;rmula',
                'attr' => array('class' => ' form-control ', 'readonly' => true)))            
            ->add('convenios', EntityType::clase, array(
                'class' => 'ADIF\RecursosHumanosBundle\Entity\Convenio',
                'label' => 'Convenios',
                'required' => false,
                'multiple' => true,    
                'em' => $options['entity_manager'],
                'property' => 'nombre',
                'attr' => array('class' => ' form-control choice ', 
                                'placeholder' => '-- Seleccione los convenios --'),))
            ->add('cuentaContable', EntityType::clase, array(
                'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                'label' => 'Cuenta contable',
                'required' => false,
                'multiple' => false,  
                'em' => $options['entity_manager_contable'],
                'attr' => array('class' => ' form-control choice ', 
                                'placeholder' => '-- Seleccione la cuenta contable --'),
            ))
            ->add('esAjuste', null, array(
                'required' => false,
                'label' => 'Es ajuste',
                'attr' => array('class' => ' form-control ignore')
            ))
            ->add('esIndemnizatorio', null, array(
                'required' => false,
                'label' => 'Es indemnizatorio',
                'attr' => array('class' => ' form-control ignore')
            ))
			->add('cambiaEscalaImpuesto', null, array(
                'required' => false,
                'label' => 'Cambia escala imp.',
                'attr' => array('class' => ' form-control ignore')
            ))
            ->add('esNegativo', null, array(
                'required' => false,
                'label' => 'Es negativo',
                'attr' => array('class' => ' form-control ignore')
            ))
            
			;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Concepto'
        ));
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager_contable');        
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_concepto';
    }

}
