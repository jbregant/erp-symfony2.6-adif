
var $detalleAsientoReversion;

$(document).ready(function () {
    initDataTable();

    initFiltroButton();

    initRevertirAsientoLink();
});

/**
 * 
 * @returns {undefined}
 */
function initDataTable() {

    var dt_asientocontable_column_index;

    // Si el usuario puede VER NRO ORIGINAL de asientos
    
		var index = 0;
        dt_asientocontable_column_index = {
            id: index++,
            multiselect: index++,
			idConFormato: index++,
            numeroOriginal: index++,
            numero: index++,
            fecha: index++,
            tipo: index++,
            concepto: index++,
            denominacion: index++,
            nroDocumento: index++,
            razonSocial: index++,
            totalDebe: index++,
            totalHaber: index++,
            usuario: index++,
            estado: index++,
            acciones: index++
        };
   

    var fechaInicio = $("#adif_contablebundle_filtro_fechaInicio").val().trim();
    var fechaFin = $("#adif_contablebundle_filtro_fechaFin").val().trim();

    if (validarRangoFechas(fechaInicio, fechaFin)) {

        dt_asientocontable = dt_datatable($('#table-asientocontable'), {
            ajax: {
                url: __AJAX_PATH__ + 'asientocontable/index_table/',
                data: function (d) {
                    d.fechaInicio = $('#adif_contablebundle_filtro_fechaInicio').val();
                    d.fechaFin = $('#adif_contablebundle_filtro_fechaFin').val();
                }
            },
            columnDefs: [
                {
                    "targets": dt_asientocontable_column_index.multiselect,
                    "data": "ch_multiselect",
                    "render": function (data, type, full, meta) {
                        return '<input type="checkbox" class="checkboxes" value="' + data + '" />';
                    }
                },
                {
                    "targets": dt_asientocontable_column_index.acciones,
                    "data": "actions",
                    "render": function (data, type, full, meta) {
                        var full_data = full[dt_asientocontable_column_index.acciones];
                        return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
								<i class="fa fa-search"></i>\n\
							</a>'
                                +
                                (full_data.edit !== undefined ?
                                        '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
									<i class="fa fa-pencil"></i>\n\
								</a>'
                                        : '')
                                +
                                (full_data.revertir !== undefined ?
                                        '<a href="' + full_data.revertir + '" class="btn btn-xs yellow-gold tooltips link-revertir" data-original-title="Revertir asiento">\n\
									<i class="fa fa-exchange"></i>\n\
								</a>'
                                        : '');
                    }
                },
                {
                    className: "nowrap",
                    targets: [
                        dt_asientocontable_column_index.numero,
                        dt_asientocontable_column_index.fecha,
                        dt_asientocontable_column_index.tipo,
                        dt_asientocontable_column_index.concepto,
                        dt_asientocontable_column_index.nroDocumento,
                        dt_asientocontable_column_index.razonSocial,
                        dt_asientocontable_column_index.totalDebe,
                        dt_asientocontable_column_index.totalHaber,
                        dt_asientocontable_column_index.usuario,
                        dt_asientocontable_column_index.estado
                    ]
                },
                {
                    className: "text-center",
                    targets: [
                        dt_asientocontable_column_index.multiselect
                    ]
                },
                {
                    className: "ctn_acciones text-center nowrap",
                    targets: dt_asientocontable_column_index.acciones
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
            dt_asientocontable.DataTable().ajax.reload();
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initRevertirAsientoLink() {

    $detalleAsientoReversion = $('#detalle_asiento_reversion').removeClass('hidden').html();

    $('#detalle_asiento_reversion').remove();


    // BOTON REVERTIR ASIENTO CONTABLE
    $(document).on('click', '.link-revertir', function (e) {

        e.preventDefault();

        var url = $(this).attr('href');

        show_confirm({
            msg: '¿Desea revertir el asiento contable?',
            callbackOK: function () {

                show_dialog({
                    titulo: 'Detalle del asiento de reversi&oacute;n',
                    contenido: $detalleAsientoReversion,
                    labelCancel: 'Cancelar',
                    closeButton: false,
                    callbackCancel: function () {

                        return;
                    },
                    callbackSuccess: function () {

                        var formulario = $('form[name=adif_contablebundle_detalle_asiento_reversion]');

                        var formularioValido = formulario.validate().form();

                        // Si el formulario es válido
                        if (formularioValido) {

                            var fechaContable = $('#adif_contablebundle_asientocontable_fechaContable')
                                    .val();

                            window.location.href = url + '?fecha_contable=' + fechaContable;

                            return;
                        }
                        else {
                            return false;
                        }
                    }
                });

                initDatepickers();

                $('#adif_contablebundle_asientocontable_fechaContable')
                        .datepicker('setStartDate', fechaMesCerradoSuperior);

                $('#adif_contablebundle_asientocontable_fechaContable')
                        .datepicker('setEndDate', getCurrentDate());

                $('#detalle_asiento_reversion').show();
            }
        });

        e.stopPropagation();
    });

}