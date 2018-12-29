<?php
namespace ADIF\InventarioBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CatalogoMaterialesRodantesType extends AbstractType
{
  /**
  * @param FormBuilderInterface $builder
  * @param array $options
  */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    //Primero busco el where en las opciones del builder, sino lo busco en el constructor:
    $where = (isset($options['where'])) ? 'u.habilitado' . $options['where'] . ' = 1' : '1 = 1';

    $builder
    ->add('numeroInterno', null, array(
          'required' => false,
          'read_only' => true,
          'disabled' => true,
          'label' => 'N&uacute;mero Interno',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control  number '),))

    ->add('provincia', 'entity', array(
          'class' => 'ADIF\RecursosHumanosBundle\Entity\Provincia',
          'label' => 'Provincia',
          'empty_value' => '-- Provincia --',
          'property' => 'nombre',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control choice'),
          'query_builder' => function(EntityRepository $er) {
               return $er->createQueryBuilder('e')
                   ->orderBy('e.nombre', 'ASC');
           },))

    ->add('numeroVehiculo', null, array(
          'required' => false,
          'label' => 'N&uacute;mero de Vehiculo',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('denominacion', null, array(
          'required' => false,
          'label' => 'Descripci&oacute;n',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('denominacionOtroLenguaje', null, array(
          'required' => false,
          'label' => 'Descripci&oacute;n para otro Lenguaje',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('ubicacion', null, array(
          'required' => false,
          'label' => 'Ubicaci&oacute;n',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('participaInventario', null, array(
          'required' => false,
          'label' => '¿Es item de Inventario?',
          'data' => true,
          'read_only' => true,
          'disabled' => true,
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('participaVenta', null, array(
          'required' => false,
          'label' => '¿Es item para Compras?',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('esActivoFijo', null, array(
          'required' => false,
          'label' => '¿Es Activo Fijo?',
          'data' => true,
          'read_only' => true,
          'disabled' => true,
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('valorActual', null, array(
          'required' => false,
          'label' => 'Valor Actual',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('latitud', null, array(
          'required' => false,
          'label' => 'Latitud',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('longitud', null, array(
          'required' => false,
          'label' => 'Longitud',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('esSujetoImpuesto', null, array(
          'required' => false,
          'label' => '¿Sujeto a Impuestos?',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('idFabricante', null, array(
          'required' => false,
          'label' => 'Fabricante',
          'empty_value' => '-- Fabricante --',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('fechaAdquisicion', 'datetime', array(
          'required' => false,
          'label' => 'Fecha Adquisici&oacute;n',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control  datepicker '),
          'widget' => 'single_text',
          'format' => 'dd/MM/yyyy',))

    ->add('valorAdquisicion', null, array(
          'required' => false,
          'label' => 'Valor Adquisici&oacute;n',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('observacion', null, array(
          'required' => false,
          'label' => 'Observaciones',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('valorOrigen', null, array(
          'required' => false,
          'label' => 'Valor Origen',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('rubro', null, array(
          'required' => false,
          'label' => 'Rubro',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('metodoAmortizacion', null, array(
          'required' => false,
          'label' => 'Metodo de Amortizaci&oacute;n',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('vidaUtil', null, array(
          'required' => false,
          'label' => 'Vida &Uacute;til',
          'label_attr' => array('class' => 'control-label'),
          'attr' => array('class' => ' form-control '),))

    ->add('idGrupoRodante','entity', array(
          'required' => true,
          'label' => 'Grupo de Rodante',
          'class' => 'ADIF\InventarioBundle\Entity\GrupoRodante',
          'attr' => array('class' => ' form-control choice '),
          'query_builder' => function(EntityRepository $er) {
               return $er->createQueryBuilder('e')
                   ->orderBy('e.denominacion', 'ASC');
           },))

    ->add('idTipoRodante','entity', array(
          'required' => false,
          'label' => 'Tipo de Rodante',
          'empty_value' => '-- Tipo de Rodante --',
          'class' => 'ADIF\InventarioBundle\Entity\TipoRodante',
          'attr' => array('class' => ' form-control choice '),
          'query_builder' => function(EntityRepository $er) {
               return $er->createQueryBuilder('e')
                   ->orderBy('e.denominacion', 'ASC');
           },))

    ->add('idMarca','entity', array(
          'required' => false,
          'label' => 'Marca',
          'empty_value' => '-- Marca --',
          'class' => 'ADIF\InventarioBundle\Entity\Marca',
          'attr' => array('class' => ' form-control choice '),
          'query_builder' => function(EntityRepository $er) {
               return $er->createQueryBuilder('e')
                   ->orderBy('e.denominacion', 'ASC');
           },))

    ->add('idModelo','entity', array(
          'required' => false,
          'label' => 'Modelo',
          'empty_value' => '-- Modelo --',
          'class' => 'ADIF\InventarioBundle\Entity\Modelo',
          'attr' => array('class' => ' form-control choice '),
          'query_builder' => function(EntityRepository $er) {
               return $er->createQueryBuilder('e')
                   ->orderBy('e.denominacion', 'ASC');
           },))

    ->add('idEstadoConservacion','entity', array(
          'required' => true,
          'label' => 'Estado de Conservaci&oacute;n',
          'class' => 'ADIF\InventarioBundle\Entity\EstadoConservacion',
          'attr' => array('class' => ' form-control choice '),))

    ->add('idEstadoServicio','entity', array(
          'required' => true,
          'label' => 'Servicio',
          'class' => 'ADIF\InventarioBundle\Entity\Servicio',
          'attr' => array('class' => ' form-control choice '),
          'query_builder' => function(EntityRepository $er) {
               return $er->createQueryBuilder('e')
                   ->orderBy('e.denominacion', 'ASC');
           },))

    ->add('idLinea','entity', array(
          'required' => true,
          'label' => 'Linea',
          'empty_value' => '-- Linea --',
          'class' => 'ADIF\InventarioBundle\Entity\Linea',
          'attr' => array('class' => ' form-control choice '),
          'query_builder' => function(EntityRepository $er) {
               return $er->createQueryBuilder('e')
                   ->orderBy('e.denominacion', 'ASC');
           },))

    ->add('idOperador','entity', array(
          'required' => false,
          'label' => 'Operador',
          'empty_value' => '-- Operador --',
          'class' => 'ADIF\InventarioBundle\Entity\Operador',
          'attr' => array('class' => ' form-control choice '),
          'query_builder' => function(EntityRepository $er) {
               return $er->createQueryBuilder('e')
                   ->orderBy('e.denominacion', 'ASC');
           },))

    ->add('idTipoEnvio','entity', array(
          'required' => false,
          'label' => 'Tipo de Env&iacute;o',
          'empty_value' => '-- Tipo de Env&iacute;o --',
          'class' => 'ADIF\InventarioBundle\Entity\TipoEnvio',
          'attr' => array('class' => ' form-control choice '),
          'query_builder' => function(EntityRepository $er) {
               return $er->createQueryBuilder('e')
                   ->orderBy('e.denominacion', 'ASC');
           },))

    ->add('idCodigoTrafico','entity', array(
          'required' => false,
          'label' => 'C&oacute;digo de Tr&aacute;fico',
          'empty_value' => '-- C&oacute;digo de Tr&aacute;fico --',
          'class' => 'ADIF\InventarioBundle\Entity\CodigoTrafico',
          'attr' => array('class' => ' form-control choice '),
          'query_builder' => function(EntityRepository $er) {
               return $er->createQueryBuilder('e')
                   ->orderBy('e.codigo', 'ASC');
           },))

    ->add('valoresPropiedad', 'collection', array(
          'type' => new PropiedadValorCollectionType($options['where']),
          'label' => 'Valor de Propiedad',
          'allow_delete' => true,
          'allow_add' => true,
          'by_reference' => false,
          'prototype_name' => '__propiedad_material_rodante__',))

    ->add('idEstadoInventario','entity', array(
          'required' => false,
          'label' => 'Estado:',
          'class' => 'ADIF\InventarioBundle\Entity\EstadoInventario',
          'attr' => array('class' => ' form-control choice '),
          'query_builder' => function(EntityRepository $er) {
               return $er->createQueryBuilder('e')
                   ->orderBy('e.denominacion', 'ASC');
           },))

    ->add('fotos', 'collection', array(
          'type' => new InventarioFotoArchivoType(),
          'allow_delete' => true,
          'allow_add' => true,
          'label' => 'Fotos',
          'prototype_name' => '__fotos__',
          'label_attr' => array('class' => 'hidden'),
          'attr' => array('class' => 'hidden'),))

    ->add('idEstacion', 'entity', array(
          'required' => true,
          'label' => 'Estaci&oacute;n',
          'empty_value' => '-- Estaci&oacute;n --',
          'class' => 'ADIF\InventarioBundle\Entity\Estacion',
          'attr' => array('class' => ' form-control choice '),
          'query_builder' => function(EntityRepository $er) {
               return $er->createQueryBuilder('e')
                   ->orderBy('e.denominacion', 'ASC');
           },));
}

    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
        'data_class' => 'ADIF\InventarioBundle\Entity\CatalogoMaterialesRodantes',
        'where' => null,
        'em' => null
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_catalogomaterialesrodantes';
    }
}
