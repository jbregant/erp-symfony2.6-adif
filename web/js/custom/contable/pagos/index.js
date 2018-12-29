
var dt_pagos_column_index = {
    id: 0,
    multiselect: 1,
    banco: 2,
    numeroSucursalYCuenta: 3,
    formaPago: 4,
    numero: 5,
    fecha: 6,
    importe: 7,
    beneficiario: 8,
    numeroOP: 9,
    estado: 10,
    fechaUltimaActualizacion: 11,
    acciones: 12
};

$(document).ready(function () {
    initDataTable();

    initFiltroButton();
});

/**
 * 
 * @returns {undefined}
 */
function initDataTable() {
    var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
    var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();

    if (validarRangoFechas(fechaInicio, fechaFin)) {

        dt_pagos = dt_datatable($('#table-pagos'), {
            ajax: {
                url: __AJAX_PATH__ + 'pagos/index_table/',
                data: function (d) {
                    d.fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
                    d.fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();
                }
            },
            columnDefs: [
                {
                    "targets": dt_pagos_column_index.multiselect,
                    "data": "ch_multiselect",
                    "render": function (data, type, full, meta) {
                        return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {
                    "targets": dt_pagos_column_index.acciones,
                    "data": "actions",
                    "render": function (data, type, full, meta) {

                        var full_data = full[dt_pagos_column_index.acciones];

                        return (full_data.edit !== undefined ? '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
									<i class="fa fa-pencil"></i>\n\
								</a>' : '') +
                                (full_data.historico !== undefined ? '<a href="' + full_data.historico + '" class="btn btn-xs yellow-gold tooltips" data-original-title="Ver hist&oacute;rico">\n\
									<i class="fa fa-list-ul"></i>\n\
								</a>' : '');
                    }
                },
                {
                    className: "nowrap",
                    targets: [
                        dt_pagos_column_index.banco,
                        dt_pagos_column_index.numeroSucursalYCuenta,
                        dt_pagos_column_index.formaPago,
                        dt_pagos_column_index.numero,
                        dt_pagos_column_index.fecha,
                        dt_pagos_column_index.importe,
                        dt_pagos_column_index.numeroOP,
                        dt_pagos_column_index.estado,
                        dt_pagos_column_index.fechaUltimaActualizacion
                    ]
                },
                {
                    className: "ctn_acciones text-center nowrap",
                    targets: dt_pagos_column_index.acciones
                }
            ]
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroButton() {
    $('#filtrar').on('click', function (e) {
        var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
        var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();
        
        setFechasFiltro(fechaInicio, fechaFin);
        
        if (validarRangoFechas(fechaInicio, fechaFin)) {
            dt_pagos.DataTable().ajax.reload();
        }
    });
}