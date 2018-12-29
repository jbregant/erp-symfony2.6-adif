/**
* Template basico de new.js que se deberia tomar para armar los crud
* @author Gustavo Luis 
* 19/01/2017
*/

var $form = $('form[name="adif_recursoshumanosbundle_escalaimpuesto"]');
var $buttonSubmit = $('#adif_recursoshumanosbundle_escalaimpuesto_submit');

var $fechaDesdeInput = $('#adif_recursoshumanosbundle_escalaimpuesto_vigenciaDesde');
var $fechaHastaInput = $('#adif_recursoshumanosbundle_escalaimpuesto_vigenciaHasta');

jQuery(document).ready(function () {
    
	initSubmitButton();
	
	initCustomDatepickers();
	
});

function initSubmitButton()
{
	$buttonSubmit.on('click', function(){
		
		if ( $form.valid() ) {
			$form.submit();
		}
	});
}

function initCustomDatepickers()
{
	$fechaDesdeInput.on('change', function() {
		$fechaHastaInput.datepicker('setStartDate', $fechaDesdeInput.datepicker('getDate'));
	});
	
	$fechaHastaInput.on('change', function() {
		$fechaDesdeInput.datepicker('setEndDate', $fechaHastaInput.datepicker('getDate'));
	});
}