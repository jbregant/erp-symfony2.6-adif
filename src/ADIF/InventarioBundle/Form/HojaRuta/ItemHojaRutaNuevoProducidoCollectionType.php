<?php
namespace ADIF\InventarioBundle\Form\HojaRuta;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ItemHojaRutaNuevoProducidoCollectionType extends AbstractType
{
        /**
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
//                ->add('id', null, array(
//                    'label' => 'id',                 
//                ))
                ->add('idEmpresa', null, array(
                    'data' => 1,
                ))
                ->add('itemRelevado', null, array(
                    'label' => 'Item Relevado',                
                ))
                ->add('observacion', null, array(
                    'required' => false,
                    'label' => 'Observación',                    
                ))  
                ->add('provincia','entity', array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Provincia',
                ))
                ->add('linea','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Linea',
                ))
                ->add('almacen','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\Almacen',
                ))
                ->add('tipoMaterial','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\TipoMaterial',
                ))
                ->add('grupoMaterial','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\GrupoMAterial',
                ))
                ->add('estadoConservacion','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\EstadoConservacion',
                ))
                ->add('inventario','entity', array(
                    'class' => 'ADIF\InventarioBundle\Entity\InventarioMatNuevoProducido',
                ))
                ;
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\InventarioBundle\Entity\ItemHojaRutaNuevoProducido'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_itemhojaruta_nuevoproducido';
    }
}
