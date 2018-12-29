<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\InventarioBundle\Entity\ActivoLineal;
use ADIF\InventarioBundle\Form\ActivoLinealType;
use ADIF\InventarioBundle\Form\ActivoLinealSepararCollectionType;
use ADIF\InventarioBundle\Form\ActivoLinealSepararType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\Type;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormError;
use Doctrine\Common\Util\Debug;


/**
 * ActivoLineal controller.
 *
 * @Route("/activolineal")
 * @Security("has_role('ROLE_INVENTARIO_ACTIVO_LINEAL_CONSULTA')")
  */
class ActivoLinealController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Datos Maestros' => '',
            'Activos Lineales' => $this->generateUrl('activolineal')
        );
    }
    /**
     * Lists all ActivoLineal entities.
     *
     * @Route("/", name="activolineal")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Activos Lineales'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Activos Lineales',
            'page_info' => 'Lista de activos lineales',
			'bing_key' => $this->container->getParameter('bing_key'),
			'map_service_path' => $this->container->getParameter('map_service_path'),
			'layer_name_punto' => $this->container->getParameter('layer_name_punto'),
			'layer_name_linea' => $this->container->getParameter('layer_name_linea')
        );
    }

    /**
     * Tabla para ActivoLineal .
     *
     * @Route("/index_table/", name="activolineal_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->findAllactivoLineal();
        $bread = $this->base_breadcrumbs;
        $bread['Activos Lineales'] = null;

        return $this->render('ADIFInventarioBundle:ActivoLineal:index_table.html.twig', array(
            'entities' => $entities, /* 'atributoRepo' => $atributoRepo */
        ));
    }
    /**
     * Creates a new ActivoLineal entity.
     *
     * @Route("/insertar", name="activolineal_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:ActivoLineal:new.html.twig")
     */
    public function createAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SOLO_LECTURA')) {
            throw $this->createAccessDeniedException();
        }
        
        $entity = new ActivoLineal();

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $estadoInventario = $em->getRepository('ADIFInventarioBundle:EstadoInventario')->find(1);
        $entity->setEstadoInventario($estadoInventario); //Estado Borrador por default

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Multiempresa: ADIF

            $datos = $request->request->get('adif_inventariobundle_activolineal');

            //Setteo de ValoresAtributo para persistir relaciones:
            $idsValoresAtributo = (array_key_exists('valoresAtributo', $datos)) ? $datos['valoresAtributo'] : array();
            foreach ($idsValoresAtributo as $key => $va) {
                $idsValoresAtributo[$key] = $va['valoresAtributo'];
            }
            $valoresAtributo = $em->getRepository('ADIFInventarioBundle:ValoresAtributo')->findById($idsValoresAtributo);
            $entity->setValoresAtributo($valoresAtributo);

            //Setteo de ValoresPropiedad para persistir relaciones:
            $idsValoresPropiedad = (array_key_exists('valoresPropiedad', $datos)) ? $datos['valoresPropiedad'] : array();
            foreach ($idsValoresPropiedad as $key => $va) {
                $idsValoresPropiedad[$key] = $va['propiedadValor'];
            }
            $valoresPropiedad = $em->getRepository('ADIFInventarioBundle:PropiedadValor')->findById($idsValoresPropiedad);
            $entity->setValoresPropiedad($valoresPropiedad);

            //Cambio de estadoInventario:
            $idEstadoInventario = 1;
            if(!empty($idsValoresAtributo) &&  $entity->getEstadoConservacion() != null){
                $idEstadoInventario = 2;
                $numero_interno = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->createQueryBuilder('a')
                    ->select('MAX(a.numeroInterno)')->getQuery()->getSingleScalarResult();
                $entity->setNumeroInterno(($numero_interno === null)?1:$numero_interno+1);
            }

            $estadoInventario = $em->getRepository('ADIFInventarioBundle:EstadoInventario')->find($idEstadoInventario);
            $entity->setEstadoInventario($estadoInventario);

            foreach ($entity->getFotos() as $foto) {
                $foto->setActivoLineal($entity);
            }

            $activoTipo = $em->getRepository('ADIFInventarioBundle:TipoActivo')->find($datos['tipoActivo']);

            if($activoTipo->getDenominacion() != 'Estación'){
              $entity->setZonaVia($datos['zonaVia']);
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('activolineal'));
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
            'page_title' => 'Crear Activo Lineal',
        );
    }

    /**
    * Creates a form to create a ActivoLineal entity.
    *
    * @param ActivoLineal $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(ActivoLineal $entity)
    {
        $form = $this->createForm(new ActivoLinealType(), $entity, array(
            'action' => $this->generateUrl('activolineal_create'),
            'method' => 'POST',
            'where' => 'ActivoLineal' //Le indico que traiga solo las propiedades habilitadas para ActivoLineal
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new ActivoLineal entity.
     *
     * @Route("/crear", name="activolineal_new")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_INVENTARIO_ACTIVO_LINEAL_MODIFICAR')")
     */
    public function newAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SOLO_LECTURA')) {
            throw $this->createAccessDeniedException();
        }
        
        $entity = new ActivoLineal();

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $estadoInventario = $em->getRepository('ADIFInventarioBundle:EstadoInventario')->find(1);
        $entity->setEstadoInventario($estadoInventario); //Estado Borrador por default

        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Activo Lineal'
        );
    }

    /**
     * Finds and displays a ActivoLineal entity.
     *
     * @Route("/{id}", name="activolineal_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ActivoLineal.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Administraci&oacute;n'] = null;

        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Activo Lineal'
        );
    }

    /**
     * Displays a form to edit an existing ActivoLineal entity.
     *
     * @Route("/editar/{id}", name="activolineal_edit")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:ActivoLineal:new.html.twig")
     * @Security("has_role('ROLE_INVENTARIO_ACTIVO_LINEAL_MODIFICAR')")
     */
    public function editAction($id)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SOLO_LECTURA')) {
            throw $this->createAccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ActivoLineal.');
        }

        //Consulto si es un tramo intermedio:
        $progresivaFinalTramo = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->findProgresivaFinalTramo(null, $entity);
        $esTramoIntermedio = ($progresivaFinalTramo != $entity->getProgresivaFinalTramo());

        $editForm = $this->createEditForm($entity, $esTramoIntermedio);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Activo Lineal',
            'esTramoIntermedio' => $esTramoIntermedio
        );
    }

    /**
    * Creates a form to edit a ActivoLineal entity.
    *
    * @param ActivoLineal $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ActivoLineal $entity, $esTramoIntermedio = false)
    {
        $form = $this->createForm(new ActivoLinealType(), $entity, array(
            'action' => $this->generateUrl('activolineal_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'where' => 'ActivoLineal', //Le indico que traiga solo las propiedades habilitadas para ActivoLineal
            'esTramoIntermedio' => $esTramoIntermedio, //Si es tramo intermedio deshabilita la modificación de ciertos datos
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing ActivoLineal entity.
     *
     * @Route("/actualizar/{id}", name="activolineal_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:ActivoLineal:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SOLO_LECTURA')) {
            throw $this->createAccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ActivoLineal.');
        }

        $fotosDB = new ArrayCollection();

        // Se crea un ArrayCollection con las fotos que ya estan en la DB
        foreach ($entity->getFotos() as $adjunto) {
            $fotosDB->add($adjunto);
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $datos = $request->request->get('adif_inventariobundle_activolineal');

            //Setteo de ValoresAtributo para persistir relaciones:
            $idsValoresAtributo = (array_key_exists('valoresAtributo', $datos)) ? $datos['valoresAtributo'] : array();
            foreach ($idsValoresAtributo as $key => $va) {
                $idsValoresAtributo[$key] = $va['valoresAtributo'];
            }
            $valoresAtributo = $em->getRepository('ADIFInventarioBundle:ValoresAtributo')->findById($idsValoresAtributo);
            $entity->setValoresAtributo($valoresAtributo);

            //Setteo de ValoresPropiedad para persistir relaciones:
            $idsValoresPropiedad = (array_key_exists('valoresPropiedad', $datos)) ? $datos['valoresPropiedad'] : array();
            foreach ($idsValoresPropiedad as $key => $va) {
                $idsValoresPropiedad[$key] = $va['propiedadValor'];
            }
            $valoresPropiedad = $em->getRepository('ADIFInventarioBundle:PropiedadValor')->findById($idsValoresPropiedad);
            $entity->setValoresPropiedad($valoresPropiedad);

            $activoTipo = $em->getRepository('ADIFInventarioBundle:TipoActivo')->find($datos['tipoActivo']);

            //Cambio de estadoInventario:
            $idEstadoInventario = 1;
            if(!empty($idsValoresAtributo) &&  $entity->getEstadoConservacion() != null){
                $idEstadoInventario = 2;
                $numero_interno = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->createQueryBuilder('a')
                    ->select('MAX(a.numeroInterno)')->getQuery()->getSingleScalarResult();
                $entity->setNumeroInterno(($numero_interno === null)?1:$numero_interno+1);
            }

            $estadoInventario = $em->getRepository('ADIFInventarioBundle:EstadoInventario')->find($idEstadoInventario);
            $entity->setEstadoInventario($estadoInventario);

            foreach ($entity->getFotos() as $foto) {
                $foto->setActivoLineal($entity);
            }

            // Por cada adjunto original
            foreach ($fotosDB as $foto) {
                // Si fue eliminado
                if (false === $entity->getFotos()->contains($foto)) {
                    $entity->removeFoto($foto);
                    $em->remove($foto);
                }
            }

            if($activoTipo->getDenominacion() != 'Estación'){
              $entity->setZonaVia($datos['zonaVia']);
            }

            $em->merge($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('activolineal'));
        } else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Activo Lineal'
        );
    }

    /**
     * Separar Activos Lineales.
     *
     * @Route("/separar/{id}", name="activolineal_separar")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:ActivoLineal:separar.html.twig")
     * @Security("has_role('ROLE_INVENTARIO_ACTIVO_LINEAL_SEPARAR_UNIR')")
     */
    public function separarAction($id) 
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SOLO_LECTURA')) {
            throw $this->createAccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ActivoLineal.');
        }

        if($entity->getTipoActivo()->getDenominacion() != 'Vía' ){
            return array('error' => 'El tramo debe ser de tipo Vía');
        }

        $activosLineales = [];
        $entity2 = clone $entity;
        $activosLineales['activosLineales'][] = $entity->toArray();
        $activosLineales['activosLineales'][] = $entity2->toArray();

        $form = $this->createSepararForm($activosLineales, $id);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );

    }

    /**
    * Crea un form para separar un ActivoLineal.
    *
    * @param ActivoLineal $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createSepararForm($datos,$id){

        $form = $this->createFormBuilder($datos)
            ->setAction($this->generateUrl('activolineal_guardar_separar', array('id' => $id)))
            ->setMethod('POST')
            ->add('kilometrajeSeparacion', 'number', array(
                'required' => true,
                'grouping' => true,
                'precision' => 3,
                'label' => 'Kilometraje Separación',
                'label_attr' => array('class' => 'control-label'),
                'attr' => array('class' => ' form-control '),
                'constraints' => array(
                    new NotBlank(),
                    new Type(array(
                        'type' => "numeric",
                        'message' => "El valor {{ value }} no es válido."
                    )),
                    new GreaterThan(array(
                        'value' => $datos['activosLineales'][0]['progresivaInicioTramo'],
                        'message' => 'Debe ser mayor al kilometraje de inicio.'
                    )),
                    new LessThan(array(
                        'value' => $datos['activosLineales'][0]['progresivaFinalTramo'],
                        'message' => 'Debe ser menor al kilometraje final.'
                    )),
                ),))
            ->add('activosLineales', 'collection', array(
                'label' => 'Atributos',
                'by_reference' => false,
                'error_bubbling' => false,
                'type' => new ActivoLinealSepararType('ActivoLineal'),))
            ->add('submit', 'submit', array('label' => 'Confirmar'))
            ->getForm();

        return $form;
    }

    /**
     * Separar Activos Lineales.
     *
     * @Route("/guardar_separar/{id}", name="activolineal_guardar_separar")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:ActivoLineal:separar.html.twig")
     */
    public function guardarSepararAction(Request $request, $id)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SOLO_LECTURA')) {
            throw $this->createAccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ActivoLineal.');
        }

        $activosLineales = [];
        $entities = [];
        $entities[0] = $entity;
        $entities[1] = clone $entity;
        $activosLineales['activosLineales'][] = $entities[0]->toArray();
        $activosLineales['activosLineales'][] = $entities[1]->toArray();

        $form = $this->createSepararForm($activosLineales,$id);
        $form->handleRequest($request);

