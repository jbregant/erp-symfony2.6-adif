$(document).ready(function(){
	$('#adif_contablebundle_cajachica_comprobante_puntoVenta').inputmask({
	    mask: "9999",
	    placeholder: "0",
	    numericInput: true
	});

	$('#adif_contablebundle_cajachica_comprobante_numero').inputmask({
	    mask: "99999999",
	    placeholder: "0",
	    numericInput: true
	});	
})
