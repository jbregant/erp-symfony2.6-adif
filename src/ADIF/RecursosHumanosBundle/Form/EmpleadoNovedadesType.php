<?php

namespace ADIF\RecursosHumanosBundle\Form;

use ADIF\RecursosHumanosBundle\Entity\Empleado;
use ADIF\RecursosHumanosBundle\Entity\TipoLiquidacion;
use ADIF\RecursosHumanosBundle\Repository\ConceptosRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class EmpleadoNovedadesType extends AbstractType {

    /**
     */
    function __construct(Empleado $empleado) {
        $this->convenio = $empleado->getConvenio();
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('idConcepto', EntityType::clase, array(
                'class' => 'ADIF\RecursosHumanosBundle\Entity\Concepto',
                'required' => true,
                'label' => 'Novedad',
                'attr' => array('class' => ' form-control choice  '),
                'empty_value' => '-- Elija una Novedad --',
                'property' => 'nombreCodigo',
                'em' => $options['entity_manager'],
                'query_builder' => function(ConceptosRepository $er) {
                    return $er->findAllNovedadesByConvenio($this->convenio);
                }))
            ->add('fechaAlta', 'date', array(
                'required' => true,
                'label' => 'Fecha',
                'attr' => array('class' => ' form-control  datepicker '), 'widget' => 'single_text',
                'format' => 'dd/MM/yyyy'))
            ->add('valor', null, array(
                'required' => true,
                'label' => 'Valor',
                'attr' => array('class' => ' form-control currency')))
            ->add('dias', null, array(
                'required' => false,
                'label' => 'D&iacute;as',
                'attr' => array('class' => ' form-control  number ')))
            ->add('liquidacionAjuste', EntityType::clase, array(
                'class' => 'ADIF\RecursosHumanosBundle\Entity\Liquidacion',
                'required' => false,
                'label' => 'Liquidaci&oacute;n aplica',
                'attr' => array('class' => ' form-control choice  '),
                'empty_value' => '-- Elija una Liquidaci&oacute;n --',
                'em' => $options['entity_manager'],
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('l')                        
                        ->where('l.tipoLiquidacion = :tipoLiquidacion')->setParameter('tipoLiquidacion',TipoLiquidacion::__HABITUAL)
                        ->orderBy('l.fechaCierreNovedades', 'DESC');
                }))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\EmpleadoNovedad'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_empleado_novedades';
    }

}
