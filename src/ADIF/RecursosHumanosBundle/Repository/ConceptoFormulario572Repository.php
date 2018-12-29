<?php

namespace ADIF\RecursosHumanosBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use ADIF\RecursosHumanosBundle\Entity\ConceptoGanancia;

/**
 * 
 */
class ConceptoFormulario572Repository extends EntityRepository {

    /**
     * 
     * @param type $formulario572
     * @param type $ordenAplicacionTipoConcepto
     * @return type
     */
    public function getConceptosFormulario572ByOrdenAplicacionTipoConcepto($formulario572, $ordenAplicacionTipoConcepto, $aplicaGananciaAnual = false) {

        $query= $this->createQueryBuilder('cf')
                ->innerJoin('cf.conceptoGanancia', 'cg')
                ->innerJoin('cg.tipoConceptoGanancia', 'tcg')
                ->where('cf.formulario572 = :formulario572')
                ->andWhere('tcg.ordenAplicacion = :ordenAplicacion')
                ->andWhere('cg.aplicaGananciaAnual = :gananciaAnual')
                ->setParameters(new ArrayCollection(array(
                    new Parameter('formulario572', $formulario572),
                    new Parameter('ordenAplicacion', $ordenAplicacionTipoConcepto),
                    new Parameter('gananciaAnual', $aplicaGananciaAnual)))
                )
                ->getQuery();

        return $query->getResult();
    }
    
    /**
     * Devuelve los conceptos del F572, que se aplican una sola vez y que no se tienen que volver a aplicar mas
     * en otras liquidaciones
     * @param Formulario572 $formulario572
     * @return ArrayCollection<ConceptoFormulario572>
     */
    public function getConceptosFormulario572Aplicables($formulario572) {

        $codigos = array(
            ConceptoGanancia::__CODIGO_AJUSTE_REINTEGRO, 
            ConceptoGanancia::__CODIGO_AJUSTE_RETROACTIVO, 
            ConceptoGanancia::__CODIGO_OTRO_EMPLEADOR,
            ConceptoGanancia::__CODIGO_JUBILACION_OTRO_EMPLEADOR,
            ConceptoGanancia::__CODIGO_OBRA_SOCIAL_OTRO_EMPLEADOR,
            ConceptoGanancia::__CODIGO_SINDICAL_OTRO_EMPLEADOR,
            ConceptoGanancia::__CODIGO_RETENCION_OTROS_EMPLEADOR
        ); 
        
        $query= $this->createQueryBuilder('cf')
                ->innerJoin('cf.conceptoGanancia', 'cg')
                ->innerJoin('cg.tipoConceptoGanancia', 'tcg')
                ->where('cf.formulario572 = :formulario572')
                ->andWhere('cg.codigo572 in (:codigos)')
                ->setParameters(new ArrayCollection(array(
                    new Parameter('formulario572', $formulario572),
                    new Parameter('codigos', $codigos)))
                )
                ->getQuery();

        return $query->getResult();
    }

}
