<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ADIF\InventarioBundle\Entity\HojaRuta;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * HojaRuta controller.
 *
 * @Route("/hojaruta")
  */
class HojaRutaController extends BaseController
{
    private $base_breadcrumbs;


    private $constante_material_producido_obra = 1;
    private $constante_material_nuevo = 2;
    private $constante_activo_lineal = 3;
    private $constante_material_rodante = 4;

    private $tipos_materialArray = [
        [
            'view'   => '',
            'formType' => 'ADIF\InventarioBundle\Form\HojaRuta\HojaRutaType',
            'breadcrumbs' => ''
        ],
        [
            'view'   => 'MaterialProducidoDeObra',
//            'entity' => 'InventarioMatNuevoProducido',
            'entity' => 'InventarioMatNuevoProducido',
            'itemEntity' => 'ItemHojaRutaMaterialNuevo',
            'formType' => 'ADIF\InventarioBundle\Form\HojaRuta\HojaRutaMaterialProducidoDeObraType',
            'breadcrumbs' => 'Material Producido De Obra'
        ],
        [
            'view'   => 'MaterialNuevo',
            'entity' => 'InventarioMatNuevoProducido',   // Nombre del respositorio
            'itemEntity' => 'ItemHojaRutaMaterialNuevo',
            'formType' => 'ADIF\InventarioBundle\Form\HojaRuta\HojaRutaNuevoProducidoType',
            'breadcrumbs' => 'Material Nuevo'
        ],
        [
            'view'   => 'ActivoLineal',
            'entity' => 'ActivoLineal',
            'itemEntity' => 'ItemHojaRutaActivoLineal',
            'formType' => 'ADIF\InventarioBundle\Form\HojaRuta\HojaRutaActivoLinealType',
            'breadcrumbs' => 'Activo Lineal'
        ],
        [
            'view'   => 'MaterialRodante',
            'entity' => 'CatalogoMaterialesRodantes',
            'itemEntity' => 'ItemHojaRutaMaterialRodante',
            'formType' => 'ADIF\InventarioBundle\Form\HojaRuta\HojaRutaMaterialRodanteType',
            'breadcrumbs' => 'Material Rodante'
        ],


    ];

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inventario' => '',
            'Movimiento' => '',
            'Administrar Hojas de Ruta' => $this->generateUrl('hojaruta')
        );
    }
    /**
     * Lists all HojaRuta entities.
     *
     * @Route("/{indice}", name="hojaruta")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($indice = 0)
    {
        $bread = $this->base_breadcrumbs;
        //$bread['Hoja de Ruta'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Administrar Hojas de Ruta',
            'page_info' => 'Lista de hoja de ruta',
            'indice' => $indice
        );
    }

    /**
     * Tabla para HojaRuta .
     *
     * @Route("/index_table/", name="hojaruta_table")
     * @Route("/index_table/{indice}", name="hojaruta_table_indice")
     * @Method("GET|POST")
     */
    public function indexTableAction($indice = 0)
    {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if($indice == 0){
            $entities = $em->getRepository('ADIFInventarioBundle:HojaRuta')->findAll();
        }else{
            $entities = $em->getRepository('ADIFInventarioBundle:HojaRuta')->findBy( array("tipoMaterial" => $indice) );
        }

        // Este index es generico, no importa el material seleccionado
        return $this->render('ADIFInventarioBundle:HojaRuta:index_table.html.twig', array(
            'entities' => $entities,
            'indice' => $indice
        ));
    }

    /**
     * Tabla para items HojaRuta .
     *
     * @Route("/items_table/{indice}/{id}", name="hojaruta_items_table_id")
     * @Route("/items_table/{indice}", name="hojaruta_items_table")
     * @Method("GET|POST")
     */
    public function itemsTableAction($indice, $id = null)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if($id == null){
            $entity = new HojaRuta();
        }else{
            $entity = $em->getRepository('ADIFInventarioBundle:HojaRuta')->find($id);
        }

        // Este index es generico, no importa el material seleccionado
        return $this->render("ADIFInventarioBundle:HojaRuta:{$this->tipos_materialArray[$indice]['view']}/items_table.html.twig", array(
            'entity' => $entity
        ));
    }

    /**
     * Creates a new HojaRuta entity.
     *
     * @Route("/insertar", name="hojaruta_create")
     * @Route("/insertar/{indice}", name="hojaruta_create_indice")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:HojaRuta:new.html.twig")
     */
    public function createAction(Request $request, $indice = 0)
    {
        $entity = new HojaRuta();

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $tipoMaterial = $em->getRepository('ADIFInventarioBundle:TipoMaterial')->find($indice);
        $entity->setTipoMaterial($tipoMaterial);

        $estadoHojaRuta = $em->getRepository('ADIFInventarioBundle:EstadoHojaRuta')->findOneByDenominacion('Asignada');
        $entity->setEstadoHojaRuta($estadoHojaRuta);

        $form = $this->createCreateForm($entity, $indice);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //Id_emoresa 1 = ADIF
            $entity->setTipoMaterial($tipoMaterial);

            $estadoHojaRuta = $em->getRepository('ADIFInventarioBundle:EstadoHojaRuta')->findOneByDenominacion('Asignada');
            $entity->setEstadoHojaRuta($estadoHojaRuta);

            // Si viene de Material Nuevo
            if( $indice == $this->constante_material_nuevo || $indice == $this->constante_material_producido_obra ){
                // Seteo la hoja de ruta para cada material nuevo
                foreach ($entity->getItemsHojaRutaNuevoProducido() as $item) {
                    $item->setHojaRuta($entity);
                }
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('hojaruta', array('indice' => $indice)));
        } //.
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return $this->render("ADIFInventarioBundle:HojaRuta:{$this->tipos_materialArray[$indice]['view']}/new.html.twig", array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Hoja Ruta',
            'indice' => $indice
        ));
    }

    /**
    * Creates a form to create a HojaRuta entity.
    *
    * @param HojaRuta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(HojaRuta $entity, $indice)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $form = $this->createForm(new $this->tipos_materialArray[$indice]['formType']($em), $entity, array(
            'action' => $this->generateUrl('hojaruta_create_indice', array('indice' => $indice)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new HojaRuta entity.
     *
     * @Route("/crear", name="hojaruta_new")
     * @Route("/crear/{indice}", name="hojaruta_new_indice")
     * @Method("GET")
     * @Template()
     */
    public function newAction($indice = 0)
    {
        $entity = new HojaRuta();

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $tipoMaterial = $em->getRepository('ADIFInventarioBundle:TipoMaterial')->find($indice);
        $entity->setTipoMaterial($tipoMaterial);

        $estado = $em->getRepository('ADIFInventarioBundle:EstadoHojaRuta')->findOneByDenominacion('Asignada');
        $entity->setEstadoHojaRuta($estado);

        $form   = $this->createCreateForm($entity, $indice);

        $bread = $this->base_breadcrumbs;

        $bread[$this->tipos_materialArray[$indice]['breadcrumbs']] = null;

        return $this->render("ADIFInventarioBundle:HojaRuta:{$this->tipos_materialArray[$indice]['view']}/new.html.twig", array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Hoja de Ruta',
            'indice' => $indice
        ));
    }

    /**
     * Finds and displays a HojaRuta entity.
     *
     * @Route("/{id}", name="hojaruta_show", requirements={"id": "\d+"})
     * @Route("/{id}/{indice}", name="hojaruta_show_indice", requirements={"id": "\d+", "indice": "\d+" })
     * @Method("GET")
     * @Template()
     */
    public function showAction($id, $indice = 0)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:HojaRuta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad HojaRuta.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$this->tipos_materialArray[$indice]['breadcrumbs']] = null;


        return $this->render("ADIFInventarioBundle:HojaRuta:{$this->tipos_materialArray[$indice]['view']}/show.html.twig", array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Hoja de Ruta',
            'idHojaRuta' => $id,
            'indice' => $indice
        ));
    }

    /**
     * Displays a form to edit an existing HojaRuta entity.
     *
     * @Route("/editar/{id}", name="hojaruta_edit")
     * @Route("/editar/{id}/{indice}", name="hojaruta_edit_indice")
     * @Method("GET")
     * @Template("ADIFInventarioBundle:HojaRuta:new.html.twig")
     */
    public function editAction($id, $indice = 0)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:HojaRuta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad HojaRuta.');
        }

        $estado = $em->getRepository('ADIFInventarioBundle:EstadoHojaRuta')->findOneByDenominacion('Asignada');
        $entity->setEstadoHojaRuta($estado);

        $editForm = $this->createEditForm($entity, $indice);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return $this->render("ADIFInventarioBundle:HojaRuta:{$this->tipos_materialArray[$indice]['view']}/new.html.twig", array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Hoja de Ruta',
            'idHojaRuta' => $id,
            'indice' => $indice
        ));
    }

    /**
    * Creates a form to edit a HojaRuta entity.
    *
    * @param HojaRuta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(HojaRuta $entity, $indice)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $form = $this->createForm(new $this->tipos_materialArray[$indice]['formType']($em), $entity, array(
            'action' => $this->generateUrl('hojaruta_update_indice', array('id' => $entity->getId(),'indice' => $indice)),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing HojaRuta entity.
     *
     * @Route("/actualizar/{id}", name="hojaruta_update")
     * @Route("/actualizar/{id}/{indice}", name="hojaruta_update_indice")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:HojaRuta:new.html.twig")
     */
    public function updateAction(Request $request, $id, $indice = 0)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:HojaRuta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad HojaRuta.');
        }

        $editForm = $this->createEditForm($entity, $indice);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('hojaruta', array('indice' => $indice)));
        } //.
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return $this->render("ADIFInventarioBundle:HojaRuta:{$this->tipos_materialArray[$indice]['view']}/new.html.twig", array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Hoja de Ruta',
            'indice' => $indice
        ));
    }
    /**
     * Deletes a HojaRuta entity.
     *
     * @Route("/borrar/{id}/{indice}", name="hojaruta_delete")
     * @Method("GET")
     */
    public function deleteAction($id, $indice = 0)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFInventarioBundle:HojaRuta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad HojaRuta.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('hojaruta', array('indice' => $indice)));
    }


    /**
     * @Route("/obtener_items/{indice}", name="obtener_items")
     */
    public function getItemsAction(Request $request, $indice) {

        if($request->isXmlHttpRequest()) { // is it an Ajax request?
            $datos = $request->request->all();

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            $entity = $this->tipos_materialArray[$indice]['entity'];
            $datos = ($entity == 'CatalogoMaterialesRodantes')? $this->fixNomCampos($datos) : $datos;


            $result = $em->getRepository('ADIFInventarioBundle:'.$entity)->getItems($datos);

            // Este index es generico, no importa el material seleccionado
            // $return = $this->render("ADIFInventarioBundle:HojaRuta:{$this->tipos_materialArray[$indice]['view']}/new_items_table.html.twig", array(
            //     'result' => $result
            // ));
            return new JsonResponse($result);
        }
        throw $this->createNotFoundException('Esta peticion no es un AJAX');
    }


    private function fixNomCampos($campos)
    {
        $camposOut = [];
        foreach ($campos as $key => $value) {
            $key= ($key == 'tipoRodante') ? 'idTipoRodante' : $key;
            $key= ($key == 'operador') ? 'idOperador' : $key;
            $key= ($key == 'linea') ? 'idLinea' : $key;
            $key= ($key == 'estacion') ? 'idEstacion' : $key;
            $key= ($key == 'grupoRodante') ? 'idGrupoRodante' : $key;

            $camposOut[$key] = $value;
        }
        return $camposOut;
    }
}
