<?php

namespace ADIF\ContableBundle\Controller\EgresoValor;

use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRendicionEgresoValor;
use ADIF\ContableBundle\Entity\Constantes\ConstanteJurisdiccion;
use ADIF\ContableBundle\Entity\EgresoValor\ComprobanteEgresoValor;
use ADIF\ContableBundle\Entity\EgresoValor\EgresoValor;
use ADIF\ContableBundle\Entity\EstadoComprobante;
use ADIF\ContableBundle\Form\EgresoValor\ComprobanteEgresoValorType;
use ADIF\ContableBundle\Form\EgresoValor\ComprobanteEgresoValorCreateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * ComprobanteEgresoValorController controller.
 *
 * @Route("/comprobanteegresovalor")
 */
class ComprobanteEgresoValorController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Egresos de valor' => $this->generateUrl('egresovalor')
        );
    }

    /**
     * Lists all ComprobanteEgresoValor entities.
     *
     * @Route("/", name="comprobanteegresovalor")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Comprobantes de egreso de valor'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Comprobante de egreso de valor',
            'page_info' => 'Lista de comprobantes de egreso de valor'
        );
    }

    /**
     * Creates a new ComprobanteEgresoValor entity.
     *
     * @Route("/insertar/{id}", name="comprobanteegresovalor_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:EgresoValor/ComprobanteEgresoValor:new.html.twig")
     */
    public function createAction(Request $request, $id) {

        $emContable = $this->getDoctrine()->getManager($this->getEntityManager());


        /* @var $egresoValor \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor */
        $egresoValor = $emContable->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')
                ->find($id);

        if (!$egresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        // Obtengo la rendición actual
        $rendicion = $egresoValor->getRendicionEgresoValor();

        if ($rendicion->getEstadoRendicionEgresoValor() == null) {
            $rendicion->setEstadoRendicionEgresoValor(
                    $emContable->getRepository('ADIFContableBundle:EgresoValor\EstadoRendicionEgresoValor')
                            ->findOneByCodigo(ConstanteEstadoRendicionEgresoValor::ESTADO_BORRADOR)
            );
        }

        $rendicion->setEgresoValor($egresoValor);
        $rendicion->setResponsableEgresoValor($egresoValor->getResponsableEgresoValor());



        $comprobanteEgresoValor = new ComprobanteEgresoValor();
        $comprobanteEgresoValor->setRendicionEgresoValor($rendicion);

        $form = $this->createCreateForm($comprobanteEgresoValor, $egresoValor);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $emContable = $this->getDoctrine()->getManager($this->getEntityManager());

            // Seteo el Estado
            $comprobanteEgresoValor->setEstadoComprobante($emContable->getRepository('ADIFContableBundle:EstadoComprobante')
                            ->find(EstadoComprobante::__ESTADO_INGRESADO));


            $emContable->persist($comprobanteEgresoValor);
            $emContable->flush();


            $this->get('session')->getFlashBag()
                    ->add('success', "El comprobante del proveedor "
                            . $comprobanteEgresoValor->getRazonSocial()
                            . ' - ' . $comprobanteEgresoValor->getCUIT()
                            . " se carg&oacute; con &eacute;xito");

            return $this->redirect($this->generateUrl('comprobanteegresovalor_new', array('id' => $id)));
			
        } else {
			
            $request->attributes->set('form-error', true);
			//return $this->redirect($this->generateUrl('comprobanteegresovalor_new', array('id' => $id)));
        }

        $responsable = $egresoValor->getTipoEgresoValor()->__toString()
                . ' - ' . $egresoValor->getResponsableEgresoValor()->getNombre();
		
		$denominacionJurisdiccionCABA = null;

        $jurisdiccionCABA = $emContable->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $bread = $this->base_breadcrumbs;
        $bread[$responsable] = null;
        $bread['Cargar rendici&oacute;n'] = null;

        return array(
            'entity' => $comprobanteEgresoValor,
			'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'tope' => $egresoValor->getTipoEgresoValor()->getMaximoComprobante(),
            'limiteRendicion' => ($egresoValor->getTipoEgresoValor()->getPermiteReposicion()) ? $egresoValor->getSaldo() : $egresoValor->getTipoEgresoValor()->getMaximoComprobante(),
            'egresoValor' => $egresoValor,
            'comprobantes' => $this->getComprobantes($rendicion),
            'numeroReferencia' => $rendicion->getNumeroReferencia(),
            'fechaIngresoADIF' => $rendicion->getFechaIngresoADIF(),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de egreso de valor'
        );
    }

    /**
     * Creates a form to create a ComprobanteEgresoValor entity.
     *
     * @param ComprobanteEgresoValor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ComprobanteEgresoValor $entity, EgresoValor $egresoValor) {
        $form = $this->createForm(new ComprobanteEgresoValorCreateType(), $entity, array(
            'action' => $this->generateUrl('comprobanteegresovalor_create', //
                    array('id' => $egresoValor->getId())
            ),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager-rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ComprobanteEgresoValor entity.
     *
     * @Route("/crear/{id}", name="comprobanteegresovalor_new")
     * @Method("GET")
     * @Template("ADIFContableBundle:EgresoValor/ComprobanteEgresoValor:new.html.twig")
     */
    public function newAction($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $egresoValor \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor */
        $egresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')
                ->find($id);

        if (!$egresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        // Obtengo la rendición actual
        $rendicionEgresoValor = $egresoValor->getRendicionEgresoValor();

        if ($rendicionEgresoValor->getEstadoRendicionEgresoValor() == null) {
            $rendicionEgresoValor->setEstadoRendicionEgresoValor(
                    $em->getRepository('ADIFContableBundle:EgresoValor\EstadoRendicionEgresoValor')
                            ->findOneByCodigo(ConstanteEstadoRendicionEgresoValor::ESTADO_BORRADOR)
            );
        }

        $rendicionEgresoValor->setEgresoValor($egresoValor);

        $comprobanteEgresoValor = new ComprobanteEgresoValor();
        $comprobanteEgresoValor->setRendicionEgresoValor($rendicionEgresoValor);

        $form = $this->createCreateForm($comprobanteEgresoValor, $egresoValor);

        $responsable = $egresoValor->getTipoEgresoValor()->__toString()
                . ' - ' . $egresoValor->getResponsableEgresoValor()->getNombre();

        $denominacionJurisdiccionCABA = null;

        $jurisdiccionCABA = $em->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $bread = $this->base_breadcrumbs;
        $bread[$responsable] = null;
        $bread['Cargar rendici&oacute;n'] = null;

        return array(
            'entity' => $comprobanteEgresoValor,
            'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'tope' => $egresoValor->getTipoEgresoValor()->getMaximoComprobante(),
            'limiteRendicion' => ($egresoValor->getTipoEgresoValor()->getPermiteReposicion()) ? $egresoValor->getSaldo() : $egresoValor->getTipoEgresoValor()->getMaximoComprobante(),
            'egresoValor' => $egresoValor,
            'comprobantes' => $this->getComprobantes($rendicionEgresoValor),
            'numeroReferencia' => $rendicionEgresoValor->getNumeroReferencia(),
            'fechaIngresoADIF' => $rendicionEgresoValor->getFechaIngresoADIF(),
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear comprobante de egreso de valor'
        );
    }

    /**
     * Finds and displays a ComprobanteEgresoValor entity.
     *
     * @Route("/{id}", name="comprobanteegresovalor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\ComprobanteEgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteEgresoValor.');
        }


        $bread = $this->base_breadcrumbs;
        $bread['Detalle'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver comprobante de egreso de valor'
        );
    }

    /**
     * Displays a form to edit an existing ComprobanteEgresoValor entity.
     *
     * @Route("/editar/{id}", name="comprobanteegresovalor_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:EgresoValor\ComprobanteEgresoValor:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\ComprobanteEgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteEgresoValor.');
        }

        $editForm = $this->createEditForm($entity);

        $denominacionJurisdiccionCABA = null;

        $jurisdiccionCABA = $em->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'egresoValor' => $entity->getRendicionEgresoValor()->getEgresoValor(),
            'tope' => $entity->getRendicionEgresoValor()->getEgresoValor()->getTipoEgresoValor()->getMaximoComprobante(),
            'limiteRendicion' => ($entity->getRendicionEgresoValor()->getEgresoValor()->getTipoEgresoValor()->getPermiteReposicion()) ? $entity->getRendicionEgresoValor()->getEgresoValor()->getSaldo() : $entity->getRendicionEgresoValor()->getEgresoValor()->getTipoEgresoValor()->getMaximoComprobante(),
            'comprobantes' => $this->getComprobantes($entity->getRendicionEgresoValor()),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar comprobante de egreso de valor'
        );
    }

    /**
     * Creates a form to edit a ComprobanteEgresoValor entity.
     *
     * @param ComprobanteEgresoValor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ComprobanteEgresoValor $entity) {
        $form = $this->createForm(new ComprobanteEgresoValorType(), $entity, array(
            'action' => $this->generateUrl('comprobanteegresovalor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager-rrhh' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing ComprobanteEgresoValor entity.
     *
     * @Route("/actualizar/{id}", name="comprobanteegresovalor_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:EgresoValor\ComprobanteEgresoValor:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\ComprobanteEgresoValor')->find($id);

        /* @var $entity ComprobanteEgresoValor */
        foreach ($entity->getRenglonesComprobante() as $renglon) {

            $entity->removeRenglonesComprobante($renglon);

            $em->remove($renglon);
        }

        foreach ($entity->getRenglonesPercepcion() as $renglonPercepcion) {
            $entity->removeRenglonesPercepcion($renglonPercepcion);
            $em->remove($renglonPercepcion);
        }

        foreach ($entity->getRenglonesImpuesto() as $renglonImpuesto) {
            $entity->removeRenglonesImpuesto($renglonImpuesto);
            $em->remove($renglonImpuesto);
        }

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteEgresoValor.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            foreach ($entity->getRenglonesComprobante() as $renglon) {
                $renglon->setComprobante($entity);
            }

            $em->flush();
			
			$this->get('session')->getFlashBag()
                    ->add('success', "El comprobante del proveedor "
                            . $entity->getRazonSocial()
                            . ' - ' . $entity->getCUIT()
                            . " se ha editado con &eacute;xito");

            return $this->redirect($this->generateUrl('comprobanteegresovalor_new', array('id' => $entity->getRendicionEgresoValor()->getEgresoValor()->getId())));
			
        } else {
			
            $request->attributes->set('form-error', true);
			//return $this->redirect($this->generateUrl('comprobanteegresovalor_new', array('id' => $entity->getRendicionEgresoValor()->getEgresoValor()->getId())));
        }
		
		$denominacionJurisdiccionCABA = null;

        $jurisdiccionCABA = $em->getRepository('ADIFContableBundle:Jurisdiccion')
                ->findOneByCodigo(ConstanteJurisdiccion::CODIGO_CABA);

        if ($jurisdiccionCABA) {
            $denominacionJurisdiccionCABA = $jurisdiccionCABA->getDenominacion();
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'jurisdiccionCABA' => $denominacionJurisdiccionCABA,
            'egresoValor' => $entity->getRendicionEgresoValor()->getEgresoValor(),
            'tope' => $entity->getRendicionEgresoValor()->getEgresoValor()->getTipoEgresoValor()->getMaximoComprobante(),
            'limiteRendicion' => ($entity->getRendicionEgresoValor()->getEgresoValor()->getTipoEgresoValor()->getPermiteReposicion()) ? $entity->getRendicionEgresoValor()->getEgresoValor()->getSaldo() : $entity->getRendicionEgresoValor()->getEgresoValor()->getTipoEgresoValor()->getMaximoComprobante(),
            'comprobantes' => $this->getComprobantes($entity->getRendicionEgresoValor()),
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar comprobante de egreso de valor'
        );
    }

    /**
     * Deletes a ComprobanteEgresoValor entity.
     *
     * @Route("/borrar/{id}", name="comprobanteegresovalor_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\ComprobanteEgresoValor')->find($id);

        $idEgresoValor = $entity->getRendicionEgresoValor()->getEgresoValor()->getId();

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteEgresoValor.');
        }

        $this->get('session')->getFlashBag()
                ->add('success', "El comprobante se elimin&oacute; con &eacute;xito");

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('comprobanteegresovalor_new', array('id' => $idEgresoValor)));
    }

    /**
     * Tabla para ComprobanteEgresoValor
     * .
     *
     * @Route("/index_table/", name="comprobanteegresovalor_index_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:ComprobanteEgresoValor')->findBy(
                array('ordenPago' => null));
        return $this->render('ADIFContableBundle:ComprobanteEgresoValor:index_table.html.twig', array('entities' => $entities)
        );
    }

    public function getComprobantes($rendicionEgresoValor) {
        // Obtengo los comprobantes
        $comprobantes = [];

        // Por cada ComprobanteEgresoValor
        foreach ($rendicionEgresoValor->getComprobantes() as $comprobante) {

            /* @var $comprobante \ADIF\ContableBundle\Entity\EgresoValor\ComprobanteEgresoValor */

			$totalNeto = 0;
			$totalIva = 0;
			foreach($comprobante->getRenglonesComprobante() as $renglon) {
				$totalNeto += $renglon->getMontoNeto();
				$totalIva += $renglon->getMontoIva();
			}
			
            $comprobantes[] = [
                'id' => $comprobante->getId(),
                'fecha' => $comprobante->getFechaComprobante(),
                'tipoComprobante' => $comprobante->getTipoComprobante() . ( $comprobante->getLetraComprobante() != null ? ' (' . $comprobante->getLetraComprobante() . ')' : '' ),
                'numero' => $comprobante->getNumeroCompleto(),
                'proveedor' => $comprobante->getRazonSocial() . ' - ' . $comprobante->getCUIT(),
                'conceptosEgresoValor' => $comprobante->getConceptosEgresoValor(),
                'importe' => $comprobante->getTotal(),
                'link_show' => $this->generateUrl('comprobanteegresovalor_show', array('id' => $comprobante->getId())),
                'link_edit' => $this->generateUrl('comprobanteegresovalor_edit', array('id' => $comprobante->getId())),
                'class_edit' => '',
                'link_borrar' => $this->generateUrl('comprobanteegresovalor_delete', array('id' => $comprobante->getId())),
				'totalNeto'		=> $totalNeto,
				'totalIva'		=> $totalIva
            ];
        }

        // Por cada DevolucionDinero
        foreach ($rendicionEgresoValor->getDevoluciones() as $devolucion) {

            /* @var $devolucion \ADIF\ContableBundle\Entity\EgresoValor\DevolucionDinero */

            $comprobantes[] = [
                'id' => $devolucion->getId(),
                'fecha' => $devolucion->getFechaCreacion(),
                'tipoComprobante' => 'Devoluci&oacute;n',
                'numero' => $devolucion->getNumero(),
                'proveedor' => '-',
                'importe' => $devolucion->getMontoDevolucion(),
                'link_show' => $this->generateUrl('egresovalor_devoluciondinero_show', array('id' => $devolucion->getId())),
                'link_edit' => $this->generateUrl('egresovalor_devoluciondinero_create', array('id' => $rendicionEgresoValor->getEgresoValor()->getId(), 'idDevolucion' => $devolucion->getId())),
                'class_edit' => 'editar_devolucion_link',
                'link_borrar' => $this->generateUrl('egresovalor_devoluciondinero_delete', array('id' => $devolucion->getId()))
            ];
        }

        return $comprobantes;
    }

    /**
     * Finds and displays a EgresoValor\DevolucionDinero entity.
     *
     * @Route("/rendicion/{id}", name="egresovalor_rendicion_show")
     * @Method("GET")
     * @Template("ADIFContableBundle:EgresoValor\ComprobanteEgresoValor:showRendicion.html.twig")
     */
    public function showRendicionAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\RendicionEgresoValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\RendicionEgresoValor.');
        }

        $egresoValor = $entity->getEgresoValor();
        $responsable = $egresoValor->getTipoEgresoValor()->__toString()
                . ' - ' . $egresoValor->getResponsableEgresoValor()->getNombre();

        $bread = $this->base_breadcrumbs;
        $bread[$responsable] = null;
        $bread['Rendici&oacute;n'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'comprobantes' => $this->getComprobantes($entity),
            'page_title' => 'Detalle Rendición'
        );
    }

    /**
     * @Route("/autocomplete/form", name="comprobanteegresovalor_autocomplete_proveedor")
     */
    public function getProveedoresAction(Request $request) {
        $term = $request->query->get('term', null);

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $comprobantes = $em->getRepository('ADIFContableBundle:EgresoValor\ComprobanteEgresoValor')
                ->createQueryBuilder('ce')
                ->where('upper(ce.razonSocial) LIKE :term')
                ->orWhere('ce.CUIT LIKE :term')
                ->groupBy('ce.CUIT, ce.razonSocial')
                ->orderBy('ce.razonSocial', 'DESC')
                ->setParameter('term', '%' . strtoupper($term) . '%')
                ->getQuery()
                ->getResult();

        $jsonResult = [];

        foreach ($comprobantes as $comprobante) {
            $jsonResult[] = array(
                'razonSocial' => $comprobante->getRazonSocial(),
                'CUIT' => $comprobante->getCUIT(),
            );
        }

        return new JsonResponse($jsonResult);
    }

    /**
     * 
     * @param type $em
     * @param EgresoValor $egresoValor
     * @return type
     */
    public function getFechaInicioRendicion($em, EgresoValor $egresoValor) {

        /* @var $ordenPagoReposicion \ADIF\ContableBundle\Entity\EgresoValor\OrdenPagoEgresoValor */
        $ordenPagoReposicion = $em->getRepository('ADIFContableBundle:EgresoValor\OrdenPagoEgresoValor')
                ->findOneByReposicionEgresoValor($egresoValor->getUltimaReposicionPagada());

        return $ordenPagoReposicion->getFechaContable();
    }

    /**
     * @Route("/generarAsientos/", name="comprobanteegresovalor_asientos")
     * @Method("PUT|GET")     
     */
    public function generarAsientosComprobantesEgresoValor() {

//        gc_enable();
//
//        $parcial = false;
//
//        $offset = 0;
//        $limit = 20;
//        $i = 1;
//
//        $em = $this->getDoctrine()->getManager($this->getEntityManager());
//        $rendicionesEgresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\RendicionEgresoValor')
//                ->createQueryBuilder('r')
//                ->where('r.fechaRendicion >= :fecha')
//                ->setFirstResult($offset)
//                ->setMaxResults($limit)
//                ->setParameter('fecha', '2015-08-01 00:00:00')
//                ->orderBy('r.id', 'asc')
//                ->getQuery()
//                ->getResult();
//
//        $offset = $limit * $i;
//        $i++;
//        while (count($rendicionesEgresoValor) > 0) {
//            /* @var $rendicionEgresoValor \ADIF\ContableBundle\Entity\EgresoValor\RendicionEgresoValor */
//            foreach ($rendicionesEgresoValor as $rendicionEgresoValor) {
//                $this->get('adif.asiento_service')->generarAsientoFromRendicionEgresoValor($rendicionEgresoValor, $this->getUser());
//            }
//            unset($rendicionesEgresoValor);
//            $em->flush();
//            $em->clear();
//            gc_collect_cycles();
//            $rendicionesEgresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\RendicionEgresoValor')
//                    ->createQueryBuilder('r')
//                    ->where('r.fechaRendicion >= :fecha')
//                    ->setFirstResult($offset)
//                    ->setMaxResults($limit)
//                    ->setParameter('fecha', '2015-08-01 00:00:00')
//                    ->orderBy('r.id', 'asc')
//                    ->getQuery()
//                    ->getResult();
//            $offset = $limit * $i;
//            $i++;
//        }
//        unset($rendicionesEgresoValor);
//        $em->clear();
//        unset($em);
//        gc_collect_cycles();
//
//        if (!$parcial) {
//            $this->get('session')->getFlashBag()->add('success', 'Generacion de asientos de Rendicion exitosa');
//        }
        
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $rendicionEgresoValor = $em->getRepository('ADIFContableBundle:EgresoValor\RendicionEgresoValor')->find(2303);
        $this->get('adif.asiento_service')->generarAsientoFromRendicionEgresoValor($rendicionEgresoValor, $this->getUser());
        
        $em->flush();
        $em->clear();        

        return $this->redirect($this->generateUrl('egresovalor'));
    }

}
