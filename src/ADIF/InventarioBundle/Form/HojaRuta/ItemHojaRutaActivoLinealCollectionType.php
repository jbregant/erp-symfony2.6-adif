<?php
namespace ADIF\InventarioBundle\Form\HojaRuta;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ADIF\InventarioBundle\Form\HojaRuta\DataTransformer\EntityToNumberTransformer;
use Doctrine\ORM\EntityManager;

class ItemHojaRutaActivoLinealCollectionType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder            
            ->add('itemRelevado', null, array(
                'value' => 0,
                'label' => 'Item Relevado',      
            ))
            ->add('idEmpresa', null, array(
                'data' => 1,
            ))
            ->add('observacion', null, array(
                'required' => false,
                'label' => 'Observación',                    
            ))              
            ->add('linea','entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\Linea',
            ))                
            ->add('operador','entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\Operador',
            ))                
            ->add('division','entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\Divisiones',
            ))                
            ->add('tipoActivo','entity', array(
                'class' => 'ADIF\InventarioBundle\Entity\TipoActivo',
            ))                             
            ->add('activoLineal','text', array(
                'required' => false,
            ));
        
        $builder->get('activoLineal')
            ->addModelTransformer(new EntityToNumberTransformer($this->entityManager,'ActivoLineal'));
        
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $item = $event->getData();
            $form = $event->getForm();

            $attrs = array(
                'required' => false,
                'mapped' => false,
                'grouping' => true,
                'precision' => 3,
            );
            $inicio = $final = $attrs;
            if($item){
                $activoLineal = $item->getActivoLineal();
                if($activoLineal){
                    $inicio['data'] = $activoLineal->getProgresivaInicioTramo();
                    $final['data'] = $activoLineal->getProgresivaFinalTramo();
                }
            }
            $form->add('progresivaInicioTramo','number', $inicio)        
                ->add('progresivaFinalTramo','number', $final);
            
        });
    
    }
    
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\InventarioBundle\Entity\ItemHojaRutaActivoLineal'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_itemhojaruta_activolineal';
    }
}
