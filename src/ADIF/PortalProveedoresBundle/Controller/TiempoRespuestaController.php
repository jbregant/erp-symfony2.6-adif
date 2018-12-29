<?php

namespace ADIF\PortalProveedoresBundle\Controller;

use ADIF\PortalProveedoresBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\PortalProveedoresBundle\Entity\TiempoRespuesta;
use ADIF\PortalProveedoresBundle\Form\FechaDesdeHastaType;

/**
 * TiempoRespuesta controller.
 *
 * @Route("/tiemporespuesta")
  */
class TiempoRespuestaController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Tiempos de Respuesta' => $this->generateUrl('tiemporespuesta')
        );
    }
    /**
     * Lists all TiempoRespuesta entities.
     *
     * @Route("/", name="tiemporespuesta")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Tiempo de Respuesta'] = null;
        
        $form   = $this->makeForm();
        
        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Tiempo de Respuesta',
            'page_info' => 'Lista de Tiempos de Respuesta', 
            'form' => $form->createView(),
        );
    }

    /**
     * Tabla para TiempoRespuesta .
     *
     * @Route("/index_table/", name="tiemporespuesta_table")
     * @Method("GET|POST")
     */
    public function indexTableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        
        $desde = $request->query->get('fechaInicio');
        $hasta = $request->query->get('fechaFin');
        $tAccion = (int)$request->query->get('tAccion');

        $entities = $em->getRepository('ADIFPortalProveedoresBundle:TiempoRespuesta')->getTiempoRespuestaPorFecha($desde, $hasta, $tAccion);
        
        $bread = $this->base_breadcrumbs;
        $bread['Tiempo de Respuesta'] = null;

        return $this->render('ADIFPortalProveedoresBundle:TiempoRespuesta:index_table.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a TiempoRespuesta entity.
     *
     * @Route("/{id}", name="tiemporespuesta_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFPortalProveedoresBundle:TiempoRespuesta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TiempoRespuesta.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['TiempoRespuesta'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver TiempoRespuesta'
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
            'action' => $this->generateUrl('tiemporespuesta_table'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Filtrar'));

        return $form;
    }
}
