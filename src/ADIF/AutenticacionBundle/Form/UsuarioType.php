<?php

namespace ADIF\AutenticacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class UsuarioType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('username', null, array(
                    'label' => 'form.username',
                    'translation_domain' => 'FOSUserBundle',
                    'error_bubbling' => true,
                    'required' => true,
                    'attr' => array('class' => 'form-control')
                ))
                ->add('email', null, array(
                    'label' => 'form.email',
                    'translation_domain' => 'FOSUserBundle',
                    'required' => true,
                    'error_bubbling' => true,
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba el email aqu&iacute;.'),
                ))
                ->add('nombre', null, array(
                    'label' => 'Nombre',
                    'translation_domain' => 'FOSUserBundle',
                    'required' => true,
                    'error_bubbling' => true,
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba el nombre aqu&iacute;.'),
                ))
                ->add('apellido', null, array(
                    'label' => 'Apellido',
                    'translation_domain' => 'FOSUserBundle',
                    'required' => true,
                    'error_bubbling' => true,
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba el apellido aqu&iacute;.'),
                ))
                ->add('enabled', null, array(
                    'label' => 'Habilitado',
                    'required' => false
                ))
                ->add('area', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Area',
                    'required' => true,
                    'label' => 'Área',
                    'empty_value' => '-- Área --',
                    'em' => $options['entity_manager_rrhh'],
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice')
                ))
                ->add('groups', EntityType::clase, array(
                    'class' => 'ADIF\AutenticacionBundle\Entity\Grupo',
                    'required' => true,
                    'label' => 'Grupos',
                    'multiple' => true,
                    'error_bubbling' => true,
                    'em' => $options['entity_manager'],
                    'attr' => array(
                        'class' => 'form-control choice',
                        'placeholder' => 'Seleccione los grupos aqu&iacute;.')
                ))
				->add('empresas', EntityType::clase, array(
                    'class' => 'ADIF\AutenticacionBundle\Entity\Empresa',
                    'required' => true,
                    'label' => 'Empresas',
                    'em' => $options['entity_manager'],
                    'attr' => array(
                        'class' => 'form-control choice',
                        'placeholder' => 'Seleccione la empresa.')
                ))
                ->add('usuarioComo', EntityType::clase, array(
                    'class' => 'ADIF\AutenticacionBundle\Entity\Usuario',
                    'required' => false,
					'mapped' => false,
                    'em' => $options['entity_manager'],
                    'label' => 'Usuario como...',
                    'attr' => array(
                        'class' => 'form-control choice',
                        'placeholder' => 'Seleccione el usuario.'),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                                ->where('u.enabled = 1')
                                ->orderBy('u.username', 'ASC');
                    }
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\AutenticacionBundle\Entity\Usuario'
        ));
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager_rrhh');
        
        
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_autenticacionbundle_usuario';
    }

}
