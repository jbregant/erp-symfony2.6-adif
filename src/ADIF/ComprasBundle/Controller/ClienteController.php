<?php

namespace ADIF\ComprasBundle\Controller;

use ADIF\BaseBundle\Entity\EntityManagers;
use ADIF\ComprasBundle\Controller\BaseController;
use ADIF\ComprasBundle\Entity\CertificadoExencion;
use ADIF\ComprasBundle\Entity\Cliente;
use ADIF\ComprasBundle\Entity\ClienteProveedor;
use ADIF\ComprasBundle\Form\ClienteType;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoInternoCuentaContable;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoContrato;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoComprobanteVenta;
use ADIF\ContableBundle\Entity\Constantes\ConstanteTipoResponsable;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Types\Type;
use ADIF\ContableBundle\Entity\Constantes\ConstanteCodigoRegimenPercepcion;

/**
 * Cliente controller.
 *
 * @Route("/cliente")
 */
class ClienteController extends BaseController {

    private $base_breadcrumbs;
	
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Clientes' => $this->generateUrl('cliente')
        );
    }

    /**
     * Lists all Cliente entities.
     *
     * @Route("/", name="cliente")
     * @Method("GET")
     * @Template()
     */
//	 * @Security("has_role('ROLE_VISUALIZAR_CLIENTES')")
    public function indexAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Clientes'] = null;

        return array(
            'breadcrumbs' => $bread,
            'page_title' => 'Cliente',
            'page_info' => 'Lista de clientes'
        );
    }

    /**
     * Tabla para Cliente.
     *
     * @Route("/index_table/", name="cliente_table")
     * @Method("GET|POST")
     */
    public function indexTableAction() {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('cuitDni', 'cuitDni');
        $rsm->addScalarResult('razonSocial', 'razonSocial');
        $rsm->addScalarResult('actividades', 'actividades');
        $rsm->addScalarResult('denominacionEstado', 'denominacionEstado');
        $rsm->addScalarResult('aliasTipoImportancia', 'aliasTipoImportancia');
        $rsm->addScalarResult('codigocliente', 'codigocliente');
        $rsm->addScalarResult('representantelegal', 'representantelegal');
        $rsm->addScalarResult('extrajero', 'extrajero');
        $rsm->addScalarResult('dc_direccion', 'dc_direccion');
        $rsm->addScalarResult('dl_direccion', 'dl_direccion');
//             $rsm->addScalarResult('dc_calle','dc_calle');
//             $rsm->addScalarResult('dc_numero','dc_numero');
//             $rsm->addScalarResult('dc_piso','dc_piso');
//             $rsm->addScalarResult('dc_depto','dc_depto');
//             $rsm->addScalarResult('dc_cp','dc_cp');
//             $rsm->addScalarResult('dc_provincia','dc_provincia');
//             $rsm->addScalarResult('dc_localidad','dc_localidad');
//             $rsm->addScalarResult('dl_calle','dl_calle');
//             $rsm->addScalarResult('dl_numero','dl_numero');
//             $rsm->addScalarResult('dl_piso','dl_piso');
//             $rsm->addScalarResult('dl_depto','dl_depto');
//             $rsm->addScalarResult('dl_cp','dl_cp');
//             $rsm->addScalarResult('dl_provincia','dl_provincia');
//             $rsm->addScalarResult('dl_localidad','dl_localidad');
        $rsm->addScalarResult('contactos', 'contactos');
        $rsm->addScalarResult('numeroIIBB', 'numeroIIBB');
        $rsm->addScalarResult('condicionIVA', 'condicionIVA');
        $rsm->addScalarResult('exentoIVA', 'exentoIVA');
        $rsm->addScalarResult('condicionGANANCIAS', 'condicionGANANCIAS');
        $rsm->addScalarResult('exentoGANANCIAS', 'exentoGANANCIAS');
        $rsm->addScalarResult('condicionSUSS', 'condicionSUSS');
        $rsm->addScalarResult('exentoSUSS', 'exentoSUSS');
        $rsm->addScalarResult('condicionIIBB', 'condicionIIBB');
        $rsm->addScalarResult('exentoIIBB', 'exentoIIBB');
        $rsm->addScalarResult('calificacionfiscal', 'calificacionfiscal');
        $rsm->addScalarResult('problemasafip', 'problemasafip');
        $rsm->addScalarResult('riesgofiscal', 'riesgofiscal');
        $rsm->addScalarResult('magnitudessuperadas', 'magnitudessuperadas');

        $native_query = $em->createNativeQuery('
            SELECT
            *
            FROM
                vistaclientes            
      
        ', $rsm);

        $entities = $native_query->getResult();


        return $this->render('ADIFComprasBundle:Cliente:index_table.html.twig', array(
                    'entities' => $entities
        ));
    }

    /**
     * Creates a new Cliente entity.
     *
     * @Route("/insertar", name="cliente_create")
     * @Method("POST")
     * @Template("ADIFComprasBundle:Cliente:new.html.twig")
     */
    public function createAction(Request $request) {
        $cliente = new Cliente();

        $form = $this->createCreateForm($cliente);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager($this->getEntityManager());

            // Actualiza los Certificados de Exención
            $this->updateCertificadosExencion($em, $cliente);

            // Actualiza los archivos adjuntos
            $this->updateAdjuntos($cliente->getClienteProveedor());

            // Actualiza el Convenio Multilateral
            $this->updateConvenioMultilateral($em, $cliente->getClienteProveedor());

            // Verificar si tengo el cliente proveedor
            if ($cliente->getClienteProveedor()->getEsExtranjero()) {

                $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                        ->findOneBy(array('codigoIdentificacion' => $cliente->getClienteProveedor()->getCodigoIdentificacion()));
            } //.
            else {

                if ($cliente->getClienteProveedor()->getCuit() != null) {

                    $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                            ->findOneBy(array('CUIT' => $cliente->getClienteProveedor()->getCuit()));
                } //.
                else {
                    $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                            ->findOneBy(array('DNI' => $cliente->getClienteProveedor()->getDNI()));
                }
            }

            if ($clienteProveedor != null) {

                $clienteProveedorService = $this->get('adif.cliente_proveedor_service');

                // Actualizo el Código del ClienteProveedor
                $clienteProveedor->setCodigo($clienteProveedorService->getSiguienteCodigoClienteProveedor());

                $cliente->setClienteProveedor($clienteProveedor);
            }

            // Creo/actualizo históricos de condición fiscal
            $this->chequearHistoricoCondicionFiscal($em, $cliente);

            $em->persist($cliente);
            $em->flush();

            return $this->redirect($this->generateUrl('cliente'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;

        return array(
            'entity' => $cliente,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear cliente',
        );
    }

    /**
     * Creates a form to create a Cliente entity.
     *
     * @param Cliente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Cliente $entity) {
        $form = $this->createForm(new ClienteType(), $entity, array(
            'action' => $this->generateUrl('cliente_create'),
            'method' => 'POST',
            'entity_manager_compras' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
            'entity_manager_hhrr' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Cliente entity.
     *
     * @Route("/crear", name="cliente_new")
     * @Method("GET")
     * @Template()
     */
//	 * @Security("has_role('ROLE_CREAR_MODIFICAR_CLIENTES')")
    public function newAction(Request $request) {

        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());
        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $parametroBusqueda = $request->query->get('esExtranjero') ? ($request->query->get('DNI') !== null ? 'DNI' : 'codigoIdentificacion') : ($request->query->get('DNI') !== null ? 'DNI' : 'CUIT');

        $clienteProveedor = $emCompras->getRepository('ADIFComprasBundle:ClienteProveedor')
                ->findOneBy(array($parametroBusqueda => $request->query->get('identificacion')));

        // Nuevo
        if (!$clienteProveedor) {

            $clienteProveedorService = $this->get('adif.cliente_proveedor_service');

            $cliente = new Cliente();

            $clienteProveedor = new ClienteProveedor();

            $clienteProveedor->setCodigo($clienteProveedorService->getSiguienteCodigoClienteProveedor());

            if ($request->query->get('esExtranjero')) {
                if ($request->query->get('DNI') !== null) {
                    $clienteProveedor->setDNI($request->query->get('DNI'));
                } else {
                    $clienteProveedor->setCodigoIdentificacion($request->query->get('identificacion'));
                }
                $clienteProveedor->setEsExtranjero(true);
            } //.
            else {
                if ($request->query->get('DNI') !== null) {
                    $clienteProveedor->setDNI($request->query->get('DNI'));
                } else {
                    $clienteProveedor->setCUIT($request->query->get('identificacion'));
                }
            }

            $cuentaContable = $emContable->getRepository('ADIFContableBundle:CuentaContable')
                    ->findOneByCodigoInterno(ConstanteCodigoInternoCuentaContable::CLIENTE);

            $cliente->setCuentaContable($cuentaContable);

            $cliente->setClienteProveedor($clienteProveedor);
        } //.
        else {

            $clienteExiste = $emCompras->getRepository('ADIFComprasBundle:Cliente')->findOneByClienteProveedor($clienteProveedor);

            if (!$clienteExiste) {
                $cliente = new Cliente();
                $cliente->setClienteProveedor($clienteProveedor);
            } else {
                //ya existe
                $codigo = $request->query->get('esExtranjero') ? 'CDI' : 'CUIT';
                $this->get('session')->getFlashBag()->add(
                        'warning', 'Ya existe un Cliente con el número de ' . $codigo . ' ingresado.'
                );

                return $this->redirect($this->generateUrl('cliente'));
            }
        }

        // Obtengo el TipoMoneda de curso legal
        $tipoMonedaMCL = $emContable->getRepository('ADIFContableBundle:TipoMoneda')->
                findOneBy(array('esMCL' => true), array('id' => 'desc'), 1, 0);

        $cliente->setTipoMoneda($tipoMonedaMCL);

        // Obtengo el EstadoCliente cuya denominacion sea igual a "Activo"
        $estadoClienteActivo = $emCompras->getRepository('ADIFComprasBundle:EstadoCliente')->
                findOneBy(array('denominacionEstado' => 'Activo'), array('id' => 'desc'), 1, 0);

        $cliente->setEstadoCliente($estadoClienteActivo);
        $form = $this->createCreateForm($cliente);

        $bread = $this->base_breadcrumbs;
        $bread['Crear'] = null;


        return array(
            'entity' => $cliente,
            'form' => $form->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Crear cliente'
        );
    }

    /**
     * Finds and displays a Cliente entity.
     *
     * @Route("/{id}", name="cliente_show")
     * @Method("GET")
     * @Template()
     */
//	 * @Security("has_role('ROLE_VISUALIZAR_CLIENTES')")
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFComprasBundle:Cliente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cliente.');
        }

        $bread = $this->base_breadcrumbs;
        $bread[$entity->getClienteProveedor()->getRazonSocial()] = null;


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver cliente'
        );
    }

    /**
     * Displays a form to edit an existing Cliente entity.
     *
     * @Route("/editar/{id}", name="cliente_edit")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Cliente:new.html.twig")
     */
