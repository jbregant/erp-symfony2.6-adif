
var index = 0;

var dt_activolineal_column_index = {
    id: index++,
    multiselect: index++,

    operador: index++,

    linea: index++,

    division: index++,

    corredor: index++,

    ramal: index++,

    categoria: index++,

    progresivaInicioTramo: index++,

    progresivaFinalTramo: index++,

    tipoVia: index++,

    kilometraje: index++,

    tipoActivo: index++,

    estacion: index++,

    tipoServicio: index++,

    estadoConservacion: index++,

    //zonaVia: index++,

    balasto: index++,

    rieles: index++,

    durmientes: index++,

    velocidad: index++,

    capacidad: index++,

    estadoInventario: index++,

    acciones: index
};

initTable();

function initTable(){
    dt_activolineal = dt_datatable($('#table-activolineal'), {
        ajax: __AJAX_PATH__ + 'activolineal/index_table/',
        order: [ [2, 'asc'], [3, 'asc'], [4, 'asc'], [8, 'asc'] ],
        stateSave: false,
        columnDefs: [
            {
                "targets": dt_activolineal_column_index.multiselect,
                "data": "ch_multiselect",
                "render": function (data, type, full, meta) {
                    var id = full[dt_activolineal_column_index.id];
                    return '<input type="checkbox" class="checkboxes" value="' + data + '" id="' + id + '" />';
                }
            },
            {
                "targets": dt_activolineal_column_index.acciones,
                "data": "actions",
                "render": function (data, type, full, meta) {
                    var full_data = full[dt_activolineal_column_index.acciones];
                    var datos = full[dt_activolineal_column_index.tipoActivo];
                    return '<a href="' + full_data.show + '" class="btn btn-xs blue tooltips" data-original-title="Ver detalle">\n\
                                <i class="fa fa-search"></i>\n\
                            </a>' +
                            (full_data.edit !== undefined ?
                                '<a href="' + full_data.edit + '" class="btn btn-xs green tooltips" data-original-title="Editar">\n\
                                    <i class="fa fa-pencil"></i>\n\
                                </a>' : '') +
                            (full_data.delete !== undefined ?
                                '<a href="' + full_data.delete + '" class="btn btn-xs red tooltips accion-borrar" data-original-title="Eliminar">\n\
                                    <i class="fa fa-trash"></i>\n\
                                </a>' : '') +
                            (full_data.separar !== undefined ?
                                '<a href="' + full_data.separar + '" ' + (datos !== 'Vía'?'disabled="disabled"':'') + ' class="btn btn-xs orange tooltips btn-separar" data-original-title="Separar">\n\
                                    <i class="fa fa-chain-broken"></i>\n\
                                </a>' : '') + 
                                '<a onclick="javascript:showMap(' + full[0] + ', '+full_data.es_punto +')" class="btn btn-xs purple tooltips btn-mapa" data-original-title="Mapa">\n\
                                    <i class="fa fa-map-o"></i>\n\
                                </a>';
                }
            },
            {
                className: "text-center",
                targets: [
                    dt_activolineal_column_index.multiselect
                ]
            },
            {
                className: "ctn_acciones text-center nowrap",
                targets: dt_activolineal_column_index.acciones
            }
        ],
        "initComplete": function (settings, json) {

            initBorrarButton();
        }
    });
}

jQuery(document).ready(function () {

    initUnir();
    initSeparar();
    //dt_activolineal.state.clear();
});


/**
 *
 * @returns {undefined}
 */
function initUnir() {
    $('.btn-unir').addClass('disabled');

    $('#table-activolineal').on('click', '.checkboxes,tr', function () {
        var ids = [];
        $('.checkboxes:checked').each(function(){
            if($(this).parents('tr').find('.btn-separar').attr('disabled') === 'disabled'){
                ids = [];
                return false;
            }
            ids.push($(this).attr('id'));
        });
        if(ids.length >= 2){
            $('.btn-unir').removeClass('disabled');
        }else{
            $('.btn-unir').addClass('disabled');
        }
    });

    $('.btn-unir').click(function(e){
        e.preventDefault();
        var ids = [];
        $('.checkboxes:checked').each(function(){
            if(typeof $(this).parents('tr').find('.btn-separar').attr('disabled') === 'undefined'){
                ids.push($(this).attr('id'));
            }
        });
        if(ids.length >= 2){
            var data = {ids:ids};
            $.ajax({
                type: 'post',
                url: __AJAX_PATH__ + 'activolineal/unir',
                data: data,
                success: function(data) {
                    $('.modal_container').html(data);
                    $('#modal').modal('show');
                }
            });
        }
    });


}

function initSeparar(){

    $('#table-activolineal').on('click', '.btn-separar', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            type: 'get',
            url: url,
            success: function(data) {
                $('.modal_container').html(data);
                $('#modal').modal('show');
            }
        });
    });

    $('.modal_container').on('click', '#form_submit', function(e){
        e.preventDefault();
        show_confirm({
            msg: '¿Confirma la separación del Activo Lineal seleccionado?',
            callbackOK: function () {
                var url = $('#modal form').attr('action');
                var datos = $('#modal form').serializeArray();
                $('#modal').modal('hide');
                $.ajax({
                    type: 'post',
                    url: url,
                    data: datos,
                    success: function(data) {
                        if(data === 'OK'){
                            location.reload();
                        }else{
                            $('.modal_container').html(data);
                            $('#modal').modal('show');
                        }
                    }
                });
            }
        });
    });

    $('.modal_container').on('keyup','#form_kilometrajeSeparacion',function(){
        var separacion = Number($(this).val().replace(',','.')).toFixed(3);
        if(!isNaN(separacion) && separacion > 0){
            $('#form_activosLineales_0_progresivaFinalTramo').val(separacion.replace('.',','));
            $('#form_activosLineales_1_progresivaInicioTramo').val(separacion.replace('.',','));
        }
    });

}
