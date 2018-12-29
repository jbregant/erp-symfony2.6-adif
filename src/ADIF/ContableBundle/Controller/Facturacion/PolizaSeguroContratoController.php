<?php

namespace ADIF\ContableBundle\Controller\Facturacion;

use ADIF\ContableBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Facturacion\PolizaSeguroContrato controller.
 *
 * @Route("/polizacontrato")
 */
class PolizaSeguroContratoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'P&oacute;lizas' => $this->generateUrl('polizacontrato')
        );
    }

    /**
     * Lists all Facturacion\PolizaSeguroContrato entities.
     *
     * @Route("/", name="polizacontrato")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_VISUALIZAR_POLIZAS')")
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['P&oacute;lizas'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'P&oacute;lizas',
            'page_info' => 'Lista de p&oacute;lizas'
        );
    }

    /**
     * Tabla para Facturacion\PolizaSeguroContrato .
     *
     * @Route("/index_table/", name="polizacontrato_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Facturacion\PolizaSeguroContrato')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['P&oacute;lizas'] = null;

        return $this->render('ADIFContableBundle:Facturacion/PolizaSeguroContrato:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Finds and displays a Facturacion\PolizaSeguroContrato entity.
     *
     * @Route("/{id}", name="polizacontrato_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_VISUALIZAR_POLIZAS')")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:Facturacion\PolizaSeguroContrato')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Facturacion\PolizaSeguroContrato.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['P&oacute;liza'] = null;

        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver p&oacute;liza'
        );
    }

}
