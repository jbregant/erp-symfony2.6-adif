<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class SubcategoriaType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('idCategoria', EntityType::clase, array(
                'class' => 'ADIF\RecursosHumanosBundle\Entity\Categoria',
                'required' => true,
                'label' => 'Categor&iacute;a',
                'em' => $options['entity_manager'],
                'attr' => array('class' => 'form-control select2me')
            ))
            ->add('nombre', null, array(
                'required' => true,
                'label' => 'Nombre',
                'attr' => array('class' => ' form-control ')
            ))
            ->add('montoBasico', null, array(
                'required' => true,
                'label' => 'Monto b&aacute;sico',
                'attr' => array('class' => ' form-control currency')
            ))
            ->add('categoriaRecibo', null, array(
                'required' => false,
                'label' => 'Categor&iacute;a recibo',
                'attr' => array('class' => ' form-control ')
            ))
            ->add('esCategoria02', 'checkbox', array(
                //'choices'  => array('No', 'Si'),
                'required' => false,
                'label' => '¿Es categor&iacute;a 02? (02 Sueldo)',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => 'form-control ')
            ))
             ->add('esTiempoCompleto', 'checkbox', array(
                'required' => false,
                'label' => '¿Es tiempo completo?',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => 'form-control ')
            ))
             ->add('sirhuGrado', null, array(
                'required' => false,
                'label' => 'Grado',
                'attr' => array('class' => ' form-control ')
            ))
             ->add('sirhuEscalafon', null, array(
                'required' => false,
                'label' => 'Escalaf&oacute;n',
                'attr' => array('class' => ' form-control ')
            ))
        ;
    }
    
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Subcategoria'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_recursoshumanosbundle_subcategoria';
    }
}
