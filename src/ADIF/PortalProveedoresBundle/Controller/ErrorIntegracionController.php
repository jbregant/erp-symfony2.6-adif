<?php

namespace ADIF\PortalProveedoresBundle\Controller;

use ADIF\PortalProveedoresBundle\Controller\BaseController;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\PortalProveedoresBundle\Form\FechaDesdeHastaType;

/**
 * ErrorIntegracion controller.
 *
 * @Route("/errorintegracion")
  */
class ErrorIntegracionController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Errores de Integración' => $this->generateUrl('errorintegracion')
        );
    }
    /**
     * Lists all ErrorIntegracion entities.
     *
     * @Route("/", name="errorintegracion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Error de Integración'] = null;
        
        $form   = $this->makeForm();

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Errores de Integración',
            'page_info' => 'Lista de Errores de Integración',
            'form'   => $form->createView(),
        );
    }

    /**
     * Tabla para ErrorIntegracion .
     *
     * @Route("/index_table/", name="errorintegracion_table")
     * @Method("GET|POST")
     */
    public function indexTableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $desde = $request->query->get('fechaInicio');
        $hasta = $request->query->get('fechaFin');

        $entities = $em->getRepository('ADIFPortalProveedoresBundle:ProveedorEvaluacionLog')->getErroresPorFecha($desde, $hasta);

        $bread = $this->base_breadcrumbs;
        $bread['Error de Integración'] = null;

        return $this->render('ADIFPortalProveedoresBundle:ErrorIntegracion:index_table.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a ErrorIntegracion entity.
     *
     * @Route("/{id}", name="errorintegracion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFPortalProveedoresBundle:ProveedorEvaluacionLog')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ErrorIntegracion.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['Error de Integración'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Error de Integración'
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
            'action' => $this->generateUrl('errorintegracion_table'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Filtrar'));

        return $form;
    }
}
