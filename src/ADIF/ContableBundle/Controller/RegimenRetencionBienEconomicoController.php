<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ADIF\ContableBundle\Entity\RegimenRetencionBienEconomico;
use Symfony\Component\HttpFoundation\Response;

/**
 * RegimenRetencionBienEconomico controller.
 *
 * @Route("/regimenretencionbieneconomico")
 */
class RegimenRetencionBienEconomicoController extends BaseController {

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        parent::setContainer($container);
    }

    /**
     * 
     * @throws NotFoundHttpException
     * 
     * @Route("/regimenesmultiple/", name="asignar_regimenes_multiple")
     * @Method("GET|POST")
     */
    public function setRegimenesMultiplesBienes(Request $request) {
        if ((!$request->request->get('ids')) || (!$request->request->get('regimen'))) {
            $this->get('session')->getFlashBag()->add(
                    'error', 'Debe seleccionar al menos un bien y un régimen para realizar la asignación.'
            );
            return $this->redirect($this->generateUrl('bieneconomico'));
        }
        $idsBienes = json_decode($request->request->get('ids', '[]'));
        $idRegimen = json_decode($request->request->get('regimen'));

        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $regimen = $em->getRepository('ADIFContableBundle:RegimenRetencion')->find($idRegimen);

        if (!$regimen) {
            throw $this->createNotFoundException('No se puede encontrar el Régimen.');
        }

        $constanteImpuesto = $regimen->getTipoImpuesto()->getDenominacion();

        foreach ($idsBienes as $idBien) {

            $regimenViejo = $em->getRepository('ADIFContableBundle:RegimenRetencionBienEconomico')->getRegimenRetencionBienEconomicoByImpuestoYBienEconomico($constanteImpuesto, $idBien);
            if ($regimenViejo != null) {
                $em->remove($regimenViejo);
            }

            $regimenBien = new RegimenRetencionBienEconomico();
            $regimenBien->setIdBienEconomico($idBien);
            $regimenBien->setRegimenRetencion($regimen);
            $em->persist($regimenBien);
        }

        $em->flush();

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

}
