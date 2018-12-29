<?php

namespace ADIF\ContableBundle\Service;

use ADIF\BaseBundle\Entity\EntityManagers;
use Doctrine\ORM\NoResultException;
use ADIF\ContableBundle\Entity\TipoImpuesto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

/**
 * Description of ComprobanteRetencionService
 */
class ComprobanteRetencionService {

    protected $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * 
     * @param TipoImpuesto $tipoImpuesto
     * @return type
     */
    public function getSiguienteNumeroComprobanteRetencionPorImpuesto(TipoImpuesto $tipoImpuesto) {

        $repository = $this->doctrine
                ->getRepository('ADIFContableBundle:ComprobanteRetencionImpuestoCompras', EntityManagers::getEmContable());

        $query = $repository->createQueryBuilder('criop')
                ->select('criop.numeroComprobanteRetencion')
                ->innerJoin('criop.regimenRetencion', 'rr')
                ->where('rr.tipoImpuesto = :tipoImpuesto')
                ->orderBy('criop.numeroComprobanteRetencion', 'DESC')
                ->setMaxResults(1)
                ->setParameters(new ArrayCollection(array(
                    new Parameter('tipoImpuesto', $tipoImpuesto)
                        )
                        )
                )
                ->getQuery();

        try {
            $siguienteNumero = $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            $siguienteNumero = 0;
        }

        return $siguienteNumero + 1;
    }

}
