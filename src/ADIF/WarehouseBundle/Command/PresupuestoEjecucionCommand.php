<?php

namespace ADIF\WarehouseBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use ADIF\BaseBundle\Entity\EntityManagers;
use Doctrine\ORM\Query\ResultSetMapping;
use ADIF\WarehouseBundle\Entity\PresupuestoEjecucion;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
* Este comando bath prepara los datos de la ejecucion presupuestaria y la guarda en adifprod_warehouse.presupuesto_ejecucion
* @author Gustavo Luis
* 12/01/2017
*/
class PresupuestoEjecucionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('app:warehouse:crear-ejecucion-presupuestaria')
        ->setDescription('Crea el warehouse para el presupuesto.')
        // the "--help" option
        ->setHelp("Este comando va a crear la tabla de ejeuci�n presupuestaria, de acuerdo al rango de fechas establecido.")
		->addOption(
			'presupuesto',
			'-p',
			InputOption::VALUE_REQUIRED,
			'Campo obligatorio presupuesto',
			date('Y') 
		);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$presupuesto = $input->getOption('presupuesto');
		
		if ($presupuesto == null) {
			$output->writeln('<bg=red;options=bold>El parametro -p [presupuesto] es obligatorio. Por favor ingrese un anio de un presupuesto valido.</>');
			exit;
		}
		
		$fechaCorrida = new \DateTime();
        $output->writeln('Creacion de tabla de ejecucion presupuestaria ' . $presupuesto);
		$output->writeln('--------------------------------------------------------------');
		$output->writeln('Fecha de corrida ' . $fechaCorrida->format('d/m/Y H:i:s') );
		
		$emContable = $this->getContainer()->get('doctrine')->getManager(EntityManagers::getEmContable());
		$emWareHouse = $this->getContainer()->get('doctrine')->getManager(EntityManagers::getEmWarehouse());
		
		$rsm = new ResultSetMapping();

        $rsm->addScalarResult('id', 'id');
		$rsm->addScalarResult('codigo', 'codigo');
		$rsm->addScalarResult('denominacion', 'denominacion');
		$rsm->addScalarResult('monto_inicial', 'monto_inicial');
		$rsm->addScalarResult('monto_actual', 'monto_actual');
		$rsm->addScalarResult('monto_actual', 'monto_actual');
        $rsm->addScalarResult('totales', 'totales');
		$rsm->addScalarResult('fecha_inicio', 'fecha_inicio');
		$rsm->addScalarResult('fecha_fin', 'fecha_fin');
		$rsm->addScalarResult('presupuesto', 'presupuesto');
		
		$sql = '
			SELECT 
				cpe.id,
				cpe.codigo,
				cpe.denominacion,
				cp.monto_inicial,
				cp.monto_actual,
				tot_presupuestario(cpe.id, ec.fecha_inicio, ec.fecha_fin) AS totales,
				ec.fecha_inicio,
				ec.fecha_fin,
				ec.denominacion AS presupuesto
			FROM cuenta_presupuestaria_economica cpe
			INNER JOIN cuenta_presupuestaria cp ON cpe.id = cp.id_cuenta_presupuestaria_economica
			INNER JOIN presupuesto p ON cp.id_presupuesto = p.id 
			INNER JOIN ejercicio_contable ec ON p.id_ejercicio_contable = ec.id
			WHERE ec.denominacion = ?
		';
		
		$nativeQuery = $emContable->createNativeQuery($sql, $rsm);
		
		$nativeQuery->setParameter(1, $presupuesto);
	
		$totales = $nativeQuery->getResult();
		
		$emWareHouse->getConnection()->beginTransaction();

		try {
		
			foreach ($totales as $actual) {

				// Seteo las variables
				$totales = explode('|', $actual['totales']);

				$codigo = $actual['codigo'];
				$denominacion = $actual['denominacion'];
				$monto_inicial = $actual['monto_inicial'];
				$monto_actual = $actual['monto_actual'];
				// Totales spliteado
				$provisorio = $totales[1];
				$definitivo = $totales[2];
				$devengado = $totales[3];
				$ejecutado = $totales[4];
				
				$fecha_inicio = $actual['fecha_inicio'];
				$fecha_fin = $actual['fecha_fin'];
				$presupuesto = $actual['presupuesto'];
			   
				$presupuestoEjecucion = new PresupuestoEjecucion();
			   
				$presupuestoEjecucion->setCodigoCuentaPresupuestariaEconomica($codigo);
				$presupuestoEjecucion->setDenominacionCuentaPresupuestariaEconomica($denominacion);
				
				$presupuestoEjecucion->setMontoInicial($monto_inicial);
				$presupuestoEjecucion->setMontoActual($monto_actual);
				
				$presupuestoEjecucion->setProvisorio($provisorio);
				$presupuestoEjecucion->setDefinitivo($definitivo);
				$presupuestoEjecucion->setDevengado($devengado);
				$presupuestoEjecucion->setEjecutado($ejecutado);
				
				// Calculo el saldo
				$saldo = $monto_actual - $provisorio - $definitivo - $devengado - $ejecutado;
				$presupuestoEjecucion->setSaldo($saldo);
				
				$dtFechaInicio = \DateTime::createFromFormat('Y-m-d', $fecha_inicio);
				$dtFechaFin = \DateTime::createFromFormat('Y-m-d', $fecha_fin);
				
				$presupuestoEjecucion->setEjercicioContableFechaInicio($dtFechaInicio);
				$presupuestoEjecucion->setEjercicioContableFechaFin($dtFechaFin);
				
				$presupuestoEjecucion->setPresupuesto($presupuesto);
				$presupuestoEjecucion->setFechaCreacion(new \DateTime());
				
				$emWareHouse->persist($presupuestoEjecucion);
			}
			
			$emWareHouse->flush();
			$emWareHouse->getConnection()->commit();
			
		} catch (\Exception $e) {
			
			$emWareHouse->getConnection()->rollback();
            $emWareHouse->close();
			
			throw $e;
		}
		
		$emWareHouse->clear();
        
		$fechaFin = new \DateTime();
		$output->writeln('--------------------------------------------------------------');
		$output->writeln('Fecha de finalizacion ' . $fechaFin->format('d/m/Y H:i:s') );
		$output->writeln('--Fin batch--');
    }
}