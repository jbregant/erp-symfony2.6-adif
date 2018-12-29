<?php

namespace ADIF\ContableBundle\Controller\EgresoValor;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ADIF\ContableBundle\Entity\EgresoValor\DevolucionDinero;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRendicionEgresoValor;
use ADIF\BaseBundle\Entity\EntityManagers;

/**
 * EgresoValor\DevolucionDinero controller.
 *
 * @Route("/egresovalor_devoluciondinero")
 */
class DevolucionDineroController extends BaseController {

    private $base_breadcrumbs;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->base_breadcrumbs = array(
            'Inicio' => '',
            'Egresos de valor' => $this->generateUrl('egresovalor')
        );
    }

    /**
     * Finds and displays a EgresoValor\DevolucionDinero entity.
     *
     * @Route("/{id}", name="egresovalor_devoluciondinero_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\DevolucionDinero')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\DevolucionDinero.');
        }
        $egresoValor = $entity->getRendicionEgresoValor()->getEgresoValor();
        $responsable = $egresoValor->getTipoEgresoValor()->__toString()
                . ' - ' . $egresoValor->getResponsableEgresoValor()->getNombre();

        $bread = $this->base_breadcrumbs;
        $bread[$responsable] = null;
        $bread['Rendici&oacute;n'] = $this->generateUrl('comprobanteegresovalor_new', array('id' => $egresoValor->getId()));


        return array(
            'entity' => $entity,
            'breadcrumbs' => $bread,
            'page_title' => 'Ver Devolución'
        );
    }

    /**
     *
     * @Route("/insertar/{id}", name="egresovalor_devoluciondinero_create")
     * @Method("GET|POST")
     * @Template()
     */
    public function createAction(Request $request, $id) {

        $emContable = $this->getDoctrine()->getManager($this->getEntityManager());
        $emRRHH = $this->getDoctrine()->getManager(EntityManagers::getEmRrhh());

        $idDevolucion = $request->request->get('idDevolucion');

        /* @var $egresoValor \ADIF\ContableBundle\Entity\EgresoValor\EgresoValor */
        $egresoValor = $emContable->getRepository('ADIFContableBundle:EgresoValor\EgresoValor')
                ->find($id);

        if (!$egresoValor) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor.');
        }

        $requestRendicionEgresoValor = $request->request
                ->get('adif_contablebundle_comprobanteegresovalor');

        $parametrosDevolucionDinero = $requestRendicionEgresoValor['devolucionDinero'];

        $idCuentaBancariaADIF = $parametrosDevolucionDinero['cuenta'];

        $rendicionEgresoValor = $egresoValor->getRendicionEgresoValor();

        if ($rendicionEgresoValor->getEstadoRendicionEgresoValor() == null) {

            $rendicionEgresoValor->setEstadoRendicionEgresoValor(
                    $emContable->getRepository('ADIFContableBundle:EgresoValor\EstadoRendicionEgresoValor')
                            ->findOneByCodigo(ConstanteEstadoRendicionEgresoValor::ESTADO_BORRADOR)
            );
        }

        $cuentaBancariaADIF = $emRRHH->getRepository('ADIFRecursosHumanosBundle:CuentaBancariaADIF')
                ->find($idCuentaBancariaADIF);

        $montoDevolucion = $parametrosDevolucionDinero['montoDevolucion'];
        $numero = $parametrosDevolucionDinero['numero'];
        $fechaFormulario = $parametrosDevolucionDinero['fechaIngresoADIF'];
        $fecha = new \DateTime(date(substr($fechaFormulario, 6, 4) . '-' . substr($fechaFormulario, 3, 2) . '-' . substr($fechaFormulario, 0, 2)));
        $referencia = $parametrosDevolucionDinero['numeroReferencia'];

        if ($idDevolucion != null) {
            $devolucionDineroOriginal = $emContable->getRepository('ADIFContableBundle:EgresoValor\DevolucionDinero')
                    ->find($idDevolucion);

            if (!$devolucionDineroOriginal) {
                throw $this->createNotFoundException('No se puede encontrar la entidad DevolucionDinero.');
            }

            $devolucionDineroOriginal->setMontoDevolucion($montoDevolucion);
            $devolucionDineroOriginal->setNumero($numero);
            $devolucionDineroOriginal->setCuenta($cuentaBancariaADIF);
            $devolucionDineroOriginal->setFechaIngresoADIF($fecha);
            $devolucionDineroOriginal->setNumeroReferencia($referencia);
        } else {

            $devolucionDinero = new DevolucionDinero();

            $devolucionDinero->setMontoDevolucion($montoDevolucion);
            $devolucionDinero->setNumero($numero);
            $devolucionDinero->setFechaIngresoADIF($fecha);
            $devolucionDinero->setNumeroReferencia($referencia);
            $devolucionDinero->setCuenta($cuentaBancariaADIF);

            $devolucionDinero->setRendicionEgresoValor($rendicionEgresoValor);

            $rendicionEgresoValor->addDevolucion($devolucionDinero);

            $emContable->persist($devolucionDinero);
        }
        $emContable->flush();

        $this->get('session')->getFlashBag()
                ->add('success', "La devoluci&oacute;n de dinero "
                        . "se carg&oacute; con &eacute;xito");

        return $this->redirect($this->generateUrl('comprobanteegresovalor_new', array('id' => $id)));
    }

    /**
     * Devuelve el template para pagar ordenes de pago
     *
     * @Route("/editar/{id}", name="egresovalor_devoluciondinero_editar")
     * @Method("GET|POST")   
     */
    public function editarAction($id) {

        $emContable = $em = $this->getDoctrine()->getManager($this->getEntityManager());

        /* @var $devolucionDinero \ADIF\ContableBundle\Entity\EgresoValor\DevolucionDinero */
        $devolucionDinero = $emContable->getRepository('ADIFContableBundle:EgresoValor\DevolucionDinero')
                ->find($id);

        if (!$devolucionDinero) {
            throw $this->createNotFoundException('No se puede encontrar la entidad DevolucionDinero.');
        }

        return new JsonResponse(array(
            'id' => $devolucionDinero->getId(),
            'cuentaBancoAdif' => $devolucionDinero->getIdCuenta(),
            'monto' => $devolucionDinero->getMontoDevolucion(),
            'numero' => $devolucionDinero->getNumero(),
            'fechaIngresoADIF' => $devolucionDinero->getFechaIngresoADIF()->format('d/m/Y'),
            'numeroReferencia' => $devolucionDinero->getNumeroReferencia()
        ));
    }

    /**
     * Deletes a EgresoValor\DevolucionDinero entity.
     *
     * @Route("/borrar/{id}", name="egresovalor_devoluciondinero_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());
        $entity = $em->getRepository('ADIFContableBundle:EgresoValor\DevolucionDinero')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad EgresoValor\DevolucionDinero.');
        }

        $idEgresoValor = $entity->getRendicionEgresoValor()->getEgresoValor()->getId();

        $em->remove($entity);
        $em->flush();

        $this->get('session')->getFlashBag()
                ->add('success', "La devoluci&oacute;n se elimin&oacute; con &eacute;xito");

        return $this->redirect($this->generateUrl('comprobanteegresovalor_new', array('id' => $idEgresoValor)));
    }

}
