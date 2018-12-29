
var validator;

$(document).ready(function () {
        
    initValidator();

    initButtons();

    checkError();
	
	initSelectUsuarioComo();
});

// Para que funcione en las siguientes páginas de datatables
$('.seleccionado').on('click', function () {

    $tr = $(this).closest('tr');

    $('#fos_user_registration_form_username').val($tr.find('.ad_user').html());
    $('#fos_user_registration_form_email').val($tr.find('.ad_mail').html());
    $('#fos_user_registration_form_nombre').val($tr.find('.ad_nombre').html());
    $('#fos_user_registration_form_apellido').val($tr.find('.ad_apellido').html());
    $('#fos_user_registration_form_plainPassword_first').val($tr.find('.ad_user').html());
    $('#fos_user_registration_form_plainPassword_second').val($tr.find('.ad_user').html());
    $('#usuario_form_paso_2').addClass('hidden');
    $('#usuario_form_paso_3').removeClass('hidden');
    $('#fos_user_registration_form_usuario_ad').val(true);
});

/**
 * 
 * @returns {undefined}
 */
function initValidator() {

    validator = $('form[name="fos_user_registration_form"]').validate({
        rules: {
            'fos_user_registration_form[username]': {
                required: true
            },
            'fos_user_registration_form[email]': {
                required: true,
                email: true
            },
            'fos_user_registration_form[plainPassword][first]': {
                required: '#fos_user_registration_form_usuario_ad[value=false]'
            },
            'fos_user_registration_form[plainPassword][second]': {
                required: '#fos_user_registration_form_usuario_ad[value=false]'
            },
            'fos_user_registration_form[nombre]': {
                required: true
            },
            'fos_user_registration_form[apellido]': {
                required: true
            },
            'fos_user_registration_form[grupos][]': {
                required: true
            }
        }
    });
}

/**
 * 
 * @returns {undefined}
 */
function initButtons() {

    $('#sin_ad').on('click', function () {

        validator.resetForm();

        $('div').removeClass('has-error');

        $('#usuario_form_paso_1').hide();
        $('#usuario_form_paso_3').removeClass('hidden');

        $('#fos_user_registration_form_username').prop('readonly', false);
    });

    $('#con_ad').on('click', function () {

        validator.resetForm();

        $('div').removeClass('has-error');

        $('#usuario_form_paso_1').hide();
        $('#usuario_form_paso_2').removeClass('hidden');

        $('#fos_user_registration_form_username').prop('readonly', true);
        $('#fos_user_registration_form_plainPassword_first').closest('.form-group').addClass('hidden');
        $('#fos_user_registration_form_plainPassword_second').closest('.form-group').addClass('hidden');
    });

    $('.back_paso_1').on('click', function () {

        $('#usuario_form_paso_1').show();
        $('#usuario_form_paso_2').addClass('hidden');
        $('#usuario_form_paso_3').addClass('hidden');

        $('#fos_user_registration_form_username').val('');
        $('#fos_user_registration_form_email').val('');
        $('#fos_user_registration_form_nombre').val('');
        $('#fos_user_registration_form_apellido').val('');
        $('#fos_user_registration_form_plainPassword_first').val('');
        $('#fos_user_registration_form_plainPassword_second').val('');
        $('#fos_user_registration_form_plainPassword_first').closest('.form-group').removeClass('hidden');
        $('#fos_user_registration_form_plainPassword_second').closest('.form-group').removeClass('hidden');
        $('#fos_user_registration_form_usuario_ad').val(false);
    });

    $('.seleccionado').on('click', function () {

        $tr = $(this).closest('tr');

        $('#fos_user_registration_form_username').val($tr.find('.ad_user').html());
        $('#fos_user_registration_form_email').val($tr.find('.ad_mail').html());
        $('#fos_user_registration_form_nombre').val($tr.find('.ad_nombre').html());
        $('#fos_user_registration_form_apellido').val($tr.find('.ad_apellido').html());
        $('#fos_user_registration_form_plainPassword_first').val($tr.find('.ad_user').html());
        $('#fos_user_registration_form_plainPassword_second').val($tr.find('.ad_user').html());
        $('#usuario_form_paso_2').addClass('hidden');
        $('#usuario_form_paso_3').removeClass('hidden');
        $('#fos_user_registration_form_usuario_ad').val(true);
    });
}

/**
 * 
 * @returns {undefined}
 */
function checkError() {

    if (__hayError == 1) {

        bloquear();

        if (__esUsuarioAD != 1) {
            $('#sin_ad').trigger('click');
        }
        else {
            $('#con_ad').trigger('click');
        }

        desbloquear();
    }
}

function initSelectUsuarioComo()
{
	$('#fos_user_registration_form_usuarioComo').on('change', function() {
		
        var idEmpresa = $('#fos_user_registration_form_empresas').val();
		var idUsuario = $('#fos_user_registration_form_usuarioComo').val();
        
        var data = {
			idEmpresa: idEmpresa,
			idUsuario: idUsuario,
            idSelect: 'fos_user_registration_form_groups'
		};
		
		$.ajax({
			type: "POST",
			url: __AJAX_PATH__ + 'grupos/get_select_grupos_by_empresa_and_usuario',
			data: data
		}).success(function (response) {
			$('#s2id_fos_user_registration_form_groups').remove();
			$('#fos_user_registration_form_groups').remove();
			$('#div_grupos_append').append(response.select);			
			initSelectById('fos_user_registration_form_groups');
		});
		
		
	});
}