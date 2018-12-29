
var $form = $('form[name="adif_recursoshumanosbundle_puesto"]');
var $buttonSubmit = $('#adif_recursoshumanosbundle_puesto_submit');

jQuery(document).ready(function () {
    
	initSubmitButton();
	
});

function initSubmitButton()
{
	$buttonSubmit.on('click', function(){
		
		if ( $form.valid() ) {
			$form.submit();
		}
	});
}