<?php

namespace ADIF\ContableBundle\Entity\Facturacion;

use ADIF\AutenticacionBundle\Entity\BaseAuditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContratoAlquiler
 *
 * @author Manuel Becerra
 * created 23/01/2015
 * 
 * @ORM\Table(name="contrato_alquiler")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({
 *      "contrato_alquiler_vivienda" = "ContratoAlquilerVivienda",
 *      "contrato_alquiler_comercial" = "ContratoAlquilerComercial",
 *      "contrato_alquiler_agropecuario" = "ContratoAlquilerAgropecuario",
 *      "contrato_tenencia_precaria" = "ContratoTenenciaPrecaria",
 *      "contrato_asunto_oficial_municipalidad" = "ContratoAsuntoOficialMunicipalidad"
 * })
 * )
 */
class ContratoAlquiler extends ContratoVenta implements BaseAuditable {

    /**
     * @var string
     *
     * @ORM\Column(name="numero_inmueble", type="string", length=50, nullable=true)
     * @Assert\Length(
     *      max="50", 
     *      maxMessage="El número de inmueble no puede superar los {{ limit }} caracteres.")
     * )
     */
    protected $numeroInmueble;

    /**
     * Set numeroInmueble
     *
     * @param string $numeroInmueble
     * @return ContratoAlquiler
     */
    public function setNumeroInmueble($numeroInmueble) {
        $this->numeroInmueble = $numeroInmueble;

        return $this;
    }

    /**
     * Get numeroInmueble
     *
     * @return string 
     */
    public function getNumeroInmueble() {
        return $this->numeroInmueble;
    }

    /**
     * Getter esContratoAlquiler
     * 
     * @return boolean
     */
    public function getEsContratoAlquiler() {
        return true;
    }

    /**
     * Getter indicaNumeroInmueble
     * 
     * @return boolean
     */
    public function getIndicaNumeroInmueble() {
        return true;
    }

}
