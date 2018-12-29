<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\TipoRodante;
use ADIF\InventarioBundle\Form\TipoRodanteType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * TipoRodante controller.
 *
 * @Route("/tiporodante")
  */
class TipoRodanteController extends BaseController
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'Materiales Rodantes' => '',
            'Tipos de Rodante' => $this->generateUrl('tiporodante')
        );
    }
    /**
     * Lists all TipoRodante entities.
     *
     * @Route("/", name="tiporodante")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Tipos de Rodante'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Tipos de Rodante',
            'page_info' => 'Lista Tipos de Rodante'
        );
    }

    /**
     * Tabla para TipoRodante .
     *
     * @Route("/index_table/", name="tiporodante_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:TipoRodante')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de Rodante'] = null;

    return $this->render('ADIFInventarioBundle:TipoRodante:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new TipoRodante entity.
     *
     * @Route("/insertar", name="tiporodante_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:TipoRodante:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TipoRodante();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tiporodante'));
        } //.
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Tipo de Rodante',
        );
    }

    /**
    * Creates a form to create a TipoRodante entity.
    *
    * @param TipoRodante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(TipoRodante $entity)
    {
        $form = $this->createForm(new TipoRodanteType(), $entity, array(
            'action' => $this->generateUrl('tiporodante_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoRodante entity.
     *
     * @Route("/crear", name="tiporodante_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TipoRodante();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Tipo de Rodante'
        );
}

    /**
     * Finds and displays a TipoRodante entity.
     *
     * @Route("/{id}", name="tiporodante_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoRodante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoRodante.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Tipos de Rodante'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Tipo de Rodante'
        );
    }

    /**
     * Displays a form to edit an existing TipoRodante entity.
     *
     * @Route("/editar/{id}", name="tiporodante_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:TipoRodante:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoRodante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoRodante.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Tipo de Rodante'
        );
    }

    /**
    * Creates a form to edit a TipoRodante entity.
    *
    * @param TipoRodante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoRodante $entity)
    {
        $form = $this->createForm(new TipoRodanteType(), $entity, array(
            'action' => $this->generateUrl('tiporodante_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing TipoRodante entity.
     *
     * @Route("/actualizar/{id}", name="tiporodante_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:TipoRodante:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:TipoRodante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad TipoRodante.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tiporodante'));
        } //.
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Tipo de Rodante'
        );
    }
    /**
     * Deletes a TipoRodante entity.
     *
     * @Route("/borrar/{id}", name="tiporodante_delete")
     * @Method("GET")
     */
    public function deleteAction($id){
        return parent::baseDeleteAction($id);
    }

    /**
     *
     * @param type $id
     * @return boolean
     */
    public function validateLocalDeleteById($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        // CatalogoMaterialesRodantes
        $qbMaterialesRodantes = $em
            ->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.idTipoRodante = :id')
            ->setParameter('id', $id);

        $countMaterialesRodantes = $qbMaterialesRodantes->getQuery()->getSingleScalarResult();

        return ($countMaterialesRodantes) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el Tipo de Rodante '
                . 'ya que es referenciado por otras entidades.';
    }

    /**
     * @Route("/lista", name="tipoRodante_por_rodante")
     * @Method("POST")
     */
    public function getTipoRodanteByRodanteAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $id = $request->request->get('grupoRodante');
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $result = $em->getRepository('ADIFInventarioBundle:TipoRodante')->findTipoRodantebyRodante($id);

            return new JsonResponse($result);
        }
    }

    /**
     * @Route("/lista_por_grupo", name="tiporodante_por_grupo")
     * @Method("POST")
     */
    public function getTipoRodanteByGrupoRodanteAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $grupo_rodante = $em->getRepository('ADIFInventarioBundle:GrupoRodante')->find($id);

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:TipoRodante', $this->getEntityManager());

            $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion')
                    ->where('c.grupoRodante =  :grupo_rodante')
                    ->setParameter('grupo_rodante', $grupo_rodante)
                    ->orderBy('c.denominacion', 'ASC')
                    ->getQuery();

            return new JsonResponse($query->getResult());
            //return new JsonResponse($linea->getCorredores());
        }
    }
}