//	 * @Security("has_role('ROLE_CREAR_MODIFICAR_CLIENTES')")
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $cliente = $em->getRepository('ADIFComprasBundle:Cliente')->find($id);

        if (!$cliente) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cliente.');
        }

        $editForm = $this->createEditForm($cliente);

        $bread = $this->base_breadcrumbs;
        $bread[$cliente->getClienteProveedor()->getRazonSocial()] = $this->generateUrl('cliente_show', array('id' => $cliente->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $cliente,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar cliente'
        );
    }

    /**
     * Creates a form to edit a Cliente entity.
     *
     * @param Cliente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Cliente $entity) {
        $form = $this->createForm(new ClienteType(), $entity, array(
            'action' => $this->generateUrl('cliente_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'entity_manager_compras' => $this->getDoctrine()->getManager($this->getEntityManager()),
            'entity_manager_contable' => $this->getDoctrine()->getManager(EntityManagers::getEmContable()),
            'entity_manager_hhrr' => $this->getDoctrine()->getManager(EntityManagers::getEmRrhh()),
            
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Edits an existing Cliente entity.
     *
     * @Route("/actualizar/{id}", name="cliente_update")
     * @Method("PUT")
     * @Template("ADIFComprasBundle:Cliente:new.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $cliente = $em->getRepository('ADIFComprasBundle:Cliente')->find($id);

        if (!$cliente) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cliente.');
        }

        $adjuntosOriginales = new ArrayCollection();

        $datosContactoOriginales = new ArrayCollection();

        // Creo un ArrayCollection de los adjuntos actuales en la BBDD
        foreach ($cliente->getClienteProveedor()->getArchivos() as $adjunto) {
            $adjuntosOriginales->add($adjunto);
        }

        // Creo un ArrayCollection de los DatoContacto actuales en la BBDD
        foreach ($cliente->getClienteProveedor()->getDatosContacto() as $datoContacto) {
            $datosContactoOriginales->add($datoContacto);
        }

        if ($cliente->getClienteProveedor()->getEsExtranjero()) {
            $codigoAnterior = $cliente->getClienteProveedor()->getCodigoIdentificacion();
        } else {
            $codigoAnterior = $cliente->getClienteProveedor()->getCUIT();
        }

        $editForm = $this->createEditForm($cliente);
        $editForm->handleRequest($request);

        $verificarCodigo = false;

        $valorBusqueda = null;

        // Verifico si tengo que validar si el CUIT o CDI esta en uso
        if ($cliente->getClienteProveedor()->getEsExtranjero()) {

            if ($codigoAnterior != $cliente->getClienteProveedor()->getCodigoIdentificacion()) {
                $parametroBusqueda = 'codigoIdentificacion';
                $valorBusqueda = $cliente->getClienteProveedor()->getCodigoIdentificacion();
                $verificarCodigo = true;
            }
        } else {

            if ($codigoAnterior != $cliente->getClienteProveedor()->getCUIT()) {
                $parametroBusqueda = 'CUIT';
                $valorBusqueda = $cliente->getClienteProveedor()->getCUIT();
                $verificarCodigo = true;
            }
        }

        if ($valorBusqueda != null && $verificarCodigo) {

            // Verifico si el CUIT o CDI esta en uso
            $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                    ->findOneBy(array($parametroBusqueda => $valorBusqueda));

            if ($clienteProveedor) {
                $editForm->addError(
                        new FormError('El número de ' . $parametroBusqueda . ' proporcionado pertenece a otro cliente')
                );
            }
        }

        if ($editForm->isValid()) {

            $cliente->setFechaUltimaActualizacion(new \DateTime());

            // Actualiza los Certificados de Exención
            $this->updateCertificadosExencion($em, $cliente);

            // Actualiza los archivos adjuntos
            $this->updateAdjuntos($cliente->getClienteProveedor());

            // Actualiza el Convenio Multilateral
            $this->updateConvenioMultilateral($em, $cliente->getClienteProveedor());

            // Por cada adjunto original
            foreach ($adjuntosOriginales as $adjunto) {

                // Si fue eliminado
                if (false === $cliente->getClienteProveedor()->getArchivos()->contains($adjunto)) {

                    $cliente->getClienteProveedor()->removeArchivo($adjunto);

                    $em->remove($adjunto);
                }
            }

            // Por cada datoContacto original
            foreach ($datosContactoOriginales as $datoContacto) {

                // Si fue eliminado
                if (false === $cliente->getClienteProveedor()->getDatosContacto()->contains($datoContacto)) {

                    $cliente->getClienteProveedor()->removeDatosContacto($datoContacto);

                    $em->remove($datoContacto);
                }
            }

            // Creo/actualizo históricos de condición fiscal
            $this->chequearHistoricoCondicionFiscal($em, $cliente);

            $em->flush();

            return $this->redirect($this->generateUrl('cliente'));
        } //. 
        else {
            $request->attributes->set('form-error', true);
        }

        $bread = $this->base_breadcrumbs;
        $bread[$cliente->getClienteProveedor()->getRazonSocial()] = $this->generateUrl('cliente_show', array('id' => $cliente->getId()));
        $bread['Editar'] = null;

        return array(
            'entity' => $cliente,
            'form' => $editForm->createView(),
            'breadcrumbs' => $bread,
            'page_title' => 'Editar cliente'
        );
    }

    /**
     * Deletes a Cliente entity.
     *
     * @Route("/borrar/{id}", name="cliente_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFComprasBundle:Cliente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cliente.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('cliente'));
    }

    /**
     * 
     * @param type $em
     * @param \ADIF\ComprasBundle\Controller\ClienteProveedor $clienteProveedor
     */
    private function updateConvenioMultilateral($em, ClienteProveedor $clienteProveedor) {
        if (!$clienteProveedor->getCondicionIngresosBrutos() == ConstanteTipoResponsable::CONVENIO_MULTILATERAL) {
            if (null != $clienteProveedor->getConvenioMultilateralIngresosBrutos()) {
                $em->remove($clienteProveedor->getConvenioMultilateralIngresosBrutos());
                $clienteProveedor->setConvenioMultilateralIngresosBrutos(null);
            }
        } else {
            if (null != $clienteProveedor->getConvenioMultilateralIngresosBrutos()) {
                $clienteProveedor->getConvenioMultilateralIngresosBrutos()->setDatosImpositivos($clienteProveedor->getDatosImpositivos());
            }
        }
    }

    /**
     * @Route("/estados", name="cliente_estados")
     */
    public function listaEstadoClienteAction() {

        $repository = $this->getDoctrine()
                ->getRepository('ADIFComprasBundle:EstadoCliente', $this->getEntityManager());

        $query = $repository->createQueryBuilder('e')
                ->select('e.id', 'e.denominacionEstado')
                ->orderBy('e.denominacionEstado', 'ASC')
                ->getQuery()
                ->useResultCache(true, 36000, 'cliente_estados')
                ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return new JsonResponse($query->getResult());
    }

    /**
     * 
     * @param type $em
     * @param Cliente $cliente
     */
    private function chequearHistoricoCondicionFiscal($em, Cliente $cliente) {

        $clienteProveedor = null;

        if ($cliente->getClienteProveedor()->getId() != null) {

            $clienteProveedor = $em->getRepository('ADIFComprasBundle:ClienteProveedor')
                    ->find($cliente->getClienteProveedor()->getId());
        }

        if (null != $clienteProveedor) {
            $this->cerrarHistoricoCondicionFiscalAnteriores($em, $cliente);
        }

        $this->crearHistoricoCondicionFiscal($em, $cliente);
    }

    /**
     * 
     * @param type $em
     * @param Cliente $cliente
     */
    private function crearHistoricoCondicionFiscal($em, Cliente $cliente) {

        // ClienteProveedorHistoricoGanancias
        $historicoClienteGanancias = new \ADIF\ComprasBundle\Entity\ClienteProveedorHistoricoGanancias();

        $historicoClienteGanancias->setFechaDesde(new \DateTime());
        $historicoClienteGanancias->setClienteProveedor($cliente->getClienteProveedor());

        $historicoClienteGanancias->setCondicion($cliente->getClienteProveedor()->getCondicionGanancias());
        $historicoClienteGanancias->setExento($cliente->getClienteProveedor()->getExentoGanancias());

        if (null != $cliente->getCertificadoExencionGanancias()) {
            $historicoClienteGanancias->setCertificadoExencion(clone $cliente->getCertificadoExencionGanancias());
        }

        $em->persist($historicoClienteGanancias);
        /* FIN ClienteProveedorHistoricoGanancias */


        // ClienteProveedorHistoricoIIBB
        $historicoClienteIIBB = new \ADIF\ComprasBundle\Entity\ClienteProveedorHistoricoIIBB();

        $historicoClienteIIBB->setFechaDesde(new \DateTime());
        $historicoClienteIIBB->setClienteProveedor($cliente->getClienteProveedor());

        $historicoClienteIIBB->setCondicion($cliente->getClienteProveedor()->getCondicionIngresosBrutos());
        $historicoClienteIIBB->setExento($cliente->getClienteProveedor()->getExentoIngresosBrutos());
        $historicoClienteIIBB->setPasiblePercepcion($cliente->getPasiblePercepcionIngresosBrutos());

        if (null != $cliente->getCertificadoExencionIngresosBrutos()) {
            $historicoClienteIIBB->setCertificadoExencion(clone $cliente->getCertificadoExencionIngresosBrutos());
        }

        $convenioMultilateral = $cliente->getClienteProveedor()
                ->getConvenioMultilateralIngresosBrutos();

        if (null != $convenioMultilateral) {

            $historicoClienteIIBB->setJurisdiccion($convenioMultilateral->getJurisdiccion());
            $historicoClienteIIBB->setPorcentajeAplicacionCABA($convenioMultilateral
                            ->getPorcentajeAplicacionCABA()
            );
        }

        $em->persist($historicoClienteIIBB);
        /* FIN ClienteProveedorHistoricoIIBB */


        // ClienteProveedorHistoricoIVA
        $historicoClienteIVA = new \ADIF\ComprasBundle\Entity\ClienteProveedorHistoricoIVA();

        $historicoClienteIVA->setFechaDesde(new \DateTime());
        $historicoClienteIVA->setClienteProveedor($cliente->getClienteProveedor());

        $historicoClienteIVA->setCondicion($cliente->getClienteProveedor()->getCondicionIVA());
        $historicoClienteIVA->setExento($cliente->getClienteProveedor()->getExentoIVA());

        if (null != $cliente->getCertificadoExencionIVA()) {
            $historicoClienteIVA->setCertificadoExencion(clone $cliente->getCertificadoExencionIVA());
        }

        $em->persist($historicoClienteIVA);
        /* FIN ClienteProveedorHistoricoIVA */


        // ClienteProveedorHistoricoSUSS
        $historicoClienteSUSS = new \ADIF\ComprasBundle\Entity\ClienteProveedorHistoricoSUSS();

        $historicoClienteSUSS->setFechaDesde(new \DateTime());
        $historicoClienteSUSS->setClienteProveedor($cliente->getClienteProveedor());

        $historicoClienteSUSS->setCondicion($cliente->getClienteProveedor()->getCondicionSUSS());
        $historicoClienteSUSS->setExento($cliente->getClienteProveedor()->getExentoSUSS());

        if (null != $cliente->getCertificadoExencionSUSS()) {
            $historicoClienteSUSS->setCertificadoExencion(clone $cliente->getCertificadoExencionSUSS());
        }

        $em->persist($historicoClienteSUSS);
        /* FIN ClienteProveedorHistoricoSUSS */
    }

    /**
     * 
     * @param type $em
     * @param Cliente $cliente
     */
    private function cerrarHistoricoCondicionFiscalAnteriores($em, Cliente $cliente) {

        $now = new \DateTime();

        /* @var ClienteProveedorHistoricoIIBB */
        $historicoClienteGanancias = $em->getRepository('ADIFComprasBundle:ClienteProveedorHistoricoGanancias')->
                findOneBy(
                array('clienteProveedor' => $cliente->getClienteProveedor(), 'fechaHasta' => null), //
                array('id' => 'desc'), 1, 0
        );

        $historicoClienteGanancias->setFechaHasta($now);

        $em->persist($historicoClienteGanancias);


        /* @var ClienteProveedorHistoricoIIBB */
        $historicoClienteIIBB = $em->getRepository('ADIFComprasBundle:ClienteProveedorHistoricoIIBB')->
                findOneBy(
                array('clienteProveedor' => $cliente->getClienteProveedor(), 'fechaHasta' => null), //
                array('id' => 'desc'), 1, 0
        );

        $historicoClienteIIBB->setFechaHasta($now);

        $em->persist($historicoClienteIIBB);


        /* @var ClienteProveedorHistoricoIVA */
        $historicoClienteIVA = $em->getRepository('ADIFComprasBundle:ClienteProveedorHistoricoIVA')->
                findOneBy(
                array('clienteProveedor' => $cliente->getClienteProveedor(), 'fechaHasta' => null), //
                array('id' => 'desc'), 1, 0
        );

        $historicoClienteIVA->setFechaHasta($now);

        $em->persist($historicoClienteIVA);


        /* @var ClienteProveedorHistoricoSUSS */
        $historicoClienteSUSS = $em->getRepository('ADIFComprasBundle:ClienteProveedorHistoricoSUSS')->
                findOneBy(
                array('clienteProveedor' => $cliente->getClienteProveedor(), 'fechaHasta' => null), //
                array('id' => 'desc'), 1, 0
        );

        $historicoClienteSUSS->setFechaHasta($now);

        $em->persist($historicoClienteSUSS);
    }

    /**
     * 
     * @param ClienteProveedor $clienteProveedor
     */
    private function updateAdjuntos(ClienteProveedor $clienteProveedor) {

        foreach ($clienteProveedor->getArchivos() as $adjunto) {

            if ($adjunto->getArchivo() != null) {

                $adjunto->setClienteProveedor($clienteProveedor);

                $adjunto->setNombre($adjunto->getArchivo()->getClientOriginalName());
            }
        }
    }

    /**
     * @Route("/autocomplete/form", name="autocomplete_cliente")
     */
    public function getClientesAction(Request $request) {

        $term = $request->query->get('term', null);

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $clientes = $em->getRepository('ADIFComprasBundle:Cliente')
                ->createQueryBuilder('c')
                ->innerJoin('c.clienteProveedor', 'cp')
                ->where('upper(cp.razonSocial) LIKE :term')
                ->orWhere('cp.CUIT LIKE :term')
                ->orWhere('cp.DNI LIKE :term')
                ->orWhere('cp.codigoIdentificacion LIKE :term')
                ->orderBy('cp.razonSocial', 'DESC')
                ->setParameter('term', '%' . strtoupper($term) . '%')
                ->getQuery()
                ->getResult();

        $jsonResult = [];

        foreach ($clientes as $cliente) {

            $jsonResult[] = array(
                'id' => $cliente->getId(),
                'razonSocial' => $cliente->getClienteProveedor()->getRazonSocial(),
                'CUIT' => $cliente->getClienteProveedor()->getCUIT(),
                'condicionIVA' => $cliente->getClienteProveedor()->getCondicionIVA()//
                        ? $cliente->getClienteProveedor()->getCondicionIVA()->__toString() //
                        : '',
            );
        }

        return new JsonResponse($jsonResult);
    }

    /**
     * 
     * @param Request $request
     * 
     * @Route("/letra_comprobante", name="cliente_letra_comprobante")
     * @Method("POST")
     */
    function getLetraComprobanteVenta(Request $request) {

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $idCliente = $request->get('idCliente');

        $cliente = $em->getRepository('ADIFComprasBundle:Cliente')
                ->find($idCliente);

        if (!$cliente) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cliente.');
        }

        $letraComprobante = $cliente->getLetraComprobanteVenta();

        return new JsonResponse($letraComprobante);
    }

    /**
     * Muestra la cuenta corriente del cliente.
     *
     * @Route("/{idCliente}/cuentacorriente", name="cliente_cta_cte")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Cliente:cuenta_corriente.html.twig")
     */
    public function cuentaCorrienteIndexAction($idCliente) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em_contable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        /* @var $cliente Cliente */
        $cliente = $em->getRepository('ADIFComprasBundle:Cliente')->find($idCliente);

        if (!$cliente) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cliente.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Clientes'] = $this->generateUrl('cliente');
        $bread[$cliente->getClienteProveedor()->getRazonSocial()] = $this->generateUrl('cliente_show', ['id' => $cliente->getId()]);
        $bread['Cuenta corriente'] = null;

        $contratos = $em_contable->getRepository('ADIFContableBundle:Facturacion\ContratoVenta')->findByIdCliente($idCliente);


        return array(
            'contratos' => $contratos,
            'cliente' => $cliente,
            'breadcrumbs' => $bread,
            'page_title' => 'Cliente | Detalle de cuenta corriente',
            'page_info' => 'Cuenta corriente'
        );
    }

    /**
     * Muestra la cuenta corriente del cliente.
     *
     * @Route("/{idCliente}/cuentacorriente/{idContrato}", name="cliente_cta_cte_detalle")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Cliente:cuenta_corriente_detalle.html.twig")
     */
