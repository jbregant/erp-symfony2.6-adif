<?php
namespace ADIF\InventarioBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CatalogoMaterialesNuevosCompraType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder

                ->add('itemPorUnidadCompra', null, array(
                    'required' => false,
                    'label' => 'Items por Unidad de Compra',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),
                  ))


                ->add('factor1', null, array(
                    'required' => false,
                    'label' => 'Factor 1',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),
                  ))


                ->add('factor2', null, array(
                    'required' => false,
                    'label' => 'Factor 2',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))


                ->add('factor3', null, array(
                    'required' => false,
                    'label' => 'Factor 3',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))


                ->add('factor4', null, array(
                    'required' => false,
                    'label' => 'Factor 4',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))

                ->add('catalogoMaterialesNuevos','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos',
                    'attr' => array('class' => ' form-control choice '),
                ))

                ->add('unidadMedida','entity', array(
                    'required' => false,
                    'label' => 'Unidad de Medida de Compra',
                    'class' => 'ADIF\InventarioBundle\Entity\UnidadMedida',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                ))

                ->add('unidadMedidaPackaging','entity', array(
                    'required' => false,
                    'label' => 'Items de Packaging ',
                    'class' => 'ADIF\InventarioBundle\Entity\UnidadMedida',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                ))

                ->add('grupoAduana','entity', array(
                    'required' => false,
                    'class' => 'ADIF\InventarioBundle\Entity\GrupoAduana',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                ));
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevosCompra'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_catalogomaterialesnuevoscompra';
    }
}
