<?php

namespace ADIF\AutenticacionBundle\Controller;

use ADIF\AutenticacionBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\AutenticacionBundle\Entity\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Logger controller.
 *
 * @Route("/auditoria")
 * @Security("has_role('ROLE_ADMIN')")
 */
class LoggerController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {

        parent::setContainer($container);

        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Auditoría' => $this->generateUrl('auditoria')
        );
    }

    /**
     * Lists all Logger entities.
     *
     * @Route("/", name="auditoria")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFAutenticacionBundle:Logger')
                ->findBy(array(), array('fecha' => 'DESC'), 500, 0);

        $bread = $this->base_breadcrumbs;
        $bread['Auditoría'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Logs',
            'page_info' => 'Lista de logs'
        );
    }

    /**
     * Finds and displays a Logger entity.
     *
     * @Route("/{id}", name="auditoria_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFAutenticacionBundle:Logger')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Logger.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Auditoría'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Log'
        );
    }

}
