<?php

namespace ADIF\PortalProveedoresBundle\Controller;

use ADIF\PortalProveedoresBundle\Controller\BaseController;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\PortalProveedoresBundle\Entity\Usuario;

/**
 * Usuario controller.
 *
 * @Route("/portal/usuarios")
  */
class UsuarioController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Usuario' => $this->generateUrl('portal_usuarios')
        );
    }
    /**
     * Lists all Usuario entities.
     *
     * @Route("/", name="portal_usuarios")
     * @Method("GET")
     * @Template("ADIFPortalProveedoresBundle:Usuario:index.html.twig")
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Usuario'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Usuarios',
            'page_info' => 'Lista de usuarios'
        );
    }

    /**
     * Tabla para Usuario .
     *
     * @Route("/index_table/", name="portal_usuarios_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFPortalProveedoresBundle:Usuario')->getAllWithStatus();
        
        $bread = $this->base_breadcrumbs;
        $bread['Usuario'] = null;

    return $this->render('ADIFPortalProveedoresBundle:Usuario:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Finds and displays a Usuario entity.
     *
     * @Route("/{id}", name="portal_usuarios_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFPortalProveedoresBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Usuario.');
        }
        
        $bread = $this->base_breadcrumbs;
        $bread['Usuario'] = null;
        

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Usuario'
        );
    }
}
