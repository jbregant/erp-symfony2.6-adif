<?php
namespace ADIF\InventarioBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PropiedadValorCollectionType extends AbstractType
{
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
        $where = (isset($options['where']))?'u.habilitado'.$options['where'].' = 1':((isset($this->where))?'u.habilitado'.$this->where.' = 1':'1 = 1');

        $builder
                ->add('idPropiedad', 'entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Propiedades',
                    'label' => 'Propiedad',
                    'empty_value' => '-- Propiedad --',
                    'mapped' => false,
                    'query_builder' => function (EntityRepository $er) use ($where) {
                        return $er->createQueryBuilder('u')
                            ->where($where);
                    },
                    'attr' => array('class' => ' form-control choice propiedad'),));

        $formModifier = function (FormInterface $form, $data = null, $idPropiedad = null) {

            $wherePropiedad = null === $data ? ( null === $idPropiedad ? '1' : $idPropiedad->getId() ) : $data->getIdPropiedad()->getId();
            
            $form->add('propiedadValor','entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\PropiedadValor',
                'mapped' => false,
                'empty_value' => '-- Valor --',
                'attr' => array('class' => ' form-control choice propiedadValor', 'idPropiedad' => $wherePropiedad),
                'query_builder' => function(EntityRepository $er) use ($wherePropiedad){
                    return $er->createQueryBuilder('c') //Busco los ValoresPropiedad correspondientes a la Propiedad
                        ->where('c.idPropiedad = ' . $wherePropiedad);
                },
                'data' => $data, //Setteo la entity PropiedadValor para el editar
            ));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                //Le paso la entity PropiedadValor
                $data = $event->getData();

                $formModifier($event->getForm(), $data);
            }
        );

        $builder->get('idPropiedad')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                // Envío el data del padre para enviar la entity PropiedadValor
                $data = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), null, $data);
            }
        );
    }

    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\InventarioBundle\Entity\PropiedadValor',
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_valorespropiedad';
    }
}