//	 * @Security("has_role('ROLE_VISUALIZAR_CC_CLIENTES_CONTRATOS')")
    public function cuentaCorrienteDetalleIndexAction($idCliente, $idContrato) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em_contable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        /* @var $cliente Cliente */
        $cliente = $em->getRepository('ADIFComprasBundle:Cliente')->find($idCliente);

        if (!$cliente) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cliente.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Clientes'] = $this->generateUrl('cliente');
        $bread[$cliente->getClienteProveedor()->getRazonSocial()] = $this->generateUrl('cliente_show', ['id' => $cliente->getId()]);
        $bread['Cuenta corriente'] = null;

        $contrato = $em_contable->getRepository('ADIFContableBundle:Facturacion\ContratoVenta')->find($idContrato);
        if (!$contrato) {
            throw $this->createNotFoundException('No se puede encontrar la entidad ContratoVenta.');
        }

        return array(
            'contrato' => $contrato,
            'cliente' => $cliente,
            'breadcrumbs' => $bread,
            'page_title' => 'Cliente | Detalle de cuenta corriente',
            'page_info' => 'Cuenta corriente'
        );
    }

    /**
     * Tabla para ContratoVenta .
     *
     * @Route("/index_table_contrato/", name="contrato_table_cliente")
     * @Method("GET|POST")
     */
    public function indexTableByClienteAction(Request $request) {

        $fechaRequest = $request->query->get('fechaFin');
        $fecha_fin = $fechaRequest == null ?
                (new \DateTime()) :
                \DateTime::createFromFormat('d/m/Y H:i:s', $fechaRequest . ' 23:59:59');


        $em = $this->getDoctrine()->getManager('contable');
        $contratos = $em->getRepository('ADIFContableBundle:Facturacion\ContratoVenta')
                ->createQueryBuilder('c')
                ->where('c.idCliente = :idCliente')
                ->setParameter('idCliente', $request->request->get('id_cliente'))
                ->orderBy('c.numeroContrato', 'ASC')
                ->getQuery()
                ->getResult();

        $contratosJson = [];
        foreach ($contratos as $contrato) {

            $contratosJson[] = [
                'id' => $contrato->getId(),
                'claseContrato' => $contrato->getClaseContrato()->__toString(),
                'numeroContrato' => $contrato->getNumeroContrato(),
                'saldoPendienteCobro' => $contrato->getSaldoPendienteCobro($fecha_fin)
            ];
        }
        return new JsonResponse($contratosJson);
    }

    /**
     * Muestra la cuenta corriente del cliente.
     *
     * @Route("/cuentacorrientedetalletotal/{idCliente}/", name="cliente_cta_cte_detalle_total")
     * @Method("GET")
     * @Template("ADIFComprasBundle:Cliente:cuenta_corriente_detalle_total.html.twig")
     */
