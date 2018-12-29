<?php

namespace ADIF\ComprasBundle\Form;

use ADIF\ComprasBundle\Entity\Constantes\ConstanteEstadoBienEconomico;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RenglonOrdenCompraType extends AbstractType {
    
    private $em;
    private $emContable;
    
    public function __construct($em = null, $emContable = null) {
        $this->em = $em;
        $this->emContable = $emContable;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('bienEconomico', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\BienEconomico',
                    'required' => true,
                    'label' => 'Bien económico',
                    'empty_value' => '-- Bien Económico --',
                    'em' => $this->em,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('be')
                                ->join('be.estadoBienEconomico', 'e')
                                ->where('e.denominacionEstadoBienEconomico =  :denominacionEstadoBienEconomico')
                                ->setParameter('denominacionEstadoBienEconomico', ConstanteEstadoBienEconomico::ESTADO_BIEN_ECONOMICO_ACTIVO)
                                ->orderBy('be.denominacionBienEconomico', 'ASC');
                    })
                )
                ->add('unidadMedida', EntityType::clase, array(
                    'class' => 'ADIF\ComprasBundle\Entity\UnidadMedida',
                    'required' => true,
                    'label' => 'Unidad',
                    // 'empty_value' => '-- Unidad --',
                    'em' => $this->em,
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('cantidad', null, array(
                    'required' => true,
                    'label' => 'Cantidad',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => 'form-control currency'),
                ))
                ->add('precioUnitario', null, array(
                    'required' => true,
                    'label' => 'Precio unit.',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => ' form-control money-format',
                        'data-digits' => '4'
                    ),
                ))
                ->add('alicuotaIva', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\AlicuotaIva',
                    'label' => 'IVA',
                    'em' => $this->emContable,
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('ai')->orderBy('ai.valor', 'ASC');
                    })
                )
                ->add('tipoCambio', null, array(
                    'required' => true,
                    'label' => 'Tipo cambio',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array(
                        'class' => 'form-control money-format',
                        'data-digits' => '4'
                    ))
                )
                ->add('centroCosto', EntityType::clase, array(
                    'required' => true,
                    'mapped' => false,
                    'class' => 'ADIF\ContableBundle\Entity\CentroCosto',
                    'em' => $this->emContable,
                    'attr' => array('class' => ' form-control choice '))
                )
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ComprasBundle\Entity\RenglonOrdenCompra'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_comprasbundle_renglonordencompra';
    }

}
