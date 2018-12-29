<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\BaseBundle\Controller\AlertControllerInterface;
use ADIF\ContableBundle\Controller\BaseController;
use ADIF\ContableBundle\Entity\LicitacionObra;
use ADIF\ContableBundle\Form\LicitacionObraType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Entity\EntityManagers;


/**
 * LicitacionObra controller.
 *
 * @Route("/licitacion_obra")
 */
class LicitacionObraController extends BaseController implements AlertControllerInterface {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Licitaciones de obra' => $this->generateUrl('licitacion_obra')
        );
    }

    /**
     * Lists all LicitacionObra entities.
     *
     * @Route("/", name="licitacion_obra")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $bread = $this->base_breadcrumbs;
        $bread['Licitaciones de obra'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Licitaciones de obra',
            'page_info' => 'Lista de licitaciones de obra'
        );
    }

    /**
     * Tabla para LicitacionObra .
     *
     * @Route("/index_table/", name="licitacion_obra_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:LicitacionObra')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Licitaciones de obra'] = null;

        return $this->render('ADIFContableBundle:LicitacionObra:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }

    /**
     * Creates a new LicitacionObra entity.
     *
     * @Route("/insertar", name="licitacion_obra_create")
     * @Method("POST")
     * @Template("ADIFContableBundle:LicitacionObra:new.html.twig")
     */
    public function createAction(Request $request) {

        $entity = new LicitacionObra();

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Actualiza los archivos adjuntos
            $this->updateAdjuntos($entity);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('licitacion_obra'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear licitaci&oacute;n de obra',
        );
    }

    /**
     * Creates a form to create a LicitacionObra entity.
     *
     * @param LicitacionObra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(LicitacionObra $entity) {
        $form = $this->createForm(new LicitacionObraType(), $entity, array(
            'action' => $this->generateUrl('licitacion_obra_create'),
            'method' => 'POST',
            'entity_manager_compras' => $this->getDoctrine()->getManager(EntityManagers::getEmCompras()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new LicitacionObra entity.
     *
     * @Route("/crear", name="licitacion_obra_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new LicitacionObra();
        $form = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear licitaci&oacute;n de obra'
        );
    }

    /**
     * Finds and displays a LicitacionObra entity.
     *
     * @Route("/{id}", name="licitacion_obra_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:LicitacionObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad LicitacionObra.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Licitaci&oacute;n de obra'] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver licitaci&oacute;n de obra'
        );
    }

    /**
     * Displays a form to edit an existing LicitacionObra entity.
     *
     * @Route("/editar/{id}", name="licitacion_obra_edit")
     * @Method("GET")
     * @Template("ADIFContableBundle:LicitacionObra:new.html.twig")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:LicitacionObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad LicitacionObra.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar licitaci&oacute;n de obra'
        );
    }

    /**
     * Creates a form to edit a LicitacionObra entity.
     *
     * @param LicitacionObra $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(LicitacionObra $entity) {
        $form = $this->createForm(new LicitacionObraType(), $entity, array(
            'action' => $this->generateUrl('licitacion_obra_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_compras' => $this->getDoctrine()->getManager(EntityManagers::getEmCompras()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing LicitacionObra entity.
     *
     * @Route("/actualizar/{id}", name="licitacion_obra_update")
     * @Method("PUT")
     * @Template("ADIFContableBundle:LicitacionObra:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:LicitacionObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad LicitacionObra.');
        }

        $adjuntosOriginales = new ArrayCollection();

        // Creo un ArrayCollection de los adjuntos actuales en la BBDD
        foreach ($entity->getArchivos() as $adjunto) {
            $adjuntosOriginales->add($adjunto);
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->setFechaUltimaActualizacion(new \DateTime());

            // Actualiza los archivos adjuntos
            $this->updateAdjuntos($entity);

            // Por cada adjunto original
            foreach ($adjuntosOriginales as $adjunto) {

                // Si fue eliminado
                if (false === $entity->getArchivos()->contains($adjunto)) {

                    $entity->removeArchivo($adjunto);

                    $em->remove($adjunto);
                }
            }

            $em->flush();

            return $this->redirect($this->generateUrl('licitacion_obra'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar licitaci&oacute;n de obra'
        );
    }

    /**
     * Deletes a LicitacionObra entity.
     *
     * @Route("/borrar/{id}", name="licitacion_obra_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:LicitacionObra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad LicitacionObra.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('licitacion_obra'));
    }

    /**
     * 
     * @param LicitacionObra $licitacion
     */
    private function updateAdjuntos(LicitacionObra $licitacion) {

        foreach ($licitacion->getArchivos() as $adjunto) {

            if ($adjunto->getArchivo() != null) {

                $adjunto->setLicitacion($licitacion);

                $adjunto->setNombre($adjunto->getArchivo()->getClientOriginalName());
            }
        }
    }

    /**
     * @Route("/autocomplete/form", name="autocomplete_licitacion_obra")
     */
    public function getLicitacionesObraAction(Request $request) {

        $term = $request->query->get('term', null);

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $licitaciones = $em->getRepository('ADIFContableBundle:LicitacionObra')
                ->createQueryBuilder('l')
                ->where('upper(l.numero) LIKE :term')
                ->orderBy('l.numero', 'ASC')
                ->setParameter('term', '%' . strtoupper($term) . '%')
                ->getQuery()
                ->getResult();

        $jsonResult = [];

        foreach ($licitaciones as $licitacion) {

            $jsonResult[] = array(
                'id' => $licitacion->getId(),
                'numero' => $licitacion->getNumero(),
                'importePliego' => $licitacion->getImportePliego()
            );
        }

        return new JsonResponse($jsonResult);
    }
    
    /**
     * @Route("/getLicitacionObraByTipoContratacionAndNumeroAndAnio", name="get_licitacion_obra_busqueda")
     */
    public function getLicitacionObraByTipoContratacionAndNumeroAndAnioAction(Request $request) {

        $idTipoContratacion = $request->get('tipoContratacion', null);
        $numero = $request->get('numero', null);
        $anio = $request->get('anio', null); //

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $licitacion = $em->getRepository('ADIFContableBundle:LicitacionObra')
                ->createQueryBuilder('l')
                ->where('l.numero = :numero')->setParameter('numero', $numero)
                ->andWhere('l.anio = :anio')->setParameter('anio', $anio)
                ->andWhere('l.idTipoContratacion = :idTipoContratacion')->setParameter('idTipoContratacion', $idTipoContratacion)
                ->andWhere('l.importePliego > 0')
                ->getQuery()
                ->getOneOrNullResult();

        $jsonResult = [];
        if ($licitacion != null) { 
            $jsonResult = array(
                'id' => $licitacion->getId(),
                'numero' => $licitacion->getNumero(),
                'anio' => $licitacion->getAnio(),
                'importePliego' => $licitacion->getImportePliego(),
                'tipoContratacion' => $licitacion->getTipoContratacion()->getAlias()
            );
        }
        
        return new JsonResponse($jsonResult);
    }
}
