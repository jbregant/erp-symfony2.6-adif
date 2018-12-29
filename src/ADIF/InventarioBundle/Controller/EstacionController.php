<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Estacion;
use ADIF\InventarioBundle\Form\EstacionType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Estacion controller.
 *
 * @Route("/estacion")
  */
class EstacionController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuración' => '',
            'General' => '',
            'Estaciones' => $this->generateUrl('estacion')
        );
    }
    /**
     * Lists all Estacion entities.
     *
     * @Route("/", name="estacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Estaciones'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Estaciones',
            'page_info' => 'Lista de Estaciones'
        );
    }

    /**
     * Tabla para Estacion .
     *
     * @Route("/index_table/", name="estacion_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Estacion')->findAllEstaciones();
        $bread = $this->base_breadcrumbs;
        $bread['Estaciones'] = null;

        return $this->render('ADIFInventarioBundle:Estacion:index_table.html.twig', array(
            'entities' => $entities
        ));
    }
    /**
     * Creates a new Estacion entity.
     *
     * @Route("/insertar", name="estacion_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Estacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Estacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF
            //$entity->setLinea($entity->getRamal()->getLinea());

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estacion'));
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
            'page_title' => 'Crear Estaci&oacute;n',
        );
    }

    /**
    * Creates a form to create a Estacion entity.
    *
    * @param Estacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Estacion $entity)
    {
        $form = $this->createForm(new EstacionType(), $entity, array(
            'action' => $this->generateUrl('estacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Estacion entity.
     *
     * @Route("/crear", name="estacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Estacion();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Estaci&oacute;n'
        );
}

    /**
     * Finds and displays a Estacion entity.
     *
     * @Route("/{id}", name="estacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Estacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Estacion.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Estaciones'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Estaci&oacute;n'
        );
    }

    /**
     * Displays a form to edit an existing Estacion entity.
     *
     * @Route("/editar/{id}", name="estacion_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Estacion:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Estacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Estacion.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Estaci&oacute;n'
        );
    }

    /**
    * Creates a form to edit a Estacion entity.
    *
    * @param Estacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Estacion $entity)
    {
        $form = $this->createForm(new EstacionType(), $entity, array(
            'action' => $this->generateUrl('estacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Estacion entity.
     *
     * @Route("/actualizar/{id}", name="estacion_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Estacion:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Estacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Estacion.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            //$entity->setLinea($entity->getRamal()->getLinea());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estacion'));
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
            'page_title' => 'Editar Estaci&oacute;n'
        );
    }
    /**
     * Deletes a Estacion entity.
     *
     * @Route("/borrar/{id}", name="estacion_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        return parent::baseDeleteAction($id);
    }

    /**
     *
     * @param type $id
     * @return boolean
     */
    public function validateLocalDeleteById($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        //Almacen
        $qbAlmacen = $em
            ->getRepository('ADIFInventarioBundle:Almacen')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.estacion = :id')
            ->setParameter('id', $id);

        $countAlmacen = $qbAlmacen->getQuery()->getSingleScalarResult();

        //Activo Lineal
        $qbActivoLineal = $em
            ->getRepository('ADIFInventarioBundle:ActivoLineal')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.estacion = :id')
            ->setParameter('id', $id);

        $countActivoLineal = $qbActivoLineal->getQuery()->getSingleScalarResult();

        // CatalogoMaterialesRodantes
        $qbMaterialesRodantes = $em
            ->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.idEstacion = :id')
            ->setParameter('id', $id);

        $countMaterialesRodantes = $qbMaterialesRodantes->getQuery()->getSingleScalarResult();

        return ($countAlmacen+$countActivoLineal+$countMaterialesRodantes) == 0;
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar la Estación '
                . 'ya que es referenciado por otras entidades.';
    }

    /**
     * @Route("/lista_por_linea", name="estacion_por_linea")
     */
    public function getEstacionesByLineaAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $linea = $em->getRepository('ADIFInventarioBundle:Linea')->find($id);

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Estacion', $this->getEntityManager());

            $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion')
                    ->where('c.linea =  :linea')
                    ->setParameter('linea', $linea)
                    ->orderBy('c.denominacion', 'ASC')
                    ->getQuery();

            return new JsonResponse($query->getResult());
            //return new JsonResponse($linea->getCorredores());
        }
    }

    /**
     * @Route("/lista_por_ramal", name="estacion_por_ramal")
     */
    public function getEstacionesByRamalAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $ramal = $em->getRepository('ADIFInventarioBundle:Ramal')->find($id);

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Estacion', $this->getEntityManager());

            $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion')
                    ->where('c.ramal =  :ramal')
                    ->setParameter('ramal', $ramal)
                    ->orderBy('c.denominacion', 'ASC')
                    ->getQuery();

            return new JsonResponse($query->getResult());
            //return new JsonResponse($linea->getCorredores());
        }
    }

    /**
     * @Route("/lista", name="estaciones_lista")
     */
    public function getEstacionesAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $ids = $request->request->all();

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Estacion', $this->getEntityManager());

            $queryBuilder = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion');

            foreach($ids as $key => $id){
                $entity = $em->getRepository('ADIFInventarioBundle:'.ucfirst($key))->find($id);
                $queryBuilder
                        ->andWhere("c.$key = :$key")
                        ->setParameter($key,$entity);
            }
            $queryBuilder
                ->orderBy('c.denominacion', 'ASC');

            $query = $queryBuilder->orderBy('c.denominacion', 'ASC')->getQuery();

            return new JsonResponse($query->getResult());
        }
    }


}
