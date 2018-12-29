<?php
namespace ADIF\InventarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TipoRodanteType extends AbstractType
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


                // ->add('idEmpresa', null, array(
                //     'required' => true,
                //     'label' => 'Idempresa',
                //     'label_attr' => array('class' => 'control-label'),
                //     'attr' => array('class' => ' form-control  number '),                ))
                //
                //
                // ->add('idUsuarioCreacion', null, array(
                //     'required' => false,
                //     'label' => 'Idusuariocreacion',
                //     'label_attr' => array('class' => 'control-label'),
                //     'attr' => array('class' => ' form-control  number '),                ))
                //
                //
                // ->add('idUsuarioUltimaModificacion', null, array(
                //     'required' => false,
                //     'label' => 'Idusuarioultimamodificacion',
                //     'label_attr' => array('class' => 'control-label'),
                //     'attr' => array('class' => ' form-control  number '),                ))

                ->add('grupoRodante','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\GrupoRodante',
                    'attr' => array('class' => ' form-control choice '),
                ))        ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\InventarioBundle\Entity\TipoRodante'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_tiporodante';
    }
}
