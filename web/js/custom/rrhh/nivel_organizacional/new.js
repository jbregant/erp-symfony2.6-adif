
var $form = $('form[name="adif_recursoshumanosbundle_nivelorganizacional"]');
var $buttonSubmit = $('#adif_recursoshumanosbundle_nivelorganizacional_submit');

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