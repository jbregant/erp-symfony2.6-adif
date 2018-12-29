 $(document).ready(function(){
 	$('#adif_contablebundle_cajachica_rendicion_numero').inputmask({
	    mask: "99999999",
	    placeholder: "",
	    numericInput: true
	})

	$('form[name=adif_contablebundle_cajachica_rendicion]').validate();
	
 })