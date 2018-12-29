<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class RenglonComprobanteType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('idRenglonOrdenCompra', 'hidden', array(
                    'required' => true))
                ->add('descripcion', null, array(
                    'required' => true,
                    'label' => 'Detalle',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('cantidad', null, array(
                    'required' => true,
                    'label' => 'Cantidad',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control numberPositive'),))
                ->add('precioUnitario', null, array(
                    'required' => true,
                    'label' => 'Precio unitario',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control numberPositive'),))
                ->add('bonificacionTipo', null, array(
                    'required' => true,
                    'label' => 'Tipo bonificaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('bonificacionValor', null, array(
                    'required' => false,
                    'label' => 'Valor bonificaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('neto', null, array(
                    'label' => 'Neto',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control numberPositive'),))
                ->add('montoIva', null, array(
                    'label' => 'Monto IVA',
                    'mapped' => false,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control numberPositive'),))
                ->add('observaciones', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('alicuotaIva', 'entity', array(
                    'class' => 'ADIF\ContableBundle\Entity\AlicuotaIva',
                    'label' => 'IVA',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('ai')
                        ->orderBy('ai.valor', 'ASC');
                    }
                ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\RenglonComprobante'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_rengloncomprobante';
    }

}