//	 * @Security("has_role('ROLE_VISUALIZAR_CC_CLIENTES_CONTRATOS')")
    public function cuentaCorrienteDetalleTotalIndexAction($idCliente) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $em_contable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        /* @var $cliente Cliente */
        $cliente = $em->getRepository('ADIFComprasBundle:Cliente')->find($idCliente);

        if (!$cliente) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Cliente.');
        }

        $bread = $this->base_breadcrumbs;
        $bread['Clientes'] = $this->generateUrl('cliente');
        $bread[$cliente->getClienteProveedor()->getRazonSocial()] = $this->generateUrl('cliente_show', ['id' => $cliente->getId()]);
        $bread['Cuenta corriente'] = null;

        $contratos = $em_contable->getRepository('ADIFContableBundle:Facturacion\ContratoVenta')
                    ->getContratosConComprobantesByIdCliente($cliente->getId());
        
        // Obtengo aquellos comprobantes de venta que NO tienene contrato
        $comprobantesSinContrato = $this->getComprobantesSinContratoByIdCliente($cliente->getId());

        
        //\Doctrine\Common\Util\Debug::dump( $comprobantesSinContrato ); exit;

        //var_dump($comprobantesSinContrato); exit;
        
        $query_anticipos = $em_contable->getRepository('ADIFContableBundle:Cobranza\AnticipoCliente')
                ->createQueryBuilder('a')
                ->innerJoin('a.cobroRenglonCobranza', 'c')
                ->where('a.idCliente = ' . $cliente->getId())
                ->andWhere('a.saldo > 0')// OR c.monto = 0')
                ->andWhere('a.fechaBaja IS NULL')
                ->getQuery();

        $anticipos = $query_anticipos->getResult();

        return array(
            //'contrato' => $contrato,
            'contratos' => $contratos,
            'comprobantes_sin_contrato' => $comprobantesSinContrato,
            'anticipos' => $anticipos,
            'cliente' => $cliente,
            'breadcrumbs' => $bread,
            'page_title' => 'Cliente | Detalle de cuenta corriente',
            'page_info' => 'Cuenta corriente'
        );
    }

    /**
     * 
     * @param type $em
     * @param Cliente $cliente
     */
    private function updateCertificadosExencion($em, Cliente $cliente) {

        if (null != $cliente->getCertificadoExencionIVA()) {

            if (null == $cliente->getCertificadoExencionIVA()->getNumeroCertificado()) {
                $em->remove($cliente->getCertificadoExencionIVA());
                $cliente->setCertificadoExencionIVA(null);
            } else {
                $this->setAdjuntoACertificadoExencion($cliente->getCertificadoExencionIVA());
            }
        }

        if (null != $cliente->getCertificadoExencionGanancias()) {

            if (null == $cliente->getCertificadoExencionGanancias()->getNumeroCertificado()) {
                $em->remove($cliente->getCertificadoExencionGanancias());
                $cliente->setCertificadoExencionGanancias(null);
            } else {
                $this->setAdjuntoACertificadoExencion($cliente->getCertificadoExencionGanancias());
            }
        }

        if (null != $cliente->getCertificadoExencionIngresosBrutos()) {

            if (null == $cliente->getCertificadoExencionIngresosBrutos()->getNumeroCertificado()) {
                $em->remove($cliente->getCertificadoExencionIngresosBrutos());
                $cliente->setCertificadoExencionIngresosBrutos(null);
            } else {
                $this->setAdjuntoACertificadoExencion($cliente->getCertificadoExencionIngresosBrutos());
            }
        }

        if (null != $cliente->getCertificadoExencionSUSS()) {

            if (null == $cliente->getCertificadoExencionSUSS()->getNumeroCertificado()) {
                $em->remove($cliente->getCertificadoExencionSUSS());
                $cliente->setCertificadoExencionSUSS(null);
            } else {
                $this->setAdjuntoACertificadoExencion($cliente->getCertificadoExencionSUSS());
            }
        }
    }

    /**
     * 
     * @param \ADIF\ComprasBundle\Controller\CertificadoExencion $certificadoExencion
     */
    private function setAdjuntoACertificadoExencion(CertificadoExencion $certificadoExencion) {

        if (null != $certificadoExencion->getAdjunto() && //
                null != $certificadoExencion->getAdjunto()->getArchivo()) {

            $certificadoExencion->getAdjunto()
                    ->setNombre($certificadoExencion->getAdjunto()->getArchivo()->getClientOriginalName());

            $certificadoExencion->getAdjunto()->setCertificadoExencion($certificadoExencion);
        }
    }

    /**
     * Reporte resumen de cuenta corriente de cliente.
     *
     * @Route("/resumen_cuenta_corriente/", name="cliente_resumen_cuenta_corriente")
     * @Method("GET|POST")
     */
