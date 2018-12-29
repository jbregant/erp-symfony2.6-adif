<?php
namespace ADIF\InventarioBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\InventarioBundle\Form\InventarioType;

class CatalogoMaterialesProducidosDeObraType extends AbstractType
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
//                    'disabled' => true,
                    'label' => 'Número Interno',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number ', 'maxlength' => '9 !important'),))

                ->add('num', null, array(
                    'required' => false,
                    'label' => 'NUM',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('denominacionOtroLenguaje', null, array(
                    'required' => false,
                    'label' => 'Descripción para otros lenguajes',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('codigoBarra', null, array(
                    'required' => false,
                    'label' => 'Código de Barras',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('medida', null, array(
                    'required' => false,
                    'label' => 'Medidas',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('peso', null, array(
                    'required' => false,
                    'label' => 'Peso',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('volumen', null, array(
                    'required' => false,
                    'label' => 'Volumen',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('participaInventario', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => '¿Es Item de Inventario?',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ','checked' => ''),
                    ))

                ->add('participaVenta', null, array(
                    'required' => false,
                    'label' => '¿Es Item de Ventas?',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                    ))

                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('valorOrigen', null, array(
                    'required' => false,
                    'label' => 'Valor Origen',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('rubro', null, array(
                    'required' => false,
                    'label' => 'Rubro',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('metodoAmortizacion', null, array(
                    'required' => false,
                    'label' => 'Metodoamortizacion',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('vidaUtil', null, array(
                    'required' => false,
                    'label' => 'Vidautil',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),                ))

                ->add('grupoMaterial','entity', array(
                    'label' => 'Grupo de Material',
                    'class' => 'ADIF\InventarioBundle\Entity\GrupoMaterial',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                ))

                ->add('unidadMedida','entity', array(
                    'required' => false,
                    'label' => 'Unidad de Medida de Inventario',
                    'empty_value' => '-- Unidad --',
                    'class' => 'ADIF\InventarioBundle\Entity\UnidadMedida',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                ))

                ->add('tipoEnvio','entity', array(
                    'required' => false,
                    'label' => 'Tipo de Envio',
                    'empty_value' => '-- Tipo --',
                    'class' => 'ADIF\InventarioBundle\Entity\TipoEnvio',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                ))

                ->add('estadoInventario','entity', array(
                    'required' => false,
                    'label' => 'Estado:',
                    'class' => 'ADIF\InventarioBundle\Entity\EstadoInventario',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                ))

                ->add('valoresPropiedad', 'collection', array(
                    'type' => new PropiedadValorCollectionType($options['where']),
                    'label' => 'Valor de Propiedad',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'by_reference' => false,
                    'prototype_name' => '__propiedad_material_producido__',
                ))

                ->add('inventario', new InventarioType(),array(
                    'required' => false,
                ))
                ->add('fotos', 'collection', array(
                    'type' => new InventarioFotoArchivoType(),
                    'allow_delete' => true,
                    'allow_add' => true,
                    'label' => 'Fotos',
                    'prototype_name' => '__fotos__',
                    'label_attr' => array(
                        'class' => 'hidden'),
                    'attr' => array(
                        'class' => 'hidden')))
                ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\InventarioBundle\Entity\CatalogoMaterialesProducidosDeObra',
            'where' => null,
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_catalogomaterialesproducidosdeobra';
    }
}
