<?php

namespace ADIF\ContableBundle\Controller;

use ADIF\ContableBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Devengado controller.
 *
 * @Route("/devengado")
 */
class DevengadoController extends BaseController {

    /**
     *
     * @Route("/", name="devengado")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager($this->getEntityManager());

        $entities = $em->getRepository('ADIFContableBundle:Devengado')
                ->createQueryBuilder('d')
                ->orderBy('d.fechaCreacion', 'DESC')
                ->getQuery()
                ->getResult();

        echo '<table border="1">';
        echo '<thead><tr>' .
        '<th>Fecha creaci&oacute;n</th>' .
        '<th>Monto</th>' .
        '<th>Cuenta contable</th>' .
        '</th></thead>';

        foreach ($entities as $d) {
            echo '<tr>' .
            '<td style="text-align: center;">' . $d->getFechaCreacion()->format('d/m/Y') . '</td>' .
            '<td style="text-align: right;width: 150px;">' . $d->getMonto() . '</td>' .
            '<td>' . $d->getCuentaContable() . '</td>' .
            '</tr>';
        }

        echo '<table>';

        die();
    }

}
