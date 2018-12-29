<?php

namespace ADIF\ContableBundle\Repository;

use ADIF\ContableBundle\Entity\BeneficiarioLiquidacion;
use ADIF\ContableBundle\Entity\Constantes\ConstanteEstadoRenglonRetencionLiquidacion;
use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class RenglonRetencionLiquidacionRepository extends EntityRepository {

    /**
     */
    public function getRenglonesByBeneficiario($beneficiario, $historico) {

        $query = $this->createQueryBuilder('r')
            ->innerJoin('r.estadoRenglonRetencionLiquidacion', 'e')
            ->innerJoin('r.beneficiarioLiquidacion', 'b')
            ->where('e.denominacion = (:estado)')
            ->setParameter('estado', $historico ? ConstanteEstadoRenglonRetencionLiquidacion::CON_PAGO : ConstanteEstadoRenglonRetencionLiquidacion::PENDIENTE);
        
        if($beneficiario == BeneficiarioLiquidacion::__OTROS){
            $query->andWhere('b.id NOT IN (:beneficiarios)')
                ->setParameter('beneficiarios', array(BeneficiarioLiquidacion::__APDFA, BeneficiarioLiquidacion::__UF, BeneficiarioLiquidacion::__NACION));
        } else {
            $query->andWhere('b.id = :beneficiario')
                ->setParameter('beneficiario', $beneficiario);
        }
        
        //echo $query->getQuery()->getSQL();die;
        
        return $query->getQuery()->getResult();
    }

}
