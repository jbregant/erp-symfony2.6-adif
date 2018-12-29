$(document).ready(function () {

    initStep1();
    initVolverStep1();

    initStep2();
    initVolverStep2();

    initStep3();

    initStep4();
    
    var options = {
        "order": [1, 'asc']
    };
    
    $('.table').each(function(){
        dt_init($(this), options);
    });

});

function initStep1() {
    $(document).on('click', '#table-comprobantes tbody tr', function (e, tr) {
        chequear_comprobantes_anteriores($(this).closest('table'), $(this).attr('id-contrato'), $(this).attr('indice'), $(this).hasClass('active'));
    });    
}

function initVolverStep1() {
    $('#btn_volver_step1').on('click', function (e) {
        e.preventDefault();
        var json = {
            'ids': idsStep1
        };

        $('#form_volver_step1').addHiddenInputData(json);
        $('#form_volver_step1').submit();
    });
}

function initStep2() {
    $('#btn_generar_comprobantes_step2').on('click', function (e) {
        e.preventDefault();
        bloquear();
        var table = $('#table-comprobantes');
        var ids = [];
        ids = dt_getSelectedRowsIds(table);

        if (!ids.length) {
            show_alert({msg: 'Debe seleccionar al menos un comprobante para generar.'});
            desbloquear();
            return;
        }

        var json = {
            'indices': ids.toArray()
        };

        $('#form_generar_comprobantes_step2').addHiddenInputData(json);
        $('#form_generar_comprobantes_step2').submit();
    });

    $('.bloqueado').each(function () {
        $(this).block({message: null, overlayCSS: {backgroundColor: 'black', opacity: 0.05, cursor: 'not-allowed'}});
    });

    $(document).on('click', '.table-comprobantes tbody tr', function (e, tr) {
        chequear_comprobantes_anteriores($(this).closest('table'), $(this).attr('id-contrato'), $(this).attr('indice'), $(this).hasClass('active'));
    });
}


function initVolverStep2() {

    $('#btn_volver_step2').on('click', function (e) {
        e.preventDefault();
        var json = {
            'indices': idsStep2
        };

        $('#form_volver_step2').addHiddenInputData(json);
        $('#form_volver_step2').submit();
    });
}

function initStep3() {
    $('#btn_generar_comprobantes_step3, #btn_generar_comprobantes_cupones_step3').on('click', function (e) {
        e.preventDefault();
        bloquear();

        comprobantesAGenerar = [];

        $('table').each(function () {
            comprobantesAGenerar = comprobantesAGenerar.concat(dt_getSelectedRowsIds($(this)).toArray());
        });


        if (!comprobantesAGenerar.length) {
            show_alert({msg: 'Debe seleccionar al menos un comprobante para continuar.'});
            desbloquear();
            return;
        }

        var json = {
            'comprobantes': comprobantesAGenerar,
            'esCupon': esCupon
        };

        $('#form_generar_comprobantes_step3').addHiddenInputData(json);
        $('#form_generar_comprobantes_step3').submit();
    });

    $(document).on('click', '.table-comprobantes tbody tr', function (e, tr) {
        habilitar_checkboxes($(this));
    });
}

function chequear_comprobantes_anteriores(tabla, id_contrato, indice, chequear) {
    $(tabla).find('tr[id-contrato=' + id_contrato + ']').each(function () {
        var indice_tr = parseInt($(this).attr('indice'));
        if ((chequear && indice_tr < indice) ||
                (!chequear && indice_tr > indice)) {

            var el_check = $(this).find('input.' + _row_checkbox_class)[0];
            if (chequear) {
                $(this).addClass(_selected_class);
            } else {
                $(this).removeClass(_selected_class);
            }
            if (el_check) {
                el_check.checked = $(this).hasClass(_selected_class);
                $.uniform.update(el_check);
            }
        }
    });
}

function habilitar_checkboxes(tr) {
    var table = $(tr).closest('table');
    var numero_comprobantes_maximo = $(table).attr('numero-comprobantes-maximo');
    var comprobantes_seleccionados = dt_getSelectedRows(table);

    $(table).find('input[type=checkbox]:not(:checked)').each(function () {
        $(this).prop('disabled', comprobantes_seleccionados.length === numero_comprobantes_maximo);
    });
}


function initStep4() {
    $('#btn_generar_comprobantes_step4').on('click', function (e) {
        e.preventDefault();
        bloquear();
        $('#form_generar_comprobantes_step4').submit();
    });
}