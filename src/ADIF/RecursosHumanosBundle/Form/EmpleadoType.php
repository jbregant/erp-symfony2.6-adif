<?php

namespace ADIF\RecursosHumanosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class EmpleadoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->add('nroLegajo', null, array(
                    'required' => true,
                    'label' => 'Nrolegajo',
                    'attr' => array('class' => ' form-control  number '),))
                ->add('fechaInicioAntiguedad', 'date', array(
                    'required' => true,
                    'label' => 'Fecha de inicio de antiguedad',
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy'))
                ->add('idGerencia', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Gerencia',
                    'label' => 'Gerencia',
                    'required' => true,
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '),
                    'empty_value' => '-- Indefinido --',
                    'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('g')
								->orderBy('g.nombre', 'ASC');
					}
                ))
                ->add('idSubgerencia', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Subgerencia',
                    'label' => 'Subgerencia',
                    'required' => true,
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '),
                    'empty_value' => '-- Indefinido --',
                    'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('s')
								->orderBy('s.nombre', 'ASC');
					}
                ))
                ->add('idArea', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Area',
                    'label' => 'Area',
                    'required' => true,
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '),
                    'empty_value' => '-- Indefinido --',
                    'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('a')
								->orderBy('a.nombre', 'ASC');
					}
                ))
                ->add('idSector', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Sector',
                    'label' => 'Sector',
                    'required' => false,
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '),
                    'empty_value' => '-- Indefinido --',
                    'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('s')
								->orderBy('s.nombre', 'ASC');
					}
                ))
                ->add('idConvenio', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Convenio',
                    'attr' => array('class' => ' form-control choice '),
                    'required' => true,
                    'label' => 'Convenio',
                    'em' => $options['entity_manager'],
                    'mapped' => false,
                    'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('c')
								->orderBy('c.nombre', 'ASC');
					}
                ))
                ->add('idCategoria', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Categoria',
                    'attr' => array('class' => ' form-control choice '),
                    'label' => 'Categor&iacute;a',
                    'required' => true,
                    'em' => $options['entity_manager'],
                    'mapped' => false))
                ->add('idSubcategoria', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Subcategoria',
                    'label' => 'Subcategor&iacute;a',
                    'em' => $options['entity_manager'],
                    'required' => true,
                    'attr' => array('class' => ' form-control choice '))
                )
                ->add('fechaEgreso', 'date', array(
                    'required' => false,
                    'label' => 'Fecha de egreso',
                    'attr' => array('class' => ' form-control  datepicker '),
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy')
                )
                ->add('aplicaEscalaDiciembre', null, array(
                    'required' => false,
                    'label' => 'Aplica escala Diciembre',
                    'attr' => array('class' => ' form-control ignore ')
				))
				->add('puesto', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Puesto',
                    'label' => 'Puesto',
                    'em' => $options['entity_manager'],
                    'required' => false,
                    'attr' => array('class' => ' form-control choice '),
                    'empty_value' => '-- Indefinido --',
                    'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('p')
							->orderBy('p.denominacion', 'ASC');
						}
                ))
				->add('superior', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\Empleado',
                    'label' => 'Superior',
                    'em' => $options['entity_manager'],
                    'required' => false,
					'mapped' => true,
                    'attr' => array('class' => ' form-control choice '),
                    'empty_value' => '-- Indefinido --',
                    'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('e')
							->innerJoin('e.persona', 'p')
							->where('e.activo = 1')
							->orderBy('p.apellido', 'ASC');
						}
                ))
				->add('nivelOrganizacional', EntityType::clase, array(
                    'class' => 'ADIF\RecursosHumanosBundle\Entity\NivelOrganizacional',
                    'label' => 'Nivel organizacional',
                    'em' => $options['entity_manager'],
                    'required' => false,
                    'attr' => array('class' => ' form-control choice '),
                    'empty_value' => '-- Indefinido --',
                    'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('n')
							->orderBy('n.denominacion', 'ASC');
						}
                ))
				
			;

        $builder->add('acdt', null, array(
            'required' => false,
            'label' => 'A.C.D.T.',
            'attr' => array('class' => ' form-control currency')));

        $builder->add('foto', 'file', array(
            'required' => false,
            'label' => 'Foto',
			'mapped' => true,
            'data_class' => null,
            'attr' => array('class' => ' form-control ', 'tabindex' => '-1'),));
        $builder->add('persona', new PersonaType($options['entity_manager']));
        $builder->add('idCuenta', new CuentaType($options['entity_manager']));
        $builder->add('condicion', EntityType::clase, array(
            'class' => 'ADIF\RecursosHumanosBundle\Entity\Condicion',
            'label' => 'Condici&oacute;n',
            'em' => $options['entity_manager'],
            'attr' => array('class' => ' form-control choice ')
        ));
        $builder->add('tiposContrato', 'collection', array(
            'label' => 'Tipos de contrataci&oacute;n',
            'type' => new EmpleadoTipoContratoType($options['entity_manager']),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ));
        $builder->add('tiposLicencia', 'collection', array(
            'type' => new EmpleadoTipoLicenciaType($options['entity_manager']),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ));
        $builder->add('obrasSociales', 'collection', array(
            'type' => new EmpleadoObraSocialType($options['entity_manager']),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ));
        $builder->add('motivoEgreso', EntityType::clase, array(
            'class' => 'ADIF\RecursosHumanosBundle\Entity\MotivoEgreso',
            'required' => false,
            'em' => $options['entity_manager'],
            'label' => 'Motivo del egreso',
            'attr' => array(
                'class' => 'form-control choice',
                'placeholder' => 'Seleccione el motivo del egreso')
        ));

        //rango remuneración
        $builder->add('rangoRemuneracion', EntityType::clase, array(
            'class' => 'ADIF\RecursosHumanosBundle\Entity\RangoRemuneracion',
            'required' => true,
            'error_bubbling' => true,
            'em' => $options['entity_manager'],
            'label' => 'Rango de remuneraci&oacute;n',
            'attr' => array(
                'class' => 'form-control choice',
                'placeholder' => 'El rango aqu&iacute;'
            ),
			'query_builder' => function(EntityRepository $er) {
                  return $er->createQueryBuilder('rr')
					->where('rr.vigente = 1')
					->orderBy('rr.montoDesde', 'ASC');
			} 
        ));

        $builder->add('presenta649', 'checkbox', array(
            'label' => '¿Presenta formulario 649 inicial?',
            'required' => false,
            'mapped' => false
        ));

        $builder->add('formulario649', new Formulario649Type());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\RecursosHumanosBundle\Entity\Empleado',
            'cascade_validation' => true
        ));
        
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_recursoshumanosbundle_empleado';
    }

}
