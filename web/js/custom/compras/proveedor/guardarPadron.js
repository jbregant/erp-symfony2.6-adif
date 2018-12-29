var dt_renglon;

var $form = $('#form_guardar_padron');

$(document).ready(function(){

	initRenglonTable();

	initBotonSubmit();

});

function initRenglonTable() 
{
	dt_renglon = dt_datatable($('#tabla_renglones'));
	dt_renglon = dt_renglon.DataTable();
}

function initBotonSubmit()
{
	$('#form_guardar_padron_submit').on('click', function(e) {

		e.preventDefault();
		//$form.submit();
	});
}