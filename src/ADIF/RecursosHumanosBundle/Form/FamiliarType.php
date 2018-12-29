<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class FamiliarType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('escolaridad', null, array(
                'required' => false,
                'label' => 'Escolaridad',
                'attr' => array('class' => ' form-control '),))
            ->add('anioCursa', null, array(
                'required' => false,
                'label' => 'A&ntilde;o cursa',
                'attr' => array('class' => ' form-control '),))
            ->add('enGuarderia', null, array(
                'required' => false,
                'label' => 'En guarderia',
                'attr' => array('class' => ' form-control '),))
            ->add('aCargoOS', null, array(
                'required' => false,
                'label' => 'A cargo OS',
                'attr' => array('class' => ' form-control '),))
            ->add('idEmpleado', 'hidden')
            ->add('idTipoRelacion', EntityType::clase, array(
                'class' => 'ADIF\RecursosHumanosBundle\Entity\TipoRelacion',
                'label' => 'Tipo relaci&oacute;n',
                'attr' => array('class' => ' form-control choice '),
                'em' => $options['entity_manager'],
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                    ->orderBy('t.nombre', 'ASC');
                }
            ));
        
        $builder->add('idPersona', new PersonaType($options['entity_manager']));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Familiar'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_familiar';
    }

}