//        print_r((string)$form->get('kilometrajeSeparacion')->getErrors(true));
//        echo 'false';
//        print_r($form->get('kilometrajeSeparacion')->isValid());
//        die();

        if ( $form->get('kilometrajeSeparacion')->isValid() ) {
            $data = $form->getData();
            $formData = $request->request->get('form');

            //$valoresAtributo1 = (!empty($formData['activosLineales'][0]['valoresAtributo']))?$formData['activosLineales'][0]['valoresAtributo']:array();
            //$valoresAtributo2 = (!empty($formData['activosLineales'][1]['valoresAtributo']))?$formData['activosLineales'][1]['valoresAtributo']:array();
            //if( $data['activosLineales'][0]['estadoConservacion'] != $data['activosLineales'][1]['estadoConservacion'] ||
                //$valoresAtributo1 != $valoresAtributo2 ){

            $entities[0]->setProgresivaFinalTramo($data['kilometrajeSeparacion']);
            $entities[1]->setProgresivaInicioTramo($data['kilometrajeSeparacion']);

            foreach($entities as $key => $entityNueva){
                $estadoConservacion = $em->getRepository('ADIFInventarioBundle:EstadoConservacion')->find($formData['activosLineales'][$key]['estadoConservacion']);
                $entityNueva->setEstadoConservacion($estadoConservacion);
                if(!empty($formData['activosLineales'][$key]['valoresAtributo'])){
                    $idsValoresAtributo = [];
                    foreach ($formData['activosLineales'][$key]['valoresAtributo'] as $i => $va) {
                        $idsValoresAtributo[$i] = $va['valoresAtributo'];
                    }
                    $valoresAtributo = $em->getRepository('ADIFInventarioBundle:ValoresAtributo')->findById($idsValoresAtributo);
                    $entityNueva->setValoresAtributo($valoresAtributo);
                }
                $em->persist($entityNueva);
                $em->flush();
            }
            //$em->remove($entity);
            $em->flush();
            return new Response('OK');
            //}else{
                //$form->addError(new FormError('Debe existir alguna modificación en los tramos que justifique la separación.'));
            //}
        }

        $request->attributes->set('form-error', true);
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Renderiza el modal para Unir Activos Lineales.
     *
     * @Route("/unir", name="activolineal_unir")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:ActivoLineal:unir.html.twig")
     * @Security("has_role('ROLE_INVENTARIO_ACTIVO_LINEAL_SEPARAR_UNIR')")
     */
    public function unirAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SOLO_LECTURA')) {
            throw $this->createAccessDeniedException();
        }
        
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $ids = $request->request->get('ids');

            $session = new Session();
            $session->set('ids_union',$ids);

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $activosLineales = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->findByIdsFormated($ids);

            $arrayAtributos = [];
            $arrayDatos = [];
            $tramoNuevo = $activosLineales[0];
            foreach($activosLineales as $activoLineal){
                if($activoLineal['tipoActivo'] != 'Vía' ){
                    return array('error' => 'Todos los tramos deben ser de tipo Vía');
                }
                if(isset($finTramoAnterior) && $activoLineal['progresivaInicioTramo'] != $finTramoAnterior){
                    return array('error' => 'Los tramos no son contiguos');
                }
                if(isset($estadoAnterior) &&  $activoLineal['estadoConservacion'] != $estadoAnterior){
                    return array('error' => 'Los tramos a unir deben poseer el mismo estado de conservación');
                }
                $finTramoAnterior = $activoLineal['progresivaFinalTramo'];
                $estadoAnterior = $activoLineal['estadoConservacion'];
                $arrayAtributos[] = (!empty($activoLineal['atributos']))?$activoLineal['atributos']:array();
                $arrayDatos[] = $activoLineal['datos'];
            }

            uasort($arrayDatos, function ($a, $b) {
                $a = count($a);
                $b = count($b);
                return ($a == $b) ? 0 : (($a > $b) ? -1 : 1);
            });
            $diff_datos = call_user_func_array( 'array_diff_assoc', $arrayDatos);
            if(!empty($diff_datos)){
                return array('error' => 'Los tramos a unir deben poseer la misma línea, operador y división');
            }

            uasort($arrayAtributos, function ($a, $b) {
                $a = count($a);
                $b = count($b);
                return ($a == $b) ? 0 : (($a > $b) ? -1 : 1);
            });
            $diff_atributos = call_user_func_array( 'array_diff_assoc', $arrayAtributos);
            if(!empty($diff_atributos)){
                return array('error' => 'Los tramos a unir deben poseer los mismos atributos con iguales valores');
            }

            $tramoNuevo['progresivaFinalTramo'] = $finTramoAnterior;

            return array(
                'tramos' => $activosLineales,
                'tramoNuevo' => $tramoNuevo,
            );
        }
    }

    /**
     * Unir Activos Lineales.
     *
     * @Route("/guardar_unir", name="activolineal_guardar_union")
     * @Method("GET")
     */
    public function guardarUnionAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SOLO_LECTURA')) {
            throw $this->createAccessDeniedException();
        }
        
        $session = new Session();
        //$session->setId($sid);
        $ids = $session->get('ids_union');
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $activosLineales = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->findBy(['id' => $ids],['progresivaInicioTramo' => 'ASC']);

        if (empty($activosLineales)) {
            throw $this->createNotFoundException('No se puedieron encontrar las entidades ActivoLineal.');
        }

        $nuevo = clone $activosLineales[0];
        $last = end($activosLineales);
        $nuevo->setProgresivaFinalTramo($last->getProgresivaFinalTramo());

        foreach($activosLineales as $entity){
            $em->remove($entity);
        }
        $em->persist($nuevo);
        $em->flush();

        return $this->redirect($this->generateUrl('activolineal'));
    }

    public function getAtribute(){ //integer $id, integer $value
        return "Valor";
    }

    /**
     * @Route("/progresivainiciotramo", name="activolineal_get_progresivainiciotramo")
     * @Method("POST")
     *
     * @param Request $request
     */
    public function getProgresivaInicioTramo(Request $request)
    { 
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $datos = $request->request->all();

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $progresivaFinalTramo = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->findProgresivaFinalTramo($datos);
            $resp = ($progresivaFinalTramo)?$progresivaFinalTramo:0;
            return new Response($resp);
        }
    }

    /**
     * Deletes a ActivoLineal entity.
     *
     * @Route("/borrar/{id}", name="activolineal_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SOLO_LECTURA')) {
            throw $this->createAccessDeniedException();
        }
        
        return parent::baseDeleteAction($id);
    }

    /**
     *
     * @param type $id
     * @return boolean
     */
    public function validateLocalDeleteById($id) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->find($id);
        $progresivaFinalTramo = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->findProgresivaFinalTramo(null, $entity);

        return ($progresivaFinalTramo == $entity->getProgresivaFinalTramo());
    }

    /**
     *
     * @return type
     */
    public function getSessionMessage() {
        return 'No se pudo eliminar el Activo Lineal '
                . 'ya que es un tramo intermedio.';
    }

    /**
     * @Route("/hojaruta_progresivas", name="activolineal_get_progresivas")
     * @Method("POST")
     *
     * @param Request $request
     */
    public function getProgresivas(Request $request)
    {
        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $datos = $request->request->all();

            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $result = $em->getRepository('ADIFInventarioBundle:ActivoLineal')->findProgresivas($datos);
            return new JsonResponse($result);
        }
    }
}
