<?php

namespace ADIF\RecursosHumanosBundle\Controller;

use ADIF\RecursosHumanosBundle\Controller\BaseController;
use ADIF\RecursosHumanosBundle\Entity\Concepto;
use ADIF\RecursosHumanosBundle\Entity\ConceptoVersion;
use ADIF\RecursosHumanosBundle\Form\ConceptoType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Exception\Exception;
use ADIF\BaseBundle\Entity\EntityManagers;

/**
 * Concepto controller.
 *
 * @Route("/conceptos")
 * @Security("has_role('ROLE_RRHH_VISTA_CONCEPTOS')")
 */
class ConceptoController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Conceptos' => $this->generateUrl('conceptos')
        );
    }

    /**
     * Lists all Concepto entities.
     *
     * @Route("/", name="conceptos")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')
                ->createQueryBuilder('c')
                ->select('c, co')
                ->leftJoin('c.convenios', 'co')
                ->getQuery()
                ->getResult();

        $bread = $this->base_breadcrumbs;
        $bread['Conceptos'] = null;

        return array(
            'entities' => $entities,
            'breadcrumbs' => $bread,
            'page_title' => 'Conceptos',
            'page_info' => 'Lista de conceptos'
        );
    }

    /**
     * Creates a new Concepto entity.
     *
     * @Route("/insertar", name="conceptos_create")
     * @Method("POST")
     * @Template("ADIFRecursosHumanosBundle:Concepto:new.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_CONCEPTOS')")
     */
    public function createAction(Request $request) {
        $concepto = new Concepto();
        $form = $this->createCreateForm($concepto);
        $form->handleRequest($request);
        
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if ($form->isValid()) {
            
            if ($concepto->getValor() == null){
                $concepto->setValor(0);
            }

            $em->getConnection()->beginTransaction();
            
            try {
                $em->persist($concepto);
                $em->flush();
                $this->versionarConcepto($concepto);
                
                // Try and commit the transaction
                $em->getConnection()->commit();
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback();
                throw $e;
            }
            
            
            return $this->redirect($this->generateUrl('conceptos'));
        }

        $parametrosFormula = $em->getRepository('ADIFRecursosHumanosBundle:ParametrosFormula')->findBy(array(), array('tag' => 'asc'));
        
        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'formulas' => $parametrosFormula,
            'entity' => $concepto,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear concepto',
        );
    }

    /**
     * Creates a form to create a Concepto entity.
     *
     * @param Concepto $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Concepto $entity) {
        $form = $this->createForm(new ConceptoType(), $entity, array(
            'action' => $this->generateUrl('conceptos_create'),
            'method' => 'POST',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Concepto entity.
     *
     * @Route("/crear", name="conceptos_new")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_RRHH_ALTA_CONCEPTOS')")
     */
    public function newAction() {
        $entity = new Concepto();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $parametrosFormula = $em->getRepository('ADIFRecursosHumanosBundle:ParametrosFormula')->findBy(array(), array('tag' => 'asc'));
        
        return array(
            'formulas' => $parametrosFormula,
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear concepto'
        );
    }

    /**
     * Finds and displays a Concepto entity.
     *
     * @Route("/{id}", name="conceptos_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Concepto.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Concepto'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver concepto'
        );
    }

    /**
     * Displays a form to edit an existing Concepto entity.
     *
     * @Route("/editar/{id}", name="conceptos_edit")
     * @Method("GET")
     * @Template("ADIFRecursosHumanosBundle:Concepto:new.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_CONCEPTOS')")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Concepto.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        $parametrosFormula = $em->getRepository('ADIFRecursosHumanosBundle:ParametrosFormula')->findBy(array(), array('descripcion' => 'asc'));

        return array(
            'formulas' => $parametrosFormula,
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar concepto'
        );
    }

    /**
     * Creates a form to edit a Concepto entity.
     *
     * @param Concepto $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Concepto $entity) {
        $form = $this->createForm(new ConceptoType(), $entity, array(
            'action' => $this->generateUrl('conceptos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable())
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Concepto entity.
     *
     * @Route("/actualizar/{id}", name="conceptos_update")
     * @Method("PUT")
     * @Template("ADIFRecursosHumanosBundle:Concepto:new.html.twig")
     * @Security("has_role('ROLE_RRHH_ALTA_CONCEPTOS')")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $concepto = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')->find($id);

        if (!$concepto) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Concepto.');
        }

        $editForm = $this->createEditForm($concepto);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            if ($concepto->getValor() == null){
                $concepto->setValor(0);
            }
            
            $em->getConnection()->beginTransaction();
            
            try {
                $em->flush();
                $this->versionarConcepto($concepto);
                
                // Try and commit the transaction
                $em->getConnection()->commit();
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback();
                throw $e;
            }
            
            return $this->redirect($this->generateUrl('conceptos'));
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        $parametrosFormula = $em->getRepository('ADIFRecursosHumanosBundle:ParametrosFormula')->findAll();
        
        return array(
            'formulas' => $parametrosFormula,
            'entity' => $concepto,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar concepto'
        );
    }

    /**
     * Deletes a Concepto entity.
     *
     * @Route("/borrar/{id}", name="conceptos_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_RRHH_ALTA_CONCEPTOS')")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $concepto = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')->find($id);

        if (!$concepto) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Concepto.');
        }

        $em->getConnection()->beginTransaction();
        try {
            $em->remove($concepto);
            $em->flush();

            // Try and commit the transaction
            $em->getConnection()->commit();
        } catch (Exception $e) {
            // Rollback the failed transaction attempt
            $em->getConnection()->rollback();
            throw $e;
        }
        
        return $this->redirect($this->generateUrl('conceptos'));
    }

    /**
     * Activa un Concepto.
     *
     * @Route("/activar/{id}", name="conceptos_activar")
     * @Security("has_role('ROLE_RRHH_ALTA_CONCEPTOS')")
     */
    public function activar($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $concepto = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')->find($id);

        if (!$concepto) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Concepto.');
        }

        $concepto->setActivo(true);
        
        $em->getConnection()->beginTransaction();
        try {
            $em->flush();
            $this->versionarConcepto($concepto);
        
            // Try and commit the transaction
            $em->getConnection()->commit();
        } catch (Exception $e) {
            // Rollback the failed transaction attempt
            $em->getConnection()->rollback();
            throw $e;
        }
        
        return $this->redirect($this->generateUrl('conceptos'));
    }

    /**
     * Desactiva un Concepto.
     *
     * @Route("/desactivar/{id}", name="conceptos_desactivar")
     * @Security("has_role('ROLE_RRHH_ALTA_CONCEPTOS')")
     */
    public function desactivar($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $concepto = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')->find($id);

        if (!$concepto) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Concepto.');
        }

        $concepto->setActivo(false);
        
        $em->getConnection()->beginTransaction();
        try {
            $em->flush();
            $this->versionarConcepto($concepto);
        
            // Try and commit the transaction
            $em->getConnection()->commit();
        } catch (Exception $e) {
            // Rollback the failed transaction attempt
            $em->getConnection()->rollback();
            throw $e;
        }

        return $this->redirect($this->generateUrl('conceptos'));
    }

    private function versionarConcepto(Concepto $c) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $conceptoVersion = new ConceptoVersion();
        $conceptoVersion
                ->setConcepto($c)
                ->setActivo($c->getActivo())
                ->setAplicaTope($c->getAplicaTope())
                ->setCodigo($c->getCodigo())
                ->setConvenios(implode(',', $c->getConvenios()->toArray()))
                ->setDescripcion($c->getDescripcion())
                ->setEsNovedad($c->getEsNovedad())
                ->setEsPorcentaje($c->getEsPorcentaje())
                ->setFechaAlta($c->getFechaAlta())
                ->setFechaBaja($c->getFechaBaja())
                ->setFormula($c->getFormula())
                ->setIdTipoConcepto($c->getIdTipoConcepto()->getId())
                ->setImprimeLey($c->getImprimeLey())
                ->setImprimeRecibo($c->getImprimeRecibo())
                ->setIntegraIg($c->getIntegraIg())
                ->setIntegraSac($c->getIntegraSac())
                ->setLeyenda($c->getLeyenda())
                ->setValor($c->getValor())
                ->setIdCuentaContable($c->getIdCuentaContable())
                ->setEsAjuste($c->getEsAjuste())
                ->setEsNegativo($c->getEsNegativo())
                ->setEsIndemnizatorio($c->getEsIndemnizatorio())
				->setCambiaEscalaImpuesto($c->getCambiaEscalaImpuesto());

        $em->persist($conceptoVersion);
        $em->flush();
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/multiple/", name="conceptos_multiple")
     * @Method("GET|POST")
     * 
     */
    public function getConceptosMultiplesConvenios(Request $request) {
        if (!$request->request->get('convenios')) {
            $this->get('session')->getFlashBag()->add(
                    'error', 'Debe seleccionar al menos un empleado para asignar conceptos.'
            );
            return $this->redirect($this->generateUrl('empleados'));
        }
        // convenios
        $idsConvenios = json_decode($request->request->get('convenios', '[]'));

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')
                ->createQueryBuilder('c')
                ->select('c, co')
                ->leftJoin('c.convenios', 'co')
                ->where('c.esNovedad = 0')
                ->andWhere('co.id IN (:idsConvenios)')
                ->groupBy('c.id')
                ->having('count(co.id) = :cantidad')
                ->setParameters(new ArrayCollection(array(
                    new Parameter('idsConvenios', $idsConvenios),
                    new Parameter('cantidad', sizeof($idsConvenios))))
                )
                ->getQuery()
                ->getResult();

        $html = "";

        if ($entities != null) {
            $html = "<option value='' selected='selected'>-- Elija un concepto --</option>";
            foreach ($entities as $concepto) {
                $html = $html . sprintf(
                                "<option value=\"%d\">%s</option>", //
                                $concepto->getId(), 'Código:' .$concepto->getCodigo() . ' - ' .$concepto->getDescripcion());
            }
        }


        $response = new Response();
        $response->setContent($html);
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }
    
     /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/novedadesmultiple/", name="novedades_multiple")
     * @Method("GET|POST")
     * 
     */
    public function getNovedadesMultiplesConvenios(Request $request) {
        if (!$request->request->get('convenios')) {
            $this->get('session')->getFlashBag()->add(
                    'error', 'Debe seleccionar al menos un empleado para asignar conceptos.'
            );
            return $this->redirect($this->generateUrl('empleados'));
        }
        // convenios
        $idsConvenios = json_decode($request->request->get('convenios', '[]'));

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFRecursosHumanosBundle:Concepto')
                ->createQueryBuilder('c')
                ->select('c, co')
                ->leftJoin('c.convenios', 'co')
                ->where('c.esNovedad = 1')
                ->andWhere('co.id IN (:idsConvenios)')
                ->groupBy('c.id')
                ->having('count(co.id) = :cantidad')
                ->setParameters(new ArrayCollection(array(
                    new Parameter('idsConvenios', $idsConvenios),
                    new Parameter('cantidad', sizeof($idsConvenios))))
                )
                ->getQuery()
                ->getResult();

        $html = "";

        if ($entities != null) {
            $html = "<option value='' selected='selected'>-- Elija una novedad --</option>";
            foreach ($entities as $concepto) {
                $html = $html . sprintf(
                    "<option es-ajuste=\"%d\" value=\"%d\">%s</option>", 
                    $concepto->getEsAjuste() ? 1 : 0,
                    $concepto->getId(), 
                    $concepto->getDescripcion().' - C&oacute;digo: ' .$concepto->getCodigo());
            }
        }


        $response = new Response();
        $response->setContent($html);
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

    /**
     * @Route("/lista_conceptos", name="lista_conceptos")
     */
    public function listaConceptosAction(Request $request) {
               
        $repository = $this->getDoctrine()->getRepository('ADIFRecursosHumanosBundle:Concepto', $this->getEntityManager());
 
        $query = $repository->createQueryBuilder('c')
            ->select('c.id', 'c.descripcion',' c.codigo')
            ->where('c.fechaBaja IS NULL')
            ->orderBy('c.codigo * 1', 'ASC')
            ->getQuery();
        
        return new JsonResponse($query->getResult());
    }
}
