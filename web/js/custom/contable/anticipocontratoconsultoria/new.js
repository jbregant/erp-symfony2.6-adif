$(document).ready(function () {
    $('form[name=adif_contablebundle_anticipocontratoconsultoria]').validate();
    initAutocompleteConsultor();
    initSubmitButton();
    initContratoConsultoriaHandler();
});

/**
 * 
 * @returns {undefined}
 */
function initAutocompleteConsultor() {    
    $('#adif_contablebundle_anticipocontratoconsultoria_consultor').autocomplete({
        source: __AJAX_PATH__ + 'consultor/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            selectConsultor(event, ui, null);
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (CUIT: " + item.CUIT + ")</a>")
                .appendTo(ul);
    };
}

function selectConsultor(event, ui, id_contrato_seleccionado) {
    $('#adif_contablebundle_anticipocontratoconsultoria_consultor_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_anticipocontratoconsultoria_consultor_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_anticipocontratoconsultoria_idConsultor').val(ui.item.id);

    $('#table_contratos_consultor').hide();
    $.ajax({
        url: __AJAX_PATH__ + 'contratoconsultoria/index_table_por_consultor/',
        data: {id_consultor: ui.item.id}
    }).done(function (result) {

        var ocs = JSON.parse(result).data;

        $('#table_contratos_consultor tbody').empty();
        $('#table_contratos_consultor').show();

        $(ocs).each(function (oc) {
            var $tr = $('<tr />', {id_contrato: this[0], style: 'cursor: pointer;'});
            $('<td />', {text: this[1]}).appendTo($tr);
            $('<td />', {text: this[2]}).appendTo($tr);
            $('<td />', {text: this[3]}).appendTo($tr);
            $('<td />', {text: this[4]}).appendTo($tr);
            $('#table_contratos_consultor tbody').append($tr);
        });
        
        if(id_contrato_seleccionado){
            $('#table_contratos_consultor tbody tr[id_contrato='+id_contrato_seleccionado+']').trigger('click');
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_anticipocontratoconsultoria_submit').on('click', function (e) {

        var tr_seleccionado = $('#table_contratos_consultor tbody tr[class="active"]');

        if (tr_seleccionado.length > 0) {

            if ($('form[name=adif_contablebundle_anticipocontratoconsultoria]').valid()) {
                e.preventDefault();

                show_confirm({
                    msg: '¿Desea guardar el anticipo?',
                    callbackOK: function () {

                        var json = {
                            'id_contrato': tr_seleccionado.attr('id_contrato')
                        };

                        $('form[name=adif_contablebundle_anticipocontratoconsultoria]').addHiddenInputData(json);
                        $('form[name=adif_contablebundle_anticipocontratoconsultoria]').submit();
                    }
                });

                e.stopPropagation();

                return false;
            }
        } else {
            show_alert({msg: 'Debe seleccionar al menos una Órden de compra o tramo'});
        }

        return false;
    });
}

/**
 * 
 * @returns {undefined}
 */
function initContratoConsultoriaHandler() {
    $(document).on('click', '#table_contratos_consultor tbody tr', function (e) {
        e.preventDefault();
        bloquear();
        $(this).parents('tbody').find('tr').removeClass('active');
        $(this).addClass('active');
        $.uniform.update($(this).find('input'));
        desbloquear();
    });
}