<?php
namespace ADIF\InventarioBundle\Form\HojaRuta;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class HojaRutaMaterialProducidoDeObraType extends HojaRutaType
{
    /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        parent::buildForm($builder, $options);

        $builder
                ->add('provincia', 'entity', array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Provincia',
                    'label' => 'Provincia',
                    'mapped' => false,
                    'empty_value' => '-- Provincia --',
                    'property' => 'nombre',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice'),
                ))
                ->add('linea','entity', array(
                    'label' => 'Línea',
                    'mapped' => false,
                    'empty_value' => '-- Línea --',
                    'class' => 'ADIF\InventarioBundle\Entity\Linea',
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('almacen','entity', array(
                    'label' => 'Almacen/Obrador',
                    'mapped' => false,
                    'empty_value' => '-- Almacen/Obrador --',
                    'class' => 'ADIF\InventarioBundle\Entity\Almacen',
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('tipoMaterial','entity', array(
                    'disabled' => true,
                    'label' => 'Tipo de Material',
                    'class' => 'ADIF\InventarioBundle\Entity\TipoMaterial',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('grupoMaterial','entity', array(
                    'label' => 'Grupo de Material',
                    'mapped' => false,
                    'empty_value' => '-- Grupo de Material --',
                    'class' => 'ADIF\InventarioBundle\Entity\GrupoMaterial',
                    'attr' => array('class' => ' form-control choice '),
                ))
                ->add('estadoConservacion','entity', array(
                    'label' => 'Estado de Conservación',
                    'mapped' => false,
                    'empty_value' => '-- Estado de Conservación --',
                    'class' => 'ADIF\InventarioBundle\Entity\EstadoConservacion',
                    'attr' => array('class' => ' form-control choice '),
                ))
                 ->add('itemsHojaRutaNuevoProducido', 'collection', array(
                    'type' => new ItemHojaRutaNuevoProducidoCollectionType(),
                    'label' => 'Materiales Nuevos',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'by_reference' => false,
                    'prototype_name' => '__item_hoja_ruta__',
                ))

                ;
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_hojaruta_materialproducidodeobra';
    }
}
