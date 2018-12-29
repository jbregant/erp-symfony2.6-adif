<?php

namespace ADIF\ComprasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\ContableBundle\Repository\RegimenRetencionRepository;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class BienEconomicoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('denominacionBienEconomico', null, array(
                    'required' => true,
                    'label' => 'Denominación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ')
                        )
                )
                ->add('descripcionBienEconomico', null, array(
                    'required' => false,
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ')
                        )
                )
                ->add('esProducto', null, array(
                    'required' => false,
                    'label' => 'Es producto',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ')
                        )
                )
                ->add('rubro', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\Rubro',
                    'required' => true,
                    'empty_value' => '-- Rubro --',
                    'em' => $options['entity_manager'],
                    'attr' => array(
                        'class' => ' form-control choice ')
                        )
                )
                ->add('cuentaContable', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'required' => false,
                    'label' => 'Cuenta contable',
                    'label_attr' => array('class' => 'control-label'),
                    'empty_value' => '-- Cuenta contable --',
                    'em' => $options['entity_manager_contable'],
                    'attr' => array(
                        'class' => ' form-control choice'
                    ),
                    'query_builder' => function(\ADIF\ContableBundle\Repository\CuentaContableRepository $r ) {
                        return $r->createQueryBuilder('cc')
                            ->where('cc.esImputable = 1')
                            ->orderBy('cc.codigoCuentaContable', 'ASC');
                        }
                ))
                ->add('requiereEspecificacionTecnica', null, array(
                    'required' => false,
                    'label' => 'Especificación técnica',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control ')
                        )
                )
                ->add('regimenRetencionSUSS', EntityType::clase, array(
                    'mapped' => false,
                    'required' => false,
                    'class' => 'ADIF\ContableBundle\Entity\RegimenRetencion',
                    'attr' => array('class' => ' form-control choice '),
                    'empty_value' => '-- Elija un régimen de retención SUSS --',
                    'label' => 'Régimen de retención SUSS',
                    'em' => $options['entity_manager_contable'],
                    'query_builder' => function(RegimenRetencionRepository $er) {
                        return $er->getRegimenes(ConstanteTipoImpuesto::SUSS);
                    }
                ))
                ->add('regimenRetencionIIBB', EntityType::clase, array(
                    'mapped' => false,
                    'required' => false,
                    'class' => 'ADIF\ContableBundle\Entity\RegimenRetencion',
                    'attr' => array('class' => ' form-control choice '),
                    'empty_value' => '-- Elija un régimen de retención IIBB --',
                    'label' => 'Régimen de retención IIBB',
                    'em' => $options['entity_manager_contable'],
                    'query_builder' => function(RegimenRetencionRepository $er) {
                        return $er->getRegimenes(ConstanteTipoImpuesto::IIBB);
                    }
                ))
                ->add('regimenRetencionIVA', EntityType::clase, array(
                    'mapped' => false,
                    'required' => false,
                    'class' => 'ADIF\ContableBundle\Entity\RegimenRetencion',
                    'attr' => array('class' => ' form-control choice '),
                    'empty_value' => '-- Elija un régimen de retención IVA --',
                    'label' => 'Régimen de retención IVA',
                    'em' => $options['entity_manager_contable'],
                    'query_builder' => function(RegimenRetencionRepository $er) {
                        return $er->getRegimenes(ConstanteTipoImpuesto::IVA);
                    }
                ))
                ->add('regimenRetencionGanancias', EntityType::clase, array(
                    'mapped' => false,
                    'required' => false,
                    'class' => 'ADIF\ContableBundle\Entity\RegimenRetencion',
                    'attr' => array('class' => ' form-control choice '),
                    'empty_value' => '-- Elija un régimen de retención Ganancias --',
                    'label' => 'Régimen de retención Ganancias',
                    'em' => $options['entity_manager_contable'],
                    'query_builder' => function(RegimenRetencionRepository $er) {
                        return $er->getRegimenes(ConstanteTipoImpuesto::Ganancias);
                    }
                ))
                ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\BienEconomico'
        ));
        $resolver->setRequired('entity_manager');
        $resolver->setRequired('entity_manager_contable');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_bieneconomico';
    }

}
