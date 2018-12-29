<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RegimenRetencionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Denominación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('descripcion', null, array(
                    'required' => true,
                    'label' => 'Descripci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('minimoExento', null, array(
                    'required' => false,
                    'label' => 'Mínimo exento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control numberPositive'),))
                ->add('minimoNoImponible', null, array(
                    'required' => false,
                    'label' => 'Mínimo no imponible',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control numberPositive'),))
                ->add('alicuota', null, array(
                    'required' => true,
                    'label' => 'Alicuota',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control numberPositive'),))
                ->add('minimoRetencion', null, array(
                    'required' => false,
                    'label' => 'Mínimo retención',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control numberPositive'),))
                ->add('tipoImpuesto', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoImpuesto',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager']))
                ->add('cuentaContable', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'label' => 'Cuenta contable',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager']))
                ->add('asociableBienEconomico', null, array(
                    'required' => false,
                    'label' => 'Se asocia a un bienes o servicios',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')))
                ->add('codigoSiap', null, array(
                    'required' => true,
                    'label' => 'C&oacute;digo SIAP',
                    'attr' => array('class' => ' form-control  number ')));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\RegimenRetencion'
        ));

        $resolver->setRequired('entity_manager');

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_regimenretencion';
    }

}
