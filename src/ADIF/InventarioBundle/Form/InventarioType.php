<?php
namespace ADIF\InventarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InventarioType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder

                ->add('num', null, array(
                    'required' => true,
                    'label' => 'Num',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('esItemPorLote', null, array(
                    'required' => true,
                    'label' => 'Esitemporlote',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('metodoValoracion', null, array(
                    'required' => false,
                    'label' => 'Método de valoración',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('ubicacion', null, array(
                    'required' => true,
                    'label' => 'Ubicacion',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('cantidad', null, array(
                    'required' => true,
                    'label' => 'Cantidad',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('numeroLote', null, array(
                    'required' => true,
                    'label' => 'Numerolote',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('observaciones', null, array(
                    'required' => true,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('idEmpresa', null, array(
                    'required' => true,
                    'label' => 'Idempresa',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))

                ->add('idUsuarioCreacion', null, array(
                    'required' => false,
                    'label' => 'Idusuariocreacion',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))

                ->add('idUsuarioUltimaModificacion', null, array(
                    'required' => false,
                    'label' => 'Idusuarioultimamodificacion',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),                ))

                ->add('materialNuevo','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos',
                    'attr' => array('class' => ' form-control choice '),                 ))

                ->add('materialProducidoObra','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\CatalogoMaterialesProducidosDeObra',
                    'attr' => array('class' => ' form-control choice '),                 ))

                ->add('tipoMaterial','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\TipoMaterial',
                    'attr' => array('class' => ' form-control choice '),                 ))

                ->add('almacen','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Almacen',
                    'attr' => array('class' => ' form-control choice '),                 ))

                ->add('unidadMedida','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\UnidadMedida',
                    'attr' => array('class' => ' form-control choice '),                 ))

                ->add('estadoConservacion','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\EstadoConservacion',
                    'attr' => array('class' => ' form-control choice '),                 ))

                ->add('estadoServicio','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\EstadoServicio',
                    'attr' => array('class' => ' form-control choice '),                 ))
                ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_inventario';
    }
}
