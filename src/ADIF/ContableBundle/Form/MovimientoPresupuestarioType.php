<?php

namespace ADIF\ContableBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\ContableBundle\Repository\CuentaPresupuestariaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class MovimientoPresupuestarioType extends AbstractType {

    protected $ejercicioContable;

    public function __construct($ejercicioContable) {
        $this->ejercicioContable = $ejercicioContable;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('tipoMovimientoPresupuestario', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoMovimientoPresupuestario',
                    'label' => 'Tipo',
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => 'form-control choice'))
                )
                ->add('monto', null, array(
                    'required' => true,
                    'label' => 'Monto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control currency'))
                )
                ->add('cuentaPresupuestariaOrigen', EntityType::clase, array(
                    'required' => true,
                    'class' => 'ADIF\ContableBundle\Entity\CuentaPresupuestaria',
                    'empty_value' => '-- Elija una cuenta origen --',
                    'label' => 'Cuenta presupuestaria origen',
                    'label_attr' => array('class' => 'cuenta-origen'),
                    'property' => 'cuentaSaldo',
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => ' form-control choice cuenta-origen'),
                    'query_builder' => function(CuentaPresupuestariaRepository $er) {
                        return $er->findAllPresupuestariasImputables($this->ejercicioContable);
                    })
                )
                ->add('tipoOperacion', 'checkbox', array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Tipo operaci&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control tipo-operacion'))
                )
                ->add('cuentaPresupuestariaDestino', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaPresupuestaria',
                    'empty_value' => '-- Elija una cuenta destino --',
                    'label' => 'Cuenta presupuestaria destino',
                    'label_attr' => array('class' => 'cuenta-destino'),
                    'property' => 'cuentaSaldo',
                    'em' => $options['entity_manager_contable'],
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(CuentaPresupuestariaRepository $er) {
                        return $er->findAllPresupuestariasImputables($this->ejercicioContable);
                    })
                )
                ->add('detalle', null, array(
                    'required' => true,
                    'label' => 'Detalle',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\MovimientoPresupuestario'
        ));
        $resolver->setRequired('entity_manager_contable');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_movimientopresupuestario';
    }

}
