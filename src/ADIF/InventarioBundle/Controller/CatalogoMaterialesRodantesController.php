<?php

namespace ADIF\InventarioBundle\Controller;

use ADIF\InventarioBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ADIF\InventarioBundle\Entity\CatalogoMaterialesRodantes;
use ADIF\InventarioBundle\Form\CatalogoMaterialesRodantesType;
use ADIF\BaseBundle\Controller\AlertControllerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CatalogoMaterialesRodantes controller.
 *
 * @Route("/catalogomaterialesrodantes")
 * @Security("has_role('ROLE_INVENTARIO_MATERIAL_RODANTE_CONSULTA')")
 */
class CatalogoMaterialesRodantesController extends BaseController implements AlertControllerInterface
{
    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Inventarios' => '',
            'Datos Maestros' => '',
            'Materiales Rodantes' => $this->generateUrl('catalogomaterialesrodantes')
        );
    }

    /**
     * Lists all CatalogoMaterialesRodantes entities.
     *
     * @Route("/", name="catalogomaterialesrodantes")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $bread = $this->base_breadcrumbs;
        $bread['Materiales Rodantes'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Materiales Rodantes',
            'page_info' => 'Lista de Materiales Rodantes'
        );
    }

    /**
     * Tabla para CatalogoMaterialesRodantes .
     *
     * @Route("/index_table/", name="catalogomaterialesrodantes_table")
     * @Method("GET|POST")
     */
    public function indexTableAction()
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        //$entities = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')->findAll();
        $entities = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')->findAllMaterialeRodante();

        $bread = $this->base_breadcrumbs;
        $bread['Materiales Rodantes'] = null;

        return $this->render('ADIFInventarioBundle:CatalogoMaterialesRodantes:index_table.html.twig',
                      array('entities' => $entities));
    }
    /**
     * Creates a new CatalogoMaterialesRodantes entity.
     *
     * @Route("/insertar", name="catalogomaterialesrodantes_create")
     * @Method("POST")
     * @Template("ADIFInventarioBundle:CatalogoMaterialesRodantes:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new CatalogoMaterialesRodantes();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        if ($form->isValid()) {
            $entity->setIdEmpresa(1); //ADIF;
            $idEstadoInventario = 1;

            if($entity->getIdOperador() != null){
              $idEstadoInventario = 2;
            }

            $estadoInventario = $em->getRepository('ADIFInventarioBundle:EstadoInventario')->find($idEstadoInventario);
            $entity->setIdEstadoInventario($estadoInventario);

            $datos = $request->request->get('adif_inventariobundle_catalogomaterialesrodantes');

            //Setteo de ValoresPropiedad para persistir relaciones:
            $idsValoresPropiedad = (array_key_exists('valoresPropiedad', $datos)) ? $datos['valoresPropiedad'] : array();
            foreach ($idsValoresPropiedad as $key => $va) {
                $idsValoresPropiedad[$key] = $va['propiedadValor'];
            }
            $valoresPropiedad = $em->getRepository('ADIFInventarioBundle:PropiedadValor')->findById($idsValoresPropiedad);
            $entity->setValoresPropiedad($valoresPropiedad);

            foreach ($entity->getFotos() as $foto) {
                $foto->setCatalogoMaterialesRodantes($entity);
            }

            $em->persist($entity);
            $em->flush();

            if($entity->getNumeroVehiculo() == null){
              $entity->setNumeroVehiculo('SN -'.$entity->getId());
            }

            if($entity->getNumeroInterno() == null && $entity->getIdEstadoInventario() == 'Activo'){
                $entity->setNumeroInterno($entity->getId());
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('catalogomaterialesrodantes'));
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
            'page_title' => 'Crear Material Rodante',
        );
    }

    /**
    * Creates a form to create a CatalogoMaterialesRodantes entity.
    *
    * @param CatalogoMaterialesRodantes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(CatalogoMaterialesRodantes $entity)
    {
        $form = $this->createForm(new CatalogoMaterialesRodantesType(), $entity, array(
            'action' => $this->generateUrl('catalogomaterialesrodantes_create'),
            'method' => 'POST',
            'where' => 'MaterialRodante' //Le indico que traiga solo las propiedades habilitadas para MaterialRodante
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new CatalogoMaterialesRodantes entity.
     *
     * @Route("/crear", name="catalogomaterialesrodantes_new")
     * @Method("GET")
     * @Security("has_role('ROLE_INVENTARIO_MATERIAL_RODANTE_MODIFICAR')")
     * @Template()
     */
    public function newAction()
    {
        $entity = new CatalogoMaterialesRodantes();

        $entity->setIdEstadoInventario(1); //Estado Borrador por default

        $form   = $this->createCreateForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear Material Rodante'
        );
    }

    /**
     * Finds and displays a CatalogoMaterialesRodantes entity.
     *
     * @Route("/{id}", name="catalogomaterialesrodantes_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CatalogoMaterialesRodantes.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Materiales Rodantes'] = null;


        return array(
            'entity'      => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Material Rodante'
        );
    }

    /**
     * Displays a form to edit an existing CatalogoMaterialesRodantes entity.
     *
     * @Route("/editar/{id}", name="catalogomaterialesrodantes_edit")
     * @Method("GET")
     * @Security("has_role('ROLE_INVENTARIO_MATERIAL_RODANTE_MODIFICAR')")
     * @Template("ADIFInventarioBundle:CatalogoMaterialesRodantes:new.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CatalogoMaterialesRodantes.');
        }

        $editForm = $this->createEditForm($entity);

        $bread = $this->base_breadcrumbs;
        $bread['Editar'] = null;

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar Material Rodante'
        );
    }

    /**
    * Creates a form to edit a CatalogoMaterialesRodantes entity.
    *
    * @param CatalogoMaterialesRodantes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CatalogoMaterialesRodantes $entity)
    {
        $form = $this->createForm(new CatalogoMaterialesRodantesType(), $entity, array(
            'action' => $this->generateUrl('catalogomaterialesrodantes_update', array('id' => $entity->getId())),
            'method' => 'PUT'
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing CatalogoMaterialesRodantes entity.
     *
     * @Route("/actualizar/{id}", name="catalogomaterialesrodantes_update")
     * @Method("PUT")
     * @Template("ADIFInventarioBundle:CatalogoMaterialesRodantes:new.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad CatalogoMaterialesRodantes.');
        }

        $fotosDB = new ArrayCollection();

        // Se crea un ArrayCollection con las fotos que ya estan en la DB
        foreach ($entity->getFotos() as $adjunto) {
            $fotosDB->add($adjunto);
        }


        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $datos = $request->request->get('adif_inventariobundle_catalogomaterialesrodantes');

            //Setteo de ValoresPropiedad para persistir relaciones:
            $idsValoresPropiedad = (array_key_exists('valoresPropiedad', $datos)) ? $datos['valoresPropiedad'] : array();
            foreach ($idsValoresPropiedad as $key => $va) {
                $idsValoresPropiedad[$key] = $va['propiedadValor'];
            }
            $valoresPropiedad = $em->getRepository('ADIFInventarioBundle:PropiedadValor')->findById($idsValoresPropiedad);
            $entity->setValoresPropiedad($valoresPropiedad);

            $idEstadoInventario = 1;

            if($entity->getIdOperador() != null){
              $idEstadoInventario = 2;
            }

            $estadoInventario = $em->getRepository('ADIFInventarioBundle:EstadoInventario')->find($idEstadoInventario);
            $entity->setIdEstadoInventario($estadoInventario);

            foreach ($entity->getFotos() as $foto) {
                $foto->setCatalogoMaterialesRodantes($entity);
            }

            // Por cada adjunto original
            foreach ($fotosDB as $foto) {
                // Si fue eliminado
                if (false === $entity->getFotos()->contains($foto)) {
                    $entity->removeFoto($foto);
                    $em->remove($foto);
                }
            }

            $em->merge($entity);
            $em->flush();

            if($entity->getNumeroVehiculo() == null){
              $entity->setNumeroVehiculo('SN -'.$entity->getId());
            }

            if($entity->getNumeroInterno() == null && $entity->getIdEstadoInventario() == 'Activo'){
                $entity->setNumeroInterno($entity->getId());
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('catalogomaterialesrodantes'));
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
            'page_title' => 'Editar Material Rodante'
        );
    }
    /**
     * Deletes a CatalogoMaterialesRodantes entity.
     *
     * @Route("/borrar/{id}", name="catalogomaterialesrodantes_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Material Rodante.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('catalogomaterialesrodantes'));
    }

    /**
     * @Route("/mr_por_num_inventario", name="mr_por_num_inventario")
     */
    public function getNumInventarioByTipoRodanteAction(Request $request) {
        if($request->isXmlHttpRequest()) { //si es una peticion ajax
            $ids = $request->request->all();
            $em = $this->getDoctrine()->getManager($this->getEntityManager());
            $repository = $this->getDoctrine()
                    ->getRepository('ADIFInventarioBundle:CatalogoMaterialesRodantes', $this->getEntityManager());
            return new JsonResponse($repository->getMaterialRodante($ids));
        }
        throw $this->createNotFoundException('Not AJAX');
    }

}
