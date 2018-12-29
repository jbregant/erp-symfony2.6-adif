var index = 0;

var dt_index = {
	id: index++,
	multiselect: index++,
	legajo: index++,
	apellido: index++,
	nombre: index++,
	cuil: index++,
	telefono: index++,
	celular: index++,
	domicilio: index++,
	localidad: index++,
	fecha_nacimiento: index++,
	edad: index++,
	estado_civil: index++,
	tipo_contrato: index++,
	categoria: index++,
	subcategoria: index++,
	convenio: index++,
	puesto: index++,
	superior: index++,
	banco: index++,
	tipo_cuenta: index++,
	cbu: index++,
	obra_social: index++,
	ingreso_planta: index++,
	antiguedad: index++,
	titulo: index++,
	nivel_educacion: index++,
	inicio_primer_contrato: index++,
	inicio_ultimo_contrato: index++,
	fin_ultimo_contrato: index++,
	fecha_inicio_antiguedad: index++,
	periodos_contratados: index++,
	gerencia: index++,
	subgerencia: index++,
	area: index++,
	nivel_organizacional: index++,
	bruto: index++,
	ganancias: index++,
	neto: index++,
	rango_remuneracion: index++,
	fecha_egreso: index++,
	motivo_egreso: index++,
	afiliacion_uf: index++,
	afiliacion_apdfa: index++,
    area_rrhh: index++,
    tablero_mt: index++,
    mail: index++
};

$(document).ready(function () {

//  ACCIONES DE LOS REGISTROS
    $('body').tooltip({
        selector: '.tooltips'
    });

    dt_datatable($('#table-empleado'), {
        ajax: __AJAX_PATH__ + 'empleados/index_extendido_table/',
        columnDefs: [
            {
                "targets": 1,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                }
            },
            {className: "text-center", targets: [
				dt_index.multiselect,
				dt_index.legajo,
				dt_index.telefono,
				dt_index.celular,
				dt_index.fecha_nacimiento,
				dt_index.edad,
				dt_index.estado_civil,
				dt_index.tipo_contrato,
				dt_index.categoria,
				dt_index.subcategoria,
				dt_index.ingreso_planta,
				dt_index.antiguedad,
				dt_index.nivel_educacion,
				dt_index.inicio_primer_contrato,
				dt_index.inicio_ultimo_contrato,
				dt_index.fin_ultimo_contrato,
				dt_index.fecha_inicio_antiguedad,
				dt_index.rango_remuneracion,
				dt_index.fecha_egreso,
				dt_index.banco,
				dt_index.cbu
			]},
            {className: "text-center nowrap", targets: [
				dt_index.apellido,
				dt_index.nombre,
				dt_index.cuil,
				dt_index.obra_social,
				dt_index.tipo_cuenta,
				dt_index.domicilio,
				dt_index.localidad,
				dt_index.puesto,
				dt_index.superior,
				dt_index.gerencia,
				dt_index.subgerencia,
				dt_index.area,
				dt_index.nivel_organizacional,
				dt_index.titulo,
				dt_index.convenio,
                dt_index.mail
			]},
            {className: "text-right", targets: [dt_index.bruto, dt_index.ganancias, dt_index.neto]},
            {"width": "30px", "targets": 2},
            {"width": "100px", "targets": 5}
        ]
    });
});