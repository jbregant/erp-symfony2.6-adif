<?php

namespace ADIF\BaseBundle\Controller;



/**
 * Todos las clases que implementen IContainerAnulable, son todos aquellos que cuando se anulen,
 * tienen que liberar todos los items al paso anterior
 *
 * @author gluis
 */
interface IContainerAnulable
{
	public function anularYLiberarComprobantes($contenedorAnular, $em);
}
