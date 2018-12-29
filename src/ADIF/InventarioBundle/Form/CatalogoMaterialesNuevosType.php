<?php
namespace ADIF\InventarioBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\InventarioBundle\Form\InventarioType;
use ADIF\InventarioBundle\Form\PropiedadValorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CatalogoMaterialesNuevosType extends AbstractType
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
                    'label' => 'Número Interno',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  number '),
                ))

                ->add('num', null, array(
                    'required' => false,
                    'label' => 'NUM',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control number ', 'maxlength' => 9),
                ))


                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('denominacionOtroLenguaje', null, array(
                    'required' => false,
                    'label' => 'Descripción para otros lenguajes',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('medida', null, array(
                    'required' => false,
                    'label' => 'Medidas',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('peso', null, array(
                    'required' => false,
                    'label' => 'Peso',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('volumen', null, array(
                    'required' => false,
                    'label' => 'Volumen',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('valor', null, array(
                    'required' => false,
                    'label' => 'Valor',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('tipoValor', null, array(
                    'required' => false,
                    'label' => 'Tipo de Valor',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('participaInventario', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => '¿Es Item de Inventario?',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ','checked' => ''),
                ))

                ->add('participaVenta', null, array(
                    'required' => false,
                    'label' => 'Participaventa',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('participaCompra', null, array(
                    'required' => false,
                    'label' => '¿Es Item para Compras?',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('esSet', null, array(
                    'required' => false,
                    'label' => '¿Es Set de Materiales?',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('setMateriales', 'collection', array(
                    'type' => new SetMaterialType(),
                    'label' => 'Material Nuevo',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype_name' => '__componente_set_material__'))

                ->add('sujetoAImpuesto', null, array(
                    'required' => false,
                    'label' => '¿Sujeto a impuestos?',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observaciones',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('codigoBarra', null, array(
                    'required' => false,
                    'label' => 'Código de Barras',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('valorOrigen', null, array(
                    'required' => false,
                    'label' => 'Valororigen',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('rubro', null, array(
                    'required' => false,
                    'label' => 'Rubro',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('metodoAmortizacion', null, array(
                    'required' => false,
                    'label' => 'Metodoamortizacion',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('vidaUtil', null, array(
                    'required' => false,
                    'label' => 'Vidautil',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('grupoMaterial','entity', array(
                    'label' => 'Grupo de Material',
                    'class' => 'ADIF\InventarioBundle\Entity\GrupoMaterial',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                ))

//                ->add('tipoImpuesto','entity', array(
//                    'label' => 'Tipo de Impuesto',
//                    'class' => 'ADIF\InventarioBundle\Entity\TipoImpuesto',
//                    'attr' => array('class' => ' form-control choice '),
//                ))
                ->add('tipoImpuesto', 'entity', array(
                    'class' => 'ADIF\ContableBundle\Entity\TipoImpuesto',
                    'required' => false,
                    'label' => 'Tipo de Impuesto',
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                    'empty_value' => ' ',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice',

                )))


                ->add('unidadMedida','entity', array(
                    'required' => false,
                    'class' => 'ADIF\InventarioBundle\Entity\UnidadMedida',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacionCorta', 'ASC');
                     },
                ))

                ->add('fabricante','entity', array(
                    'required' => false,
                    'class' => 'ADIF\InventarioBundle\Entity\Fabricante',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                ))

                ->add('tipoEnvio','entity', array(
                    'required' => false,
                    'class' => 'ADIF\InventarioBundle\Entity\TipoEnvio',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) {
                         return $er->createQueryBuilder('e')
                             ->orderBy('e.denominacion', 'ASC');
                     },
                ))

                ->add('transportePallet', null, array(
                    'required' => false,
                    'label' => '¿Transporte por Pallets?',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('transporteCajas', null, array(
                    'required' => false,
                    'label' => '¿Transporte por Cajas?',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('unidadesPallet', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'Unidades en Pallet',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control number '),
                ))

                ->add('unidadesCajas', null, array(
                    'required' => false,
                    'disabled' => true,
                    'label' => 'Unidades en Cajas',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control number '),
                ))

                ->add('metodoValoracion', null, array(
                    'required' => false,
                    'label' => 'Metodo de Valoración',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))

                ->add('cuentaContable', null, array(
                    'required' => false,
                    'label' => 'Cuenta Contable',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),
                ))


                ->add('estadoInventario','entity', array(
                    'required' => false,
                    'label' => 'Estado:',
                    'class' => 'ADIF\InventarioBundle\Entity\EstadoInventario',
                    'attr' => array('class' => ' form-control choice '),
                ))

                ->add('catalogoMaterialesNuevosCompra', new CatalogoMaterialesNuevosCompraType(), array(
                    'required' => true)
                )

                ->add('inventario', new InventarioType(), array( 'required' => true )
                        )

//                ->add('propiedades', 'collection', array(
//                    'type' => new PropiedadValorType(),
//                    'label' => 'Propiedades',
//                    'allow_delete' => true,
//                    'allow_add' => true,
//                    'prototype_name' => '__propiedades__'
//                ))

                ->add('valoresPropiedad', 'collection', array(
                    'type' => new PropiedadValorCollectionType($options['where']),
                    'label' => 'Valor de Propiedad',
                    'allow_delete' => true,
                    'allow_add' => true,
                    'by_reference' => false,
                    'prototype_name' => '__propiedad_CMN__',
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
            'data_class' => 'ADIF\InventarioBundle\Entity\CatalogoMaterialesNuevos',
             'where' => null,
             'em' => null
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_catalogomaterialesnuevos';
    }
}
