<?php

namespace ADIF\InventarioBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PropiedadValorType extends AbstractType {

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
        $where = (isset($options['where'])) ? 'u.habilitado' . $options['where'] . ' = 1' : ((isset($this->where)) ? 'u.habilitado' . $this->where . ' = 1' : '1 = 1');

        $builder
                ->add('valor', null, array(
                    'required' => true,
                    'label' => 'Valor',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '),))
                ->add('idPropiedad', 'entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Propiedades',
                    'label' => 'Propiedad',
                    'query_builder' => function (EntityRepository $er) use ($where) {
                        return $er->createQueryBuilder('u')
                                ->where($where);
                    },
                    'attr' => array('class' => ' form-control choice '),));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\InventarioBundle\Entity\PropiedadValor'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_inventariobundle_propiedadvalor';
    }

}
