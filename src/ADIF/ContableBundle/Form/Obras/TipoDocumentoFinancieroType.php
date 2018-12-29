<?php

namespace ADIF\ContableBundle\Form\Obras;

use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoImpuesto;
use ADIF\ContableBundle\Repository\RegimenRetencionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class TipoDocumentoFinancieroType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nombre', null, array(
                    'required' => true,
                    'label' => 'Nombre',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('cuentaContable', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'label' => 'Cuenta contable',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('imputaCuentaTipoDocumento', 'checkbox', array(
                    'required' => true,
                    'label' => 'Imputa a la cuenta del Tipo de Documento Financiero',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control'))
                )
                ->add('regimenRetencionIVA', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\RegimenRetencion',
                    'label' => 'Régimen retención IVA',
                    'attr' => array('class' => ' form-control choice'),
                    'em' => $options['entity_manager'],                    
                    'query_builder' => function(RegimenRetencionRepository $er) {
                        return $er->getRegimenes(ConstanteTipoImpuesto::IVA);
                    })
                )
                ->add('regimenRetencionIIBB', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\RegimenRetencion',
                    'label' => 'Régimen retención IIBB',
                    'attr' => array('class' => ' form-control choice '),    
                    'em' => $options['entity_manager'],                    
                    'query_builder' => function(RegimenRetencionRepository $er) {
                        return $er->getRegimenes(ConstanteTipoImpuesto::IIBB);
                    })
                )
                ->add('regimenRetencionSUSS', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\RegimenRetencion',
                    'label' => 'Régimen retención SUSS',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'],                                       
                    'query_builder' => function(RegimenRetencionRepository $er) {
                        return $er->getRegimenes(ConstanteTipoImpuesto::SUSS);
                    })
                )
                ->add('regimenRetencionGanancias', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\RegimenRetencion',
                    'label' => 'Régimen retención Ganancias',
                    'em' => $options['entity_manager'],                    
                    'attr' => array('class' => ' form-control choice '),                    
                    'query_builder' => function(RegimenRetencionRepository $er) {
                        return $er->getRegimenes(ConstanteTipoImpuesto::Ganancias);
                    })
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\Obras\TipoDocumentoFinanciero'
        ));
        
        $resolver->setRequired('entity_manager');

    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_obras_tipodocumentofinanciero';
    }

}
