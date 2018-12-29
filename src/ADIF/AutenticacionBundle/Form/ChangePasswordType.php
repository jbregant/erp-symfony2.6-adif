<?php

namespace ADIF\AutenticacionBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ChangePasswordFormType as BaseType;

class ChangePasswordType extends BaseType {
   
    public function buildForm(FormBuilderInterface $builder, array $options)
        {
            parent::buildForm($builder, $options);

            $builder->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.new_password','attr' => array(
                       'class' => 'form-control',
                       'placeholder' => 'Escriba su nueva contraseña.',
                       'tabindex' => '5')),
                'second_options' => array('label' => 'form.new_password_confirmation','attr' => array(
                       'class' => 'form-control',
                       'placeholder' => 'Repita nueva contraseña.',
                       'tabindex' => '5')),
                'invalid_message' => 'fos_user.password.mismatch',
            ));
        }

        /**
         * @return string
         */
        public function getName()
        {
            return 'adif_autenticacionbundle_change_password';
        }

}