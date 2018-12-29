
$(document).ready(function () {

    initValidate();
	
	initSelectEmpresas();
});

/**
 * 
 * @returns {undefined}
 */
function initValidate() {

    // Validacion del Formulario
    $('form[name=adif_autenticacionbundle_usuario]').validate();
}

function initSelectEmpresas()
{
	$('#adif_autenticacionbundle_usuario_empresas').on('change', function() {
        //var idUsuario = $('#idUsuario').val();
		//getSelectGruposByEmpresasAndUsuario(idUsuario);
	});
    
    $('#adif_autenticacionbundle_usuario_usuarioComo').on('change', function() {
        var idUsuario = $('#adif_autenticacionbundle_usuario_usuarioComo').val();
        getSelectGruposByEmpresasAndUsuario(idUsuario);
    });
}

function getSelectGruposByEmpresasAndUsuario(idUsuario)
{
    var idEmpresa = $('#adif_autenticacionbundle_usuario_empresas').val();
   
    var data = {
        idEmpresa: idEmpresa,
        idUsuario: idUsuario,
        idSelect: 'adif_autenticacionbundle_usuario_groups'
    };

    $.ajax({
        type: "POST",
        url: __AJAX_PATH__ + 'grupos/get_select_grupos_by_empresa_and_usuario',
        data: data
    }).success(function (response) {
        $('#s2id_adif_autenticacionbundle_usuario_groups').remove();
        $('#adif_autenticacionbundle_usuario_groups').remove();
        $('#div_grupos_append').append(response.select);			
        initSelectById('adif_autenticacionbundle_usuario_groups');
    });
}
