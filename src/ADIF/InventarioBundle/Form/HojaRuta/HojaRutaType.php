<?php
namespace ADIF\InventarioBundle\Form\HojaRuta;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class HojaRutaType extends AbstractType
{
    protected $entityManager;

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
                ->add('denominacion', null, array(
                    'required' => true,
                    'label' => 'Denominación',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ')))

                ->add('usuarioAsignado', 'entity', array(
                    'required' => true,
                    'label' => 'Usuario',
                    'class' => 'ADIF\AutenticacionBundle\Entity\Usuario',
                    'empty_value' => '-- Usuario --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice')))

                ->add('fechaVencimiento', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de Vencimiento',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy'))

//                ->add('tipoMaterial','entity', array(
//                    'label' => 'Tipo Material',
//                    'label_attr' => array('class' => 'control-label'),
//                    'class' => 'ADIF\InventarioBundle\Entity\TipoMaterial',
//                    'attr' => array('class' => ' form-control choice '),
//                ))
//                ->add('estadoHojaRuta','entity', array(
//                    'label' => 'Estado',
//                    'label_attr' => array('class' => 'control-label'),
//                    'class' => 'ADIF\InventarioBundle\Entity\EstadoHojaRuta',
//                    'attr' => array('class' => ' form-control choice '),
//                ))
                ->add('esInspeccionTecnica', null, array(
                    'required' => false,
                    'disabled' => false,
                    'label' => '¿Es Inspección Técnica ?',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control ', 'checked' => 'checked')))

                ->add('tipoRelevamiento', 'entity', array(
                    'required' => false,
                    'label' => 'Tipo de Relevamiento',
                    'class' => 'ADIF\InventarioBundle\Entity\TipoRelevamiento',
                    'empty_value' => '-- Tipo de Relevamiento --',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control choice')))

                ->add('levantamiento', 'entity', array(
                      'class' => 'ADIF\InventarioBundle\Entity\HojaRuta',
                      'label' => 'Planilla de Levantamiento',
                      'empty_value' => '-- Planilla de Levantamiento --',
                      'required' => false,
                      'mapped' => false,
                      'query_builder' => function(EntityRepository $repository) {
                          $qb = $repository->createQueryBuilder('u');
                          return $qb
                          ->where('u.tipoMaterial = :tipo_m and u.fechaBaja is null
                                   and u.tipoRelevamiento = :tipo_r and u.estadoHojaRuta = :estado_hr')
                          ->setParameter('tipo_m', '1')
                          ->setParameter('tipo_r', '1')
                          ->setParameter('estado_hr', '3')
                          ->orderBy('u.denominacion', 'ASC');
                      },
                      'attr' => array('class' => ' form-control choice ')));
    }
    /**
    * @param OptionsResolverInterface $resolver
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\InventarioBundle\Entity\HojaRuta'
        ));
    }

    /**
    * @return string
    */
    public function getName() {
        return 'adif_inventariobundle_hojaruta';
    }
}