//     * @Security("has_role('ROLE_EMITIR_RESUMEN_CC_CLIENTES')")
    public function reporteResumenCuentaCorrienteAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Clientes'] = $this->generateUrl('cliente');
        $bread['Resumen cuenta corriente cliente'] = null;

        return $this->render('ADIFComprasBundle:Cliente:reporte.resumen_cuenta_corriente.html.twig', array(
//                    'clientes' => $clientes,
                    'breadcrumbs' => $bread,
                    'page_title' => 'Cliente | Resumen cuenta corriente',
                    'page_info' => 'Resumen cuenta corriente cliente'
        ));
    }

    /**
     * 
     * @return type
     */
    private function getComprobantesSinContratoByIdCliente($idCliente) {

        $emContable = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $comprobantesSinContrato = $emContable
                ->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')
                ->createQueryBuilder('c', 'ajuste')
                ->join('c.tipoComprobante', 't')
				->leftJoin('c.comprobantesAjustes', 'ajuste')
                ->where('c.contrato IS NULL')
                //->andWhere('t.id != :idCupon')
                ->andWhere('c.idCliente = :idCliente')
                //->setParameter('idCupon', ConstanteTipoComprobanteVenta::CUPON)
                ->setParameter('idCliente', $idCliente)
                ->addOrderBy('c.fechaComprobante', 'ASC')
                ->addOrderBy('c.tipoComprobante', 'DESC')
                ->addOrderBy('c.numero', 'DESC')
                ->getQuery()
                ->getResult();

        return $comprobantesSinContrato;
    }

    /**
     * @param Request $request
     * 
     * @Route("/percepciones", name="cliente_percepciones")
     * @Method("POST")
     */
    public function getPercepciones(Request $request) {

        $emCompras = $this->getDoctrine()->getManager(EntityManagers::getEmCompras());

        $idCliente = $request->request->get('idCliente');

        $cliente = $emCompras->getRepository('ADIFComprasBundle:Cliente')
                ->findOneById($idCliente);

        $alicuotaIIBB = 0;
        $alicuotaIVA = 0;

        $regimenPercepcion = $this->get('adif.percepciones_service')
                ->getRegimenIIBB($cliente);

        if ($regimenPercepcion) {
			
			if ($regimenPercepcion->getCodigo() == ConstanteCodigoRegimenPercepcion::CODIGO_IIBB_CABA) {
				// Voy a buscar la alicuota a la tabla iibb_caba.iibb_caba y no la alicuota del regimen
				$alicuotaIIBB = $this->get('adif.percepciones_service')->getAlicuotaIIBB($cliente);
				
			} else {
				
				$porcentajeAlicuota = $this->get('adif.percepciones_service')
                    ->getPorcentajeAlicuotaIIBB($cliente);

				$alicuotaIIBB = $porcentajeAlicuota * ($regimenPercepcion->getAlicuota() / 100);
			}
        }


        $regimenPercepcionIVA = $this->get('adif.percepciones_service')
                ->getRegimenIVA($cliente);

        if ($regimenPercepcionIVA) {
            $alicuotaIVA = $regimenPercepcionIVA->getAlicuota() / 100;
        }


        $percepciones = array(
            'alicuota_iibb' => $alicuotaIIBB,
            'alicuota_iva' => $alicuotaIVA
        );

        return new JsonResponse($percepciones);
    }

    /**
     * Tabla para reporte resumen de cuenta corriente .
     *
     * @Route("/index_table_reporte_resumen_cuenta_corriente/", name="index_table_cliente_reporte_resumen_cuenta_corriente")
     * @Method("GET|POST")
     */
    public function indexTableReporteResumenCuetaCorrienteAction(Request $request) {

        $fechaRequest = $request->query->get('fechaFin');
        $fecha = $fechaRequest == null ?
                (new \DateTime()) :
                \DateTime::createFromFormat('d/m/Y H:i:s', $fechaRequest . ' 23:59:59');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('codigo', 'codigo');
        $rsm->addScalarResult('razonSocial', 'razonSocial');
        $rsm->addScalarResult('cuit', 'cuit');
        $rsm->addScalarResult('tipoContratacion', 'tipoContratacion');
        $rsm->addScalarResult('cuentaContable', 'cuentaContable');
        $rsm->addScalarResult('numeroContrato', 'numeroContrato');
        $rsm->addScalarResult('saldoComprobantesConContrato', 'saldoComprobantesConContrato');
        $rsm->addScalarResult('saldoComprobantesSinContrato', 'saldoComprobantesSinContrato');
        $rsm->addScalarResult('anticipoCliente', 'anticipoCliente');
        $rsm->addScalarResult('total', 'total');

        $native_query = $em->createNativeQuery('
            call sp_vista_cuentacorrienteclientes(?)
        ', $rsm);

        $native_query->setParameter(1, $fecha, Type::DATETIME);

        $clientes = $native_query->getResult();



        return $this->render('ADIFComprasBundle:Cliente:index_table_reporte.resumen_cuenta_corriente.html.twig', array(
                    'clientes' => $clientes
        ));
    }
    
    /**
     * Reporte facturacion por provincias
     *
     * @Route("/reporte_facturacion_provincias/", name="reporte_facturacion_provincias")
     * @Method("GET|POST")
     */
    public function reporteFacturacionProvinciasAction() {

        $bread = $this->base_breadcrumbs;
        $bread['Clientes'] = $this->generateUrl('cliente');
        $bread['Facturacion provincias'] = null;

        return $this->render('ADIFComprasBundle:Cliente:reporte.facturacion_provincias.html.twig', array(
                    'breadcrumbs' => $bread,
                    'page_title' => 'Facturacion | Reporte Facturacion Provincias',
                    'page_info' => 'Reporte facturacion por provincias'
        ));
    }

    /**
     * Tabla para reporte de facturacion por provincias
     *
     * @Route("/index_table_reporte_facturacion_provincias/", name="index_table_reporte_facturacion_provincias")
     * @Method("GET|POST")
     */
    public function indexTableReporteFacturacionProvinciasAction(Request $request) {

        $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaInicio') . ' 00:00:00');

        $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaFin') . ' 23:59:59');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('Provincia', 'Provincia');
        $rsm->addScalarResult('TipoComprobante', 'TipoComprobante');
        $rsm->addScalarResult('letra', 'letra');
        $rsm->addScalarResult('NumeroComprobante', 'NumeroComprobante');
        $rsm->addScalarResult('Fecha', 'Fecha');
        $rsm->addScalarResult('Destinatario', 'Destinatario');
        $rsm->addScalarResult('Neto', 'Neto');
        $rsm->addScalarResult('Gravado', 'Gravado');
        $rsm->addScalarResult('Exento', 'Exento');

        $native_query = $em->createNativeQuery('
            call sp_vista_reporteFacturacionProvincias(?,?,?)
        ', $rsm);

        $native_query->setParameter(1, '');
        $native_query->setParameter(2, $fechaInicio, Type::DATETIME);
        $native_query->setParameter(3, $fechaFin, Type::DATETIME);

        $clientes = $native_query->getResult();



        return $this->render('ADIFComprasBundle:Cliente:index_table_reporte.facturacion_provincias.html.twig', array(
                    'clientes' => $clientes
        ));
    }

    /**
     * Tabla para reporte de facturacion por provincias sumarizada
     *
     * @Route("/index_table_reporte_facturacion_provincias_sum/", name="index_table_reporte_facturacion_provincias_sum")
     * @Method("GET|POST")
     */
    public function indexTableReporteFacturacionProvinciasSumAction(Request $request) {

        $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaInicio') . ' 00:00:00');

        $fechaFin = \DateTime::createFromFormat('d/m/Y H:i:s', $request->query->get('fechaFin') . ' 23:59:59');

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('Provincia', 'Provincia');
        $rsm->addScalarResult('Neto', 'Neto');
        $rsm->addScalarResult('Gravado', 'Gravado');
        $rsm->addScalarResult('Exento', 'Exento');

        $native_query = $em->createNativeQuery('
            call sp_vista_reporteFacturacionProvincias(?,?,?)
        ', $rsm);
        $native_query->setParameter(1, 'sumarizado');
        $native_query->setParameter(2, $fechaInicio, Type::DATETIME);
        $native_query->setParameter(3, $fechaFin, Type::DATETIME);

        $clientes = $native_query->getResult();



        return $this->render('ADIFComprasBundle:Cliente:index_table_reporte.facturacion_provincias_sum.html.twig', array(
                    'clientes' => $clientes
        ));
    }
	
	/**
	* Me devuelve las partidas abiertas que tenga el proveedor de compra/obra
	* @Route("/get_partidas_abiertas_cliente")
	*/
	public function getPartidasAbiertasByIdCliente(Request $request)
	{
		$idCliente = $request->get('id');
		
		if ($idCliente == null) {
			return new JsonResponse(array());
		}

		$em = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('idComprobante', 'idComprobante');
		$rsm->addScalarResult('fechaContable', 'fechaContable');
        $rsm->addScalarResult('tipoComprobante', 'tipoComprobante');
        $rsm->addScalarResult('letra', 'letra');
        $rsm->addScalarResult('puntoVenta', 'puntoVenta');
        $rsm->addScalarResult('numero', 'numero');
        //$rsm->addScalarResult('saldo', 'saldo');
        $rsm->addScalarResult('total', 'total');
		$rsm->addScalarResult('estado', 'estado');
        
        $nativeQuery = $em->createNativeQuery('CALL sp_get_partidas_abiertas_cliente(?)', $rsm);
		$nativeQuery->setParameter(1, $idCliente);
		
		$comprobantes = $nativeQuery->getResult();
		
		for($i = 0; $i < count($comprobantes); $i++){
			$comprobante = $em->getRepository('ADIFContableBundle:Comprobante')->find($comprobantes[$i]['idComprobante']);
			$comprobantes[$i]['total'] = number_format($comprobantes[$i]['total'], 2, ',', '.');
			$comprobantes[$i]['saldo'] = number_format($comprobante->getSaldoALaFecha(new \DateTime()), 2, ',', '.');
		}
		
		return new JsonResponse($comprobantes);
	}
	
	
    /**
     * @Route("/cuentacorrientedetalletotal/subircupones/", name="subir_cupones")
     * @Method("POST")
    */

    public function getFormSaldarCuponesAction(Request $request) {

        $ids  = $request->get('ids');

        $em = $this->getDoctrine()->getManager(EntityManagers::getEmContable());

        $error = false;
        $comprobanteVenta = '';

        foreach ($ids as $id) {

            $comprobanteVenta = $em->getRepository('ADIFContableBundle:Facturacion\ComprobanteVenta')->find($id);

            if (!$comprobanteVenta) {
                throw $this->createNotFoundException('No se puede encontrar la entidad ComprobanteVenta.');
            }

            try {

                $comprobanteVenta->setSaldar(true);

                $em->persist($comprobanteVenta);

                $response = array('result' => 'OK');

            } catch (Exception $e) {
                $response = array('result' => 'NOK', 'message' => $e->getMessage());
                $error = true;
            }

        }

        //\Doctrine\Common\Util\Debug::dump( JsonResponse($response) ); exit; 

        if (!$error) {
            $em->flush();
        }

        return new JsonResponse($response);

    }

}