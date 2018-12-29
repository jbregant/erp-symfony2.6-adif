var _exportar_todos = 'todos';
var _exportar_filtrados = 'filtrados';
var _exportar_mostrados = 'mostrados';
var _exportar_seleccionados = 'seleccionados';

var _selected_class = 'active';
var _row_checkbox_class = 'checkboxes';
var _select_all_checkbox_class = 'group-checkable';
var _select_all_header_class = 'table-checkbox';
var _multiselect_class = 'dt-multiselect';
var _singleselect_class = 'dt-singleselect';
var _id_column_index = 0;
var _id_column_class = 'e_id';
var _actions_class = 'ctn_acciones';
var _currency_column_attr = 'currency';
var _date_column_attr = 'date';
var _datetime_column_attr = 'datetime';
var _number_column_attr = 'numeric';



/**
 * 
 * @param {type} name
 * @param {type} fn
 * @returns {undefined}
 */
$.fn.bindFirst = function (name, fn) {

    this.on(name, fn);

    this.each(function () {

        var handlers = $._data(this, 'events')[name.split('.')[0]];

        var handler = handlers.pop();

        handlers.splice(0, 0, handler);
    });
};

$.extend($.fn.dataTableExt.oSort, {
    "currency-pre": function (a) {
        a = (a === "-") ? 0 : a.replace(/[^\d\-\,]/g, "");
        a = a.replace(/^\(%\)\s/g, "");
        return parseFloat(a);
    },
    "currency-asc": function (a, b) {
        return a - b;
    },
    "currency-desc": function (a, b) {
        return b - a;
    },
    "date-uk-pre": function (a) {
        var ukDatea = a.split('/');
        return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    },
    "date-uk-asc": function (a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "date-uk-desc": function (a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    },
    "date-euro-pre": function (a) {
        var x;

        if ($.trim(a) !== '') {
            var frDatea = $.trim(a).split(' ');
            var frTimea = frDatea[1].split(':');
            var frDatea2 = frDatea[0].split('/');
            x = (frDatea2[2] + frDatea2[1] + frDatea2[0] + frTimea[0] + frTimea[1] + frTimea[2]) * 1;
        }
        else {
            x = Infinity;
        }

        return x;
    },
    "date-euro-asc": function (a, b) {
        return a - b;
    },
    "date-euro-desc": function (a, b) {
        return b - a;
    },
    "numeric-comma-asc": function (a, b) {
        var x = (a == "-") ? 0 : a.replace(/,/g, "");
        var y = (b == "-") ? 0 : b.replace(/,/g, "");
        x = parseFloat(x);
        y = parseFloat(y);
        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    },
    "numeric-comma-desc": function (a, b) {
        var x = (a == "-") ? 0 : a.replace(/,/g, "");
        var y = (b == "-") ? 0 : b.replace(/,/g, "");
        x = parseFloat(x);
        y = parseFloat(y);
        return ((x < y) ? 1 : ((x > y) ? -1 : 0));
    }
});

$.fn.dataTableExt.oStdClasses.sWrapper = $.fn.dataTableExt.oStdClasses.sWrapper + " dataTables_extended_wrapper";

$(document).ready(function () {

    initDoubleScroll();

    initColumnResize();

    $('table.datatable').each(function () {
        var table = $(this);
        dt_datatable(table);
    });

});

/**
 * 
 * @param {type} table
 * @param {type} options
 * @returns {unresolved}
 */
function dt_datatable(table, options) {
    return dt_init(table, options);
}

/**
 * 
 * @param {type} table
 * @param {type} options
 * @returns {unresolved}
 */
function dt_init(table, options) {
    if (options === undefined) {
        options = {};
    }

    // Se muestran los tr ocultados por demora de carga
    $(table).find('tbody > tr').show();

    var def_opts = {
        "language": {
            "processing": "Procesando...",
            "lengthMenu": "Registros a mostrar: _MENU_",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "Ningún dato disponible en esta tabla",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "infoPostFix": "",
            "search": "Buscar:",
            "url": "",
            "thousands": ".",
            "loadingRecords": "Cargando...",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        "preDrawCallback": function (settings) {

            if (!$.fn.DataTable.isDataTable(table)) {

                var options = {
                    message: '<span class="bold">\n\
                            <img src="' + __LOADING_IMG_PATH__ + '" style="margin-right: .3em" /> Por favor espere...\n\
                        </span>',
                    centerY: 0,
                    css: {
                        top: '130px',
                        'font-size': '18px',
                        border: 'none',
                        padding: '15px'
                    },
                    overlayCSS: {
                        backgroundColor: '#000'
                    }
                };

                $(".page-content").block(options);
            }
        },
        "initComplete": function (settings, json) {
            $(".page-content").unblock();

            initFiltroBackground();

            updateDoubleScroll();
        },
        "pagingType": "bootstrap_full_number",
        "columnDefs": [],
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        "order": [],
        "stateSave": true,
        "dom": "<'row'r><" + ($(table).hasClass('table-no-scrollable') ? "" : "'table-scrollable'") + "t><'row'<'col-md-4 col-sm-12'i><'col-md-4 col-sm-12'p><'col-md-4 col-sm-12'l>>"

    };

    $.extend(def_opts, options);

    if ($(table).hasClass('mostrar-todos')) {
        def_opts.paging = false;
    }

    if ($(table).find('thead tr th.entity_id').length > 0) {
        // Ocultar primera columna: ID
        def_opts.columnDefs.push({
            "visible": false,
            "orderable": true,
            "targets": _id_column_index
        });
    }

    // Row select
    dt_initRowSelect($(table), def_opts);

    // // Multiselect
    // dt_initMultiselect($(table), def_opts);

    // Custom columns
    dt_initCustomColumns($(table), def_opts);

    // Orden
    dt_initColumnOrden($(table), def_opts);

    // Eventos
    dt_bindEvents($(table));

    // Plugin 
    var dt_table = $(table).dataTable(def_opts);

    // Filtros
    dt_customDataTableFilter(dt_table);

    // Estilos
    dt_initCustomStyle($(table));

    // Export excel
    dt_initExport($(table));

    // Links acciones de tabla
    dt_initActionButtons($(table));

//    if ($(table).attr("data-toggler")) {
//        dt_hideShowColumns(table, dt_table);
//    }

    return dt_table;
}

function dt_bindEvents(table) {
    table.on('preXhr.dt', function (e, settings, data) {

    });

    table.on('xhr.dt', function (e, settings, data) {
        // Uniform en checkboxes
        // dt_initCheckboxesStyle($(table));
        // desbloquear(table.parents('.portlet-body'));
    });

    table.on('init.dt', function (e, settings, data) {
        // Uniform en checkboxes
        // dt_initCheckboxesStyle($(table));
        table.find('.' + _row_checkbox_class).on('change', function (e) {
            dt_actualizar_seleccionados($(table));
        });
        //dt_initCheckboxesStyle(table)
        desbloquear(table.parents('.portlet-body'));
    });

    table.on('search.dt', function (e, settings, data) {
        dt_highlight_filters(this);
    });
}

function dt_initCheckboxesStyle(table) {
    if (table.DataTable().settings()[0].ajax !== null) {
        // Uniform checkboxes si los datos se cargan via AJAX
        _.each(table.DataTable().rows().nodes(), function (el_tr) {
            $(el_tr).find("td > input.checkboxes").uniform();
        });
    }
}

function dt_initColumnOrden(table, def_opts) {
    // Orden deshabilitado
    if ($(table).find('thead tr.headers th.no-order').length > 0) {
        var orders_not = [];
        $(table).find('thead tr.headers th.no-order').each(function () {
            orders_not.push($(this).index());
        });

        def_opts.columnDefs.push({
            "orderable": false,
            "targets": orders_not
        });
    }

    if (!$(table).hasClass('table-no-order')) {

        if (!($(table).data('no-ordenable') > 0)) {

            def_opts.order = def_opts.order.length > 0
                    ? def_opts.order
                    : [$(table).find('thead tr.headers th.entity_id').length > 0
                                ? $(table).find('thead tr.headers th.entity_id').first().index()
                                : $(table).find('thead tr.headers th:not(.no-order)').first().index(), 'desc'
                    ];
        }

    }
}

function dt_initCustomColumns(table, def_opts) {
    // Currency
    if ($(table).find('thead tr.headers th[' + _currency_column_attr + ']').length > 0) {
        var currency_index = [];
        $(table).find('thead tr.headers th[' + _currency_column_attr + ']').each(function () {
            currency_index.push($(this).index());
        });

        def_opts.columnDefs.push({
            "type": 'currency',
            "targets": currency_index
        });
    }

    // Date
    if ($(table).find('thead tr.headers th[' + _date_column_attr + ']').length > 0) {
        var dates_index = [];
        $(table).find('thead tr.headers th[' + _date_column_attr + ']').each(function () {
            dates_index.push($(this).index());
        });

        def_opts.columnDefs.push({
            "type": 'date-uk',
            "targets": dates_index
        });
    }

    // DateTime
    if ($(table).find('thead tr.headers th[' + _datetime_column_attr + ']').length > 0) {
        var dates_index = [];
        $(table).find('thead tr.headers th[' + _datetime_column_attr + ']').each(function () {
            dates_index.push($(this).index());
        });

        def_opts.columnDefs.push({
            "type": 'date-euro',
            "targets": dates_index
        });
    }

    // Number
    if ($(table).find('thead tr.headers th[' + _number_column_attr + ']').length > 0) {
        var number_index = [];
        $(table).find('thead tr.headers th[' + _number_column_attr + ']').each(function () {
            number_index.push($(this).index());
        });

        def_opts.columnDefs.push({
            "type": 'numeric-comma',
            "targets": number_index
        });
    }
}

function dt_initExport(table) {
    if ($(table).hasClass('export-excel')) {
        $(table).closest('.portlet-body').find('.table-toolbar').prepend(
                '<div class="btn-group pull-right dt_export_ctn">\n\
                <div class="btn-group">\n\
                    <button class="btn btn-sm dark" type="button">\n\
                    <i class="fa fa-floppy-o"></i>\n\
                    Exportar a Excel</button>\n\
                    <button data-toggle="dropdown" class="btn btn-sm dark dropdown-toggle" type="button">\n\
                        <i class="fa fa-angle-down"></i>\n\
                    </button>\n\
                    <ul role="menu" class="dropdown-menu">\n\
                        <li><a href="" data-table-excel-id="' + $(table).attr('id') + '" data-que-exportar="' + _exportar_todos + '">Todos</a></li>\n\
                        <li><a href="" data-table-excel-id="' + $(table).attr('id') + '" data-que-exportar="' + _exportar_filtrados + '">Filtrados</a></li>\n\
                        <li><a href="" data-table-excel-id="' + $(table).attr('id') + '" data-que-exportar="' + _exportar_mostrados + '">Esta p&aacute;gina</a></li>\n\
                        <li><a href="" data-table-excel-id="' + $(table).attr('id') + '" data-que-exportar="' + _exportar_seleccionados + '">Seleccionados</a></li>\n\
                    </ul>\n\
                    </div>\n\
             </div>');
        $('a[data-table-excel-id=' + $(table).attr('id') + ']').on('click', function (e) {
            e.preventDefault();
            dt_export(table, {registros: $(this).data('que-exportar'), formato: 'excel'});
            e.stopPropagation();
        });
        if ($(table).hasClass('export-pdf')) {
            $(table).closest('.portlet-body').find('.table-toolbar').prepend(
                    '<div class="btn-group pull-right dt_export_ctn">\n\
                <div class="btn-group">\n\
                    <button class="btn btn-sm dark" type="button">\n\
                    <i class="fa fa-file-pdf-o"></i>\n\
                    Exportar a PDF</button>\n\
                    <button data-toggle="dropdown" class="btn btn-sm dark dropdown-toggle" type="button">\n\
                        <i class="fa fa-angle-down"></i>\n\
                    </button>\n\
                    <ul role="menu" class="dropdown-menu">\n\
                        <li><a href="" data-table-pdf-id="' + $(table).attr('id') + '" data-que-exportar="' + _exportar_todos + '">Todos</a></li>\n\
                        <li><a href="" data-table-pdf-id="' + $(table).attr('id') + '" data-que-exportar="' + _exportar_filtrados + '">Filtrados</a></li>\n\
                        <li><a href="" data-table-pdf-id="' + $(table).attr('id') + '" data-que-exportar="' + _exportar_mostrados + '">Esta p&aacute;gina</a></li>\n\
                        <li><a href="" data-table-pdf-id="' + $(table).attr('id') + '" data-que-exportar="' + _exportar_seleccionados + '">Seleccionados</a></li>\n\
                    </ul>\n\
                    </div>\n\
             </div>');
            $('a[data-table-pdf-id=' + $(table).attr('id') + ']').on('click', function (e) {
                e.preventDefault();
                dt_export(table, {registros: $(this).data('que-exportar'), formato: 'pdf'});
                e.stopPropagation();
            });
        }
    }
}

function dt_initCustomStyle(table) {
    var tableWrapper = $(table).parents('.dataTables_wrapper');
    $('.dataTables_filter input', tableWrapper).addClass("form-control input-medium input-inline");
    $('.dataTables_length select', tableWrapper).addClass("form-control input-small input-sm");
    $('.dataTables_length select', tableWrapper).select2();
    $('.dataTables_paginate', tableWrapper).parent().css('text-align', 'center');
    $('.dataTables_length', tableWrapper).parent().css('text-align', 'right');
    $('.not-in-filter span', tableWrapper).remove();
}

function dt_initRowSelect(table) {
    // Multiselect
    if ($(table).hasClass(_multiselect_class)) {
        dt_initMultiselect(table)
    } else if ($(table).hasClass(_singleselect_class)) {
        dt_initSingleselect(table)
    }
}

function dt_initSingleselect(table) {
    // Deshabilitar CHECKED EN CHECKBOXES
    $(table).find('div.checker span').removeClass('checked');

    $(table).on('click', 'tbody tr', function (e) {
        if ($(e.target).is('a') || $(e.target).is('i.fa')) {
            return;
        }

        if ($(this).parents('tr').length > 0 || $(this).parents('tr').prev().hasClass('shown') || $(this).prev().hasClass('shown')) {
            // TABLA HIJA
            e.preventDefault();
            e.stopPropagation();
            return;
        }

        // Deseleccionar todos
        dt_select_all(table, false);

        $(this).toggleClass(_selected_class);
        var el_check = $(this).find('input.' + _row_checkbox_class)[0];
        if (el_check) {
            el_check.checked = $(this).hasClass(_selected_class);
            dt_actualizar_seleccionados(table);
            $.uniform.update(el_check);
        }
        $(table).trigger('single_selected_row', [$(this)]);
    });

    $('.' + _select_all_checkbox_class).hide();
}

function dt_initMultiselect(table) {
    // Deshabilitar CHECKED EN CHECKBOXES
    $(table).find('div.checker span').removeClass('checked');

    // Multiselect
    if ($(table).hasClass(_multiselect_class)) {
        $(table).on('click', 'tbody tr', function (e) {
            if ($(e.target).is('a') || $(e.target).is('i.fa')) {
                return;
            }

            if ($(this).parents('tr').length > 0 || $(this).parents('tr').prev().hasClass('shown') || $(this).prev().hasClass('shown')) {
                // TABLA HIJA
                e.preventDefault();
                e.stopPropagation();
                return;
            }

            // Selecciona un solo elemento
            var el_check = $(this).find('input.' + _row_checkbox_class)[0];

            if (!el_check) {
                return
            }

            $(this).toggleClass(_selected_class);
            el_check.checked = $(this).hasClass(_selected_class);
            dt_actualizar_seleccionados(table);
            $.uniform.update(el_check);
        });

        $(table).on('change', '.' + _select_all_checkbox_class, function (e) {
            var checked = $(this).is(":checked");
            dt_select_all(table, checked, false);
            dt_actualizar_seleccionados(table);
        });
    }
}

function dt_select_all(table, checked, all) {
    var set = [];
    if (all || !checked) {
        set = $(table).DataTable().rows().nodes().to$();
    } else {
        set = $(table).DataTable().rows({page: 'current'}).nodes().to$();
    }

    $(set).each(function () {
        var el_check = $(this).find('.' + _row_checkbox_class)[0];
        if (el_check) {
            el_check.checked = checked;

            if (checked) {
                $(this).addClass(_selected_class);
            } else {
                $(this).removeClass(_selected_class);
            }
            $.uniform.update(el_check);
        }
    });
}

function dt_customDataTableFilter(table) {
    var dt_table = $(table).DataTable();

    var tr_filters = $(table).find('.filter');
    var tr_headers = $(table).find('.headers');

    $(tr_headers).after(tr_filters);

    $(table).find('.filter th').not('.not-in-filter').each(function () {
        var filter_th = $(this);
        var placeholder_th = filter_th.parents('thead').find('.headers th:nth-child(' + (filter_th.index() + 1) + ')').html();

        if (filter_th.data('type') === 'select') {
            if (filter_th.data('select-mode') === 'embedded') {
                $(this).find('select').select2({dropdownAutoWidth: 'true'});
            } else {
                filter_th.html('<select ' + ($(this).data('select-multiple') ? 'multiple="multiple" ' : '') + 'class="input-filter"></select>');
                var filter_sel = $(this).find('select');
                if (filter_th.data('select-mode') === 'auto') {
                    if (filter_th.data('select-all') === 'Todos' || filter_th.data('select-all') === 'Todas') {
                        filter_sel.append('<option value="">Todos</option>');
                    }
                    var appended = [];

                    dt_table.column(dt_table.column.index('fromVisible', filter_th.index())).data().each(function (val) {
                        if (appended.indexOf(val) === -1) {
                            filter_sel.append('<option value="^' + val.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&") + '$">' + val + '</option>');
                            appended.push(val);
                        }
                    });
                    filter_sel.select2({dropdownAutoWidth: 'true'});
                } else if (filter_th.data('select-ajax')) {
                    //              AJAX
                    var select_ajax = filter_th.data('select-ajax');
                    var select_value = filter_th.data('select-value');
                    var select_label = filter_th.data('select-label');
                    var select_all = filter_th.data('select-all') || 'Todos';

                    $.ajax({
                        type: 'post',
                        url: __AJAX_PATH__ + select_ajax,
                        success: function (data) {
                            filter_sel.append('<option value="">' + select_all + '</option>');
                            for (var i = 0, total = data.length; i < total; i++) {
                                filter_sel.append('<option value="^' + data[i][select_value].replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&") + '$">' + data[i][select_label] + '</option>');
                            }
                            if (dt_table.state !== undefined && dt_table.state() !== null) {
                                var saved_column = dt_table.state().columns[dt_table.column.index('fromVisible', filter_th.index())];
                                if (saved_column.visible && saved_column.search.search !== "") {
                                    filter_sel.val(saved_column.search.search);
                                }
                            }

                            filter_sel.select2({dropdownAutoWidth: 'true'});
                        }
                    });
                }
            }
        } else if (filter_th.data('type') === 'date') {
//          DATEPICKERS COMO FILTROS
            filter_th.html('<input class="input-filter filter-dp" type="text" placeholder="' + placeholder_th + '" />');
            initDatepicker(filter_th.find('.filter-dp'), {forceParse: false});
            if ($.inputmask) {
                filter_th.find('.filter-dp').inputmask('remove');
            }
        } else if (filter_th.data('type') === 'numeric') {
//          FILTRO NUMERICO AGREGAR COMPARADORES (>,<,=, etc)
            $(this).html('<input class="input-filter" type="text" placeholder="' + placeholder_th + '" />');
//            $(this).html('<span class="input-filter-numeric-comparator"></span>');
        } else {
//          INPUT COMUN
            $(this).html('<input class="input-filter" type="text" placeholder="' + placeholder_th + '" />');
        }

//      Setear valores del localStorage para inputs
        if (dt_table.state !== undefined && dt_table.state() !== null) {
            $(dt_table.state().columns).each(function (i, c) {
                if (c.visible && c.search.search !== "") {
                    var real_idx = dt_table.column.index('fromData', i);
//                  INPUT COMUN
                    $(table).find('thead tr.filter th:nth-child(' + (real_idx + 1) + ') input').val(c.search.search);
                }
            });
        }
    });

//  EVENTOS TRIGGERS DE FILTROS
//  INPUT 
    $(table).on('keyup change', "thead tr.filter th input.input-filter", function () {

        var dt_column = dt_table.column(dt_table.column.index('fromVisible', $(this).parent().index()));

        dt_column.search(this.value).draw();

        setFiltroBackground($(this));
    });

//  SELECT    
    $(table).on('change', "thead tr.filter th select.input-filter", function () {
        //select multiple
        if ($(this).prop('multiple')) {
            if ($(this).val() === null) {
                dt_table
                        .column(dt_table.column.index('fromVisible', $(this).parent().index()))
                        .search("")
                        .draw();
            } else {
                dt_table
                        .column(dt_table.column.index('fromVisible', $(this).parent().index()))
                        .search($(this).val().toString().replace(',', '   '), true)
                        .draw();
            }
        } else { //select no multiple 
            dt_table
                    .column(dt_table.column.index('fromVisible', $(this).parent().index()))
                    .search(this.value, true)
                    .draw();
        }
    });

    $("thead tr.filter th .input-filter").addClass('form-control form-filter input-sm');

    // Boton limpiar filtros
    var clear_btn_pos = false;
    if (tr_headers.find('th.' + _actions_class).length) {
        clear_btn_pos = 'last';
    } else if ($(table).hasClass(_multiselect_class)) {
        clear_btn_pos = 'first';
    }

    if (clear_btn_pos) {
        tr_filters.find('th:' + clear_btn_pos).append('<button class="btn btn-sm btn-danger btn-clear-filters tooltips" data-original-title="Limpiar filtros"><i class="fa fa-times"></i></button>');
        tr_filters.find('th:' + clear_btn_pos).addClass('text-center');
        tr_filters.find('th:' + clear_btn_pos).on('click', function (e) {

            e.preventDefault();

            tr_filters.find('.input-filter').val('');

            tr_filters.find('select.input-filter').select2();

            dt_table.columns().search('').draw();

            e.stopPropagation();

            initFiltroBackground();
        });
    }
}

/**
 * 
 * @param {type} table
 * @returns {undefined}
 */
function dt_highlight_filters(table) {
    $(table).find('thead > tr.filter > th').find('input.form-filter,select.form-filter').each(function () {
        if ($(this).val() !== null && $(this).val() !== "") {
            $(this).parent().addClass('hlt');
        } else {
            $(this).parent().removeClass('hlt');
        }
    });
}

/**
 * 
 * @param {type} table
 * @param {type} options
 * @returns {undefined}
 */
function dt_export(table, options) {

    var data = null;

    formato = (typeof options.formato === typeof undefined) ? 'excel' : options.formato;

    if (typeof (destroyEllipsis) == "function") {
        destroyEllipsis();
    }

    switch (options.registros) {
        case _exportar_todos:
            data = dt_getRows(table, true);
            break;
        case _exportar_mostrados:
            data = dt_getShowedRows(table, true);
            break;
        case _exportar_filtrados:
            data = dt_getFilteredRows(table, true);
            break;
        case _exportar_seleccionados:
            data = dt_getSelectedRows(table, true);
            if (data.length == 0) {
                show_alert({msg: 'Debe seleccionar al menos un item de la grilla para imprimir.'});
                return false;
            }
            break;
    }


    data = dt_removeHiddenData(table, data);
    data = dt_formatCurrenciesFields(table, data);
    data = JSON.stringify(dt_excludeColumns(table, data));

    content = {
        content: {
            title: $(table).attr('dataexport-title'),
            sheets: {
                0: {
                    title: $(table).attr('dataexport-title'),
                    tables: {
                        0: {
                            title: $(table).attr('dataexport-title'),
                            titulo_alternativo: (typeof $(table).attr('dataexport-title-alternativo') !== typeof undefined && $(table).attr('dataexport-title-alternativo') !== false ? $(table).attr('dataexport-title-alternativo') : ''),
                            data: data,
                            headers: JSON.stringify(dt_getHeaders(table))
                        }
                    }
                }
            }
        }
    };

    if (typeof (initEllipsis) == "function") {
        initEllipsis();
    }

    open_window('POST', __AJAX_PATH__ + 'export_' + formato, content, '_blank');
}

/**
 * 
 * @param {type} table
 * @param {type} data
 * @returns {unresolved}
 */
function dt_excludeColumns(table, data) {

    if (data.length == 0) {
        return null;
    }

    var first_row_index = 0;

    first_row_index += $(table).find('thead tr th.entity_id').length > 0 ? 1 : 0; // SACO EL ID

    first_row_index += $(table).find('thead tr th.' + _select_all_header_class).length > 0 ? 1 : 0; // SACO EL CHECKER

    var last_row_index = $(table).find('thead tr th.' + _actions_class).length > 0 ? data[0].length - 1 : data[0].length; // SACO LAS ACCIONES

    return _.map(data, function (row) {
        return row.slice(first_row_index, last_row_index);
    });
}

/**
 * 
 * @param {type} table
 * @param {type} notExcludeColumns
 * @returns {unresolved|Array}
 */
function dt_getRows(table, notExcludeColumns) {
    var nodes = $(table).DataTable().rows().nodes();

    if (nodes.flatten().length === 0) {
        return [];
    }

    var data = _.map(nodes, function (el_tr) {
        return _.map($(el_tr).find('td'), function (el_td) {
            return getTDValue(el_td);
        });
    });

    if (notExcludeColumns !== undefined && notExcludeColumns) {
        return data;
    }
    return dt_excludeColumns(table, data);
}

/**
 * 
 * @param {type} table
 * @param {type} notExcludeColumns
 * @returns {unresolved|Array}
 */
function dt_getFilteredRows(table, notExcludeColumns) {
    var nodes = $(table).DataTable().rows({filter: 'applied'}).nodes();

    if (nodes.flatten().length === 0) {
        return [];
    }

    var data = _.map(nodes, function (el_tr) {
        return _.map($(el_tr).find('td'), function (el_td) {
            return getTDValue(el_td);
        });
    });

    if (notExcludeColumns !== undefined && notExcludeColumns) {
        return data;
    }
    return dt_excludeColumns(table, data);

}

/**
 * 
 * @param {type} table
 * @param {type} notExcludeColumns
 * @returns {unresolved|Array}
 */
function dt_getSelectedRows(table, notExcludeColumns) {

    var nodes = $(table).DataTable().rows('tr.' + _selected_class).nodes();

    if (nodes.flatten().length === 0) {
        return [];
    }

    var data = _.map(nodes, function (el_tr) {
        return _.map($(el_tr).find('td'), function (el_td) {
            return getTDValue(el_td);
        });
    });

    if (notExcludeColumns !== undefined && notExcludeColumns) {
        return data;
    }
    return dt_excludeColumns(table, data);
}

/**
 * 
 * @param {type} table
 * @param {type} notExcludeColumns
 * @returns {unresolved|Array}
 */
function dt_getUnselectedRows(table, notExcludeColumns) {

    var nodes = $(table).DataTable().rows('tr:not(.' + _selected_class + ')').nodes();

    if (nodes.flatten().length === 0) {
        return [];
    }

    var data = _.map(nodes, function (el_tr) {
        return _.map($(el_tr).find('td'), function (el_td) {
            return getTDValue(el_td);
        });
    });

    if (notExcludeColumns !== undefined && notExcludeColumns) {
        return data;
    }
    return dt_excludeColumns(table, data);
}

function dt_getShowedRows(table, notExcludeColumns) {
    var nodes = $(table).DataTable().rows({page: 'current'}).nodes();

    if (nodes.flatten().length === 0) {
        return [];
    }

    var data = _.map(nodes, function (el_tr) {
        return _.map($(el_tr).find('td'), function (el_td) {
            return getTDValue(el_td);
        });
    });

    if (notExcludeColumns !== undefined && notExcludeColumns) {
        return data;
    }

    return dt_excludeColumns(table, data);
}

/**
 * 
 * @param {type} table
 * @returns {unresolved}
 */
function dt_getRowsIds(table) {

    var ids = $(table).DataTable().column('th.entity_id').data();
    return ids.flatten();
}

/**
 * 
 * @param {type} table
 * @returns {unresolved}
 */
function dt_getFilteredRowsIds(table) {

    var ids = $(table).DataTable().columns('th.entity_id', {filter: 'applied'}).data();

    return ids.flatten();
}

/**
 * 
 * @param {type} table
 * @returns {unresolved}
 */
function dt_getSelectedRowsIds(table) {

    var ids = $(table).DataTable().cells('tr.' + _selected_class, $(table).DataTable().column('th.entity_id').index()).data();

    return ids.flatten();
}

/**
 * 
 * @param {type} table
 * @returns {unresolved}
 */
function dt_getShowedRowsIds(table) {

    var ids = $(table).DataTable().cells({page: 'current'}, $(table).DataTable().column('th.entity_id').index()).data();

    return ids.flatten();
}

/**
 * 
 * @param {type} table
 * @returns {unresolved}
 */
function dt_getHeaders(table) {
    var thead = $(table).DataTable().tables().header();
    return _.map($(thead).find('tr.headers th:not(.' + _actions_class + '):not(.' + _select_all_header_class + '):not(".hidden")').toArray(), function (e) {
        return {texto: $(e).text(),
            formato: $(e).attr('export-format') ? $(e).attr('export-format') : 'text'
        };
    });
}

/**
 * 
 * @param {type} table
 * @returns {dt_getCurrencyColumns.indexes|Array}
 */
function dt_getCurrencyColumns(table) {
    var indexes = [];
    $(table).DataTable().tables().header().find('tr.headers th:not(.' + _actions_class + '):not(.' + _select_all_header_class + ')').each(function (i, e) {
        if ($(e).hasClass(_currency_column_class)) {
            indexes.push[i];
        }
    });

    return indexes;
}
/**
 * 
 * @param {type} table
 * @returns {undefined}
 */
function dt_initActionButtons(table) {

    $(document).on('click', '.table-actions-popover a, tbody tr td.ctn_acciones > a', function (e) {
        e.stopPropagation();
    });
}

/**
 * 
 * @param {type} table
 * @returns {undefined}
 */
function dt_actualizar_seleccionados(table) {
    table.trigger("selected_element", [$(table).find('.' + _row_checkbox_class + ':checked').length]);
}

/**
 * 
 * @param {type} table
 * @param {type} dt_table
 * @returns {undefined}
 */
function dt_hideShowColumns(table, dt_table) {
    columnas = '';
    $.each($(table).DataTable().columns()[0], function (index, value) {
        header = $($(table).DataTable().columns(index).header()).not('.hidden').not('.table-checkbox').not('.entity_id');
        if (header.length > 0) {
            //columnas = columnas.concat('<label><input type="checkbox" class="not-checkbox-transform" checked="checked" data-column="' + index + '">' + $(header).html() + '</label>');
            columnas = columnas.concat('<label><input type="checkbox" class="not-checkbox-transform"' + ($(header[0]).is(":visible") ? 'checked="checked"' : '') + ' data-column="' + index + '">' + $(header).html() + '</label>');
        }
    });
    $(table).closest('.portlet-body').find('.table-toolbar').prepend(
            '<div class="btn-group pull-right">\n\
                <div class="btn-group">\n\
            <a class="btn btn-sm default" href="javascript:;" data-toggle="dropdown" aria-expanded="true">Columnas <i class="fa fa-angle-down"></i></a>\n\
            <div class="opcion-columnas dropdown-menu hold-on-click dropdown-checkboxes pull-right">'
            + columnas +
            '</div>\n\
                </div>\n\
            </div>'
            );

    $('input[type="checkbox"]', $(table).closest('.portlet-body').find('.table-toolbar').find('.opcion-columnas')).change(function () {
        /* Get the DataTables object again - this is not a recreation, just a get of the object */
        var iCol = parseInt($(this).attr("data-column"));
        var bVis = dt_table.fnSettings().aoColumns[iCol].bVisible;
        dt_table.fnSetColumnVis(iCol, (bVis ? false : true));
    });
}

/**
 * 
 * @param {type} el_td
 * @returns {jQuery}
 */
function getTDValue(el_td) {
    return $(el_td).html();
}

/**
 * 
 * @returns {undefined}
 */
function initFiltroBackground() {

    $('input.input-filter').each(function () {

        setFiltroBackground($(this));
    });
}

/**
 * 
 * @param {type} inputFilter
 * @returns {undefined}
 */
function setFiltroBackground(inputFilter) {

    var thFilter = inputFilter.parent('th');

    if (inputFilter.val() !== '') {
        thFilter.css('background-color', '#ffb3b3');
    }
    else {
        thFilter.css('background-color', 'white');
    }
}

/**
 * 
 * @returns {undefined}
 */
function initDoubleScroll() {

    $(document).on('init.dt', function (e, settings) {
        updateDoubleScroll();
    });

    $(document).on('draw.dt', function () {
        updateDoubleScroll();
    });

    $(window).on('resize', function () {
        updateDoubleScroll();
    });
}

/**
 * 
 * @returns {undefined}
 */
function updateDoubleScroll() {

    $('body').find('.table-scrollable').doubleScroll();
}

/**
 * 
 * @returns {undefined}
 */
function initColumnResize() {

    $(function () {

        var pressed = false;
        var clickLocked = false;
        var $start = undefined;
        var $table = undefined;
        var startX, startWidth = 0;
        var mouseEpsilon = 10;

        $("table .headers th").not('.ctn_acciones, .table-checkbox').mousedown(function (e) {

            e.preventDefault();

            pressed = true;

            $start = $(this);

            $table = $(this).parents('table');

            startX = e.pageX;

            $(this).css('cursor', 'col-resize');

            startWidth = $(this).width();

            $($start).removeClass("nowrap");

            $($start).addClass("resizing");

        }).mousemove(function (e) {

            if (pressed) {

                var width = startWidth + (e.pageX - startX) < 0
                        ? 0
                        : startWidth + (e.pageX - startX);

                $($start).width(width);
            }

        }).on('mouseup mouseleave', function (e) {

            if (pressed) {

                $($start).css('cursor', 'default');

                $($start).removeClass("resizing");

                if ($.fn.DataTable.isDataTable($table)) {
                    $table.DataTable().draw();
                }

                pressed = false;

                if (Math.abs((startX - e.clientX)) > mouseEpsilon) {

                    if (typeof $($start).data('click-locked') === "undefined") {

                        $($start).data('click-locked', true);

                        $($start).bindFirst('click', function (e) {

                            e.stopImmediatePropagation();

                        });
                    }
                } else {

                    if (typeof $($start).data('click-locked') !== "undefined") {

                        $($start).removeData('click-locked');

                        $._data($(this)[0], "events")['click'].shift();
                    }
                }
            }
        });
    });

}

/**
 * 
 * @param {type} table
 * @param {type} data
 * @returns {unresolved}
 */
function dt_removeHiddenData(table, data) {

    var hiddenIdexes = [];

    $($(table).DataTable().rows().nodes()[0]).find('td.hidden').each(function (valueTd, keyTd) {
        hiddenIdexes.push($(this).index());
    });

    var newData = _.map(data, function (el_tr) {
        return _.filter(el_tr, function (value, key) {
            return $.inArray(key, hiddenIdexes) == -1;
        });
    });

    return newData;

}

/**
 * 
 * @param {type} table
 * @param {type} data
 * @returns {unresolved}
 */
function dt_formatCurrenciesFields(table, data) {

    var currencyIdexes = [];

    $($(table).DataTable().tables().header()).find('tr.headers th[export-format="currency"]').each(function (valueTh, keyTh) {
        currencyIdexes.push($(this).index());
    });

    var newData = _.map(data, function (row) {
        return _.map(row, function (content, key) {
            return $.inArray(key, currencyIdexes) == -1 ? content : content.replace('$ ', '');
        });
    });

    return newData;

}