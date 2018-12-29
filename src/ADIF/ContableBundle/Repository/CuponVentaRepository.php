<?php

namespace ADIF\ContableBundle\Repository;

use ADIF\ContableBundle\Entity\EstadoComprobante;
use Doctrine\ORM\EntityRepository;

/**
 * Description of CuponVentaRepository
 */
class CuponVentaRepository extends EntityRepository {

    /**
     * 
     * @param type $numeroCupon
     * @param type $fechaVencimiento
     * @return type
     */
    public function validarNumeroCuponUnico($numeroCupon, $fechaVencimiento) {

        $query = $this->createQueryBuilder('cv')
                ->select('count(cv.id)')
                ->innerJoin('cv.estadoComprobante', 'e')
                ->andWhere('cv.numeroCupon = :numeroCupon')
                ->andWhere('e.id != :idEstadoComprobante')
                ->setParameter('numeroCupon', $numeroCupon)
                ->setParameter('idEstadoComprobante', EstadoComprobante::__ESTADO_ANULADO);

        if ($fechaVencimiento !== null) {
            $query->andWhere('cv.fechaVencimiento = :fechaVencimiento')
                    ->setParameter('fechaVencimiento', $fechaVencimiento, \Doctrine\DBAL\Types\Type::DATE);
        } else {
            $query->andWhere('cv.fechaVencimiento IS NULL');
        }

        return $query->getQuery()->getSingleScalarResult() == 0;
    }

}
