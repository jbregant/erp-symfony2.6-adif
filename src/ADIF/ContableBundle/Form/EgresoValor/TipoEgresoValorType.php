<?php

namespace ADIF\ContableBundle\Form\EgresoValor;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class TipoEgresoValorType extends AbstractType {

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
                    'attr' => array('class' => ' form-control '))
                )
                ->add('descripcion', null, array(
                    'required' => false,
                    'label' => 'Descripción',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('permiteReposicion', null, array(
                    'required' => false,
                    'label' => 'Permite reposición',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
                ->add('cuentaContable', EntityType::clase, array(
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'],
					'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('cc')
								->where('cc.activa = 1')
								->andWhere('cc.esImputable = 1')
								->orderBy('cc.codigoCuentaContable', 'ASC');
					}
                ))
				->add('cuentaContablReconocimiento', EntityType::clase, array(
					'required' => false,
					'label' => 'Cuenta contable reconocimiento',
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'attr' => array('class' => ' form-control choice '),
                    'em' => $options['entity_manager'],
					'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('cc')
								->where('cc.activa = 1')
								->andWhere('cc.esImputable = 1')
								->orderBy('cc.codigoCuentaContable', 'ASC');
					}
                ))
				->add('cuentaContablGanancia', EntityType::clase, array(
					'required' => false,
					'label' => 'Cuenta contable ganancia',
                    'class' => 'ADIF\ContableBundle\Entity\CuentaContable',
                    'em' => $options['entity_manager'],
                    'attr' => array('class' => ' form-control choice '),
					'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('cc')
								->where('cc.activa = 1')
								->andWhere('cc.esImputable = 1')
								->orderBy('cc.codigoCuentaContable', 'ASC');
					}
                ))
				->add('limitaPersona', null, array(
                    'required' => false,
                    'label' => 'Limita persona',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
				->add('limitaGerencia', null, array(
                    'required' => false,
                    'label' => 'Limita gerencia',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
				->add('cantidadMaxima', null, array(
                    'required' => false,
                    'label' => 'Cantidad m&aacute;xima',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
				->add('maximoComprobante', null, array(
                    'required' => false,
                    'label' => 'M&aacute;ximo por comprobante (tope)',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
				->add('minimoRendicion', null, array(
                    'required' => false,
                    'label' => 'M&iacute;nimo rendici&oacute;n',
                    'label_attr' => array('class' => 'control-label'),
                    'attr' => array('class' => ' form-control '))
                )
				
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ADIF\ContableBundle\Entity\EgresoValor\TipoEgresoValor'
        ));
        $resolver->setRequired('entity_manager');
    }

    /**
     * @return string
     */
    public function getName() {
        return 'adif_contablebundle_egresovalor_tipoegresovalor';
    }

}
