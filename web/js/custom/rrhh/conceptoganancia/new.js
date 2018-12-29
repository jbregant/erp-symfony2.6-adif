/**
* Template basico de new.js que se deberia tomar para armar los crud
* @author Gustavo Luis 
* 19/01/2017
*/

var $form = $('form[name="adif_recursoshumanosbundle_conceptoganancia"]');
var $buttonSubmit = $('#adif_recursoshumanosbundle_conceptoganancia_submit');

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