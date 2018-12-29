<?php
namespace ADIF\InventarioBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ValoresAtributoCollectionType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('atributo','entity', array(
            'class' => 'ADIF\InventarioBundle\Entity\Atributo',
            'attr' => array('class' => ' form-control choice atributo'),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('o')
                    ->orderBy('o.denominacion','asc');
            },
            'empty_value' => '-- Atributo --',
        ));
        
        $formModifier = function (FormInterface $form, $data = null, $atributo = null) {
            
            $whereAtributo = null === $data ? ( null === $atributo ? '1' : $atributo->getId() ) : $data->getAtributo()->getId();
            
            $form->add('valoresAtributo','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\ValoresAtributo',
                    'mapped' => false,
                    'empty_value' => '-- Valor --',
                    'attr' => array('class' => ' form-control choice '),
                    'query_builder' => function(EntityRepository $er) use ($whereAtributo){
                        return $er->createQueryBuilder('c') //Busco los ValoresAtributo correspondientes al atributo
                            ->where('c.atributo = ' . $whereAtributo)
                            ->orderBy('c.denominacion');
                },
                'data' => $data //Setteo la entity ValorAtributo para el editar
            ));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                //Le paso la entity ValorAtributo
                $data = $event->getData();
                
                $formModifier($event->getForm(), $data);
            }
        );

        $builder->get('atributo')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                // Envío el data del padre para enviar la entity ValorAtributo
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
            'data_class' => 'ADIF\InventarioBundle\Entity\ValoresAtributo'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_valoresatributo';
    }
}
