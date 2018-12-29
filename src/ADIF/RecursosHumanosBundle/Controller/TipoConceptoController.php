<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\RecursosHumanosBundle\Entity\TipoConcepto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * TipoConcepto controller.
 *
 * @Route("/tipos_concepto")
 * @Security("has_role('ROLE_RRHH_CONFIGURACION')")
 */
class TipoConceptoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'TipoConcepto' => $this->generateUrl('tiposconcepto')
        );
    }

    /**
     * Lists all TipoConcepto entities.
     *
     * @Route("/", name="tiposconcepto")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:TipoConcepto')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['TipoConcepto'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Tipos de conceptos',
            'page_info' => 'Lista de tipos de conceptos'
        );
    }

    /**
     * Finds and displays a TipoConcepto entity.
     *
     * @Route("/{id}", name="tiposconcepto_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:TipoConcepto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoConcepto.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['TipoConcepto'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver TipoConcepto'
        );
    }

    /**
     * @Route("/lista_tipos_concepto", name="lista_tipos_concepto")
     * @Security("has_role('ROLE_USER')")
     */
    public function listaTiposConceptoAction() {
        $repository = $this->getDoctrine()->getRepository('ADIFRecursosHumanosBundle:TipoConcepto', $this->getEntityManager());

        $query = $repository->createQueryBuilder('t')
                ->select('t.id', 't.nombre')
                ->orderBy('t.nombre', 'ASC')
                ->getQuery();

        return new \Symfony\Component\HttpFoundation\JsonResponse($query->getResult());
    }

}
