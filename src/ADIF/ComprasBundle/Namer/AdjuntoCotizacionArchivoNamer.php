<?php

namespace ADIF\ComprasBundle\Namer;

use ADIF\BaseBundle\Entity\AdifApi;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;

/**
 * AdjuntoCotizacionArchivoNamer.
 */
class AdjuntoCotizacionArchivoNamer implements NamerInterface {

    /**
     * Creates a name for the file being uploaded.
     *
     * @param object $archivo The object the upload is attached to.
     * @param Propertymapping $mapping The mapping to use to manipulate the given object.
     * @return string The file name.
     */
    function name($archivo, PropertyMapping $mapping) {

        if (null !== $archivo->getArchivo()) {

            $nombre = uniqid('C-' . (new \DateTime())->format('Ymd_His') . '-');

            return AdifApi::stringCleaner($nombre) . "." . $archivo->getArchivo()->getClientOriginalExtension();
        }
    }

}
