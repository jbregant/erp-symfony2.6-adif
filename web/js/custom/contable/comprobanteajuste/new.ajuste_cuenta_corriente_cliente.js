
var $formularioComprobanteAjuste = $('form[name="adif_contablebundle_comprobanteajuste"]');

var totales = new Array();

var dataTable;

$(document).ready(function () {

    initValidate();
	
	initComponentes();

    initAutocompleteCliente();

    initSubmitButton();

});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion del Formulario
    $formularioComprobanteAjuste.validate();
	
}

function initComponentes()
{
	$(".row_subtotal").remove();
	desactivarComponentes(true);
}


/**
 * 
 * @returns {undefined}
 */
function initAutocompleteCliente() {

    $('#adif_contablebundle_comprobanteajuste_cliente').autocomplete({
        source: __AJAX_PATH__ + 'cliente/autocomplete/form',
        minLength: 3,
        select: function (event, ui) {
            completarInformacionCliente(event, ui);
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
                .append("<a>" + item.razonSocial + " (CUIT: " + item.CUIT + ")</a>")
                .appendTo(ul);
    };

}

/**
 * 
 * @param {type} event
 * @param {type} ui
 * @returns {undefined}
 */
function completarInformacionCliente(event, ui) {

    $('#adif_contablebundle_comprobanteajuste_cliente_razonSocial').val(ui.item.razonSocial);
    $('#adif_contablebundle_comprobanteajuste_cliente_cuit').val(ui.item.CUIT);
    $('#adif_contablebundle_comprobanteajuste_idCliente').val(ui.item.id);
    $('#legend_oc_cliente').html(': ' + ui.item.razonSocial + ' - CUIT: ' + ui.item.CUIT);
	
	buscarPartidasAbiertas(ui.item.id);
}

/**
 * 
 * @returns {undefined}
 */
function initSubmitButton() {

    // Handler para el boton "Guardar"
    $('#adif_contablebundle_comprobanteajuste_submit').on('click', function (e) {

        if ($formularioComprobanteAjuste.valid()) {

            e.preventDefault();

            show_confirm({
                msg: '¿Desea guardar el comprobante?',
                callbackOK: function () {

                    $formularioComprobanteAjuste.submit();
                }
            });

            e.stopPropagation();

            return false;
        }

        return false;
    });
}

/**
* Busca los comprobantes abiertos sin pagar del cliente
*/
function buscarPartidasAbiertas(idCliente)
{
	if (idCliente == null || typeof idCliente == 'undefined') {
		return false;
	}
	
	$.ajax({
		url: __AJAX_PATH__ + 'cliente/get_partidas_abiertas_cliente',
		data: 'id=' + idCliente,
		type: 'post',
		success: function(response) {
			if (response != '') {
				desactivarComponentes(false);
				var tabla = armoTablaPartidasAbiertas(response); 
				$("#div_tabla_partidas_abiertas").html(tabla);
				dataTable = dt_datatable($("#tabla_partidas_abiertas")).DataTable();
				attachEventsTablaPartidasAbiertas();
				$('.btn-clear-filters').remove();
				//$('.dataTables_info').remove();
				$("#comprobantes_abiertos_section").show();
			} else {
				show_alert({msg: 'El cliente seleccionado no tiene partidas abiertas.'});
			}
		},
		complete: function(){
			
			dataTable.on('search', function(){
				attachEventsTablaPartidasAbiertas();
			});
			
		}
	});
}

function armoTablaPartidasAbiertas(comprobantes) 
{
	var tabla = '<table class="table table-bordered table-striped table-condensed table-hover dt-multiselect" id="tabla_partidas_abiertas">';
	tabla += '<thead>';
	tabla += '<tr class="replace-inputs filter">';
	
	tabla += '<th>ID Comprobante</th>';
	tabla += '<th>Comprobante</th>';
	tabla += '<th>N&uacute;mero</th>';
	tabla += '<th>Fecha contable</th>';
	tabla += '<th>Saldo</th>';
	tabla += '<th>Total</th>';
	tabla += '<th>Estado</th>';
	tabla += '</tr>';
	
	tabla += '<tr class="headers">';
	tabla += '<th>ID Comprobante</th>';
	tabla += '<th>Comprobante</th>';
	tabla += '<th>N&uacute;mero</th>';
	tabla += '<th>Fecha contable</th>';
	tabla += '<th>Saldo</th>';
	tabla += '<th>Total</th>';
	tabla += '<th>Estado</th>';
	tabla += '</tr>';
	
	tabla += '</thead>';
	
	tabla += '<tbody>';
	
	for(i in comprobantes) {
		var comprobante = comprobantes[i];
		totales.push(comprobante.saldo);
		tabla += '<tr idComprobante="' + comprobante.idComprobante + '" index="' + i + '" class="no-seleccionado" >';
		tabla += '<td class="clickeable">' + comprobante.idComprobante + '</td>';
		tabla += '<td class="clickeable">' + comprobante.tipoComprobante + ' ' + comprobante.letra + '</td>';
		if (comprobante.puntoVenta != null) {
			tabla += '<td class="clickeable">' + comprobante.puntoVenta + '-' + comprobante.numero + '</td>';
		} else if(comprobante.numero != null) {
			// Casos de NC/ND "Y"
			tabla += '<td class="clickeable">' + comprobante.numero + '</td>';
		} else {
			tabla += '<td class="clickeable">--</td>';
		}
		tabla += '<td class="clickeable">' + comprobante.fechaContable + '</td>';
		tabla += '<td class="clickeable">' + comprobante.saldo + '</td>';
		tabla += '<td class="clickeable">' + comprobante.total + '</td>';
		tabla += '<td class="clickeable">' + comprobante.estado + '</td>';
		tabla += '</tr>';
	}
	tabla += '</tbody>';
	tabla += '</table>';
	return tabla;
}

function attachEventsTablaPartidasAbiertas()
{
	$(".clickeable").on('click', function() {
		
		$(".seleccionado").removeClass('seleccionado').addClass('no-seleccionado');
		var $tr = $(this).parent();
		$tr.removeClass('no-seleccionado').addClass('seleccionado');
		var idComprobante = $tr.attr('idComprobante');
		var indiceTrSeleccionado = $tr.attr('index');
		$("#adif_contablebundle_comprobanteajuste_idComprobante").val(idComprobante);
		var totalSeleccionado = totales[indiceTrSeleccionado];
		$("#adif_contablebundle_comprobanteajuste_total").val(totalSeleccionado);
	});	
}

function desactivarComponentes(valor) 
{
	$("#adif_contablebundle_comprobanteajuste_tipoComprobante").prop('disabled', valor);
	$("#adif_contablebundle_comprobanteajuste_fechaComprobante").prop('disabled', valor);
	$("#adif_contablebundle_comprobanteajuste_observaciones").prop('disabled', valor);
	$("#adif_contablebundle_comprobanteajuste_total").prop('disabled', valor);
	$("#adif_contablebundle_comprobanteajuste_submit").prop('disabled', valor);
}