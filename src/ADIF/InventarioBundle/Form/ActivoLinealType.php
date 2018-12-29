<?php

namespace ADIF\InventarioBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActivoLinealType extends AbstractType {

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
            ->add('localidad', 'entity', array(
                'class' => 'ADIF\RecursosHumanosBundle\Entity\Localidad',
                'required' => false,
                'label' => 'Localidad',
                'empty_value' => '-- Localidad --',
                'property' => 'NombreConProvincia',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control choice')
            ))
            ->add('progresivaInicioTramo', 'number', array(
                'grouping' => true,
                'precision' => 3,
                'required' => true,
                //'read_only' => true, COMENTADO POR IA-247
                'label' => 'Progresiva Inicio Tramo',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control ')
            ))
            ->add('progresivaFinalTramo', 'number', array(
                'grouping' => true,
                'precision' => 3,
                'required' => true,
                //'read_only' => $options['esTramoIntermedio'], COMENTADO POR IA-247
                'label' => 'Progresiva Final Tramo',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control ')
            ))
            ->add('kilometraje', 'number', array(
                'grouping' => true,
                'precision' => 3,
                'required' => false,
                'disabled' => true,
                'label' => 'Kilometraje',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control ')
            ))
            ->add('zonaVia', null, array(
                'required' => false,
                'disabled' => true,
                'label' => 'Zona Vía',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control '),
            ))
            ->add('participaInventario', null, array(
                'required' => false,
                'disabled' => true,
                'label' => '¿Es Item de Inventario?',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control ', 'checked' => 'checked'),
            ))
            ->add('latitud', null, array(
                'required' => false,
                'label' => 'Latitud',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control '),
            ))
            ->add('longitud', null, array(
                'required' => false,
                'label' => 'Longitud',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control '),
            ))
            ->add('observaciones', null, array(
                'required' => false,
                'label' => 'Observaciones',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control '),
            ))
            ->add('ultimoRelevamiento', null, array(
                'required' => false,
                'label' => 'Ultimo Relevamiento',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control  number '),
            ))
            ->add('esActivoFijo', null, array(
                'required' => false,
                'disabled' => true,
                'label' => '¿Es Activo Fijo?',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control ', 'checked' => 'checked'),
            ))
            ->add('estaSujetoImpuestos', null, array(
                'required' => false,
                'label' => '¿Sujeto a Impuestos?',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control '),
            ))
            ->add('valorOrigen', null, array(
                'required' => false,
                'label' => 'Valor Origen',
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
                'label' => 'Método de Amortización',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control '),
            ))
            ->add('vidaUtil', null, array(
                'required' => false,
                'label' => 'Vida Útil',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control '),
            ))
            ->add('operador', 'entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\Operador',
                'label' => 'Operador',
                'read_only' => $options['esTramoIntermedio'],
                'empty_value' => '-- Operador --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('o')
                        ->orderBy('o.denominacion','asc');
                },
                'attr' => array('class' => ' form-control choice '),
            ))
            ->add('linea', 'entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\Linea',
                'label' => 'Línea',
                'read_only' => $options['esTramoIntermedio'],
                'empty_value' => '-- Línea --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->orderBy('l.denominacion','asc');
                },
                'attr' => array('class' => ' form-control choice '),
            ))
            ->add('corredor', 'entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\Corredor',
                'label' => 'Corredor',
                'read_only' => $options['esTramoIntermedio'],
                'empty_value' => '-- Corredor --',
                'attr' => array('class' => ' form-control choice '),
            ))
            ->add('division', 'entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\Divisiones',
                'label' => 'División',
                'read_only' => $options['esTramoIntermedio'],
                'empty_value' => '-- División --',
                'attr' => array('class' => ' form-control choice '),
            ))
            ->add('ramal', 'entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\Ramal',
                'required' => false,
                'empty_value' => '-- Ramal --',
                'label' => 'Ramal',
                'attr' => array('class' => ' form-control choice '),
            ))
            ->add('categoria', 'entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\Categorizacion',
                'label' => 'Categoría',
                'empty_value' => '-- Categoría --',
                'attr' => array('class' => ' form-control choice '),
            ))
            ->add('estadoConservacion', 'entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\EstadoConservacion',
                'label' => 'Estado de Conservación',
                'empty_value' => '-- Estado de Conservación --',
                'required' => false,
                'attr' => array('class' => ' form-control choice '),
                'query_builder' => function (EntityRepository $er) use ($where) {
                    return $er->createQueryBuilder('u')
                        ->where($where);
                },
            ))
            ->add('tipoVia', 'entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\TipoVia',
                'label' => 'Tipo de Vía',
                'empty_value' => '-- Tipo de Vía --',
                'attr' => array('class' => ' form-control choice '),
            ))
            ->add('tipoActivo', 'entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\TipoActivo',
                'label' => 'Tipo de Activo',
                'read_only' => $options['esTramoIntermedio'],
                'empty_value' => '-- Tipo de Activo --',
                'attr' => array('class' => ' form-control choice '),
            ))
            ->add('tipoServicio', 'entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\TipoServicio',
                'label' => 'Tipo de Servicio',
                'empty_value' => '-- Tipo de Servicio --',
                'attr' => array('class' => ' form-control choice '),
            ))
            ->add('estadoInventario', null, array(
                'label' => 'Estado',
                'required' => false,
                'disabled' => true,
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control'),
            ))
            ->add('estacion', 'entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\Estacion',
                'label' => 'Estación',
                'query_builder' => function(EntityRepository $er) {
                     return $er->createQueryBuilder('e')
                         ->orderBy('e.denominacion', 'ASC');
                 },
                'required' => false,
                'empty_value' => '-- Estación --',
                'attr' => array('class' => ' form-control choice '),
            ))
            ->add('valoresAtributo', 'collection', array(
                'type' => new ValoresAtributoCollectionType(),
                'label' => 'Valor de Atributo',
                'allow_delete' => true,
                'allow_add' => true,
                'by_reference' => false,
                'prototype_name' => '__atributo_activo_lineal__'
            ))
            ->add('valoresPropiedad', 'collection', array(
                'type' => new PropiedadValorCollectionType($options['where']),
                'label' => 'Valor de Propiedad',
                'allow_delete' => true,
                'allow_add' => true,
                'by_reference' => false,
                'prototype_name' => '__propiedad_activo_lineal__',
            ))
            ->add('fotos', 'collection', array(
                'type' => new InventarioFotoArchivoType(),
                'allow_delete' => true,
                'allow_add' => true,
                'label' => 'Fotos',
                'prototype_name' => '__fotos__',
                'label_attr' => array('class' => 'hidden'),
                'attr' => array('class' => 'hidden')
            ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options) {

        usort($view['valoresAtributo']->children, function (FormView $a, FormView $b) {

            $idA = $a['atributo']->vars['data'];
            $idB = $b['atributo']->vars['data'];

            if ($idA == $idB) {
                return 0;
            }

            return ($idA > $idB) ? -1 : 1; //Orden Descendiente
        });
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\InventarioBundle\Entity\ActivoLineal',
            'where' => null,
            'esTramoIntermedio' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_inventariobundle_activolineal';
    }

}
