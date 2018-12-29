<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\Divisiones;
use ADIF\InventarioBundle\Form\DivisionesType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Divisiones controller.
 *
 * @Route("/divisiones")
  */
class DivisionesController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Configuraci&oacute;n' => '',
            'Activos Lineales' => '',
            'Divisiones' => $this->generateUrl('divisiones')
        );
    }
    /**
     * Lists all Divisiones entities.
     *
     * @Route("/", name="divisiones")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Divisiones'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Divisiones',
            'page_info' => 'Lista de Divisiones'
        );
    }

    /**
     * Tabla para Divisiones .
     *
     * @Route("/index_table/", name="divisiones_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:Divisiones')->findAll();

        $bread = $this->base_breadcrumbs;
        $bread['Divisiones'] = null;

    return $this->render('ADIFInventarioBundle:Divisiones:index_table.html.twig', array(
                    'entities' => $entities
                        )
        );
    }
    /**
     * Creates a new Divisiones entity.
     *
     * @Route("/insertar", name="divisiones_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:Divisiones:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Divisiones();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if(!$this->isDivisionValidToCreate($entity->getDenominacion(), $entity->getLinea(), $entity->getOperador())){
                $form->addError(new FormError('La denominación ya se encuentra en uso para la Linea y el Operador seleccionados.'));
                $request->attributes->set('form-error', true);
            }else{
                $entity->setIdEmpresa(1); //Multiempresa: ADIF

                $em = $this->getDoctrine()->getManager($this->getEntityManager());
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('divisiones'));
            }
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
            'page_title' => 'Crear Division',
        );
    }

    /**
    * Creates a form to create a Divisiones entity.
    *
    * @param Divisiones $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Divisiones $entity)
    {
        $form = $this->createForm(new DivisionesType(), $entity, array(
            'action' => $this->generateUrl('divisiones_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Divisiones entity.
     *
     * @Route("/crear", name="divisiones_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Divisiones();
        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Division'
        );
}

    /**
     * Finds and displays a Divisiones entity.
     *
     * @Route("/{id}", name="divisiones_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Divisiones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Divisiones.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Divisiones'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Division'
        );
    }

    /**
     * Displays a form to edit an existing Divisiones entity.
     *
     * @Route("/editar/{id}", name="divisiones_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:Divisiones:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Divisiones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Divisiones.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Division'
        );
    }

    /**
    * Creates a form to edit a Divisiones entity.
    *
    * @param Divisiones $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Divisiones $entity)
    {
        $form = $this->createForm(new DivisionesType(), $entity, array(
            'action' => $this->generateUrl('divisiones_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Divisiones entity.
     *
     * @Route("/actualizar/{id}", name="divisiones_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:Divisiones:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:Divisiones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Divisiones.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if(!$this->isDivisionValidToEdit($entity->getDenominacion(), $entity->getLinea(), $entity->getOperador(), $entity->getId())){
                $editForm->addError(new FormError('La denominación ya se encuentra en uso para la Linea y el Operador seleccionados.'));
                $request->attributes->set('form-error', true);
            }else{
                $em->flush();

                return $this->redirect($this->generateUrl('divisiones'));
            }

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
            'page_title' => 'Editar Division'
        );
    }
    /**
    * Deletes a Divisiones entity.
    *
    * @Route("/borrar/{id}", name="divisiones_delete")
    * @Method("GET")
    */
    public function deleteAction($id)
    {
        return parent::baseDeleteAction($id);

    }

    public function validateLocalDeleteById($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        //Corredores
        $qbCorredores = $em
            ->getRepository('ADIFInventarioBundle:Corredor')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.division = :id')
            ->setParameter('id', $id);

        $counCorredores = $qbCorredores->getQuery()->getSingleScalarResult();

        //Activo Lineal
        $qbActivoLineal = $em
            ->getRepository('ADIFInventarioBundle:ActivoLineal')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.division = :id')
            ->setParameter('id', $id);

        $countActivoLineal = $qbActivoLineal->getQuery()->getSingleScalarResult();

        return ($counCorredores+$countActivoLineal) == 0;
    }

     /**
      *
      * @return type
      */
     public function getSessionMessage() {
         return 'No se pudo eliminar la División '
                 . 'ya que es referenciada por otras entidades.';
     }

     public function isDivisionValidToCreate($denominacion, $linea, $operador) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $qbDivisiones = $em
            ->getRepository('ADIFInventarioBundle:Divisiones')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.denominacion = :denom
                AND u.linea = :linea
                AND u.operador = :operador')
            ->setParameters(['denom' => $denominacion, 'linea' => $linea,
                'operador' => $operador]);

        $countDivisiones = $qbDivisiones->getQuery()->getSingleScalarResult();

        return $countDivisiones==0;

    }

     public function isDivisionValidToEdit($denominacion, $linea, $operador, $idDivision) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $qbDivisiones = $em
            ->getRepository('ADIFInventarioBundle:Divisiones')
            ->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.denominacion = :denom
                AND u.linea = :linea
                AND u.operador = :operador
                AND u.id <> :id')
            ->setParameters(['denom' => $denominacion, 'linea' => $linea,
                'operador' => $operador, 'id' => $idDivision]);

        $countDivisiones = $qbDivisiones->getQuery()->getSingleScalarResult();

        return $countDivisiones==0;

    }

    /**
     * @Route("/lista_por_linea", name="divisiones_por_linea")
     */
    public function getDivisionesByLineaAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $linea = $em->getRepository('ADIFInventarioBundle:Linea')
                ->find($id);

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Divisiones', $this->getEntityManager());

            $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion')
                    ->where('c.linea =  :linea')
                    ->setParameter('linea', $linea)
                    ->orderBy('c.id', 'ASC')
                    ->getQuery();

            return new JsonResponse($query->getResult());
            //return new JsonResponse($linea->getDivisiones()->toArray());
        }
    }


    /**
     * @Route("/lista_por_operador", name="divisiones_por_operador")
     */
    public function getDivisionesByOperadorAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $operador = $em->getRepository('ADIFInventarioBundle:Operador')->find($id);

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Divisiones', $this->getEntityManager());

            $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion')
                    ->where('c.operador =  :operador')
                    ->setParameter('operador', $operador)
                    ->orderBy('c.id', 'ASC')
                    ->getQuery();

            return new JsonResponse($query->getResult());
            //return new JsonResponse($operador->getDivisiones()->toArray());
        }
    }

    /**
     * @Route("/lista_por_ramal", name="divisiones_por_ramal")
     */
    public function getDivisionesByRamalAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $ramal = $em->getRepository('ADIFInventarioBundle:Ramal')
                ->find($id);

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Divisiones', $this->getEntityManager());

            $query = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion')
                    ->where('c.ramal =  :ramal')
                    ->setParameter('ramal', $ramal)
                    ->orderBy('c.id', 'ASC')
                    ->getQuery();

            return new JsonResponse($query->getResult());
            //return new JsonResponse($ramal->getDivisiones());
        }
    }

    /**
     * @Route("/lista", name="divisiones_lista")
     */
    public function getDivisionesAction(Request $request) {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $ids = $request->request->all();

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:Divisiones', $this->getEntityManager());

            $queryBuilder = $repository->createQueryBuilder('c')
                    ->select('c.id', 'c.denominacion');

            foreach($ids as $key => $id){
                $entity = $em->getRepository('ADIFInventarioBundle:'.ucfirst($key))->find($id);
                $queryBuilder
                        ->andWhere("c.$key = :$key")
                        ->setParameter($key,$entity);
            }

            $query = $queryBuilder->orderBy('c.denominacion', 'ASC')->getQuery();

            return new JsonResponse($query->getResult());
        }
    }
}
