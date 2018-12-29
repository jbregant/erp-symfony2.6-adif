<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ConceptoVersionRepository extends EntityRepository {
    
    /**
     * Última version del concepto para asociarla a la $liquidacionEmpleadoConcepto
     * 
     * @param int $idConcepto
     * @return \ADIF\RecursosHumanosBundle\Entity\Concepto
     */
    public function ultimaVersionByConcepto($idConcepto){
        return $this->createQueryBuilder('cv')
                        ->innerJoin('cv.concepto','c')
                    ->where('c.id = :idConcepto')->setParameter('idConcepto', $idConcepto)
                    ->orderBy('cv.fechaVersion', 'DESC')
                    ->getQuery()
                    ->setMaxResults(1)
                    ->getSingleResult();
    }
}
