<?php

namespace ADIF\AutenticacionBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RegistrationType extends BaseType {

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\AutenticacionBundle\Entity\Usuario',
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        $builder->add('enabled', null, array(
                    'label' => 'Habilitado',
                    'required' => false,
                ))
                ->add('username', null, array(
                    'label' => 'Nombre de usuario',
                    'translation_domain' => 'FOSUserBundle',
                    'required' => true,
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba el nombre de usuario aqu&iacute;.'),
                ))
                ->add('email', null, array(
                    'label' => 'Email',
                    'translation_domain' => 'FOSUserBundle',
                    'required' => true,
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
                ->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array(
                        'label' => 'form.password',
                        'attr' => array(
                            'class' => 'form-control',
                            'placeholder' => 'Escriba contraseña aqu&iacute;.')),
                    'second_options' => array(
                        'label' => 'form.password_confirmation',
                        'attr' => array(
                            'class' => 'form-control',
                            'placeholder' => 'Repita contraseña aqu&iacute;.',
                            'data-rule-equalto' => '#fos_user_registration_form_plainPassword_first'),
                    ),
                    'invalid_message' => 'fos_user.password.mismatch',
                ))
                ->add('area', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Area',
                    'required' => true,
                    'label' => 'Área',
                    'empty_value' => '-- Área --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice')
                ))
                ->add('groups', EntityType::clase, array(
                    'class' => 'ADIF\AutenticacionBundle\Entity\Grupo',
                    'required' => true,
                    'multiple' => true,
                    'attr' => array(
                        'class' => 'form-control choice',
                        'placeholder' => 'Seleccione los grupos aqu&iacute;.',
                    ),
				))
				->add('empresas', EntityType::clase, array(
                    'class' => 'ADIF\AutenticacionBundle\Entity\Empresa',
                    'required' => true,
                    'label' => 'Empresas',
                    'attr' => array(
                        'class' => 'form-control choice',
                        'placeholder' => 'Seleccione la empresa.')
                ))
				->add('usuarioComo', EntityType::clase, array(
                    'class' => 'ADIF\AutenticacionBundle\Entity\Usuario',
                    'required' => false,
					'mapped' => false,
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

        $builder->add('usuario_ad', 'hidden', array(
            'mapped' => false,
            'data' => 'false',
            'attr' => array('id' => 'usuario_ad')
        ));
    }

    
    /**
     * @return string
     */
    public function getName() {
        return 'adif_autenticacionbundle_registration';
    }

}
