<?php

namespace ADIF\InventarioBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActivoLinealSepararType extends AbstractType {

    /**
     * Variable para definir las propiedades a mostrar según
     * el ABMC que la utilice
     *
     * @var string
     */
    private $where;

    public function __construct($where = null) {
        $this->where = $where;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

         //Primero busco el where en las opciones del builder, sino lo busco en el constructor:
        $where = (isset($this->where)) ? 'u.habilitado' . $this->where . ' = 1' : '1 = 1';

        $builder
                ->add('progresivaInicioTramo', 'number', array(
                    'required' => true,
                    'grouping' => true,
                    'precision' => 3,
                    'read_only' => true,
                    'label' => 'Progresiva Inicio Tramo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))

                ->add('progresivaFinalTramo', 'number', array(
                    'required' => true,
                    'grouping' => true,
                    'precision' => 3,
                    'read_only' => true,
                    'label' => 'Progresiva Final Tramo',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),

                ))->add('estadoConservacion', 'entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\EstadoConservacion',
                    'label' => 'Estado de Conservación',
                    'empty_value' => '-- Estado de Conservación --',
                    'required' => false,
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function (EntityRepository $er) use ($where) {
                        return $er->createQueryBuilder('u')
                                ->where($where);
                    },
                ))->add('valoresAtributo', 'collection', array(
                    'type' => new ValoresAtributoCollectionType(),
                    'label' => 'Valores de Atributo',
                    'by_reference' => false,
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'where' => null,
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_inventariobundle_activolineal_separar_tramo';
    }

}
