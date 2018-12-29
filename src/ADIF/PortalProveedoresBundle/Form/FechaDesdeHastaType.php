<?php
namespace ADIF\PortalProveedoresBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FechaDesdeHastaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('fechaDesde', 'date', array(
                'required' => true,
                'label' => 'Fecha Desde',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control  datepicker '),
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy'
            ))
            ->add('fechaHasta', 'date', array(
                'required' => true,
                'label' => 'Fecha Hasta',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control  datepicker '),
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy'
            ))
            ->add('fAcciones', 'entity', array(
                'required' => false,
                'label' => 'Acciones',
                'class' => 'ADIF\PortalProveedoresBundle\Entity\TipoAccion',
            ));
    }


    /**
     * @return string
     */
    public function getName() {
        return 'adif_portalproveedoresbundle_desde_hasta_form';
    }

    /**
     * add 'Seleccione una accion' row to combobox
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $newChoice = new ChoiceView(array(), '0', 'Seleccione una accion');
        array_unshift($view->children['fAcciones']->vars['choices'], $newChoice);
    }
}
