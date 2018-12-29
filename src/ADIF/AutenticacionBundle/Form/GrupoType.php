<?php

namespace ADIF\AutenticacionBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\GroupFormType as BaseType;

class GrupoType extends BaseType {

    private $roles;

    public function __construct(ContainerInterface $container, $class) {

        parent::__construct($class);

        $roles = $container->getParameter('security.role_hierarchy.roles');
        $this->roles = $this->flatArray($roles);
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);

        $builder->add('name', null, array(
                    'label' => 'form.group_name',
                    'translation_domain' => 'FOSUserBundle',
                    'label' => 'Grupo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba el nombre aqu&iacute;.'))
                )
                ->add('roles', 'choice', array(
                    'choices' => $this->roles,
                    'required' => true,
                    'multiple' => true,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control choice',
                        'placeholder' => 'Seleccione los roles aqu&iacute;.'))
                )
                ->add('descripcion', null, array(
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Escriba una descripci&oacute;n aqu&iacute;.'))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\AutenticacionBundle\Entity\Grupo'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_autenticacionbundle_grupo';
    }

    /**
     * Retorna un array filtrado, el cual contiene todos los roles definidos
     * 
     * @param array $data
     * @return array
     */
    private function flatArray(array $data) {

        $result = array();

        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) === 'ROLE') {
                $result[$key] = $key;
            }
            if (is_array($value)) {
                $tmpresult = $this->flatArray($value);
                if (count($tmpresult) > 0) {
                    $result = array_merge($result, $tmpresult);
                }
            } else {
                $result[$value] = $value;
            }
        }
        return array_unique($result);
    }

}
