<?php

namespace ADIF\PortalProveedoresBundle\Controller;

use ADIF\PortalProveedoresBundle\Controller\BaseController;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\PortalProveedoresBundle\Entity\Visita;
use ADIF\PortalProveedoresBundle\Form\FechaDesdeHastaType;


/**
 * Visita controller.
 *
 * @Route("/visitas")
 */
class VisitaController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Visita' => $this->generateUrl('visitas')
        );
    }
    /**
     * Lists all Visita entities.
     *
     * @Route("/", name="visitas")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Visita'] = null;

        $form   = $this->makeForm();

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Visita',
            'page_info' => 'Lista de visita',
            'form'   => $form->createView(),
        );
    }

    /**
     * Tabla para Visita .
     *
     * @Route("/index_table/", name="visitas_table")
     * @Method("GET|POST")
     */
    public function indexTableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $desde = $request->request->get('fDesde');
        $hasta = $request->request->get('fHasta');
        $busquedaEstrictaPorUser = $request->request->get('chkStrictBtn');

        if(!empty($desde) && !empty($hasta)){
            $entities = $em->getRepository('ADIFPortalProveedoresBundle:Visita')->getVisitasPorFecha($desde, $hasta, $busquedaEstrictaPorUser);
            $bread = $this->base_breadcrumbs;
            $bread['Visita'] = null;

            return $this->render('ADIFPortalProveedoresBundle:Visita:index_table.html.twig', array(
                    'entities' => $entities
                )
            );
        }
    }

    /**
     * Datos de visitas para el grafico.
     *
     * @Route("/index_chart/", name="visitas_chart")
     * @Method("GET|POST")
     */
    public function indexChartAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $desde = $request->request->get('fDesde');
        $hasta = $request->request->get('fHasta');
        $busquedaEstrictaPorUser = $request->request->get('chkStrictBtn');


        $entities = $em->getRepository('ADIFPortalProveedoresBundle:Visita')->getVisitasPorHora($desde, $hasta, $busquedaEstrictaPorUser);
        $bread = $this->base_breadcrumbs;
        $bread['Visita'] = null;

        return $this->render('ADIFPortalProveedoresBundle:Visita:index_table.html.twig', array(
                'entities' => $entities
            )
        );
    }

    /**
     * Finds and displays a Visita entity.
     *
     * @Route("/{id}", name="visitas_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFPortalProveedoresBundle:Visita')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Visita.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Visita'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Visita'
        );
    }


    /**
     * Creates a form for filtering visits by date.
     *
     * @param Notificacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function makeForm()
    {
        $form = $this->createForm(new FechaDesdeHastaType(), null, array(
            'action' => $this->generateUrl('visitas_table'),
            'method' => 'POST',
        ));
        $form->add('chkStrict', 'checkbox', array(
            'required' => false,
            'label' => 'Busqueda unitaria por usuario',
//            'label_attr' => array('class' => 'control-label'),
            'attr' => array('class' => 'checkbox-strict'),
        ));
        $form->add('submit', 'submit', array('label' => 'Filtrar'));

        return $form;
    }
}
